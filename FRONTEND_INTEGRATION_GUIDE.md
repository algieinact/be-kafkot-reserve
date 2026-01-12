# Frontend Integration Guide - Visual Table Layout

## 📋 Masalah Saat Ini

1. ✅ **Komponen sudah ada**: `AreaTabs` dan `TableLayoutViewer`
2. ❌ **Masih dummy data**: Line 225-234 di `ReservationPage.tsx`
3. ❌ **Validasi table_type_id**: Line 214-217 (perlu dihapus)
4. ❌ **Pilihan tipe meja di form**: Line 562-596 (perlu dihapus)

---

## 🔧 Perubahan yang Diperlukan

### **1. Hapus Pilihan Tipe Meja dari Form**

**File**: `src/pages/Public/ReservationPage.tsx`

**Hapus bagian ini** (Line 562-596):

```tsx
// ❌ HAPUS BAGIAN INI:
<div>
  <label className="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
    Tipe Meja <span className="text-error-500">*</span>
  </label>
  {loadingTableTypes ? (
    <div className="text-center py-4">
      <p className="text-sm text-gray-500">Memuat tipe meja...</p>
    </div>
  ) : tableTypes.length > 0 ? (
    <div className={`grid gap-3 ${tableTypes.length === 3 ? 'sm:grid-cols-3' : tableTypes.length === 2 ? 'sm:grid-cols-2' : 'sm:grid-cols-1'}`}>
      {tableTypes.map((type) => (
        <button
          key={type.id}
          type="button"
          onClick={() => handleTableTypeChange(type.id)}
          className={...}
        >
          <div className="font-medium text-gray-900 dark:text-white">{type.type_name}</div>
          {type.description && (
            <div className="mt-1 text-xs text-gray-600 dark:text-gray-400">
              {type.description}
            </div>
          )}
        </button>
      ))}
    </div>
  ) : (
    <div className="text-center py-4 rounded-lg bg-gray-50 dark:bg-gray-800">
      <p className="text-sm text-gray-500">Tidak ada tipe meja tersedia</p>
    </div>
  )}
</div>
```

**Alasan:** Tab untuk pilih area sudah ada di bagian "Pilih Meja", jadi tidak perlu pilihan tipe meja di form.

---

### **2. Update Function `checkTableAvailability`**

**File**: `src/pages/Public/ReservationPage.tsx`

**Ganti bagian ini** (Line 191-265):

```tsx
const checkTableAvailability = async () => {
  console.log("=== Checking Table Availability ===");

  if (!validateForm()) {
    console.log("Form validation failed");
    return;
  }

  if (!startTime) {
    alert("Pilih jam mulai terlebih dahulu");
    return;
  }

  if (!endTime) {
    alert("Pilih jam selesai terlebih dahulu");
    return;
  }

  if (duration <= 0 || duration > 5) {
    alert("Durasi harus antara 0.5 - 5 jam");
    return;
  }

  // ❌ HAPUS VALIDASI INI:
  // if (!formData.table_type_id) {
  //   alert("Pilih tipe meja terlebih dahulu");
  //   return;
  // }

  try {
    setCheckingAvailability(true);
    setShowTableSelection(false);
    setSelectedTable(null);
    setErrors({});

    // ✅ GUNAKAN API BACKEND:
    const requestData = {
      reservation_date: formData.reservation_date,
      reservation_time: startTime,
      duration_hours: duration,
    };

    console.log("Request data:", requestData);

    // Call API to get all tables with availability status
    const response = await fetch('/api/tables/availability-status', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(requestData),
    });

    const data = await response.json();

    console.log("API Response:", data);

    if (data.success && data.data && data.data.tables) {
      const tables = data.data.tables;
      
      // Map tables with their visual positions from layout config
      const tablesWithPositions = tables.map(table => {
        // Determine area based on table_number prefix
        let area: 'indoor' | 'semi_outdoor' | 'outdoor' = 'indoor';
        if (table.table_number.startsWith('S')) {
          area = 'semi_outdoor';
        } else if (table.table_number.startsWith('O')) {
          area = 'outdoor';
        }
        
        const layout = getLayoutByArea(area);
        const position = layout.tables.find(p => p.tableNumber === table.table_number);
        
        return {
          ...table,
          position,
          area,
        };
      }).filter(table => table.position); // Only include tables with positions

      console.log("Tables with positions:", tablesWithPositions);

      setAvailableTables(tablesWithPositions);
      setShowTableSelection(true);

      if (tablesWithPositions.length === 0) {
        setErrors((prev) => ({
          ...prev,
          table: "Tidak ada meja tersedia untuk kriteria yang dipilih"
        }));
      }
    } else {
      console.error("API returned error:", data.error);
      setErrors((prev) => ({
        ...prev,
        table: data.error || "Gagal memeriksa ketersediaan meja"
      }));
      setShowTableSelection(true);
    }
  } catch (error: any) {
    console.error("Exception during availability check:", error);
    setErrors((prev) => ({
      ...prev,
      table: "Terjadi kesalahan saat memeriksa ketersediaan meja"
    }));
    setShowTableSelection(true);
  } finally {
    setCheckingAvailability(false);
    console.log("=== Availability Check Complete ===");
  }
};
```

---

### **3. Update State Management**

**File**: `src/pages/Public/ReservationPage.tsx`

**Tambahkan state untuk area** (jika belum ada):

```tsx
const [selectedArea, setSelectedArea] = useState<'indoor' | 'semi_outdoor' | 'outdoor'>('indoor');
```

**Hapus state yang tidak diperlukan**:

```tsx
// ❌ HAPUS:
// const [tableTypes, setTableTypes] = useState<TableTypeDetail[]>([]);
// const [loadingTableTypes, setLoadingTableTypes] = useState(true);
```

**Hapus useEffect untuk fetch table types**:

```tsx
// ❌ HAPUS:
// useEffect(() => {
//   const fetchTableTypes = async () => {
//     try {
//       setLoadingTableTypes(true);
//       const response = await tableApi.getTableTypes();
//       ...
//     } catch (error) {
//       ...
//     }
//   };
//   fetchTableTypes();
// }, []);
```

---

### **4. Update TableLayoutViewer Props**

**File**: `src/pages/Public/ReservationPage.tsx`

**Update komponen TableLayoutViewer** (Line 642-647):

```tsx
<TableLayoutViewer
  layout={getLayoutByArea(selectedArea)}
  tables={availableTables.filter(t => t.area === selectedArea)}  // ✅ Filter by area
  selectedTableId={selectedTable?.id || null}
  onTableSelect={handleTableSelection}
  showAvailabilityStatus={true}  // ✅ Show green/red based on is_available_for_booking
/>
```

---

### **5. Update AreaTabs Counts**

**File**: `src/pages/Public/ReservationPage.tsx`

**Update AreaTabs** (Line 628-639):

```tsx
<AreaTabs
  activeArea={selectedArea}
  onAreaChange={(area) => {
    setSelectedArea(area);
    setSelectedTable(null); // Reset selection when changing area
  }}
  availableCounts={{
    indoor: availableTables.filter(t => 
      t.area === 'indoor' && t.is_available_for_booking
    ).length,
    semi_outdoor: availableTables.filter(t => 
      t.area === 'semi_outdoor' && t.is_available_for_booking
    ).length,
    outdoor: availableTables.filter(t => 
      t.area === 'outdoor' && t.is_available_for_booking
    ).length,
  }}
/>
```

---

### **6. Update handleSubmit**

**File**: `src/pages/Public/ReservationPage.tsx`

**Update bagian submit** (Line 297-311):

```tsx
const handleSubmit = async () => {
  if (!selectedTable) {
    setErrors((prev) => ({ ...prev, table: "Pilih meja terlebih dahulu" }));
    return;
  }

  if (!startTime) {
    setErrors((prev) => ({ ...prev, startTime: "Pilih jam mulai terlebih dahulu" }));
    return;
  }

  if (!endTime) {
    setErrors((prev) => ({ ...prev, endTime: "Pilih jam selesai terlebih dahulu" }));
    return;
  }

  if (duration <= 0 || duration > 5) {
    setErrors((prev) => ({ ...prev, duration: "Durasi harus antara 0.5 - 5 jam" }));
    return;
  }

  try {
    setSubmitting(true);
    setErrors({});

    // Prepare reservation data for API
    const reservationData = {
      customer_name: formData.customer_name,
      customer_email: formData.customer_email,
      customer_phone: formData.customer_phone,
      table_id: selectedTable.id,  // ✅ Kirim table_id langsung
      reservation_date: formData.reservation_date,
      reservation_time: startTime,
      duration_hours: duration,  // ✅ Decimal value (e.g., 1.5, 2.5)
      special_notes: formData.special_notes,
      order_items: cartItems.map(item => ({
        menu_id: item.menu.id,
        quantity: item.quantity
      }))
      // ❌ TIDAK ADA number_of_people
    };

    console.log("Submitting reservation:", reservationData);

    // Call backend API
    const response = await reservationApi.createReservation(reservationData);

    if (response.success && response.data) {
      // Save to localStorage
      reservationStorage.add({
        bookingCode: response.data.booking_code,
        customerName: formData.customer_name,
        customerEmail: formData.customer_email,
        customerPhone: formData.customer_phone,
        reservationDate: formData.reservation_date,
        reservationTime: startTime,
        durationHours: duration,
        tableNumber: selectedTable.table_number,
        tableType: selectedTable.table_type?.type_name || "Unknown",
        totalAmount: response.data.total_amount || totalPrice,
        status: response.data.status || "pending_verification",
        orderItems: cartItems.map(item => ({
          menuName: item.menu.menu_name,
          quantity: item.quantity,
          price: item.menu.price,
        })),
        createdAt: new Date().toISOString(),
      });

      // Navigate to payment page
      navigate(`/payment/${response.data.booking_code}`);
    } else {
      setErrors({ submit: response.error || "Gagal membuat reservasi. Silakan coba lagi." });
    }
  } catch (error: any) {
    console.error("Error creating reservation:", error);
    setErrors({ submit: error.message || "Terjadi kesalahan. Silakan coba lagi." });
  } finally {
    setSubmitting(false);
  }
};
```

---

## 📊 Type Definitions

**File**: `src/types/index.ts`

**Update Table type**:

```typescript
export interface Table {
  id: number;
  table_number: string;
  capacity: number;
  table_type?: {
    id: number;
    type_name: string;
    description?: string;
  };
  status: 'available' | 'reserved' | 'inactive';
  is_available_for_booking?: boolean;  // ✅ Tambahkan ini
  position?: {  // ✅ Tambahkan ini
    tableNumber: string;
    x: number;
    y: number;
    width: number;
    height: number;
    shape: 'square' | 'rectangle' | 'circle';
  };
  area?: 'indoor' | 'semi_outdoor' | 'outdoor';  // ✅ Tambahkan ini
}
```

---

## 🎨 TableLayoutViewer Component

**File**: `src/components/reservation/TableLayoutViewer.tsx`

**Update untuk menampilkan status availability**:

```tsx
const TableLayoutViewer: React.FC<Props> = ({
  layout,
  tables,
  selectedTableId,
  onTableSelect,
  showAvailabilityStatus = true,
}) => {
  return (
    <div className="relative w-full" style={{ paddingBottom: `${(layout.height / layout.width) * 100}%` }}>
      <div className="absolute inset-0">
        {layout.tables.map((tableConfig) => {
          const tableData = tables.find(t => t.table_number === tableConfig.tableNumber);
          const isSelected = tableData?.id === selectedTableId;
          const isAvailable = tableData?.is_available_for_booking ?? false;
          
          return (
            <div
              key={tableConfig.tableNumber}
              className={`
                absolute cursor-pointer transition-all duration-200
                ${isSelected 
                  ? 'border-4 border-blue-500 bg-blue-100 dark:bg-blue-900/50 scale-105 z-10' 
                  : isAvailable
                    ? 'border-2 border-green-500 bg-green-50 dark:bg-green-900/20 hover:scale-105 hover:shadow-lg'
                    : 'border-2 border-red-300 bg-red-50 dark:bg-red-900/20 opacity-50 cursor-not-allowed'
                }
                ${tableConfig.shape === 'circle' ? 'rounded-full' : 'rounded-lg'}
              `}
              style={{
                left: `${(tableConfig.x / layout.width) * 100}%`,
                top: `${(tableConfig.y / layout.height) * 100}%`,
                width: `${(tableConfig.width / layout.width) * 100}%`,
                height: `${(tableConfig.height / layout.height) * 100}%`,
              }}
              onClick={() => isAvailable && onTableSelect(tableData.id)}
            >
              <div className="flex h-full flex-col items-center justify-center p-2">
                <span className={`
                  text-sm font-bold
                  ${isSelected ? 'text-blue-700 dark:text-blue-300' : ''}
                  ${isAvailable && !isSelected ? 'text-green-700 dark:text-green-300' : ''}
                  ${!isAvailable ? 'text-red-500 dark:text-red-400' : ''}
                `}>
                  {tableConfig.tableNumber}
                </span>
                {tableData && (
                  <span className="text-xs text-gray-600 dark:text-gray-400">
                    {tableData.capacity} orang
                  </span>
                )}
                {showAvailabilityStatus && (
                  <span className={`
                    text-xs font-medium mt-1
                    ${isAvailable ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}
                  `}>
                    {isAvailable ? '✓ Tersedia' : '✗ Booked'}
                  </span>
                )}
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
};
```

---

## 🧪 Testing Flow

### **1. User Flow:**
```
1. User isi: Tanggal, Jam Mulai, Jam Selesai
2. User klik: "Cek Ketersediaan Meja"
3. Sistem fetch: POST /api/tables/availability-status
4. Sistem tampilkan: Visual layout dengan highlight hijau (available) / merah (booked)
5. User pilih tab: Indoor / Semi-Outdoor / Outdoor
6. User klik meja yang hijau
7. Sistem confirm: Meja terpilih
8. User klik: "Konfirmasi Reservasi"
9. Sistem submit: POST /api/reservations (dengan table_id)
```

### **2. Test Cases:**

#### **Test 1: Cek Availability**
```typescript
// Request
POST /api/tables/availability-status
{
  "reservation_date": "2026-01-11",
  "reservation_time": "14:00",
  "duration_hours": 2.5
}

// Expected Response
{
  "success": true,
  "data": {
    "tables": [
      {
        "id": 1,
        "table_number": "A1",
        "capacity": 2,
        "is_available_for_booking": true  // ✅ Hijau
      },
      {
        "id": 2,
        "table_number": "A2",
        "capacity": 2,
        "is_available_for_booking": false  // ❌ Merah
      },
      ...
    ]
  }
}
```

#### **Test 2: Submit Reservation**
```typescript
// Request
POST /api/reservations
FormData:
- customer_name: "John Doe"
- customer_email: "john@example.com"
- customer_phone: "08123456789"
- table_id: 5  // ✅ Langsung table_id
- reservation_date: "2026-01-11"
- reservation_time: "14:00"
- duration_hours: 2.5  // ✅ Decimal
- payment_proof: [file]
// ❌ NO number_of_people
// ❌ NO table_type_id

// Expected Response
{
  "success": true,
  "message": "Reservation created successfully",
  "data": {
    "booking_code": "RSV-20260111-ABC123",
    ...
  }
}
```

---

## 📝 Checklist Frontend

- [ ] Hapus pilihan tipe meja dari form (line 562-596)
- [ ] Hapus validasi `table_type_id` (line 214-217)
- [ ] Hapus state `tableTypes` dan `loadingTableTypes`
- [ ] Hapus useEffect untuk fetch table types
- [ ] Update `checkTableAvailability` - gunakan API `/api/tables/availability-status`
- [ ] Tambahkan state `selectedArea`
- [ ] Update `TableLayoutViewer` - tampilkan hijau/merah based on `is_available_for_booking`
- [ ] Update `AreaTabs` - hitung available count per area
- [ ] Update `handleSubmit` - kirim `table_id` (bukan `table_type_id` + `number_of_people`)
- [ ] Update type definition `Table` - tambah `is_available_for_booking`, `position`, `area`
- [ ] Test flow lengkap

---

## 🎯 Hasil Akhir

### **Sebelum:**
- User pilih tipe meja dari form
- User input jumlah orang
- Sistem tampilkan list meja (card biasa)
- Dummy data

### **Sesudah:**
- ❌ Tidak ada pilihan tipe meja di form
- ❌ Tidak ada input jumlah orang
- ✅ Visual layout meja dengan tab area (Indoor/Semi-Outdoor/Outdoor)
- ✅ Highlight hijau (available) / merah (booked)
- ✅ Real-time data dari backend
- ✅ User klik meja langsung dari layout

---

**Last Updated**: 2026-01-10  
**Status**: Backend Ready ✅ - Frontend Integration Guide Complete ✅
