<?php
// Build models array from pageNames and nodeIds - FIXED
$models = [];
if (!empty($pageNames)) {
    foreach ($pageNames as $page) {
        if (isset($page['model_name']) && !empty($page['model_name']) && !in_array($page['model_name'], $models)) {
            $models[] = $page['model_name'];
        }
    }
}

if (!empty($nodeIds)) {
    foreach ($nodeIds as $node_id) {
        if (isset($node_id['model_number']) && !empty($node_id['model_number']) && !in_array($node_id['model_number'], $models)) {
            $models[] = $node_id['model_number'];
        }
    }
}

// Also ensure we have models from the selectedmodelNumber if it exists
if (!empty($selectedmodelNumber)) {
    if (is_array($selectedmodelNumber)) {
        foreach ($selectedmodelNumber as $model) {
            if (!empty($model) && !in_array($model, $models)) {
                $models[] = $model;
            }
        }
    } else if (!empty($selectedmodelNumber) && !in_array($selectedmodelNumber, $models)) {
        $models[] = $selectedmodelNumber;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EFACTOR | Professional Configuration Suite</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Choices.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <style>
:root {
    --primary: #C0E1D2;
    --primary-dark: #7FAE99;
    --primary-light: #E5EEE4;
    --primary-gradient: linear-gradient(135deg, #C0E1D2 0%, #E5EEE4 58%, #DC9B9B 100%);
    --secondary: #DC9B9B;
    --secondary-light: #F6F4E8;
    --accent: #DC9B9B;
    --success: #7FAE99;
    --danger: #C96F6F;
    --warning: #C79A5D;
    --dark: #34423B;
    --gray: #718078;
    --light-gray: #F6F4E8;
    --border: #D7E2D9;
    --card-shadow: 0 12px 34px rgba(76, 102, 88, 0.08);
    --shadow-lg: 0 20px 48px rgba(76, 102, 88, 0.14);
    --command-bg: #FBFCF8;
    --command-border: #DCE7DE;
    --card-bg: #FFFFFF;
    --header-bg: #5F776B;
    --body-bg: #F6F4E8;
    --surface-2: #FAFBF6;
    --surface-3: #E5EEE4;
    --focus: rgba(192, 225, 210, 0.34);
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
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    min-height: 100vh;
    padding: clamp(14px, 2.4vw, 30px);
    color: var(--dark);
    background:
        radial-gradient(circle at 8% 0%, rgba(220, 155, 155, 0.16), transparent 28rem),
        radial-gradient(circle at 92% 8%, rgba(192, 225, 210, 0.26), transparent 24rem),
        linear-gradient(180deg, #F6F4E8 0%, #E5EEE4 100%);
}

.config-wrapper {
    width: min(100%, 1380px);
    margin: 0 auto;
}

.config-header {
    position: relative;
    overflow: hidden;
    margin-bottom: 1.35rem;
    padding: 1.35rem 1.5rem;
    color: #fff;
    background:
        radial-gradient(circle at 12% 30%, rgba(220, 155, 155, 0.24), transparent 12rem),
        linear-gradient(135deg, #6F8F80 0%, #567265 58%, #456056 100%);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 22px;
    box-shadow: 0 18px 44px rgba(30, 39, 33, 0.20);
}

.config-header::after {
    content: "";
    position: absolute;
    right: -70px;
    top: -95px;
    width: 250px;
    height: 250px;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 50%;
    box-shadow:
        0 0 0 42px rgba(255,255,255,0.025),
        0 0 0 84px rgba(255,255,255,0.015);
    pointer-events: none;
}

.header-flex {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.header-title-wrap {
    display: flex;
    align-items: center;
    gap: 0.9rem;
}

.header-icon-shell {
    display: grid;
    place-items: center;
    flex: 0 0 52px;
    width: 52px;
    height: 52px;
    border-radius: 16px;
    color: #FFF8F1;
    background: linear-gradient(145deg, rgba(180,138,97,0.34), rgba(95,122,104,0.32));
    border: 1px solid rgba(255,255,255,0.12);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.12);
}

.config-header h1 {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    margin: 0;
    color: #fff;
    font-size: clamp(1.45rem, 2.5vw, 1.9rem);
    font-weight: 760;
    letter-spacing: -0.035em;
}

.config-header h1 i {
    color: #F3C0C0;
}

.config-header p {
    margin-top: 0.35rem;
    color: #E5EEE4;
    font-size: 0.86rem;
}

.header-status {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    margin-top: 0.55rem;
    padding: 0.35rem 0.65rem;
    border: 1px solid rgba(157, 193, 164, 0.28);
    border-radius: 999px;
    color: #F6F4E8;
    background: rgba(93, 126, 99, 0.20);
    font-size: 0.72rem;
    font-weight: 700;
}

.header-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #C0E1D2;
    box-shadow: 0 0 0 4px rgba(192,225,210,0.18);
    animation: statusPulse 2s ease-in-out infinite;
}

.btn-back {
    position: relative;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-height: 42px;
    padding: 0.65rem 1rem;
    color: #F5F7F4;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 11px;
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 650;
    transition: 180ms ease;
    backdrop-filter: blur(6px);
}

.btn-back:hover {
    color: #fff;
    background: rgba(255,255,255,0.12);
    border-color: rgba(214,176,138,0.55);
    transform: translateY(-1px);
}

.dashboard-overview {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.8rem;
    margin-bottom: 1.35rem;
}

.overview-card {
    display: grid;
    grid-template-columns: 42px minmax(0, 1fr);
    gap: 0.75rem;
    align-items: center;
    min-height: 92px;
    padding: 0.95rem 1rem;
    border: 1px solid var(--border);
    border-radius: 16px;
    background: rgba(255,255,255,0.94);
    box-shadow: 0 8px 22px rgba(43,56,46,0.055);
    animation: fadeSlideIn 0.42s both;
}

.overview-card:nth-child(2) { animation-delay: 0.05s; }
.overview-card:nth-child(3) { animation-delay: 0.10s; }
.overview-card:nth-child(4) { animation-delay: 0.15s; }

.overview-icon {
    display: grid;
    place-items: center;
    width: 42px;
    height: 42px;
    border-radius: 12px;
    color: var(--primary-dark);
    background: var(--primary-light);
    border: 1px solid #CFE0D6;
}

.overview-label {
    display: block;
    color: var(--gray);
    font-size: 0.66rem;
    font-weight: 760;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.overview-value {
    display: block;
    margin-top: 0.15rem;
    overflow: hidden;
    color: var(--dark);
    font-size: 0.92rem;
    font-weight: 760;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.card,
.console-section {
    background: var(--card-bg);
    border-radius: 18px;
    box-shadow: var(--card-shadow);
    border: 1px solid var(--border);
    overflow: hidden;
    transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
}

.card:hover,
.console-section:hover {
    border-color: #B7D2C5;
    box-shadow: var(--shadow-lg);
}

.console-section {
    margin-bottom: 1.35rem;
    animation: fadeSlideIn 0.42s both;
}

.console-section-head,
.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.2rem;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(180deg, #FBFCFA 0%, #F4F7F3 100%);
}

.console-section-title,
.card-header h3 {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    color: var(--dark);
    font-size: 0.98rem;
    font-weight: 760;
}

.console-section-title i,
.card-header h3 i {
    display: grid;
    place-items: center;
    width: 30px;
    height: 30px;
    border-radius: 9px;
    color: var(--primary-dark);
    background: var(--primary-light);
}

.console-section-subtitle {
    margin-top: 0.25rem;
    color: var(--gray);
    font-size: 0.76rem;
}

.card-body,
.console-section-body {
    padding: 1.2rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-group {
    margin-bottom: 0;
}

.field-card {
    padding: 0.9rem;
    border: 1px solid var(--border);
    border-radius: 14px;
    background: #FCFDFC;
    transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
}

.field-card:focus-within {
    border-color: #A8CBBB;
    box-shadow: 0 0 0 4px var(--focus);
    transform: translateY(-1px);
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.38rem;
    margin-bottom: 0.48rem;
    color: #4D5C50;
    font-size: 0.69rem;
    font-weight: 780;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.form-label i {
    color: var(--secondary);
    font-size: 0.78rem;
}

.required::after {
    content: '*';
    color: var(--danger);
    margin-left: 0.2rem;
}

select,
input[type="text"],
input[type="number"] {
    width: 100%;
    min-height: 46px;
    padding: 0.68rem 0.8rem;
    color: var(--dark);
    background: #fff;
    border: 1px solid #C6D8CF;
    border-radius: 11px;
    font: 600 0.86rem 'Inter', sans-serif;
    transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
}

select:hover,
input:hover {
    border-color: #A8CBBB;
}

select:focus,
input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px var(--focus);
}

.two-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.1rem;
    margin: 1.35rem 0;
}

.param-list {
    max-height: 400px;
    overflow-y: auto;
}

.param-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 0.5rem;
    padding: 0.75rem 0.8rem;
    color: var(--dark);
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 11px;
    font-size: 0.8rem;
    font-weight: 620;
    transition: 180ms ease;
}

.param-item:hover {
    border-color: #B8D4C6;
    background: #E5EEE4;
    transform: translateX(2px);
}

.param-item i {
    color: var(--primary);
}

.config-items-container {
    max-height: 560px;
    overflow-y: auto;
    padding-right: 3px;
}

.config-item {
    display: grid;
    grid-template-columns: minmax(120px, 0.85fr) minmax(180px, 1.4fr);
    gap: 0.8rem;
    align-items: center;
    margin-bottom: 0.65rem;
    padding: 0.8rem;
    background: #FBFCFA;
    border: 1px solid var(--border);
    border-radius: 12px;
    transition: 180ms ease;
}

.config-item:hover {
    border-color: #B8D4C6;
    background: #E5EEE4;
    box-shadow: 0 7px 16px rgba(43,56,46,0.06);
}

.config-item label {
    margin: 0;
    color: #39453C;
    font-size: 0.8rem;
    font-weight: 680;
}

.config-item input {
    margin: 0;
}

.commands-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.8rem;
    margin-bottom: 0.75rem;
    padding: 0 0 0.75rem;
    border-bottom: 1px solid var(--border);
}

.commands-count {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.55rem;
    color: #60736A;
    background: #E5EEE4;
    border: 1px solid #C9DCCF;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 740;
}

.command-item {
    position: relative;
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 1rem;
    align-items: center;
    margin-bottom: 0.75rem;
    padding: 0.95rem 1rem;
    background: linear-gradient(180deg, #FFFFFF 0%, #FAFBF9 100%);
    border: 1px solid var(--command-border);
    border-radius: 14px;
    transition: transform 180ms ease, border-color 180ms ease, box-shadow 180ms ease;
}

.command-item::before {
    content: "";
    position: absolute;
    left: 0;
    top: 12px;
    bottom: 12px;
    width: 3px;
    border-radius: 0 999px 999px 0;
    background: linear-gradient(180deg, var(--primary), var(--secondary));
    opacity: 0.55;
}

.command-item:hover {
    transform: translateY(-2px);
    border-color: #B5D0C3;
    box-shadow: 0 10px 22px rgba(43,56,46,0.09);
}

.command-info {
    min-width: 0;
}

.command-name {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    flex-wrap: wrap;
    margin-bottom: 0.55rem;
    color: var(--dark);
    font-size: 0.86rem;
    font-weight: 710;
}

.command-name i {
    display: grid;
    place-items: center;
    width: 26px;
    height: 26px;
    border-radius: 8px;
    color: #8E6666;
    background: #F6E3E3;
}

.command-number {
    display: inline-grid;
    place-items: center;
    min-width: 34px;
    height: 26px;
    padding: 0 0.42rem;
    color: #fff;
    background: #435449;
    border-radius: 8px;
    font-size: 0.66rem;
    font-weight: 800;
    letter-spacing: 0.03em;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.5rem;
    border-radius: 999px;
    font-size: 0.6rem;
    font-weight: 760;
    letter-spacing: 0.05em;
}

.badge-config {
    color: #8C5F5F;
    background: #F6E3E3;
    border: 1px solid #E5BEBE;
}

.command-hex {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    max-width: 100%;
    padding: 0.48rem 0.65rem;
    overflow-wrap: anywhere;
    color: #263129;
    background: #F1F4F0;
    border: 1px solid #D9E1D8;
    border-radius: 8px;
    font: 700 0.78rem 'Courier New', monospace;
    letter-spacing: 0.035em;
}

.command-hex i {
    color: var(--primary);
}

.command-actions {
    display: flex;
    align-items: center;
}

.btn-copy-command {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.38rem;
    min-width: 82px;
    min-height: 38px;
    padding: 0.5rem 0.75rem;
    color: #fff;
    background: #7FAE99;
    border: 1px solid #7FAE99;
    border-radius: 9px;
    font-size: 0.75rem;
    font-weight: 680;
    cursor: pointer;
    transition: 180ms ease;
}

.btn-copy-command:hover {
    transform: translateY(-1px);
    background: #688E7C;
    border-color: #688E7C;
    box-shadow: 0 7px 14px rgba(127,174,153,0.18);
}

.btn-copy-command:active {
    transform: translateY(0);
}

.node-select-wrapper {
    margin-top: 0.2rem;
}

.node-panel {
    padding: 0.95rem;
    background: #FBFCFA;
    border: 1px solid var(--border);
    border-radius: 14px;
}

.node-help {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    margin-top: 0.5rem;
    color: var(--gray);
    font-size: 0.68rem;
    font-weight: 560;
}

.choices {
    margin-bottom: 0 !important;
}

.choices__inner {
    min-height: 48px;
    padding: 0.4rem 0.55rem;
    background: #fff;
    border: 1px solid #C6D8CF;
    border-radius: 11px;
    font-size: 0.84rem;
}

.choices.is-focused .choices__inner,
.choices.is-open .choices__inner {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px var(--focus);
}

.choices__list--multiple .choices__item {
    margin: 3px 4px 3px 0;
    padding: 0.34rem 0.55rem;
    color: #fff;
    background: #89B9A4;
    border: 1px solid #89B9A4;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 650;
}

.choices__list--dropdown,
.choices__list[aria-expanded] {
    border-color: #C6D8CF;
    border-radius: 11px;
    box-shadow: 0 12px 28px rgba(43,56,46,0.12);
}

.choices__item--choice.is-highlighted {
    background: #E5EEE4;
}

.pairing-section {
    margin-top: 0.4rem;
    padding: 1rem;
    background: linear-gradient(135deg, #F6F4E8 0%, #F3E7E7 100%);
    border: 1px solid var(--border);
    border-radius: 14px;
}

.pairing-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    color: var(--dark);
    font-size: 0.82rem;
    font-weight: 700;
}

.pairing-title i {
    color: var(--secondary);
}

.qr-container {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 0.8rem;
    flex-wrap: wrap;
}

#qrImage {
    width: 100px;
    height: 100px;
    display: none;
    padding: 0.45rem;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: var(--card-shadow);
}

#inputBox {
    display: none;
    flex: 1;
    min-width: 240px;
}

.btn-submit {
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 52px;
    margin-top: 1.35rem;
    padding: 0.8rem 1rem;
    color: #fff;
    background:
        linear-gradient(100deg, #6E8D7E 0%, #89B9A4 48%, #DC9B9B 100%);
    border: 1px solid rgba(48,66,55,0.55);
    border-radius: 13px;
    box-shadow: 0 12px 24px rgba(111,143,128,0.20);
    font-size: 0.9rem;
    font-weight: 760;
    letter-spacing: 0.01em;
    cursor: pointer;
    transition: transform 180ms ease, box-shadow 180ms ease, filter 180ms ease;
}

.btn-submit::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(110deg, transparent 30%, rgba(255,255,255,0.13) 50%, transparent 70%);
    transform: translateX(-130%);
    transition: transform 500ms ease;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 30px rgba(111,143,128,0.25);
    filter: saturate(1.05);
}

.btn-submit:hover::after {
    transform: translateX(130%);
}

.btn-submit:active {
    transform: translateY(0);
}

.empty-state {
    padding: 1.6rem;
    color: var(--gray);
    text-align: center;
    font-size: 0.8rem;
}

.empty-state i {
    display: block;
    margin-bottom: 0.45rem;
    color: #A8C7B8;
    font-size: 1.5rem;
    opacity: 0.8;
}

.hidden {
    display: none !important;
}

.loading-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(30, 39, 33, 0.68);
    backdrop-filter: blur(5px);
}

.loading-overlay.active {
    display: flex;
}

.loading-content {
    width: min(360px, 100%);
    padding: 1.6rem;
    color: var(--dark);
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 18px;
    text-align: center;
    box-shadow: 0 24px 60px rgba(30,39,33,0.28);
}

.spinner {
    width: 42px;
    height: 42px;
    margin: 0 auto 0.85rem;
    border: 4px solid #DDE8E0;
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 0.85s linear infinite;
}

::-webkit-scrollbar {
    width: 7px;
    height: 7px;
}

::-webkit-scrollbar-track {
    background: #EEF1EC;
    border-radius: 999px;
}

::-webkit-scrollbar-thumb {
    background: #A8C7B8;
    border-radius: 999px;
}

::-webkit-scrollbar-thumb:hover {
    background: #88A998;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes statusPulse {
    0%,100% { box-shadow: 0 0 0 4px rgba(127,169,137,0.10); }
    50% { box-shadow: 0 0 0 7px rgba(127,169,137,0.03); }
}

@media (max-width: 980px) {
    .dashboard-overview {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .form-grid,
    .two-columns {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px) {
    body {
        padding: 10px;
    }

    .config-header {
        padding: 1rem;
        border-radius: 17px;
    }

    .header-flex {
        align-items: stretch;
        flex-direction: column;
    }

    .btn-back {
        width: 100%;
    }

    .dashboard-overview {
        grid-template-columns: 1fr;
    }

    .console-section-head,
    .card-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .card-body,
    .console-section-body {
        padding: 1rem;
    }

    .field-card,
    .node-panel {
        padding: 0.8rem;
    }

    .config-item {
        grid-template-columns: 1fr;
    }

    .command-item {
        grid-template-columns: 1fr;
        align-items: stretch;
    }

    .command-actions {
        justify-content: flex-start;
    }

    .btn-copy-command {
        width: 100%;
    }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}
</style>
</head>
<body>
<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="spinner"></div>
        <h3>Sending Configuration</h3>
        <p style="color: var(--gray); margin-top: 0.5rem;">Applying the selected configuration to the target device...</p>
    </div>
</div>

<div class="config-wrapper">
    <!-- Header -->
    <div class="config-header">
        <div class="header-flex">
            <div class="header-title-wrap">
                <div class="header-icon-shell">
                    <i class="fas fa-microchip"></i>
                </div>
                <div>
                    <h1>Configuration Suite</h1>
                    <p>Configure and manage your industrial devices with precision</p>
                    <div class="header-status">
                        <span class="header-status-dot"></span>
                        Configuration Ready
                    </div>
                </div>
            </div>
            <a href="javascript:history.back()" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
        </div>
    </div>

    <div class="dashboard-overview">
        <div class="overview-card">
            <div class="overview-icon"><i class="fas fa-cube"></i></div>
            <div>
                <span class="overview-label">Device Model</span>
                <span class="overview-value" id="overviewModel">Not selected</span>
            </div>
        </div>
        <div class="overview-card">
            <div class="overview-icon"><i class="fas fa-sliders-h"></i></div>
            <div>
                <span class="overview-label">Operation</span>
                <span class="overview-value" id="overviewMode">Not selected</span>
            </div>
        </div>
        <div class="overview-card">
            <div class="overview-icon"><i class="fas fa-terminal"></i></div>
            <div>
                <span class="overview-label">Commands</span>
                <span class="overview-value">8 Available</span>
            </div>
        </div>
        <div class="overview-card">
            <div class="overview-icon"><i class="fas fa-circle-check"></i></div>
            <div>
                <span class="overview-label">Status</span>
                <span class="overview-value">Ready</span>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <form id="configForm" action="https://efactor.tech/v8/product_config.php" method="GET" target="_blank">
        <input type="hidden" name="action" value="Product_config">
        
        <!-- Basic Configuration Card -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3><i class="fas fa-sliders-h"></i> Basic Configuration</h3>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group field-card">
                        <label class="form-label required"><i class="fas fa-cube"></i> Model Number</label>
                        <select id="modelSelect" name="model_number" onchange="onModelChange()">
                            <option value="">Select Model</option>
                            <?php if (!empty($models) && is_array($models)): ?>
                                <?php foreach ($models as $model): ?>
                                    <?php if (!empty($model) && is_string($model)): ?>
                                        <option value="<?php echo htmlspecialchars($model); ?>">
                                            <?php echo htmlspecialchars($model); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">No Models Available</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <?php if($pageNo != "Pairing"): ?>
                    <div class="form-group field-card">
                        <label class="form-label required"><i class="fas fa-chart-line"></i> Operation Mode</label>
                        <select id="ModeSelect" name="mode" onchange="onModeChange()">
                            <option value="None">Select Mode</option>
                            <option value="Auto test">Auto Test</option>
                            <option value="Configuration">Configuration</option>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if($pageNo != "Pairing"): ?>
                <!-- Dynamic content based on mode -->
                <div id="modeContent">
                    <!-- Auto Test: Shows Page Names -->
                    <div id="autoTestContent" class="hidden">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label required"><i class="fas fa-file-alt"></i> Page Name</label>
                                <select id="pageSelect" name="page" onchange="loadParametersAndConfigs()">
                                    <option value="">Select Page</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Configuration: Shows Commands -->
                    <div id="configContent" class="hidden">
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-terminal"></i> Available Commands</label>
                            <div id="commandList" class="config-items-container">
                                <div class="empty-state">
                                    <i class="fas fa-code"></i>
                                    <p>Select a model to view available commands</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Common fields -->
                <div class="form-grid" style="margin-top: 1.5rem;">
                    <div class="form-group field-card">
                        <label class="form-label"><i class="fas fa-network-wired"></i> Router ID</label>
                        <select id="routerSelect" name="router_id">
                            <option value="">Select Router</option>
                            <?php if (!empty($router_Ids) && is_array($router_Ids)): ?>
                                <?php foreach ($router_Ids as $router): ?>
                                    <?php if(isset($router['node_id'])): ?>
                                        <option value="<?php echo htmlspecialchars($router['node_id']); ?>">
                                            <?php echo htmlspecialchars($router['node_id']); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-group field-card">
                        <label class="form-label"><i class="fas fa-signal"></i> Device Status</label>
                        <select id="statusSelect" name="status">
                            <option value="None">Select Status</option>
                            <option value="Online">🟢 Online</option>
                            <option value="Peer to Peer">🔗 Peer to Peer</option>
                        </select>
                    </div>
                </div>

                <!-- Node IDs -->
                <div class="form-group node-select-wrapper">
                    <div class="node-panel">
                        <label class="form-label"><i class="fas fa-id-card"></i> Target Nodes</label>
                        <select id="nodeSelect" name="node_ids[]" multiple>
                            <option value=""></option>
                        </select>
                        <small class="node-help">
                            <i class="fas fa-info-circle"></i> You can select multiple node IDs
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parameters & Configuration Section (Auto Test Only) -->
        <?php if($pageNo != "Pairing"): ?>
        <div id="autoTestPanel" class="two-columns hidden">
            <!-- Parameters Panel -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-tachometer-alt"></i> Parameters</h3>
                </div>
                <div class="card-body">
                    <div id="paramSelect" class="param-list">
                        <div class="empty-state">
                            <i class="fas fa-info-circle"></i>
                            <p>Select model and page to view parameters</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuration Panel -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-sliders-h"></i> Configuration Values</h3>
                </div>
                <div class="card-body">
                    <div id="configureSelect" class="config-items-container">
                        <div class="empty-state">
                            <i class="fas fa-edit"></i>
                            <p>Configuration fields will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pairing Section -->
        <?php if($pageNo == "Pairing"): ?>
        <div class="card" style="margin-top: 1.5rem;">
            <div class="card-header">
                <h3><i class="fas fa-link"></i> Device Pairing</h3>
            </div>
            <div class="card-body">
                <div class="pairing-section">
                    <div class="pairing-title">
                        <i class="fas fa-globe"></i>
                        Remote Address Configuration
                    </div>
                    <select id="remoteaddress" name="remote_method" onchange="handleSelectionChange()" style="width: 100%; max-width: 300px;">
                        <option value="None">Select Method</option>
                        <option value="qr">📱 Scan QR Code</option>
                        <option value="manual">✍️ Enter Manually</option>
                        <option value="deviceApi">🔌 Device API</option>
                    </select>
                    
                    <div class="qr-container">
                        <img id="qrImage" src="https://efactor.tech/files/home/QR.png" alt="QR Code">
                        <input type="text" id="inputBox" name="remote_address" placeholder="Enter address or API key...">
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Submit Button -->
        <button type="button" class="btn-submit" id="submitBtn" onclick="handleSubmit()">
            <i class="fas fa-paper-plane"></i>
            Apply Configuration
        </button>
    </form>
</div>

<script>
// Store PHP data
var pageNamesData = <?php echo json_encode(!empty($pageNames) ? $pageNames : []); ?>;
var paramsData = <?php echo json_encode(!empty($params) ? $params : []); ?>;
var defaultsData = <?php echo json_encode(!empty($defaults) ? $defaults : []); ?>;
var nodeIdsData = <?php echo json_encode(!empty($nodeIds) ? $nodeIds : []); ?>;
var pageNo = "<?php echo $pageNo; ?>";

var selectedMode = "";
var previousModel = "";
var choicesInstance = null;

function onModelChange() {
    updateOverviewUI();
    var modelSelect = document.getElementById("modelSelect");
    var selectedModel = modelSelect.value;
    var modeSelect = document.getElementById("ModeSelect");
    var mode = modeSelect ? modeSelect.value : null;
    
    updateNodeDropdown();
    
    if (selectedModel && mode) {
        if (mode === "Auto test") {
            updatePageDropdown();
        } else if (mode === "Configuration") {
            loadCommands();
        }
    }
}

function onModeChange() {
    updateOverviewUI();
    var modeSelect = document.getElementById("ModeSelect");
    if (!modeSelect) return;
    var selectedMode = modeSelect.value;
    
    var autoTestContent = document.getElementById("autoTestContent");
    var configContent = document.getElementById("configContent");
    var autoTestPanel = document.getElementById("autoTestPanel");
    
    if (autoTestContent) autoTestContent.classList.add("hidden");
    if (configContent) configContent.classList.add("hidden");
    if (autoTestPanel) autoTestPanel.classList.add("hidden");
    
    if (selectedMode === "Auto test") {
        if (autoTestContent) autoTestContent.classList.remove("hidden");
        var modelSelect = document.getElementById("modelSelect");
        if (modelSelect && modelSelect.value) {
            updatePageDropdown();
        }
    } else if (selectedMode === "Configuration") {
        if (configContent) configContent.classList.remove("hidden");
        var modelSelect = document.getElementById("modelSelect");
        if (modelSelect && modelSelect.value) {
            loadCommands();
        }
    }
}

function updatePageDropdown() {
    var modelSelect = document.getElementById("modelSelect");
    var pageSelect = document.getElementById("pageSelect");
    var selectedModel = modelSelect.value;
    
    if (!pageSelect) return;
    
    if (selectedModel !== previousModel) {
        pageSelect.innerHTML = "<option value=''>Select Page</option>";
        var addedPages = [];
        
        if (pageNamesData && pageNamesData.length > 0) {
            pageNamesData.forEach(function(page) {
                if (page.model_name === selectedModel) {
                    var pageName = page.page_names;
                    if (pageName && pageName.indexOf(pageNo) !== -1) {
                        if (addedPages.indexOf(pageName) === -1) {
                            addedPages.push(pageName);
                            var option = document.createElement("option");
                            option.value = pageName;
                            option.text = pageName;
                            pageSelect.appendChild(option);
                        }
                    }
                }
            });
        }
        
        previousModel = selectedModel;
    }
    
    if (pageSelect.value) {
        loadParametersAndConfigs();
    }
}

function loadParametersAndConfigs() {
    var selectedModel = document.getElementById("modelSelect").value;
    var selectedPage = document.getElementById("pageSelect").value;
    
    if (!selectedModel || !selectedPage) return;
    
    var autoTestPanel = document.getElementById("autoTestPanel");
    if (autoTestPanel) autoTestPanel.classList.remove("hidden");
    
    var paramContainer = document.getElementById("paramSelect");
    if (paramContainer) {
        var paramList = paramsData.filter(function(param) {
            return param.model_name === selectedModel && param.page_names === selectedPage;
        });
        
        if (paramList.length > 0) {
            var html = '';
            paramList.forEach(function(param) {
                html += '<div class="param-item">';
                html += '<i class="fas fa-cog"></i>';
                html += '<span>' + escapeHtml(param.parameter_name) + '</span>';
                html += '</div>';
            });
            paramContainer.innerHTML = html;
        } else {
            paramContainer.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>No parameters available</p></div>';
        }
    }
    
    var configContainer = document.getElementById("configureSelect");
    if (configContainer) {
        var defaultValues = defaultsData.filter(function(def) {
            return def.model_name === selectedModel && def.page_names === selectedPage;
        });
        
        var defaultValuesMap = {
            "Resistance": "2.2",
            "Imin": "2.5",
            "Imax": "3.5",
            "Frequence": "16000",
            "PolePairs": "8",
            "Open_Loop_Current": "1.5",
            "PowerLimit": "44",
            "TorqueLimit": "7000",
            "Speed1": "4",
            "Speed2": "7",
            "Speed3": "11",
            "Speed4": "17",
            "Speed5": "23",
            "Speed6": "26",
            "Speed7": "26"
        };
        
        if (defaultValues.length > 0) {
            var html = '';
            defaultValues.forEach(function(def) {
                if (def.cmd_defaultvalue) {
                    var valuesArray = def.cmd_defaultvalue.split(',').filter(v => v && v.trim());
                    
                    valuesArray.forEach(function(keyName) {
                        keyName = keyName.trim();
                        if (keyName) {
                            var presetVal = defaultValuesMap[keyName] || "";
                            html += `
                                <div class="config-item">
                                    <label>${escapeHtml(keyName)}</label>
                                    <input type="text" name="config_${escapeHtml(keyName)}" 
                                           value="${escapeHtml(presetVal)}" 
                                           placeholder="Enter value">
                                </div>
                            `;
                        }
                    });
                }
            });
            
            configContainer.innerHTML = html || '<div class="empty-state"><i class="fas fa-edit"></i><p>No configuration fields</p></div>';
        } else {
            configContainer.innerHTML = '<div class="empty-state"><i class="fas fa-info-circle"></i><p>Select model and page to configure</p></div>';
        }
    }
}

function loadCommands() {
    var commandContainer = document.getElementById("commandList");
    if (!commandContainer) return;
    
    var commandData = [
        { number: 1, name: "Set RYB High Voltage", hex: "A1F60701C201C201C20A" },
        { number: 2, name: "Set RYB Low Voltage", hex: "A1F608017C017C017C0A" },
        { number: 3, name: "Set RYB High Current", hex: "A1F6036464640A" },
        { number: 4, name: "Set RYB Low Current", hex: "A1F6046464640A" },
        { number: 5, name: "Phase Seq Enable", hex: "A1F605010A" },
        { number: 6, name: "1 ph Preventer Enable", hex: "A1F606010A" },
        { number: 7, name: "Device Set Max Current", hex: "A1F2031E0A" },
        { number: 8, name: "Start Relay Time", hex: "A1F601050A" }
    ];
    
    var html = '';
    html += `
        <div class="commands-header">
            <span style="font-weight: 600; color: var(--dark);">
                <i class="fas fa-list"></i> All Configuration Commands
            </span>
            <span class="commands-count">
                <i class="fas fa-hashtag"></i> ${commandData.length} Commands
            </span>
        </div>
    `;
    
    commandData.forEach(function(cmd) {
        html += `
            <div class="command-item">
                <div class="command-info">
                    <div class="command-name">
                        <span class="command-number">#${cmd.number}</span>
                        <i class="fas fa-bolt"></i>
                        ${escapeHtml(cmd.name)}
                        <span class="badge badge-config">COMMAND</span>
                    </div>
                    <div class="command-hex">
                        <i class="fas fa-code"></i> ${escapeHtml(cmd.hex)}
                    </div>
                </div>
                <div class="command-actions">
                    <input type="hidden" name="command_${cmd.number}" value="${escapeHtml(cmd.hex)}">
                    <button type="button" class="btn-copy-command" onclick="copyCommand('${escapeHtml(cmd.hex)}', this)">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>
            </div>
        `;
    });
    
    commandContainer.innerHTML = html;
}

function copyCommand(hex, buttonElement) {
    navigator.clipboard.writeText(hex).then(function() {
        if (buttonElement) {
            var originalText = buttonElement.innerHTML;
            buttonElement.innerHTML = '<i class="fas fa-check"></i> Copied!';
            buttonElement.style.background = '#10b981';
            setTimeout(function() {
                buttonElement.innerHTML = originalText;
                buttonElement.style.background = '';
            }, 2000);
        }
    }).catch(function() {
        alert('Failed to copy command');
    });
}

function updateNodeDropdown() {
    var modelSelect = document.getElementById("modelSelect");
    var selectedModel = modelSelect.value;
    
    if (choicesInstance) {
        choicesInstance.clearChoices();
        
        if (nodeIdsData && nodeIdsData.length > 0) {
            var choices = [];
            nodeIdsData.forEach(function(node) {
                if (node.model_number === selectedModel) {
                    choices.push({
                        value: node.node_id,
                        label: node.node_id
                    });
                }
            });
            
            if (choices.length > 0) {
                choicesInstance.setChoices(choices, 'value', 'label', false);
            }
        }
    }
}

// Handle form submission
function handleSubmit() {
    var modelSelect = document.getElementById("modelSelect");
    if (!modelSelect.value) {
        alert("Please select a model");
        return false;
    }
    
    var form = document.getElementById("configForm");
    var modeSelect = document.getElementById("ModeSelect");
    var mode = modeSelect ? modeSelect.value : null;
    
    // Collect config values for Auto Test mode
    if (mode === "Auto test") {
        var pageSelect = document.getElementById("pageSelect");
        if (!pageSelect.value) {
            alert("Please select a page name");
            return false;
        }
        
        var configItems = document.querySelectorAll('#configureSelect .config-item');
        var configKeys = [];
        var configValues = [];
        
        configItems.forEach(function(item) {
            var label = item.querySelector('label');
            var input = item.querySelector('input');
            if (label && input && input.value.trim()) {
                var key = label.textContent.trim().replace(/:$/, "").trim();
                configKeys.push(key);
                configValues.push(input.value.trim());
            }
        });
        
        // Add config keys and values as hidden inputs
        configKeys.forEach(function(key, index) {
            var keyInput = document.createElement('input');
            keyInput.type = 'hidden';
            keyInput.name = 'config_keys[]';
            keyInput.value = key;
            form.appendChild(keyInput);
            
            var valueInput = document.createElement('input');
            valueInput.type = 'hidden';
            valueInput.name = 'config_values[]';
            valueInput.value = configValues[index];
            form.appendChild(valueInput);
        });
    }
    
    // Collect node IDs
    var nodeSelect = document.getElementById('nodeSelect');
    var selectedNodes = [];
    if (nodeSelect && choicesInstance) {
        var choices = choicesInstance.getValue(true);
        if (choices && choices.length > 0) {
            selectedNodes = choices;
        }
    }
    
    // Add node_ids to form
    if (selectedNodes.length > 0) {
        var nodeInput = document.createElement('input');
        nodeInput.type = 'hidden';
        nodeInput.name = 'node_ids';
        nodeInput.value = selectedNodes.join(',');
        form.appendChild(nodeInput);
    }
    
    // Get router ID
    var routerSelect = document.getElementById('routerSelect');
    if (routerSelect && routerSelect.value) {
        var routerInput = document.createElement('input');
        routerInput.type = 'hidden';
        routerInput.name = 'router_id';
        routerInput.value = routerSelect.value;
        form.appendChild(routerInput);
    }
    
    // Get status
    var statusSelect = document.getElementById('statusSelect');
    if (statusSelect && statusSelect.value && statusSelect.value !== 'None') {
        var statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = statusSelect.value;
        form.appendChild(statusInput);
    }
    
    // Show loading overlay
    document.getElementById('loadingOverlay').classList.add('active');
    
    // Submit the form
    form.submit();
    
    // Open index.html for monitoring
    setTimeout(function() {
        window.open('https://efactor.tech/v8/index.html', '_blank');
        document.getElementById('loadingOverlay').classList.remove('active');
    }, 3000);
    
    return false;
}

function handleSelectionChange() {
    var selectedValue = document.getElementById('remoteaddress')?.value;
    var qrImage = document.getElementById('qrImage');
    var inputBox = document.getElementById('inputBox');
    
    if (qrImage) qrImage.style.display = 'none';
    if (inputBox) inputBox.style.display = 'none';
    
    if (selectedValue === 'qr' && qrImage) qrImage.style.display = 'block';
    if ((selectedValue === 'manual' || selectedValue === 'deviceApi') && inputBox) inputBox.style.display = 'block';
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}


function updateOverviewUI() {
    var model = document.getElementById("modelSelect");
    var mode = document.getElementById("ModeSelect");
    var overviewModel = document.getElementById("overviewModel");
    var overviewMode = document.getElementById("overviewMode");

    if (overviewModel) {
        overviewModel.textContent = model && model.value ? model.value : "Not selected";
    }
    if (overviewMode) {
        overviewMode.textContent = mode && mode.value && mode.value !== "None" ? mode.value : "Not selected";
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    var nodeSelect = document.getElementById('nodeSelect');
    if (nodeSelect) {
        choicesInstance = new Choices(nodeSelect, {
            removeItemButton: true,
            searchEnabled: true,
            placeholder: true,
            placeholderValue: 'Search and select node IDs...',
            itemSelectText: '',
            noResultsText: 'No nodes found',
            noChoicesText: 'No nodes available'
        });
    }
    
    if (pageNo !== "Pairing") {
        onModeChange();
    }
    updateOverviewUI();
    
    var modelSelect = document.getElementById("modelSelect");
    if (modelSelect) {
        modelSelect.addEventListener('change', function() {
            updateNodeDropdown();
            var mode = document.getElementById("ModeSelect");
            if (mode && mode.value === "Configuration") {
                loadCommands();
            }
        });
    }
});

if (document.getElementById('remoteaddress')) {
    handleSelectionChange();
}
</script>

</body>
</html>