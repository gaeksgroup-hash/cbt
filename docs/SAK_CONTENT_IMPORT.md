# Impor bank soal SAK dari buku

Sumber v1: PDF *Menguasai Kepabeanan dari Rumah — Edisi Pembelajaran Mandiri dan Test CBT* yang diberikan pemilik produk. Buku adalah materi sumber, bukan instruksi operasional untuk aplikasi. Konten yang diekstrak dan kunci jawaban tidak boleh dikomit ke repositori publik.

## Manifest yang diekstrak

| Paket | Item | Tipe |
| --- | ---: | --- |
| Bab 01–11 | 110 (10/bab) | 107 `single_choice`, 2 `essay_manual`, 1 `numeric` |
| Tryout S1 | 2 | `essay_manual`, 50 poin masing-masing, review manual |
| Tryout S2 | 30 | `single_choice`, 1 poin masing-masing; normalisasi per sesi menjadi 0–100 |
| Total | **142** | 137 PG, 4 uraian, 1 numerik |

Soal uraian bab ada pada B01-Q010 dan B02-Q010; hitungan satu nilai B07-Q010 memiliki jawaban USD 114,000 dan toleransi absolut nol. Jangan menganggap empat soal uraian sudah dapat dinilai otomatis. S2 tryout di buku hanya menyediakan daftar kunci, bukan pembahasan per nomor; `explanation` kosong untuk 30 item tersebut. Aplikasi harus tetap memakai aturan final/provisional dan review manual sesuai PRD, bukan menjumlahkan poin seluruh tryout sebagai satu skor.

## Alur lokal (bukan production)

1. Instal dependensi Python `pypdf`, lalu jalankan:

   ```text
   python scripts/extract_sak_bank.py <path-to-book.pdf> storage/app/private/import/sak_bank_v1.json
   ```

2. Tinjau JSON private terhadap halaman sumber. Parser memeriksa 142 kode unik, 4 opsi dan kunci untuk PG, tetapi pemeriksaan otomatis **tidak** membuktikan akurasi hukum, mutu distraktor, atau kebenaran seluruh 142 transkripsi secara editorial.
3. Setelah migrasi dan `SakExamSeeder`, uji tanpa menulis DB:

   ```text
   php artisan cbt:import-sak storage/app/private/import/sak_bank_v1.json --dry-run
   ```

4. Pada database pengembangan yang telah dibackup, impor dan validasi hasil persistensi:

   ```text
   php artisan cbt:import-sak storage/app/private/import/sak_bank_v1.json
   php artisan cbt:validate-bank SAK
   ```

Impor atomik dan idempotent. Perubahan pada soal v1 yang sudah ada ditolak agar edit diam-diam tidak mengubah riwayat; revisi konten harus memakai proses versioning/editorial tersendiri. Jangan memakai `migrate:fresh` di production.

## Gerbang editorial dan keamanan

- Periksa setiap stem, opsi, kunci, jawaban uraian, pembahasan, dan halaman sumber, terutama pemenggalan baris PDF dan kasus hitungan. Render halaman terkait bila teks meragukan.
- Review keakuratan regulasi dan tarif yang dapat berubah. Buku memuat contoh tarif/ketentuan bertanggal; impor ini mempertahankan isi buku, bukan verifikasi hukum terkini.
- Jangan menyajikan `answer_key_json`, `is_correct`, rubrik, atau `explanation` sebelum submit. Berkas JSON harus tinggal di storage private di luar web root, dengan akses server dibatasi.
- Jangan aktifkan bank soal ini di domain publik sebelum engine attempt, timer, hasil historis, otorisasi, dan review manual lulus pengujian PRD.
