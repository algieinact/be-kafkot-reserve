# 📊 DATABASE SCHEMA REVIEW - Kafkot Reserve System

## ✅ SUMMARY PERUBAHAN

### **Status Migrations:**
- ✅ **7 Tables Sudah Sesuai** (tidak perlu perubahan)
- ⚠️ **2 Tables Diperbaiki** (reservations, payments)
- ➕ **1 Table Baru** (bank_accounts)

---

## 📋 DETAIL SCHEMA

### **1. table_types** ✅ SUDAH SESUAI
```sql
CREATE TABLE table_types (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    type_name VARCHAR(255) UNIQUE,        -- "Indoor", "Semi-Outdoor", "Outdoor"
    description TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```
**Keterangan:** Menyimpan jenis-jenis meja yang tersedia di cafe.

---

### **2. tables** ✅ SUDAH SESUAI
```sql
CREATE TABLE tables (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    table_type_id BIGINT,                 -- FK to table_types
    table_number VARCHAR(255) UNIQUE,     -- "T-001", "T-002", etc.
    capacity INT,                         -- Kapasitas orang
    status ENUM('available', 'reserved', 'inactive'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (table_type_id) REFERENCES table_types(id)
);
```
**Keterangan:** Menyimpan data meja-meja yang ada di cafe.

---

### **3. menus** ✅ SUDAH SESUAI
```sql
CREATE TABLE menus (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    menu_name VARCHAR(255),
    description TEXT NULL,
    price DECIMAL(10,2),
    category VARCHAR(255),                -- "drink", "food", "dessert"
    image_url VARCHAR(255) NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```
**Keterangan:** Menyimpan menu makanan dan minuman.

---

### **4. users** ✅ SUDAH SESUAI
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE,
    full_name VARCHAR(255),
    role ENUM('admin', 'staff'),
    email VARCHAR(255) UNIQUE,            -- dari default Laravel
    password VARCHAR(255),                -- dari default Laravel
    email_verified_at TIMESTAMP NULL,     -- dari default Laravel
    remember_token VARCHAR(100) NULL,     -- dari default Laravel
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```
**Keterangan:** Menyimpan data admin dan staff cafe.

---

### **5. reservations** ⚠️ **DIPERBAIKI**
```sql
CREATE TABLE reservations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    booking_code VARCHAR(255) UNIQUE,     -- "RSV-20251229-001"
    customer_name VARCHAR(255),
    customer_email VARCHAR(255),
    customer_phone VARCHAR(255),
    table_id BIGINT,                      -- FK to tables
    reservation_date DATE,
    reservation_time TIME,
    number_of_people INT,
    total_amount DECIMAL(10,2),
    
    -- ✅ UPDATED: Status enum disesuaikan dengan frontend
    status ENUM(
        'pending_verification',           -- Menunggu verifikasi pembayaran
        'confirmed',                      -- Reservasi dikonfirmasi
        'rejected',                       -- Pembayaran ditolak
        'cancelled',                      -- Dibatalkan
        'completed'                       -- Selesai (customer sudah datang)
    ) DEFAULT 'pending_verification',
    
    -- ✅ UPDATED: Renamed dari 'notes' ke 'special_notes'
    special_notes TEXT NULL,
    
    -- Admin verification
    verified_by BIGINT NULL,              -- FK to users
    verified_at TIMESTAMP NULL,
    
    -- ✅ ADDED: Alasan penolakan
    rejection_reason TEXT NULL,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (table_id) REFERENCES tables(id),
    FOREIGN KEY (verified_by) REFERENCES users(id)
);
```

**Perubahan:**
1. ✅ Status enum disesuaikan dengan frontend (`pending_verification`, `rejected`)
2. ✅ `notes` → `special_notes` (sesuai frontend)
3. ✅ Tambah field `rejection_reason`

---

### **6. reservation_items** ✅ SUDAH SESUAI
```sql
CREATE TABLE reservation_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    reservation_id BIGINT,                -- FK to reservations
    menu_id BIGINT,                       -- FK to menus
    quantity INT,
    price_at_order DECIMAL(10,2),         -- Harga saat order (snapshot)
    subtotal DECIMAL(10,2),               -- quantity * price_at_order
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (reservation_id) REFERENCES reservations(id),
    FOREIGN KEY (menu_id) REFERENCES menus(id)
);
```
**Keterangan:** Menyimpan detail menu yang dipesan dalam reservasi.

---

### **7. payments** ⚠️ **DIPERBAIKI**
```sql
CREATE TABLE payments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    reservation_id BIGINT,                -- FK to reservations
    
    -- Payment details
    amount DECIMAL(10,2),
    payment_method ENUM('bank_transfer', 'cash', 'e_wallet') DEFAULT 'bank_transfer',
    
    -- ✅ UPDATED: Simplified payment status
    payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
    
    -- Bank transfer proof
    payment_proof_url VARCHAR(255) NULL,
    
    -- Admin verification (optional)
    verified_by BIGINT NULL,              -- FK to users
    verified_at TIMESTAMP NULL,
    paid_at TIMESTAMP NULL,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id)
);
```

**Perubahan:**
1. ❌ **REMOVED:** `bank_account_number`, `bank_name` (pindah ke table `bank_accounts`)
2. ❌ **REMOVED:** `verification_status`, `rejection_reason` (sudah ada di `reservations`)
3. ✅ **ADDED:** `payment_method` enum
4. ✅ **ADDED:** `payment_status` enum (simplified)
5. ✅ **ADDED:** `paid_at` timestamp

---

### **8. bank_accounts** ➕ **TABLE BARU**
```sql
CREATE TABLE bank_accounts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    bank_name VARCHAR(255),               -- "BCA", "Mandiri", "BNI"
    account_number VARCHAR(255),          -- Nomor rekening cafe
    account_holder_name VARCHAR(255),     -- Nama pemilik rekening
    is_active BOOLEAN DEFAULT TRUE,       -- Aktif/tidak
    is_primary BOOLEAN DEFAULT FALSE,     -- Rekening utama
    notes TEXT NULL,                      -- Catatan tambahan
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Keterangan:** 
- Menyimpan informasi rekening bank cafe untuk menerima pembayaran
- Customer akan transfer ke rekening ini
- Bisa ada multiple rekening, tapi hanya 1 yang `is_primary = true`

---

### **9. email_logs** ✅ SUDAH SESUAI
```sql
CREATE TABLE email_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    recipient_email VARCHAR(255),
    subject VARCHAR(255),
    body TEXT,
    status ENUM('sent', 'failed'),
    error_message TEXT NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```
**Keterangan:** Log untuk tracking email yang dikirim ke customer.

---

## 🔄 RELASI ANTAR TABLE

```
table_types (1) ──< (N) tables
tables (1) ──< (N) reservations
reservations (1) ──< (N) reservation_items
reservations (1) ──< (1) payments
menus (1) ──< (N) reservation_items
users (1) ──< (N) reservations (verified_by)
users (1) ──< (N) payments (verified_by)
```

---

## 📝 CATATAN PENTING

### **Status Flow Reservasi:**
1. **pending_verification** → Customer upload bukti transfer
2. **confirmed** → Admin approve pembayaran
3. **rejected** → Admin reject pembayaran (dengan alasan)
4. **cancelled** → Dibatalkan oleh customer/admin
5. **completed** → Customer sudah datang dan selesai

### **Payment Status:**
- **unpaid** → Belum bayar / menunggu verifikasi
- **paid** → Sudah dibayar dan diverifikasi
- **refunded** → Uang dikembalikan (jika cancelled)

---

## 🚀 LANGKAH SELANJUTNYA

1. **Drop & Re-migrate Database:**
   ```bash
   php artisan migrate:fresh
   ```

2. **Buat Seeder untuk:**
   - `table_types` (Indoor, Semi-Outdoor, Outdoor)
   - `tables` (Sample tables)
   - `menus` (Sample menu items)
   - `bank_accounts` (Rekening cafe)
   - `users` (Admin account)

3. **Update Models:**
   - Sesuaikan fillable fields
   - Tambah relationships
   - Tambah casts untuk enum

4. **Update API Controllers:**
   - Sesuaikan dengan schema baru
   - Handle status transitions
   - Implement payment verification logic

---

## ✅ KESIMPULAN

**Database schema sudah diperbaiki dan siap digunakan!** 

Perubahan utama:
- ✅ Status reservasi sesuai dengan frontend
- ✅ Field names konsisten (special_notes)
- ✅ Payments table lebih sederhana
- ✅ Bank accounts terpisah untuk fleksibilitas
- ✅ Relasi antar table sudah benar

**Total Tables: 9**
- 4 Master tables (table_types, tables, menus, bank_accounts)
- 2 Transaction tables (reservations, payments)
- 1 Detail table (reservation_items)
- 1 System table (users)
- 1 Log table (email_logs)
