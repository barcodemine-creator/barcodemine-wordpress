# WordPress REST API Fix Deployment Script
Write-Host "🔧 WORDPRESS REST API FIX DEPLOYMENT" -ForegroundColor Green
Write-Host "====================================" -ForegroundColor Green
Write-Host ""

# Check if files exist
if (-not (Test-Path "wp-config-fixed-rest-api.php")) {
    Write-Host "❌ Error: wp-config-fixed-rest-api.php not found!" -ForegroundColor Red
    exit 1
}

if (-not (Test-Path "fix-rest-api-issue.php")) {
    Write-Host "❌ Error: fix-rest-api-issue.php not found!" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Found fix files" -ForegroundColor Green
Write-Host ""

# Create deployment package
Write-Host "📦 Creating deployment package..." -ForegroundColor Yellow
$deploymentDir = "rest-api-fix-package"
New-Item -ItemType Directory -Path $deploymentDir -Force | Out-Null

# Copy files
Copy-Item "wp-config-fixed-rest-api.php" "$deploymentDir\wp-config.php"
Copy-Item "fix-rest-api-issue.php" "$deploymentDir\"

# Create instructions
$instructions = @"
WORDPRESS REST API FIX - DEPLOYMENT INSTRUCTIONS
================================================

PROBLEM: "WordPress REST API is disabled" error
CAUSE: REST API is disabled or blocked
SOLUTION: Enable REST API and fix configuration

STEP 1: BACKUP CURRENT SITE
- Download current wp-config.php from barcodemine.com
- Keep it as backup in case something goes wrong

STEP 2: UPLOAD FIXED FILES
- Upload wp-config.php from this package to barcodemine.com
- Replace the existing wp-config.php
- Upload fix-rest-api-issue.php to your WordPress root

STEP 3: RUN THE FIX SCRIPT
- Go to: https://barcodemine.com/fix-rest-api-issue.php
- This will enable REST API and test endpoints
- Check the test results

STEP 4: TEST YOUR SECURITY PLUGIN
- Go to WordPress admin
- Check if the security plugin is now working
- Try publishing a blog post

STEP 5: VERIFY FIX
- Visit: https://barcodemine.com/rest-api-test.php
- All tests should show green checkmarks
- Security plugin should be working

FILES INCLUDED:
- wp-config.php (fixed version with REST API enabled)
- fix-rest-api-issue.php (automated fix script)

EXPECTED RESULT:
✅ REST API should be working
✅ Security plugin should be functional
✅ Blog publishing should work
✅ No more "REST API disabled" errors

If you need help, check the test results or contact support.
"@

$instructions | Out-File -FilePath "$deploymentDir\DEPLOYMENT_INSTRUCTIONS.txt" -Encoding UTF8

Write-Host "✅ Deployment package created in: $deploymentDir\" -ForegroundColor Green
Write-Host ""

# Create zip file
Write-Host "📦 Creating deployment zip file..." -ForegroundColor Yellow
$zipName = "rest-api-fix-$(Get-Date -Format 'yyyyMMdd_HHmmss').zip"

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
Write-Host "3. Upload fix-rest-api-issue.php to WordPress root" -ForegroundColor White
Write-Host "4. Run: https://barcodemine.com/fix-rest-api-issue.php" -ForegroundColor White
Write-Host "5. Test: https://barcodemine.com/rest-api-test.php" -ForegroundColor White
Write-Host ""
Write-Host "🚀 Ready to fix your REST API issue!" -ForegroundColor Green
