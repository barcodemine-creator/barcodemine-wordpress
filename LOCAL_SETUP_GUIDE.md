# WordPress Local Development Setup Guide
## Barcodemine.com Local Installation

---

## 🚀 **QUICK SETUP OVERVIEW:**

1. **Install Local Development Environment** (XAMPP/WAMP/Local by Flywheel)
2. **Import Database** (`latestdb.sql`)
3. **Configure wp-config.php** for local database
4. **Update URLs** in database
5. **Test Barcode Search** functionality

---

## 📋 **STEP-BY-STEP INSTRUCTIONS:**

### **OPTION 1: XAMPP (Recommended)**

#### **Step 1: Install XAMPP**
1. Download XAMPP from: https://www.apachefriends.org/
2. Install with **Apache**, **MySQL**, and **PHP**
3. Start **Apache** and **MySQL** services

#### **Step 2: Setup Project Directory**
1. Copy your entire project folder to: `C:\xampp\htdocs\barcodemine\`
2. Your structure should be:
   ```
   C:\xampp\htdocs\barcodemine\
   ├── wp-admin\
   ├── wp-content\
   ├── wp-includes\
   ├── wp-config.php
   ├── index.php
   ├── latestdb.sql
   └── ... (all other files)
   ```

#### **Step 3: Create Local Database**
1. Open browser: `http://localhost/phpmyadmin`
2. Click **"New"** to create database
3. Database name: `barcodemine_local`
4. Collation: `utf8_general_ci`
5. Click **"Create"**

#### **Step 4: Import Database**
1. Select your `barcodemine_local` database
2. Click **"Import"** tab
3. Click **"Choose File"** → Select `latestdb.sql`
4. Click **"Go"** to import
5. Wait for "Import has been successfully finished"

#### **Step 5: Configure wp-config.php**
Create a local version of wp-config.php:

```php
<?php
define( 'WP_CACHE', true ); 

// ** MySQL settings - LOCAL DEVELOPMENT ** //
define('DB_NAME', 'barcodemine_local');
define('DB_USER', 'root');
define('DB_PASSWORD', ''); // Usually empty for XAMPP
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// ** WordPress Security Keys ** //
require('wp-salt.php');

// ** WordPress Database Table prefix ** //
$table_prefix = 'wp_';

// ** Local Development Settings ** //
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('DISALLOW_FILE_EDIT', false); // Allow editing for development
define('WP_POST_REVISIONS', 3);
define('AUTOMATIC_UPDATER_DISABLED', true); // Disable updates locally

// ** Local URLs ** //
define('WP_SITEURL', 'http://localhost/barcodemine');
define('WP_HOME', 'http://localhost/barcodemine');

// ** File System Settings ** //
define('FS_METHOD','direct');
define('FS_CHMOD_DIR', (0775 & ~ umask()));
define('FS_CHMOD_FILE', (0664 & ~ umask()));

/* That's all, stop editing! Happy blogging. */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php');
?>
```

#### **Step 6: Update Database URLs**
1. Go to `http://localhost/phpmyadmin`
2. Select `barcodemine_local` database
3. Click **"SQL"** tab
4. Run these queries:

```sql
-- Update site URLs
UPDATE wp_options SET option_value = 'http://localhost/barcodemine' WHERE option_name = 'home';
UPDATE wp_options SET option_value = 'http://localhost/barcodemine' WHERE option_name = 'siteurl';

-- Update any hardcoded URLs in content (optional)
UPDATE wp_posts SET post_content = REPLACE(post_content, 'https://barcodemine.com', 'http://localhost/barcodemine');
UPDATE wp_posts SET post_content = REPLACE(post_content, 'http://barcodemine.com', 'http://localhost/barcodemine');
```

---

### **OPTION 2: Local by Flywheel (Easier)**

#### **Step 1: Install Local by Flywheel**
1. Download from: https://localwp.com/
2. Install and create new site: "Barcodemine Local"
3. Choose **Custom** setup
4. PHP Version: **7.4+**, MySQL: **8.0+**

#### **Step 2: Import Your Files**
1. Navigate to site folder (usually `~/Local Sites/Barcodemine Local/app/public/`)
2. Replace all files with your WordPress files
3. Keep the local `wp-config.php` that Local created

#### **Step 3: Import Database**
1. Right-click site → **"Open site shell"**
2. Run: `wp db import /path/to/latestdb.sql`
3. Or use **Adminer** from Local's tools

---

## 🔧 **TESTING YOUR SETUP:**

### **Step 1: Access Your Site**
- **XAMPP**: `http://localhost/barcodemine`
- **Local**: `http://barcodemine.local` (or whatever Local shows)

### **Step 2: WordPress Admin**
- **URL**: `http://localhost/barcodemine/wp-admin`
- **Username/Password**: From your production site

### **Step 3: Test Barcode Search**
1. Create a test page with `[barcode_search]` shortcode
2. Go to **WooCommerce → Orders**
3. Find an order with barcode data
4. Test searching for one of those barcodes

---

## 🛠️ **DEVELOPMENT WORKFLOW:**

### **Making Changes:**
1. Edit files in your local installation
2. Test functionality immediately
3. Check debug logs: `wp-content/debug.log`
4. Use browser developer tools for JavaScript debugging

### **Testing Barcode Search:**
1. **Add test orders** with Excel files
2. **Upload sample barcode Excel** files
3. **Test search** with known barcode numbers
4. **Check AJAX** requests in browser Network tab

### **Debugging:**
- **PHP Errors**: Check `wp-content/debug.log`
- **JavaScript Errors**: Check browser console (F12)
- **AJAX Issues**: Check Network tab in browser dev tools
- **Database Queries**: Enable query debugging in wp-config.php

---

## 📁 **IMPORTANT FILES TO WATCH:**

### **Barcode Functionality:**
- `wp-content/themes/mydecor-child/functions.php` - Main barcode logic
- `wp-content/themes/mydecor-child/assets/js/main.js` - AJAX handling
- `wp-content/themes/mydecor-child/style.css` - Search form styling

### **Configuration:**
- `wp-config.php` - Database and debug settings
- `.htaccess` - URL rewriting and security
- `wp-content/debug.log` - Error logs

---

## 🚨 **COMMON ISSUES & SOLUTIONS:**

### **Database Connection Error:**
- Check database name, username, password in wp-config.php
- Ensure MySQL service is running

### **404 Errors:**
- Check .htaccess file exists
- Enable mod_rewrite in Apache
- Update permalinks in WordPress admin

### **Barcode Search Not Working:**
- Check debug logs for errors
- Verify AJAX URL in browser Network tab
- Ensure orders have correct status and barcode data

### **File Permissions:**
- XAMPP: Usually no issues on Windows
- Linux/Mac: Set proper permissions (755 for folders, 644 for files)

---

## 🎯 **NEXT STEPS AFTER SETUP:**

1. **Verify** all functionality works locally
2. **Test** barcode search with sample data
3. **Make** your desired changes
4. **Test** thoroughly before deploying to production
5. **Backup** your changes regularly

---

**Your local development environment will be ready for testing and development!** 🎉
