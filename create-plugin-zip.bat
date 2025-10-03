@echo off
echo 🗜️ CREATING TELEGRAM BLOG PUBLISHER PLUGIN ZIP
echo ===============================================
echo.

REM Check if plugin directory exists
if not exist "telegram-blog-publisher" (
    echo ❌ Plugin directory not found: telegram-blog-publisher
    echo Make sure you're running this script from the correct directory
    pause
    exit /b 1
)

echo ✅ Plugin directory found: telegram-blog-publisher

REM Remove existing zip file if it exists
if exist "telegram-blog-publisher-updated.zip" (
    del "telegram-blog-publisher-updated.zip"
    echo 🗑️ Removed existing zip file
)

echo 📦 Creating zip file...

REM Create zip file using PowerShell
powershell -Command "Compress-Archive -Path 'telegram-blog-publisher\*' -DestinationPath 'telegram-blog-publisher-updated.zip' -Force"

if exist "telegram-blog-publisher-updated.zip" (
    echo ✅ Zip file created successfully!
    echo 📁 File: telegram-blog-publisher-updated.zip
    echo.
    echo 🎯 NEXT STEPS:
    echo ==============
    echo 1. Upload telegram-blog-publisher-updated.zip to your WordPress server
    echo 2. Extract it in /wp-content/plugins/
    echo 3. Activate the plugin in WordPress Admin
    echo 4. Go to Telegram Publisher → Settings to see new features
    echo.
    echo 🔗 Plugin files included:
    dir telegram-blog-publisher /s /b
) else (
    echo ❌ Failed to create zip file
)

echo.
pause
