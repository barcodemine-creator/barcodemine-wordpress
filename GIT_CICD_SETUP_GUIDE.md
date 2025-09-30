# Git CI/CD Pipeline Setup Guide
## WordPress Deployment with Version Control

---

## 🚀 **OVERVIEW:**

Set up a Git repository with CI/CD pipeline for automatic deployment to your live WordPress site. This approach gives you:

- ✅ **Version Control** - Track all changes
- ✅ **Safe Testing** - Test in staging before production
- ✅ **Automated Deployment** - Push code → Auto deploy
- ✅ **Rollback Capability** - Easily revert changes
- ✅ **Team Collaboration** - Multiple developers can work safely

---

## 📋 **ARCHITECTURE SETUP:**

```
Local Development → Git Repository → CI/CD Pipeline → Live Website
     ↓                    ↓                ↓              ↓
  Your Computer      GitHub/GitLab    Kloudbean/Actions   barcodemine.com
```

---

## 🛠️ **STEP 1: Initialize Git Repository**

### **Create .gitignore File:**
```gitignore
# WordPress specific
wp-config.php
wp-content/uploads/
wp-content/cache/
wp-content/backup-db/
wp-content/advanced-cache.php
wp-content/wp-cache-config.php

# Security files (keep local versions)
wp-config-local.php
wp-config-production.php
*.sql
debug.log
error_log

# Development files
*.md
.DS_Store
Thumbs.db
.vscode/
.idea/

# Sensitive data
*.log
.htpasswd

# Temporary files
*.tmp
*.temp
*~

# Environment specific
.env
.env.local
.env.production
```

### **Initialize Repository:**
```bash
# In your project directory
git init
git add .
git commit -m "Initial commit: Cleaned WordPress with security fixes"

# Create remote repository (GitHub/GitLab)
git remote add origin https://github.com/yourusername/barcodemine-wp.git
git branch -M main
git push -u origin main
```

---

## 🌿 **STEP 2: Branch Strategy**

### **Recommended Branches:**
```
main (production)     ← Live website
  ↑
staging              ← Testing environment  
  ↑
development          ← Active development
  ↑
feature/barcode-fix  ← Specific features
```

### **Create Branches:**
```bash
# Create development branch
git checkout -b development
git push -u origin development

# Create staging branch  
git checkout -b staging
git push -u origin staging

# For new features
git checkout development
git checkout -b feature/barcode-improvements
```

---

## 🔧 **STEP 3: CI/CD Pipeline Configuration**

### **Option A: GitHub Actions (Recommended)**

**Create: `.github/workflows/deploy.yml`**
```yaml
name: Deploy WordPress to Production

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v3
      
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        
    - name: Install dependencies
      run: |
        if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi
        
    - name: Run security checks
      run: |
        # Add security scanning here
        echo "Running security checks..."
        
    - name: Deploy to production
      uses: easingthemes/ssh-deploy@main
      env:
        SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
        REMOTE_HOST: ${{ secrets.REMOTE_HOST }}
        REMOTE_USER: ${{ secrets.REMOTE_USER }}
        SOURCE: "."
        TARGET: ${{ secrets.REMOTE_PATH }}
        EXCLUDE: "/node_modules/, /.git/, /.github/, /wp-config-local.php, /*.md"
        SCRIPT_BEFORE: |
          # Backup current site
          cp -r ${{ secrets.REMOTE_PATH }} ${{ secrets.REMOTE_PATH }}_backup_$(date +%Y%m%d_%H%M%S)
        SCRIPT_AFTER: |
          # Set correct permissions
          find ${{ secrets.REMOTE_PATH }} -type d -exec chmod 755 {} \;
          find ${{ secrets.REMOTE_PATH }} -type f -exec chmod 644 {} \;
          chmod 600 ${{ secrets.REMOTE_PATH }}/wp-config.php
```

### **Option B: GitLab CI/CD**

**Create: `.gitlab-ci.yml`**
```yaml
stages:
  - test
  - deploy

variables:
  PHP_VERSION: "8.1"

before_script:
  - apt-get update -qq && apt-get install -y -qq git curl libmcrypt-dev libjpeg-dev libpng-dev libfreetype6-dev libbz2-dev

test:
  stage: test
  script:
    - echo "Running tests..."
    - # Add your tests here
  only:
    - development
    - staging

deploy_production:
  stage: deploy
  script:
    - echo "Deploying to production..."
    - # Rsync or FTP deployment
    - rsync -avz --delete --exclude='.git' --exclude='wp-config-local.php' ./ $DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH
  only:
    - main
  when: manual
```

---

## 🔐 **STEP 4: Environment Configuration**

### **Create Environment Files:**

**`.env.example`**
```env
# Database Configuration
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASSWORD=your_database_password
DB_HOST=localhost

# WordPress URLs
WP_HOME=https://yourdomain.com
WP_SITEURL=https://yourdomain.com

# Security Keys (generate new ones)
AUTH_KEY=your_auth_key_here
SECURE_AUTH_KEY=your_secure_auth_key_here
LOGGED_IN_KEY=your_logged_in_key_here
NONCE_KEY=your_nonce_key_here

# Deployment Settings
DEPLOY_HOST=your.server.com
DEPLOY_USER=your_username
DEPLOY_PATH=/path/to/public_html
```

### **Update wp-config.php for Environment Variables:**
```php
<?php
// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Database settings from environment
define('DB_NAME', $_ENV['DB_NAME'] ?? 'fallback_db_name');
define('DB_USER', $_ENV['DB_USER'] ?? 'fallback_user');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? 'fallback_password');
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');

// URLs from environment
define('WP_HOME', $_ENV['WP_HOME'] ?? 'https://barcodemine.com');
define('WP_SITEURL', $_ENV['WP_SITEURL'] ?? 'https://barcodemine.com');

// Rest of your wp-config.php...
```

---

## 🚀 **STEP 5: Deployment Secrets Setup**

### **GitHub Secrets (Repository Settings → Secrets):**
```
SSH_PRIVATE_KEY      = Your private SSH key
REMOTE_HOST         = your.server.com
REMOTE_USER         = your_username
REMOTE_PATH         = /path/to/public_html
DB_PASSWORD         = your_database_password
```

### **GitLab Variables (Settings → CI/CD → Variables):**
```
DEPLOY_HOST         = your.server.com
DEPLOY_USER         = your_username  
DEPLOY_PATH         = /path/to/public_html
SSH_PRIVATE_KEY     = Your private SSH key
```

---

## 🔄 **STEP 6: Development Workflow**

### **Daily Development:**
```bash
# 1. Start new feature
git checkout development
git pull origin development
git checkout -b feature/new-barcode-feature

# 2. Make changes, test locally
# Edit files, test functionality

# 3. Commit changes
git add .
git commit -m "feat: improve barcode search with better error handling"

# 4. Push feature branch
git push origin feature/new-barcode-feature

# 5. Create Pull Request to development
# Review → Merge to development

# 6. Test in staging
git checkout staging
git merge development
git push origin staging
# Staging environment auto-deploys for testing

# 7. Deploy to production
git checkout main
git merge staging
git push origin main
# Production auto-deploys via CI/CD
```

---

## 🧪 **STEP 7: Testing Strategy**

### **Automated Tests (Optional):**
```php
// tests/BarcodeSearchTest.php
<?php
class BarcodeSearchTest extends WP_UnitTestCase {
    
    public function test_barcode_search_finds_orders() {
        // Create test order with barcode data
        $order_id = $this->factory->post->create([
            'post_type' => 'shop_order',
            'post_status' => 'wc-completed'
        ]);
        
        update_post_meta($order_id, '_excel_file_data', ['123456789', '123456790']);
        
        // Test search functionality
        $_POST['geiper_name'] = '123456789';
        ob_start();
        barcodemine_barcode_search();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Search results', $output);
    }
}
```

---

## 📊 **STEP 8: Monitoring & Rollback**

### **Deployment Monitoring:**
```bash
# Check deployment status
curl -f https://barcodemine.com/wp-admin/admin-ajax.php?action=barcode_search

# Monitor error logs
tail -f /path/to/wordpress/wp-content/debug.log
```

### **Quick Rollback:**
```bash
# Rollback to previous commit
git revert HEAD
git push origin main

# Or restore from backup
rsync -avz backup_folder/ production_folder/
```

---

## 🎯 **BENEFITS OF THIS SETUP:**

### **Development Benefits:**
- ✅ **Safe Testing** - Never break production
- ✅ **Version Control** - Track every change
- ✅ **Team Collaboration** - Multiple developers
- ✅ **Automated Deployment** - Push to deploy
- ✅ **Easy Rollback** - Revert problematic changes

### **Business Benefits:**
- ✅ **Reduced Downtime** - Automated, tested deployments
- ✅ **Better Quality** - Code review process
- ✅ **Faster Development** - Streamlined workflow
- ✅ **Audit Trail** - Complete change history

---

## 🚨 **SECURITY CONSIDERATIONS:**

### **Protect Sensitive Files:**
- Never commit `wp-config.php` with real credentials
- Use environment variables for secrets
- Exclude uploads and cache directories
- Set up proper file permissions in deployment

### **Access Control:**
- Use SSH keys for deployment
- Limit deployment permissions
- Enable two-factor authentication on Git repository
- Regular security audits of deployment pipeline

---

**This setup gives you enterprise-level WordPress development workflow!** 🚀

Your barcode functionality will be version controlled, tested, and automatically deployed with professional CI/CD practices.
