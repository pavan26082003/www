# GRID - Profiles, Roles & Company Pages Architecture

> **Document Status**: Architectural Specification & Implementation Roadmap  
> **Created**: September 15, 2026  
> **Target System**: GRID React Web Application (`Views/GRID/react`)  
> **Related Reference**: [`DESIGN_SYSTEM.md`](./DESIGN_SYSTEM.md)

---

## 1. Executive Overview

This document outlines the product architecture, user roles, profile archetypes, recruiter experience, and company page workflows for the GRID platform. It serves as the blueprint for extending the current single-user editable profile into a multi-role, interconnected professional and recruitment network.

```
                               ┌────────────────────────┐
                               │       GRID USERS       │
                               └───────────┬────────────┘
                                           │
             ┌─────────────────────────────┴─────────────────────────────┐
             ▼                                                           ▼
┌─────────────────────────┐                                 ┌─────────────────────────┐
│     CANDIDATE / PEER    │                                 │   RECRUITER / EMPLOYER  │
├─────────────────────────┤                                 ├─────────────────────────┤
│ • "Open to Work" Badge  │                                 │ • "Hiring" Ring Badge   │
│ • Portfolio & Artifacts │                                 │ • "Open Roles" Pinned   │
│ • Applied Job Tracker   │                                 │ • Applicant Pipeline    │
│ • Skill Endorsements    │                                 │ • Post & Manage Jobs    │
└─────────────────────────┘                                 └────────────┬────────────┘
                                                                         │ Manages
                                                            ┌────────────▼────────────┐
                                                            │      COMPANY PAGES      │
                                                            ├─────────────────────────┤
                                                            │ • Overview / About      │
                                                            │ • Active Job Openings   │
                                                            │ • Team / People on GRID │
                                                            │ • Culture & Life Posts  │
                                                            └─────────────────────────┘
```

---

## 2. Self Profile vs. Other User's Profile (Owner vs. Viewer Mode)

Currently, the Profile page (`/grid/profile`) defaults to **Owner / Self-Edit Mode**. When clicking any peer from the Network, Feed, or Messages, the app must render **Viewer / Public Mode**.

### Feature Comparison Matrix

| Component / Section | Owner View (`currentUser`) | Public / Viewer View (Other User) |
| :--- | :--- | :--- |
| **Edit Buttons** | Pencil icons visible on header, about, experience, education, skills | **Completely hidden** |
| **Primary Action Buttons** | *Edit Profile*, *Add Section*, *Share Visiting Card*, *Account Settings* | **Relationship CTAs**: **`Connect`** (or `Pending` / `Connected`), **`Message`**, **`Follow`**, **`••• More`** |
| **Contact Info Modal** | Edit email, phone, personal links | View public verified links, GitHub, LinkedIn, portfolio |
| **Analytics & Telemetry** | Private stats (profile views, search appearances, post analytics) | **Hidden** (replaced with mutual connections & shared spaces) |
| **Activity Stream** | All posts, drafts, saved items | **Public posts, published artifacts, and mutual endorsements only** |
| **Visiting Card** | Interactive edit & PNG/SVG export | View public visiting card, QR code scan, or save contact |

### Public Profile Action Hierarchy
1. **Connect / Message**:
   - If not connected: Blue **`Connect`** button + **`Follow`** button.
   - If pending: Neutral disabled **`Pending ⏳`** button with cancel option.
   - If connected: Subtle grey **`Connected ✓`** + Blue primary **`Message`** button.
2. **Mutual Insights Box**:
   - Displays *"12 mutual connections including Praful Shah and Sarah Chen"*.
   - Displays common skills and shared Subscribed Spaces.

---

## 3. Recruiter Profile Archetype

Recruiters are not on GRID to search for jobs; they are talent scouts representing their organizations. Their profiles must reflect this distinction.

### Key Recruiter Profile Elements
1. **"HIRING" Avatar Ring & Identity Badge**:
   - Instead of *"Open to Work"*, a distinct **`HIRING`** badge (obsidian/purple or emerald) borders their avatar.
   - Headline clearly states: *Talent Lead @ Novatech Global Solutions • We are hiring Python, AI & Cloud Engineers*.
2. **Pinned Showcase: "Open Roles I'm Hiring For"**:
   - Prominently positioned right beneath their bio/header.
   - Displays interactive mini job cards for active positions they manage.
   - Candidates visiting their profile can click **"Quick Apply"** or **"View Details"** directly.
3. **Recruiter Direct Contact**:
   - **`Inquire About Roles`** / **`Send InMail`** action button.
   - Response time indicator: *"Typically responds within 24 hours"*.
4. **Omission of Candidate Clutter**:
   - Job tracker for applied positions, job alerts, and candidate-seeking preferences are hidden.

---

## 4. Recruiter / Employer Dashboard in the Jobs Hub

The Jobs tab adapts based on the active role or a clean **Role Switcher** (*Candidate Mode ↔ Recruiter Mode*).

### A. Candidate Mode (Active Today)
- **Job Search & Recommendations**: Filter by role, location, remote, match score.
- **Job Tracker**:
  - Drill-down master-detail view of applications.
  - Real-time milestone tracker (*Application sent → Reviewing → Screened → Interviewing → Offer*).
  - Quick apply history and recruiter response badges.

### B. Recruiter / Employer Mode (New)
1. **"Post a Job" Workflow**:
   - Modal or page form: Job title, discipline, experience level, salary range, workplace type (Remote/Hybrid/Onsite), job description (Markdown), and required skills.
   - Custom screening questions (e.g., *"Years of experience with LangChain?"*).
2. **Manage Active Listings**:
   - List of all active jobs posted by the recruiter or their company.
   - Telemetry per job: Views, Clicks, Total Applicants, Quick Apply conversions.
   - Toggle to Pause, Edit, or Close a job listing.
3. **Candidate Application Pipeline**:
   - Kanban / Pipeline view for each job:
     `[New Applications] ➔ [Screened / Reviewed] ➔ [Interview Scheduled] ➔ [Offered / Hired]`.
   - Ability to view candidate's GRID profile, verified skills, and message them with one click.
4. **Talent Search / Sourcing**:
   - Search the entire GRID network by skills (e.g., Python + PyTorch), location, or discipline.
   - Reach out to candidates with *"Open to Work"* status.

---

## 5. Company Pages Architecture

Company Pages represent organizations rather than individual people.

### Creation Workflow
1. **Entry Point**: Available from the top navigation dropdown or Jobs hub via **`+ Create Company Page`**.
2. **Setup Wizard (3 Steps)**:
   - **Step 1 - Organization Profile**: Company Name, Custom URL handle (`/grid/company/novatech`), Website, Industry, Organization size.
   - **Step 2 - Visual Branding**: Company Logo (square), Cover Banner (wide landscape), Tagline.
   - **Step 3 - Verification & Administration**: Verification agreement, assigns the creator as the initial **Super Admin**.

### Anatomy of a Company Page
```
┌────────────────────────────────────────────────────────────────────────┐
│ [Banner Image]                                                         │
│ ┌──────┐                                                               │
│ │ LOGO │ Novatech Global Solutions               [+ Follow] [Visit Site]│
│ └──────┘ Cloud Automation & Enterprise AI • 1,200 Employees • Hyderabad│
├────────────────────────────────────────────────────────────────────────┤
│  [Overview]      [Open Jobs (18)]      [People / Team]      [Culture]  │
└────────────────────────────────────────────────────────────────────────┘
```

1. **Overview Tab**:
   - About Novatech, mission, tech stack, locations, recent company news/posts.
2. **Open Jobs Tab**:
   - Curated list of all open positions at the company with direct Quick Apply.
3. **People / Team Tab**:
   - Automatically aggregates all GRID members who currently list this company in their Experience section.
   - Filter by discipline: Engineering, Design, Product, Leadership.
4. **Culture / Life Tab**:
   - Photo gallery, team benefits, engineering blog posts, open source contributions.

---

## 6. Phased Implementation Roadmap

When resuming this task, execute the implementation in the following structured phases:

### Phase 1: Public Profile View (Viewer Mode for Other Users)
- [ ] Add state / route handling for viewing someone else's profile (`/grid/profile?userId=...` or `/grid/profile/:id`).
- [ ] Create `PublicProfileView.jsx` (or add an `isOwner={false}` toggle to `GridProfileApp.jsx`).
- [ ] Hide all edit buttons, pencil icons, and private telemetry when `isOwner === false`.
- [ ] Implement relationship action buttons: **`Connect`**, **`Message`**, **`Follow`**, and mutual connection insights.
- [ ] Connect peer card clicks in `GridNetworkApp.jsx` and feed author clicks in `GridHomeApp.jsx` to open the public profile.

### Phase 2: Recruiter Profile Archetype
- [ ] Add `role: 'recruiter'` property to user data models.
- [ ] Render the **`HIRING`** badge around recruiter avatars.
- [ ] Add the **"Open Roles I'm Hiring For"** pinned carousel to recruiter profiles.
- [ ] Implement recruiter messaging CTA (*"Inquire About Roles"*).

### Phase 3: Recruiter Mode in Jobs Hub
- [ ] Add a clean **Mode Switcher** in `GridJobsApp.jsx` header: `[Candidate View] ⟷ [Recruiter Dashboard]`.
- [ ] Build the **Recruiter Dashboard**:
  - Active Job Postings table with applicant counts.
  - **"Post a Job"** modal with role details and requirements.
  - Applicant pipeline drawer (review candidates who clicked Quick Apply).

### Phase 4: Company Pages
- [ ] Create `CompanyPageApp.jsx` with standard tabs: Overview, Jobs, People, Culture.
- [ ] Implement **`+ Create Company Page`** modal.
- [ ] Link company logos in job cards and experience items to navigate to their Company Page.

---

*This document is ready to be referenced whenever we resume building the public profiles, recruiter hub, and company pages.*
