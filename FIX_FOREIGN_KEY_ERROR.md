# ✅ FOREIGN KEY ERROR FIX

## 🔍 The Problem

**Error:** Foreign key constraint violation on `created_by` field

**What it means:**
- The import tries to set `created_by` to your admin profile ID
- But your admin profile **doesn't exist** in the `profiles` table
- Database rejects the insert because of foreign key constraint

**Why this happened:**
- User exists in `users` table
- But matching profile missing from `profiles` table
- This can happen after database migrations or manual edits

---

## ✅ SOLUTION (Choose One)

### **Option 1: Quick Fix Script (Recommended)**

**Step 1:** Upload fix script
```
File: fix_profile.php
Upload to: /public_html/aiq3/api/fix_profile.php
```

**Step 2:** Run the script
```
Visit: https://aiquiz.vibeai.cv/aiq3/api/fix_profile.php
```

**Step 3:** You should see
```
✅ Profile created successfully!
Details:
- Email: vibeaicasv@gmail.com
- Role: admin
- ID: [your user ID]

You can now import questions successfully!
```

**Step 4:** Delete the script (IMPORTANT!)
```
Delete: /public_html/aiq3/api/fix_profile.php
(For security - don't leave it on production)
```

**Step 5:** Import questions again
```
Go back to Import/Export
Paste your JSON
Import should work now!
```

---

### **Option 2: SQL Fix (If you have database access)**

**Run this SQL on production database:**

```sql
-- Check if user exists
SELECT id, email FROM users WHERE email = 'vibeaicasv@gmail.com';
-- Copy the ID from result

-- Create profile (replace USER_ID with the ID from above)
INSERT INTO profiles (id, email, name, role, created_at, updated_at)
VALUES ('USER_ID', 'vibeaicasv@gmail.com', 'Admin User', 'admin', NOW(), NOW());

-- Verify it worked
SELECT * FROM profiles WHERE email = 'vibeaicasv@gmail.com';
```

---

### **Option 3: Manual via phpMyAdmin**

**Step 1:** Login to phpMyAdmin on your hosting

**Step 2:** Select database: `rcdzrtua_aiquiz`

**Step 3:** Go to `users` table
- Find your admin user
- Copy the `id` value

**Step 4:** Go to `profiles` table
- Click "Insert"
- Fill in:
  - `id`: Paste the user ID you copied
  - `email`: vibeaicasv@gmail.com
  - `name`: Admin User
  - `role`: admin
  - `created_at`: Click "Function" → NOW()
  - `updated_at`: Click "Function" → NOW()
- Click "Go"

**Step 5:** Import questions again

---

## 🎯 Quick Verification

After fixing the profile:

**Check if profile exists:**
```sql
SELECT u.id, u.email, p.role 
FROM users u 
LEFT JOIN profiles p ON u.id = p.id 
WHERE u.email = 'vibeaicasv@gmail.com';
```

**Expected result:**
```
id: [some UUID]
email: vibeaicasv@gmail.com
role: admin
```

If `role` is NULL, profile is still missing!

---

## 📋 After Profile is Created

**Step 1: Try import again**
```
1. Go to: https://aiquiz.vibeai.cv/aiq3/admin/import-export
2. Paste JSON from: dsset1cp-25_READY.json
3. Click Import
```

**Step 2: Should see success**
```
✅ Successfully imported 25 questions!
```

**Step 3: View questions**
```
Admin → Questions
Filter: Type = Logo Identification
Filter: Status = All
Should see 25 questions!
```

---

## 🔍 Why This Error Occurred

**Database structure:**
```
users table:
- id (primary key)
- email
- password_hash

profiles table:
- id (references users.id)
- email
- name
- role

questions table:
- id
- question_text
- created_by (references profiles.id) ← FAILED HERE
```

**The import tries:**
```
INSERT INTO questions (..., created_by)
VALUES (..., 'your_user_id')
```

**But fails because:**
```
'your_user_id' doesn't exist in profiles table!
```

---

## 💡 Prevention

**For future deployments:**

When creating admin user, always create BOTH:
```sql
-- 1. Create user
INSERT INTO users (id, email, password_hash, created_at)
VALUES (UUID(), 'email@domain.com', 'hash', NOW());

-- 2. Create matching profile (same ID!)
INSERT INTO profiles (id, email, name, role, created_at, updated_at)
VALUES (LAST_INSERT_ID(), 'email@domain.com', 'Name', 'admin', NOW(), NOW());
```

---

## 🚨 Security Warning

**DO NOT leave fix_profile.php on production!**

After running it:
1. ✅ Verify profile created
2. ✅ Test import works
3. ❌ **DELETE fix_profile.php immediately**

Leaving it exposed is a security risk.

---

## 📞 Still Having Issues?

**Check these:**

1. **User exists?**
   ```sql
   SELECT * FROM users WHERE email = 'vibeaicasv@gmail.com';
   ```

2. **Profile exists?**
   ```sql
   SELECT * FROM profiles WHERE email = 'vibeaicasv@gmail.com';
   ```

3. **IDs match?**
   ```sql
   SELECT u.id as user_id, p.id as profile_id
   FROM users u
   LEFT JOIN profiles p ON u.id = p.id
   WHERE u.email = 'vibeaicasv@gmail.com';
   ```
   
   Both IDs should be the same!

4. **Still failing?**
   - Check error logs: `/public_html/aiq3/api/error.log`
   - Verify database name in config.php
   - Contact hosting support

---

## ✅ Summary

**Problem:** Missing profile breaks foreign key constraint

**Solution:** Create the missing profile using one of:
- ⭐ Upload and run `fix_profile.php` (easiest)
- SQL via phpMyAdmin
- Direct SQL query

**After fix:** Import will work! ✨

