# Create Telegram Blog Publisher Plugin Zip
# Run this script to create a zip file of the updated plugin

Write-Host "🗜️ CREATING TELEGRAM BLOG PUBLISHER PLUGIN ZIP" -ForegroundColor Green
Write-Host "===============================================" -ForegroundColor Green
Write-Host ""

# Set the plugin directory
$pluginDir = "telegram-blog-publisher"
$zipFile = "telegram-blog-publisher-updated.zip"

# Check if plugin directory exists
if (Test-Path $pluginDir) {
    Write-Host "✅ Plugin directory found: $pluginDir" -ForegroundColor Green
    
    # Remove existing zip file if it exists
    if (Test-Path $zipFile) {
        Remove-Item $zipFile -Force
        Write-Host "🗑️ Removed existing zip file" -ForegroundColor Yellow
    }
    
    # Create zip file
    Write-Host "📦 Creating zip file..." -ForegroundColor Blue
    Compress-Archive -Path "$pluginDir\*" -DestinationPath $zipFile -Force
    
    if (Test-Path $zipFile) {
        $zipSize = (Get-Item $zipFile).Length
        $zipSizeMB = [math]::Round($zipSize / 1MB, 2)
        
        Write-Host "✅ Zip file created successfully!" -ForegroundColor Green
        Write-Host "📁 File: $zipFile" -ForegroundColor Cyan
        Write-Host "📊 Size: $zipSizeMB MB" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "🎯 NEXT STEPS:" -ForegroundColor Yellow
        Write-Host "==============" -ForegroundColor Yellow
        Write-Host "1. Upload $zipFile to your WordPress server" -ForegroundColor White
        Write-Host "2. Extract it in /wp-content/plugins/" -ForegroundColor White
        Write-Host "3. Activate the plugin in WordPress Admin" -ForegroundColor White
        Write-Host "4. Go to Telegram Publisher → Settings to see new features" -ForegroundColor White
        Write-Host ""
        Write-Host "🔗 Plugin files included:" -ForegroundColor Blue
        Get-ChildItem $pluginDir -Recurse | ForEach-Object {
            Write-Host "   $($_.FullName.Replace((Get-Location).Path + '\', ''))" -ForegroundColor Gray
        }
    } else {
        Write-Host "❌ Failed to create zip file" -ForegroundColor Red
    }
} else {
    Write-Host "❌ Plugin directory not found: $pluginDir" -ForegroundColor Red
    Write-Host "Make sure you're running this script from the correct directory" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Press any key to continue..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
