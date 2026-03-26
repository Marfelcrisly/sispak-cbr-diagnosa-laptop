# Sispak CBR - Diagnosa Kerusakan Laptop

Sispak CBR adalah aplikasi sistem pakar berbasis web yang dikembangkan untuk membantu proses diagnosis kerusakan laptop menggunakan metode **Case-Based Reasoning (CBR)**.  
Aplikasi ini dibangun sebagai proyek skripsi dan bertujuan untuk memberikan hasil diagnosis awal berdasarkan gejala yang dipilih pengguna, kemudian mencocokkannya dengan basis kasus yang sudah ada di dalam sistem.

## Latar Belakang
Kerusakan laptop sering kali sulit dikenali oleh pengguna awam, terutama ketika gejala yang muncul mirip antara satu kerusakan dengan kerusakan lainnya. Oleh karena itu, dibutuhkan sebuah sistem yang dapat membantu memberikan diagnosis awal secara cepat dan terstruktur.  
Metode **Case-Based Reasoning (CBR)** digunakan karena mampu menyelesaikan masalah baru dengan membandingkannya terhadap kasus-kasus lama yang memiliki kemiripan.

## Tujuan Sistem
- Membantu pengguna melakukan diagnosis awal kerusakan laptop
- Menyediakan rekomendasi kerusakan berdasarkan gejala yang dipilih
- Menerapkan metode CBR dalam proses pencocokan kasus
- Mempermudah admin dan teknisi dalam mengelola basis pengetahuan

## Fitur Utama
- Login multi role: **Admin, Teknisi, User**
- Diagnosa kerusakan laptop berdasarkan gejala
- Perhitungan similarity menggunakan metode CBR
- Menampilkan hasil diagnosis dan kasus terdekat
- Pengelolaan data gejala
- Pengelolaan data kerusakan
- Pengelolaan basis kasus
- Riwayat diagnosa
- Retain case baru jika hasil similarity di bawah threshold
- Pengaturan threshold similarity
- Dashboard statistik
- Export laporan PDF

## Metode yang Digunakan
Metode utama yang digunakan pada sistem ini adalah **Case-Based Reasoning (CBR)**, dengan tahapan:
1. **Retrieve** – mencari kasus lama yang paling mirip dengan kasus baru
2. **Reuse** – menggunakan solusi dari kasus yang paling mirip
3. **Revise** – meninjau kembali hasil solusi
4. **Retain** – menyimpan kasus baru sebagai pengetahuan baru jika diperlukan

Perhitungan similarity dilakukan berdasarkan kecocokan gejala yang dipilih pengguna dengan gejala yang ada pada basis kasus.

## Role Pengguna
### Admin
- Mengelola data gejala
- Mengelola data kerusakan
- Mengelola basis kasus
- Mengatur threshold CBR
- Melihat dashboard statistik
- Melihat riwayat diagnosa
- Mengekspor laporan PDF
- Menyetujui atau menolak retain case

### Teknisi
- Melihat data kasus
- Membantu proses validasi retain case
- Melihat data diagnosa sesuai hak akses

### User
- Memilih gejala
- Melakukan diagnosa
- Melihat hasil diagnosa
- Melihat riwayat diagnosa pribadi

## Teknologi yang Digunakan
- **Framework:** Laravel 10
- **Bahasa Pemrograman:** PHP 8.1+
- **Database:** MySQL
- **Frontend:** Bootstrap 5
- **Visualisasi:** Chart.js
- **Export PDF:** DomPDF

## Struktur Singkat Sistem
Beberapa modul utama dalam aplikasi:
- **Diagnosa**: proses pemilihan gejala dan perhitungan hasil CBR
- **Gejala**: data gejala kerusakan laptop
- **Kerusakan**: data jenis kerusakan hardware/software
- **Basis Kasus**: pengetahuan utama sistem
- **Riwayat Diagnosa**: penyimpanan hasil diagnosa pengguna
- **Retain**: penyimpanan kasus baru yang perlu ditinjau
- **Setting CBR**: pengaturan threshold similarity

## Cara Menjalankan Project
1. Clone repository
   ```bash
   git clone https://github.com/Marfelcrisly/sispak-cbr-diagnosa-laptop.git
