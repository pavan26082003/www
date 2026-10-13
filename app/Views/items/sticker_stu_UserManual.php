<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Label Designer – User Manual</title>
  <style>
    :root {
      --bg: #f6f8fc;
      --surface: #ffffff;
      --surface-2: #f9fbff;
      --text: #1f2937;
      --muted: #5f6368;
      --line: #dbe3f1;
      --blue: #1a73e8;
      --blue-soft: #e8f0fe;
      --purple: #7c3aed;
      --purple-soft: #f3e8ff;
      --yellow: #f7c45a;
      --yellow-soft: #fff7df;
      --green: #16a34a;
      --green-soft: #eaf7ee;
      --red: #dc2626;
      --red-soft: #fdecec;
      --shadow: 0 10px 30px rgba(26, 115, 232, 0.08);
      --radius: 18px;
      --radius-sm: 12px;
      --max: 1400px;
    }

    * {
      box-sizing: border-box;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      margin: 0;
      font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
      color: var(--text);
      background: linear-gradient(180deg, #eef3fb 0%, #f7f9fd 240px, #f6f8fc 240px);
      line-height: 1.6;
    }

    a {
      color: var(--blue);
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    .page {
      max-width: var(--max);
      margin: 0 auto;
      padding: 24px;
    }

    .hero {
      background: linear-gradient(135deg, #ffffff 0%, #f8fbff 60%, #eef4ff 100%);
      border: 1px solid var(--line);
      border-radius: 28px;
      box-shadow: var(--shadow);
      padding: 40px;
      margin-bottom: 24px;
      position: relative;
      overflow: hidden;
      animation: fadeUp .7s ease both;
    }

    .hero::after {
      content: "";
      position: absolute;
      inset: auto -60px -60px auto;
      width: 220px;
      height: 220px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(124, 58, 237, 0.13), rgba(26, 115, 232, 0.05) 60%, transparent 70%);
      pointer-events: none;
    }

    .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 14px;
      border-radius: 999px;
      background: var(--blue-soft);
      color: var(--blue);
      font-weight: 700;
      font-size: 13px;
      letter-spacing: 0.02em;
      margin-bottom: 14px;
    }

    h1 {
      margin: 0 0 10px;
      font-size: clamp(32px, 5vw, 52px);
      line-height: 1.08;
      letter-spacing: -0.03em;
    }

    .subtitle {
      margin: 0;
      max-width: 760px;
      color: var(--muted);
      font-size: 18px;
    }

    .hero-grid {
      display: grid;
      grid-template-columns: 1.4fr 1fr;
      gap: 22px;
      margin-top: 28px;
    }

    .summary-card,
    .stat-card {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid var(--line);
      border-radius: 20px;
      padding: 22px;
      transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
    }

    .summary-card:hover,
    .stat-card:hover,
    .card:hover,
    .tool-card:hover,
    .shot-card:hover,
    .faq-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 14px 34px rgba(26, 115, 232, 0.10);
      border-color: #cbdaf7;
    }

    .summary-card h2,
    .stat-card h2 {
      margin-top: 0;
      font-size: 20px;
    }

    .quick-flow {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-top: 14px;
    }

    .quick-step {
      background: var(--surface-2);
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 14px;
    }

    .quick-step .num {
      width: 28px;
      height: 28px;
      display: inline-grid;
      place-items: center;
      border-radius: 50%;
      background: var(--blue);
      color: white;
      font-size: 13px;
      font-weight: 700;
      margin-bottom: 4px;
      margin-right: 4px;
    }

    .layout {
      display: grid;
      grid-template-columns: 280px minmax(0, 1fr);
      gap: 24px;
      align-items: start;
    }

    .toc {
      position: sticky;
      top: 18px;
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: 22px;
      box-shadow: var(--shadow);
      padding: 18px;
    }

    .toc h2 {
      margin: 2px 0 12px;
      font-size: 18px;
    }

    .toc ol {
      margin: 0;
      padding-left: 18px;
    }

    .toc li {
      margin: 6px 0;
    }

    .toc a {
      color: var(--muted);
      display: inline-block;
      padding: 3px 0;
    }

    .toc a.active,
    .toc a:hover {
      color: var(--blue);
      font-weight: 600;
      text-decoration: none;
      transform: translateX(2px);
    }

    main section {
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: 22px;
      box-shadow: var(--shadow);
      padding: 28px;
      margin-bottom: 22px;
      scroll-margin-top: 24px;
      animation: fadeUp .65s ease both;
    }

    h2.section-title {
      margin: 0 0 14px;
      font-size: 28px;
      line-height: 1.2;
      letter-spacing: -0.02em;
    }

    h3 {
      margin: 22px 0 8px;
      font-size: 21px;
      line-height: 1.3;
    }

    h4 {
      margin: 18px 0 6px;
      font-size: 17px;
    }

    p {
      margin: 10px 0;
    }

    ul,
    ol {
      margin: 10px 0 10px 22px;
    }

    li {
      margin: 7px 0;
    }

    .callout {
      border-radius: 16px;
      padding: 16px 18px;
      margin: 16px 0;
      border: 1px solid var(--line);
    }

    .callout strong {
      display: block;
      margin-bottom: 4px;
    }

    .tip {
      background: var(--blue-soft);
      border-color: #c9dafc;
    }

    .note {
      background: var(--yellow-soft);
      border-color: #f0de9d;
    }

    .warning {
      background: var(--red-soft);
      border-color: #f7c2c2;
    }

    .success {
      background: var(--green-soft);
      border-color: #cde8d5;
    }

    .two-col,
    .three-col,
    .cards {
      display: grid;
      gap: 16px;
    }

    .two-col {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .three-col {
      grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .cards {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .card,
    .tool-card,
    .shot-card {
      background: var(--surface-2);
      border: 1px solid var(--line);
      border-radius: 18px;
      padding: 18px;
    }

    .tool-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
      margin-top: 14px;
    }

    .tool-card h4 {
      margin-top: 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .tool-badge {
      display: inline-grid;
      place-items: center;
      min-width: 34px;
      height: 34px;
      padding: 0 10px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 13px;
      color: #1f2937;
      background: linear-gradient(180deg, #ffffff, #edf2fb);
      border: 1px solid var(--line);
    }

    .tool-card p:last-child {
      margin-bottom: 0;
    }

    .card,
    .tool-card,
    .shot-card,
    .faq-item,
    .toc,
    .callout {
      transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease, background-color .25s ease;
    }

    .quick-step {
      transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }

    .quick-step:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 26px rgba(26, 115, 232, 0.08);
      border-color: #cbdaf7;
    }

    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(12px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes pulseGlow {
      0% {
        box-shadow: 0 0 0 0 rgba(26, 115, 232, 0.18);
      }

      100% {
        box-shadow: 0 0 0 14px rgba(26, 115, 232, 0);
      }
    }

    .step-list {
      counter-reset: item;
      list-style: none;
      padding: 0;
      margin: 16px 0 0;
    }

    .step-list li {
      counter-increment: item;
      display: grid;
      grid-template-columns: 44px 1fr;
      gap: 14px;
      align-items: start;
      padding: 14px 0;
      border-top: 1px solid #edf2fb;
      margin: 0;
    }

    .step-list li:first-child {
      border-top: none;
      padding-top: 0;
    }

    .step-list li::before {
      content: counter(item);
      display: grid;
      place-items: center;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: var(--blue);
      color: white;
      font-weight: 700;
      margin-top: 2px;
    }

    .mini-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .mini-list li {
      padding: 10px 0;
      border-top: 1px solid #edf2fb;
      margin: 0;
    }

    .mini-list li:first-child {
      border-top: none;
    }

    .shot-card img {
      width: 100%;
      height: auto;
      display: block;
      border-radius: 14px;
      border: 1px solid var(--line);
      background: white;
    }

    figure {
      margin: 0;
    }

    figcaption {
      font-size: 14px;
      color: var(--muted);
      margin-top: 10px;
    }

    .faq {
      display: grid;
      gap: 12px;
      margin-top: 14px;
    }

    .faq-item {
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 16px 18px;
      background: var(--surface-2);
    }

    .faq-item h4 {
      margin: 0 0 6px;
    }

    .checklist {
      list-style: none;
      padding: 0;
      margin: 12px 0 0;
      display: grid;
      gap: 10px;
    }

    .checklist li {
      display: flex;
      gap: 10px;
      align-items: flex-start;
      background: var(--surface-2);
      border: 1px solid var(--line);
      border-radius: 14px;
      padding: 14px 16px;
      margin: 0;
    }

    .check {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: var(--green-soft);
      color: var(--green);
      display: inline-grid;
      place-items: center;
      font-weight: 800;
      flex: 0 0 22px;
      margin-top: 1px;
    }

    .print-only {
      display: none;
    }

    .video-modal {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.76);
      backdrop-filter: blur(6px);
      display: none;
      align-items: center;
      justify-content: center;
      padding: 28px;
      z-index: 9999;
    }

    .video-modal.open {
      display: flex;
      animation: fadeUp .2s ease both;
    }

    .video-shell {
      width: min(1100px, 96vw);
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 22px;
      box-shadow: 0 24px 80px rgba(0, 0, 0, .25);
      overflow: hidden;
    }

    .video-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 4px 18px;
      background: linear-gradient(180deg, #ffffff, #f8fbff);
      border-bottom: 1px solid var(--line);
    }

    .video-topbar h3 {
      margin: 0;
      font-size: 18px;
    }

    .video-close {
      border: none;
      background: transparent;
      color: var(--blue);
      width: 40px;
      height: 40px;
      border-radius: 12px;
      font-size: 20px;
      cursor: pointer;
      transition: transform .2s ease, background-color .2s ease;
    }

    .video-close:hover {
      transform: scale(1.04);
    }

    .video-frame {
      background: #0b1220;
    }

    .video-frame video {
      width: 100%;
      height: min(74vh, 760px);
      display: block;
      background: #000;
    }

    .video-open-btn:hover {
      transform: translateY(-2px);
    }

    @media print {
      .video-modal {
        display: none !important;
      }
    }

    @media (max-width: 1080px) {
      .layout {
        grid-template-columns: 1fr;
      }

      .toc {
        position: relative;
        top: 0;
      }

      .hero-grid,
      .two-col,
      .three-col,
      .cards,
      .tool-grid,
      .quick-flow {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 720px) {
      .page {
        padding: 14px;
      }

      .hero,
      main section,
      .toc {
        padding: 20px;
        border-radius: 18px;
      }

      h1 {
        font-size: 34px;
      }

      h2.section-title {
        font-size: 24px;
      }

      .tool-grid {
        grid-template-columns: 1fr;
      }
    }

    @media print {
      body {
        background: white;
      }

      .page {
        max-width: none;
        padding: 0;
      }

      .hero,
      .toc,
      main section {
        box-shadow: none;
        border: 1px solid #d6dce8;
        break-inside: avoid;
      }

      .layout {
        grid-template-columns: 1fr;
      }

      .toc {
        display: none;
      }

      .print-only {
        display: block;
      }

      a {
        color: inherit;
        text-decoration: none;
      }
    }
  </style>
  <style>
    @media (max-width:678px) {
      .page {
        display: none !important
      }

      .mobile-block {
        display: flex !important
      }
    }
  </style>
</head>

<body>
  <div class="mobile-block" style="display:none;min-height:100vh;align-items:center;justify-content:center;text-align:center;padding:40px;font-family:Inter,Segoe UI,Arial,sans-serif;background:#f6f8fc;">
    <div style="max-width:420px;background:white;border:1px solid #dbe3f1;border-radius:16px;padding:28px;box-shadow:0 10px 30px rgba(0,0,0,0.06);">
      <h2 style="margin-top:0">Screen Not Supported</h2>
      <p>This manual is designed for larger screens.</p>
      <p>Please open it on a <strong>tablet (≥ 678px width)</strong> or a <strong>desktop computer</strong> for the best reading experience.</p>
    </div>
  </div>

  <div class="page">
    <header class="hero" id="top">
      <div class="eyebrow">Label Designer Manual</div>
      <h1>Label Designer – User Manual</h1>
      <p class="subtitle">A simple guide to creating, arranging, saving, and printing labels. This manual is written for everyday users who want to build labels clearly and confidently.</p>
      <div class="hero-grid">
        <div class="summary-card">
          <h2>What the Label Designer helps you do</h2>
          <p>The Label Designer is the screen where you create the final look of your label. You can set the page size, create a label area, adjust text, add a QR code or barcode, place images, save your work, and print or export the result.</p>
          <div class="callout tip">
            <strong>Best way to start</strong>
            Create the page size first, then create the label size, then arrange your content.
          </div>
        </div>
        <div class="stat-card">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <h2 style="margin:0">Quick Start</h2>
            <a href="#video-demo" style="padding:8px 14px;border-radius:10px;background:#1a73e8;color:#fff;font-size:13px;font-weight:600;text-decoration:none;box-shadow:0 4px 12px rgba(26,115,232,.25);transition:transform .2s ease,box-shadow .2s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 18px rgba(26,115,232,.35)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 12px rgba(26,115,232,.25)'">Watch Video Demo</a>
          </div>
          <div class="quick-flow">
            <div class="quick-step">
              <div class="num">1</div><strong>Open</strong><br>Open the Label Designer screen.
            </div>
            <div class="quick-step">
              <div class="num">2</div><strong>Set page</strong><br>Use Page Size to create the print page.
            </div>
            <div class="quick-step">
              <div class="num">3</div><strong>Set label</strong><br>Use New Label to create the label area.
            </div>
            <div class="quick-step">
              <div class="num">4</div><strong>Check fields</strong><br>Review and update values in the left panel.
            </div>
            <div class="quick-step">
              <div class="num">5</div><strong>Arrange</strong><br>Format text and move items into place.
            </div>
            <div class="quick-step">
              <div class="num">6</div><strong>Save & print</strong><br>Save your design, preview it, and print.
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="layout">
      <div class="search-fab" style="display:none"></div>
      <nav class="toc" aria-label="Table of contents">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px;">
          <h2 style="margin:0;">On this page</h2>
        </div>
        <div class="manual-search" style="margin-bottom:14px;">
          <label for="manualSearch" style="display:block;font-size:13px;font-weight:600;color:var(--muted);margin-bottom:8px;">Search inside manual</label>
          <div style="position:relative;">
            <input id="manualSearch" type="text" placeholder="Type a word or topic" style="width:100%;padding:12px 40px 12px 12px;border:1px solid var(--line);border-radius:12px;background:#fff;font:inherit;outline:none;transition:border-color .2s ease, box-shadow .2s ease;">
            <span style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:12px;">Ctrl+F</span>
          </div>
          <p id="searchHint" style="margin:8px 2px 0;font-size:12px;color:var(--muted);">Use this box for quick topic jumps, or use your browser search with Ctrl+F.</p>
        </div>
        <h2>On this page</h2>
        <ol>
          <li><a href="#introduction">Introduction</a></li>
          <li><a href="#screen-overview">Screen Overview</a></li>
          <li><a href="#left-panel">Left Panel</a></li>
          <li><a href="#toolbar">Top Toolbar</a></li>
          <li><a href="#workspace">Workspace Guide</a></li>
          <li><a href="#create-label">Create a Label</a></li>
          <li><a href="#text">Working With Text</a></li>
          <li><a href="#qr-barcode">QR Code and Barcode</a></li>
          <li><a href="#images">Using Images</a></li>
          <li><a href="#save-clear">Save, Undo, Redo, Clear</a></li>
          <li><a href="#print-settings">Print Settings</a></li>
          <li><a href="#permission">Permission Popup</a></li>
          <li><a href="#preview">Print Preview</a></li>
          <li><a href="#best-practices">Best Practices</a></li>
          <li><a href="#mistakes">Common Mistakes</a></li>
          <li><a href="#workflow">Example Workflow</a></li>
          <li><a href="#video-demo">Video Demo</a></li>
          <li><a href="#checklist">Final Checklist</a></li>
        </ol>
      </nav>

      <main id="manualContent">
        <section id="introduction">
          <h2 class="section-title">1. Introduction</h2>
          <p>The Label Designer is where you create the final layout of your label. It brings together your field values, text formatting, QR settings, barcode settings, images, and printing options in one place.</p>
          <p>You do not need technical knowledge to use it. The screen is designed to help you work step by step: set the page, create the label, check your values, arrange the content, save the design, and print.</p>
          <div class="callout success">
            <strong>Main idea</strong>
            Think of the print page as the larger sheet and the label as the smaller design area inside it.
          </div>
        </section>

        <section id="screen-overview">
          <h2 class="section-title">2. Screen Overview</h2>
          <div class="two-col">
            <div class="card">
              <h3>Left side of the screen</h3>
              <p>This side is for label values and product settings. You can update field values directly here, control barcode and QR options, and save your changes.</p>
            </div>
            <div class="card">
              <h3>Right side of the screen</h3>
              <p>This side is the design area. It shows the print page, the label, the ruler, the grid, and the content placed on the label.</p>
            </div>
          </div>
          <div class="cards" style="margin-top:16px;">
            <div class="card">
              <h3>Top toolbar</h3>
              <p>The top toolbar is used for layout actions, text styling, object movement, image insertion, saving, and printing.</p>
            </div>
            <div class="card">
              <h3>Print settings window</h3>
              <p>When you click Print, a panel opens to help you choose the printer, page size, selected labels, copies, export format, and preview before printing.</p>
            </div>
          </div>
          <div class="callout note">
            <strong>What you will usually do most often</strong>
            Update field values on the left, arrange the label on the right, then save and print.
          </div>
        </section>

        <section id="left-panel">
          <h2 class="section-title">3. Left Panel</h2>
          <p>The left panel is where you work with the label values and product settings.</p>

          <h3>Field entries</h3>
          <p>At the top of the left panel, you will see the fields that appear on the label, such as Product Name, Serial No, and MRP. You can change the value directly in this panel.</p>
          <p>When you update these values, use <strong>Save Form</strong> to save the edits made in the left panel.</p>

          <h3>Count and QR checkboxes</h3>
          <p>Each row can also include two checkboxes:</p>
          <ul>
            <li><strong>QR checkbox</strong> – If checked, that field value is included in the QR code data.</li>
            <li><strong>Count checkbox</strong> – If checked and a count value is entered, that field can be used in batch printing for multiple stickers.</li>
          </ul>
          <p>This is useful when printing a series of labels where one value increases from one sticker to the next.</p>

          <div class="callout tip">
            <strong>Practical use</strong>
            If you want Serial No to change across multiple printed stickers, enter a starting value, check the count box, and use batch printing from the print window.
          </div>

          <h3>Save Form</h3>
          <p>This button saves the edits you made directly in the left panel, such as changed field values, count settings, and QR selections.</p>

          <h3>Edit Field</h3>
          <p>This button opens the Sticker Form page, where you add new fields or edit the structure of existing fields. Use it when you want to change which fields appear on the label, not just the current values.</p>

          <h3>Product Specifications</h3>
          <p>Below the field entries, you will see product-related options:</p>
          <ul>
            <li><strong>Product Name</strong> – Shows the current product category.</li>
            <li><strong>Model</strong> – Shows the current model name.</li>
            <li><strong>Include Barcode</strong> – Choose Yes or No.</li>
            <li><strong>Select Node ID</strong> – Choose the Node ID used for the QR code.</li>
            <li><strong>QR Size</strong> – Choose the QR size in millimetres.</li>
          </ul>

          <h3>Barcode setting</h3>
          <p>If you choose <strong>Yes</strong> for Include Barcode, more barcode options become part of your workflow, including barcode value, barcode type, and barcode size. These help you prepare the barcode before placing or printing it.</p>

          <h3>QR setting</h3>
          <p>To build a QR code on the label, select a <strong>Node ID</strong>. Then choose the <strong>QR Size</strong>. After that, the QR appears in the label area when the label is created or refreshed.</p>
        </section>

        <section id="toolbar">
          <h2 class="section-title">4. Top Toolbar</h2>
          <p>The top toolbar controls the layout, text appearance, object movement, saving, and printing.</p>

          <div class="tool-grid">
            <article class="tool-card">
              <h4><span class="tool-badge">Page</span>Page Size</h4>
              <p>Creates the print page. Use this first to define the larger working page in millimetres.</p>
              <p><strong>When to use:</strong> At the very beginning of a new design.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">Label</span>New Label</h4>
              <p>Creates the main label area inside the print page.</p>
              <p><strong>When to use:</strong> Right after you create the page size.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">Copy</span>Copy Label</h4>
              <p>Creates another label based on the size of an existing label.</p>
              <p><strong>When to use:</strong> When you want more than one label of the same size.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">Font</span>Font family</h4>
              <p>Changes the style of the selected text.</p>
              <p><strong>Example:</strong> Change from Arial to another readable font.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">Size</span>Font size</h4>
              <p>Changes how large the selected text appears.</p>
              <p><strong>When to use:</strong> To make headings larger or details smaller.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">Color</span>Font color</h4>
              <p>Changes the color of selected text.</p>
              <p><strong>When to use:</strong> Usually keep it dark for clear printing.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">B</span>Bold</h4>
              <p>Makes selected text stronger and more noticeable.</p>
              <p><strong>Best for:</strong> Product names or important values.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">I</span>Italic</h4>
              <p>Tilts selected text slightly.</p>
              <p><strong>Best for:</strong> Limited emphasis only.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">U</span>Underline</h4>
              <p>Adds a line under selected text.</p>
              <p><strong>Best for:</strong> Highlighting a short heading.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">↑</span>Move Up</h4>
              <p>Moves the selected item slightly upward.</p>
              <p><strong>When to use:</strong> For small alignment corrections.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">←</span>Move Left</h4>
              <p>Moves the selected item slightly left.</p>
              <p><strong>When to use:</strong> For fine positioning.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">↓</span>Move Down</h4>
              <p>Moves the selected item slightly downward.</p>
              <p><strong>When to use:</strong> For fine positioning.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">→</span>Move Right</h4>
              <p>Moves the selected item slightly right.</p>
              <p><strong>When to use:</strong> For fine positioning.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">Img</span>Photo / Image</h4>
              <p>Opens the image area so you can add a logo or picture to the label.</p>
              <p><strong>Best for:</strong> Brand logos and product images.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">Del</span>Delete</h4>
              <p>Removes the selected item from the label.</p>
              <p><strong>Use carefully:</strong> Only when the correct item is selected.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">↶</span>Undo</h4>
              <p>Reverses the last change.</p>
              <p><strong>Use when:</strong> You moved, edited, or removed something by mistake.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">↷</span>Redo</h4>
              <p>Brings back the last change you undid.</p>
              <p><strong>Use when:</strong> You want to restore an undone step.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">QR</span>QR Info</h4>
              <p>Shows the QR content so you can review what is being included.</p>
              <p><strong>Use when:</strong> You want to confirm the QR before printing.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">Clr</span>Clear</h4>
              <p>Removes the current design from the page.</p>
              <p><strong>Use only when:</strong> You want to start the design again.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">Save</span>Save</h4>
              <p>Saves the current label design.</p>
              <p><strong>Use often:</strong> Save before opening print settings.</p>
            </article>
            <article class="tool-card">
              <h4><span class="tool-badge">Print</span>Print</h4>
              <p>Opens the print settings window and preview.</p>
              <p><strong>Use when:</strong> You are ready to preview, print, or export.</p>
            </article>
          </div>
        </section>

        <section id="workspace">
          <h2 class="section-title">5. Workspace Guide</h2>
          <p>The right side of the screen is the workspace. This is where you see your page, label, text, QR code, and other items.</p>
          <div class="three-col">
            <div class="card">
              <h3>Rulers</h3>
              <p>The ruler at the top and the ruler at the left help you place items more accurately.</p>
            </div>
            <div class="card">
              <h3>Grid</h3>
              <p>The grid in the background helps you line up content neatly and keep spacing even.</p>
            </div>
            <div class="card">
              <h3>Page and label</h3>
              <p>The larger page contains the smaller label. Your design should sit neatly inside the label boundary.</p>
            </div>
          </div>

          <h3>How selection works</h3>
          <ul>
            <li><strong>Element selection</strong> – Single-click an item to select it. Then drag it if needed.</li>
            <li><strong>Label selection</strong> – Double-click the label to select the label itself. Then click and drag to move the label.</li>
            <li><strong>Text selection</strong> – Single-click text to select it.</li>
            <li><strong>Text editing</strong> – Double-click text to edit its value. Press <strong>Enter</strong> to save the change.</li>
          </ul>

          <div class="callout tip">
            <strong>Helpful habit</strong>
            If something does not move the way you expect, first check whether you selected the text item or the label itself.
          </div>
        </section>

        <section id="create-label">
          <h2 class="section-title">6. Creating a Label Step by Step</h2>
          <ol class="step-list">
            <li>
              <div><strong>Open the Label Designer.</strong><br>Make sure the screen is fully loaded and you can see the left panel, the top toolbar, and the workspace.</div>
            </li>
            <li>
              <div><strong>Review the field values on the left.</strong><br>Check that Product Name, Serial No, MRP, and other values are correct. If you change them here, click <strong>Save Form</strong>.</div>
            </li>
            <li>
              <div><strong>Click Page Size.</strong><br>Enter the page width and height in millimetres. This creates the print page.</div>
            </li>
            <li>
              <div><strong>Click New Label.</strong><br>Enter the label width and height in millimetres. This creates the label area inside the page.</div>
            </li>
            <li>
              <div><strong>Check that the label appears inside the page.</strong><br>You should now see the label boundary in the workspace.</div>
            </li>
            <li>
              <div><strong>Adjust text formatting from the toolbar.</strong><br>Select text, then change font family, size, color, or style as needed.</div>
            </li>
            <li>
              <div><strong>Add QR or barcode settings from the left panel.</strong><br>For QR, select a Node ID and choose a QR size. For barcode, choose Yes for Include Barcode and prepare the barcode options.</div>
            </li>
            <li>
              <div><strong>Move and align items in the workspace.</strong><br>Use drag and the move arrows to position content neatly inside the label.</div>
            </li>
            <li>
              <div><strong>Save the design.</strong><br>Click <strong>Save</strong> so your layout is stored before printing.</div>
            </li>
            <li>
              <div><strong>Open print settings.</strong><br>Click <strong>Print</strong> to preview, choose the label, set copies, and print or export.</div>
            </li>
          </ol>
        </section>

        <section id="text">
          <h2 class="section-title">7. Working With Text</h2>
          <p>Text is one of the most important parts of a label. Keep it clear and easy to read.</p>
          <ul>
            <li><strong>Select text</strong> with a single click.</li>
            <li><strong>Edit the text value</strong> by double-clicking the text. Press <strong>Enter</strong> to save the change.</li>
            <li><strong>Change font family</strong> from the dropdown to use a clean, readable style.</li>
            <li><strong>Change font size</strong> to make headings larger and details smaller.</li>
            <li><strong>Change color</strong> only if needed. Dark text is usually the safest choice for printing.</li>
            <li><strong>Use bold</strong> for important details like Product Name.</li>
            <li><strong>Use italic or underline</strong> only when it improves clarity.</li>
            <li><strong>Nudge text</strong> using the move arrows for precise placement.</li>
          </ul>
          <div class="callout note">
            <strong>Keep labels readable</strong>
            Avoid making text too small. If several lines look crowded, increase spacing or simplify the content.
          </div>
        </section>

        <section id="qr-barcode">
          <h2 class="section-title">8. Working With QR Code and Barcode</h2>

          <h3>QR code</h3>
          <p>To build the QR code, choose a <strong>Node ID</strong> from the left panel. Then select the <strong>QR Size</strong>. Once the label is created, the QR appears inside the label.</p>
          <p>If you want certain field values to be included in the QR, check the <strong>QR checkbox</strong> beside those fields in the left panel.</p>
          <p>Use the <strong>QR Info</strong> button from the toolbar when you want to review the QR content before printing.</p>

          <h3>Barcode</h3>
          <p>To use a barcode, select <strong>Yes</strong> in <strong>Include Barcode</strong>. Then use the barcode value, type, and size settings that become part of the barcode workflow.</p>
          <p>Barcode is helpful when the printed label must be scanned by a system or device.</p>

          <h3>Batch printing with counting</h3>
          <p>If a field needs to change from one printed sticker to the next, use:</p>
          <ul>
            <li>the <strong>count value</strong> input</li>
            <li>the <strong>count checkbox</strong> for that field</li>
          </ul>
          <p>This helps create multiple labels in a sequence during printing.</p>
        </section>

        <section id="images">
          <h2 class="section-title">9. Using Images / Photos</h2>
          <p>Use the image button in the toolbar when you want to add a logo or picture to the label.</p>
          <ul>
            <li>Open the image area.</li>
            <li>Upload your logo or picture.</li>
            <li>Insert it into the label.</li>
            <li>Move and resize it so it fits neatly.</li>
          </ul>
          <div class="callout tip">
            <strong>For better print quality</strong>
            Use clear, high-quality images and avoid very small logos that may look unclear after printing.
          </div>
        </section>

        <section id="save-clear">
          <h2 class="section-title">10. Save, Undo, Redo, and Clear</h2>
          <div class="two-col">
            <div class="card">
              <h3>Save</h3>
              <p>Saves your current design. Use this often, especially before opening print settings.</p>
            </div>
            <div class="card">
              <h3>Undo</h3>
              <p>Reverses the last change. Use this when you accidentally move, edit, or remove something.</p>
            </div>
            <div class="card">
              <h3>Redo</h3>
              <p>Restores a change that you just undid.</p>
            </div>
            <div class="card">
              <h3>Clear</h3>
              <p>Removes the current design from the page so you can start again.</p>
            </div>
          </div>
          <div class="callout warning">
            <strong>Warning about Clear</strong>
            Clear removes the current design from the page. Use it only when you are sure. Save your work first if you may need to come back to it.
          </div>
        </section>

        <section id="print-settings">
          <h2 class="section-title">11. Print Settings</h2>
          <p>When you click <strong>Print</strong>, the print settings window opens. This is where you choose what to print and how it should appear.</p>

          <ul class="mini-list">
            <li><strong>Printer</strong><br>Choose the printer you want to use. If the list is still loading, wait a moment.</li>
            <li><strong>Printer DPI</strong><br>Choose the print quality setting. For most everyday use, the default value is usually fine unless your printer requires something different.</li>
            <li><strong>Page Size – Default / Custom</strong><br>Use Default when your saved page size is correct. Use Custom only when you need a different page size for this print job.</li>
            <li><strong>Select Label</strong><br>Choose what you want to print. For example, you may choose <strong>Full Design</strong> or a specific label such as <strong>Label 1 (Main)</strong>.</li>
            <li><strong>Count Limit</strong><br>Use this when printing multiple stickers in a sequence. It controls how many counted labels should be produced.</li>
            <li><strong>Label Position – Default / Custom (Drag)</strong><br>Use Default for normal printing. Use Custom (Drag) when you need to manually adjust where the label sits on the page.</li>
            <li><strong>Copies</strong><br>Choose how many copies of the selected label you want.</li>
            <li><strong>Export Format</strong><br>Choose the format if you want to export instead of printing. For normal printing, keep your focus on the preview and the Print button.</li>
            <li><strong>Cancel</strong><br>Closes the print window without printing.</li>
            <li><strong>Print</strong><br>Sends the selected design to the preview and then to the printer when you are ready.</li>
          </ul>

          <div class="callout tip">
            <strong>Normal user choice</strong>
            Most users can keep Page Size on Default, Label Position on Default, choose the correct label, set Copies, check the preview, and then print.
          </div>

          <h3>How print mode works</h3>
          <p>Print mode is the final checking stage. Here you decide which label to print, how many copies you want, and whether the preview looks correct.</p>
          <p>If you are using counted values for multiple stickers, this is also where batch printing becomes important. Make sure the correct label is selected and the count limit matches what you want to produce.</p>
        </section>

        <section id="permission">
          <h2 class="section-title">12. When a permission popup appears</h2>
          <p>You may see a permission popup the first time you use printing. The popup asks whether the print system should be allowed to connect.</p>
          <ul>
            <li>Read the popup carefully.</li>
            <li>Click <strong>Allow</strong> if you want to print.</li>
            <li>You may choose to remember the decision if that fits your normal working setup.</li>
            <li>If you are unsure, ask your administrator before allowing it.</li>
          </ul>
          <div class="callout note">
            <strong>Simple rule</strong>
            If you opened the print feature on purpose and you trust your workplace setup, allowing the popup is usually the next step to continue printing.
          </div>
        </section>

        <section id="preview">
          <h2 class="section-title">13. Print Preview</h2>
          <p>The preview area shows how the selected label will appear on the page before printing.</p>
          <ul>
            <li>The selected design is shown on the preview page.</li>
            <li>This helps you confirm the size and placement.</li>
            <li>You can check whether the label looks too small, too crowded, or out of position.</li>
            <li>Review spacing, readability, and overall balance before printing.</li>
          </ul>
          <div class="callout success">
            <strong>Always preview before printing</strong>
            A quick preview can save paper, labels, and time.
          </div>
        </section>

        <section id="best-practices">
          <h2 class="section-title">14. Best Practices</h2>
          <ul>
            <li>Create the page size first.</li>
            <li>Create the label size after that.</li>
            <li>Keep important text large enough to read easily.</li>
            <li>Leave enough white space so the label does not feel crowded.</li>
            <li>Do not place too many items too close together.</li>
            <li>Check QR size before printing.</li>
            <li>Save often while you work.</li>
            <li>Use the preview before every print job.</li>
            <li>Use consistent font sizes for a cleaner result.</li>
            <li>Use batch counting only when you really want changing values across multiple stickers.</li>
          </ul>
        </section>

        <section id="mistakes">
          <h2 class="section-title">15. Common Mistakes and Fixes</h2>
          <div class="faq">
            <div class="faq-item">
              <h4>I clicked Print and nothing happened.</h4>
              <p>Check whether a permission popup appeared. If it did, read it and allow printing if appropriate.</p>
            </div>
            <div class="faq-item">
              <h4>I see a permission popup.</h4>
              <p>This can happen when printing is used for the first time. Read it and click Allow if you intend to print.</p>
            </div>
            <div class="faq-item">
              <h4>My label looks too small on the page.</h4>
              <p>Check your page size, label size, and preview. Make sure the label was created at the size you intended.</p>
            </div>
            <div class="faq-item">
              <h4>My text does not fit properly.</h4>
              <p>Select the text, reduce the font size, or move items to create more space.</p>
            </div>
            <div class="faq-item">
              <h4>My QR code looks too crowded.</h4>
              <p>Increase the QR size or include fewer values in the QR by adjusting the QR checkboxes.</p>
            </div>
            <div class="faq-item">
              <h4>I cleared my design by mistake.</h4>
              <p>Use Undo if available. If the design was already cleared and not recoverable, reopen your saved work if possible.</p>
            </div>
            <div class="faq-item">
              <h4>The wrong label is selected in print settings.</h4>
              <p>Go to Select Label and choose the correct option, such as Full Design or the main label.</p>
            </div>
          </div>
        </section>

        <section id="workflow">
          <h2 class="section-title">16. Detailed User Workflow Example</h2>
          <p>This example follows the interface shown in the screenshots and walks through a simple product label for a FAN model.</p>
          <ol class="step-list">
            <li>
              <div><strong>Check the left panel values.</strong><br>Confirm that Product Name, Serial No, and MRP are correct. Update them if needed, then click <strong>Save Form</strong>.</div>
            </li>
            <li>
              <div><strong>Create the print page.</strong><br>Click <strong>Page Size</strong> and enter the page width and height in millimetres.</div>
            </li>
            <li>
              <div><strong>Create the label.</strong><br>Click <strong>New Label</strong> and enter the label width and height. The label area appears inside the page.</div>
            </li>
            <li>
              <div><strong>Check the QR settings.</strong><br>Select the correct <strong>Node ID</strong> and choose a suitable <strong>QR Size</strong>. If Product Name, Serial No, or MRP should be included in the QR, check their QR boxes.</div>
            </li>
            <li>
              <div><strong>Format the label text.</strong><br>Click the text items one by one and adjust font family, font size, or bold if needed.</div>
            </li>
            <li>
              <div><strong>Arrange the content.</strong><br>Drag the text and QR into position. Use the move arrows for small adjustments.</div>
            </li>
            <li>
              <div><strong>Save the design.</strong><br>Click <strong>Save</strong> before going into print mode.</div>
            </li>
            <li>
              <div><strong>Open the print window.</strong><br>Click <strong>Print</strong>. If a permission popup appears, read it and allow it if you want to continue printing.</div>
            </li>
            <li>
              <div><strong>Choose the label to print.</strong><br>In <strong>Select Label</strong>, choose <strong>Full Design</strong> if you want the complete design shown in the preview.</div>
            </li>
            <li>
              <div><strong>Check the preview and print.</strong><br>Make sure the preview looks correct, set the number of copies, then click <strong>Print</strong>.</div>
            </li>
          </ol>
        </section>

        <section id="video-demo">
          <h2 class="section-title">17. Video Demo</h2>
          <p>If you prefer learning by watching, use these video demos for a quick guided tour of the Label Designer and the print flow.</p>
          <div class="cards">
            <article class="shot-card">
              <div style="aspect-ratio:16/9;border-radius:14px;border:1px solid var(--line);background:linear-gradient(135deg,#eef4ff,#f9fbff);display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                <button class="video-open-btn" data-video-src="<?= base_url('files/stickerdemos/single-sticker.mp4'); ?>" data-video-title="Single Sticker – Full Workflow" style="border:none;background:rgba(255,255,255,.96);padding:14px 18px;border-radius:999px;display:flex;align-items:center;gap:10px;font:inherit;font-weight:700;color:var(--blue);cursor:pointer;box-shadow:0 8px 22px rgba(26,115,232,.18);transition:transform .2s ease, box-shadow .2s ease;">
                  <span style="width:42px;height:42px;border-radius:50%;background:var(--blue);color:#fff;display:grid;place-items:center;font-size:16px;">▶</span>
                  Play Overview Video
                </button>
              </div>
              <div style="margin-top:14px;">
                <h3 style="margin:0 0 6px;font-size:20px;">Video 1: Single Sticker – Full Workflow</h3>
                <p style="margin:0;color:var(--muted);"> This video shows the complete workflow starting from the Sticker Management page.
                  It explains opening the Sticker Form, adding fields, creating the label design layout,
                  and finally printing a single sticker step by step.</p>
              </div>
            </article>
            <article class="shot-card">
              <div style="aspect-ratio:16/9;border-radius:14px;border:1px solid var(--line);background:linear-gradient(135deg,#f5f3ff,#fbfaff);display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                <button class="video-open-btn" data-video-src="<?= base_url('files/stickerdemos/multi-sticker.mp4'); ?>" data-video-title="Multi-Sticker Print Workflow" style="border:none;background:rgba(255,255,255,.96);padding:14px 18px;border-radius:999px;display:flex;align-items:center;gap:10px;font:inherit;font-weight:700;color:var(--purple);cursor:pointer;box-shadow:0 8px 22px rgba(124,58,237,.16);transition:transform .2s ease, box-shadow .2s ease;">
                  <span style="width:42px;height:42px;border-radius:50%;background:var(--purple);color:#fff;display:grid;place-items:center;font-size:16px;">▶</span>
                  Play Print Demo
                </button>
              </div>
              <div style="margin-top:14px;">
                <h3 style="margin:0 0 6px;font-size:20px;">Video 2: Multi-Sticker Print Workflow</h3>
                <p style="margin:0;color:var(--muted);">This video demonstrates how to use the Label Designer layout and print multiple stickers.
                  It covers preparing the design layout, selecting the label in print settings, setting the
                  count or copies, previewing the result, and printing multiple stickers.</p>
              </div>
            </article>
          </div>
          <div class="callout tip">
            <strong>Fastest way to learn</strong>
            Watch the overview video first, then watch the print demo before your first real print job.
          </div>
        </section>

        <section id="checklist">
          <h2 class="section-title">18. Final Help Section</h2>
          <p>Before printing, quickly check the following:</p>
          <ul class="checklist">
            <li><span class="check">✓</span>
              <div>Page size has been created.</div>
            </li>
            <li><span class="check">✓</span>
              <div>Label size has been created.</div>
            </li>
            <li><span class="check">✓</span>
              <div>Field values in the left panel are correct and saved.</div>
            </li>
            <li><span class="check">✓</span>
              <div>Content is aligned neatly inside the label.</div>
            </li>
            <li><span class="check">✓</span>
              <div>QR and barcode settings are correct.</div>
            </li>
            <li><span class="check">✓</span>
              <div>The design has been saved.</div>
            </li>
            <li><span class="check">✓</span>
              <div>The correct label is selected in print settings.</div>
            </li>
            <li><span class="check">✓</span>
              <div>The preview looks correct.</div>
            </li>
          </ul>
          <div class="callout success">
            <strong>You are ready to print</strong>
            Once every item in the checklist looks correct, go ahead and print with confidence.
          </div>
        </section>
      </main>
    </div>
  </div>


  <div class="video-modal" id="videoModal" aria-hidden="true">
    <div class="video-shell" role="dialog" aria-modal="true" aria-labelledby="videoModalTitle">
      <div class="video-topbar">
        <h3 id="videoModalTitle">Video Demo</h3>
        <button class="video-close" id="videoCloseBtn" aria-label="Close video">×</button>
      </div>
      <div class="video-frame">
        <video id="demoVideo" controls preload="metadata">
          <source id="demoVideoSource" src="" type="video/mp4">
          Your browser does not support video playback.
        </video>
      </div>
    </div>
  </div>




  <script>
    (() => {
      "use strict";

      // =========================
      // Table of contents active state
      // =========================
      const sections = Array.from(document.querySelectorAll("main section[id]"));
      const tocLinks = Array.from(document.querySelectorAll(".toc a"));

      function activateToc() {
        if (!sections.length || !tocLinks.length) return;

        let current = "";
        sections.forEach((section) => {
          const top = section.getBoundingClientRect().top;
          if (top <= 140) current = section.id;
        });

        tocLinks.forEach((link) => {
          link.classList.toggle("active", link.getAttribute("href") === "#" + current);
        });
      }

      document.addEventListener("scroll", activateToc, {
        passive: true
      });
      activateToc();

      // =========================
      // Search inside manual
      // =========================
      const searchInput = document.getElementById("manualSearch");
      const searchHint = document.getElementById("searchHint");
      const searchableSections = Array.from(document.querySelectorAll("main section"));

      function clearMarks(node) {
        if (!node) return;

        node.querySelectorAll('mark[data-search="1"]').forEach((mark) => {
          const text = document.createTextNode(mark.textContent || "");
          mark.replaceWith(text);
        });

        node.normalize();
      }

      function highlightText(node, term) {
        if (!node || !term) return 0;

        const walker = document.createTreeWalker(node, NodeFilter.SHOW_TEXT, null);
        const textNodes = [];
        const lowerTerm = term.toLowerCase();
        let count = 0;

        while (walker.nextNode()) {
          const textNode = walker.currentNode;
          const parentTag = textNode.parentElement ? textNode.parentElement.tagName : "";
          if (["SCRIPT", "STYLE", "MARK"].includes(parentTag)) continue;
          if (textNode.nodeValue && textNode.nodeValue.trim()) {
            textNodes.push(textNode);
          }
        }

        textNodes.forEach((textNode) => {
          const value = textNode.nodeValue || "";
          const lower = value.toLowerCase();

          if (!lower.includes(lowerTerm)) return;

          const frag = document.createDocumentFragment();
          let start = 0;
          let pos = lower.indexOf(lowerTerm, start);

          while (pos !== -1) {
            frag.appendChild(document.createTextNode(value.slice(start, pos)));

            const mark = document.createElement("mark");
            mark.setAttribute("data-search", "1");
            mark.style.background = "#fff2a8";
            mark.style.padding = "0 2px";
            mark.style.borderRadius = "4px";
            mark.textContent = value.slice(pos, pos + term.length);
            frag.appendChild(mark);

            count++;
            start = pos + term.length;
            pos = lower.indexOf(lowerTerm, start);
          }

          frag.appendChild(document.createTextNode(value.slice(start)));
          if (textNode.parentNode) {
            textNode.parentNode.replaceChild(frag, textNode);
          }
        });

        return count;
      }

      function runManualSearch(term) {
        if (!searchHint) return;

        searchableSections.forEach((section) => clearMarks(section));
        tocLinks.forEach((link) => {
          link.style.opacity = "1";
        });

        if (!term || !term.trim()) {
          searchHint.textContent =
            "Use this box for quick topic jumps, or use your browser search with Ctrl+F.";
          return;
        }

        let total = 0;
        let firstMatchSection = null;

        searchableSections.forEach((section) => {
          const matches = highlightText(section, term) || 0;
          total += matches;

          const relatedLink = document.querySelector('.toc a[href="#' + section.id + '"]');
          if (relatedLink) {
            relatedLink.style.opacity = matches ? "1" : ".45";
          }

          if (!firstMatchSection && matches > 0) {
            firstMatchSection = section;
          }
        });

        if (total > 0 && firstMatchSection) {
          searchHint.textContent =
            total + " match" + (total > 1 ? "es" : "") + " found. Jumped to the first matching section.";

          firstMatchSection.scrollIntoView({
            behavior: "smooth",
            block: "start"
          });
        } else {
          searchHint.textContent =
            "No matches found. Try a different word, or use Ctrl+F for full browser search.";
        }
      }

      if (searchInput) {
        let timer = null;

        searchInput.addEventListener("input", (e) => {
          clearTimeout(timer);
          timer = setTimeout(() => {
            runManualSearch(e.target.value);
          }, 180);
        });

        searchInput.addEventListener("focus", () => {
          searchInput.style.borderColor = "#1a73e8";
          searchInput.style.boxShadow = "0 0 0 4px rgba(26,115,232,.12)";
        });

        searchInput.addEventListener("blur", () => {
          searchInput.style.borderColor = "var(--line)";
          searchInput.style.boxShadow = "none";
        });
      }

      // =========================
      // Video modal + fullscreen
      // =========================
      const videoModal = document.getElementById("videoModal");
      const demoVideo = document.getElementById("demoVideo");
      const demoVideoSource = document.getElementById("demoVideoSource");
      const videoModalTitle = document.getElementById("videoModalTitle");
      const videoCloseBtn = document.getElementById("videoCloseBtn");
      const videoButtons = Array.from(document.querySelectorAll(".video-open-btn"));

      async function enterFullscreen(element) {
        if (!element) return;

        try {
          if (element.requestFullscreen) {
            await element.requestFullscreen();
          } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
          } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
          }
        } catch (err) {
          // Ignore fullscreen errors safely
          console.warn("Fullscreen request failed:", err);
        }
      }

      async function exitFullscreen() {
        try {
          if (document.fullscreenElement && document.exitFullscreen) {
            await document.exitFullscreen();
          }
        } catch (err) {
          console.warn("Exit fullscreen failed:", err);
        }
      }

      async function openVideo(src, title) {
        if (!videoModal || !demoVideo || !demoVideoSource) return;
        if (!src) return;

        try {
          demoVideo.pause();
          demoVideoSource.src = src;
          demoVideo.load();

          if (videoModalTitle) {
            videoModalTitle.textContent = title || "Video Demo";
          }

          videoModal.classList.add("open");
          videoModal.setAttribute("aria-hidden", "false");
          document.body.style.overflow = "hidden";

          try {
            await demoVideo.play();
          } catch (err) {
            console.warn("Autoplay failed:", err);
          }

          await enterFullscreen(demoVideo);
        } catch (err) {
          console.error("Unable to open video:", err);
        }
      }

      async function closeVideo() {
        if (!videoModal || !demoVideo) return;

        videoModal.classList.remove("open");
        videoModal.setAttribute("aria-hidden", "true");

        demoVideo.pause();
        demoVideo.currentTime = 0;
        document.body.style.overflow = "";

        await exitFullscreen();
      }

      videoButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
          openVideo(btn.dataset.videoSrc, btn.dataset.videoTitle);
        });
      });

      if (videoCloseBtn) {
        videoCloseBtn.addEventListener("click", closeVideo);
      }

      if (videoModal) {
        videoModal.addEventListener("click", (e) => {
          if (e.target === videoModal) {
            closeVideo();
          }
        });
      }

      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && videoModal && videoModal.classList.contains("open")) {
          closeVideo();
        }
      });
    })();
  </script>
</body>

</html>