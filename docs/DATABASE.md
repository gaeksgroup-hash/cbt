# DATABASE ARCHITECTURE & SCHEMA SPECIFICATION
## GAEKS CBT Platform — cbt.gaeks.com

**Version:** 1.0  
**Phase:** TASK 02 — Database Foundation  
**Source of Truth:** `docs/PRD_CBT_GAEKS.md`  
**Target Engines:** MySQL 8.x / MariaDB compatible (Production: Hostinger) | SQLite in-memory (Automated Tests)

---

## 1. Core Principles
1. **Server-Owned Data**: Seluruh ujian, timer, dan penilaian diatur server.
2. **Immutable Attempt History**: Percobaan ujian tidak ditimpa; retry membuat baris baru.
3. **Question Snapshot Integrity**: Soal dibekukan di `attempt_questions` saat pengerjaan.
4. **Answer-Key Secrecy**: Kunci jawaban disembunyikan dari serialization.
5. **Separation of Public Identifiers**: Menggunakan `public_id` dan `public_uuid`.
6. **One Active Attempt Rule**: Ditegakkan pada layer aplikasi service.
7. **UTC Timestamps**: Datetime diproses dalam UTC.

---

## 2. Table List & Foreign Keys
1. `candidate_users`
2. `programs`
3. `question_banks` (FK: `program_id` -> `programs.id`, RESTRICT)
4. `exams` (FK: `program_id` -> `programs.id`, RESTRICT)
5. `exam_tokens` (FK: `exam_id` -> `exams.id`, CASCADE)
6. `questions` (FK: `question_bank_id` -> `question_banks.id`, RESTRICT)
7. `question_options` (FK: `question_id` -> `questions.id`, CASCADE)
8. `exam_questions` (FK: `exam_id` -> `exams.id` CASCADE, `question_id` -> `questions.id` RESTRICT)
9. `attempts` (FK: `candidate_user_id` -> `candidate_users.id` RESTRICT, `exam_id` -> `exams.id` RESTRICT)
10. `attempt_questions` (FK: `attempt_id` -> `attempts.id` CASCADE, `question_id` -> `questions.id` NULL ON DELETE)
11. `attempt_answers` (FK: `attempt_id` -> `attempts.id` CASCADE, `attempt_question_id` -> `attempt_questions.id` CASCADE)
12. `admin_users`
13. `audit_logs`

---

## 6. Exam Token Security & Routing Architecture

- **Token Storage**: Database **TIDAK PERNAH** menyimpan plaintext token ujian. Kolom `exam_tokens.token_hash` menyimpan digest HMAC-SHA256 sepanjang 64 karakter heksadesimal lowercase.
- **Server-Side Pepper**: Komputasi digest menggunakan secret environment `CBT_TOKEN_PEPPER` yang terisolasi (`hash_hmac('sha256', NORMALIZED_TOKEN, CBT_TOKEN_PEPPER)`).
- **Pepper Stability**: Nilai `CBT_TOKEN_PEPPER` harus stabil antar deployment. Rotasi pepper membutuhkan prosedur rehash terkoordinasi.
- **Access Gate vs Identity**: Token berfungsi eksklusif sebagai *Exam Router* (penentu paket modul bab/tryout yang dituju), bukan sebagai kredensial autentikasi identitas peserta. Identitas peserta diverifikasi secara terpisah melalui `candidate_users.public_id`.
