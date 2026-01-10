# Perubahan Form Reservasi: Jam Mulai & Jam Selesai

## 📋 Ringkasan Perubahan

### **Perubahan UI/UX:**
**Sebelum:**
- User memilih **durasi** (1-8 jam)
- Sistem menghitung jam selesai otomatis

**Sesudah:**
- User memilih **jam mulai** (09:00 - 22:00)
- User memilih **jam selesai** (berdasarkan jam mulai)
- Sistem menghitung **durasi otomatis**

---

## 🔧 Perubahan Backend

### **1. TableController.php**

#### **Validasi Diubah:**
```php
// SEBELUM
'duration_hours' => 'required|integer|min:1|max:8',

// SESUDAH
'duration_hours' => 'required|numeric|min:0.5|max:8',
```

**Alasan:**
- Frontend menghitung durasi dari jam mulai dan jam selesai
- Durasi bisa berupa decimal (contoh: 1.5 jam, 2.5 jam)
- Minimal 0.5 jam (30 menit)
- Maksimal 8 jam

---

### **2. ReservationController.php**

#### **Validasi Diubah:**
```php
// SEBELUM
'duration_hours' => 'required|integer|min:1|max:8',

// SESUDAH
'duration_hours' => 'required|numeric|min:0.5|max:8',
```

**Alasan:** Sama dengan TableController

---

### **3. Database Migration**

#### **Kolom `duration_hours` Diubah:**

**File:** `2026_01_10_132432_change_duration_hours_to_decimal_in_reservations_table.php`

```php
// SEBELUM (integer)
$table->integer('duration_hours')->default(2);

// SESUDAH (decimal)
$table->decimal('duration_hours', 3, 1)->default(2);
```

**Penjelasan:**
- `decimal(3, 1)` = 3 digit total, 1 digit desimal
- Contoh nilai yang bisa disimpan: `0.5`, `1.0`, `1.5`, `2.0`, `2.5`, ... `8.0`
- Range: 0.0 - 99.9 (tapi validasi membatasi 0.5 - 8.0)

---

## 📊 Contoh Perhitungan Durasi

### **Frontend Logic:**

```typescript
// File: ReservationPage.tsx (line 89-97)
const calculateDuration = (start: string, end: string): number => {
  if (!start || !end) return 0;
  const [startH, startM] = start.split(':').map(Number);
  const [endH, endM] = end.split(':').map(Number);
  const startMinutes = startH * 60 + startM;
  const endMinutes = endH * 60 + endM;
  const durationMinutes = endMinutes - startMinutes;
  return durationMinutes / 60; // Convert to hours
};
```

### **Contoh Perhitungan:**

| Jam Mulai | Jam Selesai | Durasi (jam) | Durasi (decimal) |
|-----------|-------------|--------------|------------------|
| 09:00 | 10:00 | 1 jam | 1.0 |
| 09:00 | 10:30 | 1.5 jam | 1.5 |
| 09:00 | 11:00 | 2 jam | 2.0 |
| 09:30 | 12:00 | 2.5 jam | 2.5 |
| 14:00 | 17:30 | 3.5 jam | 3.5 |
| 18:00 | 22:00 | 4 jam | 4.0 |

---

## 🔍 Flow Lengkap

### **1. User Memilih Jam Mulai**
```
User pilih: 14:00
Frontend: setStartTime("14:00")
Frontend: Reset endTime ke ""
```

### **2. User Memilih Jam Selesai**
```
User pilih: 16:30
Frontend: setEndTime("16:30")
Frontend: calculateDuration("14:00", "16:30")
         = (16*60 + 30) - (14*60 + 0)
         = 990 - 840
         = 150 menit
         = 2.5 jam
Frontend: setDuration(2.5)
```

### **3. User Cek Ketersediaan Meja**
```
API Request:
POST /api/tables/check-availability
{
  "reservation_date": "2026-01-11",
  "reservation_time": "14:00",  // jam mulai
  "table_type_id": 1,
  "number_of_people": 4,
  "duration_hours": 2.5  // decimal!
}

Backend Validation:
✅ duration_hours: 2.5 (numeric, min:0.5, max:8)

Backend Calculation:
startDateTime = 2026-01-11 14:00:00
endDateTime = 2026-01-11 16:30:00 (14:00 + 2.5 jam)
```

### **4. User Submit Reservasi**
```
API Request:
POST /api/reservations
FormData:
- customer_name: "John Doe"
- reservation_date: "2026-01-11"
- reservation_time: "14:00"  // jam mulai
- duration_hours: 2.5  // decimal!
- payment_proof: [file]
- ...

Database Insert:
reservations table:
- reservation_time: "14:00:00"
- duration_hours: 2.5  // decimal(3,1)
```

---

## 🧪 Test Cases

### **Test 1: Durasi 30 Menit (0.5 jam)**
```
Input:
- Jam Mulai: 09:00
- Jam Selesai: 09:30
- Durasi: 0.5

Expected:
✅ Validasi pass (min: 0.5)
✅ Tersimpan di database: 0.5
```

### **Test 2: Durasi 1.5 Jam**
```
Input:
- Jam Mulai: 14:00
- Jam Selesai: 15:30
- Durasi: 1.5

Expected:
✅ Validasi pass
✅ Tersimpan di database: 1.5
```

### **Test 3: Durasi Maksimal (8 Jam)**
```
Input:
- Jam Mulai: 10:00
- Jam Selesai: 18:00
- Durasi: 8.0

Expected:
✅ Validasi pass (max: 8)
✅ Tersimpan di database: 8.0
```

### **Test 4: Durasi Melebihi Maksimal**
```
Input:
- Jam Mulai: 09:00
- Jam Selesai: 18:00
- Durasi: 9.0

Expected:
❌ Validasi gagal: "duration_hours must be at most 8"
```

### **Test 5: Durasi Kurang dari Minimal**
```
Input:
- Jam Mulai: 09:00
- Jam Selesai: 09:15
- Durasi: 0.25

Expected:
❌ Validasi gagal: "duration_hours must be at least 0.5"
```

---

## 📝 Perubahan Database

### **Migration Executed:**
```bash
php artisan migrate

Running migrations:
2026_01_10_132432_change_duration_hours_to_decimal_in_reservations_table ... DONE
```

### **Struktur Kolom:**

**Sebelum:**
```sql
duration_hours INT DEFAULT 2
```

**Sesudah:**
```sql
duration_hours DECIMAL(3,1) DEFAULT 2.0
```

### **Data Existing:**
- Data yang sudah ada (integer) akan otomatis dikonversi ke decimal
- Contoh: `2` → `2.0`, `3` → `3.0`

---

## 🔄 Backward Compatibility

### **API Tetap Kompatibel:**

**Request dengan integer (old):**
```json
{
  "duration_hours": 2
}
```
✅ Masih diterima, akan disimpan sebagai `2.0`

**Request dengan decimal (new):**
```json
{
  "duration_hours": 2.5
}
```
✅ Diterima dan disimpan sebagai `2.5`

---

## 📱 Frontend Changes Summary

### **State Management:**
```typescript
const [startTime, setStartTime] = useState(""); // Jam mulai
const [endTime, setEndTime] = useState("");     // Jam selesai
const [duration, setDuration] = useState(0);    // Durasi (auto-calculated)
```

### **User Flow:**
1. Pilih **Jam Mulai** → Dropdown 09:00 - 22:00 (interval 30 menit)
2. Pilih **Jam Selesai** → Dropdown dinamis berdasarkan jam mulai
3. Durasi **otomatis dihitung** dan ditampilkan
4. Submit dengan `duration_hours` yang sudah dihitung

---

## ✅ Checklist Perubahan

### **Backend:**
- [x] Update `TableController` validation: `numeric` instead of `integer`
- [x] Update `ReservationController` validation: `numeric` instead of `integer`
- [x] Create migration untuk ubah kolom ke `decimal(3,1)`
- [x] Run migration
- [x] Test API dengan decimal values

### **Frontend:**
- [x] Ubah dari input durasi ke jam mulai & jam selesai
- [x] Implementasi `calculateDuration()` function
- [x] Update API request dengan durasi yang dihitung
- [x] Validasi durasi min 0.5, max 8

### **Database:**
- [x] Kolom `duration_hours` sudah `DECIMAL(3,1)`
- [x] Default value tetap `2.0`

---

## 🎯 Kesimpulan

### **Perubahan Utama:**
1. ✅ User sekarang pilih **jam mulai** dan **jam selesai**
2. ✅ Durasi dihitung otomatis oleh sistem
3. ✅ Backend menerima durasi dalam format **decimal** (0.5 - 8.0)
4. ✅ Database menyimpan durasi sebagai **DECIMAL(3,1)**

### **Keuntungan:**
- ✅ UX lebih intuitif (pilih jam selesai lebih natural)
- ✅ Mendukung interval 30 menit (1.5 jam, 2.5 jam, dll)
- ✅ Validasi lebih akurat
- ✅ Backward compatible dengan data existing

---

**Last Updated**: 2026-01-10  
**Migration**: ✅ Completed  
**Status**: ✅ Ready to Use
