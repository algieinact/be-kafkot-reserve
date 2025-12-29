# 🚀 Kafkot Reserve - Backend API Implementation

## ✅ Implementasi Lengkap

### 📋 **Perubahan Database**

#### 1. **Migration Baru: `duration_hours`**
```bash
php artisan migrate
```

Menambahkan field `duration_hours` (integer, default: 2) ke table `reservations`:
- User dapat memilih durasi reservasi (1-8 jam)
- Digunakan untuk check availability conflict

#### 2. **Field yang Sudah Disesuaikan**
- ✅ `special_notes` (bukan `notes`)
- ✅ `rejection_reason` untuk tracking alasan penolakan
- ✅ `duration_hours` untuk durasi reservasi

---

## 🔌 **API Endpoints**

### **Public Endpoints** (No Auth Required)

#### **Menu**
```http
GET    /api/menus                    # Get all available menus
GET    /api/menus/{id}               # Get single menu
```

**Query Parameters:**
- `category` (optional): `food`, `drink`, `dessert`, `all`
- `search` (optional): Search by menu_name

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "menu_name": "Espresso",
      "category": "drink",
      "description": "...",
      "price": 25000,
      "image_url": "...",
      "is_available": true
    }
  ]
}
```

---

#### **Table Availability**
```http
POST   /api/tables/check-availability
```

**Request Body:**
```json
{
  "reservation_date": "2025-12-30",
  "reservation_time": "14:00",
  "table_type_id": 1,
  "number_of_people": 4,
  "duration_hours": 2
}
```

**Response:**
```json
{
  "success": true,
  "message": "3 table(s) available",
  "data": [
    {
      "id": 1,
      "table_number": "T-001",
      "table_type_id": 1,
      "capacity": 4,
      "status": "available",
      "table_type": {
        "id": 1,
        "type_name": "Indoor"
      }
    }
  ]
}
```

**Logic:**
- ✅ Check kapasitas meja >= number_of_people
- ✅ Check status meja = 'available'
- ✅ Check conflict dengan reservasi existing (berdasarkan date, time, duration)
- ✅ Return hanya meja yang tidak bentrok

---

#### **Reservation**
```http
POST   /api/reservations             # Create reservation
GET    /api/reservations/{bookingCode}  # Get by booking code
POST   /api/reservations/{id}/upload-payment  # Upload payment proof
```

**Create Reservation:**
```json
{
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "08123456789",
  "table_id": 1,
  "reservation_date": "2025-12-30",
  "reservation_time": "14:00",
  "number_of_people": 4,
  "duration_hours": 2,
  "special_notes": "Window seat please",
  "order_items": [
    {
      "menu_id": 1,
      "quantity": 2
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Reservation created successfully",
  "data": {
    "id": 1,
    "booking_code": "RSV-20251229-ABC123",
    "status": "pending_verification",
    "total_amount": 50000,
    "payment": {
      "id": 1,
      "payment_status": "unpaid"
    }
  }
}
```

---

### **Admin Endpoints** (Requires Auth)

**Header Required:**
```
Authorization: Bearer {token}
```

#### **Dashboard**
```http
GET    /api/admin/dashboard/stats
```

**Response:**
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_reservations": 150,
      "pending_verifications": 5,
      "confirmed_reservations": 120,
      "total_revenue": 15000000
    },
    "reservations_by_status": [...],
    "recent_reservations": [...],
    "upcoming_reservations": [...]
  }
}
```

---

#### **Menu Management**
```http
GET    /api/admin/menus              # List all menus
GET    /api/admin/menus/{id}         # Get single menu
POST   /api/admin/menus              # Create menu
PUT    /api/admin/menus/{id}         # Update menu
DELETE /api/admin/menus/{id}         # Delete menu
PATCH  /api/admin/menus/{id}/toggle-availability  # Toggle availability
```

**Create/Update Menu (multipart/form-data):**
```
menu_name: Espresso
category: drink
description: Classic espresso
price: 25000
is_available: true
image: [file]
```

---

#### **Table Management**
```http
GET    /api/admin/tables             # List all tables
GET    /api/admin/tables/{id}        # Get single table
POST   /api/admin/tables             # Create table
PUT    /api/admin/tables/{id}        # Update table
DELETE /api/admin/tables/{id}        # Delete table
PATCH  /api/admin/tables/{id}/status # Update status
GET    /api/admin/table-types        # Get all table types
```

**Create/Update Table:**
```json
{
  "table_type_id": 1,
  "table_number": "T-001",
  "capacity": 4,
  "status": "available"
}
```

---

#### **Reservation Management**
```http
GET    /api/admin/reservations       # List reservations (paginated)
GET    /api/admin/reservations/{id}  # Get single reservation
POST   /api/admin/reservations/{id}/verify   # Verify payment
POST   /api/admin/reservations/{id}/reject   # Reject payment
PATCH  /api/admin/reservations/{id}/complete # Mark as completed
DELETE /api/admin/reservations/{id}  # Cancel reservation
```

**Verify Payment:**
```json
{}  // Empty body
```

**Reject Payment:**
```json
{
  "rejection_reason": "Bukti transfer tidak valid"
}
```

---

## 🔐 **Authentication**

```http
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/auth/me
```

**Login:**
```json
{
  "username": "admin",
  "password": "password"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "username": "admin",
      "full_name": "Admin User",
      "role": "admin"
    },
    "token": "1|abcd..."
  }
}
```

---

## 📊 **Status Flow**

### Reservation Status:
1. **pending_verification** → Customer upload bukti transfer
2. **confirmed** → Admin approve payment
3. **rejected** → Admin reject payment (dengan reason)
4. **cancelled** → Dibatalkan
5. **completed** → Customer sudah datang

### Payment Status:
- **unpaid** → Belum bayar / menunggu verifikasi
- **paid** → Sudah dibayar dan diverifikasi

---

## 🛠️ **Setup Instructions**

### 1. **Run Migration**
```bash
php artisan migrate
```

### 2. **Create Storage Symlink**
```bash
php artisan storage:link
```

### 3. **Seed Database** (Optional)
```bash
php artisan db:seed
```

### 4. **Start Server**
```bash
php artisan serve
```

---

## 📝 **Frontend Integration Notes**

### Field Name Changes:
- ✅ `name` → `menu_name` (Menu)
- ✅ `notes` → `special_notes` (Reservation)
- ✅ Added `duration_hours` field

### Type Updates:
```typescript
interface Menu {
  menu_name: string;  // Changed from 'name'
  // ... other fields
}

interface Reservation {
  duration_hours: number;  // New field
  special_notes?: string;  // Changed from 'notes'
  // ... other fields
}
```

### API Service Updates:
- ✅ All admin endpoints use `/api/admin/*` prefix
- ✅ Table availability includes `duration_hours` parameter
- ✅ Reservation creation includes `duration_hours`

---

## ✅ **Testing Checklist**

### Public Flow:
- [ ] Browse menu dengan filter kategori
- [ ] Search menu by name
- [ ] Check table availability dengan duration
- [ ] Create reservation dengan order items
- [ ] Upload payment proof
- [ ] Track reservation status by booking code

### Admin Flow:
- [ ] Login admin
- [ ] View dashboard statistics
- [ ] CRUD Menu (dengan image upload)
- [ ] CRUD Tables
- [ ] View reservations list
- [ ] Verify payment (approve)
- [ ] Reject payment (dengan reason)
- [ ] Mark reservation as completed
- [ ] Cancel reservation

---

## 🎯 **Key Features Implemented**

1. ✅ **User-selectable duration** (1-8 hours)
2. ✅ **Smart table availability** (conflict detection)
3. ✅ **Complete admin CRUD** (Menu, Table, Reservation)
4. ✅ **Image upload** (Menu images, Payment proofs)
5. ✅ **Payment verification** workflow
6. ✅ **Dashboard statistics**
7. ✅ **Consistent API responses**
8. ✅ **Proper validation** on all endpoints

---

## 🔄 **Migration Path**

If you have existing data:
```bash
# Backup database first!
php artisan migrate  # Adds duration_hours field with default value 2
```

---

## 📞 **Support**

Jika ada error atau pertanyaan:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check migration status: `php artisan migrate:status`
3. Clear cache: `php artisan cache:clear && php artisan config:clear`
