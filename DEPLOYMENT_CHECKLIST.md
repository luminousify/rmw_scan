# 🚀 Production Deployment Checklist

## ✅ **COMPLETED PRE-DEPLOYMENT FIXES**

### 1. ✅ **Debug Mode Disabled**
- Removed hardcoded `debugMode: true` from `app/rmw_dashboard.php`
- Now uses `debugMode: false` for production

### 2. ✅ **Hardcoded API Key Removed**
- Removed exposed API key from `NotificationService.php`
- Now requires `OM_MESSENGER_API_KEY` in `.env`

### 3. ✅ **Production Environment Template Created**
- File: `.env.production`
- Contains all required environment variables
- **ACTION REQUIRED**: Update with actual production values before deploy

### 4. ✅ **Health Check Endpoint Created**
- File: `health_check.php`
- Endpoint: `http://your-domain.com/health_check.php`
- Returns JSON with system health status

### 5. ✅ **Security Configuration Created**
- File: `.htaccess.production`
- Blocks access to sensitive files
- Enables security headers
- Enables gzip compression

### 6. ✅ **Deployment Script Created**
- File: `deploy_production.sh`
- Automated deployment steps
- Backup, cleanup, and configuration

---

## ⚠️ **CRITICAL ACTIONS BEFORE DEPLOY**

### **Step 1: Create Production .env File**
```bash
# On production server
cp .env.production .env
nano .env
```

**UPDATE THESE VALUES:**
```
DB_MYSQL_PASS=<STRONG_PASSWORD_HERE>
OM_MESSENGER_API_KEY=<YOUR_PRODUCTION_API_KEY>
```

### **Step 2: Build Production CSS**
```bash
npm run build-prod
```

### **Step 3: Remove Development Files**
```bash
# Delete these files before deploying
rm test_*.php
rm fix_*.php
rm diagnose_*.php
rm debug_*.php
rm validate_*.php
rm check_*.php
rm *_test.html
rm *.mdb
rm *.accdb
rm bash.exe.stackdump
```

### **Step 4: Set File Permissions**
```bash
chmod 600 .env              # CRITICAL - .env must be protected
chmod 644 config.php
chmod 644 .htaccess          # After copying from .htaccess.production
chmod 755 app/
```

### **Step 5: Test Database Connection**
```bash
php health_check.php
```

Expected output:
```json
{
  "status": "healthy",
  "checks": {
    "database": {
      "status": "pass",
      "message": "Database connected"
    }
  }
}
```

---

## 🔐 **SECURITY REMINDERS**

1. **CHANGE DEFAULT PASSWORDS**
   - Production user: `production123`
   - RMW user: `rmw12345`
   - Admin user: `admin123`

2. **ENABLE HTTPS**
   - Install SSL certificate
   - Force HTTPS redirect in `.htaccess`

3. **SET UP DATABASE BACKUPS**
   ```bash
   # Daily backup cron job
   0 2 * * * mysqldump -u root -p rmw_system > /backups/rmw_system_$(date +\%Y\%m\%d).sql
   ```

4. **MONITOR LOGS**
   ```bash
   tail -f includes/conn.log
   tail -f /var/log/apache2/error.log
   ```

---

## 📋 **POST-DEPLOYMENT TESTING**

### **Core Functionality Checklist:**
- [ ] User login works
- [ ] Create material request
- [ ] View my requests
- [ ] RMW dashboard loads
- [ ] Approve/reject requests
- [ ] Real-time updates work
- [ ] Notifications send (OM Messenger)
- [ ] Search/filter works
- [ ] Pagination works
- [ ] QR scanning works

---

## 🚨 **ROLLBACK PROCEDURE**

If deployment fails:
```bash
# Restore from backup
cd backups/<timestamp>
cp -r ..//* .
```

---

## 📞 **SUPPORT**

For issues or questions:
- Check logs: `includes/conn.log`
- Test config: `php health_check.php`
- Review deployment guide: `DEPLOYMENT_GUIDE.md`

---

**Estimated Deployment Time:** 30-45 minutes (if database already exists)
