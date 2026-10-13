<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Label Designer</title>
  <link rel="icon" type="image/svg+xml" href="<?= base_url('files/sticker-images/Efavicon2.svg') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <script defer src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/bwip-js/dist/bwip-js-min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
  <!-- QZ Tray -->
  <script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.5/qz-tray.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>

  <script src="<?= base_url('assets/js/jspdfcdn/jspdf.umd.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/jspdfcdn/svg2pdf.umd.min.js') ?>"></script>
  <!-- DOCX -->
  <script src="https://unpkg.com/docx@8.5.0/build/index.umd.js"></script>

  <!-- Save -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>


  <style>
    /*<<<<<<<<<<<  RESET & BASE >>>>>>>>> */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: #f8fafd;
      caret-color: #2563eb;

    }

    .pacifico-regular {
      font-family: "Pacifico", cursive;
      font-weight: 400;
      font-style: normal;
    }

    /* Hide utility */
    .hidden {
      display: none !important;
    }

    .container {
      width: 100%;
      max-width: 1350px;
      margin: 0 auto;
      padding: 0;
      box-sizing: border-box;
    }

    /*<<<<<<< MAIN LAYOUT >>>>>>>>>>>>>*/
    .responsive-shell {
      display: flex;
      gap: 4px;
    }

    /* LEFT PANEL (FORM) */
    .app-container {
      flex: 0 0 32%;
      max-width: 32%;
      background: transparent !important;
    }

    /* RIGHT PANEL (PREVIEW) */
    .label-preview {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      background: #ffffff;
      padding: 8px;
      border-radius: 14px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
      position: sticky;
      top: 10px;
      height: calc(100vh - 40px);
      overflow: hidden;
      margin-right: 16px;
    }

    /* LEFT PANEL – STATIC */
    .app-container {
      height: calc(100vh - 30px);
      display: flex;
      flex-direction: column;
      padding: 14px 10px 0px 14px;
    }

    /* ONLY CONTROLS SHOULD SCROLL */
    .controls {
      flex: 1;
      overflow-y: auto;
      padding: 6px 6px;
      scrollbar-width: thin;
      /* scrollbar safety */
    }

    /* RIGHT PANEL MUST NOT SCROLL */
    .label-preview {
      overflow: hidden;
    }




    /*<<<<<<<<<<<<<  HEADINGS  >>>>>>>>>>>>>>*/
    h1,
    h2,
    h3 {
      margin-bottom: 12px;
      color: #222;
    }

    h2 {
      font-size: 18px;
    }

    /*<<<<<<<<  FORM SECTIONS  >>>>>>>>>.*/
    .product_sec,
    .modal-content {
      background: #fff;
      padding: 16px 16px 16px 10px;
      border-radius: 14px;
      box-shadow: 0 1px 6px rgba(32, 33, 36, 0.28);
      margin-bottom: 16px;
      position: relative;
    }

    .control-group {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 14px;
      flex-wrap: wrap;
    }

    .control-group label {
      font-weight: 500;
      font-size: 13px;
      min-width: 100px;
    }

    /* BASE */
    .control-group input,
    .control-group select,
    .control-group textarea {
      flex: 1;
      padding: 8px 12px;
      font-size: 14px;
      color: #202124;

      border: 1px solid #dadce0;
      border-radius: 8px;
      background-color: #fff;

      outline: none;
      box-shadow: none;

      transition: border-color 0.2s ease,
        box-shadow 0.2s ease;
    }

    /* THICK FOCUS */
    .control-group input:focus,
    .control-group select:focus,
    .control-group textarea:focus {
      border-color: #1a73e8;
      box-shadow: 0 0 0 1px rgba(26, 115, 232, 1);
    }

    /* CLEAN SELECT */
    .control-group select {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-clip: padding-box;
    }


    .field-rows {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Base input styling */
    .field-rows .form-control {
      border: 1px solid #ccc;
      border-radius: 4px;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    /*  Focus styling */
    .field-rows .form-control:focus {
      border-color: #1a73e8 !important;
      box-shadow: 0 0 0 1px rgba(26, 115, 232, 1) !important;
      outline: none !important;
    }

    /*<<<<<<<<<<<<<<<  BUTTONS (GLOBAL) >>>>>>>>>>>>> */
    button {
      border: none;
      border-radius: 18px;
      padding: 8px 14px;
      font-size: 13px;
      font-weight: 500 !important;
      cursor: pointer;
      transition: all 0.2s ease;
      background: #007bff;
      color: #fff;
    }

    button:hover {
      opacity: 0.9;
      transform: translateY(-1px);
    }

    button.success {
      background: #28a745;
    }

    button.warning {
      background: #ffc107;
      color: #333;
    }

    /*<< ACTION BUTTON BAR  >>>>>>>>>>>*/


    .action_btns {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin-top: 22px;
    }

    .action_btns .neutral-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;

      padding: 8px 16px;

      font-size: 13.5px;
      font-weight: 500;

      background: #ffffff;
      color: #374151;

      border: 1px solid #e5e7eb;
      border-radius: 8px;

      text-decoration: none;
      cursor: pointer;

      box-shadow: 0 1px 3px rgba(0, 0, 0, .06);

      transition: all .15s ease;
    }

    .action_btns .neutral-btn:hover {
      background: #f3f4f6;
      border-color: #d1d5db;
      transform: translateY(-1px);
    }

    .action_btns .neutral-btn:active {
      transform: scale(.97);
    }


    .save-btn i {
      color: #16a34a;
    }

    .edit-btn i {
      color: #2563eb;
    }

    .action_btns i {
      font-size: 15px;
    }

    #savePreviewBtn {

      background: #1a73e8;
      background: #0b57d0;
      color: #ffffff;
      border-radius: 8px;

      padding: 8px 18px;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: .3px;

      cursor: pointer;

      /* box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, .08),
        0 3px 8px rgba(0, 0, 0, .35); */

      transition:
        transform .06s ease,
        box-shadow .15s ease,
        background .15s ease;
    }

    #savePreviewBtn:hover {
      filter: brightness(0.95);
      transform: translateY(-1px);
      transition: all .15s ease;
    }

    .manual-btn {
      background: #c6ccd2;
      color: #000;
      border: 1px solid #dadce0;
      border-radius: 8px;
      padding: 8px 18px;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: .3px;
      cursor: pointer;
      transition: transform .06s ease, box-shadow .15s ease, background .15s
    }

    .manual-btn:hover {
      filter: brightness(0.95);
      transform: translateY(-1px);
      transition: all .15s ease;
    }

    /*<<<<<<<<<<<<  PREVIEW TOOLBAR  >>>>>>>>>>>>>>*/
    .label-preview .control-group {
      justify-content: center;
      background: #f8f9fa;
      padding: 10px 12px;
      border-radius: 10px;
      border: 1px solid #e0e0e0;
    }

    #fontSizePicker {
      width: 45px;
      font-size: 12px;
    }

    #fontFamilyPicker {
      width: 120px;
    }

    #fontColorPicker {
      padding: 4px 7px;
      background: #fff;
    }

    /*MOVE BUTTONS*/
    #moveButtons {
      display: flex;
      gap: 6px;
    }

    #moveButtons button {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: #ffc107;
      color: #333;
      padding: 0;
    }

    #moveButtons button:hover {
      background: #e0a800;
    }

    /* <<<<<<<<<<<  CANVAS AREA  >>>>>>>>*/
    .preview-box {
      display: flex;
      justify-content: center;
      align-items: center;
      background: #fafafa;
      border: 2px dashed #ddd;
      border-radius: 12px;
      height: 340px;
      margin-bottom: 15px;
    }

    /* IMPORTANT: Fabric.js fix */
    .canvas-container {
      position: relative !important;
    }

    canvas {
      display: block;
      max-width: 100%;
      height: auto;
    }

    /* <<<<<<<<<<<<<  STATUS CHECK BOX >>>>>*/
    .status-box {
      width: 20px;
      height: 20px;
      border-radius: 4px;
      border: 2px solid #bbb;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      font-weight: bold;
    }

    .status-box.active {
      background: #28a745;
      border-color: #28a745;
      color: #fff;
    }

    /* <<<<<<<<<<, LABEL GRID (PRINT PAGE) >>>>>>>>>>>. */
    .labels-container {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      justify-content: center;
    }

    .label-box {
      width: 4.6cm;
      height: 4.6cm;
      border: 1px solid #000;
      background: #fff;
      overflow: hidden;
    }

    .barcode-input {
      display: flex;
      align-items: center;
      gap: 6px;
      flex: 1;
    }

    /* <<<  the mail right tool bar  >>> */

    .word-toolbar {
      display: flex;
      padding: 10px;

      background: linear-gradient(180deg,
          #ffffff 0%,
          #f8f9fd 40%,
          #eef2fb 100%);
      background: linear-gradient(180deg, #ffffff 0%, #f3f6ff 50%, #e8eefc 100%);

      border: 1px solid #dadce0;
      border-radius: 14px;

      box-shadow:
        0 1px 2px rgba(60, 64, 67, 0.08),
        0 4px 12px rgba(60, 64, 67, 0.12);
    }

    .word-toolbar:hover {

      box-shadow:
        0 1px 3px rgba(26, 115, 232, 0.25),
        0 4px 8px rgba(26, 115, 232, 0.15);
    }

    .left-tools {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      background: transparent;
      border-right: 1px solid #e0e0e0;
    }

    .middle-tools {
      background: transparent;
      margin-left: 10px;
      border-right: 1px solid #e0e0e0;
    }

    .right-tools {
      display: flex;
      flex-wrap: wrap;
      background: transparent;
      margin-left: 10px;
    }

    .printicon {
      background: #fff;
      border: 1px solid #dadce0;
      border-radius: 6px;
      color: #202124;
    }

    .printicon i {
      font-size: 25px;
      vertical-align: middle;
      color: #202124;

    }

    /* Groups */
    .toolbar-group {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 6px;
      padding-right: 6px;

    }

    .left-tool-group {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-right: 10px;
    }

    .toolbar-group:last-child {
      border-right: none;
    }

    /* Buttons */
    .tool-btn {
      height: 34px;
      min-width: 34px;
      border: 1px solid #dadce0;
      border-radius: 6px;
      background: #fff;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      transition: background .15s ease, border .15s ease;
    }

    .tool-btn:hover {
      background: #e8f0fe;
      border-color: #1a73e8;
    }

    .tool-btn.danger:hover {
      background: #e8f0fe;
      border-color: #1a73e8;
    }

    /* Selects */
    .toolbar-select {
      height: 34px;
      padding: 0 4px 0px 6px;
      border-radius: 6px;
      border: 1px solid #dadce0;
      font-size: 14px;
      background: #fff;
    }

    .toolbar-select.small {
      width: 72px;
    }

    /* focus */
    .toolbar-select:focus,
    .tool-btn:focus {
      outline: none;
      border-color: #1a73e8;
      box-shadow: 0 0 0 3px rgba(26, 115, 232, .35);
    }

    /* Color Picker */
    .color-picker {
      width: 36px;
      height: 34px;
      border: 1px solid #dadce0;
      border-radius: 6px;
      padding: 2px;
      cursor: pointer;
    }

    /* Status */
    .status-box {
      width: 14px;
      height: 14px;
      border-radius: 3px;
      border: 1px solid #ccc;
      background: #fff;
    }

    .bi {
      font-size: 16px;
      color: #5f6368;
      transition: color 0.15s ease, transform 0.15s ease;
    }

    .bi:hover {
      color: #1a73e8;
    }

    .red {
      color: #dc3545;
    }

    .green {
      color: #4caf50;
    }

    .last_btns {
      background: #5b6cff;
      color: #fff;
      border: 1px solid #dadce0;
      border-radius: 6px;
    }

    .clear_btn {
      background-color: #ea4335;
      color: #fff;
      border: 1px solid #dadce0;
      border-radius: 6px;
    }

    .clear_btn:hover {
      filter: brightness(0.95);
      transform: translateY(-1px);
      transition: all .15s ease;
    }



    .barcode_btn {
      border-radius: 6px;

    }

    /* <<<<<<<<<<<  canavs ruler properties   >>>>>>>>>>>> */

    /* OUTER FRAME – lives inside .label-preview */
    .label-preview-frame {
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      background: #fff;
      border-radius: 12px;
      border: 1px solid #dadce0;
      overflow: hidden;
      padding: 8px;
      margin-top: 10px;
    }

    .label-preview-frame:hover {
      box-shadow:
        0 0 6px rgba(26, 115, 232, 0.28),
        0 0 14px rgba(26, 115, 232, 0.18);
    }

    /* INNER SCROLL CONTAINER */
    .label-preview-box {
      flex: 1;
      position: relative;
      overflow: hidden;
      background: #f4f6fb;
    }

    /* ===== PROFESSIONAL GOOGLE-BLUE SCROLLBAR ===== */
    #workspaceWrapper::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    #workspaceWrapper::-webkit-scrollbar-track {
      background: linear-gradient(to bottom,
          #f8f9fa,
          #eef1f7);
      border-radius: 10px;
    }

    #workspaceWrapper::-webkit-scrollbar-thumb {
      background: linear-gradient(180deg,
          rgba(26, 115, 232, 0.55),
          rgba(26, 115, 232, 0.85));
      border-radius: 10px;
      border: 2px solid #f4f6fb;
      transition: background 0.2s ease, box-shadow 0.2s ease;
    }

    #workspaceWrapper::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(180deg,
          rgba(26, 115, 232, 0.8),
          #1a73e8);
      box-shadow: 0 0 6px rgba(26, 115, 232, 0.45);
    }

    #workspaceWrapper::-webkit-scrollbar-thumb:active {
      background: #174ea6;
    }

    .cursor-line-x,
    .cursor-line-y {
      position: absolute;
      pointer-events: none;
      z-index: 15;
      background: rgba(26, 115, 232, 0.6);
    }

    .cursor-line-x {
      height: 1px;
      width: 100%;
    }

    .cursor-line-y {
      width: 1px;
      height: 100%;
    }

    /* ---------- Figma-like spacing guides ---------- */

    /* SPACING GUIDE LAYER*/
    #spacingGuideLayer {
      position: absolute;
      inset: 0;
      pointer-events: none;
      z-index: 9999;
    }

    /*SPACING LINE BASE */
    .spacing-line {
      position: absolute;
      color: #1a73e8;
      pointer-events: none;
    }

    /* Measurement lines */
    .spacing-line.h {
      border-top: 1px dashed currentColor;
    }

    .spacing-line.v {
      border-left: 1px dashed currentColor;
    }

    /* ARROWS — INSIDE THE MEASUREMENT*/
    .spacing-line::before,
    .spacing-line::after {
      content: "";
      position: absolute;
      width: 0;
      height: 0;
      border: 5px solid transparent;
    }

    /* Horizontal arrows (point inward) */
    .spacing-line.h::before {
      left: 0;
      top: -5px;
      border-left: none;
      border-right-color: currentColor;
    }

    .spacing-line.h::after {
      right: 0;
      top: -5px;
      border-right: none;
      border-left-color: currentColor;
    }

    /* Vertical arrows (point inward) */
    .spacing-line.v::before {
      top: 0;
      left: -5px;
      border-top: none;
      border-bottom-color: currentColor;
    }

    .spacing-line.v::after {
      bottom: 0;
      left: -5px;
      border-bottom: none;
      border-top-color: currentColor;
    }

    /*   DISTANCE LABEL */
    .spacing-label {
      position: absolute;
      background: #1a73e8;
      color: #ffffff;
      font-size: 10px;
      font-weight: 500;
      padding: 2px 6px;
      border-radius: 999px;
      white-space: nowrap;
      pointer-events: none;
      box-shadow: 0 2px 6px rgba(26, 115, 232, 0.35);
    }

    /* Horizontal label (above, centered) */
    .spacing-line.h+.spacing-label {
      transform: translate(-50%, -120%);
    }

    /* Vertical label (close to line, centered) */
    .spacing-line.v+.spacing-label {
      transform: translate(-105%, -50%);
    }

    /* SMALL DISTANCE SAFETY*/
    .spacing-line.small-distance::before,
    .spacing-line.small-distance::after {
      display: none;
    }

    /*<<<<<<<<<< DRAW GUIDE ACTIONS >>>>>>>>>>>>> */

    .draw_guide_actions {
      position: sticky;
      top: 30px;
      left: 38px;
      display: inline-flex;
      align-items: center;
      gap: 0px;
      background: #ffffff;
      border-radius: 14px;
      box-shadow: 0 4px 12px rgba(60, 64, 67, 0.15);
      padding: 6px;
      z-index: 50;
      transition: box-shadow 0.25s ease;
    }

    .draw_guide_actions:hover {
      box-shadow: 0 6px 18px rgba(60, 64, 67, 0.22);
    }

    /*<<<< GUIDE TOGGLE BUTTON >>>>*/

    .draw_guide_actions button {
      background: none;
    }

    .guide-toggle-btn {
      border: none;
      background: linear-gradient(135deg, #1a73e8, #4285f4);
      padding: 5px;
      border-radius: 50%;
      cursor: pointer;
      display: grid;
      place-items: center;
      color: #ffffff;
      box-shadow:
        0 2px 6px rgba(26, 115, 232, 0.35),
        inset 0 -1px 0 rgba(255, 255, 255, 0.25);
      transition:
        transform 0.25s ease,
        box-shadow 0.25s ease,
        background 0.3s ease;
    }

    /* SVG */
    .guide-toggle-btn svg {
      width: 14px;
      height: 14px;
      transition: transform 0.3s ease;
    }

    /* Hover */
    .guide-toggle-btn:hover {
      transform: translateY(-1px);
      box-shadow:
        0 6px 14px rgba(26, 115, 232, 0.45),
        inset 0 -1px 0 rgba(255, 255, 255, 0.35);
    }

    /* Active (click) */
    .guide-toggle-btn:active {
      transform: scale(0.95);
    }

    /* Rotated when panel open */
    .guide-toggle-btn.active {
      background: #e8f0fe;
      color: #1a73e8;
      box-shadow:
        0 0 0 2px rgba(26, 115, 232, 0.15),
        0 2px 6px rgba(26, 115, 232, 0.25);
    }

    .guide-toggle-btn.active svg {
      transform: rotate(90deg);
    }

    /* Subtle pulse on first load */
    @keyframes guidePulse {
      0% {
        box-shadow: 0 0 0 0 rgba(26, 115, 232, 0.45);
      }

      100% {
        box-shadow: 0 0 0 10px rgba(26, 115, 232, 0);
      }
    }

    .guide-toggle-btn {
      animation: guidePulse 1.4s ease-out 1;
    }


    /* Panel */
    .guide-panel {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 0 6px 0 12px;
      max-width: 260px;
      overflow: hidden;
      opacity: 1;
      transition: max-width 0.35s ease, opacity 0.25s ease;
    }

    /* Collapsed via toggle button */
    .draw_guide_actions:not(.open) .guide-panel {
      max-width: 0;
      opacity: 0;
      padding: 0;
    }

    /* Auto-hide on scroll (ICON STAYS) */
    .draw_guide_actions.panel-hide .guide-panel {
      max-width: 0;
      opacity: 0;
      padding: 0;
    }

    /* Items */
    .guide-item {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 10px;
      font-weight: 500;
      color: #202124;
      white-space: nowrap;
    }

    .guide-item select {
      border-radius: 6px;
      border: 1px solid #dadce0;
      padding: 3px 4px;
      font-size: 10px;
      background: #fff;
    }

    .guide-item input[type="checkbox"] {
      accent-color: #1a73e8;
    }

    /* ---------- Draggable label wrappers ---------- */
    /* wrappers are created dynamically inside printPageBox */
    #printPageBox .label-wrapper,
    #printPageBox .draggable,
    .label-preview-box .label-wrapper {
      cursor: grab;
    }

    /* ---------- While dragging ---------- */
    #printPageBox .dragging,
    .label-preview-box .dragging {
      cursor: grabbing !important;
    }

    /* ---------- Text editing ---------- */
    #printPageBox .text-element,
    .label-preview-box .text-element {
      cursor: text;
    }

    /* ---------- Resize handles (if present) ---------- */
    #printPageBox .resize-handle,
    .label-preview-box .resize-handle {
      cursor: nwse-resize;
    }

    /* ---------- Custom Figma-style crosshair ---------- */
    /* #printPageBox,
    .label-preview-box {
      cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32'%3E%3Cline x1='16' y1='0' x2='16' y2='32' stroke='%231a73e8' stroke-width='1'/%3E%3Cline x1='0' y1='16' x2='32' y2='16' stroke='%231a73e8' stroke-width='1'/%3E%3C/svg%3E") 16 16,
        crosshair;
    } */



    /* ===== SCREEN BLOCKER ===== */

    #screenBlocker {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.75);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 999999;
      backdrop-filter: blur(4px);
    }

    /* Modal box */

    .screenBlockerBox {
      background: #ffffff;
      padding: 35px 40px;
      border-radius: 14px;
      text-align: center;
      max-width: 420px;
      width: 90%;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
      animation: screenBlockFade 0.3s ease;
    }

    /* Icon */

    .blockIcon i {
      font-size: 42px;
      color: #2563eb;
      margin-bottom: 10px;
    }

    /* Image */

    .block-image img {
      width: 120px;
      opacity: 0.9;
    }

    /* Heading */

    .screenBlockerBox h2 {
      font-size: 20px;
      margin-bottom: 8px;
      color: #111827;
    }

    /* Text */

    .screenBlockerBox p {
      font-size: 14px;
      color: #6b7280;
      line-height: 1.5;
    }

    /* Disable background scrolling */

    body.screen-blocked {
      overflow: hidden;
    }

    /* Animation */

    @keyframes screenBlockFade {
      from {
        transform: scale(0.95);
        opacity: 0;
      }

      to {
        transform: scale(1);
        opacity: 1;
      }
    }




    /* ===== Modern-Style Print UI ===== */
    .gprint-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(32, 33, 36, 0.6);
      backdrop-filter: blur(4px);
      z-index: 99999;
      display: none;
      justify-content: center;
      align-items: center;
      font-family: 'Roboto', 'Segoe UI', Arial, sans-serif;
    }

    .gprint-dialog {
      width: 90vw;
      max-width: 1100px;
      height: 85vh;
      background: #fff;
      border-radius: 8px;
      display: flex;
      overflow: hidden;
      box-shadow: 0 24px 38px 3px rgba(0, 0, 0, 0.14), 0 9px 46px 8px rgba(0, 0, 0, 0.12), 0 11px 15px -7px rgba(0, 0, 0, 0.2);
      animation: gModalIn 0.2s cubic-bezier(0, 0, 0.2, 1);
    }

    @keyframes gModalIn {
      from {
        transform: scale(0.95);
        opacity: 0;
      }

      to {
        transform: scale(1);
        opacity: 1;
      }
    }

    .gprint-sidebar {
      width: 230px;
      padding: 20px 15px;
      border-right: 1px solid #dadce0;
      display: flex;
      flex-direction: column;
      background: #fff;
      overflow-y: auto;

    }

    .gprint-sidebar {
      scroll-behavior: smooth;
      scrollbar-width: thin;
      scrollbar-color: rgba(26, 115, 232, 0.6) transparent;
    }


    .gprint-sidebar h2 {
      margin: 0 0 15px;
      font-size: 22px;
      font-weight: 400;
      color: #3c4043;
    }

    /* Width */
    .gprint-sidebar::-webkit-scrollbar {
      width: 8px;
    }

    /* Track */
    .gprint-sidebar::-webkit-scrollbar-track {
      background: transparent;
    }

    /* Thumb */
    .gprint-sidebar::-webkit-scrollbar-thumb {
      background: linear-gradient(180deg,
          rgba(26, 115, 232, 0.35),
          rgba(26, 115, 232, 0.6));
      border-radius: 20px;
      backdrop-filter: blur(6px);
      transition: all 0.3s ease;
    }

    /* Hover effect */
    .gprint-sidebar::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(180deg,
          rgba(26, 115, 232, 0.6),
          rgba(26, 115, 232, 0.9));
    }

    /* Active press */
    .gprint-sidebar::-webkit-scrollbar-thumb:active {
      background: rgba(26, 115, 232, 1);
    }

    .g-field {
      margin-bottom: 10px;
    }

    .g-field label,
    .size-field label {
      font-size: 13px;
      color: #5f6368;
      margin-bottom: 8px;
      display: block;
      font-weight: 500;
    }

    .size-field span {
      font-size: 13px;
      color: #5f6368;
      font-weight: 400;
    }

    .g-field select,
    .g-field input {
      width: 100%;
      padding: 5px;
      border: 1px solid #dadce0;
      border-radius: 4px;
      font-size: 14px;
      color: #3c4043;
      transition: border-color 0.2s;
    }

    .g-field select:focus,
    .g-field input:focus {
      outline: none;
      border-color: #1a73e8;
      box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
    }

    .g-hint {
      font-size: 12px;
      color: #70757a;
      margin-top: 6px;
    }

    .g-actions {
      /* margin-top: auto; */
      display: flex;
      justify-content: space-between;
      gap: 12px;
      padding-top: 20px;
    }

    .btn {
      padding: 8px 24px;
      border-radius: 4px;
      font-size: 14px;
      cursor: pointer;
      font-weight: 500;
      border: 1px solid transparent;
    }

    .btn.primary {
      background: #1a73e8;
      color: #fff;
    }

    .btn.primary:hover {
      background: #1765cc;
    }

    .btn.ghost {
      background: #fff;
      border-color: #dadce0;
      color: #1a73e8;
    }

    .btn.ghost:hover {
      background: #f8f9fa;
    }

    .gprint-preview {
      flex: 1;
      background: #f1f3f4;
      padding: 20px;
      overflow-y: auto;
      display: flex;
      justify-content: center;
      overflow-x: hidden;
      overflow-y: auto;
    }

    #printPreviewContainer {
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
      background: #fff;
      height: fit-content;
    }

    .paper-page img {
      display: block;
      width: 100%;
      height: auto;
    }

    .tool-btn svg {
      width: 14px;
      height: 14px;
      font-size: 20px;
      fill: #5f6368;
      color: #5f6368;
    }

    .tool-btn svg:hover {
      fill: #007bff;
      color: #007bff;
    }

    .lib-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .65);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }

    .hidden {
      display: none;
    }

    .lib-modal {
      width: 85%;
      height: 85%;
      background: #fff;
      border-radius: 16px;
      display: flex;
      flex-direction: column;
      box-shadow: 0 10px 40px rgba(0, 0, 0, .3);
    }

    .lib-header {
      padding: 12px 18px;
      border-bottom: 1px solid #eee;
      font-weight: 600;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }


    .lib-content {
      flex: 1;
      padding: 20px;
      overflow: auto;
    }

    .no-images {
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #999;
    }

    .no-images i {
      font-size: 60px;
      margin-bottom: 10px;
    }

    .img-card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .lib-bottom {
      padding: 12px;
      border-top: 1px solid #eee;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .left-lib-btns,
    .right-lib-btns {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 20px;
    }

    .lib-actions-1 {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 2px;
    }

    .lib-actions-2 {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }


    .gbtn {
      padding: 8px 14px;
      border-radius: 8px;
      border: 1px solid #ddd;
      background: #f8f9fa;
      cursor: pointer;
      color: #000;
    }

    .uploadbtn {
      padding: 9.5px 14px;
      border-radius: 0px 8px 8px 0px;
      cursor: pointer;
      background-color: #1a73e8;
      color: #fff;
      margin: 0;

    }

    .primary {
      background: #4285f4;
      color: #fff;
      border: none;
    }

    .upload-preview {
      display: flex;
      justify-content: start;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      padding: 10px;
      overflow-y: auto;
      overflow-x: hidden;
      max-height: 200px;
    }


    .lib-close {
      background: transparent;
      color: none;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      padding: 0;
    }

    /* SVG icon */
    .lib-close svg {
      width: 30px;
      height: 30px;
      fill: currentColor;
      color: #1a73e8;
    }

    .select-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      background-color: #1a73e8;
      color: #fff;
      padding: 8px 10px;
      border-radius: 8px 0px 0px 8px;
      transition: all 0.2s ease;
    }

    /* Hide the actual file input */
    .select-btn input {
      display: none;
    }

    /* Icon sizing only */
    .select-btn svg {
      width: 18px;
      height: 18px;
      fill: #fff;

    }

    /* Hover */
    .select-btn:hover {
      background-color: #1669c1;
      transform: translateY(-1px);
    }

    /* Click press */
    .select-btn:active {
      transform: scale(0.95);
    }

    /* GRID */
    .img-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
      gap: 16px;
      padding: 10px;
    }

    /* CARD */
    .img-card {
      position: relative;
      border-radius: 14px;
      overflow: hidden;
      background: #fff;
      cursor: pointer;
      transition: all 0.18s ease;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    /* IMAGE WRAPPER */
    .img-wrap {
      width: 100%;
      height: 95px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f7f7f7;
    }

    .img-wrap img {
      max-width: 85%;
      max-height: 85%;
      object-fit: contain;
      transition: transform 0.18s ease;
    }

    /* HOVER (Google style) */
    .img-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 22px rgba(0, 0, 0, 0.15);
    }

    .img-card:hover img {
      transform: scale(1.05);
    }

    /* SELECTED STATE */
    .img-card.selected {
      outline: 2px solid #2563eb;
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
    }

    /* CHECK ICON */
    .img-check {
      position: absolute;
      top: 6px;
      right: 6px;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: #2563eb;
      color: #fff;
      font-size: 13px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transform: scale(0.6);
      transition: all 0.18s ease;
    }

    /* show when selected */
    .img-card.selected .img-check {
      opacity: 1;
      transform: scale(1);
    }

    .select-placeholder {
      color: #9e9e9e;
    }

    .select-placeholder.valid {
      color: #000;
    }

    .select-cunt {
      font-size: 14px;
      font-weight: 400;
      color: #666;
    }

    .lib-photo-logo {
      display: block;
      width: 40px;
      height: 40px;
    }

    .lib-photo-logo img {
      display: inline-flex;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .lib-title {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
    }


    /* Container */
    .custom-size-box {
      display: none;
      flex-direction: column;
      gap: 6px;
      margin-top: 6px;
      margin-bottom: 8px;
    }

    /* Row for inputs */
    .size-input-row {
      display: flex;
      gap: 6px;
    }

    /* Small width & height inputs */
    .size-input-row input {
      width: 90px;
      height: 28px;
      padding: 4px;
      font-size: 12px;
      border: 1px solid #ccc;
      border-radius: 4px;
      outline: none;
      transition: border 0.15s ease, box-shadow 0.15s ease;
    }

    /* Google-like blue focus border */
    .size-input-row input:focus {
      border: 2px solid #1a73e8;
      box-shadow: 0 0 0 1px rgba(26, 115, 232, 0.15);
    }

    /* Small black Apply button */
    .apply-btn {
      align-self: flex-start;
      padding: 4px 10px;
      font-size: 12px;
      background: #000;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: transform 0.1s ease, opacity 0.15s ease;
    }

    /* Click press effect */
    .apply-btn:active {
      transform: scale(0.96);
    }

    /* Hover */
    .apply-btn:hover {
      opacity: 0.9;
    }

    /* Container */
    .content-box {
      height: 110px;
      overflow-y: auto;
      overflow-x: hidden;
      border: 1px solid #dadce0;
      border-radius: 8px;
      padding: 6px 0;
      background: #fff;
      font-family: "Roboto", Arial, sans-serif;
      margin: 8px 0px;
      scrollbar-width: thin;
      padding: 10px;
      display: grid;
      font-size: 13px;
      color: #5f6368;
      font-weight: 400;
    }

    /* Each row */
    .content-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 7px 12px;
      cursor: pointer;
      transition: background 0.18s ease;
    }

    /* Hover like Google */
    .content-row:hover {
      background: #f1f3f4;
    }

    /* Text */
    .content-text {
      font-size: 13px;
      color: #202124;
    }

    /* Checkbox (Google feel) */
    .google-check {
      width: 16px;
      height: 16px;
      accent-color: #1a73e8;
      cursor: pointer;
    }

    /* Scrollbar styling */
    .content-box::-webkit-scrollbar {
      width: 6px;
    }

    .content-box::-webkit-scrollbar-thumb {
      background: #dadce0;
      border-radius: 10px;
    }

    .content-box::-webkit-scrollbar-thumb:hover {
      background: #c6c6c6;
    }

    .switch {
      position: relative;
      display: inline-block;
      width: 48px;
      height: 24px;
    }

    .switch input {
      display: none;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      inset: 0;
      background-color: #ccc;
      border-radius: 24px;
      transition: .3s;
    }

    .slider:before {
      content: "";
      position: absolute;
      height: 18px;
      width: 18px;
      left: 3px;
      top: 3px;
      background-color: white;
      border-radius: 50%;
      transition: .3s;
    }

    input:checked+.slider {
      background-color: #3b82f6;
    }

    input:checked+.slider:before {
      transform: translateX(24px);
    }

    .alignment_toggle {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .alignment_toggle span {
      font-size: 13px;
      color: #5f6368;
      font-weight: 400;
    }

    .alignment_toggle label {
      margin: 0;
    }

    .c-q_head {
      display: flex;
      justify-content: flex-end;
      gap: 2px;
      font-size: 10px;
      font-weight: 600;
      color: #333;
      position: absolute;
      right: 8px;
      top: 15px;
    }


    .ruler-wrapper {
      display: flex;
      flex-direction: column;
    }

    .ruler-body {
      display: flex;
    }

    .ruler {
      background: #f5f5f5;
    }

    .ruler-top {
      height: 20px;
    }

    .ruler-left {
      width: 20px;
    }

    #printPageBox {
      position: relative;
    }

    /* print modal css */
    .print-modal {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }

    .print-modal-content {
      width: 90%;
      height: 90%;
      background: #ffffff;
      display: flex;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
    }

    .print-controls {
      width: 290px;
      background: #ffffff;
      padding: 20px;
      overflow-y: auto;
      border-right: 2px solid #e5e7eb;
    }

    .print-controls .action-buttons {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .print-title {
      color: #000000d8;
      margin-bottom: 20px;
    }

    #applyCustomSize,
    #applyCustomPosition {
      background-color: #818a94d1;
      font-size: 10px;
      padding: 8px 10px;
    }

    .form-group {
      margin-bottom: 16px;
      display: flex;
      flex-direction: column;
    }

    .print-controls select,
    .print-controls input {
      padding: 6px;
      border-radius: 6px;
      border: 1px solid #d1d5db;
      font-size: 13px;

    }

    .controlfocus {
      margin-bottom: 10px;
    }

    .controlfocus input:focus,
    .controlfocus select:focus,
    .controlfocus textarea:focus {
      border-color: #1a73e8 !important;
      box-shadow: 0 0 0 1px rgba(26, 115, 232, 1) !important;
    }

    .print-controls button {
      padding: 8px 10px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      font-size: 13px;
    }

    .content-input {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      gap: 10px;

    }

    .content-input button {
      background-color: #818a94d1;
      font-size: 10px;
      margin-top: 5px;
    }

    .content-input i {
      font-size: 12px;
      color: #fff;
    }

    #applyCountLimit button {
      background-color: #818a94d1;
      font-size: 10px;
    }

    .print-btn {
      background: linear-gradient(135deg, #6366f1, #2563eb);
      color: white;
      padding: 10px 40px;
      width: 120px;
    }

    .cancel-btn {
      background: #374151;
      color: white;
      padding: 10px 40px;
      width: 120px;
    }

    .print-preview {
      flex: 1;
      background: #00000073;
      display: flex;
      align-items: flex-start;
      /*FIX */
      justify-content: center;
      overflow: auto;
      padding: 20px 0;

    }

    #previewContainer {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10mm;
      padding: 10px;
    }

    .preview-placeholder {
      display: flex;
      justify-content: center;
      align-items: center;
      background: transparent;
      overflow: auto;
    }

    .dropdown-box {
      background: white;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      max-height: 150px;
      overflow-y: auto;
      padding: 6px;
    }

    .hidden {
      display: none;
    }

    /*  FONT GROUP STYLING  */

    #fontFamilyPicker optgroup {
      background-color: #eaf4ff;
      color: #1e3a8a;
      font-weight: 600;
    }

    #fontFamilyPicker {
      border: 1px solid #dbeafe;
      background-color: #ffffff;
    }

    #fontFamilyPicker option {
      background-color: #ffffff;
    }

    .batch-page {
      flex-shrink: 0;
    }

    /* dropdown container */
    #labelDropdown {
      display: flex;
      flex-direction: column;
      gap: 6px;
      padding: 6px;
    }

    /* each row */
    .label-row {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 6px 10px;
      border-radius: 6px;
      background: #eff6ff;
      cursor: pointer;
      transition: background 0.15s ease;
    }

    /* hover effect */
    .label-row:hover {
      background: #dbeafe;
    }

    /* checkbox alignment */
    .label-row input[type="checkbox"] {
      width: 12px;
      height: 12px;
      cursor: pointer;
    }

    /* label text */
    .label-row label {
      font-size: 12px;
      font-weight: 500;
      color: #1f2937b3;
      cursor: pointer;

    }

    /* Dropdown container */
    .print-controls select {
      width: 100%;
      padding: 8px 10px;
      border-radius: 6px;
      border: 1px solid #cbd5e1;
      background: #fff;
      color: #1f2937b3;
      font-size: 12px;
      font-weight: 500;
      outline: none;
      cursor: pointer;
      transition: all 0.15s ease;
    }

    /* hover */
    .print-controls select:hover {
      background: #dbeafe;
      border-color: #93c5fd;
    }

    /* focus */
    .print-controls select:focus {
      background: #dbeafe;
      border-color: #60a5fa;
      box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.25);
    }

    /* dropdown options */
    .print-controls select option {
      background: #eff6ff;
      color: #1f2937b3;
      padding: 6px;
    }

    /* option hover (works in some browsers) */
    .print-controls select option:hover {
      background: #dbeafe;
    }

    .radio-group label {
      font-size: 12px;
      color: #1f2937b3;
      font-weight: 500;
    }

    .label-head {
      font-size: 14px;
      margin-bottom: 8px;
    }

    .pagesize,
    .m-label,
    .c-label {
      /* border: 1px solid #dadce0 !important; */
      border-radius: 6px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background .15s ease, border .15s ease;
    }

    .pagesize {
      padding: 8px 6px;
      display: block;
      font-size: 10px;
      background-color: #fef7e0;
      border-radius: 18px;
      color: #b06000;
    }

    .pagesize:hover {
      filter: brightness(0.95);
      transform: translateY(-1px);
      transition: all .15s ease;
    }

    .m-label,
    .c-label {
      padding: 4px 10px;
      font-size: 10px;
      gap: 6px;
      border-radius: 18px;
    }

    .m-label {
      background: #e8f0fe;
      color: #1a73e8;
    }

    .m-label:hover {
      filter: brightness(0.95);
      transform: translateY(-1px);
      transition: all .15s ease;
    }

    .m-label i {
      color: #1a73e8;
    }

    .c-label {
      background: #f3e8fd;
      color: #7b1fa2;
    }

    .c-label:hover {
      filter: brightness(0.95);
      transform: translateY(-1px);
      transition: all .15s ease;
    }

    .c-label i {
      color: #7b1fa2;
    }

    .pagesize i {
      font-size: 30px;
      color: #b06000;
    }

    .m-label i,
    .c-label i {
      font-size: 22px;
      margin-right: 3px;

    }

    .main-copy {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      gap: 3px;
    }

    .left_title {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      gap: 8px;
      position: relative;
    }

    .image-logo {
      width: 90px;
      height: 90px;
      position: absolute;
      top: -28px;
      left: 220px;
    }

    .image-logo img {
      width: 100%;
      height: 100%;
      display: block;
      object-fit: contain;
    }

    .radio-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .custom-row {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .btn-small {
      padding: 4px 10px;
      font-size: 12px;
      cursor: pointer;
    }

    .hidden {
      display: none;
    }

    .label-selected rect[data-boundary="true"] {
      stroke: #EF4444;
      stroke-width: 0.8;
    }

    /* ================= PRINT PREVIEW WARNING ================= */

    .print-preview-warning-overlay {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      pointer-events: none;
    }

    .print-warning-card {

      width: 320px;
      background: #ffffff;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
      padding: 16px 18px 12px 18px;

      position: relative;

      font-family: system-ui;
      color: #111827;

      animation: warningFadeIn .2s ease;
    }

    @keyframes warningFadeIn {
      from {
        opacity: 0;
        transform: translateY(-6px)
      }

      to {
        opacity: 1;
        transform: translateY(0)
      }
    }

    .print-warning-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 6px;
    }

    .print-warning-icon {
      width: 22px;
      height: 22px;
    }

    .print-warning-message {
      font-size: 13px;
      line-height: 1.4;
    }

    /* close button */

    .print-warning-close {
      position: absolute;
      top: 6px;
      right: 8px;
      border: none;
      background: none;
      cursor: pointer;
      padding: 2px;
    }

    .print-warning-close svg {
      width: 16px;
      height: 16px;
      stroke: #ef4444;
    }

    /* progress bar */

    .print-warning-progress {

      margin-top: 10px;
      height: 3px;
      border-radius: 4px;
      overflow: hidden;
      background: #e5e7eb;
    }

    .print-warning-progress-bar {

      height: 100%;
      width: 100%;

      background: linear-gradient(90deg,
          #2563eb 0%,
          #4f46e5 40%,
          #7c3aed 70%,
          #a855f7 100%);
      animation: warningProgress 2s linear forwards;
    }

    @keyframes warningProgress {
      from {
        width: 100%
      }

      to {
        width: 0%
      }
    }


    /* zoom  */
    /* ===== ZOOM INDICATOR ===== */

    .zoom-indicator {
      position: absolute;
      bottom: 18px;
      left: 50%;
      transform: translateX(-50%) scale(0.9);

      display: flex;
      align-items: center;
      gap: 10px;

      background: rgba(17, 24, 39, 0.92);
      color: #fff;

      padding: 8px 14px;
      border-radius: 10px;

      font-family: Inter, sans-serif;
      font-size: 13px;
      font-weight: 500;

      backdrop-filter: blur(8px);

      box-shadow:
        0 6px 20px rgba(0, 0, 0, 0.25);

      opacity: 0;
      pointer-events: none;

      transition:
        opacity .25s ease,
        transform .25s ease;
    }

    .zoom-indicator.show {
      opacity: 1;
      transform: translateX(-50%) scale(1);
      pointer-events: auto;
    }

    .zoom-indicator button {
      border: none;
      background: #374151;
      color: #fff;

      /* width: 28px;
      height: 28px; */
      padding: 4px 14px;

      border-radius: 6px;
      cursor: pointer;

      font-size: 14px;
      font-weight: 600;

      display: flex;
      align-items: center;
      justify-content: center;

      transition: background .2s;
    }


    .zoom-indicator button:hover {
      background: #4b5563;
    }

    #zoomPercent {
      min-width: 50px;
      text-align: center;
      font-weight: 600;
    }

    .zoom-indicator {
      pointer-events: auto;
    }

    .zoom-indicator button {
      cursor: pointer;
    }

    .clear-modal {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.35);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }

    .clear-modal-box {
      background: #fff;
      padding: 22px 26px;
      border-radius: 10px;
      width: 300px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      font-family: Inter, sans-serif;
    }

    .clear-title {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .clear-message {
      font-size: 14px;
      color: #555;
      margin-bottom: 18px;
    }

    .clear-actions {
      display: flex;
      justify-content: center;
      gap: 12px;
    }

    .clear-no,
    .clear-yes {
      padding: 6px 16px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
    }

    .clear-no {
      background: #00000061;
    }

    .clear-yes {
      background: #ef4444;
      color: white;
    }


    /* ===== Dialog Overlay ===== */
    .dialog-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.35);
      backdrop-filter: blur(2px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 10000;
      animation: fadeIn 0.18s ease;
    }

    /* ===== Dialog Box ===== */
    .dialog-box {
      width: 340px;
      background: #ffffff;
      border-radius: 12px;
      padding: 26px;
      box-shadow: 0 12px 35px rgba(0, 0, 0, 0.18);
      font-family: Inter, sans-serif;
      animation: dialogPop 0.18s ease;
    }

    /* Title */
    .dialog-box h3 {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 16px;
      color: #111827;
    }

    /* Labels */
    .dialog-box label {
      font-size: 13px;
      color: #6b7280;
      display: block;
      margin-top: 10px;
    }

    /* Inputs */
    .dialog-box input {
      width: 100%;
      padding: 9px 10px;
      margin-top: 4px;
      border-radius: 8px;
      border: 1px solid #e5e7eb;
      font-size: 14px;
      transition: all .15s ease;
    }

    .dialog-box input:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }

    /* Buttons area */
    .dialog-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 22px;
    }

    /* Buttons */
    .dialog-actions button {
      padding: 7px 16px;
      border-radius: 8px;
      border: none;
      font-size: 13px;
      cursor: pointer;
      transition: all .15s ease;
    }

    /* Cancel */
    .dialog-actions button:last-child {
      background: #f3f4f6;
      color: #374151;
    }

    .dialog-actions button:last-child:hover {
      background: #e5e7eb;
    }

    /* Confirm */
    .dialog-actions button:first-child {
      background: #2563eb;
      color: white;
    }

    .dialog-actions button:first-child:hover {
      background: #1d4ed8;
    }

    /* Animations */

    @keyframes dialogPop {
      from {
        transform: scale(.92);
        opacity: 0;
      }

      to {
        transform: scale(1);
        opacity: 1;
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0
      }

      to {
        opacity: 1
      }
    }


    .input-mm {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-mm span {
      position: absolute;
      right: 28px;
      top: 15px;
      font-size: 12px;
      color: #6b7280;
      pointer-events: none;
    }

    .label-preview-frame {
      position: relative;
      width: 100%;
      height: 100%;
      overflow: hidden;
    }

    .label-preview-box {
      position: relative;
      width: 100%;
      height: 100%;
      overflow: hidden;
    }

    #workspaceWrapper {
      position: absolute;
      inset: 0;
      overflow: auto;
      width: 100%;
      height: 100%;
      user-select: none;
      -webkit-user-select: none;
      /* overscroll-behavior: contain; */
      cursor: default;
    }

    #workspaceWrapper.is-panning {
      cursor: grabbing;
    }

    /* IMPORTANT: do NOT force SVG to fit parent */
    #workspaceSvg {
      display: block;
      shape-rendering: geometricPrecision;
      text-rendering: geometricPrecision;
      background: transparent;
    }

    #workspaceGrid line,
    #workspaceRuler line,
    #guideLayer line,
    #selectionLayer line,
    #printPageGroup line,
    #printPageGroup rect,
    #printPageGroup path,
    #printPageGroup circle,
    #printPageGroup ellipse,
    #printPageGroup polygon,
    #printPageGroup polyline {
      vector-effect: non-scaling-stroke;
    }









    /* ================= LABEL QUICK MENU ================= */
    .label-quick-menu {
      position: fixed;
      min-width: 170px;
      padding: 8px;
      border-radius: 18px;
      background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.94));
      border: 1px solid rgba(148, 163, 184, 0.22);
      box-shadow:
        0 22px 60px rgba(15, 23, 42, 0.22),
        0 8px 24px rgba(15, 23, 42, 0.12);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      z-index: 999999;
      display: none;
      animation: labelQuickMenuIn 0.18s ease;
    }

    @keyframes labelQuickMenuIn {
      from {
        opacity: 0;
        transform: translateY(8px) scale(0.96);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .label-quick-menu.show {
      display: block;
    }

    .label-quick-item {
      width: 100%;
      border: 0;
      background: transparent;
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 6px 10px;
      border-radius: 14px;
      cursor: pointer;
      color: #111827;
      font-size: 13px;
      font-weight: 600;
      transition: background 0.18s ease, transform 0.18s ease, color 0.18s ease;
    }

    .label-quick-item:hover {
      background: linear-gradient(135deg, rgba(37, 99, 235, 0.09), rgba(99, 102, 241, 0.08));
      color: #1d4ed8;
    }

    .label-quick-item:active {
      transform: scale(0.985);
    }

    .label-quick-icon {
      width: 25px;
      height: 25px;
      border-radius: 11px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #1d4ed8;
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.14), rgba(99, 102, 241, 0.14));
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
    }

    /* ================= LABEL RESIZE MODAL ================= */
    .label-resize-overlay {
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }

    .label-resize-box {
      width: min(430px, calc(100vw - 32px));
      background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(248, 250, 252, 0.96));
      border: 1px solid rgba(148, 163, 184, 0.22);
      border-radius: 24px;
      box-shadow:
        0 30px 80px rgba(15, 23, 42, 0.28),
        0 12px 28px rgba(15, 23, 42, 0.12);
      padding: 22px;
      animation: labelResizeIn 0.22s ease;
    }

    @keyframes labelResizeIn {
      from {
        opacity: 0;
        transform: translateY(12px) scale(0.975);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .label-resize-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 14px;
      margin-bottom: 16px;
    }

    .label-resize-head h3 {
      margin: 0;
      font-size: 16px;
      line-height: 1.15;
      color: #0f172a;
      font-weight: 700;
    }

    .label-resize-head p {
      margin: 6px 0 0;
      font-size: 10px;
      color: #64748b;
    }

    .label-resize-close {
      border: 0;
      width: 36px;
      height: 36px;
      border-radius: 999px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #334155;
      background: linear-gradient(180deg, #ffffff, #f1f5f9);
      box-shadow: 0 6px 14px rgba(15, 23, 42, 0.08);
    }

    .label-resize-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
    }

    .label-resize-field label {
      display: block;
      margin-bottom: 7px;
      font-size: 12px;
      font-weight: 700;
      color: #334155;
    }

    .premium-mm-input {
      border-radius: 16px;
      overflow: hidden;
      border: 1px solid rgba(148, 163, 184, 0.28);
      background: rgba(255, 255, 255, 0.88);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
    }

    .premium-mm-input input {
      border: 0;
      outline: none;
      background: transparent;
      font-weight: 700;
      color: #0f172a;
      padding: 8px 14px;
      font-size: 12px;
    }



    .premium-mm-input span {
      color: #475569;
      font-weight: 700;
      position: absolute;
      right: 20px;
      top: 6px;
      font-size: 12px;
      pointer-events: none;
    }

    .label-resize-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 18px;
    }

    .label-btn {
      min-width: 96px;
      border: 0;
      border-radius: 14px;
      padding: 8px 16px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      transition: transform 0.18s ease, box-shadow 0.18s ease, opacity 0.18s ease;
    }

    .label-btn:hover {
      transform: translateY(-1px);
    }

    .label-btn.ghost {
      color: #334155;
      background: linear-gradient(180deg, #ffffff, #f1f5f9);
      box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
    }

    .label-btn.primary {
      color: #fff;
      background: linear-gradient(135deg, #2563eb, #4f46e5);
      box-shadow: 0 14px 30px rgba(37, 99, 235, 0.32);
    }

    .toast-container {
      position: fixed;
      top: 50%;
      right: 30px;
      transform: translateY(-50%);
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 10px;
      z-index: 999999;
      width: auto;
      max-width: 340px;
      pointer-events: none;
    }

    .toast {
      --toast-accent: #5b8cff;
      --toast-accent-2: #7c4dff;
      --toast-glow: rgba(91, 140, 255, 0.22);

      position: relative;
      display: flex;
      align-items: center;
      gap: 10px;
      min-height: 48px;
      max-width: 320px;
      min-width: 220px;
      padding: 10px 12px;
      border-radius: 14px;
      overflow: hidden;
      pointer-events: auto;

      color: #f8fafc;
      background:
        linear-gradient(135deg,
          rgba(17, 24, 39, 0.96) 0%,
          rgba(30, 41, 59, 0.95) 52%,
          rgba(15, 23, 42, 0.96) 100%);
      border: 1px solid rgba(255, 255, 255, 0.08);

      box-shadow:
        0 14px 30px rgba(2, 6, 23, 0.30),
        0 6px 14px rgba(2, 6, 23, 0.18),
        inset 0 1px 0 rgba(255, 255, 255, 0.06);

      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);

      transform: translateX(36px) scale(0.94);
      opacity: 0;
      transition:
        transform 0.34s cubic-bezier(0.22, 1, 0.36, 1),
        opacity 0.26s ease,
        box-shadow 0.24s ease;
      will-change: transform, opacity;
    }

    .toast::before {
      content: "";
      position: absolute;
      inset: 0 auto 0 0;
      width: 3px;
      background: linear-gradient(180deg, var(--toast-accent), var(--toast-accent-2));
    }

    .toast::after {
      content: "";
      position: absolute;
      top: -36px;
      right: -24px;
      width: 116px;
      height: 116px;
      background: radial-gradient(circle, var(--toast-glow) 0%, transparent 72%);
      pointer-events: none;
    }

    .toast.show {
      transform: translateX(0) scale(1);
      opacity: 1;
    }

    .toast.hide {
      transform: translateX(42px) scale(0.92);
      opacity: 0;
    }

    .toast-icon {
      width: 28px;
      height: 28px;
      min-width: 28px;
      border-radius: 50%;
      display: grid;
      place-items: center;
      color: #fff;
      background: linear-gradient(135deg, var(--toast-accent), var(--toast-accent-2));
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.22);
      margin-bottom: 4px;
      position: relative;
      z-index: 1;
    }

    .toast-content {
      flex: 1;
      font-family: Inter, sans-serif;
      font-size: 13px;
      line-height: 1.35;
      font-weight: 500;
      color: #e5eefc;
      letter-spacing: 0.1px;
      position: relative;
      z-index: 1;
    }


    .toast-close {
      display: inline-flex;
      align-items: center;
      justify-content: center;

      padding: 6px;
      /* controls circle size */
      aspect-ratio: 1 / 1;
      /* always keep perfect circle */

      border: none;
      outline: none;

      border-radius: 50%;

      background: rgba(255, 255, 255, 0.06);
      color: rgba(255, 255, 255, 0.78);

      cursor: pointer;

      transition:
        background .18s ease,
        transform .18s ease,
        color .18s ease;

      position: relative;
      z-index: 1;
    }

    .toast-close svg {
      width: 12px;
      height: 12px;
      display: block;
    }

    .toast-close:hover {
      background: rgba(255, 255, 255, 0.14);
      color: #ffffff;
      transform: scale(1.08);
    }

    .toast-progress {
      position: absolute;
      left: 10px;
      right: 10px;
      bottom: 6px;
      height: 2.5px;
      border-radius: 999px;
      overflow: hidden;
      background: rgba(255, 255, 255, 0.08);
    }

    .toast-progress-bar {
      width: 100%;
      height: 100%;
      transform-origin: left center;
      transform: scaleX(1);
      border-radius: inherit;
      background: linear-gradient(90deg, var(--toast-accent), var(--toast-accent-2));
      transition-property: transform;
      transition-timing-function: linear;
      will-change: transform;
    }

    .toast.paused .toast-progress-bar {
      transition: none !important;
    }

    .toast.info {
      --toast-accent: #4f8cff;
      --toast-accent-2: #7c4dff;
      --toast-glow: rgba(79, 140, 255, 0.22);
    }

    .toast.success {
      --toast-accent: #22c55e;
      --toast-accent-2: #16a34a;
      --toast-glow: rgba(34, 197, 94, .25);
    }

    .toast.error {
      --toast-accent: #ef4444;
      --toast-accent-2: #f97316;
      --toast-glow: rgba(239, 68, 68, 0.22);
    }


    .loader {
      width: 2em;
      height: 2em;
    }

    .loader-ring {
      animation: ringA 2s linear infinite;
    }

    .loader-ring-a {
      stroke: #9708F4;
    }

    .loader-ring-b {
      animation-name: ringB;
      stroke: #5E14E4;
    }

    .loader-ring-c {
      animation-name: ringC;
      stroke: #9708F4;
    }

    .loader-ring-d {
      animation-name: ringD;
      stroke: #5E14E4;
    }

    /* Animations */
    @keyframes ringA {

      from,
      4% {
        stroke-dasharray: 0 660;
        stroke-width: 20;
        stroke-dashoffset: -330;
      }

      12% {
        stroke-dasharray: 60 600;
        stroke-width: 30;
        stroke-dashoffset: -335;
      }

      32% {
        stroke-dasharray: 60 600;
        stroke-width: 30;
        stroke-dashoffset: -595;
      }

      40%,
      54% {
        stroke-dasharray: 0 660;
        stroke-width: 20;
        stroke-dashoffset: -660;
      }

      62% {
        stroke-dasharray: 60 600;
        stroke-width: 30;
        stroke-dashoffset: -665;
      }

      82% {
        stroke-dasharray: 60 600;
        stroke-width: 30;
        stroke-dashoffset: -925;
      }

      90%,
      to {
        stroke-dasharray: 0 660;
        stroke-width: 20;
        stroke-dashoffset: -990;
      }
    }

    @keyframes ringB {

      from,
      12% {
        stroke-dasharray: 0 220;
        stroke-width: 20;
        stroke-dashoffset: -110;
      }

      20% {
        stroke-dasharray: 20 200;
        stroke-width: 30;
        stroke-dashoffset: -115;
      }

      40% {
        stroke-dasharray: 20 200;
        stroke-width: 30;
        stroke-dashoffset: -195;
      }

      48%,
      62% {
        stroke-dasharray: 0 220;
        stroke-width: 20;
        stroke-dashoffset: -220;
      }

      70% {
        stroke-dasharray: 20 200;
        stroke-width: 30;
        stroke-dashoffset: -225;
      }

      90% {
        stroke-dasharray: 20 200;
        stroke-width: 30;
        stroke-dashoffset: -305;
      }

      98%,
      to {
        stroke-dasharray: 0 220;
        stroke-width: 20;
        stroke-dashoffset: -330;
      }
    }

    @keyframes ringC {
      from {
        stroke-dasharray: 0 440;
        stroke-width: 20;
        stroke-dashoffset: 0;
      }

      8% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -5;
      }

      28% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -175;
      }

      36%,
      58% {
        stroke-dasharray: 0 440;
        stroke-width: 20;
        stroke-dashoffset: -220;
      }

      66% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -225;
      }

      86% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -395;
      }

      94%,
      to {
        stroke-dasharray: 0 440;
        stroke-width: 20;
        stroke-dashoffset: -440;
      }
    }

    @keyframes ringD {

      from,
      8% {
        stroke-dasharray: 0 440;
        stroke-width: 20;
        stroke-dashoffset: 0;
      }

      16% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -5;
      }

      36% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -175;
      }

      44%,
      50% {
        stroke-dasharray: 0 440;
        stroke-width: 20;
        stroke-dashoffset: -220;
      }

      58% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -225;
      }

      78% {
        stroke-dasharray: 40 400;
        stroke-width: 30;
        stroke-dashoffset: -395;
      }

      86%,
      to {
        stroke-dasharray: 0 440;
        stroke-width: 20;
        stroke-dashoffset: -440;
      }
    }

    .print-safety-warning-modal {
      position: fixed;
      inset: 0;
      z-index: 99999;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 24px;
      background: #1118275c;
      backdrop-filter: blur(6px);
    }

    .print-safety-warning-modal.show {
      display: flex;
      animation: psFadeIn 0.22s ease;
    }

    .print-safety-warning-dialog {
      position: relative;
      width: min(550px, 100%);
      border-radius: 24px;
      padding: 16px;
      background: #f8fafd;
      border: 1px solid #dbe4f3;
      box-shadow:
        0 18px 44px rgba(15, 23, 42, 0.10),
        0 4px 14px rgba(59, 130, 246, 0.08);
      animation: psPopIn 0.22s ease;
    }

    .print-safety-warning-dialog::before {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: 24px;
      padding: 1px;
      background: linear-gradient(135deg, rgba(96, 165, 250, 0.32), rgba(196, 181, 253, 0.26));
      -webkit-mask:
        linear-gradient(#000 0 0) content-box,
        linear-gradient(#000 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      pointer-events: none;
    }

    .print-safety-warning-close {
      position: absolute;
      top: 14px;
      right: 14px;
      background: transparent;
      color: #ef4444;
      font-size: 16px;
      line-height: 1;
      cursor: pointer;
      transition: all 0.18s ease;
    }

    .print-safety-warning-close:hover {
      color: #334155;

    }

    .print-safety-warning-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 14px;
      border-radius: 999px;
      background: linear-gradient(135deg, #edf4ff, #f3efff);
      color: #4f46e5;
      font-size: 12px;
      font-weight: 700;
      border: 1px solid #d7e6ff;
      margin-bottom: 14px;
    }

    .print-safety-warning-title {
      margin: 0 0 8px;
      font-size: 24px;
      font-weight: 800;
      line-height: 1.2;
      color: #1e293b;
      letter-spacing: -0.02em;
    }

    .print-safety-warning-text {
      margin: 0 0 18px;
      font-size: 14px;
      line-height: 1.7;
      color: #64748b;
    }

    .print-safety-warning-list {
      display: grid;
      gap: 10px;
      max-height: 260px;
      overflow: auto;
      padding-right: 4px;
    }

    .print-safety-warning-item {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      padding: 9px 14px;
      border-radius: 16px;
      background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
      border: 1px solid #e2e8f0;
      box-shadow: 0 3px 10px rgba(15, 23, 42, 0.04);
    }

    .print-safety-warning-icon {
      width: 30px;
      height: 30px;
      flex: 0 0 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #eef4ff, #f4efff);
      border: 1px solid #dbe4f3;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
    }

    .print-safety-warning-icon i {
      font-size: 14px;
      color: #6366f1;
    }

    .print-safety-warning-message {
      font-size: 13px;
      line-height: 1.65;
      color: #334155;
    }

    .print-safety-warning-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 22px;
    }

    .print-safety-btn {
      min-width: 130px;
      border-radius: 14px;
      padding: 12px 18px;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.18s ease;
    }

    .print-safety-btn.primary {
      border: 1px solid #5b7cff;
      background: linear-gradient(135deg, #6d7cff, #5b6cff);
      color: #ffffff;
      box-shadow: 0 10px 22px rgba(91, 108, 255, 0.20);
    }

    .print-safety-btn.primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 14px 28px rgba(91, 108, 255, 0.24);
    }

    .print-safety-btn.secondary {
      border: 1px solid #d7dee9;
      background: linear-gradient(180deg, #ffffff 0%, #f3f6fa 100%);
      color: #475569;
      box-shadow: 0 4px 10px rgba(15, 23, 42, 0.04);
    }

    .print-safety-btn.secondary:hover {
      background: linear-gradient(180deg, #ffffff 0%, #eef3f8 100%);
      color: #334155;
    }

    @keyframes psFadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    @keyframes psPopIn {
      from {
        opacity: 0;
        transform: translateY(8px) scale(0.985);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }







    /* <<<<<<<<< responsive >>>>>>>>>>>> */


    /* Base (>=1920) */
    .container {
      max-width: 1780px;
    }

    /* <= 1919px (Full HD) */
    @media screen and (max-width: 1919px) {
      .container {
        max-width: 1660px;
      }
    }

    /* <= 1599px (Large desktop) */
    @media screen and (max-width: 1599px) {
      .container {
        max-width: 1480px;
      }

    }

    /* <= 1439px (1440 screens) */
    @media screen and (max-width: 1439px) {
      .container {
        max-width: 1360px;
      }
    }

    /* <= 1365px (Laptop 1366) */
    @media screen and (max-width: 1365px) {
      .container {
        max-width: 1280px;
      }
    }

    /* <= 1279px (Compact laptop) */
    @media screen and (max-width: 1279px) {
      .container {
        max-width: 1200px;
      }

      .manual-btn {
        padding: 8px 14px;
      }
    }

    /* <= 1199px (Tablet landscape) */
    @media screen and (max-width: 1199px) {
      .container {
        max-width: 992px;
      }

      .image-logo {
        width: 80px;
        height: 80px;
        left: 215px;
      }
    }

    /* <= 991px (Tablet portrait) */
    @media screen and (max-width: 991px) {
      .container {
        max-width: 768px;
      }

      .responsive-shell {
        flex-direction: column;
      }

      .app-container {
        flex: 0 0 100%;
        max-width: 100%;
      }

      .c-q_head {

        right: 318px;
      }

      .action_btns {
        justify-content: start;

      }

      #workspaceWrapper {
        position: relative;

      }

      .print-controls {
        width: 250px;
        padding: 15px 10px 15px 14px;

      }

      .word-toolbar {
        flex-direction: column;
        gap: 8px;
      }

      .main-copy {

        flex-direction: unset;

      }
    }

    /* <= 767px (Mobile - usually blocked) */
    @media screen and (max-width: 767px) {
      .container {
        width: 480px;
      }

      .c-q_head {
        right: 30px;
      }

      .print-controls {
        width: 100%;
        overflow-y: unset;
      }

      .print-modal-content {
        width: 95%;
        height: 95%;
        overflow: auto;
        flex-direction: column-reverse;
      }

      .print-preview {
        overflow: unset;

      }

      .lib-actions-2 {
        flex-direction: column;
        align-items: center;
      }
    }

    @media screen and (max-width: 479px) {
      .container {
        width: 320px;
      }

      .c-q_head {
        right: 15px;
      }

      .lib-bottom {

        flex-direction: column;
      }
    }
  </Style>



</head>

<body>
  <div class="container">
    <div id="modalOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:999;"></div>
  </div>

  <div class="container">
    <div id="qrDataModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:20px; border-radius:8px; width:800px; max-width:90%; z-index:1000;">
      <h2>QR Data</h2>
      <div id="qrDataContent" style="margin-top:10px;"></div>
    </div>
  </div>

  <div class="container">
    <div class="responsive-shell">
      <div class="app-container">
        <div class="left_title">
          <div class="text-logo">
            <!-- <h1 class="no-print pacifico-regular">Tag Link<i class="bi bi-link"></i></h1> -->
            <h1 class="no-print pacifico-regular" style="display:flex; align-items:center; gap:5px; font-size:28px;font-weight:600;letter-spacing:0.5px; color:#1F2937;">
              <span style=" background: linear-gradient(135deg, #2563EB, #7C3AED); -webkit-background-clip: text; -webkit-text-fill-color: transparent; ">
                TagLink
              </span>
              <i class="bi bi-link" style="font-size:20px; color:#2563EB; padding:0px; border-radius:8px;transition:all 0.25s ease;"></i>
            </h1>
          </div>
          <div class="image-logo"><img src="<?= base_url('files/sticker-images/karatinfinity-logo2.png/') ?>" alt=""></div>
        </div>

        <div class="controls no-print">

          <div class="form-container">
            <div id="stickerModal" class="modal" style="display:none;">
              <div class="modal-content">
                <div id="modalFieldsContainer"></div>

                <div class="action_btns com_flex">
                  <button type="button" class="btn neutral-btn save-btn" onclick="saveModalFields()">
                    <i class="bi bi-check-circle-fill"></i>
                    Save Form
                  </button>
                  <a class="btn neutral-btn edit-btn"
                    href="<?= site_url('items/sticker_form?id=' . $id . '&modelno=' . $modelno . '&modelname=' . $modelname . '&category=' . $category . '&open=edit') ?>">
                    <i class="bi bi-pencil-square"></i>
                    Edit Field
                  </a>
                </div>
              </div>
            </div>
          </div>

          <input type="hidden" id="capacityInput" placeholder="Enter capacity">
          <input type="hidden" id="rangeInput" placeholder="Enter working range">
          <input type="hidden" id="outputInput" placeholder="Enter output">
          <input type="hidden" id="domInput" placeholder="Date of manufacture">
          <input type="hidden" id="mrpInput" placeholder="Enter MRP">
          <input type="hidden" id="serialInput" placeholder="Serial number">

          <input type="hidden" id="companyName" value="Factor Enovations Pvt Ltd">
          <input type="hidden" id="website" value="efactor.tech">
          <input type="hidden" id="address" value="Hyderabad -62">
          <input type="hidden" id="tagline" value="">
          <div class="product_sec">
            <h2>Product Specifications</h2>

            <div class="control-group">
              <label for="productName">Product Name:</label>
              <input type="text" id="productName" value="<?php echo htmlspecialchars($category); ?>" placeholder="Enter Product name" readonly>
            </div>

            <div class="control-group">
              <label for="modelInput">Model:</label>
              <input type="text" id="modelInput" value="<?php echo htmlspecialchars($modelname); ?>" placeholder="Enter model name" readonly>
            </div>
            <!-- INCLUDE BARCODE -->
            <div class="control-group">
              <label for="includeBarcode">Include Barcode:</label>
              <select id="includeBarcode">
                <option value="">Select Yes / No</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
              </select>
            </div>

            <!-- BARCODE SECTION -->
            <div id="barcodeSection" class="barcode-section hidden">

              <div class="control-group">
                <label for="barcodeType">Barcode Type:</label>
                <select id="barcodeType" class="select-placeholder">
                  <!-- <option value="">Select-Type</option> -->
                  <option value="code128" selected>Code128</option>
                  <option value="code39">Code39</option>
                  <option value="ean13">EAN13</option>
                  <option value="upc">UPC-A</option>
                  <option value="itf">ITF</option>
                  <option value="gs1databar">GS1 DataBar</option>
                </select>
              </div>

              <div class="control-group">
                <label for="barcodeValue">Barcode Value:</label>
                <div class="barcode-input">
                  <input type="text" id="barcodeValue" placeholder="Enter value">
                </div>
                <div class="control-group">
                  <label for="barcodeSize">Barcode Size:</label>
                  <select id="barcodeSize">
                    <option value="verysmall">Very Small</option>
                    <option value="small" selected>Small</option>
                    <option value="medium">Medium</option>
                    <option value="large">Large</option>
                    <option value="xlarge">Extra Large</option>
                  </select>
                  <button type="button" class="barcode_btn" onclick="generateSVGBarcode()" title="Generate Barcode">Apply
                  </button>
                </div>

              </div>


            </div>


            <!-- <div class="control-group" >
            <label for="nodeDropdown">Select Node ID:</label>
            <select id="nodeDropdown" onchange="document.getElementById('Nodeinput').value=this.value">
              <option value="">--Select Node ID--</option>
              <?php if (!empty($nodeIds)): ?>
                <?php foreach ($nodeIds as $node): ?>
                  <option value="<?= $node; ?>"><?= $node; ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div> -->
            <div class="control-group">
              <label for="qrSizeInput">QR Size (mm):</label>
              <select id="qrSizeInput">
                <!-- <option value="12">12 mm</option> -->
                <option value="14" selected>14 mm</option>
                <option value="16">16 mm</option>
                <option value="18">18 mm</option>
                <option value="20">20 mm</option>
                <option value="22">22 mm</option>
                <option value="24">24 mm</option>
                <option value="26">26 mm</option>
                <option value="28">28 mm</option>
                <option value="30">30 mm</option>
                <option value="32">32 mm</option>
                <option value="34">34 mm</option>
                <option value="36">36 mm</option>
                <option value="38">38 mm</option>
                <option value="40">40 mm</option>
                <option value="42">42 mm</option>
                <option value="44">44 mm</option>
                <option value="46">46 mm</option>
                <option value="48">48 mm</option>
                <option value="50">50 mm</option>
              </select>
            </div>



          </div>
        </div>
      </div>

      <!-- Preview Editor Box -->
      <div class="label-preview no-print">
        <!-- <<<<<<<<<<<<<<<<  tool bar   >>>>>>>>>>>>>>>> -->
        <div class="word-toolbar">
          <div class="left-tools">

            <!-- PRINT / PREVIEW -->
            <div class="left-tool-group">
              <button id="printPageBtn" class=" pagesize" title="Create Print Page">
                <i class="bi bi-file-earmark-text"></i>Page Size
              </button>
              <div class="main-copy">
                <button id="mainLabelBtn" class=" m-label" title="Main Label">
                  <i class="bi bi-sticky"></i>New Label
                </button>

                <button id="copyLabelBtn" class=" c-label" title="Copy Label">
                  <i class="bi bi-stickies"></i>Copy Label
                </button>

              </div>



            </div>
          </div>

          <div class="middle-tools">
            <!-- FONT FAMILY -->
            <div class="toolbar-group">
              <select id="fontFamilyPicker" class="toolbar-select"> </select>

              <!-- FONT SIZE -->
              <label>
                <select id="fontSizePicker" class="toolbar-select small">
                  <option value="2">2</option>
                  <option value="2.5">2.5</option>
                  <option value="3">3</option>
                  <option value="3.5">3.5</option>
                  <option value="4" selected>4</option>
                  <option value="4.5">4.5</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                  <option value="6.5">6.5 </option>
                  <option value="7">7</option>
                  <option value="7.5">7.5</option>
                  <option value="8">8</option>
                  <option value="8.5">8.5</option>
                  <option value="9">9</option>
                  <option value="10">10</option>
                  <option value="12">12</option>
                  <option value="14">14</option>
                  <option value="16">16</option>
                  <option value="18">18</option>
                  <option value="20">20</option>
                  <option value="22">22</option>
                  <option value="24">24</option>
                  <option value="26">26</option>
                  <option value="28">28</option>
                  <option value="30">30</option>
                  <option value="32">32</option>
                </select>
              </label>

              <!-- FONT COLOR -->
              <label>
                <input type="color" id="fontColorPicker" value="#000000" class="color-picker">
              </label>


              <!-- TEXT STYLE -->

              <button type="button" onclick="toggleBold()" class="tool-btn" title="Bold">
                <i class="bi bi-type-bold"></i>
              </button>
              <button type="button" onclick="toggleItalic()" class="tool-btn" title="Italic">
                <i class="bi bi-type-italic"></i>
              </button>
              <button type="button" onclick="toggleUnderline()" class="tool-btn" title="Underline">
                <i class="bi bi-type-underline"></i>
              </button>


              <!-- MOVE -->

              <button type="button" onclick="moveSelected('up')" class="tool-btn" title="Move Up"><i class="bi bi-arrow-up"></i></button>
              <button type="button" onclick="moveSelected('left')" class="tool-btn" title="Move Left"><i class="bi bi-arrow-left"></i></button>
              <button type="button" onclick="moveSelected('down')" class="tool-btn" title="Move Down"><i class="bi bi-arrow-down"></i></button>
              <button type="button" onclick="moveSelected('right')" class="tool-btn" title="Move Right"><i class="bi bi-arrow-right"></i></button>


              <button class="tool-btn" title="Photos" onclick="toggleLibrary()">
                <i class="bi bi-image"></i>
              </button>




              <!-- ACTIONS -->
              <button type="button" class="tool-btn danger" id="deleteBtn" title="Delete">
                <i class="bi bi-trash red"></i>
              </button>
              <button class="tool-btn " id="undoBtn" title="Undo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                  <path d="M255.545 8c-66.269.119-126.438 26.233-170.86 68.685L48.971 40.971C33.851 25.851 8 36.559 8 57.941V192c0 13.255 10.745 24 24 24h134.059c21.382 0 32.09-25.851 16.971-40.971l-41.75-41.75c30.864-28.899 70.801-44.907 113.23-45.273 92.398-.798 170.283 73.977 169.484 169.442C423.236 348.009 349.816 424 256 424c-41.127 0-79.997-14.678-110.63-41.556-4.743-4.161-11.906-3.908-16.368.553L89.34 422.659c-4.872 4.872-4.631 12.815.482 17.433C133.798 479.813 192.074 504 256 504c136.966 0 247.999-111.033 248-247.998C504.001 119.193 392.354 7.755 255.545 8z" />
                </svg>
              </button>
              <button class="tool-btn" id="redoBtn" title="Redo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                  <path d="M256.455 8c66.269.119 126.437 26.233 170.859 68.685l35.715-35.715C478.149 25.851 504 36.559 504 57.941V192c0 13.255-10.745 24-24 24H345.941c-21.382 0-32.09-25.851-16.971-40.971l41.75-41.75c-30.864-28.899-70.801-44.907-113.23-45.273-92.398-.798-170.283 73.977-169.484 169.442C88.764 348.009 162.184 424 256 424c41.127 0 79.997-14.678 110.629-41.556 4.743-4.161 11.906-3.908 16.368.553l39.662 39.662c4.872 4.872 4.631 12.815-.482 17.433C378.202 479.813 319.926 504 256 504 119.034 504 8.001 392.967 8 256.002 7.999 119.193 119.646 7.755 256.455 8z" />
                </svg>
              </button>

              <button id="showQRDataBtn" class="last_btns">QR Info</button>
              <button id="btn-delete-page" class="clear_btn" onclick="deletePrintPage()">Clear</button>
              <button type="button" id="savePreviewBtn" onclick="saveLayoutData()">Save</button>

              <button id="user-manual" class="manual-btn" onclick="openUserManual()">Guide</button>

            </div>

          </div>
          <div class="right-tools">

            <button type="button" class="printicon" id="printBtn" class="tool-btn" title="Print">
              <i class="bi bi-printer-fill"></i> Print
            </button>

          </div>


        </div>




        <!-- NEW LABEL MODAL -->
        <div id="sizeModalNew" class="dialog-overlay">
          <div class="dialog-box">
            <h3>New Label Size</h3>

            <label>Width</label>
            <div class="input-mm">
              <input type="number" id="labelWidthNew" min="0" max="300">
              <span>mm</span>
            </div>

            <label>Height</label>

            <div class="input-mm">
              <input type="number" id="labelHeightNew" min="0" max="300">
              <span>mm</span>
            </div>


            <div class="dialog-actions">
              <button id="confirmSizeNew">Confirm</button>
              <button id="cancelSizeNew">Cancel</button>
            </div>
          </div>
        </div>

        <!-- LABEL QUICK MENU -->
        <div id="labelQuickMenu" class="label-quick-menu" aria-hidden="true">
          <button type="button" class="label-quick-item" id="labelResizeAction">
            <span class="label-quick-icon">
              <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 9V4h5"></path>
                <path d="M20 15v5h-5"></path>
                <path d="M20 9V4h-5"></path>
                <path d="M4 15v5h5"></path>
                <path d="M9 4L4 9"></path>
                <path d="M15 20l5-5"></path>
                <path d="M15 4l5 5"></path>
                <path d="M9 20l-5-5"></path>
              </svg>
            </span>
            <span>Resize</span>
          </button>

          <button type="button" class="label-quick-item" id="labelBorderAction">
            <span class="label-quick-icon">
              <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="4" y="4" width="16" height="16" rx="2"></rect>
              </svg>
            </span>
            <span>Border</span>
          </button>
        </div>

        <!-- LABEL RESIZE MODAL -->
        <div id="labelResizeModal" class="dialog-overlay label-resize-overlay" style="display:none;">
          <div class="label-resize-box">
            <div class="label-resize-head">
              <div>
                <h3>Resize Label</h3>
                <p>Update only the selected label size</p>
              </div>
              <!-- <button type="button" class="label-resize-close" id="closeLabelResizeModal" aria-label="Close">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M18 6L6 18"></path>
                <path d="M6 6l12 12"></path>
              </svg>
            </button> -->
            </div>

            <div class="label-resize-grid">
              <div class="label-resize-field">
                <label for="resizeLabelWidth">Width</label>
                <div class="input-mm premium-mm-input">
                  <input type="number" id="resizeLabelWidth" min="1" max="300" step="0.1">
                  <span>mm</span>
                </div>
              </div>

              <div class="label-resize-field">
                <label for="resizeLabelHeight">Height</label>
                <div class="input-mm premium-mm-input">
                  <input type="number" id="resizeLabelHeight" min="1" max="300" step="0.1">
                  <span>mm</span>
                </div>
              </div>
            </div>

            <div class="label-resize-actions">
              <button type="button" class="label-btn ghost" id="cancelLabelResizeBtn">Cancel</button>
              <button type="button" class="label-btn primary" id="applyLabelResizeBtn">Apply</button>
            </div>
          </div>
        </div>


        <!-- COPY LABEL MODAL -->
        <div id="copyLabelModal" class="dialog-overlay">
          <div class="dialog-box">
            <h3>Copy Label Size</h3>

            <label>Width</label>
            <div class="input-mm">
              <input type="number" id="copyLabelWidth" min="0" max="300">
              <span>mm</span>
            </div>


            <label>Height</label>
            <div class="input-mm">
              <input type="number" id="copyLabelHeight" min="0" max="300">
              <span>mm</span>
            </div>

            <div class="dialog-actions">
              <button id="confirmCopyLabel">Confirm</button>
              <button id="cancelCopyLabel">Cancel</button>
            </div>
          </div>
        </div>


        <!-- PRINT PAGE MODAL -->
        <div id="printPageModal" class="dialog-overlay">
          <div class="dialog-box">
            <h3> Page Size Setup</h3>

            <label>Width</label>

            <div class="input-mm">
              <input type="number" id="printPageWidth" min="0" max="300">
              <span>mm</span>
            </div>

            <label>Height</label>

            <div class="input-mm">
              <input type="number" id="printPageHeight" min="0" max="300">
              <span>mm</span>
            </div>

            <div class="dialog-actions">
              <button id="confirmPrintPage">Confirm</button>
              <button id="cancelPrintPage">Cancel</button>
            </div>
          </div>
        </div>

        <div id="clearConfirmModal" class="clear-modal">
          <div class="clear-modal-box">

            <div class="clear-title">
              Clear Design
            </div>

            <div class="clear-message">
              Do you want to clear the full design?
            </div>

            <div class="clear-actions">
              <button class="clear-no" onclick="closeClearModal()">No</button>
              <button class="clear-yes" onclick="confirmClearPage()">Yes</button>
            </div>

          </div>
        </div>

        <!---------------------------- LABEL PREVIEW BOX  ---------------------------------->
        <!-- OUTER FIXED FRAME -->
        <div class="label-preview-frame">

          <div class="label-preview-box">

            <div id="workspaceWrapper">

              <svg id="workspaceSvg"
                xmlns="http://www.w3.org/2000/svg">



                <g id="workspaceViewport">


                  <g id="workspaceGrid"
                    pointer-events="none"></g>


                  <g id="workspaceRuler"
                    pointer-events="none"></g>


                  <g id="printPageGroup">
                    <g id="labelGroup"></g>
                  </g>




                  <g id="guideLayer"
                    pointer-events="none"></g>



                  <g id="selectionLayer"
                    pointer-events="none"></g>

                </g>


                <rect id="interactionOverlay"
                  x="0"
                  y="0"
                  width="100%"
                  height="100%"
                  fill="transparent"
                  pointer-events="none" />

              </svg>

            </div>

          </div>

          <div id="zoomIndicator" class="zoom-indicator">

            <button id="zoomOutBtn">−</button>

            <span id="zoomPercent">100%</span>

            <button id="zoomInBtn">+</button>

            <button id="zoomResetBtn">Reset</button>

          </div>

        </div>


      </div>
    </div>
  </div>



  <!-- <<<<<   image library popup block  >>>>>> -->
  <div class="container">
    <div id="libraryOverlay" class="lib-overlay hidden">

      <div class="lib-modal">

        <div class="lib-header">
          <div class="lib-title">
            <div class="lib-photo-logo"><img src="<?= base_url('files/sticker-images/photo-logo.png/') ?>" alt="icon"></div>
            <span>
              Photo Library
            </span>
          </div>

          <div id="selectionCount" class="select-cunt">0 / 10 selected</div>
          <button onclick="toggleLibrary()" class="lib-close">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
              <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
            </svg>
          </button>
        </div>

        <div id="libContent" class="lib-content"></div>

        <div class="lib-bottom">
          <div class="left-lib-btns">
            <div class="lib-actions-1">
              <label class="select-btn" for="fileInput">
                <input type="file" id="fileInput" multiple accept="image/*">

                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                  fill="currentColor" class="bi bi-paperclip" viewBox="0 0 16 16">
                  <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0z" />
                </svg>
              </label>


              <button onclick="uploadImages()" class="uploadbtn">Upload</button>

            </div>



            <div class="lib-actions-2">
              <select id="imgType" class="gbtn">
                <option value="">Image Type</option>
                <option value="color">Color</option>
                <option value="mono">Monochrome</option>
              </select>

              <button onclick="addSelectedToLabel()" class="gbtn primary">
                Add to Label
              </button>

              <button onclick="deleteSelectedImg()" class="gbtn ">
                <i class="bi bi-trash red"></i>
              </button>

            </div>
          </div>

          <div class="right-lib-btns">
            <div id="totalImageCount" style="font-size:13px; color:#666;"></div>
          </div>
        </div>

        <div id="uploadPreview" class="upload-preview"></div>

      </div>
    </div>
  </div>



  <!-- overlap checking  -->
  <div class="container">

    <div id="printSafetyWarningModal" class="print-safety-warning-modal" aria-hidden="true">
      <div class="print-safety-warning-dialog">
        <button type="button" id="printSafetyWarningClose" class="print-safety-warning-close" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>

        <div class="print-safety-warning-badge">Print Safety Check</div>

        <h3 class="print-safety-warning-title">Layout may cause print border issues</h3>

        <p class="print-safety-warning-text">
          Some labels are overlapping or too close to the page edge. This may cause borders to be clipped, missed, or printed incorrectly on the physical label.
        </p>

        <div id="printSafetyWarningList" class="print-safety-warning-list"></div>

        <div class="print-safety-warning-actions">
          <button type="button" id="printSafetyFixBtn" class="print-safety-btn secondary">Fix Layout</button>
          <button type="button" id="printSafetyIgnoreBtn" class="print-safety-btn primary">Ignore & Continue</button>
        </div>
      </div>
    </div>
  </div>


  <!-- <<<<<<<< print modal >>>>>>>>>>>> -->
  <div class="container">
    <div id="printModal" class="print-modal">

      <div class="print-modal-content">

        <!-- LEFT PANEL -->
        <div class="print-controls">

          <h2 class="print-title">Print Settings</h2>

          <!-- Printer -->
          <div class="form-group controlfocus">
            <label class="label-head">Printer</label>
            <select id="printerSelect"></select>
          </div>

          <!-- DPI -->
          <div class="form-group controlfocus">
            <label class="label-head">Printer DPI</label>
            <select id="dpiSelect">
              <option value="203" selected>203 DPI</option>
              <option value="300">300 DPI</option>
              <option value="600">600 DPI</option>
            </select>
          </div>

          <!-- Page Size -->
          <div class="form-group">
            <label class="label-head">Page Size</label>
            <div class="radio-group">
              <label><input type="radio" name="pageSize" value="default" checked> Default</label>
              <label><input type="radio" name="pageSize" value="custom"> Custom</label>
            </div>
          </div>
          <div id="customSizeBlock" class=" controlfocus hidden">
            <input type="number" id="customWidth" placeholder="Width (mm)">
            <input type="number" id="customHeight" placeholder="Height (mm)">
            <button id="applyCustomSize" class="">Apply</button>
          </div>


          <!-- Select Label -->
          <div class="form-group">
            <label class="label-head">Select Label</label>
            <div id="labelDropdown" class="dropdown-box focus">
              <!-- Dynamic checkbox list -->
            </div>
          </div>



          <!-- Border Options -->
          <div class="form-group controlfocus">
            <label class="label-head">Border</label>

            <div class="content-input" style="margin-bottom:8px;">
              <select id="labelBorderType" style="width: 100px;">
                <option value="none" selected>No Border</option>
                <option value="solid">Solid</option>
                <option value="dashed">Dashed</option>
                <option value="dotted">Dotted</option>
              </select>

              <input
                type="number"
                id="labelBorderWidth"
                min="0"
                step="0.1"
                value=""
                max="1"
                placeholder="Thickness (mm)" style="width:100px">
            </div>
          </div>




          <!-- Count Limit -->
          <div class="form-group">
            <label class="label-head">Count Limit</label>
            <div class="content-input controlfocus">
              <input type="number" id="countLimitInput" disabled>
              <button id="applyCountLimit" disabled>Apply</button>
            </div>

          </div>

          <!-- Label Position Mode -->
          <div class="form-group controlfocus">
            <label class="label-head">Label Position</label>

            <div class="radio-group">

              <label class="radio-option">
                <input
                  type="radio"
                  name="labelPositionMode"
                  value="default"
                  id="positionDefault"
                  checked>
                Default
              </label>

              <div class="radio-option custom-row">

                <label>
                  <input
                    type="radio"
                    name="labelPositionMode"
                    value="custom"
                    id="positionCustom">
                  Custom (Drag)
                </label>

                <button
                  type="button"
                  id="applyCustomPosition"
                  class="btn-small hidden">
                  Apply To All Pages
                </button>

              </div>

            </div>
          </div>


          <!-- Copies -->
          <div class="form-group controlfocus">
            <label class="label-head">Copies</label>
            <input type="number" id="copiesInput" value="1" min="1">
          </div>

          <!-- Export -->
          <div class="form-group controlfocus">
            <label class="label-head">Export Format</label>
            <div class="content-input">
              <select id="exportFormat">
                <option value="pdf">PDF</option>
                <option value="docx">DOCX</option>
                <option value="png">PNG</option>
                <option value="svg">SVG</option>
                <option value="jpeg">JPEG</option>
              </select>
              <button id="downloadExport"><i class="bi bi-download"></i></button>
            </div>
          </div>

          <!-- Actions -->
          <div class="action-buttons">
            <button id="closePrintModal" class="cancel-btn">Cancel</button>
            <button id="finalPrintBtn" class="print-btn">Print</button>
          </div>

        </div>

        <!-- RIGHT PREVIEW -->
        <div class="print-preview">
          <div id="previewContainer" class="preview-placeholder">

            <!-- SVG preview will be injected here -->
          </div>
        </div>

      </div>
    </div>
  </div>




  <!-- SCREEN SIZE BLOCKER -->
  <!-- <div id="screenBlocker">

    <div class="screenBlockerBox">

      <div class="block-image">
        <img src="<?= base_url('files/sticker-images/sreenblock-img.svg') ?>" alt="screen blocked">
      </div>

      <h2>Screen Too Small</h2>

      <p>
        This label designer works best on a tablet or desktop screen.
        Please switch to a larger device.
      </p>

    </div>

  </div> -->




  <!-- <<<<<<<<<<<<<<<<<<<<<<< main functions  >>>>>>>>>>>.>>>>>>>>>>> -->
  <script>
    //<<<<<<<<<<<<<<<<<< global  variables  >>>>>>>>>>>>>>>>>>
    let labelFieldState = {};
    let stickerModal = document.getElementById('stickerModal');
    let modalFieldsContainer = document.getElementById('modalFieldsContainer');
    let currentModelName = "<?= $modelname ?? '' ?>";
    let showAdvancedFields = false;
    let serialFieldName = null;
    let serialStartValue = null;


    let isLocked = false;
    let selectedNodeId = "";
    let keywordCount = 0;
    const maxKeywords = 10;
    let keywordsEnabled = false;
    let spaceAlertShown = false;
    const spacingLayer = document.getElementById('spacingGuideLayer');
    const unitToggle = document.getElementById('unitToggle'); // mm / cm
    const spacingToggle = document.getElementById('spacingToggle');
    const includeBarcode = document.getElementById('includeBarcode');
    const barcodeSection = document.getElementById('barcodeSection');
    const currentUserId = <?= json_encode(session()->get('user_id') ?? '') ?>;


    /* <<<<<<<<<<<<<<<<<<   GLOBAL PRO TOAST SYSTEM     >>>>>>>>>>>>>>>>>>>>>>>>>>>>*/





    (function() {
      const MAX_TOASTS = 5;
      const activeToasts = new Map();

      function createContainer() {
        let container = document.querySelector(".toast-container");
        if (!container) {
          container = document.createElement("div");
          container.className = "toast-container";
          document.body.appendChild(container);
        }
        return container;
      }

      function getIcon(type) {
        switch (type) {
          case "success":
            return `
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 6L9 17l-5-5"></path>
            </svg>
          `;
          case "error":
            return `
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 6L6 18"></path>
              <path d="M6 6l12 12"></path>
            </svg>
          `;
          default:
            return `
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M12 16v-4"></path>
              <path d="M12 8h.01"></path>
            </svg>
          `;
        }
      }

      function normalizeMessage(message) {
        return String(message || "").trim().replace(/\s+/g, " ");
      }

      function getToastKey(message, type) {
        return `${type}::${normalizeMessage(message)}`;
      }

      function setupProgress(toastObj) {
        const now = performance.now();
        const remaining = Math.max(0, toastObj.endTime - now);
        const ratio = toastObj.duration > 0 ? remaining / toastObj.duration : 0;

        toastObj.progressBar.style.transition = "none";
        toastObj.progressBar.style.transform = `scaleX(${ratio})`;

        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            if (!toastObj.isPaused && !toastObj.isRemoving) {
              toastObj.progressBar.style.transitionProperty = "transform";
              toastObj.progressBar.style.transitionTimingFunction = "linear";
              toastObj.progressBar.style.transitionDuration = remaining + "ms";
              toastObj.progressBar.style.transform = "scaleX(0)";
            }
          });
        });
      }

      function startTimer(toastObj) {
        clearTimeout(toastObj.timeoutId);
        const now = performance.now();
        const remaining = Math.max(0, toastObj.endTime - now);

        if (remaining <= 0) {
          removeToast(toastObj);
          return;
        }

        toastObj.timeoutId = setTimeout(() => {
          removeToast(toastObj);
        }, remaining);

        setupProgress(toastObj);
      }

      function pauseToast(toastObj) {
        if (!toastObj || toastObj.isPaused || toastObj.isRemoving) return;

        toastObj.isPaused = true;
        toastObj.toast.classList.add("paused");

        clearTimeout(toastObj.timeoutId);

        const now = performance.now();
        toastObj.remaining = Math.max(0, toastObj.endTime - now);

        const computed = getComputedStyle(toastObj.progressBar).transform;
        toastObj.progressBar.style.transition = "none";
        toastObj.progressBar.style.transform = computed === "none" ? "scaleX(0)" : computed;
      }

      function resumeToast(toastObj) {
        if (!toastObj || !toastObj.isPaused || toastObj.isRemoving) return;

        toastObj.isPaused = false;
        toastObj.toast.classList.remove("paused");

        toastObj.endTime = performance.now() + toastObj.remaining;
        startTimer(toastObj);
      }

      function removeToast(toastObj) {
        if (!toastObj || toastObj.isRemoving) return;

        toastObj.isRemoving = true;
        clearTimeout(toastObj.timeoutId);

        activeToasts.delete(toastObj.key);

        toastObj.toast.classList.remove("show");
        toastObj.toast.classList.add("hide");

        setTimeout(() => {
          toastObj.toast.remove();
        }, 280);
      }

      function removeOldestNonError() {
        const all = Array.from(activeToasts.values()).filter(t => !t.isRemoving);
        if (all.length < MAX_TOASTS) return;

        const nonErrors = all.filter(t => t.type !== "error");
        if (nonErrors.length) {
          removeToast(nonErrors[0]);
        } else {
          removeToast(all[all.length - 1]);
        }
      }

      function refreshExistingToast(toastObj, duration) {
        toastObj.duration = duration;
        toastObj.remaining = duration;
        toastObj.endTime = performance.now() + duration;
        toastObj.isPaused = false;
        toastObj.isRemoving = false;
        toastObj.toast.classList.remove("hide");
        toastObj.toast.classList.add("show", "pulse-refresh");
        startTimer(toastObj);

        setTimeout(() => {
          toastObj.toast.classList.remove("pulse-refresh");
        }, 220);
      }

      window.showToast = function(message, type = "info", duration = 2200) {
        const container = createContainer();
        const normalizedMessage = normalizeMessage(message);
        const key = getToastKey(normalizedMessage, type);

        if (activeToasts.has(key)) {
          refreshExistingToast(activeToasts.get(key), duration);
          return;
        }

        removeOldestNonError();

        const toast = document.createElement("div");
        toast.className = `toast ${type}`;

        toast.innerHTML = `
        <div class="toast-icon">${getIcon(type)}</div>
        <div class="toast-content"></div>
        <button class="toast-close" type="button" aria-label="Close">
          <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
            <path d="M18 6L6 18"></path>
            <path d="M6 6l12 12"></path>
          </svg>
        </button>
        <div class="toast-progress">
          <div class="toast-progress-bar"></div>
        </div>
      `;

        toast.querySelector(".toast-content").textContent = normalizedMessage;

        const toastObj = {
          key,
          type,
          toast,
          progressBar: toast.querySelector(".toast-progress-bar"),
          closeBtn: toast.querySelector(".toast-close"),
          duration,
          remaining: duration,
          endTime: performance.now() + duration,
          timeoutId: null,
          isPaused: false,
          isRemoving: false,
          createdAt: Date.now()
        };

        activeToasts.set(key, toastObj);

        if (type === "error") {
          container.prepend(toast);
        } else {
          const firstNonError = Array.from(container.children).find(el => !el.classList.contains("error"));
          if (firstNonError) {
            container.insertBefore(toast, firstNonError);
          } else {
            container.appendChild(toast);
          }
        }

        toastObj.closeBtn.addEventListener("click", () => {
          removeToast(toastObj);
        });

        toast.addEventListener("mouseenter", () => pauseToast(toastObj));
        toast.addEventListener("mouseleave", () => resumeToast(toastObj));

        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            toast.classList.add("show");
            startTimer(toastObj);
          });
        });
      };
    })();











    /* <<<<<<<<<<<<<<<<<<<  SCREEN SIZE CHECK >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> */

    function checkScreenSize() {

      const blocker = document.getElementById("screenBlocker");
      if (!blocker) return;

      const minWidth = 320; // tablet minimum width

      if (window.innerWidth < minWidth) {

        blocker.style.display = "flex";
        document.body.classList.add("screen-blocked");

      } else {

        blocker.style.display = "none";
        document.body.classList.remove("screen-blocked");

      }

    }

    /* Run once */
    checkScreenSize();

    /* Run on resize */
    window.addEventListener("resize", checkScreenSize);


    // <<<<<<<<<<<< clear page function >>>>>>>>>>>>>>>>>

    function deletePrintPage() {
      document.getElementById("clearConfirmModal").style.display = "flex";
    }

    function closeClearModal() {
      document.getElementById("clearConfirmModal").style.display = "none";
    }

    function confirmClearPage() {

      const printPageGroup = document.getElementById("printPageGroup");

      if (printPageGroup) {
        printPageGroup.innerHTML = "";
      }

      closeClearModal();

      if (typeof showToast === "function") {
        showToast("Design cleared", "success");
      }
    }

    document.querySelectorAll(".dialog-overlay input").forEach(input => {

      input.addEventListener("keydown", function(e) {

        if (e.key === "Enter") {

          const modal = this.closest(".dialog-overlay");

          const confirmBtn = modal.querySelector("button[id^='confirm']");

          if (confirmBtn) confirmBtn.click();

        }

      });

    });



    function openUserManual() {
      window.open("<?= site_url('items/sticker_stu_UserManual') ?>", "_blank", "noopener,noreferrer");
    }


    //<<<<<<<<<<<<<<<<<<<<< font family functions  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    // FONT REGISTRY (SAFE & CLEAN)
    const FONT_GROUPS = {
      "Sans Serif": [
        "Arial", "Arial Black", "Arial Narrow", "Calibri", "Calibri Light", "Candara",
        "Century Gothic", "Franklin Gothic", "Gill Sans", "Helvetica", "Helvetica Neue",
        "Segoe UI", "Tahoma", "Trebuchet MS", "Verdana", "Bahnschrift", "Roboto",
        "Open Sans", "Noto Sans", "Ubuntu", "Source Sans Pro", "Lato", "Montserrat",
        "PT Sans", "Futura", "Avenir", "Myriad Pro", "DIN", "DIN Condensed",
        "Frutiger", "Proxima Nova", "Inter", "IBM Plex Sans", "Work Sans",
        "Nunito", "Quicksand", "Poppins", "Rubik", "Muli", "Exo", "Asap"
      ],

      "Serif": [
        "Times New Roman", "Georgia", "Cambria", "Garamond", "Palatino", "Book Antiqua",
        "Baskerville", "Constantia", "Didot", "Bodoni", "Merriweather", "Playfair Display",
        "PT Serif", "Lora", "Libre Baskerville", "Crimson Text", "Source Serif Pro",
        "Noto Serif", "Charter", "Perpetua"
      ],

      "Monospace": [
        "Courier New", "Consolas", "Lucida Console", "Roboto Mono",
        "Source Code Pro", "Ubuntu Mono", "Inconsolata",
        "Fira Code", "IBM Plex Mono", "PT Mono"
      ],

      "Condensed / Industrial": [
        "Arial Narrow", "Bahnschrift Condensed", "DIN Condensed",
        "Roboto Condensed", "Open Sans Condensed", "Oswald",
        "Anton", "Bebas Neue", "Agency FB", "Eurostile"
      ],

      "Bold / Heavy Display": [
        "Impact", "Arial Black", "Rockwell", "Copperplate",
        "Haettenschweiler", "Stencil", "Colonna MT",
        "Broadway", "Black Ops One", "Archivo Black"
      ],

      "Clean Label-Friendly": [
        "Segoe UI", "Calibri", "Tahoma", "Verdana",
        "Roboto", "Open Sans", "DIN", "Helvetica",
        "Inter", "Source Sans Pro"
      ]
    };

    // SAFE POPULATION FUNCTION
    function populateFontFamilyPicker(defaultFont = "Arial") {

      const select = document.getElementById("fontFamilyPicker");
      if (!select) return;

      select.innerHTML = "";

      const PRIORITY_FONTS = FONT_GROUPS["Clean Label-Friendly"];

      const allFonts = Object.values(FONT_GROUPS).flat();

      const uniqueFonts = [...new Set(allFonts)];

      // === 1. Add Recommended Group First ===
      const recommendedGroup = document.createElement("optgroup");
      recommendedGroup.label = "⭐ Recommended";

      PRIORITY_FONTS.forEach(font => {
        const option = document.createElement("option");
        option.value = font;
        option.textContent = font;
        option.style.fontFamily = `'${font}', sans-serif`;

        if (font === defaultFont) option.selected = true;

        recommendedGroup.appendChild(option);
      });

      select.appendChild(recommendedGroup);

      // === 2. Add Remaining Fonts ===
      const remainingGroup = document.createElement("optgroup");
      remainingGroup.label = "All Fonts";

      uniqueFonts.forEach(font => {
        if (PRIORITY_FONTS.includes(font)) return;

        const option = document.createElement("option");
        option.value = font;
        option.textContent = font;
        option.style.fontFamily = `'${font}', sans-serif`;

        if (font === defaultFont) option.selected = true;

        remainingGroup.appendChild(option);
      });

      select.appendChild(remainingGroup);

      console.log("Font picker populated (professional mode).");
    }


    // INIT ON LOAD
    document.addEventListener("DOMContentLoaded", () => {
      populateFontFamilyPicker("Arial");
    });




    // <<<<<<<<<<<<<<<<<<<<<<<<<   SVG workspace Ruler Engine (MM Based)     >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    const WORKSPACE_SIZE = 300;
    const workspaceSvg = document.getElementById("workspaceSvg");
    const workspaceGrid = document.getElementById("workspaceGrid");
    const workspaceRuler = document.getElementById("workspaceRuler");
    const printPageGroup = document.getElementById("printPageGroup");
    const labelGroup = document.getElementById("labelGroup");
    // workspace initializer
    function initWorkspace() {

      /* ---------- SVG SIZE IN MM ---------- */
      workspaceSvg.setAttribute("width", WORKSPACE_SIZE + "mm");
      workspaceSvg.setAttribute("height", WORKSPACE_SIZE + "mm");
      workspaceSvg.setAttribute("viewBox", `0 0 ${WORKSPACE_SIZE} ${WORKSPACE_SIZE}`);

      /* ---------- DRAW STATIC SYSTEM ---------- */
      drawWorkspaceGrid();
      drawWorkspaceRuler();

      /* ---------- FIX INTERACTION OVERLAY ---------- */
      const overlay = document.getElementById("interactionOverlay");

      if (overlay) {
        overlay.setAttribute("x", 0);
        overlay.setAttribute("y", 0);
        overlay.setAttribute("width", WORKSPACE_SIZE);
        overlay.setAttribute("height", WORKSPACE_SIZE);
      }

      /* ---------- ENSURE LAYERS ARE ABOVE DESIGN ---------- */
      const guideLayer = document.getElementById("guideLayer");
      const selectionLayer = document.getElementById("selectionLayer");

      if (guideLayer) workspaceSvg.appendChild(guideLayer);
      if (selectionLayer) workspaceSvg.appendChild(selectionLayer);
      if (overlay) workspaceSvg.appendChild(overlay);
    }

    // Ruler drawing 
    function drawWorkspaceRuler() {

      workspaceRuler.innerHTML = "";

      const rulerSize = 12;

      for (let i = 0; i <= WORKSPACE_SIZE; i++) {

        const isMajor = i % 10 === 0;
        const isMid = i % 5 === 0;

        let tick = 3;
        if (isMid) tick = 4;
        if (isMajor) tick = 7;

        // Top ticks
        workspaceRuler.appendChild(
          createLine(i, 0, i, tick)
        );

        // Left ticks
        workspaceRuler.appendChild(
          createLine(0, i, tick, i)
        );

        if (isMajor && i !== 0) {

          // X ruler number (just below long tick)
          workspaceRuler.appendChild(
            createText(i, 6, i, "end")
          );

          // Y ruler number (just right of long tick)
          workspaceRuler.appendChild(
            createText(7.5, i - 1, i, "end")
          );
        }
      }
    }

    // ADD GRID
    function drawWorkspaceGrid() {

      workspaceGrid.innerHTML = "";

      for (let i = 0; i <= WORKSPACE_SIZE; i++) {

        const isMajor = i % 10 === 0;

        const color = isMajor ? "#E4DBFA" : "#F3EEFF";

        workspaceGrid.appendChild(
          createLine(i, 0, i, WORKSPACE_SIZE, color, isMajor ? 0.6 : 0.4)
        );

        workspaceGrid.appendChild(
          createLine(0, i, WORKSPACE_SIZE, i, color, isMajor ? 0.6 : 0.4)
        );
      }
    }

    //ruler draw helper function
    function createLine(x1, y1, x2, y2, color = "#6B7280", width = 0.4) {

      const l = document.createElementNS("http://www.w3.org/2000/svg", "line");

      l.setAttribute("x1", x1);
      l.setAttribute("y1", y1);
      l.setAttribute("x2", x2);
      l.setAttribute("y2", y2);

      l.setAttribute("stroke", color);
      l.setAttribute("stroke-width", width);
      l.setAttribute("vector-effect", "non-scaling-stroke");

      return l;
    }

    function createText(x, y, value, anchor = "middle") {

      const t = document.createElementNS("http://www.w3.org/2000/svg", "text");

      t.setAttribute("x", x);
      t.setAttribute("y", y);
      t.setAttribute("font-size", "2.5");
      t.setAttribute("fill", "#374151");
      t.setAttribute("font-family", "Inter, Arial, sans-serif");
      t.setAttribute("text-anchor", anchor);

      t.textContent = value;

      return t;
    }
    window.addEventListener("DOMContentLoaded", async () => {

      initWorkspace();
    });





    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  PRINT PAGE SIZE   >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    const printPageBtn = document.getElementById('printPageBtn');
    const printPageModal = document.getElementById('printPageModal');
    const confirmPrintPage = document.getElementById('confirmPrintPage');
    const cancelPrintPage = document.getElementById('cancelPrintPage');
    const printPageWidthInput = document.getElementById('printPageWidth');
    const printPageHeightInput = document.getElementById('printPageHeight');

    /* OPEN MODAL */
    printPageBtn?.addEventListener('click', () => {
      if (!printPageModal) return;

      if (printPageWidthInput) printPageWidthInput.value = '';
      if (printPageHeightInput) printPageHeightInput.value = '';

      printPageModal.style.display = 'flex';
    });

    /* CLOSE MODAL */
    cancelPrintPage?.addEventListener('click', () => {
      if (printPageModal) printPageModal.style.display = 'none';
    });

    /* CREATE PRINT PAGE */
    confirmPrintPage?.addEventListener('click', () => {

      const widthMM = parseFloat(printPageWidthInput.value);
      const heightMM = parseFloat(printPageHeightInput.value);

      createPrintPage(widthMM, heightMM);
    });

    function createPrintPage(widthMM, heightMM, options = {}) {

      const isRestore = options.fromJSON === true;

      if (
        isNaN(widthMM) || isNaN(heightMM) ||
        widthMM <= 0 || heightMM <= 0
      ) {
        if (!isRestore) {
          showToast("Enter valid dimensions in mm", "warning");
        }
        return null;
      }

      const printPageGroup = document.getElementById("printPageGroup");
      if (!printPageGroup) return null;

      const PAGE_MARGIN = 10;

      /* ================= SAVE STATE ================= */

      window.currentPrintPage = {
        x: PAGE_MARGIN,
        y: PAGE_MARGIN,
        width: widthMM,
        height: heightMM
      };

      /* ================= SAFE CLEAR ================= */

      printPageGroup.innerHTML = "";

      /* ================= POSITION GROUP ================= */

      printPageGroup.setAttribute(
        "transform",
        `translate(${PAGE_MARGIN}, ${PAGE_MARGIN})`
      );

      const SVG_NS = "http://www.w3.org/2000/svg";

      /* ================= PAGE RECT ================= */

      const rect = document.createElementNS(SVG_NS, "rect");

      rect.setAttribute("x", 0);
      rect.setAttribute("y", 0);
      rect.setAttribute("width", widthMM);
      rect.setAttribute("height", heightMM);
      rect.setAttribute("fill", "#ffffff");
      rect.setAttribute("stroke", "#111827");
      rect.setAttribute("stroke-width", 0.4);
      rect.setAttribute("vector-effect", "non-scaling-stroke");
      rect.setAttribute("id", "printPageRect");

      printPageGroup.appendChild(rect);

      /* ================= MAIN LABEL CONTAINER ================= */

      const labelGroupNew = document.createElementNS(SVG_NS, "g");
      labelGroupNew.setAttribute("id", "labelGroup");

      printPageGroup.appendChild(labelGroupNew);

      /* ================= RESET COPY COUNTER ================= */

      window.__COPY_LABEL_COUNT__ = 0;

      /* ================= NORMAL MODE UI ================= */

      if (!isRestore) {
        if (typeof printPageModal !== "undefined") {
          printPageModal.style.display = "none";
        }
        showToast("Print page created", "success");
      }

      return labelGroupNew;
    }


    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<   MAIN LABEL   >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    const mainLabelBtn = document.getElementById('mainLabelBtn');
    const sizeModalNew = document.getElementById('sizeModalNew');
    const confirmSizeNew = document.getElementById('confirmSizeNew');
    const cancelSizeNew = document.getElementById('cancelSizeNew');
    const labelWidthNew = document.getElementById('labelWidthNew');
    const labelHeightNew = document.getElementById('labelHeightNew');

    /* OPEN SIZE MODAL */
    mainLabelBtn?.addEventListener('click', () => {

      if (!window.currentPrintPage) {
        showToast('Create print page first', 'warning');
        return;
      }

      labelWidthNew.value = '';
      labelHeightNew.value = '';

      sizeModalNew.style.display = 'flex';
    });

    /* CLOSE MODAL */
    cancelSizeNew?.addEventListener('click', () => {
      sizeModalNew.style.display = 'none';
    });

    
    confirmSizeNew?.addEventListener('click', async () => {
      const wMM = parseFloat(labelWidthNew.value);
      const hMM = parseFloat(labelHeightNew.value);

      if (isNaN(wMM) || isNaN(hMM) || wMM <= 0 || hMM <= 0) {
        showToast('Enter valid label size', 'warning');
        return;
      }

      if (!window.currentPrintPage) {
        showToast('Create print page first', 'warning');
        return;
      }

      const page = window.currentPrintPage;

      if (wMM > page.width || hMM > page.height) {
        showToast('Label exceeds print page size', 'error');
        return;
      }

      sizeModalNew.style.display = 'none';

      mainLabel(wMM, hMM);

      const modelname = document.getElementById("modelInput")?.value?.trim() || currentModelName || "";

      if (!Main_NodeId) {
        await fetchNodeIds(modelname);
      }

      updateQRLive(true);
    });

    // SVG main label
    function mainLabel(widthMM, heightMM, options = {}) {

      const isRestore = options.fromJSON === true;

      const labelGroup = document.getElementById("labelGroup");
      if (!labelGroup) {
        if (!isRestore) showToast("Print page not ready", "error");
        return null;
      }

      if (!window.currentPrintPage) {
        if (!isRestore) showToast("Create print page first", "warning");
        return null;
      }

      const page = window.currentPrintPage;

      if (
        isNaN(widthMM) || isNaN(heightMM) ||
        widthMM <= 0 || heightMM <= 0
      ) {
        if (!isRestore) showToast("Invalid label size", "warning");
        return null;
      }

      if (widthMM > page.width || heightMM > page.height) {
        if (!isRestore) showToast("Label exceeds print page size", "error");
        return null;
      }

      /* ================= NORMAL MODE RESET ================= */

      if (!isRestore) {
        labelGroup.innerHTML = "";
        labelGroup.setAttribute("transform", "translate(0,0)");
      }



      /* ================= SIZE SYNC ================= */

      labelGroup.setAttribute("data-width-mm", widthMM);
      labelGroup.setAttribute("data-height-mm", heightMM);

      /* ================= POSITION READ ================= */

      let posX = 0;
      let posY = 0;

      const matrix = labelGroup.transform.baseVal.consolidate();
      if (matrix) {
        posX = matrix.matrix.e;
        posY = matrix.matrix.f;
      }

      /* ================= STATE UPDATE ================= */

      window.currentLabel = {
        x: posX,
        y: posY,
        width: widthMM,
        height: heightMM,
        elements: isRestore ?
          (window.currentLabel?.elements || []) : []
      };

      /* ================= SAFE BOUNDARY REBUILD ================= */

      // Remove ONLY boundary tagged rect
      const oldBoundary = labelGroup.querySelector('[data-boundary="true"]');
      if (oldBoundary) oldBoundary.remove();

      /* ── FIX: remove and rebuild clipPath so it always matches the label size ── */
      const OLD_CLIP_ID = "labelContentClip";
      const oldClip = labelGroup.querySelector(`#${OLD_CLIP_ID}`) ||
                      document.getElementById(OLD_CLIP_ID);
      if (oldClip) oldClip.remove();

      const SVG_NS = "http://www.w3.org/2000/svg";

      /* ClipPath — prevents any element (text, image, QR) from bleeding outside */
      const clipPath = document.createElementNS(SVG_NS, "clipPath");
      clipPath.setAttribute("id", OLD_CLIP_ID);
      const clipRect = document.createElementNS(SVG_NS, "rect");
      clipRect.setAttribute("x", 0);
      clipRect.setAttribute("y", 0);
      clipRect.setAttribute("width", widthMM);
      clipRect.setAttribute("height", heightMM);
      clipPath.appendChild(clipRect);
      labelGroup.appendChild(clipPath);

      /* Content group that is clipped — all design elements go inside this */
      const oldContentGroup = document.getElementById("labelContentGroup");
      if (oldContentGroup) oldContentGroup.remove();
      const contentGroup = document.createElementNS(SVG_NS, "g");
      contentGroup.setAttribute("id", "labelContentGroup");
      contentGroup.setAttribute("clip-path", `url(#${OLD_CLIP_ID})`);
      labelGroup.appendChild(contentGroup);

      const rect = document.createElementNS(SVG_NS, "rect");

      rect.setAttribute("x", 0);
      rect.setAttribute("y", 0);
      rect.setAttribute("width", widthMM);
      rect.setAttribute("height", heightMM);
      rect.setAttribute("fill", "none");
      rect.setAttribute("stroke", "#2563EB");
      rect.setAttribute("stroke-width", 0.4);
      rect.setAttribute("vector-effect", "non-scaling-stroke");
      rect.setAttribute("data-boundary", "true");

      // Always first child
      if (labelGroup.firstChild) {
        labelGroup.insertBefore(rect, labelGroup.firstChild);
      } else {
        labelGroup.appendChild(rect);
      }

      /* ================= NORMAL AUTO RENDER ================= */

      if (!isRestore && !window.__IS_RESTORING__) {
        renderModalFieldsToMainLabel();
      }

      /* ── FIX: fire any deferred field render that was queued before label existed ── */
      if (!isRestore && window.__pendingFieldRender) {
        window.__pendingFieldRender = false;
        renderModalFieldsToMainLabel();
      }

      /* ================= INTERACTION ================= */

      enableLabelInteraction();

      return labelGroup;
    }









    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<    FORM LOADING + SAVE FIELDS      >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

    async function fetchStickerForm() {

      if (!modalFieldsContainer) return;

      modalFieldsContainer.innerHTML = `
          <div style="text-align:center;color:#555;display:flex;gap:10px;align-items:center;justify-content:center;">
            <svg viewBox="0 0 240 240" width="40" height="40" class="loader">
              <circle stroke-linecap="round" stroke-dashoffset="-330" stroke-dasharray="0 660" stroke-width="20" stroke="#000" fill="none" r="105" cy="120" cx="120" class="loader-ring loader-ring-a"></circle>
              <circle stroke-linecap="round" stroke-dashoffset="-110" stroke-dasharray="0 220" stroke-width="20" stroke="#000" fill="none" r="35" cy="120" cx="120" class="loader-ring loader-ring-b"></circle>
              <circle stroke-linecap="round" stroke-dasharray="0 440" stroke-width="20" stroke="#000" fill="none" r="70" cy="120" cx="85" class="loader-ring loader-ring-c"></circle>
              <circle stroke-linecap="round" stroke-dasharray="0 440" stroke-width="20" stroke="#000" fill="none" r="70" cy="120" cx="155" class="loader-ring loader-ring-d"></circle>
            </svg>
            <p style="margin-top:10px;">Loading...</p>
          </div>
        `;

      try {
        const query = new URLSearchParams();

        if (currentModelName) {
          query.set("modelname", currentModelName);
        }

        if (currentUserId) {
          query.set("user_id", currentUserId);
        }

        const url = `<?= site_url('items/getFields') ?>${query.toString() ? '?' + query.toString() : ''}`;

        const res = await fetch(url, {
          headers: {
            "X-Requested-With": "XMLHttpRequest"
          }
        });

        if (!res.ok) {
          modalFieldsContainer.innerHTML =
            `<p style="color:red;text-align:center;">⚠ Server error ${res.status}</p>`;
          return;
        }

        const data = await res.json();
        modalFieldsContainer.innerHTML = '';

        if (!data || data.status !== 'success' || !Array.isArray(data.fields)) {
          modalFieldsContainer.innerHTML =
            '<p style="text-align:center;color:#555;">No fields available.</p>';
          return;
        }

        const form = data.fields.find(f => f.modelname === currentModelName) || data.fields[0] || null;

        if (!form || !form.form_data) {
          modalFieldsContainer.innerHTML =
            '<p style="text-align:center;color:#888;margin-bottom:10px">No fields found. Add fields to start.</p>';
          return;
        }

        let formData = form.form_data;

        if (typeof formData === 'string') {
          try {
            formData = JSON.parse(formData);
          } catch (e) {
            console.error("Invalid form_data JSON:", e);
            modalFieldsContainer.innerHTML =
              '<p style="text-align:center;color:#888;margin-bottom:10px">No valid fields found.</p>';
            return;
          }
        }

        const fields = Array.isArray(formData?.fields) ? formData.fields : [];

        if (!fields.length) {
          modalFieldsContainer.innerHTML =
            '<p style="text-align:center;color:#888;margin-bottom:10px">No fields found. Add fields to start.</p>';
          return;
        }

        fields.forEach(field => {
          addModalField(
            field?.name || 'Title',
            field?.value || '',
            field?.counting || '',
            field || null
          );
        });

        // if (window.currentLabel) {
        //   renderModalFieldsToMainLabel();
        // } else {
        //   /* ── FIX: label not ready yet — defer render to when mainLabel() fires ── */
        //   window.__pendingFieldRender = true;
        // }

      } catch (err) {
        console.error("Fetch failed:", err);
        modalFieldsContainer.innerHTML =
          '<p style="color:red;text-align:center;">⚠ Failed to load fields</p>';
      }
    }

    async function saveModalFields() {

      const fieldRows = document.querySelectorAll('.modal-field-row');

      if (!fieldRows.length) {
        showToast("Add at least one field before saving", "warning");
        return;
      }

      const fieldsJson = [];
      const namesSet = new Set();

      for (let row of fieldRows) {

        const nameInput = row.querySelector('.modal-field-name');
        const valueInput = row.querySelector('.modal-field-value');
        const countingInput = row.querySelector('.modal-field-counting');

        const inputTypeInput = row.querySelector('.modal-input-type');
        const keywordTypeInput = row.querySelector('.modal-keyword-type');
        const keywordValueTypeInput = row.querySelector('.modal-keyvalue-type');

        const name = nameInput?.value?.trim() || '';
        const value = valueInput?.value?.trim() || '';
        const counting = countingInput?.value?.trim() || '';

        const inputType = inputTypeInput?.value?.trim() || row.dataset.inputType || 'manual';
        const keywordType = keywordTypeInput?.value?.trim() || row.dataset.keywordType || 'variable';
        const keywordValueType = keywordValueTypeInput?.value?.trim() || row.dataset.keywordValueType || 'variable';

        if (!name) {
          showToast("Field Name cannot be empty", "warning");
          nameInput?.focus();
          return;
        }

        const dedupeKey = name.toLowerCase();

        if (name !== "Title" && namesSet.has(dedupeKey)) {
          showToast(`Duplicate field name: ${name}`, "warning");
          nameInput?.focus();
          return;
        }

        if (name !== "Title") {
          namesSet.add(dedupeKey);
        }

        fieldsJson.push({
          name: name,
          value: value,
          counting: counting,
          input_type: ['manual', 'qr', 'datetime', 'barcode'].includes(inputType) ? inputType : 'manual',
          keyword_type: ['constant', 'variable'].includes(keywordType) ? keywordType : 'variable',
          keyword_value_type: ['constant', 'variable'].includes(keywordValueType) ? keywordValueType : 'variable'
        });
      }

      const payload = {
        modelname: currentModelName || '',
        user_id: currentUserId || null,
        form_data: {
          version: 1,
          fields: fieldsJson
        }
      };

      if (!payload.modelname) {
        showToast("Model name missing", "error");
        return;
      }

      try {

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');

        const res = await fetch("<?= site_url('items/saveFields') ?>", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            ...(csrfMeta ? {
              "X-CSRF-TOKEN": csrfMeta.content
            } : {})
          },
          body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (data.status === "success") {

          showToast("Sticker form saved");

          if (window.currentLabel) {
            renderModalFieldsToMainLabel();
          }

        } else {
          showToast(data.message || "Save failed", "error");
        }

      } catch (err) {
        console.error("Save error:", err);
        showToast("Server error while saving", "error");
      }
    }

    function openModal() {
      if (!stickerModal) return;

      stickerModal.style.display = 'block';
    }

    function addModalField(name = '', value = '', counting = '', savedData = null) {

      if (!modalFieldsContainer) return;

      name = String(name ?? '').trim();
      value = String(value ?? '').trim();
      counting = String(counting ?? '').trim();

      if (!name) {
        name = 'Title';
      }

      const lowerName = name.toLowerCase();
      const isTitle = lowerName === 'title';

      const existingNames = Array.from(modalFieldsContainer.querySelectorAll('.modal-field-name'))
        .map(input => input.value.trim().toLowerCase());

      if (!isTitle && existingNames.includes(lowerName)) {
        return;
      }

      if (!document.querySelector('#qr-fields-heading')) {
        const heading = document.createElement('h5');
        heading.id = 'qr-fields-heading';
        heading.innerHTML = `
                <div class="c-q_head">
                  <span id="span-bar">Count</span>
                </div>
              `;
        heading.style.margin = '15px 0 6px 0';
        modalFieldsContainer.prepend(heading);
      }

      const row = document.createElement('div');
      row.className = 'form-group modal-field-row';
      row.style.marginBottom = '10px';

      row.dataset.inputType = savedData?.input_type || 'manual';
      row.dataset.keywordType = savedData?.keyword_type || 'variable';
      row.dataset.keywordValueType = savedData?.keyword_value_type || 'variable';

      const fieldRow = document.createElement('div');
      fieldRow.className = 'field-rows';

      const nameInput = document.createElement('input');
      nameInput.type = 'text';
      nameInput.className = 'modal-field-name form-control';
      nameInput.placeholder = 'Field Name';
      nameInput.value = name;
      nameInput.maxLength = 100;
      nameInput.style.cssText = `
                flex:0.6;
                min-width:85px;
                max-width:160px;
                padding:7px 10px;
                background:${savedData?.keyword_value_type === 'constant' ? '#e0e0e0' : '#fff'};
              `;

      const valueWrap = document.createElement('div');
      valueWrap.style.cssText = `
              position:relative;
              flex:1.2;
              min-width:85px;
              max-width:150px;
              display:flex;
              flex-direction:column;
            `;

      const valueInput = document.createElement('input');
      valueInput.type = 'text';
      valueInput.className = 'modal-field-value form-control';
      valueInput.placeholder = 'Field Value';
      valueInput.value = value;
      valueInput.maxLength = 250;
      valueInput.style.cssText = `
              width:100%;
              padding:7px 10px;
              background:${savedData?.keyword_type === 'constant' ? '#e0e0e0' : '#fff'};
            `;

      const counter = document.createElement('span');
      counter.textContent = `${value.length}/250`;
      counter.style.cssText = `
              font-size:5px;
              color:#888;
              text-align:right;
              line-height:1.2;
              right:4px;
              position:absolute;
              top:2px;
            `;

      valueInput.addEventListener('input', function() {
        counter.textContent = `${this.value.length}/250`;
      });

      valueWrap.appendChild(valueInput);
      valueWrap.appendChild(counter);

      const countingInput = document.createElement('input');
      countingInput.type = 'text';
      countingInput.className = 'modal-field-counting form-control';
      countingInput.value = counting;
      countingInput.maxLength = 50;
      countingInput.style.cssText = `
              flex:0.4;
              min-width:30px;
              max-width:35px;
              padding:7px 7px;
              text-align:center;
              background:#fff;
            `;

      const countCheckbox = document.createElement('input');
      countCheckbox.type = 'checkbox';
      countCheckbox.className = 'count-step-checkbox';
      countCheckbox.title = 'Count-step';
      countCheckbox.style.cssText = `
              transform:scale(1.2);
              cursor:pointer;
            `;



      fieldRow.appendChild(nameInput);
      fieldRow.appendChild(valueWrap);
      fieldRow.appendChild(countingInput);
      fieldRow.appendChild(countCheckbox);

      row.appendChild(fieldRow);
      modalFieldsContainer.appendChild(row);
    }

    

    function renderModalFieldsToMainLabel() {

      if (!window.currentLabel) return;
      if (!modalFieldsContainer) return;

      const labelGroup = document.getElementById("labelGroup");
      if (!labelGroup) return;

      /* ── FIX: Only remove field-text wrappers, NOT QR/barcode internal <text> nodes ── */
      labelGroup.querySelectorAll('[data-type="text"]').forEach(t => t.remove());

      window.currentLabel.elements = (window.currentLabel.elements || []).filter(
        el => !(el.id && el.id.startsWith("field_"))
      );

      const fieldRows = modalFieldsContainer.querySelectorAll('.modal-field-row');
      if (!fieldRows.length) return;

      const MARGIN_LEFT   = 4;   // mm
      const MARGIN_TOP    = 4;   // mm
      const LINE_GAP      = 1.5; // mm gap between fields
      const labelWidth    = window.currentLabel.width || 80;

      let currentY = MARGIN_TOP;

      fieldRows.forEach((row, idx) => {

        const fieldName  = row.querySelector('.modal-field-name')?.value?.trim()  || '';
        const fieldValue = row.querySelector('.modal-field-value')?.value?.trim() || '';

        if (!fieldValue) return;

        const isTitle   = fieldName.toLowerCase() === 'title';
        const fontSize  = isTitle ? 5 : 4;

        const text = isTitle
          ? fieldValue
          : `${fieldName}: ${fieldValue}`;

        const wrapper = addTextElement(
          text,
          MARGIN_LEFT,
          currentY,
          fontSize,
          `field_${idx}`
        );

        /* ── FIX: Advance Y by the actual number of wrapped lines, not a fixed step ── */
        const lineCount  = wrapper?._lineCount || 1;
        const lineHeight = fontSize * 1.3;           // matches <tspan dy>
        currentY += (lineCount * lineHeight) + LINE_GAP;
      });
    }

    function loadModalFieldsFromDB(savedData = {}) {
      if (!modalFieldsContainer) return;

      modalFieldsContainer.innerHTML = '';

      let formData = savedData.form_data || null;

      if (typeof formData === 'string') {
        try {
          formData = JSON.parse(formData);
        } catch (e) {
          console.error("Invalid form_data JSON:", e);
          formData = null;
        }
      }

      const fields = Array.isArray(formData?.fields) ? formData.fields : [];

      if (!fields.length) {
        modalFieldsContainer.innerHTML =
          '<p style="text-align:center;color:#888;margin-bottom:10px">No fields found. Add fields to start.</p>';
        return;
      }

      fields.forEach(field => {
        addModalField(
          field?.name || 'Title',
          field?.value || '',
          field?.counting || '',
          field || null
        );
      });

      /* ── FIX: remove fragile setTimeout — render now if label exists,
         otherwise set the deferred flag so mainLabel() will pick it up ── */
      if (window.currentLabel) {
        renderModalFieldsToMainLabel();
      } else {
        window.__pendingFieldRender = true;
      }
    }

    //Add Product Name + Model Name by default
    function addDefaultProductAndModelFields() {

      if (!modalFieldsContainer) return;

      /* ── FIX: idempotency guard — never run twice in the same page session ── */
      if (window.__defaultFieldsAdded) return;

      const productName = document.getElementById("productName")?.value?.trim() || '';
      const modelVal    = document.getElementById("modelInput")?.value?.trim()  || '';

      const existingRows = modalFieldsContainer.querySelectorAll('.modal-field-row');

      if (existingRows.length > 0) {
        /* Fields already loaded (e.g. from DB fetch) — nothing to add */
        window.__defaultFieldsAdded = true;
        return;
      }

      modalFieldsContainer.innerHTML = '';

      if (productName) {
        addModalField('Title', productName, '', {
          input_type: 'manual',
          keyword_type: 'variable',
          keyword_value_type: 'variable'
        });
      }

      if (modelVal) {
        addModalField('Model', modelVal, '', {
          input_type: 'manual',
          keyword_type: 'variable',
          keyword_value_type: 'variable'
        });
      }

      window.__defaultFieldsAdded = true;

      if (window.currentLabel) {
        renderModalFieldsToMainLabel();
      } else {
        window.__pendingFieldRender = true;
      }
    }
    //PAGE LOAD FLOW (CORRECT ORDER)
    window.addEventListener("DOMContentLoaded", async () => {

      /* ── FIX: initialise deferred-render flags before any async work ── */
      window.__pendingFieldRender = false;
      window.__defaultFieldsAdded = false;

      openModal();
      await fetchStickerForm();
      addDefaultProductAndModelFields();

    });








    /* <<<<<<<<<<<<<<<<<<<<<<<<<<     DESIGN ENGINE CORE  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

    const SVG_NS = "http://www.w3.org/2000/svg";

    /* ---------- CENTRAL STATE ---------- */

    window.DesignState = {
      selected: [],
      hover: null,
      elements: {},
      history: [],
      redoStack: [],
      snapping: true,
      gridSize: 1, // 1mm
      snapThreshold: 1.2
    };

    /* ---------- ID GENERATOR ---------- */

    function generateId(prefix = "el") {
      return prefix + "_" + Math.random().toString(36).substr(2, 9);
    }

    /* ---------- CREATE WRAPPER GROUP ---------- */

    function createDesignElement(type, xMM, yMM) {

      const g = document.createElementNS(SVG_NS, "g");

      const id = generateId(type);

      g.setAttribute("class", "design-element");
      g.setAttribute("data-id", id);
      g.setAttribute("data-type", type);
      g.setAttribute("data-rotation", "0");
      g.setAttribute("data-scale", "1");
      g.setAttribute("transform", `translate(${xMM}, ${yMM})`);

      DesignState.elements[id] = {
        id,
        type,
        x: xMM,
        y: yMM,
        rotation: 0,
        scale: 1
      };

      return g;
    }

    /* ---------- UPDATE POSITION ---------- */

    function updateElementPosition(el, xMM, yMM) {

      el.setAttribute("transform", `translate(${xMM}, ${yMM})`);

      const id = el.getAttribute("data-id");

      if (DesignState.elements[id]) {
        DesignState.elements[id].x = xMM;
        DesignState.elements[id].y = yMM;
      }
    }

    /* ---------- GET BOUNDING BOX IN MM ---------- */

    function getElementBBox(el) {
      const box = el.getBBox();
      return {
        x: box.x,
        y: box.y,
        width: box.width,
        height: box.height,
        centerX: box.x + box.width / 2,
        centerY: box.y + box.height / 2
      };
    }



    function rebuildDesignStateElements() {
      DesignState.elements = {};

      const all = document.querySelectorAll("#printPageGroup .design-element");

      all.forEach(el => {
        let id = el.getAttribute("data-id");
        const type = el.getAttribute("data-type") || "el";

        if (!id) {
          id = generateId(type);
          el.setAttribute("data-id", id);
        }

        const t = el.transform.baseVal.consolidate();
        const m = t ? t.matrix : workspaceSvg.createSVGMatrix();

        DesignState.elements[id] = {
          id,
          type,
          x: m.e,
          y: m.f,
          rotation: Math.atan2(m.b, m.a) * (180 / Math.PI),
          scaleX: Math.sqrt(m.a * m.a + m.b * m.b) || 1,
          scaleY: Math.sqrt(m.c * m.c + m.d * m.d) || 1
        };
      });
    }

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  SVG ENGINE  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>


    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  TEXT RENDER  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

    /* ─────────────────────────────────────────────────────────────────
       TEXT UTILITY — word-wrap long strings into SVG-friendly line array.
       Uses a hidden <canvas> for pixel-accurate measurement when available,
       with a conservative character-width estimate as fallback.
    ───────────────────────────────────────────────────────────────── */
    function wrapTextToLines(text, maxWidthMM, fontSizeMM, fontFamily) {
      if (!text) return [""];
      const safeMax = Math.max(maxWidthMM || 0, 10);

      // ── Try canvas measurement (accurate) ──
      let measureFn = null;
      try {
        const _canvas = document.createElement("canvas");
        const _ctx    = _canvas.getContext("2d");
        if (_ctx) {
          // 1mm ≈ 3.7795px at 96 dpi — close enough for label sizing
          _ctx.font = `${(fontSizeMM * 3.78).toFixed(1)}px ${fontFamily || "Arial"}`;
          measureFn = (w) => _ctx.measureText(w).width / 3.78;
        }
      } catch (_) { /* canvas blocked in some environments */ }

      // Fallback: proportional estimate per character
      const charWidthMM = fontSizeMM * 0.52;
      const measure = measureFn
        ? (w) => measureFn(w)
        : (w) => w.length * charWidthMM;

      const words  = String(text).split(" ");
      const result = [];
      let   cur    = "";

      for (const word of words) {
        const test = cur ? cur + " " + word : word;
        if (measure(test) > safeMax && cur !== "") {
          result.push(cur);
          cur = word;
        } else {
          cur = test;
        }
      }
      if (cur) result.push(cur);
      return result.length ? result : [""];
    }

    /* ─────────────────────────────────────────────────────────────────
       Helper — expand newline-delimited text AND auto-wrap each segment.
    ───────────────────────────────────────────────────────────────── */
    function expandAndWrapLines(content, maxWidthMM, fontSizeMM, fontFamily) {
      const raw = String(content || "").split("\n");
      return raw.flatMap(seg => wrapTextToLines(seg, maxWidthMM, fontSizeMM, fontFamily));
    }

    /* ─────────────────────────────────────────────────────────────────
       Helper — build <tspan> children on an existing <text> node.
    ───────────────────────────────────────────────────────────────── */
    function applyLinesToTextEl(textEl, lines, lineHeightMM, ns) {
      const NS = ns || SVG_NS;
      if (lines.length === 1) {
        textEl.textContent = lines[0];
      } else {
        textEl.textContent = "";
        lines.forEach((line, i) => {
          const tspan = document.createElementNS(NS, "tspan");
          tspan.setAttribute("x", 0);
          tspan.setAttribute("dy", i === 0 ? "0" : String(lineHeightMM));
          tspan.textContent = line;
          textEl.appendChild(tspan);
        });
      }
    }

    function addTextElement(text, xMM, yMM, fontSizeMM = 4, customId = "", targetGroup = null) {

      const labelGroup = targetGroup || document.getElementById("labelGroup");
      if (!labelGroup) return;

      const isJSON = typeof text === "object";

      let content, posX, posY, finalFontSize;
      let fontFamily = "Arial";
      let fill = "#000";
      let bold = false,
        italic = false,
        underline = false;
      let scaleX = 1,
        scaleY = 1,
        angle = 0;
      let id = customId;

      /*  JSON RESTORE MODE  */

      if (isJSON) {

        const data = text;

        content = data.text || "";
        posX = data.transform?.x || 0;
        posY = data.transform?.y || 0;

        finalFontSize = data.fontSize || 4;
        fontFamily = data.fontFamily || "Arial";
        fill = data.fill || "#000";

        bold = data.bold || false;
        italic = data.italic || false;
        underline = data.underline || false;

        scaleX = data.transform?.scaleX ?? 1;
        scaleY = data.transform?.scaleY ?? 1;
        angle = data.transform?.angle ?? 0;

        id = data.id || "";

      } else {

        /*  NORMAL MODE  */

        if (isNaN(xMM) || isNaN(yMM)) return;

        content = text || "";
        posX = xMM;
        posY = yMM;
        finalFontSize = fontSizeMM;
      }

      /* ---------- WRAPPER ---------- */

      let wrapper;

      if (isJSON) {

        wrapper = createDesignElement("text", 0, 0);

        wrapper.setAttribute(
          "transform",
          `translate(${posX}, ${posY}) rotate(${angle}) scale(${scaleX}, ${scaleY})`
        );

      } else {

        wrapper = createDesignElement("text", posX, posY);
      }

      if (id) wrapper.setAttribute("data-id", id);

      wrapper.setAttribute("data-type", "text");

      /* ---------- TEXT NODE ---------- */

      const textEl = document.createElementNS(SVG_NS, "text");

      textEl.setAttribute("x", 0);
      textEl.setAttribute("y", 0);
      textEl.setAttribute("font-size", finalFontSize);
      textEl.setAttribute("font-family", fontFamily);
      textEl.setAttribute("fill", fill);
      textEl.setAttribute("dominant-baseline", "hanging");

      if (bold) textEl.setAttribute("font-weight", "bold");
      if (italic) textEl.setAttribute("font-style", "italic");
      if (underline) textEl.setAttribute("text-decoration", "underline");

      /* ── Multi-line + word-wrap support ──
         Available width = label width minus left-margin (posX) and 2mm right padding.
         Falls back to 80mm if label state is not yet set.                           */
      const availableW = Math.max(
        ((window.currentLabel?.width || 82) - posX - 2),
        10
      );
      const lineH  = finalFontSize * 1.3;
      const lines  = expandAndWrapLines(content, availableW, finalFontSize, fontFamily);

      applyLinesToTextEl(textEl, lines, lineH, SVG_NS);

      /* Expose line count so callers can compute accurate Y advancement */
      wrapper._lineCount = lines.length;

      wrapper.appendChild(textEl);
      labelGroup.appendChild(wrapper);

      return wrapper;
    }

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< SVG QR (VECTOR)  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

    /* DOM HELPER*/
    const $ = (id) => document.getElementById(id);
    let Main_NodeId = "";

    /* QR SIZE GETTER  */
    function getQrSizeMm() {
      const el = $("qrSizeInput");
      const value = parseFloat(el?.value);
      return Number.isFinite(value) ? value : 12;
    }

    /*  BUILD QR CONTENT (PURE) */
    function buildQRContent(nodeIdOverride = null, countIndex = null) {

      // const nodeId = nodeIdOverride || $("nodeDropdown")?.value?.trim();
      // if (!nodeId) return null;

      const nodeId = String(nodeIdOverride || Main_NodeId || "").trim();
      if (!nodeId) return null;

      const model = $("modelInput")?.value?.trim() || "";

      let content = `EF/${model}/${nodeId}`;

      const rows = document.querySelectorAll(
        "#modalFieldsContainer .modal-field-row"
      );

      rows.forEach(row => {

        const include = row.querySelector(".qr-include-checkbox");
        if (!include?.checked) return;

        const name = row.querySelector(".modal-field-name")?.value?.trim();
        let value = row.querySelector(".modal-field-value")?.value?.trim();

        if (!name || !value) return;

        // Batch counting support
        if (countIndex !== null) {
          const countCb = row.querySelector(".count-step-checkbox");
          const startVal = parseInt(
            row.querySelector(".modal-field-counting")?.value || 0,
            10
          );

          if (countCb?.checked && Number.isFinite(startVal)) {
            value = startVal + countIndex;
          }
        }

        content += `/${name}:${value}`;
      });

      return content;
    }

    /* REMOVE QR */

    function removeQR(clearNodeSelection = false) {
      const labelGroup = document.getElementById("labelGroup");
      if (!labelGroup) return;

      labelGroup
        .querySelectorAll('[data-type="qr"], [data-type="qr-center"]')
        .forEach(el => el.remove());

      if (!window.currentLabel) {
        /* ── FIX: never overwrite currentLabel with a bare {} — it would lose width/height ── */
        console.warn("[removeQR] called before currentLabel was initialised — skipping state update");
        return;
      }

      if (!Array.isArray(window.currentLabel.elements)) {
        window.currentLabel.elements = [];
      } else {
        window.currentLabel.elements =
          window.currentLabel.elements.filter(e => e.type !== "qr");
      }

      // if (clearNodeSelection) {
      //   const nodeDropdown = document.getElementById("nodeDropdown");
      //   if (nodeDropdown) nodeDropdown.value = "";
      // }

      if (clearNodeSelection) {
        Main_NodeId = "";

        const nodeInput = document.getElementById("Nodeinput");
        if (nodeInput) nodeInput.value = "";
      }
    }


    function renderQR(data, sizeMM, targetGroup = null) {

      const parentGroup = targetGroup || document.getElementById("labelGroup");
      if (!parentGroup) return;

      /* ================= MODE DETECTION ================= */

      const options = arguments[3] || {};
      const isJSON = options.fromJSON === true;

      let value, finalSize, xMM, yMM;
      let efEnabled = true;
      let efText = "EF";

      /*  JSON RESTORE MODE  */

      if (isJSON) {

        value = data;
        finalSize = sizeMM;

        // In restore mode position handled by restore()
        xMM = 0;
        yMM = 0;

        efEnabled = options.efEnabled ?? true;
        efText = options.efText || "EF";

      } else {

        /* ================= NORMAL MODE ================= */

        if (!window.currentLabel) return;

        if (!targetGroup) removeQR();

        const margin = 4;

        let baseWidth = window.currentLabel.width;

        if (targetGroup && targetGroup.hasAttribute("data-width-mm")) {
          baseWidth = parseFloat(targetGroup.getAttribute("data-width-mm"));
        }

        value = data;
        finalSize = sizeMM;

        xMM = baseWidth - finalSize - margin;
        yMM = margin;
      }

      /*  QR GENERATION  */

      const qr = qrcode(0, "H");
      qr.addData(value);
      qr.make();

      const moduleCount = qr.getModuleCount();
      const quietZone = 4;
      const totalModules = moduleCount + quietZone * 2;
      const cellSize = finalSize / totalModules;

      const svgNS = "http://www.w3.org/2000/svg";

      /* ================= WRAPPER ================= */

      const wrapper = createDesignElement("qr", xMM, yMM);

      wrapper.dataset.type = "qr";
      wrapper.dataset.value = value;
      wrapper.dataset.size = finalSize;
      wrapper.dataset.ef = efEnabled ? "true" : "false";
      wrapper.dataset.efText = efText;

      /* ================= SVG ROOT ================= */

      const svg = document.createElementNS(svgNS, "svg");
      svg.setAttribute("width", finalSize);
      svg.setAttribute("height", finalSize);
      svg.setAttribute("viewBox", `0 0 ${finalSize} ${finalSize}`);
      svg.setAttribute("shape-rendering", "crispEdges");

      /* ================= BACKGROUND ================= */

      const bg = document.createElementNS(svgNS, "rect");
      bg.setAttribute("width", finalSize);
      bg.setAttribute("height", finalSize);
      bg.setAttribute("fill", "#fff");
      svg.appendChild(bg);

      /* ================= MODULES ================= */

      for (let r = 0; r < moduleCount; r++) {
        for (let c = 0; c < moduleCount; c++) {

          if (!qr.isDark(r, c)) continue;

          const rect = document.createElementNS(svgNS, "rect");
          rect.setAttribute("x", (c + quietZone) * cellSize);
          rect.setAttribute("y", (r + quietZone) * cellSize);
          rect.setAttribute("width", cellSize);
          rect.setAttribute("height", cellSize);
          rect.setAttribute("fill", "#000");

          svg.appendChild(rect);
        }
      }

      /* EF OVERLAY (ALWAYS LAST)  */

      if (efEnabled) {

        const overlay = document.createElementNS(svgNS, "g");


        const logoSize = finalSize * 0.16;

        const knockout = document.createElementNS(svgNS, "rect");

        /* square same size as logo */
        const boxSize = logoSize;

        knockout.setAttribute("x", finalSize / 2 - boxSize / 2);
        knockout.setAttribute("y", finalSize / 2 - boxSize / 2);
        knockout.setAttribute("width", boxSize);
        knockout.setAttribute("height", boxSize);
        knockout.setAttribute("fill", "#fff");
        knockout.setAttribute("stroke", "#000000");
        knockout.setAttribute("stroke-width", 0.2);

        const text = document.createElementNS(svgNS, "text");
        text.setAttribute("x", finalSize / 2);
        text.setAttribute("y", finalSize / 2);
        text.setAttribute("text-anchor", "middle");
        text.setAttribute("dominant-baseline", "middle");
        text.setAttribute("dy", "0.11em");
        text.setAttribute("font-size", logoSize * 0.70);
        text.setAttribute("font-weight", "bold");
        text.setAttribute("fill", "#000");
        text.setAttribute("pointer-events", "none");
        text.textContent = efText;

        overlay.appendChild(knockout);
        overlay.appendChild(text);

        // MUST BE LAST CHILD OF SVG
        svg.appendChild(overlay);
      }

      /* ================= FINAL ATTACH ================= */

      wrapper.appendChild(svg);

      // Ensure QR is top-most element in label
      parentGroup.appendChild(wrapper);

      /* ================= NORMAL MODE SAVE ================= */

      if (!isJSON && !targetGroup) {

        window.currentLabel.elements.push({
          type: "qr",
          value: value,
          transform: {
            x: xMM,
            y: yMM,
            angle: 0,
            scaleX: 1,
            scaleY: 1
          },
          size: finalSize,
          efEnabled,
          efText
        });
      }

      return wrapper;
    }

    /* Valida QR capacityt  */
    function validateQRCapacity(data, sizeMM) {

      try {

        const qr = qrcode(0, "H"); // use M instead of H (less dense)
        qr.addData(data);
        qr.make();

        const moduleCount = qr.getModuleCount();

        const QUIET_ZONE = 4; // ISO standard
        const totalModules = moduleCount + (QUIET_ZONE * 2);

        const moduleSizeMM = sizeMM / totalModules;

        //  Optimized safe value
        const MIN_MODULE_MM = 0.28;

        if (moduleSizeMM < MIN_MODULE_MM) {

          const requiredSize = totalModules * MIN_MODULE_MM;

          return {
            valid: false,
            requiredSize: requiredSize.toFixed(2)
          };
        }

        return {
          valid: true
        };

      } catch (err) {
        return {
          valid: false
        };
      }
    }

    /* LIVE UPDATE CONTROLLER  */
    function updateQRLive(forceRebuild = false) {
      if (!window.currentLabel) return;

      const labelGroup = document.getElementById("labelGroup");
      if (!labelGroup) return;

      let nodeId = String(Main_NodeId || "").trim();

      /* ── FALLBACK: after DB restore Main_NodeId may not be set yet ──
         Read it directly from the existing QR element's encoded value.
         QR value format:  EF / model / nodeId / ...               */
      if (!nodeId) {
        const existingQR = labelGroup.querySelector('[data-type="qr"]');
        if (existingQR?.dataset?.value) {
          const parts = existingQR.dataset.value.split("/");
          if (parts.length >= 3) {
            nodeId = parts[2];
            Main_NodeId = nodeId; // cache for future calls
            const nodeInput = document.getElementById("Nodeinput");
            if (nodeInput && !nodeInput.value) nodeInput.value = nodeId;
          }
        }
      }

      if (!nodeId) {
        removeQR();
        return;
      }

      const content = buildQRContent();
      if (!content) {
        removeQR();
        return;
      }

      const size = getQrSizeMm();
      const validation = validateQRCapacity(content, size);

      if (!validation.valid) {
        removeQR();

        if (validation.error) {
          showToast("QR data too large for capacity", "error");
        } else {
          showToast(
            `QR too dense. Increase size to at least ${validation.requiredSize} mm`, "error");
        }
        return;
      }

      const existingQR = labelGroup.querySelector('[data-type="qr"]');

      if (
        !forceRebuild &&
        existingQR &&
        existingQR.dataset.value === content &&
        parseFloat(existingQR.dataset.size || 0) === size
      ) {
        return;
      }

      /* ── Preserve the QR's dragged position across size rebuilds ── */
      const savedQRTransform = existingQR ?
        existingQR.getAttribute("transform") :
        null;

      removeQR();
      const newQRWrapper = renderQR(content, size);

      /* Restore saved position so the QR doesn't jump to default corner */
      if (savedQRTransform && newQRWrapper) {
        newQRWrapper.setAttribute("transform", savedQRTransform);
      }
    }

    /* FETCH NODE IDS  */
    // async function fetchNodeIds(modelname) {

    //   const dropdown = $("nodeDropdown");
    //   if (!dropdown) return;

    //   try {

    //     const response = await fetch(
    //       `/index.php/items/getNodeId?modelname=${encodeURIComponent(modelname)}`
    //     );

    //     const data = await response.json();

    //     dropdown.innerHTML = '<option value="">--Select Node ID--</option>';

    //     if (data?.status === "success" && Array.isArray(data.node_ids)) {

    //       data.node_ids.forEach(id => {
    //         const opt = document.createElement("option");
    //         opt.value = id;
    //         opt.textContent = id;
    //         dropdown.appendChild(opt);
    //       });

    //     }

    //   } catch (err) {
    //     console.error("Node ID fetch failed:", err);
    //   }
    // }

    async function fetchNodeIds(modelname) {
      try {
        const response = await fetch(
          `/index.php/items/getNodeId?modelname=${encodeURIComponent(modelname)}`
        );

        const data = await response.json();

        Main_NodeId = "";

        if (
          data?.status === "success" &&
          Array.isArray(data.node_ids) &&
          data.node_ids.length > 0
        ) {
          Main_NodeId = String(data.node_ids[0] || "").trim();

          const nodeInput = document.getElementById("Nodeinput");
          if (nodeInput) {
            nodeInput.value = Main_NodeId;
          }

          // updateQRLive(true);
          updateAllLabelQRsNodeId(Main_NodeId);
          return;
        }

        const nodeInput = document.getElementById("Nodeinput");
        if (nodeInput) {
          nodeInput.value = "";
        }

        removeQR(true);
        showToast("No available Node ID found", "warning");
      } catch (err) {
        console.error("Node ID fetch failed:", err);

        Main_NodeId = "";

        const nodeInput = document.getElementById("Nodeinput");
        if (nodeInput) {
          nodeInput.value = "";
        }

        removeQR(true);
        showToast("Failed to fetch Node ID", "error");
      }
    }


    /*  INITIALIZATION */
    // document.addEventListener("DOMContentLoaded", function() {
    //   const modelname = "<?= $modelname ?>";

    //   fetchNodeIds(modelname);

    //   const nodeDropdown = document.getElementById("nodeDropdown");
    //   const qrSizeInput = document.getElementById("qrSizeInput");
    //   const modalContainer = document.getElementById("modalFieldsContainer");



    //   function syncNodeAndQR(force = false) {
    //     const nodeId = nodeDropdown?.value?.trim() || "";
    //     if (!nodeId) {
    //       removeQR();
    //       return;
    //     }

    //     // Always build/update QR for current main label
    //     updateQRLive(true);

    //     // Then update copied labels if they exist
    //     const printPageGroup = document.getElementById("printPageGroup");
    //     const copyQrs = printPageGroup?.querySelectorAll('[data-copy="true"] .design-element[data-type="qr"]') || [];

    //     if (copyQrs.length) {
    //       updateAllLabelQRsNodeId(nodeId);
    //     }
    //   }

    //   /* normal node selection */
    //   nodeDropdown?.addEventListener("change", function() {
    //     syncNodeAndQR(true);
    //   });

    //   /* same selected node clicked again -> rebuild */
    //   nodeDropdown?.addEventListener("click", function() {
    //     if (this.value?.trim()) {
    //       syncNodeAndQR(true);
    //     }
    //   });

    //   /* keyboard / focus exit case */
    //   nodeDropdown?.addEventListener("blur", function() {
    //     if (this.value?.trim()) {
    //       syncNodeAndQR(true);
    //     }
    //   });

    //   /* qr size changed */
    //   qrSizeInput?.addEventListener("change", function() {
    //     updateQRLive(true);
    //   });

    //   /* checkbox / select changes inside modal */
    //   modalContainer?.addEventListener("change", function(e) {
    //     if (
    //       e.target.classList.contains("qr-include-checkbox") ||
    //       e.target.classList.contains("modal-field-value") ||
    //       e.target.classList.contains("modal-field-name")
    //     ) {
    //       updateQRLive(true);
    //     }
    //   });

    //   /* typing changes */
    //   modalContainer?.addEventListener("input", function(e) {
    //     if (
    //       e.target.classList.contains("modal-field-value") ||
    //       e.target.classList.contains("modal-field-name")
    //     ) {
    //       updateQRLive(true);
    //     }
    //   });
    // });


    document.addEventListener("DOMContentLoaded", function() {
      const modelname = "<?= $modelname ?>";

      // fetchNodeIds(modelname);

      const qrSizeInput = document.getElementById("qrSizeInput");
      const modalContainer = document.getElementById("modalFieldsContainer");

      /* qr size changed */
      qrSizeInput?.addEventListener("change", function() {
        updateQRLive(true);
      });

      /* checkbox / select changes inside modal */
      modalContainer?.addEventListener("change", function(e) {
        if (
          e.target.classList.contains("qr-include-checkbox") ||
          e.target.classList.contains("modal-field-value") ||
          e.target.classList.contains("modal-field-name")
        ) {
          updateQRLive(true);
        }
      });

      /* typing changes */
      modalContainer?.addEventListener("input", function(e) {
        if (
          e.target.classList.contains("modal-field-value") ||
          e.target.classList.contains("modal-field-name")
        ) {
          updateQRLive(true);
        }
      });
    });

    function replaceNodeIdInQRValue(qrValue, newNodeId) {
      const value = String(qrValue || "").trim();
      const nodeId = String(newNodeId || "").trim();

      if (!value || !nodeId) return value;

      const parts = value.split("/");

      // Expected: EF/model/node_id/...
      if (parts.length >= 3) {
        parts[2] = nodeId;
        return parts.join("/");
      }

      return value;
    }

    function updateAllLabelQRsNodeId(newNodeId) {
      const printPageGroup = document.getElementById("printPageGroup");
      if (!printPageGroup) return;

      const qrWrappers = printPageGroup.querySelectorAll('.design-element[data-type="qr"]');

      qrWrappers.forEach(wrapper => {
        const oldValue = wrapper.dataset.value || "";
        const newValue = replaceNodeIdInQRValue(oldValue, newNodeId);

        if (!newValue || newValue === oldValue) return;

        const svg = wrapper.querySelector("svg");
        if (!svg) return;

        const size =
          parseFloat(wrapper.dataset.size || svg.getAttribute("width") || 12) || 12;

        const efEnabled = wrapper.dataset.ef === "true";
        const efText = wrapper.dataset.efText || "EF";

        const transform = wrapper.getAttribute("transform") || "translate(0,0)";
        const parent = wrapper.parentNode;

        if (!parent) return;

        wrapper.remove();

        const newWrapper = renderQR(
          newValue,
          size,
          parent, {
            fromJSON: true,
            efEnabled,
            efText
          }
        );

        if (newWrapper) {
          newWrapper.setAttribute("transform", transform);
        }
      });
    }

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<  SVG BARCODE (VECTOR)   >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

    /*  BARCODE ENGINE – SVG + MM BASED */

    function renderBarcode(value, format, widthMM, heightMM, targetGroup = null) {

      const labelGroup = targetGroup || document.getElementById("labelGroup");
      if (!labelGroup) return;

      const isJSON = typeof value === "object";

      let finalValue, finalFormat, finalWidth, finalHeight;
      let xMM, yMM;
      let scaleX = 1,
        scaleY = 1,
        angle = 0;

      /*  JSON RESTORE MODE */

      if (isJSON) {

        const data = value;

        finalValue = data.value || "";
        finalFormat = data.format || "code128";
        finalWidth = data.width || 40;
        finalHeight = data.height || 12;

        xMM = data.transform?.x || 0;
        yMM = data.transform?.y || 0;

        scaleX = data.transform?.scaleX ?? 1;
        scaleY = data.transform?.scaleY ?? 1;
        angle = data.transform?.angle ?? 0;

      } else {

        if (!window.currentLabel) return;
        if (!targetGroup) removeBarcode();

        const OUTER_MARGIN_MM = 4;

        let baseHeight = window.currentLabel.height;

        if (targetGroup && targetGroup.hasAttribute("data-height-mm")) {
          baseHeight = parseFloat(targetGroup.getAttribute("data-height-mm"));
        }

        finalValue = value;
        finalFormat = format || "code128";
        finalWidth = widthMM;
        finalHeight = heightMM || 12;

        xMM = OUTER_MARGIN_MM;
        yMM = baseHeight - finalHeight - OUTER_MARGIN_MM;
      }

      /*  VALIDATION */

      if (!finalValue || finalValue.trim() === "") {
        showToast("Barcode value is empty", "warning");
        return;
      }

      /*  SCAN SAFE CALCULATION  */

      const svgNS = "http://www.w3.org/2000/svg";

      const tempSvg = document.createElementNS(svgNS, "svg");

      JsBarcode(tempSvg, finalValue, {
        format: finalFormat,
        width: 1,
        height: 10,
        displayValue: false,
        margin: 0
      });

      const bars = tempSvg.querySelectorAll("rect");
      if (!bars.length) {
        showToast("Barcode generation failed", "error");
        return;
      }

      let maxRight = 0;
      bars.forEach(bar => {
        const x = parseFloat(bar.getAttribute("x"));
        const w = parseFloat(bar.getAttribute("width"));
        const right = x + w;
        if (right > maxRight) maxRight = right;
      });

      const intrinsicUnits = maxRight;

      /* ===== DPI SAFE MODULE WIDTH ===== */

      const PRINTER_DPI = window.printSession?.dpi || 203;
      const DOT_MM = 25.4 / PRINTER_DPI;

      /* 2 dots per module = scan-safe standard for 203 DPI */
      const MODULE_DOTS = PRINTER_DPI === 300 ? 3 : 2;
      const moduleWidthMM = DOT_MM * MODULE_DOTS;

      /* ===== Quiet Zone (10 dots each side) ===== */
      const QUIET_ZONE_MM = DOT_MM * 10;

      const actualBarcodeWidth = intrinsicUnits * moduleWidthMM;
      const requiredWidth = actualBarcodeWidth + (QUIET_ZONE_MM * 2);

      if (requiredWidth > finalWidth) {
        showToast("Label width too small for this barcode data", "warning");
        return;
      }

      /*  FINAL SVG BUILD  */

      const svg = document.createElementNS(svgNS, "svg");

      svg.setAttribute("width", finalWidth);
      svg.setAttribute("height", finalHeight);
      svg.setAttribute("viewBox", `0 0 ${finalWidth} ${finalHeight}`);
      svg.setAttribute("shape-rendering", "crispEdges");

      JsBarcode(svg, finalValue, {
        format: finalFormat,
        width: moduleWidthMM,
        height: finalHeight,
        displayValue: false,
        margin: QUIET_ZONE_MM,
        flat: true
      });

      /* WRAPPER  */

      let wrapper;

      if (isJSON) {

        wrapper = createDesignElement("barcode", 0, 0);

        wrapper.setAttribute(
          "transform",
          `translate(${xMM}, ${yMM}) rotate(${angle}) scale(${scaleX}, ${scaleY})`
        );

      } else {

        wrapper = createDesignElement("barcode", xMM, yMM);
      }

      wrapper.dataset.type = "barcode";
      wrapper.dataset.value = finalValue;
      wrapper.dataset.format = finalFormat;
      wrapper.dataset.width = finalWidth;
      wrapper.dataset.height = finalHeight;

      wrapper.appendChild(svg);
      labelGroup.appendChild(wrapper);

      /* Save only in normal mode */

      if (!isJSON && !targetGroup) {

        window.currentLabel.elements.push({
          type: "barcode",
          value: finalValue,
          format: finalFormat,
          transform: {
            x: xMM,
            y: yMM,
            angle: 0,
            scaleX: 1,
            scaleY: 1
          },
          width: finalWidth,
          height: finalHeight
        });
      }

      return wrapper;
    }


    function removeBarcode() {

      const labelGroup = $("labelGroup");
      if (!labelGroup) return;

      labelGroup
        .querySelectorAll('[data-type="barcode"]')
        .forEach(el => el.remove());

      if (window.currentLabel) {
        window.currentLabel.elements =
          window.currentLabel.elements.filter(e => e.type !== "barcode");
      }
    }


    /*  BARCODE DENSITY VALIDATOR Prevents unreadable ultra-dense codes  */

    function validateBarcodeDensity(value, format, widthMM) {

      try {

        const svgNS = "http://www.w3.org/2000/svg";
        const tempSvg = document.createElementNS(svgNS, "svg");

        JsBarcode(tempSvg, value, {
          format: format,
          width: 1,
          height: 10,
          displayValue: false,
          margin: 0
        });

        const bars = tempSvg.querySelectorAll("rect");
        if (!bars.length) {
          return {
            valid: false
          };
        }

        let totalUnits = 0;
        bars.forEach(bar => {
          totalUnits += parseFloat(bar.getAttribute("width"));
        });

        const QUIET_ZONE_MM = 2;
        const usableWidth = widthMM - (QUIET_ZONE_MM * 2);

        const moduleWidthMM = usableWidth / totalUnits;

        const MIN_MODULE_MM = 0.26; // 203 DPI safe threshold

        if (moduleWidthMM < MIN_MODULE_MM) {
          return {
            valid: false,
            reason: "too_dense"
          };
        }

        return {
          valid: true
        };

      } catch (err) {
        return {
          valid: false
        };
      }
    }


    function generateSVGBarcode() {

      if (!window.currentLabel) {
        showToast("Create label first", "warning");
        return;
      }

      const value = $("barcodeValue")?.value?.trim();
      const type = $("barcodeType")?.value;

      if (!value || !type) {
        showToast("Enter barcode value", "warning");
        return;
      }

      const validation = validateBarcode(value, type);
      if (validation !== true) {
        showToast(validation, "error");
        return;
      }

      const sizeConfig = getBarcodeSizeConfig();

      const widthMM = window.currentLabel.width - 8; // 4mm margin both sides
      const heightMM = sizeConfig.heightMM;

      renderBarcode(value, type, widthMM, heightMM);
    }

    /* BARCODE SIZE CONFIG (DEFAULT = SMALL) */
    function getBarcodeSizeConfig() {

      const size = $("barcodeSize")?.value || "small"; // default small

      switch (size) {

        case "large":
          return {
            heightMM: 16
          };

        case "xlarge":
          return {
            heightMM: 20
          };

        case "medium":
          return {
            heightMM: 12
          };
        case "verysmall":
          return {
            heightMM: 6
          }

        case "small":
        default:
          return {
            heightMM: 8
          }; // default small height
      }
    }

    function validateBarcode(value, type) {
      const onlyNumbers = /^[0-9]+$/;
      const code39Chars = /^[0-9A-Z\-\.\ \$\/\+\%]+$/;

      switch (type) {
        case 'code128':
          return value ? true : "Code128 requires a value (any ASCII).";
        case 'code39':
          return code39Chars.test(value) ? true : "Code39 allows only A–Z, 0–9, and - . $ / + % SPACE.";
        case 'ean13':
          if (!onlyNumbers.test(value)) return "EAN13 must be numeric only.";
          if (value.length !== 12) return "EAN13 requires exactly 12 digits (13th is checksum).";
          return true;
        case 'upc':
          if (!onlyNumbers.test(value)) return "UPC-A must be numeric only.";
          if (value.length !== 11) return "UPC-A requires exactly 11 digits (12th is checksum).";
          return true;
        case 'itf':
          if (!onlyNumbers.test(value)) return "ITF requires numeric only.";
          if (value.length % 2 !== 0) return "ITF requires an even number of digits.";
          return true;
        case 'gs1databar':
          if (!onlyNumbers.test(value)) return "GS1 DataBar requires numeric only.";
          if (value.length !== 14) return "GS1 DataBar requires exactly 14 digits (GTIN).";
          return true;
        default:
          return "Please select a barcode type.";
      }
    }


    $("includeBarcode")?.addEventListener("change", function() {

      const section = $("barcodeSection");

      if (this.value === "yes") {
        section?.classList.remove("hidden");
      } else {
        section?.classList.add("hidden");
        removeBarcode();
      }

    });


    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<   SAVE AND RESTORE    >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    // saving layout design 
    async function saveLayoutData() {

      try {

        if (!window.currentPrintPage) {
          showToast("Create print page first", "warning");
          return;
        }

        const printPageGroup = document.getElementById("printPageGroup");
        if (!printPageGroup) return;

        const allLabels = [];

        const labelGroups = printPageGroup.querySelectorAll(
          ':scope > g#labelGroup, :scope > g[data-copy="true"]'
        );

        let labelIndex = 1;
        let copyIndex = 1;

        labelGroups.forEach(group => {

          const isMain = group.id === "labelGroup";

          const widthMM = parseFloat(
            group.getAttribute("data-width-mm")
          ) || 0;

          const heightMM = parseFloat(
            group.getAttribute("data-height-mm")
          ) || 0;

          /* ================= LABEL POSITION ================= */

          const groupTransform = group.transform.baseVal.consolidate();
          const groupMatrix = groupTransform ? groupTransform.matrix : null;

          const labelData = {
            labelNumber: labelIndex,
            type: isMain ? "main" : "copy",
            widthMM,
            heightMM,
            position: {
              x: groupMatrix ? groupMatrix.e : 0,
              y: groupMatrix ? groupMatrix.f : 0
            },
            elements: []
          };

          let qrCount = 1;
          let barcodeCount = 1;
          let imageCount = 1;
          let textCount = 1;

          /* ================= ELEMENT LOOP ================= */

          group.querySelectorAll(".design-element").forEach(el => {

            const type = el.dataset.type;
            if (!type) return;

            const transform = el.transform.baseVal.consolidate();
            const matrix = transform ? transform.matrix : null;

            let x = 0,
              y = 0,
              scaleX = 1,
              scaleY = 1,
              angle = 0;

            if (matrix) {
              x = matrix.e;
              y = matrix.f;

              // Extract scale
              scaleX = Math.sqrt(matrix.a * matrix.a + matrix.b * matrix.b);
              scaleY = Math.sqrt(matrix.c * matrix.c + matrix.d * matrix.d);

              // Extract rotation
              angle = Math.atan2(matrix.b, matrix.a) * (180 / Math.PI);
            }

            const elementData = {
              type,
              transform: {
                x,
                y,
                angle,
                scaleX,
                scaleY
              }
            };

            /* ================= TEXT ================= */

            if (type === "text") {

              const textNode = el.querySelector("text");
              if (!textNode) return;

              elementData.id = isMain ?
                `txtl${textCount}` :
                `txtc${copyIndex}_${textCount}`;

              /* ── Multi-line: join tspan children with \n ── */
              const tspanNodes = textNode.querySelectorAll("tspan");
              elementData.text = tspanNodes.length > 0 ?
                Array.from(tspanNodes).map(t => t.textContent).join("\n") :
                textNode.textContent || "";
              elementData.fontSize = parseFloat(
                textNode.getAttribute("font-size") || 4
              );
              elementData.fontFamily =
                textNode.getAttribute("font-family") || "Arial";
              elementData.fill =
                textNode.getAttribute("fill") || "#000000";

              elementData.bold =
                textNode.getAttribute("font-weight") === "bold";
              elementData.italic =
                textNode.getAttribute("font-style") === "italic";
              elementData.underline =
                textNode.getAttribute("text-decoration") === "underline";

              textCount++;
            }

            /* ================= QR ================= */

            if (type === "qr") {

              elementData.id = isMain ?
                `qrl${qrCount}` :
                `qrc${copyIndex}_${qrCount}`;

              elementData.value = el.dataset.value || "";
              elementData.size = parseFloat(el.dataset.size || 14);
              elementData.efEnabled = el.dataset.ef === "true";
              elementData.efText = el.dataset.efText || "EF";

              qrCount++;
            }

            /* ================= BARCODE ================= */

            if (type === "barcode") {

              elementData.id = isMain ?
                `bcl${barcodeCount}` :
                `bcc${copyIndex}_${barcodeCount}`;

              elementData.value = el.dataset.value || "";
              elementData.format = el.dataset.format || "code128";
              elementData.width = parseFloat(el.dataset.width || 40);
              elementData.height = parseFloat(el.dataset.height || 10);

              barcodeCount++;
            }

            /* ================= IMAGE ================= */

            if (type === "image") {

              const img = el.querySelector("image");
              if (!img) return;

              elementData.id = isMain ?
                `imgl${imageCount}` :
                `imgc${copyIndex}_${imageCount}`;

              elementData.src = img.getAttribute("href") || "";
              elementData.width = parseFloat(
                img.getAttribute("width") || 20
              );
              elementData.height = parseFloat(
                img.getAttribute("height") || 20
              );

              imageCount++;
            }

            labelData.elements.push(elementData);
          });

          allLabels.push(labelData);

          if (!isMain) copyIndex++;
          labelIndex++;
        });

        /* ================= SNAPSHOT ================= */

        const snapshot = {
          version: 10,
          timestamp: Date.now(),
          printPage: {
            widthMM: window.currentPrintPage.width,
            heightMM: window.currentPrintPage.height
          },
          labels: allLabels
        };

        const payload = {
          modelname: currentModelName,
          user_id: currentUserId,
          full_snapshot: JSON.stringify(snapshot)
        };

        const res = await fetch("<?= site_url('items/saveFields') ?>", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "<?= csrf_hash() ?>"
          },
          body: JSON.stringify(payload)
        });

        const result = await res.json();

        if (result.status === "success") {
          showToast("Layout saved successfully", "success");
        } else {
          showToast("Save failed", "error");
        }

      } catch (err) {
        console.error("SAVE ERROR:", err);
        showToast("Unexpected save error", "error");
      }
    }



    // restore saved layout 
    document.addEventListener("DOMContentLoaded", function() {
      autoCheckAndRestore();

    });

    async function autoCheckAndRestore() {

      try {

        const res = await fetch(
          "<?= site_url('items/getFields') ?>?modelname=" +
          encodeURIComponent(currentModelName)
        );

        const data = await res.json();

        /* ---- No record found ------ */

        if (data.status !== "success" || !data.fields?.length) {
          showToast("Create layout", "info");
          return;
        }

        const fullSnapshot = data.fields[0].full_snapshot;

        /* ------ Snapshot NULL ----- */

        if (!fullSnapshot || fullSnapshot === "null") {
          showToast("Create layout", "info");
          return;
        }

        /* ----- Snapshot EXISTS → RESTORE ----- */

        let snapshot;

        try {
          snapshot = JSON.parse(fullSnapshot);
        } catch (err) {
          console.error("Invalid snapshot JSON");
          showToast("Saved layout corrupted", "error");
          return;
        }

        if (!snapshot) {
          showToast("Create layout", "info");
          return;
        }

        /*  CALL YOUR EXISTING RESTORE */
        restoreLayout(snapshot);

        console.log(" Layout restored automatically");

      } catch (err) {

        console.error("AUTO RESTORE ERROR:", err);
        showToast("Failed to load layout", "error");

      }
    }


    function restoreLayout(snapshot) {

      try {

        if (!snapshot || typeof snapshot !== "object") {
          showToast("Invalid layout format", "error");
          return;
        }

        if (!snapshot.printPage || !Array.isArray(snapshot.labels)) {
          showToast("Incomplete layout data", "error");
          return;
        }

        const printPageGroup = document.getElementById("printPageGroup");
        if (!printPageGroup) {
          showToast("Workspace not ready", "error");
          return;
        }

        window.__IS_RESTORING__ = true;

        /*  CREATE PRINT PAGE  */

        const PAGE_MARGIN = 10;

        const pageWidth = Number(snapshot.printPage.widthMM);
        const pageHeight = Number(snapshot.printPage.heightMM);

        window.currentPrintPage = {
          x: PAGE_MARGIN,
          y: PAGE_MARGIN,
          width: pageWidth,
          height: pageHeight
        };

        printPageGroup.innerHTML = "";
        printPageGroup.setAttribute(
          "transform",
          `translate(${PAGE_MARGIN}, ${PAGE_MARGIN})`
        );

        const SVG_NS = "http://www.w3.org/2000/svg";

        const pageRect = document.createElementNS(SVG_NS, "rect");
        pageRect.setAttribute("x", 0);
        pageRect.setAttribute("y", 0);
        pageRect.setAttribute("width", pageWidth);
        pageRect.setAttribute("height", pageHeight);
        pageRect.setAttribute("fill", "#ffffff");
        pageRect.setAttribute("stroke", "#111827");
        pageRect.setAttribute("stroke-width", 0.4);
        pageRect.setAttribute("vector-effect", "non-scaling-stroke");
        pageRect.setAttribute("id", "printPageRect");

        printPageGroup.appendChild(pageRect);

        window.__COPY_LABEL_COUNT__ = 0;
        window.currentLabel = null;

        /* CREATE LABEL CONTAINERS  */

        snapshot.labels.forEach(labelData => {

          let group = null;

          if (labelData.type === "main") {

            // Create main label container
            group = document.createElementNS(SVG_NS, "g");
            group.setAttribute("id", "labelGroup");
            printPageGroup.appendChild(group);

            mainLabel(
              labelData.widthMM,
              labelData.heightMM, {
                fromJSON: true
              }
            );

          } else {

            // Create copy container (empty)
            group = copyLabel(
              labelData.widthMM,
              labelData.heightMM, {
                fromJSON: true
              }
            );
          }

          if (!group) return;

          /*   SET LABEL POSITION (PURE TRANSLATE)  */

          const pos = labelData.position || {
            x: 0,
            y: 0
          };

          group.setAttribute(
            "transform",
            `translate(${pos.x}, ${pos.y})`
          );

          /*  REBUILD ELEMENTS */

          (labelData.elements || []).forEach(el => {

            let wrapper = null;

            const t = el.transform || {};
            const x = t.x || 0;
            const y = t.y || 0;
            const angle = t.angle || 0;
            const scaleX = t.scaleX || 1;
            const scaleY = t.scaleY || 1;

            /* ---------------- TEXT ---------------- */

            if (el.type === "text") {

              wrapper = addTextElement(
                el.text,
                x,
                y,
                el.fontSize,
                el.id,
                group
              );

              if (wrapper) {

                const textNode = wrapper.querySelector("text");

                if (textNode) {
                  textNode.setAttribute("font-family", el.fontFamily || "Arial");
                  textNode.setAttribute("fill", el.fill || "#000");

                  if (el.bold)
                    textNode.setAttribute("font-weight", "bold");

                  if (el.italic)
                    textNode.setAttribute("font-style", "italic");

                  if (el.underline)
                    textNode.setAttribute("text-decoration", "underline");
                }
              }
            }

            /* ---------------- QR ---------------- */

            if (el.type === "qr") {

              wrapper = renderQR(
                el.value,
                el.size,
                group, {
                  fromJSON: true,
                  efEnabled: el.efEnabled,
                  efText: el.efText
                }
              );
            }

            /* ---------------- BARCODE ---------------- */

            if (el.type === "barcode") {

              wrapper = renderBarcode(
                el.value,
                el.format,
                el.width,
                el.height,
                group, {
                  fromJSON: true
                }
              );
            }

            /* ---------------- IMAGE ---------------- */

            if (el.type === "image") {

              wrapper = addImageToLabel(
                el.src,
                group, {
                  fromJSON: true,
                  width: el.width,
                  height: el.height,
                  x,
                  y
                }
              );
            }

            /*  APPLY FULL TRANSFORM (AFTER CREATION)  */

            if (wrapper) {

              wrapper.setAttribute(
                "transform",
                `
            translate(${x}, ${y})
            rotate(${angle})
            scale(${scaleX}, ${scaleY})
            `
              );
            }
          });
        });

        /* FINALIZE  */

        setTimeout(() => {

          clearSelection();
          clearGuides();
          enableLabelInteraction();

          /* ── Recover Main_NodeId from the restored QR so that
             changing QR size after a DB restore works correctly.
             QR value format: EF / model / nodeId / ...           */
          const restoredQR = document.querySelector(
            '#labelGroup [data-type="qr"]'
          );
          if (restoredQR?.dataset?.value) {
            const parts = restoredQR.dataset.value.split("/");
            if (parts.length >= 3) {
              Main_NodeId = parts[2];
              const nodeInput = document.getElementById("Nodeinput");
              if (nodeInput && !nodeInput.value) nodeInput.value = Main_NodeId;
            }
          }

          window.__IS_RESTORING__ = false;

          showToast("Layout restored successfully", "success");


        }, 50);

      } catch (err) {

        console.error("RESTORE ERROR:", err);
        showToast("Failed to restore layout", "error");
      }
    }






    // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  COPY LABEL SCRIPT >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    const copyLabelBtn = document.getElementById('copyLabelBtn');
    const copyLabelModal = document.getElementById('copyLabelModal');
    const confirmCopyLabel = document.getElementById('confirmCopyLabel');
    const cancelCopyLabel = document.getElementById('cancelCopyLabel');
    const copyLabelWidth = document.getElementById('copyLabelWidth');
    const copyLabelHeight = document.getElementById('copyLabelHeight');

    copyLabelBtn.addEventListener("click", () => {

      if (!window.currentLabel) {
        showToast("Create a label first", "warning");
        return;
      }

      copyLabelWidth.value = "";
      copyLabelHeight.value = "";

      copyLabelModal.style.display = "flex";
    });

    cancelCopyLabel.addEventListener('click', () => {
      copyLabelModal.style.display = 'none';
    });

    confirmCopyLabel.addEventListener("click", () => {

      const newWidthMM = parseFloat(copyLabelWidth.value);
      const newHeightMM = parseFloat(copyLabelHeight.value);

      if (
        isNaN(newWidthMM) || isNaN(newHeightMM) ||
        newWidthMM <= 0 || newHeightMM <= 0
      ) {
        showToast("Enter valid dimensions in mm", "warning");
        return;
      }

      if (!window.currentLabel) {
        showToast("No label available to copy", "error");
        return;
      }

      copyLabel(newWidthMM, newHeightMM);

      copyLabelModal.style.display = "none";
    });


    function copyLabel(widthMM, heightMM, options = {}) {

      const isRestore = options.fromJSON === true;

      const printPageGroup = document.getElementById("printPageGroup");
      const originalGroup = document.getElementById("labelGroup");

      if (!printPageGroup || !originalGroup) {
        if (!isRestore) showToast("Print page not ready", "error");
        return null;
      }

      if (!window.currentPrintPage) {
        if (!isRestore) showToast("Create print page first", "warning");
        return null;
      }

      const page = window.currentPrintPage;

      if (
        isNaN(widthMM) || isNaN(heightMM) ||
        widthMM <= 0 || heightMM <= 0 ||
        widthMM > page.width || heightMM > page.height
      ) {
        if (!isRestore) showToast("Copy exceeds print page size", "error");
        return null;
      }

      /* ================= COPY ID ================= */

      window.__COPY_LABEL_COUNT__ =
        (window.__COPY_LABEL_COUNT__ || 0) + 1;

      const copyId = "copyLabel" + window.__COPY_LABEL_COUNT__;

      const SVG_NS = "http://www.w3.org/2000/svg";

      const group = document.createElementNS(SVG_NS, "g");

      group.setAttribute("id", copyId);
      group.setAttribute("data-copy", "true");
      group.setAttribute("data-width-mm", widthMM);
      group.setAttribute("data-height-mm", heightMM);

      /* ================= AUTO STACK (NORMAL ONLY) ================= */

      let translateX = null;
      let translateY = null;

      if (!isRestore) {

        const pageWidth = window.currentPrintPage.width;
        const pageHeight = window.currentPrintPage.height;

        /* ================= COLLECT ALL LABELS ================= */

        const placedLabels = [];

        const mainLabel = document.getElementById("labelGroup");

        if (mainLabel && mainLabel.hasAttribute("data-width-mm")) {

          const matrix = mainLabel.transform.baseVal.consolidate();

          let mx = 0;
          let my = 0;

          if (matrix) {
            mx = matrix.matrix.e;
            my = matrix.matrix.f;
          }

          placedLabels.push({
            x: mx,
            y: my,
            w: parseFloat(mainLabel.getAttribute("data-width-mm")) || 0,
            h: parseFloat(mainLabel.getAttribute("data-height-mm")) || 0
          });
        }

        const copies = Array.from(
          printPageGroup.querySelectorAll('[data-copy="true"]')
        );

        copies.forEach(copy => {

          const transform = copy.getAttribute("transform") || "";
          const match = /translate\(([^,]+),([^)]+)\)/.exec(transform);

          const cx = parseFloat(match?.[1] || 0);
          const cy = parseFloat(match?.[2] || 0);

          const cw = parseFloat(copy.getAttribute("data-width-mm")) || 0;
          const ch = parseFloat(copy.getAttribute("data-height-mm")) || 0;

          placedLabels.push({
            x: cx,
            y: cy,
            w: cw,
            h: ch
          });

        });

        /* ================= FIRST LABEL ================= */

        if (placedLabels.length === 0) {

          if (widthMM > pageWidth || heightMM > pageHeight) {
            showToast("Copy exceeds page size", "error");
            return null;
          }

          translateX = 0;
          translateY = 0;

        } else {

          /* ================= GRID SPACE SCAN ================= */

          const step = 1; // 1mm precision

          for (let y = 0; y <= pageHeight - heightMM; y += step) {

            for (let x = 0; x <= pageWidth - widthMM; x += step) {

              let overlap = false;

              for (const label of placedLabels) {

                const intersects =
                  x < label.x + label.w &&
                  x + widthMM > label.x &&
                  y < label.y + label.h &&
                  y + heightMM > label.y;

                if (intersects) {
                  overlap = true;
                  break;
                }

              }

              if (!overlap) {
                translateX = x;
                translateY = y;
                break;
              }

            }

            if (translateX !== null) break;

          }

          /* ================= NO SPACE ================= */

          if (translateX === null) {

            showToast("No space available. Copy failed.", "warning");

            return null;
          }

        }

      }

      // group.setAttribute("transform", `translate(0, ${translateY})`);

      group.setAttribute("transform", `translate(${translateX}, ${translateY})`);

      /* ================= SAFE BOUNDARY ================= */

      const rect = document.createElementNS(SVG_NS, "rect");

      rect.setAttribute("x", 0);
      rect.setAttribute("y", 0);
      rect.setAttribute("width", widthMM);
      rect.setAttribute("height", heightMM);
      rect.setAttribute("fill", "none");
      rect.setAttribute("stroke", "#A855F7");
      rect.setAttribute("stroke-width", 0.4);
      rect.setAttribute("vector-effect", "non-scaling-stroke");
      rect.setAttribute("data-boundary", "true");

      group.appendChild(rect);

      /* ================= NORMAL MODE CLONE ================= */

      if (!isRestore) {

        originalGroup.querySelectorAll(".design-element").forEach(el => {

          const clone = el.cloneNode(true);
          group.appendChild(clone);
        });
      }

      /* ================= APPEND TO PAGE ================= */

      printPageGroup.appendChild(group);

      enableLabelInteraction();

      if (!isRestore) {
        showToast(copyId + " created successfully");
      }

      return group; //  IMPORTANT FOR RESTORE
    }
  </script>



  <!--<<<<<<<<<<<<<  SEPARATE QR INFO MODAL FEATURE >>>>>>>>>>>>>>>>>>>>>>>>-->
  <script>
    let __qrInfoSnapshot = null;

    function qrInfoEscapeHtml(value) {
      return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }

    function qrInfoEscapeAttr(value) {
      return qrInfoEscapeHtml(value).replace(/\n/g, " ");
    }

    function qrInfoEnsureStyles() {
      if (document.getElementById("qrInfoModalStyleTag")) return;

      const style = document.createElement("style");
      style.id = "qrInfoModalStyleTag";
      style.textContent = `
      .qr-info-label-section {
        border: 1px solid #dbe3f0;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 14px;
        background: #ffffff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
      }

      .qr-info-fixed-box {
        font-size: 13px;
        color: #475569;
        margin-bottom: 12px;
        padding: 10px 12px;
        border: 1px solid #dbe3f0;
        border-radius: 10px;
        background: #f8fbff;
      }

      .qr-info-field-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
      }

      .qr-info-field-row .modal-field-name,
      .qr-info-field-row .modal-field-value {
        border: 1px solid #cbd5e1 !important;
        border-radius: 9px !important;
        outline: none !important;
        transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
      }

      .qr-info-field-row .modal-field-name,
      .qr-info-field-row .modal-field-value[readonly] {
        background: #f8fafc !important;
        color: #334155 !important;
        cursor: default;
      }

      .qr-info-field-row .modal-field-name:focus,
      .qr-info-field-row .modal-field-value:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14) !important;
        background: #eff6ff !important;
      }

      .qr-info-check-wrap {
        display: flex;
        align-items: center;
        gap: 7px;
        min-width: 52px;
        font-size: 13px;
        color: #1e3a8a;
        user-select: none;
      }

      .qr-info-check-wrap input[type="checkbox"] {
        transform: scale(1.15);
        accent-color: #2563eb;
        cursor: pointer;
      }

      .qr-info-check-wrap input[type="checkbox"]:focus {
        outline: 2px solid rgba(37, 99, 235, 0.32);
        outline-offset: 2px;
        border-radius: 4px;
      }

      .qr-info-preview-title {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #1e3a8a;
      }

      .qr-info-preview-box {
        min-height: 44px;
        color: #2563eb;
        padding: 10px 12px;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        background: #eff6ff;
        word-break: break-word;
        line-height: 1.45;
      }

      .qr-info-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 14px;
      }

      .qr-info-btn {
        padding: 9px 16px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: transform 0.12s ease, box-shadow 0.18s ease, background-color 0.18s ease;
      }

      .qr-info-btn:hover {
        transform: translateY(-1px);
      }

      .qr-info-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
      }

      .qr-info-btn-close {
        background: #94a3b8;
        color: #ffffff;
      }

      .qr-info-btn-close:hover,
      .qr-info-btn-close:focus {
        background: #64748b;
      }

      .qr-info-btn-save {
        background: #2563eb;
        color: #ffffff;
      }

      .qr-info-btn-save:hover,
      .qr-info-btn-save:focus {
        background: #1d4ed8;
      }
    `;
      document.head.appendChild(style);
    }

    function qrInfoGetBaseFieldsFromMainModal() {
      const rows = document.querySelectorAll("#modalFieldsContainer .modal-field-row");
      const fields = [];

      rows.forEach(row => {
        const nameInput = row.querySelector(".modal-field-name");
        const valueInput = row.querySelector(".modal-field-value");

        if (!nameInput || !valueInput) return;

        const name = nameInput.value?.trim() || "";
        const value = valueInput.value?.trim() || "";

        if (!name) return;

        fields.push({
          name,
          value,
          nameStyle: nameInput.getAttribute("style") || "",
          valueStyle: valueInput.getAttribute("style") || ""
        });
      });

      return fields;
    }

    function qrInfoBuildFieldRowHtml(field, included) {
      const safeNameStyle = qrInfoEscapeAttr(field.nameStyle || "");
      const safeValueStyle = qrInfoEscapeAttr(field.valueStyle || "");

      return `
      <div class="qr-info-field-row">
        <input type="text"
              class="modal-field-name form-control"
              placeholder="Field Name"
              readonly
              tabindex="-1"
              value="${qrInfoEscapeAttr(field.name)}"
              style="${safeNameStyle}">

        <input type="text"
              class="modal-field-value form-control"
              placeholder="Field Value"
              readonly
              tabindex="-1"
              value="${qrInfoEscapeAttr(field.value)}"
              style="${safeValueStyle}">

        <label class="qr-info-check-wrap">
          <input type="checkbox"
                class="qr-include-checkbox"
                title="Include Value in QR"
                ${included ? "checked" : ""}>
          <span>QR</span>
        </label>
      </div>
    `;
    }

    function qrInfoParseFixedParts(qrValue) {
      const parts = String(qrValue || "").split("/");

      return {
        prefix: parts[0] || "EF",
        model: parts[1] || (document.getElementById("modelInput")?.value?.trim() || ""),
        nodeId: parts[2] || (document.getElementById("nodeDropdown")?.value?.trim() || "")
      };
    }

    function qrInfoParseDynamicFieldMap(qrValue) {
      const parts = String(qrValue || "").split("/");
      const map = {};

      for (let i = 3; i < parts.length; i++) {
        const part = parts[i] || "";
        const sep = part.indexOf(":");
        if (sep === -1) continue;

        const key = part.slice(0, sep).trim();
        const value = part.slice(sep + 1).trim();

        if (key) map[key] = value;
      }

      return map;
    }

    function qrInfoGetLabelTitle(labelData, index) {
      if (labelData?.type === "main") return "Main Label";

      let copyNumber = 0;
      if (__qrInfoSnapshot?.labels?.length) {
        for (let i = 0; i <= index; i++) {
          if (__qrInfoSnapshot.labels[i]?.type === "copy") copyNumber++;
        }
      }

      return `Copy Label ${copyNumber || 1}`;
    }

    async function qrInfoFetchSavedSnapshot() {
      const res = await fetch(
        "<?= site_url('items/getFields') ?>?modelname=" + encodeURIComponent(currentModelName), {
          headers: {
            "X-Requested-With": "XMLHttpRequest"
          }
        }
      );

      const data = await res.json();

      if (data.status !== "success" || !Array.isArray(data.fields) || !data.fields.length) {
        throw new Error("Saved layout not found");
      }

      const fullSnapshot = data.fields[0]?.full_snapshot;

      if (!fullSnapshot || fullSnapshot === "null") {
        throw new Error("Saved layout JSON not found");
      }

      let snapshot;
      try {
        snapshot = JSON.parse(fullSnapshot);
      } catch (err) {
        throw new Error("Saved layout JSON invalid");
      }

      if (!snapshot || !Array.isArray(snapshot.labels)) {
        throw new Error("Snapshot labels missing");
      }

      return snapshot;
    }

    function qrInfoBuildValueFromSection(sectionEl) {
      const prefix = sectionEl.dataset.prefix || "EF";
      const model = sectionEl.dataset.model || "";
      const nodeId = sectionEl.dataset.nodeId || "";

      let qrValue = `${prefix}/${model}/${nodeId}`;

      const rows = sectionEl.querySelectorAll(".qr-info-field-row");

      rows.forEach(row => {
        const name = row.querySelector(".modal-field-name")?.value?.trim() || "";
        const value = row.querySelector(".modal-field-value")?.value?.trim() || "";
        const include = row.querySelector(".qr-include-checkbox")?.checked || false;

        if (!include || !name || !value) return;

        qrValue += `/${name}:${value}`;
      });

      return qrValue;
    }

    function qrInfoUpdatePreview(sectionEl) {
      const box = sectionEl.querySelector(".qr-info-preview-box");
      if (!box) return;
      box.textContent = qrInfoBuildValueFromSection(sectionEl);
    }

    function qrInfoRenderModal(snapshot) {
      qrInfoEnsureStyles();

      const container = document.getElementById("qrDataContent");
      if (!container) return;

      const baseFields = qrInfoGetBaseFieldsFromMainModal();

      if (!baseFields.length) {
        container.innerHTML = `
        <div style="padding:14px;">
          <p style="margin:0;color:#6b7280;">No modal fields available.</p>
        </div>
        <div class="qr-info-footer">
          <button type="button"
                  id="qrInfoModalCloseBtn"
                  class="qr-info-btn qr-info-btn-close">
            Close
          </button>
        </div>
      `;

        document.getElementById("qrInfoModalCloseBtn")?.addEventListener("click", qrInfoCloseModal);
        return;
      }

      let html = `<div style="max-height:65vh;overflow:auto;padding-right:4px;">`;
      let renderedCount = 0;

      snapshot.labels.forEach((labelData, index) => {
        const qrEl = (labelData.elements || []).find(el => el.type === "qr");
        if (!qrEl) return;

        renderedCount++;

        const qrValue = qrEl.value || "";
        const fixed = qrInfoParseFixedParts(qrValue);
        const activeMap = qrInfoParseDynamicFieldMap(qrValue);

        html += `
        <div class="qr-info-label-section"
             data-label-index="${index}"
             data-prefix="${qrInfoEscapeAttr(fixed.prefix)}"
             data-model="${qrInfoEscapeAttr(fixed.model)}"
             data-node-id="${qrInfoEscapeAttr(fixed.nodeId)}">

          <h3 style="margin:0 0 10px;font-size:16px;color:#111827;">
            ${qrInfoEscapeHtml(qrInfoGetLabelTitle(labelData, index))}
          </h3>

          <div class="qr-info-fixed-box">
            <strong>Fixed QR parts:</strong>
            ${qrInfoEscapeHtml(fixed.prefix)} / ${qrInfoEscapeHtml(fixed.model)} / ${qrInfoEscapeHtml(fixed.nodeId)}
          </div>

          <div style="display:flex;flex-direction:column;gap:8px;">
      `;

        baseFields.forEach(field => {
          const included = Object.prototype.hasOwnProperty.call(activeMap, field.name);
          html += qrInfoBuildFieldRowHtml(field, included);
        });

        html += `
          </div>

          <div style="margin-top:12px;">
            <div class="qr-info-preview-title">QR data :</div>
            <div class="qr-info-preview-box"></div>
          </div>
        </div>
      `;
      });

      html += `</div>`;

      html += `
      <div class="qr-info-footer">
        <button type="button"
                id="qrInfoModalCloseBtn"
                class="qr-info-btn qr-info-btn-close">
          Close
        </button>

        <button type="button"
                id="qrInfoModalSaveBtn"
                class="qr-info-btn qr-info-btn-save">
          Save
        </button>
      </div>
    `;

      if (!renderedCount) {
        container.innerHTML = `
        <div style="padding:14px;">
          <p style="margin:0;color:#6b7280;">No QR found in saved layout.</p>
        </div>
        <div class="qr-info-footer">
          <button type="button"
                  id="qrInfoModalCloseBtn"
                  class="qr-info-btn qr-info-btn-close">
            Close
          </button>
        </div>
      `;

        document.getElementById("qrInfoModalCloseBtn")?.addEventListener("click", qrInfoCloseModal);
        return;
      }

      container.innerHTML = html;

      container.querySelectorAll(".qr-info-label-section").forEach(section => {
        qrInfoUpdatePreview(section);

        section.addEventListener("change", function(e) {
          if (e.target.classList.contains("qr-include-checkbox")) {
            qrInfoUpdatePreview(section);
          }
        });
      });

      document.getElementById("qrInfoModalCloseBtn")?.addEventListener("click", qrInfoCloseModal);
      document.getElementById("qrInfoModalSaveBtn")?.addEventListener("click", qrInfoSaveToLiveDOM);
    }

    function qrInfoGetLiveLabelGroups() {
      const printPageGroup = document.getElementById("printPageGroup");
      if (!printPageGroup) return [];

      return Array.from(
        printPageGroup.querySelectorAll(':scope > g#labelGroup, :scope > g[data-copy="true"]')
      );
    }

    function qrInfoReplaceQRInGroup(group, qrElementData) {
      if (!group || !qrElementData) return false;

      const oldQr = group.querySelector('.design-element[data-type="qr"]');
      if (!oldQr) return false;

      const transform = oldQr.getAttribute("transform") || "translate(0,0)";
      oldQr.remove();

      const ok = validateQRCapacity(qrElementData.value || "", qrElementData.size || 12);
      if (!ok.valid) {
        showToast(
          `QR too dense. Increase size to at least ${ok.requiredSize || "required"} mm`,
          "warning"
        );
        return false;
      }

      const newQr = renderQR(
        qrElementData.value,
        qrElementData.size || 12,
        group, {
          fromJSON: true,
          efEnabled: qrElementData.efEnabled,
          efText: qrElementData.efText
        }
      );

      if (!newQr) return false;

      newQr.setAttribute("transform", transform);
      return true;
    }

    function qrInfoCloseModal() {
      const modal = document.getElementById("qrDataModal");
      const overlay = document.getElementById("modalOverlay");

      if (modal) modal.style.display = "none";
      if (overlay) overlay.style.display = "none";
    }

    async function qrInfoOpenModalFlow() {
      try {
        if (!window.currentPrintPage) {
          showToast("Create print page first", "warning");
          return;
        }

        await saveLayoutData();

        const snapshot = await qrInfoFetchSavedSnapshot();
        __qrInfoSnapshot = snapshot;

        const hasQR = snapshot.labels.some(label =>
          (label.elements || []).some(el => el.type === "qr")
        );

        if (!hasQR) {
          showToast("No QR found in saved layout", "warning");
          return;
        }

        qrInfoRenderModal(snapshot);

        const modal = document.getElementById("qrDataModal");
        const overlay = document.getElementById("modalOverlay");

        if (modal) modal.style.display = "block";
        if (overlay) overlay.style.display = "block";

      } catch (err) {
        console.error("QR INFO OPEN ERROR:", err);
        showToast(err.message || "Failed to open QR info modal", "error");
      }
    }

    async function qrInfoSaveToLiveDOM() {
      try {
        if (!__qrInfoSnapshot || !Array.isArray(__qrInfoSnapshot.labels)) {
          showToast("QR snapshot missing", "error");
          return;
        }

        const sections = document.querySelectorAll("#qrDataContent .qr-info-label-section");
        const liveGroups = qrInfoGetLiveLabelGroups();

        for (const section of sections) {
          const labelIndex = parseInt(section.dataset.labelIndex, 10);
          const labelData = __qrInfoSnapshot.labels[labelIndex];
          if (!labelData) continue;

          const qrEl = (labelData.elements || []).find(el => el.type === "qr");
          if (!qrEl) continue;

          const newValue = qrInfoBuildValueFromSection(section);
          const capacity = validateQRCapacity(newValue, qrEl.size || 12);

          if (!capacity.valid) {
            showToast(
              `${qrInfoGetLabelTitle(labelData, labelIndex)} QR too dense. Increase size to at least ${capacity.requiredSize || "required"} mm`,
              "error"
            );
            return;
          }

          qrEl.value = newValue;
        }

        for (const section of sections) {
          const labelIndex = parseInt(section.dataset.labelIndex, 10);
          const labelData = __qrInfoSnapshot.labels[labelIndex];
          const liveGroup = liveGroups[labelIndex];

          if (!labelData || !liveGroup) continue;

          const qrEl = (labelData.elements || []).find(el => el.type === "qr");
          if (!qrEl) continue;

          const done = qrInfoReplaceQRInGroup(liveGroup, qrEl);
          if (!done) return;
        }

        showToast("QR values updated successfully", "success");
        await saveLayoutData();
        qrInfoCloseModal();

      } catch (err) {
        console.error("QR INFO SAVE ERROR:", err);
        showToast("Failed to update QR values", "error");
      }
    }

    document.getElementById("showQRDataBtn")?.addEventListener("click", qrInfoOpenModalFlow);
    document.getElementById("closeQRDataModal")?.addEventListener("click", qrInfoCloseModal);
    document.getElementById("modalOverlay")?.addEventListener("click", qrInfoCloseModal);
  </script>



  <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<<<< image library >>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
  <script>
    // <<<<<<<<<<<<<<<<<<<  image library and adding image to preview >>>>>>>>>>>>>>>>>>>>>>>>>

    let selectedFiles = [];
    let selectedImages = new Set();
    const MAX_SELECT = 10;
    const MAX_LIBRARY = 100;

    const API = "<?= site_url('items/imageHandler') ?>";



    //  SELECTION COUNTER

    function updateSelectionCount() {
      const countEl = document.getElementById("selectionCount");
      if (!countEl) return;
      countEl.textContent = `${selectedImages.size} / ${MAX_SELECT} selected`;
    }



    /* OPEN / CLOSE */
    function toggleLibrary() {
      document.getElementById("libraryOverlay").classList.toggle("hidden");
      loadLibrary();
    }

    /* LOAD IMAGES */

    function loadLibrary() {
      selectedImages.clear();
      updateSelectionCount();

      if (!currentUserId) {
        showToast("User session missing");
        return;
      }

      const cacheBuster = Date.now();

      fetch(
          API + "?action=load_images&user_id=" + encodeURIComponent(currentUserId) + "&ts=" + cacheBuster, {
            cache: "no-store"
          }
        )
        .then(async r => {
          const text = await r.text();
          try {
            return JSON.parse(text);
          } catch (e) {
            console.error("Load invalid JSON:", text);
            throw new Error("Invalid response");
          }
        })
        .then(data => {
          const box = document.getElementById("libContent");
          const totalCount = Array.isArray(data) ? data.length : 0;
          const info = document.getElementById("totalImageCount");

          if (info) {
            info.textContent = `Stored: ${totalCount}/100  |  Free: ${100 - totalCount}`;
          }

          if (!Array.isArray(data) || data.length === 0) {
            box.innerHTML = `
          <div class="no-images">
            <i class="bi bi-images"></i>
            <div>No images. Please upload images</div>
          </div>`;
            return;
          }

          box.innerHTML =
            '<div class="img-grid">' +
            data.map(src => `
          <div class="img-card" data-src="${src}">
            <div class="img-wrap">
              <img src="${src}?t=${cacheBuster}" loading="lazy">
            </div>
            <div class="img-check">✓</div>
          </div>
        `).join('') +
            '</div>';

          document.querySelectorAll(".img-card").forEach(card => {
            card.addEventListener("click", e => selectImage(card, e));
          });
        })
        .catch(err => {
          console.error(err);
          showToast("Failed to load images");
        });
    }



    /*  SELECT / UNSELECT (TOGGLE) */
    function selectImage(el, e) {
      if (e.target.classList.contains("del-cross")) return;

      const src = el.dataset.src;

      if (selectedImages.has(src)) {
        el.classList.remove("selected");
        selectedImages.delete(src);
      } else {

        if (selectedImages.size >= MAX_SELECT) {
          showToast("Max 10 images can be selected");
          return;
        }

        el.classList.add("selected");
        selectedImages.add(src);
      }

      updateSelectionCount();
    }


    /* FILE INPUT PREVIEW  */
    document.addEventListener("change", function(e) {
      if (e.target.id !== "fileInput") return;

      selectedFiles = [...e.target.files];

      const box = document.getElementById("uploadPreview");
      box.innerHTML = "";

      selectedFiles.forEach(f => {
        if (f.size > 5 * 1024 * 1024) {
          showToast("Max 5MB per image");
          return;
        }

        const url = URL.createObjectURL(f);
        box.innerHTML += `
      <div>
        <img src="${url}" width="60">
        <div>${f.name}</div>
      </div>`;
      });
    });

    /*  UPLOAD IMAGES */

    function uploadImages() {
      const currentCount = document.querySelectorAll(".img-card").length;

      if (currentCount + selectedFiles.length > MAX_LIBRARY) {
        showToast("Library limit 100 images per user");
        return;
      }

      if (selectedFiles.length === 0) {
        showToast("Select images first");
        return;
      }

      if (!currentUserId) {
        showToast("User session missing");
        return;
      }

      const fd = new FormData();
      selectedFiles.forEach(f => fd.append("images[]", f));
      fd.append("action", "upload_images");
      fd.append("user_id", currentUserId);

      fetch(API, {
          method: "POST",
          body: fd
        })
        .then(async r => {
          const text = await r.text();
          try {
            return JSON.parse(text);
          } catch (e) {
            console.error("Upload invalid JSON:", text);
            throw new Error("Invalid response");
          }
        })
        .then(res => {
          if (res.status === "ok") {
            showToast("Upload successful");
            selectedFiles = [];
            document.getElementById("uploadPreview").innerHTML = "";
            document.getElementById("fileInput").value = "";
            loadLibrary();
          } else {
            showToast(res.message || "Upload failed");
          }
        })
        .catch(err => {
          console.error(err);
          showToast("Upload error");
        });
    }

    /* ADD TO LABEL  */
    function addSelectedToLabel() {

      const type = $("imgType")?.value;

      if (!window.currentLabel) {
        showToast("Create label first", "warning");
        return;
      }

      if (!type) {
        showToast("Select image type");
        return;
      }

      if (selectedImages.size === 0) {
        showToast("Select at least one image");
        return;
      }

      selectedImages.forEach(src => {

        if (type === "color") {
          addImageToLabel(src);
        } else {
          convertMonoAndAdd(src);
        }

      });

      $("libraryOverlay")?.classList.add("hidden");
    }

    /*  ADD IMAGE TO LABEL PREVIEW  */

    function addImageToLabel(src, targetGroup = null) {

      const labelGroup = targetGroup || document.getElementById("labelGroup");
      if (!labelGroup) return;

      const isJSON = typeof src === "object";

      const SVG_NS = "http://www.w3.org/2000/svg";

      let finalSrc, finalWidth, finalHeight;
      let xMM, yMM;
      let scaleX = 1,
        scaleY = 1,
        angle = 0;

      /* ====== JSON RESTORE MODE ====== */

      if (isJSON) {

        const data = src;

        finalSrc = data.src || "";
        finalWidth = data.width || 20;
        finalHeight = data.height || 20;

        xMM = data.transform?.x || 0;
        yMM = data.transform?.y || 0;

        scaleX = data.transform?.scaleX ?? 1;
        scaleY = data.transform?.scaleY ?? 1;
        angle = data.transform?.angle ?? 0;

      } else {

        /* ===== NORMAL MODE (UNCHANGED) ===== */

        if (!window.currentLabel) return;

        finalSrc = src;
        finalWidth = 20;
        finalHeight = 20;

        xMM = 10;
        yMM = 10;
      }

      /* =====  WRAPPER ==== */

      let wrapper;

      if (isJSON) {

        wrapper = createDesignElement("image", 0, 0);

        wrapper.setAttribute(
          "transform",
          `translate(${xMM}, ${yMM}) rotate(${angle}) scale(${scaleX}, ${scaleY})`
        );

      } else {

        wrapper = createDesignElement("image", xMM, yMM);
      }

      wrapper.dataset.type = "image";
      wrapper.dataset.src = finalSrc;
      wrapper.dataset.width = finalWidth;
      wrapper.dataset.height = finalHeight;

      /* ==== IMAGE ELEMENT ===== */

      const image = document.createElementNS(SVG_NS, "image");

      image.setAttribute("href", finalSrc);
      image.setAttribute("x", 0);
      image.setAttribute("y", 0);
      image.setAttribute("width", finalWidth);
      image.setAttribute("height", finalHeight);


      wrapper.appendChild(image);
      labelGroup.appendChild(wrapper);

      /* ===== PUSH ONLY IN NORMAL MODE ===== */

      if (!isJSON && !targetGroup) {

        window.currentLabel.elements.push({
          type: "image",
          src: finalSrc,
          transform: {
            x: xMM,
            y: yMM,
            angle: 0,
            scaleX: 1,
            scaleY: 1
          },
          width: finalWidth,
          height: finalHeight
        });

        showToast("Image added");
      }

      return wrapper;
    }

    /*  MONOCHROME CONVERSION */
    function convertMonoAndAdd(src) {

      const img = new Image();
      img.crossOrigin = "anonymous";

      img.onload = function() {

        const canvas = document.createElement("canvas");
        canvas.width = img.width;
        canvas.height = img.height;

        const ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);

        const data = ctx.getImageData(0, 0, canvas.width, canvas.height);

        for (let i = 0; i < data.data.length; i += 4) {
          const avg = (data.data[i] + data.data[i + 1] + data.data[i + 2]) / 3;
          data.data[i] = data.data[i + 1] = data.data[i + 2] = avg;
        }

        ctx.putImageData(data, 0, 0);

        const monoURL = canvas.toDataURL("image/png");

        addImageToLabel(monoURL);
      };

      img.src = src;
    }

    /*  DELETE MULTI  */

    function deleteSelectedImg() {
      if (selectedImages.size === 0) {
        showToast("No images selected");
        return;
      }

      if (!currentUserId) {
        showToast("User session missing");
        return;
      }

      fetch(API, {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            action: "delete_images",
            user_id: currentUserId,
            images: Array.from(selectedImages)
          })
        })
        .then(async r => {
          const text = await r.text();
          try {
            return JSON.parse(text);
          } catch (e) {
            console.error("Delete invalid JSON:", text);
            throw new Error("Invalid response");
          }
        })
        .then(res => {
          if (res.status === "ok") {
            selectedImages.clear();
            loadLibrary();
            showToast("Deleted");
          } else {
            showToast(res.message || "Delete failed");
          }
        })
        .catch(err => {
          console.error(err);
          showToast("Delete error");
        });
    }
    /*  DELETE SINGLE  */

    function deleteSingle(src, e) {
      e.stopPropagation();

      if (!currentUserId) {
        showToast("User session missing");
        return;
      }

      fetch(API, {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            action: "delete_images",
            user_id: currentUserId,
            images: [src]
          })
        })
        .then(async r => {
          const text = await r.text();
          try {
            return JSON.parse(text);
          } catch (e) {
            console.error("Delete invalid JSON:", text);
            throw new Error("Invalid response");
          }
        })
        .then(res => {
          if (res.status === "ok") {
            selectedImages.delete(src);
            loadLibrary();
            showToast("Deleted");
          } else {
            showToast(res.message || "Delete failed");
          }
        })
        .catch(err => {
          console.error(err);
          showToast("Delete error");
        });
    }

    // keyboard  key actions 
    document.addEventListener("keydown", function(e) {

      const overlayOpen = !document.getElementById("libraryOverlay").classList.contains("hidden");
      if (!overlayOpen) return;

      /* CTRL + A */
      if (e.ctrlKey && e.key.toLowerCase() === "a") {
        e.preventDefault();

        const cards = document.querySelectorAll(".img-card");

        selectedImages.clear();
        document.querySelectorAll(".img-card").forEach(c => c.classList.remove("selected"));

        let count = 0;
        cards.forEach(card => {
          if (count >= MAX_SELECT) return;

          card.classList.add("selected");
          selectedImages.add(card.dataset.src);
          count++;
        });

        updateSelectionCount();
      }

      /* DELETE KEY */
      if (e.key === "Delete") {
        if (selectedImages.size > 0) {
          deleteSelectedImg();
        }
      }
    });
  </script>




  <!-- <<<<<<<<<<<<<<<<<<<<<<<<<<  selection and drag  >>>>>>>>>>>>>>>>>>>>>>>> -->
  <script>
    /*  PROFESSIONAL SVG INTERACTION ENGINE  */

    const selectionLayer = document.getElementById("selectionLayer");



    /* LABEL REGISTRATION  */

    function getAllLabels() {
      const labels = [];

      const main = document.getElementById("labelGroup");
      if (main) {
        main.classList.add("design-label");
        main.style.pointerEvents = "all";
        labels.push(main);
      }

      document.querySelectorAll('[data-copy="true"]').forEach(g => {
        g.classList.add("design-label");
        g.style.pointerEvents = "all";
        labels.push(g);
      });

      return labels;
    }

    /*  STATE  */

    DesignState.selected = [];
    DesignState.dragging = false;
    DesignState.editing = false;
    DesignState.rectSelecting = false;
    DesignState.dragOffsets = new Map();




    DesignState.pendingTarget = null;
    DesignState.mouseDownPoint = null;
    DesignState.dragThreshold = 2; // mm in SVG space
    DesignState.activeMode = null; // "drag" | "rect"

    /* SVG POINT  */

    // function svgPoint(evt) {
    //   const pt = workspaceSvg.createSVGPoint();
    //   pt.x = evt.clientX;
    //   pt.y = evt.clientY;
    //   return pt.matrixTransform(workspaceSvg.getScreenCTM().inverse());
    // }

    function svgPoint(evt) {
      if (typeof window.workspaceScreenToWorld === "function") {
        const p = window.workspaceScreenToWorld(evt.clientX, evt.clientY);
        return {
          x: p.x,
          y: p.y
        };
      }

      const pt = workspaceSvg.createSVGPoint();
      pt.x = evt.clientX;
      pt.y = evt.clientY;
      return pt.matrixTransform(workspaceSvg.getScreenCTM().inverse());
    }


    /* SELECTION  */

    function clearSelection() {
      DesignState.selected = [];
      selectionLayer.innerHTML = "";

      if (typeof resetTextToolbarDefaults === "function") {
        resetTextToolbarDefaults();
      }
    }

    function selectItems(items) {
      DesignState.selected = items;
      renderSelection();
    }

    /*  SELECTION BOX RENDER */

    function renderSelection() {
      selectionLayer.innerHTML = "";

      if (typeof syncTextToolbarFromSelection === "function") {
        syncTextToolbarFromSelection();
      }

      if (!DesignState.selected.length) return;

      const svgRect = workspaceSvg.getBoundingClientRect();
      const scaleX = workspaceSvg.viewBox.baseVal.width / svgRect.width;
      const scaleY = workspaceSvg.viewBox.baseVal.height / svgRect.height;

      DesignState.selected.forEach(el => {
        const box = el.getBoundingClientRect();

        const x = (box.left - svgRect.left) * scaleX;
        const y = (box.top - svgRect.top) * scaleY;
        const w = box.width * scaleX;
        const h = box.height * scaleY;

        const r = document.createElementNS(SVG_NS, "rect");
        r.setAttribute("x", x);
        r.setAttribute("y", y);
        r.setAttribute("width", w);
        r.setAttribute("height", h);
        r.setAttribute("fill", "none");
        r.setAttribute("stroke", "#6366F1");
        r.setAttribute("stroke-width", "0.4");
        r.setAttribute("vector-effect", "non-scaling-stroke");

        selectionLayer.appendChild(r);
      });

      if (
        DesignState.selected.length === 1 &&
        DesignState.selected[0].classList.contains("design-element")
      ) {
        renderTransformHandles();
      }
    }

    /* DRAG START  */
    function startDrag(e) {

      DesignState.dragging = true;
      DesignState.dragStart = svgPoint(e);
      DesignState.dragOffsets.clear();

      DesignState.selected.forEach(el => {

        const t = el.transform.baseVal.consolidate();

        const matrix = t ?
          t.matrix :
          workspaceSvg.createSVGMatrix();

        DesignState.dragOffsets.set(el, {
          matrix: matrix
        });

      });
    }



    DesignState.rectContainer = null; // boundary container for rectangle
    /* MOUSE DOWN  */
    workspaceSvg.addEventListener("mousedown", function(e) {

      if (DesignState.editing) return;

      getAllLabels();

      const element = e.target.closest(".design-element");
      const label = e.target.closest(".design-label");

      DesignState.mouseDownPoint = svgPoint(e);
      DesignState.activeMode = null;
      DesignState.pendingTarget = null;
      DesignState.rectContainer = null;

      const pt = svgPoint(e);
      let handle = null;

      const handles = selectionLayer.querySelectorAll(".transform-handle");

      handles.forEach(h => {

        const x = parseFloat(h.getAttribute("x") || h.getAttribute("cx"));
        const y = parseFloat(h.getAttribute("y") || h.getAttribute("cy"));
        const size = h.tagName === "circle" ?
          parseFloat(h.getAttribute("r")) :
          parseFloat(h.getAttribute("width"));

        const hx = h.tagName === "circle" ? x : x + size / 2;
        const hy = h.tagName === "circle" ? y : y + size / 2;

        const distance = Math.hypot(pt.x - hx, pt.y - hy);

        if (distance < 6) {
          handle = h;
        }
      });

      /* ==== HANDLE CLICK (Rotate / Resize) ==== */
      if (
        handle &&
        DesignState.selected.length === 1 &&
        DesignState.selected[0].classList.contains("design-element")
      ) {

        // beginAction();
        beginAction("transform");

        const el = DesignState.selected[0];

        DesignState.transformMode = true;
        DesignState.activeHandle = handle.dataset.handle;

        // Store SVG cursor start
        DesignState.dragStart = svgPoint(e);

        // Store existing transform decomposition
        DesignState.startTransform = decomposeTransform(el);

        // Store local BBOX center (SAFE – not screen space)
        const bbox = el.getBBox();
        const cx = bbox.x + bbox.width / 2;
        const cy = bbox.y + bbox.height / 2;

        DesignState.rotateCenter = {
          cx,
          cy
        };

        /* -------- PROFESSIONAL ROTATION PREP -------- */
        if (handle.dataset.handle === "rotate") {

          const startMouseAngle =
            Math.atan2(
              DesignState.dragStart.y - cy,
              DesignState.dragStart.x - cx
            ) * 180 / Math.PI;

          DesignState.startMouseAngle = startMouseAngle;
        }

        /* -------- RESIZE PREP (unchanged logic) -------- */

        const svgRect = workspaceSvg.getBoundingClientRect();
        const viewScaleX = workspaceSvg.viewBox.baseVal.width / svgRect.width;
        const viewScaleY = workspaceSvg.viewBox.baseVal.height / svgRect.height;

        const rect = el.getBoundingClientRect();

        DesignState.startRect = {
          width: rect.width * viewScaleX,
          height: rect.height * viewScaleY
        };

        return;
      }

      /* === ELEMENT → immediate drag === */
      if (element) {
        selectItems([element]);
        // beginAction();
        beginAction("position");
        DesignState.pendingTarget = element;
        return;
      }

      /* === LABEL === */
      if (label) {

        if (
          DesignState.selected.length === 1 &&
          DesignState.selected[0] === label
        ) {
          DesignState.pendingTarget = label;
          return;
        }

        selectItems([label]);
        return;
      }

      /* ===  EMPTY SPACE === */
      clearSelection();
    });


    /* smart magnetic guides and measurement */
    function applySmartGuides(el, newX, newY) {

      if (!DesignState.smartGuidesEnabled) {
        return {
          newX,
          newY
        };
      }

      clearGuides();

      const svgRect = workspaceSvg.getBoundingClientRect();
      const viewBox = workspaceSvg.viewBox.baseVal;

      const scaleX = viewBox.width / svgRect.width;
      const scaleY = viewBox.height / svgRect.height;

      const threshold = DesignState.snapThreshold;

      const originalTransform = el.getAttribute("transform") || "";
      el.setAttribute("transform", `translate(${newX}, ${newY})`);

      const movingRect = el.getBoundingClientRect();

      let moving = {
        left: (movingRect.left - svgRect.left) * scaleX,
        right: (movingRect.right - svgRect.left) * scaleX,
        top: (movingRect.top - svgRect.top) * scaleY,
        bottom: (movingRect.bottom - svgRect.top) * scaleY,
        centerX: ((movingRect.left + movingRect.right) / 2 - svgRect.left) * scaleX,
        centerY: ((movingRect.top + movingRect.bottom) / 2 - svgRect.top) * scaleY
      };

      const isLabel = el.classList.contains("design-label");
      const isElement = el.classList.contains("design-element");

      /* === SNAP TARGETS BASED ON TYPE === */

      let snapTargets = [];

      if (isLabel) {
        snapTargets = Array.from(
          printPageGroup.querySelectorAll(".design-label")
        ).filter(node => node !== el);
      }

      if (isElement) {
        const parent = el.closest(".design-label");
        if (parent) {
          snapTargets = Array.from(
            parent.querySelectorAll(".design-element")
          ).filter(node => node !== el);
        }
      }

      /* === MAGNETIC SNAP LOGIC (BOTH LABELS & ELEMENTS) === */

      snapTargets.forEach(other => {

        const rect = other.getBoundingClientRect();

        const otherBox = {
          left: (rect.left - svgRect.left) * scaleX,
          right: (rect.right - svgRect.left) * scaleX,
          top: (rect.top - svgRect.top) * scaleY,
          bottom: (rect.bottom - svgRect.top) * scaleY,
          centerX: ((rect.left + rect.right) / 2 - svgRect.left) * scaleX,
          centerY: ((rect.top + rect.bottom) / 2 - svgRect.top) * scaleY
        };

        /* HORIZONTAL SNAP */

        if (Math.abs(moving.left - otherBox.left) < threshold) {
          newX += (otherBox.left - moving.left);
          drawGuideLine(otherBox.left, viewBox.y, otherBox.left, viewBox.height);
        }

        if (Math.abs(moving.right - otherBox.right) < threshold) {
          newX += (otherBox.right - moving.right);
          drawGuideLine(otherBox.right, viewBox.y, otherBox.right, viewBox.height);
        }

        if (Math.abs(moving.centerX - otherBox.centerX) < threshold) {
          newX += (otherBox.centerX - moving.centerX);
          drawGuideLine(otherBox.centerX, viewBox.y, otherBox.centerX, viewBox.height);
        }

        /* VERTICAL SNAP */

        if (Math.abs(moving.top - otherBox.top) < threshold) {
          newY += (otherBox.top - moving.top);
          drawGuideLine(viewBox.x, otherBox.top, viewBox.width, otherBox.top);
        }

        if (Math.abs(moving.bottom - otherBox.bottom) < threshold) {
          newY += (otherBox.bottom - moving.bottom);
          drawGuideLine(viewBox.x, otherBox.bottom, viewBox.width, otherBox.bottom);
        }

        if (Math.abs(moving.centerY - otherBox.centerY) < threshold) {
          newY += (otherBox.centerY - moving.centerY);
          drawGuideLine(viewBox.x, otherBox.centerY, viewBox.width, otherBox.centerY);
        }

      });

      /* === PAGE EDGE SNAP (ONLY FOR LABELS) === */

      const pageBox = printPageGroup.getBoundingClientRect();

      const page = {
        left: (pageBox.left - svgRect.left) * scaleX,
        right: (pageBox.right - svgRect.left) * scaleX,
        top: (pageBox.top - svgRect.top) * scaleY,
        bottom: (pageBox.bottom - svgRect.top) * scaleY,
        centerX: ((pageBox.left + pageBox.right) / 2 - svgRect.left) * scaleX,
        centerY: ((pageBox.top + pageBox.bottom) / 2 - svgRect.top) * scaleY
      };

      if (isLabel) {

        if (Math.abs(moving.left - page.left) < threshold) {
          newX += (page.left - moving.left);
          drawGuideLine(page.left, page.top, page.left, page.bottom);
        }

        if (Math.abs(moving.right - page.right) < threshold) {
          newX += (page.right - moving.right);
          drawGuideLine(page.right, page.top, page.right, page.bottom);
        }

        if (Math.abs(moving.centerX - page.centerX) < threshold) {
          newX += (page.centerX - moving.centerX);
          drawGuideLine(page.centerX, page.top, page.centerX, page.bottom);
        }

        if (Math.abs(moving.top - page.top) < threshold) {
          newY += (page.top - moving.top);
          drawGuideLine(page.left, page.top, page.right, page.top);
        }

        if (Math.abs(moving.bottom - page.bottom) < threshold) {
          newY += (page.bottom - moving.bottom);
          drawGuideLine(page.left, page.bottom, page.right, page.bottom);
        }

        if (Math.abs(moving.centerY - page.centerY) < threshold) {
          newY += (page.centerY - moving.centerY);
          drawGuideLine(page.left, page.centerY, page.right, page.centerY);
        }
      }

      /* === APPLY FINAL SNAP POSITION === */

      el.setAttribute("transform", `translate(${newX}, ${newY})`);

      const finalRect = el.getBoundingClientRect();

      moving = {
        left: (finalRect.left - svgRect.left) * scaleX,
        right: (finalRect.right - svgRect.left) * scaleX,
        top: (finalRect.top - svgRect.top) * scaleY,
        bottom: (finalRect.bottom - svgRect.top) * scaleY,
        centerX: ((finalRect.left + finalRect.right) / 2 - svgRect.left) * scaleX,
        centerY: ((finalRect.top + finalRect.bottom) / 2 - svgRect.top) * scaleY
      };

      /* === MEASUREMENT GUIDES (ONLY FOR LABELS) === */

      if (isLabel) {

        const siblings = Array.from(
          printPageGroup.querySelectorAll(".design-label")
        ).filter(l => l !== el);

        let hasLeft = false,
          hasRight = false,
          hasTop = false,
          hasBottom = false;

        siblings.forEach(other => {

          const rect = other.getBoundingClientRect();

          const otherBox = {
            left: (rect.left - svgRect.left) * scaleX,
            right: (rect.right - svgRect.left) * scaleX,
            top: (rect.top - svgRect.top) * scaleY,
            bottom: (rect.bottom - svgRect.top) * scaleY
          };

          const gapRight = otherBox.left - moving.right;
          if (gapRight > 0) {
            hasRight = true;
            drawGuideLine(moving.right, moving.centerY, otherBox.left, moving.centerY, true);
            drawMeasurementText(
              moving.right + gapRight / 2,
              moving.centerY - 2,
              gapRight.toFixed(1)
            );
          }

          const gapLeft = moving.left - otherBox.right;
          if (gapLeft > 0) {
            hasLeft = true;
            drawGuideLine(otherBox.right, moving.centerY, moving.left, moving.centerY, true);
            drawMeasurementText(
              otherBox.right + gapLeft / 2,
              moving.centerY - 2,
              gapLeft.toFixed(1)
            );
          }

          const gapBottom = otherBox.top - moving.bottom;
          if (gapBottom > 0) {
            hasBottom = true;
            drawGuideLine(moving.centerX, moving.bottom, moving.centerX, otherBox.top, true);
            drawMeasurementText(
              moving.centerX,
              moving.bottom + gapBottom / 2,
              gapBottom.toFixed(1)
            );
          }

          const gapTop = moving.top - otherBox.bottom;
          if (gapTop > 0) {
            hasTop = true;
            drawGuideLine(moving.centerX, otherBox.bottom, moving.centerX, moving.top, true);
            drawMeasurementText(
              moving.centerX,
              otherBox.bottom + gapTop / 2,
              gapTop.toFixed(1)
            );
          }

        });

        if (!hasLeft) {
          const gap = moving.left - page.left;
          if (gap > 0) {
            drawGuideLine(page.left, moving.centerY, moving.left, moving.centerY, true);
            drawMeasurementText(page.left + gap / 2, moving.centerY - 2, gap.toFixed(1));
          }
        }

        if (!hasRight) {
          const gap = page.right - moving.right;
          if (gap > 0) {
            drawGuideLine(moving.right, moving.centerY, page.right, moving.centerY, true);
            drawMeasurementText(moving.right + gap / 2, moving.centerY - 2, gap.toFixed(1));
          }
        }

        if (!hasTop) {
          const gap = moving.top - page.top;
          if (gap > 0) {
            drawGuideLine(moving.centerX, page.top, moving.centerX, moving.top, true);
            drawMeasurementText(moving.centerX, page.top + gap / 2, gap.toFixed(1));
          }
        }

        if (!hasBottom) {
          const gap = page.bottom - moving.bottom;
          if (gap > 0) {
            drawGuideLine(moving.centerX, moving.bottom, moving.centerX, page.bottom, true);
            drawMeasurementText(moving.centerX, moving.bottom + gap / 2, gap.toFixed(1));
          }
        }

      }

      el.setAttribute("transform", originalTransform);

      return {
        newX,
        newY
      };
    }

    function showTransformMeasurements(el) {

      clearGuides();

      const t = el.transform.baseVal.consolidate();
      if (!t) return;

      const m = t.matrix;

      const scaleX = Math.sqrt(m.a * m.a + m.b * m.b);
      const scaleY = Math.sqrt(m.c * m.c + m.d * m.d);
      const rotation = Math.atan2(m.b, m.a) * (180 / Math.PI);

      /* ----  USE SAME SYSTEM AS SELECTION
         getBoundingClientRect → SVG space --- */

      const svgRect = workspaceSvg.getBoundingClientRect();
      const viewScaleX = workspaceSvg.viewBox.baseVal.width / svgRect.width;
      const viewScaleY = workspaceSvg.viewBox.baseVal.height / svgRect.height;

      const rect = el.getBoundingClientRect();

      const x = (rect.left - svgRect.left) * viewScaleX;
      const y = (rect.top - svgRect.top) * viewScaleY;
      const w = rect.width * viewScaleX;
      const h = rect.height * viewScaleY;

      const width = w.toFixed(1);
      const height = h.toFixed(1);

      const cx = x + w / 2;
      const cy = y - 6;

      drawMeasurementText(cx, cy, `W:${width}mm  H:${height}mm`);

      if (DesignState.activeHandle === "rotate") {
        drawMeasurementText(cx, cy - 6, `Angle: ${rotation.toFixed(1)}°`);
      }
    }

    function decomposeTransform(el) {

      const t = el.transform.baseVal.consolidate();
      if (!t) {
        return {
          x: 0,
          y: 0,
          angle: 0,
          scaleX: 1,
          scaleY: 1
        };
      }

      const m = t.matrix;

      const x = m.e;
      const y = m.f;

      const scaleX = Math.sqrt(m.a * m.a + m.b * m.b);
      const scaleY = Math.sqrt(m.c * m.c + m.d * m.d);

      const angle = Math.atan2(m.b, m.a) * (180 / Math.PI);

      return {
        x,
        y,
        angle,
        scaleX,
        scaleY
      };
    }

    /*  MOUSE MOVE  */

    workspaceSvg.addEventListener("mousemove", function(e) {

      if (!DesignState.mouseDownPoint) return;

      const cursor = svgPoint(e);

      /* === TRANSFORM MODE (Resize / Rotate) ====== */
      if (DesignState.transformMode) {

        const el = DesignState.selected[0];
        if (!el) return;

        const start = DesignState.startTransform;

        const dx = cursor.x - DesignState.dragStart.x;
        const dy = cursor.y - DesignState.dragStart.y;

        let newAngle = start.angle;
        let newScaleX = start.scaleX;
        let newScaleY = start.scaleY;

        const bbox = el.getBBox();
        const cx = bbox.x + bbox.width / 2;
        const cy = bbox.y + bbox.height / 2;

        /* ---------- ROTATE ---------- */

        if (DesignState.activeHandle === "rotate") {

          const {
            cx,
            cy
          } = DesignState.rotateCenter;

          // Current mouse angle
          let currentMouseAngle =
            Math.atan2(cursor.y - cy, cursor.x - cx) * 180 / Math.PI;

          // Calculate delta
          let deltaAngle = currentMouseAngle - DesignState.startMouseAngle;

          // Add to starting element angle
          let rawAngle = DesignState.startTransform.angle + deltaAngle;

          // Normalize
          rawAngle = (rawAngle + 360) % 360;

          /* -------- USER FRIENDLY SNAP -------- */

          const snapStep = 45;

          // Hold Shift for snap (professional behavior)
          if (!e.shiftKey) {
            // smooth free rotation
            newAngle = rawAngle;
          } else {
            // snap only when shift pressed
            newAngle = Math.round(rawAngle / snapStep) * snapStep;
          }
        }

        /* ---------- RESIZE ---------- */
        else {

          const baseWidth = DesignState.startRect.width;
          const baseHeight = DesignState.startRect.height;

          let scaleFactorX = 1;
          let scaleFactorY = 1;

          if (DesignState.activeHandle.includes("e")) {
            scaleFactorX = (baseWidth + dx) / baseWidth;
          }

          if (DesignState.activeHandle.includes("s")) {
            scaleFactorY = (baseHeight + dy) / baseHeight;
          }

          if (["nw", "ne", "se", "sw"].includes(DesignState.activeHandle)) {
            const uniform = Math.max(scaleFactorX, scaleFactorY);
            scaleFactorX = uniform;
            scaleFactorY = uniform;
          }

          newScaleX = start.scaleX * scaleFactorX;
          newScaleY = start.scaleY * scaleFactorY;
        }

        // Rebuild FULL transform safely
        el.setAttribute(
          "transform",
          `
        translate(${start.x}, ${start.y})
        rotate(${newAngle}, ${cx}, ${cy})
        scale(${newScaleX}, ${newScaleY})
      `
        );

        showTransformMeasurements(el);
        renderSelection();
        return;
      }



      /* ==== MODE DETECTION ==== */

      if (!DesignState.activeMode) {

        const dx = Math.abs(cursor.x - DesignState.mouseDownPoint.x);
        const dy = Math.abs(cursor.y - DesignState.mouseDownPoint.y);

        if (dx > 3 || dy > 3) {

          if (DesignState.pendingTarget) {
            DesignState.activeMode = "drag";
            startDrag(e);
          } else {

            const startTarget = e.target.closest(".design-label");

            DesignState.rectContainer = startTarget || printPageGroup;
            DesignState.activeMode = "rect";
            DesignState.rectStart = DesignState.mouseDownPoint;

            const rect = document.createElementNS(SVG_NS, "rect");
            rect.setAttribute("fill", "rgba(99,102,241,0.1)");
            rect.setAttribute("stroke", "#6366F1");
            rect.setAttribute("stroke-width", "0.4");
            rect.setAttribute("vector-effect", "non-scaling-stroke");

            selectionLayer.appendChild(rect);
            DesignState.selectionRect = rect;
          }
        }
      }

      /* ==== DRAG MODE (Preserves Rotation & Scale + Guides) ==== */

      if (DesignState.activeMode === "drag") {

        DesignState.dragMoved = true;
        clearGuides();

        const dx = cursor.x - DesignState.dragStart.x;
        const dy = cursor.y - DesignState.dragStart.y;

        DesignState.selected.forEach(el => {

          const offset = DesignState.dragOffsets.get(el);
          if (!offset) return;

          const m = offset.matrix;

          const start = {
            x: m.e,
            y: m.f,
            scaleX: Math.sqrt(m.a * m.a + m.b * m.b),
            scaleY: Math.sqrt(m.c * m.c + m.d * m.d),
            angle: Math.atan2(m.b, m.a) * (180 / Math.PI)
          };

          let newX = start.x + dx;
          let newY = start.y + dy;

          newX = snap(newX);
          newY = snap(newY);

          /* ---- APPLY SMART GUIDES FIRST ---- */

          let guideResult = {
            newX,
            newY
          };

          let canMove = true;

          // boundary test BEFORE guides
          if (el.classList.contains("design-element")) {
            const parent = el.closest(".design-label");
            if (parent) {
              const parentBox = parent.getBBox();
              const bbox = el.getBBox();

              if (
                newX < parentBox.x ||
                newY < parentBox.y ||
                newX + bbox.width > parentBox.x + parentBox.width ||
                newY + bbox.height > parentBox.y + parentBox.height
              ) {
                canMove = false;
              }
            }
          }

          if (el.classList.contains("design-label")) {
            const pageBox = printPageGroup.getBBox();
            const bbox = el.getBBox();

            if (
              newX < pageBox.x ||
              newY < pageBox.y ||
              newX + bbox.width > pageBox.x + pageBox.width ||
              newY + bbox.height > pageBox.y + pageBox.height
            ) {
              canMove = false;
            }
          }

          if (canMove) {
            guideResult = applySmartGuides(el, newX, newY);
          } else {
            clearGuides();
          }

          newX = guideResult.newX;
          newY = guideResult.newY;

          /* --- BOUNDARY CLAMP (AFTER GUIDES) --- */

          const bbox = el.getBBox();

          // ELEMENT → inside parent label
          if (el.classList.contains("design-element")) {

            const parent = el.closest(".design-label");
            if (!parent) return;

            const parentBox = parent.getBBox();

            newX = Math.max(
              parentBox.x,
              Math.min(newX, parentBox.x + parentBox.width - bbox.width)
            );

            newY = Math.max(
              parentBox.y,
              Math.min(newY, parentBox.y + parentBox.height - bbox.height)
            );
          }

          // LABEL → inside printPageGroup
          if (el.classList.contains("design-label")) {

            const pageBox = printPageGroup.getBBox();

            newX = Math.max(
              pageBox.x,
              Math.min(newX, pageBox.x + pageBox.width - bbox.width)
            );

            newY = Math.max(
              pageBox.y,
              Math.min(newY, pageBox.y + pageBox.height - bbox.height)
            );
          }

          /* --- APPLY FULL TRANSFORM (KEEP SCALE/ROTATE) --- */

          const cx = bbox.x + bbox.width / 2;
          const cy = bbox.y + bbox.height / 2;

          el.setAttribute(
            "transform",
            `
              translate(${newX}, ${newY})
              rotate(${start.angle}, ${cx}, ${cy})
              scale(${start.scaleX}, ${start.scaleY})
              `
          );
        });

        renderSelection();
      }

      /* === RECT MODE (unchanged) === */

      if (DesignState.activeMode === "rect" && DesignState.selectionRect) {

        const container = DesignState.rectContainer;
        if (!container) return;

        const bbox = container.getBBox();
        const matrix = container.getScreenCTM();
        const inverse = workspaceSvg.getScreenCTM().inverse();
        if (!matrix) return;

        const p1 = workspaceSvg.createSVGPoint();
        p1.x = bbox.x;
        p1.y = bbox.y;

        const p2 = workspaceSvg.createSVGPoint();
        p2.x = bbox.x + bbox.width;
        p2.y = bbox.y + bbox.height;

        const topLeft = p1.matrixTransform(matrix).matrixTransform(inverse);
        const bottomRight = p2.matrixTransform(matrix).matrixTransform(inverse);

        const bounds = {
          x: topLeft.x,
          y: topLeft.y,
          width: bottomRight.x - topLeft.x,
          height: bottomRight.y - topLeft.y
        };

        const clampedCursor = {
          x: Math.max(bounds.x, Math.min(cursor.x, bounds.x + bounds.width)),
          y: Math.max(bounds.y, Math.min(cursor.y, bounds.y + bounds.height))
        };

        const x = Math.min(clampedCursor.x, DesignState.rectStart.x);
        const y = Math.min(clampedCursor.y, DesignState.rectStart.y);
        const w = Math.abs(clampedCursor.x - DesignState.rectStart.x);
        const h = Math.abs(clampedCursor.y - DesignState.rectStart.y);

        DesignState.selectionRect.setAttribute("x", x);
        DesignState.selectionRect.setAttribute("y", y);
        DesignState.selectionRect.setAttribute("width", w);
        DesignState.selectionRect.setAttribute("height", h);
      }

    });

    /* MOUSE UP  */
    workspaceSvg.addEventListener("mouseup", function() {



      if (DesignState.transformMode) {
        endAction();
        DesignState.transformMode = null;
        DesignState.activeHandle = null;
        DesignState.startTransform = null;
      }
      /* DRAG COMPLETE  */
      if (DesignState.activeMode === "drag") {

        if (DesignState.dragMoved) {
          endAction(); // ← ONE clean history push
        } else {
          DesignState.isSavingLocked = false;
        }
      }

      if (DesignState.activeMode === "rect" && DesignState.selectionRect) {

        const rect = DesignState.selectionRect.getBBox();
        const selected = [];

        const container = DesignState.rectContainer || printPageGroup;

        const elements = container.querySelectorAll(".design-element");

        elements.forEach(el => {

          const bbox = el.getBBox();
          const matrix = el.getScreenCTM();
          if (!matrix) return;

          const inverse = workspaceSvg.getScreenCTM().inverse();

          const corners = [{
              x: bbox.x,
              y: bbox.y
            },
            {
              x: bbox.x + bbox.width,
              y: bbox.y
            },
            {
              x: bbox.x + bbox.width,
              y: bbox.y + bbox.height
            },
            {
              x: bbox.x,
              y: bbox.y + bbox.height
            }
          ];

          const fullyInside = corners.every(pt => {

            const p = workspaceSvg.createSVGPoint();
            p.x = pt.x;
            p.y = pt.y;

            const screen = p.matrixTransform(matrix);
            const root = screen.matrixTransform(inverse);

            return (
              root.x >= rect.x &&
              root.x <= rect.x + rect.width &&
              root.y >= rect.y &&
              root.y <= rect.y + rect.height
            );
          });

          if (fullyInside) selected.push(el);
        });

        selectItems(selected);

        DesignState.selectionRect.remove();
        DesignState.selectionRect = null;
      }

      clearGuides();
      DesignState.activeMode = null;
      DesignState.pendingTarget = null;
      DesignState.mouseDownPoint = null;
      DesignState.rectContainer = null;
      DesignState.dragging = false;
    });


    /*  DOUBLE CLICK TEXT EDIT  */
    workspaceSvg.addEventListener("dblclick", function(e) {
      /* ===== DOUBLE CLICK EMPTY LABEL → SELECT LABEL ===== */
      const clickedLabel = e.target.closest(".design-label");
      const clickedElement = e.target.closest(".design-element");

      if (clickedLabel && !clickedElement) {
        selectItems([clickedLabel]);
        return;
      }

      const element = e.target.closest(".design-element");
      if (!element) return;
      if (element.getAttribute("data-type") !== "text") return;

      const textNode = element.querySelector("text");
      if (!textNode) return;

      DesignState.editing = true;
      clearSelection();

      const box = textNode.getBBox();

      /* ── Gather current text (may be multi-line with tspan children) ── */
      const existingTspans = textNode.querySelectorAll("tspan");
      const currentText = existingTspans.length > 0 ?
        Array.from(existingTspans).map(t => t.textContent).join("\n") :
        textNode.textContent;

      const lineCount = (currentText.match(/\n/g) || []).length + 1;
      const fontSize = parseFloat(textNode.getAttribute("font-size") || 4);

      const foreign = document.createElementNS(SVG_NS, "foreignObject");
      foreign.setAttribute("x", box.x);
      foreign.setAttribute("y", box.y - fontSize * 0.5);
      foreign.setAttribute("width", Math.max(box.width + 120, 160));
      foreign.setAttribute("height", Math.max(lineCount * fontSize * 1.8 + 40, 60));

      /* ── Use textarea so Enter creates a new line ── */
      const input = document.createElement("textarea");
      input.value = currentText;
      input.rows = Math.max(lineCount, 2);
      input.setAttribute("maxlength", "250");
      input.style.font = window.getComputedStyle(textNode).font;
      input.style.border = "0.5px solid #1a73e8";
      input.style.borderRadius = "4px";
      input.style.background = "rgba(255,255,255,0.97)";
      input.style.outline = "none";
      input.style.width = "60%";
      input.style.height = "60%";
      input.style.resize = "both";
      input.style.padding = "2px 4px";
      input.style.boxSizing = "border-box";
      input.style.lineHeight = "1.3";

      foreign.appendChild(input);
      element.appendChild(foreign);

      input.focus();
      input.select();

      function save() {
        const newText = input.value;

        /* Build comparison string from current tspans */
        const oldText = existingTspans.length > 0 ?
          Array.from(existingTspans).map(t => t.textContent).join("\n") :
          textNode.textContent;

        foreign.remove();
        DesignState.editing = false;

        if (oldText === newText) return;

        beginAction("text-edit");

        /* ── Rebuild tspan children for multi-line ── */
        textNode.innerHTML = "";
        const lines = newText.split("\n");
        if (lines.length === 1) {
          textNode.textContent = newText;
        } else {
          lines.forEach((line, i) => {
            const tspan = document.createElementNS(SVG_NS, "tspan");
            tspan.setAttribute("x", 0);
            tspan.setAttribute("dy", i === 0 ? "0" : String(fontSize * 1.3));
            tspan.textContent = line;
            textNode.appendChild(tspan);
          });
        }

        endAction();
      }

      input.addEventListener("keydown", function(ev) {
        /* Ctrl+Enter or Escape → commit */
        if ((ev.key === "Enter" && (ev.ctrlKey || ev.metaKey)) ||
          ev.key === "Escape") {
          ev.preventDefault();
          save();
        }
        /* Plain Enter → textarea handles it naturally (new line) */
      });

      input.addEventListener("blur", save);
    });



    /* RIGHT CLICK / TRACKPAD SECONDARY CLICK ON SELECTED LABEL */
    workspaceSvg.addEventListener("contextmenu", function(e) {
      const clickedLabel = e.target.closest(".design-label");
      const clickedElement = e.target.closest(".design-element");

      if (!clickedLabel || clickedElement) return;

      e.preventDefault();

      if (!isSingleLabelSelected() || getSelectedLabel() !== clickedLabel) {
        selectItems([clickedLabel]);
      }

      showLabelQuickMenu(e.clientX, e.clientY, clickedLabel);
    });

    document.addEventListener("click", function(e) {
      if (!e.target.closest("#labelQuickMenu")) {
        hideLabelQuickMenu();
      }
    });

    document.addEventListener("keydown", function(e) {
      if (e.key === "Escape") {
        hideLabelQuickMenu();
        closeLabelResizeBox();
      }
    });

    /*  CTRL + A   */
    document.addEventListener("keydown", function(e) {

      if (DesignState.editing) return;

      if (e.ctrlKey && e.key.toLowerCase() === "a") {
        e.preventDefault();
        getAllLabels();
        const all = workspaceSvg.querySelectorAll(".design-label, .design-element");
        selectItems([...all]);
      }
    });

    function enableLabelInteraction() {
      getAllLabels().forEach(label => {
        label.style.pointerEvents = "all";
        label.style.cursor = "move";
      });
    }






    // labelResizeAction?.addEventListener("click", function() {
    //   const label = DesignState.labelMenuTarget || getSelectedLabel();
    //   hideLabelQuickMenu();

    //   if (!label) {
    //     showToast("Select a label first", "warning");
    //     return;
    //   }

    //   openLabelResizeModal(label);
    // });

    // labelBorderAction?.addEventListener("click", function() {
    //   hideLabelQuickMenu();
    //   showToast("Border options will be added next", "info");
    // });

    // applyLabelResizeBtn?.addEventListener("click", function() {
    //   const label = DesignState.labelMenuTarget || getSelectedLabel();
    //   if (!label) {
    //     showToast("Select a label first", "warning");
    //     return;
    //   }

    //   const widthMM = parseFloat(resizeLabelWidth?.value);
    //   const heightMM = parseFloat(resizeLabelHeight?.value);

    //   const ok = applyResizeToLabel(label, widthMM, heightMM);
    //   if (ok) {
    //     closeLabelResizeBox();
    //   }
    // });

    // cancelLabelResizeBtn?.addEventListener("click", closeLabelResizeBox);
    // closeLabelResizeModal?.addEventListener("click", closeLabelResizeBox);

    // labelResizeModal?.addEventListener("click", function(e) {
    //   if (e.target === labelResizeModal) {
    //     closeLabelResizeBox();
    //   }
    // });



    labelResizeAction?.addEventListener("click", function() {
      const label = DesignState.labelMenuTarget || getSelectedLabel();

      if (!label) {
        hideLabelQuickMenu();
        showToast("Select a label first", "warning");
        return;
      }

      DesignState.resizeTargetLabel = label;
      hideLabelQuickMenu();
      openLabelResizeModal(label);
    });

    labelBorderAction?.addEventListener("click", function() {
      hideLabelQuickMenu();
      showToast("Border options will be added next", "info");
    });

    applyLabelResizeBtn?.addEventListener("click", function(e) {
      e.preventDefault();

      const label =
        DesignState.resizeTargetLabel ||
        DesignState.labelMenuTarget ||
        getSelectedLabel();

      if (!label) {
        showToast("Select a label first", "warning");
        return;
      }

      const widthMM = parseFloat(resizeLabelWidth?.value);
      const heightMM = parseFloat(resizeLabelHeight?.value);

      const ok = applyResizeToLabel(label, widthMM, heightMM);
      if (ok) {
        DesignState.resizeTargetLabel = null;
        closeLabelResizeBox();
      }
    });

    cancelLabelResizeBtn?.addEventListener("click", function(e) {
      e.preventDefault();
      e.stopPropagation();
      DesignState.resizeTargetLabel = null;
      closeLabelResizeBox();
    });

    closeLabelResizeModal?.addEventListener("click", function(e) {
      e.preventDefault();
      e.stopPropagation();
      DesignState.resizeTargetLabel = null;
      closeLabelResizeBox();
    });

    labelResizeModal?.addEventListener("click", function(e) {
      if (e.target === labelResizeModal) {
        DesignState.resizeTargetLabel = null;
        closeLabelResizeBox();
      }
    });
  </script>


  <!--<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< snapgrid >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>-->
  <script>
    DesignState.snapEnabled = true;
    DesignState.gridSize = 1; // 1mm default

    function toggleSnap() {
      DesignState.snapEnabled = !DesignState.snapEnabled;
      showToast(
        DesignState.snapEnabled ? "Snap Enabled" : "Snap Disabled",
        "info"
      );
    }

    function snap(value) {
      if (!DesignState.snapEnabled) return value;
      const grid = DesignState.gridSize;
      return Math.round(value / grid) * grid;
    }

    function setSnapSize(sizeMM) {
      DesignState.gridSize = sizeMM;
      showToast("Snap size set to " + sizeMM + "mm", "info");
    }

    document.addEventListener("keydown", function(e) {
      if (DesignState.editing) return;
      if (!DesignState.selected.length) return;

      const moveSmall = 1; // 1mm
      const moveLarge = 5; // 5mm with shift
      const step = e.shiftKey ? moveLarge : moveSmall;

      let dx = 0;
      let dy = 0;

      if (e.key === "ArrowLeft") dx = -step;
      if (e.key === "ArrowRight") dx = step;
      if (e.key === "ArrowUp") dy = -step;
      if (e.key === "ArrowDown") dy = step;

      if (dx === 0 && dy === 0) return;

      e.preventDefault();

      beginAction("position");

      DesignState.selected.forEach(el => {
        const t = el.transform.baseVal.consolidate();
        const m = t ? t.matrix : workspaceSvg.createSVGMatrix();

        const newX = snap(m.e + dx);
        const newY = snap(m.f + dy);

        el.setAttribute(
          "transform",
          `matrix(${m.a} ${m.b} ${m.c} ${m.d} ${newX} ${newY})`
        );
      });

      renderSelection();
      endAction();
    });
  </script>


  <!--<<<<<<<<<<<<<<<<<<<<<< smart measurement + alignment guides >>>>>>>>>>>>>>> -->
  <script>
    DesignState.snapThreshold = 1; // mm magnetic range
    DesignState.smartGuidesEnabled = true;



    function initGuideMarkers() {

      const defs = document.createElementNS(SVG_NS, "defs");

      const marker = document.createElementNS(SVG_NS, "marker");
      marker.setAttribute("id", "arrowHead");
      marker.setAttribute("viewBox", "0 0 10 10");
      marker.setAttribute("refX", "5");
      marker.setAttribute("refY", "5");
      marker.setAttribute("markerWidth", "4");
      marker.setAttribute("markerHeight", "4");
      marker.setAttribute("orient", "auto-start-reverse");

      const path = document.createElementNS(SVG_NS, "path");
      path.setAttribute("d", "M 0 0 L 10 5 L 0 10 z");
      path.setAttribute("fill", "#22c55e");

      marker.appendChild(path);
      defs.appendChild(marker);
      workspaceSvg.appendChild(defs);
    }

    initGuideMarkers();

    function clearGuides() {
      const guideLayer = document.getElementById("guideLayer");
      if (guideLayer) guideLayer.innerHTML = "";
    }


    function drawGuideLine(x1, y1, x2, y2, withArrows = false) {
      // if (!isFinite(x1) || !isFinite(y1) || !isFinite(x2) || !isFinite(y2)) return;

      const line = document.createElementNS(SVG_NS, "line");

      line.setAttribute("x1", x1);
      line.setAttribute("y1", y1);
      line.setAttribute("x2", x2);
      line.setAttribute("y2", y2);
      line.setAttribute("stroke", "#22c55e");
      line.setAttribute("stroke-width", "0.4");
      line.setAttribute("vector-effect", "non-scaling-stroke");

      if (withArrows) {
        line.setAttribute("marker-start", "url(#arrowHead)");
        line.setAttribute("marker-end", "url(#arrowHead)");
      }

      document.getElementById("guideLayer").appendChild(line);
    }


    function drawMeasurementText(x, y, value) {

      if (!isFinite(x) || !isFinite(y)) return;

      const text = document.createElementNS(SVG_NS, "text");

      text.setAttribute("x", Number(x));
      text.setAttribute("y", Number(y));
      text.setAttribute("font-size", "3");
      text.setAttribute("fill", "#22c55e");
      text.setAttribute("text-anchor", "middle");

      text.textContent = value + "mm";

      const guideLayer = document.getElementById("guideLayer");
      if (guideLayer) guideLayer.appendChild(text);
    }
  </script>

  <script>
    DesignState.transformMode = null;
    DesignState.activeHandle = null;
    DesignState.startBox = null;
    DesignState.startTransform = null;


    function renderTransformHandles() {

      if (!DesignState.selected.length) return;

      const el = DesignState.selected[0];

      const svgRect = workspaceSvg.getBoundingClientRect();
      const scaleX = workspaceSvg.viewBox.baseVal.width / svgRect.width;
      const scaleY = workspaceSvg.viewBox.baseVal.height / svgRect.height;

      const box = el.getBoundingClientRect();

      const x = (box.left - svgRect.left) * scaleX;
      const y = (box.top - svgRect.top) * scaleY;
      const w = box.width * scaleX;
      const h = box.height * scaleY;

      const handleSize = 3;

      const positions = [{
          name: "nw",
          x: x,
          y: y
        },
        {
          name: "n",
          x: x + w / 2,
          y: y
        },
        {
          name: "ne",
          x: x + w,
          y: y
        },
        {
          name: "e",
          x: x + w,
          y: y + h / 2
        },
        {
          name: "se",
          x: x + w,
          y: y + h
        },
        {
          name: "s",
          x: x + w / 2,
          y: y + h
        },
        {
          name: "sw",
          x: x,
          y: y + h
        },
        {
          name: "w",
          x: x,
          y: y + h / 2
        }
      ];

      positions.forEach(p => {

        const r = document.createElementNS(SVG_NS, "rect");
        r.setAttribute("x", p.x - handleSize / 2);
        r.setAttribute("y", p.y - handleSize / 2);
        r.setAttribute("width", handleSize);
        r.setAttribute("height", handleSize);
        r.setAttribute("fill", "#fff");
        r.setAttribute("stroke", "#6366F1");
        r.setAttribute("stroke-width", "0.4");
        r.setAttribute("class", "transform-handle");
        r.dataset.handle = p.name;

        selectionLayer.appendChild(r);
      });

      // Rotate handle
      const rotate = document.createElementNS(SVG_NS, "circle");
      rotate.setAttribute("cx", x + w / 2);
      rotate.setAttribute("cy", y - 8);
      rotate.setAttribute("r", 2.5);
      rotate.setAttribute("fill", "#22c55e");
      rotate.setAttribute("class", "transform-handle");
      rotate.dataset.handle = "rotate";

      selectionLayer.appendChild(rotate);
    }
  </script>





  <!-- <<<<<<< resize >>>>>>>>>>>>>. -->

  <script>
    /* ================= LABEL QUICK MENU / RESIZE ================= */

    const labelQuickMenu = document.getElementById("labelQuickMenu");
    const labelResizeModal = document.getElementById("labelResizeModal");
    const labelResizeAction = document.getElementById("labelResizeAction");
    const labelBorderAction = document.getElementById("labelBorderAction");
    const resizeLabelWidth = document.getElementById("resizeLabelWidth");
    const resizeLabelHeight = document.getElementById("resizeLabelHeight");
    const applyLabelResizeBtn = document.getElementById("applyLabelResizeBtn");
    const cancelLabelResizeBtn = document.getElementById("cancelLabelResizeBtn");
    const closeLabelResizeModal = document.getElementById("closeLabelResizeModal");

    DesignState.labelMenuTarget = null;

    function isSingleLabelSelected() {
      return (
        DesignState.selected.length === 1 &&
        DesignState.selected[0] &&
        DesignState.selected[0].classList.contains("design-label")
      );
    }

    function getSelectedLabel() {
      return isSingleLabelSelected() ? DesignState.selected[0] : null;
    }

    function getLabelBoundaryRect(label) {
      if (!label) return null;
      return label.querySelector('rect[data-boundary="true"]');
    }

    function getLabelSizeMM(label) {
      const rect = getLabelBoundaryRect(label);
      return {
        width: parseFloat(rect?.getAttribute("width") || label?.getAttribute("data-width-mm") || 0) || 0,
        height: parseFloat(rect?.getAttribute("height") || label?.getAttribute("data-height-mm") || 0) || 0
      };
    }

    function parseTranslate(transform) {
      const value = String(transform || "");
      const match = /translate\(([^,\s]+)[,\s]+([^)]+)\)/.exec(value);
      return {
        x: parseFloat(match?.[1] || 0) || 0,
        y: parseFloat(match?.[2] || 0) || 0
      };
    }

    function showLabelQuickMenu(clientX, clientY, label) {
      if (!labelQuickMenu || !label) return;

      DesignState.labelMenuTarget = label;

      labelQuickMenu.style.display = "block";
      labelQuickMenu.classList.add("show");
      labelQuickMenu.setAttribute("aria-hidden", "false");

      const menuRect = labelQuickMenu.getBoundingClientRect();
      const pad = 12;

      let left = clientX;
      let top = clientY;

      if (left + menuRect.width > window.innerWidth - pad) {
        left = window.innerWidth - menuRect.width - pad;
      }

      if (top + menuRect.height > window.innerHeight - pad) {
        top = window.innerHeight - menuRect.height - pad;
      }

      if (left < pad) left = pad;
      if (top < pad) top = pad;

      labelQuickMenu.style.left = left + "px";
      labelQuickMenu.style.top = top + "px";
    }

    function hideLabelQuickMenu() {
      if (!labelQuickMenu) return;
      labelQuickMenu.classList.remove("show");
      labelQuickMenu.style.display = "none";
      labelQuickMenu.setAttribute("aria-hidden", "true");
    }

    function openLabelResizeModal(label) {
      if (!label || !labelResizeModal) return;

      const size = getLabelSizeMM(label);

      if (resizeLabelWidth) resizeLabelWidth.value = size.width || "";
      if (resizeLabelHeight) resizeLabelHeight.value = size.height || "";

      labelResizeModal.style.display = "flex";
    }

    function closeLabelResizeBox() {
      if (labelResizeModal) {
        labelResizeModal.style.display = "none";
      }
    }

    function applyResizeToLabel(label, widthMM, heightMM) {
      if (!label) return false;
      if (!window.currentPrintPage) {
        showToast("Create print page first", "warning");
        return false;
      }

      const page = window.currentPrintPage;

      if (
        isNaN(widthMM) || isNaN(heightMM) ||
        widthMM <= 0 || heightMM <= 0
      ) {
        showToast("Enter valid width and height", "warning");
        return false;
      }

      if (widthMM > page.width || heightMM > page.height) {
        showToast("Label exceeds print page size", "error");
        return false;
      }

      const boundary = getLabelBoundaryRect(label);
      if (!boundary) {
        showToast("Label boundary not found", "error");
        return false;
      }

      const pos = parseTranslate(label.getAttribute("transform"));
      const clampedX = Math.max(0, Math.min(pos.x, page.width - widthMM));
      const clampedY = Math.max(0, Math.min(pos.y, page.height - heightMM));

      if (typeof beginAction === "function") beginAction("label-resize");

      boundary.setAttribute("width", widthMM);
      boundary.setAttribute("height", heightMM);

      label.setAttribute("data-width-mm", widthMM);
      label.setAttribute("data-height-mm", heightMM);
      label.setAttribute("transform", `translate(${clampedX}, ${clampedY})`);

      if (label.id === "labelGroup" || label === document.getElementById("labelGroup")) {
        if (!window.currentLabel) window.currentLabel = {};
        window.currentLabel.width = widthMM;
        window.currentLabel.height = heightMM;
        window.currentLabel.x = clampedX;
        window.currentLabel.y = clampedY;
      }

      if (typeof renderSelection === "function") renderSelection();
      if (typeof clearGuides === "function") clearGuides();
      if (typeof endAction === "function") endAction();

      showToast("Label resized", "success");
      return true;
    }
  </script>

  <!--<<<<<<<<<<<<<<<<<<<<<<<<<< UNDO & REDO >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>-->
  <script>
    /* ACTION-BASED UNDO / REDO
     Saves ONE entry per completed user action */

    DesignState.history = [];
    DesignState.redoStack = [];
    DesignState.maxHistory = 120;

    DesignState.isRestoring = false;
    DesignState.currentAction = null;

    const undoBtn = document.getElementById("undoBtn");
    const redoBtn = document.getElementById("redoBtn");

    function clonePlain(obj) {
      return obj ? JSON.parse(JSON.stringify(obj)) : null;
    }

    function createLayoutSnapshot() {
      return JSON.stringify({
        printPage: clonePlain(window.currentPrintPage),
        label: window.currentLabel ? {
          width: window.currentLabel.width,
          height: window.currentLabel.height
        } : null,
        html: printPageGroup.innerHTML
      });
    }

    function rebuildDesignStateElements() {
      DesignState.elements = {};

      const all = document.querySelectorAll("#printPageGroup .design-element");

      all.forEach(el => {
        let id = el.getAttribute("data-id");
        const type = el.getAttribute("data-type") || "el";

        if (!id) {
          id = generateId(type);
          el.setAttribute("data-id", id);
        }

        const t = el.transform.baseVal.consolidate();
        const m = t ? t.matrix : workspaceSvg.createSVGMatrix();

        DesignState.elements[id] = {
          id,
          type,
          x: m.e,
          y: m.f,
          rotation: Math.atan2(m.b, m.a) * (180 / Math.PI),
          scaleX: Math.sqrt(m.a * m.a + m.b * m.b) || 1,
          scaleY: Math.sqrt(m.c * m.c + m.d * m.d) || 1
        };
      });
    }

    function restoreSnapshot(snapshotOrJson) {
      if (!snapshotOrJson) return;

      const snapshot =
        typeof snapshotOrJson === "string" ?
        JSON.parse(snapshotOrJson) :
        snapshotOrJson;

      DesignState.isRestoring = true;
      DesignState.currentAction = null;

      printPageGroup.innerHTML = snapshot.html;

      rebuildDesignStateElements();

      window.currentPrintPage = clonePlain(snapshot.printPage);
      window.currentLabel = clonePlain(snapshot.label) || {};

      const restoredLabelGroup = document.getElementById("labelGroup");
      if (restoredLabelGroup) {
        window.currentLabel.width =
          parseFloat(restoredLabelGroup.getAttribute("data-width-mm")) ||
          window.currentLabel.width ||
          0;

        window.currentLabel.height =
          parseFloat(restoredLabelGroup.getAttribute("data-height-mm")) ||
          window.currentLabel.height ||
          0;
      }

      if (!Array.isArray(window.currentLabel.elements)) {
        window.currentLabel.elements = [];
      }

      clearSelection();
      clearGuides();
      enableLabelInteraction();

      DesignState.isRestoring = false;
      updateUndoRedoUI();
    }

    function beginAction(type = "generic") {
      if (DesignState.isRestoring) return false;
      if (DesignState.currentAction) return false;

      DesignState.currentAction = {
        type,
        before: createLayoutSnapshot()
      };

      return true;
    }

    function endAction() {
      if (DesignState.isRestoring) return;
      if (!DesignState.currentAction) return;

      const action = DesignState.currentAction;
      DesignState.currentAction = null;

      const after = createLayoutSnapshot();

      /* no real change */
      if (action.before === after) {
        updateUndoRedoUI();
        return;
      }

      DesignState.history.push({
        type: action.type,
        before: action.before,
        after
      });

      DesignState.redoStack = [];

      if (DesignState.history.length > DesignState.maxHistory) {
        DesignState.history.shift();
      }

      updateUndoRedoUI();
    }

    function cancelAction() {
      DesignState.currentAction = null;
      updateUndoRedoUI();
    }

    function undo() {
      if (!DesignState.history.length) return;

      const action = DesignState.history.pop();
      DesignState.redoStack.push(action);

      restoreSnapshot(action.before);
      updateUndoRedoUI();
    }

    function redo() {
      if (!DesignState.redoStack.length) return;

      const action = DesignState.redoStack.pop();
      DesignState.history.push(action);

      restoreSnapshot(action.after);
      updateUndoRedoUI();
    }

    function updateUndoRedoUI() {
      if (undoBtn) undoBtn.disabled = DesignState.history.length === 0;
      if (redoBtn) redoBtn.disabled = DesignState.redoStack.length === 0;
    }

    /* initialize clean history */
    window.addEventListener("load", function() {
      setTimeout(() => {
        DesignState.history = [];
        DesignState.redoStack = [];
        DesignState.currentAction = null;

        /* optional baseline snapshot reference only */
        DesignState.initialSnapshot = createLayoutSnapshot();

        updateUndoRedoUI();
      }, 400);
    });

    /* shortcuts */
    document.addEventListener("keydown", function(e) {
      const key = e.key.toLowerCase();

      if (e.ctrlKey && !e.shiftKey && key === "z") {
        e.preventDefault();
        undo();
        return;
      }

      if ((e.ctrlKey && key === "y") || (e.ctrlKey && e.shiftKey && key === "z")) {
        e.preventDefault();
        redo();
        return;
      }
    });

    if (undoBtn) {
      undoBtn.addEventListener("click", undo);
    }

    if (redoBtn) {
      redoBtn.addEventListener("click", redo);
    }
  </script>


  <!--<<<<<<<<<<<<<<<<<<<<<<<< ui hooks  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>-->
  <script>
    /* TOOLBAR ENGINE   */

    const fontFamilyPicker = document.getElementById("fontFamilyPicker");
    const fontSizePicker = document.getElementById("fontSizePicker");
    const fontColorPicker = document.getElementById("fontColorPicker");
    const deleteBtn = document.getElementById("deleteBtn");




    function normalizeFontFamilyValue(value) {
      return String(value || "")
        .split(",")[0]
        .replace(/['"]/g, "")
        .trim();
    }

    function rgbToHex(color) {
      if (!color) return "#000000";
      if (String(color).startsWith("#")) return color;

      const m = String(color).match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
      if (!m) return "#000000";

      const r = Number(m[1]).toString(16).padStart(2, "0");
      const g = Number(m[2]).toString(16).padStart(2, "0");
      const b = Number(m[3]).toString(16).padStart(2, "0");

      return `#${r}${g}${b}`;
    }

    function resetTextToolbarDefaults() {
      if (fontFamilyPicker) fontFamilyPicker.value = "Arial";
      if (fontSizePicker) fontSizePicker.value = "4";
      if (fontColorPicker) fontColorPicker.value = "#000000";
    }

    function syncTextToolbarFromSelection() {
      const selectedTextWrapper = (DesignState.selected || []).find(el => {
        return el?.classList?.contains("design-element") &&
          el.getAttribute("data-type") === "text";
      });

      if (!selectedTextWrapper) {
        resetTextToolbarDefaults();
        return;
      }

      const textNode = selectedTextWrapper.querySelector("text");
      if (!textNode) {
        resetTextToolbarDefaults();
        return;
      }

      const fontFamily = normalizeFontFamilyValue(
        textNode.getAttribute("font-family") || "Arial"
      );

      const fontSize = String(
        parseFloat(textNode.getAttribute("font-size") || "4")
      );

      const fill = rgbToHex(
        textNode.getAttribute("fill") || "#000000"
      );

      if (fontFamilyPicker) {
        const hasOption = Array.from(fontFamilyPicker.options).some(
          opt => opt.value === fontFamily
        );
        if (!hasOption) {
          const opt = document.createElement("option");
          opt.value = fontFamily;
          opt.textContent = fontFamily;
          fontFamilyPicker.appendChild(opt);
        }
        fontFamilyPicker.value = fontFamily;
      }

      if (fontSizePicker) {
        const hasOption = Array.from(fontSizePicker.options).some(
          opt => opt.value === fontSize
        );
        if (!hasOption) {
          const opt = document.createElement("option");
          opt.value = fontSize;
          opt.textContent = fontSize;
          fontSizePicker.appendChild(opt);
        }
        fontSizePicker.value = fontSize;
      }

      if (fontColorPicker) {
        fontColorPicker.value = fill;
      }
    }

    /* APPLY TO SELECTED TEXT ONLY */

    function applyToSelectedText(callback) {

      DesignState.selected.forEach(el => {

        if (!el.classList.contains("design-element")) return;
        if (el.getAttribute("data-type") !== "text") return;

        const textNode = el.querySelector("text");
        if (!textNode) return;

        callback(textNode);
      });

      renderSelection();
    }

    /* FONT FAMILY  */

    fontFamilyPicker.addEventListener("change", function() {

      if (!DesignState.selected.length) return;

      // beginAction();
      beginAction("style");

      applyToSelectedText(t => {
        t.setAttribute("font-family", this.value);
      });

      endAction();
    });


    /* FONT SIZE  */

    fontSizePicker.addEventListener("change", function() {

      if (!DesignState.selected.length) return;

      // beginAction();
      beginAction("style");

      applyToSelectedText(t => {
        t.setAttribute("font-size", this.value);
      });

      endAction();
    });


    /*  FONT COLOR  */

    // fontColorPicker.addEventListener("input", function() {

    //   if (!DesignState.selected.length) return;

    //   // beginAction();
    //   beginAction("style");

    //   applyToSelectedText(t => {
    //     t.setAttribute("fill", this.value);
    //   });

    //   endAction();
    // });

    DesignState.colorPickerActionOpen = false;

    function startColorHistoryAction() {
      if (DesignState.colorPickerActionOpen) return;
      if (!DesignState.selected.length) return;

      DesignState.colorPickerActionOpen = true;
      beginAction("style");
    }

    function finishColorHistoryAction() {
      if (!DesignState.colorPickerActionOpen) return;

      DesignState.colorPickerActionOpen = false;
      endAction();
    }

    fontColorPicker.addEventListener("pointerdown", function() {
      startColorHistoryAction();
    });

    fontColorPicker.addEventListener("input", function() {
      if (!DesignState.selected.length) return;

      if (!DesignState.colorPickerActionOpen) {
        startColorHistoryAction();
      }

      applyToSelectedText(t => {
        t.setAttribute("fill", this.value);
      });

      renderSelection();
    });

    fontColorPicker.addEventListener("change", function() {
      finishColorHistoryAction();
    });

    document.addEventListener("pointerup", function() {
      finishColorHistoryAction();
    });


    /*  BOLD  */

    function toggleBold() {

      if (!DesignState.selected.length) return;

      // beginAction();
      beginAction("style");

      applyToSelectedText(t => {
        const current = t.getAttribute("font-weight");
        t.setAttribute("font-weight",
          current === "bold" ? "normal" : "bold"
        );
      });

      endAction();
    }


    /*  ITALIC */

    function toggleItalic() {

      if (!DesignState.selected.length) return;

      // beginAction();
      beginAction("style");

      applyToSelectedText(t => {
        const current = t.getAttribute("font-style");
        t.setAttribute("font-style",
          current === "italic" ? "normal" : "italic"
        );
      });

      endAction();
    }


    /*  UNDERLINE  */

    function toggleUnderline() {

      if (!DesignState.selected.length) return;

      // beginAction();
      beginAction("style");

      applyToSelectedText(t => {
        const current = t.getAttribute("text-decoration");
        t.setAttribute("text-decoration",
          current === "underline" ? "none" : "underline"
        );
      });

      endAction();
    }


    /* DELETE  */

    function deleteSelected() {

      if (!DesignState.selected.length) return;

      // beginAction();
      beginAction("delete");

      DesignState.selected.forEach(el => {

        if (el.classList.contains("design-element")) {
          const id = el.getAttribute("data-id");
          delete DesignState.elements[id];
          el.remove();
        }

        if (el.classList.contains("design-label")) {
          el.remove();
        }

      });

      clearSelection();
      clearGuides();

      endAction();
    }

    deleteBtn.addEventListener("click", deleteSelected);


    /* KEYBOARD DELETE  */

    document.addEventListener("keydown", function(e) {

      if (e.key === "Delete") {
        deleteSelected();
      }

    });




    function moveSelected(direction, useLargeStep = false) {
      if (window.DesignState?.editing) return;
      if (!window.DesignState?.selected?.length) return;

      const moveSmall = 1; // 1mm
      const moveLarge = 5; // 5mm
      const step = useLargeStep ? moveLarge : moveSmall;

      let dx = 0;
      let dy = 0;

      if (direction === "left") dx = -step;
      if (direction === "right") dx = step;
      if (direction === "up") dy = -step;
      if (direction === "down") dy = step;

      if (dx === 0 && dy === 0) return;

      if (typeof beginAction === "function") beginAction("position");

      DesignState.selected.forEach(el => {
        if (!el) return;

        const t = el.transform.baseVal.consolidate();
        const m = t ? t.matrix : workspaceSvg.createSVGMatrix();

        const newX = typeof snap === "function" ? snap(m.e + dx) : (m.e + dx);
        const newY = typeof snap === "function" ? snap(m.f + dy) : (m.f + dy);

        el.setAttribute(
          "transform",
          `matrix(${m.a} ${m.b} ${m.c} ${m.d} ${newX} ${newY})`
        );
      });

      if (typeof renderSelection === "function") renderSelection();
      if (typeof endAction === "function") endAction();
    }

    window.moveSelected = moveSelected;
  </script>


  <!-- <<<<<<<<<<<<<<<<<<<<<<<<  PRINT MODAL  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
  <script>
    window.PrintState = {
      mode: null, // will be set when modal opens
      snapshot: null, // loaded from DB
      selectedLabels: [], // derived from UI
      generatedPages: [], // built pages
      pageSizeMM: {
        width: 0,
        height: 0
      },
      selectedPrinter: null,
      dpi: 203,
      assignedNodeIds: [],
      borderSettings: {
        type: "none",
        widthMM: 0.4
      }
    };








    function getWorkspaceLabelRectsForPrintCheck() {
      const rects = [];
      const seen = new Set();

      const candidates = [
        ...document.querySelectorAll("#printPageGroup > g"),
        ...document.querySelectorAll("[data-copy='true']")
      ];

      candidates.forEach((el, index) => {
        if (!el || seen.has(el)) return;

        const widthMM =
          parseFloat(el.getAttribute("data-width-mm")) ||
          parseFloat(el.dataset.widthMm) ||
          parseFloat(el.getAttribute("width")) ||
          0;

        const heightMM =
          parseFloat(el.getAttribute("data-height-mm")) ||
          parseFloat(el.dataset.heightMm) ||
          parseFloat(el.getAttribute("height")) ||
          0;

        if (!widthMM || !heightMM) return;

        seen.add(el);

        let x = 0;
        let y = 0;

        try {
          const consolidated = el.transform?.baseVal?.consolidate?.();
          if (consolidated?.matrix) {
            x = Number(consolidated.matrix.e) || 0;
            y = Number(consolidated.matrix.f) || 0;
          } else {
            const transform = el.getAttribute("transform") || "";
            const match = /translate\(([^,]+),([^)]+)\)/.exec(transform);
            x = parseFloat(match?.[1] || 0);
            y = parseFloat(match?.[2] || 0);
          }
        } catch (err) {
          const transform = el.getAttribute("transform") || "";
          const match = /translate\(([^,]+),([^)]+)\)/.exec(transform);
          x = parseFloat(match?.[1] || 0);
          y = parseFloat(match?.[2] || 0);
        }

        const labelNumber = el.getAttribute("data-label-number") || el.dataset.labelNumber || "";
        const labelType = el.getAttribute("data-label-type") || el.dataset.labelType || "";

        let name = "Label";
        if (labelType || labelNumber) {
          name = `${labelType ? (labelType.charAt(0).toUpperCase() + labelType.slice(1)) : "Label"} ${labelNumber || (index + 1)}`.trim();
        } else if (el.id) {
          name = el.id;
        } else {
          name = `Label ${index + 1}`;
        }

        rects.push({
          element: el,
          name,
          x,
          y,
          width: widthMM,
          height: heightMM,
          right: x + widthMM,
          bottom: y + heightMM
        });
      });

      return rects;
    }

    function validateLiveWorkspaceBeforePrint() {
      const issues = [];
      const issueSet = new Set();

      const pageWidth =
        Number(window.currentPrintPage?.width) ||
        parseFloat(document.getElementById("printPage")?.getAttribute("data-width-mm")) ||
        parseFloat(document.getElementById("printPage")?.dataset.widthMm) ||
        0;

      const pageHeight =
        Number(window.currentPrintPage?.height) ||
        parseFloat(document.getElementById("printPage")?.getAttribute("data-height-mm")) ||
        parseFloat(document.getElementById("printPage")?.dataset.heightMm) ||
        0;

      if (!pageWidth || !pageHeight) {
        return {
          ok: true,
          issues: []
        };
      }

      const SAFE_MARGIN_MM = 1.0;
      const rects = getWorkspaceLabelRectsForPrintCheck();

      rects.forEach((a) => {
        const leftGap = a.x;
        const topGap = a.y;
        const rightGap = pageWidth - a.right;
        const bottomGap = pageHeight - a.bottom;

        if (leftGap < SAFE_MARGIN_MM) {
          issueSet.add(`${a.name} is too close to the left page edge (${leftGap.toFixed(1)} mm).`);
        }
        if (topGap < SAFE_MARGIN_MM) {
          issueSet.add(`${a.name} is too close to the top page edge (${topGap.toFixed(1)} mm).`);
        }
        if (rightGap < SAFE_MARGIN_MM) {
          issueSet.add(`${a.name} is too close to the right page edge (${rightGap.toFixed(1)} mm).`);
        }
        if (bottomGap < SAFE_MARGIN_MM) {
          issueSet.add(`${a.name} is too close to the bottom page edge (${bottomGap.toFixed(1)} mm).`);
        }
      });

      for (let i = 0; i < rects.length; i++) {
        for (let j = i + 1; j < rects.length; j++) {
          const a = rects[i];
          const b = rects[j];

          const overlaps =
            a.x < b.right &&
            a.right > b.x &&
            a.y < b.bottom &&
            a.bottom > b.y;

          if (overlaps) {
            issueSet.add(`${a.name} overlaps with ${b.name}.`);
          }
        }
      }

      issueSet.forEach(msg => issues.push(msg));

      return {
        ok: issues.length === 0,
        issues
      };
    }

    function showPrintSafetyWarningModal(issues = []) {
      return new Promise((resolve) => {
        const modal = document.getElementById("printSafetyWarningModal");
        const list = document.getElementById("printSafetyWarningList");
        const closeBtn = document.getElementById("printSafetyWarningClose");
        const fixBtn = document.getElementById("printSafetyFixBtn");
        const ignoreBtn = document.getElementById("printSafetyIgnoreBtn");

        if (!modal || !list || !closeBtn || !fixBtn || !ignoreBtn) {
          resolve(false);
          return;
        }

        list.innerHTML = "";

        issues.forEach((message) => {
          const item = document.createElement("div");
          item.className = "print-safety-warning-item";
          item.innerHTML = `
        <div class="print-safety-warning-icon"><i class="bi bi-exclamation-circle ex"></i></div>
        <div class="print-safety-warning-message">${message}</div>
      `;
          list.appendChild(item);
        });

        modal.classList.add("show");
        modal.setAttribute("aria-hidden", "false");

        const cleanup = () => {
          modal.classList.remove("show");
          modal.setAttribute("aria-hidden", "true");

          closeBtn.removeEventListener("click", onFix);
          fixBtn.removeEventListener("click", onFix);
          ignoreBtn.removeEventListener("click", onIgnore);
          modal.removeEventListener("click", onBackdrop);
        };

        const onFix = () => {
          cleanup();
          resolve(false);
        };

        const onIgnore = () => {
          cleanup();
          resolve(true);
        };

        const onBackdrop = (e) => {
          if (e.target === modal) {
            cleanup();
            resolve(false);
          }
        };

        closeBtn.addEventListener("click", onFix);
        fixBtn.addEventListener("click", onFix);
        ignoreBtn.addEventListener("click", onIgnore);
        modal.addEventListener("click", onBackdrop);
      });
    }

    async function checkPrintSafetyAndConfirm() {
      const check = validateLiveWorkspaceBeforePrint();

      if (check.ok) {
        return true;
      }

      const shouldContinue = await showPrintSafetyWarningModal(check.issues);
      return shouldContinue;
    }
















    // PRINT BUTTON CLICK HANDLER (SAFE + CLEAN)

    /* ===== CTRL + P → OPEN PRINT SIMULATOR ===== */
    document.addEventListener("keydown", async (e) => {

      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "p") {

        e.preventDefault(); // stop browser print dialog

        const printBtn = document.getElementById("printBtn");

        if (printBtn) {
          printBtn.click(); // trigger the same logic
        }

      }

    });
    document.getElementById("printBtn")?.addEventListener("click", async () => {

      try {

        /* CHECK DESIGN EXISTS */

        const labelGroup = document.getElementById("labelGroup");

        const hasDesign =
          labelGroup &&
          labelGroup.querySelectorAll(".design-element").length > 0;

        if (!hasDesign) {
          showToast("No design available to print", "warning");
          return;
        }


        const shouldContinuePrint = await checkPrintSafetyAndConfirm();

        if (!shouldContinuePrint) {
          return;
        }

        /* AUTO SAVE  */

        await saveLayoutData();

        /* FETCH SNAPSHOT FROM DB  */

        const res = await fetch(
          "<?= site_url('items/getFields') ?>?modelname=" +
          encodeURIComponent(currentModelName)
        );

        const data = await res.json();

        if (data.status !== "success" || !data.fields?.length) {
          showToast("No saved layout found in database", "error");
          return;
        }

        const fullSnapshot = data.fields[0].full_snapshot;

        if (!fullSnapshot || fullSnapshot === "null") {
          showToast("No saved layout found in database", "error");
          return;
        }

        let snapshot;

        try {
          snapshot = JSON.parse(fullSnapshot);
        } catch (err) {
          console.error("Invalid snapshot JSON");
          showToast("Saved layout corrupted", "error");
          return;
        }

        if (!snapshot?.printPage || !Array.isArray(snapshot.labels)) {
          showToast("Invalid print data structure", "error");
          return;
        }

        /*  STORE IN PRINT STATE  */

        PrintState.snapshot = snapshot;

        PrintState.pageSizeMM.width =
          Number(snapshot.printPage.widthMM) || 0;

        PrintState.pageSizeMM.height =
          Number(snapshot.printPage.heightMM) || 0;

        PrintState.generatedPages = [];
        PrintState.selectedLabels = [];
        PrintState.assignedNodeIds = [];

        /*  DETECT MODE  */

        const isBatch =
          document.querySelectorAll(".count-step-checkbox:checked").length > 0;

        PrintState.mode = isBatch ? "batch" : "normal";





        /* CHECK ALL LABELS HAVE QR VALUE BEFORE OPENING MODAL */

        const qrValidation = validateAllLabelsHaveQRValue(snapshot);

        if (!qrValidation.ok) {
          const missingLabelText = qrValidation.missing.join(", ");
          showToast(
            `No QR or value in "${missingLabelText}". Add QR to proceed.`,
            "warning"
          );
          return;
        }

        /* ===  OPEN MODAL ==== */

        openPrintModal(PrintState.mode);

        /* === INITIALIZE LABEL SELECTOR === */

        initializeLabelSelector(PrintState.snapshot);

        showToast("Opening print simulator", "success");

      } catch (err) {

        console.error("Print preparation error:", err);
        showToast("Failed to prepare print", "error");
      }

    });

    function validateAllLabelsHaveQRValue(snapshot) {
      try {
        if (!snapshot || !Array.isArray(snapshot.labels)) {
          return {
            ok: false,
            missing: ["Unknown Label"]
          };
        }

        const missingLabels = [];

        snapshot.labels.forEach((label, index) => {
          const labelDisplayName = `${capitalizeLabelType(label?.type)} Label ${label?.labelNumber || (index + 1)}`;

          const elements = Array.isArray(label?.elements) ? label.elements : [];

          const qrElement = elements.find(el => String(el?.type).toLowerCase() === "qr");

          const qrValue = String(qrElement?.value ?? "").trim();

          // Rule:
          // label must contain a QR element and QR value must not be empty
          if (!qrElement || !qrValue) {
            missingLabels.push(labelDisplayName);
          }
        });

        return {
          ok: missingLabels.length === 0,
          missing: missingLabels
        };

      } catch (err) {
        console.error("QR validation failed:", err);
        return {
          ok: false,
          missing: ["Unknown Label"]
        };
      }
    }

    function capitalizeLabelType(type) {
      const value = String(type || "Label").toLowerCase();
      return value.charAt(0).toUpperCase() + value.slice(1);
    }

    /*<<<<<<< PRINT MODAL OPEN FUNCTION >>>>>>*/
    function openPrintModal(mode) {

      const modal = document.getElementById("printModal");
      if (!modal) return;

      modal.style.display = "flex";

      PrintState.mode = mode;
      PrintState.generatedPages = [];
      PrintState.assignedNodeIds = [];

      const isBatch = mode === "batch";

      const countInput = document.getElementById("countLimitInput");
      const applyBtn = document.getElementById("applyCountLimit");

      if (countInput && applyBtn) {
        countInput.disabled = !isBatch;
        applyBtn.disabled = !isBatch;
      }

      /* DEFAULT PAGE SIZE FROM STATE */
      if (PrintState.snapshot?.printPage) {
        PrintState.pageSizeMM.width =
          Number(PrintState.snapshot.printPage.widthMM) || 0;

        PrintState.pageSizeMM.height =
          Number(PrintState.snapshot.printPage.heightMM) || 0;
      }

      const dpiSelect = document.getElementById("dpiSelect");
      if (dpiSelect) {
        PrintState.dpi = Number(dpiSelect.value) || 203;
      }

      const defaultRadio = document.querySelector(
        'input[name="pageSize"][value="default"]'
      );

      if (defaultRadio) defaultRadio.checked = true;

      const customBlock = document.getElementById("customSizeBlock");
      if (customBlock) customBlock.classList.add("hidden");

      initializePrinterSelector(); // FIXED NAME
      toggleApplyButton();

      syncPrintBorderSettingsFromUI();
    }

    /*<<<<< CLOSE PRINT MODAL  >>>>>>>> */

    function resetPrintModalState() {

      /* ===== RESET PRINT STATE ===== */

      PrintState.mode = null;
      PrintState.snapshot = null;
      PrintState.selectedLabels = [];
      PrintState.generatedPages = [];
      PrintState.assignedNodeIds = [];

      PrintState.pageSizeMM.width = 0;
      PrintState.pageSizeMM.height = 0;

      /* ===== CLEAR PREVIEW ===== */

      const preview = document.getElementById("previewContainer");
      if (preview) preview.innerHTML = "";

      /* ===== CLEAR LABEL SELECTOR ===== */

      const labelDropdown = document.getElementById("labelDropdown");
      if (labelDropdown) labelDropdown.innerHTML = "";

      /* ===== RESET COUNT INPUT ===== */

      const countInput = document.getElementById("countLimitInput");
      if (countInput) countInput.value = "";

      /* ===== RESET COPIES ===== */

      const copiesInput = document.getElementById("copiesInput");
      if (copiesInput) copiesInput.value = 1;

      /* ===== RESET PAGE SIZE OPTIONS ===== */

      const defaultRadio = document.querySelector(
        'input[name="pageSize"][value="default"]'
      );

      if (defaultRadio) defaultRadio.checked = true;

      const customBlock = document.getElementById("customSizeBlock");
      if (customBlock) customBlock.classList.add("hidden");

      const customWidth = document.getElementById("customWidth");
      const customHeight = document.getElementById("customHeight");

      if (customWidth) customWidth.value = "";
      if (customHeight) customHeight.value = "";

      /* ===== RESET PRINTER SELECT ===== */

      const printerSelect = document.getElementById("printerSelect");
      if (printerSelect) {
        printerSelect.value = "";
      }

      const borderType = document.getElementById("labelBorderType");
      const borderWidth = document.getElementById("labelBorderWidth");

      if (borderType) borderType.value = "none";
      if (borderWidth) borderWidth.value = "0.4";

      PrintState.borderSettings = {
        type: "none",
        widthMM: 0.4
      };

    }
    document.getElementById("closePrintModal")?.addEventListener("click", () => {

      const modal = document.getElementById("printModal");
      if (modal) modal.style.display = "none";
      resetPrintModalState(); // FULL RESET
    });

    //<<<<<<<<<<<<<<<< printer fetching via QZ tray   >>>>>>>>>>>>>>>
    let qzConnected = false;

    /*   CONNECT TO QZ TRAY  */
    async function connectQZ() {
      try {

        if (qzConnected) return true;

        await qz.websocket.connect();
        qzConnected = true;

        console.log("QZ Connected");
        return true;

      } catch (err) {
        console.error("QZ Connection Failed:", err);
        showToast("Printer service not running (QZ Tray)", "error");
        return false;
      }
    }
    // INITIALIZE PRINTER DROPDOWN (SAFE FOR NORMAL + BATCH)

    async function initializePrinterSelector() {

      const printerSelect = document.getElementById("printerSelect");
      if (!printerSelect) return;

      printerSelect.innerHTML = `<option value="">Loading printers...</option>`;
      printerSelect.disabled = true;

      PrintState.selectedPrinter = null; // reset state

      const connected = await connectQZ();
      if (!connected) {
        printerSelect.innerHTML = `<option value="">Printer service unavailable</option>`;
        return;
      }

      try {

        const printers = await qz.printers.find();

        if (!Array.isArray(printers) || printers.length === 0) {
          printerSelect.innerHTML = `<option value="">No printers found</option>`;
          printerSelect.disabled = true;
          return;
        }

        printerSelect.innerHTML = `<option value="">Select Printer</option>`;

        printers.forEach(name => {
          const option = document.createElement("option");
          option.value = name;
          option.textContent = name;
          printerSelect.appendChild(option);
        });

        /* ================= AUTO SELECT DEFAULT ================= */

        let selected = null;

        try {
          const defaultPrinter = await qz.printers.getDefault();
          if (defaultPrinter && printers.includes(defaultPrinter)) {
            selected = defaultPrinter;
          }
        } catch (e) {
          console.warn("Default printer fetch failed");
        }

        if (!selected) {
          selected = printers[0]; // fallback to first printer
        }

        printerSelect.value = selected;
        PrintState.selectedPrinter = selected;

        printerSelect.disabled = false;

        /* ================= CHANGE LISTENER ================= */

        printerSelect.onchange = function() {
          PrintState.selectedPrinter = this.value || null;
        };

        console.log("Printers initialized:", printers);
        console.log("Selected printer:", PrintState.selectedPrinter);

      } catch (err) {
        console.error("Printer initialization failed:", err);
        printerSelect.innerHTML = `<option value="">Failed to load printers</option>`;
        printerSelect.disabled = true;
        showToast("Failed to load printers", "error");
      }
    }

    // INITIALIZE LABEL SELECTOR

    function initializeLabelSelector(snapshot) {

      const container = document.getElementById("labelDropdown");
      if (!container) return;

      container.innerHTML = "";

      if (!snapshot || !Array.isArray(snapshot.labels) || !snapshot.labels.length) {
        return;
      }

      PrintState.snapshot = snapshot;

      /* ================= FULL DESIGN OPTION ================= */

      const fullWrapper = document.createElement("div");
      fullWrapper.className = "dropdown-item label-row";

      const fullCheckbox = document.createElement("input");
      fullCheckbox.type = "checkbox";
      fullCheckbox.value = "full";
      fullCheckbox.className = "label-select-checkbox full-design-checkbox";

      const fullLabel = document.createElement("label");
      fullLabel.textContent = "Full Design";

      fullWrapper.append(fullCheckbox, fullLabel);
      container.appendChild(fullWrapper);

      /* ================= INDIVIDUAL LABELS ================= */

      snapshot.labels.forEach((label, index) => {

        const wrapper = document.createElement("div");
        wrapper.className = "dropdown-item label-row";

        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.value = index;
        checkbox.className = "label-select-checkbox individual-label-checkbox";

        const labelText = document.createElement("label");
        labelText.textContent =
          `Label ${label.labelNumber} (${label.type === "main" ? "Main" : "Copy"})`;

        wrapper.append(checkbox, labelText);
        container.appendChild(wrapper);
      });

      attachLabelSelectionHandlers(container);
    }

    // ATTACH LABEL SELECTION EVENTS
    function attachLabelSelectionHandlers(container) {

      const fullCheckbox = container.querySelector(".full-design-checkbox");
      const individualCheckboxes =
        container.querySelectorAll(".individual-label-checkbox");

      if (!fullCheckbox) return;

      /* ===== FULL DESIGN ===== */
      fullCheckbox.addEventListener("change", function() {

        if (this.checked) {
          individualCheckboxes.forEach(cb => {
            cb.checked = false;
            cb.disabled = true;
          });
        } else {
          individualCheckboxes.forEach(cb => {
            cb.disabled = false;
          });
        }

        handlePreviewUpdateByMode();
      });

      /* ===== INDIVIDUAL ===== */
      individualCheckboxes.forEach(cb => {

        cb.addEventListener("change", function() {

          if (this.checked) {
            fullCheckbox.checked = false;
          }

          individualCheckboxes.forEach(x => x.disabled = false);

          handlePreviewUpdateByMode();
        });

      });
    }

    // PREVIEW CONTROLLER (NORMAL + BATCH )

    function handlePreviewUpdateByMode() {

      const selectedLabels = getSelectedLabelsFromUI();

      if (!selectedLabels.length) {
        renderEmptyPreview("Please select print content");
        return;
      }

      /* CLONE LABELS SO ORIGINAL JSON IS NEVER MODIFIED */

      const previewLabels = JSON.parse(JSON.stringify(selectedLabels));

      /* APPLY SMART ALIGNMENT ONLY FOR LABEL COMBINATIONS */

      if (!isFullDesignSelected()) {
        applySmartAlignment(previewLabels);
      }

      if (PrintState.mode === "normal") {
        renderNormalPreview(previewLabels);
      }

      if (PrintState.mode === "batch") {
        renderBatchPreview(previewLabels);
      }
    }

    // EXTRACT SELECTED LABELS FROM UI
    function getSelectedLabelsFromUI() {

      if (!PrintState.snapshot?.labels) return [];

      const fullChecked =
        document.querySelector(".full-design-checkbox")?.checked;

      if (fullChecked) {
        return PrintState.snapshot.labels;
      }

      const selected = Array.from(
        document.querySelectorAll(".individual-label-checkbox:checked")
      );

      return selected
        .map(cb => {
          const index = Number(cb.value);
          return PrintState.snapshot.labels[index];
        })
        .filter(Boolean);
    }


    //border selection
    function syncPrintBorderSettingsFromUI() {
      const typeEl = document.getElementById("labelBorderType");
      const widthEl = document.getElementById("labelBorderWidth");

      const type = String(typeEl?.value || "none").trim().toLowerCase();
      const widthMM = Number(widthEl?.value);

      PrintState.borderSettings = {
        type: ["none", "solid", "dashed", "dotted"].includes(type) ? type : "none",
        widthMM: Number.isFinite(widthMM) && widthMM >= 0 ? widthMM : 0.4
      };
    }

    function getBorderDashArray(type, strokeWidth) {
      if (type === "dashed") {
        return `${strokeWidth * 6} ${strokeWidth * 3}`;
      }
      if (type === "dotted") {
        return `${strokeWidth} ${strokeWidth * 2.5}`;
      }
      return null;
    }

    document.getElementById("labelBorderType")?.addEventListener("change", function() {
      syncPrintBorderSettingsFromUI();
      handlePreviewUpdateByMode();
    });

    document.getElementById("labelBorderWidth")?.addEventListener("input", function() {
      syncPrintBorderSettingsFromUI();
      handlePreviewUpdateByMode();
    });

    // PAGE SIZE MODE SWITCH  
    document.querySelectorAll('input[name="pageSize"]').forEach(radio => {

      radio.addEventListener("change", function() {

        const customBlock = document.getElementById("customSizeBlock");
        if (!customBlock) return;

        if (this.value === "custom") {

          customBlock.classList.remove("hidden");

        } else {

          customBlock.classList.add("hidden");

          if (PrintState.snapshot?.printPage) {
            PrintState.pageSizeMM.width =
              Number(PrintState.snapshot.printPage.widthMM) || 0;

            PrintState.pageSizeMM.height =
              Number(PrintState.snapshot.printPage.heightMM) || 0;
          }

          refreshPreviewAfterPageSizeChange();
        }
      });

    });

    // APPLY CUSTOM PAGE SIZE (MM SAFE)
    document.getElementById("applyCustomSize")
      ?.addEventListener("click", () => {

        const w = Number(document.getElementById("customWidth")?.value);
        const h = Number(document.getElementById("customHeight")?.value);

        if (!w || !h || w <= 0 || h <= 0) {
          showToast("Enter valid custom page size (mm)", "warning");
          return;
        }

        PrintState.pageSizeMM.width = w;
        PrintState.pageSizeMM.height = h;

        showToast("Custom page size applied", "success");

        refreshPreviewAfterPageSizeChange();
      });


    //REFRESH PREVIEW AFTER PAGE SIZE CHANGE
    function refreshPreviewAfterPageSizeChange() {

      // No snapshot? nothing to refresh
      if (!PrintState.snapshot || !Array.isArray(PrintState.snapshot.labels)) {
        return;
      }

      // Let the main preview controller handle everything
      handlePreviewUpdateByMode();
    }


    // /* ================= DPI SYNC ================= */

    document.getElementById("dpiSelect")?.addEventListener("change", function() {
      PrintState.dpi = Number(this.value) || 203;
    });

    function renderPrintPage(widthMM, heightMM) {

      if (
        isNaN(widthMM) || isNaN(heightMM) ||
        widthMM <= 0 || heightMM <= 0
      ) {
        return null;
      }

      const previewContainer = document.getElementById("previewContainer");
      if (!previewContainer) return null;

      /* ================= CLEAR PREVIOUS PAGE ================= */

      previewContainer.innerHTML = "";

      const SVG_NS = "http://www.w3.org/2000/svg";

      /* ================= CREATE ROOT SVG ================= */

      const svg = document.createElementNS(SVG_NS, "svg");

      svg.setAttribute("width", widthMM + "mm");
      svg.setAttribute("height", heightMM + "mm");
      svg.setAttribute("viewBox", `0 0 ${widthMM} ${heightMM}`);
      svg.setAttribute("id", "printPreviewSvg");

      previewContainer.appendChild(svg);

      /* ================= MAIN PRINT PREVIEW GROUP ================= */

      const printPreviewGroup = document.createElementNS(SVG_NS, "g");
      printPreviewGroup.setAttribute("id", "printPreviewGroup");

      svg.appendChild(printPreviewGroup);

      /* ================= PAGE BORDER (OPTIONAL VISUAL) ================= */

      const rect = document.createElementNS(SVG_NS, "rect");

      rect.setAttribute("x", 0);
      rect.setAttribute("y", 0);
      rect.setAttribute("width", widthMM);
      rect.setAttribute("height", heightMM);


      rect.setAttribute("fill", "#ffffff");
      rect.setAttribute("stroke", "#111827");
      rect.setAttribute("stroke-width", 0.4);
      rect.setAttribute("vector-effect", "non-scaling-stroke");

      printPreviewGroup.appendChild(rect);

      /* ================= UPDATE STATE ================= */

      PrintState.pageSizeMM.width = widthMM;
      PrintState.pageSizeMM.height = heightMM;

      /* ================= RETURN LABEL CONTAINER ================= */

      return printPreviewGroup;
    }

    function renderPrintLabelFromJSON(labelData, parentGroup, overrides = {}, borderOptions = null) {

      if (!labelData || !parentGroup) return null;

      const SVG_NS = "http://www.w3.org/2000/svg";

      const labelGroup = document.createElementNS(SVG_NS, "g");
      labelGroup.setAttribute("class", "print-label");
      labelGroup.setAttribute("data-label-number", labelData.labelNumber);

      /* ================= LABEL POSITION ================= */

      const pos = labelData.position || {};
      const x = Number(pos.x) || 0;
      const y = Number(pos.y) || 0;

      labelGroup.setAttribute("transform", `translate(${x}, ${y})`);

      /* ================= LABEL BORDER ================= */

      const width = Number(labelData.widthMM) || 0;
      const height = Number(labelData.heightMM) || 0;

      // const border = document.createElementNS(SVG_NS, "rect");

      // border.setAttribute("x", 0);
      // border.setAttribute("y", 0);
      // border.setAttribute("width", width);
      // border.setAttribute("height", height);
      // border.setAttribute("fill", "transparent");
      // border.setAttribute("pointer-events", "all");
      // border.setAttribute("stroke", "#2563EB");
      // border.setAttribute("stroke-width", 0.4);
      // border.setAttribute("vector-effect", "non-scaling-stroke");
      // border.setAttribute("data-boundary", "true");

      // labelGroup.appendChild(border);









      const border = document.createElementNS(SVG_NS, "rect");

      border.setAttribute("x", 0);
      border.setAttribute("y", 0);
      border.setAttribute("width", width);
      border.setAttribute("height", height);
      border.setAttribute("fill", "transparent");
      border.setAttribute("pointer-events", "all");
      border.setAttribute("vector-effect", "non-scaling-stroke");
      border.setAttribute("data-boundary", "true");

      const finalBorderType = String(borderOptions?.type || "none").toLowerCase();
      const finalBorderWidth = Number(borderOptions?.widthMM ?? 0.4);

      if (finalBorderType === "none" || finalBorderWidth <= 0) {
        border.setAttribute("stroke", "none");
        border.setAttribute("stroke-width", "0");
      } else {
        border.setAttribute("stroke", "#111827");
        border.setAttribute("stroke-width", finalBorderWidth);

        const dashArray = getBorderDashArray(finalBorderType, finalBorderWidth);
        if (dashArray) {
          border.setAttribute("stroke-dasharray", dashArray);
          if (finalBorderType === "dotted") {
            border.setAttribute("stroke-linecap", "round");
          }
        }
      }

      labelGroup.appendChild(border);

      /* ================= BUILD ELEMENTS ================= */

      if (Array.isArray(labelData.elements)) {

        labelData.elements.forEach(el => {

          const overrideValue =
            overrides[el.id] !== undefined ?
            overrides[el.id] :
            null;

          switch (el.type) {
            case "text":
              /* ── FIX: pass label width so word-wrap knows the boundary ── */
              renderTextForPrint(el, labelGroup, overrideValue, width);
              break;
            case "qr":
              renderQRForPrint(el, labelGroup, overrideValue);
              break;
            case "barcode":
              renderBarcodeForPrint(el, labelGroup, overrideValue);
              break;
            case "image":
              renderImageForPrint(el, labelGroup);
              break;
          }

        });
      }

      parentGroup.appendChild(labelGroup);

      return labelGroup;
    }

    function renderTextForPrint(elementData, targetLabelGroup, overrideValue = null, labelWidthMM = 80) {

      if (!elementData || !targetLabelGroup) return;

      const svgNS = "http://www.w3.org/2000/svg";

      /* ================= VALUE ================= */

      const content =
        overrideValue !== null ?
        String(overrideValue) :
        String(elementData.text || "");

      /* ================= TRANSFORM ================= */

      const t = elementData.transform || {};

      const posX = Number(t.x) || 0;
      const posY = Number(t.y) || 0;

      const scaleX = t.scaleX ?? 1;
      const scaleY = t.scaleY ?? 1;
      const angle = t.angle ?? 0;

      /* ================= STYLE ================= */

      const fontSize = Number(elementData.fontSize) || 4;
      const fontFamily = elementData.fontFamily || "Arial";
      const fill = elementData.fill || "#000";

      const bold = !!elementData.bold;
      const italic = !!elementData.italic;
      const underline = !!elementData.underline;

      /* ================= WRAPPER ================= */

      const wrapper = document.createElementNS(svgNS, "g");

      wrapper.setAttribute(
        "transform",
        `translate(${posX}, ${posY}) rotate(${angle}) scale(${scaleX}, ${scaleY})`
      );

      wrapper.setAttribute("data-type", "text");

      if (elementData.id) {
        wrapper.setAttribute("data-id", elementData.id);
      }

      /* ================= TEXT NODE ================= */

      const textEl = document.createElementNS(svgNS, "text");

      textEl.setAttribute("x", 0);
      textEl.setAttribute("y", 0);
      textEl.setAttribute("font-size", fontSize);
      textEl.setAttribute("font-family", fontFamily);
      textEl.setAttribute("fill", fill);
      textEl.setAttribute("dominant-baseline", "hanging");

      if (bold) textEl.setAttribute("font-weight", "bold");
      if (italic) textEl.setAttribute("font-style", "italic");
      if (underline) textEl.setAttribute("text-decoration", "underline");

      /* ── FIX: word-wrap so long text never overflows the label boundary ── */
      const availableW = Math.max((labelWidthMM - posX - 2), 10);
      const lineH      = fontSize * 1.3;
      const lines      = expandAndWrapLines(content, availableW, fontSize, fontFamily);

      applyLinesToTextEl(textEl, lines, lineH, svgNS);

      wrapper.appendChild(textEl);

      /* ================= APPEND TO LABEL ================= */

      targetLabelGroup.appendChild(wrapper);

      return wrapper;
    }

    function renderQRForPrint(elementData, targetLabelGroup, overrideValue = null) {

      if (!elementData || !targetLabelGroup) return;

      const svgNS = "http://www.w3.org/2000/svg";

      /* ================= VALUE ================= */

      const value =
        overrideValue !== null ?
        overrideValue :
        elementData.value || "";

      const sizeMM = elementData.size || 20;

      /* ================= TRANSFORM ================= */

      const t = elementData.transform || {};

      const posX = Number(t.x) || 0;
      const posY = Number(t.y) || 0;
      const scaleX = t.scaleX ?? 1;
      const scaleY = t.scaleY ?? 1;
      const angle = t.angle ?? 0;

      const efEnabled = elementData.efEnabled ?? true;
      const efText = elementData.efText || "EF";

      /* ================= WRAPPER ================= */

      const wrapper = document.createElementNS(svgNS, "g");

      wrapper.setAttribute(
        "transform",
        `translate(${posX}, ${posY}) rotate(${angle}) scale(${scaleX}, ${scaleY})`
      );

      wrapper.setAttribute("data-type", "qr");
      wrapper.setAttribute("data-value", value);

      if (elementData.id) {
        wrapper.setAttribute("data-id", elementData.id);
      }

      /* ================= QR GENERATION ================= */

      const qr = qrcode(0, "H");
      qr.addData(value);
      qr.make();

      const moduleCount = qr.getModuleCount();
      const quietZone = 4;
      const totalModules = moduleCount + quietZone * 2;
      const cellSize = sizeMM / totalModules;

      const svg = document.createElementNS(svgNS, "svg");
      svg.setAttribute("width", sizeMM);
      svg.setAttribute("height", sizeMM);
      svg.setAttribute("viewBox", `0 0 ${sizeMM} ${sizeMM}`);
      svg.setAttribute("shape-rendering", "crispEdges");

      /* Background */

      const bg = document.createElementNS(svgNS, "rect");
      bg.setAttribute("width", sizeMM);
      bg.setAttribute("height", sizeMM);
      bg.setAttribute("fill", "#fff");
      svg.appendChild(bg);

      /* Modules */

      for (let r = 0; r < moduleCount; r++) {
        for (let c = 0; c < moduleCount; c++) {

          if (!qr.isDark(r, c)) continue;

          const rect = document.createElementNS(svgNS, "rect");
          rect.setAttribute("x", (c + quietZone) * cellSize);
          rect.setAttribute("y", (r + quietZone) * cellSize);
          rect.setAttribute("width", cellSize);
          rect.setAttribute("height", cellSize);
          rect.setAttribute("fill", "#000");

          svg.appendChild(rect);
        }
      }

      /* ================= EF OVERLAY ================= */

      if (efEnabled) {

        const overlay = document.createElementNS(svgNS, "g");

        const logoSize = sizeMM * 0.16;

        const knockout = document.createElementNS(svgNS, "rect");

        /* square same size as logo */
        const boxSize = logoSize;

        knockout.setAttribute("x", sizeMM / 2 - boxSize / 2);
        knockout.setAttribute("y", sizeMM / 2 - boxSize / 2);
        knockout.setAttribute("width", boxSize);
        knockout.setAttribute("height", boxSize);
        knockout.setAttribute("fill", "#fff");
        knockout.setAttribute("stroke", "#000000");
        knockout.setAttribute("stroke-width", 0.2);


        const text = document.createElementNS(svgNS, "text");
        text.setAttribute("x", sizeMM / 2);
        text.setAttribute("y", sizeMM / 2);
        text.setAttribute("text-anchor", "middle");
        text.setAttribute("dominant-baseline", "middle");
        text.setAttribute("dy", "0.09em")
        text.setAttribute("font-size", logoSize * 0.70);
        text.setAttribute("font-weight", "bold");
        text.setAttribute("fill", "#000");
        text.setAttribute("pointer-events", "none");
        text.textContent = efText;

        overlay.appendChild(knockout);
        overlay.appendChild(text);

        svg.appendChild(overlay);
      }

      wrapper.appendChild(svg);

      /* ================= APPEND TO LABEL ================= */

      targetLabelGroup.appendChild(wrapper);

      return wrapper;
    }

    function renderBarcodeForPrint(elementData, targetLabelGroup, overrideValue = null) {

      if (!elementData || !targetLabelGroup) return;

      const svgNS = "http://www.w3.org/2000/svg";

      /* ================= VALUE ================= */

      const finalValue =
        overrideValue !== null ?
        String(overrideValue) :
        String(elementData.value || "");

      if (!finalValue.trim()) {
        console.warn("Barcode value empty");
        return;
      }

      const finalFormat = elementData.format || "code128";
      const finalWidthMM = Number(elementData.width) || 40;
      const finalHeightMM = Number(elementData.height) || 12;

      /* ================= TRANSFORM ================= */

      const t = elementData.transform || {};

      const posX = Number(t.x) || 0;
      const posY = Number(t.y) || 0;
      const scaleX = Number(t.scaleX ?? 1);
      const scaleY = Number(t.scaleY ?? 1);
      const angle = Number(t.angle ?? 0);

      /* ================= DPI SAFE ================= */

      const dpi = Number(PrintState.dpi) || 203;
      const DOT_MM = 25.4 / dpi;

      /*
        Scan-safe standard:
        203 DPI → 2 dots per module
        300 DPI → 3 dots per module
        600 DPI → 4 dots per module
      */

      let MODULE_DOTS = 2;

      if (dpi >= 600) MODULE_DOTS = 4;
      else if (dpi >= 300) MODULE_DOTS = 3;

      const moduleWidthMM = MODULE_DOTS * DOT_MM;

      /* Quiet zone = 10 dots each side (industrial safe) */

      const QUIET_ZONE_DOTS = 10;
      const QUIET_ZONE_MM = QUIET_ZONE_DOTS * DOT_MM;

      /* GET BAR COUNT  */

      const tempSvg = document.createElementNS(svgNS, "svg");

      JsBarcode(tempSvg, finalValue, {
        format: finalFormat,
        width: 1,
        height: 10,
        displayValue: false,
        margin: 0
      });

      const bars = tempSvg.querySelectorAll("rect");
      if (!bars.length) {
        console.warn("Barcode generation failed");
        return;
      }

      let maxRight = 0;

      bars.forEach(bar => {
        const x = parseFloat(bar.getAttribute("x")) || 0;
        const w = parseFloat(bar.getAttribute("width")) || 0;
        const right = x + w;
        if (right > maxRight) maxRight = right;
      });

      const intrinsicUnits = maxRight;

      /* ====  CALCULATE REAL WIDTH ==== */

      const actualBarcodeWidth =
        intrinsicUnits * moduleWidthMM;

      const requiredWidth =
        actualBarcodeWidth + (QUIET_ZONE_MM * 2);

      if (requiredWidth > finalWidthMM) {
        console.warn("Barcode container width too small for selected DPI/data");
        return;
      }

      /* ==== BUILD FINAL SVG ===*/

      const svg = document.createElementNS(svgNS, "svg");

      svg.setAttribute("width", finalWidthMM);
      svg.setAttribute("height", finalHeightMM);
      svg.setAttribute("viewBox", `0 0 ${finalWidthMM} ${finalHeightMM}`);
      svg.setAttribute("shape-rendering", "crispEdges");

      JsBarcode(svg, finalValue, {
        format: finalFormat,
        width: moduleWidthMM,
        height: finalHeightMM,
        displayValue: false,
        margin: QUIET_ZONE_MM,
        flat: true
      });

      /* ==== WRAPPER ==== */

      const wrapper = document.createElementNS(svgNS, "g");

      wrapper.setAttribute(
        "transform",
        `translate(${posX}, ${posY}) rotate(${angle}) scale(${scaleX}, ${scaleY})`
      );

      wrapper.dataset.type = "barcode";
      wrapper.dataset.value = finalValue;
      wrapper.dataset.format = finalFormat;
      wrapper.dataset.width = finalWidthMM;
      wrapper.dataset.height = finalHeightMM;

      if (elementData.id) {
        wrapper.setAttribute("data-id", elementData.id);
      }

      wrapper.appendChild(svg);
      targetLabelGroup.appendChild(wrapper);

      return wrapper;
    }

    function renderImageForPrint(elementData, targetLabelGroup) {

      if (!elementData || !targetLabelGroup) return;

      const svgNS = "http://www.w3.org/2000/svg";

      /* ================= SOURCE ================= */

      const finalSrc = elementData.src || "";
      const finalWidth = Number(elementData.width) || 20;
      const finalHeight = Number(elementData.height) || 20;

      /* ================= TRANSFORM ================= */

      const t = elementData.transform || {};

      const posX = Number(t.x) || 0;
      const posY = Number(t.y) || 0;
      const scaleX = t.scaleX ?? 1;
      const scaleY = t.scaleY ?? 1;
      const angle = t.angle ?? 0;

      /* ================= WRAPPER ================= */

      const wrapper = document.createElementNS(svgNS, "g");

      wrapper.setAttribute(
        "transform",
        `translate(${posX}, ${posY}) rotate(${angle}) scale(${scaleX}, ${scaleY})`
      );

      wrapper.setAttribute("data-type", "image");

      if (elementData.id) {
        wrapper.setAttribute("data-id", elementData.id);
      }

      /* ================= IMAGE ELEMENT ================= */

      const image = document.createElementNS(svgNS, "image");

      image.setAttributeNS(null, "href", finalSrc);
      image.setAttribute("x", 0);
      image.setAttribute("y", 0);
      image.setAttribute("width", finalWidth);
      image.setAttribute("height", finalHeight);

      wrapper.appendChild(image);

      /* ================= APPEND TO LABEL ================= */

      targetLabelGroup.appendChild(wrapper);

      return wrapper;
    }


    function renderEmptyPreview(message) {

      const preview = document.getElementById("previewContainer");
      if (!preview) return;

      preview.innerHTML = `
                          <div style="color:#fff;text-align:center;">
                            ${message}
                          </div>
                        `;
    }




    function buildNodeIdResultJSON() {

      if (!Array.isArray(PrintState.assignedNodeIds) ||
        !PrintState.assignedNodeIds.length) {
        return [];
      }

      return PrintState.assignedNodeIds
        .filter(id => id !== undefined && id !== null)
        .map((nodeId, index) => ({
          id: index + 1,
          node_id: nodeId,
          status: "assigned"
        }));
    }

    document.getElementById("applyCountLimit")
      ?.addEventListener("click", async () => {

        if (PrintState.mode !== "batch") return;

        const countLimit = Number(
          document.getElementById("countLimitInput")?.value
        );

        if (!countLimit || countLimit <= 0) {
          showToast("Count limit must be greater than 0", "warning");
          return;
        }

        const selectedLabels = getSelectedLabelsFromUI();

        if (!selectedLabels.length) {
          showToast("Select print content", "warning");
          return;
        }

        await buildBatchPages(selectedLabels, countLimit);

        renderBatchPreview(selectedLabels);
      });





    // <<<<<<<<<<<<  Drag functions  >>>>>>>>>>>>>
    let selectedLabel = null;
    let dragTarget = null;
    let dragSVG = null;

    let startMouse = {
      x: 0,
      y: 0
    };
    let startTranslate = {
      x: 0,
      y: 0
    };

    const defaultRadio = document.getElementById("positionDefault");
    const customRadio = document.getElementById("positionCustom");
    const applyBtn = document.getElementById("applyCustomPosition");

    function toggleApplyButton() {

      const isCustom = customRadio.checked;

      if (!isCustom) {
        applyBtn.classList.add("hidden");
        return;
      }

      if (PrintState.mode === "batch") {
        applyBtn.classList.remove("hidden");
      } else {
        applyBtn.classList.add("hidden");
      }

      enablePreviewDragging();

    }

    defaultRadio.addEventListener("change", toggleApplyButton);
    customRadio.addEventListener("change", toggleApplyButton);


    function enablePreviewDragging() {

      const labels = document.querySelectorAll(".print-label");

      labels.forEach(label => {

        label.style.cursor = "pointer";

        label.removeEventListener("dblclick", selectLabel);
        label.addEventListener("dblclick", selectLabel);

      });

    }

    function selectLabel(e) {

      if (!customRadio.checked) return;

      const label = e.currentTarget;
      const svg = label.closest("svg");

      /* Batch restriction */
      if (PrintState.mode === "batch") {
        const pageIndex = Number(svg.dataset.pageIndex);
        if (pageIndex !== 0) return;
      }

      clearSelectedLabel();

      selectedLabel = label;

      label.classList.add("label-selected");
      label.style.cursor = "move";

      label.addEventListener("mousedown", startLabelDrag);

    }
    /* ================= MM SAFE HELPERS ================= */

    function getLabelSizeMM(label) {

      const rect = label.querySelector('rect[data-boundary="true"]');
      if (!rect) return {
        width: 0,
        height: 0
      };

      return {
        width: parseFloat(rect.getAttribute("width")) || 0,
        height: parseFloat(rect.getAttribute("height")) || 0
      };

    }

    function clamp(value, min, max) {
      return Math.max(min, Math.min(max, value));
    }

    function snapMM(value) {
      return Math.round(value); // 1mm precision
    }

    /* ================= SMART ALIGNMENT HELPERS ================= */

    function isFullDesignSelected() {
      return document.querySelector(".full-design-checkbox")?.checked || false;
    }

    function applySmartAlignment(labels) {

      if (isFullDesignSelected()) return;

      if (!Array.isArray(labels) || labels.length === 0) return;

      const pageW = PrintState.pageSizeMM.width;
      const pageH = PrintState.pageSizeMM.height;

      if (!pageW || !pageH) return;

      /* ================= SINGLE LABEL ================= */

      if (labels.length === 1) {

        const label = labels[0];

        const w = Number(label.widthMM) || 0;
        const h = Number(label.heightMM) || 0;

        label.position = label.position || {};

        label.position.x = snapMM((pageW - w) / 2);
        label.position.y = snapMM((pageH - h) / 2);

        return;
      }

      /* ================= MULTIPLE LABELS ================= */

      let cursorX = 0;
      let cursorY = 0;
      let rowHeight = 0;

      for (let i = 0; i < labels.length; i++) {

        const label = labels[i];

        const w = Number(label.widthMM) || 0;
        const h = Number(label.heightMM) || 0;

        /* check space right */

        if (cursorX + w > pageW) {

          /* move to next row */

          cursorX = 0;
          cursorY += rowHeight;

          rowHeight = 0;

        }

        /* check space down */

        if (cursorY + h > pageH) {

          showPreviewWarning("Not enough page space. Increase page size.");
          return;

        }

        label.position = label.position || {};

        label.position.x = snapMM(cursorX);
        label.position.y = snapMM(cursorY);

        cursorX += w;

        if (h > rowHeight) {
          rowHeight = h;
        }

      }

    }

    function startLabelDrag(e) {

      if (!selectedLabel) return;
      if (!customRadio.checked) return;

      dragTarget = selectedLabel;
      dragSVG = dragTarget.closest("svg");

      const transform = dragTarget.getAttribute("transform") || "translate(0,0)";
      const match = /translate\(([^,]+),([^)]+)\)/.exec(transform);

      startTranslate.x = parseFloat(match?.[1] || 0);
      startTranslate.y = parseFloat(match?.[2] || 0);

      startMouse.x = e.clientX;
      startMouse.y = e.clientY;

      document.addEventListener("mousemove", dragMove);
      document.addEventListener("mouseup", endLabelDrag);

    }

    function dragMove(e) {

      if (!dragTarget) return;

      const dx = e.clientX - startMouse.x;
      const dy = e.clientY - startMouse.y;

      const scaleX = dragSVG.clientWidth / PrintState.pageSizeMM.width;
      const scaleY = dragSVG.clientHeight / PrintState.pageSizeMM.height;

      let newX = startTranslate.x + dx / scaleX;
      let newY = startTranslate.y + dy / scaleY;

      /* SNAP TO 1MM GRID */

      newX = snapMM(newX);
      newY = snapMM(newY);

      /* PAGE BOUNDARY CLAMP */

      const labelSize = getLabelSizeMM(dragTarget);

      const maxX = PrintState.pageSizeMM.width - labelSize.width;
      const maxY = PrintState.pageSizeMM.height - labelSize.height;

      newX = clamp(newX, 0, maxX);
      newY = clamp(newY, 0, maxY);

      dragTarget.setAttribute(
        "transform",
        `translate(${newX}, ${newY})`
      );

    }

    function endLabelDrag() {

      document.removeEventListener("mousemove", dragMove);
      document.removeEventListener("mouseup", endLabelDrag);

      dragTarget = null;

    }

    function clearSelectedLabel() {

      document.querySelectorAll(".print-label")
        .forEach(l => {
          l.classList.remove("label-selected");
          l.removeEventListener("mousedown", startLabelDrag);
        });

      selectedLabel = null;

    }

    /* CLICK ANYWHERE OUTSIDE LABEL → UNSELECT */

    document.addEventListener("mousedown", function(e) {

      if (!selectedLabel) return;

      const insideLabel = e.target.closest(".print-label");

      if (!insideLabel) {
        clearSelectedLabel();
      }

    });

    function applyMasterPositionsToAllPages() {

      if (PrintState.mode !== "batch") return;

      const masterPage = document.querySelector('.batch-page[data-page-index="0"]');
      if (!masterPage) return;

      const masterLabels = masterPage.querySelectorAll(".print-label");

      document.querySelectorAll(".batch-page").forEach(page => {

        const pageIndex = Number(page.dataset.pageIndex);

        if (pageIndex === 0) return;

        const labels = page.querySelectorAll(".print-label");

        labels.forEach((label, i) => {

          const masterTransform = masterLabels[i]?.getAttribute("transform");
          if (!masterTransform) return;

          label.setAttribute("transform", masterTransform);

        });

      });

      showToast("Positions applied to all pages", "success");

    }
    applyBtn.addEventListener("click", applyMasterPositionsToAllPages);

    function showPreviewWarning(message) {

      const preview = document.querySelector(".print-preview");
      if (!preview) return;

      /* remove existing warning */

      preview.querySelectorAll(".print-preview-warning-overlay")
        .forEach(el => el.remove());

      const overlay = document.createElement("div");
      overlay.className = "print-preview-warning-overlay";

      overlay.innerHTML = `
  
                            <div class="print-warning-card">

                              <button class="print-warning-close">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                  <path d="M6 6L18 18M18 6L6 18"/>
                                </svg>
                              </button>

                              <div class="print-warning-header">

                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                                  <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                                  <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                </svg>

                                <div class="print-warning-message">
                                  ${message}
                                </div>

                              </div>

                              <div class="print-warning-progress">
                                <div class="print-warning-progress-bar"></div>
                              </div>

                            </div>
                            `;

      preview.appendChild(overlay);

      const closeBtn = overlay.querySelector(".print-warning-close");

      const remove = () => {
        overlay.remove();
      };

      closeBtn.onclick = remove;

      setTimeout(remove, 2000);

    }



    function renderNormalPreview(selectedLabels) {

      /* ================= VALIDATION ================= */

      if (
        !PrintState.snapshot ||
        !Array.isArray(selectedLabels) ||
        selectedLabels.length === 0
      ) {
        renderEmptyPreview("Please select print content");
        return;
      }

      const pageWidth = Number(PrintState.pageSizeMM.width);
      const pageHeight = Number(PrintState.pageSizeMM.height);

      if (!pageWidth || !pageHeight) {
        renderEmptyPreview("Invalid page size");
        return;
      }

      /* ================= CREATE PAGE ================= */

      const printPreviewGroup = renderPrintPage(pageWidth, pageHeight);
      if (!printPreviewGroup) {
        renderEmptyPreview("Failed to create print page");
        return;
      }

      /* ================= BUILD LABELS ================= */

      selectedLabels.forEach(labelData => {

        if (!labelData || !Array.isArray(labelData.elements)) return;

        // renderPrintLabelFromJSON(labelData, printPreviewGroup);

        renderPrintLabelFromJSON(
          labelData,
          printPreviewGroup, {},
          PrintState.borderSettings
        );

      });


      /* ENABLE DRAG AFTER LABELS CREATED */
      enablePreviewDragging();

    }

    function collectAssignedNodeIdsFromNormalPreview() {

      if (PrintState.mode !== "normal") return;

      PrintState.assignedNodeIds = [];

      const page = document.getElementById("printPreviewSvg");
      if (!page) return;

      const qrGroups = page.querySelectorAll('g[data-type="qr"]');

      qrGroups.forEach(qrEl => {
        const value = String(qrEl.dataset.value || "").trim();
        const match = /\/([A-F0-9]{16,})/.exec(value);

        if (match && match[1]) {
          const nodeId = match[1];
          if (!PrintState.assignedNodeIds.includes(nodeId)) {
            PrintState.assignedNodeIds.push(nodeId);
          }
        }
      });
    }
    async function buildBatchPages(selectedLabels, countLimit) {

      PrintState.generatedPages = [];
      PrintState.assignedNodeIds = [];

      if (!currentModelName?.trim()) {
        showToast("Model name missing", "error");
        return false;
      }

      let data;

      try {
        const response = await fetch(
          `/index.php/items/getNodeId?modelname=${encodeURIComponent(currentModelName)}`
        );
        if (!response.ok) throw new Error();
        data = await response.json();
      } catch {
        showToast("Failed to load node IDs", "error");
        return false;
      }

      if (data?.status !== "success" || !Array.isArray(data.node_ids)) {
        showToast("No available node IDs", "error");
        return false;
      }

      const availableNodeIds = data.node_ids.filter(Boolean);

      if (countLimit > availableNodeIds.length) {
        showToast("Count limit exceeds available node IDs", "error");
        return false;
      }

      /* ===== READ MODAL COUNT + QR FIELDS ===== */

      const countFields = [];

      document.querySelectorAll(".modal-field-row").forEach(row => {

        const countCheckbox = row.querySelector(".count-step-checkbox");
        const qrCheckbox = row.querySelector(".qr-include-checkbox");

        if (!countCheckbox?.checked) return;

        const fieldName = row.querySelector(".modal-field-name")?.value?.trim();
        const baseValue = Number(row.querySelector(".modal-field-value")?.value);
        const stepValue = Number(row.querySelector(".modal-field-counting")?.value);

        if (!fieldName || isNaN(baseValue) || isNaN(stepValue)) return;

        countFields.push({
          fieldName,
          baseValue,
          stepValue,
          includeInQR: qrCheckbox?.checked || false
        });
      });

      /* ===== BUILD PAGES ===== */

      for (let pageIndex = 0; pageIndex < countLimit; pageIndex++) {

        const nodeId = availableNodeIds[pageIndex];

        const updatedLabels = JSON.parse(JSON.stringify(selectedLabels));

        /* APPLY SMART ALIGNMENT TO MASTER PAGE */
        if (!isFullDesignSelected()) {
          applySmartAlignment(updatedLabels);
        }

        updatedLabels.forEach(label => {

          label.elements?.forEach(el => {

            /* ---------- TEXT UPDATE (KEEP AS IS) ---------- */

            if (el.type === "text") {

              countFields.forEach(field => {

                if (el.text.includes(field.fieldName)) {

                  const newValue =
                    field.baseValue + (pageIndex * field.stepValue);

                  el.text = `${field.fieldName}: ${newValue}`;
                }
              });
            }

            /* ---------- QR UPDATE (SAFE FIX) ---------- */

            if (el.type === "qr") {

              let qrValue = el.value;

              /* Replace existing node_id if present */
              const nodeRegex = /\/([A-F0-9]{16,})/;
              // const nodeRegex = /\/([^/]+)/;

              if (nodeRegex.test(qrValue)) {
                qrValue = qrValue.replace(nodeRegex, `/${nodeId}`);
              }

              /*  Replace counting fields ONLY if:
                     - QR checkbox checked
                     - field exists inside QR
              */

              countFields.forEach(field => {

                if (!field.includeInQR) return;

                if (qrValue.includes(field.fieldName)) {

                  const newValue =
                    field.baseValue + (pageIndex * field.stepValue);

                  const regex = new RegExp(
                    `${field.fieldName}:[^/]+`,
                    "g"
                  );

                  qrValue = qrValue.replace(
                    regex,
                    `${field.fieldName}:${newValue}`
                  );
                }
              });

              el.value = qrValue;
            }

          });

        });

        // PrintState.assignedNodeIds.push(nodeId);
        if (!PrintState.assignedNodeIds.includes(nodeId)) {
          PrintState.assignedNodeIds.push(nodeId);
        }

        PrintState.generatedPages.push({
          index: pageIndex,
          labels: updatedLabels
        });
      }

      showToast("Batch pages generated", "success");
      return true;
    }

    function renderBatchPreview() {

      const container = document.getElementById("previewContainer");
      container.innerHTML = "";

      if (!PrintState.generatedPages?.length) {
        renderEmptyPreview("Click Apply to generate batch preview");
        return;
      }

      const widthMM = PrintState.pageSizeMM.width;
      const heightMM = PrintState.pageSizeMM.height;
      const SVG_NS = "http://www.w3.org/2000/svg";

      PrintState.generatedPages.forEach((page, pageIndex) => {

        const svg = document.createElementNS(SVG_NS, "svg");

        svg.setAttribute("width", widthMM + "mm");
        svg.setAttribute("height", heightMM + "mm");
        svg.setAttribute("viewBox", `0 0 ${widthMM} ${heightMM}`);
        svg.classList.add("batch-page");
        svg.dataset.pageIndex = pageIndex;

        svg.style.background = "#ffffff";
        svg.style.boxShadow = "0 6px 16px rgba(0,0,0,0.2)";
        svg.style.display = "block";

        container.appendChild(svg);

        const group = document.createElementNS(SVG_NS, "g");
        svg.appendChild(group);

        const rect = document.createElementNS(SVG_NS, "rect");
        rect.setAttribute("x", 0);
        rect.setAttribute("y", 0);
        rect.setAttribute("width", widthMM);
        rect.setAttribute("height", heightMM);
        rect.setAttribute("fill", "#fff");
        rect.setAttribute("stroke", "#111827");
        rect.setAttribute("stroke-width", 0.4);
        rect.setAttribute("vector-effect", "non-scaling-stroke");

        group.appendChild(rect);

        page.labels.forEach(label => {
          // renderPrintLabelFromJSON(label, group);
          renderPrintLabelFromJSON(
            label,
            group, {},
            PrintState.borderSettings
          );
        });

      });
      /* ENABLE DRAG AFTER ALL LABELS EXIST */
      enablePreviewDragging();
    }

    async function savePrintSnapshot() {

      try {

        let firstPage;

        if (PrintState.mode === "batch") {
          firstPage = document.querySelector('.batch-page[data-page-index="0"]');
        } else {
          firstPage = document.getElementById("printPreviewSvg");
        }

        if (!firstPage) {
          console.warn("Preview page not found");
          return;
        }

        const labels = [];

        const labelEls = firstPage.querySelectorAll(".print-label");

        labelEls.forEach(labelEl => {

          const labelNumber = Number(labelEl.dataset.labelNumber);

          const transform = labelEl.getAttribute("transform") || "translate(0,0)";
          const match = /translate\(([^,]+),([^)]+)\)/.exec(transform);

          const posX = Math.round(parseFloat(match?.[1] || 0));
          const posY = Math.round(parseFloat(match?.[2] || 0));

          const boundary = labelEl.querySelector('rect[data-boundary="true"]');

          const widthMM = Number(boundary?.getAttribute("width")) || 0;
          const heightMM = Number(boundary?.getAttribute("height")) || 0;

          const elements = [];

          labelEl.querySelectorAll("g[data-type]").forEach(el => {

            const type = el.dataset.type;
            const id = el.dataset.id || null;

            const transform = el.getAttribute("transform") || "";
            const match =
              /translate\(([^,]+),([^)]+)\)\s*rotate\(([^)]+)\)\s*scale\(([^,]+),\s*([^)]+)\)/.exec(transform) ||
              /translate\(([^,]+),([^)]+)\)/.exec(transform);

            const tx = parseFloat(match?.[1] || 0);
            const ty = parseFloat(match?.[2] || 0);
            const angle = parseFloat(match?.[3] || 0);
            const scaleX = parseFloat(match?.[4] || 1);
            const scaleY = parseFloat(match?.[5] || 1);

            const elementData = {
              type,
              id,
              transform: {
                x: tx,
                y: ty,
                angle,
                scaleX,
                scaleY
              }
            };

            /* ===== TEXT ===== */

            if (type === "text") {

              const textNode = el.querySelector("text");

              elementData.text = textNode?.textContent || "";
              // Multi-line: reconstruct with \n from tspan children
              const snapTspans = textNode ? textNode.querySelectorAll("tspan") : [];
              if (snapTspans.length > 0) {
                elementData.text = Array.from(snapTspans).map(t => t.textContent).join("\n");
              }
              elementData.fontSize = Number(textNode?.getAttribute("font-size")) || 4;
              elementData.fontFamily = textNode?.getAttribute("font-family") || "Arial";
              elementData.fill = textNode?.getAttribute("fill") || "#000";
              elementData.bold = textNode?.getAttribute("font-weight") === "bold";
              elementData.italic = textNode?.getAttribute("font-style") === "italic";
              elementData.underline = textNode?.getAttribute("text-decoration") === "underline";

            }

            /* ===== QR ===== */

            if (type === "qr") {

              elementData.value = el.dataset.value || "";
              elementData.size = Number(el.querySelector("svg")?.getAttribute("width")) || 20;

              elementData.efEnabled = true;
              elementData.efText = "EF";

            }

            /* ===== BARCODE ===== */

            if (type === "barcode") {

              elementData.value = el.dataset.value || "";
              elementData.format = el.dataset.format || "code128";
              elementData.width = Number(el.dataset.width) || 40;
              elementData.height = Number(el.dataset.height) || 12;

            }

            /* ===== IMAGE ===== */

            if (type === "image") {

              const img = el.querySelector("image");

              elementData.src = img?.getAttribute("href") || "";
              elementData.width = Number(img?.getAttribute("width")) || 20;
              elementData.height = Number(img?.getAttribute("height")) || 20;

            }

            elements.push(elementData);

          });

          labels.push({
            labelNumber,
            widthMM,
            heightMM,
            position: {
              x: posX,
              y: posY
            },
            elements
          });

        });

        const printSnapshot = {

          version: 11,
          timestamp: Date.now(),

          printer: PrintState.selectedPrinter || null,
          copies: Number(document.getElementById("copiesInput")?.value) || 1,

          printPage: {
            widthMM: PrintState.pageSizeMM.width,
            heightMM: PrintState.pageSizeMM.height
          },

          dpi: PrintState.dpi,

          node_ids: [...PrintState.assignedNodeIds],

          labels

        };

        const payload = {
          modelname: currentModelName,
          user_id: currentUserId,
          print_snapshot: JSON.stringify(printSnapshot)
        };

        const res = await fetch("<?= site_url('items/saveFields') ?>", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "<?= csrf_hash() ?>"
          },
          body: JSON.stringify(payload)
        });

        const result = await res.json();

        if (result.status === "success") {
          console.log("Print snapshot saved:", printSnapshot);
        }

      } catch (err) {

        console.error("Snapshot save error:", err);

      }

    }



    async function printPreviewAsImageZPL() {

      if (!PrintState.selectedPrinter) {
        showToast("Select printer", "warning");
        return;
      }

      const connected = await connectQZ();
      if (!connected) return;

      const dpi = PrintState.dpi || 203;

      const dotsPerMM =
        dpi === 203 ? 8 :
        dpi === 300 ? 12 :
        dpi === 600 ? 24 : 8;

      const copies = parseInt(document.getElementById("copiesInput")?.value) || 1;

      try {

        const config = qz.configs.create(PrintState.selectedPrinter, {
          encoding: "UTF-8"
        });

        let pages = [];

        if (PrintState.mode === "batch") {
          pages = Array.from(document.querySelectorAll(".batch-page"));
        } else {
          const singlePage = document.getElementById("printPreviewSvg");
          if (singlePage) pages = [singlePage];
        }

        if (!pages.length) {
          showToast("No pages to print", "warning");
          return;
        }

        const printJobs = [];

        for (let i = 0; i < pages.length; i++) {

          const svgEl = pages[i];

          const widthMM = PrintState.pageSizeMM.width;
          const heightMM = PrintState.pageSizeMM.height;

          const widthDots = Math.round(widthMM * dotsPerMM);
          const heightDots = Math.round(heightMM * dotsPerMM);

          const svgData = new XMLSerializer().serializeToString(svgEl);
          const svgBlob = new Blob([svgData], {
            type: "image/svg+xml;charset=utf-8"
          });
          const url = URL.createObjectURL(svgBlob);

          const img = new Image();
          img.src = url;

          await new Promise((resolve, reject) => {
            img.onload = resolve;
            img.onerror = reject;
          });

          const canvas = document.createElement("canvas");
          canvas.width = widthDots;
          canvas.height = heightDots;

          const ctx = canvas.getContext("2d", {
            alpha: false
          });

          ctx.imageSmoothingEnabled = true;
          ctx.imageSmoothingQuality = "high";

          ctx.fillStyle = "#FFFFFF";
          ctx.fillRect(0, 0, widthDots, heightDots);
          ctx.drawImage(img, 0, 0, widthDots, heightDots);

          URL.revokeObjectURL(url);

          const imageData = ctx.getImageData(0, 0, widthDots, heightDots);
          convertToMonochromeSharp(imageData);

          const zplImage = convertBitmapToZPL(imageData, widthDots, heightDots);

          const zpl =
            "^XA\n" +
            "^CI28\n" +
            "^MMT\n" +
            "^PR2\n" +
            "^MD25\n" +
            `^PW${widthDots}\n` +
            `^LL${heightDots}\n` +
            "^LH0,0\n" +
            zplImage +
            `^PQ${copies}\n` +
            "^XZ";

          printJobs.push({
            type: "raw",
            format: "plain",
            data: zpl
          });

          canvas.width = 0;
          canvas.height = 0;
        }

        await qz.print(config, printJobs);

        showToast(
          PrintState.mode === "batch" ?
          "All batch pages printed" :
          "Printed successfully",
          "success"
        );

        if (PrintState.mode === "normal") {
          collectAssignedNodeIdsFromNormalPreview();
        }

        const nodeResults = buildNodeIdResultJSON();

        if (nodeResults.length) {
          console.table(nodeResults);
        }

        await savePrintSnapshot();

      } catch (err) {
        console.error("Print Error:", err);
        showToast("Printing failed", "error");
      }
    }

    function convertToMonochromeSharp(imageData) {

      const data = imageData.data;

      // Calculate average brightness
      let total = 0;
      for (let i = 0; i < data.length; i += 4) {
        total += 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
      }

      const avg = total / (data.length / 4);
      const threshold = avg * 0.9; // slightly darker bias for thermal printers

      for (let i = 0; i < data.length; i += 4) {

        const gray =
          0.299 * data[i] +
          0.587 * data[i + 1] +
          0.114 * data[i + 2];

        const bw = gray > threshold ? 255 : 0;

        data[i] = data[i + 1] = data[i + 2] = bw;
      }

      return imageData;
    }


    function convertBitmapToZPL(imageData, width, height) {

      const bytesPerRow = Math.ceil(width / 8);
      const totalBytes = bytesPerRow * height;

      let hex = "";
      const data = imageData.data;

      for (let y = 0; y < height; y++) {

        for (let x = 0; x < width; x += 8) {

          let byte = 0;

          for (let bit = 0; bit < 8; bit++) {

            const px = x + bit;

            if (px < width) {

              const index = (y * width + px) * 4;
              const value = data[index];

              if (value === 0) {
                byte |= (1 << (7 - bit));
              }
            }
          }

          hex += byte.toString(16).padStart(2, "0").toUpperCase();
        }
      }

      return `^FO0,0^GFA,${totalBytes},${totalBytes},${bytesPerRow},${hex}\n`;
    }

    document.getElementById("finalPrintBtn")?.addEventListener("click", async () => {

      if (!PrintState.selectedPrinter) {
        showToast("Select printer", "warning");
        return;
      }
      await printPreviewAsImageZPL();

    });
  </script>

  <!--<<<<<<<<<<<<<<<<<<<< export >>>>>>>>>>>>>>>>>>>>>>>> -->
  <script>
    document.getElementById("downloadExport")?.addEventListener("click", async () => {

      const format = document.getElementById("exportFormat")?.value;

      let pages = [];

      if (PrintState.mode === "batch") {
        pages = Array.from(document.querySelectorAll(".batch-page"));
      } else {
        const single = document.getElementById("printPreviewSvg");
        if (single) pages = [single];
      }

      if (!pages.length) {
        showToast("Preview not found", "error");
        return;
      }

      switch (format) {

        case "svg":
          exportAllSVG(pages);
          break;

        case "png":
          await exportAllRaster(pages, "png");
          break;

        case "jpeg":
          await exportAllRaster(pages, "jpeg");
          break;

        case "pdf":
          await exportAllPDF(pages);
          break;

        case "docx":
          await exportAllDOCX(pages);
          break;

        default:
          showToast("Unsupported format", "warning");
      }

    });

    function exportAllSVG(pages) {

      const width = PrintState.pageSizeMM.width;
      const height = PrintState.pageSizeMM.height;

      const totalHeight = height * pages.length;

      const ns = "http://www.w3.org/2000/svg";

      const root = document.createElementNS(ns, "svg");

      root.setAttribute("xmlns", ns);
      root.setAttribute("width", width + "mm");
      root.setAttribute("height", totalHeight + "mm");
      root.setAttribute("viewBox", `0 0 ${width} ${totalHeight}`);

      pages.forEach((page, i) => {

        const clone = page.cloneNode(true);

        const g = document.createElementNS(ns, "g");

        g.setAttribute("transform", `translate(0,${i*height})`);

        while (clone.childNodes.length) {
          g.appendChild(clone.childNodes[0]);
        }

        root.appendChild(g);

      });

      const serializer = new XMLSerializer();
      const source = serializer.serializeToString(root);

      const blob = new Blob([source], {
        type: "image/svg+xml;charset=utf-8"
      });

      downloadFile(URL.createObjectURL(blob), "labels_export.svg");
    }
    async function exportAllRaster(pages, type) {

      const dpi = PrintState.dpi || 203;

      const dotsPerMM =
        dpi === 203 ? 8 :
        dpi === 300 ? 12 :
        dpi === 600 ? 24 : 8;

      const widthMM = PrintState.pageSizeMM.width;
      const heightMM = PrintState.pageSizeMM.height;

      const widthPx = Math.round(widthMM * dotsPerMM);
      const heightPx = Math.round(heightMM * dotsPerMM);

      const canvas = document.createElement("canvas");

      canvas.width = widthPx;
      canvas.height = heightPx * pages.length;

      const ctx = canvas.getContext("2d", {
        alpha: false
      });

      ctx.fillStyle = "#fff";
      ctx.fillRect(0, 0, canvas.width, canvas.height);

      for (let i = 0; i < pages.length; i++) {

        const svgData = new XMLSerializer().serializeToString(pages[i]);

        const blob = new Blob([svgData], {
          type: "image/svg+xml"
        });

        const url = URL.createObjectURL(blob);

        const img = new Image();
        img.src = url;

        await new Promise(r => img.onload = r);

        ctx.drawImage(img, 0, i * heightPx, widthPx, heightPx);

        URL.revokeObjectURL(url);
      }

      const mime = type === "jpeg" ? "image/jpeg" : "image/png";

      const dataURL = canvas.toDataURL(mime, 1.0);

      downloadFile(dataURL, `labels_export.${type}`);
    }

    async function exportAllPDF(pages) {

      if (!window.jspdf || !window.svg2pdf) {
        alert("PDF libraries not loaded.");
        return;
      }

      const {
        jsPDF
      } = window.jspdf;

      const {
        width,
        height
      } = PrintState.pageSizeMM;

      const pdf = new jsPDF({
        orientation: width > height ? "landscape" : "portrait",
        unit: "mm",
        format: [width, height]
      });

      for (let i = 0; i < pages.length; i++) {

        if (i > 0) pdf.addPage();

        await window.svg2pdf.svg2pdf(pages[i], pdf, {
          x: 0,
          y: 0,
          width: width,
          height: height
        });

      }

      pdf.save("labels_export.pdf");
    }



    async function exportAllDOCX(pages) {

      const {
        Document,
        Packer,
        Paragraph,
        ImageRun
      } = window.docx;

      const labelWidthMM = PrintState.pageSizeMM.width;
      const labelHeightMM = PrintState.pageSizeMM.height;

      const marginMM = 5;

      const pageWidthMM = labelWidthMM + marginMM * 2;
      const pageHeightMM = labelHeightMM + marginMM * 2;

      // render DPI for sharp quality
      const renderPxPerMM = PrintState.dpi / 25.4;

      const widthPx = Math.round(labelWidthMM * renderPxPerMM);
      const heightPx = Math.round(labelHeightMM * renderPxPerMM);

      // display size in DOCX must stay at normal document px size
      const docxPxPerMM = 96 / 25.4;
      const displayWidthPx = Math.round(labelWidthMM * docxPxPerMM);
      const displayHeightPx = Math.round(labelHeightMM * docxPxPerMM);

      const pageWidthTwip = Math.round(pageWidthMM * 56.7);
      const pageHeightTwip = Math.round(pageHeightMM * 56.7);
      const marginTwip = Math.round(marginMM * 56.7);

      const children = [];

      for (const page of pages) {

        const svgData = new XMLSerializer().serializeToString(page);

        const blob = new Blob([svgData], {
          type: "image/svg+xml"
        });
        const url = URL.createObjectURL(blob);

        const img = new Image();
        img.src = url;

        await new Promise(r => img.onload = r);

        const canvas = document.createElement("canvas");
        canvas.width = widthPx;
        canvas.height = heightPx;

        const ctx = canvas.getContext("2d");

        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0, 0, widthPx, heightPx);

        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = "high";

        ctx.drawImage(img, 0, 0, widthPx, heightPx);

        const pngBlob = await new Promise(r => canvas.toBlob(r, "image/png"));
        const buffer = await pngBlob.arrayBuffer();

        children.push(
          new Paragraph({
            children: [
              new ImageRun({
                data: buffer,
                transformation: {
                  width: displayWidthPx,
                  height: displayHeightPx
                }
              })
            ],
            pageBreakBefore: children.length > 0,
            spacing: {
              before: 0,
              after: 0
            }
          })
        );

        URL.revokeObjectURL(url);
      }

      const doc = new Document({
        sections: [{
          properties: {
            page: {
              size: {
                width: pageWidthTwip,
                height: pageHeightTwip
              },
              margin: {
                top: marginTwip,
                right: marginTwip,
                bottom: marginTwip,
                left: marginTwip
              }
            }
          },
          children
        }]
      });

      const file = await Packer.toBlob(doc);

      saveAs(file, "labels_export.docx");
    }



    function downloadFile(url, filename) {

      const a = document.createElement("a");

      a.href = url;
      a.download = filename;

      document.body.appendChild(a);
      a.click();

      setTimeout(() => {
        document.body.removeChild(a);
      }, 50);
    }
  </script>

  <!-- <<<<<<<<<<<<< zoom >>>>>>>>>>  -->
  <script>
    (function() {
      const wrapper = document.getElementById("workspaceWrapper");
      const svg = document.getElementById("workspaceSvg");
      const viewport = document.getElementById("workspaceViewport");

      const zoomInBtn = document.getElementById("zoomInBtn");
      const zoomOutBtn = document.getElementById("zoomOutBtn");
      const zoomResetBtn = document.getElementById("zoomResetBtn");
      const zoomPercent = document.getElementById("zoomPercent");
      const zoomIndicator = document.getElementById("zoomIndicator");

      if (!wrapper || !svg || !viewport) {
        console.error("Workspace zoom init failed: required elements not found.");
        return;
      }

      const WORKSPACE_MM = 300;

      const ZOOM = {
        factor: 1, // UI 100% = true neutral, no zoom
        min: 0.5, // 50%
        max: 10, // 1000%
        step: 0.25
      };

      const originalWidthAttr = svg.getAttribute("width") || "300mm";
      const originalHeightAttr = svg.getAttribute("height") || "300mm";

      let baseRenderedWidthPx = 0;
      let baseRenderedHeightPx = 0;
      let basePxPerMmX = 0;
      let basePxPerMmY = 0;

      let isInitialized = false;
      let zoomIndicatorTimer = null;

      let isPanning = false;
      let panStartX = 0;
      let panStartY = 0;
      let startScrollLeft = 0;
      let startScrollTop = 0;

      function clamp(value, min, max) {
        return Math.max(min, Math.min(max, value));
      }

      function round(value) {
        return Math.round(value * 1000) / 1000;
      }

      function updateZoomIndicator() {
        if (zoomPercent) {
          zoomPercent.textContent = Math.round(ZOOM.factor * 100) + "%";
        }

        if (!zoomIndicator) return;

        zoomIndicator.classList.add("show");
        clearTimeout(zoomIndicatorTimer);

        zoomIndicatorTimer = setTimeout(() => {
          zoomIndicator.classList.remove("show");
        }, 1800);
      }

      function restoreNeutralSvgSize() {
        svg.setAttribute("width", originalWidthAttr);
        svg.setAttribute("height", originalHeightAttr);
        svg.style.width = "";
        svg.style.height = "";
      }

      function captureBaseRenderedSize() {
        restoreNeutralSvgSize();

        const rect = svg.getBoundingClientRect();
        if (rect.width <= 20 || rect.height <= 20) return false;

        baseRenderedWidthPx = rect.width;
        baseRenderedHeightPx = rect.height;
        basePxPerMmX = rect.width / WORKSPACE_MM;
        basePxPerMmY = rect.height / WORKSPACE_MM;

        return true;
      }

      function getCurrentPxPerMm() {
        return {
          x: basePxPerMmX * ZOOM.factor,
          y: basePxPerMmY * ZOOM.factor
        };
      }

      function applyViewport() {
        if (!baseRenderedWidthPx || !baseRenderedHeightPx) return;

        svg.setAttribute("viewBox", `0 0 ${WORKSPACE_MM} ${WORKSPACE_MM}`);
        svg.setAttribute("preserveAspectRatio", "xMinYMin meet");

        /* KEEP DESIGN SPACE PURE */
        viewport.setAttribute("transform", "translate(0 0) scale(1)");

        /* TRUE NEUTRAL = ORIGINAL BROWSER-RENDERED 300mm SIZE */
        if (ZOOM.factor === 1) {
          restoreNeutralSvgSize();
        } else {
          svg.setAttribute("width", originalWidthAttr);
          svg.setAttribute("height", originalHeightAttr);

          svg.style.width = (baseRenderedWidthPx * ZOOM.factor) + "px";
          svg.style.height = (baseRenderedHeightPx * ZOOM.factor) + "px";
        }

        updateZoomIndicator();

        requestAnimationFrame(() => {
          if (typeof clearGuides === "function") clearGuides();
          if (typeof renderSelection === "function") renderSelection();
        });
      }

      function getWrapperLocalPoint(clientX, clientY) {
        const rect = wrapper.getBoundingClientRect();
        return {
          x: clientX - rect.left,
          y: clientY - rect.top
        };
      }

      /* SCREEN PX -> WORLD MM */
      function screenToWorld(clientX, clientY) {
        const local = getWrapperLocalPoint(clientX, clientY);
        const pxPerMm = getCurrentPxPerMm();

        return {
          x: (wrapper.scrollLeft + local.x) / pxPerMm.x,
          y: (wrapper.scrollTop + local.y) / pxPerMm.y
        };
      }

      /* WORLD MM -> SURFACE PX */
      function worldToSurface(worldX, worldY) {
        const pxPerMm = getCurrentPxPerMm();

        return {
          x: worldX * pxPerMm.x,
          y: worldY * pxPerMm.y
        };
      }

      function clampScroll() {
        const maxLeft = Math.max(0, wrapper.scrollWidth - wrapper.clientWidth);
        const maxTop = Math.max(0, wrapper.scrollHeight - wrapper.clientHeight);

        wrapper.scrollLeft = clamp(wrapper.scrollLeft, 0, maxLeft);
        wrapper.scrollTop = clamp(wrapper.scrollTop, 0, maxTop);
      }

      function setTopLeftView() {
        wrapper.scrollLeft = 0;
        wrapper.scrollTop = 0;
        clampScroll();
      }

      function set100View() {
        ZOOM.factor = 1;
        applyViewport();
        setTopLeftView();
      }

      function zoomAt(clientX, clientY, direction) {
        const oldFactor = ZOOM.factor;
        const nextFactor = clamp(
          round(oldFactor + (direction > 0 ? ZOOM.step : -ZOOM.step)),
          ZOOM.min,
          ZOOM.max
        );

        if (nextFactor === oldFactor) return;

        const local = getWrapperLocalPoint(clientX, clientY);
        const worldPoint = screenToWorld(clientX, clientY);

        ZOOM.factor = nextFactor;
        applyViewport();

        const nextSurfacePoint = worldToSurface(worldPoint.x, worldPoint.y);

        wrapper.scrollLeft = nextSurfacePoint.x - local.x;
        wrapper.scrollTop = nextSurfacePoint.y - local.y;

        clampScroll();
      }

      function zoomFromCenter(direction) {
        const rect = wrapper.getBoundingClientRect();

        zoomAt(
          rect.left + rect.width / 2,
          rect.top + rect.height / 2,
          direction
        );
      }

      function zoomIn() {
        zoomFromCenter(1);
      }

      function zoomOut() {
        zoomFromCenter(-1);
      }

      function zoomReset() {
        set100View();
      }

      function refreshWorkspaceSurface(keepView = true) {
        if (!isInitialized) return;

        if (!keepView) {
          set100View();
          return;
        }

        const rect = wrapper.getBoundingClientRect();
        const centerWorld = screenToWorld(
          rect.left + wrapper.clientWidth / 2,
          rect.top + wrapper.clientHeight / 2
        );

        applyViewport();

        const centerSurface = worldToSurface(centerWorld.x, centerWorld.y);

        wrapper.scrollLeft = centerSurface.x - (wrapper.clientWidth / 2);
        wrapper.scrollTop = centerSurface.y - (wrapper.clientHeight / 2);

        clampScroll();
      }

      function initializeWorkspaceZoom(attempt = 0) {
        if (isInitialized) return;

        if (!captureBaseRenderedSize()) {
          if (attempt < 40) {
            requestAnimationFrame(() => initializeWorkspaceZoom(attempt + 1));
          }
          return;
        }

        isInitialized = true;

        /* 100% = neutral, no zoom applied */
        set100View();
      }

      zoomInBtn?.addEventListener("click", zoomIn);
      zoomOutBtn?.addEventListener("click", zoomOut);
      zoomResetBtn?.addEventListener("click", zoomReset);

      wrapper.addEventListener("wheel", function(e) {
        if (!e.ctrlKey) return;

        e.preventDefault();
        zoomAt(e.clientX, e.clientY, e.deltaY < 0 ? 1 : -1);
      }, {
        passive: false
      });

      wrapper.addEventListener("mousedown", function(e) {
        const allowPan = e.button === 1 || (e.button === 0 && e.getModifierState("Space"));
        if (!allowPan) return;

        e.preventDefault();
        isPanning = true;
        panStartX = e.clientX;
        panStartY = e.clientY;
        startScrollLeft = wrapper.scrollLeft;
        startScrollTop = wrapper.scrollTop;
        wrapper.classList.add("is-panning");
      });

      window.addEventListener("mousemove", function(e) {
        if (!isPanning) return;

        const dx = e.clientX - panStartX;
        const dy = e.clientY - panStartY;

        wrapper.scrollLeft = startScrollLeft - dx;
        wrapper.scrollTop = startScrollTop - dy;

        clampScroll();
      });

      window.addEventListener("mouseup", function() {
        if (!isPanning) return;
        isPanning = false;
        wrapper.classList.remove("is-panning");
      });

      window.addEventListener("resize", function() {
        if (!isInitialized) return;
        refreshWorkspaceSurface(true);
      });

      if ("ResizeObserver" in window) {
        const resizeObserver = new ResizeObserver(() => {
          if (!isInitialized) return;
          refreshWorkspaceSurface(true);
        });
        resizeObserver.observe(wrapper);
      }

      requestAnimationFrame(() => initializeWorkspaceZoom());
      window.addEventListener("load", () => initializeWorkspaceZoom());

      if (document.fonts?.ready) {
        document.fonts.ready.then(() => {
          if (!isInitialized) initializeWorkspaceZoom();
        }).catch(() => {});
      }

      setTimeout(() => {
        if (!isInitialized) initializeWorkspaceZoom();
      }, 100);

      window.workspaceScreenToWorld = screenToWorld;
      window.workspaceZoom100 = set100View;
      window.refreshWorkspaceSurface = refreshWorkspaceSurface;
      window.workspaceZoomState = function() {
        return {
          workspaceMm: WORKSPACE_MM,
          zoomFactor: ZOOM.factor,
          baseRenderedWidthPx,
          baseRenderedHeightPx,
          basePxPerMmX,
          basePxPerMmY,
          scrollLeft: wrapper.scrollLeft,
          scrollTop: wrapper.scrollTop,
          initialized: isInitialized
        };
      };
    })();
  </script>









</body>

</html>
