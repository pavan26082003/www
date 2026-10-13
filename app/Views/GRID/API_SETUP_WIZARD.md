# API Specification: Profile Setup Wizard (GRID Onboarding)

## 1. Overview
This endpoint saves the complete initial setup profile submitted by a user upon completing the GRID 4-Step Onboarding Wizard.

- **Route**: `POST /grid/api/profile/wizard-setup`
- **Controller**: `App\Controllers\GRID_Controllers\GridApiController::wizardSetup`
- **Models Involved**:
  - `App\Models\GRID\Grid_profile_model`
  - `App\Models\GRID\Grid_skills_model`
  - `App\Models\Users_model` (for updating `users.first_name`, `users.last_name`, `users.job_title`)

---

## 2. Request Contract

- **Method**: `POST`
- **Content-Type**: `application/json` or `multipart/form-data`
- **URL**: `http://localhost/index.php/grid/api/profile/wizard-setup`

### JSON Request Payload:
```json
{
  "full_name": "Rohan Verma",
  "headline": "Staff AI Engineer & Systems Architect",
  "role": "candidate", 
  "is_open_to_work": 1,
  "is_hiring": 0,
  "location": "Hyderabad, India",
  "bio": "Building scalable LLM alignment systems and distributed training infrastructure.",
  "skills": ["Python", "PyTorch", "React.js", "Docker", "System Architecture"],
  "website": "https://rohanverma.dev",
  "github_url": "https://github.com/rohanverma",
  "linkedin_url": "https://linkedin.com/in/rohanverma"
}
```

---

## 3. Database Operations to Perform (Atomic Transaction)

1. **Update `users` table** (via `$this->Users_model`):
   - Split `full_name` into `first_name` and `last_name`.
   - Update `users.job_title = headline`.
2. **Upsert `grid_profiles` table** (via `Grid_profile_model`):
   - `headline`, `bio`, `role`, `is_open_to_work`, `is_hiring`, `location`, `website`, `github_url`, `linkedin_url`.
3. **Associate Skills in `grid_user_skills` table** (via `Grid_skills_model`):
   - For each skill name in `skills`:
     - Lookup or insert into `grid_skills` to get `skill_id`.
     - Insert into `grid_user_skills (user_id, skill_id, endorsements_count)` with `endorsements_count = 1` on duplicate ignore.

---

## 4. Standard Response Contract

### Success Response (`200 OK`):
```json
{
  "success": true,
  "message": "Profile onboarding completed successfully! 🚀",
  "data": {
    "user_id": 1,
    "name": "Rohan Verma",
    "role": "candidate",
    "skills_count": 5
  }
}
```

### Error Response (`400 Bad Request`):
```json
{
  "success": false,
  "message": "Full name and headline are required."
}
```
