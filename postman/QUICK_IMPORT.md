# 📦 Quick Import Guide

## 🚀 Import LaraBaseX Postman Collection (30 seconds)

### **Step 1: Import Collection**
1. Open Postman
2. Click **"Import"** (top-left)
3. Drag & drop or select: `postman/LaraBaseX-API-Collection.json`
4. Click **"Import"**

### **Step 2: Import Environment**
1. Click **"Import"** again
2. Drag & drop or select: `postman/LaraBaseX-Local-Environment.json`
3. Click **"Import"**

### **Step 3: Set Environment**
1. Click environment dropdown (top-right)
2. Select **"LaraBaseX Local Environment"**
3. ✅ Environment is now active!

---

## 🧪 Test Authentication (2 minutes)

### **Step 4: Test Login Flow**
1. Open collection: **"LaraBaseX API Collection"**
2. Navigate to: **"🔐 Authentication"** → **"Login with Mobile"**
3. Click **"Send"**
4. ✅ OTP sent! (Check server logs for OTP code)

### **Step 5: Verify OTP**
1. Navigate to: **"🔐 Authentication"** → **"Verify OTP"**
2. Update OTP in request body (use `1234` for testing)
3. Click **"Send"**
4. ✅ Access token automatically saved to environment!

### **Step 6: Test Authenticated Request**
1. Navigate to: **"👤 User Management"** → **"Get User Profile"**
2. Click **"Send"**
3. ✅ Returns authenticated user data!

---

## 🎯 What You Get

- ✅ **10 Pre-configured API endpoints**
- ✅ **Automated token management**
- ✅ **Complete authentication flow**
- ✅ **Error handling examples**
- ✅ **Performance monitoring**
- ✅ **Environment variables setup**

---

## 📚 Need Help?

- **Complete Documentation**: `postman/README.md`
- **Server Logs**: `storage/logs/laravel.log` (for OTP codes)
- **Default OTP**: `1234` (for testing)
- **API Documentation**: http://localhost:8001/api/documentation

---

**🎉 You're ready to test all LaraBaseX APIs!**
