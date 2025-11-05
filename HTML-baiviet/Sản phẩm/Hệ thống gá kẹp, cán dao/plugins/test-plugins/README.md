# Test Plugins - Usage Instructions

## 📦 Available Test Plugins

### 1. test-clean-plugin
**Purpose:** Test successful upload and activation  
**Expected:** Safe upload + safe activate thành công

### 2. test-fatal-error
**Purpose:** Test watchdog recovery  
**Expected:** Fatal error → watchdog deactivate + quarantine

### 3. test-dangerous-code
**Purpose:** Test security scan rejection  
**Expected:** Upload rejected do dangerous patterns detected

---

## 🎯 How to Use

### Create ZIP files

```bash
# Clean plugin
cd plugins/test-plugins/test-clean-plugin
zip -r ../test-clean-plugin.zip .

# Fatal error plugin
cd ../test-fatal-error
zip -r ../test-fatal-error.zip .

# Dangerous code plugin
cd ../test-dangerous-code
zip -r ../test-dangerous-code.zip .
```

### Test Sequence

1. **Test Clean Plugin First**
   - Upload → Success
   - Activate → Success
   - Verify working

2. **Test Dangerous Code**
   - Upload → Rejected by security scan
   - Check logs

3. **Test Fatal Error (CAREFUL!)**
   - Backup site first
   - Upload → Success
   - Activate → Fatal error → Watchdog recovery
   - Check quarantine folder
   - Re-enable watchdog

---

## ⚠️ Warnings

- **test-fatal-error:** WILL crash site temporarily (watchdog recovers)
- Always test trên staging environment trước
- Keep backups ready
- Monitor watchdog logs

---

## 📝 Expected Outcomes

| Plugin | Upload | Security Scan | Activate | Watchdog |
|--------|--------|---------------|----------|----------|
| test-clean-plugin | ✅ | ✅ Pass | ✅ | Not triggered |
| test-dangerous-code | ❌ | ❌ Reject | N/A | N/A |
| test-fatal-error | ✅ | ✅ Pass | ❌ Fatal | ✅ Recovers |
