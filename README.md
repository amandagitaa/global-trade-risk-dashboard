# 🌍 Global Trade Risk Intelligence Platform

**Global Trade Risk Intelligence Platform** adalah aplikasi web berbasis Laravel yang dikembangkan untuk memonitor risiko perdagangan internasional dan rantai pasok (*supply chain*).

Sistem ini mengumpulkan dan menampilkan berbagai informasi pendukung seperti data negara, cuaca, nilai tukar mata uang, indikator ekonomi, berita perdagangan global, data pelabuhan, dan rute pelayaran. Seluruh data ini disajikan dalam satu sistem terpadu untuk membantu pengguna memantau kondisi terkini yang dapat memengaruhi aktivitas perdagangan internasional.

---

## 👥 Fitur User Panel

Fitur-fitur yang tersedia bagi pengguna untuk memantau dan merencanakan aktivitas logistik:

- **Dashboard**: Menampilkan ringkasan kondisi perdagangan global, informasi risiko, rekomendasi, cuaca, mata uang, dan metrik penting lainnya.
- **Countries Monitoring**: Menampilkan daftar negara yang dipantau beserta informasi risikonya saat ini.
- **Country Detail**: Menampilkan profil detail ekonomi dan kondisi cuaca untuk suatu negara tertentu. 
- **Country Comparison**: Memungkinkan pengguna untuk membandingkan beberapa negara secara langsung berdasarkan risiko, indikator ekonomi, cuaca, dan data relevan lainnya.
- **Weather Monitoring**: Informasi terkait kondisi cuaca yang berpotensi menghambat logistik.
- **Currency Monitoring**: Visualisasi fluktuasi nilai tukar mata uang secara *real-time*.
- **Economy Monitoring**: Indikator tren ekonomi global.
- **Global News Intelligence**: Menampilkan berita terkait perdagangan yang dikumpulkan dari sumber eksternal (API) dan diproses menggunakan analisis sentimen serta skor dampak perdagangan. Pada halaman yang sama, terdapat bagian **Official Articles** yang memuat ulasan editorial buatan Admin, sehingga pengguna bisa membedakan antara berita eksternal otomatis dan artikel analisis manual.
- **Trade Planner**: Memungkinkan pengguna memilih pelabuhan asal dan tujuan untuk melihat informasi rute dan pertimbangan perdagangan terkait.
- **Risk Analysis**: Menampilkan perhitungan detail mengenai level risiko.
- **Watch List**: Memungkinkan pengguna untuk menyimpan entitas spesifik (negara, pelabuhan, atau rute) ke dalam daftar pantauan pribadi. 
  *(Alur penggunaan umum: pengguna dapat menelusuri dari **Countries** ➔ melihat **Country Detail** ➔ membandingkan lewat **Compare** ➔ lalu menambahkannya ke **Watch List**).*
- **Reports**: Memungkinkan pengguna melihat pratinjau data laporan dan mengekspornya ke format PDF atau Excel.
- **Profile**: Manajemen profil personal pengguna.

---

## 🛡️ Fitur Admin Panel

Sistem manajemen terpusat bagi Admin untuk mengatur data dan konfigurasi aplikasi:

- **Dashboard Monitoring**: Menampilkan laporan ringkas kondisi sistem.
- **User Management**: Mengatur akun pengguna, hak akses, dan status keanggotaan.
- **Countries Management**: Mengelola data dasar negara yang digunakan dalam aplikasi.
- **Ports Management**: Mengelola master data pelabuhan dunia.
- **News Management**: Mengelola berita yang ditarik secara otomatis dari sumber eksternal sebelum ditampilkan ke pengguna. Modul ini berbeda dengan Articles Management karena bersumber dari integrasi API.
- **Articles Management**: Memungkinkan Admin untuk menulis artikel editorial atau analisis manual. Artikel memiliki status seperti Draft atau Published. Artikel berstatus Published akan tampil secara publik kepada pengguna di menu News (bagian Official Articles), di mana pengguna dapat membaca isi artikel selengkapnya.
- **Risk Configuration**: Mengatur dan menyesuaikan bobot dari masing-masing indikator pembentuk skor risiko.
- **Sentiment Dictionary**: Mengelola daftar kata (kamus) untuk menentukan sentimen kata berita (Positif/Negatif).
- **General Settings**: Pengaturan umum, zona waktu, dan identitas aplikasi.
- **Notification Settings**: Pengaturan pemberitahuan sistem.
- **Profile Management**: Mengatur profil akun Admin.
- **Change Password**: Mengubah kata sandi akun keamanan.

---

## 💻 Teknologi yang Digunakan

Aplikasi ini dikembangkan menggunakan beberapa teknologi dan layanan berikut:

- **Laravel** (Backend Framework)
- **PHP** (Core Language)
- **MySQL** (Database)
- **Bootstrap 5** (Frontend UI Framework)
- **Leaflet.js** (Interactive Mapping)
- **Chart.js** (Data Visualization)
- **REST Countries API** — penyedia data dasar negara.
- **Open-Meteo API** — penyedia data cuaca aktual.
- **World Bank API** — penyedia indikator ekonomi.
- **GNews API** — sumber berita global terkini.
- **DomPDF** (Ekspor PDF)
- **Laravel Excel (Maatwebsite)** (Ekspor Excel)

---

## 🧮 Algoritma Perhitungan Risiko

**Risk Score** merupakan nilai gabungan yang dihitung dari beberapa indikator berikut untuk membantu pengguna mengidentifikasi risiko perdagangan relatif. Ini ditujukan sebagai informasi pendukung keputusan, bukan prediksi yang mutlak:

1. **Weather Risk**: Memperhitungkan data cuaca buruk di titik pelabuhan dan rute transit yang relevan.
2. **Inflation Risk**: Mengukur risiko berdasarkan laju inflasi negara asal atau tujuan.
3. **Currency Risk**: Menghitung fluktuasi nilai tukar mata uang lokal terhadap standar deviasi global.
4. **News Sentiment**: Menggunakan indeks sentimen (positif atau negatif) dari berita terkini yang dikumpulkan.
5. **Trade Impact Score**: Memperkirakan dampak berita yang spesifik terhadap suplai, permintaan, dan operasional rantai pasok.

> 💡 **Catatan**: Admin dapat menyesuaikan bobot dari masing-masing indikator ini melalui menu **Risk Configuration** agar skor risiko tetap relevan.

---

## 🚀 Instalasi & Konfigurasi

Ikuti panduan berikut untuk memasang aplikasi ini di *local server* Anda.

### 1. Clone Repository
```bash
git clone https://github.com/amandagitaa/global-trade-risk-dashboard.git
```

### 2. Install Dependency
```bash
composer install
```

### 3. Konfigurasi File .env
Gandakan file konfigurasi lingkungan dan sesuaikan koneksi database Anda di dalamnya.
```bash
cp .env.example .env
```
Buka file `.env` dan pastikan konfigurasi koneksi database Anda sudah benar. Selain itu, fitur API eksternal (seperti pengambilan berita) memerlukan API Key yang valid, maka lengkapi kredensial tersebut:
```env
NEWS_API_KEY=your_api_key_here
```
Setelah itu, generate application key:
```bash
php artisan key:generate
```

### 4. Jalankan Migration dan Seeder
Pastikan Anda telah membuat database MySQL yang sesuai di file `.env`, lalu jalankan perintah ini untuk membangun tabel dan memuat data awal yang diperlukan aplikasi:
```bash
php artisan migrate
php artisan db:seed
```

### 5. Install Dependency Frontend
```bash
npm install
npm run dev
```

### 6. Menjalankan Development Server
```bash
php artisan serve
```

---

## 📁 Struktur Project

Gambaran garis besar struktur direktori pada project ini:

```text
/
├── app/                  # Model, controller, service, middleware, dan logika aplikasi
├── bootstrap/            # Script untuk proses booting framework
├── config/               # File konfigurasi aplikasi
├── database/             # Migrations dan seeders database
├── public/               # Titik awal eksekusi (entry point) dan aset statis (CSS/JS/Gambar)
├── resources/            # Blade views dan resource frontend lainnya
├── routes/               # Deklarasi penjaluran (routes) aplikasi
└── storage/              # File logs, cache, sessions, dan file-file yang di-generate aplikasi
```

---

## 🗺️ Roadmap Pengembangan Masa Depan

Daftar rencana pengembangan fitur untuk sistem ini:

- [x] **Trade Recommendation**: Mesin rekomendasi rute substitusi berbasis perhitungan algoritma dan indikator risiko (Trade Planner).
- [ ] **Advanced Analytics**: Modul analisis silang indikator lanjutan.
- [ ] **Historical Risk Trends**: Grafik proyeksi dan perbandingan tren risiko jangka panjang.
- [ ] **Email Notification**: Notifikasi atau pemberitahuan *alert* sistem ke email pengguna.
- [x] **Export Enhancement**: Dukungan unduhan *report* laporan (CSV/PDF/Excel) yang komprehensif untuk semua modul.

---

## 👤 Author

Nama: Amanda Gita Syafitri