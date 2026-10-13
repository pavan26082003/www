# GRID Platform - Database & Media Architecture Reference

This document serves as the permanent technical specification for the GRID database structure, covering the separation between professional data, multi-media posts, and real-time chat.

---

## 1. Core Concept: The Golden Key (`user_id`)

All tables share a single relational anchor: **`user_id`** (from the central `users` table). No user ever gets a personal table. Every table is multi-tenant, partitioned logically by `user_id`.

```text
                                users (id)
                                    │
    ┌────────────────┬──────────────┼──────────────┬────────────────┐
    ▼                ▼              ▼              ▼                ▼
grid_profiles      posts      experiences     education         chat
```

---

## 2. Professional & Profile Data (Separated Tables)

### A. Core Profile (1-to-1)
- **Table**: `grid_profiles`
- **Primary Key**: `user_id` (Points directly to `users.id`)
- **Fields**: `headline`, `about`, `location`, `website`, `github_url`, `linkedin_url`, `banner_url`, `created_at`, `updated_at`

### B. Work Experience (1-to-Many)
- **Table**: `grid_experiences`
- **Primary Key**: `id` (Auto-increment)
- **Foreign Key**: `user_id` -> `users.id` (ON DELETE CASCADE)
- **Fields**: `company_name`, `role_title`, `employment_type`, `location`, `start_date`, `end_date`, `is_current`, `description`

### C. Education History (1-to-Many)
- **Table**: `grid_education`
- **Primary Key**: `id` (Auto-increment)
- **Foreign Key**: `user_id` -> `users.id` (ON DELETE CASCADE)
- **Fields**: `school_name`, `degree`, `field_of_study`, `start_year`, `end_year`, `grade`, `activities`

### D. Projects Portfolio (1-to-Many)
- **Table**: `grid_projects`
- **Primary Key**: `id` (Auto-increment)
- **Foreign Key**: `user_id` -> `users.id` (ON DELETE CASCADE)
- **Fields**: `title`, `description`, `project_url`, `repo_url`, `technologies`, `start_date`, `end_date`

### E. Skills Junction (Many-to-Many)
- **Catalogue**: `grid_skills` (`id`, `name`, `category`)
- **Junction**: `grid_user_skills` (`user_id`, `skill_id`, `proficiency`)
  - **Composite Primary Key**: `(user_id, skill_id)`

---

## 3. Posts & Media Architecture (LinkedIn 1:N Model)

### A. Posts Table (Parent)
- **Table**: `grid_posts`
- **Primary Key**: `id` (Auto-increment)
- **Foreign Key**: `user_id` -> `users.id`
- **Fields**: `content`, `post_type` ('standard', 'media', 'short', 'job'), `visibility` ('public', 'connections'), `likes_count`, `comments_count`, `shares_count`, `created_at`

### B. Post Media Table (Child / Carousel / Multi-Image)
- **Table**: `grid_post_media`
- **Primary Key**: `id` (Auto-increment)
- **Foreign Key**: `post_id` -> `grid_posts.id` (ON DELETE CASCADE)
- **Fields**: `file_url`, `media_type` ('image', 'video'), `sort_order`, `width`, `height`

```text
POST (id: 101)
 ├── Author: User 50 (Pavan)
 ├── Text: "RAG Pipeline Launched"
 └── Media Attachments:
      ├── post_media row 1: file_url = 'files/timeline_files/rag_1.jpg' (order: 0)
      ├── post_media row 2: file_url = 'files/timeline_files/rag_2.jpg' (order: 1)
      └── post_media row 3: file_url = 'files/timeline_files/rag_3.jpg' (order: 2)
```

---

## 4. Chat & Messaging Architecture (Separated Subsystem)

To ensure high concurrency, messaging is separated into conversations and messages:

### A. Conversations Table
- **Table**: `grid_conversations`
- **Primary Key**: `id` (Auto-increment)
- **Fields**: `type` ('direct', 'group'), `title`, `created_at`, `updated_at`

### B. Conversation Participants
- **Table**: `grid_conversation_participants`
- **Composite Primary Key**: `(conversation_id, user_id)`
- **Foreign Keys**: `conversation_id`, `user_id`
- **Fields**: `last_read_at`, `role`

### C. Messages Table
- **Table**: `grid_messages`
- **Primary Key**: `id` (Auto-increment)
- **Foreign Keys**: 
  - `conversation_id` -> `grid_conversations.id`
  - `sender_id` -> `users.id`
- **Fields**: `message_text`, `attachment_url`, `attachment_type`, `created_at`

---

## 5. Storage Summary

| Asset / Record | Physical Storage Location | Database Record |
| :--- | :--- | :--- |
| **Image / Video files** | `C:\Program Files\Ampps\www\files\timeline_files\` | Saved as relative URL string (e.g. `/files/timeline_files/xyz.jpg`) |
| **Database Data** | `C:\Program Files\Ampps\mysql\data\factor_db2\` | Stored in MySQL `.ibd` table space files |
| **Deletion Rule** | Backend unlinks physical file via `unlink($filepath)` | Row deleted in database (foreign keys cascade) |
