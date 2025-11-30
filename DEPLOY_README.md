# Deployment Guide - AI Quiz Application

## 🎯 Overview

This guide helps you deploy the AI Quiz application to your production server at **aiquiz.vibeai.cv**.

The application consists of:
- **Frontend**: React + Vite + TypeScript (SPA)
- **Backend**: PHP REST API
- **Database**: MySQL

## 📚 Documentation

- **[Complete Deployment Guide](.agent/workflows/deploy-production.md)** - Detailed step-by-step instructions
- **[Quick Reference](DEPLOY_QUICK_REF.md)** - Essential commands and configurations
- **[Deployment Checklist](deploy-checklist.md)** - Comprehensive checklist

## 🚀 Quick Start

### Prerequisites

- SSH access to aiquiz.vibeai.cv
- MySQL database on production server
- Web server (Apache/Nginx) configured
- SSL certificate (recommended)

### 1. Prepare Locally

```powershell
# Update deployment script with your credentials
# Edit deploy.ps1 and set REMOTE_USER

# Build the frontend
cd client
npm install
npm run build
cd ..
```

### 2. Deploy

```powershell
# Run automated deployment script
.\deploy.ps1
```

Or manually:

```bash
# Upload frontend
scp -r client/dist/* user@aiquiz.vibeai.cv:/var/www/aiquiz.vibeai.cv/public/

# Upload API
scp -r api/* user@aiquiz.vibeai.cv:/var/www/aiquiz.vibeai.cv/api/
```

### 3. Server Setup (First Time Only)

On your production server:

```bash
# Upload and run server setup script
scp server-setup.sh user@aiquiz.vibeai.cv:~/
ssh user@aiquiz.vibeai.cv
sudo bash server-setup.sh
```

This will:
- Install Apache, PHP, MySQL
- Create database and user
- Configure virtual host
- Set up SSL with Certbot
- Create backup scripts

### 4. Configure Database

```bash
# Import schema
mysql -u aiqz_user -p aiqz_production < api/schema.sql

# Create admin user (see DEPLOY_QUICK_REF.md)
```

### 5. Test

Visit: https://aiquiz.vibeai.cv

## 📁 Project Structure

```
playqzv4/
├── client/                 # Frontend React app
│   ├── src/               # Source code
│   ├── dist/              # Production build (generated)
│   ├── .env.production    # Production environment variables
│   └── package.json
│
├── api/                   # Backend PHP API
│   ├── auth/             # Authentication endpoints
│   ├── quiz/             # Quiz endpoints
│   ├── admin/            # Admin endpoints
│   ├── config.php        # Configuration (local)
│   ├── config.production.php  # Production config template
│   ├── db.php            # Database connection
│   ├── schema.sql        # Database schema
│   └── utils.php         # Utility functions
│
├── .agent/workflows/
│   └── deploy-production.md   # Complete deployment guide
│
├── deploy.ps1            # Windows deployment script
├── deploy.sh             # Linux/Mac deployment script
├── server-setup.sh       # Server setup script
├── deploy-checklist.md   # Deployment checklist
└── DEPLOY_QUICK_REF.md   # Quick reference
```

## 🔧 Configuration

### Frontend (.env.production)

```env
VITE_API_URL=https://aiquiz.vibeai.cv/api
```

### Backend (api/config.production.php)

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'aiqz_production');
define('DB_USER', 'aiqz_user');
define('DB_PASS', 'your_password');
define('ALLOWED_ORIGIN', 'https://aiquiz.vibeai.cv');
```

## 🗄️ Database

### Schema

The database schema is in `api/schema.sql` and includes:
- `users` - User authentication
- `sessions` - Session management
- `profiles` - User profiles
- `questions` - Quiz questions
- `quiz_attempts` - Quiz attempts
- `quiz_responses` - User answers
- `media_library` - Media files
- `user_activity_logs` - Activity tracking

### Admin User

Default credentials after setup:
- **Email**: vibeaicasv@gmail.com
- **Password**: password123

⚠️ **Change the password immediately after first login!**

## 🔒 Security

- [ ] SSL certificate installed and auto-renewing
- [ ] Production config.php with secure credentials
- [ ] Database user with limited privileges
- [ ] File permissions set correctly (755/644)
- [ ] Firewall configured
- [ ] Error display disabled in production
- [ ] Security headers configured

## 📊 Monitoring

### Logs

```bash
# Apache errors
tail -f /var/log/aiquiz/error.log

# Apache access
tail -f /var/log/aiquiz/access.log

# MySQL errors
tail -f /var/log/mysql/error.log
```

### Health Checks

```bash
# Website
curl https://aiquiz.vibeai.cv

# API
curl https://aiquiz.vibeai.cv/api/index.php

# Database
mysql -u aiqz_user -p aiqz_production -e "SELECT 1"
```

## 💾 Backups

Automated backup script is created during server setup:

```bash
# Manual backup
sudo /usr/local/bin/backup-aiquiz.sh

# Schedule daily backups (add to crontab)
0 2 * * * /usr/local/bin/backup-aiquiz.sh
```

Backups are stored in `/var/backups/aiquiz/`

## 🔄 Updates

To deploy updates:

```powershell
# 1. Build new version
cd client
npm run build
cd ..

# 2. Deploy
.\deploy.ps1

# 3. Test
# Visit https://aiquiz.vibeai.cv
```

## 🆘 Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| 404 on page refresh | Check Apache rewrite rules |
| CORS errors | Verify ALLOWED_ORIGIN in config.php |
| Database connection failed | Check credentials and MySQL status |
| 500 Internal Server Error | Check error logs and file permissions |
| SSL certificate errors | Renew with `sudo certbot renew` |

### Getting Help

1. Check error logs
2. Review [Troubleshooting section](DEPLOY_QUICK_REF.md#-troubleshooting)
3. Verify configuration files
4. Test database connection
5. Check file permissions

## 📋 Deployment Checklist

Before deploying, ensure:

- [ ] Frontend builds successfully
- [ ] All tests pass locally
- [ ] Configuration files updated
- [ ] Database credentials secured
- [ ] Backup of current production (if updating)
- [ ] SSL certificate valid
- [ ] DNS pointing to server

After deploying:

- [ ] Website loads correctly
- [ ] Login works
- [ ] API endpoints respond
- [ ] Database queries execute
- [ ] No console errors
- [ ] Admin dashboard accessible
- [ ] SSL certificate valid
- [ ] Backups scheduled

## 🎯 Success Criteria

Your deployment is successful when:

✅ Website loads at https://aiquiz.vibeai.cv  
✅ SSL certificate is valid (green padlock)  
✅ Admin can login  
✅ Users can register and login  
✅ Quizzes can be created and taken  
✅ Results are saved and displayed  
✅ No errors in browser console  
✅ No errors in server logs  

## 📞 Support

- **Server**: aiquiz.vibeai.cv
- **Database**: aiqz_production
- **Admin Email**: vibeaicasv@gmail.com

## 📖 Additional Resources

- [API Documentation](README_API.md)
- [Quick Reference](QUICK_REFERENCE.md)
- [Database Schema](api/schema.sql)
- [OpenAPI Specification](openapi.yaml)

---

**Last Updated**: 2025-11-29  
**Version**: 1.0.0  
**Status**: Ready for deployment
