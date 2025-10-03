# Blog Publishing Fix Deployment Script (PowerShell)
Write-Host "🚀 BLOG PUBLISHING FIX DEPLOYMENT" -ForegroundColor Green
Write-Host "==================================" -ForegroundColor Green
Write-Host ""

# Check if files exist
if (-not (Test-Path "wp-config-fixed.php")) {
    Write-Host "❌ Error: wp-config-fixed.php not found!" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Found wp-config-fixed.php" -ForegroundColor Green
Write-Host ""

# Create deployment package
Write-Host "📦 Creating deployment package..." -ForegroundColor Yellow
$deploymentDir = "deployment-package"
New-Item -ItemType Directory -Path $deploymentDir -Force | Out-Null

# Copy files
Copy-Item "wp-config-fixed.php" "$deploymentDir\wp-config.php"
Copy-Item "BLOG_PUBLISHING_FIX.md" "$deploymentDir\"
Copy-Item "fix-blog-publishing.php" "$deploymentDir\"

Write-Host "✅ Deployment package created in: $deploymentDir\" -ForegroundColor Green
Write-Host ""

# Create zip file
Write-Host "📦 Creating deployment zip file..." -ForegroundColor Yellow
$zipName = "blog-publishing-fix-$(Get-Date -Format 'yyyyMMdd_HHmmss').zip"

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
Write-Host "3. Test blog publishing" -ForegroundColor White
Write-Host ""
Write-Host "🚀 Ready to fix your blog publishing issue!" -ForegroundColor Green
