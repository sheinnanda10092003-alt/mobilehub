# 🚀 MobileHub Deployment Guide

This guide will help you deploy your MobileHub Laravel application to various hosting platforms.

## 📋 Prerequisites

Before deploying, ensure you have:
- ✅ PHP 8.2 or higher
- ✅ Composer installed
- ✅ Node.js and NPM installed
- ✅ MySQL database (local or remote)
- ✅ Git repository (recommended)

## 🛠️ Pre-Deployment Preparation

### 1. Update Environment Configuration
Copy `.env.production` to `.env` and update the following:

```bash
# Replace with your actual values
APP_URL=https://yourdomain.com
DB_HOST=your-database-host
DB_DATABASE=your-database-name
DB_USERNAME=your-database-username
DB_PASSWORD=your-database-password

# Email configuration (optional but recommended)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

### 2. Run Pre-Deployment Optimizations
```bash
# Make scripts executable (Linux/Mac)
chmod +x deploy.sh optimize.sh

# Run optimization
./optimize.sh
```

## 🌐 Deployment Options

### Option 1: Heroku (Recommended for Beginners) 

**Cost:** Free tier available, then $7/month
**Difficulty:** ⭐⭐☆☆☆

#### Step-by-Step Heroku Deployment:

1. **Install Heroku CLI**
   - Download from: https://devcenter.heroku.com/articles/heroku-cli

2. **Login to Heroku**
   ```bash
   heroku login
   ```

3. **Create Heroku App**
   ```bash
   heroku create your-app-name
   ```

4. **Add Database**
   ```bash
   heroku addons:create cleardb:ignite
   ```

5. **Set Environment Variables**
   ```bash
   heroku config:set APP_KEY=$(php artisan key:generate --show)
   heroku config:set APP_ENV=production
   heroku config:set APP_DEBUG=false
   ```

6. **Deploy**
   ```bash
   git add .
   git commit -m "Ready for deployment"
   git push heroku main
   ```

7. **Your app is live at:** `https://your-app-name.herokuapp.com`

---

### Option 2: DigitalOcean App Platform

**Cost:** $5-12/month
**Difficulty:** ⭐⭐⭐☆☆

#### Step-by-Step DigitalOcean Deployment:

1. **Create Account** at https://cloud.digitalocean.com
2. **Create New App** from your GitHub repository
3. **Configure Build Settings:**
   - Build Command: `npm run build`
   - Run Command: `heroku-php-apache2 public/`
4. **Add Database Component** (MySQL)
5. **Set Environment Variables** in the app dashboard
6. **Deploy** - automatic from GitHub

---

### Option 3: Shared Hosting (cPanel)

**Cost:** $3-10/month
**Difficulty:** ⭐⭐⭐⭐☆

#### Step-by-Step Shared Hosting Deployment:

1. **Prepare Files Locally**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm run build
   ```

2. **Upload via FTP/FileManager:**
   - Upload all files to `public_html` or domain folder
   - Move contents of `public` folder to root web directory
   - Update `public/index.php` to point to correct paths

3. **Database Setup:**
   - Create MySQL database in cPanel
   - Import your database or run migrations
   - Update `.env` with database credentials

4. **File Permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

---

### Option 4: VPS (Virtual Private Server)

**Cost:** $5-20/month
**Difficulty:** ⭐⭐⭐⭐⭐

#### Step-by-Step VPS Deployment:

1. **Server Setup:**
   ```bash
   # Update system
   sudo apt update && sudo apt upgrade -y
   
   # Install LAMP stack
   sudo apt install apache2 mysql-server php8.2 php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd -y
   ```

2. **Deploy Application:**
   ```bash
   # Clone repository
   git clone https://github.com/yourusername/mobilehub.git
   cd mobilehub
   
   # Run deployment script
   ./deploy.sh
   ```

3. **Configure Apache Virtual Host**
4. **Set up SSL certificate** (Let's Encrypt recommended)
5. **Configure firewall** and security settings

---

## 🎯 Quick Deploy Commands

### For Git-based Deployments:
```bash
# Standard deployment
git add .
git commit -m "Deploy to production"
git push origin main

# Heroku deployment
git push heroku main

# Run post-deployment optimizations
./optimize.sh
```

### For Manual Deployments:
```bash
# Prepare for upload
./deploy.sh

# After uploading, run on server:
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

## 🔧 Post-Deployment Checklist

After deployment, verify:
- [ ] Website loads correctly
- [ ] Customer registration works
- [ ] Staff registration works  
- [ ] Database connections work
- [ ] Email sending works (if configured)
- [ ] File permissions are correct
- [ ] SSL certificate is active (HTTPS)

## 🚨 Troubleshooting Common Issues

### 500 Internal Server Error
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Common fixes:
chmod -R 777 storage bootstrap/cache
php artisan config:clear
php artisan cache:clear
```

### Database Connection Issues
- Verify database credentials in `.env`
- Check if database server is accessible
- Ensure database exists and user has permissions

### Permission Errors
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 777 storage bootstrap/cache
```

## 🌟 Recommended Hosting Providers

### For Beginners:
1. **Heroku** - Easy deployment, free tier available
2. **Hostinger** - Affordable shared hosting with cPanel
3. **Bluehost** - WordPress-friendly, good support

### For Advanced Users:
1. **DigitalOcean** - Reliable, scalable, great documentation
2. **Linode** - High performance, developer-friendly
3. **AWS Lightsail** - Amazon's simplified VPS solution

## 📞 Getting Help

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode temporarily: `APP_DEBUG=true`
3. Check server error logs
4. Consult hosting provider documentation

## 🔄 Updates and Maintenance

### For Regular Updates:
```bash
# Pull latest changes
git pull origin main

# Run deployment script
./deploy.sh

# Clear caches
php artisan config:clear
php artisan cache:clear
```

---

**🎉 Congratulations!** Your MobileHub application should now be live and accessible to users worldwide!

For additional support or custom deployment needs, consider consulting with a Laravel developer or your hosting provider's support team.