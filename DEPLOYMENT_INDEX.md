# 🚀 Production Deployment - Start Here

Welcome! This directory contains everything you need to deploy your AI Quiz application to **aiquiz.vibeai.cv**.

## 📖 Documentation Index

### 🎯 Start Here (Choose One)

1. **[FIRST_TIME_DEPLOYMENT.md](FIRST_TIME_DEPLOYMENT.md)** ⭐ **RECOMMENDED FOR BEGINNERS**
   - Simple step-by-step guide
   - Perfect for first-time deployment
   - Includes troubleshooting
   - **Start here if this is your first deployment**

2. **[DEPLOYMENT_SUMMARY.md](DEPLOYMENT_SUMMARY.md)** 📋 **QUICK OVERVIEW**
   - Overview of all deployment files
   - Quick start instructions
   - What's included in the deployment package
   - **Start here for a quick overview**

### 📚 Detailed Guides

3. **[.agent/workflows/deploy-production.md](.agent/workflows/deploy-production.md)** 📖 **COMPLETE GUIDE**
   - Comprehensive deployment instructions
   - All configuration options
   - Advanced topics
   - **Reference this for detailed information**

4. **[DEPLOY_QUICK_REF.md](DEPLOY_QUICK_REF.md)** ⚡ **QUICK REFERENCE**
   - Essential commands
   - Common configurations
   - Troubleshooting tips
   - **Use this for daily operations**

5. **[deploy-checklist.md](deploy-checklist.md)** ✅ **CHECKLIST**
   - Pre-deployment checklist
   - Post-deployment verification
   - Security checklist
   - **Use this to ensure nothing is missed**

6. **[DEPLOY_FTP_GUIDE.md](DEPLOY_FTP_GUIDE.md)** 📤 **FTP DEPLOYMENT** ⭐ **NO SSH REQUIRED**
   - Deploy using FTP only
   - Perfect for shared hosting
   - Uses FileZilla and cPanel
   - **Use this if you don't have SSH access**

### 🏗️ Technical Documentation

6. **[ARCHITECTURE.md](ARCHITECTURE.md)** 🏛️ **ARCHITECTURE**
   - System architecture diagrams
   - Data flow
   - Security layers
   - **Understand how everything works**

7. **[DEPLOY_README.md](DEPLOY_README.md)** 📄 **README**
   - Project structure
   - Configuration details
   - Success criteria
   - **General deployment information**

## 🛠️ Deployment Scripts

### Automated Deployment (SSH Required)

- **[deploy.ps1](deploy.ps1)** - Windows PowerShell deployment script
- **[deploy.sh](deploy.sh)** - Linux/Mac Bash deployment script

### FTP Deployment (No SSH Required)

- **[prepare-ftp-upload.ps1](prepare-ftp-upload.ps1)** - Prepare files for FTP upload

### Server Setup

- **[server-setup.sh](server-setup.sh)** - Initial server configuration script

## ⚙️ Configuration Files

- **[client/.env.production](client/.env.production)** - Frontend production environment
- **[api/config.production.php](api/config.production.php)** - Backend production config template

## 🎯 Quick Start Guide

### For First-Time Deployment (SSH Access)

```
1. Read: FIRST_TIME_DEPLOYMENT.md
2. Update: deploy.ps1 (your SSH username)
3. Run: .\deploy.ps1
4. Follow the steps in FIRST_TIME_DEPLOYMENT.md
```

### For FTP Deployment (No SSH Access)

```powershell
1. Read: DEPLOY_FTP_GUIDE.md
2. Run: .\prepare-ftp-upload.ps1
3. Edit: upload_package\api\config.php (database credentials)
4. Upload files via FileZilla
5. Follow DEPLOY_FTP_GUIDE.md for database setup
```

### For Subsequent Deployments

```powershell
# Build and deploy
cd client
npm run build
cd ..
.\deploy.ps1  # or use FTP if no SSH
```

## 📋 Deployment Workflow

```
┌─────────────────────────────────────────────────┐
│  1. First Time?                                 │
│     YES → Read FIRST_TIME_DEPLOYMENT.md         │
│     NO  → Skip to step 3                        │
└─────────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────┐
│  2. Server Setup (First Time Only)              │
│     - Upload server-setup.sh                    │
│     - Run: sudo bash server-setup.sh            │
│     - Save database credentials                 │
└─────────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────┐
│  3. Update Configuration                        │
│     - deploy.ps1: Set REMOTE_USER               │
│     - api/config.production.php: Set DB_PASS    │
└─────────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────┐
│  4. Build & Deploy                              │
│     - Run: .\deploy.ps1                         │
│     - Wait for completion                       │
└─────────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────┐
│  5. Database Setup (First Time Only)            │
│     - Import schema.sql                         │
│     - Create admin user                         │
└─────────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────┐
│  6. SSL Certificate (First Time Only)           │
│     - Run: sudo certbot --apache -d domain      │
└─────────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────┐
│  7. Test & Verify                               │
│     - Visit: https://aiquiz.vibeai.cv           │
│     - Test login                                │
│     - Check functionality                       │
└─────────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────────┐
│  8. Post-Deployment                             │
│     - Change admin password                     │
│     - Set up backups                            │
│     - Monitor logs                              │
└─────────────────────────────────────────────────┘
```

## 🎓 Learning Path

### Beginner
1. Start with **FIRST_TIME_DEPLOYMENT.md**
2. Follow step-by-step
3. Use **deploy-checklist.md** to verify

### Intermediate
1. Review **DEPLOYMENT_SUMMARY.md**
2. Use **deploy.ps1** for automation
3. Reference **DEPLOY_QUICK_REF.md** as needed

### Advanced
1. Study **ARCHITECTURE.md**
2. Read complete guide in **.agent/workflows/deploy-production.md**
3. Customize scripts and configurations

## 🔍 Find What You Need

| I want to... | Read this... |
|--------------|--------------|
| Deploy for the first time | [FIRST_TIME_DEPLOYMENT.md](FIRST_TIME_DEPLOYMENT.md) |
| Deploy using FTP (no SSH) | [DEPLOY_FTP_GUIDE.md](DEPLOY_FTP_GUIDE.md) ⭐ |
| Get a quick overview | [DEPLOYMENT_SUMMARY.md](DEPLOYMENT_SUMMARY.md) |
| Find a specific command | [DEPLOY_QUICK_REF.md](DEPLOY_QUICK_REF.md) |
| Understand the architecture | [ARCHITECTURE.md](ARCHITECTURE.md) |
| Check if I missed anything | [deploy-checklist.md](deploy-checklist.md) |
| Learn about configuration | [DEPLOY_README.md](DEPLOY_README.md) |
| Get detailed instructions | [.agent/workflows/deploy-production.md](.agent/workflows/deploy-production.md) |
| Troubleshoot an issue | [DEPLOY_QUICK_REF.md#troubleshooting](DEPLOY_QUICK_REF.md#-troubleshooting) |
| Set up the server | [server-setup.sh](server-setup.sh) |
| Automate deployment | [deploy.ps1](deploy.ps1) |
| Prepare files for FTP | [prepare-ftp-upload.ps1](prepare-ftp-upload.ps1) |

## ⚡ Common Commands

```powershell
# Build frontend
cd client && npm run build && cd ..

# Deploy to production
.\deploy.ps1

# Connect to server
ssh your_username@aiquiz.vibeai.cv

# View logs
ssh your_username@aiquiz.vibeai.cv "tail -f /var/log/aiquiz/error.log"

# Create backup
ssh your_username@aiquiz.vibeai.cv "sudo /usr/local/bin/backup-aiquiz.sh"

# Restart web server
ssh your_username@aiquiz.vibeai.cv "sudo systemctl restart apache2"
```

## 🆘 Need Help?

### Quick Troubleshooting

1. **Website not loading**
   - Check DNS: `ping aiquiz.vibeai.cv`
   - Check Apache: `sudo systemctl status apache2`
   - Check logs: `tail -f /var/log/aiquiz/error.log`

2. **Can't login**
   - Verify database: `mysql -u aiqz_user -p aiqz_production`
   - Check API logs: `tail -f /var/log/aiquiz/error.log`
   - Verify config: `cat /var/www/aiquiz.vibeai.cv/api/config.php`

3. **Build fails**
   - Run: `cd client && npm install`
   - Clear cache: `npm cache clean --force`
   - Try again: `npm run build`

### Get More Help

- Check [DEPLOY_QUICK_REF.md#troubleshooting](DEPLOY_QUICK_REF.md#-troubleshooting)
- Review [FIRST_TIME_DEPLOYMENT.md#troubleshooting](FIRST_TIME_DEPLOYMENT.md#-troubleshooting)
- Check server logs for specific errors

## 📊 Deployment Status

### Pre-Deployment Checklist

- [ ] SSH access to server verified
- [ ] Domain DNS configured
- [ ] Local build succeeds
- [ ] Configuration files updated
- [ ] Credentials secured

### Post-Deployment Checklist

- [ ] Website loads at https://aiquiz.vibeai.cv
- [ ] SSL certificate valid
- [ ] Admin can login
- [ ] Quiz functionality works
- [ ] No console errors
- [ ] Backups scheduled
- [ ] Monitoring configured

## 🎯 Success Criteria

Your deployment is successful when:

✅ Website accessible at https://aiquiz.vibeai.cv  
✅ SSL certificate shows green padlock  
✅ Admin login works  
✅ Users can register and take quizzes  
✅ Results are saved to database  
✅ Admin dashboard displays analytics  
✅ No errors in logs  
✅ Automated backups running  

## 📞 Important Information

- **Production URL**: https://aiquiz.vibeai.cv
- **Server Path**: /var/www/aiquiz.vibeai.cv
- **Database**: aiqz_production
- **Admin Email**: vibeaicasv@gmail.com
- **Default Password**: password123 (change immediately!)

## 🔄 Version History

- **v1.0.0** (2025-11-29) - Initial deployment package created
  - Complete documentation suite
  - Automated deployment scripts
  - Server setup automation
  - Comprehensive guides and checklists

---

## 🚀 Ready to Deploy?

### First Time Deployers
👉 **Start with [FIRST_TIME_DEPLOYMENT.md](FIRST_TIME_DEPLOYMENT.md)**

### Experienced Users
👉 **Jump to [DEPLOYMENT_SUMMARY.md](DEPLOYMENT_SUMMARY.md)**

### Need Quick Reference
👉 **Use [DEPLOY_QUICK_REF.md](DEPLOY_QUICK_REF.md)**

---

**Good luck with your deployment!** 🎉

If you encounter any issues, refer to the troubleshooting sections in the documentation or check the server logs for specific error messages.
