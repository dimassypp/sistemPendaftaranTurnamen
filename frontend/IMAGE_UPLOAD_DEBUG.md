# Panduan Upload Gambar dan Debugging

## Perbaikan yang Telah Dilakukan

### 1. **TournamentForm.vue** ✅
- Menambahkan preview gambar sebelum upload
- Menampilkan nama file yang dipilih
- Validasi tipe file (harus gambar)
- Validasi ukuran file (max 5MB)
- Tombol untuk menghapus gambar yang dipilih
- Error messages yang jelas

**Perubahan:**
- File input sekarang tersembunyi dan aktif ketika area "Pilih gambar poster" diklik
- Preview gambar ditampilkan dengan tombol remove
- Validasi real-time dengan pesan error

### 2. **TeamForm.vue** ✅
- Fitur yang sama dengan TournamentForm untuk upload logo tim
- Preview logo sebelum submit
- Validasi file size dan type

### 3. **TournamentCard.vue** ✅
- Menambahkan method `getPosterUrl()` untuk handle berbagai format path gambar
- Menambahkan error handler untuk gambar yang gagal load
- Console warning jika gambar tidak bisa dimuat

**URL Handling:**
```javascript
// Mendukung:
// 1. URL lengkap: http://localhost:8000/storage/...
// 2. Path dengan /: /storage/tournaments/image.jpg
// 3. Path tanpa /: storage/tournaments/image.jpg
```

### 4. **App.vue** ✅
- Menambahkan console logging untuk debug
- `submitTournament()`: log form data dan server response
- `fetchTournaments()`: log data yang diterima dari server

## Debugging Checklist

Jika gambar masih tidak muncul, cek hal berikut:

### 1. **Frontend (Console Browser)**
Buka DevTools (F12) → Console tab:

```javascript
// Lihat apa yang diterima frontend
console.log('Tournaments loaded:', this.tournaments)

// Di TournamentCard, lihat URL yang digunakan
console.log('Poster URL:', this.getPosterUrl())
```

**Yang harus dilihat:**
- Apakah `tournaments` memiliki field `poster_image`?
- Value dari `poster_image` apa? (seharusnya path atau filename)
- URL apa yang di-generate oleh `getPosterUrl()`?

### 2. **Network Tab (DevTools)**
1. Buka DevTools → Network tab
2. Upload gambar turnamen baru
3. Lihat request POST ke `/api/tournaments`:
   - Response body seharusnya berisi `poster_image` field dengan nama file
   - Status code seharusnya 200 atau 201

Contoh response yang benar:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "ML Championship",
    "schedule": "2024-05-20T...",
    "description": "...",
    "poster_image": "tournaments/123/poster.jpg",
    "created_at": "..."
  }
}
```

### 3. **Cek di Backend (Laravel)**
File upload biasanya disimpan di:
```
storage/app/public/tournaments/
```

Pastikan:
- ✅ Folder sudah ada
- ✅ Permission folder sudah benar (755)
- ✅ Symbolic link sudah dibuat:
  ```bash
  php artisan storage:link
  ```
  Seharusnya membuat link di `public/storage` → `storage/app/public`

### 4. **Cek Server Response**
Ketika submit form, lihat di Network tab:
- Request Headers: 
  - `Authorization: Bearer ...` (auth token)
  - Multipart form data dengan file
- Response:
  - `poster_image` field berisi path/nama file

### 5. **Test URL Image Langsung**
Jika backend sudah mengembalikan `poster_image`, coba buka URL di browser:
```
http://localhost:8000/storage/tournaments/123/poster.jpg
```

Jika error 404, berarti:
- File belum disimpan di path yang benar
- Symbolic link belum dibuat

## Contoh Error & Solusi

### ❌ Error: "Ukuran gambar tidak boleh lebih dari 5MB"
**Solusi:** Gunakan gambar yang lebih kecil atau sesuaikan `maxSize` di `handlePosterChange()` method

### ❌ Error: "File harus berupa gambar"
**Solusi:** Pastikan upload file dengan tipe MIME image/* (jpg, png, gif, webp, dll)

### ❌ Error: Gambar tidak muncul meski sudah upload
**Langkah debug:**
1. Lihat di Console: apakah `tournament.poster_image` ada nilai?
2. Lihat Network tab: apa URL image yang di-load?
3. Buka URL tersebut di tab baru → apakah file ada?
4. Cek di backend storage folder

### ❌ Error: CORS Error saat load image
**Solusi:** Backend harus configure CORS, atau gunakan URL yang sama domain (localhost:8000)

## Tips Pengembangan

1. **Untuk test upload tanpa backend:**
   - Preview sudah bekerja di frontend
   - Bisa melihat form data di Network tab

2. **Untuk menambah tipe file yang didukung:**
   Edit di component masing-masing:
   ```vue
   <input accept="image/*,.pdf,.doc" ... />
   ```

3. **Untuk mengubah max file size:**
   Edit value di method `handlePosterChange()`:
   ```javascript
   const maxSize = 10 * 1024 * 1024 // 10MB
   ```

## File yang Diubah

1. ✅ `src/components/TournamentForm.vue` - Add preview & validation
2. ✅ `src/components/TeamForm.vue` - Add preview & validation  
3. ✅ `src/components/TournamentCard.vue` - Add error handling & URL handling
4. ✅ `src/App.vue` - Add debug logging

## Next Steps

Jika masih ada issue:
1. Jalankan dev server: `npm run dev`
2. Buka browser console (F12)
3. Coba upload gambar
4. Share console output dan Network tab response
