# 🔐 Fix "Unauthorized. Admin access required"

## Issue

Import now says: **"Unauthorized. Admin access required."**

This means `verifyAuth()` is working, but either:
1. You're not logged in
2. You're logged in but not as admin
3. Session is not being passed correctly

---

## 🧪 Quick Diagnostic

### Step 1: Upload Session Test

Upload this file:
```
Local:  e:\projects\playqzv4\fix\test-session.php
Remote: /public_html/fix/test-session.php
```

### Step 2: Run Session Diagnostic

**IMPORTANT**: Do this while logged in to the site!

1. **Login** to `https://aiquiz.vibeai.cv` first
2. **Then visit**: `https://aiquiz.vibeai.cv/fix/test-session.php`

This will show:
- If you're logged in
- Your user role
- If you're an admin
- Why import is failing

---

## 🔍 Common Causes

### Cause 1: Not Logged In

**Symptom**:
```json
{
  "logged_in": false,
  "error": "No user_id in session - not logged in"
}
```

**Fix**: Login to the site first, then try import

---

### Cause 2: Logged In But Not Admin

**Symptom**:
```json
{
  "logged_in": true,
  "user_data": {
    "role": "user"  ← Not admin!
  },
  "is_admin": false
}
```

**Fix**: Login with an admin account

---

### Cause 3: Session Not Shared

**Symptom**:
```json
{
  "logged_in": false,
  "has_session_cookie": false
}
```

**Fix**: CORS/cookie issue - need to fix session sharing

---

### Cause 4: No Admin Users Exist

**Symptom**:
```json
{
  "admin_count": 0
}
```

**Fix**: Create an admin user

---

## ✅ Quick Fixes

### Fix 1: Make Sure You're Logged In

1. Go to `https://aiquiz.vibeai.cv`
2. **Login** with your credentials
3. **Then** try import again

### Fix 2: Check Your Role

Run the session diagnostic:
```
https://aiquiz.vibeai.cv/fix/test-session.php
```

Look for:
```json
{
  "user_data": {
    "role": "admin"  ← Should be "admin"
  }
}
```

### Fix 3: Create Admin User (if needed)

If no admin exists, I can create a script to make your user an admin.

---

## 🎯 Step-by-Step

1. **Upload** `test-session.php` to `/public_html/fix/`

2. **Login** to your site:
   ```
   https://aiquiz.vibeai.cv
   ```

3. **Run diagnostic** (while still logged in):
   ```
   https://aiquiz.vibeai.cv/fix/test-session.php
   ```

4. **Copy the output** and send it to me

5. I'll tell you exactly what to fix!

---

## 📊 What to Look For

### ✅ Good (Should Work):
```json
{
  "logged_in": true,
  "user_data": {
    "email": "your@email.com",
    "role": "admin"
  },
  "is_admin": true,
  "can_import": true
}
```

### ❌ Problem (Won't Work):
```json
{
  "logged_in": false
}
```
or
```json
{
  "logged_in": true,
  "user_data": {
    "role": "user"  ← Not admin
  }
}
```

---

## 🔧 If You Need Admin Access

If the diagnostic shows you're not an admin, I can create a script to:

1. Make your current user an admin
2. Or create a new admin user

Just let me know the email you're logged in with!

---

**Upload test-session.php and run it while logged in!** 🔍
