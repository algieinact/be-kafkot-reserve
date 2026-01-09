# Quick Setup Guide - Cloudinary Integration

## Step 1: Update .env File

**IMPORTANT**: You must update your `.env` file manually (it's gitignored).

Replace these lines (around line 63-69):

```env
# OLD - REMOVE THESE:
VITE_APP_NAME="${APP_NAME}"
# CLOUDINARY_URL=cloudinary://469962522952248:kNlFAVfXI1lodXOnlQA0FHajGew@dsev8bqmu

CLOUDINARY_CLOUD_NAME=dsev8bqmu
CLOUDINARY_KEY=469962522952248
CLOUDINARY_SECRET=kNlFAVfXI1lodXOnlQA0FHajGew
```

With this:

```env
# NEW - USE THIS:
VITE_APP_NAME="${APP_NAME}"

# Cloudinary Configuration (Use ONLY this URL format)
CLOUDINARY_URL=cloudinary://469962522952248:kNlFAVfXI1lodXOnlQA0FHajGew@dsev8bqmu
```

## Step 2: Clear Config Cache

After updating `.env`, run:

```bash
php artisan config:clear
```

## Step 3: Test Configuration

```bash
php artisan cloudinary:test
```

Expected output:
```
✅ CLOUDINARY_URL is configured
✅ CloudinaryService initialized successfully
🎉 Cloudinary is configured correctly!
```

## Step 4: Test Upload (cURL)

First, create a test reservation, then upload:

```bash
# Windows PowerShell
$uri = "http://localhost:8000/api/reservations/1/upload-payment-proof"
$filePath = "C:\path\to\test-image.jpg"

$form = @{
    payment_proof = Get-Item -Path $filePath
}

Invoke-RestMethod -Uri $uri -Method Post -Form $form
```

## What Changed?

### ✅ Files Created:
- `app/Services/CloudinaryService.php` - Cloudinary upload service
- `app/Console/Commands/TestCloudinaryUpload.php` - Test command
- `CLOUDINARY_INTEGRATION.md` - Full documentation

### ✅ Files Modified:
- `app/Http/Controllers/Api/ReservationController.php` - Now uses Cloudinary
- `composer.json` - Added cloudinary/cloudinary_php package

### ✅ What's Different:
- Payment proof images upload to Cloudinary (cloud storage)
- No local storage used (`storage/app/public/payment-proofs` is NOT used)
- API returns secure HTTPS URLs from Cloudinary CDN
- Automatic old image deletion on re-upload

## Verification

Check that:
1. ✅ `.env` has `CLOUDINARY_URL=cloudinary://...` (uncommented)
2. ✅ No separate CLOUDINARY_CLOUD_NAME, CLOUDINARY_KEY, CLOUDINARY_SECRET
3. ✅ `php artisan cloudinary:test` passes
4. ✅ Upload returns Cloudinary URL (https://res.cloudinary.com/...)

## Troubleshooting

**Error: "CLOUDINARY_URL is not set"**
- Make sure you updated `.env` file
- Run `php artisan config:clear`
- Restart your Laravel server

**Error: "Class CloudinaryService not found"**
- Run `composer dump-autoload`

**Upload still uses local storage**
- Check you're using the updated ReservationController
- Clear route cache: `php artisan route:clear`

---

For full documentation, see: `CLOUDINARY_INTEGRATION.md`
