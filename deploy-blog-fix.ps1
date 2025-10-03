# Blog Publishing Fix Deployment Script (PowerShell)
# This script deploys the wp-config fix to resolve blog publishing issues

Write-Host "🚀 BLOG PUBLISHING FIX DEPLOYMENT" -ForegroundColor Green
Write-Host "==================================" -ForegroundColor Green
Write-Host ""

# Check if we're in the right directory
if (-not (Test-Path "wp-config-fixed.php")) {
    Write-Host "❌ Error: wp-config-fixed.php not found!" -ForegroundColor Red
    Write-Host "Please run this script from the project root directory." -ForegroundColor Red
    exit 1
}

Write-Host "✅ Found wp-config-fixed.php" -ForegroundColor Green
Write-Host ""

# Create backup directory
Write-Host "📁 Creating backup directory..." -ForegroundColor Yellow
$backupDir = "backups\$(Get-Date -Format 'yyyyMMdd_HHmmss')"
New-Item -ItemType Directory -Path $backupDir -Force | Out-Null

# Instructions for manual deployment
Write-Host "📋 DEPLOYMENT INSTRUCTIONS:" -ForegroundColor Cyan
Write-Host "==========================" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. BACKUP CURRENT SITE:" -ForegroundColor White
Write-Host "   - Download current wp-config.php from barcodemine.com" -ForegroundColor Gray
Write-Host "   - Save it as: $backupDir\wp-config-backup.php" -ForegroundColor Gray
Write-Host ""
Write-Host "2. UPLOAD FIXED FILE:" -ForegroundColor White
Write-Host "   - Upload wp-config-fixed.php to barcodemine.com" -ForegroundColor Gray
Write-Host "   - Rename it to wp-config.php" -ForegroundColor Gray
Write-Host "   - Replace the existing wp-config.php" -ForegroundColor Gray
Write-Host ""
Write-Host "3. TEST BLOG PUBLISHING:" -ForegroundColor White
Write-Host "   - Go to barcodemine.com/wp-admin/" -ForegroundColor Gray
Write-Host "   - Try creating a new blog post" -ForegroundColor Gray
Write-Host "   - Click 'Publish'" -ForegroundColor Gray
Write-Host "   - Check if it works!" -ForegroundColor Gray
Write-Host ""
Write-Host "4. IF STILL NOT WORKING:" -ForegroundColor White
Write-Host "   - Check /wp-content/debug.log for errors" -ForegroundColor Gray
Write-Host "   - Try deactivating all plugins" -ForegroundColor Gray
Write-Host "   - Switch to default WordPress theme" -ForegroundColor Gray
Write-Host ""

# Create deployment package
Write-Host "📦 Creating deployment package..." -ForegroundColor Yellow
$deploymentDir = "deployment-package"
New-Item -ItemType Directory -Path $deploymentDir -Force | Out-Null

# Copy files to deployment package
Copy-Item "wp-config-fixed.php" "$deploymentDir\wp-config.php"
Copy-Item "BLOG_PUBLISHING_FIX.md" "$deploymentDir\"
Copy-Item "fix-blog-publishing.php" "$deploymentDir\"

# Create deployment instructions
$instructions = @"
BLOG PUBLISHING FIX - DEPLOYMENT INSTRUCTIONS
=============================================

PROBLEM: "Publishing failed" error on barcodemine.com
CAUSE: Invalid security keys in wp-config.php
SOLUTION: Replace wp-config.php with fixed version

STEP 1: BACKUP CURRENT SITE
- Download current wp-config.php from barcodemine.com
- Keep it as backup in case something goes wrong

STEP 2: UPLOAD FIXED FILE
- Upload wp-config.php from this package to barcodemine.com
- Replace the existing wp-config.php
- Make sure file permissions are correct (644)

STEP 3: TEST BLOG PUBLISHING
- Go to barcodemine.com/wp-admin/
- Try creating a new blog post
- Click "Publish"
- Check if it works!

STEP 4: VERIFY FIX
- Blog posts should publish successfully
- No more "Publishing failed" errors
- Check debug logs if issues persist

FILES INCLUDED:
- wp-config.php (fixed version with real security keys)
- BLOG_PUBLISHING_FIX.md (detailed troubleshooting guide)
- fix-blog-publishing.php (automated fix script)

EXPECTED RESULT:
✅ Blog publishing should work perfectly!
✅ No more "Publishing failed" errors
✅ Better performance and debugging

If you need help, check the troubleshooting guide or contact support.
"@

$instructions | Out-File -FilePath "$deploymentDir\DEPLOYMENT_INSTRUCTIONS.txt" -Encoding UTF8

Write-Host "✅ Deployment package created in: $deploymentDir\" -ForegroundColor Green
Write-Host ""

# Create zip file for easy upload
Write-Host "📦 Creating deployment zip file..." -ForegroundColor Yellow
$zipName = "blog-publishing-fix-$(Get-Date -Format 'yyyyMMdd_HHmmss').zip"

try {
    Compress-Archive -Path "$deploymentDir\*" -DestinationPath $zipName -Force
    Write-Host "✅ Deployment zip created: $zipName" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Could not create zip file: $($_.Exception.Message)" -ForegroundColor Yellow
    Write-Host "   You can manually upload the $deploymentDir\ folder" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "🎯 NEXT STEPS:" -ForegroundColor Cyan
Write-Host "==============" -ForegroundColor Cyan
Write-Host "1. Upload the deployment package to barcodemine.com" -ForegroundColor White
Write-Host "2. Replace wp-config.php with the fixed version" -ForegroundColor White
Write-Host "3. Test blog publishing" -ForegroundColor White
Write-Host "4. Verify the fix works" -ForegroundColor White
Write-Host ""
Write-Host "📞 SUPPORT:" -ForegroundColor Cyan
Write-Host "If you need help, check BLOG_PUBLISHING_FIX.md for detailed instructions" -ForegroundColor White
Write-Host ""
Write-Host "🚀 Ready to fix your blog publishing issue!" -ForegroundColor Green
