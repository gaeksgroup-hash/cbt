# SAK EXAM CATALOG & TOKEN ROUTING SPECIFICATION
## GAEKS CBT Platform — cbt.gaeks.com

**Program Code:** `SAK`  
**Program Slug:** `sak`  
**Program Name:** Sertifikasi Ahli Kepabeanan  
**Source Material:** Buku *“Menguasai Kepabeanan dari Rumah — Edisi Pembelajaran Mandiri dan Test CBT”*  
**Platform Owner:** GAEKS GROUP / GAEKS Publishing  

---

## 1. Catalog Overview

Platform CBT GAEKS memisahkan secara tegas antara **Exam Internal Code** (kode entitas database) dengan **Access Token** (kode akses paket belajar). Token akses tidak pernah digunakan sebagai primary key, slug, atau parameter URL.

| No | Exam Code | Slug | Exam Type | Durasi (Detik / Menit) | Passing Score | Review Mode | Token Hint |
|---|---|---|---|---|---|---|---|
| 01 | `SAK-BAB01` | `bab-01-btki-npkb` | `chapter` | 1.200s (20m) | 65.00 | `full_explanation` | `SAK-BAB01-****` |
| 02 | `SAK-BAB02` | `bab-02-perhitungan-pabean` | `chapter` | 1.200s (20m) | 65.00 | `full_explanation` | `SAK-BAB02-****` |
| 03 | `SAK-BAB03` | `bab-03-undang-undang-kepabeanan` | `chapter` | 1.200s (20m) | 65.00 | `full_explanation` | `SAK-BAB03-****` |
| 04 | `SAK-BAB04` | `bab-04-prosedur-ekspor-impor` | `chapter` | 1.200s (20m) | 65.00 | `full_explanation` | `SAK-BAB04-****` |
| 05 | `SAK-BAB05` | `bab-05-fasilitas-tpb-kite-roo` | `chapter` | 1.200s (20m) | 65.00 | `full_explanation` | `SAK-BAB05-****` |
| 06 | `SAK-BAB06` | `bab-06-klasifikasi-wco` | `chapter` | 1.200s (20m) | 65.00 | `full_explanation` | `SAK-BAB06-****` |
| 07 | `SAK-BAB07` | `bab-07-nilai-pabean-wto` | `chapter` | 1.200s (20m) | 65.00 | `full_explanation` | `SAK-BAB07-****` |
| 08 | `SAK-BAB08` | `bab-08-pembayaran-penagihan` | `chapter` | 1.200s (20m) | 65.00 | `full_explanation` | `SAK-BAB08-****` |
| 09 | `SAK-BAB09` | `bab-09-keberatan-banding` | `chapter` | 1.200s (20m) | 65.00 | `full_explanation` | `SAK-BAB09-****` |
| 10 | `SAK-BAB10` | `bab-10-lartas-insw-haki` | `chapter` | 1.200s (20m) | 65.00 | `full_explanation` | `SAK-BAB10-****` |
| 11 | `SAK-BAB11` | `bab-11-pde-ceisa` | `chapter` | 1.200s (20m) | 65.00 | `full_explanation` | `SAK-BAB11-****` |
| 12 | `SAK-BAB12` | `bab-12-tryout-akbar` | `tryout` | 9.000s (150m)* | 60.00 | `score_only` | `SAK-BAB12-****` |

*\*Catatan Bab 12:* Durasi 9.000 detik merupakan total durasi nominal ujian Tryout (Sesi 1: 75 menit + Sesi 2: 75 menit). Orkestrasi batas waktu dua sesi independen akan diimplementasikan pada task Tryout khusus.

---

## 2. Token Security Model

1. **HMAC-SHA256**: Seluruh token di-hash menggunakan algoritma HMAC-SHA256 dengan server-side pepper (`CBT_TOKEN_PEPPER`).
2. **Tidak Menyimpan Plaintext**: Kolom `exam_tokens.token_hash` menyimpan digest heksadesimal 64 karakter. Kolom `token_hint` hanya menyimpan petunjuk bertopeng (masked).
3. **Router Semantics**: Token berfungsi murni sebagai penunjuk paket ujian (exam router) saat pengerjaan materi pembelajaran.
