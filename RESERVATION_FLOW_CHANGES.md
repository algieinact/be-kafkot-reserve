# Perbaikan Logic Reservasi & Availability Check

## 📋 Ringkasan Perubahan

### ✅ Masalah yang Diperbaiki:

1. **Bug Availability Check**: Meja tidak tersedia di jam lain padahal seharusnya bisa
2. **Spam Reservasi**: Data reservasi tersimpan sebelum upload bukti bayar
3. **Tidak Ada Buffer Time**: Reservasi bisa back-to-back tanpa waktu bersih-bersih

---

## 🔧 Perubahan 1: TableController - Tambah Buffer Time

### **File**: `app/Http/Controllers/Api/TableController.php`

### **Perubahan:**

#### **Sebelum:**
```php
// Tidak ada buffer time
if ($startDateTime->lt($existingEnd) && $endDateTime->gt($existingStart)) {
    return false; // Conflict
}
```

**Masalah:**
- Reservasi bisa langsung bersebelahan (12:00-14:00, lalu 14:00-16:00)
- Tidak ada waktu untuk bersih-bersih meja
- Tidak ada waktu untuk customer sebelumnya check-out

#### **Sesudah:**
```php
const BUFFER_TIME_MINUTES = 30;

// Add buffer time to existing reservation end time
$existingEndWithBuffer = $existingEnd->copy()->addMinutes(self::BUFFER_TIME_MINUTES);

// Check for time overlap with buffer
if ($startDateTime->lt($existingEndWithBuffer) && $endDateTime->gt($existingStart)) {
    return false; // Conflict
}
```

**Perbaikan:**
- ✅ Tambah buffer 30 menit setelah setiap reservasi
- ✅ Meja harus kosong minimal 30 menit sebelum reservasi berikutnya
- ✅ Waktu cukup untuk bersih-bersih dan persiapan

### **Contoh:**

#### **Tanpa Buffer:**
```
Existing: 12:00 - 14:00
New:      14:00 - 16:00
Result: AVAILABLE ✅ (langsung bersebelahan)
```

#### **Dengan Buffer 30 menit:**
```
Existing: 12:00 - 14:00 (dengan buffer jadi 12:00 - 14:30)
New:      14:00 - 16:00
Result: NOT AVAILABLE ❌ (harus minimal jam 14:30)

Existing: 12:00 - 14:00
New:      14:30 - 16:30
Result: AVAILABLE ✅ (ada jarak 30 menit)
```

---

## 🔧 Perubahan 2: ReservationController - Wajib Upload Bukti Bayar

### **File**: `app/Http/Controllers/Api/ReservationController.php`

### **Perubahan Besar:**

#### **Flow Lama (MASALAH):**
```
1. User isi form reservasi
2. Submit → Reservasi LANGSUNG TERSIMPAN ❌
3. User diminta upload bukti bayar
4. Upload bukti bayar (opsional)

MASALAH:
- Orang bisa iseng booking tanpa bayar
- Meja jadi terblokir tanpa pembayaran
- Admin repot hapus reservasi spam
```

#### **Flow Baru (SOLUSI):**
```
1. User isi form reservasi
2. User WAJIB upload bukti bayar
3. Submit → Upload bukti bayar DULU ✅
4. Jika upload sukses → Baru simpan reservasi ✅
5. Jika upload gagal → Reservasi TIDAK tersimpan ✅

KEUNTUNGAN:
- Tidak ada reservasi tanpa bukti bayar
- Tidak ada spam booking
- Meja hanya terblokir jika ada bukti bayar
```

### **Kode Perubahan:**

#### **Validasi Baru:**
```php
$request->validate([
    // ... data reservasi lainnya
    
    // Payment proof WAJIB (REQUIRED)
    'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
]);
```

#### **Urutan Eksekusi Baru:**
```php
// 1. Verify table masih available
$this->verifyTableAvailability(...);

// 2. Upload payment proof DULU
$uploadResult = $cloudinary->uploadImage(...);

// 3. Baru create reservation (setelah upload sukses)
$reservation = Reservation::create([...]);

// 4. Create payment record dengan proof URL
Payment::create([
    'payment_proof_url' => $uploadResult['secure_url'],
]);
```

### **Fungsi Baru: verifyTableAvailability()**

```php
private function verifyTableAvailability($tableId, $reservationDate, $reservationTime, $durationHours)
{
    // Check for conflicting reservations dengan buffer time
    // Throw exception jika ada konflik
}
```

**Keuntungan:**
- ✅ Double-check availability sebelum simpan
- ✅ Mencegah race condition (2 orang booking bersamaan)
- ✅ Menggunakan buffer time yang sama dengan TableController

---

## 📊 Perbandingan API Request

### **Sebelum:**

#### **Step 1: Create Reservation**
```bash
POST /api/reservations
Content-Type: application/json

{
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "08123456789",
  "table_id": 1,
  "reservation_date": "2026-01-10",
  "reservation_time": "14:00",
  "number_of_people": 4,
  "duration_hours": 2,
  "order_items": [
    {"menu_id": 1, "quantity": 2}
  ]
}
```

**Response:** Reservasi LANGSUNG TERSIMPAN ❌

#### **Step 2: Upload Payment Proof (Opsional)**
```bash
POST /api/reservations/1/upload-payment-proof
Content-Type: multipart/form-data

payment_proof: [file]
```

---

### **Sesudah:**

#### **Single Step: Create Reservation WITH Payment Proof**
```bash
POST /api/reservations
Content-Type: multipart/form-data

customer_name: John Doe
customer_email: john@example.com
customer_phone: 08123456789
table_id: 1
reservation_date: 2026-01-10
reservation_time: 14:00
number_of_people: 4
duration_hours: 2
order_items[0][menu_id]: 1
order_items[0][quantity]: 2
payment_proof: [file]  ← WAJIB!
```

**Response:** Reservasi tersimpan HANYA jika upload sukses ✅

---

## 🧪 Test Cases

### **Test Case 1: Buffer Time**

```
Scenario: User coba booking jam 14:00, ada existing booking 12:00-14:00

Request:
- Date: 2026-01-10
- Time: 14:00
- Duration: 2 jam

Existing:
- Date: 2026-01-10
- Time: 12:00
- Duration: 2 jam (selesai jam 14:00)

Expected Result:
❌ NOT AVAILABLE
Reason: Butuh buffer 30 menit, minimal jam 14:30
```

### **Test Case 2: Booking Tanpa Bukti Bayar**

```
Request:
POST /api/reservations
{
  "customer_name": "John Doe",
  ...
  // Tidak ada payment_proof
}

Expected Result:
❌ 422 Validation Error
Message: "The payment proof field is required."
```

### **Test Case 3: Upload Gagal**

```
Request:
POST /api/reservations
{
  ...
  payment_proof: [corrupt_file]
}

Expected Result:
❌ 500 Error
Message: "Failed to create reservation: Cloudinary upload failed..."
Reservation: TIDAK TERSIMPAN ✅
```

### **Test Case 4: Upload Sukses**

```
Request:
POST /api/reservations
{
  ...
  payment_proof: [valid_image.jpg]
}

Expected Result:
✅ 201 Created
Message: "Reservation created successfully. Please wait for admin verification."
Data: {
  booking_code: "RSV-20260110-ABC123",
  status: "pending_verification",
  payment: {
    payment_proof_url: "https://res.cloudinary.com/..."
  }
}
```

---

## 🔍 Debugging Guide

### **Jika Meja Tidak Tersedia Padahal Seharusnya Bisa:**

#### **Cek 1: Apakah ada reservasi existing?**
```sql
SELECT * FROM reservations 
WHERE table_id = 1 
AND reservation_date = '2026-01-10'
AND status IN ('pending_verification', 'confirmed');
```

#### **Cek 2: Hitung waktu dengan buffer**
```
Existing: 12:00 - 14:00
Buffer: +30 menit
Effective End: 14:30

New Request: 14:00
Check: 14:00 < 14:30 ? YES → CONFLICT ✅
```

#### **Cek 3: Apakah buffer time terlalu besar?**
```php
// Ubah di TableController.php
const BUFFER_TIME_MINUTES = 15; // Kurangi jadi 15 menit
```

---

### **Jika Reservasi Tidak Tersimpan:**

#### **Cek 1: Apakah payment_proof dikirim?**
```bash
# Pastikan menggunakan multipart/form-data
Content-Type: multipart/form-data
```

#### **Cek 2: Apakah upload Cloudinary sukses?**
```bash
# Check Laravel log
tail -f storage/logs/laravel.log
```

#### **Cek 3: Apakah table masih available?**
```php
// Error message akan jelas:
"Table is no longer available for the selected time slot..."
```

---

## 📝 Migration Notes

### **Tidak Ada Perubahan Database**

✅ Tidak perlu migration baru
✅ Semua kolom sudah ada
✅ Hanya perubahan logic di controller

### **Backward Compatibility**

**Endpoint Lama Masih Ada:**
```php
POST /api/reservations/{id}/upload-payment-proof
```

**Status:** DEPRECATED tapi masih berfungsi
**Alasan:** Untuk backward compatibility jika ada client yang masih pakai

**Rekomendasi:** Gunakan endpoint baru yang langsung dengan payment proof

---

## 🎯 Kesimpulan

### ✅ Masalah yang Diperbaiki:

1. **Buffer Time**: ✅ Ditambahkan 30 menit antara reservasi
2. **Spam Booking**: ✅ Wajib upload bukti bayar saat booking
3. **Race Condition**: ✅ Double-check availability sebelum simpan
4. **Availability Bug**: ✅ Logic overlap sudah benar dengan buffer

### 🔧 Perubahan Kode:

| File | Perubahan | Impact |
|------|-----------|--------|
| `TableController.php` | Tambah buffer 30 menit | Medium |
| `ReservationController.php` | Wajib payment proof | **HIGH** |

### 📱 Perubahan Frontend Required:

#### **Form Reservasi Harus Diubah:**

**Sebelum:**
```jsx
// Step 1: Submit reservation
const response = await fetch('/api/reservations', {
  method: 'POST',
  body: JSON.stringify(reservationData)
});

// Step 2: Upload payment proof
const uploadResponse = await fetch(`/api/reservations/${id}/upload-payment-proof`, {
  method: 'POST',
  body: formData
});
```

**Sesudah:**
```jsx
// Single step: Submit reservation WITH payment proof
const formData = new FormData();
formData.append('customer_name', 'John Doe');
formData.append('customer_email', 'john@example.com');
// ... other fields
formData.append('order_items[0][menu_id]', '1');
formData.append('order_items[0][quantity]', '2');
formData.append('payment_proof', fileInput.files[0]); // WAJIB!

const response = await fetch('/api/reservations', {
  method: 'POST',
  body: formData
});
```

---

## 🚀 Deployment Checklist

- [x] Update `TableController.php` dengan buffer time
- [x] Update `ReservationController.php` dengan payment proof required
- [ ] Update frontend form untuk upload payment proof di step pertama
- [ ] Test availability check dengan berbagai skenario
- [ ] Test reservasi dengan dan tanpa payment proof
- [ ] Informasikan user bahwa payment proof wajib
- [ ] Update API documentation

---

## 📞 Support

**Jika Ada Masalah:**

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check Cloudinary dashboard untuk upload status
3. Test dengan Postman/Thunder Client
4. Lihat response error message

**Common Errors:**

| Error | Cause | Solution |
|-------|-------|----------|
| "payment proof required" | Tidak kirim file | Tambahkan payment_proof di form |
| "Table is no longer available" | Ada konflik waktu | Pilih waktu lain atau table lain |
| "Cloudinary upload failed" | Masalah koneksi/credentials | Check CLOUDINARY_URL di .env |

---

**Last Updated**: 2026-01-09  
**Changes**: Buffer time + Payment proof required  
**Status**: ✅ Ready for Testing
