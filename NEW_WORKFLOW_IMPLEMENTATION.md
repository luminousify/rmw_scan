# New Material Request Workflow Implementation

## Overview

The material request workflow has been updated to follow a 4-step process with proper role-based actions.

---

## New Workflow

```
1. Production User creates request
   ↓
   Status: pending (Menunggu)
   
2. RMW approves request
   ↓
   Status: approved (Disetujui)
   
3. RMW clicks "Sudah Siap" (Ready)
   ↓
   Status: ready (Sudah Siap)
   
4. Production User scans barcode & clicks Complete
   ↓
   Status: completed (Selesai)
```

---

## Status Definitions

### 1. **pending** (Menunggu)
- **Created by:** Production user
- **Color:** Yellow badge
- **Description:** New request waiting for RMW approval
- **Next Action:** RMW can approve

### 2. **approved** (Disetujui)
- **Set by:** RMW user
- **Color:** Blue badge
- **Description:** RMW has approved the request and will prepare materials
- **Fields Updated:** 
  - `processed_date` = approval timestamp
  - `approved_by` = RMW user name
- **Next Action:** RMW can mark as "Sudah Siap"

### 3. **ready** (Sudah Siap)
- **Set by:** RMW user
- **Color:** Purple badge
- **Description:** Materials are prepared and ready for pickup
- **Fields Updated:**
  - `ready_date` = ready timestamp
  - `ready_by` = RMW user name
- **Next Action:** Production user can scan and complete

### 4. **completed** (Selesai)
- **Set by:** Production user (after scanning)
- **Color:** Green badge
- **Description:** Materials verified and received
- **Fields Updated:**
  - `completed_date` = completion timestamp
  - `completed_by` = Production user name
- **Next Action:** Final status (no further changes)

### 5. **cancelled** (Dibatalkan)
- **Set by:** Any authorized user
- **Color:** Red badge
- **Description:** Request cancelled
- **Next Action:** Final status

---

## Implementation Details

### Database Changes

**Migration:** `database/migrations/005_add_new_statuses_approved_ready.php`

1. **Status ENUM Updated:**
   ```sql
   ENUM('pending', 'approved', 'ready', 'completed', 'cancelled')
   ```
   - Removed: `diproses`
   - Added: `approved`, `ready`

2. **New Fields Added:**
   - `ready_date` DATETIME - When RMW marks as ready
   - `approved_by` VARCHAR(100) - RMW user who approved
   - `ready_by` VARCHAR(100) - RMW user who marked as ready

3. **Data Migration:**
   - Existing `diproses` status automatically converted to `approved`

---

## Role-Based Actions

### RMW Dashboard (`app/controllers/rmw_dashboard.php`)

**For `pending` status:**
- **Button:** "Setujui" (Approve)
- **Action:** Changes status to `approved`
- **Updates:** `processed_date`, `approved_by`

**For `approved` status:**
- **Button:** "Sudah Siap" (Ready)
- **Action:** Changes status to `ready`
- **Updates:** `ready_date`, `ready_by`

### Scanner Page (`app/controllers/scanner.php`)

**For Production Users:**
- **Condition:** Request status must be `ready`
- **Action:** After scanning QR code and verifying materials
- **Button:** "Selesaikan Permintaan" (Complete Request)
- **Action:** Changes status to `completed`
- **Updates:** `completed_date`, `completed_by`
- **Validation:** 
  - Only production users can complete
  - Only `ready` status can be completed
  - Requires successful QR scan first

---

## Updated Files

### Controllers
1. **`app/controllers/rmw_dashboard.php`**
   - Updated status update logic
   - Added approve and ready actions
   - Updated status translations

2. **`app/controllers/scanner.php`**
   - Added completion handler for production users
   - Validates status is `ready` before allowing completion
   - Logs completion with scanned reference

3. **`app/controllers/production_dashboard.php`**
   - Updated status translations

### Views
1. **`app/rmw_dashboard.php`**
   - Updated status badges (added purple for `ready`)
   - Changed buttons: "Setujui" and "Sudah Siap"
   - Updated status display

2. **`app/production_dashboard.php`**
   - Updated status badges
   - Updated status translations

3. **`app/scan.php`**
   - Added "Selesaikan Permintaan" button
   - Only shows for production users when status is `ready`
   - Appears after successful QR scan

4. **`app/my_requests.php`**
   - Updated status filter dropdown
   - Updated status badges
   - Updated scan button (only for `ready` status)

---

## User Flow Examples

### Example 1: Successful Flow

1. **Production User (John)** creates request `REQ-20250115-1234`
   - Status: `pending`

2. **RMW User (Sarah)** views request in RMW dashboard
   - Clicks "Setujui" button
   - Status: `approved`
   - `approved_by` = "Sarah"
   - `processed_date` = current timestamp

3. **RMW User (Sarah)** prepares materials
   - Clicks "Sudah Siap" button
   - Status: `ready`
   - `ready_by` = "Sarah"
   - `ready_date` = current timestamp

4. **Production User (John)** receives notification
   - Opens scanner page: `scanner.php?request_number=REQ-20250115-1234`
   - Scans customer reference QR code
   - Reviews comparison results
   - Clicks "Selesaikan Permintaan" button
   - Status: `completed`
   - `completed_by` = "John"
   - `completed_date` = current timestamp

### Example 2: Production User Cannot Complete Early

- Production user tries to complete request with status `approved`
- System shows error: "Request must be in 'ready' status to complete"
- User must wait for RMW to mark as ready

---

## Security & Validation

1. **Role-Based Access:**
   - Only RMW users can approve/ready requests
   - Only Production users can complete requests
   - Checked via `$department` session variable

2. **Status Validation:**
   - Completion only allowed when status is `ready`
   - Prevents skipping workflow steps

3. **QR Scan Requirement:**
   - Completion button only appears after successful scan
   - Ensures materials are verified before completion

---

## Status Badge Colors

- **pending**: Yellow (`bg-yellow-100 text-yellow-800`)
- **approved**: Blue (`bg-blue-100 text-blue-800`)
- **ready**: Purple (`bg-purple-100 text-purple-800`)
- **completed**: Green (`bg-green-100 text-green-800`)
- **cancelled**: Red (`bg-red-100 text-red-800`)

---

## Status Translations (Indonesian)

- `pending` → "Menunggu"
- `approved` → "Disetujui"
- `ready` → "Sudah Siap"
- `completed` → "Selesai"
- `cancelled` → "Dibatalkan"

---

## Testing Checklist

- [x] Database migration completed
- [x] Status ENUM updated
- [x] New fields added
- [x] RMW dashboard shows approve button for pending
- [x] RMW dashboard shows "Sudah Siap" button for approved
- [x] Scanner page shows complete button for production users
- [x] Complete button only appears when status is ready
- [x] Status badges display correctly
- [x] Status translations updated
- [x] Status filters updated

---

## Notes

1. **Backward Compatibility:**
   - Existing `diproses` requests automatically converted to `approved`
   - No data loss during migration

2. **Activity Logging:**
   - All status changes are logged in `activity_log` table
   - Includes user, timestamp, and status change details

3. **Future Enhancements:**
   - Email notifications for status changes
   - Dashboard notifications
   - Status change history view

---

*Implementation Date: 2025-01-XX*
*Status: ✅ Complete*

