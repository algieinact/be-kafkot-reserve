# Cloudinary Upload Test Script (PowerShell)
# This script tests the payment proof upload endpoint

# Configuration
$baseUrl = "http://localhost:8000"
$reservationId = 1  # Change this to an actual reservation ID
$imagePath = "C:\path\to\test-image.jpg"  # Change this to your test image path

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "Cloudinary Upload Test" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

# Check if image file exists
if (-not (Test-Path $imagePath)) {
    Write-Host "❌ Error: Image file not found at: $imagePath" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please update the `$imagePath variable with a valid image file path" -ForegroundColor Yellow
    exit 1
}

Write-Host "✅ Image file found: $imagePath" -ForegroundColor Green
Write-Host ""

# Prepare the upload
$uri = "$baseUrl/api/reservations/$reservationId/upload-payment-proof"
Write-Host "📤 Uploading to: $uri" -ForegroundColor Cyan
Write-Host ""

try {
    # Create form data
    $form = @{
        payment_proof = Get-Item -Path $imagePath
    }

    # Send request
    Write-Host "⏳ Uploading..." -ForegroundColor Yellow
    $response = Invoke-RestMethod -Uri $uri -Method Post -Form $form

    # Display response
    Write-Host ""
    Write-Host "==================================" -ForegroundColor Green
    Write-Host "✅ Upload Successful!" -ForegroundColor Green
    Write-Host "==================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Response:" -ForegroundColor Cyan
    $response | ConvertTo-Json -Depth 10 | Write-Host
    Write-Host ""

    # Extract and display Cloudinary URL
    if ($response.data.payment_proof_url) {
        Write-Host "🌐 Cloudinary URL:" -ForegroundColor Cyan
        Write-Host $response.data.payment_proof_url -ForegroundColor White
        Write-Host ""
        Write-Host "✅ Verify the URL is from Cloudinary (https://res.cloudinary.com/...)" -ForegroundColor Green
    }

} catch {
    Write-Host ""
    Write-Host "==================================" -ForegroundColor Red
    Write-Host "❌ Upload Failed!" -ForegroundColor Red
    Write-Host "==================================" -ForegroundColor Red
    Write-Host ""
    Write-Host "Error Details:" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Yellow
    Write-Host ""
    
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "Response Body:" -ForegroundColor Red
        Write-Host $responseBody -ForegroundColor Yellow
    }
    
    Write-Host ""
    Write-Host "Troubleshooting:" -ForegroundColor Cyan
    Write-Host "1. Make sure reservation ID $reservationId exists" -ForegroundColor White
    Write-Host "2. Check that CLOUDINARY_URL is set in .env" -ForegroundColor White
    Write-Host "3. Run: php artisan config:clear" -ForegroundColor White
    Write-Host "4. Run: php artisan cloudinary:test" -ForegroundColor White
    
    exit 1
}

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "Test completed successfully! 🎉" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
