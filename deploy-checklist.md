# Production Deployment Checklist for aiquiz.vibeai.cv

## Pre-Deployment

- [ ] **Backup current production** (if updating existing deployment)
  - [ ] Database backup
  - [ ] Files backup
  - [ ] Configuration backup

- [ ] **Test locally**
  - [ ] Frontend builds successfully
  - [ ] All API endpoints work
  - [ ] Database migrations tested
  - [ ] No console errors

- [ ] **Update configuration files**
  - [ ] `client/.env.production` - API URL set to production
  - [ ] `api/config.production.php` - Database credentials updated
  - [ ] CORS settings configured

## Build & Prepare

- [ ] **Build frontend**
  ```bash
  cd client
  npm install
  npm run build
  ```

- [ ] **Verify build output**
  - [ ] `client/dist/index.html` exists
  - [ ] `client/dist/assets/` contains JS/CSS files
  - [ ] No build errors or warnings

- [ ] **Prepare API files**
  - [ ] Remove development files (test files, etc.)
  - [ ] Update `config.php` or use `config.production.php`
  - [ ] Verify all required PHP files are present

## Server Setup

- [ ] **SSH Access**
  - [ ] Can connect to server: `ssh user@aiquiz.vibeai.cv`
  - [ ] Have sudo privileges

- [ ] **Database Setup**
  - [ ] MySQL installed and running
  - [ ] Production database created: `aiqz_production`
  - [ ] Database user created with proper permissions
  - [ ] Schema imported successfully
  - [ ] Test database connection

- [ ] **Web Server Configuration**
  - [ ] Apache/Nginx installed
  - [ ] Virtual host/server block configured
  - [ ] Rewrite rules enabled (for SPA routing)
  - [ ] PHP configured correctly
  - [ ] Test configuration syntax

## File Upload

- [ ] **Upload files to server**
  - [ ] Frontend files → `/var/www/aiquiz.vibeai.cv/public/`
  - [ ] API files → `/var/www/aiquiz.vibeai.cv/api/`
  - [ ] Verify all files uploaded correctly

- [ ] **Set permissions**
  - [ ] Ownership: `www-data:www-data`
  - [ ] Directories: 755
  - [ ] Files: 644
  - [ ] PHP files: 755

## SSL/HTTPS

- [ ] **SSL Certificate**
  - [ ] Certificate installed (Let's Encrypt or other)
  - [ ] HTTPS working
  - [ ] HTTP → HTTPS redirect enabled
  - [ ] Certificate auto-renewal configured

## Database

- [ ] **Import schema**
  - [ ] `schema.sql` executed successfully
  - [ ] All tables created
  - [ ] Verify table structure

- [ ] **Create admin user**
  - [ ] Admin account created
  - [ ] Can login with admin credentials
  - [ ] Admin role verified

## Testing

- [ ] **Frontend Tests**
  - [ ] Website loads at `https://aiquiz.vibeai.cv`
  - [ ] No 404 errors on page refresh
  - [ ] No console errors
  - [ ] Assets loading correctly
  - [ ] Responsive design works

- [ ] **API Tests**
  - [ ] Health check endpoint works
  - [ ] Login endpoint works
  - [ ] Protected endpoints require authentication
  - [ ] CORS headers correct
  - [ ] Error handling works

- [ ] **Database Tests**
  - [ ] Can connect from API
  - [ ] Queries execute successfully
  - [ ] Foreign keys working
  - [ ] Indexes created

- [ ] **User Flow Tests**
  - [ ] User registration works
  - [ ] Login/logout works
  - [ ] Quiz creation works
  - [ ] Quiz taking works
  - [ ] Results display correctly
  - [ ] Admin dashboard accessible

## Security

- [ ] **Security Hardening**
  - [ ] Remove test/debug files
  - [ ] Disable error display in production
  - [ ] Enable error logging
  - [ ] Firewall configured
  - [ ] Fail2ban configured (optional)
  - [ ] Database credentials secure
  - [ ] File permissions correct
  - [ ] `.git` folder not publicly accessible

- [ ] **Headers & CORS**
  - [ ] Security headers configured
  - [ ] CORS properly configured
  - [ ] CSP headers (if needed)

## Monitoring & Maintenance

- [ ] **Logging**
  - [ ] Error logs accessible
  - [ ] Access logs enabled
  - [ ] Log rotation configured

- [ ] **Backups**
  - [ ] Automated backup script created
  - [ ] Backup schedule configured (cron)
  - [ ] Test restore from backup
  - [ ] Backup retention policy set

- [ ] **Monitoring**
  - [ ] Server monitoring setup
  - [ ] Database monitoring
  - [ ] Disk space monitoring
  - [ ] Uptime monitoring (optional)

## Post-Deployment

- [ ] **Documentation**
  - [ ] Deployment date recorded
  - [ ] Server details documented
  - [ ] Credentials stored securely
  - [ ] Rollback procedure documented

- [ ] **Notifications**
  - [ ] Team notified of deployment
  - [ ] Users notified (if applicable)
  - [ ] Stakeholders informed

- [ ] **Final Checks**
  - [ ] All features working
  - [ ] Performance acceptable
  - [ ] No critical errors in logs
  - [ ] Admin can access dashboard
  - [ ] Users can complete quizzes

## Rollback Plan

If something goes wrong:

- [ ] **Database rollback**
  ```bash
  mysql -u aiqz_user -p aiqz_production < backup.sql
  ```

- [ ] **Files rollback**
  ```bash
  tar -xzf backup_files.tar.gz -C /var/www/aiquiz.vibeai.cv
  ```

- [ ] **Restart services**
  ```bash
  sudo systemctl restart apache2  # or nginx
  sudo systemctl restart mysql
  ```

## Success Criteria

✅ **Deployment is successful when:**

1. Website loads at `https://aiquiz.vibeai.cv`
2. SSL certificate is valid (green padlock)
3. Admin can login
4. Users can register and login
5. Quizzes can be created and taken
6. Results are saved and displayed
7. No errors in browser console
8. No errors in server logs
9. API responds correctly
10. Database queries work

---

**Deployment Information**

- **Date**: _______________
- **Deployed by**: _______________
- **Server**: aiquiz.vibeai.cv
- **Database**: aiqz_production
- **Version**: _______________
- **Notes**: _______________

---

## Quick Commands Reference

### Build
```bash
cd client && npm run build
```

### Upload (SCP)
```bash
scp -r client/dist/* user@aiquiz.vibeai.cv:/var/www/aiquiz.vibeai.cv/public/
scp -r api/* user@aiquiz.vibeai.cv:/var/www/aiquiz.vibeai.cv/api/
```

### Database Import
```bash
mysql -u aiqz_user -p aiqz_production < api/schema.sql
```

### Restart Services
```bash
sudo systemctl restart apache2  # or nginx
sudo systemctl restart mysql
```

### View Logs
```bash
tail -f /var/log/apache2/aiquiz_error.log
tail -f /var/log/mysql/error.log
```

### Backup
```bash
mysqldump -u aiqz_user -p aiqz_production > backup_$(date +%Y%m%d).sql
tar -czf backup_$(date +%Y%m%d).tar.gz /var/www/aiquiz.vibeai.cv
```
