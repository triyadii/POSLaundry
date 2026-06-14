# Laundry POS

**Laundry POS** adalah aplikasi Point of Sale (POS) berbasis web untuk usaha **laundry**. Aplikasi ini mencakup seluruh alur operasional laundry — mulai dari penerimaan order di kasir, manajemen layanan & pelanggan, pickup/delivery, pembayaran (tunai maupun online), hingga keuangan dan laporan.

Aplikasi ini berkembang dari basis POS restoran, sehingga sebagian **fitur restoran (legacy)** seperti manajemen menu & meja serta sistem antrian masih tersedia di dalamnya.

---

## Tech Stack

Sebagai konteks, aplikasi dibangun dengan:

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Blade + Alpine.js, Tailwind CSS 4, Vite
- **Database:** MySQL
- **Hak Akses & Audit:** Spatie Laravel Permission, Spatie Laravel Activity Log
- **Pembayaran:** Midtrans (QRIS & transfer bank)
- **Real-time:** Laravel Reverb + Laravel Echo / Pusher
- **Lainnya:** QR Code generator, Yajra DataTables, Laravel Octane

---

## Fitur Aplikasi

### A. Operasional Laundry

- **Manajemen Order / Transaksi** — Penerimaan order laundry melalui modul kasir: input detail item (berat/kuantitas, kondisi barang, foto kondisi), penetapan staf produksi, estimasi waktu selesai, dan catatan khusus.
- **Tracking Status Order** — Setiap perubahan status order (diterima → proses → quality check → siap jemput → dikirim → selesai) tercatat lengkap dengan keterangan, pelaku, dan waktunya.
- **Manajemen Pickup & Delivery** — Pengaturan penjemputan dan pengiriman barang: status pengantaran, koordinat lokasi, foto bukti, dan biaya antar.
- **Manajemen Layanan Laundry** — Pengelolaan layanan beserta kategorinya (cuci kering, cuci basah, setrika, dll.), satuan (kg, pcs, meter, pasang), harga per satuan, estimasi durasi pengerjaan, dan status aktif/nonaktif.
- **Manajemen Pelanggan** — Data pelanggan lengkap (nama, kontak, alamat, koordinat lokasi), status membership (Regular/VIP), dan pencatatan poin loyalitas.

### B. Kasir & Pembayaran

- **Modul Kasir & Pembayaran** — Pemrosesan pembayaran dengan beberapa metode: tunai, pembayaran online (Midtrans), serta DP/cicilan.
- **Integrasi Payment Gateway Midtrans** — Mendukung QRIS dan transfer bank, dengan penanganan webhook untuk pembaruan status pembayaran otomatis.
- **Manajemen Shift Kasir** — Buka/tutup shift per kasir dengan pencatatan kas awal, kas seharusnya, kas aktual, dan rekonsiliasi kas.
- **Promo & Diskon** — Pembuatan dan pengelolaan promo dengan tipe diskon persentase maupun nominal, serta toggle aktif/nonaktif.

### C. Fitur Restoran (Legacy)

- **Manajemen Menu** — CRUD menu makanan/minuman beserta harga, kategori, dan status ketersediaan.
- **Manajemen Meja & QR Code** — Daftar meja beserta statusnya, lengkap dengan pembuatan dan pencetakan QR Code per meja.
- **Queue Management (Antrian)** — Kiosk pengambilan nomor antrian, layar TV display, dan pemanggilan antrian secara real-time.
- **Customer Order via QR** — Pelanggan dapat memindai QR Code, memilih menu/layanan, memasukkan ke keranjang, dan checkout secara mandiri.

### D. Keuangan & Laporan

- **Manajemen Pengeluaran & Anggaran Harian** — Pencatatan pengeluaran (expenses) berkategori serta penetapan dan pemantauan anggaran/target harian.
- **Laporan Penjualan** — Rekap transaksi dengan filter rentang tanggal, total penjualan, pajak, diskon, dan rincian per metode pembayaran.
- **Laporan Barang/Layanan Terjual** — Laporan per item/layanan: kuantitas terjual dan pendapatan, termasuk analisis produk terlaris.
- **Dashboard KPI** — Ringkasan real-time: pendapatan, pengeluaran, laba bersih, jumlah item terjual, layanan terlaris, dan grafik penjualan vs target.

### E. User & Keamanan

- **Manajemen User & Role** — CRUD user dengan kontrol akses berbasis peran (RBAC) menggunakan Spatie Permission.
- **Permission Granular** — Hak akses diatur per modul (kasir, antrian, data master, keuangan, laporan, manajemen user, dll.).
- **Ban / Unban User** — Pemblokiran akses user tertentu beserta middleware penjaga untuk mencegah akses user yang diblokir.
- **Profil & Keamanan Akun** — Pengelolaan profil, unggah avatar, ganti kata sandi, serta riwayat dan manajemen sesi login.
- **Activity Log / Audit Trail** — Pencatatan setiap aktivitas user (buat, ubah, hapus) lengkap dengan IP address dan informasi perangkat.

### F. Pengaturan

- **Konfigurasi Aplikasi** — Pengaturan nama aplikasi, logo, nomor telepon, konfigurasi payment gateway, dan pengaturan tampilan.

---

## Role & Hak Akses

Aplikasi menggunakan sistem peran (RBAC). Gambaran umum peran dan aksesnya:

| Peran | Akses |
|-------|-------|
| **Superadmin** | Akses penuh ke seluruh modul |
| **Admin** | Operasional, data master, keuangan, dan laporan |
| **Kasir** | Order/transaksi, pembayaran, shift, antrian, laporan |

---

## Teknologi Real-time & Khusus

- **Real-time Broadcasting** (Laravel Reverb + Pusher) untuk sistem antrian.
- **QR Code** untuk meja dan order mandiri pelanggan.
- **Midtrans** untuk pembayaran online (QRIS & transfer bank).
- **Activity Log** sebagai audit trail seluruh aktivitas pengguna.
