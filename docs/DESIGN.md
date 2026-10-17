# Arah desain CBT GAEKS

## Identitas

CBT dibaca sebagai meja ujian digital yang terhubung langsung ke pembelian buku GDP. Bahasa visualnya mengambil karakter berkas kepabeanan: nomor dokumen, garis pembagi tegas, blok informasi yang dapat dipindai, dan cap status yang hanya muncul saat membawa makna.

Dial: ENERGY 2 / RHYTHM 3 / MOTION 1.

## Keputusan utama

- Warna hijau tinta `#062F33` menjadi identitas institusional; kertas hangat `#F3EFE5` mengurangi kesan dashboard generik; teal `#0D7F88` hanya untuk fokus dan progres; oker aksesibel `#9A5D0B` menjadi aksen pada informasi akses atau waktu.
- IBM Plex Sans dipakai karena bentuk hurufnya terasa dokumenter dan tetap terbaca pada teks soal; IBM Plex Mono dibatasi untuk Order ID, nomor berkas, dan timer agar data transaksi mudah dibedakan dari narasi.
- Landing memakai komposisi asimetris antara penjelasan dan berkas akses. Perbedaan ukuran ini menempatkan keputusan utama, yaitu masuk dengan Order ID dan token buku, sebagai fokus.
- Motif garis dokumen serta nomor bagian diulang pada hero, program, panduan, dan hasil agar produk tetap dikenali tanpa logo tambahan atau ilustrasi dekoratif.
- Kartu hanya dipakai untuk unit yang memang berdiri sendiri: form akses, paket ujian, hasil, dan soal. Informasi pendukung memakai daftar atau jalur proses agar halaman tidak berubah menjadi kumpulan kartu seragam.
- Bayangan hanya menandai permukaan yang menerima tindakan. Panel informasional tetap datar dengan garis tegas.
- Gerak dibatasi pada hover, fokus, perubahan status simpan, dan dialog. Ujian harus tenang dan tidak mengalihkan perhatian.

## Struktur informasi

- Beranda menjawab empat hal: apa yang dapat dikerjakan, program apa yang aktif, bagaimana pembelian buku membuka ujian, dan data nyata apa yang tersedia.
- Halaman SAK menempatkan form Order ID serta token buku lebih awal, lalu menjelaskan sumber kedua data dan alasan penolakan akses.
- Pemain ujian selalu memprioritaskan soal, waktu, status simpan, progres, dan navigator dalam urutan tersebut.
- Riwayat memakai Order ID sebagai identitas agregasi. Semua attempt lama tetap disimpan ketika pengguna mengulang.

## Responsif dan aksesibilitas

- Mobile adalah susunan satu kolom dengan urutan keputusan yang sama, bukan desktop yang diperkecil.
- Tablet memakai dua kolom hanya ketika teks dan kontrol memiliki ruang yang cukup.
- Semua target interaksi minimum 44 px, fokus terlihat, status tidak bergantung pada warna, dan teks normal memenuhi rasio kontras 4.5:1.
- Tabel hasil memiliki pembungkus gulir horizontal; halaman secara keseluruhan tidak boleh bergulir horizontal.
