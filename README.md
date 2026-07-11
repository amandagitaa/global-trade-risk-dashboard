# 🌍 Global Trade Risk Intelligence Platform

## 📌 Overview

Global Trade Risk Intelligence Platform adalah aplikasi berbasis web yang dirancang untuk membantu proses pengambilan keputusan dalam aktivitas perdagangan internasional dengan melakukan analisis terhadap berbagai faktor risiko global.

Sistem ini tidak hanya menampilkan data, tetapi mengolah beberapa sumber informasi seperti:

- Kondisi cuaca suatu negara
- Stabilitas nilai tukar mata uang
- Kondisi ekonomi negara
- Informasi berita global
- Risiko pelabuhan dan logistik

Hasil analisis tersebut digunakan oleh **Trade Recommendation Engine** untuk menghasilkan rekomendasi tindakan perdagangan yang lebih informatif.

Project ini dikembangkan menggunakan framework Laravel dengan pendekatan dashboard intelligence system.


---

# 🎯 Project Objective

Tujuan utama dari sistem ini adalah:

1. Membantu eksportir/importir memahami kondisi risiko perdagangan suatu negara.

2. Menyediakan informasi global dalam satu dashboard terintegrasi.

3. Melakukan analisis risiko berdasarkan beberapa indikator.

4. Memberikan rekomendasi keputusan perdagangan berdasarkan hasil perhitungan risiko.

5. Mengurangi ketidakpastian dalam pengambilan keputusan bisnis internasional.


---

# 🚀 Main Features

## 1. Global Dashboard

Dashboard utama menyediakan ringkasan kondisi perdagangan global melalui:

- Total negara yang dipantau
- Distribusi tingkat risiko
- Peta dunia perdagangan
- Informasi cuaca
- Kondisi mata uang
- Berita perdagangan terbaru
- Trade Recommendation Engine


---

# 🌎 World Map Monitoring

Fitur peta dunia digunakan untuk melihat persebaran negara yang dipantau.

Informasi yang ditampilkan:

- Lokasi negara
- Status risiko
- Informasi perdagangan
- Detail negara


Teknologi:

- Leaflet.js
- OpenStreetMap


---

# ⚠️ Risk Analysis Engine

Sistem melakukan perhitungan risiko berdasarkan beberapa komponen:

## Weather Risk

Parameter:

- Temperatur
- Curah hujan
- Kecepatan angin
- Risiko badai


## Port Risk

Parameter:

- Kondisi pelabuhan
- Potensi gangguan logistik


## Currency Risk

Parameter:

- Nilai tukar mata uang
- Persentase perubahan kurs


## Economic Risk

Parameter:

- GDP
- Inflasi
- Kondisi ekonomi negara


## News Risk

Parameter:

- Sentimen berita
- Berita perdagangan global


---

# 🤖 Trade Recommendation Engine

Fitur utama yang membedakan sistem ini dengan dashboard biasa.

Sistem tidak hanya membaca database, tetapi menggabungkan hasil analisis risiko menjadi rekomendasi tindakan perdagangan.

Output rekomendasi:

- Trade Action
- Priority Level
- Recommendation
- Business Reason

---

# 📊 Risk Scoring

Sistem menghasilkan nilai risiko berdasarkan beberapa faktor:



Kategori risiko:

| Score | Risk Level |
|---|---|
| Rendah | Safe |
| Sedang | Stable |
| Tinggi | Alert |
| Sangat Tinggi | Dangerous |
| Kritis | Critical |


---

# 📰 News Intelligence

Sistem menyimpan dan melakukan analisis terhadap berita perdagangan.

Informasi:

- Judul berita
- Sumber berita
- Kategori
- Sentimen berita
- Positive score
- Negative score


Kategori:

- Logistics
- Trade
- Economy


---

# 💱 Currency Monitoring

Monitoring perubahan nilai tukar mata uang.

Data:

- Base Currency
- Target Currency
- Exchange Rate
- Change Percentage


Digunakan untuk melihat potensi perubahan biaya perdagangan.


---

# 🌦 Weather Monitoring

Monitoring kondisi cuaca negara.

Data:

- Temperature
- Rainfall
- Wind Speed
- Storm Risk
- Weather Status


Status:

- Clear
- Rain
- Storm
- Extreme


---

# 🛠 Technology Stack

## Backend

- PHP 8.3
- Laravel 12
- MySQL


## Frontend

- Blade Template
- Bootstrap
- JavaScript
- Leaflet.js
- Chart.js


## Development Environment

- Laragon
- Composer
- Node.js
- NPM


---

# 📂 Project Structure

---

# 🗄 Database Design

Database:


Main Tables:

| Table | Description |
|---|---|
| countries | Data negara |
| ports | Data pelabuhan |
| weather_data | Data cuaca |
| currency_rates | Data kurs |
| economic_data | Data ekonomi |
| news_cache | Data berita |
| risk_scores | Hasil perhitungan risiko |
| risk_histories | Riwayat risiko |
| trade_recommendations | Hasil rekomendasi perdagangan |

---

# ⚙️ Installation Guide

## 1. Clone Repository

```bash
git clone https://github.com/username/global-trade-risk-dashboard.git