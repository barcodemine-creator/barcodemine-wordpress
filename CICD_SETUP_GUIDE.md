# 🚀 CI/CD Setup Guide for Barcodemine WordPress

This guide will help you set up automated deployment from GitHub to your cloud server using GitHub Actions.

## 📋 Prerequisites

- ✅ WordPress files in GitHub repository
- ✅ Cloud server with SSH access
- ✅ GitHub account with repository access

## 🔧 Step 1: Local Git Configuration

1. **Configure Git user** (if not already done):
   ```bash
   git config --global user.name "barcodemine-creator"
   git config --global user.email "barcodemine@gmail.com"
   ```

2. **Set up authentication** (choose one):
   
   **Option A: Personal Access Token**
   ```bash
   git remote set-url origin https://barcodemine-creator:YOUR_PERSONAL_ACCESS_TOKEN@github.com/barcodemine-creator/barcodemine-wordpress.git
   ```
   
   **Option B: SSH Key** (more secure)
   ```bash
   git remote set-url origin git@github.com:barcodemine-creator/barcodemine-wordpress.git
   ```

## 🔑 Step 2: Generate SSH Keys for Server Access

1. **Generate SSH key pair** on your local machine:
   ```bash
   ssh-keygen -t rsa -b 4096 -C "deploy@barcodemine.com"
   ```

2. **Copy public key to your server**:
   ```bash
   ssh-copy-id user@your-server-ip
   ```

3. **Test SSH connection**:
   ```bash
   ssh user@your-server-ip
   ```

## ⚙️ Step 3: Configure GitHub Secrets

Go to your GitHub repository → Settings → Secrets and variables → Actions

Add these secrets:

| Secret Name | Description | Example |
|-------------|-------------|---------|
| `HOST` | Server IP address | `123.456.789.012` |
| `USERNAME` | Server username | `root` or `ubuntu` |
| `SSH_PRIVATE_KEY` | Private SSH key content | `-----BEGIN RSA PRIVATE KEY-----...` |
| `TARGET_PATH` | Website directory path | `/var/www/html` |

## 🔄 Step 4: Create GitHub Actions Workflow

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Server

on:
  push:
    branches: [ master, main ]
  pull_request:
    branches: [ master, main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v3
      
    - name: Deploy to server
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_PRIVATE_KEY }}
        script: |
          cd ${{ secrets.TARGET_PATH }}
          git pull origin master
          # Set proper permissions
          chown -R www-data:www-data .
          chmod -R 755 .
          # Restart web server if needed
          systemctl reload apache2 || systemctl reload nginx
```

## 🧪 Step 5: Test the Deployment

1. **Make a test change** to any file
2. **Commit and push**:
   ```bash
   git add .
   git commit -m "Test CI/CD deployment"
   git push origin master
   ```
3. **Check GitHub Actions** tab in your repository
4. **Verify changes** on your live website

## 🛡️ Security Best Practices

### SSH Key Security
- ✅ Use strong SSH keys (4096-bit RSA minimum)
- ✅ Never share private keys
- ✅ Use different keys for different servers
- ✅ Regularly rotate SSH keys

### GitHub Secrets
- ✅ Never commit secrets to code
- ✅ Use GitHub Secrets for sensitive data
- ✅ Limit secret access to necessary workflows only
- ✅ Regularly audit and rotate secrets

### Server Security
- ✅ Keep server updated
- ✅ Use firewall rules
- ✅ Limit SSH access
- ✅ Monitor deployment logs

## 🔍 Troubleshooting

### Common Issues

**1. SSH Connection Failed**
```bash
# Test SSH connection manually
ssh -v user@server-ip

# Check SSH key permissions
chmod 600 ~/.ssh/id_rsa
chmod 644 ~/.ssh/id_rsa.pub
```

**2. Permission Denied**
```bash
# Fix file permissions on server
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
```

**3. Git Pull Failed**
```bash
# Reset git on server if needed
cd /var/www/html
git reset --hard origin/master
git clean -fd
```

## 📊 Monitoring Deployments

### GitHub Actions Logs
- Check the "Actions" tab in your GitHub repository
- Review deployment logs for errors
- Set up notifications for failed deployments

### Server Logs
```bash
# Check web server logs
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/nginx/error.log

# Check system logs
journalctl -f
```

## 🎯 Next Steps

1. ✅ Set up automated backups before deployments
2. ✅ Configure staging environment for testing
3. ✅ Add database migration scripts if needed
4. ✅ Set up monitoring and alerting
5. ✅ Document rollback procedures

---

## 🆘 Need Help?

If you encounter issues:
1. Check GitHub Actions logs first
2. Verify SSH connection manually
3. Test git commands on server directly
4. Review server logs for errors

**Remember**: Always test deployments on a staging environment first!
