# Material Request Status Information

## Current Status Overview

**Database:** `rmw_system`  
**Total Requests:** 7

### Status Distribution:
- **pending**: 1 request (14.3%)
- **diproses**: 2 requests (28.6%)
- **completed**: 3 requests (42.9%)
- **cancelled**: 1 request (14.3%)

---

## Status Definitions

The system uses **4 status values** for material requests:

### 1. **pending** (Menunggu)
- **Meaning:** Request is waiting to be processed
- **Color:** Yellow badge (`bg-yellow-100 text-yellow-800`)
- **Description:** Newly created request, not yet started by RMW staff
- **Next Action:** Can be changed to `diproses` (processed)
- **Fields Updated:** None (default status)

### 2. **diproses** (Diproses)
- **Meaning:** Request is being processed/in progress
- **Color:** Blue badge (`bg-blue-100 text-blue-800`)
- **Description:** RMW staff has started working on the request
- **Next Action:** Can be changed to `completed` (finished)
- **Fields Updated:** 
  - `processed_date` = current timestamp
  - `processed_by` = user who started processing

### 3. **completed** (Selesai)
- **Meaning:** Request has been completed/fulfilled
- **Color:** Green badge (`bg-green-100 text-green-800`)
- **Description:** Materials have been prepared and shipped
- **Next Action:** Final status (no further changes)
- **Fields Updated:**
  - `completed_date` = current timestamp
  - `completed_by` = user who completed it

### 4. **cancelled** (Dibatalkan)
- **Meaning:** Request has been cancelled
- **Color:** Red badge (`bg-red-100 text-red-800`)
- **Description:** Request was cancelled and will not be fulfilled
- **Next Action:** Final status (no further changes)
- **Fields Updated:** Status changed to cancelled

---

## Status Workflow

```
┌─────────┐
│ pending │  ← New request created
└────┬────┘
     │
     │ [RMW starts processing]
     ▼
┌──────────┐
│ diproses │  ← Request being worked on
└────┬─────┘
     │
     │ [Materials prepared/shipped]
     ▼
┌───────────┐
│ completed │  ← Request fulfilled
└───────────┘

     OR

┌─────────┐
│ pending │
└────┬────┘
     │
     │ [Request cancelled]
     ▼
┌───────────┐
│ cancelled │  ← Request cancelled
└───────────┘
```

---

## Current Requests in Database

### 1. **REQ-20250101-0001**
- **Status:** `pending`
- **Priority:** medium
- **Request Date:** 2025-11-12 16:01:36
- **Processed Date:** Not processed
- **Completed Date:** Not completed
- **Created By:** N/A
- **Action:** Waiting for RMW to start processing

### 2. **REQ-20251112-0463**
- **Status:** `completed`
- **Priority:** medium
- **Request Date:** 2025-11-12 09:12:37
- **Processed Date:** Not processed
- **Completed Date:** Not completed
- **Created By:** Production User
- **Action:** Completed (final status)

### 3. **REQ-20251106-9298** ⭐ (The one from scanner page)
- **Status:** `diproses`
- **Priority:** medium
- **Request Date:** 2025-11-06 02:46:08
- **Processed Date:** 2025-11-06 02:46:28
- **Completed Date:** Not completed
- **Created By:** Production User
- **Action:** Currently being processed by RMW

### 4. **REQ-20251106-5235**
- **Status:** `completed`
- **Priority:** medium
- **Request Date:** 2025-11-06 02:43:23
- **Processed Date:** Not processed
- **Completed Date:** Not completed
- **Created By:** Production User
- **Action:** Completed (final status)

### 5. **REQ-20251028-6176**
- **Status:** `cancelled`
- **Priority:** medium
- **Request Date:** 2025-10-28 07:50:16
- **Processed Date:** Not processed
- **Completed Date:** Not completed
- **Created By:** Production User
- **Action:** Cancelled (final status)

### 6. **REQ-20251028-7095**
- **Status:** `completed`
- **Priority:** medium
- **Request Date:** 2025-10-28 07:50:08
- **Processed Date:** 2025-10-28 09:52:27
- **Completed Date:** Not completed
- **Created By:** Production User
- **Action:** Completed (final status)

### 7. **REQ-20251028-0735**
- **Status:** `diproses`
- **Priority:** medium
- **Request Date:** 2025-10-28 07:49:57
- **Processed Date:** 2025-11-05 07:20:46
- **Completed Date:** Not completed
- **Created By:** Production User
- **Action:** Currently being processed by RMW

---

## Database Schema

### Status Field Definition
```sql
status ENUM('pending', 'diproses', 'completed', 'cancelled') 
DEFAULT 'pending'
```

### Related Fields
- `processed_date` DATETIME - When status changed to 'diproses'
- `completed_date` DATETIME - When status changed to 'completed'
- `processed_by` VARCHAR(100) - User who started processing
- `completed_by` VARCHAR(100) - User who completed the request

---

## Status Update Logic

### When Status Changes to `diproses`:
```php
processed_date = CURRENT_TIMESTAMP
processed_by = current_user
```

### When Status Changes to `completed`:
```php
completed_date = CURRENT_TIMESTAMP
completed_by = current_user
```

### When Status Changes to `cancelled`:
```php
// Status only changed, no date fields updated
```

---

## UI Display

### Status Badges (Color Coding):
- **pending**: Yellow (`bg-yellow-100 text-yellow-800`)
- **diproses**: Blue (`bg-blue-100 text-blue-800`)
- **completed**: Green (`bg-green-100 text-green-800`)
- **cancelled**: Red (`bg-red-100 text-red-800`)

### Status Labels (Indonesian):
- **pending** → "Menunggu"
- **diproses** → "Diproses"
- **completed** → "Selesai"
- **cancelled** → "Dibatalkan"

---

## Status Update Actions

### From Production Dashboard:
- **pending** → Can change to `diproses` (Proses button)
- **diproses** → Can change to `completed` (Selesaikan button)
- **completed** → No actions (final status)
- **cancelled** → No actions (final status)

### From RMW Dashboard:
- **pending** → Can change to `diproses` (Proses button)
- **diproses** → Can change to `completed` (Selesaikan button)
- **completed** → No actions (final status)
- **cancelled** → No actions (final status)

---

## Notes

1. **Default Status:** All new requests start with `pending` status
2. **Status Progression:** Normal flow is `pending` → `diproses` → `completed`
3. **Cancellation:** Can happen from `pending` status only
4. **Final Statuses:** `completed` and `cancelled` are final (no further changes)
5. **Date Tracking:** `processed_date` and `completed_date` track workflow progress

---

## Current System State Summary

- **Active Requests:** 3 (1 pending + 2 diproses)
- **Completed Requests:** 3
- **Cancelled Requests:** 1
- **Most Recent:** REQ-20250101-0001 (pending, created Nov 12, 2025)
- **Oldest Active:** REQ-20251028-0735 (diproses, created Oct 28, 2025)

---

*Last Updated: Based on current database state*

