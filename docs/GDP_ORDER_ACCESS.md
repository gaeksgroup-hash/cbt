# Akses CBT dari transaksi GDP

Tanggal audit: 17 Oktober 2026.

## Temuan produksi

- Checkout GDP aktif dan penyedia pembayaran sudah terkonfigurasi.
- Setelah status transaksi berubah, GDP mengirim event pembayaran ke layanan notifikasi SMM.
- Template email transaksi memuat Order ID pada subjek dan baris `Referensi` di isi HTML maupun teks.
- Katalog publik GDP belum memiliki e-book berstatus terbit. Karena itu belum ada transaksi e-book nyata yang dapat dipakai untuk membuka SAK.
- Database CBT produksi memiliki 10.000 identitas kandidat lama. Identitas tersebut dipertahankan untuk histori, tetapi akses ujian baru memakai Order ID terverifikasi.

## Aturan akses

1. Pembeli menyelesaikan checkout e-book di GDP.
2. Status order dan baris pembelian e-book harus sama-sama `paid`.
3. E-book harus memiliki `cbt_entitlement_key` yang sama dengan `programs.entitlement_key` di CBT.
4. Token buku menentukan paket ujian di dalam program tersebut.
5. CBT menyimpan Order ID sebagai `candidate_users.public_id`, sehingga seluruh attempt dan skor terakumulasi pada transaksi itu.

Order yang belum dibayar, dibatalkan, dikembalikan dananya, tidak ditemukan, atau membeli buku lain ditolak dengan pesan umum yang sama.

## Kontrak server ke server

GDP menyediakan endpoint POST privat `api/cbt-entitlements.php`. CBT mengirim:

```json
{"orderId":"GDP-YYYYMMDDHHMMSS-XXXXXXXX"}
```

Kedua server memakai bearer secret yang sama. Respons berhasil hanya berisi referensi order, status, serta daftar kode akses dan produk. Nama, email, nomor telepon, alamat, dan data pembayaran tidak dikirim ke CBT.

## Pemetaan program SAK

- `programs.entitlement_key`: `SAK`
- `ebooks.cbt_entitlement_key`: `SAK`

Admin GDP memilih kode ini ketika menerbitkan e-book. Satu e-book membuka seluruh paket dalam program SAK, sementara token pada buku memilih bab atau tryout tertentu.

## Urutan rilis

1. Terapkan migration GDP `008_cbt_entitlements.sql`.
2. Pasang endpoint entitlement dan shared secret pada GDP.
3. Terapkan migration CBT untuk `programs.entitlement_key` dan set secret yang sama.
4. Terbitkan e-book kepabeanan di GDP dengan kode `SAK` setelah harga ditentukan pemilik produk.
5. Lakukan transaksi uji berbayar atau sandbox, cek email Order ID, lalu uji token dari buku.

Langkah keempat sengaja tidak diotomatisasi karena katalog masih kosong dan harga buku belum ditetapkan.
