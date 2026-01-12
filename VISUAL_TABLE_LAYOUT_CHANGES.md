# Perombakan Proses Pemesanan - Visual Table Layout

## 📋 Ringkasan Perubahan

### **Perubahan Besar:**

#### **Sebelum:**
1. User mengisi **jumlah orang** (number_of_people)
2. User memilih **tipe meja** (table_type_id)
3. Sistem menampilkan **list meja** yang tersedia (card biasa)
4. User memilih meja dari list

#### **Sesudah:**
1. ❌ **Hapus field jumlah orang** (tidak perlu lagi)
2. ✅ User melihat **visual layout meja** (sesuai frontend config)
3. ✅ User **klik meja langsung** dari layout visual
4. ✅ Sistem cek availability meja yang dipilih

---

## 🗑️ Yang Dihapus

### **1. Database Column**
```sql
-- DIHAPUS dari tabel reservations:
number_of_people INT
```

**Alasan:** Tidak diperlukan karena user langsung pilih meja dari visual layout. Kapasitas meja sudah ditentukan oleh tipe meja.

### **2. Validasi Backend**
```php
// DIHAPUS dari validation:
'number_of_people' => 'required|integer|min:1|max:20'
```

### **3. Logic Filter Berdasarkan Kapasitas**
```php
// DIHAPUS:
->where('capacity', '>=', $request->number_of_people)
```

---

## ✅ Yang Ditambahkan

### **1. Endpoint Baru: Get Tables with Availability**

**Route:**
```php
POST /api/tables/availability-status
```

**Request:**
```json
{
  "reservation_date": "2026-01-11",
  "reservation_time": "14:00",
  "duration_hours": 2.5
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "tables": [
      {
        "id": 1,
        "table_number": "A1",
        "capacity": 2,
        "table_type": {
          "id": 1,
          "type_name": "Regular",
          "description": "Indoor area"
        },
        "status": "available",
        "is_available_for_booking": true
      },
      {
        "id": 2,
        "table_number": "A2",
        "capacity": 2,
        "table_type": {...},
        "status": "available",
        "is_available_for_booking": false  // Sudah dibooking
      },
      ...
    ],
    "buffer_time_minutes": 30
  }
}
```

**Kegunaan:**
- Frontend menampilkan visual layout
- Setiap meja di-highlight berdasarkan `is_available_for_booking`
- Meja yang available: hijau/clickable
- Meja yang booked: merah/disabled

---

### **2. Endpoint Updated: Check Availability**

**Route:**
```php
POST /api/tables/check-availability
```

**Request (SEBELUM):**
```json
{
  "reservation_date": "2026-01-11",
  "reservation_time": "14:00",
  "table_type_id": 1,
  "number_of_people": 4,  // ❌ DIHAPUS
  "duration_hours": 2.5
}
```

**Request (SESUDAH):**
```json
{
  "reservation_date": "2026-01-11",
  "reservation_time": "14:00",
  "table_id": 5,  // ✅ User pilih langsung dari visual layout
  "duration_hours": 2.5
}
```

**Response (Available):**
```json
{
  "success": true,
  "message": "Table is available",
  "data": {
    "available": true,
    "table": {
      "id": 5,
      "table_number": "B2",
      "capacity": 4,
      "table_type": {...}
    },
    "buffer_time_minutes": 30
  }
}
```

**Response (Not Available):**
```json
{
  "success": false,
  "message": "Table is already booked for the selected time slot",
  "data": {
    "available": false,
    "table": {...},
    "conflict": {
      "existing_time": "13:00",
      "existing_duration": 3.0,
      "existing_end_with_buffer": "16:30"
    },
    "buffer_time_minutes": 30
  }
}
```

---

## 🎨 Frontend Integration

### **Visual Layout Config (Sudah Ada)**

Frontend sudah punya config layout di `tableLayoutConfig.ts`:

```typescript
// Indoor Area
{
  tables: [
    { tableNumber: 'A1', x: 50, y: 50, width: 80, height: 80, shape: 'square' },
    { tableNumber: 'A2', x: 150, y: 50, width: 80, height: 80, shape: 'square' },
    { tableNumber: 'B1', x: 50, y: 200, width: 120, height: 80, shape: 'rectangle' },
    ...
  ]
}
```

### **Flow Baru:**

#### **1. User Pilih Tanggal & Waktu**
```typescript
const [reservationDate, setReservationDate] = useState('');
const [startTime, setStartTime] = useState('');
const [endTime, setEndTime] = useState('');
const [duration, setDuration] = useState(0);
```

#### **2. Fetch Table Availability**
```typescript
const fetchTableAvailability = async () => {
  const response = await fetch('/api/tables/availability-status', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      reservation_date: reservationDate,
      reservation_time: startTime,
      duration_hours: duration
    })
  });
  
  const data = await response.json();
  setTablesAvailability(data.data.tables);
};
```

#### **3. Render Visual Layout**
```typescript
const renderTableLayout = () => {
  return (
    <div className="table-layout-canvas">
      {tableLayout.tables.map(tableConfig => {
        const tableData = tablesAvailability.find(
          t => t.table_number === tableConfig.tableNumber
        );
        
        const isAvailable = tableData?.is_available_for_booking;
        
        return (
          <div
            key={tableConfig.tableNumber}
            className={`table ${isAvailable ? 'available' : 'booked'}`}
            style={{
              left: `${(tableConfig.x / 800) * 100}%`,
              top: `${(tableConfig.y / 600) * 100}%`,
              width: `${(tableConfig.width / 800) * 100}%`,
              height: `${(tableConfig.height / 600) * 100}%`,
            }}
            onClick={() => isAvailable && handleTableSelect(tableData)}
          >
            <span>{tableConfig.tableNumber}</span>
            <span className="capacity">{tableData?.capacity} orang</span>
          </div>
        );
      })}
    </div>
  );
};
```

#### **4. User Klik Meja**
```typescript
const handleTableSelect = async (table) => {
  // Double-check availability
  const response = await fetch('/api/tables/check-availability', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      reservation_date: reservationDate,
      reservation_time: startTime,
      table_id: table.id,
      duration_hours: duration
    })
  });
  
  const data = await response.json();
  
  if (data.data.available) {
    setSelectedTable(table);
    // Lanjut ke step berikutnya
  } else {
    alert('Meja sudah dibooking orang lain');
  }
};
```

---

## 🔄 Perubahan Backend Detail

### **1. TableController.php**

#### **Method: checkAvailability()**

**SEBELUM:**
```php
$request->validate([
    'reservation_date' => 'required|date|after_or_equal:today',
    'reservation_time' => 'required|date_format:H:i',
    'table_type_id' => 'required|exists:table_types,id',
    'number_of_people' => 'required|integer|min:1|max:20',  // ❌
    'duration_hours' => 'required|numeric|min:0.5|max:8',
]);

$candidateTables = Table::where('table_type_id', $request->table_type_id)
    ->where('capacity', '>=', $request->number_of_people)  // ❌
    ->where('status', 'available')
    ->get();
```

**SESUDAH:**
```php
$request->validate([
    'reservation_date' => 'required|date|after_or_equal:today',
    'reservation_time' => 'required|date_format:H:i',
    'table_id' => 'required|exists:tables,id',  // ✅ Langsung table_id
    'duration_hours' => 'required|numeric|min:0.5|max:8',
]);

$table = Table::with('tableType')->find($tableId);  // ✅ Ambil 1 meja spesifik
```

#### **Method: getTablesWithAvailability()** (BARU)

```php
public function getTablesWithAvailability(Request $request)
{
    // Get all tables
    $tables = Table::with('tableType')->where('status', 'available')->get();
    
    // Check availability for each table
    $tablesWithAvailability = $tables->map(function ($table) use (...) {
        // Check conflicts
        $isAvailable = true;
        foreach ($conflictingReservations as $reservation) {
            // Check overlap with buffer
            if (overlap) {
                $isAvailable = false;
                break;
            }
        }
        
        return [
            'id' => $table->id,
            'table_number' => $table->table_number,
            'capacity' => $table->capacity,
            'is_available_for_booking' => $isAvailable,  // ✅ Key field
        ];
    });
    
    return response()->json([...]);
}
```

---

### **2. ReservationController.php**

**SEBELUM:**
```php
$request->validate([
    'table_id' => 'required|exists:tables,id',
    'number_of_people' => 'required|integer|min:1|max:20',  // ❌
    ...
]);

$reservation = Reservation::create([
    'table_id' => $request->table_id,
    'number_of_people' => $request->number_of_people,  // ❌
    ...
]);
```

**SESUDAH:**
```php
$request->validate([
    'table_id' => 'required|exists:tables,id',
    // number_of_people DIHAPUS ✅
    ...
]);

$reservation = Reservation::create([
    'table_id' => $request->table_id,
    // number_of_people DIHAPUS ✅
    ...
]);
```

---

## 🗄️ Database Migration

### **Migration File:**
`2026_01_10_151144_remove_number_of_people_from_reservations_table.php`

```php
public function up(): void
{
    Schema::table('reservations', function (Blueprint $table) {
        $table->dropColumn('number_of_people');
    });
}

public function down(): void
{
    Schema::table('reservations', function (Blueprint $table) {
        $table->integer('number_of_people')->after('reservation_time');
    });
}
```

**Status:** ✅ Sudah dijalankan

---

## 📊 Perbandingan Flow

### **Flow Lama:**
```
1. User input: Tanggal, Waktu, Jumlah Orang, Tipe Meja
2. Backend filter: WHERE capacity >= number_of_people AND table_type_id = X
3. Backend return: List meja (card biasa)
4. User pilih dari list
5. Submit reservasi
```

### **Flow Baru:**
```
1. User input: Tanggal, Waktu (Jam Mulai & Jam Selesai)
2. Frontend fetch: GET /api/tables/availability-status
3. Frontend render: Visual layout dengan highlight available/booked
4. User klik meja langsung dari layout
5. Backend verify: POST /api/tables/check-availability (double-check)
6. Submit reservasi
```

---

## 🎯 Keuntungan Perubahan

### **1. UX Lebih Intuitif**
- ✅ User langsung lihat layout cafe
- ✅ User tahu posisi meja (dekat jendela, pojok, dll)
- ✅ User bisa pilih meja favorit
- ✅ Real-time availability visual

### **2. Lebih Sederhana**
- ✅ Tidak perlu input jumlah orang
- ✅ Tidak perlu pilih tipe meja dulu
- ✅ Langsung klik meja yang diinginkan

### **3. Lebih Akurat**
- ✅ User tahu persis meja mana yang available
- ✅ Tidak ada confusion tentang kapasitas
- ✅ Visual feedback langsung

---

## 🧪 Testing

### **Test 1: Get Tables with Availability**
```bash
POST /api/tables/availability-status
{
  "reservation_date": "2026-01-11",
  "reservation_time": "14:00",
  "duration_hours": 2.0
}

Expected:
✅ Return semua meja dengan is_available_for_booking flag
```

### **Test 2: Check Specific Table**
```bash
POST /api/tables/check-availability
{
  "reservation_date": "2026-01-11",
  "reservation_time": "14:00",
  "table_id": 5,
  "duration_hours": 2.0
}

Expected:
✅ Return availability status untuk meja spesifik
```

### **Test 3: Create Reservation (No number_of_people)**
```bash
POST /api/reservations
FormData:
- table_id: 5
- reservation_date: 2026-01-11
- reservation_time: 14:00
- duration_hours: 2.0
- payment_proof: [file]
// NO number_of_people ✅

Expected:
✅ Reservasi berhasil dibuat tanpa number_of_people
```

---

## 📝 Checklist Perubahan

### **Backend:**
- [x] Hapus `number_of_people` dari validation
- [x] Hapus `number_of_people` dari database
- [x] Update `TableController::checkAvailability()` - terima `table_id` langsung
- [x] Tambah `TableController::getTablesWithAvailability()` - endpoint baru
- [x] Update `ReservationController::store()` - hapus `number_of_people`
- [x] Tambah route `/api/tables/availability-status`
- [x] Run migration

### **Frontend (Yang Perlu Diupdate):**
- [ ] Hapus input field `number_of_people`
- [ ] Implementasi visual table layout
- [ ] Fetch availability status dari `/api/tables/availability-status`
- [ ] Render meja dengan highlight available/booked
- [ ] Handle klik meja untuk select
- [ ] Update API call - kirim `table_id` bukan `table_type_id` + `number_of_people`

---

## 🎯 Kesimpulan

### **Perubahan Utama:**
1. ✅ Hapus field `number_of_people` dari database
2. ✅ User pilih meja langsung dari visual layout
3. ✅ Backend cek availability per meja spesifik
4. ✅ Endpoint baru untuk get all tables dengan status availability

### **Keuntungan:**
- ✅ UX lebih intuitif dengan visual layout
- ✅ Proses lebih sederhana (less input fields)
- ✅ User bisa pilih meja favorit berdasarkan posisi
- ✅ Real-time visual feedback

---

**Last Updated**: 2026-01-10  
**Migration**: ✅ Completed  
**Status**: ✅ Backend Ready - Frontend Needs Update
