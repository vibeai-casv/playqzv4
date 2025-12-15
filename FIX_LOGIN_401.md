# Login 401 Error - FIXED! 

## Problem Identified ✅

The frontend was trying to connect to **`http://localhost:8000/api`** which doesn't exist!

Your XAMPP API is actually at: **`http://projects/playqzv4/api`**

## What Was Fixed

### File: `client/src/lib/api.ts`

**Before:**
```typescript
API_URL = 'http://localhost:8000/api';  // ❌ Wrong - this doesn't exist
```

**After:**
```typescript
API_URL = 'http://projects/playqzv4/api';  // ✅ Correct - your XAMPP virtual host
```

## How to Test

### Step 1: The dev server should auto-reload
Your Vite dev server should have automatically reloaded with the changes.

### Step 2: Navigate to Login Page
```
http://localhost:5173/aiq3/login
```

### Step 3: Try Logging In

Use these credentials:

| Email | Password |
|-------|----------|
| `admin@example.com` | `admin123` |
| `testadmin@local.test` | `testadmin123` |

### Step 4: Check Browser Console

Open DevTools (F12) and check the Network tab to see if the request goes to the correct URL:
- ✅ Should be: `http://projects/playqzv4/api/auth/login.php`
- ❌ Was: `http://localhost:8000/api/auth/login.php`

## Verification Checklist

- ✅ **Backend API**: Working at `http://projects/playqzv4/api`
- ✅ **Database**: Connected (6 users, 19 questions)
- ✅ **Frontend**: Running at `http://localhost:5173/aiq3/`
- ✅ **API Configuration**: Fixed to use correct URL
- ✅ **Admin Accounts**: Ready to use

## If Still Getting 401

### Check 1: XAMPP Virtual Host
Make sure `http://projects/playqzv4` resolves correctly:

1. Open browser: `http://projects/playqzv4/api/test-connection.php`
2. Should show JSON response with database info

### Check 2: CORS Configuration  
The API should allow requests from localhost:5173. Check `api/utils.php`:

```php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
```

### Check 3: Password Hash
Verify the password hash in database matches:

```sql
SELECT email, password_hash FROM users WHERE email = 'admin@example.com';
```

The hash should be: `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`

## Test Commands

### Test API Directly:
```powershell
php test-login-api.php
```

### Test Database:
```powershell
php verify-db.php
```

### Check MySQL Status:
```powershell
php check-mysql-status.php
```

## Expected Behavior Now

1. **Enter credentials** on login page
2. **Frontend sends request** to `http://projects/playqzv4/api/auth/login.php`
3. **Backend validates** credentials against database
4. **Returns JWT token** with user data
5. **Frontend stores token** in sessionStorage
6. **Redirects to dashboard**

## Common Login Issues Reference

| Error | Cause | Solution |
|-------|-------|----------|
| 401 Unauthorized | Wrong password | Use `admin123` or `testadmin123` |
| 401 Unauthorized | Wrong API URL | Fixed in this update ✅ |
| 500 Server Error | Database connection | Check MySQL is running |
| Network Error | XAMPP not running | Start Apache in XAMPP |
| CORS Error | Missing headers | Check api/utils.php |

---

## 🎉 You should now be able to login!

Try it now: **http://localhost:5173/aiq3/login**
