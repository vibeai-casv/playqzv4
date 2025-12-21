---
description: Test project on development server (XAMPP + Vite)
---

# Development Server Testing Workflow

This workflow starts the local development server for testing the AIQuiz application.

## Prerequisites

- XAMPP installed (for MySQL and optional Apache)
- Node.js and npm installed
- Database `aiqz` created and populated

## Steps

### 1. Start XAMPP MySQL Service

Open XAMPP Control Panel and start MySQL:

```powershell
# Option A: Open XAMPP Control Panel
Start-Process "C:\xampp\xampp-control.exe"

# Option B: Start MySQL via command line
net start mysql
```

### 2. Verify Database Connection

Check that the database is accessible:

```powershell
# Test database connection
php e:/projects/playqzv4/test-db.php
```

Expected output: Database connection successful

### 3. Start Frontend Dev Server

// turbo
```powershell
cd e:/projects/playqzv4/client
npm run dev
```

This starts the Vite development server at **http://localhost:5173**

### 4. Test the Application

Open your browser and navigate to:
- **Frontend**: http://localhost:5173
- **Admin Login**: http://localhost:5173/admin

### 5. Verify API Connectivity

The frontend dev server is configured to proxy API requests to the backend. Check browser console (F12) for any API errors.

## Default Credentials

If using the development database:
- **Email**: admin@example.com or test@example.com
- **Password**: admin123 or test123

## Troubleshooting

### Issue: MySQL won't start
- Check if port 3306 is already in use
- Stop other MySQL services
- Check XAMPP error logs

### Issue: Frontend won't start
- Run `npm install` in the client directory
- Clear node_modules and reinstall: `rm -rf node_modules && npm install`
- Check if port 5173 is available

### Issue: API connection errors
- Verify XAMPP MySQL is running
- Check database credentials in `api/config.php`
- Ensure database `aiqz` exists with proper schema

### Issue: Login fails
- Verify admin user exists in database
- Check browser console for error messages
- Verify API paths are correct

## Quick Stop

To stop the servers:
1. Press `Ctrl+C` in the terminal running `npm run dev`
2. Stop MySQL in XAMPP Control Panel (or `net stop mysql`)

## Notes

- The dev server supports hot module replacement (HMR) for instant updates
- API requests are proxied through Vite to avoid CORS issues
- Database changes require manual updates (no auto-sync)
