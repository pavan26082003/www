<!-- ==================== LINKS ==================== -->
<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.4/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<!-- jQuery (required) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- ==================== STYLES ==================== -->
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
/* Main Container */
\#page-content {
    background: var(--iot-bg);
    border-radius: 24px;
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
    padding-bottom: 16px;
    border-bottom: 1px solid var(--iot-border);
    flex-wrap: wrap;
}
.page-title h2 {
    font-weight: 700;
    color: var(--iot-graphite);
    font-size: 2rem;
    letter-spacing: -0.03em;
}
.page-title h2 i {
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
.btn-gradient, .btn-success-modern, .btn-dark-modern {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 0.95rem;
    font-weight: 600;
    padding: 12px 24px;
    border-radius: 14px;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    text-decoration: none;
    border: 1px solid rgba(229, 221, 213, 0.55);
    cursor: pointer;
}
.btn-gradient {
    background: linear-gradient(135deg, var(--iot-graphite) 0%, var(--iot-graphite-2) 100%);
    color: #FFFFFF;
    box-shadow: 0 10px 26px rgba(43, 45, 66, 0.16);
}
.btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 36px var(--iot-hover-glow);
    color: #FFFFFF;
    text-decoration: none;
}
.btn-success-modern {
    background: linear-gradient(135deg, var(--iot-sage) 0%, var(--iot-graphite-2) 100%);
    color: #FFFFFF;
    box-shadow: 0 10px 26px rgba(107, 143, 113, 0.18);
}
.btn-success-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 36px rgba(107, 143, 113, 0.22);
    color: #FFFFFF;
    text-decoration: none;
}
.btn-dark-modern {
    background: linear-gradient(135deg, var(--iot-graphite) 0%, #242638 100%);
    color: #FFFFFF;
    box-shadow: 0 10px 26px rgba(43, 45, 66, 0.18);
}
.btn-dark-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 36px var(--iot-hover-glow);
    color: #FFFFFF;
    text-decoration: none;
}
/* Cards Grid Layout */
.cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
    overflow: visible;
}
/* Individual Card */
.model-card {
    background: var(--iot-card);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 8px 24px rgba(43, 45, 66, 0.055);
    border: 1px solid var(--iot-border);
    transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    position: relative;
    overflow: visible;
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
}
.model-card:hover::before {
    opacity: 1;
}
.model-card.active {
    border-color: var(--iot-copper);
    background: linear-gradient(180deg, #FFFFFF 0%, #FFFDFC 100%);
    box-shadow: var(--iot-shadow-hover);
}
.model-card.active::before {
    opacity: 1;
}
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}
.card-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--iot-graphite) 0%, var(--iot-graphite-2) 100%);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--iot-metallic);
    font-size: 24px;
    border: 1px solid rgba(230, 185, 128, 0.22);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.10), 0 10px 22px rgba(43, 45, 66, 0.16);
}
/* Product + Brand images on Vendor cards */
    /* The header image represents the specific model, not its product category. */
    .card-icon.has-model-logo {
        padding: 4px;
        overflow: hidden;
        background: #FFFFFF;
        border-color: rgba(192, 132, 87, .24);
    }
    .card-icon.has-model-logo img {
        width: 100%;
        height: 100%;
        display: block;
        border-radius: 10px;
        object-fit: contain;
        background: #FFFFFF;
    }
    .card-icon.has-product-image {
    padding: 4px;
    overflow: hidden;
    background: #FFFFFF;
}
.card-icon.has-product-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 10px;
    display: block;
}
.vendor-brand-line {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}
.brand-card-logo-wrap {
    width: 30px;
    height: 30px;
    flex: 0 0 30px;
    border-radius: 9px;
    border: 1px solid var(--iot-border);
    background: #FFFFFF;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.brand-card-logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    padding: 3px;
}
.brand-card-logo-fallback {
    color: var(--iot-copper);
    font-size: 14px;
}
.product-value-with-logo {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    min-width: 0;
}
.product-card-thumb {
    width: 30px;
    height: 30px;
    flex: 0 0 30px;
    object-fit: contain;
    border-radius: 8px;
    border: 1px solid var(--iot-border);
    background: #FFFFFF;
    padding: 2px;
}
.card-header-actions {
    display: flex;
    gap: 6px;
}
.btn-edit-card {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: 1px solid var(--iot-border);
    background: var(--iot-card);
    color: var(--iot-muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 14px;
}
.btn-edit-card:hover {
    background: var(--iot-hover-glow);
    color: var(--iot-copper);
    border-color: var(--iot-soft-copper);
    transform: scale(1.05);
}
.card-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--iot-text);
    margin-bottom: 5px;
}
.card-subtitle {
    font-size: 0.85rem;
    color: var(--iot-muted);
    margin-bottom: 15px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--iot-border);
    font-size: 0.85rem;
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
    text-align: right;
}
.card-footer {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid var(--iot-border);
    position: relative;
}
.btn-action {
    width: 100%;
    padding: 10px 16px;
    font-size: 0.9rem;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--iot-graphite) 0%, var(--iot-graphite-2) 100%);
    color: #FFFFFF;
    border: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px var(--iot-hover-glow);
}
.model-card.active .btn-action {
    background: linear-gradient(135deg, var(--iot-graphite-2) 0%, var(--iot-graphite) 100%);
    box-shadow: 0 10px 24px var(--iot-hover-glow);
}
/* Floating Dropdown Menu */
.floating-dropdown {
    position: fixed;
    background: var(--iot-card);
    border-radius: 14px;
    box-shadow: 0 18px 42px rgba(43, 45, 66, 0.2);
    border: 1px solid var(--iot-border);
    padding: 8px;
    min-width: 220px;
    max-width: 280px;
    max-height: 320px;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 100000;
    display: none;
    opacity: 0;
    transition: opacity 0.15s ease;
}
.floating-dropdown.show {
    display: block;
    opacity: 1;
}
/* Custom scrollbar for dropdown */
.floating-dropdown::-webkit-scrollbar {
    width: 6px;
}
.floating-dropdown::-webkit-scrollbar-track {
    background: var(--iot-border);
    border-radius: 3px;
}
.floating-dropdown::-webkit-scrollbar-thumb {
    background: var(--iot-copper);
    border-radius: 3px;
}
.floating-dropdown::-webkit-scrollbar-thumb:hover {
    background: var(--iot-soft-copper);
}
/* Dropdown items */
.floating-dropdown .dropdown-item {
    padding: 10px 14px;
    border-radius: 10px;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.85rem;
    color: var(--iot-text);
    cursor: pointer;
    text-decoration: none;
    font-weight: 500;
}
.floating-dropdown .dropdown-item:hover {
    background: var(--iot-hover-glow);
    color: var(--iot-graphite);
    transform: translateX(3px);
    text-decoration: none;
}
.floating-dropdown .dropdown-item i {
    color: var(--iot-copper);
    width: 20px;
    text-align: center;
}
.floating-dropdown hr {
    margin: 8px 0;
    border: none;
    border-top: 1px solid var(--iot-border);
}
/* Edit Modal Styles */
body.edit-modal-open {
    overflow: hidden !important;
    width: 100%;
}
.edit-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(27, 30, 43, 0.5);
    backdrop-filter: blur(4px);
    z-index: 999999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    overflow: hidden;
    overscroll-behavior: none;
    touch-action: none;
    animation: fadeIn 0.2s ease;
}
.edit-modal-overlay.active {
    display: flex;
}
.edit-modal {
    background: var(--iot-card);
    border-radius: 24px;
    padding: 32px;
    max-width: 560px;
    width: 92%;
    max-height: calc(100dvh - 40px);
    overflow-y: auto;
    overflow-x: hidden;
    overscroll-behavior: contain;
    -webkit-overflow-scrolling: touch;
    touch-action: pan-y;
    scrollbar-width: thin;
    scrollbar-color: var(--iot-copper) var(--iot-border);
    box-shadow: 0 30px 60px rgba(43, 45, 66, 0.25);
    animation: slideUp 0.3s ease;
}
.edit-modal::-webkit-scrollbar {
    width: 8px;
}
.edit-modal::-webkit-scrollbar-track {
    background: var(--iot-border);
    border-radius: 999px;
}
.edit-modal::-webkit-scrollbar-thumb {
    background: var(--iot-copper);
    border-radius: 999px;
    border: 2px solid var(--iot-border);
}
.edit-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--iot-border);
}
.edit-modal-header h3 {
    font-weight: 700;
    color: var(--iot-graphite);
    font-size: 1.3rem;
    margin: 0;
}
.edit-modal-header h3 i {
    color: var(--iot-copper);
    margin-right: 10px;
}
.edit-modal-close {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    border: 1px solid var(--iot-border);
    background: transparent;
    color: var(--iot-muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 18px;
}
.edit-modal-close:hover {
    background: rgba(192, 132, 87, 0.1);
    color: var(--iot-copper);
    border-color: var(--iot-soft-copper);
}
.form-group {
    margin-bottom: 18px;
}
.form-group label {
    display: block;
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--iot-text);
    margin-bottom: 6px;
}
.form-group label i {
    color: var(--iot-copper);
    margin-right: 6px;
}
.form-control-modern {
    width: 100%;
    padding: 12px 16px;
    border-radius: 14px;
    border: 2px solid var(--iot-border);
    background: #FFFFFF;
    color: var(--iot-text);
    font-size: 0.95rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    font-family: 'Poppins', sans-serif;
}
.form-control-modern:focus {
    outline: none;
    border-color: var(--iot-copper);
    box-shadow: var(--iot-focus-ring);
}
.form-control-modern:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.edit-modal-footer {
    display: flex;
    gap: 12px;
    margin-top: 24px;
    padding-top: 16px;
    border-top: 2px solid var(--iot-border);
}
.btn-save-edit {
    flex: 1;
    padding: 12px 24px;
    border-radius: 14px;
    border: none;
    background: linear-gradient(135deg, var(--iot-sage) 0%, var(--iot-graphite-2) 100%);
    color: #FFFFFF;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.btn-save-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(107, 143, 113, 0.22);
}
.btn-cancel-edit {
    padding: 12px 24px;
    border-radius: 14px;
    border: 2px solid var(--iot-border);
    background: transparent;
    color: var(--iot-text);
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-cancel-edit:hover {
    background: var(--iot-hover-glow);
    border-color: var(--iot-soft-copper);
}
/* Datatable Tools */
.datatable-tools {
    display: flex !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 12px !important;
    width: 100% !important;
    margin: 0 0 18px !important;
    padding: 14px !important;
    border: 1px solid var(--iot-border) !important;
    border-radius: 18px !important;
    background: linear-gradient(180deg, #FFFFFF 0%, #FFFDFC 100%) !important;
    box-shadow: 0 10px 28px rgba(43, 45, 66, 0.055) !important;
}
.toolbar-left-top {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
}
.custom-toolbar {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
    flex: 1;
    justify-content: flex-end;
}
.industrial-select {
    min-height: 42px !important;
    border-radius: 13px !important;
    border: 1px solid var(--iot-border) !important;
    background: #FFFFFF !important;
    color: var(--iot-text) !important;
    font-weight: 600 !important;
    padding: 8px 34px 8px 12px !important;
    cursor: pointer;
}
\#page-size-select {
    width: 86px !important;
}
\#category-filter {
    width: 200px !important;
}
.search-input {
    border-radius: 16px !important;
    padding: 12px 18px !important;
    border: 2px solid var(--iot-border) !important;
    width: 300px !important;
    box-shadow: 0 4px 12px rgba(43, 45, 66, 0.05) !important;
    color: var(--iot-text) !important;
    font-size: 0.95rem !important;
    background: #FFFFFF !important;
}
.search-input:focus {
    border-color: var(--iot-copper) !important;
    box-shadow: var(--iot-focus-ring) !important;
    outline: none !important;
}
.column-show-hide-popover {
    min-height: 42px !important;
    border-radius: 13px !important;
    border: 1px solid var(--iot-border) !important;
    background: #FFFFFF !important;
    color: var(--iot-graphite) !important;
    font-weight: 700 !important;
    padding: 8px 14px !important;
    cursor: pointer;
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
    cursor: pointer;
    margin: 0;
}
.field-hidden {
    display: none !important;
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
/* Alerts */
.alert {
    border-radius: 18px;
    padding: 16px 20px;
    font-weight: 600;
    margin-bottom: 20px;
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
/* Loading */
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
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
.dataTables_processing {
    position: absolute !important;
    inset: 0 !important;
    display: none;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(248, 245, 242, 0.72) !important;
    backdrop-filter: blur(4px) !important;
    z-index: 10040 !important;
    border-radius: 18px !important;
}
/* Responsive */
@media (max-width: 768px) {
    #page-content {
        padding: 12px !important;
    }
    .card {
        padding: 14px !important;
        border-radius: 18px !important;
    }
    .cards-container {
        grid-template-columns: 1fr;
    }
    .page-title {
        flex-direction: column;
        align-items: stretch;
    }
    .page-title h2 {
        font-size: 1.35rem;
        line-height: 1.25;
    }
    .page-title > .d-flex {
        display: grid !important;
        grid-template-columns: 1fr;
        gap: 10px !important;
        width: 100%;
    }
    .page-title .btn-gradient,
    .page-title .btn-success-modern,
    .page-title .btn-dark-modern {
        width: 100%;
        min-height: 44px;
        padding: 10px 14px;
        border-radius: 12px;
    }
    .datatable-tools {
        display: grid !important;
        grid-template-columns: 1fr !important;
        align-items: stretch !important;
        gap: 10px !important;
        padding: 12px !important;
        border-radius: 16px !important;
        overflow: visible !important;
    }
    .toolbar-left-top {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) 52px !important;
        gap: 10px !important;
        width: 100% !important;
    }
    .custom-toolbar {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 10px !important;
        width: 100% !important;
        justify-content: stretch !important;
    }
    .search-input {
        width: 100% !important;
        min-width: 0 !important;
        grid-column: 1 / -1 !important;
        height: 46px !important;
        padding: 10px 14px !important;
    }
    #page-size-select,
    #category-filter,
    .column-show-hide-popover {
        width: 100% !important;
        min-width: 0 !important;
        min-height: 44px !important;
        border-radius: 12px !important;
    }
    #category-filter {
        grid-column: 1 / -1 !important;
    }
    .floating-dropdown {
        max-width: calc(100vw - 20px);
    }
    .edit-modal {
        padding: 20px;
        width: 96%;
        max-height: calc(100dvh - 20px);
    }
    .edit-modal-overlay {
        padding: 10px;
    }
}
/* Edit Model logo controls use the same palette as the rest of vendor.php. */
.edit-model-logo-grid {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px;
    border: 1px solid var(--iot-border);
    border-radius: 14px;
    background: #FFFDFC;
    margin-bottom: 12px;
}
.edit-model-logo-preview {
    flex: 0 0 82px;
    width: 82px;
    height: 82px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--iot-graphite), var(--iot-sage));
    border: 2px solid rgba(192,132,87,.20);
    border-radius: 16px;
    overflow: hidden;
    color: white;
    font-size: 27px;
    font-weight: 800;
}
.edit-model-logo-preview img { width:100%; height:100%; object-fit:contain; background:white; }
.edit-logo-choices { display: flex; flex-wrap: wrap; gap: 8px; }
.edit-logo-choices label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 12px;
    border: 1px solid var(--iot-border);
    border-radius: 10px;
    background: white;
    cursor: pointer;
    font-size: 12px;
    margin: 0;
}
.edit-logo-choices label:has(input:checked) {
    border-color: var(--iot-sage);
    background: #eff5ec;
}
.edit-logo-choices input { accent-color: var(--iot-sage); }
#edit-logo-upload-fields { display: none; margin-top: 12px; }
#edit-model-form:has(input[name="model_logo_mode"][value="upload"]:checked) #edit-logo-upload-fields { display: block; }
#edit-model-logo-file { padding: 10px; }
#edit-logo-feedback { margin-top: 7px; font-size: 12px; color: var(--iot-muted); }
@media (max-width: 530px) { .edit-model-logo-grid { flex-direction: column; align-items: flex-start; } }
</style>



<!-- ==================== PHP / HTML ==================== -->
<?php
$user_id = $login_user->id;
?>
<div id="page-content" class="page-wrapper clearfix px-4 py-6">
    <div class="card" style="position: relative;">
        <?php if(session()->has('error')): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?= session('error'); ?>
            </div>
        <?php endif; ?>
        <?php if(session()->has('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= session('success'); ?>
            </div>
        <?php endif; ?>
        <!-- Header -->
        <div class="page-title">
            <div>
                <h2><i class="fas fa-cubes"></i> Efactor Model Management</h2>
                <small class="text-muted">Manage Brands, Products & Models</small>
            </div>
            <div class="d-flex gap-3 flex-wrap">
                <a href="<?= site_url('items/brand') ?>" class="btn-gradient">
                    <i class="fas fa-building"></i> Add Brand
                </a>
                <a href="<?= site_url('items/product') ?>" class="btn-gradient">
                    <i class="fas fa-box"></i> Add Product
                </a>
                <?php if($user_id != 1): ?>
                    <a href="<?= get_uri('items/modal_vendorproduct') ?>" class="btn-success-modern modal-action" data-url="<?= get_uri('items/modal_vendorproduct') ?>">
                        <i class="fas fa-plus-circle"></i> Add Model
                    </a>
                <?php else: ?>
                    <a href="<?= get_uri('items/modal_vendor_admin_product') ?>" class="btn-success-modern modal-action" data-url="<?= get_uri('items/modal_vendor_admin_product') ?>">
                        <i class="fas fa-exchange-alt"></i> Assign Model
                    </a>
                <?php endif; ?>
                <a href="<?= site_url('items/copy_api') ?>" class="btn-dark-modern">
                    <i class="fas fa-code"></i> Copy API
                </a>
                <a href="<?= site_url('items/copy_modal') ?>" class="btn-dark-modern">
                    <i class="fas fa-copy"></i> Copy Model
                </a>
                  <a href="<?= site_url('items/modal_config') ?>" class="btn-dark-modern">
                    <i class="fas fa-code"></i> Efactor Test
                </a>
                <!-- <a href="<?= site_url('items/model_config') ?>" class="btn-dark-modern">
                    <i class="fas fa-sliders-h"></i> Model Config
                </a> -->
            </div>
        </div>
        <!-- Datatable Tools -->
        <div class="datatable-tools" id="model-toolbar">
            <div class="toolbar-left-top">
                <select id="page-size-select" class="industrial-select">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50" selected>50</option>
                    <option value="100">100</option>
                    <option value="-1">All</option>
                </select>
                <div class="column-tools-container" style="position: relative;">
                    <button type="button" class="column-show-hide-popover" id="field-toggle-button">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                    <div class="field-toggle-panel" id="field-toggle-panel">
                        <label><input type="checkbox" class="field-toggle" data-field="product" checked> Product</label>
                        <label><input type="checkbox" class="field-toggle" data-field="model_no" checked> Model No</label>
                        <label><input type="checkbox" class="field-toggle" data-field="version" checked> Version</label>
                        <label><input type="checkbox" class="field-toggle" data-field="batch" checked> Batch</label>
                    </div>
                </div>
            </div>
            <div class="custom-toolbar">
                <input type="search" id="search-input" class="search-input" placeholder="🔍 Search models, brands, products...">
                <button class="column-show-hide-popover" id="export-excel-btn">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button class="column-show-hide-popover" id="print-cards-btn">
                    <i class="fas fa-print"></i> Print
                </button>
                <select id="category-filter" class="industrial-select">
                    <option value="">- Category -</option>
                </select>
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
        <!-- Processing -->
        <div id="item-table_processing" class="dataTables_processing card" style="display: none;">
            <div class="loading"></div>
        </div>
    </div>
</div>
<!-- ==================== EDIT MODAL ==================== -->
<div id="edit-model-modal" class="edit-modal-overlay">
    <div class="edit-modal">
        <div class="edit-modal-header">
            <h3><i class="fas fa-edit"></i> Edit Model Details</h3>
            <button class="edit-modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
                    <form id="edit-model-form" enctype="multipart/form-data">
                <?php if (function_exists('csrf_field')) { echo csrf_field(); } ?>
            <input type="hidden" id="edit-model-id" name="id">
            <div class="form-group">
                <label><i class="fas fa-tag"></i> Model Name</label>
                <input type="text" id="edit-model-name" name="model_name" class="form-control-modern" placeholder="Enter model name" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-building"></i> Brand</label>
                <input type="text" id="edit-brand-name" name="brand_name" class="form-control-modern" disabled>
            </div>
            <div class="form-group">
                <label><i class="fas fa-box"></i> Product</label>
                <input type="text" id="edit-product-name" name="product_name" class="form-control-modern" placeholder="Enter product name">
            </div>
            <div class="form-group">
                <label><i class="fas fa-barcode"></i> Model No</label>
                <input type="text" id="edit-model-no" name="model_no" class="form-control-modern" placeholder="Enter model number">
            </div>
            <div class="form-group">
                <label><i class="fas fa-code-branch"></i> Version</label>
                <input type="text" id="edit-version" name="version" class="form-control-modern" placeholder="Enter version">
            </div>
            <div class="form-group">
                <label><i class="fas fa-layer-group"></i> Batch</label>
                <input type="text" id="edit-batch" name="batch" class="form-control-modern" placeholder="Enter batch number">
            </div>
                            <!-- Model logo: keep, generate from current model name, or replace via file. -->
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Model Logo</label>
                    <div class="edit-model-logo-grid">
                        <div class="edit-model-logo-preview" id="edit-logo-preview" aria-label="Current model logo">
                            <span id="edit-logo-initials">M</span>
                            <img id="edit-logo-image" alt="Model logo" style="display:none;">
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="edit-logo-choices">
                                <label id="edit-logo-keep-label"><input type="radio" name="model_logo_mode" value="keep" checked> Keep Current</label>
                                <label><input type="radio" name="model_logo_mode" value="generate"> Create from Model Name</label>
                                <label><input type="radio" name="model_logo_mode" value="upload"> Upload Image</label>
                            </div>
                            <div id="edit-logo-feedback" role="status" aria-live="polite">Keep the current model logo.</div>
                        </div>
                    </div>
                    <div id="edit-logo-upload-fields">
                        <label for="edit-model-logo-file">Choose image (PNG, JPG or WEBP, max 2 MB)</label>
                        <input type="file" id="edit-model-logo-file" name="model_logo_file"
                            class="form-control-modern" accept="image/png,image/jpeg,image/webp">
                    </div>
                </div>

                <div class="edit-modal-footer">
                <button type="button" class="btn-cancel-edit" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-save-edit">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
<!-- ==================== SCRIPTS ==================== -->
<script>
$(document).ready(function () {
    let allData = [];
    let filteredData = [];
    let activeDropdownId = null;
    let activeSearchQuery = '';
    let activeCategoryValue = '';
    let pageSize = 50;
    let pointerX = 0;
    let pointerY = 0;
    let visibleFields = {
        product: true,
        model_no: true,
        version: true,
        batch: true
    };
    let currentEditId = null;
    let modalPageScrollY = 0;
    const vendorViewStateKey = 'vendor-card-view-state:' + window.location.pathname;
    let savedPageScrollY = 0;
    let shouldRestoreScroll = false;
    // Save the current toolbar and card-filter state for this browser tab.
    function saveVendorViewState() {
        const state = {
            searchText: $('#search-input').val() || '',
            category: activeCategoryValue,
            pageSize: pageSize,
            visibleFields: visibleFields,
            scrollY: window.scrollY || window.pageYOffset || 0
        };
        try {
            sessionStorage.setItem(vendorViewStateKey, JSON.stringify(state));
        } catch (error) {
            console.warn('Unable to save vendor view state:', error);
        }
    }
    // Restore state before cards are loaded so the first render is already
    // filtered and the search input never flashes back to an empty value.
    function restoreVendorViewState() {
        try {
            const savedState = JSON.parse(sessionStorage.getItem(vendorViewStateKey) || 'null');
            if (!savedState) return;
            const searchText = String(savedState.searchText || '');
            activeSearchQuery = searchText.toLowerCase().trim();
            activeCategoryValue = String(savedState.category || '');
            const restoredPageSize = parseInt(savedState.pageSize, 10);
            if ([10, 25, 50, 100, -1].includes(restoredPageSize)) {
                pageSize = restoredPageSize;
            }
            if (savedState.visibleFields && typeof savedState.visibleFields === 'object') {
                Object.keys(visibleFields).forEach(function(field) {
                    if (typeof savedState.visibleFields[field] === 'boolean') {
                        visibleFields[field] = savedState.visibleFields[field];
                    }
                });
            }
            savedPageScrollY = Number(savedState.scrollY) || 0;
            shouldRestoreScroll = true;
            $('#search-input').val(searchText);
            $('#page-size-select').val(String(pageSize));
            $('.field-toggle').each(function() {
                const field = $(this).data('field');
                $(this).prop('checked', visibleFields[field] !== false);
            });
        } catch (error) {
            console.warn('Unable to restore vendor view state:', error);
            sessionStorage.removeItem(vendorViewStateKey);
        }
    }
    // Load data from backend
    function loadData() {
        showProcessing(true);
        $.ajax({
            url: '<?php echo get_uri("items/list_data1vendor") ?>',
            type: 'GET',
            dataType: 'json',
            cache: false,
            data: {
                // Prevent the browser/proxy from returning model details cached
                // before the edit was saved in the database.
                _ts: Date.now()
            },
            headers: {
                'Cache-Control': 'no-cache'
            },
            success: function (response) {
                // Support both the DataTables response shape ({data: [...]}) and
                // a plain array response.
                allData = Array.isArray(response)
                    ? response
                    : (Array.isArray(response.data) ? response.data : []);
                populateCategoryFilter(allData);
                $('#category-filter').val(activeCategoryValue);
                applyFiltersAndRender();
                showProcessing(false);
                if (shouldRestoreScroll) {
                    shouldRestoreScroll = false;
                    requestAnimationFrame(function() {
                        window.scrollTo(0, savedPageScrollY);
                    });
                }
            },
            error: function (err) {
                console.error('Error loading data:', err);
                allData = [];
                showEmptyState();
                showProcessing(false);
            }
        });
    }
    // Resolve DB filename or full URL into a usable image URL.
    function resolveVendorImageUrl(value) {
        value = String(value || '').trim();
        if (!value) return '';
        if (/^(https?:)?\/\//i.test(value) || value.charAt(0) === '/') {
            return value;
        }
        const filename = value.replace(/\\/g, '/').split('/').pop();
        return <?php echo json_encode(base_url('files/brand_logo/')); ?> + encodeURIComponent(filename);
    }
    // Model images are stored separately from brand/product images.
    function resolveModelLogoUrl(value) {
        value = String(value || '').trim();
        if (!value) return '';
        if (/^(https?:)?\/\//i.test(value) || value.charAt(0) === '/') return value;
        const filename = value.replace(/\\/g, '/').split('/').pop();
        return <?php echo json_encode(base_url('files/model_logo/')); ?> + encodeURIComponent(filename);
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
        data.forEach((item) => {
            const brandImage = resolveVendorImageUrl(
                item.brand_image || item.brand_logo || item.brandlogo || ''
            );
            const productImage = resolveVendorImageUrl(
                item.product_image || item.Product_image || item.prodimg || ''
            );
            const modelLogo = item.model_logo_url || resolveModelLogoUrl(item.model_logo || '');
            const modelTopHtml = modelLogo
                ? `<img src="${escapeAttr(modelLogo)}" alt="${escapeAttr(item.model_name || 'Model')} logo" loading="lazy" onerror="this.parentElement.classList.remove('has-model-logo'); this.parentElement.innerHTML='<i class=&quot;fas fa-microchip&quot;></i>';">`
                : `<i class="fas fa-microchip"></i>`;
            const brandLogoHtml = brandImage
                ? `<img class="brand-card-logo" src="${escapeAttr(brandImage)}" alt="${escapeAttr(item.brand_name || 'Brand')}" loading="lazy" onerror="this.outerHTML='<i class=&quot;fas fa-building brand-card-logo-fallback&quot;></i>';">`
                : `<i class="fas fa-building brand-card-logo-fallback"></i>`;
            const productThumbHtml = productImage
                ? `<img class="product-card-thumb" src="${escapeAttr(productImage)}" alt="${escapeAttr(item.product_name || 'Product')}" loading="lazy" onerror="this.style.display='none';">`
                : '';
            const card = `
                <div class="model-card" data-id="${escapeHtml(item.id)}" id="card-${item.id}">
                    <div class="card-header">
                        <div class="card-icon ${modelLogo ? 'has-model-logo' : ''}">
                            ${modelTopHtml}
                        </div>
                        <div class="card-header-actions">
                            <button class="btn-edit-card" onclick="openEditModalFromCard(${item.id}, this)" title="Edit Model">
                                <i class="fas fa-pen"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title" title="${escapeHtml(item.model_name || 'N/A')}">
                            ${escapeHtml(item.model_name || 'N/A')}
                        </h3>
                        <div class="card-subtitle vendor-brand-line">
                            <span class="brand-card-logo-wrap">${brandLogoHtml}</span>
                            <span>${escapeHtml(item.brand_name || 'No Brand')}</span>
                        </div>
                        <div class="info-row field-product">
                            <span class="info-label"><i class="fas fa-tag"></i> Product:</span>
                            <span class="info-value product-value-with-logo">
                                ${productThumbHtml}
                                <span>${escapeHtml(item.product_name || 'N/A')}</span>
                            </span>
                        </div>
                        <div class="info-row field-model_no">
                            <span class="info-label"><i class="fas fa-barcode"></i> Model No:</span>
                            <span class="info-value">${escapeHtml(item.model_no || 'N/A')}</span>
                        </div>
                        <div class="info-row field-version">
                            <span class="info-label"><i class="fas fa-code-branch"></i> Version:</span>
                            <span class="info-value">${escapeHtml(item.version || 'N/A')}</span>
                        </div>
                        <div class="info-row field-batch">
                            <span class="info-label"><i class="fas fa-layer-group"></i> Batch:</span>
                            <span class="info-value">${escapeHtml(item.batch || 'N/A')}</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn-action" onclick="toggleDropdown(${item.id}, this)">
                            <i class="fas fa-cog"></i> Actions
                            <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                        </button>
                    </div>
                </div>
            `;
            container.append(card);
        });
        applyFieldVisibility();
    }
    // Open edit modal from card button (gets data directly from the card)
    window.openEditModalFromCard = function(id, buttonElement) {
        // First try to get data from allData (most up-to-date)
        let item = allData.find(i => i.id == id);
        // If not found in allData, try to get from the card DOM
        if (!item) {
            const $card = $(buttonElement).closest('.model-card');
            if ($card.length) {
                item = {
                    id: id,
                    model_name: $card.find('.card-title').text().trim(),
                    brand_name: $card.find('.card-subtitle').text().replace(/[^\w\s-]/g, '').trim(),
                    product_name: $card.find('.field-product .info-value').text().trim(),
                    model_no: $card.find('.field-model_no .info-value').text().trim(),
                    version: $card.find('.field-version .info-value').text().trim(),
                    batch: $card.find('.field-batch .info-value').text().trim(),
                    model_logo_url: $card.find('.card-icon img').attr('src') || ''
                };
            }
        }
        if (!item) {
            showNotification('error', 'Model not found');
            return;
        }
        openEditModal(item);
    };
    let editLogoObjectUrl = '';
    let editExistingLogoUrl = '';
    function editLogoInitials() {
        const text = ($('#edit-model-name').val() || '').trim();
        const tokens = text.split(/[\s_-]+/).filter(Boolean);
        return (tokens.length >= 2 ? tokens[0][0] + tokens[1][0] : text.substring(0,2) || 'M').toUpperCase();
    }
    function refreshEditModelLogoPreview() {
        const mode = $('#edit-model-form input[name="model_logo_mode"]:checked').val() || 'keep';
        const $image = $('#edit-logo-image');
        const $initials = $('#edit-logo-initials');
        const $feedback = $('#edit-logo-feedback');
        $('#edit-logo-upload-fields').toggle(mode === 'upload');
        const fileEl = document.getElementById('edit-model-logo-file');
        let src = '';
        let message = '';
        if (mode === 'keep') {
            src = editExistingLogoUrl;
            message = src ? 'Current saved model logo.' : 'No logo saved yet; choose Create or Upload.';
        } else if (mode === 'upload') {
            const f = fileEl && fileEl.files ? fileEl.files[0] : null;
            if (f && ['image/jpeg','image/png','image/webp'].includes(f.type) && f.size <= 2*1024*1024) {
                if (editLogoObjectUrl) URL.revokeObjectURL(editLogoObjectUrl);
                editLogoObjectUrl = URL.createObjectURL(f);
                src = editLogoObjectUrl;
                message = f.name + ' — ready to upload.';
            } else {
                message = f ? 'Choose a valid PNG, JPG or WEBP image under 2 MB.' : 'Choose the image to upload.';
            }
        } else {
            message = 'The logo will be generated from the Model Name when you save.';
        }
        $initials.text(editLogoInitials()).toggle(!src);
        if (src) {
            $image.attr('src', src).show();
        } else {
            $image.hide().removeAttr('src');
        }
        $feedback.text(message);
    }
    $('#edit-model-form').on('change', 'input[name="model_logo_mode"], #edit-model-logo-file', refreshEditModelLogoPreview);
    $('#edit-logo-image').on('error', function () {
        $(this).hide().removeAttr('src');
        $('#edit-logo-initials').text(editLogoInitials()).show();
        $('#edit-logo-feedback').text('Logo file could not be displayed; choose another image.');
    });
    $('#edit-model-name').on('input', refreshEditModelLogoPreview);

    // Open edit modal with data

    window.openEditModal = function(item) {
        currentEditId = item.id;
        $('#edit-model-id').val(item.id);
        $('#edit-model-name').val(item.model_name || '');
        $('#edit-brand-name').val(item.brand_name || '');
        $('#edit-product-name').val(item.product_name || '');
        $('#edit-model-no').val(item.model_no || '');
        $('#edit-version').val(item.version || '');
        $('#edit-batch').val(item.batch || '');

        if (editLogoObjectUrl) URL.revokeObjectURL(editLogoObjectUrl);
        editLogoObjectUrl = '';
        editExistingLogoUrl = item.model_logo_url || resolveModelLogoUrl(item.model_logo || '');
        $('#edit-model-logo-file').val('');
        $('#edit-model-form input[name="model_logo_mode"][value="keep"]').prop('checked', !!editExistingLogoUrl);
        $('#edit-model-form input[name="model_logo_mode"][value="generate"]').prop('checked', !editExistingLogoUrl);
        $('#edit-logo-keep-label').toggle(!!editExistingLogoUrl);
        refreshEditModelLogoPreview();

        // Lock the page at its current position. Only the modal can scroll.
        modalPageScrollY = window.scrollY || window.pageYOffset || 0;
        $('body')
            .addClass('edit-modal-open')
            .css({
                position: 'fixed',
                top: '-' + modalPageScrollY + 'px',
                left: '0',
                right: '0'
            });
        $('#edit-model-modal').addClass('active');
        $('#edit-model-modal .edit-modal').scrollTop(0);
        closeDropdown();
    };
    // Close edit modal
    window.closeEditModal = function() {
        $('#edit-model-modal').removeClass('active');
        $('body')
            .removeClass('edit-modal-open')
            .css({ position: '', top: '', left: '', right: '' });
        window.scrollTo(0, modalPageScrollY);
        currentEditId = null;
    };
    // Keep mouse-wheel scrolling inside the edit modal. At the top/bottom,
    // prevent scroll chaining to the page behind the overlay.
    const editModalScroller = document.querySelector('#edit-model-modal .edit-modal');
    if (editModalScroller) {
        editModalScroller.addEventListener('wheel', function(e) {
            e.stopPropagation();
            const atTop = this.scrollTop <= 0;
            const atBottom = Math.ceil(this.scrollTop + this.clientHeight) >= this.scrollHeight;
            if ((e.deltaY < 0 && atTop) || (e.deltaY > 0 && atBottom)) {
                e.preventDefault();
            }
        }, { passive: false });
    }
    // Block touch scrolling on the overlay itself; touch movement inside the
    // modal remains enabled by touch-action: pan-y.
    document.getElementById('edit-model-modal').addEventListener('touchmove', function(e) {
        if (!e.target.closest('.edit-modal')) {
            e.preventDefault();
        }
    }, { passive: false });
    // Save the edited card to the existing backend save route.
    $('#edit-model-form').on('submit', function(e) {
        e.preventDefault();
        if (currentEditId === null) {
            showNotification('error', 'No model selected');
            return;
        }
        const original = allData.find(item => item.id == currentEditId) || {};
        const updatedData = {
            id: currentEditId,
            // These names must match Items::savevendorproduct().
            brand: $('#edit-brand-name').val().trim(),
            category: $('#edit-product-name').val().trim(),
            modelname: $('#edit-model-name').val().trim(),
            modelno: $('#edit-model-no').val().trim(),
            version: $('#edit-version').val().trim(),
            batch: $('#edit-batch').val().trim(),
            description: original.description || ''
        };
        // Validate required fields
        if (!updatedData.modelname) {
            showNotification('error', 'Model name is required');
            return;
        }
        if (!updatedData.modelno) {
            showNotification('error', 'Model number is required');
            return;
        }
        const mode = $('#edit-model-form input[name="model_logo_mode"]:checked').val() || 'keep';
        const imageFile = document.getElementById('edit-model-logo-file');
        const f = imageFile && imageFile.files ? imageFile.files[0] : null;
        if (mode === 'upload' && (!f || !['image/png','image/jpeg','image/webp'].includes(f.type) || f.size < 1 || f.size > 2*1024*1024)) {
            showNotification('error', 'Choose a PNG, JPG or WEBP model logo (max 2 MB).');
            return;
        }

        const $saveButton = $('#edit-model-form .btn-save-edit');
        const originalButtonHtml = $saveButton.html();
        $saveButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        $.ajax({
            url: '<?php echo get_uri("items/savevendorproduct") ?>',
            type: 'POST',
            dataType: 'json',
            data: (function () {
                const payload = new FormData(document.getElementById('edit-model-form'));
                Object.keys(updatedData).forEach(key => payload.set(key, updatedData[key]));
                const choice = $('#edit-model-form input[name="model_logo_mode"]:checked').val() || 'keep';
                payload.set('model_logo_mode', choice);
                return payload;
            })(),
            processData: false,
            contentType: false,
            success: function(response) {
                if (response && response.success) {
                    closeEditModal();
                    showNotification('success', response.message || 'Model updated successfully');
                    // Read the saved database row again; do not trust temporary
                    // form values as the source of truth.
                    loadData();
                } else {
                    showNotification('error', (response && response.message) || 'Unable to update model');
                }
            },
            error: function(xhr) {
                let message = 'Unable to update model';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showNotification('error', message);
            },
            complete: function() {
                $saveButton.prop('disabled', false).html(originalButtonHtml);
            }
        });
    });
    // Click outside modal to close
    $(document).on('click', '#edit-model-modal', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
    // ESC key to close modal
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
            closeDropdown();
        }
    });
    // Toggle dropdown menu - REMOVED "Edit Model" option
    window.toggleDropdown = function(id, buttonElement) {
        if (activeDropdownId === id) {
            closeDropdown();
            return;
        }
        closeDropdown();
        const item = allData.find(i => i.id == id);
        if (!item || !item.actions || item.actions.length === 0) {
            console.error('No actions found');
            return;
        }
        const $button = $(buttonElement);
        const $card = $button.closest('.model-card');
        let actionsHtml = '<div class="floating-dropdown" id="dropdown-' + id + '">';
        item.actions.forEach((action, index) => {
            const icon = action.icon || 'fa-circle';
            const label = escapeHtml(action.label);
            const url = action.url || '#';
            const safeUrl = escapeAttr(url);
            if (action.class === 'delete-action') {
                actionsHtml += `<a href="#" class="dropdown-item" onclick="deleteModel(${id}); return false;">
                    <i class="fas ${icon}"></i> ${label}
                </a>`;
            } 
            else if (action.class === 'modal-action') {
                actionsHtml += `<a href="${safeUrl}" class="dropdown-item" data-act="ajax-modal" data-action-url="${safeUrl}" data-title="${escapeAttr(action.title || action.label)}">
                    <i class="fas ${icon}"></i> ${label}
                </a>`;
            } 
            else if (action.class === 'external') {
                actionsHtml += `<a href="${safeUrl}" target="_blank" class="dropdown-item">
                    <i class="fas ${icon}"></i> ${label}
                </a>`;
            } 
            else if (action.class === 'confirm-action') {
                actionsHtml += `<a href="${safeUrl}" class="dropdown-item confirm-menu-action" data-confirm-message="Do you want to create the table?">
                    <i class="fas ${icon}"></i> ${label}
                </a>`;
            } 
            else {
                actionsHtml += `<a href="${safeUrl}" class="dropdown-item">
                    <i class="fas ${icon}"></i> ${label}
                </a>`;
            }
        });
        actionsHtml += '</div>';
        $('body').append(actionsHtml);
        const $menu = $('#dropdown-' + id);
        positionDropdown($menu, $button);
        setTimeout(() => {
            $menu.addClass('show');
        }, 10);
        $card.addClass('active');
        activeDropdownId = id;
        // Handle scroll inside dropdown
        $menu.on('wheel', function(e) {
            const menu = this;
            const delta = e.originalEvent.deltaY;
            if (menu.scrollHeight > menu.clientHeight) {
                const atTop = menu.scrollTop === 0;
                const atBottom = menu.scrollTop + menu.clientHeight >= menu.scrollHeight - 1;
                if ((delta < 0 && atTop) || (delta > 0 && atBottom)) {
                    return;
                }
                menu.scrollTop += delta;
                e.preventDefault();
                e.stopPropagation();
            }
        });
    };
    // Position dropdown
    function positionDropdown($menu, $button) {
        const rect = $button[0].getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        const menuHeight = $menu.outerHeight();
        const menuWidth = Math.min(260, viewportWidth - 24);
        let top = rect.bottom + 8;
        let left = rect.left;
        if (top + menuHeight > viewportHeight - 10) {
            top = rect.top - menuHeight - 8;
        }
        if (left + menuWidth > viewportWidth - 10) {
            left = viewportWidth - menuWidth - 10;
        }
        left = Math.max(10, left);
        $menu.css({
            top: top + 'px',
            left: left + 'px',
            width: menuWidth + 'px'
        });
    }
    // Close dropdown
    window.closeDropdown = function() {
        if (activeDropdownId !== null) {
            $('#dropdown-' + activeDropdownId).removeClass('show');
            setTimeout(() => {
                $('#dropdown-' + activeDropdownId).remove();
            }, 150);
            $('.model-card.active').removeClass('active');
            activeDropdownId = null;
        }
    };
    function isPointerInsideElement(element) {
        if (!element) return false;
        const rect = element.getBoundingClientRect();
        return pointerX >= rect.left && pointerX <= rect.right && pointerY >= rect.top && pointerY <= rect.bottom;
    }
    function closeDropdownIfPointerOutside() {
        if (activeDropdownId === null) return;
        const menu = document.getElementById('dropdown-' + activeDropdownId);
        const activeButton = document.querySelector('.model-card.active .btn-action');
        if (isPointerInsideElement(menu) || isPointerInsideElement(activeButton)) {
            return;
        }
        closeDropdown();
    }
    window.deleteModel = function(id) {
        const item = allData.find(i => i.id == id);
        const modelName = item ? (item.model_name || item.model_no || 'this model') : 'this model';
        if (!confirm('Are you sure you want to delete ' + modelName + '?')) {
            return false;
        }
        closeDropdown();
        showProcessing(true);
        $.ajax({
            url: '<?php echo get_uri("items/deletevendorproduct") ?>',
            type: 'POST',
            dataType: 'json',
            data: {id: id},
            success: function(response) {
                if (response && response.success) {
                    allData = allData.filter(item => item.id != id);
                    populateCategoryFilter(allData);
                    applyFiltersAndRender();
                    showNotification('success', response.message || 'Model deleted successfully');
                } else {
                    showNotification('error', (response && response.message) || 'Unable to delete model');
                }
            },
            error: function() {
                showNotification('error', 'Unable to delete model');
            },
            complete: function() {
                showProcessing(false);
            }
        });
        return false;
    };
    // Apply filters
    function applyFiltersAndRender() {
        filteredData = allData.filter(item => {
            return matchesSearch(item) && matchesCategory(item);
        });
        const visibleData = pageSize === -1 ? filteredData : filteredData.slice(0, pageSize);
        renderCards(visibleData);
    }
    function matchesSearch(item) {
        if (!activeSearchQuery) return true;
        const searchable = [
            item.model_name, item.brand_name, item.product_name,
            item.model_no, item.version, item.batch
        ].join(' ').toLowerCase();
        return searchable.indexOf(activeSearchQuery) !== -1;
    }
    function matchesCategory(item) {
        if (!activeCategoryValue) return true;
        const filterValue = activeCategoryValue.toLowerCase();
        return (item.category_name || '').toLowerCase().indexOf(filterValue) !== -1 ||
               (item.product_name || '').toLowerCase().indexOf(filterValue) !== -1;
    }
    // Populate category filter
    function populateCategoryFilter(data) {
        const select = $('#category-filter');
        const categories = new Set();
        data.forEach(item => {
            if (item.category_name) categories.add(item.category_name);
            if (item.product_name) categories.add(item.product_name);
        });
        select.empty().append('<option value="">- Category -</option>');
        Array.from(categories).sort().forEach(cat => {
            select.append(`<option value="${escapeHtml(cat)}">${escapeHtml(cat)}</option>`);
        });
    }
    // Field visibility
    function applyFieldVisibility() {
        Object.keys(visibleFields).forEach(field => {
            $('.field-' + field).toggleClass('field-hidden', !visibleFields[field]);
        });
    }
    // Export to Excel
    function exportFilteredData() {
        const data = filteredData.length ? filteredData : allData;
        if (!data.length) {
            showNotification('error', 'No data to export');
            return;
        }
        const headers = ['Model Name', 'Brand', 'Product', 'Model No', 'Version', 'Batch'];
        const rows = data.map(item => [
            item.model_name || '', item.brand_name || '', item.product_name || '',
            item.model_no || '', item.version || '', item.batch || ''
        ]);
        const csv = [headers, ...rows].map(row => 
            row.map(v => '"' + String(v).replace(/"/g, '""') + '"').join(',')
        ).join('\n');
        const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'efactor-models.csv';
        link.click();
        URL.revokeObjectURL(link.href);
    }
    // Print cards
    function printFilteredCards() {
        if (!filteredData.length) {
            showNotification('error', 'No cards to print');
            return;
        }
        const printWindow = window.open('', '_blank');
        const cardsHtml = $('#cards-container').clone();
        cardsHtml.find('.floating-dropdown').remove();
        printWindow.document.write(`
            <html><head><title>Efactor Models</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .cards-container { display: grid; grid-template-columns: repeat(3,1fr); gap: 15px; }
                .model-card { border: 1px solid #ddd; border-radius: 10px; padding: 15px; }
                .card-title { font-size: 16px; font-weight: bold; margin: 0 0 5px; }
                .info-row { display: flex; justify-content: space-between; padding: 5px 0; }
                @media print { body { margin: 10mm; } }
            </style>
            </head><body>
            <h1>Efactor Model Management</h1>
            ${cardsHtml.html()}
            </body></html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
    // Show/hide functions
    function showEmptyState() {
        $('#cards-container').hide();
        $('#empty-state').show();
    }
    function hideEmptyState() {
        $('#empty-state').hide();
        $('#cards-container').show();
    }
    function showProcessing(show) {
        $('#item-table_processing').css('display', show ? 'flex' : 'none');
    }
    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        const notification = $(`
            <div class="alert ${alertClass}" style="position: fixed; top: 20px; right: 20px; z-index: 999999; animation: slideIn 0.3s ease;">
                <i class="fas ${icon}"></i> ${message}
            </div>
        `);
        $('body').append(notification);
        setTimeout(() => notification.fadeOut(300, () => notification.remove()), 3000);
    }
    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
    function escapeAttr(str) {
        return escapeHtml(str).replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }
    // Event listeners
    $(document).on('mousemove', function(e) {
        pointerX = e.clientX;
        pointerY = e.clientY;
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.btn-action').length && !$(e.target).closest('.floating-dropdown').length) {
            closeDropdown();
        }
        if (!$(e.target).closest('.column-tools-container').length) {
            $('#field-toggle-panel').removeClass('show');
        }
    });
    $(document).on('click', '.confirm-menu-action', function(e) {
        e.preventDefault();
        const message = $(this).data('confirm-message') || 'Are you sure?';
        const url = $(this).attr('href');
        closeDropdown();
        if (confirm(message)) {
            window.location.href = url;
        }
    });
    $(document).on('click', '[data-act="ajax-modal"]', function() {
        closeDropdown();
    });
    // Close action dropdown on scroll only when cursor is outside the menu/button
    $(window).on('scroll wheel', function() {
        closeDropdownIfPointerOutside();
    });
    document.addEventListener('scroll', closeDropdownIfPointerOutside, {
        capture: true,
        passive: true
    });
    $(window).on('resize', function() {
        closeDropdown();
    });
    $('#search-input').on('input', function() {
        activeSearchQuery = $(this).val().toLowerCase().trim();
        applyFiltersAndRender();
        saveVendorViewState();
    });
    $('#category-filter').on('change', function() {
        activeCategoryValue = $(this).val();
        applyFiltersAndRender();
        saveVendorViewState();
    });
    $('#page-size-select').on('change', function() {
        pageSize = parseInt($(this).val(), 10);
        closeDropdown();
        applyFiltersAndRender();
        saveVendorViewState();
    });
    $('#export-excel-btn').on('click', function() {
        exportFilteredData();
    });
    $('#print-cards-btn').on('click', function() {
        printFilteredCards();
    });
    $('#field-toggle-button').on('click', function(e) {
        e.stopPropagation();
        $('#field-toggle-panel').toggleClass('show');
        closeDropdown();
    });
    $('.field-toggle').on('change', function() {
        const field = $(this).data('field');
        visibleFields[field] = $(this).is(':checked');
        applyFieldVisibility();
        saveVendorViewState();
    });
    // Save immediately before an Action link navigates to another page.
    $(document).on('mousedown click', '.floating-dropdown a, .btn-action, [data-act="ajax-modal"]', function() {
        saveVendorViewState();
    });
    $(document).on('click', '.modal-action', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        closeDropdown();
        if (typeof window.showModal === 'function') {
            window.showModal(url);
        } else {
            window.location.href = url;
        }
    });
    // Restore the search/filter controls first, then fetch and render cards.
    restoreVendorViewState();
    loadData();
    // pagehide also covers browser Back/Forward cache and mobile navigation.
    window.addEventListener('pagehide', saveVendorViewState);
    // Browsers may restore this page from the back/forward cache after an edit.
    // Reload from the database instead of showing the old in-memory card data.
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            restoreVendorViewState();
            loadData();
        }
    });
});
</script>
