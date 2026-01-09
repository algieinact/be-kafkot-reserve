# Cloudinary Integration Documentation

## Overview
This Laravel project uses **Cloudinary** for all image uploads, specifically for payment proof images. Local filesystem storage is **NOT** used for any user-uploaded images.

## Why Cloudinary?

### ✅ Advantages:
1. **Scalability**: Cloud storage handles unlimited uploads without server disk space concerns
2. **CDN Delivery**: Images are served via Cloudinary's global CDN for fast loading
3. **Security**: Secure HTTPS URLs with built-in access control
4. **Automatic Optimization**: Image transformation and optimization out of the box
5. **No Server Maintenance**: No need to manage local storage, backups, or cleanup
6. **Production Ready**: Eliminates issues with file permissions, disk space, and storage links

### ❌ Why NOT Local Storage:
- Requires `php artisan storage:link` setup
- Disk space limitations on shared hosting
- No CDN delivery (slower for users)
- Backup complexity
- File permission issues on deployment
- Not suitable for horizontal scaling (multiple servers)

---

## Configuration

### 1. Environment Setup

**IMPORTANT**: Use **ONLY** the `CLOUDINARY_URL` format in your `.env` file:

```env
# Cloudinary Configuration (Use ONLY this URL format)
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
```

**Example** (from your credentials):
```env
CLOUDINARY_URL=cloudinary://469962522952248:kNlFAVfXI1lodXOnlQA0FHajGew@dsev8bqmu
```

### ⚠️ DO NOT USE:
```env
# ❌ WRONG - Do not use separate credentials
CLOUDINARY_CLOUD_NAME=dsev8bqmu
CLOUDINARY_KEY=469962522952248
CLOUDINARY_SECRET=kNlFAVfXI1lodXOnlQA0FHajGew
```

**Why?** 
- The Cloudinary PHP SDK natively supports `CLOUDINARY_URL`
- Avoids config cache conflicts
- Single source of truth
- Easier deployment and environment management

---

## Architecture

### Files Changed:

1. **`app/Services/CloudinaryService.php`** (NEW)
   - Centralized Cloudinary upload/delete logic
   - Handles errors gracefully
   - Reusable across controllers

2. **`app/Http/Controllers/Api/ReservationController.php`** (MODIFIED)
   - Replaced `storeAs()` with Cloudinary upload
   - Returns secure HTTPS URLs
   - Handles old image deletion

3. **`app/Console/Commands/TestCloudinaryUpload.php`** (NEW)
   - Test command to verify configuration

### Database Schema:
- `payments.payment_proof_url` stores the Cloudinary secure URL (HTTPS)
- No changes needed to existing migrations

---

## Usage

### Upload Payment Proof (API Endpoint)

**Endpoint**: `POST /api/reservations/{id}/upload-payment-proof`

**Headers**:
```
Content-Type: multipart/form-data
```

**Body** (form-data):
```
payment_proof: [image file] (jpeg, png, jpg, webp, max 2MB)
```

**Success Response** (200):
```json
{
  "success": true,
  "message": "Payment proof uploaded successfully",
  "data": {
    "payment_proof_url": "https://res.cloudinary.com/dsev8bqmu/image/upload/v1234567890/kafkot/payment-proofs/payment-RSV-20260109-ABC123-1736394000.jpg",
    "public_id": "kafkot/payment-proofs/payment-RSV-20260109-ABC123-1736394000",
    "format": "jpg",
    "size_bytes": 245678
  }
}
```

**Error Response** (500):
```json
{
  "success": false,
  "message": "Failed to upload payment proof: Cloudinary upload failed: Invalid credentials"
}
```

---

## Testing

### 1. Test Configuration (Artisan Command)

```bash
php artisan cloudinary:test
```

**Expected Output**:
```
Testing Cloudinary Configuration...

✅ CLOUDINARY_URL is configured
✅ CloudinaryService initialized successfully

Cloudinary Configuration:
  API Key: 469962522952248
  API Secret: ************Gew
  Cloud Name: dsev8bqmu

🎉 Cloudinary is configured correctly!
```

### 2. Test Upload (cURL)

First, create a test reservation to get an ID, then:

```bash
curl -X POST http://localhost:8000/api/reservations/1/upload-payment-proof \
  -F "payment_proof=@/path/to/test-image.jpg"
```

**Windows PowerShell**:
```powershell
$uri = "http://localhost:8000/api/reservations/1/upload-payment-proof"
$filePath = "C:\path\to\test-image.jpg"

$form = @{
    payment_proof = Get-Item -Path $filePath
}

Invoke-RestMethod -Uri $uri -Method Post -Form $form
```

### 3. Test in Laravel Tinker

```bash
php artisan tinker
```

```php
// Test Cloudinary service initialization
$cloudinary = new App\Services\CloudinaryService();

// Check if Cloudinary URL is configured
env('CLOUDINARY_URL');
// Should output: "cloudinary://469962522952248:kNlFAVfXI1lodXOnlQA0FHajGew@dsev8bqmu"

// Test with a real reservation (replace ID with actual reservation ID)
$reservation = App\Models\Reservation::find(1);
$payment = $reservation->payment;

// Check current payment proof URL
$payment->payment_proof_url;
// After upload, this should be a Cloudinary HTTPS URL
```

---

## Verification Checklist

### ✅ Configuration Verified:
- [ ] `.env` contains `CLOUDINARY_URL=cloudinary://...`
- [ ] No separate `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_KEY`, `CLOUDINARY_SECRET` in `.env`
- [ ] `php artisan cloudinary:test` returns success
- [ ] `composer.json` includes `"cloudinary/cloudinary_php": "^3.1"`

### ✅ Code Verified:
- [ ] `ReservationController::uploadPaymentProof()` uses `CloudinaryService`
- [ ] No `storeAs()` or `Storage::` calls for payment proofs
- [ ] Response returns `secure_url` (HTTPS)
- [ ] Old images are deleted from Cloudinary on re-upload

### ✅ Functionality Verified:
- [ ] Upload endpoint returns Cloudinary URL
- [ ] URL is accessible (HTTPS)
- [ ] Image displays correctly in frontend
- [ ] Database stores full Cloudinary URL
- [ ] No files created in `storage/app/public/payment-proofs`

---

## Troubleshooting

### Error: "CLOUDINARY_URL is not set"
**Solution**: Ensure `.env` has the correct format:
```env
CLOUDINARY_URL=cloudinary://469962522952248:kNlFAVfXI1lodXOnlQA0FHajGew@dsev8bqmu
```

Then clear config cache:
```bash
php artisan config:clear
```

### Error: "Invalid credentials"
**Solution**: Verify your Cloudinary credentials at https://cloudinary.com/console
- API Key: `469962522952248`
- Cloud Name: `dsev8bqmu`
- API Secret: Check your Cloudinary dashboard

### Error: "Class CloudinaryService not found"
**Solution**: Run:
```bash
composer dump-autoload
```

### Upload works but returns local path
**Solution**: Check that you're using the **updated** `ReservationController.php` with `CloudinaryService`, not the old version with `storeAs()`.

---

## Production Deployment

### Before Deploying:
1. ✅ Update `.env` on production server with `CLOUDINARY_URL`
2. ✅ Run `composer install --no-dev --optimize-autoloader`
3. ✅ Run `php artisan config:cache`
4. ✅ Test upload endpoint
5. ✅ Verify Cloudinary dashboard shows uploaded images

### Environment Variables:
```env
# Production .env
CLOUDINARY_URL=cloudinary://YOUR_API_KEY:YOUR_API_SECRET@YOUR_CLOUD_NAME
```

### No Additional Setup Required:
- ❌ No `storage:link` needed
- ❌ No file permissions to configure
- ❌ No disk space monitoring
- ❌ No backup scripts for uploaded files

---

## API Response Changes

### Before (Local Storage):
```json
{
  "payment_proof_url": "http://localhost:8000/storage/payment-proofs/payment-RSV-20260109-ABC123-1736394000.jpg",
  "payment_proof_path": "payment-proofs/payment-RSV-20260109-ABC123-1736394000.jpg"
}
```

### After (Cloudinary):
```json
{
  "payment_proof_url": "https://res.cloudinary.com/dsev8bqmu/image/upload/v1736394000/kafkot/payment-proofs/payment-RSV-20260109-ABC123.jpg",
  "public_id": "kafkot/payment-proofs/payment-RSV-20260109-ABC123",
  "format": "jpg",
  "size_bytes": 245678
}
```

**Key Differences**:
- ✅ HTTPS URL (secure)
- ✅ CDN delivery (fast)
- ✅ Includes metadata (format, size)
- ✅ No local path dependency

---

## Security Notes

1. **Never commit `.env`** - Cloudinary credentials are sensitive
2. **Use HTTPS URLs** - Always return `secure_url` from Cloudinary
3. **Validate uploads** - Current validation: `image|mimes:jpeg,png,jpg,webp|max:2048`
4. **Rate limiting** - Consider adding rate limits to upload endpoints
5. **Access control** - Only authenticated users should upload

---

## Future Enhancements

### Possible Improvements:
1. **Image Transformations**: Use Cloudinary's transformation API for thumbnails
2. **Lazy Loading**: Implement progressive image loading
3. **Watermarking**: Add automatic watermarks to payment proofs
4. **Backup**: Enable Cloudinary's backup addon
5. **Analytics**: Track upload metrics via Cloudinary dashboard

### Example Transformation:
```php
// Generate thumbnail URL
$thumbnailUrl = str_replace('/upload/', '/upload/w_200,h_200,c_fill/', $uploadResult['secure_url']);
```

---

## Support

- **Cloudinary Docs**: https://cloudinary.com/documentation/php_integration
- **Laravel Docs**: https://laravel.com/docs/filesystem
- **Project Issues**: Contact backend team

---

**Last Updated**: 2026-01-09  
**Laravel Version**: 12.x  
**Cloudinary SDK**: 3.1.x
