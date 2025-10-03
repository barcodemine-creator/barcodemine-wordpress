#!/bin/bash
# Blog Publishing Fix Deployment Script
# This script deploys the wp-config fix to resolve blog publishing issues

echo "🚀 BLOG PUBLISHING FIX DEPLOYMENT"
echo "=================================="
echo ""

# Check if we're in the right directory
if [ ! -f "wp-config-fixed.php" ]; then
    echo "❌ Error: wp-config-fixed.php not found!"
    echo "Please run this script from the project root directory."
    exit 1
fi

echo "✅ Found wp-config-fixed.php"
echo ""

# Create backup directory
echo "📁 Creating backup directory..."
mkdir -p backups/$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="backups/$(date +%Y%m%d_%H%M%S)"

# Instructions for manual deployment
echo "📋 DEPLOYMENT INSTRUCTIONS:"
echo "=========================="
echo ""
echo "1. BACKUP CURRENT SITE:"
echo "   - Download current wp-config.php from barcodemine.com"
echo "   - Save it as: $BACKUP_DIR/wp-config-backup.php"
echo ""
echo "2. UPLOAD FIXED FILE:"
echo "   - Upload wp-config-fixed.php to barcodemine.com"
echo "   - Rename it to wp-config.php"
echo "   - Replace the existing wp-config.php"
echo ""
echo "3. TEST BLOG PUBLISHING:"
echo "   - Go to barcodemine.com/wp-admin/"
echo "   - Try creating a new blog post"
echo "   - Click 'Publish'"
echo "   - Check if it works!"
echo ""
echo "4. IF STILL NOT WORKING:"
echo "   - Check /wp-content/debug.log for errors"
echo "   - Try deactivating all plugins"
echo "   - Switch to default WordPress theme"
echo ""

# Create deployment package
echo "📦 Creating deployment package..."
mkdir -p deployment-package
cp wp-config-fixed.php deployment-package/wp-config.php
cp BLOG_PUBLISHING_FIX.md deployment-package/
cp fix-blog-publishing.php deployment-package/

# Create deployment instructions
cat > deployment-package/DEPLOYMENT_INSTRUCTIONS.txt << 'EOF'
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
EOF

echo "✅ Deployment package created in: deployment-package/"
echo ""

# Create zip file for easy upload
echo "📦 Creating deployment zip file..."
zip -r blog-publishing-fix-$(date +%Y%m%d_%H%M%S).zip deployment-package/ > /dev/null 2>&1

if [ $? -eq 0 ]; then
    echo "✅ Deployment zip created: blog-publishing-fix-$(date +%Y%m%d_%H%M%S).zip"
else
    echo "⚠️  Could not create zip file (zip command not found)"
    echo "   You can manually upload the deployment-package/ folder"
fi

echo ""
echo "🎯 NEXT STEPS:"
echo "=============="
echo "1. Upload the deployment package to barcodemine.com"
echo "2. Replace wp-config.php with the fixed version"
echo "3. Test blog publishing"
echo "4. Verify the fix works"
echo ""
echo "📞 SUPPORT:"
echo "If you need help, check BLOG_PUBLISHING_FIX.md for detailed instructions"
echo ""
echo "🚀 Ready to fix your blog publishing issue!"
