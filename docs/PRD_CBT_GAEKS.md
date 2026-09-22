# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## GAEKS CBT Platform — cbt.gaeks.com
**Versi:** 1.0  
**Status:** Build-ready / source of truth untuk Gemini Spark  
**Produk pertama:** `cbt.gaeks.com/sak` — Simulasi Sertifikasi Ahli Kepabeanan  
**Pemilik produk:** GAEKS GROUP / GAEKS Publishing  
**Target deployment:** GitHub → Hostinger melalui Git deployment  
**Bahasa UI awal:** Bahasa Indonesia  
**Prinsip utama:** server-owned data, deterministic exam engine, retry-safe, history-preserving, dan tidak bergantung pada AI untuk penilaian inti.

---

# 1. Ringkasan Produk

`cbt.gaeks.com` adalah platform Computer-Based Test multi-bank-soal milik GAEKS. Platform induk harus dapat menampung banyak program ujian di masa depan. Implementasi pertama adalah:

`https://cbt.gaeks.com/sak`

Program `/sak` dipakai untuk pembelajaran mandiri dan simulasi Ujian Sertifikasi Ahli Kepabeanan.

Peserta wajib masuk menggunakan:
1. **User ID** yang sudah tersedia di database.
2. **Token ujian**.

Token berfungsi sebagai **exam router**. Setelah User ID dan token tervalidasi di server, sistem menentukan paket ujian yang sesuai lalu melakukan redirect ke halaman panduan ujian tersebut.

Platform wajib mendukung:
- opening/welcome;
- guidance/petunjuk;
- contoh penggunaan sebelum ujian;
- bank soal;
- countdown timer;
- progress/milestone;
- autosave jawaban;
- submit otomatis ketika waktu habis;
- result;
- riwayat attempt sebelumnya;
- multiple attempts;
- admin untuk mengelola program, bank soal, token, user, dan hasil;
- arsitektur yang dapat dipakai untuk program CBT lain tanpa membuat ulang core system.

---

# 2. Konteks Materi SAK yang Menjadi Sumber Konten

Materi awal berasal dari buku **“Menguasai Kepabeanan dari Rumah — Edisi Pembelajaran Mandiri dan Test CBT”**.

Struktur awal yang harus didukung sistem:

- Bab 01: Teknik Klasifikasi Barang Berdasarkan BTKI 2022 & Pembuatan NPKB
- Bab 02: Perhitungan Penerimaan Negara & Pemberitahuan Pabean
- Bab 03: Ketentuan dan Konsep dalam Undang-Undang Kepabeanan
- Bab 04: Sistem & Prosedur Ekspor, Impor, serta Rezim Khusus
- Bab 05: Fasilitas Kepabeanan, TPB, KITE, & ROO
- Bab 06: Sistem Klasifikasi Barang Secara Konseptual & Harmonisasi WCO
- Bab 07: Sistem Nilai Pabean Standar WTO
- Bab 08: Pembayaran, Penagihan, Pengembalian, Jaminan Pabean, BTD/BDN/BMN
- Bab 09: Keberatan dan Banding di Bidang Kepabeanan
- Bab 10: Larangan & Pembatasan, INSW, dan HAKI
- Bab 11: Pertukaran Data Elektronik & Arsitektur CEISA 4.0
- Bab 12: Simulasi Tryout Akbar SAK

Materi awal berisi:
- **11 CBT per bab × 10 soal = 110 butir**
- **Tryout akhir = 2 soal Sesi 1 + 30 soal PG Sesi 2 = 32 butir**
- Total record konten awal bila seluruh tryout disimpan sebagai item independen: **142 item**

Catatan: soal yang sama secara konsep tetap boleh menjadi record terpisah apabila merupakan bagian dari paket Tryout Akbar karena paket ujian harus mempunyai snapshot dan scoring sendiri.

---

# 3. Contoh Experience Peserta Sebelum Detail Teknis

## 3.1 Contoh login dan routing

Peserta membuka:

`https://cbt.gaeks.com/sak`

Input:

- User ID: `GSAK_CBT001`
- Token: `SAK-BAB01-BTKI`

Sistem melakukan validasi di server.

Jika benar:

`POST /sak/access`
→ server menemukan token milik Exam Bab 01  
→ membuat candidate session  
→ redirect `303` ke:

`/sak/exam/bab-01-btki/guide`

Tidak boleh menggunakan URL seperti:

`/sak?user_id=...&token=...`

User ID dan token tidak boleh disimpan di query string.

## 3.2 Contoh halaman guidance

Card utama:

**BAB 01 — Teknik Klasifikasi Barang Berdasarkan BTKI 2022 & Pembuatan NPKB**

- Jumlah: 10 soal
- Waktu: 20 menit
- Target evaluasi mandiri: ≥ 65/100
- Attempt sebelumnya: 2
- Best score: 80
- Tombol: **Lihat Contoh**
- Tombol: **Mulai Attempt #3**

## 3.3 Contoh tutorial sebelum start

Sebelum timer dimulai, tampilkan 1–3 pertanyaan demo non-scoring untuk menjelaskan:

- cara memilih jawaban;
- tombol Sebelumnya / Berikutnya;
- tombol Tandai untuk Ditinjau;
- indikator soal sudah/belum dijawab;
- lokasi countdown;
- cara submit;
- penjelasan bahwa refresh halaman tidak menghentikan waktu.

Contoh tutorial **tidak menggunakan soal scored dari bank soal utama**.

## 3.4 Contoh saat ujian

Header sticky:

`BAB 01 | Attempt #3 | 07:42 tersisa`

Progress:

`7 / 10 terjawab`

Question navigator:

`1 ✓  2 ✓  3 ⚑  4 ✓  5 —  6 ✓  7 ✓  8 aktif  9 —  10 —`

Milestone:

- 25% — tercapai
- 50% — tercapai
- 75% — belum
- 100% — belum

## 3.5 Contoh result

**Attempt #3 — Selesai**

- Skor: 80/100
- Status target belajar: Tercapai
- Benar: 8
- Salah: 2
- Tidak dijawab: 0
- Waktu terpakai: 16m 34d
- Best score: 80
- Peningkatan dari attempt sebelumnya: +10

Riwayat:
- Attempt #3 — 80
- Attempt #2 — 70
- Attempt #1 — 60

Tombol:
- **Review hasil**
- **Coba lagi**
- **Kembali ke milestone SAK**

Angka pada contoh ini hanya ilustrasi UI, bukan data peserta nyata.

---

# 4. Sasaran Produk

## 4.1 Sasaran utama

1. Memberikan pengalaman CBT yang stabil, cepat, dan mudah dipahami.
2. Menjadikan token sebagai router paket ujian tanpa mengekspos answer key.
3. Mendukung attempt berulang tanpa menghapus attempt lama.
4. Menampilkan hasil belajar secara historis.
5. Mendukung banyak bank soal/program di `cbt.gaeks.com`.
6. Menjadikan `/sak` hanya satu program di atas core CBT yang sama.
7. Menjaga data authoritative di server/database, bukan browser.
8. Membuat deployment ke Hostinger sederhana dan dapat direproduksi dari GitHub.

## 4.2 Non-goals MVP

Tidak perlu pada MVP:
- proctoring kamera;
- face recognition;
- live video;
- pembayaran;
- sertifikat otomatis;
- leaderboard publik;
- social login;
- AI grading untuk esai;
- websocket/real-time server;
- aplikasi mobile native.

AI **tidak boleh** menjadi komponen wajib agar ujian dapat berjalan atau agar nilai dapat dihitung.

---

# 5. User dan Hak Akses

## 5.1 Candidate

Candidate menggunakan:
- User ID;
- token ujian.

Candidate hanya boleh:
- melihat program/exam yang telah dirouting oleh token valid;
- membuat dan melanjutkan attempt miliknya;
- menjawab;
- submit;
- melihat result dan history miliknya sendiri.

Candidate tidak boleh:
- membaca answer key sebelum submit;
- melihat data candidate lain;
- mengubah score;
- mengubah waktu;
- mengganti exam ID melalui URL untuk membuka paket lain.

## 5.2 Admin

Gunakan login admin terpisah dari candidate login.

Role awal:
- `super_admin`
- `content_admin`
- `reviewer`
- `read_only`

Admin authentication tidak boleh memakai pola User ID CBT + token.

Credential admin wajib berasal dari environment/server provisioning dan **tidak boleh ditaruh di GitHub**.

---

# 6. User ID dan Provisioning 10.000 User

Database wajib dibuat dengan 10.000 User ID awal.

Format yang harus dipertahankan sesuai requirement:

- `GSAK_CBT001`
- `GSAK_CBT002`
- ...
- `GSAK_CBT999`
- `GSAK_CBT1000`
- ...
- `GSAK_CBT10000`

Aturan generator:

`GSAK_CBT` + nomor dengan **minimum padding 3 digit**.

Contoh:
- 1 → `GSAK_CBT001`
- 9 → `GSAK_CBT009`
- 10 → `GSAK_CBT010`
- 999 → `GSAK_CBT999`
- 1000 → `GSAK_CBT1000`
- 10000 → `GSAK_CBT10000`

Requirement:
- `public_id` unique index;
- internal primary key tetap integer/UUID, jangan gunakan `public_id` sebagai relational primary key;
- seeder harus idempotent;
- rerun seeder tidak boleh membuat duplikat;
- proses insert dilakukan batch, bukan 10.000 query individual bila dapat dihindari.

Recommended candidate status:
- `pre_generated`
- `active`
- `suspended`

Untuk MVP, aturan aktivasi harus configurable. Jangan hardcode semua user selalu aktif di controller.

### Catatan keamanan penting

User ID bersifat predictable, sedangkan token bab berasal dari materi. Kombinasi ini cocok sebagai **access gate pembelajaran**, tetapi bukan high-assurance authentication.

Jika pada fase berikutnya konten ingin benar-benar dikunci per pembeli, tambahkan `personal_access_pin` atau one-time activation code tanpa mengubah konsep User ID + token sebagai exam router.

---

# 7. Token dan Routing Ujian

## 7.1 Token seed SAK

Gunakan mapping awal berikut:

| Exam | Token |
|---|---|
| Bab 01 | `SAK-BAB01-BTKI` |
| Bab 02 | `SAK-BAB02-HITUNG` |
| Bab 03 | `SAK-BAB03-UUBC` |
| Bab 04 | `SAK-BAB04-PROS` |
| Bab 05 | `SAK-BAB05-FASTPB` |
| Bab 06 | `SAK-BAB06-WCO` |
| Bab 07 | `SAK-BAB07-WTO` |
| Bab 08 | `SAK-BAB08-KEUBMN` |
| Bab 09 | `SAK-BAB09-BANDING` |
| Bab 10 | `SAK-BAB10-INSW` |
| Bab 11 | `SAK-BAB11-CEISA` |
| Bab 12 | `SAK-BAB12-TRYOUT` |

## 7.2 Normalisasi input

Server:
- trim whitespace;
- uppercase token;
- uppercase User ID;
- jangan memperbaiki typo secara otomatis;
- invalid pair memberikan error generik.

Contoh error:
> User ID atau token tidak valid / tidak aktif. Periksa kembali data akses Anda.

Jangan memberi respons berbeda antara “User ID tidak ditemukan” dan “token salah” karena mempermudah enumerasi account.

## 7.3 Penyimpanan token

Recommended:
- DB menyimpan `token_hash`;
- token dinormalisasi lalu di-HMAC dengan server secret;
- simpan `token_hint` untuk admin, misalnya `SAK-BAB01-****`;
- token plaintext tidak pernah dikirim kembali ke candidate melalui API.

Token bukan answer key dan tidak boleh dipakai sebagai ID database.

---

# 8. Exam Naming Convention

Semua program, exam, question, attempt, dan import harus mempunyai code yang konsisten.

## 8.1 Program

- Code: `SAK`
- Slug: `sak`
- Title: `Sertifikasi Ahli Kepabeanan`

## 8.2 Exam code

Contoh:
- `SAK-BAB01-BTKI`
- `SAK-BAB02-HITUNG`
- `SAK-BAB03-UUBC`
- `SAK-BAB12-TRYOUT`

## 8.3 Exam title

Format:

`SAK • Bab {NN} • {Judul Materi}`

Contoh:

`SAK • Bab 01 • Teknik Klasifikasi Barang Berdasarkan BTKI 2022 & Pembuatan NPKB`

## 8.4 Question code

Format chapter:

`SAK-B{NN}-Q{NNN}`

Contoh:
- `SAK-B01-Q001`
- `SAK-B01-Q010`
- `SAK-B11-Q010`

Format Tryout:

- `SAK-TRYOUT-S1-Q001`
- `SAK-TRYOUT-S1-Q002`
- `SAK-TRYOUT-S2-Q001` s.d. `SAK-TRYOUT-S2-Q030`

Kode tidak berubah walaupun teks soal direvisi. Revisi konten memakai field `version`.

---

# 9. Exam Configuration SAK

## 9.1 CBT Bab 01–11

Default untuk semua bab:
- 10 soal;
- 20 menit;
- skala 0–100;
- target evaluasi mandiri: ≥ 65;
- multiple attempts: allowed;
- attempt limit: unlimited secara default, configurable oleh admin;
- timer tidak dapat pause;
- autosave aktif;
- result history aktif.

Masing-masing item default 10 poin jika paket memang 10 soal.

## 9.2 Tryout Bab 12

Tryout harus merepresentasikan dua sesi:

### Sesi 1
- Uraian & hitungan;
- 2 kasus komprehensif;
- 75 menit;
- total normalized score 0–100;
- NPKB: bobot internal 50 poin;
- hitungan: bobot internal 50 poin.

### Sesi 2
- Pilihan ganda;
- 30 soal;
- 75 menit;
- normalized score 0–100.

### Final scoring

- Sesi 1 berkontribusi 50% dari nilai gabungan.
- Sesi 2 berkontribusi 50%.
- Nilai gabungan minimal 60.
- Nilai masing-masing sesi tidak boleh di bawah 40.

Pseudo rule:

`combined = (session_1_score * 0.5) + (session_2_score * 0.5)`

`passed = combined >= 60 AND session_1_score >= 40 AND session_2_score >= 40`

### Essay grading MVP

Jangan gunakan LLM/AI untuk menentukan nilai resmi.

Tipe scoring:
- PG: otomatis.
- Numeric calculation: otomatis jika input terstruktur dan tolerance ditentukan.
- Essay/NPKB: `manual_review` atau `self_assessment` sesuai mode exam.

Result Tryout harus dapat berstatus:
- `provisional`
- `awaiting_review`
- `final`

Jika Sesi 1 membutuhkan review manual, jangan menampilkan nilai gabungan sebagai “final” sebelum review selesai.

---

# 10. Question Types

Core engine minimal harus mendukung:

1. `single_choice`
2. `multiple_choice`
3. `numeric`
4. `short_text`
5. `essay_manual`
6. `structured_case`

Untuk `structured_case`, satu soal dapat mempunyai beberapa field jawaban, misalnya:
- CIF;
- Bea Masuk;
- PPN;
- PPh;
- persentase kurang bayar;
- nilai denda.

Tiap field dapat mempunyai:
- expected value;
- tolerance;
- point weight.

Answer key dan scoring rules hanya tersedia server-side.

---

# 11. Candidate Experience dan Screen Specification

## 11.1 `cbt.gaeks.com`

Landing platform induk.

Komponen:
- GAEKS CBT branding;
- penjelasan singkat;
- card program;
- card SAK;
- status platform;
- link bantuan.

Jangan hardcode bahwa platform hanya SAK.

## 11.2 `/sak`

Opening SAK.

Urutan:
1. Hero.
2. “Cara menggunakan CBT”.
3. Form User ID + Token.
4. Information strip: token akan menentukan paket ujian.
5. CTA masuk.

Design:
- clean;
- professional;
- tidak terlihat seperti template AI generik;
- whitespace jelas;
- tidak memakai gradient berlebihan;
- tidak memakai floating bubble dekoratif berlebihan.

## 11.3 `/sak/exam/{slug}/guide`

Wajib tampil sebelum start:
- title;
- chapter;
- jumlah soal;
- durasi;
- target;
- tipe soal;
- aturan timer;
- attempt sebelumnya;
- link contoh/tutorial;
- checkbox “Saya memahami petunjuk”;
- CTA Start.

## 11.4 `/sak/exam/{slug}/demo`

Demo non-scoring.
Timer tidak aktif.
Tidak mengambil scored question utama.

## 11.5 `/sak/attempts/{id}`

Exam player.

Desktop:
- main question area;
- right rail question navigator.

Mobile:
- sticky top timer;
- compact progress;
- question navigator dalam drawer/bottom sheet.

Komponen wajib:
- exam name;
- attempt number;
- timer;
- answered count;
- milestone/progress bar;
- question stem;
- options/answer controls;
- Previous;
- Next;
- Mark for Review;
- Submit.

## 11.6 Submit confirmation

Tampilkan:
- answered;
- unanswered;
- marked for review;
- remaining time.

Candidate tetap dapat kembali ke soal selama waktu belum habis.

## 11.7 Result detail

Wajib menampilkan:
- attempt number;
- start/submitted time;
- score;
- pass/target status;
- duration used;
- correct;
- incorrect;
- unanswered;
- category breakdown bila tersedia;
- prior attempts;
- best score;
- latest score;
- retry CTA.

Result review mode configurable:
- `score_only`
- `correct_incorrect`
- `full_explanation`

Untuk pembelajaran per bab, default `full_explanation` setelah submit.
Untuk Tryout Akbar, default dapat dibuat lebih ketat.

## 11.8 Result history

Route:

`/sak/results`

Filter:
- all;
- Bab 01–11;
- Tryout.

Row:
- exam title;
- attempt;
- date;
- score;
- status;
- duration;
- View Result.

Tidak boleh overwrite attempt lama.

---

# 12. Milestone System

Ada dua jenis milestone.

## 12.1 In-exam milestone

Untuk paket 10 soal:
- 25%
- 50%
- 75%
- 100%

Untuk 30 soal:
- 10/30
- 20/30
- 30/30

Milestone berdasarkan progress pengerjaan, bukan score sementara.

Jangan menghitung atau membocorkan live score sebelum submit.

## 12.2 SAK learning milestone

Dashboard `/sak/progress` mengelompokkan progres sesuai kurikulum:

### Milestone A — Klasifikasi & Hitungan
- Bab 01
- Bab 02

### Milestone B — Teknik Kepabeanan
- Bab 03–11

### Milestone C — Tryout Akbar
- Bab 12

Setiap exam card:
- Not Attempted
- In Progress
- Completed
- Target Reached
- Needs Review

Status “Target Reached” Bab 01–11 memakai best score ≥65.
Status final Tryout memakai final scoring rule SAK.

Milestone tidak boleh mengunci token lain kecuali admin mengaktifkan prerequisite rule secara eksplisit.

---

# 13. Timer dan Autosave

Timer adalah fitur kritikal.

## 13.1 Server authoritative timer

Saat Start:
- server set `started_at`;
- server set `expires_at = started_at + duration`.

Frontend hanya menampilkan countdown berdasarkan server timestamps.

Frontend time tidak boleh menjadi sumber kebenaran.

## 13.2 Refresh/reconnect

Jika halaman direfresh:
- attempt yang sama dimuat;
- answer terakhir dimuat dari DB;
- remaining time dihitung dari `expires_at`.

Timer tidak restart.

## 13.3 Time expiry

Ketika waktu mencapai 0:
- frontend meminta finalize;
- backend juga memeriksa expiry pada setiap save/submit;
- jika request datang setelah expiry, backend finalize attempt sebagai `timed_out`.

Jadi walaupun JS dimatikan/manipulasi, waktu tetap ditegakkan server.

## 13.4 Autosave

Autosave:
- saat answer berubah;
- debounce maksimal ±500–1000 ms;
- status kecil: `Menyimpan…` → `Tersimpan`.

Jawaban authoritative disimpan di DB.

LocalStorage boleh dipakai hanya untuk preferensi UI non-authoritative, bukan score, exam state, atau answer source-of-truth.

---

# 14. Attempt Rules

1. Satu user boleh mempunyai banyak attempt terhadap exam yang sama.
2. Hanya satu `in_progress` attempt aktif per user + exam.
3. Membuka tab kedua harus melanjutkan attempt yang sama, bukan membuat attempt baru.
4. Start endpoint harus idempotent.
5. Submit endpoint harus idempotent.
6. Attempt yang sudah `submitted/timed_out` tidak dapat diedit.
7. Retry membuat attempt baru dengan `attempt_no + 1`.
8. Historical result tidak berubah setelah question bank direvisi.

Untuk poin #8, sistem wajib menyimpan snapshot revision/content reference yang digunakan pada attempt tersebut.

---

# 15. Database Model

Gunakan MySQL/MariaDB.

## 15.1 `candidate_users`

Fields utama:
- `id`
- `public_id` UNIQUE
- `name` nullable
- `email` nullable
- `status`
- `activated_at` nullable
- timestamps

## 15.2 `programs`

- `id`
- `code`
- `slug` UNIQUE
- `name`
- `description`
- `is_active`
- timestamps

## 15.3 `question_banks`

- `id`
- `program_id`
- `code`
- `name`
- `version`
- `is_active`

## 15.4 `exams`

- `id`
- `program_id`
- `code` UNIQUE
- `slug`
- `title`
- `description`
- `exam_type`
- `duration_seconds`
- `pass_score`
- `attempt_limit` nullable
- `randomize_questions`
- `randomize_options`
- `result_review_mode`
- `is_active`
- timestamps

Unique:
`program_id + slug`

## 15.5 `exam_tokens`

- `id`
- `exam_id`
- `token_hash` UNIQUE
- `token_hint`
- `is_active`
- `valid_from` nullable
- `valid_until` nullable
- timestamps

## 15.6 `questions`

- `id`
- `question_bank_id`
- `code` UNIQUE
- `version`
- `type`
- `title`
- `stem`
- `points`
- `explanation` nullable
- `legal_reference` nullable
- `metadata_json`
- `is_active`
- timestamps

## 15.7 `question_options`

- `id`
- `question_id`
- `option_key`
- `option_text`
- `is_correct`
- `sort_order`

`is_correct` tidak pernah boleh masuk ke candidate payload sebelum result rules mengizinkan review.

## 15.8 `exam_questions`

- `exam_id`
- `question_id`
- `section_code`
- `sort_order`
- `weight`
- `required`

## 15.9 `attempts`

Gunakan UUID/ULID public identifier.

Fields:
- `id`
- `public_uuid`
- `candidate_user_id`
- `exam_id`
- `attempt_no`
- `status`
- `started_at`
- `expires_at`
- `submitted_at`
- `score_auto`
- `score_manual`
- `score_final`
- `passed`
- `duration_used_seconds`
- `grading_status`
- timestamps

Unique:
`candidate_user_id + exam_id + attempt_no`

## 15.10 `attempt_questions`

Untuk membekukan apa yang benar-benar tampil:

- `attempt_id`
- `question_id`
- `question_version`
- `question_snapshot_json`
- `display_order`
- `option_order_json`
- `section_code`
- `max_points`

Snapshot hanya di server/database.

## 15.11 `attempt_answers`

- `id`
- `attempt_id`
- `attempt_question_id`
- `answer_json`
- `is_marked_review`
- `saved_at`
- `is_correct` nullable
- `awarded_points`
- `reviewer_score` nullable
- `reviewer_note` nullable

## 15.12 `admin_users`

- `id`
- `name`
- `email` UNIQUE
- `password_hash`
- `status`
- timestamps

Role dapat memakai tabel role/permission terpisah.

## 15.13 `audit_logs`

- `id`
- `actor_type`
- `actor_id`
- `action`
- `entity_type`
- `entity_id`
- `before_json`
- `after_json`
- `ip_address`
- `created_at`

Audit minimal untuk:
- question edit;
- token edit;
- result manual review;
- user status edit;
- import.

---

# 16. Content Import SAK

Jangan meminta aplikasi production membaca PDF setiap kali digunakan.

Buat satu canonical import format.

Recommended:
`storage/app/private/import/sak_bank_v1.json`

File private tersebut:
- tidak berada di public web root;
- default tidak dikomit ke public GitHub;
- dapat dipakai oleh CLI/admin import.

Command target:

`php artisan cbt:import-sak storage/app/private/import/sak_bank_v1.json --dry-run`

Jika dry-run lulus:

`php artisan cbt:import-sak storage/app/private/import/sak_bank_v1.json`

## 16.1 Validation import

Import harus gagal seluruhnya bila:
- exam code duplicate;
- question code duplicate;
- token mapping duplicate;
- MCQ tidak mempunyai answer key;
- jumlah options tidak valid;
- points tidak valid;
- exam question count tidak sesuai manifest;
- referensi question tidak ditemukan.

Gunakan DB transaction.

## 16.2 Expected manifest validation

Harus ada:
- 12 exam SAK;
- Bab 01–11 masing-masing 10 soal;
- Bab 12 Sesi 1 = 2 item;
- Bab 12 Sesi 2 = 30 item;
- total awal = 142 item bila Tryout menjadi record terpisah.

Buat command tambahan:

`php artisan cbt:validate-bank SAK`

Output harus eksplisit:
- expected;
- actual;
- missing;
- duplicates;
- invalid answer keys;
- PASS/FAIL.

---

# 17. Admin Console

Base URL:

`/admin`

## 17.1 Dashboard

Cards:
- candidate users;
- active users;
- programs;
- exams;
- total attempts;
- attempts today;
- average score;
- pending manual review.

## 17.2 User manager

Actions:
- search User ID;
- activate;
- suspend;
- attach optional name/email;
- see attempt history.

Bulk:
- CSV export;
- activation import.

## 17.3 Program & Exam manager

Admin dapat:
- create program;
- create exam;
- set duration;
- set target/pass score;
- set attempt limit;
- set review mode;
- activate/deactivate.

## 17.4 Question bank manager

Editor:
- code;
- title;
- question type;
- stem;
- options;
- answer key;
- explanation;
- reference;
- point;
- version.

Preview candidate view.

## 17.5 Token manager

Admin dapat:
- create;
- rotate;
- deactivate;
- set validity period;
- map token ke satu exam.

Plaintext token hanya tampil ketika dibuat jika memang diperlukan; jangan terus menerus expose.

## 17.6 Result/review

Admin:
- search attempt;
- inspect answers;
- grade essay;
- finalize review;
- audit changes.

Manual score edit selalu memerlukan:
- reason;
- actor;
- timestamp.

---

# 18. UI / UX Design System

Visual harus menyatu dengan identitas GAEKS Publishing dan materi.

Recommended base:
- Primary / Oceanic Teal: `#012E34`
- Background: `#F8FAFC`
- Surface: `#FFFFFF`
- Cyan accent: gunakan satu token cyan konsisten dan configurable
- Text primary: very dark teal/charcoal
- Success / warning / danger mengikuti WCAG contrast.

Style:
- executive;
- academic;
- clean;
- modern;
- tidak “AI-looking”;
- thin borders;
- subtle shadows;
- 12–16 px radius;
- iconography sederhana;
- progress visual jelas.

Typography:
- heading tegas;
- body sangat readable;
- min 16px untuk soal;
- line-height nyaman.

Jangan:
- menaruh cyan terang sebagai teks tipis di atas putih;
- memakai neon glow;
- membuat timer hanya dibedakan lewat warna;
- menggunakan animasi berat saat exam.

---

# 19. Responsive dan Accessibility

Target:
- desktop;
- laptop;
- tablet;
- smartphone.

Wajib:
- keyboard navigation;
- visible focus;
- label form;
- error text;
- contrast;
- `aria-live` untuk autosave dan timer warning yang tidak berlebihan;
- tap target minimal layak mobile.

Countdown warning:
- 10 menit;
- 5 menit;
- 1 menit.

Jangan memunculkan modal setiap menit.

---

# 20. Technical Architecture

## 20.1 Recommended stack

Untuk kompatibilitas Hostinger dan menekan risiko deployment:

**Backend**
- PHP 8.3+
- Laravel 12 stable/pinned
- MySQL 8 / MariaDB compatible

**Frontend**
- Laravel Blade
- Tailwind CSS
- Alpine.js secukupnya
- Vite untuk asset build

**Session**
- Laravel secure server session
- database/session driver sesuai hosting

**Testing**
- PHPUnit/Pest feature tests

Alasan:
- tidak memerlukan Redis;
- tidak memerlukan websocket;
- candidate exam state mudah dibuat server-authoritative;
- deployment shared/VPS Hostinger relatif sederhana;
- backend dan frontend tetap satu repo sehingga lebih kecil kemungkinan “AI flop”.

Jika implementasi dimulai ketika versi runtime Hostinger berbeda, pin versi stabil yang benar-benar didukung oleh environment deployment. Jangan mengganti framework di tengah build tanpa ADR/documented decision.

## 20.2 Architecture principles

Controller → Service → Model/Repository.

Business logic penting seperti:
- access token;
- attempt lifecycle;
- timer;
- grading;
- result;
- import

tidak boleh tercecer di Blade/JavaScript.

Recommended services:
- `AccessGateService`
- `ExamRoutingService`
- `AttemptService`
- `TimerService`
- `AnswerService`
- `GradingService`
- `ResultService`
- `QuestionImportService`

---

# 21. Route Contract

Candidate:

- `GET /`
- `GET /sak`
- `POST /sak/access`
- `GET /sak/progress`
- `GET /sak/exam/{exam:slug}/guide`
- `GET /sak/exam/{exam:slug}/demo`
- `POST /sak/exam/{exam:slug}/attempts`
- `GET /sak/attempts/{attempt:public_uuid}`
- `PATCH /sak/attempts/{attempt:public_uuid}/answers/{attemptQuestion}`
- `POST /sak/attempts/{attempt:public_uuid}/submit`
- `GET /sak/results`
- `GET /sak/results/{attempt:public_uuid}`
- `POST /sak/logout`

Admin:
- `/admin/login`
- `/admin`
- `/admin/users`
- `/admin/programs`
- `/admin/exams`
- `/admin/question-banks`
- `/admin/questions`
- `/admin/tokens`
- `/admin/attempts`
- `/admin/reviews`
- `/admin/imports`
- `/admin/audit-logs`

Use route model binding tetapi tetap lakukan authorization policy.

---

# 22. API / Response Contract

Jika Alpine/fetch digunakan, gunakan envelope konsisten:

```json
{
  "success": true,
  "data": {},
  "error": null,
  "timestamp": "ISO-8601"
}
```

Error:

```json
{
  "success": false,
  "data": null,
  "error": {
    "code": "ATTEMPT_EXPIRED",
    "message": "Waktu ujian telah berakhir."
  },
  "timestamp": "ISO-8601"
}
```

Jangan kirim stack trace ke browser production.

---

# 23. Security Requirements

Wajib:
- HTTPS;
- secure + HttpOnly session cookie;
- SameSite;
- CSRF protection;
- server-side validation;
- authorization per attempt;
- rate limit login/access attempts;
- rate limit answer endpoints secara rasional;
- ORM/prepared queries;
- mass-assignment protection;
- secrets di `.env`;
- `.env` di `.gitignore`;
- production debug OFF;
- answer key tidak ada di client payload;
- token tidak ada di query string;
- XSS escaping default;
- Content Security Policy;
- security headers;
- backup database;
- audit admin changes.

### Object access rule

Setiap candidate request ke attempt/result:

`attempt.candidate_user_id` harus sama dengan user session.

ID di URL saja tidak pernah cukup.

### Answer leakage rule

Sebelum exam submitted:
candidate response tidak boleh mengandung:
- `is_correct`;
- answer key;
- hidden correct option;
- scoring rubric rahasia;
- explanation yang membocorkan jawaban.

---

# 24. Scoring Engine

## 24.1 Single choice

`awarded = max_points` bila correct, selain itu 0.

## 24.2 Multiple choice

Default MVP: exact match.
Tidak ada partial credit kecuali exam config menetapkan.

## 24.3 Numeric

Field:
- expected;
- tolerance absolute atau percentage.

## 24.4 Essay

Manual review.

## 24.5 Score normalization

Sistem harus dapat menghitung nilai 0–100 walaupun total raw points berbeda.

`normalized = earned / possible * 100`

Rounding:
- simpan minimal 2 decimal;
- display 2 decimal bila diperlukan;
- jangan melakukan pembulatan prematur di tengah kalkulasi.

---

# 25. Result Integrity dan History

History adalah immutable learning record.

Question edit setelah attempt:
- tidak mengubah score attempt lama;
- tidak mengubah text review attempt lama.

Karena itu attempt menyimpan snapshot.

Result page harus membaca snapshot untuk review historical attempt.

Best score:
- hanya attempt berstatus final;
- provisional/manual-pending tidak ikut best score sampai final.

---

# 26. Randomization

Config per exam.

Default chapter CBT:
- question order: configurable;
- option order: configurable.

Untuk initial release berdasarkan buku, disarankan:
- Bab 01–11: question order fixed lebih dulu agar validasi konten mudah;
- option order boleh fixed pada initial import;
- randomization dinyalakan setelah QA lulus.

Tryout:
- initial release fixed order untuk kesesuaian terhadap materi.

Saat randomization aktif:
- urutan disimpan di `attempt_questions`;
- refresh tidak boleh mengacak ulang.

---

# 27. Analytics Admin

MVP:
- attempts per exam;
- average score;
- target reach rate;
- average completion time;
- most frequently wrong questions;
- pending review.

Phase 2:
- item difficulty;
- distractor analysis;
- score distribution;
- per-cohort analysis.

Tidak perlu leaderboard candidate publik.

---

# 28. Logging dan Observability

Log server:
- access success/fail;
- attempt start;
- answer save failure;
- submit;
- timeout;
- grading;
- admin mutation;
- import.

Jangan log:
- plaintext password;
- full token;
- session cookie;
- sensitive secrets.

Tambahkan request/correlation ID untuk error tracing bila mudah.

---

# 29. Repository Structure

Recommended:

```text
gaeks-cbt/
├─ app/
│  ├─ Http/Controllers/
│  │  ├─ Candidate/
│  │  └─ Admin/
│  ├─ Models/
│  ├─ Policies/
│  └─ Services/CBT/
├─ config/
├─ database/
│  ├─ migrations/
│  ├─ seeders/
│  └─ factories/
├─ docs/
│  ├─ PRD_CBT_GAEKS.md
│  ├─ ARCHITECTURE.md
│  ├─ DATABASE.md
│  ├─ SECURITY.md
│  ├─ CONTENT_IMPORT.md
│  ├─ DEPLOYMENT_HOSTINGER.md
│  └─ TEST_PLAN.md
├─ resources/
│  ├─ views/
│  ├─ css/
│  └─ js/
├─ routes/
├─ storage/
│  └─ app/private/import/
├─ tests/
│  ├─ Feature/
│  └─ Unit/
├─ .env.example
├─ composer.json
├─ package.json
└─ README.md
```

`storage/app/private/import` jangan di-public expose.

---

# 30. GitHub dan Branching

Recommended repo:
`gaeksgroup-hash/gaeks-cbt`

Branches:
- `main` → production-ready
- `develop` → integration
- `feature/*`

Setiap milestone:
1. code;
2. tests;
3. docs;
4. commit;
5. merge setelah acceptance criteria lulus.

Jangan push secret.

`.env.example` berisi placeholder saja.

---

# 31. Hostinger Deployment

Target:
`cbt.gaeks.com`

## 31.1 Environment

Server `.env`:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://cbt.gaeks.com`
- DB credentials
- session config
- admin bootstrap settings/secrets
- token HMAC pepper bila dipakai

## 31.2 Web root

Document root harus menuju Laravel `/public`.

Jangan expose:
- project root;
- `.env`;
- `storage/app/private`;
- composer internals.

## 31.3 Deployment steps

Equivalent process:

1. Git pull/deploy approved commit.
2. `composer install --no-dev --optimize-autoloader`
3. asset build via CI/local/Hostinger supported runner.
4. `php artisan migrate --force`
5. `php artisan config:cache`
6. `php artisan route:cache`
7. `php artisan view:cache`
8. verify storage permissions.
9. smoke test.
10. backup sebelum migration produksi yang berisiko.

Jangan menjalankan `migrate:fresh` di production.

## 31.4 Asset build

Jika Hostinger tidak menyediakan Node build pada deployment runtime:
- build asset di CI;
- deploy hasil build.

Jangan mengubah arsitektur menjadi runtime-Node hanya untuk asset build.

---

# 32. Milestone Pembangunan

Estimasi untuk 1 developer dengan AI-assisted coding, bukan janji durasi final.

## M0 — Foundation & Docs
**Estimasi:** 0.5–1 hari

Deliverables:
- repo;
- Laravel boot;
- docs;
- env example;
- CI basic;
- health page.

Gate:
- local boot clean;
- production config documented.

## M1 — Database + 10.000 User + Program/Exam/Token
**Estimasi:** 1–2 hari

Deliverables:
- migrations;
- idempotent user seeder;
- SAK program;
- 12 exam records;
- token mapping.

Gate:
- exact 10.000 unique users;
- no duplicate exam/token;
- tests pass.

## M2 — Access Gate + Opening + Guidance + Demo
**Estimasi:** 1–2 hari

Deliverables:
- `/sak`;
- User ID + token;
- routing;
- guide;
- demo.

Gate:
- valid token routes correct exam;
- invalid pair does not leak which field is wrong.

## M3 — Exam Engine + Timer + Autosave
**Estimasi:** 2–3 hari

Deliverables:
- attempt lifecycle;
- player;
- server timer;
- autosave;
- question navigation;
- milestone;
- timeout.

Gate:
- refresh preserves time/answers;
- answer keys absent from pre-submit payload.

## M4 — Scoring + Result + History + Retry
**Estimasi:** 1–2 hari

Deliverables:
- grading;
- result;
- history;
- attempt retry;
- best/latest score.

Gate:
- retry creates new row;
- old result unchanged.

## M5 — Admin + Question Bank + Import
**Estimasi:** 2–3 hari

Deliverables:
- admin;
- user manager;
- question editor;
- token manager;
- import validator;
- SAK canonical import.

Gate:
- 12 exam manifest passes;
- 110 chapter questions + 32 tryout items accounted for.

## M6 — Tryout Sesi 1/2 + Manual Review
**Estimasi:** 1–2 hari

Deliverables:
- two-session engine;
- 75 min each;
- manual essay review;
- final combined scoring.

Gate:
- 50/50 formula;
- min 40 per session;
- combined min 60.

## M7 — Security, QA, Hostinger Production
**Estimasi:** 1–2 hari

Deliverables:
- security hardening;
- regression tests;
- deploy docs;
- backup/rollback;
- smoke test production.

Total working estimate:
**±10–15 working days** tergantung content cleanup/review dan Hostinger environment.

---

# 33. Acceptance Criteria

Release tidak boleh dianggap selesai sebelum semua berikut PASS.

## Access
- [ ] `GSAK_CBT001` valid bila status mengizinkan.
- [ ] `GSAK_CBT10000` tersedia.
- [ ] Tepat 10.000 User ID unique.
- [ ] Valid User ID + Bab 01 token route ke Bab 01.
- [ ] Invalid pair ditolak.
- [ ] Token tidak muncul di URL.

## Exam
- [ ] Bab 01–11 mempunyai config 10 soal/20 menit/target 65.
- [ ] Start membuat attempt.
- [ ] Second tab tidak membuat attempt kedua.
- [ ] Refresh tidak reset timer.
- [ ] Refresh tidak menghapus answer.
- [ ] Timer expired menjadi timed out.
- [ ] Jawaban tidak bisa diubah setelah final.

## Result
- [ ] Score benar.
- [ ] History menampilkan attempt lama.
- [ ] Retry membuat attempt baru.
- [ ] Best score dihitung benar.
- [ ] Candidate tidak bisa membaca result candidate lain.

## Tryout
- [ ] Sesi 1 75 menit.
- [ ] Sesi 2 75 menit.
- [ ] 2 item Sesi 1.
- [ ] 30 PG Sesi 2.
- [ ] Formula final 50/50.
- [ ] Session score <40 membuat final tidak memenuhi passing rule walaupun combined >=60.
- [ ] Essay manual review tidak dipalsukan sebagai score otomatis.

## Security
- [ ] No answer key in page source/API pre-submit.
- [ ] CSRF aktif.
- [ ] Rate limit access gate.
- [ ] Production debug off.
- [ ] `.env` tidak ada di Git.
- [ ] Authorization test terhadap IDOR lulus.

## Deployment
- [ ] `/health`/equivalent OK.
- [ ] migration berhasil.
- [ ] cache build berhasil.
- [ ] public root benar.
- [ ] HTTPS bekerja.
- [ ] smoke test candidate + admin lulus.

---

# 34. Required Automated Tests

Minimal feature tests:

1. `UserSeederCreatesExactly10000Users`
2. `UserIdFormatIsCorrect`
3. `ValidAccessRedirectsToMappedExam`
4. `InvalidAccessReturnsGenericError`
5. `TokenCannotOpenDifferentExam`
6. `CandidateCannotReadAnotherUsersAttempt`
7. `StartingExamCreatesOneActiveAttempt`
8. `SecondStartReusesActiveAttempt`
9. `AnswerAutosavePersists`
10. `RefreshDoesNotResetTimer`
11. `ExpiredAttemptRejectsNewAnswers`
12. `ExpiredAttemptFinalizes`
13. `SubmitIsIdempotent`
14. `AnswerKeyNotPresentBeforeSubmission`
15. `RetryCreatesNextAttemptNumber`
16. `HistoryPreservesPreviousAttempts`
17. `OldAttemptUsesQuestionSnapshot`
18. `ChapterScoreNormalizesTo100`
19. `TryoutCombinedScoreUsesFiftyFifty`
20. `TryoutFailsIfAnySessionBelowForty`
21. `AdminQuestionEditCreatesAuditLog`
22. `SakImportDryRunValidatesExpectedCounts`

---

# 35. Anti-AI-Flop Build Rules untuk Gemini Spark

Bagian ini **wajib dibaca Spark sebelum menulis kode**.

1. PRD ini adalah source of truth.
2. Jangan mengganti stack tanpa instruksi eksplisit.
3. Jangan membangun seluruh sistem dalam satu prompt/task.
4. Selesaikan satu milestone secara atomic.
5. Sebelum mengubah code, baca repo saat ini dan sebutkan file yang akan diubah.
6. Jangan menghapus fitur yang sudah PASS hanya untuk menyederhanakan task baru.
7. Jangan membuat mock endpoint lalu menyebut fitur selesai.
8. Jangan menaruh state authoritative di localStorage.
9. Jangan menaruh answer key di JavaScript/HTML/client API.
10. Jangan hardcode credential atau `.env`.
11. Jangan menggunakan `migrate:fresh` pada flow produksi.
12. Migration harus forward-safe dan rollback-aware.
13. Seeder harus idempotent.
14. Import harus transactional.
15. Start/submit harus idempotent.
16. Timer harus server-authoritative.
17. Historical attempt harus immutable.
18. Semua query attempt/result candidate wajib di-authorize dengan session user.
19. Semua error production harus generic kepada user dan detail ke server log.
20. Tidak boleh menyebut task selesai sebelum test dan acceptance criteria task tersebut lulus.
21. Tidak boleh membuat TODO placeholder untuk requirement inti.
22. Jika informasi belum ada, buat konfigurasi/abstraction; jangan mengarang business rule.
23. Untuk essay, jangan mengarang AI grader. Gunakan manual review sampai ada requirement lain.
24. Setelah setiap task, output wajib:
   - ringkasan perubahan;
   - daftar file berubah;
   - migration baru;
   - test yang dijalankan;
   - hasil test;
   - command deploy;
   - risiko/known issue;
   - commit message yang disarankan.
25. Bila test gagal, berhenti dan perbaiki task yang sama. Jangan lanjut milestone berikut.

---

# 36. Urutan Instruksi Spark

Jalankan dalam urutan ini:

### TASK 00 — Repository Baseline
Buat Laravel project, docs, `.env.example`, CI, health check. Jangan buat exam engine.

### TASK 01 — Database Foundation
Buat migration semua core entity, tanpa UI kompleks.

### TASK 02 — Candidate Seeder
Generate tepat 10.000 User ID dan test format.

### TASK 03 — SAK Program/Exam/Token Seeder
Seed 12 exams + 12 token mappings + config.

### TASK 04 — Candidate Access Gate
Implement `/sak`, POST access, secure session, server redirect.

### TASK 05 — Guide + Demo
Implement opening, guide, tutorial/demo non-scored.

### TASK 06 — Attempt Lifecycle
Start/resume/idempotency.

### TASK 07 — Exam Player
Question display, nav, milestone.

### TASK 08 — Timer
Server authoritative countdown + expiry tests.

### TASK 09 — Autosave
Persist answers and mark-for-review.

### TASK 10 — Submit & Objective Grading
Scoring engine.

### TASK 11 — Result & History
Multiple attempts, best/latest.

### TASK 12 — Admin Foundation
Admin auth/roles/dashboard.

### TASK 13 — Question Bank Management
CRUD/version/audit.

### TASK 14 — Content Import
Canonical JSON importer + dry-run + validation.

### TASK 15 — Import SAK Content
Import and validate all initial SAK bank content.

### TASK 16 — Tryout Multi-Session
Sesi 1 and Sesi 2 independent 75-min timers.

### TASK 17 — Manual Essay Review
Reviewer flow and finalization.

### TASK 18 — Security QA
IDOR, CSRF, rate-limit, answer-key leakage, headers.

### TASK 19 — Hostinger Deployment
Production runbook, Git deployment, migration, smoke test.

### TASK 20 — Release Audit
Run full acceptance checklist and create release report.

---

# 37. Definition of Done

Project MVP hanya dianggap DONE jika:

- platform root dapat mendukung program selain SAK;
- `/sak` bekerja;
- 10.000 User ID tersedia;
- 12 token mengarah ke 12 exam yang benar;
- Bab 01–11 dapat dikerjakan dengan timer, autosave, submit, result, history, retry;
- Tryout mendukung dua sesi;
- hasil tidak menimpa history;
- candidate hanya bisa melihat datanya sendiri;
- answer key tidak bocor;
- admin dapat mengelola bank soal;
- import konten tervalidasi;
- automated tests lulus;
- `.env` dan secret tidak ada di Git;
- production Hostinger berjalan HTTPS dengan `APP_DEBUG=false`;
- rollback/backup procedure terdokumentasi.

---

# 38. Future Phase

Setelah MVP stabil:
- per-user activation PIN;
- voucher/purchase entitlement;
- certificate;
- email result;
- cohort/class;
- advanced analytics;
- import CSV/XLSX;
- timed exam schedule;
- question pool/random subset;
- official exam lockdown mode;
- integrity/proctoring optional;
- API integration dengan platform GAEKS lain.

Core schema harus dibuat sekarang agar penambahan tersebut tidak memerlukan rewrite exam engine.
