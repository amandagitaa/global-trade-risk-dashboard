# 🌍 Global Trade Risk Intelligence Platform

**Global Trade Risk Intelligence Platform** adalah aplikasi berbasis Laravel yang dirancang khusus untuk membantu perusahaan, eksportir, importir, dan analis perdagangan dalam memonitor risiko perdagangan internasional secara *real-time*.

Sistem ini secara pintar mengintegrasikan berbagai sumber data global yang vital seperti cuaca, nilai tukar mata uang, indikator ekonomi makro, berita intelijen global, serta informasi logistik pelabuhan untuk menghasilkan wawasan dan informasi pendukung keputusan yang presisi.

---

## 👥 Fitur User Panel

Antarmuka cerdas yang dirancang untuk pengguna akhir guna menganalisis dan merencanakan strategi logistik:

- **Dashboard**: Tinjauan ringkas skor risiko global terkini.
- **Countries Monitoring**: Pemantauan detail risiko per negara.
- **Country Detail**: Rincian profil ekonomi dan kondisi cuaca negara.
- **Country Comparison**: Pembandingan risiko antar negara secara bersebelahan (*side-by-side*).
- **Weather Monitoring**: Peringatan dini terkait anomali cuaca yang dapat menghambat logistik.
- **Currency Monitoring**: Visualisasi fluktuasi nilai tukar secara *real-time*.
- **Economy Monitoring**: Indikator tren ekonomi global.
- **Global News Intelligence**: Aliran berita perdagangan yang disematkan sistem analisis sentimen AI.
- **Trade Planner**: Perencana rute dan simulasi pelayaran antar pelabuhan.
- **Risk Analysis**: Komputasi komprehensif terkait level risiko.
- **Watch List**: Daftar pantauan instan untuk rute maupun negara favorit.
- **Reports**: Sistem pelaporan intelijen.
- **Profile**: Manajemen profil personal.

---

## 🛡️ Fitur Admin Panel

Sistem manajemen terpusat khusus untuk Administrator dalam mengatur konfigurasi *platform*:

- **Dashboard Monitoring**: Laporan ringkas sistem (*System Health*).
- **User Management** (CRUD User): Pengaturan hak akses (*Role*) dan status keanggotaan pengguna.
- **Countries Management** (CRUD Countries): Pengelolaan data pokok negara yang direferensikan pada sistem.
- **Ports Management** (CRUD Ports): Registri pusat master data pelabuhan dunia.
- **News Management** (News Cache): Moderasi aliran tangkapan berita yang di- *cache* sebelum dirilis ke audiens.
- **Articles Management**: Modul khusus penulisan manual ulasan dan artikel analisis dari pakar/Admin.
- **Risk Configuration**: Sistem pengaturan dan rekalibrasi bobot pembentuk algoritma *Risk Score*.
- **Sentiment Dictionary**: Modifikasi kamus cerdas (*Lexicon Dictionary*) pengklasifikasi sentimen kata berita (Positif/Negatif).
- **General Settings**: Pengaturan umum, zona waktu, dan identitas *platform*.
- **Notification Settings**: Pemantik pengingat (*Alert*) notifikasi sistem.
- **Profile Management**: Konfigurasi identitas Administrator.
- **Change Password**: Modul pengubahan kata sandi akun keamanan yang di- *hash*.

---

## 💻 Teknologi yang Digunakan

Proyek ini dibangun di atas fondasi teknologi mutakhir:

- **Laravel** (Backend Framework)
- **PHP** (Core Language)
- **MySQL** (Database)
- **Bootstrap 5** (Frontend UI Framework)
- **Leaflet.js** (Interactive Mapping)
- **Chart.js** (Data Visualization)
- **REST Countries API** (Data Geografis Negara)
- **Open-Meteo API** (Data Iklim & Cuaca Aktual)
- **World Bank API** (Indikator Ekonomi Mikro/Makro)
- **GNews API** (Arus Berita Global Aktual)

---

## 🧮 Algoritma Perhitungan Risiko

**Risk Score** merupakan nilai kalkulasi dinamis yang dihitung dari peleburan data terpadu berikut:

1. **Weather Risk**: Variabel risiko cuaca di titik pelabuhan dan rute transit.
2. **Inflation Risk**: Laju inflasi negara asal/tujuan.
3. **Currency Risk**: Variabilitas nilai tukar mata uang lokal terhadap standar deviasi global.
4. **News Sentiment**: Indeks sentimen (*Positive/Negative*) yang diekstrak dari analisis berita terkini.

> 💡 **Catatan**: Bobot dari masing-masing indikator dapat dimodifikasi dan dikalibrasi ulang sewaktu-waktu melalui menu **Risk Configuration** pada panel Admin.

---

## 🚀 Instalasi & Konfigurasi

Ikuti panduan berikut untuk memasang platform ini di dalam server lokal Anda.

### 1. Clone Repository
```bash
git clone https://github.com/amandagitaa/global-trade-risk-dashboard.git
```
*(Ganti URL di atas apabila URL repositori berubah)*

### 2. Unduh Dependensi Composer
```bash
composer install
```

### 3. Konfigurasi Lingkungan (*Environment*)
Gandakan fail konfigurasi lingkungan dan sesuaikan parameter pangkalan data Anda di dalamnya.
```bash
cp .env.example .env
```
Lalu, bangkitkan kunci aplikasi:
```bash
php artisan key:generate
```

### 4. Eksekusi Database (Migrasi & Seeder)
Pastikan Anda telah membuat pangkalan data MySQL (contoh: `global_trade`), lalu jalankan perintah ini untuk membangun tabel dan memuat data awal (*Dummy Data*).
```bash
php artisan migrate
php artisan db:seed
```

### 5. Kompilasi Aset Frontend (NPM)
```bash
npm install
npm run dev
```

### 6. Menjalankan Laravel Development Server
```bash
php artisan serve
```

---

## 🔑 Default Login Credential

Gunakan kredensial berikut untuk masuk sebagai Administrator (setelah perintah `db:seed` dijalankan):

- **Email**: `admin@company.com`
- **Password**: `password`

*(Catatan: Kredensial di atas merupakan data dasar/awal. Silakan sesuaikan kembali apabila konfigurasi lokal seeder Anda berbeda).*

---

## 📂 Struktur Project

Gambaran garis besar struktur fail dan direktori kunci pada *platform* ini:

```text
/
├── app/                  # Direktori Model, Controller, Middleware, & algoritma Logika.
├── bootstrap/            # Skrip pemuat awal (*Bootstrapping*) framework Laravel.
├── config/               # Kumpulan konfigurasi aplikasi & sistem integrasi API.
├── database/             # Skema Migrasi, pengaturan Seeder, serta pola basis data.
├── public/               # Titik awal eksekusi, serta penyimpanan aset statis (CSS/JS/Gambar).
├── resources/            # Aset mentah (Sass, Vue/React) dan berkas tampilan Blade (*Views*).
├── routes/               # Deklarasi penjaluran (*Routing*), meliputi web.php & api.php.
└── storage/              # Repositori tembolok (*Cache*), sesi (*Session*), dan pengunggahan berkas.
```

---

## 📸 Screenshot

Berikut adalah visualisasi beberapa antarmuka kunci pada *platform* ini:

### Dashboard User
*(Sediakan tempat untuk Placeholder / Screenshot kelak)*
`![Dashboard User](placeholder-dashboard.png)`

### Countries Monitoring
*(Sediakan tempat untuk Placeholder / Screenshot kelak)*
`![Countries Monitoring](placeholder-countries.png)`

### Trade Planner
*(Sediakan tempat untuk Placeholder / Screenshot kelak)*
`![Trade Planner](placeholder-trade-planner.png)`

### Risk Analysis
*(Sediakan tempat untuk Placeholder / Screenshot kelak)*
`![Risk Analysis](placeholder-risk-analysis.png)`

### Admin Dashboard
*(Sediakan tempat untuk Placeholder / Screenshot kelak)*
`![Admin Dashboard](placeholder-admin-dashboard.png)`

---

## 🗺️ Roadmap Pengembangan Masa Depan

Kami memiliki visi untuk terus mempertajam dan memperluas kapabilitas intelijen pada *platform* ini:

- [ ] **AI Recommendation**: Mesin penasihat otomatis rute substitusi berbasis *Deep Learning*.
- [ ] **Advanced Analytics**: Modul analisis silang parameter tingkat lanjut.
- [ ] **Historical Risk Trends**: Grafik proyeksi dan komparasi tren risiko jangka panjang.
- [ ] **Email Notification**: Lansiran sistem (*Alert*) langsung menuju kotak masuk surel.
- [ ] **Export Enhancement**: Ekspansi dukungan unduhan *Report* (CSV/PDF) yang komprehensif.

---

## 📄 Lisensi

*Global Trade Risk Intelligence Platform* dilisensikan di bawah lisensi [MIT License](https://opensource.org/licenses/MIT). Anda diizinkan menggunakan, menyalin, mengubah, serta mendistribusikan salinan *platform* ini selagi mencantumkan atribusi yang sesuai.

---

## 👤 Author

Nama: Amanda Gita Syafitri
Kelas: A3
