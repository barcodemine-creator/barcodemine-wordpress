# REST API 500 Error Fix Deployment Script
Write-Host "🔧 REST API 500 ERROR FIX DEPLOYMENT" -ForegroundColor Green
Write-Host "====================================" -ForegroundColor Green
Write-Host ""

# Check if files exist
if (-not (Test-Path "wp-config-fix-500-error.php")) {
    Write-Host "❌ Error: wp-config-fix-500-error.php not found!" -ForegroundColor Red
    exit 1
}

if (-not (Test-Path "fix-rest-api-500-error.php")) {
    Write-Host "❌ Error: fix-rest-api-500-error.php not found!" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Found fix files" -ForegroundColor Green
Write-Host ""

# Create deployment package
Write-Host "📦 Creating deployment package..." -ForegroundColor Yellow
$deploymentDir = "500-error-fix-package"
New-Item -ItemType Directory -Path $deploymentDir -Force | Out-Null

# Copy files
Copy-Item "wp-config-fix-500-error.php" "$deploymentDir\wp-config.php"
Copy-Item "fix-rest-api-500-error.php" "$deploymentDir\"

Write-Host "✅ Deployment package created in: $deploymentDir\" -ForegroundColor Green
Write-Host ""

# Create zip file
Write-Host "📦 Creating deployment zip file..." -ForegroundColor Yellow
$zipName = "500-error-fix-$(Get-Date -Format 'yyyyMMdd_HHmmss').zip"

try {
    Compress-Archive -Path "$deploymentDir\*" -DestinationPath $zipName -Force
    Write-Host "✅ Deployment zip created: $zipName" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Could not create zip file" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "🎯 DEPLOYMENT INSTRUCTIONS:" -ForegroundColor Cyan
Write-Host "1. Upload $zipName to barcodemine.com" -ForegroundColor White
Write-Host "2. Extract and replace wp-config.php" -ForegroundColor White
Write-Host "3. Upload fix-rest-api-500-error.php to WordPress root" -ForegroundColor White
Write-Host "4. Run: https://barcodemine.com/fix-rest-api-500-error.php" -ForegroundColor White
Write-Host "5. Test: https://barcodemine.com/rest-api-500-test.php" -ForegroundColor White
Write-Host "6. Check WordPress Site Health again" -ForegroundColor White
Write-Host ""
Write-Host "🚀 Ready to fix the REST API 500 error!" -ForegroundColor Green
