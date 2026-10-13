<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.4/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">


<style>

:root {
    --iot-bg: #F8F5F2;
    --iot-card: #FFFFFF;
    --iot-text: #1B1E2B;
    --iot-muted: #6B7280;
    --iot-border: #E5DDD5;
    --iot-graphite: #2B2D42;
    --iot-graphite-2: #3A3F58;
    --iot-copper: #C08457;
    --iot-soft-copper: #D4A373;
    --iot-metallic: #E6B980;
    --iot-sage: #6B8F71;
    --iot-hover-glow: rgba(192, 132, 87, 0.18);
    --iot-shadow-soft: 0 10px 30px rgba(43, 45, 66, 0.07);
    --iot-shadow-card: 0 14px 40px rgba(43, 45, 66, 0.08);
    --iot-shadow-hover: 0 22px 54px rgba(192, 132, 87, 0.18);
    --iot-focus-ring: 0 0 0 4px rgba(192, 132, 87, 0.14);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    background: var(--iot-bg);
    color: var(--iot-text);
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
}

html, body {
    scroll-behavior: smooth !important;
}

/* Main Container */
#page-content {
    background: var(--iot-bg);
    border-radius: 24px;
    backdrop-filter: none;
    box-shadow: none;
    margin: 0;
    padding: 0;
}

/* Card */
.card {
    border-radius: 24px;
    box-shadow: var(--iot-shadow-card);
    border: 1px solid var(--iot-border);
    background: var(--iot-card);
    padding: 20px;
    margin: 0;
}

/* Page Title */
.page-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 20px;
    margin-top: 0;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--iot-border);
}

.page-title h2 {
    font-weight: 700;
    color: var(--iot-graphite);
    font-size: 2rem;
    letter-spacing: -0.03em;
    background: none;
    -webkit-background-clip: unset;
    -webkit-text-fill-color: unset;
    background-clip: unset;
}

.page-title h2 i,
.page-title .title-icon,
.section-title i {
    color: var(--iot-copper);
}

.page-title small {
    display: block;
    margin-top: 6px;
    color: var(--iot-muted);
    font-weight: 500;
    font-size: 0.95rem;
}

/* Buttons */
.btn-gradient,
.btn-success-modern,
.btn-dark-modern {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 0.95rem;
    font-weight: 600;
    padding: 12px 24px;
    border-radius: 14px;
    transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease, border-color 0.25s ease;
    text-decoration: none;
    border: 1px solid rgba(229, 221, 213, 0.55);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.btn-gradient::before,
.btn-success-modern::before,
.btn-dark-modern::before,
.btn-action::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(230, 185, 128, 0.22), transparent 42%, rgba(192, 132, 87, 0.12));
    opacity: 0;
    transition: opacity 0.25s ease;
    pointer-events: none;
}

.btn-gradient:hover::before,
.btn-success-modern:hover::before,
.btn-dark-modern:hover::before,
.btn-action:hover::before {
    opacity: 1;
}

.btn-gradient {
    background: linear-gradient(135deg, var(--iot-graphite) 0%, var(--iot-graphite-2) 100%);
    color: #FFFFFF;
    box-shadow: 0 10px 26px rgba(43, 45, 66, 0.16);
}

.btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 36px var(--iot-hover-glow);
    border-color: rgba(192, 132, 87, 0.35);
    background: linear-gradient(135deg, var(--iot-graphite-2) 0%, var(--iot-graphite) 100%);
    color: #FFFFFF;
}

.btn-success-modern {
    background: linear-gradient(135deg, var(--iot-sage) 0%, var(--iot-graphite-2) 100%);
    color: #FFFFFF;
    box-shadow: 0 10px 26px rgba(107, 143, 113, 0.18);
}

.btn-success-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 36px rgba(107, 143, 113, 0.22), 0 0 0 1px rgba(192, 132, 87, 0.14);
    border-color: rgba(107, 143, 113, 0.35);
    background: linear-gradient(135deg, var(--iot-sage) 0%, var(--iot-graphite) 100%);
    color: #FFFFFF;
}

.btn-dark-modern {
    background: linear-gradient(135deg, var(--iot-graphite) 0%, #242638 100%);
    color: #FFFFFF;
    box-shadow: 0 10px 26px rgba(43, 45, 66, 0.18);
}

.btn-dark-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 36px var(--iot-hover-glow);
    border-color: rgba(230, 185, 128, 0.32);
    background: linear-gradient(135deg, #242638 0%, var(--iot-graphite-2) 100%);
    color: #FFFFFF;
}

.btn-gradient:focus,
.btn-success-modern:focus,
.btn-dark-modern:focus,
.btn-action:focus,
.search-input:focus {
    outline: none !important;
    box-shadow: var(--iot-focus-ring), 0 12px 28px rgba(43, 45, 66, 0.12) !important;
}

/* Cards Grid Layout */
.cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    margin-top: 20px;
    overflow: visible;
    position: relative;
}

/* Individual Card */
.model-card {
    background: var(--iot-card);
    border-radius: 20px;
    padding: 18px;
    box-shadow: 0 8px 24px rgba(43, 45, 66, 0.055);
    border: 1px solid var(--iot-border);
    transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease, background 0.28s ease;
    position: relative;
    overflow: visible; /* IMPORTANT: allows action dropdown to open outside card */
    z-index: 1;
}

.model-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 18px;
    right: 18px;
    height: 3px;
    border-radius: 0 0 999px 999px;
    background: linear-gradient(90deg, var(--iot-copper), var(--iot-metallic), var(--iot-sage));
    opacity: 0;
    transition: opacity 0.28s ease;
}

.model-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--iot-shadow-hover);
    border-color: var(--iot-soft-copper);
    background: linear-gradient(180deg, #FFFFFF 0%, #FFFDFC 100%);
}

.model-card:hover::before {
    opacity: 1;
}

.card-header {
    display: flex;
    justify-content: flex-start;
    align-items: flex-start;
    margin-bottom: 12px;
    gap: 12px;
}

.card-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--iot-graphite) 0%, var(--iot-graphite-2) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--iot-metallic);
    font-size: 20px;
    flex-shrink: 0;
    border: 1px solid rgba(230, 185, 128, 0.22);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.10), 0 10px 22px rgba(43, 45, 66, 0.16);
}

.card-actions {
    position: relative;
}

.card-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--iot-text);
    margin-bottom: 4px;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
}

.card-subtitle {
    font-size: 0.85rem;
    color: var(--iot-muted);
    margin-bottom: 10px;
    font-weight: 500;
}

.card-content {
    margin-bottom: 10px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid var(--iot-border);
    font-size: 0.8rem;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    color: var(--iot-muted);
    font-weight: 600;
}

.info-value {
    color: var(--iot-text);
    font-weight: 700;
}

.badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-top: 8px;
}

.badge-primary {
    background: rgba(43, 45, 66, 0.08);
    color: var(--iot-graphite);
    border: 1px solid rgba(43, 45, 66, 0.12);
}

.badge-success {
    background: rgba(107, 143, 113, 0.12);
    color: var(--iot-sage);
    border: 1px solid rgba(107, 143, 113, 0.20);
}

.badge-warning,
.badge-copper {
    background: rgba(192, 132, 87, 0.12);
    color: var(--iot-copper);
    border: 1px solid rgba(192, 132, 87, 0.20);
}

.card-footer {
    display: flex;
    gap: 8px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid var(--iot-border);
    position: relative;
}

.btn-sm {
    flex: 1;
    padding: 8px 12px;
    font-size: 0.8rem;
    border-radius: 9px;
    text-decoration: none;
    text-align: center;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.btn-action {
    flex: 1;
    background: linear-gradient(135deg, var(--iot-graphite) 0%, var(--iot-graphite-2) 100%) !important;
    color: #FFFFFF !important;
    padding: 8px 12px !important;
    border-radius: 9px !important;
    font-weight: 600 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease !important;
    font-size: 0.8rem !important;
    border: 1px solid rgba(229, 221, 213, 0.16) !important;
    position: relative !important;
    overflow: hidden !important;
    box-shadow: 0 8px 18px rgba(43, 45, 66, 0.14) !important;
}

.btn-action:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 10px 24px var(--iot-hover-glow) !important;
    background: linear-gradient(135deg, var(--iot-graphite-2) 0%, var(--iot-graphite) 100%) !important;
    color: #FFFFFF !important;
}

.btn-edit {
    background: linear-gradient(135deg, var(--iot-graphite) 0%, var(--iot-graphite-2) 100%);
    color: #FFFFFF;
    box-shadow: 0 8px 20px rgba(43, 45, 66, 0.16);
}

.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px var(--iot-hover-glow);
}

.btn-delete {
    background: linear-gradient(135deg, var(--iot-copper) 0%, var(--iot-soft-copper) 100%);
    color: #FFFFFF;
    box-shadow: 0 8px 20px rgba(192, 132, 87, 0.16);
}

.btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px var(--iot-hover-glow);
}

/* Dropdown Menu */
.dropdown-menu {
    border-radius: 14px;
    box-shadow: 0 18px 42px rgba(43, 45, 66, 0.14);
    border: 1px solid var(--iot-border);
    padding: 8px;
    z-index: 10 !important;
    background: var(--iot-card);
    min-width: 220px;
    max-height: 420px;
    overflow-y: auto;
    overflow-x: hidden;
}

.dropdown-menu::-webkit-scrollbar {
    width: 6px;
}

.dropdown-menu::-webkit-scrollbar-track {
    background: transparent;
}

.dropdown-menu::-webkit-scrollbar-thumb {
    background: var(--iot-soft-copper);
    border-radius: 3px;
}

.dropdown-menu::-webkit-scrollbar-thumb:hover {
    background: var(--iot-copper);
}

.dropdown-menu a {
    padding: 8px 12px;
    border-radius: 10px;
    transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: var(--iot-text);
    cursor: pointer;
    white-space: nowrap;
}

.dropdown-menu a i {
    color: var(--iot-copper);
    width: 16px;
    text-align: center;
}

.dropdown-menu a:hover {
    background: var(--iot-hover-glow);
    color: var(--iot-graphite);
    transform: translateX(2px);
}

/* Alerts */
.alert {
    border-radius: 18px;
    padding: 16px 20px;
    font-weight: 600;
    margin-bottom: 20px;
    border: 1px solid transparent;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-danger {
    background: rgba(192, 132, 87, 0.10);
    color: var(--iot-copper);
    border-color: rgba(192, 132, 87, 0.20);
    border-left: 4px solid var(--iot-copper);
}

.alert-success {
    background: rgba(107, 143, 113, 0.10);
    color: var(--iot-sage);
    border-color: rgba(107, 143, 113, 0.18);
    border-left: 4px solid var(--iot-sage);
}

.alert::before {
    font-size: 1.2rem;
}

/* Search & Filter */
.dataTables_filter {
    margin-bottom: 20px;
}

.dataTables_filter input {
    border-radius: 16px !important;
    padding: 12px 18px !important;
    border: 2px solid var(--iot-border) !important;
    box-shadow: 0 4px 12px rgba(43, 45, 66, 0.05) !important;
    font-size: 0.95rem;
    color: var(--iot-text);
    transition: border-color 0.25s ease, box-shadow 0.25s ease;
    background: #FFFFFF !important;
}

.dataTables_filter input::placeholder {
    color: var(--iot-muted);
}

.dataTables_filter input:focus {
    border-color: var(--iot-copper) !important;
    box-shadow: var(--iot-focus-ring) !important;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state-icon {
    font-size: 4rem;
    color: rgba(192, 132, 87, 0.22);
    margin-bottom: 20px;
}

.empty-state-text {
    color: var(--iot-muted);
    font-size: 1.1rem;
    font-weight: 500;
}

/* Optional premium IoT palette helpers for existing dashboard widgets if present elsewhere */
.stat-total,
.total-modules {
    color: var(--iot-graphite) !important;
}

.stat-online,
.online-modules {
    color: var(--iot-sage) !important;
}

.stat-offline,
.offline-modules {
    color: var(--iot-copper) !important;
}

.quick-action-card,
.download-card {
    background: #FFFFFF;
    border: 1px solid var(--iot-border);
    box-shadow: var(--iot-shadow-soft);
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.quick-action-card:hover,
.download-card:hover {
    transform: translateY(-4px);
    border-color: var(--iot-soft-copper);
    box-shadow: var(--iot-shadow-hover);
}

.download-card.api {
    background: linear-gradient(135deg, var(--iot-graphite) 0%, var(--iot-graphite-2) 100%);
}

.download-card.library {
    background: linear-gradient(135deg, var(--iot-sage) 0%, var(--iot-graphite-2) 100%);
}

.download-card.provision {
    background: linear-gradient(135deg, var(--iot-copper) 0%, var(--iot-soft-copper) 100%);
}

.download-card.driver {
    background: linear-gradient(135deg, #242638 0%, var(--iot-graphite) 100%);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .cards-container {
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 14px;
    }
}

@media (max-width: 768px) {
    .cards-container {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 12px;
    }

    .page-title {
        flex-direction: column;
        align-items: flex-start;
    }

    .page-title h2 {
        font-size: 1.5rem;
    }

    .page-title .d-flex {
        width: 100%;
        flex-direction: column;
    }

    .btn-gradient,
    .btn-success-modern,
    .btn-dark-modern {
        width: 100%;
        justify-content: center;
    }

    .card {
        padding: 20px;
    }
}

@media (max-width: 480px) {
    .cards-container {
        grid-template-columns: 1fr;
    }

    .model-card {
        padding: 18px;
    }

    .card-title {
        font-size: 1rem;
    }

    .info-row {
        flex-direction: column;
        gap: 4px;
    }
}

/* Loading Animation */
.loading {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(43, 45, 66, 0.18);
    border-radius: 50%;
    border-top-color: var(--iot-copper);
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.search-input {
    border-radius: 16px !important;
    padding: 12px 18px !important;
    border: 2px solid var(--iot-border) !important;
    width: 300px !important;
    max-width: 100% !important;
    box-shadow: 0 4px 12px rgba(43, 45, 66, 0.05) !important;
    color: var(--iot-text) !important;
    font-size: 0.95rem !important;
    transition: border-color 0.25s ease, box-shadow 0.25s ease !important;
    background: #FFFFFF !important;
}

.search-input::placeholder {
    color: var(--iot-muted) !important;
}

.search-input:focus {
    border-color: var(--iot-copper) !important;
    box-shadow: var(--iot-focus-ring) !important;
    outline: none !important;
}

.model-card.active {
    border-color: var(--iot-soft-copper);
    background: #F4ECE2;
    box-shadow: var(--iot-shadow-hover);
}

.model-card.active::before {
    opacity: 1;
}

.model-card.active .btn-action {
    background: linear-gradient(135deg, var(--iot-graphite-2) 0%, var(--iot-graphite) 100%) !important;
    box-shadow: 0 10px 24px var(--iot-hover-glow) !important;
}

/* Dropdown positioning - opens outside card cleanly */
.card,
#page-content,
.cards-container,
.model-card,
.card-footer,
.card-actions {
    overflow: visible !important;
}

.card-footer,
.card-actions {
    position: relative;
}

/* Keep the active/open card above neighboring cards */
.model-card.active,
.model-card:has(.dropdown-menu.show),
.model-card.dropdown-open {
    z-index: 9999 !important;
    border-color: var(--iot-soft-copper);
    box-shadow: var(--iot-shadow-hover);
}

.card-actions .dropdown-menu,
.card-footer .dropdown-menu,
.model-card .dropdown-menu {
    position: absolute !important;
    top: calc(100% + 8px) !important;
    right: 0 !important;
    left: auto !important;
    bottom: auto !important;
    margin: 0 !important;
    min-width: 230px;
    width: max-content;
    max-width: min(280px, calc(100vw - 28px));
    max-height: 420px;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 10050 !important;
    border-radius: 16px;
    border: 1px solid var(--iot-border);
    background: #FFFFFF;
    box-shadow: 0 22px 54px rgba(43, 45, 66, 0.18), 0 0 0 1px rgba(192, 132, 87, 0.08);
}

/* Small arrow/pointer outside the card */
.card-actions .dropdown-menu::before,
.card-footer .dropdown-menu::before,
.model-card .dropdown-menu::before {
    content: '';
    position: absolute;
    top: -7px;
    right: 18px;
    width: 14px;
    height: 14px;
    background: #FFFFFF;
    border-left: 1px solid var(--iot-border);
    border-top: 1px solid var(--iot-border);
    transform: rotate(45deg);
    z-index: -1;
}

.dropdown-menu.show {
    display: flex !important;
    flex-direction: column !important;
}

.dropdown-menu a.dropdown-item,
.dropdown-menu .dropdown-item {
    display: flex !important;
    align-items: center;
    gap: 9px;
    min-height: 38px;
    padding: 9px 12px !important;
    border-radius: 11px;
    color: var(--iot-text) !important;
    font-weight: 600;
}

.dropdown-menu a.dropdown-item:hover,
.dropdown-menu .dropdown-item:hover {
    background: rgba(192, 132, 87, 0.10) !important;
    color: var(--iot-graphite) !important;
}

@media (max-width: 575px) {
    .card-actions .dropdown-menu,
    .card-footer .dropdown-menu,
    .model-card .dropdown-menu {
        right: auto !important;
        left: 0 !important;
        min-width: 220px;
        max-width: calc(100vw - 36px);
    }

    .card-actions .dropdown-menu::before,
    .card-footer .dropdown-menu::before,
    .model-card .dropdown-menu::before {
        right: auto;
        left: 18px;
    }
}

</style>

<style>

    :root {

    /* Premium Industrial IoT palette - old variable support */
    --bg: #F8F5F2;
    --card: #FFFFFF;
    --text-main: #1B1E2B;
    --text-secondary: #6B7280;
    --border: #E5DDD5;
    --graphite: #2B2D42;
    --graphite-2: #3A3F58;
    --copper: #C08457;
    --soft-copper: #D4A373;
    --metallic: #E6B980;
    --sage: #6B8F71;
    --hover-glow: rgba(192, 132, 87, 0.18);
    --shadow-soft: 0 10px 30px rgba(43, 45, 66, 0.05);
    --shadow-hover: 0 18px 45px rgba(192, 132, 87, 0.18);
    --radius-lg: 28px;

    /* Premium Industrial IoT palette - new variable support */
    --iot-bg: var(--bg);
    --iot-card: var(--card);
    --iot-text: var(--text-main);
    --iot-text-main: var(--text-main);
    --iot-muted: var(--text-secondary);
    --iot-text-secondary: var(--text-secondary);
    --iot-border: var(--border);
    --iot-graphite: var(--graphite);
    --iot-graphite-2: var(--graphite-2);
    --iot-copper: var(--copper);
    --iot-soft-copper: var(--soft-copper);
    --iot-metallic: var(--metallic);
    --iot-sage: var(--sage);
    --iot-hover-glow: var(--hover-glow);
    --iot-shadow-soft: 0 10px 30px rgba(43, 45, 66, 0.07);
    --iot-shadow-card: 0 14px 40px rgba(43, 45, 66, 0.08);
    --iot-shadow-hover: 0 22px 54px rgba(192, 132, 87, 0.18);
    --iot-focus-ring: 0 0 0 4px rgba(192, 132, 87, 0.14);
    --iot-radius-lg: var(--radius-lg);

}

/* Base dropdown styling used by action menu */
/* Dropdown Menu */
.dropdown-menu {
    border-radius: 14px;
    box-shadow: 0 18px 42px rgba(43, 45, 66, 0.14);
    border: 1px solid var(--iot-border);
    padding: 8px;
    z-index: 10001 !important;
    background: var(--iot-card);
    min-width: 220px;
    max-height: 420px;
    overflow-y: auto;
    overflow-x: hidden;
}

.dropdown-menu::-webkit-scrollbar {
    width: 6px;
}

.dropdown-menu::-webkit-scrollbar-track {
    background: transparent;
}

.dropdown-menu::-webkit-scrollbar-thumb {
    background: var(--iot-soft-copper);
    border-radius: 3px;
}

.dropdown-menu::-webkit-scrollbar-thumb:hover {
    background: var(--iot-copper);
}

.dropdown-menu a {
    padding: 8px 12px;
    border-radius: 10px;
    transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: var(--iot-text);
    cursor: pointer;
    white-space: nowrap;
}

.dropdown-menu a i {
    color: var(--iot-copper);
    width: 16px;
    text-align: center;
}

.dropdown-menu a:hover {
    background: var(--iot-hover-glow);
    color: var(--iot-graphite);
    transform: translateX(2px);
}

/* Active state for cards when action dropdown is open */
.model-card.active {
    border-color: var(--iot-soft-copper);
    background: #F4ECE2;
    box-shadow: var(--iot-shadow-hover);
}

.model-card.active::before {
    opacity: 1;
}

.model-card.active .btn-action {
    background: linear-gradient(135deg, var(--iot-graphite-2) 0%, var(--iot-graphite) 100%) !important;
    box-shadow: 0 10px 24px var(--iot-hover-glow) !important;
}

/* Loading animation used by datatable processing overlay */
/* Loading Animation */
.loading {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(43, 45, 66, 0.18);
    border-radius: 50%;
    border-top-color: var(--iot-copper);
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Datatable toolbar + floating action dropdown */
/* Datatable-style toolbar for the existing card dashboard */
.datatable-tools,
.datatable-tools.row {
    display: flex !important;
    align-items: center !important;
    flex-wrap: nowrap !important;
    gap: 12px !important;
    width: 100% !important;
    margin: 0 0 18px !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    padding: 14px !important;
    border: 1px solid var(--iot-border) !important;
    border-radius: 18px !important;
    background: linear-gradient(180deg, #FFFFFF 0%, #FFFDFC 100%) !important;
    box-shadow: 0 10px 28px rgba(43, 45, 66, 0.055) !important;
    position: relative !important;
    overflow: visible !important;
}

/* Keep Bootstrap row/column widths from forcing a second line on desktop */
#model-toolbar::before,
#model-toolbar::after {
    display: none !important;
}

#model-toolbar > .toolbar-left-top,
#model-toolbar > .custom-toolbar {
    width: auto !important;
    max-width: none !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
}

#model-toolbar > .toolbar-left-top {
    flex: 0 0 auto !important;
}

#model-toolbar > .custom-toolbar {
    flex: 1 1 auto !important;
    min-width: 0 !important;
}

.toolbar-left-top,
.custom-toolbar {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: nowrap !important;
}

.custom-toolbar {
    justify-content: flex-end !important;
}

.dataTables_length,
.DTTT_container,
.datatable-export,
.dt-buttons {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin: 0 !important;
    flex: 0 0 auto !important;
}

.dataTables_length label,
.dataTables_filter label {
    margin: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    width: 100% !important;
}

#field-toggle-button,
#model-toolbar .ml15,
#model-toolbar .mr15 {
    margin-left: 0 !important;
    margin-right: 0 !important;
}

/* Search stays in the same desktop row and expands before buttons/category */
.dataTables_filter,
#item-table_filter {
    margin: 0 !important;
    flex: 1 1 320px !important;
    min-width: 240px !important;
    max-width: 380px !important;
}

#item-table_filter input,
#search-input {
    width: 100% !important;
    min-height: 42px !important;
    border-radius: 13px !important;
    border: 1px solid var(--iot-border) !important;
    background: #FFFFFF !important;
    color: var(--iot-text) !important;
    font-weight: 600 !important;
    box-shadow: 0 4px 12px rgba(43, 45, 66, 0.045) !important;
    padding: 8px 14px !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
}

#search-input::placeholder {
    color: var(--iot-muted) !important;
}

#search-input:focus {
    border-color: var(--iot-copper) !important;
    box-shadow: var(--iot-focus-ring) !important;
    outline: none !important;
}

.industrial-select,
#item-table_length select,
#category-filter {
    min-height: 42px !important;
    border-radius: 13px !important;
    border: 1px solid var(--iot-border) !important;
    background: #FFFFFF !important;
    color: var(--iot-text) !important;
    font-weight: 600 !important;
    box-shadow: 0 4px 12px rgba(43, 45, 66, 0.045) !important;
    padding: 8px 34px 8px 12px !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease !important;
}

#page-size-select {
    width: 86px !important;
}

#category-filter,
.w200 {
    width: 200px !important;
    max-width: 200px !important;
}

.industrial-select:focus,
#item-table_length select:focus,
#category-filter:focus {
    border-color: var(--iot-copper) !important;
    box-shadow: var(--iot-focus-ring) !important;
    outline: none !important;
}

/* Select2 support if the page length/category select is converted */
#model-toolbar .select2-container {
    min-width: 86px !important;
    height: 42px !important;
}

#model-toolbar .select2-choice,
#model-toolbar .select2-selection {
    min-height: 42px !important;
    border-radius: 13px !important;
    border: 1px solid var(--iot-border) !important;
    background: #FFFFFF !important;
    color: var(--iot-text) !important;
    box-shadow: 0 4px 12px rgba(43, 45, 66, 0.045) !important;
    display: flex !important;
    align-items: center !important;
}

.dt-buttons {
    flex-wrap: nowrap !important;
}

.dt-buttons .btn,
.column-show-hide-popover,
.btn-toolbar-industrial {
    min-height: 42px !important;
    border-radius: 13px !important;
    border: 1px solid var(--iot-border) !important;
    background: #FFFFFF !important;
    color: var(--iot-graphite) !important;
    font-weight: 700 !important;
    padding: 8px 14px !important;
    box-shadow: 0 4px 12px rgba(43, 45, 66, 0.045) !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease !important;
    white-space: nowrap !important;
}

.dt-buttons .btn:hover,
.column-show-hide-popover:hover,
.column-show-hide-popover.active,
.btn-toolbar-industrial:hover {
    transform: translateY(-1px) !important;
    border-color: var(--iot-soft-copper) !important;
    background: rgba(192, 132, 87, 0.08) !important;
    box-shadow: 0 10px 24px var(--iot-hover-glow) !important;
}

.column-tools-container {
    position: relative !important;
    overflow: visible !important;
}

.field-toggle-panel {
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    width: 210px;
    padding: 10px;
    border-radius: 14px;
    border: 1px solid var(--iot-border);
    background: #FFFFFF;
    box-shadow: 0 18px 42px rgba(43, 45, 66, 0.14);
    z-index: 10050;
}

.field-toggle-panel.show {
    display: block;
}

.field-toggle-panel label {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 9px;
    border-radius: 10px;
    color: var(--iot-text);
    font-weight: 600;
    font-size: 0.84rem;
    cursor: pointer;
    margin: 0;
}

.field-toggle-panel label:hover {
    background: rgba(192, 132, 87, 0.10);
}

.field-toggle-panel input {
    accent-color: var(--iot-copper);
}

.field-hidden {
    display: none !important;
}

.dataTables_processing.card {
    position: absolute !important;
    inset: 0 !important;
    min-height: 74px !important;
    border-radius: 18px !important;
    display: none;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(248, 245, 242, 0.72) !important;
    backdrop-filter: blur(4px) !important;
    z-index: 10040 !important;
    box-shadow: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.table-loader {
    width: 46px;
    height: 46px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #FFFFFF;
    border: 1px solid var(--iot-border);
    box-shadow: 0 12px 28px rgba(43, 45, 66, 0.10);
}

/* Floating action dropdown: appended to body so it is never clipped by cards/grid */
.card,
#page-content,
.cards-container,
.card-footer,
.model-card {
    overflow: visible !important;
}

.card-footer {
    position: relative !important;
}

.model-card.active {
    z-index: 1000 !important;
}

.dropdown-menu.industrial-action-menu {
    display: none;
    position: fixed !important;
    bottom: auto !important;
    top: auto !important;
    left: auto !important;
    right: auto !important;
    margin: 0 !important;
    width: 250px;
    max-width: calc(100vw - 24px);
    max-height: min(420px, calc(100vh - 24px));
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 999999 !important;
}

.dropdown-menu.industrial-action-menu.show {
    display: flex !important;
    flex-direction: column !important;
}

.dropdown-menu.industrial-action-menu a.dropdown-item {
    display: flex !important;
}

/* Tablet: wrap safely instead of overflowing */
@media (max-width: 991px) {
    .datatable-tools,
    .datatable-tools.row {
        flex-wrap: wrap !important;
    }

    #model-toolbar > .toolbar-left-top,
    #model-toolbar > .custom-toolbar {
        width: 100% !important;
        flex: 0 0 100% !important;
    }

    .toolbar-left-top,
    .custom-toolbar {
        justify-content: flex-start !important;
        flex-wrap: wrap !important;
    }

    .dataTables_filter,
    #item-table_filter {
        flex: 1 1 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
    }

    #category-filter,
    .w200 {
        width: 100% !important;
        max-width: 100% !important;
    }
}

/* Mobile */
@media (max-width: 575px) {
    .datatable-tools,
    .datatable-tools.row {
        padding: 12px !important;
        gap: 10px !important;
    }

    .toolbar-left-top,
    .custom-toolbar,
    .dataTables_length,
    .DTTT_container,
    .datatable-export,
    .dt-buttons {
        width: 100% !important;
    }

    .dt-buttons {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
    }

    .dt-buttons .btn,
    .column-show-hide-popover,
    #page-size-select,
    #category-filter,
    .industrial-select {
        width: 100% !important;
    }

    .field-toggle-panel {
        left: 0;
        right: auto;
        width: min(250px, calc(100vw - 32px));
    }
}

</style>

<!-- scroll menu -->

<style>
    /* Dropdown should scroll internally */
.dropdown-menu.industrial-action-menu {
    max-height: min(420px, calc(100vh - 24px)) !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    overscroll-behavior: contain;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: var(--iot-soft-copper) transparent;
}

.dropdown-menu.industrial-action-menu::-webkit-scrollbar {
    width: 7px;
}

.dropdown-menu.industrial-action-menu::-webkit-scrollbar-track {
    background: transparent;
}

.dropdown-menu.industrial-action-menu::-webkit-scrollbar-thumb {
    background: var(--iot-soft-copper);
    border-radius: 999px;
}

.dropdown-menu.industrial-action-menu::-webkit-scrollbar-thumb:hover {
    background: var(--iot-copper);
}
</style>

<?php
$user_id = $login_user->id;
session_start();
$_SESSION['user_id'] = $user_id;
?>

<div id="page-content" class="page-wrapper clearfix px-4 py-6">
    <div class="card">

        <?php if(session()->has('error')): ?>
            <div class="alert alert-danger"><?= session('error'); ?></div>
        <?php endif; ?>

        <?php if(session()->has('success')): ?>
            <div class="alert alert-success"><?= session('success'); ?></div>
        <?php endif; ?>

        <!-- Header -->
        <div class="page-title">
            <div>
                <h2>Efactor Model Management</h2>
                <small class="text-muted">Manage Brands, Products & Models</small>
            </div>


            <div class="d-flex gap-3 flex-wrap items-center">
                <a href="<?= site_url('items/brand') ?>" class="btn btn-gradient">Add Brand</a>
                <a href="<?= site_url('items/product') ?>" class="btn btn-gradient">Add Product</a>

                <?php if($user_id !== '1'){ ?>
                    <?= modal_anchor(
                        get_uri("items/modal_vendorproduct"),
                        'Add Model',
                        ["class" => "btn btn-success-modern"]
                    ); ?>
                <?php } else { ?>
                    <?= modal_anchor(
                        get_uri("items/modal_vendor_admin_product"),
                        'Assign Model',
                        ["class" => "btn btn-success-modern"]
                    ); ?>
                <?php } ?>

                <a href="<?= site_url('items/copy_api') ?>" class="btn btn-dark-modern">Copy API</a>
            </div>
        </div>


        <!-- Datatable Tools / Filters -->
        <div class="datatable-tools row" id="model-toolbar">
            <div class="col-md-2 toolbar-left-top pl15">
                <div class="dataTables_length" id="item-table_length">
                    <label>
                        <select id="page-size-select" name="item-table_length" aria-controls="item-table" class="custom-select custom-select-sm form-control form-control-sm industrial-select">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                            <option value="-1">All</option>
                        </select>
                    </label>
                </div>

                <div class="DTTT_container float-start column-tools-container">
                    <button type="button" class="btn btn-default column-show-hide-popover ml15" id="field-toggle-button" title="Show / hide card fields">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye-off icon-16"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                    <div class="field-toggle-panel" id="field-toggle-panel">
                        <label><input type="checkbox" class="field-toggle" data-field="product" checked> Product</label>
                        <label><input type="checkbox" class="field-toggle" data-field="model_no" checked> Model No</label>
                        <label><input type="checkbox" class="field-toggle" data-field="version" checked> Version</label>
                        <label><input type="checkbox" class="field-toggle" data-field="batch" checked> Batch</label>
                    </div>
                </div>
            </div>

            <div class="col-md-10 custom-toolbar pr15">
                <div id="item-table_filter" class="dataTables_filter">
                    <label>
                        <input type="search" id="search-input" class="form-control form-control-sm search-input" placeholder="Search" aria-controls="item-table">
                    </label>
                </div>

                <div class="datatable-export DTTT_container">
                    <div class="dt-buttons btn-group flex-wrap">
                        <button class="btn btn-default buttons-excel buttons-html5" id="export-excel-btn" tabindex="0" aria-controls="item-table" type="button"><span>Excel</span></button>
                        <button class="btn btn-default buttons-print" id="print-cards-btn" tabindex="0" aria-controls="item-table" type="button"><span>Print</span></button>
                    </div>
                </div>

                <div class="mr15 DTTT_container">
                    <select class="w200 industrial-select" id="category-filter" name="category_id">
                        <option value="">- Category -</option>
                    </select>
                </div>
            </div>

            <div id="item-table_processing" class="dataTables_processing card" style="display: none;">
                <div class="table-loader"><span class="loading"></span></div>
            </div>
        </div>

        <!-- Cards Grid -->
        <div id="cards-container" class="cards-container">
            <!-- Cards will be loaded here -->
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="empty-state" style="display: none;">
            <div class="empty-state-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <div class="empty-state-text">No models found</div>
        </div>

    </div>
</div>

        <!-- Cards Grid -->
        <div class="d-flex align-items-center justify-content-between mb-3" style="margin-top: 0; padding-top: 0;">
            <div id="filter-placeholder"></div>
            <div id="search-box"></div>
        </div>

        <div id="cards-container" class="cards-container">
            <!-- Cards will be loaded here -->
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="empty-state" style="display: none;">
            <div class="empty-state-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <div class="empty-state-text">No models found</div>
        </div>

    </div>
</div>

<!-- Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



<!-- scroll cursor -->

<script>
    // Keep mouse-wheel scroll inside action dropdown.
// When cursor is inside menu, menu scrolls. Outside menu, page scrolls normally.
$(document).on('wheel', '.industrial-action-menu', function (e) {
    const menu = this;

    if (menu.scrollHeight <= menu.clientHeight) {
        return;
    }

    const originalEvent = e.originalEvent;
    const delta = originalEvent.deltaY;

    menu.scrollTop += delta;

    e.preventDefault();
    e.stopPropagation();
});
</script>


<!-- data table script  -->


<script>
$(document).ready(function () {
    let currentPage = 1;
    let pageSize = 50;
    let allData = [];
    let filteredData = [];
    let activeSearchQuery = '';
    let activeCategoryValue = '';
    let visibleFields = {
        product: true,
        model_no: true,
        version: true,
        batch: true
    };

    setupToolbar();

    // Fetch data from backend
    function loadData(page = 1) {
        showProcessing(true);

        $.ajax({
            url: '<?php echo_uri("items/list_data1vendor") ?>',
            type: 'GET',
            dataType: 'json',
            data: {
                page: page,
                length: pageSize
            },
            success: function (response) {
                allData = response.data || [];
                currentPage = page;
                populateCategoryFilter(allData);
                applyFiltersAndRender();
                showProcessing(false);
            },
            error: function (err) {
                console.error('Error loading data:', err);
                allData = [];
                filteredData = [];
                showEmptyState();
                showProcessing(false);
            }
        });
    }

    // Toolbar events
    function setupToolbar() {
        $('#page-size-select').off('change').on('change', function () {
            pageSize = parseInt($(this).val(), 10);
            loadData(1);
        });

        $('#search-input').off('input').on('input', function () {
            activeSearchQuery = ($(this).val() || '').toLowerCase().trim();
            applyFiltersAndRender();
        });

        $('#category-filter').off('change').on('change', function () {
            activeCategoryValue = $(this).val() || '';
            applyFiltersAndRender();
        });

        $('#export-excel-btn').off('click').on('click', function () {
            exportFilteredData();
        });

        $('#print-cards-btn').off('click').on('click', function () {
            printFilteredCards();
        });

        $('#field-toggle-button').off('click').on('click', function (e) {
            e.stopPropagation();
            $('#field-toggle-panel').toggleClass('show');
            $('#field-toggle-button').toggleClass('active', $('#field-toggle-panel').hasClass('show'));
            closeFloatingDropdowns();
        });

        $('.field-toggle').off('change').on('change', function () {
            const field = $(this).data('field');
            visibleFields[field] = $(this).is(':checked');
            applyFieldVisibility();
        });
    }

    // Render cards
    function renderCards(data) {
        const container = $('#cards-container');

        // Remove body-level floating menus from previous render to prevent orphan dropdowns.
        $('.industrial-action-menu').remove();
        container.empty();

        if (!data || data.length === 0) {
            showEmptyState();
            return;
        }

        hideEmptyState();

        data.forEach((item, index) => {
            const menuKey = 'model-menu-' + (item.id || index);
            const card = `
                <div class="model-card" data-id="${escapeHtml(item.id || '')}" data-menu-key="${menuKey}">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-cube"></i>
                        </div>
                    </div>

                    <div class="card-content">
                        <h3 class="card-title" title="${escapeHtml(item.model_name || 'N/A')}">${escapeHtml(item.model_name || 'N/A')}</h3>
                        <p class="card-subtitle">${escapeHtml(item.brand_name || 'Brand')}</p>

                        <div class="info-row field-product">
                            <span class="info-label">Product:</span>
                            <span class="info-value" title="${escapeHtml(item.product_name || 'N/A')}">${escapeHtml(item.product_name || 'N/A')}</span>
                        </div>

                        <div class="info-row field-model_no">
                            <span class="info-label">Model No:</span>
                            <span class="info-value">${escapeHtml(item.model_no || 'N/A')}</span>
                        </div>

                        <div class="info-row field-version">
                            <span class="info-label">Version:</span>
                            <span class="info-value">${escapeHtml(item.version || 'N/A')}</span>
                        </div>

                        <div class="info-row field-batch">
                            <span class="info-label">Batch:</span>
                            <span class="info-value">${escapeHtml(item.batch || 'N/A')}</span>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div style="position: relative; flex: 1;">
                            <button type="button" class="btn-sm btn-action dropdown-toggle" data-menu-key="${menuKey}" title="Actions">
                                <i class="fas fa-cog"></i> Actions
                            </button>
                            ${renderActions(item, menuKey)}
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });

        applyFieldVisibility();
    }

    // Render action dropdown
    function renderActions(item, menuKey) {
        let actionsHtml = '<div class="dropdown-menu industrial-action-menu" data-menu-key="' + menuKey + '">';

        if (item.actions && Array.isArray(item.actions)) {
            item.actions.forEach((action, index) => {
                const icon = escapeHtml(action.icon || 'fa-circle');
                const href = escapeAttribute(action.url || '#');
                const classStr = escapeAttribute(action.class || '');
                const label = escapeHtml(action.label || 'Action');
                const title = escapeAttribute(action.title || action.label || 'Action');

                if (action.class === 'modal-action') {
                    actionsHtml += `<a href="${href}" class="dropdown-item ${classStr}" data-title="${title}"><i class="fas ${icon}"></i> ${label}</a>`;
                } else if (action.class === 'external') {
                    actionsHtml += `<a href="${href}" target="_blank" class="dropdown-item ${classStr}"><i class="fas ${icon}"></i> ${label}</a>`;
                } else if (action.class === 'confirm-action') {
                    actionsHtml += `<a href="#" onclick="if(confirm('Do you want to create the table?')) { window.location.href='${href}'; } return false;" class="dropdown-item ${classStr}"><i class="fas ${icon}"></i> ${label}</a>`;
                } else if (action.class === 'delete-action') {
                    actionsHtml += `<a href="#" onclick="deleteModel(${parseInt(item.id, 10) || 0})" class="dropdown-item ${classStr}"><i class="fas ${icon}"></i> ${label}</a>`;
                } else {
                    actionsHtml += `<a href="${href}" class="dropdown-item ${classStr}"><i class="fas ${icon}"></i> ${label}</a>`;
                }

                if (index < item.actions.length - 1 && !(action.class || '').includes('delete')) {
                    const nextAction = item.actions[index + 1];
                    if (nextAction && nextAction.class === 'delete-action') {
                        actionsHtml += '<hr style="margin: 6px 0; border: none; border-top: 1px solid #E5DDD5;">';
                    }
                }
            });
        }

        actionsHtml += '</div>';
        return actionsHtml;
    }

    // Apply search and category filter
    function applyFiltersAndRender() {
        filteredData = allData.filter(item => {
            return matchesSearch(item) && matchesCategory(item);
        });

        renderCards(filteredData);
    }

    function matchesSearch(item) {
        if (!activeSearchQuery) {
            return true;
        }

        const searchable = [
            item.model_name,
            item.brand_name,
            item.product_name,
            item.model_no,
            item.version,
            item.batch,
            item.category_name,
            item.category
        ].join(' ').toLowerCase();

        return searchable.indexOf(activeSearchQuery) !== -1;
    }

    function matchesCategory(item) {
        if (!activeCategoryValue) {
            return true;
        }

        const parts = activeCategoryValue.split(':');
        const filterType = parts[0] || '';
        const filterValue = parts.slice(1).join(':').toLowerCase();

        if (filterType === 'category') {
            return [item.category_id, item.product_category_id, item.category_name, item.product_category_name, item.category]
                .join(' ')
                .toLowerCase()
                .indexOf(filterValue) !== -1;
        }

        if (filterType === 'product') {
            return String(item.product_name || '').toLowerCase() === filterValue;
        }

        return true;
    }

    // Category options are created from backend data. If category fields are missing, product names are used as a safe fallback.
    function populateCategoryFilter(data) {
        const select = $('#category-filter');
        const previousValue = activeCategoryValue || select.val() || '';
        const options = new Map();
        let hasRealCategories = false;

        data.forEach(item => {
            const categoryId = item.category_id || item.product_category_id || '';
            const categoryName = item.category_name || item.product_category_name || item.category || '';

            if (categoryId || categoryName) {
                hasRealCategories = true;
                const key = 'category:' + String(categoryId || categoryName);
                const label = categoryName || String(categoryId);
                options.set(key, label);
            }
        });

        if (!hasRealCategories) {
            data.forEach(item => {
                if (item.product_name) {
                    options.set('product:' + String(item.product_name), item.product_name);
                }
            });
        }

        select.empty().append('<option value="">- Category -</option>');

        Array.from(options.entries())
            .sort((a, b) => String(a[1]).localeCompare(String(b[1])))
            .forEach(([value, label]) => {
                select.append(`<option value="${escapeAttribute(value)}">${escapeHtml(label)}</option>`);
            });

        if (previousValue && select.find(`option[value="${escapeSelector(previousValue)}"]`).length) {
            select.val(previousValue);
            activeCategoryValue = previousValue;
        } else {
            select.val('');
            activeCategoryValue = '';
        }
    }

    function applyFieldVisibility() {
        Object.keys(visibleFields).forEach(field => {
            $('.field-' + field).toggleClass('field-hidden', !visibleFields[field]);
        });
    }

    // Export current filtered cards as CSV file that Excel can open.
    function exportFilteredData() {
        const data = filteredData.length ? filteredData : allData;

        if (!data.length) {
            alert('No data available to export.');
            return;
        }

        const headers = ['Model Name', 'Brand', 'Product', 'Model No', 'Version', 'Batch'];
        const rows = data.map(item => [
            item.model_name || '',
            item.brand_name || '',
            item.product_name || '',
            item.model_no || '',
            item.version || '',
            item.batch || ''
        ]);

        const csv = [headers].concat(rows).map(row => {
            return row.map(value => '"' + String(value).replace(/"/g, '""') + '"').join(',');
        }).join('\n');

        const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'efactor-models-export.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    // Print only the current card grid, not the whole browser/admin shell.
    function printFilteredCards() {
        if (!filteredData.length) {
            alert('No cards available to print.');
            return;
        }

        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            window.print();
            return;
        }

        const cardsHtml = $('#cards-container').clone();
        cardsHtml.find('.dropdown-menu').remove();
        cardsHtml.find('.card-footer').remove();

        printWindow.document.write(`
            <!doctype html>
            <html>
            <head>
                <title>Efactor Models</title>
                <style>
                    body { margin: 24px; background: #F8F5F2; color: #1B1E2B; font-family: Arial, sans-serif; }
                    h1 { font-size: 22px; margin: 0 0 18px; color: #2B2D42; }
                    .cards-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
                    .model-card { background: #FFFFFF; border: 1px solid #E5DDD5; border-radius: 16px; padding: 16px; break-inside: avoid; }
                    .card-icon { display: none; }
                    .card-title { font-size: 16px; margin: 0 0 4px; color: #1B1E2B; }
                    .card-subtitle { margin: 0 0 10px; color: #6B7280; }
                    .info-row { display: flex; justify-content: space-between; border-top: 1px solid #E5DDD5; padding: 7px 0; font-size: 12px; }
                    .info-label { color: #6B7280; font-weight: 700; }
                    .info-value { color: #1B1E2B; font-weight: 700; text-align: right; }
                    @media print { body { margin: 12mm; } }
                </style>
            </head>
            <body>
                <h1>Efactor Model Management</h1>
                ${cardsHtml.prop('outerHTML')}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(function () {
            printWindow.print();
            printWindow.close();
        }, 300);
    }

    // Delete model
    window.deleteModel = function (id) {
        closeFloatingDropdowns();

        if (confirm('Are you sure you want to delete this model?')) {
            $.ajax({
                url: '<?php echo_uri("items/delete_model") ?>/' + id,
                type: 'DELETE',
                success: function (response) {
                    alert('Model deleted successfully');
                    loadData(currentPage);
                },
                error: function (err) {
                    alert('Error deleting model');
                    console.error(err);
                }
            });
        }
    };

    // Show empty state
    function showEmptyState() {
        $('#cards-container').hide();
        $('#empty-state').show();
    }

    // Hide empty state
    function hideEmptyState() {
        $('#empty-state').hide();
        $('#cards-container').show();
    }

    function showProcessing(show) {
        $('#item-table_processing').css('display', show ? 'flex' : 'none');
    }

    // Floating dropdown control. The menu is appended to body and positioned near the clicked button.
    $(document).on('click', '.dropdown-toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const button = $(this);
        const menuKey = button.data('menu-key');
        const menu = $('.industrial-action-menu[data-menu-key="' + menuKey + '"]');
        const card = button.closest('.model-card');
        const isOpen = menu.hasClass('show');

        closeFloatingDropdowns();
        $('#field-toggle-panel').removeClass('show');
            $('#field-toggle-button').removeClass('active');

        if (!isOpen) {
            menu.appendTo('body');
            positionFloatingDropdown(menu, button);
            menu.addClass('show');
            card.addClass('active');
        }
    });

    $(document).on('click', '.industrial-action-menu', function (e) {
        e.stopPropagation();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.btn-action').length && !$(e.target).closest('.industrial-action-menu').length) {
            closeFloatingDropdowns();
        }

        if (!$(e.target).closest('.column-tools-container').length) {
            $('#field-toggle-panel').removeClass('show');
            $('#field-toggle-button').removeClass('active');
        }
    });

    $(window).on('resize scroll', function () {
        closeFloatingDropdowns();
        $('#field-toggle-panel').removeClass('show');
            $('#field-toggle-button').removeClass('active');
    });

    function positionFloatingDropdown(menu, button) {
        const rect = button[0].getBoundingClientRect();
        const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
        const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
        const menuWidth = Math.min(260, viewportWidth - 24);
        const spaceBelow = viewportHeight - rect.bottom - 12;
        const spaceAbove = rect.top - 12;
        const maxHeight = Math.max(180, Math.min(420, Math.max(spaceBelow, spaceAbove)));
        let top = rect.bottom + 8;
        let left = rect.left;

        if (spaceBelow < 240 && spaceAbove > spaceBelow) {
            top = Math.max(12, rect.top - maxHeight - 8);
        }

        if (left + menuWidth > viewportWidth - 12) {
            left = viewportWidth - menuWidth - 12;
        }

        left = Math.max(12, left);

        menu.css({
            top: top + 'px',
            left: left + 'px',
            width: menuWidth + 'px',
            maxHeight: maxHeight + 'px'
        });
    }

    function closeFloatingDropdowns() {
        $('.industrial-action-menu').removeClass('show').removeAttr('style');
        $('.model-card').removeClass('active');
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeAttribute(value) {
        return escapeHtml(value).replace(/`/g, '&#096;');
    }

    function escapeSelector(value) {
        if (window.CSS && CSS.escape) {
            return CSS.escape(value);
        }

        return String(value).replace(/([ #;&,.+*~':"!^$[\]()=>|/@])/g, '\\$1');
    }

    // Initial load
    loadData();
});
</script>

<!-- ------------- -->
<script>
$(document).ready(function () {
    let currentPage = 1;
    let pageSize = 12;
    let allData = [];
    let filteredData = [];

    // Fetch data from backend
    function loadData(page = 1) {
        $.ajax({
            url: '<?php echo_uri("items/list_data1vendor") ?>',
            type: 'GET',
            dataType: 'json',
            data: {
                page: page,
                length: pageSize
            },
            success: function (response) {
                allData = response.data || [];
                filteredData = allData;
                currentPage = page;
                renderCards(filteredData);
                setupSearch();
            },
            error: function (err) {
                console.error('Error loading data:', err);
                showEmptyState();
            }
        });
    }

    // Render cards
    function renderCards(data) {
        const container = $('#cards-container');
        container.empty();

        if (!data || data.length === 0) {
            showEmptyState();
            return;
        }

        hideEmptyState();

        data.forEach((item, index) => {
            const card = `
                <div class="model-card" data-id="${item.id}">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-cube"></i>
                        </div>
                    </div>

                    <div class="card-content">
                        <h3 class="card-title" title="${item.model_name || 'N/A'}">${item.model_name || 'N/A'}</h3>
                        <p class="card-subtitle">${item.brand_name || 'Brand'}</p>

                        <div class="info-row">
                            <span class="info-label">Product:</span>
                            <span class="info-value" title="${item.product_name || 'N/A'}">${item.product_name || 'N/A'}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Model No:</span>
                            <span class="info-value">${item.model_no || 'N/A'}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Version:</span>
                            <span class="info-value">${item.version || 'N/A'}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Batch:</span>
                            <span class="info-value">${item.batch || 'N/A'}</span>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div style="position: relative; flex: 1;">
                            <button class="btn-sm btn-action dropdown-toggle" data-toggle="dropdown" title="Actions">
                                <i class="fas fa-cog"></i> Actions
                            </button>
                            ${renderActions(item)}
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    // Render action dropdown
    function renderActions(item) {
        let actionsHtml = '<div class="dropdown-menu">';
        
        if (item.actions && Array.isArray(item.actions)) {
            item.actions.forEach((action, index) => {
                const icon = `<i class="fas ${action.icon}"></i>`;
                const href = action.url || '#';
                const classStr = action.class || '';
                
                if (action.class === 'modal-action') {
                    actionsHtml += `<a href="${href}" class="dropdown-item ${classStr}" data-title="${action.title}"><i class="fas ${action.icon}"></i> ${action.label}</a>`;
                } else if (action.class === 'external') {
                    actionsHtml += `<a href="${href}" target="_blank" class="dropdown-item ${classStr}"><i class="fas ${action.icon}"></i> ${action.label}</a>`;
                } else if (action.class === 'confirm-action') {
                    actionsHtml += `<a href="#" onclick="if(confirm('Do you want to create the table?')) { window.location.href='${href}'; } return false;" class="dropdown-item ${classStr}"><i class="fas ${action.icon}"></i> ${action.label}</a>`;
                } else if (action.class === 'delete-action') {
                    actionsHtml += `<a href="#" onclick="deleteModel(${item.id})" class="dropdown-item ${classStr}"><i class="fas ${action.icon}"></i> ${action.label}</a>`;
                } else {
                    actionsHtml += `<a href="${href}" class="dropdown-item ${classStr}"><i class="fas ${action.icon}"></i> ${action.label}</a>`;
                }
                
                if (index < item.actions.length - 1 && !action.class?.includes('delete')) {
                    // Add divider before delete action
                    const nextAction = item.actions[index + 1];
                    if (nextAction && nextAction.class === 'delete-action') {
                        actionsHtml += '<hr style="margin: 6px 0; border: none; border-top: 1px solid #E5DDD5;">';
                    }
                }
            });
        }
        
        actionsHtml += '</div>';
        return actionsHtml;
    }

    // Setup search
    function setupSearch() {
        const searchBox = $('#search-box');
        if (searchBox.find('input').length === 0) {
            searchBox.html(`
                <input type="text" id="search-input" class="form-control search-input" placeholder="Search models, brands, products...">
            `);
        }

        $('#search-input').off('keyup').on('keyup', function () {
            const query = $(this).val().toLowerCase();
            filteredData = allData.filter(item => {
                return (item.model_name && item.model_name.toLowerCase().includes(query)) ||
                       (item.brand_name && item.brand_name.toLowerCase().includes(query)) ||
                       (item.product_name && item.product_name.toLowerCase().includes(query)) ||
                       (item.model_no && item.model_no.toLowerCase().includes(query));
            });
            renderCards(filteredData);
        });
    }

    // Delete model
    window.deleteModel = function (id) {
        if (confirm('Are you sure you want to delete this model?')) {
            $.ajax({
                url: '<?php echo_uri("items/delete_model") ?>/' + id,
                type: 'DELETE',
                success: function (response) {
                    alert('Model deleted successfully');
                    loadData();
                },
                error: function (err) {
                    alert('Error deleting model');
                    console.error(err);
                }
            });
        }
    };

    // Show empty state
    function showEmptyState() {
        $('#cards-container').hide();
        $('#empty-state').show();
    }

    // Hide empty state
    function hideEmptyState() {
        $('#empty-state').hide();
        $('#cards-container').show();
    }

    // Perfect dropdown control
    $(document).on("click", ".dropdown-toggle", function (e) {
        e.stopPropagation();
        const menu = $(this).closest('.card-footer').find(".dropdown-menu");
        const card = $(this).closest('.model-card');

        $(".dropdown-menu").not(menu).removeClass("show");
        $(".model-card").not(card).removeClass("active");

        const open = menu.hasClass("show");
        menu.toggleClass("show");
        card.toggleClass("active", !open);
    });

    $(document).on("click", function (e) {
        if (!$(e.target).closest(".btn-action").length && !$(e.target).closest(".dropdown-menu").length) {
            $(".dropdown-menu").removeClass("show");
            $(".model-card").removeClass("active");
        }
    });

    // Initial load
    loadData();
});
</script>


