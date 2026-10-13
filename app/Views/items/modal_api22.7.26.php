<?php
date_default_timezone_set('Asia/Kolkata');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Multi-Step Form (Product + Command)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <!-- /* =========================================================
   ROOT VARIABLES
========================================================= */ -->


<style>
:root{
    --bg:#F8F5F2;
    --card:#FFFFFF;
    --text:#1B1E2B;
    --border:#E5DDD5;

    --graphite:#232946;
    --graphite2:#2E3350;

    --copper:#C08457;
    --copper2:#D4A373;

    --sage:#6B8F71;
    --sage2:#58725E;

    --light:#F4EFE9;

    --primary:#1E40AF;
    --primary-dark:#1D4ED8;

    --success:#15803D;
    --danger:#DC2626;
}

/* =========================================================
   RESET
========================================================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:var(--bg);
    font-family:"Segoe UI",sans-serif;
    color:var(--text);
    padding:18px;
    overflow-x:hidden;
}

/* Icon spacing for Font Awesome controls */
button .fa-solid,
.flow-arrow .fa-solid,
small .fa-solid{
    pointer-events:none;
}

button .fa-solid{
    font-size:.95em;
}

button{
    gap:8px;
}

/* =========================================================
   LAYOUT
========================================================= */
.main-wrapper{
    max-width:1600px;
    margin:auto;
}

.container{
    background:#fff;
    border:1px solid var(--border);
    border-radius:22px;
    padding:28px;
    margin-bottom:24px;
}

h2{
    text-align:center;
    font-size:30px;
    margin-bottom:32px;
    color:var(--graphite);
}

/* =========================================================
   PAGE HEADER
========================================================= */
.page-header{
    background:#fff;
    border:1px solid var(--border);
    border-radius:22px;
    padding:24px;
    margin-bottom:24px;
}

.page-title{
    font-size:34px;
    font-weight:800;
    color:var(--graphite);
    margin-bottom:24px;
}

/* =========================================================
   PROGRESS FLOW
========================================================= */
.progress-flow{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:14px;
    flex-wrap:wrap;
}

.flow-step{
    display:flex;
    align-items:center;
    gap:10px;
    padding:12px 18px;
    background:#ECE7E1;
    border:1px solid #DDD4CB;
    border-radius:16px;
    cursor:pointer;
    transition:0.3s;
}

.flow-step.active{
    background:linear-gradient(135deg,var(--graphite),var(--graphite2));
    color:#fff;
}

.flow-step.completed{
    background:linear-gradient(135deg,var(--sage),var(--sage2));
    color:#fff;
}

.flow-number{
    width:34px;
    height:34px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#D8CEC5;
    font-weight:700;
}

.flow-step.active .flow-number{
    background:linear-gradient(135deg,var(--copper),var(--copper2));
    color:#fff;
}

.flow-step.completed .flow-number{
    background:#fff;
    color:var(--sage);
}

.flow-text{
    font-size:14px;
    font-weight:700;
}

.flow-arrow{
    color:var(--copper);
    font-size:18px;
    font-weight:bold;
}

.info-card-body{
    background:var(--card);
    border:1px solid var(--border);
    border-radius:22px;
    padding:24px;
    margin-bottom:24px;
    box-shadow:0 8px 24px rgba(35,41,70,.06);
}

.info-card-back{
    border:1px solid var(--border);
    background:#fff;
    padding:8px 14px;
    border-radius:12px;
    cursor:pointer;
    font-weight:800;
    color:var(--graphite);
}

.info-card-back:hover{
    background:var(--light);
    border-color:var(--copper2);
}

.info-heading{
    font-size:28px;
    color:var(--graphite);
    margin-bottom:6px;
    font-weight:800;
}

.info-subtitle{
    color:#6B7280;
    margin-bottom:18px;
}

.info-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:12px;
}

.info-box{
    background:var(--light);
    border:1px solid var(--border);
    border-radius:14px;
    padding:14px;
}

.info-label{
    font-size:12px;
    font-weight:800;
    color:var(--copper);
    text-transform:uppercase;
    margin-bottom:6px;
}

.info-value{
    font-size:15px;
    font-weight:700;
    color:var(--graphite);
    overflow-wrap:anywhere;
}

/* =========================================================
   FORM STEPS
========================================================= */
.form-step{
    display:none;
}

.form-step.active{
    display:block;
    animation:fadeIn .3s ease;
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(10px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* =========================================================
   FORM
========================================================= */
.form-row{
    display:flex;
    gap:24px;
    flex-wrap:wrap;
}

.form-col{
    flex:1 1 320px;
    min-width:280px;
}

.form-group{
    margin-bottom:22px;
}

label{
    display:block;
    margin-bottom:8px;
    font-size:14px;
    font-weight:700;
    color:var(--graphite);
}

label.required::after{
    content:" *";
    color:var(--danger);
}

/* =========================================================
   INPUTS
========================================================= */
input,
select,
textarea{
    width:100%;
    padding:14px 16px;
    border:1px solid var(--border);
    border-radius:14px;
    font-size:14px;
    transition:.3s;
    background:#fff;
}

input:focus,
select:focus,
textarea:focus{
    border-color:var(--copper);
    outline:none;
    box-shadow:0 0 0 3px rgba(192,132,87,.1);
}

input[readonly]{
    background:#f5f5f5;
}

/* Data Flow Direction selector */
.flow-direction-field{
    position:relative;
}

.flow-direction-field select{
    position:absolute;
    inset:0;
    width:1px;
    height:1px;
    opacity:0;
    pointer-events:none;
}

.flow-direction-select{
    position:relative;
}

.flow-direction-trigger{
    width:100%;
    min-height:62px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
    padding:10px 14px;
    border:1px solid var(--border);
    border-radius:14px;
    background:#fff;
    color:var(--graphite);
    cursor:pointer;
    transition:.25s ease;
    text-align:left;
}

.flow-direction-trigger:hover,
.flow-direction-select.open .flow-direction-trigger{
    border-color:var(--copper);
    box-shadow:0 0 0 3px rgba(192,132,87,.1);
}

.flow-direction-select.has-error .flow-direction-trigger{
    border-color:var(--danger);
    box-shadow:0 0 0 3px rgba(220,38,38,.1);
}

.flow-direction-selected{
    min-width:0;
    display:flex;
    align-items:center;
    gap:12px;
}

.flow-direction-icon{
    width:38px;
    height:38px;
    flex:0 0 38px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:12px;
    background:#F4EFE9;
    color:var(--copper);
    font-size:16px;
}

.flow-direction-title{
    display:block;
    font-size:14px;
    font-weight:800;
    color:var(--graphite);
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.flow-direction-hint{
    display:block;
    margin-top:2px;
    font-size:12px;
    font-weight:600;
    color:#6B7280;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.flow-direction-chevron{
    color:var(--copper);
    transition:.25s ease;
}

.flow-direction-select.open .flow-direction-chevron{
    transform:rotate(180deg);
}

.flow-direction-menu{
    position:absolute;
    z-index:50;
    top:calc(100% + 8px);
    left:0;
    right:0;
    display:none;
    padding:8px;
    border:1px solid var(--border);
    border-radius:16px;
    background:#fff;
    box-shadow:0 18px 40px rgba(35,41,70,.16);
}

.flow-direction-select.open .flow-direction-menu{
    display:block;
}

.flow-direction-option{
    width:100%;
    display:flex;
    align-items:center;
    gap:12px;
    padding:10px;
    border:0;
    border-radius:12px;
    background:transparent;
    color:var(--graphite);
    cursor:pointer;
    text-align:left;
    transition:.2s ease;
}

.flow-direction-option:hover,
.flow-direction-option.active{
    background:#F4EFE9;
}

.flow-direction-option.active .flow-direction-icon{
    background:linear-gradient(135deg,var(--graphite),var(--graphite2));
    color:#fff;
}

@media(max-width:480px){
    .flow-direction-title,
    .flow-direction-hint{
        white-space:normal;
    }
}

/* Unified selector skin for API dropdown fields */
.api-select-native,
select[data-api-smart-select-ready="true"]{
    position:absolute !important;
    width:1px !important;
    height:1px !important;
    min-width:1px !important;
    max-width:1px !important;
    min-height:1px !important;
    padding:0 !important;
    margin:0 !important;
    border:0 !important;
    opacity:0 !important;
    pointer-events:none !important;
}

.api-smart-select{
    position:relative;
    width:100%;
}

.api-select-trigger{
    width:100%;
    min-height:54px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:9px 13px;
    border:1px solid var(--border);
    border-radius:14px;
    background:linear-gradient(180deg,#FFFFFF 0%,#FBFAF8 100%);
    color:var(--graphite);
    cursor:pointer;
    text-align:left;
    transition:border-color .2s ease, box-shadow .2s ease, transform .2s ease;
}

.api-select-trigger:hover,
.api-smart-select.open .api-select-trigger{
    border-color:var(--copper);
    box-shadow:0 0 0 3px rgba(192,132,87,.1);
}

.api-smart-select.has-error .api-select-trigger{
    border-color:var(--danger);
    background:#FEF2F2;
    box-shadow:0 0 0 4px rgba(220,38,38,.18);
}

.modal-field-warning,
input.error,
select.error,
textarea.error{
    border:2px solid var(--danger) !important;
    background:#FEF2F2 !important;
    box-shadow:0 0 0 4px rgba(220,38,38,.14) !important;
}

.pink-highlight td{
    background:#FFF1F2 !important;
}

.pink-highlight .sno-column{
    color:#B91C1C !important;
    font-weight:900 !important;
}

.byte-range-invalid{
    border:2px solid var(--danger) !important;
    background:#FEF2F2 !important;
    box-shadow:0 0 0 4px rgba(220,38,38,.14) !important;
}

.byte-range-warning{
    display:none;
    margin-top:6px;
    color:#B91C1C;
    font-size:11px;
    font-weight:800;
    line-height:1.25;
    white-space:normal;
}

.byte-range-warning.is-visible{
    display:block;
}

.api-smart-select.is-disabled .api-select-trigger{
    cursor:not-allowed;
    opacity:.68;
    background:#F5F5F5;
}

.api-select-selected{
    min-width:0;
    display:flex;
    align-items:center;
    gap:11px;
}

.api-select-icon{
    width:36px;
    height:36px;
    flex:0 0 36px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:11px;
    background:#F4EFE9;
    color:var(--copper);
    font-size:15px;
}

.api-select-copy{
    min-width:0;
    display:block;
}

.api-select-title{
    display:block;
    font-size:14px;
    font-weight:800;
    color:var(--graphite);
    line-height:1.2;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.api-select-hint{
    display:block;
    margin-top:2px;
    font-size:12px;
    font-weight:600;
    color:#6B7280;
    line-height:1.2;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.api-select-chevron{
    flex:0 0 auto;
    color:var(--copper);
    font-size:13px;
    transition:transform .2s ease;
}

.api-smart-select.open .api-select-chevron{
    transform:rotate(180deg);
}

.api-select-menu{
    position:absolute;
    z-index:80;
    top:calc(100% + 8px);
    left:0;
    right:0;
    display:none;
    max-height:290px;
    overflow:auto;
    padding:8px;
    border:1px solid var(--border);
    border-radius:16px;
    background:#FFFFFF;
    box-shadow:0 18px 40px rgba(35,41,70,.16);
}

.api-smart-select.open .api-select-menu{
    display:block;
}

.api-select-menu.is-floating{
    position:fixed;
    display:block;
    right:auto;
    z-index:9999;
}

.api-select-option{
    width:100%;
    display:flex;
    align-items:center;
    gap:11px;
    padding:9px 10px;
    border:0;
    border-radius:12px;
    background:transparent;
    color:var(--graphite);
    cursor:pointer;
    text-align:left;
    transition:background .18s ease, color .18s ease;
}

.api-select-option:hover,
.api-select-option.active{
    background:#F4EFE9;
}

.api-select-option.active .api-select-icon{
    background:linear-gradient(135deg,var(--graphite),var(--graphite2));
    color:#FFFFFF;
}

.dynamic-table .api-smart-select,
.config-table .api-smart-select{
    min-width:150px;
    max-width:210px;
}

.dynamic-table .api-select-trigger,
.config-table .api-select-trigger{
    min-height:36px !important;
    height:36px !important;
    padding:5px 8px !important;
    border-radius:9px !important;
    gap:7px;
}

.dynamic-table .api-select-icon,
.config-table .api-select-icon{
    width:24px;
    height:24px;
    flex-basis:24px;
    border-radius:7px;
    font-size:11px;
}

.dynamic-table .api-select-title,
.config-table .api-select-title{
    font-size:12px;
}

.dynamic-table .api-select-hint,
.config-table .api-select-hint{
    display:none;
}

.dynamic-table .api-select-menu,
.config-table .api-select-menu{
    min-width:220px;
}

.dynamic-table input,
.dynamic-table textarea,
.compact-input,
.compact-select{
    border-color:var(--border) !important;
    background:#FFFFFF !important;
    color:var(--graphite) !important;
    transition:border-color .2s ease, box-shadow .2s ease !important;
}

.dynamic-table input:focus,
.dynamic-table textarea:focus,
.compact-input:focus,
.compact-select:focus{
    border-color:var(--copper) !important;
    box-shadow:0 0 0 3px rgba(192,132,87,.1) !important;
    outline:none !important;
}

@media(max-width:768px){
    .api-select-trigger{
        min-height:48px;
    }

    .api-select-title,
    .api-select-hint{
        white-space:normal;
    }
}

@media(max-width:480px){
    .dynamic-table .api-smart-select,
    .config-table .api-smart-select{
        min-width:132px;
        max-width:170px;
    }

    .dynamic-table .api-select-trigger,
    .config-table .api-select-trigger{
        min-height:34px !important;
        height:34px !important;
    }

    .api-select-menu{
        max-height:240px;
    }
}

/* =========================================================
   BUTTONS
========================================================= */
.btn,
.add-row-btn,
.save-btn,
.cancel-btn,
.frame-tab,
.next-btn,
.back-btn{
    border:none;
    border-radius:14px;
    cursor:pointer;
    font-weight:700;
    transition:.3s;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    white-space:nowrap;
}

/* Main Buttons */
.btn,
.next-btn{
    background:linear-gradient(135deg,var(--graphite),var(--graphite2));
    color:#fff;
    padding:12px 24px;
    min-height:46px;
}

.btn:hover,
.next-btn:hover{
    transform:translateY(-2px);
    opacity:.95;
}

/* Back Button */
.back-btn{
    background:#fff;
    color:var(--graphite);
    border:1px solid var(--border);
    padding:12px 24px;
    min-height:46px;
}

.back-btn:hover{
    background:var(--light);
    border-color:var(--copper2);
}

/* Add Row */
.add-row-btn{
    background:linear-gradient(135deg,var(--sage),var(--sage2));
    color:#fff;
    padding:12px 24px;
}

.add-row-btn:hover{
    transform:translateY(-2px);
}

/* Save */
.save-btn{
    background:linear-gradient(135deg,var(--copper),var(--copper2));
    color:#fff;
    padding:12px 24px;
    min-width:140px;
    min-height:46px;
    box-shadow:0 6px 16px rgba(192,132,87,.28);
}

.save-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 24px rgba(192,132,87,.35);
}

/* Cancel */
.cancel-btn{
    min-width:140px;
    min-height:46px;
    padding:12px 24px;
    border:1px solid var(--border);
    background:#fff;
    color:var(--text);
    box-shadow:0 2px 6px rgba(43,45,66,.04);
}

.cancel-btn:hover{
    background:var(--light);
    border-color:var(--copper2);
    transform:translateY(-1px);
}

/* =========================================================
   STEP ACTIONS
========================================================= */
.step-actions{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:14px;
    margin-top:30px;
    flex-wrap:wrap;
}

/* =========================================================
   TABLE
========================================================= */
.table-container{
    margin-top:32px;
    border:1px solid var(--border);
    border-radius:22px;
    overflow:hidden;
}

.table-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 20px;
    background:linear-gradient(135deg,var(--graphite),var(--graphite2));
    flex-wrap:wrap;
    gap:15px;
}

.table-header h3{
    color:#fff;
    margin:0;
}

.table-responsive{
    overflow:auto;
}

.dynamic-table{
    width:100%;
    min-width:2200px;
    border-collapse:collapse;
}

.dynamic-table th{
    background:var(--light);
    padding:14px 10px;
    border:1px solid var(--border);
    font-size:12px;
    font-weight:700;
    text-align:center;
    position:sticky;
    top:0;
    z-index:2;
}

.dynamic-table th:first-child,
.dynamic-table td:first-child{
    position:sticky;
    left:0;
    z-index:8;
    min-width:66px;
    max-width:66px;
    text-align:center;
    background:#FFF7ED;
    color:#1B1E2B;
    font-weight:900;
    box-shadow:4px 0 10px rgba(35,41,70,.1);
}

.dynamic-table th:first-child{
    z-index:18;
    background:#EDE7DF;
}

.dynamic-table tbody tr:nth-child(even) td:first-child{
    background:#FFF3E6;
}

.dynamic-table tbody tr:hover td:first-child,
.dynamic-table tbody tr.pink-highlight td:first-child{
    background:#FEE2E2 !important;
    color:#991B1B !important;
}

.dynamic-table td{
    border:1px solid #EEE5DC;
    padding:8px;
    vertical-align:top;
}

.dynamic-table td.action-buttons{
    overflow:visible !important;
    z-index:140 !important;
}

.dynamic-table tbody tr:hover td.action-buttons{
    z-index:180 !important;
}

.dynamic-table input,
.dynamic-table select{
    width:100%;
    padding:8px;
    font-size:12px;
    border-radius:8px;
}

.dynamic-table td:last-child{
    min-width:110px;
    text-align:center;
    white-space:nowrap;
}

/* =========================================================
   ACTION BUTTONS
========================================================= */
.action-buttons{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    flex-wrap:nowrap;
}

.add-btn,
.remove-btn{
    width:32px;
    height:32px;
    border:none;
    border-radius:8px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    font-size:18px;
    font-weight:bold;
    transition:.2s;
    flex-shrink:0;
}

/* Add */
.add-btn{
    background:#15803d;
    color:#fff;
}

.add-btn:hover{
    background:#047857;
    transform:scale(1.05);
}

/* Remove */
.remove-btn{
    background:#dc2626;
    color:#fff;
}

.remove-btn:hover{
    background:#b91c1c;
    transform:scale(1.05);
}

/* =========================================================
   COMMAND PROGRESS
========================================================= */
.command-progress-container{
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:10px;
}

.progress-circles{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    justify-content:center;
}

.step-box{
    width:35px;
    height:35px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:4px;
    border:2px solid #ccc;
    font-weight:bold;
    cursor:pointer;
    background:#fff;
    color:#333;
    transition:.3s;
}

.step-box:hover{
    transform:scale(1.1);
    box-shadow:0 2px 8px rgba(0,0,0,.2);
}

.command-frame-title{
    font-size:14px;
    font-weight:bold;
    margin-top:5px;
    color:var(--graphite);
}

/* =========================================================
   FRAME TABS
========================================================= */
.frame-navigation-tabs{
    display:flex;
    justify-content:center;
    gap:20px;
    margin:20px 0;
    flex-wrap:wrap;
}

.frame-tab{
    background:#E7E5E4;
    color:#333;
    padding:12px 20px;
}

.frame-tab.active-tab{
    background:linear-gradient(135deg,var(--graphite),var(--graphite2));
    color:#fff;
}

/* =========================================================
   ERRORS
========================================================= */
.step-error{
    display:none;
    padding:12px;
    margin-bottom:20px;
    border-radius:12px;
    background:#FEE2E2;
    color:#B91C1C;
    border:1px solid #FECACA;
}

input.error,
select.error{
    border:2px solid #DC2626 !important;
    background:#FEF2F2 !important;
}

/* =========================================================
   MODAL
========================================================= */
.modal-overlay{
    position:fixed;
    inset:0;
    background:rgba(27,30,43,.55);
    backdrop-filter:blur(5px);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:9999;
    padding:20px;
    animation:fadeIn .25s ease;
}

.modal-box{
    width:100%;
    max-width:98vw;
    height:94vh;
    background:#fff;
    border-radius:22px;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    border:1px solid var(--border);
    box-shadow:
        0 10px 30px rgba(43,45,66,.10),
        0 20px 60px rgba(43,45,66,.12);
}

.modal-box h3{
    margin:0;
    padding:22px 28px;
    font-size:24px;
    font-weight:700;
    color:var(--text);
    background:linear-gradient(to right,#F8F5F2,#FFFFFF);
    border-bottom:1px solid var(--border);
    flex-shrink:0;
}

.table-wrapper{
    flex:1;
    overflow:auto;
    background:#fff;
    min-height:0;
}

/* =========================================================
   CONFIG TABLE
========================================================= */
.config-table{
    width:max-content;
    min-width:100%;
    border-collapse:separate;
    border-spacing:0;
    font-size:13px;
    color:var(--text);
}

.config-table thead th{
    position:sticky;
    top:0;
    z-index:10;
    background:#2B2D42;
    color:#fff;
    padding:14px 12px;
    font-size:12px;
    font-weight:600;
    white-space:nowrap;
    border-bottom:1px solid #3A3F58;
}

.config-table td{
    padding:10px 12px;
    border-bottom:1px solid var(--border);
    border-right:1px solid #F1ECE7;
    white-space:nowrap;
    background:#fff;
}

.config-table td:first-child,
.config-table th:first-child{
    border-left:1px solid var(--border);
}

.config-table tbody tr:nth-child(even) td{
    background:#FCFAF8;
}

.config-table tbody tr:hover td{
    background:rgba(192,132,87,.08);
}

/* Sticky Action Column */
.config-table th:last-child,
.config-table td:last-child{
    position:sticky;
    right:0;
    z-index:20;
    min-width:140px;
    background:#fff;
    border-left:1px solid var(--border);
    box-shadow:-4px 0 8px rgba(43,45,66,.06);
}

.config-table thead th:last-child{
    background:#3A3F58;
}

.config-table tbody tr:nth-child(even) td:last-child{
    background:#FCFAF8;
}

/* Inputs */
.config-table input,
.config-table select,
.config-table textarea{
    width:100%;
    min-width:110px;
    padding:9px 10px;
    border:1px solid var(--border);
    border-radius:10px;
    background:#fff;
    color:var(--text);
    font-size:13px;
}

.config-table input:focus,
.config-table select:focus,
.config-table textarea:focus{
    border-color:var(--copper);
    box-shadow:0 0 0 4px rgba(192,132,87,.18);
}

.config-table textarea{
    resize:vertical;
    min-height:42px;
}

.config-table input[type="checkbox"]{
    width:18px;
    height:18px;
    accent-color:var(--sage);
}

/* =========================================================
   MODAL ACTIONS
========================================================= */
.modal-actions{
    display:flex;
    justify-content:flex-end;
    align-items:center;
    gap:14px;
    padding:18px 24px;
    background:#F8F5F2;
    border-top:1px solid var(--border);
    flex-shrink:0;
}

/* =========================================================
   SCROLLBAR
========================================================= */
.table-wrapper::-webkit-scrollbar{
    width:12px;
    height:12px;
}

.table-wrapper::-webkit-scrollbar-track{
    background:#F3EEE9;
    border-radius:20px;
}

.table-wrapper::-webkit-scrollbar-thumb{
    background:linear-gradient(to bottom,#C08457,#D4A373);
    border-radius:20px;
    border:2px solid #F3EEE9;
}

/* =========================================================
   FORM NAVIGATION
========================================================= */
.form-navigation{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:18px;
    margin-top:38px;
    padding-top:24px;
    border-top:1px solid #E5DDD5;
    flex-wrap:wrap;
}

/* Navigation Buttons */
.form-navigation .btn{
    min-width:140px;
    height:48px;
    padding:0 24px;
    border:none;
    border-radius:14px;
    cursor:pointer;
    font-size:14px;
    font-weight:700;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    transition:all .25s ease;
    white-space:nowrap;
}

/* First Button (Back) */
.form-navigation .btn:first-child{
    background:#FFFFFF;
    color:#1B1E2B;
    border:1px solid #E5DDD5;
    box-shadow:0 2px 6px rgba(43,45,66,.04);
}

.form-navigation .btn:first-child:hover{
    background:#F8F5F2;
    border-color:#D4A373;
    transform:translateY(-1px);
}

/* Second Button (Next) */
.form-navigation .btn:last-child{
    background:linear-gradient(135deg,#232946,#2E3350);
    color:#FFFFFF;
    box-shadow:0 6px 16px rgba(35,41,70,.18);
}

.form-navigation .btn:last-child:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 24px rgba(35,41,70,.24);
}


/* =========================================================
   FIX FORM NAVIGATION PROPERLY
========================================================= */

.form-navigation{
    display:flex !important;
    justify-content:flex-start !important;
    align-items:center !important;
    gap:16px !important;
    margin-top:28px !important;
    margin-bottom:10px !important;
    flex-direction:row !important;
    flex-wrap:wrap !important;
}

/* BACK + NEXT BUTTONS */
.form-navigation .btn{
    min-width:120px !important;
    height:48px !important;
    padding:0 24px !important;
    border-radius:14px !important;
    font-size:15px !important;
    font-weight:700 !important;

    display:inline-flex !important;
    align-items:center !important;
    justify-content:center !important;

    white-space:nowrap !important;

    transition:all .25s ease !important;
}

/* BACK BUTTON */
.form-navigation .btn:first-child{
    background:#232946 !important;
    color:#fff !important;
}

/* NEXT BUTTON */
.form-navigation .btn:last-child{
    background:#2E3350 !important;
    color:#fff !important;
}

/* HOVER */
.form-navigation .btn:hover{
    transform:translateY(-2px);
    opacity:.95;
}

/* MOBILE FIX */
@media (max-width:768px){

    .form-navigation{
        flex-direction:row !important;
        justify-content:center !important;
        gap:12px !important;
    }

    .form-navigation .btn{
        flex:1;
        min-width:130px !important;
    }
}
/* Mobile */
@media (max-width:768px){

    .form-navigation{
        flex-direction:column;
        align-items:stretch;
        gap:14px;
    }

    .form-navigation .btn{
        width:100%;
    }
}

/* =========================================================
   RESPONSIVE
========================================================= */
@media (max-width:992px){

    .container{
        padding:22px;
    }

    .page-title{
        font-size:28px;
    }

    h2{
        font-size:24px;
    }

    .modal-box{
        height:96vh;
        border-radius:18px;
    }

    .modal-box h3{
        font-size:20px;
        padding:18px 20px;
    }

    .config-table{
        font-size:12px;
    }

    .config-table th{
        padding:12px 10px;
        font-size:11px;
    }

    .config-table td{
        padding:8px 10px;
    }

    .config-table input,
    .config-table select,
    .config-table textarea{
        min-width:95px;
        font-size:12px;
    }
}

@media (max-width:768px){

    body{
        padding:10px;
    }

    .container{
        padding:18px;
        border-radius:18px;
    }

    .page-header{
        padding:18px;
    }

    .page-title{
        font-size:24px;
    }

    .form-row{
        gap:16px;
    }

    .modal-overlay{
        padding:10px;
    }

    .modal-box{
        height:98vh;
        border-radius:14px;
    }

    .modal-actions,
    .step-actions{
        flex-direction:column;
        align-items:stretch;
    }

    .save-btn,
    .cancel-btn,
    .btn,
    .next-btn,
    .back-btn{
        width:100%;
    }

    .flow-step{
        width:100%;
        justify-content:center;
    }
}
.dynamic-table th:first-child,
.dynamic-table td:first-child {
    position: sticky !important;
    left: 0 !important;
    z-index: 40 !important;
    min-width: 66px !important;
    max-width: 66px !important;
    text-align: center !important;
    background: #FFF7ED !important;
    color: #1B1E2B !important;
    font-weight: 900 !important;
    box-shadow: 4px 0 10px rgba(35,41,70,.12) !important;
}

.dynamic-table th:first-child {
    z-index: 80 !important;
    background: #EDE7DF !important;
}

.dynamic-table tbody tr:nth-child(even) td:first-child {
    background: #FFF3E6 !important;
}

.dynamic-table tbody tr:hover td:first-child,
.dynamic-table tbody tr.pink-highlight td:first-child {
    background: #FEE2E2 !important;
    color: #991B1B !important;
}

.dynamic-table th:last-child,
.dynamic-table td:last-child {
    position: sticky !important;
    right: 0 !important;
    z-index: 55 !important;
    width: 236px !important;
    min-width: 236px !important;
    max-width: 236px !important;
    background: #fff !important;
    border-left: 1px solid var(--border) !important;
    box-shadow: -5px 0 12px rgba(35,41,70,.1) !important;
}

.dynamic-table th:last-child {
    z-index: 90 !important;
    background: #EDE7DF !important;
}

.dynamic-table td.action-buttons {
    display: grid !important;
    grid-template-columns: repeat(6, 28px) !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 6px !important;
    width: 236px !important;
    min-width: 236px !important;
    max-width: 236px !important;
    padding: 8px 6px !important;
    overflow: visible !important;
}
</style>

<style>
/* Ensure progress trackers are scrollable and responsive */
#command-progress-tracker, #response-progress-tracker, #error-progress-tracker {
    display:flex;
    gap:8px;
    align-items:center;
    overflow-x:auto;
    padding:6px 8px;
    -webkit-overflow-scrolling:touch;
}
.step-box { flex: 0 0 auto; }
.frame-mini-nav { gap:12px; flex-wrap:nowrap; }
</style>



<!-- ==================== FINAL FIX PATCH STYLES ==================== -->
<style>
/* Page back button */
.page-back-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    min-height:42px;
    padding:0 18px;
    margin-bottom:14px;
    border-radius:14px;
    border:1px solid var(--border);
    background:#fff;
    color:var(--graphite);
    font-weight:800;
    cursor:pointer;
    box-shadow:0 4px 12px rgba(43,45,66,.06);
}
.page-back-btn:hover{ background:var(--light); border-color:var(--copper2); transform:translateY(-1px); }

/* Two-column compact layout for long form sections */
#step-2 .container,
#step-3 .container,
#step-4 .container{
    display:grid;
    grid-template-columns:repeat(2,minmax(260px,1fr));
    gap:16px 22px;
}
#step-2 .container > h2,
#step-2 .container > .frame-mini-nav,
#step-2 .container > .step-error,
#step-2 .container > .table-container,
#step-2 .container > .form-navigation,
#step-2 .container > .save-btn-container,
#step-3 .container > h2,
#step-3 .container > .frame-mini-nav,
#step-3 .container > .step-error,
#step-3 .container > .table-container,
#step-3 .container > .form-navigation,
#step-3 .container > .save-btn-container,
#step-4 .container > h2,
#step-4 .container > .frame-mini-nav,
#step-4 .container > .step-error,
#step-4 .container > .table-container,
#step-4 .container > .form-navigation,
#step-4 .container > .save-btn-container{
    grid-column:1 / -1;
}
#step-2 .container > .form-row,
#step-3 .container > .form-row,
#step-4 .container > .form-row{
    display:grid;
    grid-template-columns:repeat(2,minmax(220px,1fr));
    gap:16px;
    grid-column:1 / -1;
}

/* Frame mini nav */
.frame-mini-nav{
    display:flex;
    align-items:center;
    justify-content:flex-start;
    gap:16px;
    padding:12px 14px;
    margin:-12px 0 12px;
    border:1px solid var(--border);
    border-radius:18px;
    background:#fff;
    box-shadow:0 8px 20px rgba(43,45,66,.05);
}
.frame-mini-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-left:8px;}
.frame-mini-nav .progress-circles{justify-content:flex-start;}

/* Scroll behaviour for wide tables */
.table-responsive{
    overflow:auto !important;
    max-height:68vh;
    overscroll-behavior:contain;
    -webkit-overflow-scrolling:touch;
}
.dynamic-table th{ position:sticky; top:0; z-index:5; }
.dynamic-table input,
.dynamic-table select,
.config-table input,
.config-table select,
.config-table textarea{
    min-width:82px !important;
    max-width:150px;
    padding:6px 8px !important;
    font-size:12px !important;
}
.dynamic-table td:nth-child(4) select,
.dynamic-table td:nth-child(8) select,
.dynamic-table td:nth-child(13) select,
.dynamic-table td:nth-child(15) input,
.dynamic-table td:nth-child(17) input,
.dynamic-table td:nth-child(22) input,
.dynamic-table td:nth-child(24) input,
.dynamic-table td:nth-child(25) input{
    min-width:130px !important;
    max-width:190px;
}
.dynamic-table td:last-child,
.config-table td:last-child{
    white-space:nowrap;
    text-align:center;
}

.dynamic-table th:last-child,
.dynamic-table td:last-child{
    position:sticky !important;
    right:0 !important;
    z-index:55 !important;
    width:236px !important;
    min-width:236px !important;
    max-width:236px !important;
    background:#fff !important;
    border-left:1px solid var(--border) !important;
    box-shadow:-5px 0 12px rgba(35,41,70,.1) !important;
}

.dynamic-table th:last-child{
    z-index:90 !important;
    background:#EDE7DF !important;
}

.dynamic-table tbody tr:nth-child(even) td:last-child{
    background:#FCFAF8 !important;
}

.dynamic-table tbody tr:hover td:last-child{
    background:#FFF7ED !important;
}
.action-buttons{
    display:grid !important;
    grid-template-columns:repeat(6, 28px) !important;
    justify-content:center !important;
    align-items:center !important;
    gap:6px !important;
    min-width:236px !important;
    max-width:236px !important;
    width:236px !important;
    position:relative !important;
    overflow:visible !important;
    z-index:120 !important;
    padding:8px 6px !important;
}
.add-btn,.remove-btn,
.config-table td:last-child button{
    width:32px !important;
    height:32px !important;
    border:none !important;
    border-radius:9px !important;
    display:inline-flex !important;
    align-items:center !important;
    justify-content:center !important;
    font-size:16px !important;
    font-weight:900 !important;
    cursor:pointer !important;
    transition:.2s ease !important;
    margin:0 2px !important;
}
.add-btn,.config-table td:last-child button:first-child{ background:#15803d !important; color:#fff !important; }
.remove-btn,.config-table td:last-child button:last-child{ background:#dc2626 !important; color:#fff !important; }
.add-btn:hover,.remove-btn:hover,.config-table td:last-child button:hover{ transform:scale(1.05); }

.row-tool-btn,
.table-history-btn{
    position:relative !important;
    overflow:visible !important;
    z-index:1 !important;
    width:32px !important;
    height:32px !important;
    border:none !important;
    border-radius:9px !important;
    display:inline-flex !important;
    align-items:center !important;
    justify-content:center !important;
    font-size:14px !important;
    font-weight:900 !important;
    cursor:pointer !important;
    transition:.2s ease !important;
    margin:0 2px !important;
    color:#fff !important;
}
.duplicate-row-btn{ background:var(--copper) !important; }
.move-up-row-btn,
.move-down-row-btn{ background:var(--graphite) !important; }
.move-position-row-btn{ background:var(--sage2) !important; }
.table-history-btn{ background:#475569 !important; }
.table-history-btn:disabled{
    opacity:.45 !important;
    cursor:not-allowed !important;
    transform:none !important;
}
.row-tool-btn:hover,
.table-history-btn:not(:disabled):hover{ transform:scale(1.05); }

.row-tool-btn::after,
.table-history-btn::after{
    content:attr(data-tooltip);
    position:absolute;
    left:50%;
    top:calc(100% + 9px);
    transform:translateX(-50%) translateY(4px);
    z-index:2147483647;
    min-width:max-content;
    max-width:180px;
    padding:7px 10px;
    border-radius:8px;
    background:#111827;
    color:#fff;
    font-size:12px;
    font-weight:900;
    line-height:1.2;
    letter-spacing:0;
    opacity:0;
    pointer-events:none;
    white-space:nowrap;
    box-shadow:0 10px 22px rgba(17,24,39,.24);
    transition:opacity .16s ease, transform .16s ease;
}

.row-tool-btn::before,
.table-history-btn::before{
    content:"";
    position:absolute;
    left:50%;
    top:calc(100% + 3px);
    transform:translateX(-50%) translateY(4px);
    z-index:2147483647;
    border:6px solid transparent;
    border-bottom-color:#111827;
    opacity:0;
    pointer-events:none;
    transition:opacity .16s ease, transform .16s ease;
}

.row-tool-btn:hover::after,
.row-tool-btn:hover::before,
.row-tool-btn:focus-visible::after,
.row-tool-btn:focus-visible::before,
.table-history-btn:not(:disabled):hover::after,
.table-history-btn:not(:disabled):hover::before,
.table-history-btn:not(:disabled):focus-visible::after,
.table-history-btn:not(:disabled):focus-visible::before{
    opacity:1;
    transform:translateX(-50%) translateY(0);
}

.row-tool-btn:hover,
.row-tool-btn:focus-visible,
.table-history-btn:not(:disabled):hover,
.table-history-btn:not(:disabled):focus-visible{
    z-index:2147483646 !important;
}

.action-buttons .add-btn,
.action-buttons .remove-btn{
    position:relative !important;
    overflow:visible !important;
    z-index:1 !important;
}

.action-buttons .add-btn::after,
.action-buttons .remove-btn::after{
    content:attr(data-tooltip);
    position:absolute;
    left:50%;
    top:calc(100% + 9px);
    transform:translateX(-50%) translateY(4px);
    z-index:2147483647;
    min-width:max-content;
    padding:7px 10px;
    border-radius:8px;
    background:#111827;
    color:#fff;
    font-size:12px;
    font-weight:900;
    line-height:1.2;
    opacity:0;
    pointer-events:none;
    white-space:nowrap;
    box-shadow:0 10px 22px rgba(17,24,39,.24);
    transition:opacity .16s ease, transform .16s ease;
}

.action-buttons .add-btn::before,
.action-buttons .remove-btn::before{
    content:"";
    position:absolute;
    left:50%;
    top:calc(100% + 3px);
    transform:translateX(-50%) translateY(4px);
    z-index:2147483647;
    border:6px solid transparent;
    border-bottom-color:#111827;
    opacity:0;
    pointer-events:none;
    transition:opacity .16s ease, transform .16s ease;
}

.action-buttons .add-btn:hover::after,
.action-buttons .add-btn:hover::before,
.action-buttons .add-btn:focus-visible::after,
.action-buttons .add-btn:focus-visible::before,
.action-buttons .remove-btn:hover::after,
.action-buttons .remove-btn:hover::before,
.action-buttons .remove-btn:focus-visible::after,
.action-buttons .remove-btn:focus-visible::before{
    opacity:1;
    transform:translateX(-50%) translateY(0);
}

.action-buttons .add-btn:hover,
.action-buttons .add-btn:focus-visible,
.action-buttons .remove-btn:hover,
.action-buttons .remove-btn:focus-visible{
    z-index:2147483646 !important;
}

/* Bitwise/modal scroll */
.modal-box{ max-height:96vh; }
.table-wrapper{
    overflow:auto !important;
    overscroll-behavior:contain;
    -webkit-overflow-scrolling:touch;
}
.config-table thead th{ position:sticky; top:0; z-index:20; }

@media(max-width:768px){
    #step-2 .container,
    #step-3 .container,
    #step-4 .container,
    #step-2 .container > .form-row,
    #step-3 .container > .form-row,
    #step-4 .container > .form-row{
        grid-template-columns:1fr;
    }
    .frame-mini-nav{align-items:stretch; flex-direction:column;}
    .frame-mini-actions{width:100%;}
    .frame-mini-actions .back-btn,.frame-mini-actions .next-btn{flex:1; min-width:140px;}
}
</style>

<style>
/* ==================== RESPONSIVE OVERRIDE LAYER ==================== */
html{
    -webkit-text-size-adjust:100%;
}

body{
    min-width:0;
}

.main-wrapper,
.page-header,
.info-card-body,
.container{
    width:100%;
    max-width:100%;
}

.main-wrapper{
    margin:0 auto;
    width:min(100%,1600px);
    padding-inline:clamp(0px,1.4vw,12px);
}

.page-title{
    font-size:clamp(24px,3vw,34px);
    line-height:1.15;
}

h2{
    font-size:clamp(22px,2.4vw,30px);
    line-height:1.2;
}

.info-heading{
    font-size:clamp(22px,2.6vw,28px);
    line-height:1.2;
    overflow-wrap:anywhere;
}

.info-grid{
    grid-template-columns:repeat(auto-fit,minmax(min(180px,100%),1fr));
}

.progress-flow{
    max-width:100%;
}

.form-row,
#step-2 .container > .form-row,
#step-3 .container > .form-row,
#step-4 .container > .form-row{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(min(260px,100%),1fr));
    gap:18px;
}

.form-col{
    min-width:0;
    width:100%;
}

input,
select,
textarea{
    width:100%;
    max-width:100%;
}

#step-2 .container,
#step-3 .container,
#step-4 .container{
    grid-template-columns:repeat(auto-fit,minmax(min(320px,100%),1fr));
}

.table-container,
.table-responsive,
.table-wrapper{
    width:100%;
    max-width:100%;
    min-width:0;
}

.table-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:12px;
}

.table-header > div{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:10px;
}

.table-responsive,
.table-wrapper{
    overflow:auto !important;
    -webkit-overflow-scrolling:touch;
}

.dynamic-table{
    min-width:1600px;
    width:max-content;
}

.config-table{
    min-width:980px;
}

.frame-mini-nav{
    max-width:100%;
    flex-wrap:wrap;
}

#command-progress-tracker,
#response-progress-tracker,
#error-progress-tracker{
    max-width:100%;
}

.frame-mini-actions{
    margin-left:0;
}

.form-navigation,
.save-btn-container,
.modal-actions,
.step-actions{
    max-width:100%;
}

.modal-overlay{
    padding:clamp(8px,2vw,18px);
}

.modal-box{
    width:min(1180px,100%);
    max-width:100%;
}

#api-overlay{
    padding:clamp(10px,3vw,24px) !important;
}

#api-overlay > div{
    width:min(1180px,100%) !important;
    max-height:min(92dvh,920px) !important;
}

#api-overlay h2{
    font-size:clamp(18px,2.6vw,22px) !important;
}

#api-overlay > div > div:first-child{
    flex-wrap:wrap !important;
}

.modal-box h3{
    line-height:1.25;
}

@media(max-width:992px){
    body{
        padding:12px;
    }

    .page-header,
    .info-card-body,
    .container{
        padding:20px;
        border-radius:18px;
    }

    .progress-flow{
        justify-content:flex-start;
        flex-wrap:nowrap;
        overflow-x:auto;
        padding-bottom:6px;
        -webkit-overflow-scrolling:touch;
    }

    .flow-step,
    .flow-arrow{
        flex:0 0 auto;
    }

    .table-responsive{
        max-height:64vh;
    }

    .dynamic-table{
        min-width:1450px;
    }
}

@media(max-width:768px){
    body{
        padding:10px;
    }

    .page-header,
    .info-card-body,
    .container{
        padding:16px;
        border-radius:16px;
    }

    .page-back-btn{
        width:100%;
    }

    .info-grid{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }

    .flow-step{
        width:auto;
        min-width:max-content;
    }

    .frame-mini-nav{
        align-items:stretch;
        flex-direction:column;
        gap:12px;
        padding:12px;
    }

    .frame-mini-actions{
        width:100%;
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
    }

    .frame-mini-actions .back-btn,
    .frame-mini-actions .next-btn{
        width:100%;
        min-width:0 !important;
    }

    #command-progress-tracker,
    #response-progress-tracker,
    #error-progress-tracker{
        width:100%;
        overflow-x:auto;
        padding:4px 2px;
    }

    .table-header{
        align-items:stretch;
    }

    .table-header h3,
    .table-header > div{
        width:100%;
    }

    .table-header button,
    .table-header .btn{
        flex:1 1 140px;
    }

    .dynamic-table{
        min-width:1250px;
    }

    .form-navigation,
    .save-btn-container{
        align-items:stretch !important;
        gap:12px !important;
    }

    .form-navigation .btn,
    .save-btn{
        flex:1 1 140px;
    }

    .modal-box{
        width:100%;
        height:min(96dvh,900px);
        border-radius:14px;
    }

    .modal-actions{
        flex-direction:column;
        align-items:stretch;
    }

    .modal-actions .btn,
    .modal-actions button{
        width:100%;
    }
}

@media(max-width:480px){
    body{
        padding:8px;
    }

    .page-header,
    .info-card-body,
    .container{
        padding:14px;
        border-radius:14px;
        margin-bottom:16px;
    }

    .info-grid{
        grid-template-columns:1fr;
    }

    .frame-mini-actions{
        grid-template-columns:1fr;
    }

    .form-navigation{
        flex-direction:column !important;
    }

    .form-navigation .btn,
    .save-btn{
        width:100% !important;
    }

    .modal-overlay{
        padding:0;
    }

    .modal-box{
        height:100dvh;
        border-radius:0;
    }
}

#step-2 .form-navigation,
#step-3 .form-navigation,
#step-4 .form-navigation {
    display: grid !important;
    grid-template-columns: minmax(120px, 170px) auto minmax(120px, 170px) !important;
    justify-content: space-between !important;
    align-items: center !important;
    gap: 24px !important;
}

#step-2 .form-navigation .save-inline-btn,
#step-3 .form-navigation .save-inline-btn,
#step-4 .form-navigation .save-inline-btn {
    justify-self: center !important;
    min-width: 220px !important;
    height: 52px !important;
    border: 0 !important;
    border-radius: 14px !important;
    background: linear-gradient(135deg, #C08457, #D4A373) !important;
    color: #fff !important;
    font-size: 15px !important;
    font-weight: 800 !important;
    box-shadow: 0 10px 24px rgba(192, 132, 87, .24) !important;
}

@media(max-width:768px){
    #step-2 .form-navigation,
    #step-3 .form-navigation,
    #step-4 .form-navigation {
        grid-template-columns: 1fr !important;
    }

    #step-2 .form-navigation .save-inline-btn,
    #step-3 .form-navigation .save-inline-btn,
    #step-4 .form-navigation .save-inline-btn {
        width: 100% !important;
    }
}

#api-editing-badge.editing-badge {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 12px !important;
    width: fit-content !important;
    max-width: min(100%, 760px) !important;
    margin: 0 auto 22px !important;
    padding: 13px 20px !important;
    border-radius: 10px !important;
    background: linear-gradient(135deg, #B91C1C, #7F1D1D) !important;
    border: 2px solid #FCA5A5 !important;
    color: #FFFFFF !important;
    font-size: 14px !important;
    font-weight: 900 !important;
    line-height: 1.2 !important;
    text-align: center !important;
    text-transform: uppercase !important;
    letter-spacing: 0 !important;
    box-shadow: 0 12px 28px rgba(185, 28, 28, .28) !important;
}

#api-editing-badge.editing-badge::before {
    content: "EDIT MODE";
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 26px !important;
    padding: 0 10px !important;
    border-radius: 7px !important;
    background: #FEF3C7 !important;
    color: #92400E !important;
    font-size: 11px !important;
    font-weight: 900 !important;
    white-space: nowrap !important;
}

.field-desc-inline .arrow-btn {
    width: 34px !important;
    height: 34px !important;
    min-width: 34px !important;
    border: 0 !important;
    border-radius: 8px !important;
    background: linear-gradient(135deg, var(--copper), var(--copper2)) !important;
    color: #FFFFFF !important;
    font-size: 15px !important;
    font-weight: 900 !important;
    line-height: 1 !important;
    display: none !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    box-shadow: 0 8px 18px rgba(192, 132, 87, .24) !important;
    transition: transform .18s ease, box-shadow .18s ease, background .18s ease !important;
}

.field-desc-inline .arrow-btn.is-visible {
    display: inline-flex !important;
}

.field-desc-inline .arrow-btn:hover {
    transform: translateY(-1px) !important;
    background: linear-gradient(135deg, #B87345, var(--copper)) !important;
    box-shadow: 0 10px 22px rgba(192, 132, 87, .32) !important;
}

.field-desc-inline .arrow-btn:focus {
    outline: 3px solid rgba(192, 132, 87, .28) !important;
    outline-offset: 2px !important;
}

@media(max-width:480px){
    #api-editing-badge.editing-badge {
        width: 100% !important;
        flex-direction: column !important;
        gap: 8px !important;
        padding: 12px 14px !important;
    }
}

/* ==================== MOBILE RESPONSIVE FINAL OVERRIDES ==================== */
@media (max-width: 600px) {
    html,
    body {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    body {
        padding: 6px !important;
    }

    .main-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .info-card-body,
    .page-header,
    .container {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 0 10px !important;
        padding: 10px !important;
        border-radius: 10px !important;
        overflow: hidden !important;
    }

    .page-back-btn {
        width: auto !important;
        min-height: 30px !important;
        padding: 0 10px !important;
        border-radius: 8px !important;
        font-size: 10px !important;
        margin-bottom: 8px !important;
    }

    .page-title,
    .info-heading,
    h2 {
        font-size: 16px !important;
        line-height: 1.2 !important;
        margin-bottom: 10px !important;
        overflow-wrap: anywhere !important;
        text-align: left !important;
    }

    .info-subtitle {
        font-size: 10px !important;
        line-height: 1.35 !important;
        margin-bottom: 10px !important;
    }

    .info-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }

    .info-box {
        min-width: 0 !important;
        padding: 8px !important;
        border-radius: 7px !important;
    }

    .info-label {
        font-size: 8px !important;
    }

    .info-value {
        font-size: 10px !important;
        overflow-wrap: anywhere !important;
    }

    .progress-flow {
        width: 100% !important;
        justify-content: flex-start !important;
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        gap: 6px !important;
        padding-bottom: 4px !important;
    }

    .flow-step {
        flex: 0 0 auto !important;
        gap: 6px !important;
        padding: 7px 9px !important;
        border-radius: 8px !important;
    }

    .flow-number {
        width: 22px !important;
        height: 22px !important;
        font-size: 11px !important;
    }

    .flow-text {
        font-size: 10px !important;
    }

    .flow-arrow {
        flex: 0 0 auto !important;
        font-size: 12px !important;
    }

    #api-editing-badge.editing-badge {
        width: 100% !important;
        max-width: 100% !important;
        margin-bottom: 12px !important;
        padding: 10px !important;
        font-size: 11px !important;
        border-radius: 8px !important;
        flex-direction: column !important;
        gap: 6px !important;
    }

    #api-editing-badge.editing-badge::before {
        min-height: 22px !important;
        font-size: 9px !important;
        padding: 0 8px !important;
    }

    #step-1 .form-row,
    #step-2 .container,
    #step-3 .container,
    #step-4 .container,
    #step-2 .container > .form-row,
    #step-3 .container > .form-row,
    #step-4 .container > .form-row {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 10px !important;
    }

    .form-group {
        margin-bottom: 10px !important;
        min-width: 0 !important;
    }

    label {
        font-size: 10px !important;
        margin-bottom: 5px !important;
    }

    input,
    select,
    textarea {
        min-width: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        min-height: 34px !important;
        padding: 7px 9px !important;
        border-radius: 8px !important;
        font-size: 11px !important;
    }

    .table-container {
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
        border-radius: 10px !important;
        margin-top: 12px !important;
    }

    .table-header {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 10px !important;
        padding: 10px !important;
    }

    .table-header h3 {
        width: 100% !important;
        font-size: 13px !important;
        line-height: 1.2 !important;
        margin: 0 !important;
        overflow-wrap: anywhere !important;
    }

    .table-header > div {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }

    .table-header button,
    .add-row-btn {
        width: 100% !important;
        min-width: 0 !important;
        min-height: 36px !important;
        padding: 0 8px !important;
        border-radius: 9px !important;
        font-size: 10px !important;
        white-space: nowrap !important;
    }

    .table-responsive {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        overflow-y: auto !important;
        max-height: 58dvh !important;
        -webkit-overflow-scrolling: touch !important;
    }

    .dynamic-table {
        width: max-content !important;
        min-width: 1180px !important;
        font-size: 10px !important;
    }

    .dynamic-table th,
    .dynamic-table td {
        padding: 6px !important;
    }

    .dynamic-table input,
    .dynamic-table select {
        min-height: 30px !important;
        padding: 5px 7px !important;
        font-size: 10px !important;
        border-radius: 7px !important;
    }

    .field-desc-inline {
        gap: 5px !important;
    }

    .field-desc-inline .arrow-btn {
        width: 30px !important;
        height: 30px !important;
        min-width: 30px !important;
        border-radius: 7px !important;
    }

    .frame-mini-nav {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 10px !important;
        padding: 10px !important;
        margin: 10px 0 !important;
        border-radius: 10px !important;
    }

    #command-progress-tracker,
    #response-progress-tracker,
    #error-progress-tracker {
        width: 100% !important;
        overflow-x: auto !important;
    }

    .frame-mini-actions {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 8px !important;
        margin-left: 0 !important;
    }

    .frame-mini-actions .back-btn,
    .frame-mini-actions .next-btn {
        width: 100% !important;
        min-width: 0 !important;
        min-height: 38px !important;
        font-size: 11px !important;
        border-radius: 10px !important;
    }

    #step-2 .form-navigation,
    #step-3 .form-navigation,
    #step-4 .form-navigation,
    .form-navigation {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 8px !important;
        margin-top: 14px !important;
        padding-top: 14px !important;
    }

    .form-navigation .btn,
    .save-inline-btn,
    .save-btn {
        width: 100% !important;
        min-width: 0 !important;
        min-height: 42px !important;
        height: 42px !important;
        font-size: 12px !important;
        border-radius: 10px !important;
    }

    .modal-overlay {
        padding: 0 !important;
    }

    .modal-box {
        width: 100dvw !important;
        height: 100dvh !important;
        max-height: 100dvh !important;
        border-radius: 0 !important;
    }

    .modal-actions {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 8px !important;
        padding: 10px !important;
    }
}

/* =========================================================
   RESPONSIVE POLISH LAYER
   Scope: layout responsiveness only. Do not change backend IDs,
   form names, routes, JS hooks, or dynamic rendering contracts.
========================================================= */

/* Large desktop: keep the existing visual identity, just cap ultra-wide reading width. */
@media (min-width: 1601px) {
    .main-wrapper {
        max-width: 1600px !important;
    }

    .table-responsive {
        max-width: 100% !important;
    }
}

/* Laptop and compact desktop: preserve desktop layout while preventing edge overflow. */
@media (max-width: 1366px) {
    body {
        overflow-x: hidden !important;
    }

    .main-wrapper,
    .info-card-body,
    .page-header,
    .container {
        max-width: 100% !important;
    }

    .table-container,
    .table-responsive,
    .table-wrapper {
        max-width: 100% !important;
    }

    .dynamic-table,
    .config-table {
        width: max-content !important;
    }
}

/* Tablet: stack dense page sections and keep only table wrappers scrollable. */
@media (max-width: 1024px) {
    body {
        padding: 12px !important;
        overflow-x: hidden !important;
    }

    .main-wrapper {
        width: 100% !important;
        padding-inline: 0 !important;
    }

    .info-card-body,
    .page-header,
    .container {
        width: 100% !important;
        padding: 18px !important;
        border-radius: 16px !important;
    }

    .info-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 12px !important;
    }

    .progress-flow {
        justify-content: flex-start !important;
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
        padding-bottom: 6px !important;
        -webkit-overflow-scrolling: touch !important;
    }

    .flow-step,
    .flow-arrow {
        flex: 0 0 auto !important;
    }

    #step-2 .container,
    #step-3 .container,
    #step-4 .container {
        grid-template-columns: 1fr !important;
    }

    #step-2 .container > .form-row,
    #step-3 .container > .form-row,
    #step-4 .container > .form-row,
    .form-row {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .table-responsive,
    .table-wrapper {
        overflow-x: auto !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        max-width: 100% !important;
    }

    .modal-box {
        width: min(96vw, 1180px) !important;
        max-height: 92dvh !important;
    }

    /* App shell safety: if this page is rendered inside a sidebar layout, avoid page overflow. */
    .sidebar,
    #sidebar,
    .app-sidebar {
        max-width: 280px;
    }

    .topbar,
    .navbar,
    .app-header,
    .page-header {
        flex-wrap: wrap !important;
    }
}

/* Tablet portrait and mobile landscape: forms become single column, buttons remain touch-friendly. */
@media (max-width: 768px) {
    html,
    body {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    .page-title {
        font-size: 24px !important;
    }

    .info-heading,
    h2 {
        font-size: 22px !important;
    }

    .info-grid,
    #step-1 .form-row,
    #step-2 .container > .form-row,
    #step-3 .container > .form-row,
    #step-4 .container > .form-row,
    .form-row {
        grid-template-columns: 1fr !important;
    }

    .form-col,
    .form-group {
        min-width: 0 !important;
        width: 100% !important;
    }

    input,
    select,
    textarea {
        min-height: 40px !important;
        font-size: 13px !important;
    }

    .table-header {
        display: grid !important;
        grid-template-columns: 1fr !important;
        align-items: stretch !important;
    }

    .table-header > div {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        width: 100% !important;
    }

    .table-header button,
    .add-row-btn {
        width: 100% !important;
        min-width: 0 !important;
    }

    .frame-mini-nav {
        display: grid !important;
        grid-template-columns: 1fr !important;
        align-items: stretch !important;
    }

    .frame-mini-actions {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        width: 100% !important;
        margin-left: 0 !important;
    }

    .frame-mini-actions .back-btn,
    .frame-mini-actions .next-btn {
        width: 100% !important;
        min-width: 0 !important;
    }

    #step-2 .form-navigation,
    #step-3 .form-navigation,
    #step-4 .form-navigation,
    .form-navigation {
        grid-template-columns: 1fr !important;
        justify-content: stretch !important;
    }

    .form-navigation .btn,
    .save-inline-btn,
    .save-btn {
        width: 100% !important;
        min-width: 0 !important;
    }

    /* Header behavior: keep controls visible by wrapping/collapsing width instead of overflowing. */
    .topbar input[type="search"],
    .navbar input[type="search"],
    .app-header input[type="search"],
    .search-input {
        max-width: 100% !important;
        flex: 1 1 180px !important;
    }

    /* Sidebar behavior: app shell can toggle visibility; this prevents fixed sidebars from squeezing mobile content. */
    .sidebar,
    #sidebar,
    .app-sidebar {
        position: fixed;
        inset: 0 auto 0 0;
        width: min(82vw, 300px);
        max-width: min(82vw, 300px);
        transform: translateX(-100%);
        z-index: 9998;
    }

    .sidebar.open,
    #sidebar.open,
    .app-sidebar.open,
    body.sidebar-open .sidebar,
    body.sidebar-open #sidebar,
    body.sidebar-open .app-sidebar {
        transform: translateX(0);
    }
}

/* Mobile: fit cards and modals to viewport; keep tables scrollable only inside wrappers. */
@media (max-width: 600px) {
    .page-title,
    .info-heading,
    h2 {
        text-align: left !important;
    }

    .info-card-body,
    .page-header,
    .container {
        box-shadow: 0 8px 22px rgba(35, 41, 70, .06) !important;
    }

    .table-responsive,
    .table-wrapper {
        border-radius: 0 0 10px 10px !important;
        overscroll-behavior-x: contain !important;
    }

    .dynamic-table {
        min-width: 1180px !important;
    }

    .config-table {
        min-width: 980px !important;
    }

    .modal-overlay {
        align-items: stretch !important;
        justify-content: stretch !important;
    }

    .modal-box {
        width: 100dvw !important;
        max-width: 100dvw !important;
        height: 100dvh !important;
        max-height: 100dvh !important;
        border-radius: 0 !important;
        display: flex !important;
        flex-direction: column !important;
    }

    .modal-box h3 {
        flex: 0 0 auto !important;
        font-size: 16px !important;
        padding: 12px !important;
    }

    .modal-box .table-wrapper,
    .modal-box .table-responsive {
        flex: 1 1 auto !important;
        min-height: 0 !important;
    }

    .modal-actions {
        flex: 0 0 auto !important;
    }
}

/* Small mobile: compact typography and spacing for 320px devices. */
@media (max-width: 380px) {
    body {
        padding: 4px !important;
    }

    .info-card-body,
    .page-header,
    .container {
        padding: 8px !important;
        border-radius: 9px !important;
    }

    .page-title {
        font-size: 15px !important;
    }

    .info-heading,
    h2 {
        font-size: 15px !important;
    }

    .info-subtitle,
    small {
        font-size: 9px !important;
    }

    label {
        font-size: 9px !important;
    }

    input,
    select,
    textarea {
        min-height: 34px !important;
        padding: 6px 8px !important;
        font-size: 11px !important;
    }

    .table-header > div {
        grid-template-columns: 1fr !important;
    }

    .flow-step {
        padding: 6px 8px !important;
    }

    .flow-text {
        font-size: 9px !important;
    }
}

/* =========================================================
   RESPONSIVE BUTTON SIZING
   Scope: visual sizing only. Existing button classes, IDs,
   onclick handlers, AJAX hooks, and form behavior are untouched.
========================================================= */

@media (max-width: 1024px) {
    .btn,
    .save-btn,
    .add-row-btn,
    .back-btn,
    .next-btn,
    .cancel-btn,
    .remove-btn,
    .add-btn {
        min-height: 42px !important;
        height: auto !important;
        padding: 9px 16px !important;
        border-radius: 11px !important;
        font-size: 13px !important;
        line-height: 1.2 !important;
    }

    .form-navigation .btn,
    #step-2 .form-navigation .save-inline-btn,
    #step-3 .form-navigation .save-inline-btn,
    #step-4 .form-navigation .save-inline-btn {
        min-width: 140px !important;
        min-height: 44px !important;
    }
}

@media (max-width: 768px) {
    .form-navigation .btn,
    #step-2 .form-navigation .save-inline-btn,
    #step-3 .form-navigation .save-inline-btn,
    #step-4 .form-navigation .save-inline-btn {
        width: 100% !important;
        min-width: 0 !important;
        min-height: 40px !important;
        height: 40px !important;
        padding: 0 12px !important;
        border-radius: 10px !important;
        font-size: 12px !important;
    }

    .frame-mini-actions .back-btn,
    .frame-mini-actions .next-btn {
        min-height: 38px !important;
        height: 38px !important;
        padding: 0 10px !important;
        border-radius: 9px !important;
        font-size: 11px !important;
    }

    .table-header > div {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .table-header button,
    .add-row-btn {
        min-height: 34px !important;
        height: 34px !important;
        padding: 0 8px !important;
        border-radius: 9px !important;
        font-size: 10px !important;
    }

    .action-buttons {
        gap: 5px !important;
        min-width: 64px !important;
    }

    .action-buttons .remove-btn,
    .action-buttons .add-btn,
    .action-buttons .row-tool-btn,
    .remove-btn,
    .add-btn,
    .row-tool-btn {
        width: 28px !important;
        min-width: 28px !important;
        height: 28px !important;
        min-height: 28px !important;
        padding: 0 !important;
        border-radius: 7px !important;
        font-size: 13px !important;
    }

    .field-desc-inline .arrow-btn {
        width: 28px !important;
        min-width: 28px !important;
        height: 28px !important;
        min-height: 28px !important;
        border-radius: 7px !important;
        font-size: 12px !important;
        padding: 0 !important;
    }

    .modal-actions button,
    .modal-actions .btn {
        min-height: 38px !important;
        height: 38px !important;
        padding: 0 12px !important;
        border-radius: 9px !important;
        font-size: 12px !important;
    }
}

@media (max-width: 480px) {
    .form-navigation {
        gap: 7px !important;
    }

    .form-navigation .btn,
    #step-2 .form-navigation .save-inline-btn,
    #step-3 .form-navigation .save-inline-btn,
    #step-4 .form-navigation .save-inline-btn,
    .save-btn {
        min-height: 36px !important;
        height: 36px !important;
        padding: 0 10px !important;
        border-radius: 8px !important;
        font-size: 11px !important;
        box-shadow: 0 5px 12px rgba(35, 41, 70, .14) !important;
    }

    .table-header button,
    .add-row-btn {
        min-height: 32px !important;
        height: 32px !important;
        font-size: 9px !important;
        border-radius: 8px !important;
    }

    .frame-mini-actions .back-btn,
    .frame-mini-actions .next-btn {
        min-height: 34px !important;
        height: 34px !important;
        font-size: 10px !important;
    }
}

@media (max-width: 360px) {
    .form-navigation .btn,
    #step-2 .form-navigation .save-inline-btn,
    #step-3 .form-navigation .save-inline-btn,
    #step-4 .form-navigation .save-inline-btn,
    .save-btn {
        min-height: 34px !important;
        height: 34px !important;
        font-size: 10px !important;
    }

    .table-header > div {
        grid-template-columns: 1fr !important;
    }
}

/* =========================================================
   RESPONSIVE UI REFINEMENT
   Keeps desktop styling intact while making tablet/mobile
   controls compact, premium, and free from page overflow.
========================================================= */
@media (max-width: 1024px) {
    html,
    body {
        overflow-x: hidden !important;
    }

    .main-wrapper,
    .info-card-body,
    .page-header,
    .container,
    .table-container,
    .frame-mini-nav {
        max-width: 100% !important;
    }

    .page-header {
        padding: 14px !important;
    }

    .progress-flow {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 8px !important;
        width: 100% !important;
        overflow: visible !important;
        padding-bottom: 0 !important;
    }

    .flow-arrow {
        display: none !important;
    }

    .flow-step {
        min-width: 0 !important;
        width: 100% !important;
        justify-content: center !important;
        gap: 7px !important;
        padding: 9px 10px !important;
        border-radius: 12px !important;
    }

    .flow-number {
        width: 28px !important;
        height: 28px !important;
        min-width: 28px !important;
        font-size: 12px !important;
    }

    .flow-text {
        font-size: 12px !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    .table-header {
        padding: 14px 16px !important;
        gap: 10px !important;
    }

    .table-header h3 {
        font-size: 20px !important;
        line-height: 1.25 !important;
    }

    .table-header > div {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(118px, 1fr)) !important;
        gap: 10px !important;
    }

    .table-header button,
    .add-row-btn {
        width: 100% !important;
        min-width: 0 !important;
        height: 38px !important;
        min-height: 38px !important;
        padding: 0 12px !important;
        font-size: 12px !important;
        border-radius: 11px !important;
    }

    .dynamic-table th {
        font-size: 12px !important;
        padding: 10px 8px !important;
    }

    .dynamic-table td {
        font-size: 12px !important;
        padding: 8px !important;
    }

    .dynamic-table input,
    .dynamic-table select,
    .compact-input,
    .compact-select {
        min-height: 34px !important;
        height: 34px !important;
        font-size: 12px !important;
        padding: 6px 9px !important;
        border-radius: 9px !important;
    }

    .form-navigation {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(120px, max-content)) !important;
        justify-content: start !important;
        align-items: center !important;
        gap: 12px !important;
    }

    .form-navigation .btn,
    #step-2 .form-navigation .save-inline-btn,
    #step-3 .form-navigation .save-inline-btn,
    #step-4 .form-navigation .save-inline-btn {
        width: auto !important;
        min-width: 124px !important;
        height: 40px !important;
        min-height: 40px !important;
        padding: 0 18px !important;
        font-size: 12px !important;
        border-radius: 11px !important;
    }
}

@media (max-width: 768px) {
    body {
        padding: 8px !important;
    }

    .info-card-body,
    .page-header,
    .container {
        padding: 12px !important;
        border-radius: 13px !important;
        margin-bottom: 12px !important;
    }

    .page-title,
    .info-heading,
    h2 {
        font-size: 18px !important;
        line-height: 1.2 !important;
        margin-bottom: 10px !important;
    }

    .info-subtitle {
        font-size: 11px !important;
        margin-bottom: 10px !important;
    }

    .info-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }

    .info-box {
        padding: 9px !important;
        border-radius: 9px !important;
    }

    .info-label {
        font-size: 8px !important;
    }

    .info-value {
        font-size: 11px !important;
    }

    .progress-flow {
        grid-template-columns: minmax(64px, 1fr) 12px minmax(72px, 1fr) 12px minmax(72px, 1fr) 12px minmax(58px, 1fr) !important;
        align-items: center !important;
        gap: 5px !important;
    }

    .flow-arrow {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 10px !important;
        line-height: 1 !important;
        color: var(--copper) !important;
    }

    .flow-step {
        flex-direction: row !important;
        justify-content: center !important;
        gap: 4px !important;
        padding: 6px 5px !important;
        border-radius: 9px !important;
    }

    .flow-number {
        width: 22px !important;
        height: 22px !important;
        min-width: 22px !important;
        font-size: 10px !important;
    }

    .flow-text {
        width: 100% !important;
        text-align: center !important;
        font-size: 9px !important;
    }

    label {
        font-size: 11px !important;
    }

    input,
    select,
    textarea {
        min-height: 36px !important;
        font-size: 12px !important;
        border-radius: 9px !important;
    }

    .table-header {
        grid-template-columns: 1fr !important;
        padding: 12px !important;
        border-radius: 12px 12px 0 0 !important;
    }

    .table-header h3 {
        font-size: 16px !important;
        text-align: left !important;
    }

    .table-header > div {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }

    .table-header button,
    .add-row-btn {
        height: 34px !important;
        min-height: 34px !important;
        font-size: 10px !important;
        padding: 0 8px !important;
    }

    .table-responsive {
        max-height: 54dvh !important;
        overflow-x: auto !important;
        overflow-y: auto !important;
    }

    .dynamic-table {
        min-width: 1080px !important;
        font-size: 10px !important;
    }

    .dynamic-table th,
    .dynamic-table td {
        padding: 6px !important;
        font-size: 10px !important;
    }

    .dynamic-table input,
    .dynamic-table select,
    .compact-input,
    .compact-select {
        min-height: 30px !important;
        height: 30px !important;
        font-size: 10px !important;
        padding: 4px 7px !important;
        border-radius: 7px !important;
    }

    .frame-mini-nav {
        padding: 10px !important;
        gap: 8px !important;
    }

    .frame-mini-actions {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }

    .frame-mini-actions .back-btn,
    .frame-mini-actions .next-btn {
        height: 34px !important;
        min-height: 34px !important;
        font-size: 10px !important;
        padding: 0 8px !important;
        border-radius: 9px !important;
    }

    #step-1 .form-navigation,
    #step-2 .form-navigation,
    #step-3 .form-navigation,
    #step-4 .form-navigation,
    .form-navigation {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        justify-content: stretch !important;
        gap: 8px !important;
        margin-top: 16px !important;
        padding-top: 14px !important;
    }

    .form-navigation > div:empty {
        display: none !important;
    }

    .form-navigation .btn,
    #step-2 .form-navigation .save-inline-btn,
    #step-3 .form-navigation .save-inline-btn,
    #step-4 .form-navigation .save-inline-btn,
    .save-btn {
        width: 100% !important;
        min-width: 0 !important;
        height: 38px !important;
        min-height: 38px !important;
        padding: 0 8px !important;
        font-size: 10px !important;
        border-radius: 9px !important;
    }

    #step-2 .form-navigation .save-inline-btn,
    #step-3 .form-navigation .save-inline-btn,
    #step-4 .form-navigation .save-inline-btn {
        grid-column: auto !important;
        justify-self: stretch !important;
        width: 100% !important;
    }
}

@media (max-width: 480px) {
    .progress-flow {
        grid-template-columns: minmax(54px, 1fr) 9px minmax(62px, 1fr) 9px minmax(64px, 1fr) 9px minmax(48px, 1fr) !important;
        gap: 3px !important;
    }

    .flow-step {
        flex-direction: row !important;
        justify-content: center !important;
        gap: 3px !important;
        padding: 5px 3px !important;
        min-height: 34px !important;
    }

    .flow-text {
        text-align: center !important;
        font-size: 8px !important;
        line-height: 1 !important;
    }

    .flow-number {
        width: 19px !important;
        height: 19px !important;
        min-width: 19px !important;
        font-size: 9px !important;
    }

    .flow-arrow {
        display: flex !important;
        font-size: 8px !important;
    }

    .info-grid {
        grid-template-columns: 1fr !important;
    }

    .table-header > div {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .dynamic-table {
        min-width: 980px !important;
    }

    #step-1 .form-navigation,
    #step-2 .form-navigation,
    #step-3 .form-navigation,
    #step-4 .form-navigation,
    .form-navigation {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 7px !important;
    }

    .form-navigation .btn,
    #step-2 .form-navigation .save-inline-btn,
    #step-3 .form-navigation .save-inline-btn,
    #step-4 .form-navigation .save-inline-btn,
    .save-btn {
        height: 34px !important;
        min-height: 34px !important;
        padding: 0 6px !important;
        font-size: 9px !important;
        border-radius: 8px !important;
        box-shadow: 0 5px 12px rgba(35, 41, 70, .12) !important;
    }
}

@media (max-width: 360px) {
    .table-header > div,
    .frame-mini-actions {
        grid-template-columns: 1fr !important;
    }

    #step-1 .form-navigation,
    #step-2 .form-navigation,
    #step-3 .form-navigation,
    #step-4 .form-navigation,
    .form-navigation {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 5px !important;
    }

    .form-navigation .btn,
    #step-2 .form-navigation .save-inline-btn,
    #step-3 .form-navigation .save-inline-btn,
    #step-4 .form-navigation .save-inline-btn,
    .save-btn {
        height: 32px !important;
        min-height: 32px !important;
        font-size: 8px !important;
        padding: 0 4px !important;
    }
}

/* Final navigation polish */
#step-1 .form-navigation,
#step-2 .form-navigation,
#step-3 .form-navigation,
#step-4 .form-navigation,
.form-navigation {
    display: flex !important;
    justify-content: flex-start !important;
    align-items: center !important;
    gap: 16px !important;
    grid-template-columns: none !important;
}

.form-navigation > div:empty {
    display: none !important;
}

#step-1 .form-navigation .btn,
#step-2 .form-navigation .btn,
#step-3 .form-navigation .btn,
#step-4 .form-navigation .btn,
.form-navigation .btn {
    flex: 0 0 auto !important;
    width: auto !important;
    min-width: 150px !important;
    height: 48px !important;
    border-radius: 14px !important;
    border: 1px solid var(--graphite) !important;
    background: linear-gradient(135deg, var(--graphite), var(--graphite2)) !important;
    color: #fff !important;
    box-shadow: 0 8px 18px rgba(35,41,70,.16) !important;
}

#step-1 .form-navigation .btn:hover,
#step-2 .form-navigation .btn:hover,
#step-3 .form-navigation .btn:hover,
#step-4 .form-navigation .btn:hover,
.form-navigation .btn:hover {
    transform: translateY(-2px) !important;
    border-color: var(--copper2) !important;
    box-shadow: 0 12px 26px rgba(192,132,87,.24) !important;
    opacity: .98 !important;
}

#step-1 .form-navigation .btn:active,
#step-2 .form-navigation .btn:active,
#step-3 .form-navigation .btn:active,
#step-4 .form-navigation .btn:active,
.form-navigation .btn:active {
    transform: translateY(0) !important;
}

@media (max-width: 640px) {
    #step-1 .form-navigation,
    #step-2 .form-navigation,
    #step-3 .form-navigation,
    #step-4 .form-navigation,
    .form-navigation {
        justify-content: flex-start !important;
    }

    .form-navigation > div:empty {
        display: none !important;
    }

    #step-1 .form-navigation .btn,
    #step-2 .form-navigation .btn,
    #step-3 .form-navigation .btn,
    #step-4 .form-navigation .btn,
    .form-navigation .btn {
        flex: 1 1 0 !important;
        min-width: 0 !important;
        width: auto !important;
    }
}

/* Mobile final navigation: keep Back/Save/Submit usable at 320px widths. */
@media (max-width: 600px) {
    #step-1 .form-navigation,
    #step-2 .form-navigation,
    #step-3 .form-navigation,
    #step-4 .form-navigation,
    .form-navigation {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 10px !important;
        justify-content: stretch !important;
        align-items: stretch !important;
        margin-top: 16px !important;
        padding-top: 14px !important;
        border-top: 1px solid var(--border) !important;
    }

    #step-1 .form-navigation .btn,
    #step-2 .form-navigation .btn,
    #step-3 .form-navigation .btn,
    #step-4 .form-navigation .btn,
    .form-navigation .btn,
    #final-submit-btn {
        width: 100% !important;
        min-width: 0 !important;
        min-height: 44px !important;
        height: 44px !important;
        padding: 0 14px !important;
        border-radius: 12px !important;
        font-size: 13px !important;
        font-weight: 900 !important;
        line-height: 1.1 !important;
        white-space: normal !important;
        box-shadow: 0 8px 18px rgba(35,41,70,.14) !important;
    }

    #step-1 .form-navigation .btn:first-child,
    #step-2 .form-navigation .btn:first-child,
    #step-3 .form-navigation .btn:first-child,
    #step-4 .form-navigation .btn:first-child,
    .form-navigation .btn:first-child {
        background: #FFFFFF !important;
        color: var(--graphite) !important;
        border: 1px solid var(--border) !important;
        box-shadow: 0 4px 12px rgba(35,41,70,.08) !important;
    }

    #step-1 .form-navigation .btn:last-child,
    #step-2 .form-navigation .btn:last-child,
    #step-3 .form-navigation .btn:last-child,
    .form-navigation .btn:last-child {
        background: linear-gradient(135deg, var(--graphite), var(--graphite2)) !important;
        color: #FFFFFF !important;
        border: 1px solid var(--graphite) !important;
    }

    #step-4 .form-navigation #final-submit-btn {
        background: linear-gradient(135deg, var(--sage), var(--sage2)) !important;
        color: #FFFFFF !important;
        border: 1px solid var(--sage2) !important;
    }
}

@media (max-width: 360px) {
    #step-1 .form-navigation .btn,
    #step-2 .form-navigation .btn,
    #step-3 .form-navigation .btn,
    #step-4 .form-navigation .btn,
    .form-navigation .btn,
    #final-submit-btn {
        min-height: 42px !important;
        height: 42px !important;
        font-size: 12px !important;
        padding: 0 10px !important;
    }
}

/* Mobile table toolbars: two controls per row at phone widths. */
@media (max-width: 600px) {
    .table-header {
        padding: 14px !important;
        gap: 12px !important;
        align-items: stretch !important;
    }

    .table-header h3 {
        width: 100% !important;
        font-size: 20px !important;
        line-height: 1.2 !important;
    }

    .table-header > div {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 10px !important;
        align-items: stretch !important;
    }

    .table-header .add-row-btn,
    .table-header .bulk-row-btn,
    .table-header .table-history-btn {
        width: 100% !important;
        min-width: 0 !important;
        min-height: 42px !important;
        height: 42px !important;
        padding: 0 8px !important;
        margin: 0 !important;
        border-radius: 12px !important;
        font-size: 12px !important;
        line-height: 1.1 !important;
        white-space: normal !important;
        text-align: center !important;
    }

    .table-header .table-history-btn {
        display: inline-flex !important;
        justify-self: stretch !important;
    }
}

@media (max-width: 360px) {
    .table-header > div {
        gap: 8px !important;
    }

    .table-header .add-row-btn,
    .table-header .bulk-row-btn,
    .table-header .table-history-btn {
        min-height: 40px !important;
        height: 40px !important;
        font-size: 11px !important;
        padding: 0 6px !important;
    }
}

.dynamic-table th:first-child,
.dynamic-table td:first-child {
    position: sticky !important;
    left: 0 !important;
    z-index: 40 !important;
    min-width: 66px !important;
    max-width: 66px !important;
    text-align: center !important;
    background: #FFF7ED !important;
    color: #1B1E2B !important;
    font-weight: 900 !important;
    box-shadow: 4px 0 10px rgba(35,41,70,.12) !important;
}

.dynamic-table th:first-child {
    z-index: 80 !important;
    background: #EDE7DF !important;
}

.dynamic-table tbody tr:nth-child(even) td:first-child {
    background: #FFF3E6 !important;
}

.dynamic-table tbody tr:hover td:first-child,
.dynamic-table tbody tr.pink-highlight td:first-child {
    background: #FEE2E2 !important;
    color: #991B1B !important;
}

/* Final table layer order: headers stay above scrolling body cells. */
.dynamic-table thead th {
    position: sticky !important;
    top: 0 !important;
    z-index: 180 !important;
}

.dynamic-table th:first-child {
    z-index: 220 !important;
}

.dynamic-table th:last-child {
    position: sticky !important;
    top: 0 !important;
    right: 0 !important;
    z-index: 230 !important;
    width: 236px !important;
    min-width: 236px !important;
    max-width: 236px !important;
    background: #EDE7DF !important;
}

.dynamic-table td:first-child {
    z-index: 70 !important;
}

.dynamic-table td:last-child,
.dynamic-table td.action-buttons {
    position: sticky !important;
    right: 0 !important;
    z-index: 60 !important;
    width: 236px !important;
    min-width: 236px !important;
    max-width: 236px !important;
    background: #fff !important;
}

.dynamic-table tbody tr:hover td.action-buttons,
.dynamic-table tbody tr:hover td:last-child {
    z-index: 65 !important;
}

.dynamic-table td.action-buttons {
    display: grid !important;
    grid-template-columns: repeat(6, 28px) !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 8px 6px !important;
    overflow: visible !important;
}

.dynamic-table td.action-buttons .add-btn,
.dynamic-table td.action-buttons .remove-btn,
.dynamic-table td.action-buttons .row-tool-btn {
    position: relative !important;
}

.dynamic-table td.action-buttons .action-menu-close-btn {
    display: none !important;
}

.row-select-wrap {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
    width: 50px !important;
    min-width: 50px !important;
    max-width: 50px !important;
    height: 32px !important;
    margin: 0 auto !important;
    cursor: pointer !important;
    white-space: nowrap !important;
    border-radius: 8px !important;
    background: rgba(255,255,255,.42) !important;
}

.row-select-checkbox {
    position: absolute !important;
    opacity: 0 !important;
    pointer-events: none !important;
    width: 1px !important;
    height: 1px !important;
    margin: 0 !important;
    padding: 0 !important;
}

.sno-number {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex: 0 0 20px !important;
    width: 20px !important;
    min-width: 20px !important;
    max-width: 20px !important;
    font-weight: 900 !important;
    font-size: 18px !important;
    line-height: 1 !important;
    overflow: hidden !important;
}

.row-check-ui {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex: 0 0 18px !important;
    width: 18px !important;
    height: 18px !important;
    border: 2px solid #6b7280 !important;
    border-radius: 5px !important;
    background: #fff !important;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.04) !important;
}

.row-select-checkbox:checked + .row-check-ui {
    border-color: #2563eb !important;
    background: #2563eb !important;
}

.row-select-checkbox:checked + .row-check-ui::after {
    content: "";
    width: 5px;
    height: 9px;
    border: solid #fff;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
    margin-top: -2px;
}

.dynamic-table th:first-child,
.dynamic-table td:first-child {
    min-width: 84px !important;
    max-width: 84px !important;
    width: 84px !important;
    overflow: hidden !important;
    padding-left: 8px !important;
    padding-right: 8px !important;
    text-align: center !important;
}

.dynamic-table tbody tr.is-row-selected td {
    background: #e0f2fe !important;
    border-color: #93c5fd !important;
}

.dynamic-table tbody tr.is-row-selected td:first-child {
    background: #bfdbfe !important;
    color: #1e3a8a !important;
}

.dynamic-table tbody tr.is-row-selected td.action-buttons,
.dynamic-table tbody tr.is-row-selected td:last-child {
    background: #eff6ff !important;
}

.bulk-row-btn {
    min-height: 40px !important;
    border: 1px solid rgba(255,255,255,.18) !important;
    border-radius: 12px !important;
    background: rgba(255,255,255,.12) !important;
    color: #fff !important;
    font-weight: 900 !important;
    padding: 8px 12px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 7px !important;
    cursor: pointer !important;
}

.bulk-row-btn:hover,
.bulk-row-btn:focus-visible {
    background: rgba(255,255,255,.2) !important;
    transform: translateY(-1px);
}

.bulk-row-btn.danger {
    background: rgba(220,38,38,.9) !important;
    border-color: rgba(254,202,202,.8) !important;
}

@media (max-width: 600px) {
    .dynamic-table th:last-child,
    .dynamic-table td:last-child,
    .dynamic-table td.action-buttons {
        position: static !important;
        left: auto !important;
        right: auto !important;
        z-index: auto !important;
        box-shadow: none !important;
    }

    .dynamic-table th:first-child,
    .dynamic-table td:first-child {
        position: sticky !important;
        left: 0 !important;
        min-width: 68px !important;
        max-width: 68px !important;
        width: 68px !important;
        padding-left: 6px !important;
        padding-right: 6px !important;
        background: #fff8ef !important;
        box-shadow: 6px 0 14px rgba(35,41,70,.08) !important;
    }

    .row-select-wrap {
        gap: 4px !important;
        width: 48px !important;
        min-width: 48px !important;
        max-width: 48px !important;
        height: 30px !important;
    }

    .sno-number {
        flex-basis: 20px !important;
        width: 20px !important;
        min-width: 20px !important;
        max-width: 20px !important;
        font-size: 17px !important;
    }

    .row-check-ui {
        flex-basis: 17px !important;
        width: 17px !important;
        height: 17px !important;
    }

    .dynamic-table th:first-child {
        z-index: 255 !important;
        background: #f2ece5 !important;
    }

    .dynamic-table td:first-child {
        z-index: 225 !important;
    }

    .dynamic-table th:last-child,
    .dynamic-table td:last-child,
    .dynamic-table td.action-buttons {
        width: 96px !important;
        min-width: 96px !important;
        max-width: 96px !important;
    }

    .dynamic-table td.action-buttons {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0 !important;
        padding: 8px !important;
        min-height: 52px !important;
        overflow: visible !important;
        cursor: pointer !important;
    }

    .dynamic-table td.action-buttons::before {
        content: "Actions";
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 76px;
        min-height: 38px;
        border-radius: 9px;
        background: linear-gradient(135deg, var(--graphite), var(--graphite2));
        color: #fff;
        font-size: 11px;
        font-weight: 900;
        box-shadow: 0 8px 16px rgba(35,41,70,.18);
    }

    .dynamic-table td.action-buttons > button {
        display: none !important;
    }

    .dynamic-table td.action-buttons.is-open {
        position: sticky !important;
        right: 0 !important;
        z-index: 260 !important;
    }

    .dynamic-table td.action-buttons.is-open > button {
        display: inline-flex !important;
    }

    .dynamic-table td.action-buttons.is-open::before {
        display: none;
    }

    .dynamic-table td.action-buttons.is-open {
        width: 180px !important;
        min-width: 180px !important;
        max-width: 180px !important;
        display: grid !important;
        grid-template-columns: repeat(3, 34px) !important;
        gap: 7px !important;
        padding: 8px !important;
        background: #fff !important;
        border: 1px solid var(--border) !important;
        border-radius: 12px !important;
        box-shadow: 0 14px 30px rgba(35,41,70,.22) !important;
    }

    .dynamic-table td.action-buttons .add-btn,
    .dynamic-table td.action-buttons .remove-btn,
    .dynamic-table td.action-buttons .row-tool-btn {
        width: 34px !important;
        min-width: 34px !important;
        height: 34px !important;
        min-height: 34px !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .dynamic-table td.action-buttons.is-open .action-menu-close-btn {
        display: inline-flex !important;
        grid-column: 1 / -1 !important;
        order: -1 !important;
        width: 100% !important;
        min-width: 100% !important;
        height: 30px !important;
        min-height: 30px !important;
        color: #991b1b !important;
        background: #fff7f7 !important;
        border: 1px solid #fecaca !important;
        border-radius: 999px !important;
        font-size: 12px !important;
        font-weight: 900 !important;
        gap: 6px !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 10px !important;
        margin: 0 0 2px 0 !important;
        box-shadow: none !important;
    }

    .dynamic-table td.action-buttons.is-open .action-menu-close-btn:hover,
    .dynamic-table td.action-buttons.is-open .action-menu-close-btn:focus-visible {
        background: #fee2e2 !important;
        color: #7f1d1d !important;
        transform: none !important;
    }
}
</style>


<style>
/* Final bitwise configuration table polish. */
#bitwiseConfigModal .modal-box {
    width: min(96vw, 1770px) !important;
    max-height: 88vh !important;
    border-radius: 24px !important;
    overflow: hidden !important;
}

#bitwiseConfigModal .modal-box h3 {
    padding: 22px 28px !important;
    font-size: 26px !important;
    font-weight: 900 !important;
    color: var(--text, #1B1E2B) !important;
    background: linear-gradient(180deg, #FFFFFF 0%, #FBFAF8 100%) !important;
}

#bitwiseConfigModal .table-wrapper {
    max-height: calc(88vh - 164px) !important;
    min-height: 280px !important;
    overflow: auto !important;
    background: #fff !important;
    border-top: 1px solid var(--border, #E5DDD5) !important;
    border-bottom: 1px solid var(--border, #E5DDD5) !important;
}

#bitwiseConfigModal .bitwise-bulk-tools {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
    padding: 12px 28px 16px !important;
    background: #fff !important;
    border-bottom: 1px solid var(--border, #E5DDD5) !important;
}

#bitwiseConfigModal .bitwise-bulk-tools .bulk-row-btn {
    color: var(--graphite, #232946) !important;
    background: #F4F0EA !important;
    border-color: var(--border, #E5DDD5) !important;
}

#bitwiseConfigModal .bitwise-bulk-tools .bulk-row-btn.danger {
    color: #fff !important;
    background: #dc2626 !important;
}

#bitwiseConfigModal .config-table {
    width: max-content !important;
    min-width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 14px !important;
}

#bitwiseConfigModal .config-table thead th {
    position: sticky !important;
    top: 0 !important;
    z-index: 180 !important;
    padding: 16px 14px !important;
    background: var(--graphite, #232946) !important;
    color: #fff !important;
    font-size: 13px !important;
    font-weight: 900 !important;
    text-align: center !important;
    white-space: nowrap !important;
}

#bitwiseConfigModal .config-table th:first-child,
#bitwiseConfigModal .config-table td:first-child {
    position: sticky !important;
    left: 0 !important;
    z-index: 70 !important;
    width: 78px !important;
    min-width: 78px !important;
    max-width: 78px !important;
    text-align: center !important;
    font-weight: 900 !important;
    background: #FFF7ED !important;
    box-shadow: 5px 0 12px rgba(35,41,70,.1) !important;
}

#bitwiseConfigModal .config-table th:first-child {
    z-index: 220 !important;
    background: var(--graphite, #232946) !important;
    color: #fff !important;
}

#bitwiseConfigModal .config-table th:last-child,
#bitwiseConfigModal .config-table td:last-child {
    position: sticky !important;
    right: 0 !important;
    z-index: 75 !important;
    width: 236px !important;
    min-width: 236px !important;
    max-width: 236px !important;
    text-align: center !important;
    background: #fff !important;
    box-shadow: -5px 0 12px rgba(35,41,70,.1) !important;
}

#bitwiseConfigModal .config-table th:last-child {
    z-index: 230 !important;
    background: #3A3F58 !important;
    color: #fff !important;
}

#bitwiseConfigModal .config-table td {
    padding: 12px 12px !important;
    border-bottom: 1px solid var(--border, #E5DDD5) !important;
    border-right: 1px solid #F1ECE7 !important;
    background: #fff !important;
    vertical-align: middle !important;
}

#bitwiseConfigModal .config-table tbody tr:nth-child(even) td {
    background: #FFFCFA !important;
}

#bitwiseConfigModal .config-table tbody tr:hover td {
    background: #FFF3E6 !important;
}

#bitwiseConfigModal .config-table tbody tr:hover td:first-child {
    color: #991B1B !important;
    background: #FEE2E2 !important;
}

#bitwiseConfigModal .config-table tbody tr:hover td:last-child {
    background: #fff !important;
}

#bitwiseConfigModal .config-table tbody tr.is-row-selected td {
    background: #e0f2fe !important;
    border-color: #93c5fd !important;
}

#bitwiseConfigModal .config-table tbody tr.is-row-selected td:first-child {
    background: #bfdbfe !important;
    color: #1e3a8a !important;
}

#bitwiseConfigModal .config-table tbody tr.is-row-selected td:last-child {
    background: #eff6ff !important;
}

#bitwiseConfigModal .config-table input,
#bitwiseConfigModal .config-table select,
#bitwiseConfigModal .config-table textarea {
    min-width: 190px !important;
    height: 42px !important;
    padding: 9px 12px !important;
    border: 1px solid var(--border, #E5DDD5) !important;
    border-radius: 12px !important;
    background: #fff !important;
    color: var(--text, #1B1E2B) !important;
    font-size: 14px !important;
    font-weight: 600 !important;
}

#bitwiseConfigModal .config-table input:focus,
#bitwiseConfigModal .config-table select:focus,
#bitwiseConfigModal .config-table textarea:focus {
    border-color: var(--copper, #C08457) !important;
    box-shadow: 0 0 0 4px rgba(192,132,87,.18) !important;
    outline: none !important;
}

#bitwiseConfigModal .config-table .row-select-checkbox {
    position: absolute !important;
    opacity: 0 !important;
    pointer-events: none !important;
    width: 1px !important;
    min-width: 1px !important;
    max-width: 1px !important;
    height: 1px !important;
    min-height: 1px !important;
    max-height: 1px !important;
    padding: 0 !important;
    margin: 0 !important;
    border: 0 !important;
    box-shadow: none !important;
}

#bitwiseConfigModal .config-table td.bitwise-action-buttons {
    display: grid !important;
    grid-template-columns: repeat(6, 28px) !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 8px 6px !important;
}

#bitwiseConfigModal .config-table td:last-child button {
    width: 28px !important;
    height: 28px !important;
    min-width: 28px !important;
    border: 0 !important;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 !important;
    color: #fff !important;
    font-size: 14px !important;
    font-weight: 900 !important;
    cursor: pointer !important;
}

#bitwiseConfigModal .config-table td:last-child .add-btn {
    background: #15803d !important;
}

#bitwiseConfigModal .config-table td:last-child .remove-btn {
    background: #dc2626 !important;
}

#bitwiseConfigModal .config-table td:last-child .duplicate-row-btn {
    background: var(--copper) !important;
}

#bitwiseConfigModal .config-table td:last-child .move-up-row-btn,
#bitwiseConfigModal .config-table td:last-child .move-down-row-btn {
    background: var(--graphite) !important;
}

#bitwiseConfigModal .config-table td:last-child .move-position-row-btn {
    background: var(--sage2) !important;
}

#bitwiseConfigModal .modal-actions {
    padding: 20px 36px !important;
    gap: 16px !important;
    background: #FBFAF8 !important;
}

#bitwiseConfigModal .modal-actions button {
    min-width: 180px !important;
    min-height: 54px !important;
    border-radius: 16px !important;
    font-size: 16px !important;
    font-weight: 900 !important;
}

@media (max-width: 600px) {
    #bitwiseConfigModal .modal-box {
        width: calc(100vw - 16px) !important;
        max-height: 92vh !important;
        border-radius: 18px !important;
    }

    #bitwiseConfigModal .modal-box h3 {
        padding: 16px 18px !important;
        font-size: 22px !important;
    }

    #bitwiseConfigModal .table-wrapper {
        max-height: calc(92vh - 206px) !important;
        min-height: 260px !important;
    }

    #bitwiseConfigModal .bitwise-bulk-tools {
        padding: 10px 14px !important;
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    #bitwiseConfigModal .bitwise-bulk-tools .bulk-row-btn {
        min-height: 38px !important;
        padding: 7px 8px !important;
        border-radius: 10px !important;
        font-size: 11px !important;
        line-height: 1.15 !important;
        white-space: normal !important;
    }

    #bitwiseConfigModal .config-table th:first-child,
    #bitwiseConfigModal .config-table td:first-child {
        width: 58px !important;
        min-width: 58px !important;
        max-width: 58px !important;
    }

    #bitwiseConfigModal .config-table th:last-child,
    #bitwiseConfigModal .config-table td:last-child {
        width: 104px !important;
        min-width: 104px !important;
        max-width: 104px !important;
    }

    #bitwiseConfigModal .config-table input,
    #bitwiseConfigModal .config-table select,
    #bitwiseConfigModal .config-table textarea {
        min-width: 150px !important;
        height: 40px !important;
        font-size: 13px !important;
    }

    #bitwiseConfigModal .config-table td.bitwise-action-buttons {
        grid-template-columns: repeat(2, 34px) !important;
        gap: 8px !important;
        padding: 8px 9px !important;
    }

    #bitwiseConfigModal .config-table td:last-child button {
        width: 34px !important;
        height: 34px !important;
        min-width: 34px !important;
        min-height: 34px !important;
        font-size: 14px !important;
    }

    #bitwiseConfigModal .modal-actions {
        padding: 14px !important;
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
    }

    #bitwiseConfigModal .modal-actions button {
        min-width: 0 !important;
        width: 100% !important;
        min-height: 46px !important;
        font-size: 13px !important;
    }
}
</style>

<!-- ressponsive      -->
</head>
<body>

<!-- Progress Indicator -->


<div class="main-wrapper">

<div class="info-card-body">
      
        <button type="button" class="page-back-btn" onclick="window.history.back()"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back</button>

        <div class="page-title">Add API / Product</div>
        <h1 class="info-heading"><?php echo esc($item_info->title ?? $model_name ?? $category ?? "Product Information"); ?></h1>
        <p class="info-subtitle">Product overview - stays visible while you use the action panel below.</p>

        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">Brand Name</div>
                <div class="info-value"><?php echo esc($item_info->brand_name ?? $brand ?? "N/A"); ?></div>
            </div>
            <div class="info-box">
                <div class="info-label">Product Name</div>
                <div class="info-value"><?php echo esc($item_info->product_name ?? $category ?? "N/A"); ?></div>
            </div>
            <div class="info-box">
                <div class="info-label">Model No</div>
                <div class="info-value"><?php echo esc($item_info->model_no ?? $model_no ?? $model_name ?? "N/A"); ?></div>
            </div>
            <div class="info-box">
                <div class="info-label">Version</div>
                <div class="info-value"><?php echo esc($item_info->version ?? $version ?? "N/A"); ?></div>
            </div>
            <div class="info-box">
                <div class="info-label">Batch</div>
                <div class="info-value"><?php echo esc($item_info->batch ?? $batch ?? "N/A"); ?></div>
            </div>
        </div>
    </div>

    <!-- page header  -->
    <div class="page-header">
        
        <div class="progress-flow">
            <div class="flow-step active" id="step-indicator-1">
                <span class="flow-number">1</span>
                <span class="flow-text">Product</span>
            </div>
            <div class="flow-arrow"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></div>
            <div class="flow-step" id="step-indicator-2">
                <span class="flow-number">2</span>
                <span class="flow-text">Command</span>
            </div>
            <div class="flow-arrow"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></div>
            <div class="flow-step" id="step-indicator-3">
                <span class="flow-number">3</span>
                <span class="flow-text">Response</span>
            </div>
            <div class="flow-arrow"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></div>
            <div class="flow-step" id="step-indicator-4">
                <span class="flow-number">4</span>
                <span class="flow-text">Error</span>
            </div>
        </div>
    </div>
    


    <?php
    $is_editing_api = !empty($edit_config_id);
    $edit_config_json = !empty($edit_config_data) ? json_encode($edit_config_data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) : 'null';
    ?>

    <?php echo form_open(get_uri("items/saveapi"), array("id" => "file-form", "class" => "general-form")); ?>
    <input type="hidden" name="itemid" id="api_itemid" value="<?php echo (int) ($itemid ?? 0); ?>">
    <input type="hidden" name="config_id" id="api_config_id" value="<?php echo (int) ($edit_config_id ?? 0); ?>">

    <?php if ($is_editing_api): ?>
        <div class="editing-badge" id="api-editing-badge">
            Editing Parameter: <?php echo esc($edit_config_data['product']['parameter_name'] ?? 'API Configuration'); ?>
        </div>
    <?php endif; ?>

    <!-- Step 1 -->
    <div id="step-1" class="form-step active">
        <div class="container">
            <h2>PRODUCT INFORMATION</h2>
            <div id="step1-error" class="step-error"></div>

            <div class="form-group">
                <label class="required">Parameter Names</label>
                <input type="text" name="parametername" id="par_parametername" placeholder="e.g., Power Control">
                <input type="hidden" id="default-param-list" value='<?= json_encode($paramNames ?? []); ?>'>
                <small id="duplicate-error" style="color:red; display:none;"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Parameter already exists!</small>
                <small id="char-error" style="color:red; display:none;"><i class="fa-solid fa-circle-xmark" aria-hidden="true"></i> Special characters not allowed!</small>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" value="<?php echo $category ?? ''; ?>" id="productname" name="productname" readonly placeholder="Product name">
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label>Brand Name</label>
                        <input type="text" value="<?php echo $brand ?? ''; ?>" id="brandname" name="brandname" readonly placeholder="Brand name">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label>Model Name</label>
                        <input type="text" value="<?= esc($model_name ?? ''); ?>" id="modelname" name="modelname" readonly placeholder="Model name">
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" placeholder="Select date">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="required">Page Number</label>
                        <input type="text" name="numberofpage" id="numberofpage" placeholder="e.g., 1">
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="required">Sequence No</label>
                        <input type="text" name="Sequence" id="Sequence" placeholder="e.g., 1">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="required">Number of Command Frames</label>
                <input type="number" name="numberofpages" id="numberofpages" min="1" value="1" placeholder="e.g., 1">
            </div>
            <div class="form-group">
                <label class="required">Number of Response Frames</label>
                <input type="number" name="numberofpages_response" id="page" min="1" value="1" placeholder="e.g., 1">
            </div>
            <div class="form-group">
                <label class="required">Number of Error Frames</label>
                <input type="number" name="numberofpages_error" id="pages" min="1" value="1" placeholder="e.g., 1">
            </div>

            <div class="form-group">
                <label class="required">Page Names</label>
                <input type="text" name="pagenames" id="pagenames" placeholder="e.g., Controls Page">
            </div>

            <div class="form-group">
                <label>Product Description</label>
                <textarea name="productdescription" id="productdescription" rows="4" placeholder="Briefly describe product features, protocol, and usage"></textarea>
                <small>Max 300 words</small>
            </div>

            <div class="form-navigation">
                <div></div>
                <button class="btn" type="button" onclick="goToNextStep(1)">Save & Next</button>
            </div>
        </div>
    </div>

    <!-- Step 2 -->
    <div id="step-2" class="form-step">
        <div class="container">
            <input type="hidden" name="id" value="<?php echo $id ?? ''; ?>">
            <h2 id="command-title-display">COMMAND FRAME</h2>
            
            <div id="step2-error" class="step-error"></div>

            <div class="form-group">
                <label>Parameter Name</label>
                <input type="text" name="cmd_parametername" id="com_parametername" readonly aria-disabled="true" placeholder="Parameter name from product information">
            </div>

            <div class="form-group">
                <label class="required">Data Flow Direction</label>
                <select name="cmd_data_flow_direction" id="cmd_data_flow_direction">
                    <option value="">Select Direction</option>
                    <option value="App/Server to Device">App/Server to Device</option>
                    <option value="Device to App/Server">Device to App/Server</option>
                </select>
            </div>

            <div class="form-group">
                <label class="required">Frame Type</label>
                <select name="cmd_frametype" id="cmd_frametype">
                    <option value="COMMAND">COMMAND</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="required">Frame size in bytes</label>
                        <input type="number" id="cmd_framesizeinbytes" name="cmd_framesizeinbytes" min="0" placeholder="e.g., 8">
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="required">No of rows</label>
                        <input type="number" id="cmd_noofrows" name="cmd_noofrows" readonly value="0" placeholder="Auto calculated">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="required">Frame Trigger</label>
                <select name="cmd_frametrigger" id="cmd_frame_trigger_options">
                    <option value="None">None</option>
                    <option value="Text">Text</option>
                    <option value="Incoming Frame">INCOMING FRAME</option>
                    <option value="Toggle Switch">Toggle Switch</option>
                    <option value="Notification">Notification</option>
                    <option value="Console">Console</option>
                    <option value="Enter Text">Enter Text</option>
                    <option value="Slider">Slider</option>
                    <option value="Brightness Slider">Brightness Slider</option>
                    <option value="Airflow Slider">Airflow Slider</option>
                    <option value="CCT Slider">CCT Slider</option>
                    <option value="Saturation Slider">Saturation Slider</option>
                    <option value="Hue Slider">Hue Slider</option>
                    <option value="Hue Slider Custom">Hue Slider Custom</option>
                    <option value="Hue Circle">Hue Circle</option>
                    <option value="Push Button">Push Button (Big)</option>
                    <option value="Drop Down">Drop Down</option>
                    <option value="Trigger">Trigger</option>
                    <option value="Display">Display</option>
                    <option value="Button">Button</option>
                    <option value="Auto Trigger With Timer">Auto Trigger With Timer</option>
                    <option value="Toast Display">Toast Display</option>
                </select>
            </div>

            <div class="form-group">
                <label class="required">Multiple UI</label>
                <select name="cmd_multipleui" id="cmd_multipleui">
                    <option value="false">False</option>
                    <option value="true">True</option>
                </select>
            </div>

            <div class="form-group">
                <label class="required">Trigger UI</label>
                <select name="cmd_triggerui" id="cmd_triggerui">
                    <option value="false">False</option>
                    <option value="true">True</option>
                </select>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <h3>Command Frame Configuration</h3>
                    <div>
                        <button type="button" class="add-row-btn" onclick="addRow('cmd')"><i class="fa-solid fa-plus" aria-hidden="true"></i> Add Row</button>
                        <button type="button" class="add-row-btn" onclick="clearTable('cmd')">Clear Table</button>
                        <button type="button" class="bulk-row-btn danger" onclick="deleteSelectedRows('cmd')"><i class="fa-solid fa-trash" aria-hidden="true"></i> Delete Selected</button>
                        <button type="button" class="bulk-row-btn" onclick="moveSelectedRowsUp('cmd')" aria-label="Move selected command rows up"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i> Selected</button>
                        <button type="button" class="bulk-row-btn" onclick="moveSelectedRowsDown('cmd')" aria-label="Move selected command rows down"><i class="fa-solid fa-arrow-down" aria-hidden="true"></i> Selected</button>
                        <button type="button" class="bulk-row-btn" onclick="moveSelectedRowsToPosition('cmd')"><i class="fa-solid fa-list-ol" aria-hidden="true"></i> Position</button>
                        <button type="button" class="table-history-btn" data-history-action="undo" data-prefix="cmd" data-tooltip="Undo" onclick="undoTableChange('cmd')" aria-label="Undo row change"><i class="fa-solid fa-rotate-left" aria-hidden="true"></i></button>
                        <button type="button" class="table-history-btn" data-history-action="redo" data-prefix="cmd" data-tooltip="Redo" onclick="redoTableChange('cmd')" aria-label="Redo row change"><i class="fa-solid fa-rotate-right" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="dynamic-table">
                        <thead>
                            <tr><th>Select<br>S.No</th><th>Start Byte</th><th>End Byte</th><th>Field Description</th><th>No of Bytes</th><th>Bit Size</th><th>Data Type</th><th>Value Definition</th><th>Display Min</th><th>Display Max</th><th>Hex Min</th><th>Hex Max</th><th>UI Type</th><th>UI ID</th><th>AS PER UI</th><th>UI Serial No</th><th>UI Values</th><th>UI Page No</th><th>UI In/Out</th><th>Slider Step</th><th>Default Value</th><th>Dev Notes</th><th>Enable</th><th>Message</th><th>Scaling</th><th style="min-width:220px;">Action</th></tr>
                        </thead>
                        <tbody id="additionalTableBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="frame-mini-nav command-mini-nav">
                <div id="command-progress-tracker" class="progress-circles"></div>
                <div class="frame-mini-actions">
                    <button type="button" id="back-command-btn" class="back-btn" onclick="handleBackCommand()"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back Frame</button>
                    <button type="button" id="next-command-btn" class="next-btn" onclick="handleNextCommand()">Next Command Frame <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                </div>
            </div>

            <div class="form-navigation">
                <button class="btn" type="button" onclick="goToPreviousStep(2)">Back</button>
                <button class="btn" type="button" onclick="goToNextStep(2)">Save & Next</button>
            </div>
        </div>
    </div>


    <!-- Step 3 -->
    <div id="step-3" class="form-step">
        <div class="container">
            <h2 id="response-title-display">RESPONSE FRAME</h2>
          
            <div id="step3-error" class="step-error"></div>
            <div class="form-group">
                <label>Parameter Name</label>
                <input type="text" name="resp_parametername" id="resp_parametername" readonly aria-disabled="true" placeholder="Parameter name from product information">
            </div>

            <div class="form-group">
                <label class="required">Data Flow Direction</label>
                <select name="resp_data_flow_direction" id="resp_data_flow_direction">
                    <option value="">Select Direction</option>
                    <option value="App/Server to Device">App/Server to Device</option>
                    <option value="Device to App/Server">Device to App/Server</option>
                </select>
            </div>

            <div class="form-group">
                <label class="required">Frame Type</label>
                <select name="resp_frametype" id="resp_frametype">
                    <option value="RESPONSE">RESPONSE</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="required">Frame size in bytes</label>
                        <input type="number" id="resp_framesizeinbytes" name="resp_framesizeinbytes" min="0" placeholder="e.g., 8">
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="required">No of rows</label>
                        <input type="number" id="resp_noofrows" name="resp_noofrows" readonly value="0" placeholder="Auto calculated">
                    </div>
                </div>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <h3>Response Frame Configuration</h3>
                    <div>
                        <button type="button" class="add-row-btn" onclick="addRow('resp')"><i class="fa-solid fa-plus" aria-hidden="true"></i> Add Row</button>
                        <button type="button" class="add-row-btn" onclick="clearTable('resp')">Clear Table</button>
                        <button type="button" class="bulk-row-btn danger" onclick="deleteSelectedRows('resp')"><i class="fa-solid fa-trash" aria-hidden="true"></i> Delete Selected</button>
                        <button type="button" class="bulk-row-btn" onclick="moveSelectedRowsUp('resp')" aria-label="Move selected response rows up"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i> Selected</button>
                        <button type="button" class="bulk-row-btn" onclick="moveSelectedRowsDown('resp')" aria-label="Move selected response rows down"><i class="fa-solid fa-arrow-down" aria-hidden="true"></i> Selected</button>
                        <button type="button" class="bulk-row-btn" onclick="moveSelectedRowsToPosition('resp')"><i class="fa-solid fa-list-ol" aria-hidden="true"></i> Position</button>
                        <button type="button" class="table-history-btn" data-history-action="undo" data-prefix="resp" data-tooltip="Undo" onclick="undoTableChange('resp')" aria-label="Undo row change"><i class="fa-solid fa-rotate-left" aria-hidden="true"></i></button>
                        <button type="button" class="table-history-btn" data-history-action="redo" data-prefix="resp" data-tooltip="Redo" onclick="redoTableChange('resp')" aria-label="Redo row change"><i class="fa-solid fa-rotate-right" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="dynamic-table">
                        <thead>
                            <tr><th>Select<br>S.No</th><th>Start Byte</th><th>End Byte</th><th>Field Description</th><th>No of Bytes</th><th>Bit Size</th><th>Data Type</th><th>Value Definition</th><th>Display Min</th><th>Display Max</th><th>Hex Min</th><th>Hex Max</th><th>UI Type</th><th>UI ID</th><th>AS PER UI</th><th>UI Serial No</th><th>UI Values</th><th>UI Page No</th><th>UI In/Out</th><th>Slider Step</th><th>Default Value</th><th>Dev Notes</th><th>Enable</th><th>Message</th><th>Scaling</th><th style="min-width:220px;">Action</th></tr>
                        </thead>
                        <tbody id="responseTableBody"></tbody>
                    </table>
                </div>
            </div>

              <div class="frame-mini-nav response-mini-nav">
                <div id="response-progress-tracker" class="progress-circles"></div>
                <div class="frame-mini-actions">
                    <button type="button" id="back-response-page-btn" class="back-btn pagination-back-btn" onclick="handleBackResponse()"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back Response Frame</button>
                    <button type="button" id="next-response-page-btn" class="next-btn pagination-next-btn" onclick="handleNextResponse()">Next Response Frame <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                </div>
            </div>

            <div class="form-navigation">
                <button class="btn" type="button" onclick="goToPreviousStep(3)">Back</button>
                <button class="btn" type="button" onclick="goToNextStep(3)">Save & Next</button>
            </div>
        </div>
    </div>

    <!-- Step 4 -->
    <div id="step-4" class="form-step">
        <div class="container">
            <h2 id="error-title-display">ERROR FRAME</h2>
            
            <div id="step4-error" class="step-error"></div>

            <div class="form-group">
                <label>Parameter Name</label>
                <input type="text" name="error_parametername" id="error_parametername" readonly aria-disabled="true" placeholder="Parameter name from product information">
            </div>

            <div class="form-group">
                <label class="required">Data Flow Direction</label>
                <select name="error_data_flow_direction" id="error_data_flow_direction">
                    <option value="">Select Direction</option>
                    <option value="App/Server to Device">App/Server to Device</option>
                    <option value="Device to App/Server">Device to App/Server</option>
                </select>
            </div>

            <div class="form-group">
                <label class="required">Frame Type</label>
                <select name="error_frametype" id="error_frametype">
                    <option value="ERROR">ERROR</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="required">Frame size in bytes</label>
                        <input type="number" id="error_framesizeinbytes" name="error_framesizeinbytes" min="0" placeholder="e.g., 4">
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="required">No of rows</label>
                        <input type="number" id="error_noofrows" name="error_noofrows" readonly value="0" placeholder="Auto calculated">
                    </div>
                </div>
            </div>



            
            <div class="table-container">
                <div class="table-header">
                    <h3>Error Frame Configuration</h3>
                    <div>
                        <button type="button" class="add-row-btn" onclick="addRow('error')"><i class="fa-solid fa-plus" aria-hidden="true"></i> Add Row</button>
                        <button type="button" class="add-row-btn" onclick="clearTable('error')">Clear Table</button>
                        <button type="button" class="bulk-row-btn danger" onclick="deleteSelectedRows('error')"><i class="fa-solid fa-trash" aria-hidden="true"></i> Delete Selected</button>
                        <button type="button" class="bulk-row-btn" onclick="moveSelectedRowsUp('error')" aria-label="Move selected error rows up"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i> Selected</button>
                        <button type="button" class="bulk-row-btn" onclick="moveSelectedRowsDown('error')" aria-label="Move selected error rows down"><i class="fa-solid fa-arrow-down" aria-hidden="true"></i> Selected</button>
                        <button type="button" class="bulk-row-btn" onclick="moveSelectedRowsToPosition('error')"><i class="fa-solid fa-list-ol" aria-hidden="true"></i> Position</button>
                        <button type="button" class="table-history-btn" data-history-action="undo" data-prefix="error" data-tooltip="Undo" onclick="undoTableChange('error')" aria-label="Undo row change"><i class="fa-solid fa-rotate-left" aria-hidden="true"></i></button>
                        <button type="button" class="table-history-btn" data-history-action="redo" data-prefix="error" data-tooltip="Redo" onclick="redoTableChange('error')" aria-label="Redo row change"><i class="fa-solid fa-rotate-right" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="dynamic-table">
                        <thead>
                            <tr><th>Select<br>S.No</th><th>Start Byte</th><th>End Byte</th><th>Field Description</th><th>No of Bytes</th><th>Bit Size</th><th>Data Type</th><th>Value Definition</th><th>Display Min</th><th>Display Max</th><th>Hex Min</th><th>Hex Max</th><th>UI Type</th><th>UI ID</th><th>AS PER UI</th><th>UI Serial No</th><th>UI Values</th><th>UI Page No</th><th>UI In/Out</th><th>Slider Step</th><th>Default Value</th><th>Dev Notes</th><th>Enable</th><th>Message</th><th>Scaling</th><th style="min-width:220px;">Action</th></tr>
                        </thead>
                        <tbody id="errorTableBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="frame-mini-nav error-mini-nav">
                <div id="error-progress-tracker" class="progress-circles"></div>
                <div class="frame-mini-actions">
                    <button type="button" id="back-error-page-btn" class="back-btn" onclick="handleBackError()"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back Error Frame</button>
                    <button type="button" id="next-error-page-btn" class="next-btn" onclick="handleNextError()">Next Error Frame <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                </div>
            </div>

            <div class="form-navigation">
                <button class="btn" type="button" onclick="goToPreviousStep(4)">Back</button>
                <button id="final-submit-btn" class="btn" type="button" style="background-color: #28a745; display:none;" onclick="submitFinalApiForm()">Submit</button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <!-- <div id="bitwiseConfigModal" class="modal-overlay">
        <div class="modal-box">
            <h3>Bitwise Configuration</h3>
            <table class="config-table">
                <thead><tr><th>S.No</th><th>Start Bit</th><th>End Bit</th><th>No. of Bits</th><th>Data Type</th><th>Value Definition</th><th>Display Min</th><th>Display Max</th><th>Hex Min</th><th>Hex Max</th><th>UI Type</th><th>UI ID</th><th>As Per UI</th><th>UI Serial No</th><th>UI Values</th><th>UI Page No</th><th>UI In/Out</th><th>Slider Step</th><th>Default Value</th><th>Dev Notes</th><th>Scaling</th></tr></thead>
                <tbody id="bitwiseTableBody"></tbody>
            </table>
            <div class="modal-actions">
                <button class="save-btn" onclick="saveBitwiseConfig(event)">Save</button>
                <button class="btn" onclick="closeBitwiseConfig(event)">Cancel</button>
            </div>
        </div>
    </div> -->

<!-- bit wise -->
    <div id="bitwiseConfigModal" class="modal-overlay">
    <div class="modal-box">

        <h3>Bitwise Configuration</h3>

        <div class="bitwise-bulk-tools">
            <button type="button" class="bulk-row-btn danger" onclick="deleteSelectedBitwiseRows()"><i class="fa-solid fa-trash" aria-hidden="true"></i> Delete Selected</button>
            <button type="button" class="bulk-row-btn" onclick="moveSelectedBitwiseRowsUp()"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i> Selected</button>
            <button type="button" class="bulk-row-btn" onclick="moveSelectedBitwiseRowsDown()"><i class="fa-solid fa-arrow-down" aria-hidden="true"></i> Selected</button>
            <button type="button" class="bulk-row-btn" onclick="moveSelectedBitwiseRowsToPosition()"><i class="fa-solid fa-list-ol" aria-hidden="true"></i> Position</button>
        </div>

        <div class="table-wrapper">
            <table class="config-table">
                <thead>
                    <tr>
                        <th>Select<br>S.No</th>
                        <th>Start Bit</th>
                        <th>End Bit</th>
                        <th>No. of Bits</th>
                        <th>Data Type</th>
                        <th>Value Definition</th>
                        <th>Display Min</th>
                        <th>Display Max</th>
                        <th>Hex Min</th>
                        <th>Hex Max</th>
                        <th>UI Type</th>
                        <th>UI ID</th>
                        <th>As Per UI</th>
                        <th>UI Serial No</th>
                        <th>UI Values</th>
                        <th>UI Page No</th>
                        <th>UI In/Out</th>
                        <th>Slider Step</th>
                        <th>Default Value</th>
                        <th>Dev Notes</th>
                        <th>Notification Enable</th>
                        <th>Message</th>
                        <th>Scaling</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="bitwiseTableBody"></tbody>
            </table>
        </div>

        <div class="modal-actions">
            <button class="save-btn" onclick="saveBitwiseConfig(event)">Save</button>
            <button class="cancel-btn" onclick="closeBitwiseConfig(event)">Cancel</button>
        </div>
        

    </div>
</div>

    <?php echo form_close(); ?>
</div>



<!-- workign buttons of form  -->
<script>

function showStep(stepNumber) {

    // Hide all forms
    document.querySelectorAll('.form-step').forEach(step => {
        step.classList.remove('active');
    });

    // Show selected form
    document.getElementById('step-' + stepNumber)
        .classList.add('active');

    // Remove active class from all progress buttons
    document.querySelectorAll('.flow-step').forEach(btn => {
        btn.classList.remove('active');
    });

    // Activate selected progress button
    document.getElementById('step-indicator-' + stepNumber)
        .classList.add('active');
}

/* ======================================
   PROGRESS BAR BUTTON CLICK EVENTS
====================================== */

document.getElementById('step-indicator-1')
    .addEventListener('click', function () {
        showStep(1);
    });

document.getElementById('step-indicator-2')
    .addEventListener('click', function () {
        showStep(2);
    });

document.getElementById('step-indicator-3')
    .addEventListener('click', function () {
        showStep(3);
    });

document.getElementById('step-indicator-4')
    .addEventListener('click', function () {
        showStep(4);
    });

</script>

<script>
/* ===========================
   JavaScript - main script
   =========================== */


// Global variables

let currentStep = 1;
const totalSteps = 4;
let currentCommandSubStep = 1;
let totalCommandsToCreate = 2;
var isCommandSaved = false;
var isResponseSaved = false;
var isErrorSaved = false;
// Initialize form
document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM Loaded - Initializing form");
    
    // Sync parameter name listeners
    syncParameterNameToAllSteps();
    updateProgressIndicator();

    // Initialize tables (adds initial rows if none)
    initializeTables();

    // Attach handlers to any existing rows (if page was pre-populated)
    ['additionalTableBody','responseTableBody','errorTableBody'].forEach(id => {
        const tbody = document.getElementById(id);
        if (!tbody) return;
        const prefix = id === 'additionalTableBody' ? 'cmd' : (id === 'responseTableBody' ? 'resp' : 'error');
        console.log(`Setting up rows for ${prefix} table (${tbody.rows.length} rows)`);
        
        Array.from(tbody.rows).forEach((row, idx) => {
            attachUIChangeHandlerForRow(row, prefix, idx + 1);
            attachFieldDescriptionHandler(row, prefix, idx + 1);
            attachUIChangeValidationHandler(row, prefix, idx + 1);
            setupByteCalculation(row, prefix, idx + 1);
            attachFieldDescriptionChangeHandler(row, prefix, idx + 1); // NEW: Add field description change handler
        });
        // update frame size fields to reflect current rows
        updateFrameCountsForPrefix(prefix);
    });

    // Initialize cmd frame size based on existing rows
    updateFrameCountsForPrefix('cmd');
    updateFrameCountsForPrefix('resp');
    updateFrameCountsForPrefix('error');
    
    console.log("Initialization complete");
});

// Initialize all tables
function initializeTables() {
    console.log("Initializing tables");
    // If tables are empty, add two rows by default (only for fresh forms)
    const tables = [
        {id:'additionalTableBody', prefix:'cmd'}, 
        {id:'responseTableBody', prefix:'resp'}, 
        {id:'errorTableBody', prefix:'error'}
    ];
    
    tables.forEach(t => {
        const tbody = document.getElementById(t.id);
        if (!tbody) return;
        console.log(`${t.id}: ${tbody.rows.length} rows`);
        if (tbody.rows.length === 0) {
            console.log(`Adding 2 rows to ${t.id}`);
            for (let i = 0; i < 2; i++) {
                addRow(t.prefix);
            }
        } else {
            // ensure indices & handlers are correct
            updateSerialNumbers(t.id);
        }
    });
}

// ======================================================
// NEW FUNCTION: Update Default Value based on Field Description
// ======================================================
function updateDefaultValueBasedOnFieldDescription(selectElement, prefix, index) {
    if (!selectElement) return;
    
    const fieldDescription = selectElement.value;
    const row = selectElement.closest('tr');
    if (!row) return;
    
    const defaultValueInput = row.querySelector(`input[name="defaultValue_${prefix}_${index}"]`);
    const parameterName = document.getElementById('par_parametername').value;
    
    if (defaultValueInput) {
        if (fieldDescription === 'Data') {
            // Set Default Value to Parameter Name
            defaultValueInput.value = parameterName;
            defaultValueInput.classList.add('auto-calculated');
            defaultValueInput.readOnly = false;
        } else {
            // Clear Default Value
            defaultValueInput.value = '';
            defaultValueInput.classList.remove('auto-calculated');
            defaultValueInput.readOnly = false;
        }
    }
    
    // Also update the hidden field
    const hiddenField = row.querySelector(`input[name="${prefix}_defaultvalue_${index}"]`);
    if (hiddenField) {
        hiddenField.value = defaultValueInput ? defaultValueInput.value : '';
    }
}

// ======================================================
// NEW FUNCTION: Attach field description change handler
// ======================================================
function attachFieldDescriptionChangeHandler(row, prefix, index) {
    if (!row) return;
    
    const fieldDescSelect = row.querySelector(`select[name="fieldDescription_${prefix}_${index}"]`);
    if (fieldDescSelect) {
        if (!fieldDescSelect.__fieldDescriptionDefaultHandler) {
            fieldDescSelect.__fieldDescriptionDefaultHandler = function() {
                const nameParts = (this.getAttribute('name') || '').split('_');
                const currentIndex = parseInt(nameParts[nameParts.length - 1], 10) || index;
                updateDefaultValueBasedOnFieldDescription(this, prefix, currentIndex);
            };
            fieldDescSelect.addEventListener('change', fieldDescSelect.__fieldDescriptionDefaultHandler);
        }
        
        // Also trigger on initial load if value is already "Data"
        if (fieldDescSelect.value === 'Data') {
            fieldDescSelect.__fieldDescriptionDefaultHandler.call(fieldDescSelect);
        }
    }
}

// ======================================================
// NAVIGATION FUNCTIONS
// ======================================================

function showStep(step) {
    // Hide all steps
    document.querySelectorAll('.form-step').forEach(div => {
        div.classList.remove('active');
    });
    
    // Show current step
    const el = document.getElementById('step-' + step);
    if (el) {
        el.classList.add('active');
    }
    
    currentStep = step;
    updateProgressIndicator();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goToNextStep(step) {
    // 1. If we are on Step 1, find out how many commands the user wants
    if (step === 1) {
        totalCommandsToCreate = parseInt(document.getElementById('numberofpages').value) || 1;
        currentCommandSubStep = 1;
        prepareCommandFrame(currentCommandSubStep);
    }

    // 2. Logic for Step 2 (Command Frame)
    if (step === 2) {
        if (currentCommandSubStep < totalCommandsToCreate) {
            // Save current table data here if needed, then:
            currentCommandSubStep++;
            prepareCommandFrame(currentCommandSubStep);
            return; // EXIT HERE so we don't move to Step 3 yet
        }
    }

    // 3. Standard move to next main step (e.g. Step 2 to Step 3)
    document.getElementById(`step-${step}`).style.display = 'none';
    currentStep = step + 1;
    const nextStepEl = document.getElementById(`step-${currentStep}`);
    if (nextStepEl) nextStepEl.style.display = 'block';
    
    updateProgressIndicator();
}
function goToPreviousStep(step) {
    if (step === 2 && currentCommandSubStep > 1) {
        // Go to previous Command Frame
        currentCommandSubStep--;
        prepareCommandFramePage(currentCommandSubStep);
        return;
    }

    // Standard back transition
    document.getElementById(`step-${step}`).classList.remove('active');
    currentStep = step - 1;
    document.getElementById(`step-${currentStep}`).classList.add('active');
    updateProgressIndicator();
}


    function goToPreviousStep(step) {
    if (step === 2 && currentCommandSubStep > 1) {
        // Go to previous Command Frame
        currentCommandSubStep--;
        prepareCommandFramePage(currentCommandSubStep);
        return;
    }

    // Standard back transition
    document.getElementById(`step-${step}`).classList.remove('active');
    currentStep = step - 1;
    document.getElementById(`step-${currentStep}`).classList.add('active');
    updateProgressIndicator();
}

function prepareCommandFrame(index) {
    // Update the Title
    const title = document.querySelector('#step-2 h2');
    if (title) title.innerText = `COMMAND FRAME #${index}`;

    const tbody = document.getElementById('additionalTableBody');
    if (!tbody) return;

    // If table already has rows, DO NOTHING (keep user data)
    if (tbody.rows.length === 0) {
        // Only add rows if it's truly empty (first load)
        for (let i = 0; i < 2; i++) {
            addRow('cmd');
        }
    }

    // Only update counters, do not reset user data
    updateFrameCountsForPrefix('cmd');

    // Scroll to top so user sees the new header
    window.scrollTo(0, 0);
}



// Progress indicator
function updateProgressIndicator() {
    for (let i = 1; i <= totalSteps; i++) {
        const stepIndicator = document.getElementById('step-indicator-' + i);
        if (!stepIndicator) continue;
        
        stepIndicator.classList.remove('active', 'completed');
        
        if (i < currentStep) {
            stepIndicator.classList.add('completed');
        } else if (i === currentStep) {
            stepIndicator.classList.add('active');
        }
    }
}

// Update frame size and number of rows for a given prefix (cmd/resp/error)
function updateFrameCountsForPrefix(prefix) {
    const tableId = prefix === 'cmd' ? 'additionalTableBody' : 
                   (prefix === 'resp' ? 'responseTableBody' : 'errorTableBody');
    const tableBody = document.getElementById(tableId);
    const count = tableBody ? tableBody.rows.length : 0;

    // Helper to update both the .value and the HTML attribute
    const updateInput = (id) => {
        const el = document.getElementById(id);
        if (el) {
            el.value = count;
            el.setAttribute('value', count); // Mandatory for some form handlers
        }
    };

    if (prefix === 'cmd') {
        updateInput('cmd_framesizeinbytes');
        updateInput('cmd_noofrows');
    } else if (prefix === 'resp') {
        updateInput('resp_framesizeinbytes');
        updateInput('resp_noofrows');
    } else if (prefix === 'error') {
        updateInput('error_framesizeinbytes');
        updateInput('error_noofrows');
    }
}
// Frame size and rows management (user input of frame size will add/remove rows for cmd)
const frameSizeInput = document.getElementById('cmd_framesizeinbytes');
const noOfRowsInput = document.getElementById('cmd_noofrows');

if (frameSizeInput && noOfRowsInput) {
    frameSizeInput.addEventListener('input', () => {
        const frameSize = parseInt(frameSizeInput.value) || 0;
        noOfRowsInput.value = frameSize; // update read-only no of rows

        const tableBody = document.getElementById('additionalTableBody');
        if (!tableBody) return;
        const currentRows = tableBody.rows.length;

        // Add rows if frameSize > currentRows
        for (let i = currentRows; i < frameSize; i++) {
            addRow('cmd');
        }

        // Remove rows if frameSize < currentRows
        for (let i = currentRows; i > frameSize; i--) {
            tableBody.deleteRow(i - 1);
        }

        updateSerialNumbers('additionalTableBody');
        updateMoveButtons('additionalTableBody');
        updateFrameCountsForPrefix('cmd');
    });
}
// Inside your existing DOMContentLoaded listener
const proceedBtn = document.getElementById('proceedBtn'); // Your Step 1 button
if(proceedBtn) {
    proceedBtn.addEventListener('click', function() {
        const cmdCount = document.getElementById('numberofpages').value;
        console.log(`User requested ${cmdCount} command frames.`);
        currentCommandSubStep = 1; 
        prepareCommandFramePage(1);
    });
}

// Response frame size listener
const respFrameSizeInput = document.getElementById('resp_framesizeinbytes');
const respNoOfRowsInput = document.getElementById('resp_noofrows');

if (respFrameSizeInput && respNoOfRowsInput) {
    respFrameSizeInput.addEventListener('input', () => {
        const frameSize = parseInt(respFrameSizeInput.value) || 0;
        respNoOfRowsInput.value = frameSize;

        const tableBody = document.getElementById('responseTableBody');
        if (!tableBody) return;
        const currentRows = tableBody.rows.length;

        // Add rows if frameSize > currentRows
        for (let i = currentRows; i < frameSize; i++) {
            addRow('resp');
        }

        // Remove rows if frameSize < currentRows
        for (let i = currentRows; i > frameSize; i--) {
            tableBody.deleteRow(i - 1);
        }

        updateSerialNumbers('responseTableBody');
        updateMoveButtons('responseTableBody');
        updateFrameCountsForPrefix('resp');
    });
}

// Error frame size listener
const errorFrameSizeInput = document.getElementById('error_framesizeinbytes');
const errorNoOfRowsInput = document.getElementById('error_noofrows');

if (errorFrameSizeInput && errorNoOfRowsInput) {
    errorFrameSizeInput.addEventListener('input', () => {
        const frameSize = parseInt(errorFrameSizeInput.value) || 0;
        errorNoOfRowsInput.value = frameSize;

        const tableBody = document.getElementById('errorTableBody');
        if (!tableBody) return;
        const currentRows = tableBody.rows.length;

        // Add rows if frameSize > currentRows
        for (let i = currentRows; i < frameSize; i++) {
            addRow('error');
        }

        // Remove rows if frameSize < currentRows
        for (let i = currentRows; i > frameSize; i--) {
            tableBody.deleteRow(i - 1);
        }

        updateSerialNumbers('errorTableBody');
        updateMoveButtons('errorTableBody');
        updateFrameCountsForPrefix('error');
    });
}

// ======================================================
// MAIN AUTO-CALCULATION FUNCTION
// ======================================================

function setByteRangeWarning(startByteInput, endByteInput, message) {
    [startByteInput, endByteInput].forEach(function (input) {
        if (!input) return;
        input.classList.toggle('byte-range-invalid', Boolean(message));
        if (message) {
            input.setAttribute('aria-invalid', 'true');
            input.setAttribute('title', message);
        } else {
            input.removeAttribute('aria-invalid');
            input.removeAttribute('title');
        }
    });

    if (!endByteInput || !endByteInput.parentElement) return;
    let warning = endByteInput.parentElement.querySelector('.byte-range-warning');
    if (!warning) {
        warning = document.createElement('div');
        warning.className = 'byte-range-warning';
        endByteInput.insertAdjacentElement('afterend', warning);
    }
    warning.textContent = message || '';
    warning.classList.toggle('is-visible', Boolean(message));
}

function calculateByteValues(row, startByteInput, endByteInput, prefix, index, forceDisplay = false) {
    const startRaw = startByteInput ? startByteInput.value.trim() : '';
    const endRaw = endByteInput ? endByteInput.value.trim() : '';
    const startByte = startRaw === '' ? null : parseInt(startRaw, 10);
    const endByte = endRaw === '' ? null : parseInt(endRaw, 10);
    const hasBothValues = startByte !== null && endByte !== null && !Number.isNaN(startByte) && !Number.isNaN(endByte);
    const rangeError = hasBothValues && (startByte < 0 || endByte <= startByte);
    const byteRangeMessage = rangeError ? 'End byte must be greater than start byte. Example: start 0, end 1.' : '';
    setByteRangeWarning(startByteInput, endByteInput, byteRangeMessage);

    const numberOfBytes = hasBothValues && !rangeError ? (endByte - startByte) + 1 : 0;
    const bitSize = numberOfBytes * 8;

    // --- ALWAYS UPDATE THESE (Sizes and Hex) ---
    const noBytesIn = row.querySelector(`input[name="noOfBytes_${prefix}_${index}"]`);
    const bitSizeIn = row.querySelector(`input[name="bitSize_${prefix}_${index}"]`);
    const hMinIn = row.querySelector(`input[name="hexMin_${prefix}_${index}"]`);
    const hMaxIn = row.querySelector(`input[name="hexMax_${prefix}_${index}"]`);

    if (noBytesIn) noBytesIn.value = numberOfBytes;
    if (bitSizeIn) bitSizeIn.value = bitSize;

    if (numberOfBytes > 0) {
        if (hMinIn) hMinIn.value = `0x${"0".repeat(numberOfBytes * 2)}`;
        if (hMaxIn) {
            const maxDecimal = Math.pow(256, numberOfBytes) - 1;
            hMaxIn.value = `0x${maxDecimal.toString(16).toUpperCase().padStart(numberOfBytes * 2, "0")}`;
        }
    } else if (rangeError) {
        if (hMinIn) hMinIn.value = '';
        if (hMaxIn) hMaxIn.value = '';
    }

    // --- UPDATE DISPLAY MIN/MAX ONLY IF forceDisplay IS TRUE ---
    const dataTypeSelect = row.querySelector(`select[name="dataType_${prefix}_${index}"]`);
    const dMinIn = row.querySelector(`input[name="displayMin_${prefix}_${index}"]`);
    const dMaxIn = row.querySelector(`input[name="displayMax_${prefix}_${index}"]`);
    let displayMin = dMinIn ? dMinIn.value : 0;
    let displayMax = dMaxIn ? dMaxIn.value : 0;

    if (forceDisplay && dataTypeSelect) {
        const dataType = dataTypeSelect.value;
        displayMin = 0;
        displayMax = 0;

        // Math for Min/Max
        switch (dataType) {
            case "UnsignedInt": displayMax = Math.pow(2, bitSize) - 1; break;
            case "SignedInt": 
                const half = Math.pow(2, bitSize - 1);
                displayMin = -half; displayMax = half - 1; 
                break;
            case "Float": displayMin = 0; displayMax = 100; break;
            default: displayMax = Math.pow(2, bitSize) - 1;
        }

        if (dMinIn) dMinIn.value = displayMin;
        if (dMaxIn) dMaxIn.value = displayMax;

        // Sync hidden fields for Min/Max
        syncHiddenField(prefix, 'displayValueMin', index, displayMin);
        syncHiddenField(prefix, 'displayValueMax', index, displayMax);
    }


   // Calculate hex values
    let hexMinValue = "0x00";
    let hexMaxValue = "0x00";
    
   if (typeof hexMinInput !== 'undefined' && hexMinInput && numberOfBytes > 0) {

    // Hex MIN = 0x + (2 zeros per byte)
    const zeroCount = numberOfBytes * 2;
    const hexZeros = "0".repeat(zeroCount);

    hexMinValue = `0x${hexZeros}`;
    hexMinInput.value = hexMinValue;

    }
    
    if (typeof hexMaxInput !== 'undefined' && hexMaxInput && numberOfBytes > 0) {

    // Max value = (256 ^ numberOfBytes) - 1
    const maxDecimal = Math.pow(256, numberOfBytes) - 1;

    let hexString = maxDecimal
        .toString(16)
        .toUpperCase()
        .padStart(numberOfBytes * 2, "0");


    hexMaxValue = `0x${hexString}`;
    hexMaxInput.value = hexMaxValue;
}

    // ======================================================
    // UPDATE HIDDEN FIELDS
    // ======================================================
    
    // Get other values from the row
    const fieldDescSelect = row.querySelector(`select[name="fieldDescription_${prefix}_${index}"]`);
    const fieldDescVal = fieldDescSelect ? fieldDescSelect.value : "None";
    
    const dataTypeVal = dataTypeSelect ? dataTypeSelect.value : "None";
    
    const valueDefSelect = row.querySelector(`select[name="valueDefinition_${prefix}_${index}"]`);
    const valueDefVal = valueDefSelect ? valueDefSelect.value : "None";
    
    const uiTypeSelect = row.querySelector(`select[name="uiType_${prefix}_${index}"]`);
    const uiTypeVal = uiTypeSelect ? uiTypeSelect.value : "None";
    
    const uiIdInput = row.querySelector(`input[name="uiId_${prefix}_${index}"]`);
    const uiIdVal = uiIdInput ? uiIdInput.value : "";
    
    const asperUiInput = row.querySelector(`input[name="asperui_${prefix}_${index}"]`);
    const asperUiVal = asperUiInput ? asperUiInput.value : "";
    
    const snoInput = row.querySelector(`input[name="sno_asperui_${prefix}_${index}"]`);
    const snoVal = snoInput ? snoInput.value : "";
    
    const asperValueInput = row.querySelector(`input[name="asperuivalue_${prefix}_${index}"]`);
    const asperValueVal = asperValueInput ? asperValueInput.value : "";
    
    const uiPageInput = row.querySelector(`input[name="uiPageNo_${prefix}_${index}"]`);
    const uiPageVal = uiPageInput ? uiPageInput.value : "";

// 1. Target the correct input (ensure name is lowercase)
// 1. Use lowercase 'uiinout' to match the HTML
// MUST use lowercase 'uiinout' to match your HTML
const uiInOutInput = row.querySelector(`input[name="uiinout_${prefix}_${index}"]`);
let uiInOutVal = (uiInOutInput && uiInOutInput.value.trim() !== "") ? uiInOutInput.value.trim() : "None";

// If the system thinks it's "1" or empty, don't let it save as "None" 
// if we already have data.
if (uiInOutVal === "1" || uiInOutVal === index.toString() || uiInOutVal === "") {
    uiInOutVal = "None";
}    
    const sliderStepInput = row.querySelector(`input[name="sliderStep_${prefix}_${index}"]`);
    const sliderStepVal = sliderStepInput ? sliderStepInput.value : "";
    
    const defaultValueInput = row.querySelector(`input[name="defaultValue_${prefix}_${index}"]`);
    const defaultValueVal = defaultValueInput ? defaultValueInput.value : "";
    
    const devNotesInput = row.querySelector(`input[name="developerNotes_${prefix}_${index}"]`);
    const devNotesVal = devNotesInput ? devNotesInput.value : "";

    // Define ALL hidden fields that should exist
    const allHiddenFields = [
        {name: `${prefix}_startBytePosition_${index}`, value: startByte ?? ""},
        {name: `${prefix}_endBytePosition_${index}`, value: endByte ?? ""},
        {name: `${prefix}_numberOfBytes_${index}`, value: numberOfBytes},
        {name: `${prefix}_bitSize_${index}`, value: bitSize},
        {name: `${prefix}_displayValueMin_${index}`, value: displayMin},
        {name: `${prefix}_displayValueMax_${index}`, value: displayMax},
        {name: `${prefix}_valueInHexMin_${index}`, value: hexMinValue},
        {name: `${prefix}_valueInHexMax_${index}`, value: hexMaxValue},
        {name: `${prefix}_fieldDescription_${index}`, value: fieldDescVal},
        {name: `${prefix}_dataType_${index}`, value: dataTypeVal},
        {name: `${prefix}_valueDefinition_${index}`, value: valueDefVal},
        {name: `${prefix}_uiType_${index}`, value: uiTypeVal},
        {name: `${prefix}_uiId_${index}`, value: uiIdVal},
        {name: `${prefix}_asperui_${index}`, value: asperUiVal},
        {name: `${prefix}_sno_asperui_${index}`, value: snoVal},
        {name: `${prefix}_asperuivalue_${index}`, value: asperValueVal},
        {name: `${prefix}_uiPageNo_${index}`, value: uiPageVal},
        {name: `${prefix}_uiDescriptionInOut_${index}`, value: uiInOutVal},
        {name: `${prefix}_sliderStepValue_${index}`, value: sliderStepVal},
        {name: `${prefix}_defaultvalue_${index}`, value: defaultValueVal},
        {name: `${prefix}_developerNotes_${index}`, value: devNotesVal}
          
    ];

    

    // Create or update ALL hidden fields
    // Create or update ALL hidden fields

allHiddenFields.forEach(field => {
    let hiddenField = row.querySelector(`input[name="${field.name}"]`);
    
    if (!hiddenField) {
        hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = field.name;
        hiddenField.value = field.value;
        row.appendChild(hiddenField);
    } else {
        // --- MULTI-FIELD SAFE GUARD ---
        const isProtected = field.name.includes("fieldDescription") || 
                            field.name.includes("uiId") || 
                            field.name.includes("uiDescriptionInOut");

        const hasExistingValue = hiddenField.value !== "" && hiddenField.value !== "None";
        const newValueIsEmpty = field.value === "" || field.value === "None" || field.value === undefined;

        if (isProtected && hasExistingValue && newValueIsEmpty) {
            console.log(`[PROTECTED] Prevented ${field.name} from being cleared.`);
        } else {
            hiddenField.value = field.value;
        }
    }
});


    // Final verification
    const verifyFields = [
        `${prefix}_numberOfBytes`,
        `${prefix}_bitSize`
    ];
    
    console.log(`[CALC] Final verification:`);
    verifyFields.forEach(fieldName => {
        const field = row.querySelector(`input[name="${fieldName}"]`);
        console.log(`[CALC]   ${fieldName}: ${field ? `"${field.value}"` : 'MISSING!'}`);
    });
  printHiddenFields(row, prefix, index);
}

// Helper function to create or update hidden field
// 1. The Helper (Stays separate)
function createOrUpdateHiddenInput(row, name, value) {
    let hiddenInput = row.querySelector(`input[name="${name}"]`);
    if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = name;
        row.appendChild(hiddenInput);
    }
    hiddenInput.value = value;
    return hiddenInput;
}

// 2. The Logic (Inside your main calculation function)
function printHiddenFields(row, prefix, index) {
     console.log('🔥 printHiddenFields CALLED');
    const hiddenInputs = row.querySelectorAll('input[type="hidden"]');

    if (!hiddenInputs.length) {
        console.warn(`No hidden fields found for ${prefix} row ${index}`);
        return;
    }

    const data = {};

    hiddenInputs.forEach(input => {
        data[input.name] = input.value;
    });

    console.group(`Hidden fields -> ${prefix} row ${index}`);
    console.table(data);
    console.groupEnd();
}

// ======================================================
// Setup auto-calculation for a row
// ======================================================
function setupByteCalculation(row, prefix, index) {
    if (!row) return;

    const startIn = row.querySelector(`input[name="startBytePosition_${prefix}_${index}"]`);
    const endIn = row.querySelector(`input[name="endBytePosition_${prefix}_${index}"]`);
    const dataSel = row.querySelector(`select[name="dataType_${prefix}_${index}"]`);

    // When typing Bytes: Pass FALSE (Keeps Min/Max empty)
    const onByteInput = () => calculateByteValues(row, startIn, endIn, prefix, index, false);
    
    // When changing Data Type: Pass TRUE (Shows Min/Max)
    const onTypeChange = () => calculateByteValues(row, startIn, endIn, prefix, index, true);

    startIn.addEventListener('input', onByteInput);
    endIn.addEventListener('input', onByteInput);
    
    if (dataSel) {
        dataSel.addEventListener('change', onTypeChange);
    }
}




// ======================================================
// AUTO-FILL BASED ON UI TYPE
// ======================================================
function applyUIAutoFill(selectElement, prefix, index) {
    if (!selectElement) return;

    const uiType = (selectElement.value || '').toLowerCase();
    const row = selectElement.closest('tr');
    if (!row) return;

    // --- Selectors ---
    const asperUiInput = row.querySelector(`input[name="asperui_${prefix}_${index}"]`);
    const uiValuesInput = row.querySelector(`input[name="asperuivalue_${prefix}_${index}"]`);
    const uiSerialInput = row.querySelector(`input[name="sno_asperui_${prefix}_${index}"]`);
    const valueDefSelect = row.querySelector(`select[name="valueDefinition_${prefix}_${index}"]`);
    const dataTypeSelect = row.querySelector(`select[name="dataType_${prefix}_${index}"]`);
    const uiInOutInput = row.querySelector(`input[name="uiinout_${prefix}_${index}"]`);

    if (!asperUiInput || !uiValuesInput || !uiSerialInput || !valueDefSelect || !dataTypeSelect) return;

    const setVal = (el, val) => el && (el.value = val);

    // --- Mapping logic for different UI Types ---
    if (uiType === 'toggle switch') {
        setVal(valueDefSelect, 'Fixed Value');
        setVal(dataTypeSelect, 'UnsignedInt');
        setVal(asperUiInput, 'false,true');
        setVal(uiValuesInput, '00,01');
        setVal(uiSerialInput, '1');
        setVal(uiInOutInput, 'Read/Write');
    } 
    else if (uiType.includes('slider') || uiType === 'hue circle') {
        setVal(valueDefSelect, 'Range Value');
        setVal(dataTypeSelect, 'UnsignedInt');
        setVal(asperUiInput, '0,0');
        setVal(uiValuesInput, '1');
        setVal(uiSerialInput, '1');
        setVal(uiInOutInput, 'Read/Write');
    } 
    else if (uiType === 'console' || uiType === 'entertext') {
        setVal(valueDefSelect, 'Fixed Value');
        setVal(dataTypeSelect, 'String');
        setVal(asperUiInput, '1');
        setVal(uiValuesInput, '1');
        setVal(uiSerialInput, '1');
        setVal(uiInOutInput, 'Read/Write');
    } 
    else if (uiType === 'dropdown') {
        setVal(valueDefSelect, 'Fixed Value');
        setVal(dataTypeSelect, 'UnsignedInt');
        setVal(asperUiInput, 'Option1,Option2'); // Default placeholder
        setVal(uiValuesInput, '00,01');
        setVal(uiSerialInput, '1');
        setVal(uiInOutInput, 'Read/Write');
    } 
    else if (uiType === 'trigger' || uiType === 'button big') {
        setVal(valueDefSelect, 'Fixed Value');
        setVal(dataTypeSelect, 'UnsignedInt');
        setVal(asperUiInput, 'true');
        setVal(uiValuesInput, '01');
        setVal(uiSerialInput, '1');
        setVal(uiInOutInput, 'Write');
    } 
    else {
        // Default for "None" or others
        setVal(asperUiInput, '1');
        setVal(uiValuesInput, '1');
        setVal(uiSerialInput, '1');
        setVal(uiInOutInput, 'None');
    }

    // --- Sync Hidden Fields (Ensures DB gets the data) ---
    syncHiddenField(prefix, 'asperui', index, asperUiInput.value);
    syncHiddenField(prefix, 'asperuivalue', index, uiValuesInput.value);
    syncHiddenField(prefix, 'sno_asperui', index, uiSerialInput.value);
    syncHiddenField(prefix, 'valueDefinition', index, valueDefSelect.value);
    syncHiddenField(prefix, 'dataType', index, dataTypeSelect.value);
    if (uiInOutInput) syncHiddenField(prefix, 'uiDescriptionInOut', index, uiInOutInput.value);

    // --- Trigger Min/Max Calculation ---
    const startIn = row.querySelector(`input[name="startBytePosition_${prefix}_${index}"]`);
    const endIn = row.querySelector(`input[name="endBytePosition_${prefix}_${index}"]`);
    if (startIn && endIn) {
        // We pass TRUE to force displayMin/Max to show up for these UI types
        calculateByteValues(row, startIn, endIn, prefix, index, true);
    }
}

/**
 * UPDATED HELPER: Blocks the "1" from overwriting text
 */

function syncHiddenField(prefix, fieldKey, index, value) {
    const hiddenName = `${prefix}_${fieldKey}_${index}`;
    const hiddenInput = document.getElementsByName(hiddenName)[0];
    
    if (!hiddenInput) return;

    if (fieldKey === 'uiDescriptionInOut') {
        const newVal = String(value).trim();

        if (!['None', 'Read', 'Write', 'Read/Write'].includes(newVal)) {
            return;
        }
    }

    hiddenInput.value = value;
    hiddenInput.setAttribute('value', value);
}

/**
 * INSTANT SYNC: Captures typing immediately
 */

document.addEventListener('input', function (e) {
    if (e.target && e.target.name && e.target.name.startsWith('uiinout_')) {
        const parts = e.target.name.split('_'); 
        const prefix = parts[1];
        const index = parts[2];
        let val = e.target.value.trim();

        // Standardize the input
        if (val.toLowerCase() === 'read') val = 'Read';
        if (val.toLowerCase() === 'write') val = 'Write';
        if (val.toLowerCase() === 'read/write') val = 'Read/Write';

        // If it's empty or the index number, it's 'None'
        if (val === "" || val === "1" || val === index.toString()) {
            val = "None";
        }

        syncHiddenField(prefix, 'uiDescriptionInOut', index, val);
    }
});
// Handle field description changes
function handleFieldDescriptionChange(selectElement, prefix, index) {
    if (!selectElement) return;
    const fieldDesc = selectElement.value;
    const row = selectElement.closest('tr');
    
    // Always sync the Field Description itself first
    syncHiddenField(prefix, 'fieldDescription', index, fieldDesc);

    if (fieldDesc === 'Terminator') {
        const dataTypeSelect = row.querySelector(`select[name="dataType_${prefix}_${index}"]`);
        const valueDefSelect = row.querySelector(`select[name="valueDefinition_${prefix}_${index}"]`);
        
        if (dataTypeSelect) {
            dataTypeSelect.value = 'None';
            dataTypeSelect.classList.add('auto-calculated');
            // FIX: Pass the index so it finds "resp_dataType_1"
            syncHiddenField(prefix, 'dataType', index, 'None');
        }
        
        if (valueDefSelect) {
            valueDefSelect.value = 'None';
            valueDefSelect.classList.add('auto-calculated');
            // FIX: Pass the index so it finds "resp_valueDefinition_1"
            syncHiddenField(prefix, 'valueDefinition', index, 'None');
        }
        
        // Trigger calculation to update hidden fields for bytes
        const startInput = row.querySelector(`input[name="startBytePosition_${prefix}_${index}"]`);
        const endInput = row.querySelector(`input[name="endBytePosition_${prefix}_${index}"]`);
        if (startInput && endInput) {
            calculateByteValues(row, startInput, endInput, prefix, index);
        }
    }
}

// Optimized helper to ensure API fields are updated
function syncHiddenField(prefix, fieldKey, index, value) {
    const hiddenName = `${prefix}_${fieldKey}_${index}`;
    const hiddenInput = document.getElementsByName(hiddenName)[0];
    
    if (hiddenInput) {
        // --- MULTI-ROW PROTECTION GATE ---
        if (fieldKey === 'uiDescriptionInOut') {
            const currentVal = hiddenInput.value; // What is already saved
            const newVal = String(value).trim();  // What the script is trying to save

            // If the row already has "Read" or "Write", 
            // and an automated script tries to set it to "None", "1", or "", BLOCK IT.
            const hasRealData = ["Read", "Write", "Read/Write"].includes(currentVal);
            const incomingIsBlank = ["None", "1", "", index.toString()].includes(newVal);

            if (hasRealData && incomingIsBlank) {
                // Ignore the update to keep the user's data safe
                return; 
            }
        }

        hiddenInput.value = value;
        hiddenInput.setAttribute('value', value);
}
}

// Attach UI change handler to row

function attachUIChangeHandlerForRow(row, prefix, index) {
    const uiSelect = row.querySelector(`select[name="uiType_${prefix}_${index}"]`);
    
    if (uiSelect) {
        uiSelect.addEventListener('change', function() {
            // 1. Get the current value from the hidden field IMMEDIATELY
            const hiddenInput = document.querySelector(`input[name="${prefix}_uiDescriptionInOut_${index}"]`);
            const currentValue = hiddenInput ? hiddenInput.value : "";

            // 2. Pass that value into the UI generator
            applyUIAutoFill(this, prefix, index, currentValue);
        });
    }
}
// Attach field description handler
function attachFieldDescriptionHandler(row, prefix, index) {
    if (!row) return;
    const fieldDescSelect = row.querySelector(`select[name="fieldDescription_${prefix}_${index}"]`);
    if (fieldDescSelect) {
        fieldDescSelect.setAttribute('onchange', `handleFieldDescriptionChange(this, '${prefix}', ${index})`);
        if (fieldDescSelect.value === 'Terminator') {
            handleFieldDescriptionChange(fieldDescSelect, prefix, index);
        }
    }
}

// Attach UI change validation handler
function attachUIChangeValidationHandler(row, prefix, index) {
    if (!row) return;
    const uiSelect = row.querySelector(`select[name="uiType_${prefix}_${index}"]`);
    const asperUiInput = row.querySelector(`input[name="asperui_${prefix}_${index}"]`);
    const uiValuesInput = row.querySelector(`input[name="asperuivalue_${prefix}_${index}"]`);
    const uiSerialInput = row.querySelector(`input[name="sno_asperui_${prefix}_${index}"]`);
    
    if (uiSelect && asperUiInput && uiValuesInput && uiSerialInput) {
        uiSelect.addEventListener('change', function() {
            const normalized = (this.value || '').toString().trim().toLowerCase();
            if (normalized === 'drop down' || normalized === 'notification') {
                asperUiInput.required = true;
                uiValuesInput.required = true;
                uiSerialInput.required = true;
            } else {
                asperUiInput.required = false;
                uiValuesInput.required = false;
                uiSerialInput.required = false;
            }
        });
    }
}

// ======================================================
// TABLE MANAGEMENT FUNCTIONS
// ======================================================
function addRow(type) {
    console.log(`[TABLE] Adding row to ${type} table`);
    
    const tableConfigs = {
        'cmd': { tableId: 'additionalTableBody', prefix: 'cmd' },
        'resp': { tableId: 'responseTableBody', prefix: 'resp' },
        'error': { tableId: 'errorTableBody', prefix: 'error' }
    };
    
    const config = tableConfigs[type];
    if (!config) return;
    
    const tableBody = document.getElementById(config.tableId);
    if (!tableBody) return;
    const rowCount = tableBody.rows.length;
    const newRow = tableBody.insertRow();
    
    newRow.innerHTML = getTableRowHTML(rowCount + 1, config.prefix);
    
    // Setup auto-calculation for the new row
    setupByteCalculation(newRow, config.prefix, rowCount + 1);
    
    // Attach UI onchange handler and run initial fill if needed
    attachUIChangeHandlerForRow(newRow, config.prefix, rowCount + 1);
    attachFieldDescriptionHandler(newRow, config.prefix, rowCount + 1);
    attachUIChangeValidationHandler(newRow, config.prefix, rowCount + 1);
    attachFieldDescriptionChangeHandler(newRow, config.prefix, rowCount + 1); // NEW: Add field description change handler
    
    updateSerialNumbers(config.tableId);
    updateMoveButtons(config.tableId);

    // Update frame counts
    updateFrameCountsForPrefix(config.prefix);
    
    console.log(`[TABLE] Added row ${rowCount + 1} to ${type} table`);
}



/*function removeRow(button, type) {
    const row = button.closest('tr');
    if (!row) return;
    const tableBody = row.parentNode;
    row.remove();
    
    const tableId = tableBody ? tableBody.id : null;
    if (tableId) {
        updateSerialNumbers(tableId);
        updateMoveButtons(tableId);

        const prefix = tableId === 'additionalTableBody' ? 'cmd' : 
                      (tableId === 'responseTableBody' ? 'resp' : 'error');
        updateSerialNumbers(tableBodyId);
        updateFrameCountsForPrefix(prefix);
    }
}*/

function removeRow(button, type) {
    console.log(`[TABLE] Removing row from ${type} table`);
    
    const row = button.closest('tr');
    if (!row) return;
    
    const tableConfigs = {
        'cmd': { tableId: 'additionalTableBody', prefix: 'cmd', countId: 'cmd_framesizeinbytes' },
        'resp': { tableId: 'responseTableBody', prefix: 'resp', countId: 'resp_framesizeinbytes' },
        'error': { tableId: 'errorTableBody', prefix: 'error', countId: 'error_framesizeinbytes' }
    };
    
    const config = tableConfigs[type];
    
    // 1. Remove all hidden fields in this row
    const hiddenInputs = row.querySelectorAll('input[type="hidden"]');
    hiddenInputs.forEach(hidden => hidden.remove());
    
    // 2. Remove the row from DOM
    row.remove();
    
    if (config) {
        // 3. Re-index remaining rows so S.No and Names are correct
        updateSerialNumbers(config.tableId);
        
        // 4. Update the actual Input Value (Force decrease)
        const tableBody = document.getElementById(config.tableId);
        const currentCount = tableBody.rows.length;
        
        const countInput = document.getElementById(config.countId);
        const noOfRowsInput = document.getElementById(type + '_noofrows');

        if (countInput) countInput.value = currentCount;
        if (noOfRowsInput) noOfRowsInput.value = currentCount;

        // 5. Run your existing count update logic
        updateFrameCountsForPrefix(type);
    }
    
    console.log(`[TABLE] Row removed. New count for ${type}: ${document.getElementById(config.tableId).rows.length}`);
}
function clearTable(type) {
    const tableConfigs = {
        'cmd': { tableId: 'additionalTableBody' },
        'resp': { tableId: 'responseTableBody' },
        'error': { tableId: 'errorTableBody' }
    };
    
    const config = tableConfigs[type];
    if (!config) return;
    
    const tableBody = document.getElementById(config.tableId);
    if (!tableBody) return;
    tableBody.innerHTML = '';

    updateFrameCountsForPrefix(config.tableId === 'additionalTableBody' ? 'cmd' : 
                              (config.tableId === 'responseTableBody' ? 'resp' : 'error'));
}


function updateSerialNumbers(tableBodyId) {
    const tableBody = document.getElementById(tableBodyId);
    if (!tableBody) return;
    
    const prefix = tableBodyId.includes('additional') ? 'cmd' : 
                   tableBodyId.includes('response') ? 'resp' : 'error';
    
    const rows = tableBody.rows;
    for (let i = 0; i < rows.length; i++) {
        const serialNumber = i + 1;
        const row = rows[i];
        
        // CHECK: Is this a newly inserted row?
        const isNew = row.getAttribute('data-is-new') === 'true';
        
        // 1. Update visual S.No (Column 0)
        if (row.cells && row.cells[0]) {
            const snoNumber = row.cells[0].querySelector('.sno-number');
            if (snoNumber) {
                snoNumber.textContent = serialNumber;
            } else {
                row.cells[0].innerHTML = `<label class="row-select-wrap" title="Select row"><span class="sno-number">${serialNumber}</span><input type="checkbox" class="row-select-checkbox" onchange="toggleRowSelection(this)" aria-label="Select row ${serialNumber}"><span class="row-check-ui" aria-hidden="true"></span></label>`;
            }
        }
        
        // 2. Update UI ID (Toggle Logic)
        const uiIdInput = row.querySelector('.ui-id-field');
        if (uiIdInput) {
            const userChoice = uiIdInput.getAttribute('data-user-choice');
            if (userChoice === 'none') {
                uiIdInput.value = "None";
            } else {
                uiIdInput.value = serialNumber;
            }
            uiIdInput.setAttribute('onclick', `this.value = (this.value === 'None' ? ${serialNumber} : 'None'); this.setAttribute('data-user-choice', this.value === 'None' ? 'none' : 'id');`);
        }
        
        // 3. Rename attributes (Required for form submission)
        // We ALWAYS do this so the names like field_1, field_2 stay sequential
        if (typeof updateRowIndex === 'function') {
            updateRowIndex(row, prefix, serialNumber);
        }
        
        // 4. Re-bind handlers ONLY if it's the brand-new row
        // This prevents the "deleting" issue on existing rows
        if (isNew) {
            if (typeof setupByteCalculation === 'function') setupByteCalculation(row, prefix, serialNumber);
            if (typeof attachUIChangeHandlerForRow === 'function') attachUIChangeHandlerForRow(row, prefix, serialNumber);
            if (typeof attachFieldDescriptionHandler === 'function') attachFieldDescriptionHandler(row, prefix, serialNumber);
            if (typeof attachUIChangeValidationHandler === 'function') attachUIChangeValidationHandler(row, prefix, serialNumber);
            if (typeof attachFieldDescriptionChangeHandler === 'function') attachFieldDescriptionChangeHandler(row, prefix, serialNumber);
            
            // Remove the tag so it's treated as an "existing" row next time
            row.removeAttribute('data-is-new');
        }
    }

    if (typeof updateFrameCountsForPrefix === 'function') {
        updateFrameCountsForPrefix(prefix);
    }
}    


function updateRowIndex(row, prefix, newIndex) {
    if (!row) return;

    const inputs = row.querySelectorAll('input, select, textarea');
    const visibleData = {};

    // 1. CAPTURE data using the suffix-agnostic key
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        if (name && name.includes(`_${prefix}_`)) {
            // If name is "uiDescription_resp_1", key becomes "uidescription"
            const key = name.split(`_${prefix}_`)[0].toLowerCase(); 
            visibleData[key] = input.value;
        }
    });

    // 2. UPDATE Names and IDs
    inputs.forEach(input => {
        const oldName = input.getAttribute('name');
        if (!oldName) return;

        // Correctly replace the index at the end (e.g., name_1 to name_2)
        const newName = oldName.replace(/_\d+$/, `_${newIndex}`);
        input.setAttribute('name', newName);
        input.id = newName;

        // 3. SYNC values safely
        if (newName.startsWith(prefix)) {
            const fieldKey = newName.split('_')[1].toLowerCase();
            
            let foundValue = "";
            if (fieldKey === "uidescriptioninout") {
                foundValue = visibleData["uidescriptioninout"] || visibleData["uiinout"] || visibleData["uidescription"];
            } else if (fieldKey === "sliderstepvalue") {
                foundValue = visibleData["sliderstepvalue"] || visibleData["sliderstep"];
            } else {
                foundValue = visibleData[fieldKey];
            }

            // ONLY overwrite if we actually found data in visibleData
            // This prevents the "auto-delete" behavior
            if (foundValue !== undefined && foundValue !== null) {
                input.value = foundValue;
                input.setAttribute('value', foundValue);
            }
        } else {
            // For non-prefix fields (visible fields), just ensure the attribute matches the current value
            input.setAttribute('value', input.value);
        }
    });
}    

function removeRow(button, prefix) {
    const row = button.closest('tr');
    const tableBody = row.parentNode;
    const tableBodyId = tableBody.id;
    row.remove();
    updateSerialNumbers(tableBodyId);
}
function insertRowBelow(button, prefix) {
    const row = button.closest("tr");
    const tableBody = row.parentNode;
    const tableId = tableBody.id;
    const rows = Array.from(tableBody.rows);
    const rowIndex = rows.indexOf(row);

    const allStates = snapshotAllRows(tableId);

    const newRow = tableBody.insertRow(rowIndex + 1);
    // TAG THIS ROW AS NEW
    newRow.setAttribute('data-is-new', 'true'); 
    
    const nextIndex = rowIndex + 2;
    newRow.innerHTML = getTableRowHTML(nextIndex, prefix);

    restoreAllRows(tableId, allStates, rowIndex + 1);
    updateSerialNumbers(tableId);
}
function restoreAllRows(tableBodyId, states, insertIndex) {
    const tableBody = document.getElementById(tableBodyId);
    let stateIdx = 0;

    Array.from(tableBody.rows).forEach((row, currentRowIdx) => {
        // Skip the newly inserted row - it should stay empty/default
        if (currentRowIdx === insertIndex) return;

        const rowData = states[stateIdx++];
        if (!rowData) return;

        const inputs = row.querySelectorAll('input, select, textarea');
        inputs.forEach((el, inputIdx) => {
            // Restore by the order of inputs rather than the name attribute
            // because the name attribute was just changed by updateSerialNumbers
            if (rowData[inputIdx] !== undefined) {
                el.value = rowData[inputIdx];
            }
        });
    });
}
function snapshotAllRows(tableBodyId) {
    const tableBody = document.getElementById(tableBodyId);
    return Array.from(tableBody.rows).map(row => {
        return Array.from(row.querySelectorAll('input, select, textarea')).map(el => el.value);
    });
}

function resetRowToDefault(row) {
    row.querySelectorAll("input, select").forEach(el => {
        if (el.type === "hidden") return;
        if (el.classList.contains("ui-id-field")) return;

        // Reset value
        if (el.tagName === "SELECT") {
            el.selectedIndex = 0;
        } else {
            el.value = "";
        }

        // Reset disabled state
        el.disabled = false;
    });
}


// ======================================================
// TABLE ROW HTML GENERATOR
// ======================================================
function getTableRowHTML(index, prefix) {
    return `
        <tr>
            <td class="sno-column">
                <label class="row-select-wrap" title="Select row">
                    <span class="sno-number">${index}</span>
                    <input type="checkbox" class="row-select-checkbox" onchange="toggleRowSelection(this)" aria-label="Select row ${index}">
                    <span class="row-check-ui" aria-hidden="true"></span>
                </label>
            </td>

            <!-- Start Byte -->
            <td>
                <input type="number"
                       name="startBytePosition_${prefix}_${index}"
                       class="compact-input"
                       min="0"
                       placeholder="e.g., 0"
                       oninput="calculateByteValues(this.closest('tr'), this,
                                this.closest('tr').querySelector('input[name=\\'endBytePosition_${prefix}_${index}\\']'),
                                '${prefix}', ${index})">
            </td>

            <!-- End Byte -->
            <td>
                <input type="number"
                       name="endBytePosition_${prefix}_${index}"
                       class="compact-input"
                       min="0"
                       placeholder="e.g., 1"
                       oninput="calculateByteValues(this.closest('tr'),
                                this.closest('tr').querySelector('input[name=\\'startBytePosition_${prefix}_${index}\\']'),
                                this,
                                '${prefix}', ${index})">
                <div class="byte-range-warning" aria-live="polite"></div>
            </td>

            <!-- Field Description + Bitwise -->
            <td>
                <div class="field-desc-inline">

                    <select name="fieldDescription_${prefix}_${index}"
                            class="compact-select field-description"
                            onchange="handleFieldDescriptionChange(this, '${prefix}', ${index})">
                        <option value="">Select</option>
                        <option value="Header">Header</option>
                        <option value="Data">Data</option>
                        <option value="Payload">Payload</option>
                        <option value="Checksum">Checksum</option>
                        <option value="Terminator">Terminator</option>
                        <option value="Command ID">Command ID</option>
                        <option value="Status">Status</option>
                        <option value="Error Code">Error Code</option>
                        <option value="Length">Length</option>
                        <option value="Version">Version</option>
                        <option value="Reserved">Reserved</option>
                        <option value="Timestamp">Timestamp</option>
                        <option value="Device ID">Device ID</option>
                    </select>

                    

                    <select class="compact-select bit-byte-select"
        name="bitByte_${prefix}_${index}"
        style="display:none"
        onchange="handleBitByteChange(this, '${prefix}', ${index})"
        onclick="if(this.value === 'Bitwise') openBitwiseConfig(this.closest('tr'))">
    <option value="Bytewise">Bytewise</option>
    <option value="Bitwise">Bitwise</option>
</select>
                    <button type="button"
                            class="arrow-btn"
                            style="display:none"
                            onclick="openBitwiseConfig(this.closest('tr'), '${prefix}', ${index})">
                        <i class="fa-solid fa-play" aria-hidden="true"></i>
                    </button>
                    

                    
  
                </div>
            </td>

            <!-- No of Bytes -->
            <td>
                <input type="number"
                       name="noOfBytes_${prefix}_${index}"
                       class="compact-input auto-calculated"
                       placeholder="Auto"
                  >
            </td>

            <!-- Bit Size -->
            <td>
                <input type="number"
                       name="bitSize_${prefix}_${index}"
                       class="compact-input auto-calculated"
                       placeholder="Auto"
                       >
            </td>
        
    

    

    



    


        <!-- Data Type -->
        <td>
            <select name="dataType_${prefix}_${index}" class="compact-select">
                <option value="">Select</option>
                <option value="UnsignedInt">Unsigned Integer</option>
                <option value="SignedInt">Signed Integer</option>
                <option value="Float">Float</option>
                <option value="Double">Double</option>
                <option value="Binary">Binary</option>
                <option value="ASCII">ASCII</option>
                <option value="String">String</option>
                <option value="Boolean">Boolean</option>
                <option value="Hex">Hex</option>
                <option value="Enum">Enum</option>
                <option value="Timestamp">Timestamp</option>
                <option value="None">None</option>
            </select>
        </td>

        <!-- Value Definition -->
        <td>
            <select name="valueDefinition_${prefix}_${index}" class="compact-select">
                <option value="">Select</option>
                <option value="Fixed Value">Fixed Value</option>
                <option value="Range Value">Range Value</option>
                <option value="Dynamic Value">Dynamic Value</option>
                <option value="Enum Value">Enum Value</option>
                <option value="Calculated Value">Calculated Value</option>
                <option value="None">None</option>
            </select>
        </td>

        <!-- Display Min (auto-calculated) -->
        <td><input type="number" name="displayMin_${prefix}_${index}" class="compact-input auto-calculated" placeholder="Min"></td>
        
        <!-- Display Max (auto-calculated) -->
        <td><input type="number" name="displayMax_${prefix}_${index}" class="compact-input auto-calculated" placeholder="Max"></td>

       <!-- Hex Min -->
        <td><input type="text" name="hexMin_${prefix}_${index}" class="compact-input" placeholder="e.g., 0x00"></td>
        
        <!-- Hex Max -->
        <td><input type="text" name="hexMax_${prefix}_${index}" id="hexMax_${prefix}_${index}" class="compact-input" placeholder="e.g., 0xFF"></td>
        <!-- Hex Min -->


        <!-- UI Type -->
        <td>
            <select name="uiType_${prefix}_${index}" class="compact-select" onchange="handleUITypeChange(this, '${prefix}', ${index})">
                <option value="">Select</option>
                <option value="None">None</option>
                <option value="Text">Text</option>
                <option value="Toggle Switch">Toggle Switch</option>
                <option value="Notification">Notification</option>
                <option value="Console">Console</option>
                <option value="Enter Text">Enter Text</option>
                <option value="Slider">Slider</option>
                <option value="Brightness Slider">Brightness Slider</option>
                <option value="Airflow Slider">Airflow Slider</option>
                <option value="CCT Slider">CCT Slider</option>
                <option value="Saturation Slider">Saturation Slider</option>
                <option value="Hue Slider">Hue Slider</option>
                <option value="Hue Slider Custom">Hue Slider Custom</option>
                <option value="Hue Circle">Hue Circle</option>
                <option value="Push Button">Push Button (Big)</option>
                <option value="Drop Down">Drop Down</option>
                <option value="Trigger">Trigger</option>
                <option value="Display">Display</option>
                <option value="Button">Button (Big)</option>
                <option value="Auto Trigger With Timer">Auto Trigger With Timer</option>
                <option value="Toast Display">Toast Display</option>
            </select>
        </td>
     

        <!-- UI ID -->
        <td>
    <input type="text" 
           name="uiId_${prefix}_${index}" 
           id="uiId_${prefix}_${index}" 
           class="compact-input ui-id-field" 
           value="${index}" 
           readonly 
           placeholder="UI ID"
           style="cursor: pointer; text-align: center;" 
           onclick="this.value = (this.value === 'None' ? ${index} : 'None'); this.setAttribute('data-user-choice', this.value === 'None' ? 'none' : 'id');">
</td>              

        <!-- AS PER UI -->
        <td><input type="text" name="asperui_${prefix}_${index}" class="compact-input" placeholder="e.g., Power Button"></td>
        
        <!-- UI Serial No -->
        <td><input type="text" name="sno_asperui_${prefix}_${index}" class="compact-input" placeholder="e.g., 1"></td>
        
        <!-- UI Values -->
        <td><input type="text" name="asperuivalue_${prefix}_${index}" class="compact-input" placeholder="e.g., ON=1, OFF=0"></td>

        <!-- UI Page No -->
        <td><input type="text" name="uiPageNo_${prefix}_${index}" class="compact-input" placeholder="e.g., 1"></td>
        
        <!-- UI In/Out -->
        <td>
<select name="uiInOut_${prefix}_${index}" id="uiInOut_${prefix}_${index}" class="compact-input">
        <option value="NONE">NONE</option>
        <option value="Read">Read</option>
        <option value="Write">Write</option>
        <option value="Read/Write">Read/Write</option>
    </select>
</td>


        <!-- Slider Step -->
        <td><input type="number" name="sliderStep_${prefix}_${index}" class="compact-input" min="1" placeholder="e.g., 1"></td>
        
        <!-- Default Value -->
        <td><input type="text" name="defaultValue_${prefix}_${index}" class="compact-input" placeholder="e.g., 0"></td>

        <!-- Developer Notes -->
        <td><input type="text" name="developerNotes_${prefix}_${index}" class="compact-input" placeholder="Internal notes"></td>
         <!--  Enable -->
       
<td>
    <input type="text" 
           name="enable_${prefix}_${index}" 
           list="enableOptions_${prefix}_${index}" 
           class="compact-input" 
           placeholder="Yes or No" 
           onclick="this.value=''" 
           onchange="handleEnableChange(this, '${prefix}', ${index})">
    <datalist id="enableOptions_${prefix}_${index}">
        <option value="No">
        <option value="Yes">
    </datalist>
</td>



    <!-- Message -->
<td>
    <input type="text" 
           name="message_${prefix}_${index}" 
           class="compact-input validate-field" 
           placeholder="Notification message" 
           disabled>
</td>

        <!-- Scaling -->
        <td>
            <input type="text"
                   name="scaling_${prefix}_${index}"
                   class="compact-input"
                   placeholder="e.g., value * 0.1">
        </td>
         
        <!-- Action Buttons -->
        <td class="action-buttons">
            <button type="button" class="remove-btn" data-tooltip="Delete Row" onclick="removeRow(this, '${prefix}')" aria-label="Delete row"><i class="fa-solid fa-minus" aria-hidden="true"></i></button>
            <button type="button" class="row-tool-btn duplicate-row-btn" data-tooltip="Duplicate" onclick="duplicateRow(this, '${prefix}')" aria-label="Duplicate row"><i class="fa-solid fa-copy" aria-hidden="true"></i></button>
            <button type="button" class="row-tool-btn move-up-row-btn" data-tooltip="Move Up" onclick="moveRowUp(this, '${prefix}')" aria-label="Move row up"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i></button>
            <button type="button" class="row-tool-btn move-down-row-btn" data-tooltip="Move Down" onclick="moveRowDown(this, '${prefix}')" aria-label="Move row down"><i class="fa-solid fa-arrow-down" aria-hidden="true"></i></button>
            <button type="button" class="row-tool-btn move-position-row-btn" data-tooltip="Move to Position" onclick="moveRowToPosition(this, '${prefix}')" aria-label="Move row to position"><i class="fa-solid fa-list-ol" aria-hidden="true"></i></button>
            <button type="button" class="add-btn" data-tooltip="Add Row" onclick="insertRowBelow(this, '${prefix}')" aria-label="Add row"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
            <button type="button" class="action-menu-close-btn" onclick="closeActionMenu(this)" aria-label="Close actions"><i class="fa-solid fa-xmark" aria-hidden="true"></i> Close</button>
        </td>


        
        
   <!-- Hidden fields for server submission -->
<td class="hidden-container" style="display:none">
    <input type="hidden" name="${prefix}_startBytePosition_${index}" value="">
    <input type="hidden" name="${prefix}_endBytePosition_${index}" value="">
    <input type="hidden" name="${prefix}_numberOfBytes_${index}" value="">
    <input type="hidden" name="${prefix}_bitSize_${index}" value="">
    <input type="hidden" name="${prefix}_displayValueMin_${index}" value="">
    <input type="hidden" name="${prefix}_displayValueMax_${index}" value="">
    <input type="hidden" name="${prefix}_valueInHexMin_${index}" value="">
    <input type="hidden" name="${prefix}_valueInHexMax_${index}" value="">
    <input type="hidden" name="${prefix}_fieldDescription_${index}" value="">
    <input type="hidden" name="${prefix}_dataType_${index}" value="">
    <input type="hidden" name="${prefix}_valueDefinition_${index}" value="">
    <input type="hidden" name="${prefix}_uiType_${index}" value="">
    <input type="hidden" name="${prefix}_uiId_${index}" value="">
    <input type="hidden" name="${prefix}_asperui_${index}" value="">
    <input type="hidden" name="${prefix}_sno_asperui_${index}" value="">
    <input type="hidden" name="${prefix}_asperuivalue_${index}" value="">
    <input type="hidden" name="${prefix}_uiPageNo_${index}" value="">
    <input type="hidden" name="${prefix}_uiDescriptionInOut_${index}" value="">
    <input type="hidden" name="${prefix}_sliderStepValue_${index}" value="">
    <input type="hidden" name="${prefix}_defaultvalue_${index}" value="">
    <input type="hidden" name="${prefix}_developerNotes_${index}" value="">
    <input type="hidden" name="${prefix}_scaling_${index}" value="">
</td>
</tr>


     `;
}




// ======================================================
// VALIDATION FUNCTIONS
// ======================================================
function getWarningTarget(field) {
    if (!field) return null;
    const smartWrap = field.nextElementSibling && field.nextElementSibling.classList && field.nextElementSibling.classList.contains('api-smart-select')
        ? field.nextElementSibling
        : null;
    const flowWrap = field.closest('.flow-direction-field, .form-group')?.querySelector('.flow-direction-select, .api-smart-select');
    return smartWrap || flowWrap || field;
}

function markFieldWarning(field) {
    if (!field) return;
    const target = getWarningTarget(field);
    field.classList.add('error');
    field.classList.add('modal-field-warning');
    field.style.border = "2px solid #DC2626";
    if (target && target !== field) {
        target.classList.add('has-error');
    }
}

function clearFieldWarning(field) {
    if (!field) return;
    const target = getWarningTarget(field);
    field.classList.remove('error');
    field.classList.remove('modal-field-warning');
    field.style.border = "";
    if (target && target !== field) {
        target.classList.remove('has-error');
    }
}

function moveToWarningField(field) {
    if (!field) return;
    const target = getWarningTarget(field) || field;
    target.scrollIntoView({behavior: 'smooth', block: 'center', inline: 'center'});
    window.setTimeout(function () {
        const focusTarget = target.matches && target.matches('.api-smart-select, .flow-direction-select')
            ? target.querySelector('button')
            : field;
        if (focusTarget && typeof focusTarget.focus === 'function') {
            focusTarget.focus({preventScroll: true});
        }
    }, 350);
}

function validateStep(step,e) {
    let isValid = false;
    const errorDiv = document.getElementById(`step${step}-error`);
    if (errorDiv) errorDiv.style.display = "none";

    // --- STEP 1: General Info ---
  
   if (step === 1) {
        isValid = validateStep1();
        if (!isValid) {
            forceMessage("Please fill the required Step 1 fields.");
        }
        return isValid;
    }

    // --- STEPS 2, 3, & 4: Frame Logic ---
    const prefix = (step === 2) ? 'cmd' : (step === 3 ? 'resp' : 'error');
    const direction = document.getElementById(prefix + '_data_flow_direction'); 
    const type = document.getElementById(prefix + '_frametype');
    const trigger = document.getElementById(prefix + '_frametrigger');

    let essentialValid = true;
    let missing = [];
    let firstInvalidField = null;

    // Check Dropdowns
    if (direction && (!direction.value || direction.value.toLowerCase().includes("select"))) {
        markFieldWarning(direction);
        if (!firstInvalidField) firstInvalidField = direction;
        missing.push("Data Flow Direction");
        essentialValid = false;
    } else if (direction) {
        clearFieldWarning(direction);
    }
    if (type && (!type.value || type.value.toLowerCase().includes("select"))) {
        markFieldWarning(type);
        if (!firstInvalidField) firstInvalidField = type;
        missing.push("Frame Type");
        essentialValid = false;
    } else if (type) {
        clearFieldWarning(type);
    }
    if (trigger && (!trigger.value || trigger.value.toLowerCase().includes("select"))) {
        markFieldWarning(trigger);
        if (!firstInvalidField) firstInvalidField = trigger;
        missing.push("Frame Trigger");
        essentialValid = false;
    } else if (trigger) {
        clearFieldWarning(trigger);
    }

    // If dropdowns are missing, show message and stop
    if (!essentialValid) {
        const msg = "Please fill the following: " + missing.join(", ");
        if (errorDiv) {
            errorDiv.innerText = msg;
            errorDiv.style.display = "block";
        }
        forceMessage(msg); // Custom message only
        moveToWarningField(firstInvalidField);
        return false; 
    }

    // --- Specific Step Logic (Tables) ---
    switch(step) {
        case 2: isValid = validateStep2(); break;
        case 3: isValid = validateStep3(); break;
        case 4: isValid = validateStep4(); break;
        default: isValid = true;
    }
          // If table validation failed, validateTableStep already calls forceMessage.
    if (isValid) {
        console.log(`%c Step ${step} Validated.`, "color: green; font-weight: bold;");
        
        if (step === 2) {
            saveCommandConfiguration(); 
        } else if (step === 3) {
            saveResponseConfiguration();
        } else if (step === 4) {
            // --- CRITICAL CHANGE START ---
            
            console.log("%c STOP: API Prevented. Checking Console...", "background: orange; color: black;");
            
            saveErrorConfiguration(); // Pass the event to the logger
            
            return true;
            // --- CRITICAL CHANGE END ---
        }
    }
    return isValid;
}

 // Fixed Table Validator (Added forceMessage, Removed alert)
function validateTableStep(tableBodyId, errorId, isMandatory,prefix) {
    var isValid = true;
    var errorElement = document.getElementById(errorId);
    var rows = document.querySelectorAll('#' + tableBodyId + ' tr');
    var firstInvalidField = null;

    if (rows.length === 0) return isMandatory ? false : true;

    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        row.classList.remove("pink-highlight"); 
        var fields = row.querySelectorAll('input:not([name*="developerNotes"]), select');
        var hasErrorInRow = false;

        for (var j = 0; j < fields.length; j++) {
            var field = fields[j];
            if (field.disabled || field.type === "hidden") continue;
            if (!field.value || field.value.trim() === "" || field.value === "Error") {
                markFieldWarning(field);
                if (!firstInvalidField) firstInvalidField = field;
                hasErrorInRow = true;
                isValid = false;
            } else {
                clearFieldWarning(field);
            }
        }
        if (hasErrorInRow) row.classList.add("pink-highlight");
    }

    if (!isValid) {
        
        const msg = "Please fill all required fields in the table.";
        if (errorElement) {
            errorElement.innerText = msg;
            errorElement.style.display = "block";
        }
        forceMessage(msg); // <--- Triggers the red box
        moveToWarningField(firstInvalidField);
    }
    return isValid;
}


function validateStep1() {
    let isValid = true;
    const errors = [];
    const fields = [
        { id: 'par_parametername', name: 'Parameter Name' },
        { id: 'pagenames', name: 'Page Names' },
        { id: 'numberofpages', name: 'Page Number' },
        { id: 'Sequence', name: 'Sequence Number' }
    ];

    fields.forEach(field => {
        const el = document.getElementById(field.id);
        if (el) {
            if (!el.value.trim()) {
                errors.push(`${field.name} is required`);
                markFieldWarning(el);
                isValid = false;
            } else if (field.id === 'pagenames' && el.value === "Page0") {
                errors.push('Page0 as Page name is invalid');
                markFieldWarning(el);
                isValid = false;
            } else {
                clearFieldWarning(el);
            }
        }
    });
    
    if (!isValid) {
        const errorElement = document.getElementById('step1-error');
        const msg = "Please fill the required Step 1 fields.";
        if (errorElement) {
            errorElement.innerHTML = errors.map(err => `<div>• ${err}</div>`).join('');
            errorElement.style.display = "block";
        }
        forceMessage(msg); 
        const firstInvalid = fields.map(field => document.getElementById(field.id)).find(el => el && el.classList.contains('error'));
        moveToWarningField(firstInvalid);
    }
    return isValid;
}

function validateStep2() {
    const frameSize = document.getElementById('cmd_framesizeinbytes');
    if (frameSize) {
        if (!frameSize.value || parseInt(frameSize.value) <= 0) {
            const msg = "Frame Size in Bytes is mandatory for the Command Frame.";
            const err = document.getElementById('step2-error');
            if (err) {
                err.innerText = msg;
                err.style.display = "block";
            }
            forceMessage(msg);
            markFieldWarning(frameSize);
            moveToWarningField(frameSize);
            return false;
        }
        clearFieldWarning(frameSize);
    }
    const isTableValid = validateTableStep("additionalTableBody", "step2-error", true);
    if (!isTableValid) return false;
    // 2. New Duplicate Check
    return validateDuplicateByteRange("additionalTableBody", "cmd", "step2-error");
 
    
    return validateTableStep("additionalTableBody", "step2-error", true);

    }

   // Your Force Message UI Function
function forceMessage(msg) {
    let box = document.getElementById("customAlert");
    if (!box) {
        box = document.createElement("div");
        box.id = "customAlert";
        document.body.appendChild(box);
    }
    box.innerText = msg;
    box.style.cssText = `
        position: fixed;
        top: 20%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #FFFBEB;
        color: #991B1B;
        border: 1px solid #DC2626;
        padding: 18px 34px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(220,38,38,0.22);
        z-index: 999999;
        font-family: sans-serif;
        font-size: 16px;
        font-weight: 800;
        text-align: center;
    `;
    box.style.display = "block";
    setTimeout(() => { box.style.display = "none"; }, 3000);
}

    // Keep Step 3 and 4 as they call validateTableStep which now has the messages.

function validateStep3() { 
    // 1. Original Table Validation
    const isTableValid = validateTableStep("responseTableBody", "step3-error", false); 
    if (!isTableValid) return false;

    // 2. New Duplicate Check
    return validateDuplicateByteRange("responseTableBody", "resp", "step3-error");
}
function validateStep4() { 
    // 1. Original Table Validation
    const isTableValid = validateTableStep("errorTableBody", "step4-error", false); 
    if (!isTableValid) return false;

    // 2. New Duplicate Check
    return validateDuplicateByteRange("errorTableBody", "error", "step4-error");
}
// ======================================================
// OTHER FUNCTIONS
// ======================================================
function syncParameterNameToAllSteps() {
    const mainInput = document.getElementById("par_parametername");
    const targetIDs = ["com_parametername", "resp_parametername", "error_parametername"];

    if (!mainInput) return;

    mainInput.addEventListener("input", function() {
        const value = mainInput.value;
        targetIDs.forEach(id => {
            const input = document.getElementById(id);
            if (input) input.value = value;
        });
        
        // NEW: Also update Default Value in all rows where Field Description is "Data"
        updateDefaultValueForAllDataRows(value);
    });

    const initialValue = mainInput.value;
    if (initialValue) {
        targetIDs.forEach(id => {
            const input = document.getElementById(id);
            if (input) input.value = initialValue;
        });
        
        // NEW: Update Default Value in all rows where Field Description is "Data"
        updateDefaultValueForAllDataRows(initialValue);
    }
}

// NEW: Update Default Value in all rows where Field Description is "Data"
function updateDefaultValueForAllDataRows(parameterName) {
    const tables = [
        {id:'additionalTableBody', prefix:'cmd'}, 
        {id:'responseTableBody', prefix:'resp'}, 
        {id:'errorTableBody', prefix:'error'}
    ];
    
    tables.forEach(t => {
        const tbody = document.getElementById(t.id);
        if (!tbody) return;
        
        Array.from(tbody.rows).forEach((row, idx) => {
            const fieldDescSelect = row.querySelector(`select[name="fieldDescription_${t.prefix}_${idx + 1}"]`);
            if (fieldDescSelect && fieldDescSelect.value === 'Data') {
                const defaultValueInput = row.querySelector(`input[name="defaultValue_${t.prefix}_${idx + 1}"]`);
                if (defaultValueInput) {
                    defaultValueInput.value = parameterName;
                    defaultValueInput.classList.add('auto-calculated');
                    defaultValueInput.readOnly = true;
                    
                    // Also update hidden field
                    const hiddenField = row.querySelector(`input[name="${t.prefix}_defaultvalue_${idx + 1}"]`);
                    if (hiddenField) {
                        hiddenField.value = parameterName;
                    }
                }
            }
        });
    });
}

function syncParameterNameToAll(value) {
    const targetIDs = ["com_parametername", "resp_parametername", "error_parametername"];
    targetIDs.forEach(id => {
        const input = document.getElementById(id);
        if (input) input.value = value;
    });
    
    // NEW: Also update Default Value in all rows where Field Description is "Data"
    updateDefaultValueForAllDataRows(value);
}

function validateParamLive() {
    const input = document.getElementById("par_parametername");
    const hidden = document.getElementById("default-param-list");
    const dupError = document.getElementById("duplicate-error");
    const charError = document.getElementById("char-error");

    if (!input || !hidden || !dupError || !charError) return;

    let paramList = [];
    try {
        paramList = JSON.parse(hidden.value || "[]");
    } catch (e) {
        console.error("JSON Parse Error:", e);
    }

    const entered = input.value.trim();
    const enteredLower = entered.toLowerCase();
    const exists = paramList.some(p => p.toLowerCase() === enteredLower);

    const invalidChar = /[^a-zA-Z0-9_ ]/.test(entered);

    if (entered && exists) {
        dupError.style.display = "block";
        input.style.border = "1px solid red";
    } else {
        dupError.style.display = "none";
    }

    if (entered && invalidChar) {
        charError.style.display = "block";
        input.style.border = "1px solid red";
    } else {
        charError.style.display = "none";
    }

    if (!exists && !invalidChar) {
        input.style.border = "";
    }
}


function validatePageName() {
    const input = document.getElementById("pagenames");
    if (input.value === "Page0") {
        alert("Page0 as Page name is invalid");
        return false;
    }
    return true;
}

function checkWordCount(textarea) {
    const maxWords = 300;
    const wordCount = textarea.value.trim().split(/\s+/).length;

    if (wordCount > maxWords) {
        const words = textarea.value.trim().split(/\s+/);
        textarea.value = words.slice(0, maxWords).join(' ');
        alert('Maximum word limit exceeded. Content has been truncated.');
    }
}

// ======================================================
// FORM SUBMISSION VALIDATION
// ======================================================
const formEl = document.getElementById('file-form');
if (formEl) {
    formEl.addEventListener('submit', function(e) {
        console.log("=== FORM SUBMISSION STARTED ===");
        
        // Validate model name
        const modelNameEl = document.getElementById('modelname');
        const modelName = modelNameEl ? modelNameEl.value : '';
        if (modelName.trim() === '') {
            e.preventDefault();
            alert('Please enter the Model Name.');
            return;
        }

        let validationErrors = [];

        // Validate slider steps for slider UI types
        const uiSelects = document.querySelectorAll('select[name^="uiType_"]');
        for (let sel of uiSelects) {
            const uiValue = (sel.value || '').toLowerCase();
            
            // Slider validation
            if (uiValue.indexOf('slider') !== -1) {
                const name = sel.getAttribute('name');
                const parts = name.split('_');
                const prefix = parts[1];
                const index = parts[2];
                const sliderInput = document.querySelector(`input[name="sliderStep_${prefix}_${index}"]`);
                if (!sliderInput || !String(sliderInput.value).trim()) {
                    validationErrors.push(`Please enter Slider Step for row ${index} in ${prefix.toUpperCase()} table`);
                    if (sliderInput) sliderInput.style.border = '2px solid red';
                }
            }
            
            // Drop Down and Notification validation
            if (uiValue === 'drop down' || uiValue === 'notification') {
                const name = sel.getAttribute('name');
                const parts = name.split('_');
                const prefix = parts[1];
                const index = parts[2];
                
                const asperUiInput = document.querySelector(`input[name="asperui_${prefix}_${index}"]`);
                const uiValuesInput = document.querySelector(`input[name="asperuivalue_${prefix}_${index}"]`);
                const uiSerialInput = document.querySelector(`input[name="sno_asperui_${prefix}_${index}"]`);
                
                if (!asperUiInput || !String(asperUiInput.value).trim()) {
                    validationErrors.push(`Please enter AS PER UI for row ${index} in ${prefix.toUpperCase()} table (required for ${uiValue})`);
                    if (asperUiInput) asperUiInput.style.border = '2px solid red';
                }
                
                if (!uiValuesInput || !String(uiValuesInput.value).trim()) {
                    validationErrors.push(`Please enter UI VALUES for row ${index} in ${prefix.toUpperCase()} table (required for ${uiValue})`);
                    if (uiValuesInput) uiValuesInput.style.border = '2px solid red';
                }
                
                if (!uiSerialInput || !String(uiSerialInput.value).trim()) {
                    validationErrors.push(`Please enter UI SERIAL NO for row ${index} in ${prefix.toUpperCase()} table (required for ${uiValue})`);
                    if (uiSerialInput) uiSerialInput.style.border = '2px solid red';
                }
            }
        }

        if (validationErrors.length > 0) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + validationErrors.join('\n'));
            return;
        }

        console.log("=== FORM SUBMITTED SUCCESSFULLY ===");
    });
}

function handleUITypeChange(selectElement, prefix, index) {
    const uiInOutSelect = document.getElementById(`uiInOut_${prefix}_${index}`);
    if (!uiInOutSelect) return;

    if (selectElement.value === "Toggle Switch") {
        // Change to the 3 options you requested
        uiInOutSelect.innerHTML = `
            <option value="NONE">NONE</option>
            <option value="Read">Read</option>
            <option value="Write">Write</option>
            <option value="Read/Write" selected>Read/Write</option>
        `;
    } else {
        // Reset to default for other UI types
        uiInOutSelect.innerHTML = `
            <option value="NONE">NONE</option>
            <option value="Read">Read</option>
            <option value="Write">Write</option>
           <option value="Read/Write">Read/Write</option>
        `;
    }
}

function calculateHexMax(prefix, index) {
    // Get the elements
    const startIn = document.querySelector(`input[name="startBytePosition_${prefix}_${index}"]`);
    const endIn = document.querySelector(`input[name="endBytePosition_${prefix}_${index}"]`);
    const hexMaxOut = document.getElementById(`hexMax_${prefix}_${index}`);

    if (startIn && endIn && hexMaxOut) {
        const start = parseInt(startIn.value);
        const end = parseInt(endIn.value);

        // Logic: Only calculate if numbers are valid and End is >= Start
        if (!isNaN(start) && !isNaN(end) && end >= start) {
            // 1. Calculate Byte Count (e.g., 0 to 2 is 3 bytes)
            const byteCount = end - start + 1;
            
            // 2. Calculate Max Decimal: (256 ^ byteCount) - 1
            // 3 bytes = (256^3) - 1 = 16,777,215
            const maxDec = Math.pow(256, byteCount) - 1;
            
            // 3. Convert to Hex String (ffffff)
            let hexRes = maxDec.toString(16).toUpperCase();
            
            // 4. Pad with '0' if the hex is too short (e.g., 0x01FFFF)
            while (hexRes.length < byteCount * 2) {
                hexRes = "0" + hexRes;
            }

            // 5. Set the value to 0xFFFFFF format
            hexMaxOut.value = "0x" + hexRes;
        } else {
            hexMaxOut.value = ""; // Clear if inputs are bad
        }
    }
}
// 1. These MUST be at the top of your file
function saveErrorFrame() {

    // 🔴 Stop save if validation fails

    if (!validateStep(4)) {

        return;

    }



    // Only submit if validation passes

    document.querySelector("form").submit();

}
//
//
/* ======================================================
   NAVIGATIONAL LOGIC (Step Switching)
   ====================================================== */

function goToNextStep(step) {
    // 1. RUN VALIDATION
    const isStepValid = validateStep(step);

    // 2. ONLY MOVE IF VALID
    // If validateStep returns false, this code won't run, and the page won't change.
    if (isStepValid === true) {
        const currentElem = document.getElementById('step-' + step);
        const nextElem = document.getElementById('step-' + (step + 1));

        if (currentElem && nextElem) {
            currentElem.style.display = 'none';
            nextElem.style.display = 'block';
            window.scrollTo(0, 0);
        }
    } else {
        console.log("Validation failed. Staying on step " + step);
    }
}
function goToPreviousStep(step) {

    // If inside Step 2 (Command Frames)
    if (step === 2 && currentCommandSubStep > 1) {

        // Save current frame before moving back
        saveCurrentCommandState(currentCommandSubStep - 1);

        // Move back
        currentCommandSubStep--;

        // Load previous frame
        const savedData = storedCommands[currentCommandSubStep - 1];

        if (savedData) {
            applyCommandFrameData(savedData);
        } else {
            resetCommandUI();
        }

        // Update UI properly
        updateCommandFrameTitle();
        updateCommandBoxHighlight();
        updateCommandButtonLabels(
            parseInt(document.getElementById('numberofpages')?.value || 1)
        );

        return; // Stay in Step 2
    }

    // Standard step back (Step 3 to 2 etc.)
    document.getElementById(`step-${step}`).style.display = 'none';

    currentStep = step - 1;

    document.getElementById(`step-${currentStep}`).style.display = 'block';

    updateProgressIndicator();
}

   /* ======================================================
   SAVE LOGIC (Generates the API Form View)
   ====================================================== */

function saveErrorFrame() {
    const frameSizeInput = document.getElementById('error_framesizeinbytes');
    const errorDiv = document.getElementById('step4-error');
    const form = document.getElementById('file-form'); 
    
    const frameSize = parseInt(frameSizeInput.value) || 0;

    // 1. Visual Warning for Error Frame
    if (frameSize <= 0) {
        if (errorDiv) {
            errorDiv.innerText = "Warning: Error Frame size is 0. Saving empty configuration.";
            errorDiv.style.color = "orange";
            errorDiv.style.display = "block";
        }
        if (frameSizeInput) frameSizeInput.style.border = "2px solid orange";
    } else {
        if (errorDiv) errorDiv.style.display = "none";
        if (frameSizeInput) frameSizeInput.style.border = "1px solid #ccc";
    }

    // 2. Form Submission
    // This is the part that makes it display like the "API Form" array
    if (form) {
        console.log("Submitting form to backend...");
        form.submit(); 
    } else {
        alert("Error: Form with ID 'file-form' not found. Check your HTML.");
    }
}

// Add this at the bottom of your script
['input', 'change'].forEach(evt => {
    document.addEventListener(evt, function (e) {
        const el = e.target;
        const name = el?.getAttribute('name');
        if (!name) return;

        // ==============================
        // UI IN / OUT FIELD (SPECIAL)
        // ==============================
        if (name.toLowerCase().startsWith('uiinout_')) {
            const remainder = name.replace(/uiinout_/i, '');
            const lastUnderscore = remainder.lastIndexOf('_');
            if (lastUnderscore === -1) return;

            const prefix = remainder.substring(0, lastUnderscore);
            const index = remainder.substring(lastUnderscore + 1);

            syncHiddenField(prefix, 'uiDescriptionInOut', index, el.value.trim());
            return; // 🚨 stop here, don't fall through
        }

        // ==============================
        // ALL OTHER FIELDS
        // ==============================
        if (name.includes('_cmd_') || name.includes('_resp_') || name.includes('_error_')) {
            const parts = name.split('_');
            const fieldName = parts[0];
            const prefix = parts[1];
            const index = parts[2];

            let hiddenName = `${prefix}_${fieldName}_${index}`;

            if (fieldName.toLowerCase() === "sliderstep") {
                hiddenName = `${prefix}_sliderStepValue_${index}`;
            }

            if (fieldName.toLowerCase() === "defaultvalue") {
                hiddenName = `${prefix}_defaultvalue_${index}`;
            }

            const hiddenInput = document.querySelector(`input[name="${hiddenName}"]`);
            if (hiddenInput) {
                hiddenInput.value = el.value;
                hiddenInput.setAttribute('value', el.value);
            }
        }
    });
});


function syncFrameTrigger(prefix, index) {
    // 1. Get the value from the visible UI Type dropdown
    const uiTypeSelect = document.querySelector(`select[name="uiType_${prefix}_${index}"]`);
    
    // 2. Find the hidden frame-level trigger input (e.g., cmd_frametrigger)
    const hiddenTrigger = document.getElementById(`${prefix}_frametrigger`);

    if (uiTypeSelect && hiddenTrigger) {
        hiddenTrigger.value = uiTypeSelect.value;
        hiddenTrigger.setAttribute('value', uiTypeSelect.value);
    }
}

//commond frame
let commandCount = 1; 
let storedCommands = [];
function handleNextCommand() {
    const maxFramesField = document.getElementById('numberofpages');
    const maxFrames = parseInt(maxFramesField ? maxFramesField.value : 1) || 1;

    // 1. Validate duplicates before leaving
    if (!validateDuplicateByteRange("additionalTableBody", "cmd", "step2-error")) return;

    // 2. SAVE current state to the current index
    saveCurrentCommandState(commandCount - 1);

    // 3. Check if we are at the end
    if (commandCount >= maxFrames) {
        alert("Success: You have completed all " + maxFrames + " command frames.");
        if (typeof updateCommandBoxColors === "function") updateCommandBoxColors();
        return;
    }

    // 4. Move to next index
    commandCount++;
    
    // 5. LOAD existing data OR RESET for new page
    if (storedCommands[commandCount - 1]) {
        applyCommandFrameData(storedCommands[commandCount - 1]);
    } else {
        resetCommandUI(); // Fresh page
    }

    updateUIState(maxFrames);
}

function handleBackCommand() {
    if (commandCount > 1) {
        const maxFrames = parseInt(document.getElementById('numberofpages')?.value || 1);

        // 1. SAVE current state so changes aren't lost
        saveCurrentCommandState(commandCount - 1);

        // 2. Move index back
        commandCount--;

        // 3. LOAD the previous state
        const prevSaved = storedCommands[commandCount - 1];
        if (prevSaved) {
            applyCommandFrameData(prevSaved);
        } else {
            resetCommandUI();
        }

        updateUIState(maxFrames);
    }
}

// Helper to keep UI updates consistent
function updateUIState(maxFrames) {
    updateCommandFrameTitle();
    updateCommandButtonLabels(maxFrames);
    if (typeof updateCommandBoxColors === "function") updateCommandBoxColors();
    window.scrollTo(0, 0);
}


// Helper to clean UI
function resetCommandUI() {
    const tbody = document.getElementById('additionalTableBody');
    if (document.getElementById('cmd_data_flow_direction')) document.getElementById('cmd_data_flow_direction').value = "";
    if (document.getElementById('cmd_framesizeinbytes')) document.getElementById('cmd_framesizeinbytes').value = "";
    if (tbody) tbody.innerHTML = "";
    if (typeof addRow === "function") addRow('cmd');
}









function setField(tr, selectors, value) {
    const el = tr.querySelector(selectors);
    if (!el) return;

    if ("value" in el) {
        el.value = value || "";
    } else {
        el.textContent = value || "";
    }
}
function applyCommandFrameData(data) {
    if (!data) {
        console.error("DEBUG: No data passed to applyCommandFrameData");
        return;
    }

    console.log("DEBUG: Starting applyCommandFrameData with data:", data);

    // 1. Restore Header Fields
    if(document.getElementById('com_parametername')) 
        document.getElementById('com_parametername').value = data.parameterName || "";
    if(document.getElementById('cmd_data_flow_direction')) 
        document.getElementById('cmd_data_flow_direction').value = data.direction || "";
    if(document.getElementById('cmd_framesizeinbytes')) 
        document.getElementById('cmd_framesizeinbytes').value = data.size || "";
    if(document.getElementById('cmd_frame_trigger_options')) 
        document.getElementById('cmd_frame_trigger_options').value = data.triggerOptions || "None";

    // 2. Clear the table
    const tbody = document.getElementById('additionalTableBody');
    if (tbody) {
        tbody.innerHTML = "";
    } else {
        console.error("DEBUG: #additionalTableBody not found in DOM");
        return;
    }

    // 3. Rebuild rows
    data.rows.forEach((rowData, index) => {
        console.log(`DEBUG: Processing Row ${index}`, rowData);

        if (typeof addRow === "function") {
            addRow('cmd'); 
        } else {
            console.error("DEBUG: addRow function is missing!");
        }

        const tr = tbody.lastElementChild;
        if (!tr) {
            console.error(`DEBUG: Failed to create row element for index ${index}`);
            return;
        }

        // --- ATTACH BITWISE DATA IMMEDIATELY ---
        if (rowData.bitwise) {
            tr.setAttribute('data-bitwise-storage', JSON.stringify(rowData.bitwise));
            console.log(`DEBUG: Attached bitwise storage to Row ${index}`);
        }

        // Restore Dropdown
        const bitByteEl = tr.querySelector('[name^="bitByte_"]');
        if (bitByteEl && rowData.cmd_bitwiseMode) {
            bitByteEl.value = rowData.cmd_bitwiseMode;
        }

        // Restore standard fields (Bytewise)
        setField(tr, '[name*="Description"]', rowData.cmd_fieldDescription);
        setField(tr, '[name*="startByte"]', rowData.cmd_startBytePosition);
        setField(tr, '[name*="endByte"]', rowData.cmd_endBytePosition);
        setField(tr, '[name*="noOfBytes"]', rowData.cmd_numberOfBytes);
        setField(tr, '[name*="bitSize"]', rowData.cmd_bitSize);
        setField(tr, '[name*="dataType"]', rowData.cmd_dataType);
        setField(tr, '[name*="valueDefinition"]', rowData.cmd_valueDefinition);
        setField(tr, '[name*="Min"]', rowData.cmd_displayValueMin);
        setField(tr, '[name*="Max"]', rowData.cmd_displayValueMax);
        setField(tr, '[name*="hexMin"]', rowData.cmd_valueInHexMin);
        setField(tr, '[name*="hexMax"]', rowData.cmd_valueInHexMax);
        setField(tr, '[name*="uiType"]', rowData.cmd_uiType);
        setField(tr, '[name*="uiId"]', rowData.cmd_uiId);
        setField(tr, '[name*="asperui"]', rowData.cmd_asperui);
        setField(tr, '[name*="SerialNo"]', rowData.cmd_sno_asperui);
        setField(tr, '[name*="UiValue"]', rowData.cmd_asperuivalue);
        setField(tr, '[name*="uiPageNo"]', rowData.cmd_uiPageNo);
        setField(tr, '[name*="InOut"]', rowData.cmd_uiDescriptionInOut);
        setField(tr, '[name*="Step"]', rowData.cmd_sliderStepValue);
        
        const defaultEl = tr.querySelector('[name*="defaultValue"]');
        if (defaultEl) defaultEl.value = rowData.cmd_defaultvalue || "";

        setField(tr, '[name*="DevNotes"]', rowData.cmd_developerNotes);
        setField(tr, '[name^="enable_"]', rowData.cmd_notificationEnable);
        setField(tr, '[name*="message"]', rowData.cmd_notificationMessage);

        // --- TRIGGER BITWISE REFRESH WITH DELAY ---
        if (rowData.cmd_bitwiseMode === "Bitwise") {
            console.log(`DEBUG: Row ${index} is Bitwise. Triggering UI refresh...`);
            
            // Wait for 10ms to ensure the row is rendered in the browser
            setTimeout(() => {
                if (typeof handleBitByteChange === "function" && bitByteEl) {
                    handleBitByteChange(bitByteEl, 'cmd', index);
                }

                if (typeof applyBitwiseToMainRow === "function" && rowData.bitwise) {
                    console.log(`DEBUG: Calling applyBitwiseToMainRow for Row ${index}`);
                    applyBitwiseToMainRow(tr, rowData.bitwise);
                } else {
                    console.warn(`DEBUG: applyBitwiseToMainRow skipped. Function exists: ${typeof applyBitwiseToMainRow}, Data exists: ${!!rowData.bitwise}`);
                }
            }, 10);
        }
    });
}

function loadCommandState(index) {
    const data = storedCommands[index];
    if (!data) return;

    // restore header
    document.getElementById('com_parametername').value = data.parameterName || "";
    document.getElementById('cmd_data_flow_direction').value = data.direction || "";
    document.getElementById('cmd_framesizeinbytes').value = data.size || "";

    const tbody = document.querySelector('#additionalTableBody');
    tbody.innerHTML = ""; // clear old rows

    data.rows.forEach(rowData => {
                addCommandRow(); // your existing function that adds a row
        
               const tr = tbody.lastElementChild;

        tr.querySelector('[name*="startByte"]').value = rowData.cmd_startBytePosition;
        tr.querySelector('[name*="endByte"]').value = rowData.cmd_endBytePosition;
       
        tr.querySelector('[name*="noOfBytes"]').value = rowData.cmd_numberOfBytes || "";

        tr.querySelector('[name*="bitSize"]').value = rowData.cmd_bitSize;
        tr.querySelector('[name*="Description"], [name*="description"]').value = rowData.cmd_fieldDescription;
        tr.querySelector('[name*="dataType"], [name*="DataType"]').value = rowData.cmd_dataType;
        tr.querySelector('[name*="valueDefinition"]').value = rowData.cmd_valueDefinition;
        tr.querySelector('[name*="displayValueMin"], [name*="Min"]').value = rowData.cmd_displayValueMin;
        tr.querySelector('[name*="displayValueMax"], [name*="Max"]').value = rowData.cmd_displayValueMax;
       
        tr.querySelector('[name*="hexMin"], [name*="Min"]').value = rowData.cmd_valueInHexMin;
        tr.querySelector('[name*="hexMax"], [name*="Max"]').value = rowData.cmd_valueInHexMax;


        tr.querySelector('[name*="uiType"]').value = rowData.cmd_uiType;
        tr.querySelector('[name*="uiId"]').value = rowData.cmd_uiId;
        tr.querySelector('[name*="asperui"]').value = rowData.cmd_asperui;
        tr.querySelector('[name*="sno_asperui"], [name*="SerialNo"]').value = rowData.cmd_sno_asperui;
        tr.querySelector('[name*="asperuivalue"], [name*="UiValue"]').value = rowData.cmd_asperuivalue;
        tr.querySelector('[name*="uiPageNo"]').value = rowData.cmd_uiPageNo;
        tr.querySelector('[name*="uiDescriptionInOut"], [name*="InOut"]').value = rowData.cmd_uiDescriptionInOut;
        tr.querySelector('[name*="sliderStepValue"], [name*="Step"]').value = rowData.cmd_sliderStepValue;
        tr.querySelector('[name*="defaultvalue"], [name*="Default"]').value = rowData.cmd_defaultvalue;


        
        tr.querySelector('[name*="developerNotes"], [name*="DevNotes"]').value = rowData.cmd_developerNotes;
        tr.querySelector('[name*="notificationEnable"]').value = rowData.cmd_notificationEnable;
        tr.querySelector('[name*="notificationMessage"]').value = rowData.cmd_notificationMessage;
       

        // restore bitwise
        if (rowData.bitwise) {
            tr.setAttribute('data-bitwise-storage', JSON.stringify(rowData.bitwise));
            renderBitwiseReadonly(tr);
        }
           });

    console.log(`Loaded storedCommands[${index}]`, storedCommands[index]);
}





function renderBitwiseReadonly(tr) {
    // 1. Get the data restored by loadCommandState
    const rawData = tr.getAttribute('data-bitwise-storage');
    if (!rawData) return;
    
    const savedData = JSON.parse(rawData);
    const columns = tr.cells;

    // 2. Loop through columns (starting after byte positions)
    for (let i = 3; i < columns.length; i++) {
        // Skip columns with action buttons
        if (columns[i].querySelector('button') || columns[i].classList.contains('action-buttons')) {
            continue;
        }

        // 3. WIPE the bytewise input/select
        columns[i].innerHTML = ''; 

        // 4. CREATE the readonly summary box
        const box = document.createElement('input');
        box.type = 'text';
        box.readOnly = true;
        
        // Apply gray styling
        box.style.width = '100%';
        box.style.backgroundColor = '#eeeeee';
        box.style.textAlign = 'center';
        box.style.border = '1px solid #ccc';

        // 5. FILL the value (The Critical Part)
        if (savedData && savedData.length > 0) {
            // Use the exact key from your saveBitwiseConfig
            box.value = savedData[0].cmd_cmd_defaultvalue_1 || "Bitwise Set";
        }

        columns[i].appendChild(box);
    }
}






function updateCommandButtonLabels(limit) {
    const nextBtn = document.getElementById('next-command-btn');
    if (!nextBtn) return;

    if (commandCount === limit) {
        nextBtn.innerHTML = 'Finalize & Move to Response <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>';
        nextBtn.style.backgroundColor = "#28a745";
    } else {
        nextBtn.innerHTML = 'Next Command Frame <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>';
        nextBtn.style.backgroundColor = "";
    }

    const backBtn = document.getElementById('back-command-btn');
    if (backBtn) {
        backBtn.style.display = (commandCount > 1) ? 'inline-block' : 'none';
    }
}
function updateCommandFrameTitle() {
    const titleDisplay = document.getElementById('command-title-display');
    const titleHeader = document.getElementById('command-frame-title');
    const text = `COMMAND FRAME (${commandCount})`;

    if (titleDisplay) titleDisplay.innerText = text;
    if (titleHeader) titleHeader.innerText = text;
}




// --- 1. NEXT BUTTON LOGIC ---
// ================= RESPONSE FRAME =================
 let responseCount = 1;

let storedResponses = [];

let globalResponseName = "";



/**
 * Handle Next Button Click (Matches Error Frame logic)
 */

function handleNextResponse() {
    const maxFramesField = document.getElementById('page'); // Note: 'page' for Response
    const maxFrames = parseInt(maxFramesField ? maxFramesField.value : 1) || 1;

     if (!validateDuplicateByteRange("responseTableBody", "resp", "step3-error")) {
        return; // Stop here if duplicates are found
    }

    // 1. SAFETY GATE: Check if we are at the limit
    if (responseCount >= maxFrames) {
        alert("Success: You have completed all " + maxFrames + " response frames.");
        // Mark the final page as touched/completed
        responsePageTouched[responseCount - 1] = true;
        if (typeof updateResponseBoxColors === "function") updateResponseBoxColors();
        return; 
    }

    // 2. DATA SAVE: Save current state before moving forward
    saveCurrentResponseState(); 
    // Mark the page we just left as touched so the box turns Blue
    responsePageTouched[responseCount - 1] = true;

    // 3. INCREMENT
    responseCount++;
   // Print current command count
        console.log("Current commandCount:", commandCount);


    // 4. UPDATE PROGRESS BOXES
    if (typeof updateResponseBoxColors === "function") updateResponseBoxColors();

    // 5. UI UPDATES
    updateResponseFrameTitle();

    const tbody = document.getElementById('responseTableBody');

    // 6. LOAD EXISTING OR START FRESH
    if (storedResponses[responseCount - 1]) {
    applyResponseFrameData(storedResponses[responseCount - 1]); 
} else {
    // 1. Reset Metadata Inputs
    if(document.getElementById('resp_data_flow_direction')) document.getElementById('resp_data_flow_direction').value = "";
    if(document.getElementById('resp_framesizeinbytes')) document.getElementById('resp_framesizeinbytes').value = "1";
    if(document.getElementById('resp_frametrigger')) document.getElementById('resp_frametrigger').value = "None";
    
    // 2. Clear the UI Table
    if (tbody) {
        tbody.innerHTML = ''; 
    }

    // 3. FORCE TABLE DISPLAY: Re-add the first row
    // If addRow('resp') doesn't show the table, 
    // ensure the 'resp_noofrows' is set to 1 first.
    if(document.getElementById('resp_noofrows')) document.getElementById('resp_noofrows').value = "1";
    
    if (typeof addRow === "function") {
        addRow('resp'); // This creates the <tr> and <td> elements
    }
}
    // 7. SYNC UI
    updateResponseButtonLabels(maxFrames);
    if (typeof updateResponseBackVisibility === "function") updateResponseBackVisibility();
    window.scrollTo(0, 0);
}

/**
 * Handle Back Button Click (Matches Error Frame logic)
 */

function handleBackResponse() {
    if (responseCount > 1) {
        saveCurrentResponseState(); 

        responseCount--;
        // Print current command count
        console.log("Current commandCount:", commandCount);

        
        const prevSaved = storedResponses[responseCount - 1];
        if (prevSaved) {
            if(document.getElementById('resp_data_flow_direction')) document.getElementById('resp_data_flow_direction').value = prevSaved.direction || "";
            if(document.getElementById('resp_framesizeinbytes')) document.getElementById('resp_framesizeinbytes').value = prevSaved.size || "";
            const tbody = document.getElementById('responseTableBody');
            if (tbody) tbody.innerHTML = prevSaved.tableData || "";
        }

        // UPDATE UI
        updateResponseFrameTitle();
        
        const maxFrames = parseInt(document.getElementById('page')?.value || 1);
        updateResponseButtonLabels(maxFrames);
        if (typeof updateResponseBackVisibility === "function") updateResponseBackVisibility();

        // HIGHLIGHT BOXES
        if (typeof updateResponseBoxColors === "function") updateResponseBoxColors();
        
        window.scrollTo(0, 0);
    }
}


/**
 * Save Current Response Frame to Array
 */
let currentResponsePageIndex = 0; // Tracking the current response page

function saveCurrentResponseState(index) {
    // Default to the last response count if index is not provided
    if (index === undefined || index === null) index = responseCount - 1;

    const tableRows = document.querySelectorAll('#responseTableBody tr');
    const rows = [];

    tableRows.forEach((tr, rowIndex) => {
        // 1. CHECK IF ROW IS BITWISE
        const typeSelect = tr.querySelector('select[name*="Type"], .bit-byte-select');
        const isBitwise = typeSelect && typeSelect.value === "Bitwise";

        let rowData = {};

        if (isBitwise) {
            // --- PATH A: BITWISE MODE ---
            const existingBitwise = tr.getAttribute('data-bitwise-storage');
            rowData.bitwise = existingBitwise ? JSON.parse(existingBitwise) : [];
            
            // Note: Normal fields are implicitly left out or could be set to null
        } else {
            // --- PATH B: NORMAL MODE ---
            rowData.bitwise = null; 
            rowData.res_startBytePosition = tr.querySelector('[name*="startByte"]')?.value || "";
            rowData.res_endBytePosition = tr.querySelector('[name*="endByte"]')?.value || "";
            rowData.res_numberOfBytes = tr.querySelector('[name*="noOfBytes"]')?.value || "";
            rowData.res_fieldDescription = tr.querySelector('[name*="Description"]')?.value || "";
            rowData.res_dataType = tr.querySelector('[name*="dataType"]')?.value || "";
            rowData.res_valueDefinition = tr.querySelector('[name*="valueDefinition"]')?.value || "";
            rowData.res_displayValueMin = tr.querySelector('[name*="Min"]')?.value || "";
            rowData.res_displayValueMax = tr.querySelector('[name*="Max"]')?.value || "";
            rowData.res_valueInHexMin = tr.querySelector('[name*="hexMin"]')?.value || "";
            rowData.res_valueInHexMax = tr.querySelector('[name*="hexMax"]')?.value || "";
            rowData.res_uiType = tr.querySelector('[name*="uiType"]')?.value || "";
            rowData.res_uiId = tr.querySelector('[name*="uiId"]')?.value || "";
            rowData.res_asperui = tr.querySelector('[name*="asperui"]')?.value || "";
            rowData.res_sno_asperui = tr.querySelector('[name*="SerialNo"]')?.value || "";
            rowData.res_asperuivalue = tr.querySelector('[name*="UiValue"]')?.value || "";
            rowData.res_uiPageNo = tr.querySelector('[name*="uiPageNo"]')?.value || "";
            rowData.res_uiDescriptionInOut = tr.querySelector('[name*="InOut"]')?.value || "";
            rowData.res_sliderStepValue = tr.querySelector('[name*="Step"]')?.value || "";
            rowData.res_defaultvalue = tr.querySelector('[name*="defaultValue"]')?.value || "";
             // Inside Path B of saveCurrentResponseState
rowData.res_developerNotes = tr.querySelector('[name*="DevNotes" i], [name*="developerNotes" i]')?.value || "";
      
            rowData.res_notificationEnable = tr.querySelector('[name^="enable_"]')?.value || "No";
           rowData.res_notificationMessage = tr.querySelector('input[name*="message" i], textarea[name*="message" i], [name*="msg" i]')?.value || "";  
        }

        rows.push(rowData);
    });

    // Storing in a separate array: storedResponses
    storedResponses[index] = {
        parameterName: document.getElementById('resp_parametername')?.value || "",
        direction: document.getElementById('resp_data_flow_direction')?.value || "",
        size: document.getElementById('resp_framesizeinbytes')?.value || "",
        rows: rows
    };

    // Console Logs
    console.log(`Saved to storedResponses[${index}]`, storedResponses[index]);

    console.groupCollapsed("%cFULL storedResponses ARRAY", "color: blue; font-weight: bold;");
    storedResponses.forEach((res, i) => console.log(`Index ${i}:`, res));
    console.groupEnd();
}
/**
 * UI Sync: Titles
 */
function updateResponseFrameTitle() {
    const mainTitle = document.getElementById('response-title-display');
    const subTitle = document.getElementById('response-frame-title');
    const text = `RESPONSE FRAME (${responseCount})`;
    if (mainTitle) mainTitle.innerText = text;
    if (subTitle) subTitle.innerText = text;
}

/**
 * UI Sync: Button Labels
 */
function updateResponseButtonLabels(limit) {
    const nextBtn = document.getElementById('next-response-page-btn');
    if (!nextBtn) return;

    if (responseCount === limit) {
        nextBtn.innerHTML = 'Finalize & Move to Error <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>';
        nextBtn.style.backgroundColor = "#28a745";
    } else {
        nextBtn.innerHTML = 'Next Response Frame <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>';
        nextBtn.style.backgroundColor = "";
    }
}

//ERROR FRAME
let errorCount=1;
let storedErrors = [];
let errorCompletedPages = new Set(); 
let sharedErrorParameterName = ""; 

/**
 * Handle Next Button Click (Matches Command logic)
 */
function handleNextError() {
    const maxFramesField = document.getElementById('pages'); 
    const maxFrames = parseInt(maxFramesField ? maxFramesField.value : 1) || 1;

     if (!validateDuplicateByteRange("errorTableBody", "error", "step4-error")) {
        return; // Stop execution and stay on current frame if duplicates are found
    }
   
    // 1. SAFETY GATE: Check if we are at the limit
    if (errorCount >= maxFrames) {
        alert("Success: You have completed all " + maxFrames + " error frames.");
        errorCompletedPages.add(errorCount);
        if (typeof updateErrorBoxColors === "function") updateErrorBoxColors();
        return; 
    }

    // 2. DATA SAVE: Save current state before moving forward
    saveCurrentErrorState(); 

    // 3. INCREMENT
    errorCount++;

    // 4. UPDATE PROGRESS BOXES
    if (typeof updateErrorBoxColors === "function") updateErrorBoxColors();

    // 5. UI UPDATES
    updateErrorFrameTitle();

    const tbody = document.getElementById('errorTableBody');

    // 6. LOAD EXISTING OR START FRESH
    if (storedErrors[errorCount - 1]) {
        applyErrorFrameData(storedErrors[errorCount - 1]); 
    } else {
        // Reset inputs for a truly new page
        if(document.getElementById('error_data_flow_direction')) document.getElementById('error_data_flow_direction').value = "";
        if(document.getElementById('error_framesizeinbytes')) document.getElementById('error_framesizeinbytes').value = "";
        if(document.getElementById('error_frametrigger')) document.getElementById('error_frametrigger').value = "None";
        if (tbody) tbody.innerHTML = ''; 
        if (typeof addRow === "function") addRow('error'); 
    }

    // 7. SYNC UI
    updateErrorButtonLabels(maxFrames);
    window.scrollTo(0, 0);
}

/**
 * Handle Back Button Click (Matches Command logic)
 */
function handleBackErrorFrame() {
    if (errorCount > 1) {
        saveCurrentErrorState(); 

        errorCount--;
        
        const prevSaved = storedErrors[errorCount - 1];
        if (prevSaved) {
            applyErrorFrameData(prevSaved);
        }

        // UPDATE UI
        updateErrorFrameTitle();
        
        const maxFrames = parseInt(document.getElementById('pages')?.value || 1);
        updateErrorButtonLabels(maxFrames);

        // HIGHLIGHT CIRCLES
        if (typeof updateErrorBoxColors === "function") updateErrorBoxColors();
        
        window.scrollTo(0, 0);
    }
}

// Backwards-compatible wrapper for older onclicks
function handleBackError() {
    if (typeof handleBackErrorFrame === 'function') return handleBackErrorFrame();
}

/**
 * Save Current Frame to Array
 */

function saveCurrentErrorState(index) {
    // Default to the last error count if index is not provided
    if (index === undefined || index === null) index = errorCount - 1;

    const tableRows = document.querySelectorAll('#errorTableBody tr');
    const rows = [];

    tableRows.forEach((tr, rowIndex) => {
        // 1. CHECK IF ROW IS BITWISE
        const typeSelect = tr.querySelector('select[name*="Type"], .bit-byte-select');
        const isBitwise = typeSelect && typeSelect.value === "Bitwise";

        let rowData = {};

        if (isBitwise) {
            // --- PATH A: BITWISE MODE ---
            const existingBitwise = tr.getAttribute('data-bitwise-storage');
            rowData.bitwise = existingBitwise ? JSON.parse(existingBitwise) : [];
            
            // Other fields remain undefined/excluded for a clean Bitwise object
        } else {
            // --- PATH B: NORMAL MODE ---
            rowData.bitwise = null; 
            rowData.err_startBytePosition = tr.querySelector('[name*="startByte"]')?.value || "";
            rowData.err_endBytePosition = tr.querySelector('[name*="endByte"]')?.value || "";
            rowData.err_numberOfBytes = tr.querySelector('[name*="noOfBytes"]')?.value || "";
            rowData.err_fieldDescription = tr.querySelector('[name*="Description"]')?.value || "";
            rowData.err_dataType = tr.querySelector('[name*="dataType"]')?.value || "";
            rowData.err_valueDefinition = tr.querySelector('[name*="valueDefinition"]')?.value || "";
            rowData.err_displayValueMin = tr.querySelector('[name*="Min"]')?.value || "";
            rowData.err_displayValueMax = tr.querySelector('[name*="Max"]')?.value || "";
            rowData.err_valueInHexMin = tr.querySelector('[name*="hexMin"]')?.value || "";
            rowData.err_valueInHexMax = tr.querySelector('[name*="hexMax"]')?.value || "";
            rowData.err_uiType = tr.querySelector('[name*="uiType"]')?.value || "";
            rowData.err_uiId = tr.querySelector('[name*="uiId"]')?.value || "";
            rowData.err_asperui = tr.querySelector('[name*="asperui"]')?.value || "";
            rowData.err_sno_asperui = tr.querySelector('[name*="SerialNo"]')?.value || "";
            rowData.err_asperuivalue = tr.querySelector('[name*="UiValue"]')?.value || "";
            rowData.err_uiPageNo = tr.querySelector('[name*="uiPageNo"]')?.value || "";
            rowData.err_uiDescriptionInOut = tr.querySelector('[name*="InOut"]')?.value || "";
            rowData.err_sliderStepValue = tr.querySelector('[name*="Step"]')?.value || "";
            rowData.err_defaultvalue = tr.querySelector('[name*="defaultValue"]')?.value || "";
           
           // Inside Path B of saveCurrentErrorState
rowData.err_developerNotes = tr.querySelector('[name*="DevNotes" i], [name*="developerNotes" i]')?.value || "";
            rowData.err_notificationEnable = tr.querySelector('[name^="enable_"]')?.value || "No";
           rowData.err_notificationMessage = tr.querySelector('input[name*="message" i], textarea[name*="message" i], [name*="msg" i]')?.value || "";

              
        }

        rows.push(rowData);
    });

    // Storing in a separate array: storedErrors
    storedErrors[index] = {
        parameterName: document.getElementById('error_parametername')?.value || "",
        direction: document.getElementById('error_data_flow_direction')?.value || "",
        size: document.getElementById('error_framesizeinbytes')?.value || "",
        rows: rows
    };

    // Console Logs
    console.log(`Saved to storedErrors[${index}]`, storedErrors[index]);

    console.groupCollapsed("%cFULL storedErrors ARRAY", "color: red; font-weight: bold;");
    storedErrors.forEach((err, i) => console.log(`Index ${i}:`, err));
    console.groupEnd();
}
/**
 * Helper: Fill fields with saved data
 */
function applyErrorFrameData(data) {
    if(document.getElementById('error_parametername')) document.getElementById('error_parametername').value = sharedErrorParameterName || data.parameterName || "";
    if(document.getElementById('error_data_flow_direction')) document.getElementById('error_data_flow_direction').value = data.direction || "";
    if(document.getElementById('error_framesizeinbytes')) document.getElementById('error_framesizeinbytes').value = data.size || "";
    if(document.getElementById('error_frametrigger')) document.getElementById('error_frametrigger').value = data.trigger || "";
    
    const tableBody = document.getElementById('errorTableBody');
    if (tableBody) tableBody.innerHTML = data.tableData || "";
}

/**
 * Button Visuals & Title
 */
function updateErrorButtonLabels(limit) {
    const nextBtn = document.getElementById('next-error-page-btn');
    if (!nextBtn) return;

    if (errorCount === limit) {
        nextBtn.innerText = "Finalize Error Frames";
        nextBtn.style.backgroundColor = "#28a745"; 
    } else {
        nextBtn.innerText = "Next Error Frame";
        nextBtn.style.backgroundColor = ""; 
    }

    const backBtn = document.getElementById('back-error-page-btn');
    if (backBtn) {
        backBtn.style.display = (errorCount > 1) ? 'inline-block' : 'none';
    }
}

function updateErrorFrameTitle() {
    const titleDisplay = document.getElementById('error-title-display');
    const titleHeader = document.getElementById('error-frame-title');
    const text = `ERROR FRAME (${errorCount})`;
    if (titleDisplay) titleDisplay.innerText = text;
    if (titleHeader) titleHeader.innerText = text;
}
function updateErrorBackVisibility() {
    const backBtn = document.getElementById('back-error-page-btn');
    if (backBtn) {
        backBtn.style.display = (errorCount > 1) ? 'inline-block' : 'none';
    }
}

//save data


//
function showConfigurationSavedAlert(message) {
    const text = message || 'Configuration saved for this page.';
    if (typeof showSuccessMessage === 'function') {
        showSuccessMessage(text);
    } else {
        alert(text);
    }
}

function saveCommandData() {
    saveCurrentCommandState();
    saveCurrentCommandFrame();

    const itemData = {
    brandname: document.getElementById('brandname')?.value || "",
        productname: document.getElementById('productname')?.value || "COOKER",
    modelname: document.getElementById('modelname')?.value || "",
        productdescription: document.getElementById('productdescription')?.value || "des",
        id: document.getElementsByName('id')[0]?.value || "",
        cmd_parametername: document.getElementById('com_parametername')?.value || "",
        cmd_data_flow_direction: document.getElementById('cmd_data_flow_direction')?.value || "",
        cmd_frametype: document.getElementById('cmd_frametype')?.value || "",
        cmd_framesizeinbytes: document.getElementById('cmd_framesizeinbytes')?.value || "",
        cmd_noofrows: document.getElementById('cmd_noofrows')?.value || "0",
        cmd_frametrigger: document.getElementById('cmd_frame_trigger_options')?.value || "",
        cmd_multipleui: document.getElementById('cmd_multipleui')?.value || "",
        cmd_triggerui: document.getElementById('cmd_triggerui')?.value || "",
        cmd_voice_command: document.getElementById('cmd_voice_command')?.value || "",
        cmd_voice_control_instruction: document.getElementById('cmd_voice_control_instruction')?.value || "",
        cmd_remarks: document.getElementById('cmd_remarks')?.value || "",
        numberofpages: storedCommands.length,
        date: new Date().toISOString().split('T')[0],
        itemid: 153,
        cmd_frames: storedCommands,
    };

    console.log("--- COMMAND FRAME SAVE ---");

    // FULL ARRAY DISPLAY (no hiding)
    logFull("Item Data (FULL):", itemData);
    logFull("Command Frames (FULL):", itemData.cmd_frames);

    printCmdConsoleFromItemData(itemData);
    showConfigurationSavedAlert('Command frame configuration saved for this page.');
}



//
let currentPageIndex = 0;


// Save current page's table data
function saveCurrentCommandState(index) {
    if (index === undefined || index === null) index = commandCount - 1;

    const tableRows = document.querySelectorAll('#additionalTableBody tr');
    const rows = [];

    tableRows.forEach((tr, rowIndex) => {
        // 1. CHECK IF ROW IS BITWISE
        // Check the dropdown value. Replace '[name*="bitByte"]' with your actual selector if different.
        const typeSelect = tr.querySelector('select[name*="Type"], .bit-byte-select');
        const isBitwise = typeSelect && typeSelect.value === "Bitwise";

        let rowData = {};

        if (isBitwise) {
            // --- PATH A: BITWISE MODE (Saves Bitwise Array, Ignores Inputs) ---
            const existingBitwise = tr.getAttribute('data-bitwise-storage');
            rowData.bitwise = existingBitwise ? JSON.parse(existingBitwise) : [];

            // Set normal fields to null so they are ignored by the console/logs
            

            // Add any other fields you want to suppress in the console here
        } else {
            // --- PATH B: NORMAL MODE (Current behavior) ---
            rowData.bitwise = null; 
            rowData.cmd_startBytePosition = tr.querySelector('[name*="startByte"]')?.value || "";
            rowData.cmd_endBytePosition = tr.querySelector('[name*="endByte"]')?.value || "";
            rowData.cmd_numberOfBytes = tr.querySelector('[name*="noOfBytes"]')?.value || "";
            rowData.cmd_fieldDescription = tr.querySelector('[name*="Description"]')?.value || "";
            rowData.cmd_dataType = tr.querySelector('[name*="dataType"]')?.value || "";
            rowData.cmd_valueDefinition = tr.querySelector('[name*="valueDefinition"]')?.value || "";
            rowData.cmd_displayValueMin = tr.querySelector('[name*="Min"]')?.value || "";
            rowData.cmd_displayValueMax = tr.querySelector('[name*="Max"]')?.value || "";
            rowData.cmd_valueInHexMin = tr.querySelector('[name*="hexMin"]')?.value || "";
            rowData.cmd_valueInHexMax = tr.querySelector('[name*="hexMax"]')?.value || "";
            rowData.cmd_uiType = tr.querySelector('[name*="uiType"]')?.value || "";
            rowData.cmd_uiId = tr.querySelector('[name*="uiId"]')?.value || "";
            rowData.cmd_asperui = tr.querySelector('[name*="asperui"]')?.value || "";
            rowData.cmd_sno_asperui = tr.querySelector('[name*="SerialNo"]')?.value || "";
            rowData.cmd_asperuivalue = tr.querySelector('[name*="UiValue"]')?.value || "";
            rowData.cmd_uiPageNo = tr.querySelector('[name*="uiPageNo"]')?.value || "";
            rowData.cmd_uiDescriptionInOut = tr.querySelector('[name*="InOut"]')?.value || "";
            rowData.cmd_sliderStepValue = tr.querySelector('[name*="Step"]')?.value || "";
            rowData.cmd_defaultvalue = tr.querySelector('[name*="defaultValue"]')?.value || "";
           
           // Inside Path B of saveCurrentCommandState
       rowData.cmd_developerNotes = tr.querySelector('[name*="DevNotes" i], [name*="developerNotes" i]')?.value || "";
 
            rowData.cmd_notificationEnable = tr.querySelector('[name^="enable_"]')?.value || "No";
            
           rowData.cmd_notificationMessage = tr.querySelector('input[name*="message" i], textarea[name*="message" i], [name*="msg" i]')?.value || ""; 
        }

        rows.push(rowData);
    });

    storedCommands[index] = {
        parameterName: document.getElementById('com_parametername')?.value || "",
        direction: document.getElementById('cmd_data_flow_direction')?.value || "",
        size: document.getElementById('cmd_framesizeinbytes')?.value || "",
        rows: rows
    };


    // Old console logs (keep untouched)
    console.log(`Saved to storedCommands[${index}]`, storedCommands[index]);

    // NEW: Full storedCommands log for inspection without disturbing old logs
    console.groupCollapsed("%cFULL storedCommands ARRAY", "color: green; font-weight: bold;");
    storedCommands.forEach((cmd, i) => console.log(`Index ${i}:`, cmd));
    console.groupEnd();
}

//
function printCmdConsoleFromItemData(itemData) {
    console.log("%c --- DATA SYNC REPORT ---", "color: white; background: #222; font-weight: bold;");

    // 1. Find where the frames/pages are stored (tries both common names)
    const frames = itemData.cmd_frames || itemData.commands || [];

    if (!Array.isArray(frames) || frames.length === 0) {
        console.warn("No command frames found in the data object.");
        return;
    }

    frames.forEach((page, cmdIndex) => {
        // 2. Find the rows array inside the page object
        const rowsToProcess = page.rows || (Array.isArray(page) ? page : []);

        if (rowsToProcess.length === 0) {
            console.log(`Cmd[${cmdIndex}] contains no row data.`);
            return;
        }

        rowsToProcess.forEach((row, rowIndex) => {
            const rowNo = rowIndex + 1;

            // 3. Handle Bitwise Data
            if (row.bitwise && Array.isArray(row.bitwise)) {
                row.bitwise.forEach((bit, bitIdx) => {
                    printFormattedEntries(bit, `Cmd[${cmdIndex}]-row[${rowNo}]-bit[${bitIdx + 1}]`);
                });
            }

            // 4. Handle Bytewise Data
            printFormattedEntries(row, `Cmd[${cmdIndex}]-row[${rowNo}]`);
        });
    });
}
// Helper function to clean keys and print
function printFormattedEntries(dataObj, prefix) {
    Object.entries(dataObj).forEach(([key, value]) => {
        // Skip nested objects/arrays to keep logs clean
        if (typeof value === 'object' && value !== null) return;

        let cleanKey = key
            .replace(/^cmd_cmd_/, '')
            .replace(/^cmd_/, '')
            .replace(/_\d+$/, '');

        console.log(`${prefix}-${cleanKey} => ${value}`);
    });
}

//

//


// Save current page's table data

// Submit all pages to backend
//
//

/**
 * Function specifically for the Step 3 Save Button
 */

function saveResponseData() {
    try {
        // 1. Sync the current UI state to the array
        saveCurrentResponseState();

        // 2. Map the "Item Data" (Matching Command Frame structure)
        const itemData = {
    brandname: document.getElementById('brandname')?.value || "",
            productname: document.getElementById('productname')?.value || "COOKER",
    modelname: document.getElementById('modelname')?.value || "",
            productdescription: document.getElementById('productdescription')?.value || "",
            
            // Meta Information
            resp_parametername: document.getElementById('resp_parametername')?.value || "",
            resp_data_flow_direction: document.getElementById('resp_data_flow_direction')?.value || "",
            resp_frametype: document.getElementById('resp_frametype')?.value || "",
            resp_framesizeinbytes: document.getElementById('resp_framesizeinbytes')?.value || "",
            resp_noofrows: document.getElementById('resp_noofrows')?.value || "0",
            resp_frametrigger: document.getElementById('resp_frametrigger')?.value || "",
            resp_multipleui: document.getElementById('resp_multipleui')?.value || "",
            resp_triggerui: document.getElementById('resp_triggerui')?.value || "",
            resp_voice_command: document.getElementById('resp_voice_command')?.value || "",
            resp_voice_control_instruction: document.getElementById('resp_voice_control_instruction')?.value || "",
            resp_remarks: document.getElementById('resp_remarks')?.value || "",
            
            // Sync logic
            numberofpages: storedResponses.length,
            date: new Date().toISOString().split('T')[0],
            resp_frames: storedResponses // Similar to cmd_frames
        };

        console.log("--- RESPONSE FRAME SAVE ---");
        console.log("Item Data (raw):", itemData);

        // 3. Print to console using the shared formatter
        printRespConsoleFromItemData(itemData);

        showConfigurationSavedAlert('Response frame configuration saved for this page.');

    } catch (err) {
        console.error("Critical Error in saveResponseData:", err);
    }
}

 //
function printRespConsoleFromItemData(itemData) {
    console.log("%c --- RESPONSE DATA SYNC REPORT ---", "color: white; background: #28a745; font-weight: bold;");

    const frames = itemData.resp_frames || [];

    frames.forEach((page, respIndex) => {
        const rowsToProcess = page.rows || [];

        rowsToProcess.forEach((row, rowIndex) => {
            const rowNo = rowIndex + 1;

            // Handle Bitwise
            if (row.bitwise && Array.isArray(row.bitwise)) {
                row.bitwise.forEach((bit, bitIdx) => {
                    printFormattedEntries(bit, `Resp[${respIndex}]-row[${rowNo}]-bit[${bitIdx + 1}]`);
                });
            }

            // Handle Bytewise
            printFormattedEntries(row, `Resp[${respIndex}]-row[${rowNo}]`);
        });
    });
}

// Enhanced helper to clean both cmd_ and resp_ prefixes
function printFormattedEntries(dataObj, prefix) {
    Object.entries(dataObj).forEach(([key, value]) => {
        if (typeof value === 'object' && value !== null) return;

        let cleanKey = key
            .replace(/^(cmd_|resp_)/g, '') // Removes cmd_ or resp_
            .replace(/^(cmd_|resp_)/g, '') // Double check for nested prefixes
            .replace(/_\d+$/, '');

        console.log(`${prefix}-${cleanKey} => ${value}`);
    });
}
function loadResponseState(index) {
    const data = storedResponses[index];
    if (!data || !data.rows) {
        // If no saved data exists for this page, clear the table
        document.getElementById('responseTableBody').innerHTML = "";
        return;
    }

    // 1. Restore Top Metadata
    document.getElementById('resp_parametername').value = data.parameterName || "";
    document.getElementById('resp_data_flow_direction').value = data.direction || "";
    document.getElementById('resp_framesizeinbytes').value = data.size || "";

    // 2. Reconstruct Table Rows
    const tableBody = document.getElementById('responseTableBody');
    tableBody.innerHTML = ""; // Clear current UI

    data.rows.forEach((row) => {
        const newRow = createResponseRow(); // Call your existing function that creates a blank <tr>
        
        // Fill the inputs in the new row
        // We use the 'resp_' prefix keys we saved earlier
        newRow.querySelector('[name*="startByte"]').value = row.resp_startBytePosition;
        newRow.querySelector('[name*="endByte"]').value = row.resp_endBytePosition;
        newRow.querySelector('[name*="numberOfBytes"]').value = row.resp_numberOfBytes;
        newRow.querySelector('[name*="bitSize"]').value = row.resp_bitSize;
        newRow.querySelector('[name*="Description"]').value = row.resp_fieldDescription;
        newRow.querySelector('[name*="dataType"]').value = row.resp_dataType;
        newRow.querySelector('[name*="valueDefinition"]').value = row.resp_valueDefinition;
        newRow.querySelector('[name*="displayValueMin"]').value = row.resp_displayValueMin;
        newRow.querySelector('[name*="displayValueMax"]').value = row.resp_displayValueMax;
        newRow.querySelector('[name*="uiType"]').value = row.resp_uiType;
        // ... add any other fields you need here ...

        // 3. Restore Bitwise Data attribute if it exists
        if (row.bitwise) {
            newRow.setAttribute('data-bitwise-storage', JSON.stringify(row.bitwise));
        }

        tableBody.appendChild(newRow);
    });
}
//ERROR
// ==========================================
// 2. THE SAVE FUNCTION
// ==========================================
// --- MANDATORY GLOBALS (Add these at the top) ---
function saveErrorData() {
    try {
        // 1. Ensure the current UI state is saved to the storedErrors array first
        saveCurrentErrorState();

        // 2. Map EVERY field into the final data object
        const itemData = {
            // --- Global Meta (Step 1) ---
    brandname: document.getElementById('brandname')?.value || "",
            productname: document.getElementById('productname')?.value || "COOKER",
    modelname: document.getElementById('modelname')?.value || "",
            productdescription: document.getElementById('productdescription')?.value || "",
            id: document.getElementsByName('id')[0]?.value || "", // Database ID if exists
            itemid: 153, // Your static/system ID

            // --- Error Meta (Step 3 - Top Section) ---
            error_parametername: document.getElementById('error_parametername')?.value || "",
            error_data_flow_direction: document.getElementById('error_data_flow_direction')?.value || "",
            error_frametype: document.getElementById('error_frametype')?.value || "",
            error_framesizeinbytes: document.getElementById('error_framesizeinbytes')?.value || "",
            error_noofrows: document.getElementById('error_noofrows')?.value || "0",
            error_frametrigger: document.getElementById('error_frametrigger')?.value || "",
            error_multipleui: document.getElementById('error_multipleui')?.value || "",
            error_triggerui: document.getElementById('error_triggerui')?.value || "",
            error_voice_command: document.getElementById('error_voice_command')?.value || "",
            error_voice_control_instruction: document.getElementById('error_voice_control_instruction')?.value || "",
            error_remarks: document.getElementById('error_remarks')?.value || "",

            // --- Multi-Page Data ---
            numberofpages: (typeof storedErrors !== 'undefined') ? storedErrors.length : 1,
            error_frames: (typeof storedErrors !== 'undefined') ? storedErrors : [],

            // --- System Fields ---
            date: new Date().toISOString().split('T')[0],
            error_sequence: (typeof errorCount !== 'undefined') ? errorCount : 1,
            status: "saved"
        };

        // 3. Console Reporting
        console.log("%c --- FINAL ERROR DATA OBJECT --- ", "background: #dc3545; color: #ffffff; font-weight: bold;");
        console.log("Full Item Data:", itemData);

        // 4. Trigger your print report (if you have the function defined)
        if (typeof printErrConsoleFromItemData === "function") {
            printErrConsoleFromItemData(itemData);
        }

        showConfigurationSavedAlert('Error frame configuration saved for this page.');

    } catch (err) {
        console.error("Critical Error in saveErrorData:", err);
    }
}

//
function printErrConsoleFromItemData(itemData) {
    console.log("%c --- ERROR DATA SYNC REPORT ---", "color: white; background: #dc3545; font-weight: bold;");

    const frames = itemData.error_frames || [];

    frames.forEach((page, errIndex) => {
        // Use the 'rows' array we created in saveCurrentErrorState
        const rowsToProcess = page.rows || [];

        rowsToProcess.forEach((row, rowIndex) => {
            const rowNo = rowIndex + 1;

            // Handle Bitwise (Error specific)
            if (row.bitwise && Array.isArray(row.bitwise)) {
                row.bitwise.forEach((bit, bitIdx) => {
                    printFormattedEntries(bit, `Err[${errIndex}]-row[${rowNo}]-bit[${bitIdx + 1}]`);
                });
            }

            // Handle Bytewise
            printFormattedEntries(row, `Err[${errIndex}]-row[${rowNo}]`);
        });
    });
}

// Reuse your existing printFormattedEntries helper—it already handles cleaning err_ prefixes!
//
function loadErrorState(index) {
    const data = storedErrors[index];
    if (!data) {
        // If no saved data exists for this page, clear the table
        const tbody = document.getElementById('errorTableBody');
        if (tbody) tbody.innerHTML = "";
        return;
    }

    // 1. Restore Top Metadata (All Fields)
    if(document.getElementById('error_parametername')) document.getElementById('error_parametername').value = data.error_parametername || "";
    if(document.getElementById('error_data_flow_direction')) document.getElementById('error_data_flow_direction').value = data.error_data_flow_direction || "";
    if(document.getElementById('error_frametype')) document.getElementById('error_frametype').value = data.error_frametype || "";
    if(document.getElementById('error_framesizeinbytes')) document.getElementById('error_framesizeinbytes').value = data.error_framesizeinbytes || "";
    if(document.getElementById('error_noofrows')) document.getElementById('error_noofrows').value = data.error_noofrows || "0";
    if(document.getElementById('error_frametrigger')) document.getElementById('error_frametrigger').value = data.error_frametrigger || "";
    if(document.getElementById('error_multipleui')) document.getElementById('error_multipleui').value = data.error_multipleui || "";
    if(document.getElementById('error_triggerui')) document.getElementById('error_triggerui').value = data.error_triggerui || "";
    if(document.getElementById('error_voice_command')) document.getElementById('error_voice_command').value = data.error_voice_command || "";
    if(document.getElementById('error_voice_control_instruction')) document.getElementById('error_voice_control_instruction').value = data.error_voice_control_instruction || "";
    if(document.getElementById('error_remarks')) document.getElementById('error_remarks').value = data.error_remarks || "";

    // 2. Reconstruct Table Rows from the saved 'rows' array
    const tableBody = document.getElementById('errorTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = ""; // Clear existing UI rows

    if (data.rows && Array.isArray(data.rows)) {
        data.rows.forEach((row) => {
            // Use your function that creates a blank row for the error table
            const newRow = createErrorRow(); 
            
            // Map saved values back into the row inputs
            newRow.querySelector('[name*="startByte"]').value = row.err_startBytePosition || "";
            newRow.querySelector('[name*="endByte"]').value = row.err_endBytePosition || "";
            newRow.querySelector('[name*="numberOfBytes"]').value = row.err_numberOfBytes || "";
            newRow.querySelector('[name*="bitSize"]').value = row.err_bitSize || "";
            newRow.querySelector('[name*="Description"]').value = row.err_fieldDescription || "";
            newRow.querySelector('[name*="dataType"]').value = row.err_dataType || "";
            newRow.querySelector('[name*="valueDefinition"]').value = row.err_valueDefinition || "";
            newRow.querySelector('[name*="displayValueMin"]').value = row.err_displayValueMin || "";
            newRow.querySelector('[name*="displayValueMax"]').value = row.err_displayValueMax || "";
            newRow.querySelector('[name*="valueInHexMin"]').value = row.err_valueInHexMin || "";
            newRow.querySelector('[name*="valueInHexMax"]').value = row.err_valueInHexMax || "";
            newRow.querySelector('[name*="uiType"]').value = row.err_uiType || "";
            newRow.querySelector('[name*="uiId"]').value = row.err_uiId || "";
            newRow.querySelector('[name*="asperui"]').value = row.err_asperui || "";
            newRow.querySelector('[name*="sno_asperui"]').value = row.err_sno_asperui || "";
            newRow.querySelector('[name*="asperuivalue"]').value = row.err_asperuivalue || "";
            newRow.querySelector('[name*="uiPageNo"]').value = row.err_uiPageNo || "";
            newRow.querySelector('[name*="uiDescriptionInOut"]').value = row.err_uiDescriptionInOut || "";
            newRow.querySelector('[name*="sliderStepValue"]').value = row.err_sliderStepValue || "";
            newRow.querySelector('[name*="defaultvalue"]').value = row.err_defaultvalue || "";
            newRow.querySelector('[name*="developerNotes"]').value = row.err_developerNotes || "";

            // 3. Restore Bitwise Data attribute for the modal
            if (row.bitwise) {
                newRow.setAttribute('data-bitwise-storage', JSON.stringify(row.bitwise));
            }

            tableBody.appendChild(newRow);
        });
    }
}
// ==========================================
// 3. THE "ADD ANOTHER" BUTTON LOGIC
// ==========================================
function handleAddAnotherError() {
    // 1. Save current page data to globalStoredErrors[0] (or current index)
    saveErrorData();

    // 2. Increment the global counter
    errorCount++;

    // 3. Clear UI for next page
    const tableBody = document.getElementById('errorTableBody');
    if (tableBody) tableBody.innerHTML = "";
    
    // Reset row count input for new page
    const rowCountInput = document.getElementById('error_noofrows');
    if (rowCountInput) rowCountInput.value = 0;

    // Keep the name consistent
    document.getElementById('error_parametername').value = sharedErrorParameterName;

    // Update the UI Title
    const title = document.getElementById('error-title-display');
    if (title) title.innerText = `ERROR FRAME (${errorCount})`;
    
    console.log(`Ready for Frame ${errorCount} at Index [${errorCount - 1}]`);
}

/**
 * Helper to sync the visible table rows into the storedErrors array.
 * This function should be called by your 'Add Another Error' button 
 * or a specific 'Save Table' button within the table area.
 */
function saveCurrentErrorFrame() {
    const rows = [];
    document.querySelectorAll('#errorTableBody tr').forEach(tr => {
        const rowData = {};
        tr.querySelectorAll('input, select').forEach(input => {
            const identifier = input.className.split(' ')[0] || input.name || "input";
            const key = "error_" + identifier;
            rowData[key] = input.value;
        });
        rows.push(rowData);
    });
    
    const idx = (typeof errorCount !== 'undefined') ? errorCount - 1 : 0;
    
    if (typeof storedErrors !== 'undefined') {
        storedErrors[idx] = rows;
    }
}


 // Capture Command Table rows into the array

// 1. THIS IS THE MISSING FUNCTION CAUSING YOUR ERROR


function saveCurrentCommandFrame() {
    const rows = [];
    document.querySelectorAll('#additionalTableBody tr').forEach(tr => {
        const rowData = {};
        // Find every input in this row and map to a 'cmd_' key
        tr.querySelectorAll('input, select').forEach(input => {
            // Use the class name or name attribute to determine the key
            const key = "cmd_" + (input.className.split(' ')[0] || input.name);
            rowData[key] = input.value;
        });
        rows.push(rowData);
    });
    
    const idx = (typeof commandCount !== 'undefined') ? commandCount - 1 : 0;
    if (typeof storedCommands !== 'undefined') storedCommands[idx] = rows;
}
  
/**
 * Capture Response Table rows into the array
 */
function saveCurrentResponseFrame() {
    const rows = [];
    document.querySelectorAll('#responseTableBody tr').forEach(tr => {
        const rowData = {};
        tr.querySelectorAll('input, select').forEach(input => {
            const key = "resp_" + (input.className.split(' ')[0] || input.name);
            rowData[key] = input.value;
        });
        rows.push(rowData);
    });
    
    const idx = (typeof responseCount !== 'undefined') ? responseCount - 1 : 0;
    if (typeof storedResponses !== 'undefined') storedResponses[idx] = rows;
}

/**
 * Formatter to mimic PHP print_r / Array() structure
 */
function formatAsPHPArray(obj, indent = "    ") {
    if (obj === null || obj === undefined) return "NULL";
    let output = "Array\n" + indent.substring(4) + "(\n";
    for (const key in obj) {
        if (obj.hasOwnProperty(key)) {
            const value = obj[key];
            output += `${indent}[${key}] => `;
            if (Array.isArray(value) || (typeof value === 'object' && value !== null)) {
                output += formatAsPHPArray(value, indent + "    ");
            } else {
                output += value;
            }
            output += "\n";
        }
    }
    output += indent.substring(4) + ")";
    return output;
}

// Dummy function to prevent the "ReferenceError" from your logs
function updateMoveButtons() { return true; }
//BITWISE
function toggleBitwiseFields(select, prefix, index) {
    const container = document.getElementById(`bitwiseContainer_${prefix}_${index}`);
    if (container) {
        if (select.value === "Data") {
            container.style.display = "block";
        } else {
            container.style.display = "none";
            // Clear values when hidden so validation doesn't get confused
            container.querySelectorAll('input').forEach(input => input.value = "");
        }
    }
}
//
function validateTableStep(tableBodyId, errorId, isMandatory) {
    let isValid = true;
    let globalBitwiseError = "";
    const rows = document.querySelectorAll(`#${tableBodyId} tr`);
    const errorElement = document.getElementById(errorId);
    let firstInvalidField = null;

    // Track bit usage per byte: { "byteIndex": [bit0, bit1...bit7] }
    const byteMap = {};

    if (rows.length === 0) return isMandatory ? false : true;

    for (let rowIndex = 0; rowIndex < rows.length; rowIndex++) {
        const row = rows[rowIndex];
        row.classList.remove("pink-highlight");
        const typeSelect = row.querySelector('select[name*="Type"], .bit-byte-select');
        const startByte = row.querySelector('[name*="startByte"]')?.value;
        const isBitwise = typeSelect?.value === "Bitwise";

        // 1. STANDARD FIELD VALIDATION (Empty checks)
        const inputs = row.querySelectorAll('input, select');
        inputs.forEach(field => {
            if (field.type === "hidden" || field.disabled) return;
            const isVisible = field.offsetParent !== null || field.nextElementSibling?.classList?.contains('api-smart-select');
            if (isVisible) {
                if (!field.value || !field.value.trim() || field.value === "Error") {
                    markFieldWarning(field);
                    row.classList.add("pink-highlight");
                    if (!firstInvalidField) firstInvalidField = field;
                    isValid = false;
                } else {
                    clearFieldWarning(field);
                }
            }
        });

        // 2. CROSS-ROW BITWISE OVERLAP VALIDATION
        const endByte = row.querySelector('[name*="endByte"]')?.value;
        const parsedStartByte = startByte === undefined || startByte === '' ? null : parseInt(startByte, 10);
        const parsedEndByte = endByte === undefined || endByte === '' ? null : parseInt(endByte, 10);
        if (
            parsedStartByte !== null &&
            parsedEndByte !== null &&
            !Number.isNaN(parsedStartByte) &&
            !Number.isNaN(parsedEndByte) &&
            parsedEndByte <= parsedStartByte
        ) {
            isValid = false;
            globalBitwiseError = `Byte Range Error: End byte must be greater than start byte in Row ${rowIndex + 1}.`;
            const startByteInput = row.querySelector('[name*="startByte"]');
            const endByteInput = row.querySelector('[name*="endByte"]');
            if (typeof setByteRangeWarning === 'function') {
                setByteRangeWarning(startByteInput, endByteInput, 'End byte must be greater than start byte. Example: start 0, end 1.');
            } else {
                markFieldWarning(startByteInput);
                markFieldWarning(endByteInput);
            }
            row.classList.add("pink-highlight");
            if (!firstInvalidField) firstInvalidField = endByteInput || startByteInput;
        }

        if (isBitwise && startByte) {
            const bitwiseDataRaw = row.getAttribute('data-bitwise-storage');
            if (bitwiseDataRaw) {
                const bitwiseEntries = JSON.parse(bitwiseDataRaw);

                // Initialize bit tracker for this specific byte if it doesn't exist
                if (!byteMap[startByte]) {
                    byteMap[startByte] = new Array(8).fill(false);
                }

                for (let entry of bitwiseEntries) {
                    let sBit = parseInt(entry.bitStart);
                    let eBit = parseInt(entry.bitEnd);

                    for (let b = sBit; b <= eBit; b++) {
                        if (byteMap[startByte][b]) {
                            // BIT IS ALREADY TAKEN BY A PREVIOUS ROW
                            isValid = false;
                            globalBitwiseError = `Overlap Error: Byte ${startByte}, Bit ${b} is repeated in Row ${rowIndex + 1}.`;
                            row.classList.add("error");
                            row.classList.add("pink-highlight");
                            if (!firstInvalidField) firstInvalidField = row.querySelector('input, select');
                            break; 
                        }
                        byteMap[startByte][b] = true;
                    }
                    if (!isValid) break;
                }
            }
        }
        if (!isValid && globalBitwiseError) break; // Exit loop on first bit error
    }

    // 3. UI ERROR DISPLAY
    if (errorElement) {
        if (!isValid) {
            errorElement.innerText = globalBitwiseError || "Please fill all required fields.";
            errorElement.style.display = "block";
            forceMessage(errorElement.innerText);
            moveToWarningField(firstInvalidField);
        } else {
            errorElement.innerText = "";
            errorElement.style.display = "none";
        }
    }

    return isValid;
}
//
// Store previous selection so we can revert if needed
// Store previous selection per row
let previousBitByteValue = {};

function getSmartSelectWrap(selectEl) {
    return selectEl && selectEl.nextElementSibling && selectEl.nextElementSibling.classList.contains('api-smart-select')
        ? selectEl.nextElementSibling
        : null;
}

function setSmartSelectVisible(selectEl, isVisible) {
    if (!selectEl) return;
    if (isVisible) {
        selectEl.style.display = 'inline-block';
        if (typeof window.syncApiSmartSelects === 'function') {
            window.syncApiSmartSelects(selectEl.parentElement || selectEl);
        }
    } else {
        selectEl.style.display = 'none';
    }

    const smartWrap = getSmartSelectWrap(selectEl);
    if (smartWrap) {
        smartWrap.style.display = isVisible ? '' : 'none';
    }
}

function handleFieldDescriptionChange(selectEl) {
    const container = selectEl.closest('.field-desc-inline');
    if (!container) return;
    const bitByte = container.querySelector('.bit-byte-select');
    const arrow = container.querySelector('.arrow-btn');
    if (!bitByte || !arrow) return;

    if (selectEl.value === 'Data') {
        setSmartSelectVisible(bitByte, true);
        if (bitByte.value === 'Bitwise') {
            arrow.style.display = 'inline-block';
            arrow.classList.add('is-visible');
        } else {
            arrow.style.display = 'none';
            arrow.classList.remove('is-visible');
        }
    } else {
        setSmartSelectVisible(bitByte, false);
        arrow.style.display = 'none';
        arrow.classList.remove('is-visible');
        bitByte.value = 'Bytewise';
    }
}

function handleBitByteChange(selectEl) {
    const row = selectEl.closest('tr');
    const currentValue = selectEl.value;
    const rowKey = row.rowIndex;
    const prevValue = previousBitByteValue[rowKey] || 'Bytewise';

    if (currentValue !== prevValue) {
        const confirmed = confirm(
            `Are you sure you want to switch from ${prevValue} to ${currentValue}?\nThis will reset Bitwise values.`
        );

        if (!confirmed) {
            selectEl.value = prevValue;
            const arrow = row ? row.querySelector('.arrow-btn') : null;
            if (arrow) arrow.classList.toggle('is-visible', prevValue === 'Bitwise');
            return;
        }

        previousBitByteValue[rowKey] = currentValue;
        const arrow = row ? row.querySelector('.arrow-btn') : null;
        if (arrow) arrow.classList.toggle('is-visible', currentValue === 'Bitwise');

        if (currentValue === 'Bitwise') {
            // Automatically open the bitwise table
           openBitwiseConfig(row);
        } else {
            // Restore row to bytewise
            restoreRowToBytewise(row);   
        }
    } else {
        if (currentValue === 'Bitwise') {
            openBitwiseConfig(row);
        }
    }
};
//
function reopenBitwiseModal(selectEl) {

    if (selectEl.value === 'Bitwise') {
        const row = selectEl.closest('tr');
        openBitwiseConfig(row);
    }
}

// Reset a row's Bitwise inputs back to editable Bytewise defaults
function restoreRowToBytewise(row) {
    if (!row) return;

    // 1️⃣ Restore original HTML (full bytewise UI)
    if (row.dataset.originalHtml) {
        row.innerHTML = row.dataset.originalHtml;
    }

    // 2️⃣ REMOVE bitwise stored data
    row.removeAttribute('data-bitwise-storage');

    // 3️⃣ Clear previous Bitwise tracking (VERY IMPORTANT)
    const rowIndex = row.rowIndex;
    if (typeof previousBitByteValue !== 'undefined') {
        delete previousBitByteValue[rowIndex];
    }
}

//


     
//
 let activeMainRow = null;

function openBitwiseConfig(row) {

    activeMainRow = row;

    if (!activeMainRow) return;

   
       
    // 🔥 SAVE ORIGINAL BYTEWISE ROW ONLY ONCE
  
    
     if (!activeMainRow.dataset.originalHtml) {
        activeMainRow.dataset.originalHtml = activeMainRow.innerHTML;
    }


    // ------------------------
    // MAIN TABLE
    // ------------------------
    const columns = activeMainRow.cells;

    // Lock start and end byte inputs readonly and style them
    [1, 2].forEach(i => {
        const cell = columns[i];
        if (!cell) return;
         
        const input = cell.querySelector('input');
        if (input) {
            input.readOnly = true;

            input.style.backgroundColor = '#ddd';
            input.style.border = '1px solid #aaa';
            input.style.textAlign = 'center';
            input.style.color = '#555';
        }
    });

    // Show readonly boxes for other columns (index > 2 and no buttons)
    for (let i = 0; i < columns.length; i++) {
        if (i > 2 && !columns[i].querySelector('button')) {
            columns[i].style.visibility = 'visible';
            columns[i].innerHTML = '';
          
            
            // Show saved value if exists
            let valueToShow = '';
            const savedData = JSON.parse(activeMainRow.getAttribute('data-bitwise-storage') || '[]');
            if (savedData.length > 0) {
                valueToShow = savedData[0].defaultvalue_1 || savedData[0].bitwise_parameter_name_1 || '';
            }

            // Create readonly styled input box
            const box = document.createElement('input');
            box.type = 'text';
            box.readOnly = true;
            box.value = valueToShow;

            box.style.width = '50px';
            box.style.height = '25px';
            box.style.backgroundColor = '#ddd';
            box.style.border = '1px solid #aaa';
            box.style.textAlign = 'center';
            box.style.color = '#555';
            columns[i].appendChild(box);
        }
    }

    // ------------------------
    // MODAL TABLE: Load saved Bitwise values
    // ------------------------
    const tbody = document.getElementById('bitwiseTableBody');
    if (!tbody) {
        alert('Error: bitwiseTableBody element not found!');
        return;
    }
    tbody.innerHTML = ''; // clear old rows

    const savedData = JSON.parse(activeMainRow.getAttribute('data-bitwise-storage') || '[]');
    const rowsToCreate = savedData.length > 0 ? savedData.length : 1;

    if (typeof addBitwiseRow !== 'function') {
        alert('Error: addBitwiseRow function is not defined!');
        return;
    }

    for (let i = 0; i < rowsToCreate; i++) {
        addBitwiseRow(); // your function that adds an empty row
    }

    // Fill modal table with saved data
      // Fill modal table with saved data
    function firstBitwiseValue() {
        for (let i = 0; i < arguments.length; i++) {
            const value = arguments[i];
            if (value !== undefined && value !== null && String(value).trim() !== '') {
                return value;
            }
        }
        return '';
    }

    function setBitwiseControl(cell, selector, value) {
        const control = cell ? cell.querySelector(selector) : null;
        if (!control) return;
        let nextValue = value === undefined || value === null ? '' : String(value);
        if (control.tagName === 'SELECT' && typeof window.normalizeApiSelectRestoreValue === 'function') {
            nextValue = window.normalizeApiSelectRestoreValue(control, nextValue);
        }
        control.value = nextValue;
        control.setAttribute('value', nextValue);
        if (control.tagName === 'SELECT') {
            Array.from(control.options).forEach(function (option) {
                option.selected = option.value === nextValue;
            });
            if (typeof control.__apiSmartSelectSync === 'function') {
                control.__apiSmartSelectSync();
            }
        }
    }

    const modalRows = tbody.querySelectorAll('tr');
    modalRows.forEach((row, rowIndex) => {
        const rowData = savedData[rowIndex];
        if (!rowData) return;

        const tds = row.querySelectorAll('td');
        if (!tds.length) return;

        // FIXED: Match the keys used in bitwiseStorage (no cmd_cmd_ prefix)
        setBitwiseControl(tds[1], 'input', firstBitwiseValue(rowData.startBytePosition_1, rowData.bitwise_start_bit_1, rowData.startBit));
        setBitwiseControl(tds[2], 'input', firstBitwiseValue(rowData.endBytePosition_1, rowData.bitwise_end_bit_1, rowData.endBit));
        setBitwiseControl(tds[3], 'input', firstBitwiseValue(rowData.numberOfBytes_1, rowData.bitSize_1, rowData.bitwise_no_of_bits_1, rowData.noOfBits));
        setBitwiseControl(tds[4], 'select', firstBitwiseValue(rowData.dataType_1, rowData.bitwise_data_type_1, rowData.dataType));
        setBitwiseControl(tds[5], 'select', firstBitwiseValue(rowData.valueDefinition_1, rowData.bitwise_value_definition_1, rowData.valueDefinition));
        setBitwiseControl(tds[6], 'input', firstBitwiseValue(rowData.displayValueMin_1, rowData.displayMin));
        setBitwiseControl(tds[7], 'input', firstBitwiseValue(rowData.displayValueMax_1, rowData.displayMax));
        setBitwiseControl(tds[8], 'input', firstBitwiseValue(rowData.valueInHexMin_1, rowData.hexMin));
        setBitwiseControl(tds[9], 'input', firstBitwiseValue(rowData.valueInHexMax_1, rowData.hexMax));
        setBitwiseControl(tds[10], 'select', firstBitwiseValue(rowData.uiType_1, rowData.uiType));
        setBitwiseControl(tds[11], 'input', firstBitwiseValue(rowData.uiId_1, rowData.uiId));
        setBitwiseControl(tds[12], 'input', firstBitwiseValue(rowData.asperui_1, rowData.asperUi));
        setBitwiseControl(tds[13], 'input', firstBitwiseValue(rowData.sno_asperui_1, rowData.uiSerialNo));
        setBitwiseControl(tds[14], 'input', firstBitwiseValue(rowData.asperuivalue_1, rowData.uiValues));
        setBitwiseControl(tds[15], 'input', firstBitwiseValue(rowData.uiPageNo_1, rowData.uiPageNo));
        setBitwiseControl(tds[16], 'select', firstBitwiseValue(rowData.uiDescriptionInOut_1, rowData.uiInOut));
        setBitwiseControl(tds[17], 'input', firstBitwiseValue(rowData.sliderStepValue_1, rowData.sliderStep));
        setBitwiseControl(tds[18], 'input', firstBitwiseValue(rowData.defaultvalue_1, rowData.defaultValue));
        setBitwiseControl(tds[19], 'input', firstBitwiseValue(rowData.developerNotes_1, rowData.devNotes));
        // Add notification fields if they are in your UI
        setBitwiseControl(tds[20], 'select, input', firstBitwiseValue(rowData.notifEnable_1, rowData.notificationEnable, 'No'));
        setBitwiseControl(tds[21], 'input', firstBitwiseValue(rowData.notifMsg_1, rowData.notificationMessage));
        setBitwiseControl(tds[22], 'input', firstBitwiseValue(rowData.scaling_1, rowData.scaling));
    });

const pageNumberElement = document.getElementById('bitwise-page-number');
if (pageNumberElement) {
    // We set the text here. Let's also set a data attribute as a backup.
    pageNumberElement.innerText = `Cmd[${currentPageIndex}]`; 
    pageNumberElement.setAttribute('data-value', currentPageIndex); 
    console.log("Setting Modal Page to:", currentPageIndex);
}


    // Show modal
    const modal = document.getElementById('bitwiseConfigModal');
    if (!modal) {
        alert('Error: bitwiseConfigModal element not found!');
        return;
    }
    modal.style.display = 'flex';
}
   
 //
/**
 * 3. CLOSE
 */
function closeBitwiseConfig(event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }
  document.getElementById('bitwiseConfigModal').style.display = 'none';
}

//


// Your existing function remains the same

function handleUiTypeChange(uiTypeSelect) {
    const row = uiTypeSelect.closest('tr');
    
    // Find the UI ID input in this specific row
       const uiInOutSelect = row.querySelector('.ui-inout-select');

    // SAFETY: If the UI ID already has a specific number (not 1 or empty), 
    // do not let the UI Type change it.
   
    // Handle the Read/Write logic as you did before
    if (uiInOutSelect) {
        if (uiTypeSelect.value === 'Status LED' || uiTypeSelect.value === 'Network Icon') {
            uiInOutSelect.value = 'Read';
            uiInOutSelect.disabled = true;
        } else {
            uiInOutSelect.disabled = false;
        }
    }
}

//save bitwise
function saveBitwiseConfig(event) {
    
      if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
const tbody = document.getElementById('bitwiseTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    let allValid = true;
    let overlapError = false;
    let errorMsg = "";

    // --- NEW: BIT TRACKER ---
    // This array represents bits 0 to 7 of the current byte
    const bitTracker = new Array(8).fill(false);


           
    // 1. Prepare storage for every single field
    const sync = {
        start: [], end: [], bits: [], type: [], def: [], 
        dMin: [], dMax: [], hMin: [], hMax: [], uiType: [], 
        uiId: [], asper: [], serial: [], vals: [], page: [], 
        inout: [], step: [], dflt: [], notes: [],notifEnable: [],          // ADD
    notifMsg: [],
        scaling: []
    };

    // ADDITION: row-wise storage for reopen
     
    
const bitwiseStorage = [];
const activeBitwisePrefix = activeMainRow && activeMainRow.closest('#responseTableBody') ? 'resp' : (activeMainRow && activeMainRow.closest('#errorTableBody') ? 'error' : 'cmd');

    rows.forEach((row, idx) => {
        const getInput = (n) => row.querySelector(`td:nth-child(${n}) input`);
        const getSelect = (n) => row.querySelector(`td:nth-child(${n}) select`);
     

    const startBit = parseInt(getInput(2)?.value);
        const endBit = parseInt(getInput(3)?.value);

        // --- 1. OVERLAP VALIDATION ---
        if (!isNaN(startBit) && !isNaN(endBit)) {
            for (let i = startBit; i <= endBit; i++) {
                if (bitTracker[i]) {
                    overlapError = true;
                    errorMsg = `Bit ${i} is repeated! Check Row ${idx + 1}.`;
                    getInput(2).style.border = "2px solid red";
                    getInput(3).style.border = "2px solid red";
                }
                bitTracker[i] = true;
            }
        }
        // Capture data from Modal
        const rowData = {
           startBit: getInput(2)?.value || "",
            endBit: getInput(3)?.value || "",
            noOfBits: getInput(4)?.value || "",
            dataType: getSelect(5)?.value || "",
            valueDefinition: getSelect(6)?.value || "",
            displayMin: getInput(7)?.value || "",
            displayMax: getInput(8)?.value || "",
            hexMin: getInput(9)?.value || "",
            hexMax: getInput(10)?.value || "",
            uiType: getSelect(11)?.value || "",
            uiId: getInput(12)?.value || "",
            asperUi: getInput(13)?.value || "",
            uiSerialNo: getInput(14)?.value || "",
            uiValues: getInput(15)?.value || "",
            uiPageNo: getInput(16)?.value || "",
            uiInOut: row.querySelector('.ui-inout-select')?.value || "",
            sliderStep: getInput(18)?.value || "",
            defaultValue: getInput(19)?.value || "",
            devNotes: getInput(20)?.value || "",
           

           };

const notifEnableEl = getSelect(21);
const notifMsgEl    = getInput(22);

rowData.notifEnable   = notifEnableEl?.value || "No";
rowData.notifMsgInput = notifMsgEl?.value || "";
rowData.scaling = getInput(23)?.value || rowData.scaling || "";

const hasBitwiseValue = [
    rowData.startBit,
    rowData.endBit,
    rowData.noOfBits,
    rowData.dataType,
    rowData.valueDefinition,
    rowData.displayMin,
    rowData.displayMax,
    rowData.hexMin,
    rowData.hexMax,
    rowData.uiType,
    rowData.uiId,
    rowData.asperUi,
    rowData.uiSerialNo,
    rowData.uiValues,
    rowData.uiPageNo,
    rowData.uiInOut,
    rowData.sliderStep,
    rowData.defaultValue,
    rowData.devNotes,
    rowData.notifMsgInput,
    rowData.scaling
].some(value => String(value || '').trim() !== '');

if (!hasBitwiseValue) {
    return;
}

// --- VALIDATION RULE ---
if (rowData.notifEnable === "Yes") {
    if (!rowData.notifMsgInput.trim()) {
        if (notifMsgEl) notifMsgEl.style.border = "2px solid red";
        allValid = false;
    } else {
        if (notifMsgEl) notifMsgEl.style.border = "1px solid #ccc";
    }
} else {
    if (notifMsgEl) notifMsgEl.style.border = "1px solid #ccc";
}
        // Fill sync arrays
        sync.start.push(rowData.startBit);
        sync.end.push(rowData.endBit);
        sync.bits.push(rowData.noOfBits);
        sync.type.push(rowData.dataType);
        sync.def.push(rowData.valueDefinition);
        sync.dMin.push(rowData.displayMin);
        sync.dMax.push(rowData.displayMax);
        sync.hMin.push(rowData.hexMin);
        sync.hMax.push(rowData.hexMax);
        sync.uiType.push(rowData.uiType);
        sync.uiId.push(rowData.uiId);
        sync.asper.push(rowData.asperUi);
        sync.serial.push(rowData.uiSerialNo);
        sync.vals.push(rowData.uiValues);
        sync.page.push(rowData.uiPageNo);
        sync.inout.push(rowData.uiInOut);
        sync.step.push(rowData.sliderStep);
        sync.dflt.push(rowData.defaultValue);
        sync.notes.push(rowData.devNotes);
        sync.notifEnable.push(rowData.notifEnable);
        
        sync.notifMsg.push(rowData.notifMsgInput || "");
        sync.scaling.push(rowData.scaling || "");
       
        // ADDITION: store per-row object for reopen
        bitwiseStorage.push({
            bitwise_parameter_name_1: rowData.defaultValue || "",
            bitwise_start_bit_1: rowData.startBit,
            bitwise_end_bit_1: rowData.endBit,
            bitwise_no_of_bits_1: rowData.noOfBits,
            bitwise_data_type_1: rowData.dataType,
            bitwise_value_definition_1: rowData.valueDefinition,
            startBytePosition_1: rowData.startBit,
            endBytePosition_1: rowData.endBit,
            fieldDescription_1: rowData.defaultValue || "", 
            numberOfBytes_1: rowData.noOfBits,
           
            dataType_1: rowData.dataType,
            valueDefinition_1: rowData.valueDefinition,
            displayValueMin_1: rowData.displayMin,
            displayValueMax_1: rowData.displayMax,
            valueInHexMin_1: rowData.hexMin,
            valueInHexMax_1: rowData.hexMax,
            uiType_1: rowData.uiType,
            uiId_1: rowData.uiId,
            asperui_1: rowData.asperUi,
            sno_asperui_1: rowData.uiSerialNo,
            asperuivalue_1: rowData.uiValues,
            uiPageNo_1: rowData.uiPageNo,
            uiDescriptionInOut_1: rowData.uiInOut,
            sliderStepValue_1: rowData.sliderStep,
            defaultvalue_1: rowData.defaultValue,
            developerNotes_1: rowData.devNotes,
            notifEnable_1: rowData.notifEnable,
            notifMsg_1: rowData.notifMsgInput || "",
            scaling_1: rowData.scaling || "",
        });
    });

if (overlapError) {
        alert(errorMsg); // This will now trigger if you give 1,1 and 1,1
        return; // Stop the save
    }

    if (!allValid) {
        alert("Please enter the Notification Message for all enabled rows.");
        return;
    }
const pageElem = document.getElementById('bitwise-page-number');
let actualPage = 0;

if (pageElem) {
    // This regex looks for any number inside the text "Cmd[2]" -> results in 2
    const match = pageElem.innerText.match(/\d+/);
    actualPage = match ? match[0] : 0;
}

if (!allValid) {
    forceMessage("Please enter the Notification Message for all enabled rows.");
    return;
}

    // ADDITION: save for openBitwiseConfig()
   // ADDITION: save for openBitwiseConfig()
if (activeMainRow) {
    // Store for JS
    activeMainRow.setAttribute(
        'data-bitwise-storage',
        JSON.stringify(bitwiseStorage)
    );

    // Store for form submit
    let hidden = activeMainRow.querySelector('input[name="bitwise_json[]"]');
    if (!hidden) {
        hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'bitwise_json[]';
        activeMainRow.appendChild(hidden);
    }
    hidden.value = JSON.stringify(bitwiseStorage);
}
// CRITICAL: merge bitwise into storedCommands

if (activeMainRow) {
    const frameIndex = activeBitwisePrefix === 'cmd'
        ? (typeof commandCount !== 'undefined' ? commandCount - 1 : 0)
        : (activeBitwisePrefix === 'resp' ? (typeof responseCount !== 'undefined' ? responseCount - 1 : 0) : (typeof errorCount !== 'undefined' ? errorCount - 1 : 0));
    const store = activeBitwisePrefix === 'cmd'
        ? (typeof storedCommands !== 'undefined' ? storedCommands : [])
        : (activeBitwisePrefix === 'resp' ? (typeof storedResponses !== 'undefined' ? storedResponses : []) : (typeof storedErrors !== 'undefined' ? storedErrors : []));
    const rowIndex = activeMainRow.rowIndex - 1;

    if (store[frameIndex] && store[frameIndex].rows && store[frameIndex].rows[rowIndex]) {
        store[frameIndex].rows[rowIndex].bitwise = bitwiseStorage;
        store[frameIndex].rows[rowIndex].bitwiseMode = 'Bitwise';
        store[frameIndex].rows[rowIndex][activeBitwisePrefix + '_bitwiseMode'] = 'Bitwise';
    }
}

// 1. First, clear the previous logs to avoid confusion

console.log("%c --- FINAL BITWISE CONFIGURATION ---", "color: green; font-weight: bold;");

// 2. Iterate through the bitwiseStorage you just created

    bitwiseStorage.forEach((bitData, bitIdx) => {
        
        // Define the identifiers
        const cmdPage = currentPageIndex; // The Page/Cmd number
        const rowNo = activeMainRow.rowIndex; // The Row number in the main table
        const bitNo = bitIdx + 1; // The sequence of the bit (1, 2, 3...)

        // Create the prefix string
        const p = `Cmd[${cmdPage}]-row[${rowNo}]-bit[${bitNo}]`;


                // Print each value clearly
        // We use backticks and ${} to inject the values directly into the string
        console.log(`${p}-startByte => ${bitData.cmd_cmd_startBytePosition_1}`);
        console.log(`${p}-endByte => ${bitData.cmd_cmd_endBytePosition_1}`);
        console.log(`${p}-bitSize => ${bitData.cmd_cmd_numberOfBytes_1}`);
        console.log(`${p}-fieldDescription => ${bitData.cmd_cmd_valueDefinition_1}`);
        console.log(`${p}-dataType => ${bitData.cmd_cmd_dataType_1}`);
        console.log(`${p}-uiType => ${bitData.cmd_cmd_uiType_1}`);
        console.log(`${p}-uiPageNo => ${bitData.cmd_cmd_uiPageNo_1}`);
        console.log(`${p}-defaultValue => ${bitData.cmd_cmd_defaultvalue_1}`);

        // NEW FIELDS ADDED BELOW
        console.log(`${p}-valueDefinition => ${bitData.cmd_cmd_valueDefinition_1}`);
        console.log(`${p}-displayMin => ${bitData.cmd_cmd_displayValueMin_1}`);
        console.log(`${p}-displayMax => ${bitData.cmd_cmd_displayValueMax_1}`);
        console.log(`${p}-hexMin => ${bitData.cmd_cmd_valueInHexMin_1}`);
        console.log(`${p}-hexMax => ${bitData.cmd_cmd_valueInHexMax_1}`);
        console.log(`${p}-uiId => ${bitData.cmd_cmd_uiId_1}`);
        console.log(`${p}-uiSerialNo => ${bitData.cmd_cmd_sno_asperui_1}`);
        console.log(`${p}-uiValues => ${bitData.cmd_cmd_asperuivalue_1}`);
        console.log(`${p}-uiInOut => ${bitData.cmd_cmd_uiDescriptionInOut_1}`);
        console.log(`${p}-sliderStep => ${bitData.cmd_cmd_sliderStepValue_1}`);
        console.log(`${p}-devNotes => ${bitData.cmd_cmd_developerNotes_1}`);
        
        // Visual separator for the next bit
        console.log("---------------------------------------");
    });

// Later, for printing
const bitwiseArray = JSON.parse(activeMainRow.getAttribute('data-bitwise-storage'));
console.log('Bitwise Array:', bitwiseArray); // Array type in console

    // 2. The Bridge to Main Table (UNCHANGED)
    
        if (activeMainRow) {
        const updateCell = (cellIdx, arr) => {
    const cell = activeMainRow.cells[cellIdx];
    if (cell) {
        let mainElement = cell.querySelector('input, select');
        const arrayString = `[${arr.join(',')}]`;
        
        if (mainElement) {
            // REMOVE validation limits if this is the UI ID column (13)
            if (cellIdx === 13) {
                mainElement.removeAttribute('min');
                mainElement.removeAttribute('max');
                mainElement.removeAttribute('pattern');
            }

            if (mainElement.tagName === 'SELECT') {
                const newInput = document.createElement('input');
                newInput.type = 'text';
                newInput.className = 'form-control';
                newInput.value = arrayString;
                mainElement.parentNode.replaceChild(newInput, mainElement);
            } else {
                mainElement.type = 'text';
                mainElement.value = arrayString;
            }
        } else {
            cell.textContent = arrayString;
        }
    }
};


        updateCell(1, sync.start);   
        updateCell(2, sync.end);
        updateCell(4, sync.bits);     
        updateCell(5, sync.bits);    
        updateCell(6, sync.type);    
        updateCell(7, sync.def);     
        updateCell(8, sync.dMin);    
        updateCell(9, sync.dMax); 
        updateCell(10, sync.hMin);
        updateCell(11, sync.hMax);   
        updateCell(12, sync.uiType);
        updateCell(13, sync.uiId);
        updateCell(14, sync.asper);
        updateCell(15, sync.serial); 
        updateCell(16, sync.vals);   
        updateCell(17, sync.page);   
        updateCell(18, sync.inout);  
        updateCell(19, sync.step);   
        updateCell(20, sync.dflt);   
        updateCell(21, sync.notes);
        updateCell(22, sync.notifEnable);
        updateCell(23, sync.notifMsg);
        updateCell(24, sync.scaling);
         }

    alert("Full configuration applied to Command Frame.");
    closeBitwiseConfig(); 
}


//


  //


// Add this logic to your "onchange" for the dropdown
function handleSelectChange(selectElement) {
    if (selectElement.value === "Bitwise") {
        const parentRow = selectElement.closest('tr');
        // Clear the old flat values so they don't print
        parentRow.querySelectorAll('input[name^="cmd_cmd_"]').forEach(input => {
            if (!input.classList.contains('stay-active')) {
                input.value = ""; 
            }
        });
    }
}     


//

//
function addBitwiseRow(btn = null) {
    const tbody = document.getElementById('bitwiseTableBody');
    const rowCount = tbody.querySelectorAll('tr').length;

    if (rowCount >= 8) {
        alert("Maximum 8 rows allowed.");
        return; 
    }

    const i = rowCount + 1;
    const row = document.createElement('tr');

    row.innerHTML = `
        <td>
            <label class="row-select-wrap" title="Select row">
                <span class="sno-number">${i}</span>
                <input type="checkbox" class="row-select-checkbox" onchange="toggleBitwiseRowSelection(this)" aria-label="Select bitwise row ${i}">
                <span class="row-check-ui" aria-hidden="true"></span>
            </label>
        </td>
        <td><input type="number" placeholder="e.g., 0"></td>
        <td><input type="number" placeholder="e.g., 7"></td>
        <td><input type="number" placeholder="Auto, e.g., 8"></td>
        <td>
            <select>
                <option value="">Select</option>
                <option value="UnsignedInteger">Unsigned Integer</option>
                <option value="SignedInteger">Signed Integer</option>
                <option value="Boolean">Boolean</option>
                <option value="Float">Float</option>
                <option value="Double">Double</option>
                <option value="Binary">Binary</option>
                <option value="ASCII">ASCII</option>
                <option value="String">String</option>
                <option value="Hex">Hex</option>
                <option value="Enum">Enum</option>
                <option value="Timestamp">Timestamp</option>
                <option value="None">None</option>
            </select>
        </td>
        <td>
            <select>
                <option value="">Select</option>
                <option value="Fixed">Fixed Value</option>
                <option value="Range">Range Value</option>
                <option value="Dynamic Value">Dynamic Value</option>
                <option value="Enum Value">Enum Value</option>
                <option value="Calculated Value">Calculated Value</option>
                <option value="None">None</option>
            </select>
        </td>
        <td><input type="number" placeholder="Min"></td>
        <td><input type="number" placeholder="Max"></td>
        <td><input type="text" placeholder="e.g., 0x0"></td>
        <td><input type="text" placeholder="e.g., 0xF"></td>
        <td>
            <select name="uiType_${i}">
                <option value="">Select</option>
                <option value="Toggle Switch">Toggle Switch</option>
                <option value="Checkbox">Checkbox</option>
                <option value="Power Button">Power Button</option>
                <option value="Text Trigger">Text Trigger</option>
                <option value="Status LED">Status LED</option>
                <option value="Lock Icon">LOCK Icon</option>
                <option value="Alaram Icon">Alaram Icon</option>
                <option value="Eye Icon">Eye Icon</option>
                <option value="Network Icon">Network Icon</option>
            </select>
        </td>
        <td><input type="number" placeholder="UI ID"></td> <td><input type="text" placeholder="e.g., Power Button"></td>   <td><input type="number" placeholder="Serial no"></td> <td><input type="text" placeholder="e.g., ON=1"></td>   <td><input type="number" placeholder="Page no"></td> <td>
            <select class="ui-inout-select">
                <option value="">Select</option>
                <option value="Read">Read</option>
                <option value="Write">Write</option>
                <option value="Read/Write">Read/Write</option>
            </select>
        </td>
        <td><input type="number" placeholder="Step value"></td>
        <td><input type="text" placeholder="Default value"></td>
        <td><input type="text" placeholder="Internal notes"></td>
        <td>
        <select class="notif-enable">
            <option value="No">No</option>
            <option value="Yes">Yes</option>
        </select>
    </td>
    <td><input type="text" class="notif-message" placeholder="Notification message"></td>
        <td><input type="text" class="bitwise-scaling" placeholder="e.g., value * 0.1"></td>

        <td class="bitwise-action-buttons">
            <button type="button" class="remove-btn" data-tooltip="Delete Row" onclick="removeBitwiseRow(this)" aria-label="Delete bitwise row"><i class="fa-solid fa-minus" aria-hidden="true"></i></button>
            <button type="button" class="row-tool-btn duplicate-row-btn" data-tooltip="Duplicate" onclick="duplicateBitwiseRow(this)" aria-label="Duplicate bitwise row"><i class="fa-solid fa-copy" aria-hidden="true"></i></button>
            <button type="button" class="row-tool-btn move-up-row-btn" data-tooltip="Move Up" onclick="moveBitwiseRowUp(this)" aria-label="Move bitwise row up"><i class="fa-solid fa-arrow-up" aria-hidden="true"></i></button>
            <button type="button" class="row-tool-btn move-down-row-btn" data-tooltip="Move Down" onclick="moveBitwiseRowDown(this)" aria-label="Move bitwise row down"><i class="fa-solid fa-arrow-down" aria-hidden="true"></i></button>
            <button type="button" class="row-tool-btn move-position-row-btn" data-tooltip="Move to Position" onclick="moveBitwiseRowToPosition(this)" aria-label="Move bitwise row to position"><i class="fa-solid fa-list-ol" aria-hidden="true"></i></button>
            <button type="button" class="add-btn" data-tooltip="Add Row" onclick="addBitwiseRow(this)" aria-label="Add bitwise row"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
        </td>
    `;

    if (btn && btn.tagName === 'BUTTON') {
        const currentRow = btn.closest('tr');
        currentRow.insertAdjacentElement('afterend', row);
    } else {
        tbody.appendChild(row);
    }

    // --- Locking System-Controlled Fields ---
    row.querySelector('td:nth-child(7) input').style.background = "#f2f2f2";  // Min
    row.querySelector('td:nth-child(8) input').style.background = "#f2f2f2";  // Max
    row.querySelector('td:nth-child(4) input').style.background = "#f2f2f2";  // No of Bits
    row.querySelector('td:nth-child(13) input').style.background = "#f2f2f2"; // UI ID

    // --- 1. UI Type Listener ---
    const uiSelect = row.querySelector(`select[name^="uiType_"]`);
    uiSelect.addEventListener('change', function() {
        const typeLower = (this.value || '').toLowerCase();
        const dataTypeSelect  = row.querySelector('td:nth-child(5) select');   
        const valueDefSelect  = row.querySelector('td:nth-child(6) select');   
        const displayMinInput = row.querySelector('td:nth-child(7) input');  
        const displayMaxInput = row.querySelector('td:nth-child(8) input');  
        const hexMinInput     = row.querySelector('td:nth-child(9) input');       
        const hexMaxInput     = row.querySelector('td:nth-child(10) input');     
        const uiInOutSelect   = row.querySelector('.ui-inout-select');
        const asperUiInput    = row.querySelector('td:nth-child(12) input');    
        const uiSerialInput   = row.querySelector('td:nth-child(13) input');   
        const uiValuesInput   = row.querySelector('td:nth-child(14) input');   

        const autoFillList = ['toggle switch','checkbox','power button','text trigger','lock icon','alaram icon','eye icon','network icon','status led'];

        if (autoFillList.includes(typeLower)) {
            dataTypeSelect.value = 'UnsignedInteger';
            valueDefSelect.value = 'Fixed';
            displayMinInput.value = 0;
            displayMaxInput.value = 1;
            hexMinInput.value = '0';
            hexMaxInput.value = '1';
            if (asperUiInput) asperUiInput.value = "false,true";
           if (uiSerialInput) uiSerialInput.value = "1";
            if (uiValuesInput) uiValuesInput.value = "0,1";

           
            uiInOutSelect.value = (typeLower === 'status led' || typeLower === 'network icon') ? 'Read' : 'Read/Write';
        }
    });

    // --- 2. Data Type Listener ---
    const dataTypeSelectForAuto = row.querySelector('td:nth-child(5) select');
    dataTypeSelectForAuto.addEventListener('change', function() {
        const displayMinInput = row.querySelector('td:nth-child(7) input');
        const displayMaxInput = row.querySelector('td:nth-child(8) input');
        const hexMinInput     = row.querySelector('td:nth-child(9) input');
        const hexMaxInput     = row.querySelector('td:nth-child(10) input');

        // This triggers for ANY data type selection
        if (this.value !== "") {
            displayMinInput.value = 0;
            displayMaxInput.value = 1;
            hexMinInput.value = '0';
            hexMaxInput.value = '1';
        }
    });

    // --- 3. Strict 0 or 1 Restriction (Universal) ---
    const minInput = row.querySelector('td:nth-child(7) input');
    const maxInput = row.querySelector('td:nth-child(8) input');

    [minInput, maxInput].forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key !== "0" && e.key !== "1") {
                e.preventDefault();
            }
        });
        input.addEventListener('input', function() {
            if (this.value !== "" && this.value !== "0" && this.value !== "1") {
                this.value = ""; 
            }
        });
    });

    // --- 4. Bit Calculation ---
    
// Use class selectors instead of nth-child to prevent column mismatch
const startBitInput = row.querySelector('td:nth-child(2) input');
    const endBitInput   = row.querySelector('td:nth-child(3) input');
    const noOfBitsInput = row.querySelector('td:nth-child(4) input');

    function recalcBits() {
        const start = parseInt(startBitInput.value);
        const end   = parseInt(endBitInput.value);
        if (!isNaN(start) && !isNaN(end) && end >= start) {
            noOfBitsInput.value = (end - start) + 1;
        } else {
            noOfBitsInput.value = '';
        }
    }

    startBitInput.addEventListener('input', recalcBits);
    endBitInput.addEventListener('input', recalcBits);

// 1. Target the UI ID input
// 1. Target the UI ID input
// Target Column 13 (UI ID)
const uiIdInput = row.querySelector('td:nth-child(12) input'); 

uiIdInput.addEventListener('click', function () {
    // Look at the first cell (S.No) to see what the row number is
    const sNo = row.querySelector('td:first-child').innerText;
    
    // If S.No is empty, fallback to counting previous rows
    const currentNumber = sNo || (Array.from(row.parentNode.children).indexOf(row) + 1);

    if (this.value !== "None" && this.value !== "") {
        this.value = "None";
    } else {
        this.value = currentNumber; // This will now correctly give 2, 3, 4...
    }
});

updateBitwiseRowNumbers();
if (typeof window.syncApiSmartSelects === 'function') {
    window.syncApiSmartSelects(row);
}
return row;
}
// 2. Set the ID and "Lock" it into the element's memory
// We use PHP to pass the correct row number (i) into JavaScript
//
//
function removeBitwiseRow(btn) {
    const tbody = document.getElementById('bitwiseTableBody');

    if (tbody.querySelectorAll('tr').length === 1) {
        alert("At least one row is required");
        return;
    }

    btn.closest('tr').remove();
    updateBitwiseRowNumbers();
}
//
function updateBitwiseRowNumbers() {
    const rows = document.querySelectorAll('#bitwiseTableBody tr');
    rows.forEach((row, index) => {
        const newNum = index + 1;
        const snoNumber = row.cells[0] ? row.cells[0].querySelector('.sno-number') : null;
        if (snoNumber) {
            snoNumber.textContent = newNum;
        } else if (row.cells[0]) {
            row.cells[0].innerHTML = `<label class="row-select-wrap" title="Select row"><span class="sno-number">${newNum}</span><input type="checkbox" class="row-select-checkbox" onchange="toggleBitwiseRowSelection(this)" aria-label="Select bitwise row ${newNum}"><span class="row-check-ui" aria-hidden="true"></span></label>`;
        }
        if (typeof syncBitwiseRowSelectionState === 'function') syncBitwiseRowSelectionState(row);
        
        const uiIdInput = row.querySelector('td:nth-child(12) input');
        if (uiIdInput && uiIdInput.value !== "None") {
            uiIdInput.value = newNum; // This will now correctly show 2, 3, 4...
        }
    });
}
//

function syncBitwiseRowSelectionState(row) {
    const checkbox = row ? row.querySelector('.row-select-checkbox') : null;
    if (!row || !checkbox) return;
    row.classList.toggle('is-row-selected', checkbox.checked);
}

function toggleBitwiseRowSelection(checkbox) {
    syncBitwiseRowSelectionState(checkbox ? checkbox.closest('tr') : null);
}

function getBitwiseSelectedRows() {
    return Array.from(document.querySelectorAll('#bitwiseTableBody tr')).filter(function (row) {
        const checkbox = row.querySelector('.row-select-checkbox');
        return checkbox && checkbox.checked;
    });
}

function requireBitwiseSelectedRows() {
    const rows = getBitwiseSelectedRows();
    if (!rows.length) alert('Please select one or more bitwise rows first.');
    return rows;
}

function copyBitwiseRowValues(sourceRow, targetRow) {
    if (!sourceRow || !targetRow) return;
    const sourceFields = sourceRow.querySelectorAll('input:not(.row-select-checkbox), select, textarea');
    const targetFields = targetRow.querySelectorAll('input:not(.row-select-checkbox), select, textarea');
    sourceFields.forEach(function (field, index) {
        const target = targetFields[index];
        if (!target) return;
        target.value = field.value;
        target.checked = field.checked;
        target.dispatchEvent(new Event('change', {bubbles: true}));
    });
}

function duplicateBitwiseRow(button) {
    const sourceRow = button ? button.closest('tr') : null;
    if (!sourceRow) return;
    const newRow = addBitwiseRow(button);
    if (newRow) {
        copyBitwiseRowValues(sourceRow, newRow);
        updateBitwiseRowNumbers();
    }
}

function moveBitwiseRowUp(button) {
    const row = button ? button.closest('tr') : null;
    if (!row || !row.previousElementSibling) return;
    row.parentNode.insertBefore(row, row.previousElementSibling);
    updateBitwiseRowNumbers();
}

function moveBitwiseRowDown(button) {
    const row = button ? button.closest('tr') : null;
    if (!row || !row.nextElementSibling) return;
    row.parentNode.insertBefore(row.nextElementSibling, row);
    updateBitwiseRowNumbers();
}

function moveBitwiseRowToPosition(button) {
    const row = button ? button.closest('tr') : null;
    const tbody = document.getElementById('bitwiseTableBody');
    if (!row || !tbody) return;
    const rows = Array.from(tbody.rows);
    const currentIndex = rows.indexOf(row);
    const target = prompt('Move row to position (1 - ' + rows.length + '):', String(currentIndex + 1));
    if (target === null) return;
    const targetIndex = parseInt(target, 10);
    if (!targetIndex || targetIndex < 1 || targetIndex > rows.length) {
        alert('Please enter a valid position between 1 and ' + rows.length + '.');
        return;
    }
    const ref = rows[targetIndex - 1];
    if (targetIndex - 1 > currentIndex) {
        tbody.insertBefore(row, ref.nextSibling);
    } else {
        tbody.insertBefore(row, ref);
    }
    updateBitwiseRowNumbers();
}

function deleteSelectedBitwiseRows() {
    const rows = requireBitwiseSelectedRows();
    const tbody = document.getElementById('bitwiseTableBody');
    if (!rows.length || !tbody) return;
    if (tbody.rows.length - rows.length < 1) {
        alert('At least one row is required.');
        return;
    }
    if (!confirm('Delete ' + rows.length + ' selected bitwise row(s)?')) return;
    rows.forEach(function (row) {
        row.remove();
    });
    updateBitwiseRowNumbers();
}

function moveSelectedBitwiseRowsUp() {
    const rows = requireBitwiseSelectedRows();
    if (!rows.length) return;
    const tbody = document.getElementById('bitwiseTableBody');
    rows.forEach(function (row) {
        const previous = row.previousElementSibling;
        if (previous && !previous.querySelector('.row-select-checkbox:checked')) {
            tbody.insertBefore(row, previous);
        }
    });
    updateBitwiseRowNumbers();
}

function moveSelectedBitwiseRowsDown() {
    const rows = requireBitwiseSelectedRows().reverse();
    if (!rows.length) return;
    const tbody = document.getElementById('bitwiseTableBody');
    rows.forEach(function (row) {
        const next = row.nextElementSibling;
        if (next && !next.querySelector('.row-select-checkbox:checked')) {
            tbody.insertBefore(next, row);
        }
    });
    updateBitwiseRowNumbers();
}

function moveSelectedBitwiseRowsToPosition() {
    const rows = requireBitwiseSelectedRows();
    const tbody = document.getElementById('bitwiseTableBody');
    if (!rows.length || !tbody) return;
    const totalRows = tbody.rows.length;
    const target = prompt('Move selected rows to position (1 - ' + totalRows + '):', '1');
    if (target === null) return;
    const targetIndex = parseInt(target, 10);
    if (!targetIndex || targetIndex < 1 || targetIndex > totalRows) {
        alert('Please enter a valid position between 1 and ' + totalRows + '.');
        return;
    }
    const selectedSet = new Set(rows);
    const remainingRows = Array.from(tbody.rows).filter(function (row) {
        return !selectedSet.has(row);
    });
    const referenceRow = remainingRows[targetIndex - 1] || null;
    rows.forEach(function (row) {
        tbody.insertBefore(row, referenceRow);
    });
    updateBitwiseRowNumbers();
}

 
// --- MUST GO AT THE VERY TOP OF YOUR FILE ---

// 🔄 RESET STORAGE ON PAGE LOAD (FIXES YOUR ISSUE)
window.addEventListener("load", () => {
    sessionStorage.removeItem('frame_command');
    sessionStorage.removeItem('frame_response');
    sessionStorage.removeItem('frame_error');
    console.log("System: Frame session reset");
});

// CHECK WHAT IS SAVED
function getRemainingFrames() {
    const isCommandDone = sessionStorage.getItem('frame_command') === 'true';
    const isResponseDone = sessionStorage.getItem('frame_response') === 'true';
    const isErrorDone = sessionStorage.getItem('frame_error') === 'true';

    let missing = [];
    if (!isCommandDone) missing.push("Command Frame");
    if (!isResponseDone) missing.push("Response Frame");
    if (!isErrorDone) missing.push("Error Frame");

    return missing;
}

// 🟦 COMMAND FRAME BUTTON

function saveCommandConfiguration() {
    // Capture latest table state
    saveCurrentCommandState(commandCount - 1);
const activeIdx = commandCount - 1; // Page 1 = 0, Page 2 = 1

    // 1. ENSURE OBJECT EXISTS IN ARRAY
    if (!storedCommands[activeIdx]) {
        storedCommands[activeIdx] = { rows: [], meta: {} };
    }

    // 2. SAVE TABLE STATE
    saveCurrentCommandState(activeIdx);
   
    
    // STEP 1 — PRODUCT INFO (Print this FIRST)
    const productInfo = {
        parameterName: document.getElementById("par_parametername")?.value || "",
        productName: document.getElementById("productname")?.value || "",
        brandName: document.getElementById("brandname")?.value || "",
        modelName: document.getElementById("modelname")?.value || "",
        date: document.querySelector("input[name='date']")?.value || "",
        sequence: document.getElementById("Sequence")?.value || "",
        totalCommandFrames: document.getElementById("numberofpages")?.value || "",
        totalResponseFrames: document.getElementById("page")?.value || "",
        totalErrorFrames: document.getElementById("pages")?.value || "",
        pageNames: document.getElementById("pagenames")?.value || "",
        productDescription: document.getElementById("productdescription")?.value || ""
    };

    console.log("%c========= 1. PRODUCT INFORMATION =========", "color:blue;font-weight:bold;font-size:12px;");
    Object.entries(productInfo).forEach(([k, v]) => console.log(`PRODUCT-${k} => ${v}`));

    // --- ADDED CODE: CAPTURE AND ATTACH META DATA ---
    const currentMeta = {
        parametername: document.getElementById('com_parametername')?.value || "",
        noofrows: document.getElementById('cmd_noofrows')?.value || "0",
        remarks: document.getElementById('cmd_remarks')?.value || "",
        dataflowdirection: document.getElementById('cmd_data_flow_direction')?.value || "",
        frametype: document.getElementById('cmd_frametype')?.value || "",
        framesize_bytes: document.getElementById('cmd_framesizeinbytes')?.value || "",
        frametrigger: document.getElementById('cmd_frame_trigger_options')?.value || "",
        multipleui: document.getElementById('cmd_multipleui')?.value || "",
        triggerui: document.getElementById('cmd_triggerui')?.value || "",
        voicecommand: document.getElementById('cmd_voice_command')?.value || "",
        voicecontrol: document.getElementById('cmd_voice_control_instruction')?.value || ""
    };

    // This ensures the Meta Data is stored for Page 1 (0), Page 2 (1), etc.
        // STEP 2 & 3 — LOOP THROUGH PAGES (Print Meta then Table)
    storedCommands.forEach((frame, fIndex) => {
        console.log(`%c\n========= PAGE DATA: Cmd[${fIndex}] =========`, "color:green;font-weight:bold;border-bottom:1px solid green;");

        // --- A. PRINT META DATA NEXT ---
        console.log(`%c[Cmd-${fIndex}] 2. META DATA`, "color:purple;font-weight:bold");
        
        // Use the meta stored in the frame (if it exists)
        const metaToPrint = frame.meta || currentMeta; 
        Object.entries(metaToPrint).forEach(([k, v]) => {
            console.log(`Cmd[${fIndex}]-meta-${k} => ${v}`);
        });

        // --- B. PRINT TABLE VALUES LAST ---
        console.log(`%c[Cmd-${fIndex}] 3. TABLE VALUES`, "color:brown;font-weight:bold");
        const rows = frame.rows || [];
        rows.forEach((row, rIndex) => {
            console.log(`%c--- Row[${rIndex + 1}] ---`, "color:gray;font-style:italic");
            
            for (const [key, value] of Object.entries(row)) {
                if (key !== "bitwise") {
                    const cleanKey = key.replace("cmd_", "");
                    console.log(`Cmd[${fIndex}]-row[${rIndex + 1}]-${cleanKey} => ${value}`);
                }
            }

            // --- BITWISE DATA (Inside Table) ---
            if (Array.isArray(row.bitwise)) {
                row.bitwise.forEach((bit, bIndex) => {
                    const p = `Cmd[${fIndex}]-row[${rIndex + 1}]-bit[${bIndex + 1}]`;
                    Object.entries(bit).forEach(([k, v]) => {
                        const clean = k.replace("cmd_cmd_", "");
                        console.log(`${p}-${clean} => ${v}`);
                    });
                });
            }
        });
    });

             sessionStorage.setItem('frame_command', 'true');
storedCommands[activeIdx].meta = currentMeta;
    
    if (typeof printMasterConsole === "function") printMasterConsole();
      showRemainingFramesAlert();
   
        alert("Configuration saved!");
}
      
//
// 🟩 RESPONSE FRAME BUTTON
function saveResponseConfiguration() {
    

    // 1. SAVE STATE: Capture current table rows into the array
    saveCurrentResponseState(responseCount - 1);
    const activeIdx = responseCount - 1;

    if (!storedResponses[activeIdx]) {
        storedResponses[activeIdx] = { rows: [], meta: {} };
    }

    saveCurrentResponseState(activeIdx);

    // 2. CAPTURE META: One proper meta object from the UI
    const currentMeta = {
        parametername: document.getElementById('resp_parametername')?.value || "",
        noofrows: document.getElementById('resp_noofrows')?.value || "0",
        remarks: document.getElementById('resp_remarks')?.value || "",
        dataflowdirection: document.getElementById('resp_data_flow_direction')?.value || "",
        frametype: document.getElementById('resp_frametype')?.value || "",
        framesize_bytes: document.getElementById('resp_framesizeinbytes')?.value || "",
        frametrigger: document.getElementById('resp_frametrigger')?.value || "",
        multipleui: document.getElementById('resp_multipleui')?.value || "",
        triggerui: document.getElementById('resp_triggerui')?.value || "",
        voicecommand: document.getElementById('resp_voice_command')?.value || "",
        voicecontrol: document.getElementById('resp_voice_control_instruction')?.value || ""
    };

    // 3. ATTACH META: Bind the meta data to the specific frame in storage
    if (storedResponses[responseCount - 1]) {
        storedResponses[responseCount - 1].meta = currentMeta;

    }

    // --- DISPLAY SEQUENCE ---

    // STEP 1: PRODUCT INFORMATION
    const productInfo = {
        parameterName: document.getElementById("par_parametername")?.value || "",
        productName: document.getElementById("productname")?.value || "",
        brandName: document.getElementById("brandname")?.value || "",
        modelName: document.getElementById("modelname")?.value || "",
                 date: document.querySelector("input[name='date']")?.value || "",
        sequence: document.getElementById("Sequence")?.value || "",

        totalCommandFrames: document.getElementById("numberofpages")?.value || "",
        totalResponseFrames: document.getElementById("page")?.value || "",
        totalErrorFrames: document.getElementById("pages")?.value || "",
        pageNames: document.getElementById("pagenames")?.value || "",
        productDescription: document.getElementById("productdescription")?.value || ""

    };

    console.log("%c========= 1. PRODUCT INFORMATION =========", "color:blue;font-weight:bold;font-size:12px;");
    Object.entries(productInfo).forEach(([k, v]) => console.log(`PRODUCT-${k} => ${v}`));

    // STEP 2 & 3: PAGE-WISE DATA
    storedResponses.forEach((frame, fIndex) => {
        console.log(`%c\n========= PAGE DATA: Res[${fIndex}] =========`, "color:darkgreen;font-weight:bold;");

        // --- STEP 2: META DATA ---
        console.log(`%c[Res-${fIndex}] 2. META DATA`, "color:purple;font-weight:bold");
        const metaToLog = frame.meta || currentMeta; // Use stored meta or current fallback
        Object.entries(metaToLog).forEach(([k, v]) => {
            console.log(`Res[${fIndex}]-meta-${k} => ${v}`);
        });

        // --- STEP 3: TABLE DATA ---
        console.log(`%c[Res-${fIndex}] 3. TABLE DATA`, "color:brown;font-weight:bold");
        const rows = frame.rows || [];
        rows.forEach((row, rIndex) => {
            console.log(`%c--- Row[${rIndex + 1}] ---`, "color:gray;font-style:italic");
            
            for (const [key, value] of Object.entries(row)) {
                if (key !== "bitwise") {
                    const cleanKey = key.replace("resp_", "");
                    console.log(`Res[${fIndex}]-row[${rIndex + 1}]-${cleanKey} => ${value}`);
                }
            }

            // --- BITWISE DATA ---
            if (Array.isArray(row.bitwise)) {
                row.bitwise.forEach((bit, bIndex) => {
                    const p = `Res[${fIndex}]-row[${rIndex + 1}]-bit[${bIndex + 1}]`;
                    Object.entries(bit).forEach(([k, v]) => {
                        const clean = k.replace("resp_resp_", "");
                        console.log(`${p}-${clean} => ${v}`);
                    });
                });
            }
        });
    });

    sessionStorage.setItem('frame_response', 'true');
    storedResponses[activeIdx].meta = currentMeta;
    
    // Check for other save flags (Cumulative printing)
    if (typeof printMasterConsole === "function") printMasterConsole();
    showRemainingFramesAlert();
    alert("Configuration saved!");
}
// ERROR FRAME BUTTON (FINAL STEP)
//

function saveErrorConfiguration() {
    // 1. Sync the current visible state
    const activeErrIdx = errorCount - 1;
    saveCurrentErrorState(activeErrIdx);

    // 2. CREATE A COMPLETE META OBJECT
    // This collects data from your inputs to apply to ALL frames
    
   const fullMeta = {
    parametername: document.getElementById('error_parametername')?.value || "",
    noofrows: document.getElementById('error_noofrows')?.value || "0",
    remarks: document.getElementById('error_remarks')?.value || "",
    dataflowdirection: document.getElementById('error_data_flow_direction')?.value || "",
    frametype: document.getElementById('error_frametype')?.value || "ERROR",
    framesize_bytes: document.getElementById('error_framesizeinbytes')?.value || "",
    frametrigger: document.getElementById('error_frametrigger')?.value || "",
    multipleui: document.getElementById('error_multipleui')?.value || "",
    triggerui: document.getElementById('error_triggerui')?.value || "",
    voicecommand: document.getElementById('error_voice_command')?.value || "",
    voicecontrol: document.getElementById('error_voice_control_instruction')?.value || ""
};

    function frameMetaFromState(frame, frameType) {
        frame = frame || {};
        return {
            parametername: frame.parameterName || document.getElementById('par_parametername')?.value || "",
            noofrows: Array.isArray(frame.rows) ? String(frame.rows.length) : "0",
            remarks: frame.remarks || "",
            dataflowdirection: frame.direction || "",
            frametype: frameType || "",
            framesize_bytes: frame.size || "",
            frametrigger: frame.triggerOptions || "",
            multipleui: frame.multipleui || "",
            triggerui: frame.triggerui || "",
            voicecommand: frame.voicecommand || "",
            voicecontrol: frame.voicecontrol || ""
        };
    }

    // 3. Keep each frame's own metadata. Do not copy error metadata into command/response frames.
    if (typeof storedCommands !== 'undefined') {
        storedCommands.forEach(f => { if(!f.meta) f.meta = frameMetaFromState(f, 'COMMAND'); });
    }
    if (typeof storedResponses !== 'undefined') {
        storedResponses.forEach(f => { if(!f.meta) f.meta = frameMetaFromState(f, 'RESPONSE'); });
    }
    if (typeof storedErrors !== 'undefined') {
        storedErrors.forEach(f => { if(!f.meta) f.meta = fullMeta; });
    }

    return true;

    // 4. GATHER PRODUCT INFO FOR THE HEADER
    // Ensure these IDs match your HTML input IDs exactly
    const productInfo = {
        itemid: document.getElementById("api_itemid")?.value || "",
        brandname: document.getElementById("brandname")?.value || "N/A",
        productname: document.getElementById("productname")?.value || "N/A",
        modelname: document.getElementById("modelname")?.value || "N/A",
        date: document.querySelector("input[name='date']")?.value || "N/A",
        parameter_name: document.getElementById('error_parametername')?.value || "N/A",
        page_number: document.getElementById("pages")?.value || "1", 
        sequence_number: document.getElementById("Sequence")?.value || "1",
        product_description: document.getElementById("error_remarks")?.value || "N/A"
    };

    const formData = new FormData();
    Object.entries(productInfo).forEach(([k, v]) => formData.append(k, v));

    const masterData = {
        commands: typeof storedCommands !== 'undefined' ? storedCommands : [],
        responses: typeof storedResponses !== 'undefined' ? storedResponses : [],
        errors: typeof storedErrors !== 'undefined' ? storedErrors : []
    };
    formData.append('full_config_json', JSON.stringify(masterData));

    // 5. SEND TO API
    fetch('<?php echo get_uri("items/saveapi"); ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showApiResponsePopup(data.print_r_output);
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => alert("API Connection Failed"));

    return false;
}

function escapeSubmitPreviewHtml(value) {
    return String(value || '').replace(/[&<>"']/g, function (char) {
        return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[char];
    });
}

function splitSubmitPreviewPair(line) {
    const index = line.indexOf(':');
    if (index === -1) return null;
    return {
        label: line.slice(0, index).replace(/\s+/g, ' ').replace(/^NO\. OF /i, '').trim(),
        value: line.slice(index + 1).trim() || 'N/A'
    };
}

function isSubmitPreviewNoise(line) {
    const clean = String(line || '').trim();
    return !clean || /^=+$/.test(clean) || /^-+$/.test(clean) || /^=+ END OF REPORT =+$/i.test(clean);
}

function parseSubmitApiPreview(content) {
    const lines = String(content || '').split(/\r?\n/);
    const parsed = {
        title: 'Full API Data Preview',
        summary: [],
        counts: [],
        sections: []
    };
    let currentSection = null;
    let currentFrame = null;
    let currentRow = null;
    let beforeSection = true;

    function pushSection(title, count) {
        currentSection = {title: title, count: count || '', frames: []};
        parsed.sections.push(currentSection);
        currentFrame = null;
        currentRow = null;
        beforeSection = false;
    }

    function pushFrame(title) {
        if (!currentSection) pushSection('API Details', '');
        currentFrame = {title: title, details: [], rows: []};
        currentSection.frames.push(currentFrame);
        currentRow = null;
    }

    function pushRow(title) {
        if (!currentFrame) pushFrame('Frame Details');
        currentRow = {title: title, details: []};
        currentFrame.rows.push(currentRow);
    }

    lines.forEach(function (rawLine) {
        const line = String(rawLine || '').trim();
        if (isSubmitPreviewNoise(line)) return;

        if (/FULL PRODUCT CONFIGURATION SUMMARY|FULL API DATA PREVIEW|LEGACY PRODUCT API SUMMARY/i.test(line)) {
            parsed.title = line.replace(/\s+/g, ' ').trim();
            return;
        }

        const sectionMatch = line.match(/^\[SECTION\]\s+(.+?)(?:\s+\((.*?)\))?$/i);
        if (sectionMatch) {
            pushSection(sectionMatch[1], sectionMatch[2] || '');
            return;
        }

        if (/^(COMMAND|RESPONSE|ERROR)$/i.test(line)) {
            pushSection(line.toUpperCase(), '');
            pushFrame(line.toUpperCase() + ' Details');
            return;
        }

        if (/^---\s*Frame/i.test(line)) {
            pushFrame(line.replace(/^---\s*|\s*---$/g, ''));
            return;
        }

        if (/^(BYTEWISE|BITWISE)\s+ROW/i.test(line)) {
            pushRow(line);
            return;
        }

        const pair = splitSubmitPreviewPair(line);
        if (!pair) return;

        if (/COMMAND FRAMES|RESPONSE FRAMES|ERROR FRAMES/i.test(pair.label)) {
            parsed.counts.push(pair);
        } else if (currentRow) {
            currentRow.details.push(pair);
        } else if (currentFrame) {
            currentFrame.details.push(pair);
        } else if (beforeSection) {
            parsed.summary.push(pair);
        } else {
            if (!currentFrame) pushFrame('Frame Details');
            currentFrame.details.push(pair);
        }
    });

    return parsed;
}

function renderSubmitPreviewTable(pairs) {
    if (!pairs || !pairs.length) return '<div class="submit-preview-empty">No values available.</div>';
    return '<div class="submit-preview-table-wrap"><table class="submit-preview-table"><tbody>' +
        pairs.map(function (pair) {
            return '<tr><th>' + escapeSubmitPreviewHtml(pair.label) + '</th><td>' + escapeSubmitPreviewHtml(pair.value) + '</td></tr>';
        }).join('') +
        '</tbody></table></div>';
}

function renderSubmitPreviewCards(pairs) {
    if (!pairs || !pairs.length) return '<div class="submit-preview-empty">No summary values available.</div>';
    return '<div class="submit-preview-grid">' + pairs.map(function (pair) {
        return '<div class="submit-preview-card"><span>' + escapeSubmitPreviewHtml(pair.label) + '</span><strong>' + escapeSubmitPreviewHtml(pair.value) + '</strong></div>';
    }).join('') + '</div>';
}

function renderSubmitApiPreview(content, meta) {
    const parsed = parseSubmitApiPreview(content);
    const hasStructuredData = parsed.summary.length || parsed.counts.length || parsed.sections.length;
    let html = '';

    html += '<style>';
    html += '.submit-preview-panel{max-height:calc(92vh - 112px);overflow:auto;padding:16px;background:#FAF7F4;}';
    html += '.submit-preview-hero{display:grid;grid-template-columns:minmax(220px,1fr) auto;gap:16px;align-items:start;padding:16px;border-radius:12px;background:linear-gradient(135deg,var(--graphite,#232946),var(--graphite2,#2E3350));color:#fff;margin-bottom:14px;}';
    html += '.submit-preview-hero h3{margin:0 0 6px;color:#fff;font-size:18px;font-weight:900;}.submit-preview-hero p{margin:0;color:#E9D7C4;font-weight:700;}';
    html += '.submit-preview-badges{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;}.submit-preview-badge{display:inline-flex;align-items:center;min-height:30px;padding:0 10px;border-radius:999px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);color:#fff;font-size:12px;font-weight:900;}';
    html += '.submit-preview-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:10px;margin-bottom:14px;}.submit-preview-card{padding:11px 12px;border:1px solid var(--border,#E5DDD5);border-radius:10px;background:#fff;}.submit-preview-card span{display:block;margin-bottom:4px;color:var(--copper,#C08457);font-size:11px;font-weight:900;text-transform:uppercase;}.submit-preview-card strong{display:block;color:var(--graphite,#232946);font-size:14px;overflow-wrap:anywhere;}';
    html += '.submit-preview-section{margin-top:14px;border:1px solid var(--border,#E5DDD5);border-radius:14px;background:#fff;overflow:hidden;}.submit-preview-section-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:13px 15px;background:#F1ECE6;border-bottom:1px solid var(--border,#E5DDD5);}.submit-preview-section-head h4{margin:0;color:var(--graphite,#232946);font-size:16px;font-weight:900;}.submit-preview-count{color:#667085;font-size:12px;font-weight:900;}';
    html += '.submit-preview-section-body{padding:14px;}.submit-preview-frame{border:1px solid #E8DED4;border-radius:12px;overflow:hidden;margin-bottom:12px;}.submit-preview-frame:last-child{margin-bottom:0;}.submit-preview-frame-title{padding:10px 12px;background:#FBFAF8;color:var(--graphite,#232946);font-size:13px;font-weight:900;border-bottom:1px solid #E8DED4;}';
    html += '.submit-preview-table-wrap{overflow:auto;}.submit-preview-table{width:100%;min-width:640px;border-collapse:collapse;}.submit-preview-table th,.submit-preview-table td{padding:9px 10px;border-bottom:1px solid #EFE7DF;text-align:left;vertical-align:top;font-size:13px;}.submit-preview-table th{width:210px;color:#667085;background:#FFFCFA;font-weight:900;}.submit-preview-table td{color:#1B1E2B;font-weight:700;overflow-wrap:anywhere;}';
    html += '.submit-preview-chip{display:inline-flex;align-items:center;min-height:24px;padding:0 8px;border-radius:999px;background:rgba(94,139,106,.14);color:#3F684A;font-size:11px;font-weight:900;}.submit-preview-empty{padding:18px;border:1px dashed var(--border,#E5DDD5);border-radius:12px;background:#fff;color:#667085;font-weight:800;}.submit-preview-raw{margin:12px 0 0;padding:14px;border:1px solid var(--border,#E5DDD5);border-radius:12px;background:#fff;color:#1B1E2B;font-family:Consolas,monospace;font-size:12px;white-space:pre-wrap;word-break:break-word;}';
    html += '@media(max-width:768px){.submit-preview-hero{grid-template-columns:1fr}.submit-preview-badges{justify-content:flex-start}.submit-preview-panel{padding:10px}}';
    html += '</style>';

    if (!hasStructuredData) {
        return html + '<div class="submit-preview-panel"><pre class="submit-preview-raw">' + escapeSubmitPreviewHtml(content || '') + '</pre></div>';
    }

    html += '<div class="submit-preview-panel">';
    html += '<div class="submit-preview-hero"><div><h3>' + escapeSubmitPreviewHtml(parsed.title) + '</h3><p>' + escapeSubmitPreviewHtml(meta && meta.message ? meta.message : 'Saved API configuration organized by product, frame, and field values.') + '</p></div><div class="submit-preview-badges">';
    if (meta && meta.configId) html += '<span class="submit-preview-badge">Config ID: ' + escapeSubmitPreviewHtml(meta.configId) + '</span>';
    if (parsed.counts.length) {
        html += parsed.counts.map(function (count) {
            return '<span class="submit-preview-badge">' + escapeSubmitPreviewHtml(count.label) + ': ' + escapeSubmitPreviewHtml(count.value) + '</span>';
        }).join('');
    }
    html += '</div></div>';
    html += renderSubmitPreviewCards(parsed.summary);

    parsed.sections.forEach(function (section) {
        html += '<section class="submit-preview-section"><div class="submit-preview-section-head"><h4>' + escapeSubmitPreviewHtml(section.title) + '</h4><span class="submit-preview-count">' + escapeSubmitPreviewHtml(section.count ? section.count + ' item(s)' : section.frames.length + ' block(s)') + '</span></div><div class="submit-preview-section-body">';
        if (!section.frames.length) html += '<div class="submit-preview-empty">No blocks found in this section.</div>';
        section.frames.forEach(function (frame) {
            html += '<article class="submit-preview-frame"><div class="submit-preview-frame-title">' + escapeSubmitPreviewHtml(frame.title) + '</div>';
            html += renderSubmitPreviewTable(frame.details);
            frame.rows.forEach(function (row) {
                html += '<div class="submit-preview-frame-title"><span class="submit-preview-chip">' + escapeSubmitPreviewHtml(row.title) + '</span></div>';
                html += renderSubmitPreviewTable(row.details);
            });
            html += '</article>';
        });
        html += '</div></section>';
    });

    html += '<details><summary class="submit-preview-count">Raw text</summary><pre class="submit-preview-raw">' + escapeSubmitPreviewHtml(content || '') + '</pre></details>';
    html += '</div>';
    return html;
}

// Helper to show the form results on screen
function showApiResponsePopup(content, meta) {
    const old = document.getElementById('api-overlay');
    if (old) old.remove();

    const overlay = document.createElement('div');
    overlay.id = 'api-overlay';
    overlay.style.cssText = `
        position:fixed;
        inset:0;
        z-index:99999;
        display:flex;
        align-items:center;
        justify-content:center;
        padding:24px;
        background:rgba(27,30,43,.72);
        backdrop-filter:blur(3px);
    `;

    const popup = document.createElement('div');
    popup.style.cssText = `
        width:min(1180px,96vw);
        max-height:92vh;
        background:var(--card,#fff);
        border:1px solid var(--border,#E5DDD5);
        border-radius:18px;
        box-shadow:0 24px 70px rgba(27,30,43,.28);
        display:flex;
        flex-direction:column;
        overflow:hidden;
    `;

    const closeApiPreview = function (resetForm) {
        overlay.remove();
        if (resetForm && typeof resetApiFormToProductStep === 'function') {
            resetApiFormToProductStep();
        }
    };

    const header = document.createElement('div');
    header.style.cssText = `
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:16px;
        padding:18px 22px;
        background:linear-gradient(135deg,var(--graphite,#232946),var(--graphite2,#2E3350));
        color:#fff;
        border-bottom:1px solid rgba(255,255,255,.12);
    `;

    const title = document.createElement('h2');
    title.textContent = 'FULL API DATA PREVIEW';
    title.style.cssText = `
        margin:0;
        color:#fff;
        font-size:22px;
        font-weight:800;
        text-align:left;
        letter-spacing:0;
    `;

    const actionBar = document.createElement('div');
    actionBar.style.cssText = `
        display:flex;
        align-items:center;
        justify-content:flex-end;
        gap:10px;
        flex-wrap:wrap;
    `;

    const viewApiBtn = document.createElement('button');
    viewApiBtn.type = 'button';
    viewApiBtn.textContent = 'View API';
    viewApiBtn.onclick = function () {
        window.location.href = '<?php echo get_uri("items/viewapis/" . (int) ($itemid ?? 0)); ?>';
    };
    viewApiBtn.style.cssText = `
        border:1px solid rgba(255,255,255,.28);
        background:#5E8B6A;
        color:#fff;
        min-height:38px;
        padding:0 16px;
        border-radius:10px;
        cursor:pointer;
        font-weight:800;
        box-shadow:0 8px 18px rgba(94,139,106,.28);
    `;

    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.textContent = 'Close';
    closeBtn.onclick = function () { closeApiPreview(true); };
    closeBtn.style.cssText = `
        border:1px solid rgba(255,255,255,.24);
        background:rgba(255,255,255,.12);
        color:#fff;
        min-height:38px;
        padding:0 16px;
        border-radius:10px;
        cursor:pointer;
        font-weight:800;
    `;

    const body = document.createElement('div');
    body.style.cssText = `
        padding:18px;
        overflow:auto;
        background:#F8F5F2;
    `;

    const preview = document.createElement('div');
    preview.innerHTML = renderSubmitApiPreview(content || '', meta || {});

    actionBar.appendChild(viewApiBtn);
    actionBar.appendChild(closeBtn);
    header.appendChild(title);
    header.appendChild(actionBar);
    body.appendChild(preview);
    popup.appendChild(header);
    popup.appendChild(body);
    overlay.appendChild(popup);

    overlay.addEventListener('click', function (event) {
        if (event.target === overlay) closeApiPreview(true);
    });

    document.addEventListener('keydown', function closePreviewOnEscape(event) {
        if (event.key === 'Escape') {
            closeApiPreview(true);
            document.removeEventListener('keydown', closePreviewOnEscape);
        }
    });

    document.body.appendChild(overlay);
}

function resetApiFormToProductStep() {
    const form = document.getElementById('file-form');
    if (form) form.reset();

    if (typeof storedCommands !== 'undefined') storedCommands.length = 0;
    if (typeof storedResponses !== 'undefined') storedResponses.length = 0;
    if (typeof storedErrors !== 'undefined') storedErrors.length = 0;

    if (typeof commandCount !== 'undefined') commandCount = 1;
    if (typeof responseCount !== 'undefined') responseCount = 1;
    if (typeof errorCount !== 'undefined') errorCount = 1;
    if (typeof currentStep !== 'undefined') currentStep = 1;

    ['step1-error', 'step2-error', 'step3-error', 'step4-error', 'duplicate-error', 'char-error'].forEach(function (id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = '';
        el.style.display = 'none';
    });

    document.querySelectorAll('.error, .pink-highlight').forEach(function (el) {
        el.classList.remove('error', 'pink-highlight');
    });

    if (typeof resetCommandUI === 'function') resetCommandUI();
    if (typeof resetResponseUI === 'function') resetResponseUI();
    if (typeof resetErrorUI === 'function') resetErrorUI();

    if (typeof generateCommandProgressBoxes === 'function') generateCommandProgressBoxes();
    if (typeof generateResponseProgressBoxes === 'function') generateResponseProgressBoxes();
    if (typeof generateErrorProgressBoxes === 'function') generateErrorProgressBoxes();

    document.querySelectorAll('.form-step').forEach(function (el) {
        el.classList.remove('active');
        el.style.display = 'none';
    });
    const stepOne = document.getElementById('step-1');
    if (stepOne) {
        stepOne.classList.add('active');
        stepOne.style.display = 'block';
    }

    if (typeof updateProgressIndicator === 'function') updateProgressIndicator();
    window.scrollTo({top: 0, behavior: 'smooth'});
}

//

function printMasterConsole() {
    console.clear();
    console.log("%c==================== MASTER CUMULATIVE LOG ====================", "background: #222; color: #fff; font-size: 16px; font-weight: bold; padding: 5px;");

    // 1. PRODUCT INFO (Always prints first)
    console.log("%c========= 1. PRODUCT INFORMATION =========", "color:blue;font-weight:bold");
    const pInfo = {
        parameterName: document.getElementById("par_parametername")?.value || "",
        productName: document.getElementById("productname")?.value || "",
        brandName: document.getElementById("brandname")?.value || "",
        modelName: document.getElementById("modelname")?.value || "",
        date: document.querySelector("input[name='date']")?.value || "",
        sequence: document.getElementById("Sequence")?.value || "",
        totalCommandFrames: document.getElementById("numberofpages")?.value || "0",
        totalResponseFrames: document.getElementById("page")?.value || "0",
        totalErrorFrames: document.getElementById("pages")?.value || "0",
        pageNames: document.getElementById("pagenames")?.value || "",
        productDescription: document.getElementById("productdescription")?.value || ""
    };
    Object.entries(pInfo).forEach(([k, v]) => console.log(`PRODUCT-${k} => ${v}`));

    // 2. COMMANDS
    if (sessionStorage.getItem('frame_command') === 'true') {
        console.log("%c\n========= 2. COMMAND SECTION =========", "color: purple; font-weight: bold; text-decoration: underline;");
        storedCommands.forEach((frame, fIndex) => {
            logUnifiedFrame("Cmd", fIndex, frame, /cmd_|com_|com/);
        });
    }

    // 3. RESPONSES
    if (sessionStorage.getItem('frame_response') === 'true') {
        console.log("%c\n========= 3. RESPONSE SECTION =========", "color: orange; font-weight: bold; text-decoration: underline;");
        storedResponses.forEach((frame, fIndex) => {
            logUnifiedFrame("Res", fIndex, frame, /res_|resp_|resp/);
        });
    }

    // 4. ERRORS
    if (sessionStorage.getItem('frame_error') === 'true') {
        console.log("%c\n========= 4. ERROR SECTION =========", "color: red; font-weight: bold; text-decoration: underline;");
        storedErrors.forEach((frame, fIndex) => {
            logUnifiedFrame("Err", fIndex, frame, /err_|error_|error/);
        });
    }
}

// Helper to log Meta, then Rows, then Bits in proper sequence
function logUnifiedFrame(type, fIndex, frame, prefixRegex) {
    console.log(`%c>>> ${type}[${fIndex}] Configuration`, "font-weight:bold; color: #444;");

    // --- STEP A: PRINT META DATA FIRST ---
    // NEW: Define prefixes for live capture fallback
    const mPre = (type === "Cmd") ? "com" : (type === "Res" ? "resp" : "error");
    const sPre = (type === "Cmd") ? "cmd" : (type === "Res" ? "resp" : "error");

    // Use stored meta, OR if missing, capture live from UI
    const metaToPrint = frame.meta || {
        parametername: document.getElementById(`${mPre}_parametername`)?.value || "",
        noofrows: document.getElementById(`${sPre}_noofrows`)?.value || "0",
        remarks: document.getElementById(`${sPre}_remarks`)?.value || "",
        dataflowdirection: document.getElementById(`${sPre}_data_flow_direction`)?.value || "",
        frametype: document.getElementById(`${sPre}_frametype`)?.value || "",
        framesize_bytes: document.getElementById(`${sPre}_framesizeinbytes`)?.value || "",
        frametrigger: document.getElementById(`${sPre}_frametrigger`)?.value || document.getElementById('cmd_frame_trigger_options')?.value || "",
        multipleui: document.getElementById(`${sPre}_multipleui`)?.value || "",
        triggerui: document.getElementById(`${sPre}_triggerui`)?.value || "",
        voicecommand: document.getElementById(`${sPre}_voice_command`)?.value || "",
        voicecontrol: document.getElementById(`${sPre}_voice_control_instruction`)?.value || ""
    };

    console.log(`%c[${type}-${fIndex}] Metadata:`, "color: #800080; font-style: italic;");
    Object.entries(metaToPrint).forEach(([k, v]) => {
        const cleanKey = k.replace(prefixRegex, "");
        console.log(`${type}[${fIndex}]-meta-${cleanKey} => ${v}`);
    });

    // --- STEP B: PRINT TABLE ROWS NEXT ---
    const rows = frame.rows || [];
    if (rows.length > 0) {
        console.log(`%c[${type}-${fIndex}] Table Values:`, "color: #A52A2A; font-style: italic;");
        rows.forEach((row, rIndex) => {
            Object.entries(row).forEach(([rk, rv]) => {
                if (rk !== "bitwise") {
                    const cleanRowKey = rk.replace(prefixRegex, "");
                    console.log(`${type}[${fIndex}]-row[${rIndex+1}]-${cleanRowKey} => ${rv}`);
                }
            });

            if (Array.isArray(row.bitwise)) {
                row.bitwise.forEach((bit, bIndex) => {
                    Object.entries(bit).forEach(([bk, bv]) => {
                        const cleanBitKey = bk.replace(/cmd_cmd_|resp_resp_|error_error_|cmd_|resp_|error_/, "");
                        console.log(`${type}[${fIndex}]-row[${rIndex+1}]-bit[${bIndex+1}]-${cleanBitKey} => ${bv}`);
                    });
                });
            }
        });
    }
    console.log("%c------------------------------------------------", "color: #ccc;");
}
function checkPendingPages() {
    let pending = [];
    const validate = (id, storage, label) => {
        const expected = parseInt(document.getElementById(id)?.value || 0);
        for (let i = 0; i < expected; i++) {
            if (!storage[i] || !storage[i].rows || storage[i].rows.length === 0) {
                pending.push(`${label} Page ${i}`);
            }
        }
    };

    validate("numberofpages", storedCommands, "Command");
    validate("page", storedResponses, "Response");
    validate("pages", storedErrors, "Error");

    if (pending.length > 0) {
        alert("PENDING PAGES:\n" + pending.join("\n"));
        return false;
    }
    return true;
}
//
function showRemainingFramesAlert() {
    const remaining = [];

    const getRemaining = (inputId, storage, label) => {
        const expected = parseInt(document.getElementById(inputId)?.value || 0);
        let filled = 0;

        for (let i = 0; i < expected; i++) {
            if (storage[i] && storage[i].rows && storage[i].rows.length > 0) {
                filled++;
            }
        }

        const left = expected - filled;
        if (left > 0) {
            remaining.push(`${label}: ${left} page${left > 1 ? "s" : ""} remaining`);
        }
    };

    getRemaining("numberofpages", storedCommands, "Command");
    getRemaining("page", storedResponses, "Response");
    getRemaining("pages", storedErrors, "Error");

    if (remaining.length === 0) {
        alert("🎉 All frames completed!");
    } else {
        alert("Remaining Frames:\n\n" + remaining.join("\n"));
    }
}


/**
 * 1. GENERATES COMMAND BOXES
 */
function generateCommandProgressBoxes() {
    const tracker = document.getElementById('command-progress-tracker');
    const inputField = document.getElementById('numberofpages'); // Command total pages

    if (!tracker || !inputField) return;

    let totalPages = parseInt(inputField.value) || 1;
    tracker.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const box = document.createElement('div');
        box.textContent = i;
        box.id = `box-command-step-${i}`;
        box.className = 'step-box';

        Object.assign(box.style, {
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            width: "35px",
            height: "35px",
            borderRadius: "4px",
            border: "2px solid #ccc",
            fontWeight: "bold",
            margin: "0 5px",
            flexShrink: "0",
            backgroundColor: "white",
            color: "black",
            transition: "all 0.3s ease"
        });

        // make boxes clickable to jump directly to a page
        box.addEventListener('click', function () {
            // Save current page then move
            try { saveCurrentCommandState(commandCount - 1); } catch (e) {}
            commandCount = i;
            if (storedCommands[commandCount - 1]) {
                applyCommandFrameData(storedCommands[commandCount - 1]);
            } else {
                resetCommandUI();
            }
            updateUIState(parseInt(document.getElementById('numberofpages')?.value || 1));
            box.scrollIntoView({ behavior: 'smooth', inline: 'center' });
        });

        tracker.appendChild(box);
    }

    updateCommandBoxColors();
    // Initialize button labels
    const maxFrames = parseInt(document.getElementById('numberofpages')?.value || 1);
    updateCommandButtonLabels(maxFrames);
}
let filledCommandPages = [];
let commandPageTouched = [];

function updateCommandBoxColors() {
    const tracker = document.getElementById('command-progress-tracker');
    if (!tracker) return;

    const boxes = tracker.children;

    for (let i = 0; i < boxes.length; i++) {
        const box = boxes[i];
        const pageNum = i + 1;
        const idx = pageNum - 1;

        // -------- CURRENT COMMAND PAGE --------
        if (pageNum === commandCount) {

            box.style.backgroundColor = '#cce5ff';
            box.style.color = '#004085';
            box.style.borderWidth = '2px';
            box.style.borderStyle = 'solid';
            box.style.borderColor = 'gold';

            if (commandPageTouched[idx] === true) {

                const dir = (document.getElementById('cmd_data_flow_direction')?.value || "").trim();
                const size = (document.getElementById('cmd_framesizeinbytes')?.value || "").trim();
                const trigger = (document.getElementById('cmd_frame_trigger_options')?.value || "").trim();

                const isDirFilled = dir !== "" && !dir.toLowerCase().includes("select");
                const isSizeFilled = size !== "" && size !== "0";
                const isTriggerFilled = trigger !== "" && trigger !== "None";

                let tableHasUserData = false;
                document.querySelectorAll('#additionalTableBody tr input').forEach(input => {
                    if (input.value.trim() !== "") tableHasUserData = true;
                });

                if (isDirFilled || isSizeFilled || isTriggerFilled || tableHasUserData) {
                    filledCommandPages[idx] = true;
                }
            }

            if (filledCommandPages[idx] === true) {
                box.style.backgroundColor = '#007bff';
                box.style.color = 'white';
                box.style.borderColor = 'gold';
            }

        }
        // -------- OTHER COMMAND PAGES --------
        else {
            if (filledCommandPages[idx] === true) {
                box.style.backgroundColor = '#007bff';
                box.style.borderColor = '#0056b3';
                box.style.color = 'white';
                box.style.borderWidth = '1px';
                box.style.borderStyle = 'solid';
            } else {
                box.style.backgroundColor = 'white';
                box.style.borderColor = '#ccc';
                box.style.color = 'black';
                box.style.borderWidth = '1px';
                box.style.borderStyle = 'solid';
            }
        }
    }
}
// When total pages change
document.getElementById('numberofpages').addEventListener('input', () => {
    commandCount = 1;
    generateCommandProgressBoxes();
    updateCommandFrameTitle();
});

// Initialize on load
window.addEventListener('DOMContentLoaded', generateCommandProgressBoxes);

// Track typing/input inside command step or table
document.addEventListener('input', (e) => {
    if (!e.target.closest('#step-2, #additionalTableBody')) return;

    commandPageTouched[commandCount - 1] = true;
    updateCommandBoxColors();
});

// Track dropdown changes inside command step or table
document.addEventListener('change', (e) => {
    if (!e.target.closest('#step-2, #additionalTableBody')) return;

    commandPageTouched[commandCount - 1] = true;
    updateCommandBoxColors();
});




//
// --- Response Frame State ---
 
/**
 * 1. GENERATES THE PROGRESS BOXES FOR RESPONSE
 */
function generateResponseProgressBoxes() {
    const tracker = document.getElementById('response-progress-tracker');
    const inputField = document.getElementById('page'); // Response total pages input ID
    
    if (!tracker || !inputField) return;

    let totalPages = parseInt(inputField.value) || 1;
    tracker.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const box = document.createElement('div');
        box.textContent = i;
        box.id = `box-response-step-${i}`;
        box.className = 'step-box';
        
        Object.assign(box.style, {
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            width: "35px",
            height: "35px",
            borderRadius: "4px", // Square style
            border: "2px solid #ccc",
            fontWeight: "bold",
            margin: "0 5px",
            flexShrink: "0",
            backgroundColor: "white",
            color: "black",
            transition: "all 0.3s ease" // Smooth color change
        });
        // clickable boxes for response pages
        box.addEventListener('click', function() {
            try { saveCurrentResponseState(responseCount - 1); } catch (e) {}
            responseCount = i;
            if (storedResponses[responseCount - 1]) {
                applyResponseFrameData(storedResponses[responseCount - 1]);
            } else {
                if(document.getElementById('resp_data_flow_direction')) document.getElementById('resp_data_flow_direction').value = "";
                if(document.getElementById('resp_framesizeinbytes')) document.getElementById('resp_framesizeinbytes').value = "1";
                const tbody = document.getElementById('responseTableBody');
                if (tbody) tbody.innerHTML = '';
                if (typeof addRow === "function") addRow('resp');
            }
            updateResponseFrameTitle();
            updateResponseButtonLabels(parseInt(document.getElementById('page')?.value || 1));
            box.scrollIntoView({ behavior: 'smooth', inline: 'center' });
        });

        tracker.appendChild(box);
    }
    updateResponseBoxColors();
    // Initialize button labels
    const maxRespFrames = parseInt(document.getElementById('page')?.value || 1);
    updateResponseButtonLabels(maxRespFrames);
}

/**
 * 2. UPDATES COLORS FOR RESPONSE
 */
let filledResponsePages = [];
let responsePageTouched = [];

function updateResponseBoxColors() {
    const tracker = document.getElementById('response-progress-tracker');
    if (!tracker) return;

    const boxes = tracker.children;

    for (let i = 0; i < boxes.length; i++) {
        const box = boxes[i];
        const pageNum = i + 1;
        const idx = pageNum - 1;

        // -------- CURRENT RESPONSE PAGE --------
        if (pageNum === responseCount) {

            // Default style for current page
            box.style.backgroundColor = '#cce5ff'; // light blue
            box.style.color = '#0056b3';
            box.style.borderWidth = '2px';
            box.style.borderStyle = 'solid';
            box.style.borderColor = 'gold'; // 🔶 GOLD BORDER for current page

            // Only evaluate after user interaction
            if (responsePageTouched[idx] === true) {

                const dir = (document.getElementById('resp_data_flow_direction')?.value || "").trim();
                const size = (document.getElementById('resp_framesizeinbytes')?.value || "").trim();

                const isDirFilled = dir !== "" && !dir.toLowerCase().includes("select");
                const isSizeFilled = size !== "" && size !== "0";

                let tableHasUserData = false;
                document.querySelectorAll('#responseTableBody tr input').forEach(input => {
                    if (input.value.trim() !== "") tableHasUserData = true;
                });

                if (isDirFilled || isSizeFilled || tableHasUserData) {
                    filledResponsePages[idx] = true;
                }
            }

            // If current page already filled, make background dark blue
            if (filledResponsePages[idx] === true) {
                box.style.backgroundColor = '#007bff'; // dark blue
                box.style.color = 'white';
                box.style.borderColor = 'gold';
            }

        } 
        // -------- OTHER RESPONSE PAGES --------
        else {
            if (filledResponsePages[idx] === true) {
                box.style.backgroundColor = '#007bff'; // dark blue
                box.style.borderColor = '#0056b3';
                box.style.color = 'white';
                box.style.borderWidth = '1px';
                box.style.borderStyle = 'solid';
            } else {
                box.style.backgroundColor = 'white';
                box.style.borderColor = '#ccc';
                box.style.color = 'black';
                box.style.borderWidth = '1px';
                box.style.borderStyle = 'solid';
            }
        }
    }
}

// --- 3. Event Listeners for Response ---

// Triggered when the total number of pages input changes
document.getElementById('page').addEventListener('input', () => {
    responseCount = 1; // Reset to start when page count changes
    generateResponseProgressBoxes();
    updateResponseFrameTitle();
});

// Initialize boxes when page loads
window.addEventListener('DOMContentLoaded', generateResponseProgressBoxes);

// Track typing/input in Response Frame fields or table
document.addEventListener('input', (e) => {
    // Fire only if input is inside response step or table
    if (!e.target.closest('#step-3, #responseTableBody')) return;

    responsePageTouched[responseCount - 1] = true;
    updateResponseBoxColors();
});

// Track dropdown changes
document.addEventListener('change', (e) => {
    if (!e.target.closest('#step-3, #responseTableBody')) return;

    responsePageTouched[responseCount - 1] = true;
    updateResponseBoxColors();
});

//--error frame--

/**
 * 1. GENERATES THE PROGRESS BOXES FOR ERROR
 */
function generateErrorProgressBoxes() {
    const tracker = document.getElementById('error-progress-tracker');
    const inputField = document.getElementById('pages'); // Error total pages input ID
    
    if (!tracker || !inputField) return;

    let totalPages = parseInt(inputField.value) || 1;
    tracker.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const box = document.createElement('div');
        box.textContent = i;
        box.id = `box-error-step-${i}`;
        box.className = 'step-box';
        
        Object.assign(box.style, {
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            width: "35px",
            height: "35px",
            borderRadius: "4px", // Square style
            border: "2px solid #ccc",
            fontWeight: "bold",
            margin: "0 5px",
            flexShrink: "0",
            backgroundColor: "white",
            color: "black",
            transition: "all 0.3s ease" // Smooth color change
        });
        
        // clickable boxes for error pages
        box.addEventListener('click', function() {
            try { saveCurrentErrorState(errorCount - 1); } catch (e) {}
            errorCount = i;
            if (storedErrors[errorCount - 1]) {
                applyErrorFrameData(storedErrors[errorCount - 1]);
            } else {
                if(document.getElementById('error_data_flow_direction')) document.getElementById('error_data_flow_direction').value = "";
                if(document.getElementById('error_framesizeinbytes')) document.getElementById('error_framesizeinbytes').value = "";
                const tbody = document.getElementById('errorTableBody');
                if (tbody) tbody.innerHTML = '';
                if (typeof addRow === "function") addRow('error');
            }
            updateErrorFrameTitle();
            updateErrorButtonLabels(parseInt(document.getElementById('pages')?.value || 1));
            box.scrollIntoView({ behavior: 'smooth', inline: 'center' });
        });

        tracker.appendChild(box);
    }
    updateErrorBoxColors();
    // Initialize button labels
    const maxErrFrames = parseInt(document.getElementById('pages')?.value || 1);
    updateErrorButtonLabels(maxErrFrames);
}

/**
 * 2. UPDATES COLORS FOR ERROR
 */
let filledErrorPages = [];
let errorPageTouched = [];

function updateErrorBoxColors() {
    const tracker = document.getElementById('error-progress-tracker');
    if (!tracker) return;

    const boxes = tracker.children;

    for (let i = 0; i < boxes.length; i++) {
        const box = boxes[i];
        const pageNum = i + 1;
        const idx = pageNum - 1;

        // -------- CURRENT ERROR PAGE --------
        if (pageNum === errorCount) {

            // Default style for current page
            box.style.backgroundColor = '#cce5ff'; // light blue
            box.style.color = '#0056b3';
            box.style.borderWidth = '2px';
            box.style.borderStyle = 'solid';
            box.style.borderColor = 'gold'; // 🔶 GOLD BORDER for current page

            // Only evaluate after user interaction
            if (errorPageTouched[idx] === true) {

                const dir = (document.getElementById('error_data_flow_direction')?.value || "").trim();
                const size = (document.getElementById('error_framesizeinbytes')?.value || "").trim();
                const trigger = (document.getElementById('error_frametrigger')?.value || "").trim();

                const isDirFilled = dir !== "" && !dir.toLowerCase().includes("select");
                const isSizeFilled = size !== "" && size !== "0";
                const isTriggerFilled = trigger !== "" && trigger !== "None";

                let tableHasUserData = false;
                document.querySelectorAll('#errorTableBody tr input').forEach(input => {
                    if (input.value.trim() !== "") tableHasUserData = true;
                });

                if (isDirFilled || isSizeFilled || isTriggerFilled || tableHasUserData) {
                    filledErrorPages[idx] = true;
                }
            }

            // If current page already filled, make background dark blue
            if (filledErrorPages[idx] === true) {
                box.style.backgroundColor = '#007bff'; // dark blue
                box.style.color = 'white';
                box.style.borderColor = 'gold';
            }

        } 
        // -------- OTHER ERROR PAGES --------
        else {
            if (filledErrorPages[idx] === true) {
                box.style.backgroundColor = '#007bff'; // dark blue
                box.style.borderColor = '#0056b3';
                box.style.color = 'white';
                box.style.borderWidth = '1px';
                box.style.borderStyle = 'solid';
            } else {
                box.style.backgroundColor = 'white';
                box.style.borderColor = '#ccc';
                box.style.color = 'black';
                box.style.borderWidth = '1px';
                box.style.borderStyle = 'solid';
            }
        }
    }
}

// --- 3. Event Listeners for Error ---
document.getElementById('pages').addEventListener('input', () => {
    errorCount = 1; // Reset to start when page count changes
    generateErrorProgressBoxes();
    updateErrorFrameTitle();
});

// Initialize boxes when page loads
window.addEventListener('DOMContentLoaded', generateErrorProgressBoxes);

// Track typing/input in Error Frame fields or table
document.addEventListener('input', (e) => {
    // Only fire if the input is inside the error step or error table
    if (!e.target.closest('#step-4, #errorTableBody')) return;

    errorPageTouched[errorCount - 1] = true;
    updateErrorBoxColors();
});

// Track dropdown changes
document.addEventListener('change', (e) => {
    if (!e.target.closest('#step-4, #errorTableBody')) return;

    errorPageTouched[errorCount - 1] = true;
    updateErrorBoxColors();
});

//1st page 

//
//
function showFrame(frameType) {
    // Use the existing step ids: step-2 (command), step-3 (response), step-4 (error)
    ['step-2','step-3','step-4'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.remove('active');
    });

    if (frameType === 'command') {
        const el = document.getElementById('step-2'); if (el) el.classList.add('active');
        generateCommandProgressBoxes();
    } else if (frameType === 'response') {
        const el = document.getElementById('step-3'); if (el) el.classList.add('active');
        generateResponseProgressBoxes();
    } else if (frameType === 'error') {
        const el = document.getElementById('step-4'); if (el) el.classList.add('active');
        generateErrorProgressBoxes();
    }

    highlightFrameTab(frameType);
}

// Highlights the top progress flow step for the given frame type
function highlightFrameTab(frameType) {
    const map = { command: 'step-indicator-2', response: 'step-indicator-3', error: 'step-indicator-4' };
    document.querySelectorAll('.flow-step').forEach(el => el.classList.remove('active'));
    const id = map[frameType];
    if (id) {
        const target = document.getElementById(id);
        if (target) target.classList.add('active');
    }
}

//
function initForm() {

    responseCount = 1;
    errorCount = 1;

    responsePageTouched = [];
    errorPageTouched = [];
    filledErrorPages = [];

    document.querySelectorAll('#file-form input').forEach(i => i.value = '');
    document.querySelectorAll('#file-form select').forEach(s => s.selectedIndex = 0);

    bindFrameSizeListeners();
    bindRowCalculations();

    generateCommandProgressBoxes();
    generateResponseProgressBoxes();
    generateErrorProgressBoxes();

    showFrame('command');

    console.log("Form initialized clean");
}
//
function closeAllModalsAndReset() {
    const modal = document.getElementById("modalContainer");
    modal.style.display = "none";

    // reset to original state
    modal.innerHTML = originalModalHTML;
}

//
function openFormAgain() {
    const modal = document.getElementById("modalContainer");
    modal.style.display = "block";
}

//



//
document.addEventListener('click', function() {
    // Determine which form-step is currently active and sync top flow indicator
    const active = document.querySelector('.form-step.active');
    if (!active) return;
    const id = active.id || '';
    if (id === 'step-2') highlightFrameTab('command');
    else if (id === 'step-3') highlightFrameTab('response');
    else if (id === 'step-4') highlightFrameTab('error');
});
//enablemsg
function handleEnableChange(inputElement, prefix, index) {
    const row = inputElement.closest('tr');
    const messageInput = row.querySelector(`input[name="message_${prefix}_${index}"]`);
    let val = inputElement.value.trim().toLowerCase();
    
    if (val === 'yes' || val === 'y') {
        inputElement.value = "Yes";
        messageInput.disabled = false;
        
        // Only set the error state if the box is actually empty
        if (messageInput.value.trim() === "" || messageInput.value === "Enter notification message") {
            messageInput.value = "Enter notification message";
            messageInput.style.color = "red";
            messageInput.classList.add("error");
            // DO NOT add pink-highlight here yet, 
            // wait until they try to click "Next"
        }
    } else {
        inputElement.value = "No";
        messageInput.value = "";
        messageInput.disabled = true;
        
        // Clean up only the message field
        messageInput.classList.remove("error");
        messageInput.style.color = "";
        
        // Only remove pink-highlight if NO OTHER field has an error
        const otherErrors = row.querySelectorAll('.error');
        if (otherErrors.length === 0) {
            row.classList.remove("pink-highlight");
        }
    }
    
    if (typeof syncHiddenField === "function") {
        syncHiddenField(prefix, 'enable', index, inputElement.value);
    }
}

//
// 1. THIS CLEARS RED COLOR WHILE TYPING
document.addEventListener('input', function(e) {
    // Only target message fields
    if (e.target && e.target.name && e.target.name.startsWith('message_')) {
        const field = e.target;
        const row = field.closest('tr');

        // If the user has typed actual content
        if (field.value.trim() !== "" && field.value !== "Enter notification message" && field.value !== "Error") {
            field.classList.remove("error");
            field.style.color = "black"; // Reset text to black
            field.style.borderColor = ""; // Reset border
            
            // Check if we can remove the pink highlight from the row
            const otherErrors = row.querySelectorAll('.error');
            if (otherErrors.length === 0) {
                row.classList.remove("pink-highlight");
            }
        }
    }
});

// 2. UPDATE YOUR FOCUSIN (To match "Enter notification message")
document.addEventListener('focusin', function(e) {
    if (e.target && e.target.name && e.target.name.startsWith('message_')) {
        // Clear both "Error" and your new placeholder text
        if (e.target.value === "Error" || e.target.value === "Enter notification message") {
            e.target.value = "";
            e.target.style.color = "black";
            e.target.classList.remove("error");
        }
    }
});

//
function printCmdConsole(storedCommands) {

    storedCommands.forEach((page, cmdIndex) => {
        if (!page || !page.rows) return;

        page.rows.forEach((row, rowIndex) => {
            const rowNo = rowIndex + 1;

            // ================= BITWISE =================
            if (Array.isArray(row.bitwise) && row.bitwise.length) {

                row.bitwise.forEach((bitRow, bitIndex) => {
                    Object.entries(bitRow).forEach(([key, value]) => {
                        console.log(
                          `Cmd[${cmdIndex}]-row[${rowNo}]-bit[${bitIndex + 1}]-${key} => ${value}`
                        );
                    });
                });

            }
            // ================= BYTEWISE =================
            else {
                Object.entries(row).forEach(([key, value]) => {
                    if (key === 'bitwise') return;

                    console.log(
                      `Cmd[${cmdIndex}]-row[${rowNo}]-${key} => ${value}`
                    );
                });
            }

        });
    });
}

//


</script>




<script>
function logFull(label, obj) {
    console.log(label, JSON.parse(JSON.stringify(obj)));
}
</script>

<script>
if (!window.originalModalHTML) {
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("modalContainer");
        if (modal) {
            window.originalModalHTML = modal.innerHTML;
            console.log("Snapshot stored");
        }
    });
}
</script>


<script>
function logAllRowCounts() {
    console.log("------ FORM STATE ------");
    console.log("Command rows:", document.querySelectorAll("#commandTableBody tr").length);
    console.log("Response rows:", document.querySelectorAll("#responseTableBody tr").length);
    console.log("Error rows:", document.querySelectorAll("#errorTableBody tr").length);
    console.log("------------------------");
}

</script>


<script>
(function() {
    const originalShow = Element.prototype.appendChild;
    Element.prototype.appendChild = function() {
        console.trace("APPEND CHILD CALLED");
        return originalShow.apply(this, arguments);
    };
})();

//
function validateDuplicateByteRange(tableBodyId, prefix, errorId) {
    const rows = document.querySelectorAll(`#${tableBodyId} tr`);
    const usedNumbers = {}; // Stores { number: inputElement }
    let hasDuplicate = false;

    // 1. Clear previous red borders from ALL inputs in this table
    rows.forEach(row => {
        row.querySelectorAll('input').forEach(input => {
            input.style.border = "1px solid #ccc"; // Reset to default
        });
    });

    rows.forEach((row, index) => {
        const startInput = row.querySelector(`input[name^="startBytePosition_${prefix}_"]`);
        const endInput   = row.querySelector(`input[name^="endBytePosition_${prefix}_"]`);

        if (!startInput || !endInput) return;

        const inputs = [startInput, endInput];

        inputs.forEach(currentInput => {
            const val = currentInput.value.trim();
            if (val === "") return;

            // Check if this number was used by an input in a DIFFERENT row
            if (usedNumbers[val] !== undefined) {
                const previousInput = usedNumbers[val];
                
                // Get the row index of the previous input to compare
                const prevRow = previousInput.closest('tr');
                const currRow = currentInput.closest('tr');

                if (prevRow !== currRow) {
                    // Highlight the current field
                    currentInput.style.border = "2px solid red";
                    currentInput.style.backgroundColor = "#ffe6e6";
                    
                    // Highlight the previous field that had this number
                    previousInput.style.border = "2px solid red";
                    previousInput.style.backgroundColor = "#ffe6e6";
                    
                    hasDuplicate = true;
                }
            } else {
                // First time seeing this number, store this specific input element
                usedNumbers[val] = currentInput;
            }
        });
    });

    if (hasDuplicate) {
        const msg = "Duplicate Number: The highlighted byte positions are already used in another row.";
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.innerText = msg;
            errorElement.style.display = "block";
        }
        forceMessage(msg);
        return false;
    }
    return true;
}
//

</script>


<script>
document.addEventListener('click', function(event) {
    // Detect if the clicked element (or its icon/parent) is the close button
    const closeBtn = event.target.closest('.btn-close');
    
    if (closeBtn) {
        // This forces the entire page to reload, clearing all memory/states
        window.location.reload();
    }
});


</script>



<!-- ==================== FINAL FIX PATCH SCRIPT ==================== -->
<script>
(function () {
    "use strict";

    function getFramePrefix(step) {
        return step === 2 ? 'cmd' : (step === 3 ? 'resp' : 'error');
    }

    function getTableIdByPrefix(prefix) {
        if (prefix === 'cmd') return 'additionalTableBody';
        if (prefix === 'resp') return 'responseTableBody';
        return 'errorTableBody';
    }

    function getStateIndex(prefix) {
        if (prefix === 'cmd') return (typeof commandCount !== 'undefined' ? commandCount : 1) - 1;
        if (prefix === 'resp') return (typeof responseCount !== 'undefined' ? responseCount : 1) - 1;
        return (typeof errorCount !== 'undefined' ? errorCount : 1) - 1;
    }

    function safeJsonParse(value, fallback) {
        try { return value ? JSON.parse(value) : fallback; } catch (e) { return fallback; }
    }

    function getField(row, selector) {
        const el = row ? row.querySelector(selector) : null;
        return el ? (el.value || '') : '';
    }

    function firstFilledValue() {
        for (let i = 0; i < arguments.length; i++) {
            const value = arguments[i];
            if (value !== undefined && value !== null && String(value).trim() !== '') {
                return value;
            }
        }
        return '';
    }

    function rowValue(rowData, keys, fallback) {
        for (let i = 0; i < keys.length; i++) {
            if (rowData && rowData[keys[i]] !== undefined && rowData[keys[i]] !== null && String(rowData[keys[i]]).trim() !== '') {
                return rowData[keys[i]];
            }
        }
        return fallback !== undefined ? fallback : '';
    }

    function setFrameField(id, value) {
        const el = document.getElementById(id);
        if (!el) return;
        const incomingValue = value === undefined || value === null ? '' : String(value);
        const normalizedValue = el.tagName === 'SELECT' ? normalizeSelectRestoreValue(el, incomingValue) : incomingValue;
        el.value = normalizedValue;
        el.setAttribute('value', normalizedValue);
        if (el.tagName === 'SELECT') {
            Array.from(el.options).forEach(function (option) {
                option.selected = option.value === normalizedValue;
            });
            if (typeof el.__apiSmartSelectSync === 'function') {
                el.__apiSmartSelectSync();
            }
        }
    }

    function setRowField(row, selector, value) {
        const el = row ? row.querySelector(selector) : null;
        if (!el) return;
        const incomingValue = value === undefined || value === null ? '' : String(value);
        let normalizedValue = incomingValue;
        if (el.tagName === 'SELECT') {
            normalizedValue = normalizeSelectRestoreValue(el, incomingValue);
        }
        el.value = normalizedValue;
        el.setAttribute('value', normalizedValue);
        if (el.tagName === 'SELECT') {
            Array.from(el.options).forEach(opt => opt.selected = (opt.value === normalizedValue));
            if (typeof el.__apiSmartSelectSync === 'function') {
                el.__apiSmartSelectSync();
            }
        } else if (/^\[name="enable_/.test(selector) && typeof handleEnableChange === 'function') {
            const match = selector.match(/enable_([^_]+)_(\d+)/);
            const restorePrefix = match ? match[1] : '';
            const restoreIndex = match ? parseInt(match[2], 10) : 1;
            if (restorePrefix && row.querySelector(`input[name="message_${restorePrefix}_${restoreIndex}"]`)) {
                handleEnableChange(el, restorePrefix, restoreIndex);
            }
        }
    }

    function normalizeSelectRestoreValue(select, value) {
        const raw = String(value || '').trim();
        if (!raw) return '';

        const compact = raw.toLowerCase().replace(/[^a-z0-9]/g, '');
        const aliases = {
            unsignedinteger: 'UnsignedInt',
            unsignedint: 'UnsignedInt',
            signedinteger: 'SignedInt',
            signedint: 'SignedInt',
            fixed: 'Fixed Value',
            fixedvalue: 'Fixed Value',
            range: 'Range Value',
            rangevalue: 'Range Value',
            dynamic: 'Dynamic Value',
            dynamicvalue: 'Dynamic Value',
            enum: 'Enum',
            enumvalue: 'Enum Value',
            calculated: 'Calculated Value',
            calculatedvalue: 'Calculated Value',
            dropdown: 'Drop Down',
            dropdownvalue: 'Drop Down',
            pushbuttonbig: 'Push Button',
            buttonbig: 'Button',
            readwrite: 'Read/Write',
            none: select.querySelector('option[value="None"]') ? 'None' : (select.querySelector('option[value="NONE"]') ? 'NONE' : 'None')
        };

        const direct = Array.from(select.options).find(function (option) {
            return option.value === raw;
        });
        if (direct) return direct.value;

        const byText = Array.from(select.options).find(function (option) {
            return option.textContent.trim().toLowerCase() === raw.toLowerCase();
        });
        if (byText) return byText.value;

        if (aliases[compact]) {
            const aliasValue = aliases[compact];
            const aliasOption = Array.from(select.options).find(function (option) {
                return option.value === aliasValue || option.textContent.trim() === aliasValue;
            });
            if (aliasOption) return aliasOption.value;
        }

        return raw;
    }

    window.normalizeApiSelectRestoreValue = normalizeSelectRestoreValue;

    function bitwiseValue(bit, keys) {
        for (let i = 0; i < keys.length; i++) {
            if (bit && bit[keys[i]] !== undefined && bit[keys[i]] !== null && String(bit[keys[i]]).trim() !== '') {
                return bit[keys[i]];
            }
        }
        return '';
    }

    function setBitwiseSummaryCell(row, cellIndex, values) {
        const cell = row.cells[cellIndex];
        if (!cell) return;

        const cleanValues = values.filter(function (item) {
            return String(item || '').trim() !== '';
        });
        const value = cleanValues.length ? '[' + cleanValues.join(',') + ']' : '';

        let control = cell.querySelector('input, select');
        if (!control) {
            control = document.createElement('input');
            control.type = 'text';
            control.className = 'compact-input';
            cell.innerHTML = '';
            cell.appendChild(control);
        }

        if (control.tagName === 'SELECT') {
            const input = document.createElement('input');
            input.type = 'text';
            input.className = control.className || 'compact-input';
            control.parentNode.replaceChild(input, control);
            control = input;
        }

        control.type = 'text';
        control.value = value;
        control.setAttribute('value', value);
        control.readOnly = true;
        control.style.backgroundColor = '#F3F4F6';
        control.style.cursor = 'pointer';
        control.title = 'Open Bitwise to edit these values';
        control.onclick = function () { openBitwiseConfig(row); };
    }

    function showBitwiseControls(row) {
        const bitByte = row.querySelector('.bit-byte-select');
        const arrow = row.querySelector('.arrow-btn');
        if (bitByte) {
            bitByte.style.display = 'inline-block';
            bitByte.value = 'Bitwise';
        }
        if (arrow) {
            arrow.style.display = 'inline-block';
            arrow.classList.add('is-visible');
        }
    }

    function applyBitwiseSummaryToMainRow(row, bitwiseRows) {
        if (!row || !Array.isArray(bitwiseRows) || !bitwiseRows.length) return;
        showBitwiseControls(row);

        const map = {
            start: ['startBytePosition_1', 'bitwise_start_bit_1', 'startBit'],
            end: ['endBytePosition_1', 'bitwise_end_bit_1', 'endBit'],
            bits: ['numberOfBytes_1', 'bitSize_1', 'bitwise_no_of_bits_1', 'noOfBits'],
            type: ['dataType_1', 'bitwise_data_type_1', 'dataType'],
            def: ['valueDefinition_1', 'bitwise_value_definition_1', 'valueDefinition'],
            dMin: ['displayValueMin_1', 'displayMin'],
            dMax: ['displayValueMax_1', 'displayMax'],
            hMin: ['valueInHexMin_1', 'hexMin'],
            hMax: ['valueInHexMax_1', 'hexMax'],
            uiType: ['uiType_1', 'uiType'],
            uiId: ['uiId_1', 'uiId'],
            asper: ['asperui_1', 'asperUi'],
            serial: ['sno_asperui_1', 'uiSerialNo'],
            vals: ['asperuivalue_1', 'uiValues'],
            page: ['uiPageNo_1', 'uiPageNo'],
            inout: ['uiDescriptionInOut_1', 'uiInOut'],
            step: ['sliderStepValue_1', 'sliderStep'],
            dflt: ['defaultvalue_1', 'defaultValue'],
            notes: ['developerNotes_1', 'devNotes'],
            notifEnable: ['notifEnable_1', 'notificationEnable'],
            notifMsg: ['notifMsg_1', 'notificationMessage'],
            scaling: ['scaling_1', 'scaling']
        };

        const values = {};
        Object.keys(map).forEach(function (key) {
            values[key] = bitwiseRows.map(function (bit) { return bitwiseValue(bit, map[key]); });
        });

        setBitwiseSummaryCell(row, 1, values.start);
        setBitwiseSummaryCell(row, 2, values.end);
        setBitwiseSummaryCell(row, 4, values.bits);
        setBitwiseSummaryCell(row, 5, values.bits);
        setBitwiseSummaryCell(row, 6, values.type);
        setBitwiseSummaryCell(row, 7, values.def);
        setBitwiseSummaryCell(row, 8, values.dMin);
        setBitwiseSummaryCell(row, 9, values.dMax);
        setBitwiseSummaryCell(row, 10, values.hMin);
        setBitwiseSummaryCell(row, 11, values.hMax);
        setBitwiseSummaryCell(row, 12, values.uiType);
        setBitwiseSummaryCell(row, 13, values.uiId);
        setBitwiseSummaryCell(row, 14, values.asper);
        setBitwiseSummaryCell(row, 15, values.serial);
        setBitwiseSummaryCell(row, 16, values.vals);
        setBitwiseSummaryCell(row, 17, values.page);
        setBitwiseSummaryCell(row, 18, values.inout);
        setBitwiseSummaryCell(row, 19, values.step);
        setBitwiseSummaryCell(row, 20, values.dflt);
        setBitwiseSummaryCell(row, 21, values.notes);
        setBitwiseSummaryCell(row, 22, values.notifEnable);
        setBitwiseSummaryCell(row, 23, values.notifMsg);
        setBitwiseSummaryCell(row, 24, values.scaling);
    }

    function collectRows(prefix) {
        const tableId = getTableIdByPrefix(prefix);
        const rows = [];
        document.querySelectorAll('#' + tableId + ' tr').forEach(function (tr) {
            const typeSelect = tr.querySelector('.bit-byte-select');
            const bitwiseMode = typeSelect ? typeSelect.value : 'Bytewise';
            const bitwise = safeJsonParse(tr.getAttribute('data-bitwise-storage'), null);
            const base = {
                bitwise: bitwise,
                bitwiseMode: bitwiseMode,
                startBytePosition: getField(tr, '[name^="startBytePosition_"]'),
                endBytePosition: getField(tr, '[name^="endBytePosition_"]'),
                numberOfBytes: getField(tr, '[name^="noOfBytes_"]'),
                bitSize: getField(tr, '[name^="bitSize_"]'),
                fieldDescription: getField(tr, '[name^="fieldDescription_"]'),
                dataType: getField(tr, '[name^="dataType_"]'),
                valueDefinition: getField(tr, '[name^="valueDefinition_"]'),
                displayValueMin: getField(tr, '[name^="displayMin_"]'),
                displayValueMax: getField(tr, '[name^="displayMax_"]'),
                valueInHexMin: getField(tr, '[name^="hexMin_"]'),
                valueInHexMax: getField(tr, '[name^="hexMax_"]'),
                uiType: getField(tr, '[name^="uiType_"]'),
                uiId: getField(tr, '[name^="uiId_"]'),
                asperui: getField(tr, '[name^="asperui_"]'),
                sno_asperui: getField(tr, '[name^="sno_asperui_"]'),
                asperuivalue: getField(tr, '[name^="asperuivalue_"]'),
                uiPageNo: getField(tr, '[name^="uiPageNo_"]'),
                uiDescriptionInOut: getField(tr, '[name^="uiInOut_"]'),
                sliderStepValue: getField(tr, '[name^="sliderStep_"]'),
                defaultvalue: getField(tr, '[name^="defaultValue_"]'),
                developerNotes: getField(tr, '[name^="developerNotes_"]'),
                notificationEnable: getField(tr, '[name^="enable_"]') || 'No',
                notificationMessage: getField(tr, '[name^="message_"]'),
                scaling: getField(tr, '[name^="scaling_"]')
            };

            if (prefix === 'cmd') {
                Object.assign(base, {
                    cmd_bitwiseMode: base.bitwiseMode,
                    cmd_startBytePosition: base.startBytePosition,
                    cmd_endBytePosition: base.endBytePosition,
                    cmd_numberOfBytes: base.numberOfBytes,
                    cmd_bitSize: base.bitSize,
                    cmd_fieldDescription: base.fieldDescription,
                    cmd_dataType: base.dataType,
                    cmd_valueDefinition: base.valueDefinition,
                    cmd_displayValueMin: base.displayValueMin,
                    cmd_displayValueMax: base.displayValueMax,
                    cmd_valueInHexMin: base.valueInHexMin,
                    cmd_valueInHexMax: base.valueInHexMax,
                    cmd_uiType: base.uiType,
                    cmd_uiId: base.uiId,
                    cmd_asperui: base.asperui,
                    cmd_sno_asperui: base.sno_asperui,
                    cmd_asperuivalue: base.asperuivalue,
                    cmd_uiPageNo: base.uiPageNo,
                    cmd_uiDescriptionInOut: base.uiDescriptionInOut,
                    cmd_sliderStepValue: base.sliderStepValue,
                    cmd_defaultvalue: base.defaultvalue,
                    cmd_developerNotes: base.developerNotes,
                    cmd_notificationEnable: base.notificationEnable,
                    cmd_notificationMessage: base.notificationMessage,
                    cmd_scaling: base.scaling
                });
            } else if (prefix === 'resp') {
                Object.assign(base, {
                    resp_bitwiseMode: base.bitwiseMode,
                    resp_startBytePosition: base.startBytePosition,
                    resp_endBytePosition: base.endBytePosition,
                    resp_numberOfBytes: base.numberOfBytes,
                    resp_bitSize: base.bitSize,
                    resp_fieldDescription: base.fieldDescription,
                    resp_dataType: base.dataType,
                    resp_valueDefinition: base.valueDefinition,
                    resp_displayValueMin: base.displayValueMin,
                    resp_displayValueMax: base.displayValueMax,
                    resp_valueInHexMin: base.valueInHexMin,
                    resp_valueInHexMax: base.valueInHexMax,
                    resp_uiType: base.uiType,
                    resp_uiId: base.uiId,
                    resp_asperui: base.asperui,
                    resp_sno_asperui: base.sno_asperui,
                    resp_asperuivalue: base.asperuivalue,
                    resp_uiPageNo: base.uiPageNo,
                    resp_uiDescriptionInOut: base.uiDescriptionInOut,
                    resp_sliderStepValue: base.sliderStepValue,
                    resp_defaultvalue: base.defaultvalue,
                    resp_developerNotes: base.developerNotes,
                    resp_notificationEnable: base.notificationEnable,
                    resp_notificationMessage: base.notificationMessage,
                    resp_scaling: base.scaling,
                    res_startBytePosition: base.startBytePosition,
                    res_endBytePosition: base.endBytePosition,
                    res_numberOfBytes: base.numberOfBytes,
                    res_fieldDescription: base.fieldDescription,
                    res_dataType: base.dataType,
                    res_valueDefinition: base.valueDefinition,
                    res_displayValueMin: base.displayValueMin,
                    res_displayValueMax: base.displayValueMax,
                    res_valueInHexMin: base.valueInHexMin,
                    res_valueInHexMax: base.valueInHexMax,
                    res_uiType: base.uiType,
                    res_uiId: base.uiId,
                    res_asperui: base.asperui,
                    res_sno_asperui: base.sno_asperui,
                    res_asperuivalue: base.asperuivalue,
                    res_uiPageNo: base.uiPageNo,
                    res_uiDescriptionInOut: base.uiDescriptionInOut,
                    res_sliderStepValue: base.sliderStepValue,
                    res_defaultvalue: base.defaultvalue,
                    res_developerNotes: base.developerNotes,
                    res_notificationEnable: base.notificationEnable,
                    res_notificationMessage: base.notificationMessage,
                    res_scaling: base.scaling
                });
            } else {
                Object.assign(base, {
                    err_bitwiseMode: base.bitwiseMode,
                    err_startBytePosition: base.startBytePosition,
                    err_endBytePosition: base.endBytePosition,
                    err_numberOfBytes: base.numberOfBytes,
                    err_bitSize: base.bitSize,
                    err_fieldDescription: base.fieldDescription,
                    err_dataType: base.dataType,
                    err_valueDefinition: base.valueDefinition,
                    err_displayValueMin: base.displayValueMin,
                    err_displayValueMax: base.displayValueMax,
                    err_valueInHexMin: base.valueInHexMin,
                    err_valueInHexMax: base.valueInHexMax,
                    err_uiType: base.uiType,
                    err_uiId: base.uiId,
                    err_asperui: base.asperui,
                    err_sno_asperui: base.sno_asperui,
                    err_asperuivalue: base.asperuivalue,
                    err_uiPageNo: base.uiPageNo,
                    err_uiDescriptionInOut: base.uiDescriptionInOut,
                    err_sliderStepValue: base.sliderStepValue,
                    err_defaultvalue: base.defaultvalue,
                    err_developerNotes: base.developerNotes,
                    err_notificationEnable: base.notificationEnable,
                    err_notificationMessage: base.notificationMessage,
                    err_scaling: base.scaling
                });
            }
            rows.push(base);
        });
        return rows;
    }

    function applyRows(prefix, rows) {
        const tableId = getTableIdByPrefix(prefix);
        const tbody = document.getElementById(tableId);
        if (!tbody) return;
        tbody.innerHTML = '';
        (rows || []).forEach(function (rowData, idx) {
            if (typeof addRow === 'function') addRow(prefix);
            const tr = tbody.lastElementChild;
            if (!tr) return;
            const index = idx + 1;

            setRowField(tr, `[name="startBytePosition_${prefix}_${index}"]`, rowValue(rowData, ['startBytePosition', prefix + '_startBytePosition', 'res_startBytePosition', 'resp_startBytePosition', 'err_startBytePosition']));
            setRowField(tr, `[name="endBytePosition_${prefix}_${index}"]`, rowValue(rowData, ['endBytePosition', prefix + '_endBytePosition', 'res_endBytePosition', 'resp_endBytePosition', 'err_endBytePosition']));
            setRowField(tr, `[name="noOfBytes_${prefix}_${index}"]`, rowValue(rowData, ['numberOfBytes', prefix + '_numberOfBytes', 'res_numberOfBytes', 'resp_numberOfBytes', 'err_numberOfBytes']));
            setRowField(tr, `[name="bitSize_${prefix}_${index}"]`, rowValue(rowData, ['bitSize', prefix + '_bitSize', 'res_bitSize', 'resp_bitSize', 'err_bitSize']));
            setRowField(tr, `[name="fieldDescription_${prefix}_${index}"]`, rowValue(rowData, ['fieldDescription', prefix + '_fieldDescription', 'res_fieldDescription', 'resp_fieldDescription', 'err_fieldDescription']));
            setRowField(tr, `[name="dataType_${prefix}_${index}"]`, rowValue(rowData, ['dataType', prefix + '_dataType', 'res_dataType', 'resp_dataType', 'err_dataType']));
            setRowField(tr, `[name="valueDefinition_${prefix}_${index}"]`, rowValue(rowData, ['valueDefinition', prefix + '_valueDefinition', 'res_valueDefinition', 'resp_valueDefinition', 'err_valueDefinition']));
            setRowField(tr, `[name="displayMin_${prefix}_${index}"]`, rowValue(rowData, ['displayValueMin', prefix + '_displayValueMin', 'res_displayValueMin', 'resp_displayValueMin', 'err_displayValueMin']));
            setRowField(tr, `[name="displayMax_${prefix}_${index}"]`, rowValue(rowData, ['displayValueMax', prefix + '_displayValueMax', 'res_displayValueMax', 'resp_displayValueMax', 'err_displayValueMax']));
            setRowField(tr, `[name="hexMin_${prefix}_${index}"]`, rowValue(rowData, ['valueInHexMin', prefix + '_valueInHexMin', 'res_valueInHexMin', 'resp_valueInHexMin', 'err_valueInHexMin']));
            setRowField(tr, `[name="hexMax_${prefix}_${index}"]`, rowValue(rowData, ['valueInHexMax', prefix + '_valueInHexMax', 'res_valueInHexMax', 'resp_valueInHexMax', 'err_valueInHexMax']));
            setRowField(tr, `[name="uiType_${prefix}_${index}"]`, rowValue(rowData, ['uiType', prefix + '_uiType', 'res_uiType', 'resp_uiType', 'err_uiType']));
            setRowField(tr, `[name="uiId_${prefix}_${index}"]`, rowValue(rowData, ['uiId', prefix + '_uiId', 'res_uiId', 'resp_uiId', 'err_uiId']));
            setRowField(tr, `[name="asperui_${prefix}_${index}"]`, rowValue(rowData, ['asperui', prefix + '_asperui', 'res_asperui', 'resp_asperui', 'err_asperui']));
            setRowField(tr, `[name="sno_asperui_${prefix}_${index}"]`, rowValue(rowData, ['sno_asperui', prefix + '_sno_asperui', 'res_sno_asperui', 'resp_sno_asperui', 'err_sno_asperui']));
            setRowField(tr, `[name="asperuivalue_${prefix}_${index}"]`, rowValue(rowData, ['asperuivalue', prefix + '_asperuivalue', 'res_asperuivalue', 'resp_asperuivalue', 'err_asperuivalue']));
            setRowField(tr, `[name="uiPageNo_${prefix}_${index}"]`, rowValue(rowData, ['uiPageNo', prefix + '_uiPageNo', 'res_uiPageNo', 'resp_uiPageNo', 'err_uiPageNo']));
            setRowField(tr, `[name="uiInOut_${prefix}_${index}"]`, rowValue(rowData, ['uiDescriptionInOut', prefix + '_uiDescriptionInOut', 'res_uiDescriptionInOut', 'resp_uiDescriptionInOut', 'err_uiDescriptionInOut']));
            setRowField(tr, `[name="sliderStep_${prefix}_${index}"]`, rowValue(rowData, ['sliderStepValue', prefix + '_sliderStepValue', 'res_sliderStepValue', 'resp_sliderStepValue', 'err_sliderStepValue']));
            setRowField(tr, `[name="defaultValue_${prefix}_${index}"]`, rowValue(rowData, ['defaultvalue', 'defaultValue', prefix + '_defaultvalue', 'res_defaultvalue', 'resp_defaultvalue', 'err_defaultvalue']));
            setRowField(tr, `[name="developerNotes_${prefix}_${index}"]`, rowValue(rowData, ['developerNotes', prefix + '_developerNotes', 'res_developerNotes', 'resp_developerNotes', 'err_developerNotes']));
            setRowField(tr, `[name="enable_${prefix}_${index}"]`, rowValue(rowData, ['notificationEnable', prefix + '_notificationEnable', 'res_notificationEnable', 'resp_notificationEnable', 'err_notificationEnable'], 'No'));
            setRowField(tr, `[name="message_${prefix}_${index}"]`, rowValue(rowData, ['notificationMessage', prefix + '_notificationMessage', 'res_notificationMessage', 'resp_notificationMessage', 'err_notificationMessage']));
            setRowField(tr, `[name="scaling_${prefix}_${index}"]`, rowValue(rowData, ['scaling', prefix + '_scaling', 'res_scaling', 'resp_scaling', 'err_scaling']));

            if (rowData.bitwise) {
                tr.setAttribute('data-bitwise-storage', JSON.stringify(rowData.bitwise));
                const mode = tr.querySelector('.bit-byte-select');
                if (mode) mode.value = 'Bitwise';
                applyBitwiseSummaryToMainRow(tr, rowData.bitwise);
            }
        });
        if (typeof updateSerialNumbers === 'function') updateSerialNumbers(tableId);
        if (typeof updateFrameCountsForPrefix === 'function') updateFrameCountsForPrefix(prefix);
    }

    function saveCurrentFrame(prefix) {
        const index = getStateIndex(prefix);
        const rows = collectRows(prefix);
        if (prefix === 'cmd') {
            window.storedCommands = window.storedCommands || (typeof storedCommands !== 'undefined' ? storedCommands : []);
            storedCommands[index] = {
                parameterName: document.getElementById('com_parametername')?.value || '',
                direction: document.getElementById('cmd_data_flow_direction')?.value || '',
                size: document.getElementById('cmd_framesizeinbytes')?.value || '',
                triggerOptions: document.getElementById('cmd_frame_trigger_options')?.value || 'None',
                frameType: document.getElementById('cmd_frametype')?.value || 'COMMAND',
                multipleui: document.getElementById('cmd_multipleui')?.value || 'false',
                triggerui: document.getElementById('cmd_triggerui')?.value || 'false',
                meta: {
                    parametername: document.getElementById('com_parametername')?.value || '',
                    noofrows: document.getElementById('cmd_noofrows')?.value || '0',
                    dataflowdirection: document.getElementById('cmd_data_flow_direction')?.value || '',
                    frametype: document.getElementById('cmd_frametype')?.value || 'COMMAND',
                    framesize_bytes: document.getElementById('cmd_framesizeinbytes')?.value || '',
                    frametrigger: document.getElementById('cmd_frame_trigger_options')?.value || 'None',
                    multipleui: document.getElementById('cmd_multipleui')?.value || '',
                    triggerui: document.getElementById('cmd_triggerui')?.value || ''
                },
                rows: rows
            };
        } else if (prefix === 'resp') {
            window.storedResponses = window.storedResponses || (typeof storedResponses !== 'undefined' ? storedResponses : []);
            storedResponses[index] = {
                parameterName: document.getElementById('resp_parametername')?.value || '',
                direction: document.getElementById('resp_data_flow_direction')?.value || '',
                size: document.getElementById('resp_framesizeinbytes')?.value || '',
                frameType: document.getElementById('resp_frametype')?.value || 'RESPONSE',
                meta: {
                    parametername: document.getElementById('resp_parametername')?.value || '',
                    noofrows: document.getElementById('resp_noofrows')?.value || '0',
                    dataflowdirection: document.getElementById('resp_data_flow_direction')?.value || '',
                    frametype: document.getElementById('resp_frametype')?.value || 'RESPONSE',
                    framesize_bytes: document.getElementById('resp_framesizeinbytes')?.value || ''
                },
                rows: rows
            };
        } else {
            window.storedErrors = window.storedErrors || (typeof storedErrors !== 'undefined' ? storedErrors : []);
            storedErrors[index] = {
                parameterName: document.getElementById('error_parametername')?.value || '',
                direction: document.getElementById('error_data_flow_direction')?.value || '',
                size: document.getElementById('error_framesizeinbytes')?.value || '',
                frameType: document.getElementById('error_frametype')?.value || 'ERROR',
                meta: {
                    parametername: document.getElementById('error_parametername')?.value || '',
                    noofrows: document.getElementById('error_noofrows')?.value || '0',
                    dataflowdirection: document.getElementById('error_data_flow_direction')?.value || '',
                    frametype: document.getElementById('error_frametype')?.value || 'ERROR',
                    framesize_bytes: document.getElementById('error_framesizeinbytes')?.value || ''
                },
                rows: rows
            };
        }
    }

    window.saveCurrentCommandState = function (index) {
        if (typeof commandCount !== 'undefined' && index !== undefined && index !== null) commandCount = index + 1;
        saveCurrentFrame('cmd');
    };
    window.saveCurrentResponseState = function (index) {
        if (typeof responseCount !== 'undefined' && index !== undefined && index !== null) responseCount = index + 1;
        saveCurrentFrame('resp');
    };
    window.saveCurrentErrorState = function (index) {
        if (typeof errorCount !== 'undefined' && index !== undefined && index !== null) errorCount = index + 1;
        saveCurrentFrame('error');
    };
    window.applyCommandFrameData = function (data) {
        if (!data) return;
        const meta = data.meta || {};
        setFrameField('com_parametername', firstFilledValue(data.parameterName, meta.parametername));
        setFrameField('cmd_data_flow_direction', firstFilledValue(data.direction, meta.dataflowdirection, meta.data_flow_direction));
        setFrameField('cmd_frametype', firstFilledValue(data.frameType, meta.frametype, 'COMMAND'));
        setFrameField('cmd_framesizeinbytes', firstFilledValue(data.size, meta.framesize_bytes, meta.frame_size_bytes));
        setFrameField('cmd_frame_trigger_options', firstFilledValue(data.triggerOptions, meta.frametrigger, meta.frame_trigger, 'None'));
        setFrameField('cmd_multipleui', firstFilledValue(data.multipleui, meta.multipleui, meta.multiple_ui, 'false'));
        setFrameField('cmd_triggerui', firstFilledValue(data.triggerui, meta.triggerui, meta.trigger_ui, 'false'));
        applyRows('cmd', data.rows || []);
    };
    window.applyResponseFrameData = function (data) {
        if (!data) return;
        const meta = data.meta || {};
        setFrameField('resp_parametername', firstFilledValue(data.parameterName, meta.parametername));
        setFrameField('resp_data_flow_direction', firstFilledValue(data.direction, meta.dataflowdirection, meta.data_flow_direction));
        setFrameField('resp_frametype', firstFilledValue(data.frameType, meta.frametype, 'RESPONSE'));
        setFrameField('resp_framesizeinbytes', firstFilledValue(data.size, meta.framesize_bytes, meta.frame_size_bytes));
        applyRows('resp', data.rows || []);
    };
    window.applyErrorFrameData = function (data) {
        if (!data) return;
        const meta = data.meta || {};
        setFrameField('error_parametername', firstFilledValue(data.parameterName, meta.parametername));
        setFrameField('error_data_flow_direction', firstFilledValue(data.direction, meta.dataflowdirection, meta.data_flow_direction));
        setFrameField('error_frametype', firstFilledValue(data.frameType, meta.frametype, 'ERROR'));
        setFrameField('error_framesizeinbytes', firstFilledValue(data.size, meta.framesize_bytes, meta.frame_size_bytes));
        applyRows('error', data.rows || []);
    };

    function saveVisibleStep() {
        const active = document.querySelector('.form-step.active') || Array.from(document.querySelectorAll('.form-step')).find(function (el) { return el.style.display === 'block'; });
        if (!active) return;
        if (active.id === 'step-2') saveCurrentFrame('cmd');
        if (active.id === 'step-3') saveCurrentFrame('resp');
        if (active.id === 'step-4') saveCurrentFrame('error');
    }

    window.showStep = function (step) {
        saveVisibleStep();
        document.querySelectorAll('.form-step').forEach(function (el) {
            el.classList.remove('active');
            el.style.display = 'none';
        });
        const target = document.getElementById('step-' + step);
        if (target) {
            target.classList.add('active');
            target.style.display = 'block';
        }
        currentStep = step;
        if (typeof updateProgressIndicator === 'function') updateProgressIndicator();
        window.scrollTo({top: 0, behavior: 'smooth'});
    };

    window.goToNextStep = function (step) {
        let ok = true;
        if (typeof validateStep === 'function') ok = validateStep(step) === true;
        if (!ok) return false;
        saveVisibleStep();
        if (step < 4) window.showStep(step + 1);
        return true;
    };

    window.goToPreviousStep = function (step) {
        saveVisibleStep();
        if (step > 1) window.showStep(step - 1);
    };

    window.clearTable = function (type) {
        const map = {cmd: 'additionalTableBody', resp: 'responseTableBody', error: 'errorTableBody'};
        const tableId = map[type];
        const tbody = document.getElementById(tableId);
        if (!tbody) return;
        if (!confirm('Are you sure you want to clear this table? This will remove all rows.')) return;
        tbody.innerHTML = '';
        if (typeof updateFrameCountsForPrefix === 'function') updateFrameCountsForPrefix(type);
    };

    window.removeRow = function (button, type) {
        const row = button ? button.closest('tr') : null;
        if (!row) return;
        const tbody = row.parentNode;
        if (!confirm('Are you sure you want to delete this row?')) return;
        row.remove();
        const tableId = tbody ? tbody.id : getTableIdByPrefix(type);
        if (typeof updateSerialNumbers === 'function') updateSerialNumbers(tableId);
        if (typeof updateMoveButtons === 'function') updateMoveButtons(tableId);
        if (typeof updateFrameCountsForPrefix === 'function') updateFrameCountsForPrefix(type);
    };

    window.removeBitwiseRow = function (btn) {
        const tbody = document.getElementById('bitwiseTableBody');
        if (!tbody || !btn) return;
        if (tbody.querySelectorAll('tr').length <= 1) {
            alert('At least one row is required.');
            return;
        }
        if (!confirm('Are you sure you want to delete this bitwise row?')) return;
        btn.closest('tr').remove();
        if (typeof updateBitwiseRowNumbers === 'function') updateBitwiseRowNumbers();
    };

    // window.submitFinalApiForm = function () {
    //     saveVisibleStep();
    //     let ok = true;
    //     if (typeof validateStep === 'function') ok = validateStep(4) === true;
    //     if (!ok) return false;
    //     if (typeof saveErrorConfiguration === 'function') saveErrorConfiguration();
    //     const form = document.getElementById('file-form') || document.querySelector('form');
    //     if (form) HTMLFormElement.prototype.submit.call(form);
    //     return true;
    // };

    // Dropdown/modal/table wheel behavior: scroll the inner area while cursor is inside it.
  
// modified funcitton to get conformation 

window.submitFinalApiForm = function () {
    saveVisibleStep();

    let ok = true;
    if (typeof validateStep === 'function') {
        ok = validateStep(4) === true;
    }

    if (!ok) {
        return false;
    }

    if (typeof saveErrorConfiguration === 'function') {
        saveErrorConfiguration();
    }

    const productInfo = {
        itemid: document.getElementById("api_itemid")?.value || "",
        brandname: document.getElementById("brandname")?.value || "N/A",
        productname: document.getElementById("productname")?.value || "N/A",
        modelname: document.getElementById("modelname")?.value || "N/A",
        date: document.querySelector("input[name='date']")?.value || "N/A",
        parameter_name: document.getElementById("error_parametername")?.value || document.getElementById("par_parametername")?.value || "N/A",
        page_number: document.getElementById("numberofpage")?.value || "1",
        sequence_number: document.getElementById("Sequence")?.value || "1",
        command_frame_count: document.getElementById("numberofpages")?.value || "1",
        response_frame_count: document.getElementById("page")?.value || "1",
        error_frame_count: document.getElementById("pages")?.value || "1",
        page_names: document.getElementById("pagenames")?.value || "",
        product_description: document.getElementById("productdescription")?.value || "N/A"
    };

    const masterData = {
        product: productInfo,
        commands: typeof storedCommands !== 'undefined' ? storedCommands : [],
        responses: typeof storedResponses !== 'undefined' ? storedResponses : [],
        errors: typeof storedErrors !== 'undefined' ? storedErrors : []
    };

    const formData = new FormData();
    Object.entries(productInfo).forEach(function (item) {
        formData.append(item[0], item[1]);
    });

    formData.append('config_id', document.getElementById('api_config_id')?.value || '');
    formData.append('full_config_json', JSON.stringify(masterData));

    fetch('<?php echo get_uri("items/saveapi"); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function (response) {
        return response.json();
    })
    .then(function (data) {
        if (data.success) {
            try {
                sessionStorage.removeItem('items_modal_api_draft_' + (document.getElementById('api_itemid')?.value || 'new') + '_' + (document.getElementById('api_config_id')?.value || 'draft'));
            } catch (e) {}

            if (typeof showApiResponsePopup === 'function') {
                showApiResponsePopup(data.print_r_output, {
                    message: data.message || 'API configuration saved successfully.',
                    configId: data.config_id || ''
                });
            }
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(function () {
        alert("API Connection Failed");
    });

    return false;
};
  
  
  
  
  
  
  
    document.addEventListener('wheel', function (e) {
        const scrollBox = e.target.closest('.table-responsive, .table-wrapper, .modal-box');
        if (!scrollBox) return;
        if (scrollBox.scrollHeight <= scrollBox.clientHeight && scrollBox.scrollWidth <= scrollBox.clientWidth) return;
        e.stopPropagation();
    }, {passive: true});

    document.addEventListener('DOMContentLoaded', function () {
        // Replace old progress listeners so tab switching first saves current table data.
        [1,2,3,4].forEach(function (num) {
            const oldBtn = document.getElementById('step-indicator-' + num);
            if (!oldBtn || !oldBtn.parentNode) return;
            const newBtn = oldBtn.cloneNode(true);
            oldBtn.parentNode.replaceChild(newBtn, oldBtn);
            newBtn.addEventListener('click', function () { window.showStep(num); });
        });

        if (typeof generateCommandProgressBoxes === 'function') generateCommandProgressBoxes();
        if (typeof generateResponseProgressBoxes === 'function') generateResponseProgressBoxes();
        if (typeof generateErrorProgressBoxes === 'function') generateErrorProgressBoxes();

        const maxCmd = parseInt(document.getElementById('numberofpages')?.value || '1', 10) || 1;
        const maxResp = parseInt(document.getElementById('page')?.value || '1', 10) || 1;
        const maxErr = parseInt(document.getElementById('pages')?.value || '1', 10) || 1;
        if (typeof updateCommandButtonLabels === 'function') updateCommandButtonLabels(maxCmd);
        if (typeof updateResponseButtonLabels === 'function') updateResponseButtonLabels(maxResp);
        if (typeof updateErrorButtonLabels === 'function') updateErrorButtonLabels(maxErr);
    });
})();
</script>



<script>
    // Navigation functions for frames
// function handleNextCommand() {
//     const maxFramesField = document.getElementById('numberofpages');
//     const maxFrames = parseInt(maxFramesField ? maxFramesField.value : 1) || 1;

//     if (!validateDuplicateByteRange("additionalTableBody", "cmd", "step2-error")) return;

//     saveCurrentCommandState(commandCount - 1);

//     if (commandCount >= maxFrames) {
//         alert("Success: You have completed all " + maxFrames + " command frames.");
//         if (typeof updateCommandBoxColors === "function") updateCommandBoxColors();
//         return;
//     }

//     commandCount++;
    
//     if (storedCommands[commandCount - 1]) {
//         applyCommandFrameData(storedCommands[commandCount - 1]);
//     } else {
//         resetCommandUI();
//     }

//     updateUIState(maxFrames);
// }


function updateUIState(maxFrames) {
    updateCommandFrameTitle();
    updateCommandButtonLabels(maxFrames);
    if (typeof updateCommandBoxColors === "function") updateCommandBoxColors();
    window.scrollTo(0, 0);
}

function updateCommandButtonLabels(limit) {
    const nextBtn = document.getElementById('next-command-btn');
    if (!nextBtn) return;

    if (commandCount === limit) {
        nextBtn.innerText = "Finalize Command Frames";
        nextBtn.style.backgroundColor = "#28a745";
    } else {
        nextBtn.innerText = "Next Command Frame";
        nextBtn.style.backgroundColor = "";
    }

    const backBtn = document.getElementById('back-command-btn');
    if (backBtn) {
        backBtn.style.display = (commandCount > 1) ? 'inline-flex' : 'none';
    }
}

function resetCommandUI() {
    const tbody = document.getElementById('additionalTableBody');
    if (document.getElementById('cmd_data_flow_direction')) document.getElementById('cmd_data_flow_direction').value = "";
    if (document.getElementById('cmd_framesizeinbytes')) document.getElementById('cmd_framesizeinbytes').value = "";
    if (tbody) tbody.innerHTML = "";
    if (typeof addRow === "function") addRow('cmd');
}



function updateResponseButtonLabels(limit) {
    const nextBtn = document.getElementById('next-response-page-btn');
    if (!nextBtn) return;

    if (responseCount === limit) {
        nextBtn.innerHTML = `<span>Finalize & Move to Error</span>`;
        nextBtn.style.backgroundColor = "#28a745";
    } else {
        nextBtn.innerHTML = `<span>Next</span><span class="arrow"> ›</span>`;
        nextBtn.style.backgroundColor = "";
    }
}

function updateResponseFrameTitle() {
    const titleDisplay = document.getElementById('response-title-display');
    const text = `RESPONSE FRAME (${responseCount})`;
    if (titleDisplay) titleDisplay.innerText = text;
}

// Error frame functions
// function handleNextError() {
//     const maxFramesField = document.getElementById('pages');
//     const maxFrames = parseInt(maxFramesField ? maxFramesField.value : 1) || 1;

//     if (!validateDuplicateByteRange("errorTableBody", "error", "step4-error")) return;

//     if (errorCount >= maxFrames) {
//         alert("Success: You have completed all " + maxFrames + " error frames.");
//         errorCompletedPages.add(errorCount);
//         if (typeof updateErrorBoxColors === "function") updateErrorBoxColors();
//         return;
//     }

//     saveCurrentErrorState();
//     errorCount++;

//     if (typeof updateErrorBoxColors === "function") updateErrorBoxColors();
//     updateErrorFrameTitle();

//     const tbody = document.getElementById('errorTableBody');

//     if (storedErrors[errorCount - 1]) {
//         applyErrorFrameData(storedErrors[errorCount - 1]);
//     } else {
//         if (document.getElementById('error_data_flow_direction')) document.getElementById('error_data_flow_direction').value = "";
//         if (document.getElementById('error_framesizeinbytes')) document.getElementById('error_framesizeinbytes').value = "";
//         if (tbody) tbody.innerHTML = '';
//         if (typeof addRow === "function") addRow('error');
//     }

//     updateErrorButtonLabels(maxFrames);
//     window.scrollTo(0, 0);
// }

// function handleBackError() {
//     if (errorCount > 1) {
//         saveCurrentErrorState();
//         errorCount--;
        
//         const prevSaved = storedErrors[errorCount - 1];
//         if (prevSaved) {
//             applyErrorFrameData(prevSaved);
//         }
        
//         updateErrorFrameTitle();
//         const maxFrames = parseInt(document.getElementById('pages')?.value || 1);
//         updateErrorButtonLabels(maxFrames);
//         if (typeof updateErrorBoxColors === "function") updateErrorBoxColors();
//         window.scrollTo(0, 0);
//     }
// }

function updateErrorButtonLabels(limit) {
    const nextBtn = document.getElementById('next-error-page-btn');
    if (!nextBtn) return;

    if (errorCount === limit) {
        nextBtn.innerText = "Finalize Error Frames";
        nextBtn.style.backgroundColor = "#28a745";
    } else {
        nextBtn.innerText = "Next Error Frame";
        nextBtn.style.backgroundColor = "";
    }

    const backBtn = document.getElementById('back-error-page-btn');
    if (backBtn) {
        backBtn.style.display = (errorCount > 1) ? 'inline-flex' : 'none';
    }
}

function updateErrorFrameTitle() {
    const titleDisplay = document.getElementById('error-title-display');
    const text = `ERROR FRAME (${errorCount})`;
    if (titleDisplay) titleDisplay.innerText = text;
}


// Add these to your DOMContentLoaded initialization
generateCommandProgressBoxes();
generateResponseProgressBoxes();
generateErrorProgressBoxes();

// Also add these event listeners for the total page inputs
document.getElementById('numberofpages').addEventListener('input', function() {
    commandCount = 1;
    generateCommandProgressBoxes();
    updateCommandFrameTitle();
    updateCommandButtonLabels(parseInt(this.value) || 1);
});

document.getElementById('page').addEventListener('input', function() {
    responseCount = 1;
    generateResponseProgressBoxes();
    updateResponseFrameTitle();
    updateResponseButtonLabels(parseInt(this.value) || 1);
});

document.getElementById('pages').addEventListener('input', function() {
    errorCount = 1;
    generateErrorProgressBoxes();
    updateErrorFrameTitle();
    updateErrorButtonLabels(parseInt(this.value) || 1);
});
</script>

<script>
/* Final frontend-only navigation, draft restore, and progress fixes. Backend route/field names stay unchanged. */
(function () {
    const finalizedSections = {cmd: false, resp: false, error: false};
    const draftKey = 'items_modal_api_draft_' + (document.getElementById('api_itemid')?.value || 'new') + '_' + (document.getElementById('api_config_id')?.value || 'draft');
    const nativeAlert = window.alert.bind(window);
    let lastAlertText = '';
    let lastAlertAt = 0;
    let isRestoringDraft = false;
    let isPersistingDraft = false;

    window.alert = function (message) {
        const text = String(message);
        const now = Date.now();
        if (text === lastAlertText && now - lastAlertAt < 1200) return;
        lastAlertText = text;
        lastAlertAt = now;
        nativeAlert(message);
    };

    function intValue(id, fallback) {
        const el = document.getElementById(id);
        const value = parseInt(el ? el.value : fallback, 10);
        return Number.isFinite(value) && value > 0 ? value : fallback;
    }

    function getFrameConfig(prefix) {
        if (prefix === 'cmd') {
            return {
                step: 2,
                totalId: 'numberofpages',
                tableId: 'additionalTableBody',
                errorId: 'step2-error',
                countName: 'commandCount',
                storeName: 'storedCommands',
                saveName: 'saveCurrentCommandState',
                applyName: 'applyCommandFrameData',
                titleName: 'updateCommandFrameTitle',
                labelName: 'updateCommandButtonLabels',
                colorName: 'updateCommandBoxColors',
                nextStep: 3
            };
        }
        if (prefix === 'resp') {
            return {
                step: 3,
                totalId: 'page',
                tableId: 'responseTableBody',
                errorId: 'step3-error',
                countName: 'responseCount',
                storeName: 'storedResponses',
                saveName: 'saveCurrentResponseState',
                applyName: 'applyResponseFrameData',
                titleName: 'updateResponseFrameTitle',
                labelName: 'updateResponseButtonLabels',
                colorName: 'updateResponseBoxColors',
                nextStep: 4
            };
        }
        return {
            step: 4,
            totalId: 'pages',
            tableId: 'errorTableBody',
            errorId: 'step4-error',
            countName: 'errorCount',
            storeName: 'storedErrors',
            saveName: 'saveCurrentErrorState',
            applyName: 'applyErrorFrameData',
            titleName: 'updateErrorFrameTitle',
            labelName: 'updateErrorButtonLabels',
            colorName: 'updateErrorBoxColors',
            nextStep: null
        };
    }

    function getCount(config) {
        if (config.countName === 'commandCount' && typeof commandCount !== 'undefined') return commandCount;
        if (config.countName === 'responseCount' && typeof responseCount !== 'undefined') return responseCount;
        if (config.countName === 'errorCount' && typeof errorCount !== 'undefined') return errorCount;
        return window[config.countName] || 1;
    }

    function setCount(config, value) {
        if (config.countName === 'commandCount' && typeof commandCount !== 'undefined') commandCount = value;
        if (config.countName === 'responseCount' && typeof responseCount !== 'undefined') responseCount = value;
        if (config.countName === 'errorCount' && typeof errorCount !== 'undefined') errorCount = value;
        window[config.countName] = value;
    }

    function getStore(config) {
        if (config.storeName === 'storedCommands' && typeof storedCommands !== 'undefined') return storedCommands;
        if (config.storeName === 'storedResponses' && typeof storedResponses !== 'undefined') return storedResponses;
        if (config.storeName === 'storedErrors' && typeof storedErrors !== 'undefined') return storedErrors;
        return window[config.storeName] || [];
    }

    function syncDefaultNames() {
        const name = (document.getElementById('par_parametername')?.value || '').trim();
        if (!name) return;

        ['com_parametername', 'resp_parametername', 'error_parametername'].forEach(function (id) {
            const input = document.getElementById(id);
            if (input) {
                input.value = name;
                input.readOnly = true;
                input.setAttribute('aria-disabled', 'true');
            }
        });

        if (typeof updateDefaultValueForAllDataRows === 'function') {
            updateDefaultValueForAllDataRows(name);
        }
    }

    function showSavedAndMovingMessage(nextLabel) {
        const label = nextLabel ? ' Moving to ' + nextLabel + '.' : '';
        showSuccessMessage('Information saved.' + label);
    }

    function getStepLabel(step) {
        const labels = {1: 'Product', 2: 'Command', 3: 'Response', 4: 'Error'};
        return labels[step] || 'next step';
    }

    function setPlainValue(id, value) {
        const el = document.getElementById(id);
        if (el && value !== undefined && value !== null) el.value = value;
    }

    function collectProductDraft() {
        return {
            parametername: document.getElementById('par_parametername')?.value || '',
            productname: document.getElementById('productname')?.value || '',
            brandname: document.getElementById('brandname')?.value || '',
            modelname: document.getElementById('modelname')?.value || '',
            date: document.querySelector('input[name="date"]')?.value || '',
            numberofpage: document.getElementById('numberofpage')?.value || '',
            Sequence: document.getElementById('Sequence')?.value || '',
            numberofpages: document.getElementById('numberofpages')?.value || '1',
            page: document.getElementById('page')?.value || '1',
            pages: document.getElementById('pages')?.value || '1',
            pagenames: document.getElementById('pagenames')?.value || '',
            productdescription: document.getElementById('productdescription')?.value || ''
        };
    }

    function saveVisibleFrameState() {
        const active = document.querySelector('.form-step.active') || Array.from(document.querySelectorAll('.form-step')).find(function (el) { return el.style.display === 'block'; });
        if (!active) return;
        if (active.id === 'step-2') saveFrame('cmd');
        if (active.id === 'step-3') saveFrame('resp');
        if (active.id === 'step-4') saveFrame('error');
    }

    function persistDraft() {
        if (isRestoringDraft || isPersistingDraft) return;
        try {
            isPersistingDraft = true;
            saveVisibleFrameState();
            const payload = {
                product: collectProductDraft(),
                currentStep: typeof currentStep !== 'undefined' ? currentStep : 1,
                commandCount: typeof commandCount !== 'undefined' ? commandCount : 1,
                responseCount: typeof responseCount !== 'undefined' ? responseCount : 1,
                errorCount: typeof errorCount !== 'undefined' ? errorCount : 1,
                finalizedSections: finalizedSections,
                commandPageTouched: typeof commandPageTouched !== 'undefined' ? commandPageTouched : [],
                responsePageTouched: typeof responsePageTouched !== 'undefined' ? responsePageTouched : [],
                errorPageTouched: typeof errorPageTouched !== 'undefined' ? errorPageTouched : [],
                commands: typeof storedCommands !== 'undefined' ? storedCommands : [],
                responses: typeof storedResponses !== 'undefined' ? storedResponses : [],
                errors: typeof storedErrors !== 'undefined' ? storedErrors : []
            };
            sessionStorage.setItem(draftKey, JSON.stringify(payload));
        } catch (e) {
            console.warn('Unable to persist API draft', e);
        } finally {
            isPersistingDraft = false;
        }
    }

    function restoreDraft() {
        if (window.API_EDIT_CONFIG) return false;
        let draft = null;
        try {
            draft = JSON.parse(sessionStorage.getItem(draftKey) || 'null');
        } catch (e) {
            draft = null;
        }
        if (!draft || !draft.product) return false;

        isRestoringDraft = true;

        const product = draft.product;
        setPlainValue('par_parametername', product.parametername);
        setPlainValue('productname', product.productname);
        setPlainValue('brandname', product.brandname);
        setPlainValue('modelname', product.modelname);
        const dateInput = document.querySelector('input[name="date"]');
        if (dateInput && product.date) dateInput.value = product.date;
        setPlainValue('numberofpage', product.numberofpage);
        setPlainValue('Sequence', product.Sequence);
        setPlainValue('numberofpages', product.numberofpages || '1');
        setPlainValue('page', product.page || '1');
        setPlainValue('pages', product.pages || '1');
        setPlainValue('pagenames', product.pagenames);
        setPlainValue('productdescription', product.productdescription);

        if (typeof storedCommands !== 'undefined') {
            storedCommands.length = 0;
            (draft.commands || []).forEach(function (frame, index) { storedCommands[index] = frame; });
        }
        if (typeof storedResponses !== 'undefined') {
            storedResponses.length = 0;
            (draft.responses || []).forEach(function (frame, index) { storedResponses[index] = frame; });
        }
        if (typeof storedErrors !== 'undefined') {
            storedErrors.length = 0;
            (draft.errors || []).forEach(function (frame, index) { storedErrors[index] = frame; });
        }
        if (typeof commandPageTouched !== 'undefined') {
            commandPageTouched.length = 0;
            (draft.commandPageTouched || []).forEach(function (value, index) { commandPageTouched[index] = value; });
        }
        if (typeof responsePageTouched !== 'undefined') {
            responsePageTouched.length = 0;
            (draft.responsePageTouched || []).forEach(function (value, index) { responsePageTouched[index] = value; });
        }
        if (typeof errorPageTouched !== 'undefined') {
            errorPageTouched.length = 0;
            (draft.errorPageTouched || []).forEach(function (value, index) { errorPageTouched[index] = value; });
        }

        if (typeof commandCount !== 'undefined') commandCount = parseInt(draft.commandCount || 1, 10) || 1;
        if (typeof responseCount !== 'undefined') responseCount = parseInt(draft.responseCount || 1, 10) || 1;
        if (typeof errorCount !== 'undefined') errorCount = parseInt(draft.errorCount || 1, 10) || 1;
        Object.assign(finalizedSections, draft.finalizedSections || {});

        if (typeof generateCommandProgressBoxes === 'function') generateCommandProgressBoxes();
        if (typeof generateResponseProgressBoxes === 'function') generateResponseProgressBoxes();
        if (typeof generateErrorProgressBoxes === 'function') generateErrorProgressBoxes();

        if (typeof storedCommands !== 'undefined' && storedCommands[commandCount - 1] && typeof applyCommandFrameData === 'function') applyCommandFrameData(storedCommands[commandCount - 1]);
        if (typeof storedResponses !== 'undefined' && storedResponses[responseCount - 1] && typeof applyResponseFrameData === 'function') applyResponseFrameData(storedResponses[responseCount - 1]);
        if (typeof storedErrors !== 'undefined' && storedErrors[errorCount - 1] && typeof applyErrorFrameData === 'function') applyErrorFrameData(storedErrors[errorCount - 1]);

        syncDefaultNames();
        if (typeof window.showStep === 'function') window.showStep(parseInt(draft.currentStep || 1, 10) || 1);
        ['cmd', 'resp', 'error'].forEach(paintBoxes);

        isRestoringDraft = false;
        return true;
    }

    function resetFrame(prefix) {
        const tableId = prefix === 'cmd' ? 'additionalTableBody' : (prefix === 'resp' ? 'responseTableBody' : 'errorTableBody');
        const directionId = prefix === 'cmd' ? 'cmd_data_flow_direction' : (prefix === 'resp' ? 'resp_data_flow_direction' : 'error_data_flow_direction');
        const sizeId = prefix === 'cmd' ? 'cmd_framesizeinbytes' : (prefix === 'resp' ? 'resp_framesizeinbytes' : 'error_framesizeinbytes');

        if (prefix === 'cmd' && typeof resetCommandUI === 'function') {
            resetCommandUI();
            syncDefaultNames();
            return;
        }

        const direction = document.getElementById(directionId);
        const size = document.getElementById(sizeId);
        const tbody = document.getElementById(tableId);
        if (direction) direction.value = '';
        if (size) size.value = '';
        if (tbody) tbody.innerHTML = '';
        if (typeof addRow === 'function') addRow(prefix);
        syncDefaultNames();
    }

    function loadFrame(prefix, index) {
        const config = getFrameConfig(prefix);
        const saved = getStore(config)[index - 1];
        if (saved && typeof window[config.applyName] === 'function') {
            window[config.applyName](saved);
        } else {
            resetFrame(prefix);
        }
        if (typeof window[config.titleName] === 'function') window[config.titleName]();
        if (typeof window[config.labelName] === 'function') window[config.labelName](intValue(config.totalId, 1));
        if (typeof window[config.colorName] === 'function') window[config.colorName]();
        syncDefaultNames();
        persistDraft();
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    function hydrateStepFrame(step) {
        if (step === 2) loadFrame('cmd', getCount(getFrameConfig('cmd')));
        if (step === 3) loadFrame('resp', getCount(getFrameConfig('resp')));
        if (step === 4) loadFrame('error', getCount(getFrameConfig('error')));
    }

    const baseShowStep = window.showStep;
    window.showStep = function (step) {
        if (!isRestoringDraft) saveVisibleFrameState();

        if (typeof baseShowStep === 'function') {
            baseShowStep(step);
        } else {
            document.querySelectorAll('.form-step').forEach(function (el) {
                el.classList.remove('active');
                el.style.display = 'none';
            });
            const target = document.getElementById('step-' + step);
            if (target) {
                target.classList.add('active');
                target.style.display = 'block';
            }
            if (typeof currentStep !== 'undefined') currentStep = step;
            if (typeof updateProgressIndicator === 'function') updateProgressIndicator();
        }

        hydrateStepFrame(step);
        syncDefaultNames();
        persistDraft();
        return true;
    };

    function saveFrame(prefix) {
        const config = getFrameConfig(prefix);
        if (typeof window[config.saveName] === 'function') {
            window[config.saveName](getCount(config) - 1);
        }
        if (prefix === 'cmd') commandPageTouched[getCount(config) - 1] = true;
        if (prefix === 'resp') responsePageTouched[getCount(config) - 1] = true;
        if (prefix === 'error') errorPageTouched[getCount(config) - 1] = true;
        persistDraft();
    }

    function validateFrameOnly(prefix) {
        if (prefix === 'cmd' && typeof validateStep2 === 'function') return validateStep2() === true;
        if (prefix === 'resp' && typeof validateStep3 === 'function') return validateStep3() === true;
        if (prefix === 'error' && typeof validateStep4 === 'function') return validateStep4() === true;
        return true;
    }

    function showSuccessMessage(message) {
        let box = document.getElementById('customAlert');
        if (!box) {
            box = document.createElement('div');
            box.id = 'customAlert';
            document.body.appendChild(box);
        }

        box.innerText = message;
        box.style.cssText = `
            position: fixed;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #F0FDF4;
            color: #166534;
            border: 1px solid #86EFAC;
            padding: 18px 34px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(22,101,52,0.18);
            z-index: 999999;
            font-family: sans-serif;
            font-size: 16px;
            font-weight: 800;
            text-align: center;
        `;
        box.style.display = 'block';

        setTimeout(function () {
            if (box) box.style.display = 'none';
        }, 2200);
    }

    function goFrameNext(prefix) {
        const config = getFrameConfig(prefix);
        const total = intValue(config.totalId, 1);
        const current = Math.min(getCount(config), total);

        if (typeof validateDuplicateByteRange === 'function' && !validateDuplicateByteRange(config.tableId, prefix, config.errorId)) {
            return false;
        }

        if (current < total) {
            finalizedSections[prefix] = false;
            saveFrame(prefix);
            setCount(config, current + 1);
            loadFrame(prefix, current + 1);
            showSavedAndMovingMessage((prefix === 'cmd' ? 'Command Frame ' : (prefix === 'resp' ? 'Response Frame ' : 'Error Frame ')) + (current + 1));
            return true;
        }

        if (!validateFrameOnly(prefix)) return false;
        saveFrame(prefix);
        finalizedSections[prefix] = true;

        if (typeof window[config.colorName] === 'function') window[config.colorName]();
        labelButton(prefix);
        if (prefix === 'error') {
            updateFinalSubmitVisibility();
            showSuccessMessage('Saved. You can submit now.');
        } else {
            showSavedAndMovingMessage(getStepLabel(config.nextStep));
        }
        persistDraft();
        return true;
    }

    function goFrameBack(prefix) {
        const config = getFrameConfig(prefix);
        const current = getCount(config);
        if (current <= 1) return false;
        saveFrame(prefix);
        setCount(config, current - 1);
        loadFrame(prefix, current - 1);
        showSavedAndMovingMessage((prefix === 'cmd' ? 'Command Frame ' : (prefix === 'resp' ? 'Response Frame ' : 'Error Frame ')) + (current - 1));
        return true;
    }

    function labelButton(prefix) {
        const config = getFrameConfig(prefix);
        const total = intValue(config.totalId, 1);
        const current = Math.min(getCount(config), total);
        const labels = {
            cmd: ['Next Command Frame', 'Finalize'],
            resp: ['Next Response Frame', 'Finalize'],
            error: ['Next Error Frame', 'Finalize']
        };
        const nextId = prefix === 'cmd' ? 'next-command-btn' : (prefix === 'resp' ? 'next-response-page-btn' : 'next-error-page-btn');
        const backId = prefix === 'cmd' ? 'back-command-btn' : (prefix === 'resp' ? 'back-response-page-btn' : 'back-error-page-btn');
        const nextBtn = document.getElementById(nextId);
        const backBtn = document.getElementById(backId);
        const actionWrap = nextBtn ? nextBtn.closest('.frame-mini-actions') : null;
        if (actionWrap) {
            actionWrap.querySelectorAll('.frame-current-label').forEach(function (label) {
                label.remove();
            });
        }
        if (nextBtn) {
            nextBtn.style.display = 'inline-flex';
            nextBtn.innerText = current === total ? labels[prefix][1] : labels[prefix][0];
            nextBtn.style.backgroundColor = current === total ? '#15803d' : '';
        }
        if (backBtn) {
            backBtn.style.display = 'inline-flex';
            backBtn.disabled = current <= 1;
            backBtn.style.opacity = current <= 1 ? '.55' : '1';
            backBtn.style.cursor = current <= 1 ? 'not-allowed' : 'pointer';
        }
        updateFinalSubmitVisibility();
    }

    function updateFinalSubmitVisibility() {
        const submitBtn = document.getElementById('final-submit-btn');
        if (!submitBtn) return;

        const currentErrorPage = getCount(getFrameConfig('error'));
        const totalErrorPages = intValue('pages', 1);
        const canSubmit = finalizedSections.error && currentErrorPage >= totalErrorPages;
        submitBtn.style.display = canSubmit ? 'inline-flex' : 'none';
    }

    function paintBoxes(prefix) {
        const config = getFrameConfig(prefix);
        const trackerId = prefix === 'cmd' ? 'command-progress-tracker' : (prefix === 'resp' ? 'response-progress-tracker' : 'error-progress-tracker');
        const tracker = document.getElementById(trackerId);
        if (!tracker) return;

        const current = getCount(config);
        Array.from(tracker.querySelectorAll('.step-box:not(.finalize-box)')).forEach(function (box, index) {
            const page = index + 1;
            const saved = !!(getStore(config)[index]);
            const touched = prefix === 'cmd' ? commandPageTouched[index] : (prefix === 'resp' ? responsePageTouched[index] : errorPageTouched[index]);
            const completed = saved || touched;

            box.title = page === current ? 'Current frame page' : 'Frame page ' + page;
            box.style.borderColor = page === current ? '#2563eb' : '#cbd5e1';
            box.style.borderWidth = page === current ? '2px' : '1px';
            box.style.boxShadow = page === current ? '0 0 0 3px rgba(37,99,235,.18)' : 'none';
            box.style.transform = page === current ? 'translateY(-1px)' : 'none';

            if (page === current) {
                box.style.backgroundColor = '#dbeafe';
                box.style.color = '#1d4ed8';
                box.style.borderColor = '#2563eb';
            } else if (completed) {
                box.style.backgroundColor = '#16a34a';
                box.style.color = '#fff';
                box.style.borderColor = '#15803d';
            } else {
                box.style.backgroundColor = '#fff';
                box.style.color = '#334155';
            }
        });

        tracker.querySelectorAll('.finalize-box').forEach(function (box) { box.remove(); });

        labelButton(prefix);
    }

    function bindFrameBoxes(prefix) {
        const trackerId = prefix === 'cmd' ? 'command-progress-tracker' : (prefix === 'resp' ? 'response-progress-tracker' : 'error-progress-tracker');
        const tracker = document.getElementById(trackerId);
        if (!tracker) return;

        Array.from(tracker.querySelectorAll('.step-box:not(.finalize-box)')).forEach(function (box, index) {
            const cleanBox = box.cloneNode(true);
            box.parentNode.replaceChild(cleanBox, box);
            cleanBox.addEventListener('click', function () {
                const config = getFrameConfig(prefix);
                const page = index + 1;
                saveFrame(prefix);
                setCount(config, page);
                loadFrame(prefix, page);
                paintBoxes(prefix);
            });
        });
    }

    function appendFinalizeBox(prefix) {
        const trackerId = prefix === 'cmd' ? 'command-progress-tracker' : (prefix === 'resp' ? 'response-progress-tracker' : 'error-progress-tracker');
        const tracker = document.getElementById(trackerId);
        if (!tracker) return;
        tracker.querySelectorAll('.finalize-box').forEach(function (box) { box.remove(); });
    }

    function wrapProgressGenerator(name, prefix) {
        const original = window[name];
        if (typeof original !== 'function') return;
        window[name] = function () {
            original.apply(this, arguments);
            appendFinalizeBox(prefix);
            bindFrameBoxes(prefix);
            paintBoxes(prefix);
        };
    }

    ['Command', 'Response', 'Error'].forEach(function (name) {
        const prefix = name === 'Command' ? 'cmd' : (name === 'Response' ? 'resp' : 'error');
        const colorName = 'update' + name + 'BoxColors';
        const labelName = 'update' + name + 'ButtonLabels';
        const originalColor = window[colorName];
        window[colorName] = function () {
            if (typeof originalColor === 'function') originalColor.apply(this, arguments);
            paintBoxes(prefix);
        };
        window[labelName] = function () {
            labelButton(prefix);
        };
    });

    wrapProgressGenerator('generateCommandProgressBoxes', 'cmd');
    wrapProgressGenerator('generateResponseProgressBoxes', 'resp');
    wrapProgressGenerator('generateErrorProgressBoxes', 'error');

    window.handleNextCommand = function () { return goFrameNext('cmd'); };
    window.handleBackCommand = function () { return goFrameBack('cmd'); };
    window.handleNextResponse = function () { return goFrameNext('resp'); };
    window.handleBackResponse = function () { return goFrameBack('resp'); };
    window.handleNextError = function () { return goFrameNext('error'); };
    window.handleBackError = function () { return goFrameBack('error'); };

    const originalGoToNext = window.goToNextStep;
    window.goToNextStep = function (step) {
        syncDefaultNames();
        if (step === 1) {
            if (typeof validateStep === 'function' && validateStep(1) !== true) return false;
            if (typeof generateCommandProgressBoxes === 'function') generateCommandProgressBoxes();
            if (typeof generateResponseProgressBoxes === 'function') generateResponseProgressBoxes();
            if (typeof generateErrorProgressBoxes === 'function') generateErrorProgressBoxes();
            window.showStep(2);
            syncDefaultNames();
            persistDraft();
            showSavedAndMovingMessage('Command');
            return true;
        }
        if (step === 2 && getCount(getFrameConfig('cmd')) < intValue('numberofpages', 1)) {
            if (typeof forceMessage === 'function') forceMessage('Please complete all Command frames using Next Command Frame.');
            return false;
        }
        if (step === 3 && getCount(getFrameConfig('resp')) < intValue('page', 1)) {
            if (typeof forceMessage === 'function') forceMessage('Please complete all Response frames using Next Response Frame.');
            return false;
        }
        if (step === 4 && getCount(getFrameConfig('error')) < intValue('pages', 1)) {
            if (typeof forceMessage === 'function') forceMessage('Please complete all Error frames using Next Error Frame.');
            return false;
        }
        const prefix = step === 2 ? 'cmd' : (step === 3 ? 'resp' : 'error');
        if (step >= 2 && step <= 4 && !finalizedSections[prefix]) {
            if (!goFrameNext(prefix) || !finalizedSections[prefix]) return false;
        }
        if (!validateFrameOnly(prefix)) return false;
        saveFrame(prefix);
        if (step < 4) {
            window.showStep(step + 1);
            showSavedAndMovingMessage(getStepLabel(step + 1));
            return true;
        }
        if (typeof originalGoToNext === 'function') return originalGoToNext(step);
        return true;
    };

    const originalSubmitFinalApiForm = window.submitFinalApiForm;
    window.submitFinalApiForm = function () {
        if (!finalizedSections.error) {
            if (typeof forceMessage === 'function') forceMessage('Click Finalize before submitting.');
            return false;
        }
        if (typeof originalSubmitFinalApiForm === 'function') {
            return originalSubmitFinalApiForm.apply(this, arguments);
        }
        return false;
    };

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#step-2 .save-inline-btn, #step-3 .save-inline-btn, #step-4 .save-inline-btn').forEach(function (btn) {
            btn.style.display = 'none';
        });
        ['com_parametername', 'resp_parametername', 'error_parametername'].forEach(function (id) {
            const input = document.getElementById(id);
            if (input) {
                input.readOnly = true;
                input.setAttribute('aria-disabled', 'true');
            }
        });

        syncDefaultNames();
        if (typeof generateCommandProgressBoxes === 'function') generateCommandProgressBoxes();
        if (typeof generateResponseProgressBoxes === 'function') generateResponseProgressBoxes();
        if (typeof generateErrorProgressBoxes === 'function') generateErrorProgressBoxes();
        restoreDraft();
        ['cmd', 'resp', 'error'].forEach(paintBoxes);
    });

    document.addEventListener('input', function (event) {
        if (event.target && event.target.id === 'par_parametername') syncDefaultNames();
        if (event.target.closest('#step-2')) finalizedSections.cmd = false;
        if (event.target.closest('#step-3')) finalizedSections.resp = false;
        if (event.target.closest('#step-4')) {
            finalizedSections.error = false;
            updateFinalSubmitVisibility();
        }
        persistDraft();
    });

    document.addEventListener('change', function (event) {
        if (event.target.closest('#step-2')) finalizedSections.cmd = false;
        if (event.target.closest('#step-3')) finalizedSections.resp = false;
        if (event.target.closest('#step-4')) {
            finalizedSections.error = false;
            updateFinalSubmitVisibility();
        }
        persistDraft();
    });

    window.addEventListener('beforeunload', persistDraft);
    window.addEventListener('pageshow', function () {
        restoreDraft();
        syncDefaultNames();
    });

})();
</script>

<script>
(function () {
    const enhancedSelects = new WeakSet();
    const valueDescriptor = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'value');
    const selectedIndexDescriptor = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'selectedIndex');
    let activeSmartWrap = null;

    const fieldDefaults = {
        direction: {icon: 'fa-route', hint: 'Data path'},
        frameType: {icon: 'fa-layer-group', hint: 'Frame category'},
        frameTrigger: {icon: 'fa-bolt', hint: 'Trigger action'},
        boolean: {icon: 'fa-toggle-on', hint: 'True or false'},
        fieldDescription: {icon: 'fa-tags', hint: 'Frame segment'},
        bitByte: {icon: 'fa-table-cells', hint: 'Byte or bit mode'},
        dataType: {icon: 'fa-code', hint: 'Stored value type'},
        valueDefinition: {icon: 'fa-diagram-project', hint: 'Value rule'},
        uiType: {icon: 'fa-window-restore', hint: 'Interface control'},
        uiInOut: {icon: 'fa-right-left', hint: 'UI data mode'},
        notification: {icon: 'fa-bell', hint: 'Notification'},
        generic: {icon: 'fa-list-ul', hint: 'Select option'}
    };

    const iconMap = {
        direction: {
            '': ['fa-route', 'Choose data path'],
            'app/server to device': ['fa-cloud-arrow-down', 'Server command to device'],
            'device to app/server': ['fa-tower-broadcast', 'Device message to server']
        },
        frameType: {
            'command': ['fa-terminal', 'Command frame'],
            'response': ['fa-reply', 'Response frame'],
            'error': ['fa-triangle-exclamation', 'Error frame']
        },
        frameTrigger: {
            'none': ['fa-ban', 'No trigger'],
            'text': ['fa-align-left', 'Text trigger'],
            'incoming frame': ['fa-inbox', 'Incoming frame trigger'],
            'toggle switch': ['fa-toggle-on', 'Switch trigger'],
            'notification': ['fa-bell', 'Notification trigger'],
            'console': ['fa-terminal', 'Console trigger'],
            'enter text': ['fa-keyboard', 'Text input trigger'],
            'slider': ['fa-sliders', 'Slider trigger'],
            'brightness slider': ['fa-sun', 'Brightness trigger'],
            'airflow slider': ['fa-wind', 'Airflow trigger'],
            'cct slider': ['fa-temperature-half', 'CCT trigger'],
            'saturation slider': ['fa-palette', 'Saturation trigger'],
            'hue slider': ['fa-droplet', 'Hue trigger'],
            'hue slider custom': ['fa-eye-dropper', 'Custom hue trigger'],
            'hue circle': ['fa-circle-notch', 'Hue circle trigger'],
            'push button': ['fa-hand-pointer', 'Push button trigger'],
            'drop down': ['fa-square-caret-down', 'Dropdown trigger'],
            'trigger': ['fa-bolt-lightning', 'Manual trigger'],
            'display': ['fa-desktop', 'Display trigger'],
            'button': ['fa-circle-dot', 'Button trigger'],
            'auto trigger with timer': ['fa-stopwatch', 'Timed trigger'],
            'toast display': ['fa-message', 'Toast trigger']
        },
        boolean: {
            'true': ['fa-circle-check', 'Enabled'],
            'false': ['fa-circle-xmark', 'Disabled']
        },
        fieldDescription: {
            '': ['fa-tags', 'Choose segment'],
            'header': ['fa-heading', 'Frame header'],
            'data': ['fa-database', 'Data bytes'],
            'payload': ['fa-box-open', 'Payload block'],
            'checksum': ['fa-shield-halved', 'Integrity check'],
            'terminator': ['fa-flag-checkered', 'Frame ending'],
            'command id': ['fa-fingerprint', 'Command identity'],
            'status': ['fa-signal', 'Status value'],
            'error code': ['fa-circle-exclamation', 'Error value'],
            'length': ['fa-ruler-horizontal', 'Length byte'],
            'version': ['fa-code-branch', 'Version marker'],
            'reserved': ['fa-lock', 'Reserved byte'],
            'timestamp': ['fa-clock', 'Time value'],
            'device id': ['fa-microchip', 'Device identity']
        },
        bitByte: {
            'bytewise': ['fa-table-cells', 'Byte-level config'],
            'bitwise': ['fa-microchip', 'Bit-level config']
        },
        dataType: {
            '': ['fa-code', 'Choose data type'],
            'unsignedint': ['fa-hashtag', 'Positive integer'],
            'unsignedinteger': ['fa-hashtag', 'Positive integer'],
            'signedint': ['fa-plus-minus', 'Signed integer'],
            'signedinteger': ['fa-plus-minus', 'Signed integer'],
            'float': ['fa-percent', 'Floating value'],
            'double': ['fa-superscript', 'Double precision'],
            'binary': ['fa-barcode', 'Binary data'],
            'ascii': ['fa-font', 'ASCII text'],
            'string': ['fa-quote-right', 'String data'],
            'boolean': ['fa-toggle-off', 'Boolean flag'],
            'hex': ['fa-fingerprint', 'Hex value'],
            'enum': ['fa-list-ol', 'Enum values'],
            'timestamp': ['fa-stopwatch', 'Timestamp'],
            'none': ['fa-ban', 'No type']
        },
        valueDefinition: {
            '': ['fa-diagram-project', 'Choose value rule'],
            'fixed value': ['fa-thumbtack', 'Fixed value'],
            'fixed': ['fa-thumbtack', 'Fixed value'],
            'range value': ['fa-sliders', 'Range value'],
            'range': ['fa-sliders', 'Range value'],
            'dynamic value': ['fa-bolt', 'Dynamic value'],
            'enum value': ['fa-list-check', 'Enum mapping'],
            'calculated value': ['fa-calculator', 'Calculated value'],
            'none': ['fa-ban', 'No definition']
        },
        uiType: {
            '': ['fa-window-restore', 'Choose UI control'],
            'none': ['fa-ban', 'No UI'],
            'text': ['fa-align-left', 'Text display'],
            'toggle switch': ['fa-toggle-on', 'Switch control'],
            'notification': ['fa-bell', 'Notification UI'],
            'console': ['fa-terminal', 'Console output'],
            'enter text': ['fa-keyboard', 'Text input'],
            'entertext': ['fa-keyboard', 'Text input'],
            'slider': ['fa-sliders', 'Slider control'],
            'brightness slider': ['fa-sun', 'Brightness control'],
            'airflow slider': ['fa-wind', 'Airflow control'],
            'cct slider': ['fa-temperature-half', 'CCT control'],
            'saturation slider': ['fa-palette', 'Saturation control'],
            'hue slider': ['fa-droplet', 'Hue control'],
            'hue slider custom': ['fa-eye-dropper', 'Custom hue control'],
            'hue circle': ['fa-circle-notch', 'Color wheel'],
            'push button': ['fa-hand-pointer', 'Push action'],
            'button': ['fa-circle-dot', 'Button action'],
            'button big': ['fa-circle-dot', 'Large button'],
            'drop down': ['fa-square-caret-down', 'Dropdown control'],
            'dropdown': ['fa-square-caret-down', 'Dropdown control'],
            'trigger': ['fa-bolt-lightning', 'Trigger action'],
            'display': ['fa-desktop', 'Display output'],
            'auto trigger with timer': ['fa-stopwatch', 'Timed trigger'],
            'toast display': ['fa-message', 'Toast message'],
            'checkbox': ['fa-square-check', 'Checkbox'],
            'power button': ['fa-power-off', 'Power action'],
            'text trigger': ['fa-i-cursor', 'Text trigger'],
            'status led': ['fa-lightbulb', 'Status LED'],
            'lock icon': ['fa-lock', 'Lock indicator'],
            'alaram icon': ['fa-bell', 'Alarm indicator'],
            'alarm icon': ['fa-bell', 'Alarm indicator'],
            'eye icon': ['fa-eye', 'Visibility indicator'],
            'network icon': ['fa-wifi', 'Network indicator']
        },
        uiInOut: {
            '': ['fa-right-left', 'Choose flow mode'],
            'none': ['fa-ban', 'No UI direction'],
            'read': ['fa-book-open', 'Read from device'],
            'write': ['fa-pen-to-square', 'Write to device'],
            'read/write': ['fa-right-left', 'Read and write']
        },
        notification: {
            'yes': ['fa-bell', 'Notification on'],
            'no': ['fa-bell-slash', 'Notification off']
        }
    };

    function normalize(value) {
        return String(value || '').trim().toLowerCase();
    }

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, function (char) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[char];
        });
    }

    function getFieldKey(select) {
        const signal = normalize([
            select.name || '',
            select.id || '',
            select.className || ''
        ].join(' '));

        if (signal.includes('data_flow_direction')) return 'direction';
        if (signal.includes('frametype')) return 'frameType';
        if (signal.includes('frametrigger') || signal.includes('frame_trigger')) return 'frameTrigger';
        if (signal.includes('multipleui') || signal.includes('triggerui')) return 'boolean';
        if (signal.includes('fielddescription') || signal.includes('field-description')) return 'fieldDescription';
        if (signal.includes('bitbyte')) return 'bitByte';
        if (signal.includes('datatype')) return 'dataType';
        if (signal.includes('valuedefinition')) return 'valueDefinition';
        if (signal.includes('uitype')) return 'uiType';
        if (signal.includes('uiinout') || signal.includes('ui-inout')) return 'uiInOut';
        if (signal.includes('notif-enable')) return 'notification';

        const optionSignal = normalize(Array.from(select.options || []).map(function (option) {
            return [option.value, option.textContent].join(' ');
        }).join(' '));

        if (optionSignal.includes('unsignedinteger') || optionSignal.includes('signedinteger') || optionSignal.includes('boolean') || optionSignal.includes('float')) return 'dataType';
        if (optionSignal.includes('fixed value') || optionSignal.includes('range value') || optionSignal.includes('dynamic value') || optionSignal.includes('calculated value')) return 'valueDefinition';
        if (optionSignal.includes('read/write') || (optionSignal.includes('read') && optionSignal.includes('write'))) return 'uiInOut';
        if (optionSignal.includes('toggle switch') || optionSignal.includes('power button') || optionSignal.includes('status led')) return 'uiType';
        if (optionSignal.includes('yes') && optionSignal.includes('no')) return 'notification';

        return 'generic';
    }

    function getOptionText(option) {
        return (option && (option.textContent || option.value) || 'Select').trim();
    }

    function getOptionMeta(select, option) {
        const key = getFieldKey(select);
        const optionText = getOptionText(option);
        const optionValue = option ? option.value : '';
        const mapped = (iconMap[key] && (iconMap[key][normalize(optionValue)] || iconMap[key][normalize(optionText)])) || null;
        const fallback = fieldDefaults[key] || fieldDefaults.generic;

        return {
            icon: mapped ? mapped[0] : fallback.icon,
            hint: mapped ? mapped[1] : fallback.hint,
            title: optionText || 'Select'
        };
    }

    function optionMarkup(meta) {
        return `
            <span class="api-select-icon"><i class="fa-solid ${meta.icon}" aria-hidden="true"></i></span>
            <span class="api-select-copy">
                <span class="api-select-title">${escapeHtml(meta.title)}</span>
                <span class="api-select-hint">${escapeHtml(meta.hint)}</span>
            </span>
        `;
    }

    function positionFloatingMenu(wrap) {
        if (!wrap || !wrap.classList.contains('open')) return;
        const trigger = wrap.querySelector('.api-select-trigger');
        const menu = wrap.querySelector('.api-select-menu');
        if (!trigger || !menu) return;

        const rect = trigger.getBoundingClientRect();
        const viewportGap = 10;
        const menuWidth = Math.max(rect.width, Math.min(280, window.innerWidth - viewportGap * 2));
        const left = Math.min(Math.max(rect.left, viewportGap), window.innerWidth - menuWidth - viewportGap);
        const spaceBelow = window.innerHeight - rect.bottom - viewportGap;
        const spaceAbove = rect.top - viewportGap;
        const maxHeight = Math.max(170, Math.min(320, Math.max(spaceBelow, spaceAbove) - 8));
        const openUp = spaceBelow < 180 && spaceAbove > spaceBelow;

        menu.classList.add('is-floating');
        menu.style.width = menuWidth + 'px';
        menu.style.maxHeight = maxHeight + 'px';
        menu.style.left = left + 'px';
        menu.style.top = openUp ? 'auto' : (rect.bottom + 8) + 'px';
        menu.style.bottom = openUp ? (window.innerHeight - rect.top + 8) + 'px' : 'auto';
    }

    function closeSmartWrap(wrap) {
        if (!wrap) return;
        wrap.classList.remove('open');
        const trigger = wrap.querySelector('[aria-expanded="true"]');
        const menu = wrap.querySelector('.api-select-menu');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
        if (menu) {
            menu.classList.remove('is-floating');
            menu.style.width = '';
            menu.style.maxHeight = '';
            menu.style.left = '';
            menu.style.top = '';
            menu.style.bottom = '';
        }
        if (activeSmartWrap === wrap) activeSmartWrap = null;
    }

    function closeSmartSelects(activeWrap) {
        document.querySelectorAll('.api-smart-select.open, .flow-direction-select.open').forEach(function (wrap) {
            if (wrap !== activeWrap) {
                closeSmartWrap(wrap);
            }
        });
    }

    function shouldSkipSelect(select) {
        if (!select) return true;
        if (enhancedSelects.has(select)) return true;
        if (select.closest('.api-smart-select')) return true;
        if ((select.getAttribute('style') || '').replace(/\s/g, '').includes('display:none')) return true;
        return false;
    }

    function removeAdjacentSmartSelectDuplicates(select) {
        if (!select || !select.parentElement) return;
        let next = select.nextElementSibling;
        let kept = false;
        while (next && next.classList && next.classList.contains('api-smart-select')) {
            const current = next;
            next = next.nextElementSibling;
            if (!kept && select.__apiSmartSelectSync) {
                kept = true;
            } else {
                current.remove();
            }
        }
    }

    function enhanceSelect(select) {
        removeAdjacentSmartSelectDuplicates(select);
        if (select.dataset.apiSmartSelectReady === 'true' && !select.__apiSmartSelectSync) {
            select.dataset.apiSmartSelectReady = '';
            removeAdjacentSmartSelectDuplicates(select);
        }
        if (shouldSkipSelect(select)) return;

        enhancedSelects.add(select);
        select.dataset.apiSmartSelectReady = 'true';

        const parent = select.parentElement;
        if (parent) parent.classList.add('api-select-field');

        const wrap = document.createElement('div');
        wrap.className = 'api-smart-select';
        wrap.innerHTML = `
            <button type="button" class="api-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                <span class="api-select-selected"></span>
                <i class="fa-solid fa-chevron-down api-select-chevron" aria-hidden="true"></i>
            </button>
            <div class="api-select-menu" role="listbox"></div>
        `;

        select.insertAdjacentElement('afterend', wrap);

        const trigger = wrap.querySelector('.api-select-trigger');
        const selected = wrap.querySelector('.api-select-selected');
        const menu = wrap.querySelector('.api-select-menu');
        let renderedSignature = '';

        function getSignature() {
            return Array.from(select.options).map(function (option) {
                return option.value + ':' + getOptionText(option);
            }).join('|');
        }

        function renderOptions() {
            const signature = getSignature();
            if (signature === renderedSignature) return;
            renderedSignature = signature;
            menu.innerHTML = '';

            Array.from(select.options).forEach(function (sourceOption, index) {
                const meta = getOptionMeta(select, sourceOption);
                const option = document.createElement('button');
                option.type = 'button';
                option.className = 'api-select-option';
                option.setAttribute('role', 'option');
                option.dataset.index = String(index);
                option.dataset.value = sourceOption.value;
                option.innerHTML = optionMarkup(meta);
                option.addEventListener('click', function () {
                    select.selectedIndex = index;
                    select.style.border = '';
                    select.dispatchEvent(new Event('input', {bubbles: true}));
                    select.dispatchEvent(new Event('change', {bubbles: true}));
                    syncSmartSelect();
                    closeSmartWrap(wrap);
                });
                menu.appendChild(option);
            });
        }

        function syncSmartSelect() {
            renderOptions();
            const currentOption = select.options[select.selectedIndex] || select.options[0];
            const meta = getOptionMeta(select, currentOption);
            selected.innerHTML = optionMarkup(meta);
            wrap.classList.toggle('has-value', Boolean(select.value));
            wrap.classList.toggle('has-error', select.style.border.includes('red'));
            wrap.classList.toggle('is-disabled', Boolean(select.disabled));

            menu.querySelectorAll('.api-select-option').forEach(function (option) {
                const isActive = parseInt(option.dataset.index || '-1', 10) === select.selectedIndex;
                option.classList.toggle('active', isActive);
                option.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });
        }

        if (valueDescriptor && valueDescriptor.configurable && !select.dataset.apiSmartValueHooked) {
            Object.defineProperty(select, 'value', {
                get: function () {
                    return valueDescriptor.get.call(select);
                },
                set: function (value) {
                    valueDescriptor.set.call(select, value);
                    window.requestAnimationFrame(syncSmartSelect);
                }
            });
            select.dataset.apiSmartValueHooked = 'true';
        }

        if (selectedIndexDescriptor && selectedIndexDescriptor.configurable && !select.dataset.apiSmartIndexHooked) {
            Object.defineProperty(select, 'selectedIndex', {
                get: function () {
                    return selectedIndexDescriptor.get.call(select);
                },
                set: function (value) {
                    selectedIndexDescriptor.set.call(select, value);
                    window.requestAnimationFrame(syncSmartSelect);
                }
            });
            select.dataset.apiSmartIndexHooked = 'true';
        }

        trigger.addEventListener('click', function () {
            if (select.disabled) return;
            const willOpen = !wrap.classList.contains('open');
            closeSmartSelects(wrap);
            wrap.classList.toggle('open', willOpen);
            trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            activeSmartWrap = willOpen ? wrap : null;
            if (willOpen) {
                syncSmartSelect();
                window.requestAnimationFrame(function () {
                    positionFloatingMenu(wrap);
                });
            } else {
                closeSmartWrap(wrap);
            }
        });

        trigger.addEventListener('keydown', function (event) {
            if (select.disabled) return;

            if (event.key === 'Escape') {
                closeSmartWrap(wrap);
                return;
            }

            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                trigger.click();
                return;
            }

            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                const offset = event.key === 'ArrowDown' ? 1 : -1;
                const total = select.options.length;
                if (!total) return;
                select.selectedIndex = (select.selectedIndex + offset + total) % total;
                select.dispatchEvent(new Event('change', {bubbles: true}));
                syncSmartSelect();
            }
        });

        select.addEventListener('change', syncSmartSelect);
        select.addEventListener('input', syncSmartSelect);
        select.__apiSmartSelectSync = syncSmartSelect;
        syncSmartSelect();
        window.setTimeout(syncSmartSelect, 0);
    }

    function initSmartSelects(root) {
        const scope = root && root.querySelectorAll ? root : document;
        if (scope.matches && scope.matches('select')) enhanceSelect(scope);
        scope.querySelectorAll('select').forEach(enhanceSelect);
    }

    window.syncApiSmartSelects = function (root) {
        const scope = root && (root.matches || root.querySelectorAll) ? root : document;
        initSmartSelects(scope);

        const selects = [];
        if (scope.matches && scope.matches('select')) {
            selects.push(scope);
        }
        if (scope.querySelectorAll) {
            scope.querySelectorAll('select').forEach(function (select) {
                selects.push(select);
            });
        }

        selects.forEach(function (select) {
            removeAdjacentSmartSelectDuplicates(select);
            if (typeof select.__apiSmartSelectSync === 'function') select.__apiSmartSelectSync();
        });
    };

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.api-smart-select, .flow-direction-select, .api-select-menu')) {
            closeSmartSelects(null);
        }
    });

    window.addEventListener('resize', function () {
        if (activeSmartWrap) positionFloatingMenu(activeSmartWrap);
    });

    document.addEventListener('scroll', function () {
        if (activeSmartWrap) positionFloatingMenu(activeSmartWrap);
    }, true);

    document.addEventListener('DOMContentLoaded', function () {
        initSmartSelects(document);

        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType !== 1) return;
                    if ((node.matches && node.matches('select')) || (node.querySelector && node.querySelector('select'))) {
                        initSmartSelects(node);
                    }
                });
            });
        });

        observer.observe(document.body, {childList: true, subtree: true});
    });
})();
</script>

<script>
window.API_EDIT_CONFIG = <?php echo $edit_config_json; ?>;

(function () {
    function firstEditValue() {
        for (let i = 0; i < arguments.length; i++) {
            const value = arguments[i];
            if (value !== undefined && value !== null && String(value).trim() !== '') {
                return value;
            }
        }
        return '';
    }

    function setValue(id, value) {
        const el = document.getElementById(id);
        if (!el || value === undefined || value === null) return;
        let normalizedValue = String(value);
        if (el.tagName === 'SELECT' && typeof window.normalizeApiSelectRestoreValue === 'function') {
            normalizedValue = window.normalizeApiSelectRestoreValue(el, normalizedValue);
        }
        el.value = normalizedValue;
        el.setAttribute('value', normalizedValue);
        if (el.tagName === 'SELECT') {
            Array.from(el.options).forEach(function (option) {
                option.selected = option.value === normalizedValue;
            });
            if (typeof el.__apiSmartSelectSync === 'function') {
                el.__apiSmartSelectSync();
            }
        }
    }

    function normalizeFrame(frame, type) {
        frame = frame || {};
        const meta = frame.meta || {};
        const fallbackType = type === 'cmd' ? 'COMMAND' : (type === 'resp' ? 'RESPONSE' : 'ERROR');
        const choose = function () {
            for (let i = 0; i < arguments.length; i++) {
                const value = arguments[i];
                if (value !== undefined && value !== null && String(value).trim() !== '') {
                    return value;
                }
            }
            return '';
        };

        return {
            parameterName: choose(frame.parameterName, meta.parametername, meta.parameter_name),
            direction: choose(frame.direction, meta.dataflowdirection, meta.data_flow_direction),
            size: choose(frame.size, meta.framesize_bytes, meta.frame_size_bytes),
            triggerOptions: choose(frame.triggerOptions, meta.frametrigger, meta.frame_trigger, 'None'),
            frameType: choose(frame.frameType, meta.frametype, meta.frame_type, fallbackType),
            multipleui: choose(frame.multipleui, meta.multipleui, meta.multiple_ui, 'false'),
            triggerui: choose(frame.triggerui, meta.triggerui, meta.trigger_ui, 'false'),
            meta: Object.assign({
                parametername: choose(frame.parameterName, meta.parametername),
                dataflowdirection: choose(frame.direction, meta.dataflowdirection),
                frametype: choose(meta.frametype, fallbackType),
                framesize_bytes: choose(frame.size, meta.framesize_bytes),
                frametrigger: choose(frame.triggerOptions, meta.frametrigger, 'None')
            }, meta),
            rows: Array.isArray(frame.rows) ? frame.rows : []
        };
    }

    function frameTotal(savedCount, frames) {
        const fromSavedCount = parseInt(savedCount || '0', 10) || 0;
        const fromFrames = Array.isArray(frames) ? frames.length : 0;
        return Math.max(fromSavedCount, fromFrames, 1);
    }

    function hydrateApiEditForm() {
        const editConfig = window.API_EDIT_CONFIG;
        if (!editConfig || !editConfig.product) return;

        const product = editConfig.product || {};
        const payload = editConfig.payload || {};
        const payloadProduct = payload.product || {};
        const pageNames = product.page_names || product.pageNames || product.pagenames || payloadProduct.page_names || payloadProduct.pageNames || payloadProduct.pagenames || '';
        const commands = (payload.commands || []).map(function (frame) { return normalizeFrame(frame, 'cmd'); });
        const responses = (payload.responses || []).map(function (frame) { return normalizeFrame(frame, 'resp'); });
        const errors = (payload.errors || []).map(function (frame) { return normalizeFrame(frame, 'error'); });

        setValue('api_config_id', firstEditValue(editConfig.config_id));
        setValue('par_parametername', firstEditValue(product.parameter_name));
        setValue('com_parametername', firstEditValue(product.parameter_name));
        setValue('resp_parametername', firstEditValue(product.parameter_name));
        setValue('error_parametername', firstEditValue(product.parameter_name));
        setValue('productname', firstEditValue(product.productname));
        setValue('brandname', firstEditValue(product.brandname));
        setValue('modelname', firstEditValue(product.modelname));
        setValue('numberofpage', firstEditValue(product.page_number));
        setValue('Sequence', firstEditValue(product.sequence_number));
        setValue('numberofpages', frameTotal(product.command_frame_count, commands));
        setValue('page', frameTotal(product.response_frame_count, responses));
        setValue('pages', frameTotal(product.error_frame_count, errors));
        setValue('pagenames', pageNames);
        setValue('productdescription', firstEditValue(product.product_description));
        if (product.date) setValue(document.querySelector('input[name="date"]')?.id || 'date', product.date);
        const dateInput = document.querySelector('input[name="date"]');
        if (dateInput && product.date) dateInput.value = product.date;

        if (typeof storedCommands !== 'undefined') {
            storedCommands.length = 0;
            commands.forEach(function (frame, index) { storedCommands[index] = frame; });
        }
        if (typeof storedResponses !== 'undefined') {
            storedResponses.length = 0;
            responses.forEach(function (frame, index) { storedResponses[index] = frame; });
        }
        if (typeof storedErrors !== 'undefined') {
            storedErrors.length = 0;
            errors.forEach(function (frame, index) { storedErrors[index] = frame; });
        }

        if (typeof commandCount !== 'undefined') commandCount = 1;
        if (typeof responseCount !== 'undefined') responseCount = 1;
        if (typeof errorCount !== 'undefined') errorCount = 1;
        if (typeof commandPageTouched !== 'undefined') {
            commandPageTouched.length = 0;
            commands.forEach(function (_, index) { commandPageTouched[index] = true; });
        }
        if (typeof responsePageTouched !== 'undefined') {
            responsePageTouched.length = 0;
            responses.forEach(function (_, index) { responsePageTouched[index] = true; });
        }
        if (typeof errorPageTouched !== 'undefined') {
            errorPageTouched.length = 0;
            errors.forEach(function (_, index) { errorPageTouched[index] = true; });
        }

        if (typeof generateCommandProgressBoxes === 'function') generateCommandProgressBoxes();
        if (typeof generateResponseProgressBoxes === 'function') generateResponseProgressBoxes();
        if (typeof generateErrorProgressBoxes === 'function') generateErrorProgressBoxes();

        if (commands[0] && typeof applyCommandFrameData === 'function') applyCommandFrameData(commands[0]);
        if (responses[0] && typeof applyResponseFrameData === 'function') applyResponseFrameData(responses[0]);
        if (errors[0] && typeof applyErrorFrameData === 'function') applyErrorFrameData(errors[0]);

        const firstCommandMeta = commands[0]?.meta || {};
        setValue('cmd_frame_trigger_options', firstCommandMeta.frametrigger || 'None');
        setValue('cmd_multipleui', firstCommandMeta.multipleui || 'false');
        setValue('cmd_triggerui', firstCommandMeta.triggerui || 'false');

        if (typeof updateCommandFrameTitle === 'function') updateCommandFrameTitle();
        if (typeof updateResponseFrameTitle === 'function') updateResponseFrameTitle();
        if (typeof updateErrorFrameTitle === 'function') updateErrorFrameTitle();

        if (typeof updateCommandButtonLabels === 'function') updateCommandButtonLabels(parseInt(document.getElementById('numberofpages')?.value || '1', 10));
        if (typeof updateResponseButtonLabels === 'function') updateResponseButtonLabels(parseInt(document.getElementById('page')?.value || '1', 10));
        if (typeof updateErrorButtonLabels === 'function') updateErrorButtonLabels(parseInt(document.getElementById('pages')?.value || '1', 10));

        if (typeof window.syncApiSmartSelects === 'function') {
            window.syncApiSmartSelects(document);
        }

        if (typeof window.showStep === 'function') window.showStep(1);
    }

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(hydrateApiEditForm, 0);
    });
})();
</script>

<script>
/* Frontend-only row tools: duplicate, move, position, undo, redo. */
(function () {
    const tableMap = {
        cmd: 'additionalTableBody',
        resp: 'responseTableBody',
        error: 'errorTableBody'
    };
    const history = {
        cmd: {undo: [], redo: []},
        resp: {undo: [], redo: []},
        error: {undo: [], redo: []}
    };
    let restoring = false;
    let historyReady = false;

    function tableIdFor(prefix) {
        return tableMap[prefix] || (typeof getTableIdByPrefix === 'function' ? getTableIdByPrefix(prefix) : '');
    }

    function tbodyFor(prefix) {
        return document.getElementById(tableIdFor(prefix));
    }

    function fieldKey(name, prefix) {
        if (!name) return '';
        return name
            .replace(new RegExp('^' + prefix + '_'), '')
            .replace(new RegExp('_' + prefix + '_\\d+$'), '')
            .replace(/_\d+$/, '')
            .toLowerCase();
    }

    function snapshotRow(row, prefix) {
        const fields = {};
        row.querySelectorAll('input, select, textarea').forEach(function (field) {
            const name = field.getAttribute('name') || field.id || '';
            const key = fieldKey(name, prefix);
            if (!key) return;
            fields[key] = {
                value: field.value,
                checked: field.checked,
                disabled: field.disabled,
                readOnly: field.readOnly,
                userChoice: field.getAttribute('data-user-choice') || ''
            };
        });

        return {
            fields: fields,
            bitwise: row.getAttribute('data-bitwise-storage') || ''
        };
    }

    function snapshotTable(prefix) {
        const tbody = tbodyFor(prefix);
        if (!tbody) return [];
        return Array.from(tbody.rows).map(function (row) {
            return snapshotRow(row, prefix);
        });
    }

    function setFieldValue(field, state) {
        field.value = state.value;
        field.checked = !!state.checked;
        field.disabled = !!state.disabled;
        field.readOnly = !!state.readOnly;
        field.setAttribute('value', state.value);
        if (state.userChoice) {
            field.setAttribute('data-user-choice', state.userChoice);
        } else {
            field.removeAttribute('data-user-choice');
        }
        if (field.tagName === 'SELECT') {
            Array.from(field.options).forEach(function (option) {
                option.selected = option.value === state.value;
            });
            field.dispatchEvent(new Event('change', {bubbles: true}));
        }
    }

    function applyRowState(row, prefix, rowState) {
        if (!row || !rowState) return;
        row.querySelectorAll('input, select, textarea').forEach(function (field) {
            const name = field.getAttribute('name') || field.id || '';
            const key = fieldKey(name, prefix);
            if (key && rowState.fields[key]) {
                setFieldValue(field, rowState.fields[key]);
            }
        });
        if (rowState.bitwise) {
            row.setAttribute('data-bitwise-storage', rowState.bitwise);
        } else {
            row.removeAttribute('data-bitwise-storage');
        }
    }

    function refreshTable(prefix) {
        const tableId = tableIdFor(prefix);
        const tbody = document.getElementById(tableId);
        if (!tbody) return;
        if (typeof updateSerialNumbers === 'function') updateSerialNumbers(tableId);
        Array.from(tbody.rows).forEach(function (row, index) {
            const rowNumber = index + 1;
            if (typeof setupByteCalculation === 'function') setupByteCalculation(row, prefix, rowNumber);
            if (typeof attachUIChangeHandlerForRow === 'function') attachUIChangeHandlerForRow(row, prefix, rowNumber);
            if (typeof attachFieldDescriptionHandler === 'function') attachFieldDescriptionHandler(row, prefix, rowNumber);
            if (typeof attachUIChangeValidationHandler === 'function') attachUIChangeValidationHandler(row, prefix, rowNumber);
            if (typeof attachFieldDescriptionChangeHandler === 'function') attachFieldDescriptionChangeHandler(row, prefix, rowNumber);
            if (typeof syncSelectedRowState === 'function') syncSelectedRowState(row);
        });
        if (typeof updateMoveButtons === 'function') updateMoveButtons(tableId);
        if (typeof updateFrameCountsForPrefix === 'function') updateFrameCountsForPrefix(prefix);
        if (typeof window.syncApiSmartSelects === 'function') window.syncApiSmartSelects(tbody);
        updateHistoryButtons(prefix);
    }

    function restoreTable(prefix, snapshot) {
        const tbody = tbodyFor(prefix);
        if (!tbody) return;
        restoring = true;
        tbody.innerHTML = '';
        (snapshot || []).forEach(function (rowState, index) {
            const row = tbody.insertRow();
            row.innerHTML = getTableRowHTML(index + 1, prefix);
            applyRowState(row, prefix, rowState);
        });
        restoring = false;
        refreshTable(prefix);
    }

    function pushHistory(prefix) {
        if (restoring || !historyReady || !history[prefix]) return;
        history[prefix].undo.push(snapshotTable(prefix));
        if (history[prefix].undo.length > 40) history[prefix].undo.shift();
        history[prefix].redo = [];
        updateHistoryButtons(prefix);
    }

    function updateHistoryButtons(prefix) {
        const state = history[prefix];
        if (!state) return;
        document.querySelectorAll('[data-history-action="undo"][data-prefix="' + prefix + '"]').forEach(function (btn) {
            btn.disabled = state.undo.length === 0;
        });
        document.querySelectorAll('[data-history-action="redo"][data-prefix="' + prefix + '"]').forEach(function (btn) {
            btn.disabled = state.redo.length === 0;
        });
    }

    function rowContext(button, prefix) {
        const row = button ? button.closest('tr') : null;
        const tbody = row ? row.parentNode : tbodyFor(prefix);
        return {row: row, tbody: tbody, rows: tbody ? Array.from(tbody.rows) : []};
    }

    function selectedRowsFor(prefix) {
        const tbody = tbodyFor(prefix);
        if (!tbody) return [];
        return Array.from(tbody.rows).filter(function (row) {
            const checkbox = row.querySelector('.row-select-checkbox');
            return checkbox && checkbox.checked;
        });
    }

    function syncSelectedRowState(row) {
        const checkbox = row ? row.querySelector('.row-select-checkbox') : null;
        if (!row || !checkbox) return;
        row.classList.toggle('is-row-selected', checkbox.checked);
    }

    function requireSelectedRows(prefix) {
        const selectedRows = selectedRowsFor(prefix);
        if (!selectedRows.length) {
            alert('Please select one or more rows first.');
        }
        return selectedRows;
    }

    window.toggleRowSelection = function (checkbox) {
        syncSelectedRowState(checkbox ? checkbox.closest('tr') : null);
    };

    const originalAddRow = window.addRow;
    window.addRow = function (type) {
        pushHistory(type);
        if (typeof originalAddRow === 'function') {
            originalAddRow(type);
        }
        refreshTable(type);
    };

    window.removeRow = function (button, prefix) {
        const ctx = rowContext(button, prefix);
        if (!ctx.row) return;
        if (!confirm('Are you sure you want to delete this row?')) return;
        pushHistory(prefix);
        ctx.row.remove();
        refreshTable(prefix);
    };

    window.clearTable = function (prefix) {
        const tbody = tbodyFor(prefix);
        if (!tbody) return;
        if (!confirm('Are you sure you want to clear this table? This will remove all rows.')) return;
        pushHistory(prefix);
        tbody.innerHTML = '';
        refreshTable(prefix);
    };

    window.insertRowBelow = function (button, prefix) {
        const ctx = rowContext(button, prefix);
        if (!ctx.row || !ctx.tbody) return;
        pushHistory(prefix);
        const rowIndex = ctx.rows.indexOf(ctx.row);
        const newRow = ctx.tbody.insertRow(rowIndex + 1);
        newRow.innerHTML = getTableRowHTML(rowIndex + 2, prefix);
        refreshTable(prefix);
    };

    window.duplicateRow = function (button, prefix) {
        const ctx = rowContext(button, prefix);
        if (!ctx.row || !ctx.tbody) return;
        pushHistory(prefix);
        const rowIndex = ctx.rows.indexOf(ctx.row);
        const state = snapshotRow(ctx.row, prefix);
        const newRow = ctx.tbody.insertRow(rowIndex + 1);
        newRow.innerHTML = getTableRowHTML(rowIndex + 2, prefix);
        applyRowState(newRow, prefix, state);
        refreshTable(prefix);
    };

    window.moveRowUp = function (button, prefix) {
        const ctx = rowContext(button, prefix);
        const rowIndex = ctx.rows.indexOf(ctx.row);
        if (!ctx.row || rowIndex <= 0) return;
        pushHistory(prefix);
        ctx.tbody.insertBefore(ctx.row, ctx.rows[rowIndex - 1]);
        refreshTable(prefix);
    };

    window.moveRowDown = function (button, prefix) {
        const ctx = rowContext(button, prefix);
        const rowIndex = ctx.rows.indexOf(ctx.row);
        if (!ctx.row || rowIndex < 0 || rowIndex >= ctx.rows.length - 1) return;
        pushHistory(prefix);
        ctx.tbody.insertBefore(ctx.rows[rowIndex + 1], ctx.row);
        refreshTable(prefix);
    };

    window.moveRowToPosition = function (button, prefix) {
        const ctx = rowContext(button, prefix);
        const rowIndex = ctx.rows.indexOf(ctx.row);
        if (!ctx.row || rowIndex < 0) return;
        const target = prompt('Move row to position (1 - ' + ctx.rows.length + '):', String(rowIndex + 1));
        if (target === null) return;
        const targetIndex = parseInt(target, 10);
        if (!targetIndex || targetIndex < 1 || targetIndex > ctx.rows.length) {
            alert('Please enter a valid position between 1 and ' + ctx.rows.length + '.');
            return;
        }
        if (targetIndex === rowIndex + 1) return;
        pushHistory(prefix);
        const ref = ctx.rows[targetIndex - 1];
        if (targetIndex - 1 > rowIndex) {
            ctx.tbody.insertBefore(ctx.row, ref.nextSibling);
        } else {
            ctx.tbody.insertBefore(ctx.row, ref);
        }
        refreshTable(prefix);
    };

    window.deleteSelectedRows = function (prefix) {
        const selectedRows = requireSelectedRows(prefix);
        if (!selectedRows.length) return;
        if (!confirm('Delete ' + selectedRows.length + ' selected row(s)?')) return;
        pushHistory(prefix);
        selectedRows.forEach(function (row) {
            row.remove();
        });
        refreshTable(prefix);
    };

    window.moveSelectedRowsUp = function (prefix) {
        const selectedRows = requireSelectedRows(prefix);
        if (!selectedRows.length) return;
        const tbody = tbodyFor(prefix);
        if (!tbody) return;
        pushHistory(prefix);
        selectedRows.forEach(function (row) {
            const previous = row.previousElementSibling;
            if (previous && !previous.querySelector('.row-select-checkbox:checked')) {
                tbody.insertBefore(row, previous);
            }
        });
        refreshTable(prefix);
    };

    window.moveSelectedRowsDown = function (prefix) {
        const selectedRows = requireSelectedRows(prefix).reverse();
        if (!selectedRows.length) return;
        const tbody = tbodyFor(prefix);
        if (!tbody) return;
        pushHistory(prefix);
        selectedRows.forEach(function (row) {
            const next = row.nextElementSibling;
            if (next && !next.querySelector('.row-select-checkbox:checked')) {
                tbody.insertBefore(next, row);
            }
        });
        refreshTable(prefix);
    };

    window.moveSelectedRowsToPosition = function (prefix) {
        const selectedRows = requireSelectedRows(prefix);
        if (!selectedRows.length) return;
        const tbody = tbodyFor(prefix);
        if (!tbody) return;
        const totalRows = tbody.rows.length;
        const target = prompt('Move selected rows to position (1 - ' + totalRows + '):', '1');
        if (target === null) return;
        const targetIndex = parseInt(target, 10);
        if (!targetIndex || targetIndex < 1 || targetIndex > totalRows) {
            alert('Please enter a valid position between 1 and ' + totalRows + '.');
            return;
        }
        pushHistory(prefix);
        const selectedSet = new Set(selectedRows);
        const remainingRows = Array.from(tbody.rows).filter(function (row) {
            return !selectedSet.has(row);
        });
        const referenceRow = remainingRows[targetIndex - 1] || null;
        selectedRows.forEach(function (row) {
            tbody.insertBefore(row, referenceRow);
        });
        refreshTable(prefix);
    };

    window.undoTableChange = function (prefix) {
        const state = history[prefix];
        if (!state || !state.undo.length) return;
        state.redo.push(snapshotTable(prefix));
        restoreTable(prefix, state.undo.pop());
    };

    window.redoTableChange = function (prefix) {
        const state = history[prefix];
        if (!state || !state.redo.length) return;
        state.undo.push(snapshotTable(prefix));
        restoreTable(prefix, state.redo.pop());
    };

    window.closeActionMenu = function (button) {
        const actionCell = button && button.closest ? button.closest('.dynamic-table td.action-buttons') : null;
        if (button && button.blur) button.blur();
        if (actionCell) actionCell.classList.remove('is-open');
    };

    document.addEventListener('DOMContentLoaded', function () {
        window.setTimeout(function () {
            historyReady = true;
            Object.keys(history).forEach(function (prefix) {
                history[prefix].undo = [];
                history[prefix].redo = [];
                updateHistoryButtons(prefix);
            });
        }, 250);

        document.addEventListener('click', function (event) {
            const closeButton = event.target.closest('.action-menu-close-btn');
            if (closeButton) {
                event.preventDefault();
                event.stopPropagation();
                window.closeActionMenu(closeButton);
                return;
            }

            const actionCell = event.target.closest('.dynamic-table td.action-buttons');
            document.querySelectorAll('.dynamic-table td.action-buttons.is-open').forEach(function (cell) {
                if (cell !== actionCell) cell.classList.remove('is-open');
            });

            if (!actionCell || window.matchMedia('(min-width: 601px)').matches) return;
            if (event.target.closest('button')) {
                window.setTimeout(function () {
                    actionCell.classList.remove('is-open');
                }, 120);
                return;
            }

            event.preventDefault();
            actionCell.classList.toggle('is-open');
        });
    });
})();
</script>



</body>
</html>                                                                                                                                                                                                                                                                                                                                                                                                                                                             
