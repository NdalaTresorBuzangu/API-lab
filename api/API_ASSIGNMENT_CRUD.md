# Document Retrieval API – CRUD Assignment

**Project domain:** Document retrieval platform  

**Server (deployed):**
| Access | URL |
|--------|-----|
| Website | https://wastejustice.online |
| Website (www) | https://www.wastejustice.online |
| IP address | 82.25.96.126 |

**Base URL for API actions:** `https://wastejustice.online/actions`  
**All responses are JSON only.**

**Authentication:** This assignment API **does not require authentication**. Anyone with the endpoint URLs can send GET or POST requests (Read All, Read One, Create, Update, Delete) without logging in or sending a token. For a production or secured version you would add auth (e.g. session after login, or API key in headers).

**Assignment compliance:**  
- **CRUD:** Read All, Read One, Create, Update, Delete (all in `actions/`).  
- **Database:** MySQL only; no static data (see `db/document.sql`).  
- **Folder:** All endpoints in `actions/` (read_all.php, read_one.php, create.php, update.php, delete.php).  
- **JSON everywhere:** Success and error responses are JSON; no plain text, no raw booleans.

**IDs:** The **document id** is **auto-generated** on Create (you never send it). For Create, **userID**, **schoolID**, and **documentTypeID** are **references** to existing rows (who is requesting, which school, which document type)—only the document row is new; users and types already exist in the DB (see valid IDs in the Create section).

**Postman:** Use **Body** → **x-www-form-urlencoded** or **raw** → **JSON** for POST requests. All parameters below are the exact keys to use.

**If you get 403 Forbidden** when sending POST with Body → raw (JSON), the server (e.g. mod_security or firewall) may be blocking that request. Do this:
1. Use **Body → x-www-form-urlencoded** instead of raw, and add each key-value pair (same keys and values as in the JSON). The API accepts both; form-encoded is often allowed when raw is blocked.
2. If you must use raw JSON, set the header **Content-Type: application/json** in the **Headers** tab in Postman.
3. If 403 persists, contact your host (wastejustice.online) to allow POST to `actions/*.php` or to relax mod_security for your folder.

---

## Postman – Complete Setup (Satisfy Assignment)

**Import the collection:** In Postman → Import → upload `api/Document_CRUD_API.postman_collection.json`. All requests are pre-configured with URL and body. Set the collection variable `documentId` to an existing id (e.g. `document_001`) after running Read All, or use the id returned by Create.

**Base URL variable:** The collection uses `{{baseUrl}}` = `https://wastejustice.online/actions`. If your project is in a subfolder (e.g. `https://wastejustice.online/tshijukardp/actions`), edit the collection variable.

### Postman – Every request (assignment compliance)

| # | Assignment requirement | Method | URL | Postman: Params | Postman: Body (x-www-form-urlencoded) | Expected JSON response |
|---|------------------------|--------|-----|-----------------|--------------------------------------|-------------------------|
| 1 | **Read All** | GET | `https://wastejustice.online/actions/read_all.php` | None | None | `{"success": true, "data": [{"id": "...", ...}, ...]}` |
| 2 | **Read One** | GET | `https://wastejustice.online/actions/read_one.php` | **Params** tab: `id` = `document_001` (or any id from Read All) | None | `{"success": true, "data": {...}}` or `{"success": false, "error": "not found"}` |
| 3 | **Create** | POST | `https://wastejustice.online/actions/create.php` | None | **Body** tab: description, location, userID, schoolID, documentTypeID (all 5 required; see table below) | `{"success": true, "data": {"id": "doc_xxxxx"}}` |
| 4 | **Update** | POST | `https://wastejustice.online/actions/update.php` | None | **Body**: id (required), then optional: description, location, statusID | `{"success": true}` |
| 5 | **Delete** | POST | `https://wastejustice.online/actions/delete.php` | None | **Body**: id = document id to delete | `{"success": true}` |

**Create – Body key-value (all mandatory):**

| KEY | VALUE |
|-----|--------|
| description | Postman test document |
| location | Kinshasa |
| userID | 1 |
| schoolID | 2 |
| documentTypeID | 1 |

**Update – Body:** id = `document_001` (or id from Create), description = `Updated for PDF report`, location = `Updated City`, statusID = `2`.  
**Delete – Body:** id = `document_001` (or same id you updated).

---

## Request body – copy-paste for each POST

In Postman, choose **Body** → **x-www-form-urlencoded** and add the key-value pairs below, or use **raw** → **JSON** and paste the JSON.

### Create (`POST .../actions/create.php`)

**As form (x-www-form-urlencoded) — add each row in Postman:**

| KEY | VALUE |
|-----|--------|
| description | Need my transcript |
| location | Kinshasa |
| userID | 1 |
| schoolID | 2 |
| documentTypeID | 1 |

**As JSON (Body → raw → JSON):**
```json
{
  "description": "Need my transcript",
  "location": "Kinshasa",
  "userID": 1,
  "schoolID": 2,
  "documentTypeID": 1
}
```

---

### Update (`POST .../actions/update.php`)

Replace `document_001` with a real document id from Read All or from Create response.

**As form (x-www-form-urlencoded):**

| KEY | VALUE |
|-----|--------|
| id | document_001 |
| description | Updated for PDF report |
| location | Updated City |
| statusID | 2 |

**As JSON (Body → raw → JSON):**
```json
{
  "id": "document_001",
  "description": "Updated for PDF report",
  "location": "Updated City",
  "statusID": 2
}
```

*(Only `id` is required; description, location, statusID are optional.)*

---

### Delete (`POST .../actions/delete.php`)

Replace `document_001` with the document id you want to delete.

**As form (x-www-form-urlencoded):**

| KEY | VALUE |
|-----|--------|
| id | document_001 |

**As JSON (Body → raw → JSON):**
```json
{
  "id": "document_001"
}
```

---

## 1. Read All Records

**Endpoint:** `GET https://wastejustice.online/actions/read_all.php`  
**Request method:** GET  

**Parameters:** None. No query params or body.

**Postman:** Method GET, URL only. No Params, no Body.

**curl:**
```bash
curl "https://wastejustice.online/actions/read_all.php"
```

**Sample JSON response:**
```json
{
  "success": true,
  "data": [
    { "id": "document_001", "description": "...", "location": "...", "userID": 1, "schoolID": 2, "documentTypeID": 1, "statusID": 1, "seekerName": "...", "issuerName": "...", "documentType": "...", "statusName": "Pending", "submissionDate": "..." },
    { "id": "document_002", ... }
  ]
}
```

---

## 2. Read One Record

**Endpoint:** `GET https://wastejustice.online/actions/read_one.php`  
**Request method:** GET  

**Parameters (Query Params in Postman):**

| Key | Required | Type   | Example      | Description      |
|-----|----------|--------|--------------|------------------|
| id  | **Yes**  | string | document_001 | Document ID (PK) |

**Postman:** Params tab → add Query Param: **id** = `document_001` (or any existing document ID from Read All).

**curl:**
```bash
curl "https://wastejustice.online/actions/read_one.php?id=document_001"
```

**Sample JSON response (found):**
```json
{
  "success": true,
  "data": { "id": "document_001", "description": "...", "location": "...", "userID": 1, "schoolID": 2, "documentTypeID": 1, "statusID": 1, "seekerName": "...", "issuerName": "...", "documentType": "...", "statusName": "Pending", "submissionDate": "..." }
}
```

**If not found:**
```json
{ "success": false, "error": "not found" }
```

---

## 3. Create Record

**Endpoint:** `POST https://wastejustice.online/actions/create.php`  
**Request method:** POST  

**Send all parameters in one request** — include every key below in a single POST (Body → x-www-form-urlencoded or JSON). You cannot create with only one parameter; all five are required at once.

**Why existing IDs?** Creating a document here means creating a **new document request** (one new row in the `Document` table). That row must **point to** an existing seeker (`userID`), an existing issuer/school (`schoolID`), and an existing document type (`documentTypeID`). So we are not creating new users or new types—only the **document record** is new; the IDs say *which* user, *which* school, and *which* type. The **document id** (e.g. `doc_67890abcdef`) is **auto-generated** by the API and returned in the response; you do not send it.

**Valid IDs (from default seed data in `db/document.sql`) — use these when calling Create:**

| Table          | ID | Use as      | Description        |
|----------------|----|-------------|--------------------|
| User           | 1  | userID      | John Doe (Document Seeker) |
| User           | 2  | schoolID    | ABC Institute (Document Issuer) |
| User           | 3  | userID/schoolID | Admin (Tresor Ndala) |
| DocumentType   | 1  | documentTypeID | State exams   |
| DocumentType   | 2  | documentTypeID | P6           |
| DocumentType   | 3  | documentTypeID | P5           |
| DocumentType   | 4  | documentTypeID | P4           |
| DocumentType   | 5  | documentTypeID | P1 to P3     |

**Parameters (Body – all mandatory in Postman):**

| Key             | Required | Type   | Example              | Description                    |
|-----------------|----------|--------|----------------------|--------------------------------|
| description     | **Yes**  | string | Need my transcript   | What the document request is   |
| location        | **Yes**  | string | Kinshasa             | Location                       |
| userID          | **Yes**  | number | 1                    | **Existing** User ID (seeker)  |
| schoolID        | **Yes**  | number | 2                    | **Existing** User ID (issuer)   |
| documentTypeID  | **Yes**  | number | 1                    | **Existing** DocumentType ID (1–5) |

**Request body (documentation) — use in Postman Body (x-www-form-urlencoded or raw JSON):**

Form (key-value):

| KEY             | VALUE              |
|-----------------|--------------------|
| description     | Need my transcript |
| location        | Kinshasa           |
| userID          | 1                  |
| schoolID        | 2                  |
| documentTypeID  | 1                  |

JSON (Body → raw → JSON):
```json
{
  "description": "Need my transcript",
  "location": "Kinshasa",
  "userID": 1,
  "schoolID": 2,
  "documentTypeID": 1
}
```

**curl (form) — all five params in one request:**
```bash
curl -X POST "https://wastejustice.online/actions/create.php" \
  -d "description=Need my transcript" \
  -d "location=Kinshasa" \
  -d "userID=1" \
  -d "schoolID=2" \
  -d "documentTypeID=1"
```

**Sample JSON response:**
```json
{ "success": true, "data": { "id": "doc_67890abcdef" } }
```

**Verification (Read All after Create):**
```bash
curl "https://wastejustice.online/actions/read_all.php"
```

---

## 4. Update Record

**Endpoint:** `POST https://wastejustice.online/actions/update.php`  
**Request method:** POST  

**Parameters (Body in Postman):**

| Key         | Required | Type   | Example           | Description                          |
|-------------|----------|--------|-------------------|--------------------------------------|
| id          | **Yes**  | string | document_001      | Document ID to update                |
| description | No       | string | Updated text      | New description                      |
| location    | No       | string | New location      | New location                         |
| statusID    | No       | number | 1, 2, 3, or 4     | 1=Pending, 2=In Progress, 3=Completed, 4=Cancelled |

**Request body (documentation) — use in Postman Body (x-www-form-urlencoded or raw JSON):**

Form (key-value; replace `document_001` with a real document id):

| KEY         | VALUE (example)   | Required |
|-------------|-------------------|----------|
| id          | document_001      | **Yes**  |
| description | Updated description | No    |
| location    | New location      | No       |
| statusID    | 2                 | No       |

JSON (Body → raw → JSON):
```json
{
  "id": "document_001",
  "description": "Updated for PDF report",
  "location": "Updated City",
  "statusID": 2
}
```

**curl (form):**
```bash
curl -X POST "https://wastejustice.online/actions/update.php" \
  -d "id=document_001" \
  -d "description=Updated description" \
  -d "location=New location" \
  -d "statusID=2"
```

**Sample JSON response:**
```json
{ "success": true }
```

**Verification (Read All after Update):**
```bash
curl "https://wastejustice.online/actions/read_all.php"
```

---

## 5. Delete Record

**Endpoint:** `POST https://wastejustice.online/actions/delete.php`  
**Request method:** POST  

**Parameters (Body – mandatory in Postman):**

| Key | Required | Type   | Example      | Description       |
|-----|----------|--------|--------------|-------------------|
| id  | **Yes**  | string | document_001 | Document ID to delete |

**Request body (documentation) — use in Postman Body (x-www-form-urlencoded or raw JSON):**

Form (key-value; replace `document_001` with the document id to delete):

| KEY | VALUE       |
|-----|-------------|
| id  | document_001 |

JSON (Body → raw → JSON):
```json
{
  "id": "document_001"
}
```

**curl (form):**
```bash
curl -X POST "https://wastejustice.online/actions/delete.php" \
  -d "id=document_001"
```

**Sample JSON response:**
```json
{ "success": true }
```

**Verification (Read All after Delete):**
```bash
curl "https://wastejustice.online/actions/read_all.php"
```

---

## PDF Report – Testing Steps (Screenshots)

Use these steps with **curl** or **Postman** and capture screenshots for your PDF.

### 1. Read All (screenshot once)

- **curl:** `curl "https://wastejustice.online/actions/read_all.php"`
- **Postman:** GET `https://wastejustice.online/actions/read_all.php`
- **Screenshot:** Response showing `"success": true` and the `data` array (list of documents). Note one existing `id` (e.g. `document_001`) for Update/Delete.

---

### 2. Create + follow-up Read showing changes

**Step A – Create**
- **curl:**
  ```bash
  curl -X POST "https://wastejustice.online/actions/create.php" \
    -d "description=PDF test document" \
    -d "location=Test City" \
    -d "userID=1" \
    -d "schoolID=2" \
    -d "documentTypeID=1"
  ```
- **Postman:** POST `https://wastejustice.online/actions/create.php`, Body x-www-form-urlencoded: description, location, userID, schoolID, documentTypeID (values as above).
- **Screenshot:** Request (URL + body) and response, e.g. `{"success": true, "data": {"id": "doc_xxxxx"}}`. **Copy the returned `id`** (e.g. `doc_67890abcdef`).

**Step B – Read All (follow-up)**
- **curl:** `curl "https://wastejustice.online/actions/read_all.php"`
- **Postman:** GET `https://wastejustice.online/actions/read_all.php`
- **Screenshot:** Response showing the **new document** in the list (same `id` as in Step A), so the Create is visible in the data.

---

### 3. Update + follow-up Read showing changes

**Step A – Update**
- Use an existing document id (e.g. `document_001` or the `doc_xxxxx` from Create).
- **curl:**
  ```bash
  curl -X POST "https://wastejustice.online/actions/update.php" \
    -d "id=document_001" \
    -d "description=Updated for PDF report" \
    -d "location=Updated City" \
    -d "statusID=2"
  ```
- **Postman:** POST `https://wastejustice.online/actions/update.php`, Body: id, description, location, statusID (replace `document_001` with a real id).
- **Screenshot:** Request and response `{"success": true}`.

**Step B – Read All (follow-up)**
- **curl:** `curl "https://wastejustice.online/actions/read_all.php"`
- **Postman:** GET `https://wastejustice.online/actions/read_all.php`
- **Screenshot:** Response showing the **updated document** with the new description, location, or statusName (e.g. "In Progress" if statusID=2).

---

### 4. Delete + follow-up Read showing change

**Step A – Delete**
- Use the same document id you updated (or another existing id).
- **curl:**
  ```bash
  curl -X POST "https://wastejustice.online/actions/delete.php" -d "id=document_001"
  ```
- **Postman:** POST `https://wastejustice.online/actions/delete.php`, Body: id = document_001 (or the id you used in Update).
- **Screenshot:** Request and response `{"success": true}`.

**Step B – Read All (follow-up)**
- **curl:** `curl "https://wastejustice.online/actions/read_all.php"`
- **Postman:** GET `https://wastejustice.online/actions/read_all.php`
- **Screenshot:** Response showing the **list without that document** (one fewer record), so the Delete is visible.

---

### Checklist for PDF

| Requirement | What to screenshot |
|-------------|--------------------|
| Read All | One screenshot of GET read_all.php response |
| Read One | One screenshot of GET read_one.php?id=... response |
| Create + follow-up Read | Create request/response, then Read All response with the new document |
| Update + follow-up Read | Update request/response, then Read All response with updated document |
| Delete + follow-up Read | Delete request/response, then Read All response without the deleted document |

---

## Summary

| Operation | Method | Endpoint      | Mandatory params              | Optional params              | Response (success)                |
|-----------|--------|---------------|-------------------------------|------------------------------|----------------------------------|
| Read All  | GET    | read_all.php  | —                             | —                            | `{"success": true, "data": [...]}` |
| Read One  | GET    | read_one.php  | **id** (query)                | —                            | `{"success": true, "data": {...}}`  |
| Create    | POST   | create.php    | **description**, **location**, **userID**, **schoolID**, **documentTypeID** | — | `{"success": true, "data": {"id": "..."}}` |
| Update    | POST   | update.php    | **id**                        | description, location, statusID | `{"success": true}`           |
| Delete    | POST   | delete.php    | **id**                        | —                            | `{"success": true}`               |

All error responses are JSON, e.g. `{"success": false, "error": "not found"}` or `{"success": false, "error": "id is required"}`.

**Database:** MySQL. Tables: `Document`, `User`, `DocumentType`, `Status` (see `db/document.sql`).  
**Config:** Set `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` on the server if different from `config/config.php` defaults.

---

## Submission Package (3 Items Required)

### 1) GitHub Link
Submit the link to your code repository (e.g. `https://github.com/your_username/your_repo`).

### 2) Deployed API Link
Submit the link to your **actions** folder. Must be reachable and show that the `actions/` subfolder exists.

- **Your server:** `https://wastejustice.online/actions`  
  (If the project is in a subfolder: `https://wastejustice.online/<project_folder>/actions`)

- **If tested on assignment server:** `http://169.239.251.102:280/~your_username/<project_folder>/actions`

### 3) PDF Report (Postman Screenshots)

Use **Postman** for all tests. The PDF must include screenshots for:

| PDF requirement | What to do in Postman | What to screenshot |
|----------------|------------------------|--------------------|
| **Read All** | Send **1. Read All** (GET). | Response body showing `"success": true` and `"data"` array with at least one object that has `"id"`. |
| **Read One** | Send **2. Read One** with Params `id` = an existing id (e.g. `document_001`). | Response body: `"success": true`, `"data"` with one document, or `"success": false`, `"error": "not found"` for invalid id. |
| **Create + follow-up Read showing changes** | 1) Send **3. Create** (POST with all 5 body params). 2) Copy the returned `data.id` (e.g. `doc_xxxxx`). 3) Send **Read All (verification)**. | Screenshot 1: Create request (URL + Body) and response `{"success": true, "data": {"id": "doc_..."}}`. Screenshot 2: Read All response showing the **new** document in the list (same id). |
| **Update + follow-up Read showing changes** | 1) Set Body **id** to an existing id (e.g. `document_001` or the one from Create). 2) Send **4. Update** (description/location/statusID as desired). 3) Send **Read All (verification)**. | Screenshot 1: Update request and response `{"success": true}`. Screenshot 2: Read All response showing that document with **updated** description/location/status. |
| **Delete + follow-up Read showing change** | 1) Send **5. Delete** (Body: id = same document you updated). 2) Send **Read All (verification)**. | Screenshot 1: Delete request and response `{"success": true}`. Screenshot 2: Read All response with **one fewer** document (deleted one no longer in list). |

**Order to run in Postman (for PDF):**  
1. Read All → screenshot.  
2. Read One → screenshot.  
3. Create → screenshot request + response; then Read All → screenshot (new record visible).  
4. Update → screenshot request + response; then Read All → screenshot (updated record visible).  
5. Delete → screenshot request + response; then Read All → screenshot (record gone).
