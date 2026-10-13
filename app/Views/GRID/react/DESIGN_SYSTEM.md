# GRID Web Application - Design System & UI Architecture Guidelines

> **Purpose**: This document defines the core design system, architectural patterns, component anatomy, and aesthetic standards for the GRID application. All new pages, components, and refactors MUST follow these specifications to maintain visual harmony and consistency across Home, Network, Profile, Jobs, and Messaging.

---

## 1. Core Aesthetic Philosophy & Ideology

1. **Executive Ambient Minimalism**:
   - Inspired by **Linear.app**, **Vercel**, and **Stripe**.
   - Clean typography, high-contrast text hierarchy, precise spacing, and refined micro-interactions.
2. **Atmospheric Glassmorphism (`g-glass-card`)**:
   - NEVER use opaque solid background colors (e.g., avoid flat `#f3f2ef` or harsh solid grey blocks).
   - All major cards and panels must use **`g-glass-card`** with frosted glass blur (`backdrop-filter: blur(20px)`), allowing the subtle ambient canvas and precision micro-grid to glow through.
3. **Dual Theme Harmony**:
   - **Executive Light**: Crisp titanium slate canvas + soft ambient glow + subtle white borders (`border-white/70`).
   - **Obsidian Dark**: Deep void canvas (`#0d121e`) + indigo ambient flare + translucent dark borders (`dark:border-slate-800/80`).

---

## 2. Canvas & Page Layout Standard

### Master Container Anatomy
All page apps rendered inside `AppShell` must inherit the ambient canvas and avoid injecting full-page solid wrappers.

```jsx
export default function MyPageApp({ onNavigate, onToast, currentUser }) {
    return (
        <div className="w-full font-sans">
            {/* Standard Responsive Container */}
            <div className="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8 py-4 sm:py-6 w-full">
                {/* 2-Column Grid: 8 Cols Main Feed + 4 Cols Sidebar */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-5 w-full items-start">
                    <main className="space-y-4 lg:col-span-8 min-w-0 w-full overflow-hidden">
                        {/* Feed Content */}
                    </main>
                    <aside className="space-y-4 lg:col-span-4 min-w-0">
                        {/* Sidebar Content */}
                    </aside>
                </div>
            </div>
        </div>
    );
}
```

---

## 3. Card & Container Standards (`g-glass-card`)

| Element | Class Definition | Description |
| :--- | :--- | :--- |
| **Primary Card** | `g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm p-4 sm:p-6 overflow-hidden` | Standard container for all feed and detail cards |
| **Compact Card** | `g-glass-card rounded-2xl border border-white/70 dark:border-slate-800/80 shadow-sm p-3.5 sm:p-4` | Navigation bars, filter bars, breadcrumbs |
| **Sub-Panel / Box** | `bg-slate-50/60 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/50 rounded-xl p-3 sm:p-4` | Inner callout boxes, reviews, telemetry |
| **Divider Line** | `border-slate-200/60 dark:border-slate-800/80` | Soft translucent border for lists and sections |
| **Notice / Alert** | `bg-amber-500/10 dark:bg-amber-500/10 border border-amber-500/20 rounded-2xl p-4 text-amber-900 dark:text-amber-200` | Imposter warnings, disclaimer notices |

---

## 4. Typography, Header & Subheading Conventions

### Hierarchy
1. **Section / Page Heading**:
   ```jsx
   <h1 className="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white leading-tight">
       Senior Python Full Stack Developer
   </h1>
   ```
2. **Card Primary Header**:
   ```jsx
   <h2 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
       About the job
   </h2>
   ```
3. **Card Subtitle / Context**:
   ```jsx
   <h3 className="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 mt-1 mb-2">
       About Novatech Global Solutions
   </h3>
   ```
4. **Body & Descriptive Text**:
   ```jsx
   <p className="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
       Novatech is building the next generation of cloud automation software...
   </p>
   ```

---

## 5. Progressive Disclosure Pattern (3-Line Truncation & `... more`)

Whenever long descriptions or rich job details are presented, clamp content to **3 lines** by default to maintain page rhythm and scannability.

### Standard Implementation
```jsx
const [isDescriptionExpanded, setIsDescriptionExpanded] = useState(false);

{!isDescriptionExpanded ? (
    <div>
        <p className="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed line-clamp-3">
            {description.intro}
        </p>
        <button
            type="button"
            onClick={() => setIsDescriptionExpanded(true)}
            className="text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 hover:underline cursor-pointer inline-flex items-center gap-1 mt-1.5"
        >
            ... more
        </button>
    </div>
) : (
    <div className="space-y-4 animate-in fade-in duration-200">
        <div>
            <p className="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                {description.intro}
            </p>
            <button
                type="button"
                onClick={() => setIsDescriptionExpanded(false)}
                className="text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 hover:underline cursor-pointer inline-flex items-center gap-1 mt-1.5"
            >
                ... less
            </button>
        </div>
        {/* Expanded full specifications, responsibilities, tables, skills, etc. */}
    </div>
)}
```

---

## 6. Interactive Action Elements & Badges

### Buttons
- **Primary CTA (Apply / Submit)**:
  `px-6 py-2 text-xs sm:text-sm font-bold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition-all cursor-pointer`
- **Success / Applied State**:
  `px-6 py-2 text-xs sm:text-sm font-bold rounded-xl bg-emerald-600 text-white opacity-95 cursor-not-allowed inline-flex items-center gap-1.5`
- **Secondary / Save Bookmark**:
  `px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl border border-slate-300/80 dark:border-slate-700/80 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 cursor-pointer`
- **Active Bookmark**:
  `bg-blue-50/80 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800`

### Badges & Chips
- **Match Score Badge**:
  `inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-emerald-500/10 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/25`
- **Preferred Key Skill Chip (`★`)**:
  `bg-blue-500/10 dark:bg-blue-500/15 text-blue-700 dark:text-blue-300 border border-blue-500/25`
- **Standard Skill Chip**:
  `bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60`

---

## 7. Build, Validation & Test Workflow

When working on the React client bundle:
1. **Working Directory**: `c:\Program Files\Ampps\www\app\Views\GRID\react`
2. **Build Command**:
   ```bash
   cmd /c "npm run build"
   ```
   *(Note: Use `cmd /c` to avoid Windows PowerShell script execution policy errors).*
3. **Bundle Output**: `dist/bundle.js` (bundled via `esbuild`).
4. **Verification**: Always ensure the build completes with 0 errors and output size is logged.

---

## 8. Application Status & Job Tracker Pattern

### Right Rail Sidebar Card ("Job Tracker")
- Replaces generic connection/follower statistics with a focused tracking utility.
- Displays:
  - **Applied jobs count**: Primary entry point with "Track →" button and Quick Apply status.
  - **Saved jobs count**: Secondary bookmark count.
  - **Direct CTA button**: "Open Application Status (Count)" to switch to the full tracker view.

### 2-Column Job Application Status Architecture
- **Header**: Back to all jobs button, total applies counter (`62 Total applies`), and recruiter updates (`13 Application updates`).
- **Left Column (5 cols)**:
  - Filter chips: `Recruiter Actions (13)`, `Applies on GRID (57)`, `Applies on External Site (5)`.
  - Applied Job Cards: Company rating, logo badge, status badge (`Resume viewed 4 weeks ago`), recruiter last active timestamp, and AI interview prep links (`🎙️ Mock`, `📖 Q&A`).
- **Right Column (7 cols)**:
  - Selected Job Header with direct link to details.
  - **Quick Apply Milestone Stepper**:
    - Connected horizontal timeline across 4 stages: `Contacted By Email` → `Application Viewed` → `Resume Viewed` → `Awaiting Recruiter Action`.
    - Completed nodes render green checkmarks with emerald connecting lines.
    - Active stage renders an emerald ring with center dot.
    - External applications show clear attribution notice indicating external ATS submission.
  - **Activity on this job**: Dual-metric card for `Total applications` and `Applications viewed by recruiter`.
  - **Match criteria checklist**: `✓` / `✕` breakdown across early applicant, keyskills, location, experience, industry, department.
  - **Recruiter Profile Card**: Hiring lead contact and activity telemetry.

---

## 9. Mobile Navigation Drawer & Sub-Menu Standards

### Clean Option Hierarchy (Zero Clutter)
- Avoid redundant status pills or badges (`Pass`, `Scanner`, `Pro`, `Hiring`, `New`, etc.) on top-level drawer items to maintain an executive, scannable aesthetic matching native mobile OS menus.

### Nested Accordion Navigation Pattern
- Complex or multi-faceted hubs (e.g. **Jobs Hub**) use an inline expand/collapse chevron (`rotate-180` animated arrow).
- When expanded, reveals clean, left-bordered sub-items with dedicated iconography:
  1. **Job Search**: Direct launcher for recommendations and search feed.
  2. **Job Tracking**: Direct launcher for the Quick Apply application progress dashboard.
- Utilizes cross-component custom events (`grid:open-jobs-tracker`, `grid:open-jobs-search`) and session flags to ensure instant view synchronization regardless of initial route state.

### Mobile Master-Detail Drill-Down Pattern
- **First Screen on Mobile**: Opening the Job Tracker on mobile displays **ONLY the list of applied jobs** (with the top summary card showing 62 applies / 13 updates, filter chips, and cards with `Track progress →`). The progress stepper column is hidden (`hidden lg:block`).
- **Second Screen on Mobile (Detail View)**: Tapping `Track progress` on any applied job hides the top 62 applies summary card (`hidden lg:flex`) on mobile. ONLY the lower action bar is visible:
  - Header back button: `← Back to applied jobs`
  - Quick action: `Similar jobs ↗`
  - 4-Stage Progress Stepper, Telemetry Metrics, Match Breakdown, and Recruiter Profile.
- **Desktop**: Automatically maintains the side-by-side 2-column view (`5 cols` list + `7 cols` detail) with the top summary card always displayed.


