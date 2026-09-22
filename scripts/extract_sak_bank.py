"""Extract the GAEKS SAK question bank from the supplied book PDF.

Requires pypdf. The output contains answer keys and must stay private.
This is a deterministic transcription aid, not legal/content approval.
"""

import argparse
import json
import re
from pathlib import Path

from pypdf import PdfReader


HEADING = re.compile(r"(?m)^SOAL (\d+):\s*(.*?)\s*10 Poin\s*$")
FIELDS = re.compile(r"(?m)^(?:PEMBAHASAN:|DASAR HUKUM:|TIPS UJIAN:|HINDARI:)")
RUNNING_FOOTER = re.compile(
    r"(?m)^MENGUASAI KEPABEANAN DARI RUMAH EDISI PEMBELAJARAN MANDIRI DAN TEST CBT\s*\n\d+\s*$"
)


def tidy(value):
    return re.sub(r"\s+", " ", value).strip()


def page_text(page):
    text = page.extract_text() or ""
    return RUNNING_FOOTER.sub("", text).strip()


def field(body, name, next_names):
    match = re.search(
        rf"(?s)(?:^|\n){re.escape(name)}:\s*(.*?)(?=\n(?:{'|'.join(re.escape(n) for n in next_names)}):|\Z)",
        body,
    )
    return tidy(match.group(1)) if match else ""


def chapter_questions(pages, chapter):
    marker = f"POIN PEMBAHASAN UTAMA & ANALISIS MENDALAM SOAL BAB {chapter:02d}"
    start = next(i for i, p in enumerate(pages) if marker in p)
    end = next(i for i in range(start + 1, len(pages)) if f"CHECKLIST PENGUASAAN BAB {chapter:02d}" in pages[i])
    block = "\n".join(pages[start:end])
    matches = list(HEADING.finditer(block))
    if len(matches) != 10 or [int(m.group(1)) for m in matches] != list(range(1, 11)):
        raise ValueError(f"Bab {chapter:02d}: expected 10 consecutive questions, got {[m.group(1) for m in matches]}")

    questions = []
    for idx, match in enumerate(matches):
        number = int(match.group(1))
        body = block[match.end():matches[idx + 1].start() if idx + 1 < len(matches) else len(block)]
        body = re.sub(r"(?m)^\s*(?:POIN PEMBAHASAN UTAMA.*|Bagian ini menyajikan.*)$", "", body)
        explanation = field(body, "PEMBAHASAN", ["DASAR HUKUM", "TIPS UJIAN", "HINDARI"])
        legal = field(body, "DASAR HUKUM", ["TIPS UJIAN", "HINDARI"])
        before = body.split("\nPEMBAHASAN:", 1)[0]
        key_match = re.search(r"(?m)^KUNCI JAWABAN:\s*([A-D])\s*$", before)
        if key_match:
            before = before[:key_match.start()]
            option_matches = list(re.finditer(r"(?m)^([A-D])\.\s+", before))
            if [m.group(1) for m in option_matches] != list("ABCD"):
                raise ValueError(f"Bab {chapter:02d} soal {number}: invalid options")
            stem = tidy(before[:option_matches[0].start()])
            options = [
                {"key": m.group(1), "text": tidy(before[m.end():option_matches[j + 1].start() if j < 3 else len(before)])}
                for j, m in enumerate(option_matches)
            ]
            if any(not option["text"] for option in options):
                raise ValueError(f"Bab {chapter:02d} soal {number}: empty option")
            kind = "single_choice"
            answer = key_match.group(1)
        else:
            if "KUNCI JAWABAN:" in before:
                raise ValueError(f"Bab {chapter:02d} soal {number}: malformed answer key")
            stem = tidy(before)
            options = []
            kind = "numeric" if chapter == 7 and number == 10 else "essay_manual"
            answer = {"expected": 114000, "tolerance_absolute": 0} if kind == "numeric" else None

        # Locate the heading directly in the original page range; page breaks
        # may occur inside the stem or options, but not within this heading.
        source_page = next(i + 1 for i in range(start, end) if match.group(0) in pages[i])
        questions.append({
            "code": f"SAK-B{chapter:02d}-Q{number:03d}",
            "exam_code": f"SAK-BAB{chapter:02d}",
            "section_code": None,
            "type": kind,
            "title": tidy(match.group(2)),
            "stem": stem,
            "options": options,
            "answer_key": answer,
            "explanation": explanation,
            "legal_reference": legal,
            "points": 10,
            "source_page": source_page,
        })
    return questions


def tryout_questions(pages):
    page127, page128, page129, page130 = (pages[n - 1] for n in (127, 128, 129, 130))
    if not all("SOAL " in p for p in (page127, page128)):
        raise ValueError("Tryout pages do not match expected book layout")
    s1_block = page127.split("SOAL URAIAN 1:", 1)[1]
    first, second = s1_block.split("SOAL HITUNGAN 2:", 1)
    first_title, first_stem = first.split("Bobot: 50 Poin", 1)
    second_title, second_stem = second.split("Bobot: 50 Poin", 1)
    solution = page130.split("PEMBAHASAN SOAL URAIAN 1 (NPKB):", 1)[1]
    first_solution, second_solution = solution.split("PEMBAHASAN SOAL HITUNGAN 2 (KERTAS KERJA PERHITUNGAN):", 1)
    second_solution = second_solution.split("KUNCI JAWABAN SESI 2", 1)[0]
    questions = [
        {"code": f"SAK-TRYOUT-S1-Q{idx:03d}", "exam_code": "SAK-BAB12", "section_code": "S1",
         "type": "essay_manual", "title": tidy(title), "stem": tidy(stem), "options": [],
         "answer_key": None, "explanation": tidy(explanation), "legal_reference": "", "points": 50,
         "source_page": 127}
        for idx, (title, stem, explanation) in enumerate(
            ((first_title, first_stem, first_solution), (second_title, second_stem, second_solution)), 1
        )
    ]

    s2 = page128.split("Paket 30 Soal Pilihan Ganda Terpadu Standar BPPK Kemenkeu", 1)[1] + "\n" + page129
    question_matches = list(re.finditer(r"(?m)^(\d{1,2})\.\s+", s2))
    if [int(m.group(1)) for m in question_matches] != list(range(1, 31)):
        raise ValueError("Tryout S2: expected questions 1..30")
    key_text = page130.split("KUNCI JAWABAN SESI 2 (PILIHAN GANDA 1 - 30):", 1)[1]
    keys = {int(n): letter for n, letter in re.findall(r"(\d{1,2})\.([A-D])\b", key_text)}
    if len(keys) != 30:
        raise ValueError(f"Tryout S2: expected 30 answer keys, got {len(keys)}")
    for j, match in enumerate(question_matches):
        number = int(match.group(1))
        body = s2[match.end():question_matches[j + 1].start() if j < 29 else len(s2)]
        option_matches = list(re.finditer(r"(?:^|\s|\|)\s*([A-D])\.\s+", body))
        if [m.group(1) for m in option_matches] != list("ABCD"):
            raise ValueError(f"Tryout S2 question {number}: invalid options")
        options = [
            {"key": m.group(1), "text": tidy(body[m.end():option_matches[k + 1].start() if k < 3 else len(body)]).strip(" |")}
            for k, m in enumerate(option_matches)
        ]
        questions.append({
            "code": f"SAK-TRYOUT-S2-Q{number:03d}", "exam_code": "SAK-BAB12", "section_code": "S2",
            "type": "single_choice", "title": None, "stem": tidy(body[:option_matches[0].start()]),
            "options": options, "answer_key": keys[number], "explanation": "",
            "legal_reference": "", "points": 1, "source_page": 128 if number <= 15 else 129,
        })
    return questions


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("pdf", type=Path)
    parser.add_argument("output", type=Path)
    args = parser.parse_args()
    pages = [page_text(page) for page in PdfReader(args.pdf).pages]
    questions = [q for chapter in range(1, 12) for q in chapter_questions(pages, chapter)]
    questions.extend(tryout_questions(pages))
    codes = [q["code"] for q in questions]
    if len(codes) != 142 or len(set(codes)) != 142:
        raise ValueError(f"Expected 142 unique question codes, got {len(codes)} / {len(set(codes))}")
    if any(not q["stem"] or (q["type"] == "single_choice" and q["answer_key"] not in [o["key"] for o in q["options"]]) for q in questions):
        raise ValueError("Empty stem or invalid answer key")
    result = {"program_code": "SAK", "source_title": (PdfReader(args.pdf).metadata or {}).get("/Title"),
              "questions": questions}
    args.output.parent.mkdir(parents=True, exist_ok=True)
    args.output.write_text(json.dumps(result, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    print(f"Extracted {len(questions)} items to {args.output}")
    for kind in ("single_choice", "numeric", "essay_manual"):
        print(f"  {kind}: {sum(q['type'] == kind for q in questions)}")


if __name__ == "__main__":
    main()
