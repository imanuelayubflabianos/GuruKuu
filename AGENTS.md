# Core Agent Instructions & Behavioral Constraints

Aturan permanen untuk setiap prompt dan eksekusi:

1. **Efisiensi Kata & Hemat Limit**:
   - Jangan kebanyakan kata, penjelasan harus sangat ringkas dan langsung ke inti (to the point).
   - Jangan melakukan analisis panjang lebar yang tidak diperlukan di respons akhir.

2. **Tanpa Pengulangan**:
   - Lanjut tanpa mengulang apa yang sudah selesai.
   - Jangan mengulang penjelasan atau mengulang pekerjaan yang sudah berfungsi.

3. **Cepat & Presisi**:
   - Lakukan tindakan dan perbaikan secara cepat, efisien, dan tuntas tanpa menunggu lama.
   - Sesuaikan baik dari segi tampilan UI/UX, sistem backend, database, dll sesuai konteks perintah.

4. **Keamanan Kode (Zero Side Effects)**:
   - Kerjakan dengan hati-hati tanpa merusak fitur atau layout yang sudah ada (termasuk mempertahankan tampilan desktop saat memperbaiki mobile).
   - Jangan mengubah sesuatu yang tidak berhubungan dengan perintah yang diminta.

5. **Minimalkan Bug Menyeluruh**:
   - Tuntaskan bug hingga tidak ada yang tersisa sehingga sistem benar-benar jadi dan stabil.

6. **Wajib Rujuk & Sinkronkan Dokumentasi Sistem**:
   - Selalu baca dan patuhi [GURUKUU_SYSTEM_DOCUMENTATION.md](file:///c:/Users/ADVAN/PROJEK/Laravel/GuruKuu/GURUKUU_SYSTEM_DOCUMENTATION.md) sebelum menganalisis atau mengubah kode.
   - Setiap kali melakukan penambahan fitur, perubahan alur, rumus, atau perbaikan sistem, AI **WAJIB** memperbarui file dokumentasi tersebut agar informasinya tidak pernah usang bagi AI/pengembang berikutnya.

