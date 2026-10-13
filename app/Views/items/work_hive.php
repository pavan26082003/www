<?php

// WORKHIVE_DIRECT_VIEW_GUARD
// Do not open this view file directly from /app/Views/work_hive/work_hive.php.
// CodeIgniter must load it through WorkHiveController so session data is available.
if (!defined('APPPATH')) {
  header('Location: /index.php/work-hive');
  exit;
}

/**
 * WorkHive view — work_hive.php
 *
 * IST (Asia/Kolkata) everywhere:
 *  - PHP  : date_default_timezone_set('Asia/Kolkata') in the controller
 *  - MySQL: SET time_zone = '+05:30' in the models
 *  - JS   : times from DB are already IST strings → displayed as-is
 *           (NO 'Z' appended — that would wrongly treat them as UTC)
 *
 * New features vs original:
 *  - Add Employee modal (HR / Manager only)
 *  - Edit modal pre-fills IST times correctly
 *  - No timezone double-conversion in fmtTime()
 */

if (!function_exists('attend_h')) {
  function attend_h($value): string
  {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
  }
}

if (!function_exists('attend_normalize_role')) {
  function attend_normalize_role($role): string
  {
    $role = strtolower(trim((string) $role));
    $role = str_replace([' ', '-'], '_', $role);
    return ($role === 'team_lead' || $role === 'teamlead') ? 'teamlead' : $role;
  }
}

if (!function_exists('attend_role_label')) {
  function attend_role_label($role): string
  {
    switch (attend_normalize_role($role)) {
      case 'teamlead':
        return 'Team Lead';
      case 'hr':
        return 'HR';
      case 'manager':
        return 'Manager';
      case 'employee':
        return 'Employee';
      default:
        $r = attend_normalize_role($role);
        return $r !== '' ? ucwords(str_replace('_', ' ', $r)) : 'User';
    }
  }
}

if (!function_exists('attend_initials')) {
  function attend_initials($name): string
  {
    $name = trim((string) $name);
    if ($name === '') return 'AT';
    $parts = preg_split('/\s+/', $name);
    $first = strtoupper(substr($parts[0] ?? 'A', 0, 1));
    $last  = strtoupper(substr($parts[count($parts) - 1] ?? 'T', 0, 1));
    return count($parts) > 1 ? $first . $last : substr(strtoupper($name), 0, 2);
  }
}

$isLoggedIn   = !empty($isLoggedIn);
$userName     = $userName     ?? '';
$empId        = $empId        ?? '';
$userRole     = attend_normalize_role($userRole ?? '');
$canEdit      = !empty($canEdit);
$canAnalytics = !empty($canAnalytics);
$canAddEmp    = !empty($canAddEmp);
$department   = $department   ?? '';

$apiBase   = $apiBase   ?? (function_exists('site_url') ? site_url('work-hive/api') : '/index.php/work-hive/api');
$logoutUrl = $logoutUrl ?? (function_exists('site_url') ? site_url('work-hive/logout') : '/index.php/work-hive/logout');
$pageUrl   = $pageUrl   ?? (function_exists('site_url') ? site_url('work-hive') : '/index.php/work-hive');
$csrfName  = $csrfName  ?? (function_exists('csrf_token') ? csrf_token() : '');
$csrfHash  = $csrfHash  ?? (function_exists('csrf_hash')  ? csrf_hash()  : '');

$userRoleClass = preg_replace('/[^a-z0-9_-]/', '', $userRole) ?: 'employee';
$userRoleLabel = attend_role_label($userRole);
$monthStart    = date('Y-m-01');
$todayDate     = date('Y-m-d');

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WorkSync</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;550;600;650;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
  <script>
    (function () {
      try {
        var saved = localStorage.getItem('workhive_theme') || 'light';
        document.documentElement.setAttribute('data-theme', saved === 'dark' ? 'dark' : 'light');
      } catch (e) {
        document.documentElement.setAttribute('data-theme', 'light');
      }
    })();
  </script>
  <style>
    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0
    }

    :root {
      /* ─── These match the light theme so the page never flashes dark ─── */
      --bg: #EFF3FF;
      --bg2: #E2E9FF;
      --card: #FFFFFF;
      --card2: #F5F8FF;
      --border: #D8E2FF;
      --border2: #B6C6FE;
      --accent: #3B5BDB;
      --accent2: #7048E8;
      --green: #2F9E44;
      --red: #E03131;
      --yellow: #E67700;
      --purple: #7048E8;
      --text: #1C2333;
      --text2: #5C6982;
      --text3: #97A3B5;
      --shadow: 0 20px 60px rgba(59, 91, 219, .12);
      --shadow-soft: 0 8px 24px rgba(59, 91, 219, .08);
      --surface-grad: linear-gradient(135deg, rgba(59,91,219,.07), rgba(112,72,232,.05));
      --hero-grad: radial-gradient(ellipse at 0% 0%, rgba(59,91,219,.12), transparent 45%),
                   radial-gradient(ellipse at 100% 0%, rgba(112,72,232,.09), transparent 45%),
                   #EFF3FF;
      --chart-text: #5C6982;
      --chart-grid: rgba(92, 105, 130, .14);
      --r: 18px;
    }

    html,
    body {
      height: 100%;
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      overflow: hidden
    }

    .app {
      display: flex;
      flex-direction: column;
      height: 100vh
    }

    /* ── Top nav ── */
    .topnav {
      display: flex;
      align-items: center;
      gap: 20px;
      height: 58px;
      padding: 0 28px;
      background: var(--card);
      border-bottom: 1px solid var(--border);
      flex-shrink: 0
    }

    .brand {
      font-size: 1.15rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      display: flex;
      align-items: center;
      gap: 8px
    }

    .nav-spacer {
      flex: 1
    }

    .nav-user {
      display: flex;
      align-items: center;
      gap: 10px
    }

    .avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: .75rem;
      color: #fff
    }

    .nav-info .nm {
      font-size: .85rem;
      font-weight: 600
    }

    .nav-info .eid {
      font-size: .72rem;
      color: var(--text2);
      font-family: 'JetBrains Mono', monospace
    }

    .role-badge {
      padding: 3px 10px;
      border-radius: 20px;
      font-size: .68rem;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase
    }

    .rb-employee {
      background: rgba(59, 130, 246, .15);
      color: #60a5fa
    }

    .rb-teamlead {
      background: rgba(16, 185, 129, .15);
      color: #34d399
    }

    .rb-manager {
      background: rgba(245, 158, 11, .15);
      color: #fbbf24
    }

    .rb-hr {
      background: rgba(239, 68, 68, .15);
      color: #f87171
    }

    /* ── Buttons ── */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      padding: 8px 18px;
      border: none;
      border-radius: 9px;
      cursor: pointer;
      font-family: 'Inter', inherit;
      font-size: .83rem;
      font-weight: 600;
      transition: .18s
    }

    .btn-sm {
      padding: 6px 13px;
      font-size: .78rem;
      border-radius: 8px
    }

    .btn-outline {
      background: transparent;
      border: 1px solid var(--border2);
      color: var(--text2)
    }

    .btn-outline:hover {
      border-color: var(--accent);
      color: var(--accent);
      background: rgba(59, 130, 246, .06)
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: #fff
    }

    .btn-primary:hover {
      opacity: .9;
      transform: translateY(-1px)
    }

    .btn-green {
      background: var(--green);
      color: #fff;
      font-weight: 700
    }

    .btn-green:hover {
      background: #059669
    }

    .btn-red {
      background: var(--red);
      color: #fff
    }

    .btn-warn {
      background: rgba(245, 158, 11, .12);
      color: var(--yellow);
      border: 1px solid rgba(245, 158, 11, .28)
    }

    .btn-warn:hover {
      background: rgba(245, 158, 11, .22)
    }

    .btn-purple {
      background: rgba(139, 92, 246, .15);
      color: #a78bfa;
      border: 1px solid rgba(139, 92, 246, .3)
    }

    .btn-purple:hover {
      background: rgba(139, 92, 246, .25)
    }

    /* ── Shell ── */
    .shell {
      display: flex;
      flex: 1;
      overflow: hidden
    }

    /* ── Sidebar ── */
    .sidebar {
      width: 210px;
      flex-shrink: 0;
      background: var(--card);
      border-right: 1px solid var(--border);
      padding: 18px 0;
      display: flex;
      flex-direction: column;
      gap: 2px
    }

    .nav-section {
      padding: 6px 18px 4px;
      font-size: .65rem;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--text3);
      margin-top: 10px
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 18px;
      cursor: pointer;
      font-size: .87rem;
      font-weight: 500;
      color: var(--text2);
      border-left: 3px solid transparent;
      transition: .15s
    }

    .nav-item:hover {
      background: var(--bg2);
      color: var(--text)
    }

    .nav-item.active {
      background: rgba(59, 130, 246, .08);
      color: var(--accent);
      border-left-color: var(--accent)
    }

    .nav-item i {
      width: 16px;
      text-align: center;
      font-size: .9rem
    }

    /* ── Content ── */
    .content {
      flex: 1;
      overflow-y: auto;
      padding: 28px 32px
    }

    .page {
      display: none
    }

    .page.active {
      display: block;
      animation: fadeUp .3s ease
    }

    /* ── Card ── */
    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--r);
      padding: 20px
    }

    .card-title {
      font-size: .72rem;
      font-weight: 700;
      letter-spacing: .09em;
      text-transform: uppercase;
      color: var(--text2);
      margin-bottom: 14px
    }

    /* ── Grids ── */
    .g2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px
    }

    .g3 {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 14px
    }

    .g4 {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px
    }

    /* ── Stat ── */
    .stat {
      background: var(--card2);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 18px;
      position: relative;
      overflow: hidden
    }

    .stat::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2.5px;
      background: var(--c, var(--accent))
    }

    .stat .val {
      font-size: 2rem;
      font-weight: 700;
      font-family: 'JetBrains Mono', monospace;
      line-height: 1
    }

    .stat .lbl {
      font-size: .72rem;
      color: var(--text2);
      margin-top: 5px;
      text-transform: uppercase;
      letter-spacing: .06em;
      font-weight: 600
    }

    .stat .ico {
      position: absolute;
      right: 14px;
      top: 14px;
      font-size: 1.3rem;
      opacity: .2
    }

    /* ── Hero ── */
    .hero {
      text-align: center;
      padding: 44px 28px;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      margin-bottom: 24px;
      position: relative;
      overflow: hidden
    }

    .clock {
      font-size: 3.4rem;
      font-weight: 700;
      font-family: 'JetBrains Mono', monospace;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      line-height: 1
    }

    .dateline {
      color: var(--text2);
      font-size: .9rem;
      margin: 8px 0 28px
    }

    .tz-badge {
      display: inline-block;
      margin-top: 6px;
      padding: 2px 10px;
      border-radius: 20px;
      font-size: .7rem;
      font-weight: 700;
      letter-spacing: .08em;
      background: rgba(59, 130, 246, .1);
      color: #60a5fa;
      border: 1px solid rgba(59, 130, 246, .2)
    }

    .status-pill {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 6px 18px;
      border-radius: 30px;
      font-weight: 600;
      font-size: .85rem;
      margin-bottom: 20px
    }

    .sp-present {
      background: rgba(16, 185, 129, .12);
      color: #34d399;
      border: 1px solid rgba(16, 185, 129, .25)
    }

    .sp-absent {
      background: rgba(239, 68, 68, .12);
      color: #f87171;
      border: 1px solid rgba(239, 68, 68, .25)
    }

    .sp-idle {
      background: rgba(59, 130, 246, .1);
      color: #60a5fa;
      border: 1px solid rgba(59, 130, 246, .22)
    }

    .ci-row {
      display: flex;
      justify-content: center;
      gap: 44px;
      margin: 16px 0 28px
    }

    .ci-item .t {
      font-size: 1.2rem;
      font-family: 'JetBrains Mono', monospace;
      font-weight: 500
    }

    .ci-item .l {
      font-size: .7rem;
      color: var(--text2);
      text-transform: uppercase;
      letter-spacing: .07em;
      margin-top: 3px
    }

    .action-row {
      display: flex;
      gap: 12px;
      justify-content: center
    }

    .action-row .btn {
      min-width: 150px;
      font-size: .92rem;
      padding: 13px 24px;
      border-radius: 11px
    }

    .loc-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-top: 18px;
      background: var(--bg2);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 5px 14px;
      font-size: .78rem;
      color: var(--text2);
      cursor: pointer;
      transition: .2s
    }

    .loc-btn:hover {
      border-color: var(--accent);
      color: var(--accent)
    }

    .loc-fetched {
      border-color: rgba(16, 185, 129, .4) !important;
      color: var(--green) !important
    }

    /* ── Table ── */
    .tbl-wrap {
      overflow-x: auto;
      border-radius: 10px;
      border: 1px solid var(--border)
    }

    table {
      width: 100%;
      border-collapse: collapse
    }

    thead tr {
      background: var(--bg2)
    }

    th {
      padding: 10px 14px;
      font-size: .7rem;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: var(--text2);
      text-align: left;
      white-space: nowrap
    }

    td {
      padding: 11px 14px;
      font-size: .84rem;
      border-top: 1px solid var(--border)
    }

    tbody tr:hover {
      background: rgba(255, 255, 255, .02)
    }

    .mono {
      font-family: 'JetBrains Mono', monospace;
      font-size: .8rem
    }

    /* ── Pills ── */
    .pill {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 9px;
      border-radius: 20px;
      font-size: .72rem;
      font-weight: 700;
      white-space: nowrap
    }

    .pp {
      background: rgba(16, 185, 129, .12);
      color: #34d399
    }

    .pa {
      background: rgba(239, 68, 68, .12);
      color: #f87171
    }

    .ph {
      background: rgba(245, 158, 11, .12);
      color: #fbbf24
    }

    .pl {
      background: rgba(59, 130, 246, .12);
      color: #60a5fa
    }

    .pw {
      background: rgba(139, 92, 246, .12);
      color: #a78bfa
    }

    .pho {
      background: rgba(249, 115, 22, .12);
      color: #fb923c
    }

    .pe {
      background: rgba(245, 158, 11, .1);
      color: #fbbf24;
      font-size: .65rem
    }

    /* ── Page header ── */
    .ph-row {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 24px
    }

    .ph-title {
      font-size: 1.3rem;
      font-weight: 800;
      letter-spacing: -.3px
    }

    .ph-sub {
      color: var(--text2);
      font-size: .83rem;
      margin-top: 3px
    }

    .filters {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 18px;
      align-items: center
    }

    .fi {
      padding: 8px 13px;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 9px;
      color: var(--text);
      font-family: 'Inter', inherit;
      font-size: .82rem;
      outline: none;
      transition: .15s
    }

    .fi:focus {
      border-color: var(--accent)
    }

    /* ── Chart ── */
    .chart-wrap {
      position: relative;
      height: 200px
    }

    /* ── Login ── */
    .login-screen {
      position: fixed;
      inset: 0;
      background: var(--bg);
      z-index: 500;
      display: flex;
      align-items: center;
      justify-content: center;
      background-image: radial-gradient(ellipse at 25% 45%, rgba(59, 130, 246, .07) 0%, transparent 55%), radial-gradient(ellipse at 75% 55%, rgba(99, 102, 241, .06) 0%, transparent 55%)
    }

    .login-box {
      width: 400px;
      background: var(--card);
      border: 1px solid var(--border2);
      border-radius: 22px;
      padding: 44px 36px;
      box-shadow: 0 32px 80px rgba(0, 0, 0, .5);
      animation: fadeUp .5s ease
    }

    .login-logo {
      text-align: center;
      margin-bottom: 30px
    }

    .login-logo .b {
      font-size: 1.9rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent
    }

    .login-logo p {
      color: var(--text2);
      font-size: .8rem;
      margin-top: 3px
    }

    .login-logo .li {
      font-size: 2.2rem;
      margin-bottom: 6px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent
    }

    .form-group {
      margin-bottom: 16px
    }

    .form-group label {
      display: block;
      font-size: .72rem;
      font-weight: 700;
      letter-spacing: .07em;
      text-transform: uppercase;
      color: var(--text2);
      margin-bottom: 7px
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 10px 14px;
      background: var(--bg2);
      border: 1px solid var(--border);
      border-radius: 9px;
      color: var(--text);
      font-family: 'Inter', inherit;
      font-size: .88rem;
      outline: none;
      transition: .15s
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: var(--accent)
    }

    .form-group select option {
      background: var(--bg2)
    }

    .form-hint {
      font-size: .72rem;
      color: var(--text3);
      margin-top: 4px
    }

    .login-btn {
      width: 100%;
      padding: 13px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: #fff;
      font-family: 'Inter', inherit;
      font-size: .95rem;
      font-weight: 700;
      transition: .2s;
      margin-top: 4px
    }

    .login-btn:hover {
      opacity: .9;
      transform: translateY(-1px)
    }

    .login-error {
      color: var(--red);
      font-size: .8rem;
      margin-top: 10px;
      display: none;
      text-align: center
    }

    /* ── Modal ── */
    .modal-bg {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .7);
      z-index: 200;
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(4px)
    }

    .modal-bg.hidden {
      display: none
    }

    .modal {
      background: var(--card);
      border: 1px solid var(--border2);
      border-radius: 18px;
      padding: 28px;
      width: 480px;
      max-width: 95vw;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 24px 80px rgba(0, 0, 0, .6);
      animation: fadeUp .25s ease
    }

    .modal-title {
      font-size: 1rem;
      font-weight: 700;
      margin-bottom: 22px;
      display: flex;
      align-items: center;
      gap: 9px
    }

    .modal-actions {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      margin-top: 20px
    }

    .modal-section {
      font-size: .65rem;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--text3);
      margin: 18px 0 10px;
      padding-bottom: 6px;
      border-bottom: 1px solid var(--border)
    }

    /* ── Toast ── */
    .toast {
      position: fixed;
      bottom: 22px;
      right: 22px;
      z-index: 400;
      padding: 12px 18px;
      border-radius: 11px;
      font-weight: 500;
      font-size: .87rem;
      display: flex;
      align-items: center;
      gap: 9px;
      min-width: 260px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, .4);
      transform: translateY(70px);
      opacity: 0;
      transition: .3s cubic-bezier(.34, 1.56, .64, 1);
      pointer-events: none
    }

    .toast.show {
      transform: translateY(0);
      opacity: 1
    }

    .t-success {
      background: var(--green);
      color: #fff
    }

    .t-error {
      background: var(--red);
      color: #fff
    }

    .t-info {
      background: var(--accent2);
      color: #fff
    }

    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(16px)
      }

      to {
        opacity: 1;
        transform: translateY(0)
      }
    }

    @keyframes blink {

      0%,
      100% {
        opacity: 1
      }

      50% {
        opacity: .3
      }
    }

    .blink {
      animation: blink 1.6s infinite
    }

    /* ── Block mobile/tablet ── */
    .attend-device-blocker {
      display: none
    }

    @media (max-width:1199px),
    (hover:none) and (pointer:coarse) {

      html,
      body {
        overflow: hidden !important
      }

      .login-screen,
      .app,
      .modal-bg,
      .toast {
        display: none !important
      }

      .attend-device-blocker {
        position: fixed;
        inset: 0;
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 28px;
        background: radial-gradient(circle at top left, rgba(59, 130, 246, .18), transparent 34%), radial-gradient(circle at bottom right, rgba(99, 102, 241, .16), transparent 38%), var(--bg);
        color: var(--text)
      }

      .attend-device-card {
        width: min(460px, 100%);
        padding: 34px 28px;
        border-radius: 24px;
        background: rgba(20, 27, 45, .94);
        border: 1px solid var(--border2);
        box-shadow: 0 28px 80px rgba(0, 0, 0, .45);
        text-align: center
      }

      .attend-device-icon {
        width: 58px;
        height: 58px;
        margin: 0 auto 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        color: #fff;
        font-size: 1.45rem
      }

      .attend-device-title {
        font-size: 1.45rem;
        font-weight: 800;
        letter-spacing: -.03em;
        margin-bottom: 10px
      }

      .attend-device-text {
        color: var(--text2);
        font-size: .92rem;
        line-height: 1.6
      }

      .attend-device-note {
        margin-top: 18px;
        padding: 10px 14px;
        border-radius: 14px;
        background: rgba(59, 130, 246, .1);
        border: 1px solid rgba(59, 130, 246, .22);
        color: #93c5fd;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase
      }
    }

    /* ── Enterprise Work Tracking UI ── */
    .work-hero {
      display: grid;
      grid-template-columns: minmax(0, 1.25fr) minmax(340px, .75fr);
      gap: 18px;
      margin-bottom: 18px
    }

    .work-hero-card {
      position: relative;
      overflow: hidden;
      border-radius: 28px;
      padding: 24px;
      background: linear-gradient(135deg, rgba(59, 130, 246, .18), rgba(99, 102, 241, .10)), var(--card);
      border: 1px solid var(--border2);
      box-shadow: 0 22px 60px rgba(0, 0, 0, .22)
    }

    .work-hero-card::after {
      content: '';
      position: absolute;
      right: -70px;
      top: -70px;
      width: 210px;
      height: 210px;
      border-radius: 999px;
      background: rgba(34, 197, 94, .12)
    }

    /* .work-tabs / .work-tab — defined in canonical block below */

    .work-panel { display: none }
    .work-panel.active { display: block; animation: fadeUp .25s ease both }

    /* ══════════════════════════════════════════════════
       WORK PROGRESS COMMAND CENTER — CSS
       ══════════════════════════════════════════════════ */

    /* Filter bar */
    .cc-filter-bar {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--r);
      padding: 16px 20px 14px;
      margin-bottom: 20px;
      box-shadow: var(--shadow-soft)
    }
    .cc-filter-row {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center
    }
    .cc-fi {
      height: 36px;
      padding: 0 12px;
      border: 1px solid var(--border2);
      border-radius: 8px;
      background: var(--bg2);
      color: var(--text);
      font-size: .82rem;
      outline: none;
      transition: border-color .15s
    }
    .cc-fi:focus { border-color: var(--accent) }
    .cc-fi-search { min-width: 210px; flex: 1 }
    .cc-period-label {
      margin-top: 10px;
      font-size: .8rem;
      color: var(--text2)
    }
    .cc-period-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 12px;
      background: rgba(59,130,246,.12);
      color: var(--accent);
      border-radius: 20px;
      font-weight: 600;
      font-size: .78rem
    }

    /* KPI grid */
    .cc-kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0,1fr));
      gap: 14px;
      margin-bottom: 20px
    }
    .cc-kpi-skeleton { padding: 20px; color: var(--text2); grid-column: 1/-1 }
    .cc-kpi-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--r);
      padding: 18px 20px 16px;
      display: flex;
      flex-direction: column;
      gap: 6px;
      position: relative;
      overflow: hidden;
      transition: transform .18s, box-shadow .18s;
      box-shadow: var(--shadow-soft)
    }
    .cc-kpi-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
      border-radius: var(--r) var(--r) 0 0
    }
    .cc-kpi-card:hover { transform: translateY(-2px); box-shadow: var(--shadow) }
    .cc-kpi-icon { font-size: 1.25rem; margin-bottom: 2px }
    .cc-kpi-val { font-size: 1.7rem; font-weight: 800; line-height: 1 }
    .cc-kpi-lbl { font-size: .73rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--text2) }

    /* KPI colour variants */
    .cc-kpi-accent .cc-kpi-icon { color: var(--accent) }
    .cc-kpi-accent::before { background: var(--accent) }
    .cc-kpi-accent .cc-kpi-val { color: var(--accent) }
    .cc-kpi-green .cc-kpi-icon { color: var(--green) }
    .cc-kpi-green::before { background: var(--green) }
    .cc-kpi-green .cc-kpi-val { color: var(--green) }
    .cc-kpi-purple .cc-kpi-icon { color: var(--purple) }
    .cc-kpi-purple::before { background: var(--purple) }
    .cc-kpi-purple .cc-kpi-val { color: var(--purple) }
    .cc-kpi-blue .cc-kpi-icon { color: #60a5fa }
    .cc-kpi-blue::before { background: #60a5fa }
    .cc-kpi-blue .cc-kpi-val { color: #60a5fa }
    .cc-kpi-teal .cc-kpi-icon { color: #2dd4bf }
    .cc-kpi-teal::before { background: #2dd4bf }
    .cc-kpi-teal .cc-kpi-val { color: #2dd4bf }
    .cc-kpi-red .cc-kpi-icon { color: var(--red) }
    .cc-kpi-red::before { background: var(--red) }
    .cc-kpi-red .cc-kpi-val { color: var(--red) }
    .cc-kpi-yellow .cc-kpi-icon { color: var(--yellow) }
    .cc-kpi-yellow::before { background: var(--yellow) }
    .cc-kpi-yellow .cc-kpi-val { color: var(--yellow) }

    /* Admin Insights */
    .cc-insights-card { margin-bottom: 20px }
    .cc-insight-row {
      display: flex; gap: 16px; flex-wrap: wrap;
      margin: 14px 0 12px
    }
    .cc-insight-stat {
      flex: 1; min-width: 100px;
      background: var(--bg2);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 12px 16px;
      text-align: center
    }
    .cc-insight-stat .v { font-size: 1.5rem; font-weight: 800 }
    .cc-insight-stat .l { font-size: .72rem; color: var(--text2); font-weight: 700; text-transform: uppercase; letter-spacing: .05em; margin-top: 2px }
    .cc-ins-green .v { color: var(--green) }
    .cc-ins-red .v { color: var(--red) }
    .cc-ins-blue .v { color: var(--accent) }
    .cc-progress-bar-wrap {
      height: 6px; background: var(--border);
      border-radius: 99px; overflow: hidden; margin: 0 0 14px
    }
    .cc-progress-bar { height: 100%; background: var(--green); border-radius: 99px; transition: width .5s }
    .cc-no-update-title { font-size: .8rem; font-weight: 700; color: var(--red); margin-bottom: 8px }
    .cc-no-update-chips { display: flex; flex-wrap: wrap; gap: 6px }
    .cc-emp-chip {
      padding: 3px 10px;
      background: rgba(239,68,68,.1);
      color: var(--red);
      border-radius: 20px;
      font-size: .75rem;
      font-weight: 600
    }
    .cc-emp-chip .chip-eid { opacity: .7; font-size: .7rem; margin-left: 4px }
    .cc-all-submitted { color: var(--green); font-weight: 600; font-size: .84rem; padding: 6px 0 }

    /* Two-column layout */
    .cc-two-col {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
      margin-bottom: 20px
    }
    .cc-table-card { min-width: 0; overflow: hidden }
    .cc-table { width: 100%; border-collapse: collapse; font-size: .82rem }
    .cc-empty { text-align: center; color: var(--text2); padding: 28px }

    /* Performance / Risk cell helpers */
    .emp-name-cell { display: flex; align-items: center; gap: 10px }
    .cc-click-row { cursor: pointer; transition: background .16s, transform .16s }
    .cc-click-row:hover { background: rgba(59,130,246,.07); transform: translateY(-1px) }
    .cc-title-line { display: flex; align-items: center; justify-content: space-between; gap: 12px }
    .cc-title-line small { font-size: .72rem; color: var(--text3); text-transform: none; letter-spacing: 0; font-weight: 600 }
    .cc-fi-year { width: 94px }

    .emp-detail-modal { width: min(1080px, 96vw); padding: 0; overflow: hidden }
    .emp-detail-shell { max-height: 88vh; overflow-y: auto }
    .emp-detail-head {
      padding: 24px 26px;
      background: linear-gradient(135deg, rgba(59,130,246,.15), rgba(112,72,232,.10)), var(--card);
      border-bottom: 1px solid var(--border);
      display: flex; align-items: flex-start; justify-content: space-between; gap: 18px
    }
    .emp-detail-profile { display: flex; align-items: center; gap: 14px; min-width: 0 }
    .emp-detail-avatar {
      width: 58px; height: 58px; border-radius: 18px; flex-shrink: 0;
      background: linear-gradient(135deg, var(--accent), var(--accent2)); color: #fff;
      display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.05rem
    }
    .emp-detail-name { font-size: 1.2rem; font-weight: 800; letter-spacing: -.02em }
    .emp-detail-meta { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 7px; color: var(--text2); font-size: .78rem }
    .emp-detail-chip { padding: 4px 9px; border-radius: 999px; background: var(--bg2); border: 1px solid var(--border); font-weight: 700 }
    .emp-detail-body { padding: 22px 26px 26px }
    .emp-detail-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 18px }
    .emp-detail-kpi { background: var(--card2); border: 1px solid var(--border); border-radius: 15px; padding: 14px; min-width: 0 }
    .emp-detail-kpi .v { font-size: 1.35rem; line-height: 1; font-weight: 850; font-variant-numeric: tabular-nums }
    .emp-detail-kpi .l { margin-top: 5px; color: var(--text2); font-size: .68rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase }
    .emp-detail-grid { display: grid; grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr); gap: 16px }
    .emp-detail-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 16px; min-width: 0 }
    .emp-detail-card-title { font-size: .74rem; font-weight: 850; color: var(--text2); letter-spacing: .08em; text-transform: uppercase; margin-bottom: 12px }
    .emp-info-list { display: grid; gap: 9px }
    .emp-info-row { display: flex; justify-content: space-between; gap: 12px; border-bottom: 1px dashed var(--border); padding-bottom: 8px; font-size: .84rem }
    .emp-info-row span:first-child { color: var(--text2); font-weight: 700 }
    .emp-info-row span:last-child { text-align: right; font-weight: 650 }
    .emp-detail-list { display: grid; gap: 10px }
    .emp-project-card, .emp-task-card, .emp-update-card { background: var(--card2); border: 1px solid var(--border); border-radius: 13px; padding: 12px }
    .emp-project-top, .emp-task-top { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; margin-bottom:8px }
    .emp-project-title, .emp-task-title { font-weight: 800; font-size: .9rem }
    .emp-project-sub, .emp-task-sub, .emp-update-sub { color: var(--text2); font-size: .74rem; margin-top: 2px }
    .emp-progress-line { height: 7px; border-radius: 99px; background: var(--border); overflow: hidden; margin-top: 8px }
    .emp-progress-line span { display:block; height:100%; border-radius:inherit; background: linear-gradient(90deg, var(--accent), var(--accent2)) }
    .emp-update-card { border-left: 3px solid var(--accent) }
    .emp-update-title { font-weight: 800; font-size: .9rem; margin: 7px 0 5px }
    .emp-update-note { color: var(--text2); font-size: .82rem; line-height: 1.45 }
    .emp-step-mini { margin-top: 8px; display: grid; gap: 5px; font-size: .78rem }
    .emp-step-mini div { display:flex; gap:7px; color: var(--text2) }
    .emp-step-mini .done { color: var(--green); font-weight: 700 }
    .emp-detail-empty { padding: 22px; text-align: center; color: var(--text2); background: var(--card2); border: 1px dashed var(--border); border-radius: 14px }
    @media (max-width: 1200px) {
      .emp-detail-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)) }
      .emp-detail-grid { grid-template-columns: 1fr }
    }
    .emp-avatar-sm {
      width: 30px; height: 30px; flex-shrink: 0;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: #fff; font-weight: 700; font-size: .75rem;
      display: flex; align-items: center; justify-content: center
    }
    .cc-num { font-weight: 700; font-variant-numeric: tabular-nums }
    .cc-num.muted { color: var(--text2); font-weight: 400 }
    .cc-mini-progress {
      height: 3px; background: var(--border); border-radius: 99px;
      overflow: hidden; margin-top: 4px
    }
    .cc-mini-progress div { height: 100%; background: var(--green); border-radius: 99px }
    .cc-progress-pill {
      position: relative;
      height: 20px;
      background: var(--border);
      border-radius: 99px;
      overflow: hidden;
      min-width: 70px
    }
    .cc-progress-pill-fill {
      position: absolute; top: 0; left: 0; height: 100%;
      background: var(--accent);
      border-radius: 99px;
      transition: width .4s
    }
    .cc-progress-pill-fill.fill-red { background: var(--red) }
    .cc-progress-pill-fill.fill-yellow { background: var(--yellow) }
    .cc-progress-pill-fill.fill-green { background: var(--green) }
    .cc-progress-pill span {
      position: absolute; inset: 0;
      display: flex; align-items: center; justify-content: center;
      font-size: .72rem; font-weight: 700; z-index: 1
    }
    .cc-score-badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 99px;
      font-weight: 700;
      font-size: .78rem
    }
    .risk-badge { font-weight: 700; font-size: .8rem }
    .risk-badge-box {
      display: inline-block;
      padding: 2px 9px;
      border-radius: 5px;
      font-size: .72rem;
      font-weight: 800;
      letter-spacing: .05em
    }
    .wtype-chip {
      display: inline-block;
      padding: 1px 7px;
      border-radius: 4px;
      font-size: .65rem;
      font-weight: 800;
      letter-spacing: .06em;
      margin-right: 4px;
      vertical-align: middle
    }
    .wtype-prj { background: rgba(99,102,241,.15); color: #818cf8 }
    .wtype-sch { background: rgba(16,185,129,.15); color: #34d399 }
    .blocked-chip {
      display: inline-block;
      padding: 2px 7px;
      background: rgba(239,68,68,.12);
      color: var(--red);
      border-radius: 5px;
      font-size: .73rem;
      font-weight: 700;
      margin: 1px
    }
    .overdue-chip {
      display: inline-block;
      padding: 2px 7px;
      background: rgba(245,158,11,.12);
      color: var(--yellow);
      border-radius: 5px;
      font-size: .73rem;
      font-weight: 700
    }
    .chip-eid { font-size: .7rem; color: var(--text2); margin-left: 3px }

    /* Timeline */
    .cc-timeline-card { margin-bottom: 20px }
    .cc-timeline-header { margin-bottom: 16px }
    .cc-tl-subtitle { font-size: .78rem; color: var(--text2); display: block; margin-top: 4px }
    .cc-timeline-day { margin-bottom: 24px }
    .cc-tl-day-header {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 14px;
      background: var(--bg2);
      border: 1px solid var(--border);
      border-radius: 10px;
      margin-bottom: 12px;
      font-weight: 700;
      font-size: .85rem
    }
    .cc-tl-date { color: var(--text); font-weight: 800 }
    .cc-tl-count {
      padding: 2px 10px;
      background: rgba(59,130,246,.12);
      color: var(--accent);
      border-radius: 20px;
      font-size: .73rem;
      font-weight: 700;
      margin-left: auto
    }
    .cc-tl-updates { display: flex; flex-direction: column; gap: 12px; padding-left: 16px }
    .cc-tl-update {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 14px 16px;
      border-left: 3px solid var(--border2);
      transition: border-color .15s
    }
    .cc-tl-update:hover { border-left-color: var(--accent) }
    .cc-tl-update-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 8px;
      flex-wrap: wrap
    }
    .cc-tl-meta { flex: 1; min-width: 0 }
    .cc-tl-empname { font-weight: 700; font-size: .85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }
    .cc-tl-proj { font-size: .76rem; color: var(--text2); margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }
    .cc-tl-hours {
      font-weight: 700; font-size: .82rem;
      padding: 2px 8px;
      background: rgba(99,102,241,.12);
      color: var(--purple);
      border-radius: 5px
    }
    .cc-tl-pct {
      font-weight: 700; font-size: .82rem;
      padding: 2px 8px;
      background: rgba(16,185,129,.12);
      color: var(--green);
      border-radius: 5px
    }
    .cc-tl-title { font-weight: 600; font-size: .88rem; margin-bottom: 6px }
    .cc-tl-note { font-size: .8rem; color: var(--text2); margin-bottom: 5px; padding: 6px 10px; background: var(--bg2); border-radius: 6px }
    .cc-tl-achieve { font-size: .78rem; color: var(--green); margin-bottom: 4px }
    .cc-tl-blocker { font-size: .78rem; color: var(--red); margin-bottom: 4px }
    .cc-tl-plan { font-size: .78rem; color: var(--accent); margin-bottom: 4px }
    .cc-step-list {
      display: flex; flex-direction: column; gap: 4px;
      margin-top: 8px;
      padding: 10px 12px;
      background: var(--bg2);
      border-radius: 8px
    }
    .cc-step-item { display: flex; gap: 8px; align-items: flex-start; font-size: .78rem; color: var(--text2) }
    .cc-step-item.step-done { color: var(--green) }
    .cc-step-chk { flex-shrink: 0; font-weight: 800; width: 16px }

    /* Responsive: stack two-col on smaller screens */
    @media (max-width: 1100px) {
      .cc-kpi-grid { grid-template-columns: repeat(2, minmax(0,1fr)) }
      .cc-two-col { grid-template-columns: 1fr }
    }
    @media (max-width: 600px) {
      .cc-kpi-grid { grid-template-columns: 1fr 1fr }
      .cc-filter-row { flex-direction: column; align-items: stretch }
      .cc-fi, .cc-fi-search { min-width: 0; width: 100% }
    }

    .work-mini-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 12px
    }

    .work-mini {
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, .045);
      border-radius: 18px;
      padding: 14px
    }

    .work-mini .v {
      font-size: 1.35rem;
      font-weight: 900;
      color: var(--text)
    }

    .work-mini .l {
      margin-top: 4px;
      color: var(--text2);
      font-size: .72rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .06em
    }

    .work-form-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 14px
    }

    .work-form-grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)) }
    .work-form-grid .full { grid-column: 1 / -1 }

    .work-board {
      display: grid;
      grid-template-columns: 1fr;
      gap: 18px
    }

    .work-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 9px;
      border-radius: 999px;
      border: 1px solid rgba(255, 255, 255, .08);
      background: rgba(255, 255, 255, .05);
      color: var(--text2);
      font-size: .72rem;
      font-weight: 800;
      white-space: nowrap
    }

    .work-chip.project { color: #93c5fd; background: rgba(59, 130, 246, .12); border-color: rgba(59, 130, 246, .22) }
    .work-chip.schedule { color: #86efac; background: rgba(34, 197, 94, .12); border-color: rgba(34, 197, 94, .22) }
    .work-chip.blocked { color: #fca5a5; background: rgba(239, 68, 68, .12); border-color: rgba(239, 68, 68, .22) }
    .work-chip.completed { color: #86efac; background: rgba(34, 197, 94, .12); border-color: rgba(34, 197, 94, .22) }
    .work-chip.review { color: #c4b5fd; background: rgba(124, 58, 237, .13); border-color: rgba(124, 58, 237, .22) }

    .progress-line {
      width: 100%;
      height: 8px;
      border-radius: 99px;
      overflow: hidden;
      background: rgba(255, 255, 255, .08)
    }

    .progress-line span {
      display: block;
      height: 100%;
      width: var(--p, 0%);
      border-radius: inherit;
      background: linear-gradient(90deg, var(--accent), var(--green))
    }

    .muted-small { color: var(--text2); font-size: .78rem; line-height: 1.45 }

    /* ── Assignee Checkbox Picker ── */
    .assignee-picker {
      border: 1px solid var(--border2);
      border-radius: 10px;
      overflow: hidden;
      background: var(--bg2)
    }
    .assignee-picker-top {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 12px;
      border-bottom: 1px solid var(--border);
      background: var(--card)
    }
    .assignee-search {
      flex: 1;
      height: 32px;
      padding: 0 10px;
      border: 1px solid var(--border2);
      border-radius: 7px;
      background: var(--bg2);
      color: var(--text);
      font-size: .82rem;
      outline: none;
      transition: border-color .15s
    }
    .assignee-search:focus { border-color: var(--accent) }
    .assignee-picker-actions {
      display: flex;
      align-items: center;
      gap: 6px;
      flex-shrink: 0
    }
    .assignee-action-btn {
      padding: 4px 11px;
      border: 1px solid var(--border2);
      border-radius: 6px;
      background: transparent;
      color: var(--text2);
      font-size: .75rem;
      font-weight: 600;
      cursor: pointer;
      transition: .15s;
      display: flex;
      align-items: center;
      gap: 4px
    }
    .assignee-action-btn:hover {
      border-color: var(--accent);
      color: var(--accent);
      background: rgba(59,130,246,.07)
    }
    .assignee-selected-count {
      font-size: .73rem;
      font-weight: 700;
      color: var(--accent);
      padding: 3px 9px;
      background: rgba(59,130,246,.10);
      border-radius: 20px;
      white-space: nowrap;
      min-width: 72px;
      text-align: center
    }
    .assignee-list {
      max-height: 210px;
      overflow-y: auto;
      padding: 6px 4px
    }
    .assignee-list::-webkit-scrollbar { width: 5px }
    .assignee-list::-webkit-scrollbar-track { background: transparent }
    .assignee-list::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 99px }
    .assignee-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 7px 10px;
      border-radius: 7px;
      cursor: pointer;
      transition: background .12s;
      user-select: none
    }
    .assignee-item:hover { background: rgba(59,130,246,.07) }
    .assignee-item.is-checked { background: rgba(59,130,246,.10) }
    .assignee-item input[type="checkbox"] { display: none }
    .assignee-chk-box {
      width: 17px;
      height: 17px;
      border: 2px solid var(--border2);
      border-radius: 4px;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: .15s;
      background: var(--bg2)
    }
    .assignee-item.is-checked .assignee-chk-box {
      border-color: var(--accent);
      background: var(--accent)
    }
    .assignee-item.is-checked .assignee-chk-box::after {
      content: '';
      width: 9px;
      height: 5px;
      border-left: 2px solid #fff;
      border-bottom: 2px solid #fff;
      transform: rotate(-45deg) translate(0, -1px)
    }
    .assignee-avatar {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: #fff;
      font-size: .68rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0
    }
    .assignee-item.is-checked .assignee-avatar { box-shadow: 0 0 0 2px var(--accent) }
    .assignee-info { flex: 1; min-width: 0 }
    .assignee-name { font-size: .83rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }
    .assignee-eid { font-size: .71rem; color: var(--text2); margin-top: 1px }
    .assignee-empty { padding: 20px; text-align: center; color: var(--text2); font-size: .82rem }
    .assignee-hidden { display: none }


      .work-hero { grid-template-columns: 1fr }
      .work-mini-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) }
      .work-form-grid, .work-form-grid.two { grid-template-columns: 1fr }
    }



    /* ─────────────────────────────────────────────────────────────
       WorkHive Premium UI Layer
       Safe front-end only upgrade: no API, route, session, or DB changes.
       Default theme is light. Dark mode is persisted in localStorage.
    ───────────────────────────────────────────────────────────── */
    html[data-theme="light"] {
      color-scheme: light;
      /* ─── Lumina Light — Google-inspired, unique indigo-violet palette ─── */
      --bg:    #EFF3FF;          /* Soft indigo-white page */
      --bg2:   #E2E9FF;          /* Inputs, sidebar, form bg */
      --card:  #FFFFFF;          /* Pure white cards */
      --card2: #F5F8FF;          /* Card variant / secondary surfaces */

      --border:  #D8E2FF;        /* Indigo-tinted border */
      --border2: #B6C6FE;        /* Stronger border */

      --accent:  #3B5BDB;        /* Rich indigo — buttons, links, active */
      --accent2: #7048E8;        /* Violet — secondary highlights */

      --green:  #2F9E44;         /* Forest green */
      --red:    #E03131;         /* Warm red */
      --yellow: #E67700;         /* Deep amber (readable on white) */
      --purple: #7048E8;         /* Violet */

      --text:  #1C2333;          /* Near-black with blue cast */
      --text2: #5C6982;          /* Medium slate */
      --text3: #97A3B5;          /* Muted */

      --shadow:      0 20px 60px rgba(59, 91, 219, .12);
      --shadow-soft: 0 8px 24px  rgba(59, 91, 219, .08);

      --surface-grad: linear-gradient(135deg, rgba(59,91,219,.07), rgba(112,72,232,.05));
      --hero-grad:
        radial-gradient(ellipse at 0% 0%,   rgba(59,91,219,.14), transparent 45%),
        radial-gradient(ellipse at 100% 0%, rgba(112,72,232,.10), transparent 45%),
        #EFF3FF;

      --chart-text: #5C6982;
      --chart-grid: rgba(92, 105, 130, .14);
      --r: 18px;
    }

    html[data-theme="dark"] {
      color-scheme: dark;
      --bg: #09111f;
      --bg2: #0f1a2b;
      --card: rgba(15, 23, 42, .94);
      --card2: #111d31;
      --border: rgba(148, 163, 184, .16);
      --border2: rgba(148, 163, 184, .24);
      --accent: #2dd4bf;
      --accent2: #60a5fa;
      --green: #34d399;
      --red: #f87171;
      --yellow: #fbbf24;
      --purple: #a78bfa;
      --text: #f8fafc;
      --text2: #aab8cc;
      --text3: #72829a;
      --shadow: 0 22px 70px rgba(0, 0, 0, .35);
      --shadow-soft: 0 14px 36px rgba(0, 0, 0, .24);
      --surface-grad: linear-gradient(135deg, rgba(45, 212, 191, .11), rgba(96, 165, 250, .10));
      --hero-grad: radial-gradient(circle at 8% 0%, rgba(45, 212, 191, .16), transparent 35%), radial-gradient(circle at 92% 8%, rgba(96, 165, 250, .18), transparent 38%), #0f172a;
      --chart-text: #aab8cc;
      --chart-grid: rgba(148, 163, 184, .13);
      --r: 18px;
    }

    /* ════════════════════════════════════════════════════════
       LIGHT THEME — Component-level overrides
       Fixes hardcoded dark colours in components so every
       element responds correctly when data-theme="light"
       ════════════════════════════════════════════════════════ */

    /* ── Page & sidebar backgrounds ── */
    html[data-theme="light"] .sidebar {
      background: #FFFFFF;
      border-right-color: var(--border);
    }
    html[data-theme="light"] .content {
      background: transparent;
    }

    /* ── Top nav ── */
    html[data-theme="light"] .topnav {
      background: rgba(255,255,255,.92);
      border-bottom-color: var(--border);
      box-shadow: 0 1px 0 var(--border), var(--shadow-soft);
    }

    /* ── Role badges ── */
    html[data-theme="light"] .rb-employee { background: rgba(59,91,219,.10); color: #3B5BDB }
    html[data-theme="light"] .rb-teamlead { background: rgba(47,158,68,.10);  color: #2F9E44 }
    html[data-theme="light"] .rb-manager  { background: rgba(230,119,0,.10);  color: #C76B00 }
    html[data-theme="light"] .rb-hr       { background: rgba(224,49,49,.10);  color: #C92A2A }
    html[data-theme="light"] .rb-admin,
    html[data-theme="light"] [class*="rb-admin"] {
      background: rgba(112,72,232,.10); color: #6741D9
    }

    /* ── Nav items ── */
    html[data-theme="light"] .nav-item {
      color: var(--text2);
    }
    html[data-theme="light"] .nav-item:hover,
    html[data-theme="light"] .nav-item.active {
      background: rgba(59,91,219,.09);
      color: #3B5BDB;
      border-left-color: #3B5BDB;
    }
    html[data-theme="light"] .nav-section {
      color: var(--text3);
    }

    /* ── Cards ── */
    html[data-theme="light"] .card {
      background: #FFFFFF;
      border-color: var(--border);
      box-shadow: 0 1px 4px rgba(59,91,219,.06), 0 4px 16px rgba(59,91,219,.05);
    }

    /* ── Buttons ── */
    html[data-theme="light"] .btn-outline {
      border-color: var(--border2);
      color: var(--text2);
      background: #FFFFFF;
    }
    html[data-theme="light"] .btn-outline:hover {
      border-color: var(--accent);
      color: var(--accent);
      background: rgba(59,91,219,.06);
    }
    html[data-theme="light"] .theme-toggle {
      background: #FFFFFF;
      border-color: var(--border2);
      color: var(--text);
      box-shadow: 0 2px 8px rgba(59,91,219,.08);
    }

    /* ── Tables ── */
    html[data-theme="light"] table thead tr {
      background: #F0F4FF;
      color: var(--text2);
    }
    html[data-theme="light"] table tbody tr:hover {
      background: rgba(59,91,219,.04);
    }
    html[data-theme="light"] table td,
    html[data-theme="light"] table th {
      border-color: var(--border);
    }

    /* ── Form inputs ── */
    html[data-theme="light"] .fi,
    html[data-theme="light"] .form-group input,
    html[data-theme="light"] .form-group select,
    html[data-theme="light"] .form-group textarea,
    html[data-theme="light"] .cc-fi,
    html[data-theme="light"] .assignee-search {
      background: #FFFFFF;
      border-color: var(--border2);
      color: var(--text);
    }
    html[data-theme="light"] .fi:focus,
    html[data-theme="light"] .form-group input:focus,
    html[data-theme="light"] .form-group select:focus,
    html[data-theme="light"] .form-group textarea:focus,
    html[data-theme="light"] .cc-fi:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(59,91,219,.12);
    }

    /* ── Modal ── */
    html[data-theme="light"] .modal-bg {
      background: rgba(28, 35, 51, .30);
      backdrop-filter: blur(8px);
    }
    html[data-theme="light"] .modal {
      background: #FFFFFF;
      border: 1px solid var(--border);
      box-shadow: 0 24px 80px rgba(59,91,219,.18);
    }
    html[data-theme="light"] .modal-title {
      color: var(--text);
      border-bottom-color: var(--border);
    }
    html[data-theme="light"] .modal-section {
      color: var(--accent);
      border-color: var(--border);
    }

    /* ── Work chips (type + status badges) ── */
    html[data-theme="light"] .work-chip {
      border-color: var(--border2);
      background: var(--bg2);
      color: var(--text2);
    }
    html[data-theme="light"] .work-chip.project  { color: #3B5BDB; background: rgba(59,91,219,.10); border-color: rgba(59,91,219,.22) }
    html[data-theme="light"] .work-chip.schedule { color: #2F9E44; background: rgba(47,158,68,.10); border-color: rgba(47,158,68,.22) }
    html[data-theme="light"] .work-chip.blocked  { color: #C92A2A; background: rgba(224,49,49,.10); border-color: rgba(224,49,49,.22) }
    html[data-theme="light"] .work-chip.completed{ color: #2F9E44; background: rgba(47,158,68,.10); border-color: rgba(47,158,68,.22) }
    html[data-theme="light"] .work-chip.review   { color: #6741D9; background: rgba(112,72,232,.10); border-color: rgba(112,72,232,.22) }
    html[data-theme="light"] .work-chip.in_progress { color: #3B5BDB; background: rgba(59,91,219,.10); border-color: rgba(59,91,219,.22) }
    html[data-theme="light"] .work-chip.planned  { color: #C76B00; background: rgba(230,119,0,.10); border-color: rgba(230,119,0,.22) }

    /* ── Progress lines (hardcoded dark rgba bg) ── */
    html[data-theme="light"] .progress-line {
      background: rgba(59,91,219,.10);
    }
    html[data-theme="light"] .progress-line span {
      background: linear-gradient(90deg, var(--accent), var(--accent2));
    }

    /* ── Status pills ── */
    html[data-theme="light"] .pill,
    html[data-theme="light"] .status-pill {
      background: rgba(59,91,219,.10);
      color: var(--accent);
      border-color: rgba(59,91,219,.22);
    }

    /* ── Work tabs ── */
    /* light .work-tab — in canonical block */

    /* ── Work mini KPI cards ── */
    html[data-theme="light"] .work-mini {
      background: #F5F8FF;
      border-color: var(--border);
    }
    html[data-theme="light"] .work-mini .v { color: var(--accent) }

    /* ── Hero / Work hero ── */
    html[data-theme="light"] .hero,
    html[data-theme="light"] .work-hero-card {
      background: linear-gradient(135deg, rgba(59,91,219,.10), rgba(112,72,232,.07)), #FFFFFF;
      border-color: var(--border);
    }
    html[data-theme="light"] .hero::before,
    html[data-theme="light"] .work-hero-card::before {
      background: linear-gradient(180deg, rgba(59,91,219,.08), transparent 40%);
    }

    /* ── Filter bar (cc) ── */
    html[data-theme="light"] .cc-filter-bar {
      background: #FFFFFF;
      border-color: var(--border);
      box-shadow: 0 2px 12px rgba(59,91,219,.07);
    }
    html[data-theme="light"] .cc-kpi-card {
      background: #FFFFFF;
      border-color: var(--border);
      box-shadow: 0 2px 12px rgba(59,91,219,.07);
    }
    html[data-theme="light"] .cc-insight-stat {
      background: var(--bg2);
      border-color: var(--border);
    }
    html[data-theme="light"] .cc-tl-update {
      background: #FAFBFF;
      border-color: var(--border);
    }
    html[data-theme="light"] .cc-tl-update:hover { border-left-color: var(--accent) }
    html[data-theme="light"] .cc-tl-day-header {
      background: var(--bg2);
      border-color: var(--border);
    }
    html[data-theme="light"] .cc-step-list { background: var(--bg2) }
    html[data-theme="light"] .cc-tl-note { background: var(--bg2) }

    /* ── CC KPI hardcoded teal / blue → remap to light-friendly ── */
    html[data-theme="light"] .cc-kpi-teal .cc-kpi-icon,
    html[data-theme="light"] .cc-kpi-teal .cc-kpi-val  { color: #0E7490 }
    html[data-theme="light"] .cc-kpi-teal::before       { background: #0E7490 }
    html[data-theme="light"] .cc-kpi-blue .cc-kpi-icon,
    html[data-theme="light"] .cc-kpi-blue .cc-kpi-val  { color: #3B5BDB }
    html[data-theme="light"] .cc-kpi-blue::before       { background: #3B5BDB }

    /* ── wtype chips ── */
    html[data-theme="light"] .wtype-prj { background: rgba(59,91,219,.12);  color: #3B5BDB }
    html[data-theme="light"] .wtype-sch { background: rgba(47,158,68,.12);  color: #2F9E44 }

    /* ── Assignee picker ── */
    html[data-theme="light"] .assignee-picker { background: #FFFFFF; border-color: var(--border2) }
    html[data-theme="light"] .assignee-picker-top { background: #F5F8FF; border-bottom-color: var(--border) }
    html[data-theme="light"] .assignee-item:hover { background: rgba(59,91,219,.06) }
    html[data-theme="light"] .assignee-item.is-checked { background: rgba(59,91,219,.09) }

    /* ── Clock / dashboard hero ── */
    html[data-theme="light"] .clock {
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    /* ── Login page ── */
    html[data-theme="light"] .login-wrap,
    html[data-theme="light"] .attend-login-outer {
      background: linear-gradient(135deg, rgba(59,91,219,.12), rgba(112,72,232,.08)), #EFF3FF;
    }
    html[data-theme="light"] .login-box,
    html[data-theme="light"] .attend-login-box {
      background: #FFFFFF;
      box-shadow: 0 24px 80px rgba(59,91,219,.15);
      border-color: var(--border);
    }



    html,
    body {
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: var(--hero-grad);
      color: var(--text);
      letter-spacing: -.01em;
    }

    body,
    .btn,
    .fi,
    .form-group input,
    .form-group select,
    .form-group textarea,
    .login-btn,
    .work-tab {
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
    }

    .mono,
    .clock,
    .stat .val,
    .nav-info .eid {
      font-family: 'JetBrains Mono', 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, monospace !important;
    }

    .topnav {
      min-height: 68px;
      height: auto;
      padding: 12px 24px;
      background: color-mix(in srgb, var(--card) 92%, transparent);
      backdrop-filter: blur(18px);
      border-bottom: 1px solid var(--border);
      box-shadow: var(--shadow-soft);
      position: sticky;
      top: 0;
      z-index: 40;
    }

    .brand {
      position: relative;
      gap: 12px;
      font-size: 1.08rem;
      font-weight: 750;
      color: var(--text);
      background: none;
      -webkit-text-fill-color: currentColor;
    }

    .brand i {
      width: 38px;
      height: 38px;
      border-radius: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: #fff;
      box-shadow: 0 14px 28px color-mix(in srgb, var(--accent) 26%, transparent);
    }

    .brand::after {
      content: 'Attendance • Projects • Daily Work';
      display: block;
      position: absolute;
      left: 50px;
      top: 25px;
      font-size: .67rem;
      font-weight: 650;
      letter-spacing: .03em;
      color: var(--text2);
      white-space: nowrap;
    }

    .nav-actions {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .theme-toggle {
      height: 36px;
      padding: 0 12px;
      border-radius: 999px;
      border: 1px solid var(--border2);
      background: var(--card2);
      color: var(--text);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: .78rem;
      font-weight: 750;
      box-shadow: 0 6px 16px rgba(15, 23, 42, .05);
      transition: transform .18s ease, border-color .18s ease, background .18s ease;
    }

    .theme-toggle:hover,
    .btn:hover {
      transform: translateY(-1px);
    }

    .theme-toggle i {
      color: var(--accent);
    }

    .nav-user {
      min-width: 0;
      padding: 7px 10px 7px 7px;
      border: 1px solid var(--border);
      border-radius: 999px;
      background: var(--card2);
    }

    .avatar {
      width: 36px;
      height: 36px;
      box-shadow: 0 12px 24px color-mix(in srgb, var(--accent) 22%, transparent);
    }

    .role-badge {
      border: 1px solid currentColor;
      background: color-mix(in srgb, currentColor 12%, transparent) !important;
      color: var(--accent) !important;
    }

    .shell {
      background: transparent;
    }

    .sidebar {
      width: 238px;
      background: color-mix(in srgb, var(--card) 90%, transparent);
      border-right: 1px solid var(--border);
      padding: 22px 12px;
      gap: 6px;
      backdrop-filter: blur(14px);
    }

    .nav-section {
      padding: 12px 12px 5px;
      color: var(--text3);
      font-size: .68rem;
      letter-spacing: .12em;
      font-weight: 800;
    }

    .nav-item {
      min-height: 42px;
      border-left: 0;
      border-radius: 14px;
      padding: 10px 12px;
      color: var(--text2);
      font-weight: 650;
      letter-spacing: -.01em;
    }

    .nav-item i {
      width: 30px;
      height: 30px;
      border-radius: 11px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: var(--card2);
      color: var(--text2);
    }

    .nav-item:hover {
      background: var(--surface-grad);
      color: var(--text);
    }

    .nav-item.active {
      background: linear-gradient(135deg, color-mix(in srgb, var(--accent) 16%, transparent), color-mix(in srgb, var(--accent2) 12%, transparent));
      color: var(--text);
      border: 1px solid color-mix(in srgb, var(--accent) 26%, transparent);
      box-shadow: 0 10px 24px color-mix(in srgb, var(--accent) 10%, transparent);
    }

    .nav-item.active i {
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: #fff;
    }

    .content {
      padding: 28px;
      overflow-x: hidden;
      scrollbar-width: thin;
      scrollbar-color: var(--border2) transparent;
    }

    .page.active {
      animation: fadeUp .22s ease;
    }

    .card,
    .hero,
    .login-box,
    .modal,
    .work-hero-card,
    .tbl-wrap {
      background: var(--card);
      border: 1px solid var(--border);
      box-shadow: var(--shadow-soft);
    }

    .card,
    .hero,
    .work-hero-card {
      border-radius: 24px;
    }

    .card {
      padding: 22px;
    }

    .card-title {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--text);
      font-size: .78rem;
      font-weight: 800;
      letter-spacing: .07em;
      margin-bottom: 16px;
    }

    .ph-row {
      gap: 16px;
      align-items: center;
    }

    .ph-title {
      font-size: clamp(1.25rem, 1.8vw, 1.9rem);
      line-height: 1.08;
      font-weight: 800;
      letter-spacing: -.04em;
      color: var(--text);
    }

    .ph-sub,
    .muted-small {
      color: var(--text2);
      line-height: 1.55;
      font-weight: 500;
    }

    .hero {
      min-height: 372px;
      padding: 52px 28px;
      background: var(--hero-grad);
      border-color: color-mix(in srgb, var(--accent) 14%, var(--border));
    }

    .hero::before,
    .work-hero-card::before {
      content: '';
      position: absolute;
      inset: 0;
      pointer-events: none;
      background: linear-gradient(180deg, rgba(255,255,255,.14), transparent 36%);
    }

    html[data-theme="dark"] .hero::before,
    html[data-theme="dark"] .work-hero-card::before {
      background: linear-gradient(180deg, rgba(255,255,255,.05), transparent 40%);
    }
    .clock {
      font-size: clamp(3rem, 6vw, 5.3rem);
      font-weight: 750;
      letter-spacing: -.06em;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .dateline {
      color: var(--text2);
      font-weight: 600;
      margin: 10px 0 18px;
    }

    .tz-badge,
    .loc-btn,
    .status-pill,
    .pill,
    .work-chip {
      border-radius: 999px;
      border: 1px solid color-mix(in srgb, var(--accent) 20%, var(--border));
      background: color-mix(in srgb, var(--accent) 9%, transparent);
      color: var(--accent);
      font-weight: 750;
    }

    .ci-row {
      margin: 22px auto 30px;
      padding: 12px;
      width: min(720px, 100%);
      gap: 12px;
      background: color-mix(in srgb, var(--card) 72%, transparent);
      border: 1px solid var(--border);
      border-radius: 22px;
      box-shadow: var(--shadow-soft);
    }

    .ci-item {
      flex: 1;
      padding: 13px 16px;
      border-radius: 16px;
      background: var(--card2);
      border: 1px solid var(--border);
    }

    .ci-item .t {
      color: var(--text);
      font-weight: 750;
    }

    .btn,
    .login-btn {
      border-radius: 12px;
      font-weight: 750;
      letter-spacing: -.01em;
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
    }

    .btn:not(.btn-outline):not(.btn-red):not(.btn-green):not(.btn-warn):not(.btn-purple),
    .btn-primary,
    .login-btn {
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: #fff;
      box-shadow: 0 12px 26px color-mix(in srgb, var(--accent) 18%, transparent);
    }

    .btn-outline {
      background: var(--card2);
      border-color: var(--border2);
      color: var(--text);
    }

    .btn-outline:hover {
      color: var(--accent);
      border-color: color-mix(in srgb, var(--accent) 52%, var(--border));
      background: color-mix(in srgb, var(--accent) 8%, var(--card2));
    }

    .btn-green {
      background: linear-gradient(135deg, #059669, #10b981);
      box-shadow: 0 12px 26px rgba(16, 185, 129, .18);
    }

    .btn-red {
      background: linear-gradient(135deg, #dc2626, #ef4444);
      box-shadow: 0 12px 26px rgba(239, 68, 68, .16);
    }

    .filters {
      padding: 12px;
      border-radius: 18px;
      background: color-mix(in srgb, var(--card2) 68%, transparent);
      border: 1px solid var(--border);
    }

    .fi,
    .form-group input,
    .form-group select,
    .form-group textarea {
      min-height: 42px;
      background: var(--card);
      border-color: var(--border2);
      color: var(--text);
      border-radius: 12px;
      font-weight: 550;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
    }

    .form-group textarea {
      min-height: 96px;
      resize: vertical;
    }

    .fi:focus,
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: color-mix(in srgb, var(--accent) 64%, var(--border));
      box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 12%, transparent);
    }

    .form-group label {
      color: var(--text2);
      font-size: .72rem;
      font-weight: 800;
    }

    .tbl-wrap {
      border-radius: 18px;
      overflow: auto;
      max-width: 100%;
    }

    table {
      min-width: 860px;
    }

    thead tr {
      background: var(--card2);
    }

    th {
      position: sticky;
      top: 0;
      z-index: 1;
      padding: 13px 15px;
      color: var(--text2);
      background: var(--card2);
      font-size: .68rem;
      font-weight: 850;
    }

    td {
      padding: 14px 15px;
      border-top: 1px solid var(--border);
      color: var(--text);
      vertical-align: middle;
    }

    tbody tr {
      transition: background .15s ease;
    }

    tbody tr:hover {
      background: color-mix(in srgb, var(--accent) 5%, transparent);
    }

    .stat,
    .work-mini {
      border-radius: 18px;
      background: var(--card2);
      border: 1px solid var(--border);
      box-shadow: 0 8px 20px rgba(15, 23, 42, .04);
    }

    .stat {
      padding: 20px;
    }

    .stat::before {
      height: 4px;
      background: linear-gradient(90deg, var(--c, var(--accent)), color-mix(in srgb, var(--c, var(--accent)) 40%, transparent));
    }

    .stat .val,
    .work-mini .v {
      color: var(--text);
      font-weight: 800;
    }

    .work-hero {
      grid-template-columns: minmax(0, 1.16fr) minmax(360px, .84fr);
      gap: 20px;
      margin-bottom: 22px;
    }

    .work-hero-card {
      padding: 28px;
      background: var(--hero-grad);
      border-color: color-mix(in srgb, var(--accent) 18%, var(--border));
      box-shadow: var(--shadow);
    }

    .work-hero-card::after {
      background: linear-gradient(135deg, color-mix(in srgb, var(--accent) 18%, transparent), color-mix(in srgb, var(--accent2) 12%, transparent));
      filter: blur(.2px);
    }



    .work-mini-grid {
      gap: 12px;
    }

    .work-mini {
      padding: 16px;
      min-height: 88px;
    }

    .work-mini .l {
      color: var(--text2);
      font-size: .68rem;
      font-weight: 850;
    }

    .work-form-grid {
      gap: 16px;
    }

    .progress-line {
      height: 10px;
      background: color-mix(in srgb, var(--text3) 18%, transparent);
    }

    .progress-line span {
      background: linear-gradient(90deg, var(--accent), var(--green));
    }

    .modal-bg {
      background: rgba(15, 23, 42, .56);
      backdrop-filter: blur(12px);
    }

    .modal {
      border-radius: 24px;
      box-shadow: 0 28px 90px rgba(0, 0, 0, .28);
    }

    .login-screen {
      background:
        radial-gradient(circle at 18% 15%, color-mix(in srgb, var(--accent) 18%, transparent), transparent 34%),
        radial-gradient(circle at 80% 84%, color-mix(in srgb, var(--accent2) 16%, transparent), transparent 34%),
        var(--bg);
    }

    .login-box {
      width: min(430px, calc(100vw - 34px));
      background: color-mix(in srgb, var(--card) 96%, transparent);
      padding: 42px 36px;
    }

    .login-logo .li {
      width: 60px;
      height: 60px;
      margin: 0 auto 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 22px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: #fff;
      -webkit-text-fill-color: currentColor;
      box-shadow: 0 16px 34px color-mix(in srgb, var(--accent) 22%, transparent);
    }

    .login-logo .b {
      color: var(--text);
      background: none;
      -webkit-text-fill-color: currentColor;
      font-weight: 850;
      letter-spacing: -.05em;
    }

    .toast {
      border-radius: 16px;
      box-shadow: var(--shadow);
      font-weight: 700;
    }

    @supports not (color: color-mix(in srgb, #000 50%, #fff)) {
      .brand i,
      .btn-primary,
      .login-btn,
      .work-tab.active,
      .nav-item.active i { background: linear-gradient(135deg, var(--accent), var(--accent2)); }
      .card,
      .hero,
      .work-hero-card { box-shadow: var(--shadow-soft); }
    }

    @media (max-width:1199px), (hover:none) and (pointer:coarse) {
      html,
      body {
        overflow: hidden !important;
      }

      .attend-device-blocker {
        display: none !important;
      }

      body.wh-guest .login-screen {
        display: flex !important;
      }

      body.wh-guest .app {
        display: none !important;
      }

      body.wh-logged-in .login-screen {
        display: none !important;
      }

      body.wh-logged-in .app {
        display: flex !important;
      }

      body.wh-logged-in .modal-bg.hidden,
      body.wh-guest .modal-bg.hidden {
        display: none !important;
      }

      .topnav {
        padding: 10px 14px;
        gap: 10px;
      }

      .brand::after,
      .nav-info,
      #themeText {
        display: none;
      }

      .nav-user {
        padding: 4px;
      }

      .shell {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        flex-direction: row;
        overflow-x: auto;
        border-right: 0;
        border-bottom: 1px solid var(--border);
        padding: 10px;
        gap: 8px;
      }

      .nav-section {
        display: none;
      }

      .nav-item {
        white-space: nowrap;
        flex: 0 0 auto;
      }

      .content {
        padding: 16px;
      }

      .g2,
      .g3,
      .g4,
      .work-hero,
      .work-form-grid,
      .work-form-grid.two {
        grid-template-columns: 1fr;
      }

      .work-mini-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .ci-row {
        flex-direction: column;
      }
    }

    @media (max-width:640px) {
      .topnav {
        flex-wrap: wrap;
      }

      .nav-spacer {
        display: none;
      }

      .nav-actions {
        margin-left: auto;
      }

      .hero {
        padding: 34px 18px;
      }

      .work-mini-grid {
        grid-template-columns: 1fr;
      }

      .filters,
      .action-row,
      .modal-actions {
        flex-direction: column;
        align-items: stretch;
      }

      .btn,
      .fi {
        width: 100%;
      }
    }



    /* ─────────────────────────────────────────────────────────
       CLEAN LAYOUT OVERRIDES  (single canonical block)
       ───────────────────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; }
    html, body { max-width: 100%; overflow-x: hidden; }
    .content, .page, .card, .tbl-wrap, .work-hero,
    .work-board, .cc-two-col { min-width: 0; }
    .page  { width: 100%; }
    .card  { border-radius: 22px; }
    .btn   { align-items: center; justify-content: center; white-space: nowrap; }
    .btn i { line-height: 1; }

    /* Work hero */
    #page-work { gap: 18px; }
    .work-hero {
      display: grid;
      grid-template-columns: 1fr 340px;
      gap: 18px;
      margin-bottom: 0;
      align-items: start;
    }
    .work-hero-card {
      padding: 28px 32px;
      background: var(--hero-grad);
      border-color: color-mix(in srgb, var(--accent) 18%, var(--border));
      box-shadow: var(--shadow);
      overflow: hidden;
      border-radius: 26px;
      min-height: 200px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .work-hero-card::after {
      background: linear-gradient(135deg,
        color-mix(in srgb, var(--accent) 14%, transparent),
        color-mix(in srgb, var(--accent2) 10%, transparent));
      filter: blur(.5px);
    }
    .work-hero-card .ph-title {
      font-size: clamp(1.25rem, 1.9vw, 1.85rem);
      letter-spacing: -.04em;
      line-height: 1.08;
    }
    .work-hero-card .ph-sub {
      line-height: 1.55;
      margin-top: 6px;
      font-size: .88rem;
      color: var(--text2);
    }

    /* Work tabs — single definition, no conflicts */
    .work-tabs {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 8px;
      margin-top: 18px;
      position: relative;
      z-index: 2;
    }
    .work-tab {
      height: 38px;
      padding: 0 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      line-height: 1;
      font-size: .79rem;
      font-weight: 750;
      border-radius: 999px;
      border: 1px solid var(--border2);
      background: var(--card);
      color: var(--text2);
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(15,23,42,.05);
      transition: all .18s ease;
      letter-spacing: .01em;
    }
    .work-tab.active,
    .work-tab:hover {
      color: #fff;
      border-color: transparent;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      box-shadow: 0 8px 20px color-mix(in srgb, var(--accent) 22%, transparent);
      transform: translateY(-1px);
    }
    html[data-theme="light"] .work-tab {
      background: #fff;
      border-color: var(--border2);
      color: var(--text2);
      box-shadow: 0 2px 8px rgba(59,91,219,.06);
    }
    html[data-theme="light"] .work-tab.active,
    html[data-theme="light"] .work-tab:hover {
      color: #fff;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      border-color: transparent;
      box-shadow: 0 6px 18px rgba(59,91,219,.25);
    }

    /* Work board & forms */
    .work-board {
      display: grid;
      grid-template-columns: 1fr;
      gap: 18px;
    }
    .work-form-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0,1fr));
      gap: 16px;
      align-items: start;
    }
    .work-form-grid .full   { grid-column: 1 / -1; }
    .work-form-grid .span-2 { grid-column: span 2; }
    .work-form-grid .split-2 {
      display: grid;
      grid-template-columns: repeat(2, minmax(0,1fr));
      gap: 14px;
    }
    .form-group label {
      display: flex;
      align-items: center;
      min-height: 18px;
      font-weight: 700;
      margin-bottom: 7px;
      color: var(--text);
      font-size: .82rem;
    }

    /* Work mini KPI strip */
    .work-mini-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0,1fr));
      gap: 10px;
    }
    .work-mini {
      border: 1px solid var(--border);
      background: var(--bg2);
      border-radius: 14px;
      padding: 12px 14px;
    }
    .work-mini .v {
      font-size: 1.25rem;
      font-weight: 900;
      color: var(--accent);
    }
    .work-mini .l {
      margin-top: 3px;
      color: var(--text2);
      font-size: .7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em;
    }
    html[data-theme="light"] .work-mini {
      background: #f5f8ff;
      border-color: var(--border);
    }

    /* Toast refinement */
    .toast { border-radius: 16px; box-shadow: var(--shadow); font-weight: 700; }

    /* @supports fallback */
    @supports not (color: color-mix(in srgb, #000 50%, #fff)) {
      .brand i, .btn-primary, .login-btn,
      .work-tab.active, .nav-item.active i {
        background: linear-gradient(135deg, var(--accent), var(--accent2));
      }
      .card, .hero, .work-hero-card { box-shadow: var(--shadow-soft); }
    }

    /* ── Command-center hero ── */
    .cc-command-hero {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 20px 24px;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--r);
      margin-bottom: 18px;
    }
    .cc-command-hero h2 { font-size: 1.05rem; font-weight: 750; margin: 4px 0 6px; }
    .cc-command-hero p  { font-size: .82rem; color: var(--text2); margin: 0; }
    .cc-eyebrow {
      font-size: .7rem; font-weight: 800; letter-spacing: .08em;
      text-transform: uppercase; color: var(--accent); margin-bottom: 4px;
    }

    /* ── Responsive ── */
    @media (max-width: 1280px) {
      .work-hero { grid-template-columns: 1fr; }
      .work-form-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
    }
    @media (max-width: 720px) {
      .work-form-grid { grid-template-columns: 1fr; }
      .work-form-grid .span-2 { grid-column: 1 / -1; }
      .work-form-grid .split-2 { grid-template-columns: 1fr; }
      .cc-two-col { grid-template-columns: 1fr; }
      .cc-kpi-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
      .cc-filter-row { flex-direction: column; align-items: stretch; }
      .cc-fi, .cc-fi-search { min-width: 0; width: 100%; }
      .work-hero-card, .cc-command-hero { padding: 18px; }
      .cc-command-hero { flex-direction: column; }
      .work-tab { flex: 1 1 auto; }
      .cc-tl-update-header { align-items: flex-start; flex-wrap: wrap; }
      .cc-tl-empname, .cc-tl-proj { max-width: calc(100vw - 110px); }
      table { min-width: 680px; }
    }

  

    /* ════════════════════════════════════════════════════════
       WORKHIVE LOGIN V2 — modern, clean, pixel-focused layer
       Safe front-end only: existing lRole/lEmpId/lPass/lErr/doLogin retained.
       ════════════════════════════════════════════════════════ */
    .wh-login {
      /* Premium teal + ivory login palette only. Keeps the main app theme untouched. */
      --bg: #FFF7EF;
      --bg2: #FFF0E4;
      --card: #FFFFFF;
      --card2: #FFE7D2;
      --border: rgba(0, 121, 121, .14);
      --border2: rgba(0, 121, 121, .24);
      --accent: #007979;
      --accent2: #24B1B1;
      --green: #168A68;
      --red: #D94848;
      --yellow: #B87514;
      --purple: #0E8D9B;
      --text: #142A2A;
      --text2: #5D6D6A;
      --text3: #9A8B7E;
      --shadow: 0 24px 74px rgba(0, 121, 121, .16);
      --shadow-soft: 0 12px 34px rgba(0, 121, 121, .10);

      position: fixed;
      inset: 0;
      z-index: 500;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 28px;
      overflow: hidden;
      isolation: isolate;
      background:
        radial-gradient(circle at 16% 14%, rgba(36, 177, 177, .22), transparent 30%),
        radial-gradient(circle at 88% 80%, rgba(255, 224, 197, .78), transparent 34%),
        radial-gradient(circle at 54% -8%, rgba(0, 121, 121, .14), transparent 34%),
        linear-gradient(145deg, #FFF7EF 0%, #FFF0E4 48%, #F8FFFC 100%);
    }

    html[data-theme="dark"] .wh-login {
      --bg: #061818;
      --bg2: #0A2424;
      --card: rgba(8, 28, 28, .92);
      --card2: rgba(13, 45, 45, .72);
      --border: rgba(117, 255, 241, .13);
      --border2: rgba(117, 255, 241, .22);
      --accent: #2DD4BF;
      --accent2: #5EEAD4;
      --green: #34D399;
      --red: #FB7185;
      --yellow: #FBBF24;
      --purple: #67E8F9;
      --text: #F4FFFD;
      --text2: #A7C4BF;
      --text3: #6F918A;
      background:
        radial-gradient(circle at 18% 16%, rgba(45, 212, 191, .18), transparent 31%),
        radial-gradient(circle at 86% 78%, rgba(94, 234, 212, .12), transparent 34%),
        linear-gradient(145deg, #061818 0%, #0A2424 52%, #071313 100%);
    }

    .wh-login::before,
    .wh-login::after {
      content: '';
      position: absolute;
      pointer-events: none;
      z-index: -3;
    }

    .wh-login::before {
      inset: -1px;
      background-image:
        linear-gradient(color-mix(in srgb, var(--accent) 7%, transparent) 1px, transparent 1px),
        linear-gradient(90deg, color-mix(in srgb, var(--accent2) 6%, transparent) 1px, transparent 1px);
      background-size: 48px 48px;
      mask-image: radial-gradient(circle at 50% 48%, #000 0 40%, transparent 72%);
      opacity: .64;
      animation: whGridDrift 20s linear infinite;
    }

    .wh-login::after {
      width: 760px;
      height: 760px;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      border-radius: 999px;
      border: 1px solid color-mix(in srgb, var(--accent) 12%, transparent);
      box-shadow:
        0 0 0 72px color-mix(in srgb, var(--accent) 3%, transparent),
        0 0 0 152px color-mix(in srgb, var(--accent2) 2.5%, transparent);
      opacity: .82;
      animation: whBackdropRing 28s linear infinite;
    }

    .login-ambient {
      position: absolute;
      inset: 0;
      overflow: hidden;
      z-index: -2;
      pointer-events: none;
    }

    .orb {
      position: absolute;
      width: 310px;
      height: 310px;
      border-radius: 999px;
      filter: blur(24px);
      opacity: .58;
      animation: whOrbFloat 9s ease-in-out infinite;
      will-change: transform;
    }

    .orb-a {
      left: 5%;
      top: 11%;
      background: radial-gradient(circle, color-mix(in srgb, var(--accent) 26%, transparent), transparent 66%);
    }

    .orb-b {
      right: 8%;
      bottom: 7%;
      animation-delay: -3.4s;
      background: radial-gradient(circle, color-mix(in srgb, var(--accent2) 24%, transparent), transparent 68%);
    }

    .orb-c {
      right: 33%;
      top: -10%;
      width: 240px;
      height: 240px;
      animation-delay: -5.8s;
      background: radial-gradient(circle, color-mix(in srgb, var(--green) 18%, transparent), transparent 70%);
    }

    .pulse-dot {
      position: absolute;
      width: 8px;
      height: 8px;
      border-radius: 999px;
      background: currentColor;
      color: var(--accent);
      opacity: .75;
      box-shadow: 0 0 0 0 color-mix(in srgb, currentColor 26%, transparent);
      animation: whPulse 2.5s ease-out infinite;
    }

    .pd-1 { left: 18%; top: 25%; }
    .pd-2 { right: 18%; top: 26%; color: var(--accent2); animation-delay: .8s; }
    .pd-3 { left: 47%; bottom: 17%; color: var(--green); animation-delay: 1.35s; }

    .login-shell {
      width: min(1040px, 100%);
      min-height: 620px;
      display: grid;
      grid-template-columns: minmax(0, 1fr) 420px;
      gap: 18px;
      align-items: stretch;
      animation: whLoginEnter .62s cubic-bezier(.22, 1, .36, 1) both;
    }

    .login-visual,
    .premium-login-card {
      position: relative;
      overflow: hidden;
      border-radius: 32px;
      border: 1px solid color-mix(in srgb, var(--border) 82%, #fff);
      backdrop-filter: blur(22px);
      -webkit-backdrop-filter: blur(22px);
      box-shadow:
        0 32px 90px color-mix(in srgb, var(--accent) 13%, transparent),
        inset 0 1px 0 color-mix(in srgb, #fff 42%, transparent);
    }

    .login-visual {
      min-height: 620px;
      padding: 26px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background:
        radial-gradient(circle at 18% 16%, rgba(255,240,228,.28), transparent 25%),
        radial-gradient(circle at 86% 14%, rgba(255,255,255,.16), transparent 30%),
        radial-gradient(circle at 78% 86%, rgba(36,177,177,.30), transparent 36%),
        linear-gradient(135deg, #005E5E 0%, #007979 42%, #24B1B1 100%);
      color: #fff;
    }

    .login-visual::before {
      content: '';
      position: absolute;
      inset: 0;
      background:
        linear-gradient(115deg, transparent 0 30%, rgba(255,255,255,.20) 43%, transparent 56%),
        radial-gradient(circle at 75% 70%, rgba(255,255,255,.14), transparent 34%);
      transform: translateX(-18%);
      animation: whPanelSheen 7.5s ease-in-out infinite;
      opacity: .72;
    }

    .login-visual::after {
      content: '';
      position: absolute;
      right: -150px;
      bottom: -150px;
      width: 420px;
      height: 420px;
      border-radius: 999px;
      border: 1px solid rgba(255,255,255,.28);
      box-shadow:
        inset 0 0 0 48px rgba(255,255,255,.055),
        inset 0 0 0 118px rgba(255,255,255,.04);
    }

    .brand-lockup,
    .login-hero-copy,
    .login-metrics {
      position: relative;
      z-index: 1;
    }

    .brand-lockup {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
    }

    .brand-lockup > div:last-child {
      margin-right: auto;
    }

    .brand-mark {
      width: 52px;
      height: 52px;
      display: grid;
      place-items: center;
      border-radius: 18px;
      background: rgba(255,255,255,.15);
      border: 1px solid rgba(255,255,255,.24);
      box-shadow: 0 16px 42px rgba(15,23,42,.16);
      font-size: 1.22rem;
    }

    .brand-title {
      font-size: 1.25rem;
      font-weight: 850;
      letter-spacing: -.055em;
      line-height: 1;
    }

    .brand-subtitle {
      margin-top: 5px;
      color: rgba(255,255,255,.68);
      font-size: .76rem;
      font-weight: 700;
      letter-spacing: .02em;
    }

    .login-hero-copy {
      align-self: center;
      width: min(430px, 100%);
      aspect-ratio: 1 / .92;
      display: grid;
      place-items: center;
      margin: 24px auto;
    }

    .login-focus-orbit {
      position: relative;
      width: 340px;
      height: 340px;
      display: grid;
      place-items: center;
      border-radius: 999px;
      background:
        radial-gradient(circle at 50% 50%, rgba(255,255,255,.18), transparent 47%),
        conic-gradient(from 220deg, transparent 0 16%, rgba(255,255,255,.30) 19%, transparent 24%, transparent 56%, rgba(255,255,255,.22) 61%, transparent 66%);
      box-shadow: inset 0 0 0 1px rgba(255,255,255,.18);
      
    }

    .login-focus-orbit::before,
    .login-focus-orbit::after {
      content: '';
      position: absolute;
      border-radius: inherit;
      border: 1px solid rgba(255,255,255,.22);
    }

    .login-focus-orbit::before {
      inset: 42px;
      animation: whSpin 18s linear infinite reverse;
    }

    .login-focus-orbit::after {
      inset: 88px;
      background: rgba(255,255,255,.10);
      backdrop-filter: blur(12px);
    }

    .focus-core {
      position: relative;
      z-index: 2;
      width: 122px;
      height: 122px;
      display: grid;
      place-items: center;
      border-radius: 38px;
      background: rgba(255,255,255,.18);
      border: 1px solid rgba(255,255,255,.30);
      color: #fff;
      font-size: 3.15rem;
      box-shadow: 0 24px 60px rgba(15,23,42,.22);
      animation: whCoreFloat 3.4s ease-in-out infinite;
    }

    .orbit-chip {
      position: absolute;
      z-index: 3;
      min-width: 82px;
      height: 38px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      border-radius: 999px;
      background: rgba(255,255,255,.16);
      border: 1px solid rgba(255,255,255,.24);
      color: rgba(255,255,255,.92);
      font-size: .72rem;
      font-weight: 850;
      letter-spacing: .015em;
      backdrop-filter: blur(14px);
    }

    .orbit-chip.a { left: -8px; top: 92px; }
    .orbit-chip.b { right: -6px; top: 140px; }
    .orbit-chip.c { left: 128px; bottom: -6px; }

    .login-metrics {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 10px;
    }

    .metric-card {
      min-height: 86px;
      padding: 14px;
      border-radius: 20px;
      background: rgba(255,255,255,.13);
      border: 1px solid rgba(255,255,255,.18);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform .18s ease, background .18s ease;
    }

    .metric-card:hover {
      transform: translateY(-3px);
      background: rgba(255,255,255,.18);
    }

    .metric-card i {
      width: 34px;
      height: 34px;
      display: grid;
      place-items: center;
      border-radius: 13px;
      background: rgba(255,255,255,.17);
      font-size: .9rem;
    }

    .metric-card span {
      color: rgba(255,255,255,.90);
      font-size: .72rem;
      font-weight: 850;
      letter-spacing: -.01em;
    }

    .premium-login-card {
      width: 100%;
      min-height: 620px;
      padding: 34px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background:
        linear-gradient(180deg, color-mix(in srgb, var(--card) 94%, transparent), color-mix(in srgb, var(--card) 88%, transparent)),
        color-mix(in srgb, var(--card) 92%, transparent);
    }

    .premium-login-card::before {
      content: '';
      position: absolute;
      inset: 0;
      pointer-events: none;
      background:
        radial-gradient(circle at 50% 0%, color-mix(in srgb, var(--accent) 12%, transparent), transparent 36%),
        linear-gradient(180deg, color-mix(in srgb, #fff 32%, transparent), transparent 36%);
      opacity: .75;
    }

    .premium-login-card > * {
      position: relative;
      z-index: 1;
    }

    .login-card-top {
      text-align: left;
      margin-bottom: 24px;
    }

    .login-icon-ring {
      width: 58px;
      height: 58px;
      margin-bottom: 18px;
      display: grid;
      place-items: center;
      border-radius: 20px;
      color: #fff;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      box-shadow:
        0 18px 44px color-mix(in srgb, var(--accent) 22%, transparent),
        0 0 0 8px color-mix(in srgb, var(--accent) 7%, transparent);
      font-size: 1.42rem;
      animation: whCoreFloat 3.4s ease-in-out infinite;
    }

    .login-card-title {
      color: var(--text);
      font-size: 1.92rem;
      line-height: 1;
      font-weight: 850;
      letter-spacing: -.07em;
    }

    .login-card-top p {
      margin-top: 8px;
      color: var(--text2);
      font-size: .88rem;
      font-weight: 650;
      line-height: 1.45;
    }

    .login-live-chip {
      position: absolute;
      top: 28px;
      right: 28px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 10px;
      border-radius: 999px;
      border: 1px solid color-mix(in srgb, var(--accent) 16%, var(--border));
      background: color-mix(in srgb, var(--card) 82%, transparent);
      color: var(--accent);
      box-shadow: 0 10px 24px color-mix(in srgb, var(--accent) 7%, transparent);
      font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, monospace;
      font-size: .70rem;
      font-weight: 850;
      white-space: nowrap;
    }

    .login-role-group {
      margin-bottom: 17px;
    }

    .login-role-select {
      position: absolute !important;
      width: 1px !important;
      height: 1px !important;
      opacity: 0 !important;
      pointer-events: none !important;
    }

    .role-choice-grid {
      display: grid;
      grid-template-columns: repeat(5, minmax(0, 1fr));
      gap: 8px;
    }

    .role-choice {
      min-height: 60px;
      border: 1px solid var(--border);
      border-radius: 17px;
      background: color-mix(in srgb, var(--card2) 94%, var(--card));
      color: var(--text2);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 6px;
      cursor: pointer;
      font: inherit;
      font-size: .67rem;
      font-weight: 850;
      letter-spacing: -.01em;
      transition:
        transform .16s ease,
        box-shadow .16s ease,
        border-color .16s ease,
        color .16s ease,
        background .16s ease;
    }

    .role-choice i {
      font-size: .94rem;
    }

    .role-choice:hover {
      transform: translateY(-2px);
      border-color: color-mix(in srgb, var(--accent) 42%, var(--border));
      color: var(--accent);
      box-shadow: 0 12px 24px color-mix(in srgb, var(--accent) 9%, transparent);
    }

    .role-choice.active {
      background:
        linear-gradient(145deg, color-mix(in srgb, var(--accent) 12%, var(--card)), color-mix(in srgb, var(--accent2) 9%, var(--card)));
      color: var(--accent);
      border-color: color-mix(in srgb, var(--accent) 52%, var(--border));
      box-shadow:
        0 13px 26px color-mix(in srgb, var(--accent) 12%, transparent),
        inset 0 1px 0 color-mix(in srgb, #fff 40%, transparent);
    }

    .smart-field {
      margin-bottom: 14px;
    }

    .smart-field label,
    .login-role-group label {
      display: block;
      margin-bottom: 8px;
      color: var(--text2);
      font-size: .67rem;
      font-weight: 850;
      letter-spacing: .095em;
      text-transform: uppercase;
    }

    .input-shell {
      min-height: 54px;
      display: flex;
      align-items: center;
      gap: 11px;
      border: 1px solid var(--border);
      border-radius: 18px;
      background: color-mix(in srgb, var(--card2) 94%, var(--card));
      padding: 0 14px;
      transition:
        border-color .16s ease,
        box-shadow .16s ease,
        background .16s ease,
        transform .16s ease;
    }

    .input-shell:focus-within {
      transform: translateY(-1px);
      border-color: color-mix(in srgb, var(--accent) 54%, var(--border));
      background: var(--card);
      box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 10%, transparent);
    }

    .input-shell > i {
      width: 20px;
      text-align: center;
      color: var(--accent);
      font-size: .94rem;
    }

    .input-shell input {
      flex: 1;
      min-width: 0;
      height: 52px;
      padding: 0 !important;
      border: 0 !important;
      outline: none !important;
      box-shadow: none !important;
      background: transparent !important;
      color: var(--text);
      font-size: .94rem;
      font-weight: 700;
      letter-spacing: -.01em;
    }

    .input-shell input::placeholder {
      color: var(--text3);
      font-weight: 550;
    }

    .password-toggle {
      width: 36px;
      height: 36px;
      border: 0;
      border-radius: 13px;
      background: transparent;
      color: var(--text2);
      cursor: pointer;
      transition: background .15s ease, color .15s ease;
    }

    .password-toggle:hover {
      background: color-mix(in srgb, var(--accent) 9%, transparent);
      color: var(--accent);
    }

    .premium-signin {
      position: relative;
      min-height: 54px;
      margin-top: 7px;
      border-radius: 18px;
      overflow: hidden;
      isolation: isolate;
      box-shadow: 0 18px 36px color-mix(in srgb, var(--accent) 18%, transparent);
    }

    .premium-signin.btn-primary,
    .premium-signin {
      background: linear-gradient(135deg, #007979 0%, #0C9B9B 48%, #24B1B1 100%);
      color: #fff;
      border: 0;
    }


    .premium-signin::before {
      content: '';
      position: absolute;
      inset: 0;
      z-index: -1;
      transform: translateX(-125%);
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.28), transparent);
      transition: transform .68s ease;
    }

    .premium-signin:hover::before {
      transform: translateX(125%);
    }

    .premium-signin .btn-loading {
      display: none;
    }

    .premium-signin.is-loading {
      pointer-events: none;
      opacity: .88;
    }

    .premium-signin.is-loading .btn-ready {
      display: none;
    }

    .premium-signin.is-loading .btn-loading {
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .premium-login-card .login-error {
      margin: 12px 0 0;
      padding: 10px 12px;
      border-radius: 14px;
      background: color-mix(in srgb, var(--red) 9%, transparent);
      border: 1px solid color-mix(in srgb, var(--red) 20%, transparent);
      color: var(--red);
      font-size: .78rem;
      font-weight: 800;
      text-align: center;
    }

    .login-footnote {
      margin-top: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      color: var(--text2);
      font-size: .72rem;
      font-weight: 800;
      white-space: nowrap;
    }

    .login-footnote span {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 10px;
      border-radius: 999px;
      background: color-mix(in srgb, var(--card2) 84%, transparent);
      border: 1px solid var(--border);
    }

    .login-footnote i {
      color: var(--green);
    }

    html[data-theme="dark"] .premium-login-card {
      background: rgba(15, 23, 42, .78);
      border-color: rgba(148, 163, 184, .18);
      box-shadow: 0 34px 110px rgba(0,0,0,.44), inset 0 1px 0 rgba(255,255,255,.05);
    }

    html[data-theme="dark"] .login-visual {
      box-shadow: 0 34px 110px rgba(0,0,0,.36), inset 0 1px 0 rgba(255,255,255,.08);
    }

    html[data-theme="dark"] .input-shell,
    html[data-theme="dark"] .role-choice {
      background: rgba(15, 23, 42, .62);
      border-color: rgba(148, 163, 184, .16);
    }

    @keyframes whLoginEnter {
      from { opacity: 0; transform: translateY(18px) scale(.985); filter: blur(8px); }
      to { opacity: 1; transform: translateY(0) scale(1); filter: blur(0); }
    }

    @keyframes whGridDrift {
      from { background-position: 0 0, 0 0; }
      to { background-position: 48px 48px, 48px 48px; }
    }

    @keyframes whBackdropRing {
      from { transform: translate(-50%, -50%) rotate(0deg); }
      to { transform: translate(-50%, -50%) rotate(360deg); }
    }

    @keyframes whSpin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    @keyframes whPanelSheen {
      0%, 100% { transform: translateX(-22%); opacity: .48; }
      50% { transform: translateX(22%); opacity: .84; }
    }

    @keyframes whOrbFloat {
      0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
      50% { transform: translate3d(18px, -18px, 0) scale(1.05); }
    }

    @keyframes whPulse {
      0% { box-shadow: 0 0 0 0 color-mix(in srgb, currentColor 26%, transparent); opacity: .82; }
      70% { box-shadow: 0 0 0 18px transparent; opacity: .16; }
      100% { box-shadow: 0 0 0 0 transparent; opacity: .82; }
    }

    @keyframes whCoreFloat {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-4px); }
    }

    @media (max-width: 1040px) {
      .wh-login {
        padding: 18px;
        align-items: flex-start;
        overflow-y: auto;
      }

      .login-shell {
        grid-template-columns: 1fr;
        min-height: 0;
      }

      .login-visual {
        min-height: 330px;
      }

      .login-hero-copy {
        margin: 12px auto;
      }

      .login-focus-orbit {
        width: 260px;
        height: 260px;
      }

      .focus-core {
        width: 96px;
        height: 96px;
        border-radius: 30px;
        font-size: 2.35rem;
      }

      .orbit-chip.a { left: -18px; top: 76px; }
      .orbit-chip.b { right: -18px; top: 116px; }
      .orbit-chip.c { left: 92px; bottom: -12px; }

      .premium-login-card {
        min-height: 0;
      }
    }

    @media (max-width: 620px) {
      .wh-login {
        padding: 14px;
      }

      .login-visual {
        display: none;
      }

      .login-shell {
        width: 100%;
      }

      .premium-login-card {
        min-height: auto;
        padding: 26px 18px 22px;
        border-radius: 26px;
      }

      .login-card-top {
        text-align: center;
        margin-bottom: 22px;
      }

      .login-icon-ring {
        margin: 0 auto 16px;
      }

      .login-live-chip {
        position: static;
        margin: 14px auto 0;
        width: max-content;
      }

      .role-choice-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .role-choice:last-child {
        grid-column: 1 / -1;
      }

      .login-footnote {
        flex-wrap: wrap;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .wh-login::before,
      .wh-login::after,
      .orb,
      .pulse-dot,
      .login-visual::before,
      .focus-core,
      .login-icon-ring,
      .login-shell {
        animation: none !important;
      }
    }



    /* ═══════════════════════════════════════════════════════════════
       WorkHive Final Enterprise UI Stability Patch
       Purpose: premium alignment, responsive shell, zero page X-scroll.
       Safe scope: CSS-only visual/layout corrections. Existing IDs, JS hooks,
       API endpoints, forms and render targets remain untouched.
    ═══════════════════════════════════════════════════════════════ */
    :root {
      --sidebar-w: 268px;
      --topnav-h: 68px;
      --focus-ring: 0 0 0 4px color-mix(in srgb, var(--accent) 16%, transparent);
    }

    html,
    body {
      width: 100%;
      max-width: 100%;
      min-width: 0;
      overflow: hidden !important;
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
      text-rendering: optimizeLegibility;
      -webkit-font-smoothing: antialiased;
    }

    *,
    *::before,
    *::after {
      min-width: 0;
    }

    .mono,
    .clock,
    .stat .val,
    .nav-info .eid,
    code,
    pre {
      font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      pointer-events: none;
      z-index: -1;
      background:
        radial-gradient(circle at 12% 10%, color-mix(in srgb, var(--accent) 12%, transparent), transparent 30%),
        radial-gradient(circle at 88% 14%, color-mix(in srgb, var(--accent2) 10%, transparent), transparent 30%),
        linear-gradient(180deg, color-mix(in srgb, var(--bg) 98%, #fff 2%), var(--bg));
    }

    .app,
    .shell,
    .content,
    .topnav,
    .sidebar,
    .page,
    .card,
    .hero,
    .work-hero,
    .work-hero-card,
    .modal,
    .login-screen,
    .login-shell,
    .login-box,
    .tbl-wrap {
      max-width: 100%;
    }

    .app {
      width: 100%;
      height: 100dvh;
      min-height: 0;
      overflow: hidden;
      background: transparent;
    }

    .topnav {
      height: var(--topnav-h);
      padding: 0 24px;
      gap: 16px;
      background: color-mix(in srgb, var(--card) 94%, transparent) !important;
      border-bottom: 1px solid var(--border);
      box-shadow: 0 10px 30px color-mix(in srgb, var(--accent) 8%, transparent);
      backdrop-filter: blur(18px);
      position: relative;
      z-index: 20;
    }

    .brand {
      min-width: max-content;
      font-size: 1.05rem;
      letter-spacing: -.025em;
      color: var(--text);
    }

    .brand i {
      width: 36px;
      height: 36px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 14px;
      color: #fff;
      -webkit-text-fill-color: currentColor;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      box-shadow: 0 14px 26px color-mix(in srgb, var(--accent) 24%, transparent);
    }

    .nav-user {
      padding: 6px 10px 6px 6px;
      border: 1px solid var(--border);
      border-radius: 999px;
      background: color-mix(in srgb, var(--card2) 68%, transparent);
      max-width: 360px;
    }

    .nav-info {
      min-width: 0;
      max-width: 170px;
    }

    .nav-info .nm,
    .nav-info .eid {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .avatar {
      width: 34px;
      height: 34px;
      box-shadow: inset 0 0 0 1px rgba(255,255,255,.28), 0 10px 24px color-mix(in srgb, var(--accent) 20%, transparent);
      flex: 0 0 auto;
    }

    .role-badge {
      flex: 0 0 auto;
      border: 1px solid currentColor;
      border-color: color-mix(in srgb, currentColor 20%, transparent);
    }

    .nav-actions {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      flex: 0 0 auto;
    }

    .theme-toggle,
    .nav-actions .btn,
    .btn {
      min-height: 38px;
      white-space: nowrap;
    }

    .shell {
      width: 100%;
      min-height: 0;
      overflow: hidden;
      display: grid;
      grid-template-columns: var(--sidebar-w) minmax(0, 1fr);
    }

    .sidebar {
      width: auto;
      min-width: 0;
      height: calc(100dvh - var(--topnav-h));
      overflow-y: auto;
      overflow-x: hidden;
      padding: 18px 14px;
      background: color-mix(in srgb, var(--card) 92%, transparent) !important;
      border-right: 1px solid var(--border);
      box-shadow: 18px 0 38px color-mix(in srgb, var(--accent) 5%, transparent);
      backdrop-filter: blur(18px);
      scrollbar-width: thin;
    }

    .nav-section {
      padding: 12px 14px 8px;
      margin: 6px 0 4px;
      color: var(--text3);
    }

    .nav-item {
      min-height: 44px;
      width: 100%;
      padding: 11px 14px;
      border-left: 0 !important;
      border-radius: 15px;
      gap: 12px;
      color: var(--text2);
      white-space: normal;
      line-height: 1.25;
    }

    .nav-item i {
      width: 34px;
      height: 34px;
      flex: 0 0 34px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
      background: color-mix(in srgb, var(--bg2) 80%, transparent);
      color: var(--text2);
      transition: .18s ease;
    }

    .nav-item:hover {
      background: color-mix(in srgb, var(--accent) 8%, transparent);
      color: var(--text);
      transform: translateX(2px);
    }

    .nav-item.active {
      background: linear-gradient(135deg, color-mix(in srgb, var(--accent) 15%, transparent), color-mix(in srgb, var(--accent2) 11%, transparent)) !important;
      color: var(--accent) !important;
      box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--accent) 16%, transparent), 0 12px 26px color-mix(in srgb, var(--accent) 9%, transparent);
    }

    .nav-item.active i {
      color: #fff;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      box-shadow: 0 10px 20px color-mix(in srgb, var(--accent) 22%, transparent);
    }

    .content {
      width: 100%;
      min-width: 0;
      min-height: 0;
      height: calc(100dvh - var(--topnav-h));
      overflow-y: auto;
      overflow-x: hidden !important;
      padding: 26px;
      scroll-behavior: smooth;
      scrollbar-width: thin;
    }

    .page {
      width: 100%;
      min-width: 0;
    }

    .page.active {
      display: block;
    }

    .card,
    .hero,
    .work-hero-card,
    .modal,
    .login-box,
    .emp-detail-panel {
      border-radius: 24px;
      border-color: var(--border);
      box-shadow: var(--shadow-soft);
    }

    .card,
    .hero,
    .work-hero-card {
      background: color-mix(in srgb, var(--card) 94%, transparent);
    }

    .ph-row {
      gap: 16px;
      flex-wrap: wrap;
    }

    .ph-title {
      font-weight: 750;
      letter-spacing: -.035em;
      line-height: 1.15;
    }

    .ph-sub,
    .muted-small {
      line-height: 1.55;
    }

    .filters {
      width: 100%;
      gap: 10px;
    }

    .filters > *,
    .fi,
    .form-group input,
    .form-group select,
    .form-group textarea,
    .cc-fi,
    .assignee-search {
      min-width: 0;
      max-width: 100%;
    }

    .fi,
    .form-group input,
    .form-group select,
    .form-group textarea,
    .cc-fi,
    .assignee-search,
    .input-shell {
      border-radius: 13px !important;
      min-height: 42px;
      box-shadow: none;
    }

    .fi:focus,
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus,
    .cc-fi:focus,
    .assignee-search:focus-within,
    .input-shell:focus-within {
      outline: none;
      box-shadow: var(--focus-ring) !important;
    }

    .g2,
    .g3,
    .g4,
    .work-board,
    .work-hero,
    .work-form-grid,
    .work-mini-grid,
    .cc-command-grid,
    .emp-detail-grid,
    .emp-detail-kpis,
    .split-2 {
      min-width: 0;
    }

    .g2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .g3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .g4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .work-hero { grid-template-columns: minmax(0, 1.25fr) minmax(300px, .75fr); }

    .work-tabs {
      display: flex !important;
      flex-direction: row !important;
      flex-wrap: wrap !important;
      display: flex !important; flex-direction: row !important; flex-wrap: wrap !important;
      gap: 10px;
      align-items: stretch;
      width: 100%;
      max-width: 100%;
      overflow: visible !important;
    }

    .work-tab {
      width: auto !important;
      min-width: 0 !important;
      flex: 1 1 auto !important;
      justify-content: center;
      text-align: center;
      white-space: nowrap;
      line-height: 1.25;
      min-height: 44px;
    }

    .work-mini-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .work-mini,
    .stat,
    .emp-detail-kpi,
    .cc-kpi,
    .emp-project-card,
    .emp-task-card,
    .emp-update-card {
      min-width: 0;
      overflow: hidden;
    }

    .tbl-wrap {
      width: 100%;
      overflow-x: hidden !important;
      overflow-y: visible;
      border-radius: 18px;
      background: var(--card);
    }

    table {
      width: 100% !important;
      min-width: 0 !important;
      max-width: 100%;
      table-layout: fixed;
      border-collapse: collapse;
    }

    th,
    td {
      white-space: normal !important;
      overflow-wrap: anywhere;
      word-break: normal;
      vertical-align: top;
      line-height: 1.45;
    }

    th {
      font-size: .68rem;
      padding: 11px 12px;
    }

    td {
      padding: 12px;
    }

    .mono {
      word-break: break-word;
      overflow-wrap: anywhere;
    }

    img,
    svg,
    canvas,
    video,
    iframe {
      max-width: 100%;
    }

    .chart-wrap {
      width: 100%;
      min-width: 0;
      overflow: hidden;
    }

    .modal-bg {
      padding: 20px;
      overflow-y: auto;
      overflow-x: hidden;
    }

    .modal {
      width: min(560px, 100%);
      max-height: calc(100dvh - 40px);
    }

    .modal-actions,
    .action-row {
      flex-wrap: wrap;
    }

    .toast {
      right: 18px;
      left: auto;
      min-width: min(280px, calc(100vw - 36px));
      max-width: calc(100vw - 36px);
    }

    .login-screen {
      width: 100%;
      max-width: 100vw;
      min-height: 100dvh;
      overflow-y: auto;
      overflow-x: hidden;
      padding: 24px;
    }

    .login-shell {
      width: min(1120px, 100%);
      display: grid;
      grid-template-columns: minmax(0, 1.05fr) minmax(360px, 440px);
      align-items: stretch;
      gap: 26px;
    }

    .premium-login-card,
    .login-box {
      width: 100% !important;
      margin: 0;
      padding: clamp(24px, 4vw, 42px) !important;
      background: color-mix(in srgb, var(--card) 95%, transparent) !important;
      border: 1px solid var(--border) !important;
      box-shadow: 0 28px 90px color-mix(in srgb, var(--accent) 16%, rgba(0,0,0,.14)) !important;
      backdrop-filter: blur(20px);
    }

    .role-choice-grid {
      grid-template-columns: repeat(5, minmax(0, 1fr));
      gap: 8px;
    }

    .role-choice {
      min-width: 0;
      white-space: normal;
      line-height: 1.2;
    }

    .attend-device-blocker {
      display: none !important;
    }

    body.wh-guest .login-screen { display: flex !important; }
    body.wh-guest .app { display: none !important; }
    body.wh-logged-in .login-screen { display: none !important; }
    body.wh-logged-in .app { display: flex !important; }

    @supports not (color: color-mix(in srgb, #000 50%, #fff)) {
      .topnav,
      .sidebar,
      .card,
      .hero,
      .work-hero-card,
      .premium-login-card,
      .login-box {
        background: var(--card) !important;
      }
    }

    @media (max-width: 1280px) {
      :root { --sidebar-w: 238px; }
      .content { padding: 22px; }
      .g4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .work-hero { grid-template-columns: 1fr; }
    }

    @media (max-width: 1024px) {
      :root { --topnav-h: 64px; }
      .shell {
        display: flex;
        flex-direction: column;
      }
      .sidebar {
        width: 100%;
        height: auto;
        max-height: 92px;
        min-height: 72px;
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
        overflow-x: hidden !important;
        overflow-y: auto;
        border-right: 0;
        border-bottom: 1px solid var(--border);
        padding: 10px 12px;
        box-shadow: 0 14px 34px color-mix(in srgb, var(--accent) 7%, transparent);
      }
      .nav-section { display: none; }
      .nav-item {
        flex: 1 1 126px;
        min-width: 0;
        justify-content: center;
        text-align: center;
        padding: 10px;
        gap: 8px;
      }
      .nav-item i {
        width: 30px;
        height: 30px;
        flex-basis: 30px;
      }
      .content {
        height: calc(100dvh - var(--topnav-h) - 92px);
        padding: 18px;
      }
      .login-shell {
        grid-template-columns: 1fr;
        max-width: 640px;
      }
      .login-visual { display: none !important; }
      .role-choice-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    @media (max-width: 760px) {
      :root { --topnav-h: auto; }
      .topnav {
        min-height: 64px;
        height: auto;
        flex-wrap: wrap;
        padding: 10px 12px;
        gap: 10px;
      }
      .brand {
        flex: 1 1 auto;
      }
      .nav-spacer { display: none; }
      .nav-user {
        order: 3;
        width: 100%;
        max-width: none;
      }
      .nav-info { max-width: none; }
      .nav-actions {
        margin-left: auto;
      }
      #themeText,
      .nav-actions .btn span,
      .nav-actions .btn:not(.theme-toggle) {
        font-size: 0;
      }
      .nav-actions .btn i { font-size: .9rem; }
      .shell {
        height: calc(100dvh - 114px);
      }
      .sidebar {
        max-height: 120px;
        flex-wrap: wrap;
        align-content: flex-start;
      }
      .nav-item {
        flex: 1 1 calc(50% - 8px);
        min-height: 40px;
        font-size: .78rem;
      }
      .content {
        height: calc(100dvh - 114px - 120px);
        padding: 14px;
      }
      .g2,
      .g3,
      .g4,
      .work-form-grid,
      .work-form-grid.two,
      .work-mini-grid,
      .emp-detail-kpis,
      .emp-detail-grid,
      .cc-command-grid,
      .split-2 {
        grid-template-columns: 1fr !important;
      }
      .hero {
        padding: 28px 16px;
      }
      .clock {
        font-size: clamp(2.1rem, 13vw, 3.15rem);
      }
      .ci-row {
        gap: 12px;
      }
      .action-row .btn,
      .action-row .login-btn,
      .modal-actions .btn {
        width: 100%;
        min-width: 0;
      }
      .filters {
        display: grid;
        grid-template-columns: 1fr;
      }
      .filters .btn,
      .filters .fi {
        width: 100%;
      }
      .tbl-wrap {
        border: 0;
        background: transparent;
      }
      .tbl-wrap table,
      .tbl-wrap thead,
      .tbl-wrap tbody,
      .tbl-wrap tr,
      .tbl-wrap th,
      .tbl-wrap td {
        display: block;
        width: 100%;
      }
      .tbl-wrap thead { display: none; }
      .tbl-wrap tbody tr {
        margin-bottom: 12px;
        padding: 10px;
        border: 1px solid var(--border);
        border-radius: 18px;
        background: var(--card);
        box-shadow: var(--shadow-soft);
      }
      .tbl-wrap td {
        border-top: 0;
        padding: 7px 8px;
      }
      .work-tabs {
        display: flex !important; flex-direction: row !important; flex-wrap: wrap !important;
      }
      .modal-bg { padding: 12px; }
      .modal { max-height: calc(100dvh - 24px); padding: 20px; }
      .login-screen { padding: 16px; align-items: flex-start; }
      .role-choice-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 430px) {
      .brand { font-size: .95rem; }
      .brand i { width: 32px; height: 32px; border-radius: 12px; }
      .role-badge { font-size: .62rem; padding: 3px 8px; }
      .work-tabs { display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; }
      .role-choice-grid { grid-template-columns: 1fr; }
      .login-card-title { font-size: 1.45rem; }
    }


    /* ═══════════════════════════════════════════════════════════════
       WorkHive Google Minimal UI Upgrade
       Safe scope: final CSS/markup polish only. Existing JS hooks, IDs,
       PHP variables, API endpoints and render targets are preserved.
    ═══════════════════════════════════════════════════════════════ */
    html[data-theme="light"] {
      color-scheme: light;
      --bg: #f8fafd;
      --bg2: #f1f4f9;
      --card: #ffffff;
      --card2: #f8fafd;
      --border: #e0e3eb;
      --border2: #d2d9e5;
      --accent: #1a73e8;
      --accent2: #0b57d0;
      --green: #188038;
      --red: #d93025;
      --yellow: #f9ab00;
      --purple: #a142f4;
      --text: #202124;
      --text2: #5f6368;
      --text3: #9aa0a6;
      --shadow: 0 18px 44px rgba(60, 64, 67, .12);
      --shadow-soft: 0 6px 18px rgba(60, 64, 67, .08);
      --surface-grad: linear-gradient(180deg, #ffffff 0%, #f8fafd 100%);
      --hero-grad: linear-gradient(180deg, #ffffff 0%, #f8fafd 100%);
      --chart-text: #5f6368;
      --chart-grid: rgba(95, 99, 104, .16);
      --r: 20px;
    }

    html[data-theme="dark"] {
      color-scheme: dark;
      --bg: #0b0f14;
      --bg2: #111820;
      --card: #151b23;
      --card2: #10161d;
      --border: rgba(232, 234, 237, .11);
      --border2: rgba(232, 234, 237, .18);
      --accent: #8ab4f8;
      --accent2: #669df6;
      --green: #81c995;
      --red: #f28b82;
      --yellow: #fdd663;
      --purple: #c58af9;
      --text: #e8eaed;
      --text2: #bdc1c6;
      --text3: #80868b;
      --shadow: 0 22px 54px rgba(0, 0, 0, .34);
      --shadow-soft: 0 10px 26px rgba(0, 0, 0, .24);
      --surface-grad: linear-gradient(180deg, #151b23 0%, #10161d 100%);
      --hero-grad: linear-gradient(180deg, #151b23 0%, #10161d 100%);
      --chart-text: #bdc1c6;
      --chart-grid: rgba(189, 193, 198, .13);
      --r: 20px;
    }

    html,
    body {
      width: 100%;
      max-width: 100%;
      overflow-x: hidden !important;
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
      background: var(--bg);
      color: var(--text);
      text-rendering: geometricPrecision;
    }

    body {
      background:
        radial-gradient(circle at 8% -10%, rgba(66, 133, 244, .12), transparent 28%),
        radial-gradient(circle at 110% 0%, rgba(52, 168, 83, .10), transparent 26%),
        var(--bg);
    }

    html[data-theme="dark"] body {
      background:
        radial-gradient(circle at 8% -10%, rgba(138, 180, 248, .13), transparent 30%),
        radial-gradient(circle at 110% 0%, rgba(129, 201, 149, .09), transparent 28%),
        var(--bg);
    }

    .app {
      height: 100dvh;
      background: transparent;
      overflow: hidden;
    }

    .topnav {
      height: 66px;
      padding: 0 24px;
      gap: 16px;
      background: color-mix(in srgb, var(--card) 94%, transparent);
      border-bottom: 1px solid var(--border);
      box-shadow: 0 1px 2px rgba(60, 64, 67, .08);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
    }

    .brand {
      min-width: max-content;
      font-size: 1.04rem;
      font-weight: 650;
      letter-spacing: -.02em;
      color: var(--text) !important;
      background: none !important;
      -webkit-text-fill-color: currentColor !important;
    }

    .brand i {
      width: 36px;
      height: 36px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
      color: #fff;
      background: #1a73e8;
      box-shadow: 0 8px 18px rgba(26, 115, 232, .22);
    }

    .nav-user {
      min-width: 0;
      padding: 5px 7px 5px 5px;
      border: 1px solid transparent;
      border-radius: 999px;
      background: transparent;
      transition: border-color .16s ease, background .16s ease;
    }

    .nav-user:hover {
      border-color: var(--border);
      background: var(--card2);
    }

    .avatar,
    .emp-avatar-sm,
    .assignee-avatar,
    .emp-detail-avatar {
      background: #1a73e8 !important;
      box-shadow: none !important;
    }

    .nav-info .nm {
      max-width: 180px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      font-size: .86rem;
      font-weight: 650;
      color: var(--text);
    }

    .nav-info .eid {
      color: var(--text2);
      font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, monospace;
      font-size: .7rem;
    }

    .role-badge {
      border-radius: 999px;
      padding: 4px 10px;
      font-size: .66rem;
      letter-spacing: .05em;
      background: #e8f0fe !important;
      color: #1967d2 !important;
      border: 1px solid #d2e3fc;
    }

    html[data-theme="dark"] .role-badge {
      background: rgba(138, 180, 248, .13) !important;
      border-color: rgba(138, 180, 248, .22);
      color: #8ab4f8 !important;
    }

    .nav-actions {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn,
    .theme-toggle,
    .login-btn {
      min-height: 38px;
      border-radius: 999px;
      font-family: 'Inter', system-ui, sans-serif !important;
      font-weight: 650;
      letter-spacing: -.01em;
      transition: transform .16s ease, box-shadow .16s ease, background .16s ease, border-color .16s ease;
    }

    .btn:hover,
    .theme-toggle:hover,
    .login-btn:hover {
      transform: translateY(-1px);
    }

    .btn-primary,
    .login-btn,
    .premium-signin {
      background: #1a73e8 !important;
      color: #fff !important;
      border: 1px solid #1a73e8 !important;
      box-shadow: 0 8px 18px rgba(26, 115, 232, .22) !important;
    }

    .btn-primary:hover,
    .login-btn:hover,
    .premium-signin:hover {
      background: #1558c4 !important;
      border-color: #1558c4 !important;
    }

    .btn-outline,
    .theme-toggle {
      background: var(--card) !important;
      color: var(--text2) !important;
      border: 1px solid var(--border) !important;
      box-shadow: none !important;
    }

    .btn-outline:hover,
    .theme-toggle:hover {
      color: var(--accent) !important;
      border-color: #d2e3fc !important;
      background: #f8fbff !important;
    }

    html[data-theme="dark"] .btn-outline:hover,
    html[data-theme="dark"] .theme-toggle:hover {
      background: rgba(138, 180, 248, .08) !important;
      border-color: rgba(138, 180, 248, .22) !important;
    }

    .shell {
      height: calc(100dvh - 66px);
      background: transparent;
      overflow: hidden;
    }

    .sidebar {
      width: 256px;
      padding: 18px 12px;
      background: color-mix(in srgb, var(--card) 92%, transparent);
      border-right: 1px solid var(--border);
      box-shadow: none;
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
    }

    .nav-section {
      padding: 14px 12px 7px;
      margin: 0;
      color: var(--text3);
      font-size: .68rem;
      font-weight: 750;
      letter-spacing: .08em;
    }

    .nav-item {
      min-height: 42px;
      margin: 2px 0;
      padding: 9px 12px;
      border: 0 !important;
      border-radius: 999px;
      color: var(--text2);
      font-size: .88rem;
      font-weight: 600;
      letter-spacing: -.01em;
    }

    .nav-item i {
      width: 30px;
      height: 30px;
      flex: 0 0 30px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      color: inherit;
      background: transparent;
      font-size: .9rem;
    }

    .nav-item:hover {
      background: var(--bg2) !important;
      color: var(--text) !important;
    }

    .nav-item.active {
      background: #e8f0fe !important;
      color: #1967d2 !important;
      box-shadow: inset 0 0 0 1px #d2e3fc;
    }

    html[data-theme="dark"] .nav-item.active {
      background: rgba(138, 180, 248, .13) !important;
      color: #8ab4f8 !important;
      box-shadow: inset 0 0 0 1px rgba(138, 180, 248, .20);
    }

    .content {
      min-width: 0;
      padding: 28px;
      overflow-y: auto;
      overflow-x: hidden !important;
      scroll-behavior: smooth;
    }

    .content::-webkit-scrollbar,
    .sidebar::-webkit-scrollbar,
    .modal::-webkit-scrollbar,
    .emp-detail-shell::-webkit-scrollbar {
      width: 10px;
      height: 10px;
    }

    .content::-webkit-scrollbar-thumb,
    .sidebar::-webkit-scrollbar-thumb,
    .modal::-webkit-scrollbar-thumb,
    .emp-detail-shell::-webkit-scrollbar-thumb {
      border: 3px solid transparent;
      border-radius: 999px;
      background: color-mix(in srgb, var(--text3) 48%, transparent);
      background-clip: content-box;
    }

    .page.active {
      width: 100%;
      max-width: 1480px;
      margin: 0 auto;
      animation: fadeUp .24s ease both;
    }

    .card,
    .hero,
    .work-hero-card,
    .cc-filter-bar,
    .cc-kpi-card,
    .cc-command-hero,
    .emp-detail-card,
    .modal {
      border: 1px solid var(--border) !important;
      border-radius: 24px !important;
      background: var(--card) !important;
      box-shadow: 0 1px 2px rgba(60, 64, 67, .08), 0 8px 24px rgba(60, 64, 67, .06) !important;
    }

    html[data-theme="dark"] .card,
    html[data-theme="dark"] .hero,
    html[data-theme="dark"] .work-hero-card,
    html[data-theme="dark"] .cc-filter-bar,
    html[data-theme="dark"] .cc-kpi-card,
    html[data-theme="dark"] .cc-command-hero,
    html[data-theme="dark"] .emp-detail-card,
    html[data-theme="dark"] .modal {
      box-shadow: 0 1px 2px rgba(0,0,0,.32), 0 10px 28px rgba(0,0,0,.20) !important;
    }

    .card-title,
    .cc-eyebrow,
    .emp-detail-card-title {
      color: var(--text2);
      font-size: .72rem;
      font-weight: 750;
      letter-spacing: .08em;
    }

    .ph-title,
    .cc-command-hero h2,
    .emp-detail-name {
      color: var(--text);
      font-weight: 700;
      letter-spacing: -.025em;
    }

    .ph-sub,
    .muted-small,
    .cc-tl-subtitle,
    .emp-detail-meta,
    .dateline {
      color: var(--text2);
    }

    .stat,
    .work-mini,
    .cc-insight-stat,
    .emp-detail-kpi,
    .emp-project-card,
    .emp-task-card,
    .emp-update-card,
    .assignee-picker,
    .tbl-wrap,
    .cc-tl-day-header,
    .cc-tl-update,
    .cc-step-list {
      background: var(--card2) !important;
      border-color: var(--border) !important;
      border-radius: 18px !important;
      box-shadow: none !important;
    }

    .hero {
      padding: 42px 28px;
      background: linear-gradient(180deg, var(--card), var(--card2)) !important;
    }

    .clock {
      color: var(--accent) !important;
      background: none !important;
      -webkit-text-fill-color: currentColor !important;
      letter-spacing: -.05em;
    }

    .tz-badge,
    .status-pill,
    .pill,
    .work-chip,
    .cc-period-chip,
    .login-live-chip {
      border-radius: 999px !important;
      font-weight: 650;
    }

    .sp-idle,
    .pl,
    .tz-badge,
    .cc-period-chip {
      background: #e8f0fe !important;
      color: #1967d2 !important;
      border: 1px solid #d2e3fc !important;
    }

    .sp-present,
    .pp {
      background: #e6f4ea !important;
      color: #137333 !important;
      border: 1px solid #ceead6 !important;
    }

    .sp-absent,
    .pa {
      background: #fce8e6 !important;
      color: #c5221f !important;
      border: 1px solid #fad2cf !important;
    }

    .ph,
    .pe,
    .pho {
      background: #fef7e0 !important;
      color: #b06000 !important;
      border: 1px solid #feefc3 !important;
    }

    .pw {
      background: #f3e8fd !important;
      color: #8430ce !important;
      border: 1px solid #ead5fb !important;
    }

    html[data-theme="dark"] .sp-idle,
    html[data-theme="dark"] .pl,
    html[data-theme="dark"] .tz-badge,
    html[data-theme="dark"] .cc-period-chip {
      background: rgba(138, 180, 248, .13) !important;
      color: #8ab4f8 !important;
      border-color: rgba(138, 180, 248, .20) !important;
    }

    html[data-theme="dark"] .sp-present,
    html[data-theme="dark"] .pp {
      background: rgba(129, 201, 149, .13) !important;
      color: #81c995 !important;
      border-color: rgba(129, 201, 149, .20) !important;
    }

    html[data-theme="dark"] .sp-absent,
    html[data-theme="dark"] .pa {
      background: rgba(242, 139, 130, .13) !important;
      color: #f28b82 !important;
      border-color: rgba(242, 139, 130, .20) !important;
    }

    table {
      min-width: 0;
      border-collapse: separate;
      border-spacing: 0;
    }

    .tbl-wrap {
      width: 100%;
      max-width: 100%;
      overflow-x: auto;
      background: var(--card) !important;
    }

    thead tr,
    html[data-theme="light"] table thead tr {
      background: var(--card2) !important;
    }

    th {
      padding: 12px 14px;
      color: var(--text2);
      font-weight: 700;
      letter-spacing: .06em;
    }

    td {
      padding: 13px 14px;
      color: var(--text);
      border-color: var(--border) !important;
      vertical-align: middle;
    }

    tbody tr:hover,
    html[data-theme="light"] table tbody tr:hover {
      background: color-mix(in srgb, var(--accent) 5%, transparent) !important;
    }

    .fi,
    .form-group input,
    .form-group select,
    .form-group textarea,
    .cc-fi,
    .assignee-search,
    .input-shell {
      min-height: 44px;
      border-radius: 14px !important;
      background: var(--card) !important;
      border: 1px solid var(--border2) !important;
      color: var(--text) !important;
      font-family: 'Inter', system-ui, sans-serif !important;
      box-shadow: none !important;
    }

    .fi:focus,
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus,
    .cc-fi:focus,
    .assignee-search:focus,
    .input-shell:focus-within {
      border-color: var(--accent) !important;
      box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 15%, transparent) !important;
      transform: none !important;
    }

    .form-group label {
      color: var(--text2) !important;
      font-size: .78rem;
      font-weight: 650;
      letter-spacing: .02em;
      text-transform: none;
    }

    .work-tabs {
      padding: 4px;
      border: 1px solid var(--border);
      border-radius: 999px;
      background: var(--card2);
      box-shadow: none;
    }

    .work-tab,
    html[data-theme="light"] .work-tab {
      min-height: 38px;
      border-radius: 999px;
      background: transparent !important;
      border: 0 !important;
      color: var(--text2) !important;
      box-shadow: none !important;
    }

    .work-tab:hover,
    .work-tab.active,
    html[data-theme="light"] .work-tab:hover,
    html[data-theme="light"] .work-tab.active {
      background: var(--card) !important;
      color: var(--accent) !important;
      box-shadow: 0 1px 2px rgba(60,64,67,.10) !important;
      transform: none !important;
    }

    .cc-kpi-card::before,
    .stat::before {
      height: 4px;
      background: var(--accent) !important;
    }

    .cc-kpi-val,
    .cc-kpi-icon,
    .work-mini .v,
    .stat .val {
      color: var(--accent) !important;
    }

    .modal-bg {
      background: rgba(32, 33, 36, .42);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }

    .modal {
      width: min(540px, 94vw);
      padding: 26px;
    }

    .toast {
      border-radius: 14px;
      box-shadow: 0 10px 28px rgba(60, 64, 67, .24);
    }

    /* Google-style login */
    .wh-google-login,
    body.wh-guest .login-screen.wh-google-login {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100dvh;
      padding: 28px;
      overflow: hidden;
      isolation: isolate;
      background:
        radial-gradient(circle at 0% 0%, rgba(66, 133, 244, .14), transparent 30%),
        radial-gradient(circle at 100% 0%, rgba(251, 188, 4, .13), transparent 26%),
        radial-gradient(circle at 100% 100%, rgba(52, 168, 83, .13), transparent 30%),
        radial-gradient(circle at 0% 100%, rgba(234, 67, 53, .10), transparent 26%),
        #f8fafd !important;
    }

    html[data-theme="dark"] .wh-google-login,
    html[data-theme="dark"] body.wh-guest .login-screen.wh-google-login {
      background:
        radial-gradient(circle at 0% 0%, rgba(138, 180, 248, .14), transparent 32%),
        radial-gradient(circle at 100% 0%, rgba(253, 214, 99, .10), transparent 28%),
        radial-gradient(circle at 100% 100%, rgba(129, 201, 149, .10), transparent 30%),
        radial-gradient(circle at 0% 100%, rgba(242, 139, 130, .09), transparent 26%),
        #0b0f14 !important;
    }

    .wh-google-login::before,
    .wh-google-login::after,
    .wh-google-login .login-ambient {
      display: none !important;
    }

    .wh-google-shell {
      width: min(1080px, 100%);
      min-height: 0;
      display: block;
      animation: whLoginEnter .36s cubic-bezier(.2, 0, 0, 1) both;
    }

    .wh-google-card {
      width: 100% !important;
      min-height: 560px;
      display: grid;
      grid-template-columns: minmax(0, 1fr) 440px;
      gap: 0;
      padding: 0 !important;
      overflow: hidden;
      border-radius: 32px !important;
      background: var(--card) !important;
      border: 1px solid var(--border) !important;
      box-shadow: 0 24px 70px rgba(60, 64, 67, .18) !important;
    }

    html[data-theme="dark"] .wh-google-card {
      background: #151b23 !important;
      box-shadow: 0 28px 80px rgba(0, 0, 0, .45) !important;
    }

    .wh-google-card::before {
      display: none !important;
    }

    .wh-google-brand-panel,
    .wh-google-form-panel {
      min-width: 0;
      position: relative;
    }

    .wh-google-brand-panel {
      padding: 42px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background:
        linear-gradient(180deg, rgba(248, 250, 253, .92), rgba(241, 244, 249, .72)),
        radial-gradient(circle at 22% 22%, rgba(66, 133, 244, .16), transparent 28%),
        radial-gradient(circle at 82% 72%, rgba(52, 168, 83, .12), transparent 30%);
      border-right: 1px solid var(--border);
    }

    html[data-theme="dark"] .wh-google-brand-panel {
      background:
        linear-gradient(180deg, rgba(17, 24, 32, .92), rgba(11, 15, 20, .72)),
        radial-gradient(circle at 22% 22%, rgba(138, 180, 248, .14), transparent 30%),
        radial-gradient(circle at 82% 72%, rgba(129, 201, 149, .10), transparent 30%);
    }

    .wh-google-brand-lockup {
      display: grid;
      gap: 14px;
      justify-items: start;
    }

    .wh-google-logo {
      font-size: clamp(2rem, 4vw, 3.3rem);
      line-height: 1;
      font-weight: 750;
      letter-spacing: -.055em;
    }

    .g-blue { color: #4285f4; }
    .g-red { color: #ea4335; }
    .g-yellow { color: #fbbc04; }
    .g-green { color: #34a853; }

    .wh-google-product-chip,
    .wh-google-status-card {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      min-height: 34px;
      padding: 7px 12px;
      border-radius: 999px;
      background: var(--card);
      border: 1px solid var(--border);
      color: var(--text2);
      box-shadow: 0 1px 2px rgba(60, 64, 67, .08);
      font-size: .78rem;
      font-weight: 650;
    }

    .wh-google-product-chip i,
    .wh-google-status-card i {
      color: #1a73e8;
    }

    .wh-google-copy {
      max-width: 520px;
      padding: 36px 0;
    }

    .wh-google-copy h1 {
      margin: 0 0 14px;
      color: var(--text);
      font-size: clamp(2.1rem, 5vw, 4.3rem);
      line-height: .98;
      font-weight: 650;
      letter-spacing: -.06em;
    }

    .wh-google-copy p {
      max-width: 440px;
      color: var(--text2);
      font-size: 1rem;
      line-height: 1.6;
      font-weight: 450;
    }

    .wh-google-status-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .wh-google-form-panel {
      padding: 42px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: var(--card);
    }

    .wh-google-form-panel .login-live-chip {
      position: absolute;
      top: 24px;
      right: 24px;
      margin: 0;
      padding: 7px 11px;
      background: var(--card2) !important;
      color: var(--text2) !important;
      border: 1px solid var(--border) !important;
      box-shadow: none !important;
      font-size: .72rem;
    }

    .wh-google-form-panel .login-card-top {
      margin: 0 0 26px;
      text-align: left;
    }

    .wh-google-form-panel .login-icon-ring {
      width: 48px;
      height: 48px;
      margin: 0 0 18px;
      border-radius: 16px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #e8f0fe !important;
      color: #1967d2 !important;
      border: 1px solid #d2e3fc !important;
      box-shadow: none !important;
      animation: none !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .login-icon-ring {
      background: rgba(138, 180, 248, .13) !important;
      color: #8ab4f8 !important;
      border-color: rgba(138, 180, 248, .22) !important;
    }

    .wh-google-form-panel .login-card-title {
      margin: 0 0 8px;
      color: var(--text);
      font-size: 1.62rem;
      line-height: 1.15;
      font-weight: 650;
      letter-spacing: -.04em;
    }

    .wh-google-form-panel .login-card-top p {
      max-width: 320px;
      margin: 0;
      color: var(--text2);
      font-size: .9rem;
      line-height: 1.55;
    }

    .wh-google-form-panel .login-role-group,
    .wh-google-form-panel .smart-field {
      margin-bottom: 16px;
    }

    .wh-google-form-panel .role-choice-grid {
      grid-template-columns: repeat(5, minmax(0, 1fr));
      gap: 8px;
    }

    .wh-google-form-panel .role-choice {
      min-height: 58px;
      border-radius: 16px;
      background: var(--card2) !important;
      border: 1px solid var(--border) !important;
      color: var(--text2) !important;
      box-shadow: none !important;
      font-size: .66rem;
      font-weight: 700;
    }

    .wh-google-form-panel .role-choice:hover {
      color: #1967d2 !important;
      border-color: #d2e3fc !important;
      background: #f8fbff !important;
      transform: none;
    }

    .wh-google-form-panel .role-choice.active {
      color: #1967d2 !important;
      background: #e8f0fe !important;
      border-color: #d2e3fc !important;
      box-shadow: inset 0 0 0 1px #d2e3fc !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .role-choice:hover,
    html[data-theme="dark"] .wh-google-form-panel .role-choice.active {
      color: #8ab4f8 !important;
      background: rgba(138, 180, 248, .13) !important;
      border-color: rgba(138, 180, 248, .22) !important;
    }

    .wh-google-form-panel .input-shell {
      min-height: 56px;
      padding: 0 14px;
      border-radius: 16px !important;
      background: var(--card) !important;
    }

    .wh-google-form-panel .input-shell input {
      height: 54px;
      font-size: .96rem;
      font-weight: 520;
    }

    .wh-google-form-panel .input-shell > i {
      color: var(--text2);
    }

    .wh-google-form-panel .password-toggle {
      border-radius: 50%;
      color: var(--text2);
    }

    .wh-google-form-panel .password-toggle:hover {
      color: var(--accent);
      background: var(--card2);
    }

    .wh-google-form-panel .premium-signin {
      width: 100%;
      min-height: 48px;
      margin-top: 4px;
      border-radius: 999px !important;
      font-size: .94rem;
      font-weight: 650;
      box-shadow: none !important;
    }

    .wh-google-form-panel .login-error {
      display: none;
      margin: 14px 0 0;
      padding: 10px 12px;
      border-radius: 14px;
      color: #c5221f;
      background: #fce8e6;
      border: 1px solid #fad2cf;
      font-size: .8rem;
      font-weight: 650;
      text-align: left;
    }

    html[data-theme="dark"] .wh-google-form-panel .login-error {
      color: #f28b82;
      background: rgba(242, 139, 130, .13);
      border-color: rgba(242, 139, 130, .20);
    }

    .wh-google-form-panel .login-footnote {
      margin-top: 18px;
      justify-content: flex-start;
      gap: 8px;
      color: var(--text2);
      font-size: .72rem;
    }

    .wh-google-form-panel .login-footnote span {
      background: transparent;
      border: 0;
      padding: 0;
      gap: 6px;
      color: var(--text2);
    }

    .wh-google-form-panel .login-footnote i {
      color: var(--green);
    }

    @media (max-width: 1180px) {
      .wh-google-card {
        grid-template-columns: 1fr;
        max-width: 680px;
        margin: 0 auto;
      }
      .wh-google-brand-panel {
        min-height: 250px;
        padding: 32px;
        border-right: 0;
        border-bottom: 1px solid var(--border);
      }
      .wh-google-copy {
        padding: 26px 0 12px;
      }
      .wh-google-copy h1 {
        font-size: clamp(2rem, 8vw, 3.2rem);
      }
      .wh-google-form-panel {
        padding: 34px 32px;
      }
    }

    @media (max-width: 760px) {
      .topnav {
        min-height: 66px;
        padding: 10px 14px;
      }
      .content {
        padding: 18px;
      }
      .wh-google-login {
        padding: 16px;
        align-items: flex-start;
        overflow-y: auto !important;
      }
      .wh-google-card {
        border-radius: 28px !important;
      }
      .wh-google-brand-panel {
        padding: 26px 22px;
      }
      .wh-google-form-panel {
        padding: 30px 22px 24px;
      }
      .wh-google-form-panel .login-live-chip {
        position: static;
        width: max-content;
        margin: 0 0 18px;
      }
      .wh-google-form-panel .role-choice-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
      .wh-google-form-panel .role-choice:last-child {
        grid-column: 1 / -1;
      }
      .wh-google-status-grid {
        display: none;
      }
    }

    @media (max-width: 430px) {
      .wh-google-brand-panel {
        min-height: 220px;
      }
      .wh-google-copy p {
        font-size: .9rem;
      }
      .wh-google-form-panel .role-choice-grid {
        grid-template-columns: 1fr;
      }
    }


    /* ═══════════════════════════════════════════════════════════════
       WorkHive Purple Login Variant
       Safe login-only visual layer. Structure, IDs, JS hooks, API calls,
       session logic and existing form behaviour are preserved.
    ═══════════════════════════════════════════════════════════════ */
    body.wh-guest .login-screen.wh-google-login,
    .login-screen.wh-google-login {
      background:
        radial-gradient(circle at 16% 18%, rgba(124, 58, 237, .16), transparent 30%),
        radial-gradient(circle at 86% 18%, rgba(168, 85, 247, .14), transparent 30%),
        radial-gradient(circle at 18% 82%, rgba(236, 72, 153, .08), transparent 30%),
        linear-gradient(135deg, #fbf7ff 0%, #f3eefe 48%, #fffafc 100%) !important;
    }

    html[data-theme="dark"] body.wh-guest .login-screen.wh-google-login,
    html[data-theme="dark"] .login-screen.wh-google-login {
      background:
        radial-gradient(circle at 14% 18%, rgba(139, 92, 246, .22), transparent 32%),
        radial-gradient(circle at 88% 12%, rgba(192, 132, 252, .16), transparent 30%),
        radial-gradient(circle at 80% 88%, rgba(236, 72, 153, .12), transparent 30%),
        linear-gradient(135deg, #140d22 0%, #1a1230 48%, #0d1020 100%) !important;
    }

    .wh-google-card {
      border-radius: 32px !important;
      border: 1px solid rgba(139, 92, 246, .18) !important;
      background: rgba(255, 255, 255, .88) !important;
      box-shadow:
        0 30px 80px rgba(88, 28, 135, .16),
        0 1px 0 rgba(255, 255, 255, .80) inset !important;
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
    }

    html[data-theme="dark"] .wh-google-card {
      border-color: rgba(192, 132, 252, .22) !important;
      background: rgba(22, 16, 38, .92) !important;
      box-shadow:
        0 34px 90px rgba(0, 0, 0, .48),
        0 1px 0 rgba(255, 255, 255, .08) inset !important;
    }

    .wh-google-brand-panel {
      background:
        radial-gradient(circle at 20% 18%, rgba(124, 58, 237, .18), transparent 34%),
        radial-gradient(circle at 86% 80%, rgba(236, 72, 153, .10), transparent 34%),
        linear-gradient(135deg, rgba(250, 245, 255, .96), rgba(245, 240, 255, .86)) !important;
      border-right: 1px solid rgba(139, 92, 246, .14) !important;
    }

    html[data-theme="dark"] .wh-google-brand-panel {
      background:
        radial-gradient(circle at 20% 18%, rgba(139, 92, 246, .22), transparent 35%),
        radial-gradient(circle at 86% 80%, rgba(236, 72, 153, .11), transparent 35%),
        linear-gradient(135deg, rgba(30, 22, 52, .96), rgba(18, 15, 31, .94)) !important;
      border-right-color: rgba(192, 132, 252, .18) !important;
    }

    .wh-google-form-panel {
      background:
        linear-gradient(180deg, rgba(255,255,255,.96), rgba(252,250,255,.92)) !important;
    }

    html[data-theme="dark"] .wh-google-form-panel {
      background:
        linear-gradient(180deg, rgba(26, 19, 44, .96), rgba(17, 14, 29, .94)) !important;
    }

    .wh-google-copy h1 {
      color: #2e1065 !important;
      text-shadow: 0 16px 38px rgba(88, 28, 135, .10);
    }

    html[data-theme="dark"] .wh-google-copy h1 {
      color: #f5f3ff !important;
      text-shadow: none;
    }

    .wh-google-copy p,
    .wh-google-form-panel .login-card-top p,
    .wh-google-form-panel .login-footnote,
    .wh-google-form-panel .login-footnote span {
      color: #5b526f !important;
    }

    html[data-theme="dark"] .wh-google-copy p,
    html[data-theme="dark"] .wh-google-form-panel .login-card-top p,
    html[data-theme="dark"] .wh-google-form-panel .login-footnote,
    html[data-theme="dark"] .wh-google-form-panel .login-footnote span {
      color: #c8bde2 !important;
    }

    .wh-google-product-chip,
    .wh-google-status-card {
      background: rgba(255, 255, 255, .72) !important;
      border-color: rgba(139, 92, 246, .18) !important;
      color: #4c1d95 !important;
      box-shadow: 0 10px 26px rgba(88, 28, 135, .06) !important;
    }

    html[data-theme="dark"] .wh-google-product-chip,
    html[data-theme="dark"] .wh-google-status-card {
      background: rgba(255, 255, 255, .06) !important;
      border-color: rgba(192, 132, 252, .18) !important;
      color: #ddd6fe !important;
      box-shadow: none !important;
    }

    .wh-google-product-chip i,
    .wh-google-status-card i,
    .wh-google-form-panel .login-footnote i {
      color: #7c3aed !important;
    }

    .wh-google-form-panel .login-live-chip {
      background: #f3e8ff !important;
      border-color: #e9d5ff !important;
      color: #6d28d9 !important;
      box-shadow: 0 8px 22px rgba(124, 58, 237, .08) !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .login-live-chip {
      background: rgba(124, 58, 237, .16) !important;
      border-color: rgba(192, 132, 252, .20) !important;
      color: #ddd6fe !important;
      box-shadow: none !important;
    }

    .wh-google-form-panel .login-icon-ring {
      background: linear-gradient(135deg, #f3e8ff, #ede9fe) !important;
      border-color: #e9d5ff !important;
      color: #7c3aed !important;
      box-shadow: 0 12px 28px rgba(124, 58, 237, .12) !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .login-icon-ring {
      background: rgba(124, 58, 237, .18) !important;
      border-color: rgba(192, 132, 252, .22) !important;
      color: #c4b5fd !important;
      box-shadow: none !important;
    }

    .wh-google-form-panel .login-card-title {
      color: #2e1065 !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .login-card-title {
      color: #f5f3ff !important;
    }

    .wh-google-form-panel .login-role-group label,
    .wh-google-form-panel .smart-field label {
      color: #6b5c7d !important;
      font-weight: 750 !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .login-role-group label,
    html[data-theme="dark"] .wh-google-form-panel .smart-field label {
      color: #bbaee0 !important;
    }

    .wh-google-form-panel .role-choice {
      background: #f6edff !important;
      border-color: #eadcff !important;
      color: #5b21b6 !important;
      box-shadow: 0 8px 18px rgba(124, 58, 237, .06) !important;
    }

    .wh-google-form-panel .role-choice:hover {
      background: #f0e3ff !important;
      border-color: #d8b4fe !important;
      color: #6d28d9 !important;
      transform: translateY(-1px) !important;
    }

    .wh-google-form-panel .role-choice.active {
      background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
      border-color: #7c3aed !important;
      color: #ffffff !important;
      box-shadow: 0 12px 26px rgba(124, 58, 237, .22) !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .role-choice {
      background: rgba(255, 255, 255, .06) !important;
      border-color: rgba(192, 132, 252, .18) !important;
      color: #ddd6fe !important;
      box-shadow: none !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .role-choice:hover,
    html[data-theme="dark"] .wh-google-form-panel .role-choice.active {
      background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
      border-color: #a78bfa !important;
      color: #ffffff !important;
      box-shadow: 0 12px 28px rgba(124, 58, 237, .30) !important;
    }

    .wh-google-form-panel .input-shell {
      background: #fbf8ff !important;
      border-color: #dcc7ff !important;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .75) !important;
    }

    .wh-google-form-panel .input-shell:focus-within {
      border-color: #8b5cf6 !important;
      box-shadow:
        0 0 0 4px rgba(139, 92, 246, .14),
        inset 0 1px 0 rgba(255, 255, 255, .75) !important;
    }

    .wh-google-form-panel .input-shell input {
      color: #2e1065 !important;
      background: transparent !important;
    }

    .wh-google-form-panel .input-shell input::placeholder {
      color: #9b8ab4 !important;
    }

    .wh-google-form-panel .input-shell > i,
    .wh-google-form-panel .password-toggle {
      color: #6d5c82 !important;
    }

    .wh-google-form-panel .password-toggle:hover {
      background: #f3e8ff !important;
      color: #7c3aed !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .input-shell {
      background: rgba(255, 255, 255, .06) !important;
      border-color: rgba(192, 132, 252, .22) !important;
      box-shadow: none !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .input-shell:focus-within {
      border-color: #a78bfa !important;
      box-shadow: 0 0 0 4px rgba(167, 139, 250, .14) !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .input-shell input {
      color: #f5f3ff !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .input-shell input::placeholder,
    html[data-theme="dark"] .wh-google-form-panel .input-shell > i,
    html[data-theme="dark"] .wh-google-form-panel .password-toggle {
      color: #c4b5fd !important;
    }

    .wh-google-form-panel .premium-signin {
      background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 52%, #6d28d9 100%) !important;
      color: #ffffff !important;
      box-shadow: 0 16px 34px rgba(124, 58, 237, .28) !important;
    }

    .wh-google-form-panel .premium-signin:hover {
      transform: translateY(-1px) !important;
      box-shadow: 0 18px 38px rgba(124, 58, 237, .34) !important;
      opacity: 1 !important;
    }

    .wh-google-form-panel .login-error {
      color: #9f1239 !important;
      background: #fff1f2 !important;
      border-color: #fecdd3 !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .login-error {
      color: #fda4af !important;
      background: rgba(244, 63, 94, .12) !important;
      border-color: rgba(251, 113, 133, .20) !important;
    }

    @media (max-width: 1180px) {
      .wh-google-brand-panel {
        border-right: 0 !important;
        border-bottom: 1px solid rgba(139, 92, 246, .14) !important;
      }
    }


    /* Login input cleanup: remove the inner rounded input boxes while keeping the outer field shell. */
    .wh-google-form-panel .input-shell input,
    .wh-google-form-panel .input-shell input:focus,
    .wh-google-form-panel .input-shell input:active {
      min-height: 0 !important;
      height: 54px !important;
      padding: 0 !important;
      margin: 0 !important;
      border: 0 !important;
      outline: 0 !important;
      border-radius: 0 !important;
      background: transparent !important;
      box-shadow: none !important;
      -webkit-box-shadow: none !important;
      appearance: none !important;
      -webkit-appearance: none !important;
    }

    .wh-google-form-panel .input-shell input:-webkit-autofill,
    .wh-google-form-panel .input-shell input:-webkit-autofill:hover,
    .wh-google-form-panel .input-shell input:-webkit-autofill:focus,
    .wh-google-form-panel .input-shell input:-webkit-autofill:active {
      border: 0 !important;
      border-radius: 0 !important;
      -webkit-text-fill-color: #2e1065 !important;
      caret-color: #2e1065 !important;
      -webkit-box-shadow: 0 0 0 1000px #fbf8ff inset !important;
      box-shadow: 0 0 0 1000px #fbf8ff inset !important;
      transition: background-color 9999s ease-in-out 0s !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .input-shell input:-webkit-autofill,
    html[data-theme="dark"] .wh-google-form-panel .input-shell input:-webkit-autofill:hover,
    html[data-theme="dark"] .wh-google-form-panel .input-shell input:-webkit-autofill:focus,
    html[data-theme="dark"] .wh-google-form-panel .input-shell input:-webkit-autofill:active {
      -webkit-text-fill-color: #f5f3ff !important;
      caret-color: #f5f3ff !important;
      -webkit-box-shadow: 0 0 0 1000px rgba(255, 255, 255, .06) inset !important;
      box-shadow: 0 0 0 1000px rgba(255, 255, 255, .06) inset !important;
    }


    /* ═══════════════════════════════════════════════════════════════
       WorkHive Full Purple Theme Patch
       Safe scope: CSS-only visual theme conversion. Existing PHP variables,
       IDs, JS hooks, API endpoints, forms and render targets are preserved.
       This block intentionally comes last so it overrides earlier Google/blue
       UI colors and extends the purple login palette across the full app.
    ═══════════════════════════════════════════════════════════════ */
    html[data-theme="light"] {
      color-scheme: light;
      --bg: #fbf7ff;
      --bg2: #f6edff;
      --card: #ffffff;
      --card2: #fcf9ff;
      --border: rgba(139, 92, 246, .16);
      --border2: #dcc7ff;
      --accent: #7c3aed;
      --accent2: #a855f7;
      --green: #168a68;
      --red: #d94848;
      --yellow: #b87514;
      --purple: #8b5cf6;
      --text: #2e1065;
      --text2: #5b526f;
      --text3: #9b8ab4;
      --shadow: 0 26px 70px rgba(88, 28, 135, .14);
      --shadow-soft: 0 10px 28px rgba(88, 28, 135, .08);
      --surface-grad: linear-gradient(135deg, rgba(139, 92, 246, .10), rgba(168, 85, 247, .07));
      --hero-grad:
        radial-gradient(circle at 12% 14%, rgba(124, 58, 237, .13), transparent 34%),
        radial-gradient(circle at 88% 20%, rgba(168, 85, 247, .11), transparent 32%),
        linear-gradient(135deg, #ffffff 0%, #fbf7ff 55%, #fffafc 100%);
      --chart-text: #5b526f;
      --chart-grid: rgba(109, 40, 217, .14);
      --focus-ring: 0 0 0 4px rgba(139, 92, 246, .14);
    }

    html[data-theme="dark"] {
      color-scheme: dark;
      --bg: #140d22;
      --bg2: #1a1230;
      --card: #1d1530;
      --card2: #251a3c;
      --border: rgba(192, 132, 252, .18);
      --border2: rgba(216, 180, 254, .26);
      --accent: #a78bfa;
      --accent2: #c084fc;
      --green: #34d399;
      --red: #fb7185;
      --yellow: #fbbf24;
      --purple: #c4b5fd;
      --text: #f5f3ff;
      --text2: #c8bde2;
      --text3: #8f7bad;
      --shadow: 0 26px 80px rgba(0, 0, 0, .46);
      --shadow-soft: 0 14px 34px rgba(0, 0, 0, .28);
      --surface-grad: linear-gradient(135deg, rgba(139, 92, 246, .15), rgba(192, 132, 252, .10));
      --hero-grad:
        radial-gradient(circle at 12% 14%, rgba(139, 92, 246, .18), transparent 35%),
        radial-gradient(circle at 90% 20%, rgba(192, 132, 252, .13), transparent 32%),
        linear-gradient(135deg, #1d1530 0%, #171026 58%, #120d1f 100%);
      --chart-text: #c8bde2;
      --chart-grid: rgba(216, 180, 254, .14);
      --focus-ring: 0 0 0 4px rgba(167, 139, 250, .16);
    }

    html,
    body {
      background: var(--bg) !important;
      color: var(--text) !important;
    }

    body {
      background:
        radial-gradient(circle at 8% -8%, rgba(124, 58, 237, .16), transparent 29%),
        radial-gradient(circle at 102% 4%, rgba(168, 85, 247, .13), transparent 28%),
        radial-gradient(circle at 100% 100%, rgba(236, 72, 153, .06), transparent 30%),
        var(--bg) !important;
    }

    html[data-theme="dark"] body {
      background:
        radial-gradient(circle at 8% -8%, rgba(167, 139, 250, .16), transparent 30%),
        radial-gradient(circle at 102% 4%, rgba(192, 132, 252, .12), transparent 29%),
        radial-gradient(circle at 100% 100%, rgba(236, 72, 153, .08), transparent 30%),
        var(--bg) !important;
    }

    body::before {
      background:
        radial-gradient(circle at 12% 10%, color-mix(in srgb, var(--accent) 14%, transparent), transparent 30%),
        radial-gradient(circle at 88% 14%, color-mix(in srgb, var(--accent2) 12%, transparent), transparent 30%),
        linear-gradient(180deg, color-mix(in srgb, var(--bg) 96%, #fff 4%), var(--bg)) !important;
    }

    .topnav {
      background: color-mix(in srgb, var(--card) 90%, transparent) !important;
      border-bottom-color: var(--border) !important;
      box-shadow: 0 12px 34px rgba(88, 28, 135, .08) !important;
    }

    html[data-theme="dark"] .topnav {
      box-shadow: 0 14px 34px rgba(0, 0, 0, .28) !important;
    }

    .brand i,
    .avatar,
    .emp-avatar-sm,
    .assignee-avatar,
    .emp-detail-avatar,
    .attend-device-icon,
    .login-icon-ring,
    .nav-item.active i {
      background: linear-gradient(135deg, var(--accent), var(--accent2)) !important;
      color: #fff !important;
      box-shadow: 0 12px 28px color-mix(in srgb, var(--accent) 22%, transparent) !important;
    }

    .brand,
    .ph-title,
    .cc-command-hero h2,
    .emp-detail-name,
    .card-title {
      color: var(--text) !important;
    }

    .nav-info .eid,
    .ph-sub,
    .muted-small,
    .dateline,
    .cc-tl-subtitle,
    .emp-detail-meta,
    .card-title,
    .form-hint {
      color: var(--text2) !important;
    }

    .role-badge,
    .rb-employee,
    .rb-teamlead,
    .rb-manager,
    .rb-hr,
    .rb-admin,
    [class*="rb-admin"] {
      background: color-mix(in srgb, var(--accent) 12%, var(--card)) !important;
      color: var(--accent) !important;
      border: 1px solid color-mix(in srgb, var(--accent) 22%, transparent) !important;
    }

    .sidebar {
      background: color-mix(in srgb, var(--card) 88%, transparent) !important;
      border-right-color: var(--border) !important;
      box-shadow: 18px 0 40px rgba(88, 28, 135, .05) !important;
    }

    .nav-section {
      color: var(--text3) !important;
    }

    .nav-item {
      color: var(--text2) !important;
    }

    .nav-item i {
      color: var(--text2) !important;
      background: color-mix(in srgb, var(--accent) 6%, transparent) !important;
    }

    .nav-item:hover {
      background: color-mix(in srgb, var(--accent) 8%, transparent) !important;
      color: var(--text) !important;
    }

    .nav-item.active,
    html[data-theme="light"] .nav-item.active,
    html[data-theme="dark"] .nav-item.active {
      background: linear-gradient(135deg,
        color-mix(in srgb, var(--accent) 17%, transparent),
        color-mix(in srgb, var(--accent2) 12%, transparent)) !important;
      color: var(--accent) !important;
      border: 0 !important;
      box-shadow:
        inset 0 0 0 1px color-mix(in srgb, var(--accent) 22%, transparent),
        0 12px 28px color-mix(in srgb, var(--accent) 10%, transparent) !important;
    }

    .content {
      background: transparent !important;
    }

    .card,
    .hero,
    .work-hero-card,
    .cc-filter-bar,
    .cc-kpi-card,
    .cc-command-hero,
    .emp-detail-card,
    .modal,
    .tbl-wrap,
    .assignee-picker,
    .premium-login-card,
    .login-box {
      background: color-mix(in srgb, var(--card) 94%, transparent) !important;
      border-color: var(--border) !important;
      box-shadow: var(--shadow-soft) !important;
    }

    .hero,
    .work-hero-card,
    .emp-detail-head {
      background: var(--hero-grad) !important;
      border-color: color-mix(in srgb, var(--accent) 18%, var(--border)) !important;
    }

    .hero::before,
    .work-hero-card::before {
      background: linear-gradient(180deg, rgba(255,255,255,.28), transparent 42%) !important;
    }

    html[data-theme="dark"] .hero::before,
    html[data-theme="dark"] .work-hero-card::before {
      background: linear-gradient(180deg, rgba(255,255,255,.06), transparent 42%) !important;
    }

    .work-hero-card::after {
      background: linear-gradient(135deg,
        color-mix(in srgb, var(--accent) 18%, transparent),
        color-mix(in srgb, var(--accent2) 14%, transparent)) !important;
    }

    .clock,
    html[data-theme="light"] .clock {
      background: linear-gradient(135deg, var(--accent), var(--accent2)) !important;
      -webkit-background-clip: text !important;
      background-clip: text !important;
      -webkit-text-fill-color: transparent !important;
      color: var(--accent) !important;
    }

    .btn-primary,
    .login-btn,
    .premium-signin,
    .btn:not(.btn-outline):not(.btn-red):not(.btn-green):not(.btn-warn),
    .assignee-item.is-checked .assignee-chk-box,
    .cc-progress-pill-fill,
    .progress-line span,
    .emp-progress-line span {
      background: linear-gradient(135deg, var(--accent), var(--accent2)) !important;
      border-color: var(--accent) !important;
      color: #fff !important;
      box-shadow: 0 14px 30px color-mix(in srgb, var(--accent) 22%, transparent) !important;
    }

    .btn-primary:hover,
    .login-btn:hover,
    .premium-signin:hover {
      background: linear-gradient(135deg, #6d28d9, #9333ea) !important;
      border-color: #7c3aed !important;
      opacity: 1 !important;
    }

    html[data-theme="dark"] .btn-primary:hover,
    html[data-theme="dark"] .login-btn:hover,
    html[data-theme="dark"] .premium-signin:hover {
      background: linear-gradient(135deg, #8b5cf6, #a855f7) !important;
      border-color: #a78bfa !important;
    }

    .btn-outline,
    .theme-toggle {
      background: color-mix(in srgb, var(--card) 92%, transparent) !important;
      color: var(--text2) !important;
      border-color: var(--border) !important;
      box-shadow: none !important;
    }

    .btn-outline:hover,
    .theme-toggle:hover,
    html[data-theme="light"] .btn-outline:hover,
    html[data-theme="light"] .theme-toggle:hover,
    html[data-theme="dark"] .btn-outline:hover,
    html[data-theme="dark"] .theme-toggle:hover {
      color: var(--accent) !important;
      border-color: var(--border2) !important;
      background: color-mix(in srgb, var(--accent) 8%, var(--card)) !important;
    }

    .btn-purple {
      background: color-mix(in srgb, var(--accent) 14%, var(--card)) !important;
      color: var(--accent) !important;
      border-color: color-mix(in srgb, var(--accent) 24%, transparent) !important;
    }

    .btn-green {
      background: linear-gradient(135deg, #12805c, #16a34a) !important;
      color: #fff !important;
      box-shadow: 0 12px 26px rgba(22, 138, 104, .18) !important;
    }

    .btn-red {
      background: linear-gradient(135deg, #be123c, #e11d48) !important;
      color: #fff !important;
      box-shadow: 0 12px 26px rgba(225, 29, 72, .16) !important;
    }

    .btn-warn {
      background: rgba(184, 117, 20, .10) !important;
      color: var(--yellow) !important;
      border-color: rgba(184, 117, 20, .24) !important;
    }

    .fi,
    .form-group input,
    .form-group select,
    .form-group textarea,
    .cc-fi,
    .assignee-search,
    .input-shell,
    html[data-theme="light"] .fi,
    html[data-theme="light"] .form-group input,
    html[data-theme="light"] .form-group select,
    html[data-theme="light"] .form-group textarea,
    html[data-theme="light"] .cc-fi,
    html[data-theme="light"] .assignee-search {
      background: color-mix(in srgb, var(--card) 96%, transparent) !important;
      border: 1px solid var(--border2) !important;
      color: var(--text) !important;
      box-shadow: none !important;
    }

    .fi:focus,
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus,
    .cc-fi:focus,
    .assignee-search:focus,
    .input-shell:focus-within,
    html[data-theme="light"] .fi:focus,
    html[data-theme="light"] .form-group input:focus,
    html[data-theme="light"] .form-group select:focus,
    html[data-theme="light"] .form-group textarea:focus,
    html[data-theme="light"] .cc-fi:focus {
      border-color: var(--accent) !important;
      box-shadow: var(--focus-ring) !important;
    }

    .form-group label {
      color: var(--text2) !important;
    }

    .filters,
    .ci-row,
    .stat,
    .work-mini,
    .cc-insight-stat,
    .emp-detail-kpi,
    .emp-project-card,
    .emp-task-card,
    .emp-update-card,
    .cc-tl-day-header,
    .cc-tl-update,
    .cc-step-list,
    .cc-tl-note,
    .assignee-picker-top {
      background: color-mix(in srgb, var(--card2) 92%, transparent) !important;
      border-color: var(--border) !important;
    }

    .tbl-wrap {
      background: var(--card) !important;
    }

    thead tr,
    html[data-theme="light"] table thead tr {
      background: color-mix(in srgb, var(--accent) 7%, var(--card)) !important;
    }

    th {
      background: color-mix(in srgb, var(--accent) 7%, var(--card)) !important;
      color: var(--text2) !important;
    }

    td {
      color: var(--text) !important;
      border-color: var(--border) !important;
    }

    tbody tr:hover,
    html[data-theme="light"] table tbody tr:hover {
      background: color-mix(in srgb, var(--accent) 5%, transparent) !important;
    }

    .work-tabs {
      background: color-mix(in srgb, var(--accent) 7%, var(--card)) !important;
      border-color: var(--border) !important;
      box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--accent) 4%, transparent) !important;
    }

    .work-tab,
    html[data-theme="light"] .work-tab {
      color: var(--text2) !important;
      background: transparent !important;
    }

    .work-tab:hover,
    .work-tab.active,
    html[data-theme="light"] .work-tab:hover,
    html[data-theme="light"] .work-tab.active {
      background: linear-gradient(135deg, var(--accent), var(--accent2)) !important;
      color: #fff !important;
      box-shadow: 0 10px 24px color-mix(in srgb, var(--accent) 24%, transparent) !important;
    }

    .tz-badge,
    .sp-idle,
    .pl,
    .pw,
    .cc-period-chip,
    .work-chip,
    .wtype-prj,
    .cc-score-badge,
    .emp-detail-chip,
    .login-live-chip {
      background: color-mix(in srgb, var(--accent) 12%, var(--card)) !important;
      color: var(--accent) !important;
      border: 1px solid color-mix(in srgb, var(--accent) 22%, transparent) !important;
    }

    .sp-present,
    .pp,
    .work-chip.completed,
    .cc-ins-green .v,
    .cc-tl-achieve {
      background: rgba(22, 138, 104, .10) !important;
      color: var(--green) !important;
      border-color: rgba(22, 138, 104, .22) !important;
    }

    .sp-absent,
    .pa,
    .work-chip.blocked,
    .blocked-chip,
    .cc-tl-blocker,
    .cc-no-update-title {
      background: rgba(217, 72, 72, .10) !important;
      color: var(--red) !important;
      border-color: rgba(217, 72, 72, .22) !important;
    }

    .ph,
    .pe,
    .pho,
    .overdue-chip,
    .work-chip.planned {
      background: rgba(184, 117, 20, .10) !important;
      color: var(--yellow) !important;
      border-color: rgba(184, 117, 20, .22) !important;
    }

    .work-chip.project,
    .work-chip.review,
    .work-chip.in_progress {
      background: color-mix(in srgb, var(--accent) 12%, var(--card)) !important;
      color: var(--accent) !important;
      border-color: color-mix(in srgb, var(--accent) 22%, transparent) !important;
    }

    .cc-kpi-card::before,
    .stat::before,
    .emp-update-card {
      border-left-color: var(--accent) !important;
    }

    .cc-kpi-card::before,
    .stat::before,
    .cc-kpi-accent::before,
    .cc-kpi-purple::before,
    .cc-kpi-blue::before,
    html[data-theme="light"] .cc-kpi-blue::before {
      background: linear-gradient(90deg, var(--accent), var(--accent2)) !important;
    }

    .cc-kpi-accent .cc-kpi-icon,
    .cc-kpi-accent .cc-kpi-val,
    .cc-kpi-purple .cc-kpi-icon,
    .cc-kpi-purple .cc-kpi-val,
    .cc-kpi-blue .cc-kpi-icon,
    .cc-kpi-blue .cc-kpi-val,
    html[data-theme="light"] .cc-kpi-blue .cc-kpi-icon,
    html[data-theme="light"] .cc-kpi-blue .cc-kpi-val,
    .work-mini .v,
    .stat .val {
      color: var(--accent) !important;
    }

    .progress-line,
    .cc-progress-bar-wrap,
    .cc-progress-pill,
    .emp-progress-line {
      background: color-mix(in srgb, var(--accent) 12%, transparent) !important;
    }

    .modal-bg {
      background: rgba(46, 16, 101, .30) !important;
      backdrop-filter: blur(12px) !important;
      -webkit-backdrop-filter: blur(12px) !important;
    }

    html[data-theme="dark"] .modal-bg {
      background: rgba(6, 3, 16, .68) !important;
    }

    .toast {
      box-shadow: 0 16px 40px rgba(88, 28, 135, .22) !important;
    }

    .t-info {
      background: linear-gradient(135deg, var(--accent), var(--accent2)) !important;
      color: #fff !important;
    }

    .t-success {
      background: linear-gradient(135deg, #12805c, #16a34a) !important;
    }

    .t-error {
      background: linear-gradient(135deg, #be123c, #e11d48) !important;
    }

    .assignee-item:hover,
    html[data-theme="light"] .assignee-item:hover {
      background: color-mix(in srgb, var(--accent) 7%, transparent) !important;
    }

    .assignee-item.is-checked,
    html[data-theme="light"] .assignee-item.is-checked {
      background: color-mix(in srgb, var(--accent) 11%, transparent) !important;
    }

    .assignee-action-btn:hover {
      background: color-mix(in srgb, var(--accent) 8%, transparent) !important;
      border-color: var(--border2) !important;
      color: var(--accent) !important;
    }

    .assignee-selected-count,
    .cc-tl-count {
      background: color-mix(in srgb, var(--accent) 12%, transparent) !important;
      color: var(--accent) !important;
    }

    .attend-device-blocker {
      background:
        radial-gradient(circle at top left, color-mix(in srgb, var(--accent) 18%, transparent), transparent 34%),
        radial-gradient(circle at bottom right, color-mix(in srgb, var(--accent2) 16%, transparent), transparent 38%),
        var(--bg) !important;
    }

    .attend-device-card {
      background: color-mix(in srgb, var(--card) 96%, transparent) !important;
      border-color: var(--border) !important;
      box-shadow: var(--shadow) !important;
    }

    .attend-device-note {
      background: color-mix(in srgb, var(--accent) 12%, transparent) !important;
      border-color: color-mix(in srgb, var(--accent) 22%, transparent) !important;
      color: var(--accent) !important;
    }

    /* Keep the purple login exactly aligned with the full application theme. */
    .wh-login {
      --bg: #fbf7ff;
      --bg2: #f6edff;
      --card: #ffffff;
      --card2: #fbf8ff;
      --border: rgba(139, 92, 246, .16);
      --border2: #dcc7ff;
      --accent: #7c3aed;
      --accent2: #a855f7;
      --green: #168a68;
      --red: #d94848;
      --yellow: #b87514;
      --purple: #8b5cf6;
      --text: #2e1065;
      --text2: #5b526f;
      --text3: #9b8ab4;
      --shadow: 0 24px 74px rgba(88, 28, 135, .16);
      --shadow-soft: 0 12px 34px rgba(88, 28, 135, .10);
      background:
        radial-gradient(circle at 16% 18%, rgba(124, 58, 237, .16), transparent 30%),
        radial-gradient(circle at 86% 18%, rgba(168, 85, 247, .14), transparent 30%),
        radial-gradient(circle at 18% 82%, rgba(236, 72, 153, .08), transparent 30%),
        linear-gradient(135deg, #fbf7ff 0%, #f3eefe 48%, #fffafc 100%) !important;
    }

    html[data-theme="dark"] .wh-login {
      --bg: #140d22;
      --bg2: #1a1230;
      --card: #1d1530;
      --card2: #251a3c;
      --border: rgba(192, 132, 252, .18);
      --border2: rgba(216, 180, 254, .26);
      --accent: #a78bfa;
      --accent2: #c084fc;
      --green: #34d399;
      --red: #fb7185;
      --yellow: #fbbf24;
      --purple: #c4b5fd;
      --text: #f5f3ff;
      --text2: #c8bde2;
      --text3: #8f7bad;
      background:
        radial-gradient(circle at 14% 18%, rgba(139, 92, 246, .22), transparent 32%),
        radial-gradient(circle at 88% 12%, rgba(192, 132, 252, .16), transparent 30%),
        radial-gradient(circle at 80% 88%, rgba(236, 72, 153, .12), transparent 30%),
        linear-gradient(135deg, #140d22 0%, #1a1230 48%, #0d1020 100%) !important;
    }

    .wh-google-product-chip i,
    .wh-google-status-card i,
    .wh-google-form-panel .login-footnote i,
    .theme-toggle i,
    .cc-eyebrow {
      color: var(--accent) !important;
    }

    .wh-google-form-panel .input-shell input,
    .wh-google-form-panel .input-shell input:focus,
    .wh-google-form-panel .input-shell input:active {
      border: 0 !important;
      border-radius: 0 !important;
      background: transparent !important;
      box-shadow: none !important;
    }

    @supports not (color: color-mix(in srgb, #000 50%, #fff)) {
      .topnav,
      .sidebar,
      .card,
      .hero,
      .work-hero-card,
      .modal,
      .premium-login-card,
      .login-box {
        background: var(--card) !important;
      }
      .nav-item.active,
      .work-tab.active,
      .btn-primary,
      .login-btn,
      .premium-signin,
      .brand i,
      .avatar,
      .emp-avatar-sm,
      .assignee-avatar,
      .emp-detail-avatar {
        background: linear-gradient(135deg, var(--accent), var(--accent2)) !important;
      }
    }


    /* ═══════════════════════════════════════════════════════════════
       WorkHive Minimal 100% Zoom Compact Patch
       Safe scope: final CSS-only density layer. PHP variables, IDs,
       JS hooks, API endpoints, forms, tables and render targets remain
       untouched. This block only reduces visual scale/spacing.
    ═══════════════════════════════════════════════════════════════ */
    :root {
      --sidebar-w: 218px;
      --topnav-h: 52px;
      --r: 14px;
      --shadow: 0 14px 34px rgba(88, 28, 135, .10);
      --shadow-soft: 0 4px 14px rgba(88, 28, 135, .07);
      --focus-ring: 0 0 0 3px rgba(139, 92, 246, .12);
    }

    html,
    body {
      font-size: 14px !important;
      overflow: hidden !important;
    }

    body::before {
      opacity: .55 !important;
    }

    .app {
      height: 100dvh !important;
      overflow: hidden !important;
    }

    .topnav {
      height: var(--topnav-h) !important;
      min-height: var(--topnav-h) !important;
      padding: 0 16px !important;
      gap: 10px !important;
      box-shadow: 0 1px 2px rgba(88, 28, 135, .06) !important;
    }

    .brand {
      font-size: .96rem !important;
      gap: 7px !important;
      letter-spacing: -.02em !important;
    }

    .brand i {
      width: 30px !important;
      height: 30px !important;
      flex: 0 0 30px !important;
      border-radius: 10px !important;
      font-size: .82rem !important;
      box-shadow: none !important;
    }

    .nav-actions {
      gap: 6px !important;
    }

    .nav-user {
      gap: 7px !important;
      padding: 3px 7px 3px 3px !important;
      max-width: 280px !important;
    }

    .avatar {
      width: 28px !important;
      height: 28px !important;
      flex: 0 0 28px !important;
      font-size: .66rem !important;
      box-shadow: none !important;
    }

    .nav-info {
      max-width: 145px !important;
    }

    .nav-info .nm {
      font-size: .78rem !important;
      line-height: 1.15 !important;
    }

    .nav-info .eid {
      font-size: .62rem !important;
      line-height: 1.15 !important;
    }

    .role-badge {
      padding: 2px 8px !important;
      font-size: .58rem !important;
      letter-spacing: .045em !important;
    }

    .btn,
    .theme-toggle,
    .login-btn {
      min-height: 32px !important;
      padding: 6px 12px !important;
      border-radius: 10px !important;
      gap: 6px !important;
      font-size: .76rem !important;
      line-height: 1 !important;
      box-shadow: none !important;
    }

    .btn-sm {
      min-height: 28px !important;
      padding: 4px 9px !important;
      border-radius: 8px !important;
      font-size: .70rem !important;
    }

    .shell {
      display: grid !important;
      grid-template-columns: var(--sidebar-w) minmax(0, 1fr) !important;
      height: calc(100dvh - var(--topnav-h)) !important;
      min-height: 0 !important;
      overflow: hidden !important;
    }

    .sidebar {
      width: auto !important;
      height: calc(100dvh - var(--topnav-h)) !important;
      padding: 10px 8px !important;
      box-shadow: none !important;
      overflow-x: hidden !important;
      overflow-y: auto !important;
    }

    .nav-section {
      padding: 8px 10px 5px !important;
      margin: 2px 0 !important;
      font-size: .58rem !important;
      letter-spacing: .07em !important;
    }

    .nav-item {
      min-height: 34px !important;
      margin: 1px 0 !important;
      padding: 7px 10px !important;
      border-radius: 10px !important;
      gap: 8px !important;
      font-size: .78rem !important;
      line-height: 1.15 !important;
      box-shadow: none !important;
      transform: none !important;
    }

    .nav-item:hover {
      transform: none !important;
    }

    .nav-item i {
      width: 24px !important;
      height: 24px !important;
      flex: 0 0 24px !important;
      border-radius: 8px !important;
      font-size: .76rem !important;
      box-shadow: none !important;
    }

    .content {
      height: calc(100dvh - var(--topnav-h)) !important;
      padding: 14px 16px !important;
      overflow-y: auto !important;
      overflow-x: hidden !important;
    }

    .page.active {
      max-width: 1320px !important;
      margin: 0 auto !important;
    }

    .ph-row {
      margin-bottom: 14px !important;
      gap: 10px !important;
      align-items: center !important;
    }

    .ph-title {
      font-size: 1.05rem !important;
      line-height: 1.15 !important;
      font-weight: 700 !important;
    }

    .ph-sub {
      margin-top: 2px !important;
      font-size: .74rem !important;
      line-height: 1.35 !important;
    }

    .card,
    .hero,
    .work-hero-card,
    .cc-filter-bar,
    .cc-kpi-card,
    .cc-command-hero,
    .emp-detail-card,
    .modal,
    .tbl-wrap,
    .assignee-picker,
    .premium-login-card,
    .login-box {
      border-radius: 14px !important;
      box-shadow: var(--shadow-soft) !important;
    }

    .card,
    .cc-command-hero,
    .emp-detail-card {
      padding: 14px !important;
    }

    .card-title,
    .cc-eyebrow,
    .emp-detail-card-title {
      margin-bottom: 9px !important;
      font-size: .62rem !important;
      letter-spacing: .065em !important;
      line-height: 1.2 !important;
    }

    .g2,
    .g3,
    .g4,
    .cc-two-col,
    .work-board,
    .work-hero,
    .work-form-grid,
    .work-form-grid.two,
    .work-mini-grid,
    .emp-detail-grid,
    .emp-detail-kpis {
      gap: 10px !important;
    }

    .g4,
    .cc-kpi-grid {
      gap: 10px !important;
      margin-bottom: 12px !important;
    }

    .filters,
    .cc-filter-bar {
      margin-bottom: 12px !important;
      padding: 10px 12px !important;
      gap: 7px !important;
      border-radius: 12px !important;
    }

    .fi,
    .form-group input,
    .form-group select,
    .form-group textarea,
    .cc-fi,
    .assignee-search,
    .input-shell {
      min-height: 34px !important;
      border-radius: 9px !important;
      padding: 0 10px !important;
      font-size: .76rem !important;
    }

    .form-group textarea {
      min-height: 76px !important;
      padding-top: 8px !important;
      line-height: 1.35 !important;
    }

    .form-group {
      margin-bottom: 10px !important;
    }

    .form-group label {
      margin-bottom: 5px !important;
      font-size: .66rem !important;
      line-height: 1.2 !important;
    }

    .form-hint {
      font-size: .66rem !important;
      margin-top: 3px !important;
    }

    .hero {
      padding: 20px 18px !important;
      margin-bottom: 14px !important;
      border-radius: 16px !important;
    }

    .clock {
      font-size: 2.25rem !important;
      line-height: .98 !important;
    }

    .dateline {
      margin: 6px 0 14px !important;
      font-size: .76rem !important;
    }

    .tz-badge,
    .status-pill,
    .pill,
    .work-chip,
    .cc-period-chip,
    .emp-detail-chip,
    .cc-score-badge,
    .risk-badge-box,
    .blocked-chip,
    .overdue-chip {
      padding: 2px 7px !important;
      font-size: .64rem !important;
      line-height: 1.25 !important;
      border-radius: 999px !important;
    }

    .status-pill {
      padding: 4px 12px !important;
      margin-bottom: 12px !important;
      font-size: .75rem !important;
    }

    .ci-row {
      gap: 24px !important;
      margin: 10px 0 16px !important;
    }

    .ci-item .t {
      font-size: 1rem !important;
      line-height: 1 !important;
    }

    .ci-item .l {
      font-size: .62rem !important;
      margin-top: 2px !important;
    }

    .action-row {
      gap: 8px !important;
    }

    .action-row .btn {
      min-width: 118px !important;
      min-height: 36px !important;
      padding: 8px 14px !important;
      font-size: .78rem !important;
      border-radius: 10px !important;
    }

    .loc-btn {
      margin-top: 12px !important;
      padding: 4px 10px !important;
      font-size: .68rem !important;
    }

    .stat,
    .work-mini,
    .cc-kpi-card,
    .cc-insight-stat,
    .emp-detail-kpi,
    .emp-project-card,
    .emp-task-card,
    .emp-update-card {
      padding: 11px 12px !important;
      border-radius: 12px !important;
    }

    .stat::before,
    .cc-kpi-card::before {
      height: 3px !important;
    }

    .stat .val,
    .work-mini .v,
    .cc-kpi-val,
    .cc-insight-stat .v,
    .emp-detail-kpi .v {
      font-size: 1.28rem !important;
      line-height: 1 !important;
    }

    .stat .lbl,
    .work-mini .l,
    .cc-kpi-lbl,
    .cc-insight-stat .l,
    .emp-detail-kpi .l {
      margin-top: 3px !important;
      font-size: .62rem !important;
      letter-spacing: .05em !important;
    }

    .stat .ico,
    .cc-kpi-icon {
      right: 10px !important;
      top: 10px !important;
      font-size: 1rem !important;
      opacity: .16 !important;
    }

    .cc-kpi-card {
      gap: 4px !important;
    }

    .cc-insights-card,
    .cc-timeline-card,
    .cc-table-card {
      margin-bottom: 12px !important;
    }

    .cc-insight-row {
      gap: 10px !important;
      margin: 10px 0 !important;
    }

    .cc-progress-bar-wrap {
      height: 5px !important;
      margin-bottom: 10px !important;
    }

    .work-hero {
      grid-template-columns: minmax(0, 1.2fr) minmax(280px, .8fr) !important;
      gap: 12px !important;
      margin-bottom: 12px !important;
    }

    .work-hero-card {
      padding: 16px !important;
      border-radius: 16px !important;
    }

    .work-hero-card::after {
      width: 138px !important;
      height: 138px !important;
      right: -48px !important;
      top: -48px !important;
    }

    .work-mini-grid {
      grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    }

    .work-tabs {
      gap: 4px !important;
      padding: 3px !important;
      border-radius: 12px !important;
      display: flex !important;
      flex-direction: row !important;
      flex-wrap: wrap !important;
      display: flex !important; flex-direction: row !important; flex-wrap: wrap !important;
    }

    .work-tab {
      width: auto !important;
      flex: 1 1 auto !important;
      min-height: 32px !important;
      padding: 6px 10px !important;
      border-radius: 9px !important;
      font-size: .74rem !important;
      line-height: 1.15 !important;
      box-shadow: none !important;
    }

    .work-form-grid,
    .work-form-grid.two {
      gap: 10px !important;
    }

    .muted-small,
    .cc-period-label,
    .cc-tl-subtitle,
    .emp-project-sub,
    .emp-task-sub,
    .emp-update-sub,
    .emp-update-note,
    .cc-tl-note,
    .cc-tl-achieve,
    .cc-tl-blocker,
    .cc-tl-plan {
      font-size: .72rem !important;
      line-height: 1.35 !important;
    }

    .tbl-wrap {
      border-radius: 12px !important;
      overflow-x: auto !important;
    }

    table {
      table-layout: fixed !important;
    }

    th {
      padding: 7px 9px !important;
      font-size: .58rem !important;
      line-height: 1.2 !important;
      letter-spacing: .055em !important;
    }

    td {
      padding: 8px 9px !important;
      font-size: .74rem !important;
      line-height: 1.32 !important;
    }

    .mono {
      font-size: .70rem !important;
    }

    .emp-name-cell {
      gap: 7px !important;
    }

    .emp-avatar-sm,
    .assignee-avatar {
      width: 24px !important;
      height: 24px !important;
      flex: 0 0 24px !important;
      font-size: .58rem !important;
    }

    .chart-wrap {
      height: 158px !important;
    }

    .cc-timeline-day {
      margin-bottom: 14px !important;
    }

    .cc-tl-day-header {
      padding: 6px 10px !important;
      margin-bottom: 8px !important;
      border-radius: 10px !important;
      font-size: .74rem !important;
    }

    .cc-tl-updates {
      gap: 8px !important;
      padding-left: 8px !important;
    }

    .cc-tl-update {
      padding: 10px 12px !important;
      border-radius: 10px !important;
    }

    .cc-tl-update-header {
      gap: 8px !important;
      margin-bottom: 6px !important;
    }

    .cc-tl-empname,
    .cc-tl-title,
    .emp-project-title,
    .emp-task-title,
    .emp-update-title {
      font-size: .78rem !important;
    }

    .cc-tl-proj,
    .cc-tl-hours,
    .cc-tl-pct {
      font-size: .68rem !important;
    }

    .cc-step-list {
      margin-top: 6px !important;
      padding: 7px 9px !important;
      gap: 3px !important;
      border-radius: 9px !important;
    }

    .cc-step-item,
    .emp-step-mini {
      font-size: .70rem !important;
      gap: 5px !important;
    }

    .assignee-picker {
      border-radius: 10px !important;
    }

    .assignee-picker-top {
      padding: 7px 9px !important;
      gap: 6px !important;
    }

    .assignee-action-btn {
      padding: 3px 8px !important;
      font-size: .66rem !important;
      border-radius: 7px !important;
    }

    .assignee-selected-count {
      min-width: 58px !important;
      padding: 2px 7px !important;
      font-size: .66rem !important;
    }

    .assignee-list {
      max-height: 156px !important;
      padding: 4px !important;
    }

    .assignee-item {
      gap: 7px !important;
      padding: 6px 8px !important;
      border-radius: 8px !important;
    }

    .assignee-chk-box {
      width: 15px !important;
      height: 15px !important;
      border-radius: 4px !important;
    }

    .assignee-name {
      font-size: .74rem !important;
    }

    .assignee-eid {
      font-size: .62rem !important;
    }

    .modal-bg {
      padding: 14px !important;
    }

    .modal {
      width: min(500px, 94vw) !important;
      max-height: calc(100dvh - 28px) !important;
      padding: 18px !important;
      border-radius: 16px !important;
    }

    .modal-title {
      margin-bottom: 14px !important;
      font-size: .92rem !important;
      gap: 7px !important;
    }

    .modal-section {
      margin: 12px 0 8px !important;
      padding-bottom: 5px !important;
      font-size: .58rem !important;
    }

    .modal-actions {
      gap: 8px !important;
      margin-top: 14px !important;
    }

    .emp-detail-modal {
      width: min(960px, 96vw) !important;
    }

    .emp-detail-head {
      padding: 16px 18px !important;
      border-radius: 16px 16px 0 0 !important;
    }

    .emp-detail-avatar {
      width: 44px !important;
      height: 44px !important;
      border-radius: 12px !important;
      font-size: .86rem !important;
    }

    .emp-detail-name {
      font-size: 1rem !important;
    }

    .emp-detail-meta {
      gap: 4px !important;
      margin-top: 5px !important;
      font-size: .68rem !important;
    }

    .emp-detail-body {
      padding: 16px 18px 18px !important;
    }

    .emp-detail-kpis {
      gap: 8px !important;
      margin-bottom: 12px !important;
    }

    .emp-info-list {
      gap: 7px !important;
    }

    .emp-info-row {
      padding-bottom: 6px !important;
      font-size: .74rem !important;
    }

    .progress-line,
    .emp-progress-line {
      height: 6px !important;
    }

    .cc-progress-pill {
      height: 18px !important;
      min-width: 58px !important;
    }

    .cc-progress-pill span {
      font-size: .64rem !important;
    }

    .toast {
      right: 14px !important;
      bottom: 14px !important;
      min-width: min(240px, calc(100vw - 28px)) !important;
      max-width: calc(100vw - 28px) !important;
      padding: 10px 12px !important;
      border-radius: 10px !important;
      font-size: .78rem !important;
    }

    /* Compact login keeps the same purple structure but removes oversized spacing. */
    .wh-google-login,
    body.wh-guest .login-screen.wh-google-login {
      padding: 18px !important;
    }

    .wh-google-shell {
      width: min(960px, 100%) !important;
    }

    .wh-google-card {
      min-height: 500px !important;
      grid-template-columns: minmax(0, 1fr) 390px !important;
      border-radius: 24px !important;
    }

    .wh-google-brand-panel {
      padding: 28px !important;
    }

    .wh-google-form-panel {
      padding: 28px 30px !important;
    }

    .wh-google-logo {
      font-size: clamp(1.8rem, 3vw, 2.8rem) !important;
    }

    .wh-google-copy {
      padding: 22px 0 !important;
    }

    .wh-google-copy h1 {
      margin-bottom: 10px !important;
      font-size: clamp(1.9rem, 4vw, 3.35rem) !important;
    }

    .wh-google-copy p,
    .wh-google-form-panel .login-card-top p {
      font-size: .84rem !important;
      line-height: 1.45 !important;
    }

    .wh-google-form-panel .login-card-top {
      margin-bottom: 18px !important;
    }

    .wh-google-form-panel .login-icon-ring {
      width: 40px !important;
      height: 40px !important;
      margin-bottom: 12px !important;
      border-radius: 12px !important;
    }

    .wh-google-form-panel .login-card-title {
      font-size: 1.34rem !important;
    }

    .wh-google-form-panel .role-choice-grid {
      gap: 6px !important;
    }

    .wh-google-form-panel .role-choice {
      min-height: 46px !important;
      border-radius: 12px !important;
      font-size: .60rem !important;
    }

    .wh-google-form-panel .input-shell {
      min-height: 46px !important;
      height: 46px !important;
      border-radius: 12px !important;
      padding: 0 12px !important;
    }

    .wh-google-form-panel .input-shell input,
    .wh-google-form-panel .input-shell input:focus,
    .wh-google-form-panel .input-shell input:active {
      height: 44px !important;
      font-size: .86rem !important;
    }

    .wh-google-form-panel .premium-signin {
      min-height: 42px !important;
      font-size: .84rem !important;
    }

    .wh-google-form-panel .login-footnote {
      margin-top: 12px !important;
      font-size: .66rem !important;
    }

    @media (max-width: 1280px) {
      :root { --sidebar-w: 204px; }
      .content { padding: 12px !important; }
      .page.active { max-width: none !important; }
      .work-hero { grid-template-columns: 1fr !important; }
      .work-mini-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
      .g4,
      .cc-kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    }

    @media (max-width: 1024px) {
      :root { --topnav-h: 54px; }
      .shell {
        display: flex !important;
        flex-direction: column !important;
        height: calc(100dvh - var(--topnav-h)) !important;
      }
      .sidebar {
        width: 100% !important;
        height: auto !important;
        max-height: 68px !important;
        min-height: 54px !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 6px !important;
        padding: 7px 8px !important;
        border-right: 0 !important;
        border-bottom: 1px solid var(--border) !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
      }
      .nav-section { display: none !important; }
      .nav-item {
        flex: 1 1 104px !important;
        justify-content: center !important;
        text-align: center !important;
        padding: 6px 8px !important;
      }
      .content {
        height: calc(100dvh - var(--topnav-h) - 68px) !important;
        padding: 12px !important;
      }
      .wh-google-card {
        grid-template-columns: 1fr !important;
        max-width: 620px !important;
      }
      .wh-google-brand-panel {
        min-height: 190px !important;
        border-right: 0 !important;
        border-bottom: 1px solid var(--border) !important;
      }
    }

    @media (max-width: 760px) {
      :root { --topnav-h: auto; }
      .topnav {
        min-height: 56px !important;
        height: auto !important;
        padding: 8px 10px !important;
        gap: 8px !important;
      }
      .brand { flex: 1 1 auto !important; }
      .nav-spacer { display: none !important; }
      .nav-user {
        order: 3 !important;
        width: 100% !important;
        max-width: none !important;
      }
      .shell { height: calc(100dvh - 104px) !important; }
      .sidebar {
        max-height: 96px !important;
        min-height: 54px !important;
        flex-wrap: wrap !important;
        align-content: flex-start !important;
      }
      .nav-item {
        flex: 1 1 calc(50% - 6px) !important;
        min-height: 32px !important;
        font-size: .72rem !important;
      }
      .content {
        height: calc(100dvh - 104px - 96px) !important;
        padding: 10px !important;
      }
      .g2,
      .g3,
      .g4,
      .cc-two-col,
      .cc-kpi-grid,
      .work-form-grid,
      .work-form-grid.two,
      .work-mini-grid,
      .emp-detail-kpis,
      .emp-detail-grid {
        grid-template-columns: 1fr !important;
      }
      .hero { padding: 18px 12px !important; }
      .clock { font-size: clamp(1.8rem, 11vw, 2.35rem) !important; }
      .ci-row { gap: 12px !important; }
      .action-row .btn,
      .modal-actions .btn {
        width: 100% !important;
        min-width: 0 !important;
      }
      .work-tabs { display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; }
      .modal { padding: 16px !important; }
      .wh-google-login,
      body.wh-guest .login-screen.wh-google-login {
        padding: 12px !important;
        align-items: flex-start !important;
        overflow-y: auto !important;
      }
      .wh-google-card { border-radius: 20px !important; }
      .wh-google-brand-panel { padding: 22px 18px !important; }
      .wh-google-form-panel { padding: 24px 18px 20px !important; }
      .wh-google-form-panel .role-choice-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    }

    @media (max-width: 430px) {
      .brand { font-size: .88rem !important; }
      .brand i { width: 28px !important; height: 28px !important; }
      .wh-google-form-panel .role-choice-grid {
        grid-template-columns: 1fr !important;
      }
    }

    /* WorkHive palette refresh: #E8EDF2 / #2C3947 / #547A95 / #C2A56D */
    :root,
    html[data-theme="light"] {
      --bg: #E8EDF2;
      --bg2: #F3F6F8;
      --card: #FFFFFF;
      --card2: #F8FAFB;
      --border: rgba(44, 57, 71, .12);
      --border2: rgba(84, 122, 149, .34);
      --accent: #547A95;
      --accent2: #C2A56D;
      --green: #3E7A63;
      --red: #B85B5B;
      --yellow: #C2A56D;
      --purple: #6B6F9B;
      --text: #2C3947;
      --text2: rgba(44, 57, 71, .72);
      --text3: rgba(44, 57, 71, .48);
      --shadow: 0 18px 48px rgba(44, 57, 71, .12);
      --shadow-soft: 0 8px 22px rgba(44, 57, 71, .08);
      --focus-ring: 0 0 0 3px rgba(84, 122, 149, .16);
      --surface-grad: linear-gradient(135deg, rgba(84, 122, 149, .10), rgba(194, 165, 109, .10));
      --hero-grad:
        radial-gradient(circle at 12% 8%, rgba(84, 122, 149, .16), transparent 34%),
        radial-gradient(circle at 88% 4%, rgba(194, 165, 109, .18), transparent 34%),
        linear-gradient(135deg, #E8EDF2 0%, #F8FAFB 52%, #EDF1F4 100%);
      --chart-text: rgba(44, 57, 71, .72);
      --chart-grid: rgba(44, 57, 71, .12);
    }

    html[data-theme="dark"] {
      --bg: #1F2933;
      --bg2: #263342;
      --card: #2C3947;
      --card2: #334252;
      --border: rgba(232, 237, 242, .12);
      --border2: rgba(232, 237, 242, .20);
      --accent: #86ABC4;
      --accent2: #C2A56D;
      --green: #74B49B;
      --red: #E08C8C;
      --yellow: #D6BC7D;
      --purple: #A9A6D6;
      --text: #F4F7F9;
      --text2: rgba(244, 247, 249, .72);
      --text3: rgba(244, 247, 249, .48);
      --shadow: 0 22px 60px rgba(0, 0, 0, .32);
      --shadow-soft: 0 10px 28px rgba(0, 0, 0, .22);
      --focus-ring: 0 0 0 3px rgba(134, 171, 196, .18);
      --surface-grad: linear-gradient(135deg, rgba(134, 171, 196, .14), rgba(194, 165, 109, .12));
      --hero-grad:
        radial-gradient(circle at 12% 8%, rgba(134, 171, 196, .18), transparent 34%),
        radial-gradient(circle at 88% 4%, rgba(194, 165, 109, .16), transparent 34%),
        #1F2933;
      --chart-text: rgba(244, 247, 249, .72);
      --chart-grid: rgba(244, 247, 249, .12);
    }

    html,
    body,
    .login-screen {
      background: var(--hero-grad) !important;
    }

    .wh-login {
      --bg: #E8EDF2;
      --bg2: #F3F6F8;
      --card: #FFFFFF;
      --card2: #F8FAFB;
      --border: rgba(44, 57, 71, .12);
      --border2: rgba(84, 122, 149, .34);
      --accent: #547A95;
      --accent2: #C2A56D;
      --green: #3E7A63;
      --red: #B85B5B;
      --yellow: #C2A56D;
      --purple: #6B6F9B;
      --text: #2C3947;
      --text2: rgba(44, 57, 71, .72);
      --text3: rgba(44, 57, 71, .48);
      background:
        radial-gradient(circle at 14% 18%, rgba(84, 122, 149, .18), transparent 31%),
        radial-gradient(circle at 86% 12%, rgba(194, 165, 109, .20), transparent 30%),
        linear-gradient(135deg, #E8EDF2 0%, #F7F9FA 48%, #EEF2F5 100%) !important;
    }

    html[data-theme="dark"] .wh-login {
      --bg: #1F2933;
      --bg2: #263342;
      --card: #2C3947;
      --card2: #334252;
      --border: rgba(232, 237, 242, .12);
      --border2: rgba(232, 237, 242, .20);
      --accent: #86ABC4;
      --accent2: #C2A56D;
      --green: #74B49B;
      --red: #E08C8C;
      --yellow: #D6BC7D;
      --purple: #A9A6D6;
      --text: #F4F7F9;
      --text2: rgba(244, 247, 249, .72);
      --text3: rgba(244, 247, 249, .48);
      background:
        radial-gradient(circle at 14% 18%, rgba(134, 171, 196, .18), transparent 31%),
        radial-gradient(circle at 86% 12%, rgba(194, 165, 109, .16), transparent 30%),
        linear-gradient(135deg, #1F2933 0%, #263342 52%, #202B36 100%) !important;
    }

    .topnav,
    .sidebar,
    .card,
    .hero,
    .work-hero-card,
    .cc-filter-bar,
    .cc-kpi-card,
    .modal,
    .tbl-wrap,
    .assignee-picker,
    .premium-login-card,
    .login-box,
    .wh-google-card {
      border-color: var(--border) !important;
      box-shadow: var(--shadow-soft) !important;
    }

    .brand,
    .clock,
    .login-logo .b,
    .login-logo .li {
      background: linear-gradient(135deg, #2C3947, #547A95 58%, #C2A56D) !important;
      -webkit-background-clip: text !important;
      -webkit-text-fill-color: transparent !important;
    }

    .brand i,
    .avatar,
    .login-icon-ring,
    .btn-primary,
    .login-btn,
    .premium-signin,
    .emp-avatar-sm,
    .assignee-avatar,
    .emp-detail-avatar {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      color: #fff !important;
    }

    .btn-warn,
    .btn-purple,
    .login-live-chip,
    .wh-google-product-chip,
    .tz-badge,
    .cc-period-chip {
      background: rgba(194, 165, 109, .14) !important;
      border-color: rgba(194, 165, 109, .34) !important;
      color: #8A6D2F !important;
    }

    .btn-outline:hover,
    .nav-item:hover,
    .nav-item.active,
    .work-tab.active,
    .role-choice.active,
    .assignee-item.is-checked,
    tbody tr:hover,
    html[data-theme="light"] table tbody tr:hover {
      background: rgba(84, 122, 149, .10) !important;
      color: var(--accent) !important;
      border-color: rgba(84, 122, 149, .36) !important;
    }

    .nav-item.active {
      border-left-color: var(--accent) !important;
    }

    .fi:focus,
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus,
    .cc-fi:focus,
    .input-shell:focus-within {
      border-color: var(--accent) !important;
      box-shadow: var(--focus-ring) !important;
    }

    thead tr,
    html[data-theme="light"] table thead tr,
    .assignee-picker-top,
    .cc-tl-day-header {
      background: rgba(232, 237, 242, .82) !important;
    }

    .wh-google-card {
      overflow: hidden !important;
      background: rgba(255, 255, 255, .92) !important;
    }

    .wh-google-brand-panel {
      background:
        linear-gradient(135deg, rgba(44, 57, 71, .96), rgba(84, 122, 149, .92)),
        #2C3947 !important;
      color: #fff !important;
      border-right: 1px solid rgba(232, 237, 242, .16) !important;
    }

    .wh-google-brand-panel::before {
      background:
        radial-gradient(circle at 16% 16%, rgba(194, 165, 109, .32), transparent 28%),
        radial-gradient(circle at 88% 22%, rgba(232, 237, 242, .16), transparent 28%) !important;
    }

    .wh-google-logo {
      display: inline-flex !important;
      gap: .14em !important;
      color: #fff !important;
      letter-spacing: 0 !important;
      -webkit-text-fill-color: currentColor !important;
      text-shadow: 0 10px 24px rgba(0, 0, 0, .16) !important;
    }

    .wh-google-logo span:first-child {
      color: #E8EDF2 !important;
    }

    .wh-google-logo span:last-child {
      color: #C2A56D !important;
    }

    .g-blue,
    .g-red,
    .g-yellow,
    .g-green {
      color: currentColor !important;
    }

    .wh-google-copy h1,
    .wh-google-copy p,
    .wh-google-status-card,
    .wh-google-product-chip {
      color: #F4F7F9 !important;
    }

    .wh-google-product-chip,
    .wh-google-status-card {
      background: rgba(232, 237, 242, .10) !important;
      border-color: rgba(232, 237, 242, .18) !important;
    }

    .wh-google-product-chip i,
    .wh-google-status-card i,
    .wh-google-form-panel .login-footnote i,
    .theme-toggle i,
    .cc-eyebrow,
    .input-shell > i {
      color: var(--accent) !important;
    }

    .role-choice,
    .input-shell,
    .fi,
    .form-group input,
    .form-group select,
    .form-group textarea,
    .cc-fi,
    .assignee-search {
      background: #FFFFFF !important;
      border-color: var(--border2) !important;
      color: var(--text) !important;
    }

    .role-choice.active i,
    .role-choice.active span {
      color: var(--accent) !important;
    }

    .progress-line span,
    .cc-progress-bar,
    .emp-progress-line span {
      background: linear-gradient(90deg, #547A95, #C2A56D) !important;
    }

    .sp-present,
    .pp,
    .work-chip.completed {
      background: rgba(62, 122, 99, .13) !important;
      color: #2F6B55 !important;
    }

    .sp-absent,
    .pa,
    .work-chip.blocked {
      background: rgba(184, 91, 91, .13) !important;
      color: #9E4545 !important;
    }

    .ph,
    .pe,
    .work-chip.planned {
      background: rgba(194, 165, 109, .16) !important;
      color: #8A6D2F !important;
    }

    /* ═══════════════════════════════════════════════════════════════
       WorkSync — Final Polish & Full Responsive Layer
       Palette: #E8EDF2 · #2C3947 · #547A95 · #C2A56D
       Font: Inter (body) · JetBrains Mono (mono)
       Scope: UI-only. No IDs, JS hooks, PHP vars, or API routes changed.
    ═══════════════════════════════════════════════════════════════ */

    /* ── Base typography lock ── */
    html, body, input, select, textarea, button {
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
    }
    .mono, code, pre,
    .clock, .stat .val, .nav-info .eid,
    .ci-item .t, td.mono, span.mono {
      font-family: 'JetBrains Mono', 'Menlo', 'Monaco', 'Consolas', ui-monospace, monospace !important;
    }

    /* ── WorkSync brand gradient ── */
    .brand {
      font-size: 1.08rem !important;
      font-weight: 750 !important;
      letter-spacing: -.025em !important;
      background: linear-gradient(135deg, #2C3947 0%, #547A95 55%, #C2A56D 100%) !important;
      -webkit-background-clip: text !important;
      -webkit-text-fill-color: transparent !important;
    }
    .brand i {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      -webkit-text-fill-color: #fff !important;
      color: #fff !important;
    }

    /* ── Login logo colours ── */
    .wh-google-logo span:first-child { color: #E8EDF2 !important; }
    .wh-google-logo span:last-child  { color: #C2A56D !important; }

    /* ── Topnav ── */
    .topnav {
      background: rgba(232,237,242,.96) !important;
      backdrop-filter: blur(20px) !important;
      -webkit-backdrop-filter: blur(20px) !important;
      border-bottom: 1px solid rgba(44,57,71,.12) !important;
    }
    html[data-theme="dark"] .topnav {
      background: rgba(44,57,71,.94) !important;
      border-bottom-color: rgba(232,237,242,.12) !important;
    }

    /* ── Sidebar active state ── */
    .nav-item.active {
      background: rgba(84,122,149,.13) !important;
      color: #547A95 !important;
      border-left-color: #547A95 !important;
    }
    .nav-item.active i {
      background: rgba(84,122,149,.18) !important;
      color: #547A95 !important;
    }

    /* ── Primary buttons ── */
    .btn-primary,
    .login-btn,
    .premium-signin {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      border-color: transparent !important;
      box-shadow: 0 6px 18px rgba(44,57,71,.22) !important;
    }
    .btn-primary:hover,
    .login-btn:hover,
    .premium-signin:hover {
      background: linear-gradient(135deg, #3d6680, #1e2a36) !important;
      transform: translateY(-1px) !important;
      box-shadow: 0 10px 28px rgba(44,57,71,.30) !important;
    }

    /* ── Gold accent chip / badge ── */
    .role-badge {
      background: rgba(194,165,109,.15) !important;
      color: #7A6030 !important;
      border-color: rgba(194,165,109,.30) !important;
    }
    html[data-theme="dark"] .role-badge {
      background: rgba(194,165,109,.18) !important;
      color: #C2A56D !important;
      border-color: rgba(194,165,109,.30) !important;
    }  

    /* ── KPI card accent line colours using palette ── */
    .cc-kpi-accent::before { background: #547A95 !important; }
    .cc-kpi-accent .cc-kpi-icon,
    .cc-kpi-accent .cc-kpi-val { color: #547A95 !important; }
    .cc-kpi-blue::before { background: #547A95 !important; }
    .cc-kpi-blue .cc-kpi-icon,
    .cc-kpi-blue .cc-kpi-val { color: #547A95 !important; }
    .cc-kpi-yellow::before { background: #C2A56D !important; }
    .cc-kpi-yellow .cc-kpi-icon,
    .cc-kpi-yellow .cc-kpi-val { color: #C2A56D !important; }

    /* ── Hero clock ── */
    .clock {
      background: linear-gradient(135deg, #2C3947, #547A95 58%, #C2A56D) !important;
      -webkit-background-clip: text !important;
      -webkit-text-fill-color: transparent !important;
    }

    /* ── Table header subtle ── */
    thead tr,
    html[data-theme="light"] table thead tr {
      background: rgba(232,237,242,.80) !important;
    }
    html[data-theme="dark"] thead tr {
      background: rgba(44,57,71,.60) !important;
    }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb {
      background: rgba(84,122,149,.32);
      border-radius: 99px;
    }
    ::-webkit-scrollbar-thumb:hover { background: rgba(84,122,149,.55); }

    /* ── Focus ring palette ── */
    *:focus-visible {
      outline: 2px solid #547A95 !important;
      outline-offset: 2px !important;
    }

    /* ── Responsive: Mobile-first full support ── */

    /* Large desktop tweaks */
    @media (min-width: 1440px) {
      .content { padding: 32px 40px; }
      .cc-kpi-grid { grid-template-columns: repeat(4, minmax(0,1fr)); }
    }

    /* Medium tablet (1024–1280px) */
    @media (max-width: 1280px) and (min-width: 1025px) {
      :root { --sidebar-w: 220px; }
      .content { padding: 20px 24px; }
      .g4 { grid-template-columns: repeat(2, minmax(0,1fr)); }
      .cc-kpi-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
    }

    /* Tablet portrait (768–1024px) */
    @media (max-width: 1024px) and (min-width: 769px) {
      .topnav { padding: 0 16px; gap: 12px; }
      .nav-info .nm { max-width: 130px; }
      .sidebar {
        max-height: 80px !important;
        min-height: 64px !important;
        padding: 8px 12px !important;
      }
      .nav-item { flex: 1 1 auto; min-width: 80px; max-width: 140px; font-size: .78rem; }
      .content { padding: 16px 20px; }
      .g3 { grid-template-columns: 1fr 1fr; }
      .cc-kpi-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
      .work-hero { grid-template-columns: 1fr; }
    }

    /* Mobile (≤768px) */
    @media (max-width: 768px) {
      .topnav {
        padding: 8px 12px !important;
        height: auto !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
      }
      .nav-spacer { display: none !important; }
      .nav-user { width: 100%; max-width: none !important; order: 3; }
      .nav-actions { margin-left: auto; }
      #themeText { display: none !important; }
      .brand { font-size: 1rem !important; }

      .sidebar {
        flex-wrap: wrap !important;
        max-height: none !important;
        height: auto !important;
      }
      .nav-item { flex: 1 1 calc(50% - 8px); min-height: 40px; font-size: .77rem; }

      .content { padding: 12px 10px !important; }

      .g2, .g3, .g4 { grid-template-columns: 1fr !important; }
      .cc-kpi-grid { grid-template-columns: 1fr 1fr !important; gap: 10px !important; }
      .work-hero { grid-template-columns: 1fr !important; gap: 12px; }

      .hero { padding: 24px 14px !important; }
      .clock { font-size: clamp(2rem, 12vw, 3rem) !important; }
      .ci-row { gap: 10px; flex-wrap: wrap; justify-content: center; }
      .action-row { flex-direction: column; align-items: stretch; }
      .action-row .btn { min-width: 0 !important; width: 100%; }

      .work-tabs { display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; gap: 6px; }

      .filters { flex-direction: column; }
      .filters .fi, .filters .btn { width: 100%; }

      .modal { padding: 18px !important; }
      .modal-actions { flex-direction: column; }
      .modal-actions .btn { width: 100%; }

      .ph-row { flex-direction: column; gap: 12px; }
      .ph-row > * { width: 100%; }
    }

    /* Small mobile (≤430px) */
    @media (max-width: 430px) {
      .cc-kpi-grid { grid-template-columns: 1fr !important; }
      .work-tabs { display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; }
      .role-choice-grid { grid-template-columns: 1fr 1fr !important; }
      .nav-item { flex: 1 1 calc(50% - 6px); }
      .brand { font-size: .92rem !important; }
      .brand i { width: 30px !important; height: 30px !important; border-radius: 10px !important; font-size: .8rem !important; }
    }

    /* ── Device blocker — full-mobile styled ── */
    .attend-device-card {
      background: #2C3947 !important;
      border: 1px solid rgba(84,122,149,.30) !important;
    }
    .attend-device-icon {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
    }
    .attend-device-note {
      background: rgba(84,122,149,.15) !important;
      border-color: rgba(84,122,149,.28) !important;
      color: #86ABC4 !important;
    }

    /* ═══════════════════════════════════════════════════════════════
       WorkSync — Mobile Nav Drawer + Work-Tab Dropdown + Full Fix
       Scope: CSS-only new components. No IDs, backend, or JS hooks changed.
    ═══════════════════════════════════════════════════════════════ */

    /* ── Hamburger button (hidden on desktop) ── */
    .mob-menu-btn {
      display: none;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border: 1px solid var(--border2);
      border-radius: 10px;
      background: transparent;
      color: var(--text);
      cursor: pointer;
      font-size: 1rem;
      flex-shrink: 0;
      transition: background .15s, border-color .15s;
    }
    .mob-menu-btn:hover {
      background: rgba(84,122,149,.10);
      border-color: #547A95;
      color: #547A95;
    }

    /* ── Overlay backdrop ── */
    .mob-nav-overlay {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 299;
      background: rgba(44,57,71,.48);
      backdrop-filter: blur(3px);
      -webkit-backdrop-filter: blur(3px);
    }
    .mob-nav-overlay.open { display: block; animation: fadeIn .2s ease; }

    @keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
    @keyframes slideInLeft {
      from { transform: translateX(-100%); opacity: 0 }
      to   { transform: translateX(0);    opacity: 1 }
    }
    @keyframes slideOutLeft {
      from { transform: translateX(0);    opacity: 1 }
      to   { transform: translateX(-100%); opacity: 0 }
    }

    /* ── Mobile drawer state ── */
    .sidebar.mob-open {
      transform: translateX(0) !important;
      box-shadow: 4px 0 40px rgba(44,57,71,.30) !important;
    }

    /* ── Work-tabs dropdown wrapper ── */
    .work-tabs-wrap {
      position: relative;
      width: 100%;
    }

    /* Mobile tab dropdown trigger — hidden on desktop */
    .work-tab-mob-btn {
      display: none;
      width: 100%;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding: 11px 16px;
      background: var(--card);
      border: 1px solid var(--border2);
      border-radius: 12px;
      color: var(--text);
      font-family: 'Inter', sans-serif;
      font-size: .88rem;
      font-weight: 600;
      cursor: pointer;
      transition: border-color .15s, background .15s;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .work-tab-mob-btn:hover {
      border-color: #547A95;
      background: rgba(84,122,149,.07);
    }
    .work-tab-mob-btn[aria-expanded="true"] {
      border-color: #547A95;
      background: rgba(84,122,149,.09);
    }
    .work-tab-mob-chevron {
      flex-shrink: 0;
      font-size: .75rem;
      color: var(--text2);
      transition: transform .2s ease;
    }
    .work-tab-mob-btn[aria-expanded="true"] .work-tab-mob-chevron {
      transform: rotate(180deg);
    }
    #workTabMobLabel {
      display: flex;
      align-items: center;
      gap: 7px;
      min-width: 0;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    /* Dropdown panel (mobile) */
    .work-tabs.mob-dropdown {
      position: absolute;
      top: calc(100% + 6px);
      left: 0;
      right: 0;
      z-index: 100;
      background: var(--card);
      border: 1px solid var(--border2);
      border-radius: 14px;
      box-shadow: 0 16px 40px rgba(44,57,71,.18);
      overflow: hidden;
      animation: fadeUp .18s ease;
      display: flex !important;
      flex-direction: column !important;
      gap: 0 !important;
      padding: 6px !important;
    }
    .work-tabs.mob-dropdown .work-tab {
      width: 100% !important;
      text-align: left !important;
      border-radius: 9px !important;
      justify-content: flex-start !important;
      border: none !important;
      background: transparent !important;
      margin: 0 !important;
    }
    .work-tabs.mob-dropdown .work-tab:hover {
      background: rgba(84,122,149,.10) !important;
      color: #547A95 !important;
    }
    .work-tabs.mob-dropdown .work-tab.active {
      background: rgba(84,122,149,.14) !important;
      color: #547A95 !important;
      font-weight: 700 !important;
    }
    .work-tabs.mob-dropdown .work-tab.active::after {
      display: none !important;
    }

    /* ── Full responsive overhaul ── */

    /* Remove the old device blocker — allow mobile access */
    .attend-device-blocker { display: none !important; }

    /* Override old max-width:1199px rule that hid the whole app on mobile */
    @media (max-width: 1199px), (hover: none) and (pointer: coarse) {
      html, body { overflow-x: hidden !important; overflow-y: auto !important; }
      .login-screen { display: flex !important; }
      .app { display: flex !important; }
      .modal-bg { display: flex !important; }
      .toast { display: block !important; }
      /* Body class overrides (correct state) */
      body.wh-guest   .login-screen { display: flex !important; }
      body.wh-guest   .app          { display: none !important; }
      body.wh-logged-in .login-screen { display: none !important; }
      body.wh-logged-in .app          { display: flex !important; }
    }

    /* ── Mobile-only drawer user card — hidden on desktop ── */
    .mob-drawer-user { display: none; }

    /* ── Desktop default: sidebar visible, no hamburger ── */
    @media (min-width: 1025px) {
      .mob-menu-btn { display: none !important; }
      .sidebar {
        position: static !important;
        transform: none !important;
        height: calc(100dvh - var(--topnav-h, 68px)) !important;
        min-width: 200px;
      }
      .work-tab-mob-btn { display: none !important; }
      .work-tabs { display: flex !important; flex-wrap: wrap !important; position: static !important;
        box-shadow: none !important; border: none !important; border-radius: 0 !important;
        padding: 0 !important; flex-direction: row !important; background: transparent !important; }
    }

    /* ── Tablet (769–1024px): sidebar becomes top horizontal bar ── */
    @media (max-width: 1024px) and (min-width: 769px) {
      .mob-menu-btn { display: none !important; }
      .shell { flex-direction: column !important; }
      .sidebar {
        position: static !important;
        transform: none !important;
        width: 100% !important;
        height: auto !important;
        max-height: 72px !important;
        min-height: 56px !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
        padding: 8px 12px !important;
        border-right: none !important;
        border-bottom: 1px solid var(--border) !important;
        gap: 4px !important;
      }
      .nav-section { display: none !important; }
      .nav-item {
        flex: 0 0 auto !important;
        min-width: 90px;
        max-width: none;
        padding: 8px 14px !important;
        border-radius: 8px !important;
        border-left: none !important;
        font-size: .78rem !important;
        white-space: nowrap;
        justify-content: center !important;
        text-align: center !important;
      }
      .nav-item i {
        width: 22px; height: 22px; flex: 0 0 22px;
        border-radius: 7px; font-size: .78rem;
      }
      .content {
        height: calc(100dvh - var(--topnav-h, 68px) - 72px) !important;
        overflow-y: auto;
        padding: 18px 20px !important;
      }
      .work-tab-mob-btn { display: none !important; }
      .work-tabs { display: flex !important; flex-wrap: wrap !important; }
    }

    /* ── Mobile (≤768px): drawer nav + tab dropdown ── */
    @media (max-width: 768px) {
      /* Topnav */
      :root { --topnav-h: auto; }
      .topnav {
        position: relative;
        z-index: 300;
        min-height: 56px !important;
        height: auto !important;
        padding: 10px 14px !important;
        flex-wrap: nowrap !important;
        gap: 10px !important;
        background: rgba(232,237,242,.97) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
      }
      html[data-theme="dark"] .topnav {
        background: rgba(44,57,71,.97) !important;
      }
      .mob-menu-btn { display: flex !important; order: 0; }
      .brand { order: 1; flex: 1; font-size: .98rem !important; }
      .nav-spacer { display: none !important; }
      .nav-actions { order: 2; flex-shrink: 0; gap: 6px !important; }
      .nav-user { display: none !important; } /* shown in drawer instead */
      #themeText { display: none !important; }
      .theme-toggle, .nav-actions .btn { min-height: 34px !important; padding: 6px 10px !important; font-size: .8rem !important; }

      /* Drawer sidebar */
      .shell {
        position: relative !important;
        display: block !important;
        height: calc(100dvh - 56px) !important;
        overflow: hidden;
      }
      .sidebar {
        position: fixed !important;
        top: 56px !important;
        left: 0 !important;
        width: min(300px, 82vw) !important;
        height: calc(100dvh - 56px) !important;
        z-index: 300 !important;
        transform: translateX(-105%) !important;
        transition: transform .26s cubic-bezier(.4,0,.2,1), box-shadow .26s !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        background: var(--card) !important;
        border-right: 1px solid var(--border) !important;
        border-radius: 0 18px 18px 0 !important;
        flex-direction: column !important;
        display: flex !important;
        padding: 16px 12px !important;
        gap: 2px !important;
        align-items: stretch !important;
        justify-content: flex-start !important;
      }
      /* User card inside drawer */
      .sidebar::before {
        content: '';
        display: block;
        height: 0;
      }
      .sidebar .nav-section {
        display: block !important;
        padding: 6px 14px 4px !important;
        margin: 6px 0 2px !important;
      }
      .sidebar .nav-item {
        flex: none !important;
        width: 100% !important;
        max-width: none !important;
        min-width: 0 !important;
        min-height: 46px !important;
        border-radius: 12px !important;
        border-left: 3px solid transparent !important;
        text-align: left !important;
        justify-content: flex-start !important;
        padding: 11px 14px !important;
        font-size: .88rem !important;
        white-space: normal;
        gap: 10px !important;
      }
      .sidebar .nav-item i {
        width: 32px; height: 32px; flex: 0 0 32px;
        border-radius: 10px; font-size: .85rem;
      }
      .sidebar .nav-item.active {
        border-left-color: #547A95 !important;
        background: rgba(84,122,149,.12) !important;
        color: #547A95 !important;
      }

      /* Main content takes full width */
      .content {
        width: 100% !important;
        height: calc(100dvh - 56px) !important;
        overflow-y: auto !important;
        padding: 14px 12px !important;
      }

      /* Mobile user card at top of drawer */
      .mob-drawer-user {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 14px 12px;
        margin-bottom: 8px;
        border-bottom: 1px solid var(--border);
      }

      /* Work tabs → dropdown */
      .work-tab-mob-btn { display: flex !important; margin-bottom: 4px; }
      .work-tabs {
        display: none !important;
      }
      .work-tabs.mob-dropdown {
        display: flex !important;
      }

      /* Grids */
      .g2, .g3, .g4 { grid-template-columns: 1fr !important; }
      .cc-kpi-grid { grid-template-columns: 1fr 1fr !important; gap: 10px !important; }
      .work-hero { grid-template-columns: 1fr !important; gap: 14px !important; }
      .work-hero > .card { order: 2; }
      .work-hero-card { order: 1; }

      /* Hero */
      .hero { padding: 22px 14px !important; border-radius: 14px !important; }
      .clock { font-size: clamp(2rem, 11vw, 2.8rem) !important; }
      .ci-row { gap: 8px; flex-wrap: wrap; justify-content: center; }
      .action-row { flex-direction: column; align-items: stretch; }
      .action-row .btn { min-width: 0 !important; width: 100%; }

      /* Tables go card mode */
      .tbl-wrap { border: none !important; background: transparent !important; }
      .tbl-wrap table { display: block; }
      .tbl-wrap thead { display: none; }
      .tbl-wrap tbody { display: flex; flex-direction: column; gap: 10px; }
      .tbl-wrap tbody tr {
        display: block !important;
        background: var(--card) !important;
        border: 1px solid var(--border) !important;
        border-radius: 14px !important;
        padding: 12px !important;
        box-shadow: var(--shadow-soft) !important;
      }
      .tbl-wrap tbody td {
        display: flex !important;
        justify-content: space-between !important;
        align-items: flex-start !important;
        border-top: none !important;
        padding: 5px 4px !important;
        font-size: .83rem !important;
        gap: 8px;
        flex-wrap: wrap;
      }
      .tbl-wrap tbody td:not(:last-child) {
        border-bottom: 1px solid var(--border);
        padding-bottom: 7px !important;
        margin-bottom: 2px;
      }
      /* Pseudo labels via data-label */
      .tbl-wrap tbody td[data-label]::before {
        content: attr(data-label);
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text3);
        flex-shrink: 0;
        min-width: 90px;
      }

      /* Filters stack */
      .filters { flex-direction: column !important; }
      .filters .fi, .filters .btn { width: 100% !important; }

      /* ph-row */
      .ph-row { flex-direction: column !important; gap: 12px !important; }
      .ph-row > * { width: 100% !important; }

      /* Modals */
      .modal-bg { padding: 10px !important; align-items: flex-end !important; }
      .modal {
        width: 100% !important;
        max-height: 92dvh !important;
        border-radius: 20px 20px 0 0 !important;
        padding: 20px !important;
      }
      .modal-actions { flex-direction: column; }
      .modal-actions .btn { width: 100%; }

      /* KPI cards compact */
      .cc-kpi-card { padding: 14px 16px 12px !important; }
      .cc-kpi-val { font-size: 1.4rem !important; }

      /* Card padding reduce */
      .card { padding: 14px !important; border-radius: 14px !important; }
      .cc-filter-bar { padding: 12px 14px !important; }
    }

    /* ── Small mobile (≤480px) ── */
    @media (max-width: 480px) {
      .topnav { padding: 8px 10px !important; }
      .brand { font-size: .9rem !important; }
      .brand i { width: 28px !important; height: 28px !important; border-radius: 9px !important; font-size: .78rem !important; }
      .cc-kpi-grid { grid-template-columns: 1fr !important; }
      .role-choice-grid { grid-template-columns: 1fr 1fr !important; }
      .work-tab-mob-btn { font-size: .82rem !important; padding: 10px 13px !important; }
      .hero { padding: 18px 12px !important; }
      .clock { font-size: clamp(1.8rem, 10vw, 2.4rem) !important; }
    }

    /* ── Prevent horizontal scroll everywhere ── */
    html, body { overflow-x: hidden !important; max-width: 100vw; }
    .app, .shell, .content, .topnav, .sidebar, .page,
    .card, .hero, .work-hero, .modal, .login-screen,
    .login-shell, .tbl-wrap, .filters, .cc-filter-bar {
      max-width: 100% !important;
      min-width: 0 !important;
    }

    /* ═══════════════════════════════════════════════════════════════
       WorkSync UI Redesign — Login · Work Tabs · Add Employee Modal
       Palette: #E8EDF2 · #2C3947 · #547A95 · #C2A56D
       Safe scope: CSS + minor HTML additions only.
    ═══════════════════════════════════════════════════════════════ */

    /* ── 1. LOGIN PAGE — Full redesign ── */

    /* Background */
    .wh-google-login,
    body.wh-guest .login-screen.wh-google-login,
    .login-screen.wh-google-login {
      background:
        radial-gradient(ellipse 80% 60% at 0% 0%,   rgba(84,122,149,.22), transparent 55%),
        radial-gradient(ellipse 70% 55% at 100% 100%, rgba(194,165,109,.18), transparent 55%),
        radial-gradient(ellipse 50% 40% at 80% 10%,  rgba(44,57,71,.10), transparent 45%),
        linear-gradient(160deg, #E8EDF2 0%, #F3F6F8 40%, #EDF1F4 70%, #E8EDF2 100%) !important;
      align-items: center !important;
      justify-content: center !important;
    }

    html[data-theme="dark"] .wh-google-login,
    html[data-theme="dark"] body.wh-guest .login-screen.wh-google-login,
    html[data-theme="dark"] .login-screen.wh-google-login {
      background:
        radial-gradient(ellipse 80% 60% at 0% 0%,    rgba(84,122,149,.20), transparent 55%),
        radial-gradient(ellipse 70% 55% at 100% 100%, rgba(194,165,109,.14), transparent 55%),
        linear-gradient(160deg, #1A2330 0%, #1F2C3A 50%, #1A2330 100%) !important;
    }

    /* Card — glassmorphism with palette */
    .wh-google-card {
      grid-template-columns: minmax(0, 1.1fr) 420px !important;
      border-radius: 28px !important;
      border: 1px solid rgba(84,122,149,.18) !important;
      background: rgba(255,255,255,.88) !important;
      box-shadow:
        0 32px 80px rgba(44,57,71,.16),
        0 1px 0 rgba(255,255,255,.85) inset !important;
      backdrop-filter: blur(24px) !important;
      -webkit-backdrop-filter: blur(24px) !important;
    }

    html[data-theme="dark"] .wh-google-card {
      background: rgba(30,42,55,.92) !important;
      border-color: rgba(84,122,149,.24) !important;
      box-shadow: 0 36px 90px rgba(0,0,0,.45), 0 1px 0 rgba(255,255,255,.06) inset !important;
    }

    /* Brand panel — rich gradient */
    .wh-google-brand-panel {
      padding: 48px 44px !important;
      background:
        radial-gradient(ellipse 100% 80% at 10% 20%, rgba(84,122,149,.28), transparent 55%),
        radial-gradient(ellipse 80% 70% at 88% 80%,  rgba(194,165,109,.22), transparent 50%),
        radial-gradient(ellipse 60% 50% at 50% 50%,  rgba(44,57,71,.06), transparent 60%),
        linear-gradient(145deg, #2C3947 0%, #3A4F64 40%, #2C3947 70%, #1E2D3D 100%) !important;
      border-right: 1px solid rgba(255,255,255,.08) !important;
      position: relative;
      overflow: hidden;
    }

    /* Decorative glow rings on brand panel */
    .wh-google-brand-panel::before {
      content: '' !important;
      display: block !important;
      position: absolute !important;
      bottom: -80px !important; right: -80px !important;
      width: 320px !important; height: 320px !important;
      border-radius: 999px !important;
      border: 1px solid rgba(194,165,109,.18) !important;
      box-shadow: inset 0 0 80px rgba(194,165,109,.08) !important;
      pointer-events: none !important;
    }

    .wh-google-brand-panel::after {
      content: '' !important;
      display: block !important;
      position: absolute !important;
      top: -40px !important; left: -40px !important;
      width: 200px !important; height: 200px !important;
      border-radius: 999px !important;
      border: 1px solid rgba(84,122,149,.20) !important;
      pointer-events: none !important;
    }

    /* Logo text on dark panel */
    .wh-google-brand-panel .wh-google-logo {
      font-size: clamp(2.4rem, 4.5vw, 3.8rem) !important;
      letter-spacing: -.06em !important;
      font-weight: 800 !important;
      -webkit-text-fill-color: initial !important;
      background: none !important;
    }

    .wh-google-brand-panel .wh-google-logo span:first-child {
      color: #E8EDF2 !important;
      -webkit-text-fill-color: #E8EDF2 !important;
    }

    .wh-google-brand-panel .wh-google-logo span:last-child {
      color: #C2A56D !important;
      -webkit-text-fill-color: #C2A56D !important;
    }

    /* Product chip on dark panel */
    .wh-google-brand-panel .wh-google-product-chip {
      background: rgba(255,255,255,.10) !important;
      border-color: rgba(255,255,255,.16) !important;
      color: rgba(232,237,242,.90) !important;
      box-shadow: none !important;
    }

    .wh-google-brand-panel .wh-google-product-chip i {
      color: #C2A56D !important;
    }

    /* Brand panel copy text */
    .wh-google-copy h1 {
      color: #E8EDF2 !important;
      font-size: clamp(2rem, 4.5vw, 3.8rem) !important;
      font-weight: 750 !important;
      letter-spacing: -.055em !important;
      line-height: 1 !important;
      text-shadow: 0 16px 40px rgba(0,0,0,.24) !important;
    }

    .wh-google-copy p {
      color: rgba(232,237,242,.75) !important;
      font-size: .98rem !important;
    }

    /* Status cards on dark panel */
    .wh-google-status-card {
      background: rgba(255,255,255,.09) !important;
      border-color: rgba(255,255,255,.14) !important;
      color: rgba(232,237,242,.85) !important;
      box-shadow: none !important;
      backdrop-filter: blur(10px) !important;
    }

    .wh-google-status-card i {
      color: #C2A56D !important;
    }

    /* Form panel */
    .wh-google-form-panel {
      padding: 44px 42px !important;
      background: rgba(255,255,255,.97) !important;
      position: relative;
    }

    html[data-theme="dark"] .wh-google-form-panel {
      background: rgba(28,40,52,.97) !important;
    }

    /* Live clock chip */
    .wh-google-form-panel .login-live-chip {
      background: rgba(232,237,242,.90) !important;
      border-color: rgba(84,122,149,.22) !important;
      color: #547A95 !important;
      box-shadow: 0 6px 16px rgba(44,57,71,.08) !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .login-live-chip {
      background: rgba(44,57,71,.70) !important;
      border-color: rgba(84,122,149,.28) !important;
      color: #86ABC4 !important;
    }

    /* Icon ring */
    .wh-google-form-panel .login-icon-ring {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      border: none !important;
      color: #fff !important;
      box-shadow: 0 14px 32px rgba(44,57,71,.24) !important;
      animation: none !important;
    }

    /* Card title */
    .wh-google-form-panel .login-card-title {
      color: #2C3947 !important;
      font-size: 1.72rem !important;
      font-weight: 750 !important;
      letter-spacing: -.045em !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .login-card-title {
      color: #E8EDF2 !important;
    }

    .wh-google-form-panel .login-card-top p {
      color: rgba(44,57,71,.65) !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .login-card-top p {
      color: rgba(232,237,242,.60) !important;
    }

    /* Labels */
    .wh-google-form-panel label,
    .wh-google-form-panel .login-role-group label,
    .wh-google-form-panel .smart-field label {
      color: rgba(44,57,71,.65) !important;
      font-weight: 700 !important;
      letter-spacing: .06em !important;
    }

    html[data-theme="dark"] .wh-google-form-panel label {
      color: rgba(232,237,242,.60) !important;
    }

    /* Role choice buttons */
    .wh-google-form-panel .role-choice {
      min-height: 60px !important;
      background: #F3F6F8 !important;
      border: 1.5px solid rgba(84,122,149,.16) !important;
      color: #547A95 !important;
      border-radius: 14px !important;
      box-shadow: 0 2px 8px rgba(44,57,71,.06) !important;
      font-size: .69rem !important;
      font-weight: 700 !important;
      transition: transform .15s, box-shadow .15s, border-color .15s, background .15s !important;
    }

    .wh-google-form-panel .role-choice i {
      font-size: .95rem !important;
      color: #547A95 !important;
    }

    .wh-google-form-panel .role-choice:hover {
      background: rgba(84,122,149,.12) !important;
      border-color: #547A95 !important;
      color: #2C3947 !important;
      transform: translateY(-2px) !important;
      box-shadow: 0 8px 20px rgba(84,122,149,.16) !important;
    }

    .wh-google-form-panel .role-choice.active {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      border-color: transparent !important;
      color: #fff !important;
      box-shadow: 0 10px 26px rgba(44,57,71,.28) !important;
    }

    .wh-google-form-panel .role-choice.active i {
      color: #C2A56D !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .role-choice {
      background: rgba(44,57,71,.60) !important;
      border-color: rgba(84,122,149,.22) !important;
      color: #86ABC4 !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .role-choice.active {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      color: #fff !important;
      border-color: transparent !important;
    }

    /* Input shell */
    .wh-google-form-panel .input-shell {
      background: #F3F6F8 !important;
      border: 1.5px solid rgba(84,122,149,.18) !important;
      border-radius: 14px !important;
      min-height: 52px !important;
    }

    .wh-google-form-panel .input-shell:focus-within {
      border-color: #547A95 !important;
      background: #fff !important;
      box-shadow: 0 0 0 4px rgba(84,122,149,.12), 0 4px 12px rgba(44,57,71,.08) !important;
    }

    .wh-google-form-panel .input-shell > i {
      color: #547A95 !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .input-shell {
      background: rgba(44,57,71,.55) !important;
      border-color: rgba(84,122,149,.22) !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .input-shell:focus-within {
      background: rgba(44,57,71,.80) !important;
      border-color: #86ABC4 !important;
      box-shadow: 0 0 0 4px rgba(84,122,149,.14) !important;
    }

    /* Sign in button */
    .wh-google-form-panel .premium-signin,
    .wh-google-form-panel .login-btn {
      background: linear-gradient(135deg, #547A95 0%, #2C3947 60%, #1E2D3D 100%) !important;
      border: none !important;
      border-radius: 14px !important;
      min-height: 52px !important;
      font-size: .95rem !important;
      font-weight: 700 !important;
      letter-spacing: -.01em !important;
      box-shadow: 0 10px 32px rgba(44,57,71,.30), 0 2px 0 rgba(255,255,255,.08) inset !important;
      position: relative;
      overflow: hidden;
      transition: transform .18s, box-shadow .18s !important;
    }

    .wh-google-form-panel .premium-signin:hover,
    .wh-google-form-panel .login-btn:hover {
      transform: translateY(-2px) !important;
      box-shadow: 0 16px 40px rgba(44,57,71,.36), 0 2px 0 rgba(255,255,255,.08) inset !important;
    }

    /* Gold accent stripe on button */
    .wh-google-form-panel .premium-signin::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 2px;
      background: linear-gradient(90deg, transparent 10%, #C2A56D 50%, transparent 90%);
      opacity: .70;
    }

    /* Footnote */
    .wh-google-form-panel .login-footnote span {
      background: rgba(232,237,242,.80) !important;
      border-color: rgba(84,122,149,.16) !important;
      color: rgba(44,57,71,.65) !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .login-footnote span {
      background: rgba(44,57,71,.50) !important;
      color: rgba(232,237,242,.60) !important;
    }

    .wh-google-form-panel .login-footnote i {
      color: #547A95 !important;
    }

    /* Error state */
    .wh-google-form-panel .login-error {
      background: rgba(184,91,91,.10) !important;
      border-color: rgba(184,91,91,.24) !important;
      color: #9E4545 !important;
    }

    /* Responsive — panel stacks vertically ≤ 900px */
    @media (max-width: 900px) {
      .wh-google-card {
        grid-template-columns: 1fr !important;
        max-width: 500px !important;
        margin: 0 auto !important;
      }
      .wh-google-brand-panel {
        min-height: 200px !important;
        padding: 30px 28px !important;
        border-right: none !important;
        border-bottom: 1px solid rgba(255,255,255,.10) !important;
      }
      .wh-google-copy h1 { font-size: clamp(1.8rem, 7vw, 2.8rem) !important; }
      .wh-google-status-grid { gap: 7px !important; }
      .wh-google-form-panel { padding: 32px 28px !important; }
      .wh-google-form-panel .login-live-chip {
        position: static !important;
        width: max-content;
        margin-bottom: 16px;
      }
    }

    @media (max-width: 480px) {
      .wh-google-card { border-radius: 20px !important; }
      .wh-google-brand-panel { padding: 22px 18px !important; }
      .wh-google-form-panel { padding: 24px 18px !important; }
      .wh-google-form-panel .role-choice-grid {
        grid-template-columns: repeat(3, minmax(0,1fr)) !important;
        gap: 6px !important;
      }
      .wh-google-form-panel .role-choice:last-child {
        grid-column: 2 / span 1 !important;
      }
    }

    /* ── 2. WORK TRACKING TABS — Fix vertical stacking issue ── */

    /* Reset conflicting stacked layout */
    .work-tabs-wrap { position: relative !important; width: 100% !important; }

    .work-tabs {
      display: flex !important;
      flex-direction: row !important;
      flex-wrap: wrap !important;
      gap: 6px !important;
      padding: 4px !important;
      margin-top: 16px !important;
      background: rgba(44,57,71,.07) !important;
      border: 1px solid rgba(84,122,149,.14) !important;
      border-radius: 16px !important;
      align-items: center !important;
    }

    html[data-theme="dark"] .work-tabs {
      background: rgba(255,255,255,.05) !important;
      border-color: rgba(84,122,149,.18) !important;
    }

    .work-tab {
      flex: 1 1 auto !important;
      height: auto !important;
      min-height: 36px !important;
      padding: 8px 14px !important;
      border-radius: 11px !important;
      border: none !important;
      background: transparent !important;
      color: rgba(44,57,71,.70) !important;
      font-size: .80rem !important;
      font-weight: 650 !important;
      white-space: nowrap !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 6px !important;
      cursor: pointer !important;
      transition: background .15s, color .15s, box-shadow .15s !important;
      box-shadow: none !important;
    }

    html[data-theme="dark"] .work-tab {
      color: rgba(232,237,242,.60) !important;
    }

    .work-tab:hover {
      background: rgba(84,122,149,.14) !important;
      color: #2C3947 !important;
      transform: none !important;
    }

    html[data-theme="dark"] .work-tab:hover {
      background: rgba(84,122,149,.20) !important;
      color: #E8EDF2 !important;
    }

    .work-tab.active {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      color: #fff !important;
      border: none !important;
      box-shadow: 0 6px 18px rgba(44,57,71,.24) !important;
      transform: none !important;
    }

    /* Gold accent dot on active tab */
    .work-tab.active i {
      color: #C2A56D !important;
    }

    /* Mobile dropdown button */
    .work-tab-mob-btn {
      display: none;
      background: var(--card) !important;
      border: 1.5px solid rgba(84,122,149,.20) !important;
      border-radius: 12px !important;
      color: #2C3947 !important;
      font-weight: 650 !important;
    }

    html[data-theme="dark"] .work-tab-mob-btn {
      background: rgba(44,57,71,.70) !important;
      color: #E8EDF2 !important;
    }

    @media (max-width: 768px) {
      .work-tab-mob-btn { display: flex !important; }
      .work-tabs { display: none !important; }
      .work-tabs.mob-dropdown {
        display: flex !important;
        flex-direction: column !important;
        position: absolute !important;
        top: calc(100% + 6px) !important;
        left: 0 !important; right: 0 !important;
        z-index: 100 !important;
        background: var(--card) !important;
        border: 1px solid rgba(84,122,149,.22) !important;
        border-radius: 14px !important;
        padding: 6px !important;
        box-shadow: 0 16px 40px rgba(44,57,71,.18) !important;
      }
      .work-tabs.mob-dropdown .work-tab {
        width: 100% !important;
        text-align: left !important;
        justify-content: flex-start !important;
        border-radius: 9px !important;
        background: transparent !important;
        box-shadow: none !important;
        color: #2C3947 !important;
      }
      html[data-theme="dark"] .work-tabs.mob-dropdown .work-tab {
        color: #E8EDF2 !important;
      }
      .work-tabs.mob-dropdown .work-tab.active {
        background: linear-gradient(135deg, #547A95, #2C3947) !important;
        color: #fff !important;
      }
    }

    /* ── 3. ADD EMPLOYEE MODAL — Redesign ── */

    #addEmpModal .modal {
      background: #fff !important;
      border-radius: 22px !important;
      border: 1px solid rgba(84,122,149,.14) !important;
      box-shadow: 0 32px 80px rgba(44,57,71,.18), 0 1px 0 rgba(255,255,255,.90) inset !important;
      padding: 0 !important;
      overflow: hidden !important;
      width: min(560px, 95vw) !important;
    }

    html[data-theme="dark"] #addEmpModal .modal {
      background: #1C2A38 !important;
      border-color: rgba(84,122,149,.22) !important;
    }

    /* Modal header strip */
    #addEmpModal .modal-title {
      margin: 0 !important;
      padding: 22px 28px 20px !important;
      background: linear-gradient(135deg, #2C3947, #3A4F64) !important;
      color: #E8EDF2 !important;
      font-size: 1.05rem !important;
      font-weight: 750 !important;
      border-radius: 0 !important;
      position: relative;
      overflow: hidden;
    }

    #addEmpModal .modal-title::after {
      content: '';
      position: absolute;
      right: -30px; bottom: -30px;
      width: 120px; height: 120px;
      border-radius: 999px;
      border: 1px solid rgba(194,165,109,.22);
      pointer-events: none;
    }

    #addEmpModal .modal-title i {
      width: 34px; height: 34px;
      display: inline-flex; align-items: center; justify-content: center;
      background: rgba(194,165,109,.22) !important;
      color: #C2A56D !important;
      border-radius: 10px;
      font-size: .9rem;
    }

    /* Modal body */
    #addEmpModal .modal > *:not(.modal-title) {
      padding-left: 28px !important;
      padding-right: 28px !important;
    }

    #addEmpModal .modal-section {
      padding-top: 20px !important;
      padding-bottom: 8px !important;
      margin: 0 !important;
      color: #547A95 !important;
      font-size: .68rem !important;
      font-weight: 800 !important;
      letter-spacing: .09em !important;
      border-bottom: 1px solid rgba(84,122,149,.14) !important;
    }

    /* First section top padding */
    #addEmpModal .modal-section:first-of-type {
      padding-top: 24px !important;
    }

    #addEmpModal .form-group {
      margin-bottom: 14px !important;
    }

    #addEmpModal .form-group label {
      font-size: .68rem !important;
      font-weight: 750 !important;
      letter-spacing: .06em !important;
      text-transform: uppercase !important;
      color: rgba(44,57,71,.65) !important;
      margin-bottom: 6px !important;
    }

    html[data-theme="dark"] #addEmpModal .form-group label {
      color: rgba(232,237,242,.60) !important;
    }

    #addEmpModal .form-group input,
    #addEmpModal .form-group select {
      width: 100% !important;
      padding: 10px 14px !important;
      border: 1.5px solid rgba(84,122,149,.18) !important;
      border-radius: 11px !important;
      background: #F3F6F8 !important;
      color: #2C3947 !important;
      font-size: .88rem !important;
      font-weight: 550 !important;
      outline: none !important;
      transition: border-color .15s, box-shadow .15s, background .15s !important;
    }

    html[data-theme="dark"] #addEmpModal .form-group input,
    html[data-theme="dark"] #addEmpModal .form-group select {
      background: rgba(44,57,71,.55) !important;
      border-color: rgba(84,122,149,.22) !important;
      color: #E8EDF2 !important;
    }

    #addEmpModal .form-group input:focus,
    #addEmpModal .form-group select:focus {
      border-color: #547A95 !important;
      background: #fff !important;
      box-shadow: 0 0 0 4px rgba(84,122,149,.12) !important;
    }

    html[data-theme="dark"] #addEmpModal .form-group input:focus,
    html[data-theme="dark"] #addEmpModal .form-group select:focus {
      background: rgba(44,57,71,.80) !important;
      border-color: #86ABC4 !important;
    }

    #addEmpModal .form-hint {
      color: rgba(84,122,149,.75) !important;
      font-size: .70rem !important;
      margin-top: 4px !important;
    }

    /* Two-column grid inside modal */
    #addEmpModal .g2 {
      margin-top: 4px !important;
    }

    /* Modal actions bar */
    #addEmpModal .modal-actions {
      padding: 18px 28px 24px !important;
      margin: 0 !important;
      background: rgba(232,237,242,.50) !important;
      border-top: 1px solid rgba(84,122,149,.10) !important;
      gap: 10px !important;
      justify-content: flex-end !important;
    }

    html[data-theme="dark"] #addEmpModal .modal-actions {
      background: rgba(44,57,71,.30) !important;
      border-top-color: rgba(84,122,149,.15) !important;
    }

    #addEmpModal .modal-actions .btn-outline {
      border-color: rgba(84,122,149,.30) !important;
      color: #547A95 !important;
      border-radius: 10px !important;
    }

    #addEmpModal .modal-actions .btn-outline:hover {
      background: rgba(84,122,149,.08) !important;
      border-color: #547A95 !important;
    }

    #addEmpModal .modal-actions .btn-green {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      border: none !important;
      border-radius: 10px !important;
      box-shadow: 0 8px 22px rgba(44,57,71,.24) !important;
      font-weight: 700 !important;
    }

    #addEmpModal .modal-actions .btn-green:hover {
      transform: translateY(-1px) !important;
      box-shadow: 0 12px 28px rgba(44,57,71,.30) !important;
    }

    /* ── 4. Work hero card polish ── */
    .work-hero-card {
      background:
        radial-gradient(ellipse 80% 60% at 100% 100%, rgba(194,165,109,.12), transparent 50%),
        radial-gradient(ellipse 60% 50% at 0% 0%, rgba(84,122,149,.10), transparent 45%),
        var(--card) !important;
      border: 1px solid rgba(84,122,149,.14) !important;
      border-radius: 20px !important;
      padding: 24px !important;
    }

    .work-hero-card .ph-title {
      font-size: 1.15rem !important;
      font-weight: 750 !important;
      color: #2C3947 !important;
      letter-spacing: -.025em !important;
    }

    html[data-theme="dark"] .work-hero-card .ph-title {
      color: #E8EDF2 !important;
    }

    .work-hero-card .ph-sub {
      color: rgba(44,57,71,.60) !important;
      font-size: .83rem !important;
      line-height: 1.55 !important;
      margin-top: 6px !important;
    }

    html[data-theme="dark"] .work-hero-card .ph-sub {
      color: rgba(232,237,242,.55) !important;
    }

    /* ╔══════════════════════════════════════════════════════════════╗
       ║  WorkSync — NUCLEAR TAB FIX + BOLD UI REDESIGN              ║
       ║  Highest-specificity block — always wins cascade             ║
       ╚══════════════════════════════════════════════════════════════╝ */

    /* === WORK TABS — horizontal row, no exceptions === */
    html body .app .shell .content .work-tabs-wrap .work-tabs,
    html body #appShell #page-work .work-tabs,
    .work-hero-card .work-tabs,
    .work-hero-card .work-tabs-wrap .work-tabs,
    #workTabs {
      display: flex !important;
      flex-direction: row !important;
      flex-wrap: wrap !important;
      grid-template-columns: none !important;
      align-items: center !important;
      gap: 5px !important;
      padding: 5px !important;
      margin-top: 18px !important;
      background: rgba(44,57,71,.10) !important;
      border: 1.5px solid rgba(84,122,149,.18) !important;
      border-radius: 16px !important;
      width: 100% !important;
      box-sizing: border-box !important;
      position: static !important;
    }

    html[data-theme="dark"] #workTabs,
    html[data-theme="dark"] .work-hero-card .work-tabs {
      background: rgba(255,255,255,.06) !important;
      border-color: rgba(84,122,149,.22) !important;
    }

    /* Tab buttons — horizontal pill items */
    html body .app .shell .content .work-tabs-wrap .work-tabs .work-tab,
    html body #appShell #page-work .work-tabs .work-tab,
    .work-hero-card .work-tabs .work-tab,
    #workTabs .work-tab {
      display: inline-flex !important;
      flex-direction: row !important;
      flex: 1 1 auto !important;
      width: auto !important;
      min-width: 0 !important;
      max-width: none !important;
      height: auto !important;
      min-height: 36px !important;
      padding: 7px 12px !important;
      border-radius: 11px !important;
      border: none !important;
      background: transparent !important;
      color: rgba(44,57,71,.62) !important;
      font-size: .79rem !important;
      font-weight: 650 !important;
      white-space: nowrap !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 6px !important;
      cursor: pointer !important;
      text-align: center !important;
      line-height: 1.2 !important;
      box-shadow: none !important;
      transform: none !important;
      letter-spacing: .005em !important;
      transition: background .15s, color .15s, box-shadow .18s !important;
    }

    html[data-theme="dark"] #workTabs .work-tab {
      color: rgba(232,237,242,.52) !important;
    }

    /* Active tab */
    html body .app .shell .content .work-tabs-wrap .work-tabs .work-tab.active,
    .work-hero-card .work-tabs .work-tab.active,
    #workTabs .work-tab.active {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      color: #ffffff !important;
      box-shadow: 0 5px 18px rgba(44,57,71,.26), 0 1px 0 rgba(255,255,255,.12) inset !important;
      transform: none !important;
      border: none !important;
      font-weight: 700 !important;
    }

    #workTabs .work-tab.active i,
    .work-hero-card .work-tabs .work-tab.active i { color: #C2A56D !important; }

    /* Hover tab */
    html body .app .shell .content .work-tabs-wrap .work-tabs .work-tab:not(.active):hover,
    #workTabs .work-tab:not(.active):hover,
    .work-hero-card .work-tabs .work-tab:not(.active):hover {
      background: rgba(84,122,149,.15) !important;
      color: #2C3947 !important;
      transform: none !important;
      box-shadow: none !important;
    }

    html[data-theme="dark"] #workTabs .work-tab:not(.active):hover { color: #E8EDF2 !important; }

    /* === BOLD UI — Topnav dark bar === */
    .topnav {
      background: linear-gradient(90deg, #2C3947 0%, #3A4F64 100%) !important;
      border-bottom: 1px solid rgba(84,122,149,.28) !important;
      box-shadow: 0 2px 20px rgba(44,57,71,.22) !important;
    }

    .brand {
      background: linear-gradient(135deg, #E8EDF2, #C2A56D 65%) !important;
      -webkit-background-clip: text !important;
      -webkit-text-fill-color: transparent !important;
      font-weight: 800 !important;
    }

    .brand i {
      background: linear-gradient(135deg, #547A95, #C2A56D) !important;
      -webkit-text-fill-color: #fff !important;
      color: #fff !important;
      box-shadow: 0 6px 16px rgba(84,122,149,.30) !important;
    }

    .nav-info .nm { color: #E8EDF2 !important; }
    .nav-info .eid { color: rgba(232,237,242,.60) !important; }

    .theme-toggle,
    .nav-actions .btn-outline {
      background: rgba(255,255,255,.09) !important;
      border-color: rgba(255,255,255,.18) !important;
      color: #E8EDF2 !important;
      border-radius: 10px !important;
    }

    .theme-toggle:hover, .nav-actions .btn-outline:hover {
      background: rgba(194,165,109,.18) !important;
      border-color: rgba(194,165,109,.45) !important;
      color: #C2A56D !important;
    }

    .avatar {
      background: linear-gradient(135deg, #547A95, #C2A56D) !important;
      box-shadow: 0 0 0 2px rgba(255,255,255,.22) !important;
      color: #fff !important;
    }

    .role-badge {
      background: rgba(194,165,109,.18) !important;
      color: #C2A56D !important;
      border: 1px solid rgba(194,165,109,.30) !important;
      font-weight: 700 !important;
    }

    /* === BOLD UI — Sidebar === */
    .sidebar {
      background: linear-gradient(180deg, #F0F4F7 0%, #E8EDF2 100%) !important;
      border-right: 1.5px solid rgba(84,122,149,.14) !important;
    }

    html[data-theme="dark"] .sidebar {
      background: linear-gradient(180deg, #1C2A38 0%, #172130 100%) !important;
      border-right-color: rgba(84,122,149,.20) !important;
    }

    .nav-section {
      color: #547A95 !important;
      font-weight: 800 !important;
      letter-spacing: .12em !important;
    }

    .nav-item { color: rgba(44,57,71,.68) !important; }
    html[data-theme="dark"] .nav-item { color: rgba(232,237,242,.58) !important; }

    .nav-item:hover { background: rgba(84,122,149,.12) !important; color: #2C3947 !important; }
    html[data-theme="dark"] .nav-item:hover { color: #E8EDF2 !important; }

    .nav-item.active {
      background: linear-gradient(90deg, rgba(84,122,149,.18), rgba(84,122,149,.06)) !important;
      color: #547A95 !important;
      border-left: 3px solid #547A95 !important;
      font-weight: 700 !important;
    }

    .nav-item i { background: rgba(84,122,149,.12) !important; color: #547A95 !important; }
    .nav-item.active i { background: rgba(84,122,149,.22) !important; }

    /* === BOLD UI — Content area === */
    .content {
      background: linear-gradient(155deg, #E8EDF2 0%, #EDF1F5 50%, #E8EDF2 100%) !important;
    }

    html[data-theme="dark"] .content {
      background: linear-gradient(155deg, #161F29 0%, #1B2838 100%) !important;
    }

    /* === BOLD UI — Cards === */
    .card {
      background: #ffffff !important;
      border: 1px solid rgba(84,122,149,.12) !important;
      box-shadow: 0 2px 14px rgba(44,57,71,.07) !important;
    }

    html[data-theme="dark"] .card {
      background: #1E2D3D !important;
      border-color: rgba(84,122,149,.18) !important;
    }

    .card-title { color: #547A95 !important; font-weight: 800 !important; letter-spacing: .10em !important; }

    /* KPI top bar */
    .cc-kpi-card::before { background: linear-gradient(90deg, #547A95, #C2A56D) !important; }

    /* Tables */
    thead tr { background: linear-gradient(90deg, rgba(232,237,242,.90), rgba(232,237,242,.70)) !important; }
    html[data-theme="dark"] thead tr { background: rgba(44,57,71,.55) !important; }
    th { color: #547A95 !important; font-weight: 800 !important; }

    /* === BOLD UI — Buttons === */
    .btn-primary {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      border: none !important;
      box-shadow: 0 6px 18px rgba(44,57,71,.22) !important;
    }

    .btn-green {
      background: linear-gradient(135deg, #3E7A63, #2D5C4A) !important;
      box-shadow: 0 6px 16px rgba(62,122,99,.22) !important;
    }

    .btn-outline { border-color: rgba(84,122,149,.28) !important; color: #547A95 !important; }
    .btn-outline:hover { background: rgba(84,122,149,.09) !important; border-color: #547A95 !important; }

    /* === BOLD UI — Attendance hero === */
    .hero {
      background: linear-gradient(135deg, #ffffff, #F6F9FB) !important;
      border: 1.5px solid rgba(84,122,149,.12) !important;
      box-shadow: 0 6px 28px rgba(44,57,71,.08) !important;
    }

    html[data-theme="dark"] .hero {
      background: linear-gradient(135deg, #1E2D3D, #253240) !important;
      border-color: rgba(84,122,149,.20) !important;
    }

    .clock {
      background: linear-gradient(135deg, #2C3947, #547A95 55%, #C2A56D) !important;
      -webkit-background-clip: text !important;
      -webkit-text-fill-color: transparent !important;
      font-size: clamp(2.8rem, 6vw, 3.4rem) !important;
    }

    .tz-badge {
      background: rgba(84,122,149,.10) !important;
      border-color: rgba(84,122,149,.20) !important;
      color: #547A95 !important;
    }

    .sp-present { background: rgba(62,122,99,.13) !important; color: #2F6B55 !important; border-color: rgba(62,122,99,.22) !important; }
    .sp-absent  { background: rgba(184,91,91,.11) !important; color: #9E4545 !important; border-color: rgba(184,91,91,.20) !important; }
    .sp-idle    { background: rgba(84,122,149,.11) !important; color: #547A95 !important; border-color: rgba(84,122,149,.20) !important; }

    /* Check-in/out CTA buttons */
    #btnIn {
      background: linear-gradient(135deg, #3E7A63, #2D5C4A) !important;
      box-shadow: 0 8px 24px rgba(62,122,99,.28) !important;
      min-width: 156px !important; min-height: 50px !important;
      border-radius: 14px !important; font-size: .95rem !important; font-weight: 700 !important;
    }

    #btnOut {
      background: linear-gradient(135deg, #B85B5B, #8B3A3A) !important;
      box-shadow: 0 8px 24px rgba(184,91,91,.28) !important;
      min-width: 156px !important; min-height: 50px !important;
      border-radius: 14px !important; font-size: .95rem !important; font-weight: 700 !important;
    }

    /* Progress bars */
    .cc-progress-bar, .progress-line span, .emp-progress-line span {
      background: linear-gradient(90deg, #547A95, #C2A56D) !important;
    }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(84,122,149,.32); border-radius: 99px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(84,122,149,.55); }

    /* ═══════════════════════════════════════════════════════════════════════
       WorkSync — FINAL RICH UI POLISH
       · Base font scale (16px root, normalized rem)
       · Smooth scrolling everywhere
       · Attendance section redesign
       · Add Employee modal redesign
       · Kill purple/pink remnants
       · Required field styling, form input polish, select custom arrow
    ═══════════════════════════════════════════════════════════════════════ */

    /* 1 ─ GLOBAL SCALE & SMOOTHNESS ──────────────────────────────────────── */
    html {
      font-size: 16px !important;
      scroll-behavior: smooth !important;
      -webkit-text-size-adjust: 100%;
    }

    body {
      font-size: 1rem !important;
      line-height: 1.6 !important;
      font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
      -webkit-font-smoothing: antialiased !important;
    }

    .content, .sidebar, .modal, .modal-bg,
    .tbl-wrap, .shell, .page, body, html {
      scroll-behavior: smooth !important;
    }

    /* Modal — scrollable body */
    .modal-bg {
      overflow-y: auto !important;
      align-items: flex-start !important;
      padding: 40px 16px !important;
    }

    /* 2 ─ KILL PURPLE / PINK ─────────────────────────────────────────────── */
    .btn-primary, .login-btn, .premium-signin, button.premium-signin {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      border-color: transparent !important;
      color: #fff !important;
    }

    .role-choice {
      background: #F0F5F8 !important;
      border-color: rgba(84,122,149,.20) !important;
      color: #547A95 !important;
    }
    .role-choice:hover {
      background: rgba(84,122,149,.12) !important;
      border-color: #547A95 !important;
      color: #2C3947 !important;
    }
    .role-choice.active {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      color: #fff !important;
      border-color: transparent !important;
    }

    .pl, .pw {
      background: rgba(84,122,149,.12) !important;
      color: #547A95 !important;
    }

    .login-icon-ring {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      color: #fff !important;
      border: none !important;
      box-shadow: 0 12px 28px rgba(44,57,71,.22) !important;
    }

    /* 3 ─ FONT SCALE-UP ─────────────────────────────────────────────────── */
    .nav-info .nm         { font-size: .9375rem !important; }
    .nav-info .eid        { font-size: .8125rem !important; }
    .role-badge           { font-size: .75rem   !important; }
    .nav-section          { font-size: .6875rem !important; }
    .nav-item             { font-size: .9375rem !important; }
    .card-title           { font-size: .75rem   !important; }
    th                    { font-size: .75rem   !important; }
    td                    { font-size: .9375rem !important; }
    .ph-title             { font-size: 1.375rem !important; }
    .ph-sub               { font-size: .9375rem !important; }
    .form-group label     { font-size: .8125rem !important; }
    .form-hint            { font-size: .875rem  !important; }
    .btn                  { font-size: .9375rem !important; }
    .btn-sm               { font-size: .875rem  !important; }
    .fi                   { font-size: .9375rem !important; }
    .modal-title          { font-size: 1.0625rem !important; }
    .modal-section        { font-size: .6875rem !important; }
    .toast                { font-size: .9375rem !important; }
    .cc-kpi-val           { font-size: 1.875rem !important; }
    .cc-kpi-lbl           { font-size: .8125rem !important; }
    .stat .val            { font-size: 2.125rem !important; }
    .stat .lbl            { font-size: .875rem  !important; }
    .pill                 { font-size: .75rem   !important; }
    .clock                { font-size: clamp(3.2rem, 7vw, 4.5rem) !important; }
    .ci-item .t           { font-size: 1.375rem !important; }
    .ci-item .l           { font-size: .875rem  !important; }
    .dateline             { font-size: 1.0625rem !important; }
    .tz-badge             { font-size: .875rem  !important; }
    .status-pill          { font-size: .9375rem !important; }
    .login-card-title     { font-size: 1.875rem !important; }
    .login-card-top p     { font-size: .9375rem !important; }
    .input-shell input    { font-size: 1rem     !important; }
    .wh-google-copy h1    { font-size: clamp(2.25rem, 5vw, 4.5rem) !important; }
    .wh-google-copy p     { font-size: 1.0625rem !important; }
    .role-choice          { font-size: .875rem  !important; }
    .role-choice i        { font-size: 1.1rem   !important; }
    .work-tab             { font-size: .9375rem !important; }

    /* 4 ─ ATTENDANCE SECTION ─────────────────────────────────────────────── */
    .hero {
      padding: 52px 36px 44px !important;
      border-radius: 24px !important;
      background:
        radial-gradient(ellipse 80% 60% at 100% 0%, rgba(194,165,109,.12), transparent 55%),
        radial-gradient(ellipse 60% 50% at 0% 100%, rgba(84,122,149,.10), transparent 50%),
        linear-gradient(150deg, #ffffff 0%, #F6F9FB 60%, #EDF2F5 100%) !important;
      border: 1.5px solid rgba(84,122,149,.14) !important;
      box-shadow: 0 8px 40px rgba(44,57,71,.10) !important;
      position: relative !important;
      overflow: hidden !important;
    }

    html[data-theme="dark"] .hero {
      background:
        radial-gradient(ellipse 80% 60% at 100% 0%, rgba(194,165,109,.10), transparent 55%),
        linear-gradient(150deg, #1E2D3D 0%, #253240 100%) !important;
      border-color: rgba(84,122,149,.20) !important;
    }

    .hero::before {
      content: '' !important;
      position: absolute !important;
      right: -80px !important; top: -80px !important;
      width: 280px !important; height: 280px !important;
      border-radius: 999px !important;
      border: 1px solid rgba(194,165,109,.12) !important;
      pointer-events: none !important;
    }

    .clock {
      font-size: clamp(3.2rem, 7vw, 4.5rem) !important;
      font-weight: 800 !important;
      background: linear-gradient(135deg, #2C3947 0%, #547A95 50%, #C2A56D 100%) !important;
      -webkit-background-clip: text !important;
      -webkit-text-fill-color: transparent !important;
      letter-spacing: -.04em !important;
      line-height: 1 !important;
      margin-bottom: 6px !important;
    }

    .dateline {
      font-size: 1.0625rem !important;
      color: rgba(44,57,71,.65) !important;
      margin: 8px 0 12px !important;
    }

    html[data-theme="dark"] .dateline { color: rgba(232,237,242,.60) !important; }

    .tz-badge {
      display: inline-flex !important;
      align-items: center !important;
      gap: 7px !important;
      padding: 6px 18px !important;
      border-radius: 99px !important;
      font-size: .9rem !important;
      font-weight: 600 !important;
      background: rgba(84,122,149,.10) !important;
      border: 1.5px solid rgba(84,122,149,.20) !important;
      color: #547A95 !important;
    }

    .status-pill {
      font-size: .9375rem !important;
      padding: 8px 22px !important;
      border-radius: 99px !important;
      gap: 8px !important;
    }

    .sp-present { background: rgba(62,122,99,.12) !important; color: #2F6B55 !important; border: 1.5px solid rgba(62,122,99,.22) !important; }
    .sp-absent  { background: rgba(184,91,91,.10) !important; color: #9E4545 !important; border: 1.5px solid rgba(184,91,91,.18) !important; }
    .sp-idle    { background: rgba(84,122,149,.10) !important; color: #547A95 !important; border: 1.5px solid rgba(84,122,149,.20) !important; }

    .ci-row {
      gap: 40px !important;
      margin: 22px 0 30px !important;
      justify-content: center !important;
    }

    .ci-item .t {
      font-size: 1.375rem !important;
      font-weight: 700 !important;
      color: #2C3947 !important;
    }
    html[data-theme="dark"] .ci-item .t { color: #E8EDF2 !important; }

    .ci-item .l {
      font-size: .875rem !important;
      font-weight: 700 !important;
      letter-spacing: .07em !important;
      text-transform: uppercase !important;
      color: rgba(44,57,71,.55) !important;
      margin-top: 4px !important;
    }

    .action-row { gap: 14px !important; justify-content: center !important; }

    .action-row .btn {
      min-width: 168px !important;
      min-height: 52px !important;
      border-radius: 16px !important;
      font-size: 1rem !important;
      font-weight: 700 !important;
      gap: 9px !important;
    }

    #btnIn  { background: linear-gradient(135deg, #3E7A63, #2D5C4A) !important; box-shadow: 0 10px 28px rgba(62,122,99,.28) !important; }
    #btnOut { background: linear-gradient(135deg, #B85B5B, #8B3A3A) !important; box-shadow: 0 10px 28px rgba(184,91,91,.28) !important; }

    .loc-btn {
      display: inline-flex !important;
      align-items: center !important;
      gap: 7px !important;
      margin-top: 20px !important;
      padding: 8px 20px !important;
      border-radius: 99px !important;
      font-size: .9rem !important;
      font-weight: 600 !important;
      background: rgba(84,122,149,.08) !important;
      border: 1.5px solid rgba(84,122,149,.18) !important;
      color: #547A95 !important;
      transition: all .18s !important;
    }

    .loc-btn:hover {
      background: rgba(84,122,149,.16) !important;
      border-color: #547A95 !important;
      color: #2C3947 !important;
    }

    .loc-fetched {
      background: rgba(62,122,99,.12) !important;
      border-color: rgba(62,122,99,.25) !important;
      color: #2F6B55 !important;
    }

    /* 5 ─ FORM INPUTS GLOBAL POLISH ─────────────────────────────────────── */
    .form-group input,
    .form-group select,
    .form-group textarea,
    .fi, .cc-fi {
      font-family: 'Inter', sans-serif !important;
      font-size: 1rem !important;
      color: #2C3947 !important;
      background: #F3F6F8 !important;
      border: 1.5px solid rgba(84,122,149,.18) !important;
      border-radius: 12px !important;
      padding: 11px 15px !important;
      outline: none !important;
      transition: border-color .15s, box-shadow .15s, background .15s !important;
      width: 100% !important;
    }

    html[data-theme="dark"] .form-group input,
    html[data-theme="dark"] .form-group select,
    html[data-theme="dark"] .form-group textarea {
      background: rgba(44,57,71,.55) !important;
      border-color: rgba(84,122,149,.22) !important;
      color: #E8EDF2 !important;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus,
    .fi:focus, .cc-fi:focus {
      border-color: #547A95 !important;
      background: #fff !important;
      box-shadow: 0 0 0 4px rgba(84,122,149,.12) !important;
    }

    /* Custom select arrow */
    .form-group select, .ws-select {
      appearance: none !important;
      -webkit-appearance: none !important;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23547A95' stroke-width='1.8' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") !important;
      background-repeat: no-repeat !important;
      background-position: right 14px center !important;
      padding-right: 38px !important;
    }

    .form-group select option { background: #fff !important; color: #2C3947 !important; }
    html[data-theme="dark"] .form-group select option { background: #1E2D3D !important; color: #E8EDF2 !important; }

    /* Labels */
    .form-group label, .ws-label {
      display: block !important;
      font-size: .8125rem !important;
      font-weight: 700 !important;
      letter-spacing: .05em !important;
      text-transform: uppercase !important;
      color: rgba(44,57,71,.65) !important;
      margin-bottom: 7px !important;
    }
    html[data-theme="dark"] .form-group label { color: rgba(232,237,242,.60) !important; }

    /* Required star */
    .ws-required { color: #B85B5B !important; font-weight: 800 !important; margin-left: 2px !important; }

    /* Hint text */
    .form-hint, .ws-hint {
      font-size: .875rem !important;
      color: rgba(84,122,149,.75) !important;
      margin-top: 5px !important;
      display: flex !important;
      align-items: center !important;
      gap: 5px !important;
    }
    .ws-hint i { font-size: .8125rem !important; color: #547A95 !important; }

    /* 6 ─ ADD EMPLOYEE MODAL ─────────────────────────────────────────────── */
    .ws-emp-modal {
      padding: 0 !important;
      border-radius: 22px !important;
      overflow: hidden !important;
      border: 1.5px solid rgba(84,122,149,.16) !important;
      box-shadow: 0 32px 80px rgba(44,57,71,.22) !important;
      background: #fff !important;
      max-height: 90vh !important;
      display: flex !important;
      flex-direction: column !important;
      width: min(580px, 95vw) !important;
    }

    html[data-theme="dark"] .ws-emp-modal {
      background: #1C2A38 !important;
      border-color: rgba(84,122,149,.22) !important;
    }

    .ws-modal-header {
      display: flex !important;
      align-items: center !important;
      gap: 12px !important;
      padding: 20px 24px !important;
      margin: 0 !important;
      background: linear-gradient(135deg, #2C3947 0%, #3A4F64 100%) !important;
      color: #E8EDF2 !important;
      font-size: 1.0625rem !important;
      font-weight: 700 !important;
      flex-shrink: 0 !important;
    }

    .ws-modal-header-icon {
      width: 36px; height: 36px;
      display: inline-flex; align-items: center; justify-content: center;
      background: rgba(194,165,109,.22);
      color: #C2A56D;
      border-radius: 10px;
      font-size: .95rem;
      flex-shrink: 0;
    }

    .ws-modal-header-text {
      flex: 1 !important;
      font-size: 1.0625rem !important;
      font-weight: 700 !important;
      color: #E8EDF2 !important;
    }

    .ws-modal-header-badge {
      font-size: .6875rem !important;
      font-weight: 600 !important;
      letter-spacing: .08em !important;
      text-transform: uppercase !important;
      color: rgba(232,237,242,.55) !important;
      background: rgba(255,255,255,.08) !important;
      border: 1px solid rgba(255,255,255,.14) !important;
      padding: 3px 10px !important;
      border-radius: 99px !important;
    }

    .ws-modal-close-btn {
      width: 34px !important; height: 34px !important;
      display: inline-flex !important; align-items: center !important; justify-content: center !important;
      background: rgba(255,255,255,.10) !important;
      border: 1px solid rgba(255,255,255,.16) !important;
      color: rgba(232,237,242,.80) !important;
      border-radius: 8px !important;
      cursor: pointer !important;
      font-size: .9rem !important;
      transition: background .15s, color .15s !important;
      flex-shrink: 0 !important;
    }

    .ws-modal-close-btn:hover {
      background: rgba(184,91,91,.30) !important;
      border-color: rgba(184,91,91,.45) !important;
      color: #fff !important;
    }

    .ws-modal-body {
      flex: 1 !important;
      overflow-y: auto !important;
      overflow-x: hidden !important;
      padding: 24px 28px !important;
      scroll-behavior: smooth !important;
    }

    .ws-form-section-label {
      display: flex !important;
      align-items: center !important;
      gap: 8px !important;
      font-size: .75rem !important;
      font-weight: 800 !important;
      letter-spacing: .09em !important;
      text-transform: uppercase !important;
      color: #547A95 !important;
      padding-bottom: 10px !important;
      margin-bottom: 16px !important;
      margin-top: 8px !important;
      border-bottom: 1.5px solid rgba(84,122,149,.14) !important;
    }

    .ws-form-section-label:first-child { margin-top: 0 !important; }
    .ws-form-section-label i { font-size: .875rem !important; opacity: .80 !important; }

    .ws-form-group { margin-bottom: 18px !important; }
    .ws-field-grid { gap: 14px !important; }

    .ws-input {
      display: block !important;
      width: 100% !important;
      padding: 11px 15px !important;
      font-family: 'Inter', sans-serif !important;
      font-size: 1rem !important;
      color: #2C3947 !important;
      background: #F3F6F8 !important;
      border: 1.5px solid rgba(84,122,149,.18) !important;
      border-radius: 12px !important;
      outline: none !important;
      transition: border-color .15s, box-shadow .15s, background .15s !important;
    }

    html[data-theme="dark"] .ws-input {
      background: rgba(44,57,71,.55) !important;
      border-color: rgba(84,122,149,.22) !important;
      color: #E8EDF2 !important;
    }

    .ws-input:focus {
      border-color: #547A95 !important;
      background: #fff !important;
      box-shadow: 0 0 0 4px rgba(84,122,149,.12) !important;
    }

    .ws-select {
      display: block !important;
      width: 100% !important;
      padding: 11px 38px 11px 15px !important;
      font-family: 'Inter', sans-serif !important;
      font-size: 1rem !important;
      color: #2C3947 !important;
      background: #F3F6F8 !important;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23547A95' stroke-width='1.8' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") !important;
      background-repeat: no-repeat !important;
      background-position: right 14px center !important;
      border: 1.5px solid rgba(84,122,149,.18) !important;
      border-radius: 12px !important;
      appearance: none !important;
      -webkit-appearance: none !important;
      outline: none !important;
      cursor: pointer !important;
      transition: border-color .15s, box-shadow .15s !important;
    }

    html[data-theme="dark"] .ws-select {
      background-color: rgba(44,57,71,.55) !important;
      border-color: rgba(84,122,149,.22) !important;
      color: #E8EDF2 !important;
    }

    .ws-select:focus {
      border-color: #547A95 !important;
      background-color: #fff !important;
      box-shadow: 0 0 0 4px rgba(84,122,149,.12) !important;
    }

    .ws-modal-footer {
      display: flex !important;
      align-items: center !important;
      justify-content: flex-end !important;
      gap: 10px !important;
      padding: 16px 28px 22px !important;
      background: rgba(232,237,242,.50) !important;
      border-top: 1px solid rgba(84,122,149,.10) !important;
      flex-shrink: 0 !important;
      margin: 0 !important;
    }

    html[data-theme="dark"] .ws-modal-footer {
      background: rgba(44,57,71,.30) !important;
      border-top-color: rgba(84,122,149,.15) !important;
    }

    .ws-btn-cancel {
      padding: 10px 22px !important;
      border-radius: 10px !important;
      background: transparent !important;
      border: 1.5px solid rgba(84,122,149,.28) !important;
      color: #547A95 !important;
      font-size: .9375rem !important;
      font-weight: 600 !important;
      transition: all .15s !important;
    }

    .ws-btn-cancel:hover {
      background: rgba(84,122,149,.08) !important;
      border-color: #547A95 !important;
      color: #2C3947 !important;
    }

    .ws-btn-submit {
      padding: 10px 24px !important;
      border-radius: 10px !important;
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      border: none !important;
      color: #fff !important;
      font-size: .9375rem !important;
      font-weight: 700 !important;
      box-shadow: 0 6px 18px rgba(44,57,71,.22) !important;
      transition: transform .15s, box-shadow .15s !important;
    }

    .ws-btn-submit:hover {
      transform: translateY(-1px) !important;
      box-shadow: 0 10px 26px rgba(44,57,71,.28) !important;
    }

    /* 7 ─ LOGIN FORM SYNC ────────────────────────────────────────────────── */
    .wh-google-form-panel .input-shell {
      min-height: 54px !important;
      border-radius: 14px !important;
      border: 1.5px solid rgba(84,122,149,.18) !important;
      background: #F3F6F8 !important;
    }

    .wh-google-form-panel .input-shell:focus-within {
      border-color: #547A95 !important;
      background: #fff !important;
      box-shadow: 0 0 0 4px rgba(84,122,149,.12) !important;
    }

    .wh-google-form-panel .input-shell input { font-size: 1rem !important; }
    .wh-google-form-panel .input-shell > i { color: #547A95 !important; }

    .wh-google-form-panel .role-choice {
      min-height: 66px !important;
      font-size: .9rem !important;
    }
    .wh-google-form-panel .role-choice i { font-size: 1.15rem !important; }

    .wh-google-form-panel .premium-signin {
      min-height: 54px !important;
      font-size: 1rem !important;
      border-radius: 14px !important;
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
    }

    /* 8 ─ STAT CARDS ─────────────────────────────────────────────────────── */
    .stat {
      border-radius: 16px !important;
      padding: 22px 20px !important;
    }
    .stat .val { font-size: 2.25rem !important; font-weight: 800 !important; }
    .stat .lbl { font-size: .875rem !important; margin-top: 6px !important; font-weight: 600 !important; }

    /* ╔═══════════════════════════════════════════════════════════════════════╗
       ║  WorkSync — MASTER PALETTE & UI FIX                                  ║
       ║  Fixes ALL old blue/purple/google color leakage across both modes     ║
       ║  Fixes filter bar, cc-fi width, font sizes, dark/light accuracy       ║
       ╚═══════════════════════════════════════════════════════════════════════╝ */

    /* ── 1. LOCK CSS VARIABLES — light mode ──────────────────────────────── */
    :root,
    html,
    html[data-theme="light"] {
      --bg:          #E8EDF2 !important;
      --bg2:         #F3F6F8 !important;
      --card:        #FFFFFF !important;
      --card2:       #F8FAFB !important;
      --border:      rgba(44,57,71,.12) !important;
      --border2:     rgba(84,122,149,.28) !important;
      --accent:      #547A95 !important;
      --accent2:     #C2A56D !important;
      --green:       #3E7A63 !important;
      --red:         #B85B5B !important;
      --yellow:      #C2A56D !important;
      --purple:      #6B6F9B !important;
      --text:        #2C3947 !important;
      --text2:       rgba(44,57,71,.70) !important;
      --text3:       rgba(44,57,71,.45) !important;
      --shadow:      0 18px 48px rgba(44,57,71,.12) !important;
      --shadow-soft: 0 8px 22px rgba(44,57,71,.08) !important;
      --r:           16px !important;
    }

    /* ── 2. LOCK CSS VARIABLES — dark mode ───────────────────────────────── */
    html[data-theme="dark"] {
      --bg:          #1A2430 !important;
      --bg2:         #1F2C3A !important;
      --card:        #243040 !important;
      --card2:       #2A3848 !important;
      --border:      rgba(84,122,149,.18) !important;
      --border2:     rgba(84,122,149,.30) !important;
      --accent:      #86ABC4 !important;
      --accent2:     #C2A56D !important;
      --green:       #74B49B !important;
      --red:         #E08C8C !important;
      --yellow:      #D6BC7D !important;
      --purple:      #A9A6D6 !important;
      --text:        #E8EDF2 !important;
      --text2:       rgba(232,237,242,.72) !important;
      --text3:       rgba(232,237,242,.45) !important;
      --shadow:      0 22px 60px rgba(0,0,0,.32) !important;
      --shadow-soft: 0 10px 28px rgba(0,0,0,.22) !important;
    }

    /* ── 3. GLOBAL BACKGROUNDS ───────────────────────────────────────────── */
    html, body { background: var(--bg) !important; color: var(--text) !important; }

    html[data-theme="dark"] body {
      background: linear-gradient(160deg, #1A2430 0%, #1F2C3A 100%) !important;
    }

    /* ── 4. TOPNAV — both modes ───────────────────────────────────────────── */
    .topnav {
      background: linear-gradient(90deg, #2C3947 0%, #3A4F64 100%) !important;
      border-bottom: 1px solid rgba(84,122,149,.25) !important;
      box-shadow: 0 2px 20px rgba(44,57,71,.22) !important;
      backdrop-filter: none !important;
    }

    html[data-theme="dark"] .topnav {
      background: linear-gradient(90deg, #1A2430 0%, #243040 100%) !important;
      border-bottom-color: rgba(84,122,149,.22) !important;
    }

    /* Brand text on dark topnav */
    .brand {
      background: linear-gradient(135deg, #E8EDF2, #C2A56D 65%) !important;
      -webkit-background-clip: text !important;
      -webkit-text-fill-color: transparent !important;
      font-weight: 800 !important;
    }

    .brand i {
      background: linear-gradient(135deg, #547A95, #C2A56D) !important;
      -webkit-text-fill-color: #fff !important;
      color: #fff !important;
      box-shadow: 0 6px 16px rgba(84,122,149,.30) !important;
    }

    /* User info text on dark topnav */
    .nav-info .nm { color: #E8EDF2 !important; font-size: .9375rem !important; }
    .nav-info .eid { color: rgba(232,237,242,.60) !important; font-size: .8125rem !important; }

    /* Role badge — gold on dark */
    .role-badge {
      background: rgba(194,165,109,.18) !important;
      color: #C2A56D !important;
      border: 1px solid rgba(194,165,109,.30) !important;
      font-size: .75rem !important;
    }

    /* All role badge variants → palette */
    .rb-employee, .rb-teamlead, .rb-manager, .rb-hr, .rb-admin,
    [class*="rb-"] {
      background: rgba(194,165,109,.18) !important;
      color: #C2A56D !important;
      border-color: rgba(194,165,109,.28) !important;
    }

    /* Avatar */
    .avatar, .emp-avatar-sm, .assignee-avatar, .emp-detail-avatar {
      background: linear-gradient(135deg, #547A95, #C2A56D) !important;
      color: #fff !important;
      box-shadow: 0 0 0 2px rgba(255,255,255,.20) !important;
    }

    /* Topnav buttons */
    .theme-toggle, .nav-actions .btn-outline {
      background: rgba(255,255,255,.09) !important;
      border: 1px solid rgba(255,255,255,.18) !important;
      color: #E8EDF2 !important;
      border-radius: 10px !important;
    }

    .theme-toggle:hover, .nav-actions .btn-outline:hover {
      background: rgba(194,165,109,.18) !important;
      border-color: rgba(194,165,109,.45) !important;
      color: #C2A56D !important;
    }

    /* ── 5. SIDEBAR ───────────────────────────────────────────────────────── */
    .sidebar {
      background: linear-gradient(180deg, #F0F4F7 0%, #E8EDF2 100%) !important;
      border-right: 1.5px solid rgba(84,122,149,.14) !important;
    }

    html[data-theme="dark"] .sidebar {
      background: linear-gradient(180deg, #1C2A38 0%, #172130 100%) !important;
      border-right-color: rgba(84,122,149,.20) !important;
    }

    .nav-section { color: #547A95 !important; font-weight: 800 !important; font-size: .6875rem !important; letter-spacing: .12em !important; }
    html[data-theme="dark"] .nav-section { color: rgba(134,171,196,.80) !important; }

    .nav-item { color: rgba(44,57,71,.68) !important; font-size: .9375rem !important; }
    html[data-theme="dark"] .nav-item { color: rgba(232,237,242,.60) !important; }

    .nav-item i { background: rgba(84,122,149,.10) !important; color: #547A95 !important; }
    html[data-theme="dark"] .nav-item i { color: rgba(134,171,196,.80) !important; }

    .nav-item:hover { background: rgba(84,122,149,.12) !important; color: #2C3947 !important; }
    html[data-theme="dark"] .nav-item:hover { color: #E8EDF2 !important; background: rgba(84,122,149,.18) !important; }

    .nav-item.active {
      background: linear-gradient(90deg, rgba(84,122,149,.20), rgba(84,122,149,.08)) !important;
      color: #547A95 !important;
      border-left: 3px solid #547A95 !important;
      font-weight: 700 !important;
    }

    html[data-theme="dark"] .nav-item.active {
      background: rgba(84,122,149,.22) !important;
      color: #86ABC4 !important;
      border-left-color: #86ABC4 !important;
    }

    .nav-item.active i { background: rgba(84,122,149,.22) !important; color: #547A95 !important; }
    html[data-theme="dark"] .nav-item.active i { color: #86ABC4 !important; }

    /* ── 6. CONTENT AREA ─────────────────────────────────────────────────── */
    .content {
      background: var(--bg) !important;
      overflow-y: auto !important;
      overflow-x: hidden !important;
      padding: 24px 28px !important;
    }

    /* ── 7. CARDS ────────────────────────────────────────────────────────── */
    .card {
      background: #fff !important;
      border: 1px solid rgba(84,122,149,.12) !important;
      box-shadow: 0 2px 14px rgba(44,57,71,.07) !important;
      border-radius: var(--r) !important;
    }

    html[data-theme="dark"] .card {
      background: #243040 !important;
      border-color: rgba(84,122,149,.18) !important;
      box-shadow: 0 4px 18px rgba(0,0,0,.22) !important;
    }

    .card-title { color: #547A95 !important; font-size: .75rem !important; font-weight: 800 !important; letter-spacing: .10em !important; }
    html[data-theme="dark"] .card-title { color: rgba(134,171,196,.85) !important; }

    /* ── 8. TABLES ───────────────────────────────────────────────────────── */
    thead tr { background: rgba(232,237,242,.85) !important; }
    html[data-theme="dark"] thead tr { background: rgba(44,57,71,.60) !important; }

    th { color: #547A95 !important; font-size: .75rem !important; font-weight: 800 !important; }
    html[data-theme="dark"] th { color: rgba(134,171,196,.85) !important; }

    td { color: var(--text) !important; font-size: .9375rem !important; }

    tbody tr:hover { background: rgba(84,122,149,.06) !important; }
    html[data-theme="dark"] tbody tr:hover { background: rgba(84,122,149,.12) !important; }

    /* ── 9. FILTER BAR & cc-fi DROPDOWNS ─────────────────────────────────── */
    .cc-filter-bar {
      background: #fff !important;
      border: 1px solid rgba(84,122,149,.14) !important;
      border-radius: var(--r) !important;
      box-shadow: 0 2px 12px rgba(44,57,71,.06) !important;
      padding: 18px 20px 16px !important;
    }

    html[data-theme="dark"] .cc-filter-bar {
      background: #243040 !important;
      border-color: rgba(84,122,149,.20) !important;
    }

    .cc-filter-row {
      display: flex !important;
      flex-direction: row !important;
      flex-wrap: wrap !important;
      gap: 8px !important;
      align-items: center !important;
    }

    /* Fix: cc-fi should NOT be width:100% — that was causing vertical stacking */
    .cc-fi {
      width: auto !important;
      min-width: 140px !important;
      height: 42px !important;
      padding: 0 12px !important;
      border: 1.5px solid rgba(84,122,149,.18) !important;
      border-radius: 10px !important;
      background: #F3F6F8 !important;
      color: #2C3947 !important;
      font-family: 'Inter', sans-serif !important;
      font-size: .9rem !important;
      font-weight: 450 !important;
      outline: none !important;
      cursor: pointer !important;
      appearance: none !important;
      -webkit-appearance: none !important;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='7' viewBox='0 0 11 7'%3E%3Cpath d='M1 1l4.5 4.5L10 1' stroke='%23547A95' stroke-width='1.6' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") !important;
      background-repeat: no-repeat !important;
      background-position: right 10px center !important;
      padding-right: 32px !important;
      transition: border-color .15s, box-shadow .15s !important;
    }

    .cc-fi:focus {
      border-color: #547A95 !important;
      background-color: #fff !important;
      box-shadow: 0 0 0 4px rgba(84,122,149,.12) !important;
    }

    html[data-theme="dark"] .cc-fi {
      background-color: rgba(44,57,71,.55) !important;
      border-color: rgba(84,122,149,.22) !important;
      color: #E8EDF2 !important;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='7' viewBox='0 0 11 7'%3E%3Cpath d='M1 1l4.5 4.5L10 1' stroke='%2386ABC4' stroke-width='1.6' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") !important;
    }

    .cc-fi-search {
      min-width: 220px !important;
      flex: 1 1 auto !important;
      background-image: none !important;
      padding: 0 14px !important;
    }

    /* cc-period chip */
    .cc-period-chip {
      background: rgba(84,122,149,.12) !important;
      color: #547A95 !important;
      border: 1px solid rgba(84,122,149,.20) !important;
    }

    html[data-theme="dark"] .cc-period-chip {
      background: rgba(84,122,149,.20) !important;
      color: #86ABC4 !important;
    }

    /* ── 10. BUTTONS — wipe all old google blue ──────────────────────────── */
    .btn-primary, .login-btn, .premium-signin {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      border: none !important;
      color: #fff !important;
      box-shadow: 0 6px 18px rgba(44,57,71,.22) !important;
    }

    .btn-primary:hover, .login-btn:hover, .premium-signin:hover {
      background: linear-gradient(135deg, #3d6680, #1e2a36) !important;
      transform: translateY(-1px) !important;
    }

    .btn-green { background: linear-gradient(135deg, #3E7A63, #2D5C4A) !important; border: none !important; }
    .btn-red   { background: linear-gradient(135deg, #B85B5B, #8B3A3A) !important; border: none !important; }

    .btn-outline {
      border: 1.5px solid rgba(84,122,149,.28) !important;
      color: #547A95 !important;
      background: transparent !important;
    }

    html[data-theme="dark"] .btn-outline {
      border-color: rgba(134,171,196,.28) !important;
      color: #86ABC4 !important;
    }

    .btn-outline:hover {
      background: rgba(84,122,149,.09) !important;
      border-color: #547A95 !important;
      color: #2C3947 !important;
    }

    html[data-theme="dark"] .btn-outline:hover {
      color: #E8EDF2 !important;
    }

    /* ── 11. PILLS & STATUS CHIPS ─────────────────────────────────────────── */
    .sp-idle, .pl, .tz-badge {
      background: rgba(84,122,149,.12) !important;
      color: #547A95 !important;
      border: 1.5px solid rgba(84,122,149,.22) !important;
    }

    html[data-theme="dark"] .sp-idle,
    html[data-theme="dark"] .pl,
    html[data-theme="dark"] .tz-badge {
      background: rgba(84,122,149,.18) !important;
      color: #86ABC4 !important;
      border-color: rgba(84,122,149,.28) !important;
    }

    .sp-present, .pp { background: rgba(62,122,99,.12) !important; color: #2F6B55 !important; border: 1.5px solid rgba(62,122,99,.22) !important; }
    .sp-absent, .pa  { background: rgba(184,91,91,.11) !important; color: #9E4545 !important; border: 1.5px solid rgba(184,91,91,.20) !important; }
    .ph, .pe, .pho   { background: rgba(194,165,109,.14) !important; color: #8A6D2F !important; border: 1.5px solid rgba(194,165,109,.24) !important; }
    .pw              { background: rgba(107,111,155,.12) !important; color: #6B6F9B !important; border: 1.5px solid rgba(107,111,155,.22) !important; }

    html[data-theme="dark"] .sp-present, html[data-theme="dark"] .pp { background: rgba(116,180,155,.15) !important; color: #74B49B !important; border-color: rgba(116,180,155,.25) !important; }
    html[data-theme="dark"] .sp-absent,  html[data-theme="dark"] .pa { background: rgba(224,140,140,.13) !important; color: #E08C8C !important; border-color: rgba(224,140,140,.22) !important; }

    /* ── 12. WORK CHIPS ───────────────────────────────────────────────────── */
    .work-chip.project    { color: #547A95 !important; background: rgba(84,122,149,.12) !important; border-color: rgba(84,122,149,.22) !important; }
    .work-chip.in_progress{ color: #547A95 !important; background: rgba(84,122,149,.12) !important; border-color: rgba(84,122,149,.22) !important; }
    .work-chip.schedule   { color: #3E7A63 !important; background: rgba(62,122,99,.12) !important;  border-color: rgba(62,122,99,.22)  !important; }
    .work-chip.completed  { color: #3E7A63 !important; background: rgba(62,122,99,.12) !important;  border-color: rgba(62,122,99,.22)  !important; }
    .work-chip.blocked    { color: #9E4545 !important; background: rgba(184,91,91,.11) !important;  border-color: rgba(184,91,91,.20)  !important; }
    .work-chip.planned    { color: #8A6D2F !important; background: rgba(194,165,109,.14) !important; border-color: rgba(194,165,109,.24) !important; }
    .work-chip.review     { color: #6B6F9B !important; background: rgba(107,111,155,.12) !important; border-color: rgba(107,111,155,.22) !important; }

    /* wtype */
    .wtype-prj { background: rgba(84,122,149,.12) !important; color: #547A95 !important; }
    .wtype-sch { background: rgba(62,122,99,.12)  !important; color: #3E7A63 !important; }

    /* ── 13. MODAL ───────────────────────────────────────────────────────── */
    .modal-bg {
      background: rgba(44,57,71,.48) !important;
      backdrop-filter: blur(6px) !important;
      overflow-y: auto !important;
      padding: 40px 16px !important;
      align-items: flex-start !important;
    }

    .modal {
      background: #fff !important;
      border: 1.5px solid rgba(84,122,149,.16) !important;
      border-radius: 20px !important;
      box-shadow: 0 32px 80px rgba(44,57,71,.22) !important;
    }

    html[data-theme="dark"] .modal {
      background: #1C2A38 !important;
      border-color: rgba(84,122,149,.22) !important;
    }

    .modal-title { color: var(--text) !important; font-size: 1.0625rem !important; border-bottom-color: var(--border) !important; }
    .modal-section { color: #547A95 !important; font-size: .6875rem !important; letter-spacing: .09em !important; }
    html[data-theme="dark"] .modal-section { color: #86ABC4 !important; }

    /* ── 14. FORM INPUTS — GLOBAL ─────────────────────────────────────────── */
    .fi, .form-group input, .form-group select,
    .form-group textarea {
      background: #F3F6F8 !important;
      border: 1.5px solid rgba(84,122,149,.18) !important;
      border-radius: 12px !important;
      color: #2C3947 !important;
      font-size: 1rem !important;
      padding: 10px 14px !important;
      width: 100% !important;
    }

    html[data-theme="dark"] .fi,
    html[data-theme="dark"] .form-group input,
    html[data-theme="dark"] .form-group select,
    html[data-theme="dark"] .form-group textarea {
      background: rgba(44,57,71,.55) !important;
      border-color: rgba(84,122,149,.24) !important;
      color: #E8EDF2 !important;
    }

    .fi:focus, .form-group input:focus,
    .form-group select:focus, .form-group textarea:focus {
      border-color: #547A95 !important;
      background: #fff !important;
      box-shadow: 0 0 0 4px rgba(84,122,149,.12) !important;
      outline: none !important;
    }

    html[data-theme="dark"] .fi:focus,
    html[data-theme="dark"] .form-group input:focus,
    html[data-theme="dark"] .form-group select:focus {
      border-color: #86ABC4 !important;
      background: rgba(44,57,71,.80) !important;
    }

    .form-group label {
      color: rgba(44,57,71,.65) !important;
      font-size: .8125rem !important;
      font-weight: 700 !important;
      letter-spacing: .05em !important;
      text-transform: uppercase !important;
      margin-bottom: 7px !important;
    }

    html[data-theme="dark"] .form-group label { color: rgba(232,237,242,.60) !important; }

    /* Select custom arrow */
    .form-group select, .ws-select, .cc-fi[type!="text"][type!="search"] {
      appearance: none !important;
      -webkit-appearance: none !important;
    }

    /* ── 15. HERO & CLOCK ────────────────────────────────────────────────── */
    .hero {
      background: linear-gradient(150deg, #ffffff 0%, #F6F9FB 60%, #EDF2F5 100%) !important;
      border: 1.5px solid rgba(84,122,149,.12) !important;
      box-shadow: 0 6px 28px rgba(44,57,71,.08) !important;
    }

    html[data-theme="dark"] .hero {
      background: linear-gradient(150deg, #243040 0%, #1E2D3D 100%) !important;
      border-color: rgba(84,122,149,.18) !important;
    }

    .clock {
      background: linear-gradient(135deg, #2C3947, #547A95 55%, #C2A56D) !important;
      -webkit-background-clip: text !important;
      -webkit-text-fill-color: transparent !important;
      font-size: clamp(3rem, 7vw, 4.5rem) !important;
      font-weight: 800 !important;
      color: transparent !important;
    }

    html[data-theme="dark"] .clock {
      background: linear-gradient(135deg, #86ABC4, #C2A56D) !important;
      -webkit-background-clip: text !important;
      -webkit-text-fill-color: transparent !important;
    }

    /* ── 16. WORK HERO CARD ───────────────────────────────────────────────── */
    .work-hero-card {
      background: linear-gradient(150deg, #ffffff 0%, #F3F6F8 60%, #EDF1F5 100%) !important;
      border: 1.5px solid rgba(84,122,149,.14) !important;
      border-radius: 20px !important;
      box-shadow: 0 4px 18px rgba(44,57,71,.08) !important;
    }

    html[data-theme="dark"] .work-hero-card {
      background: linear-gradient(150deg, #243040 0%, #1E2D3D 100%) !important;
      border-color: rgba(84,122,149,.20) !important;
    }

    /* ── 17. WORK TABS ───────────────────────────────────────────────────── */
    #workTabs, .work-tabs {
      background: rgba(44,57,71,.08) !important;
      border: 1.5px solid rgba(84,122,149,.16) !important;
      border-radius: 14px !important;
      padding: 5px !important;
      display: flex !important;
      flex-direction: row !important;
      flex-wrap: wrap !important;
      gap: 4px !important;
    }

    html[data-theme="dark"] #workTabs,
    html[data-theme="dark"] .work-tabs {
      background: rgba(255,255,255,.05) !important;
      border-color: rgba(84,122,149,.20) !important;
    }

    #workTabs .work-tab, .work-tabs .work-tab {
      background: transparent !important;
      color: rgba(44,57,71,.60) !important;
      border: none !important;
      flex: 1 1 auto !important;
      width: auto !important;
      white-space: nowrap !important;
      font-size: .9rem !important;
      font-weight: 650 !important;
      min-height: 36px !important;
      border-radius: 10px !important;
      box-shadow: none !important;
      transform: none !important;
    }

    html[data-theme="dark"] #workTabs .work-tab,
    html[data-theme="dark"] .work-tabs .work-tab {
      color: rgba(232,237,242,.52) !important;
    }

    #workTabs .work-tab.active, .work-tabs .work-tab.active {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      color: #fff !important;
      box-shadow: 0 4px 14px rgba(44,57,71,.24) !important;
    }

    html[data-theme="dark"] #workTabs .work-tab.active,
    html[data-theme="dark"] .work-tabs .work-tab.active {
      background: linear-gradient(135deg, #547A95, #1A2430) !important;
    }

    #workTabs .work-tab.active i, .work-tabs .work-tab.active i { color: #C2A56D !important; }

    #workTabs .work-tab:not(.active):hover, .work-tabs .work-tab:not(.active):hover {
      background: rgba(84,122,149,.14) !important;
      color: #2C3947 !important;
    }

    html[data-theme="dark"] #workTabs .work-tab:not(.active):hover,
    html[data-theme="dark"] .work-tabs .work-tab:not(.active):hover {
      background: rgba(84,122,149,.22) !important;
      color: #E8EDF2 !important;
    }

    /* ── 18. KPI CARDS ─────────────────────────────────────────────────────── */
    .cc-kpi-card {
      background: #fff !important;
      border: 1px solid rgba(84,122,149,.12) !important;
      border-radius: var(--r) !important;
    }

    html[data-theme="dark"] .cc-kpi-card {
      background: #243040 !important;
      border-color: rgba(84,122,149,.18) !important;
    }

    .cc-kpi-card::before { background: linear-gradient(90deg, #547A95, #C2A56D) !important; }
    .cc-kpi-val  { color: #547A95 !important; font-size: 1.875rem !important; }
    .cc-kpi-lbl  { color: rgba(44,57,71,.65) !important; font-size: .8125rem !important; }
    .cc-kpi-icon { color: #547A95 !important; }

    html[data-theme="dark"] .cc-kpi-val { color: #86ABC4 !important; }
    html[data-theme="dark"] .cc-kpi-lbl { color: rgba(232,237,242,.60) !important; }
    html[data-theme="dark"] .cc-kpi-icon { color: #86ABC4 !important; }

    /* Variant overrides — keep palette-matching tones */
    .cc-kpi-green .cc-kpi-val, .cc-kpi-green .cc-kpi-icon { color: #3E7A63 !important; }
    .cc-kpi-green::before { background: #3E7A63 !important; }
    .cc-kpi-red .cc-kpi-val,   .cc-kpi-red .cc-kpi-icon   { color: #B85B5B !important; }
    .cc-kpi-red::before   { background: #B85B5B !important; }
    .cc-kpi-yellow .cc-kpi-val, .cc-kpi-yellow .cc-kpi-icon { color: #C2A56D !important; }
    .cc-kpi-yellow::before { background: #C2A56D !important; }
    .cc-kpi-teal .cc-kpi-val, .cc-kpi-teal .cc-kpi-icon { color: #3E7A7A !important; }
    .cc-kpi-teal::before { background: #3E7A7A !important; }

    /* ── 19. PROGRESS BARS ─────────────────────────────────────────────────── */
    .cc-progress-bar, .progress-line span, .emp-progress-line span {
      background: linear-gradient(90deg, #547A95, #C2A56D) !important;
    }

    .progress-line, .emp-progress-line {
      background: rgba(84,122,149,.14) !important;
    }

    /* ── 20. STAT CARDS ────────────────────────────────────────────────────── */
    .stat, .work-mini, .cc-insight-stat {
      background: #F8FAFB !important;
      border: 1px solid rgba(84,122,149,.12) !important;
      border-radius: 16px !important;
    }

    html[data-theme="dark"] .stat,
    html[data-theme="dark"] .work-mini,
    html[data-theme="dark"] .cc-insight-stat {
      background: #1E2D3D !important;
      border-color: rgba(84,122,149,.18) !important;
    }

    .stat .val { color: #2C3947 !important; font-size: 2.25rem !important; }
    html[data-theme="dark"] .stat .val { color: #E8EDF2 !important; }

    .stat::before { background: linear-gradient(90deg, #547A95, #C2A56D) !important; height: 3px !important; }

    .work-mini .v { color: #547A95 !important; font-size: 1.5rem !important; font-weight: 800 !important; }
    html[data-theme="dark"] .work-mini .v { color: #86ABC4 !important; }
    .work-mini .l { color: rgba(44,57,71,.60) !important; font-size: .8125rem !important; }
    html[data-theme="dark"] .work-mini .l { color: rgba(232,237,242,.55) !important; }

    /* ── 21. LOGIN PAGE ────────────────────────────────────────────────────── */
    /* Override the old google/purple login backgrounds */
    .login-screen.wh-google-login,
    body.wh-guest .login-screen.wh-google-login,
    .wh-google-login {
      background:
        radial-gradient(ellipse 80% 60% at 0% 0%, rgba(84,122,149,.20), transparent 55%),
        radial-gradient(ellipse 70% 55% at 100% 100%, rgba(194,165,109,.16), transparent 55%),
        linear-gradient(160deg, #E8EDF2 0%, #F3F6F8 40%, #EDF1F4 100%) !important;
    }

    html[data-theme="dark"] .login-screen.wh-google-login,
    html[data-theme="dark"] body.wh-guest .login-screen.wh-google-login,
    html[data-theme="dark"] .wh-google-login {
      background:
        radial-gradient(ellipse 80% 60% at 0% 0%, rgba(84,122,149,.18), transparent 55%),
        radial-gradient(ellipse 70% 55% at 100% 100%, rgba(194,165,109,.14), transparent 55%),
        linear-gradient(160deg, #1A2430 0%, #1F2C3A 100%) !important;
    }

    /* Login card */
    .wh-google-card {
      border: 1.5px solid rgba(84,122,149,.18) !important;
      background: rgba(255,255,255,.90) !important;
      box-shadow: 0 32px 80px rgba(44,57,71,.18), 0 1px 0 rgba(255,255,255,.85) inset !important;
      backdrop-filter: blur(24px) !important;
    }

    html[data-theme="dark"] .wh-google-card {
      background: rgba(28,42,56,.92) !important;
      border-color: rgba(84,122,149,.24) !important;
      box-shadow: 0 36px 90px rgba(0,0,0,.45) !important;
    }

    /* Brand panel on login */
    .wh-google-brand-panel {
      background:
        radial-gradient(ellipse 100% 80% at 10% 20%, rgba(84,122,149,.28), transparent 55%),
        radial-gradient(ellipse 80% 70% at 88% 80%,  rgba(194,165,109,.22), transparent 50%),
        linear-gradient(145deg, #2C3947, #3A4F64) !important;
      border-right-color: rgba(255,255,255,.08) !important;
    }

    .wh-google-brand-panel .wh-google-logo span:first-child { color: #E8EDF2 !important; -webkit-text-fill-color: #E8EDF2 !important; }
    .wh-google-brand-panel .wh-google-logo span:last-child  { color: #C2A56D !important; -webkit-text-fill-color: #C2A56D !important; }
    .wh-google-copy h1 { color: #E8EDF2 !important; text-shadow: 0 14px 36px rgba(0,0,0,.22) !important; }
    html[data-theme="dark"] .wh-google-copy h1 { color: #E8EDF2 !important; }
    .wh-google-copy p   { color: rgba(232,237,242,.75) !important; }
    .wh-google-status-card, .wh-google-product-chip {
      background: rgba(255,255,255,.10) !important;
      border-color: rgba(255,255,255,.15) !important;
      color: rgba(232,237,242,.85) !important;
    }
    .wh-google-status-card i, .wh-google-product-chip i { color: #C2A56D !important; }

    /* Form panel */
    .wh-google-form-panel {
      background: rgba(255,255,255,.97) !important;
    }

    html[data-theme="dark"] .wh-google-form-panel {
      background: rgba(26,38,52,.97) !important;
    }

    .wh-google-form-panel .login-icon-ring {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      color: #fff !important;
      border: none !important;
    }

    .wh-google-form-panel .login-card-title { color: #2C3947 !important; }
    html[data-theme="dark"] .wh-google-form-panel .login-card-title { color: #E8EDF2 !important; }

    .wh-google-form-panel .role-choice {
      background: #F0F5F8 !important;
      border: 1.5px solid rgba(84,122,149,.16) !important;
      color: #547A95 !important;
      font-size: .875rem !important;
      min-height: 64px !important;
    }

    .wh-google-form-panel .role-choice:hover {
      background: rgba(84,122,149,.12) !important;
      border-color: #547A95 !important;
      color: #2C3947 !important;
    }

    .wh-google-form-panel .role-choice.active {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      color: #fff !important;
      border-color: transparent !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .role-choice {
      background: rgba(44,57,71,.55) !important;
      border-color: rgba(84,122,149,.22) !important;
      color: #86ABC4 !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .role-choice.active {
      background: linear-gradient(135deg, #547A95, #1A2430) !important;
      color: #fff !important;
    }

    .wh-google-form-panel .input-shell {
      background: #F3F6F8 !important;
      border: 1.5px solid rgba(84,122,149,.18) !important;
      border-radius: 14px !important;
      min-height: 54px !important;
    }

    .wh-google-form-panel .input-shell:focus-within {
      border-color: #547A95 !important;
      background: #fff !important;
      box-shadow: 0 0 0 4px rgba(84,122,149,.12) !important;
    }

    .wh-google-form-panel .input-shell > i { color: #547A95 !important; }

    .wh-google-form-panel .premium-signin {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      border: none !important;
      min-height: 52px !important;
      border-radius: 14px !important;
      font-size: 1rem !important;
    }

    .wh-google-form-panel .login-live-chip {
      background: rgba(84,122,149,.10) !important;
      border-color: rgba(84,122,149,.22) !important;
      color: #547A95 !important;
    }

    html[data-theme="dark"] .wh-google-form-panel .login-live-chip {
      background: rgba(44,57,71,.60) !important;
      color: #86ABC4 !important;
    }

    /* ── 22. PAGE HEADER ─────────────────────────────────────────────────── */
    .ph-title { color: var(--text) !important; font-size: 1.375rem !important; font-weight: 750 !important; letter-spacing: -.03em !important; }
    .ph-sub   { color: var(--text2) !important; font-size: .9375rem !important; margin-top: 4px !important; }
    html[data-theme="dark"] .ph-title { color: #E8EDF2 !important; }
    html[data-theme="dark"] .ph-sub   { color: rgba(232,237,242,.60) !important; }

    /* ── 23. ASSIGNEE PICKER ─────────────────────────────────────────────── */
    .assignee-picker {
      background: #fff !important;
      border: 1px solid rgba(84,122,149,.18) !important;
      border-radius: 14px !important;
    }

    html[data-theme="dark"] .assignee-picker { background: #243040 !important; }

    .assignee-item:hover       { background: rgba(84,122,149,.08) !important; }
    .assignee-item.is-checked  { background: rgba(84,122,149,.14) !important; }

    /* ── 24. TOAST ──────────────────────────────────────────────────────── */
    .t-success { background: #3E7A63 !important; }
    .t-error   { background: #B85B5B !important; }
    .t-info    { background: #547A95 !important; }

    /* ── 25. SCROLLBAR ──────────────────────────────────────────────────── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(84,122,149,.32); border-radius: 99px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(84,122,149,.55); }

    /* ══════════════════════════════════════════════════════════════════════
       WorkSync FINAL FIX — Role buttons, Input colors, Login form polish
       Ultra-high specificity block — beats all earlier rules including purple
    ══════════════════════════════════════════════════════════════════════ */

    /* ── Role-choice buttons: fix text overflow ─────────────────────────── */
    html body .login-screen .wh-google-card .wh-google-form-panel .role-choice-grid {
      grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
      gap: 6px !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .role-choice {
      min-height: 56px !important;
      max-height: 70px !important;
      padding: 8px 4px !important;
      border-radius: 12px !important;
      background: #EEF3F7 !important;
      border: 1.5px solid rgba(84,122,149,.18) !important;
      color: #3A5570 !important;
      font-size: .72rem !important;        /* smaller so text fits in narrow column */
      font-weight: 700 !important;
      letter-spacing: -.01em !important;
      line-height: 1.2 !important;
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 5px !important;
      white-space: normal !important;
      word-break: break-word !important;
      text-align: center !important;
      overflow: hidden !important;
      cursor: pointer !important;
      transition: all .15s ease !important;
      box-shadow: 0 1px 4px rgba(44,57,71,.06) !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .role-choice i {
      font-size: 1rem !important;
      color: #547A95 !important;
      flex-shrink: 0 !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .role-choice:hover {
      background: rgba(84,122,149,.14) !important;
      border-color: #547A95 !important;
      color: #2C3947 !important;
      transform: translateY(-1px) !important;
      box-shadow: 0 6px 16px rgba(84,122,149,.16) !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .role-choice.active {
      background: linear-gradient(145deg, #547A95, #2C3947) !important;
      border-color: transparent !important;
      color: #ffffff !important;
      box-shadow: 0 8px 20px rgba(44,57,71,.28) !important;
      transform: translateY(-1px) !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .role-choice.active i {
      color: #C2A56D !important;
    }

    /* Dark mode role choice */
    html[data-theme="dark"] body .login-screen .wh-google-card .wh-google-form-panel .role-choice {
      background: rgba(44,57,71,.55) !important;
      border-color: rgba(84,122,149,.24) !important;
      color: #86ABC4 !important;
    }

    html[data-theme="dark"] body .login-screen .wh-google-card .wh-google-form-panel .role-choice.active {
      background: linear-gradient(145deg, #547A95, #1A2430) !important;
      color: #fff !important;
      border-color: transparent !important;
    }

    /* ── Input shell: correct background & color ─────────────────────────── */
    html body .login-screen .wh-google-card .wh-google-form-panel .input-shell {
      background: #F0F5F8 !important;
      border: none !important;
      border-radius: 13px !important;
      min-height: 52px !important;
      box-shadow: none !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .input-shell:focus-within {
      border: none !important;
      background: #ffffff !important;
      box-shadow: 0 0 0 0 transparent, 0 2px 8px rgba(44,57,71,.08) !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .input-shell > i {
      color: #547A95 !important;
      opacity: .85 !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .input-shell input {
      color: #2C3947 !important;
      background: transparent !important;
      font-size: .95rem !important;
      font-weight: 500 !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .input-shell input::placeholder {
      color: rgba(44,57,71,.42) !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .password-toggle {
      color: rgba(44,57,71,.50) !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .password-toggle:hover {
      color: #547A95 !important;
      background: rgba(84,122,149,.10) !important;
    }

    /* Dark mode input */
    html[data-theme="dark"] body .login-screen .wh-google-card .wh-google-form-panel .input-shell {
      background: rgba(44,57,71,.50) !important;
      border: none !important;
      box-shadow: none !important;
    }

    html[data-theme="dark"] body .login-screen .wh-google-card .wh-google-form-panel .input-shell:focus-within {
      background: rgba(44,57,71,.80) !important;
      border: none !important;
      box-shadow: none !important;
    }

    html[data-theme="dark"] body .login-screen .wh-google-card .wh-google-form-panel .input-shell input {
      color: #E8EDF2 !important;
    }

    html[data-theme="dark"] body .login-screen .wh-google-card .wh-google-form-panel .input-shell input::placeholder {
      color: rgba(232,237,242,.40) !important;
    }

    html[data-theme="dark"] body .login-screen .wh-google-card .wh-google-form-panel .input-shell > i,
    html[data-theme="dark"] body .login-screen .wh-google-card .wh-google-form-panel .password-toggle {
      color: rgba(134,171,196,.75) !important;
    }

    /* ── Sign-in button: palette gradient ────────────────────────────────── */
    html body .login-screen .wh-google-card .wh-google-form-panel .premium-signin,
    html body .login-screen .wh-google-card .wh-google-form-panel .login-btn {
      background: linear-gradient(135deg, #547A95 0%, #3A5570 50%, #2C3947 100%) !important;
      border: none !important;
      color: #ffffff !important;
      border-radius: 13px !important;
      min-height: 52px !important;
      font-size: 1rem !important;
      font-weight: 700 !important;
      box-shadow: 0 8px 28px rgba(44,57,71,.28), 0 1px 0 rgba(255,255,255,.10) inset !important;
      position: relative !important;
      overflow: hidden !important;
      letter-spacing: .01em !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .premium-signin::after {
      content: '' !important;
      position: absolute !important;
      bottom: 0 !important;
      left: 10% !important;
      right: 10% !important;
      height: 2px !important;
      background: linear-gradient(90deg, transparent, #C2A56D, transparent) !important;
      opacity: .70 !important;
      border-radius: 99px !important;
    }

    /* ── Labels in form panel ────────────────────────────────────────────── */
    html body .login-screen .wh-google-card .wh-google-form-panel .login-role-group label,
    html body .login-screen .wh-google-card .wh-google-form-panel .smart-field label {
      color: rgba(44,57,71,.60) !important;
      font-size: .72rem !important;
      font-weight: 750 !important;
      letter-spacing: .07em !important;
      text-transform: uppercase !important;
    }

    /* ── Live clock chip ─────────────────────────────────────────────────── */
    html body .login-screen .wh-google-card .wh-google-form-panel .login-live-chip {
      background: rgba(84,122,149,.10) !important;
      border-color: rgba(84,122,149,.22) !important;
      color: #547A95 !important;
      font-size: .72rem !important;
    }

    html[data-theme="dark"] body .login-screen .wh-google-card .wh-google-form-panel .login-live-chip {
      background: rgba(44,57,71,.60) !important;
      border-color: rgba(84,122,149,.28) !important;
      color: #86ABC4 !important;
    }

    /* ── Welcome back title & subtitle ──────────────────────────────────── */
    html body .login-screen .wh-google-card .wh-google-form-panel .login-card-title {
      color: #2C3947 !important;
      font-size: 1.625rem !important;
      font-weight: 750 !important;
      letter-spacing: -.04em !important;
    }

    html[data-theme="dark"] body .login-screen .wh-google-card .wh-google-form-panel .login-card-title {
      color: #E8EDF2 !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .login-card-top p {
      color: rgba(44,57,71,.62) !important;
      font-size: .9rem !important;
    }

    html[data-theme="dark"] body .login-screen .wh-google-card .wh-google-form-panel .login-card-top p {
      color: rgba(232,237,242,.58) !important;
    }

    /* ── Login icon ring ─────────────────────────────────────────────────── */
    html body .login-screen .wh-google-card .wh-google-form-panel .login-icon-ring {
      background: linear-gradient(135deg, #547A95, #2C3947) !important;
      color: #fff !important;
      border: none !important;
      box-shadow: 0 10px 26px rgba(44,57,71,.22) !important;
    }

    /* ── Error message ───────────────────────────────────────────────────── */
    html body .login-screen .wh-google-card .wh-google-form-panel .login-error {
      background: rgba(184,91,91,.10) !important;
      border-color: rgba(184,91,91,.22) !important;
      color: #9E4545 !important;
      border-radius: 10px !important;
    }

    /* ── Footnote ────────────────────────────────────────────────────────── */
    html body .login-screen .wh-google-card .wh-google-form-panel .login-footnote,
    html body .login-screen .wh-google-card .wh-google-form-panel .login-footnote span {
      color: rgba(44,57,71,.55) !important;
      font-size: .75rem !important;
    }

    html body .login-screen .wh-google-card .wh-google-form-panel .login-footnote i {
      color: #547A95 !important;
    }

  </style>

<body class="<?= $isLoggedIn ? 'wh-logged-in' : 'wh-guest' ?>">

  <div class="attend-device-blocker" aria-label="Desktop required">
    <div class="attend-device-card">
      <div class="attend-device-icon"><i class="fa-solid fa-desktop"></i></div>
      <div class="attend-device-title">Responsive Workspace</div>
      <div class="attend-device-text">WorkSync is optimized for desktop and now keeps tablet/mobile layouts stable without page-level horizontal scroll.</div>
      <div class="attend-device-note">Loading responsive shell</div>
    </div>
  </div>

  <!-- ════ WORKHIVE LOGIN ════ -->
  <div class="login-screen wh-login wh-google-login" id="loginScreen" style="<?= $isLoggedIn ? 'display:none' : '' ?>">
    <section class="login-shell wh-google-shell" aria-label="WorkSync secure login">
      <div class="login-box premium-login-card wh-google-card">
        <div class="wh-google-brand-panel" aria-hidden="true">
          <div class="wh-google-brand-lockup">
           
            <div class="wh-google-logo" aria-hidden="true">
              <span>Work</span><span>Sync</span>
            </div>
            <div class="wh-google-product-chip"><i class="fa-solid fa-fingerprint"></i> Secure work command center</div>
          </div>

          <div class="wh-google-copy">
            <h1>Sign in to continue</h1>
            <p>Track attendance, daily work progress, projects, schedules and team performance from one clean workspace.</p>
          </div>

          <div class="wh-google-status-grid">
            <div class="wh-google-status-card">
              <i class="fa-solid fa-calendar-check"></i>
              <span>Attendance</span>
            </div>
            <div class="wh-google-status-card">
              <i class="fa-solid fa-list-check"></i>
              <span>Daily work</span>
            </div>
            <div class="wh-google-status-card">
              <i class="fa-solid fa-chart-line"></i>
              <span>Insights</span>
            </div>
          </div>
        </div>

        <div class="wh-google-form-panel">
          <div class="login-live-chip"><i class="fa-regular fa-clock"></i><span id="loginIstClock">IST --:--:--</span></div>

          <div class="login-card-top">
            <div class="login-icon-ring"><i class="fa-solid fa-fingerprint"></i></div>
            <div class="login-card-title">Welcome back</div>
            <p>Use your role, employee ID and password to access WorkSync.</p>
          </div>

          <div class="form-group login-role-group">
            <label>Choose role</label>
            <select id="lRole" class="login-role-select" aria-label="Login role">
              <option value="">— Select role —</option>
              <option value="employee">Employee</option>
              <option value="teamlead">Team Lead</option>
              <option value="manager">Manager</option>
              <option value="hr">HR</option>
              <option value="admin">Admin</option>
            </select>
            <div class="role-choice-grid" aria-hidden="false">
              <button type="button" class="role-choice" data-role="employee" onclick="setLoginRole('employee', this)"><i class="fa-solid fa-user"></i><span>Employee</span></button>
              <button type="button" class="role-choice" data-role="teamlead" onclick="setLoginRole('teamlead', this)"><i class="fa-solid fa-users-line"></i><span>Lead</span></button>
              <button type="button" class="role-choice" data-role="manager" onclick="setLoginRole('manager', this)"><i class="fa-solid fa-building-user"></i><span>Manager</span></button>
              <button type="button" class="role-choice" data-role="hr" onclick="setLoginRole('hr', this)"><i class="fa-solid fa-user-tie"></i><span>HR</span></button>
              <button type="button" class="role-choice" data-role="admin" onclick="setLoginRole('admin', this)"><i class="fa-solid fa-crown"></i><span>Admin</span></button>
            </div>
          </div>

          <div class="form-group smart-field">
            <label>Employee ID</label>
            <div class="input-shell">
              <i class="fa-regular fa-id-badge"></i>
              <input id="lEmpId" placeholder="EMP001" autocomplete="username">
            </div>
          </div>

          <div class="form-group smart-field">
            <label>Password</label>
            <div class="input-shell">
              <i class="fa-solid fa-lock"></i>
              <input id="lPass" type="password" placeholder="Enter password" autocomplete="current-password">
              <button type="button" class="password-toggle" onclick="toggleLoginPassword()" aria-label="Show or hide password">
                <i class="fa-regular fa-eye" id="loginPassIcon"></i>
              </button>
            </div>
          </div>

          <button id="loginSubmitBtn" class="login-btn premium-signin" onclick="doLogin()">
            <span class="btn-ready"><i class="fa-solid fa-arrow-right-to-bracket"></i> Sign in</span>
            <span class="btn-loading"><i class="fa-solid fa-spinner fa-spin"></i> Verifying</span>
          </button>

          <div class="login-error" id="lErr"></div>

          <div class="login-footnote">
            <span><i class="fa-solid fa-shield-halved"></i> Protected session</span>
            <span><i class="fa-solid fa-location-dot"></i> Asia/Kolkata</span>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- ════ APP SHELL ════ -->
  <div class="app" id="appShell" style="<?= !$isLoggedIn ? 'display:none' : '' ?>">

    <nav class="topnav">
      <!-- Hamburger — mobile only -->
      <button class="mob-menu-btn" id="mobMenuBtn" type="button" onclick="toggleMobNav()" aria-label="Open navigation menu" aria-expanded="false">
        <i class="fa fa-bars" id="mobMenuIcon"></i>
      </button>
      <div class="brand"><i class="fa-solid fa-fingerprint"></i> WorkSync</div>
      <div class="nav-spacer"></div>
      <div class="nav-user">
        <div class="avatar" id="navAvatar"><?= attend_h($isLoggedIn ? attend_initials($userName) : 'AT') ?></div>
        <div class="nav-info">
          <div class="nm" id="navName"><?= attend_h($userName) ?></div>
          <div class="eid" id="navEid"><?= attend_h($empId) ?></div>
        </div>
        <span class="role-badge rb-<?= attend_h($userRoleClass) ?>" id="navBadge"><?= attend_h($userRoleLabel) ?></span>
      </div>
      <div class="nav-actions">
        <button class="theme-toggle" id="themeToggle" type="button" onclick="toggleTheme()" aria-label="Switch theme">
          <i class="fa fa-moon" id="themeIcon"></i><span id="themeText">Dark</span>
        </button>
        <button class="btn btn-outline btn-sm" onclick="doLogout()"><i class="fa fa-sign-out-alt"></i> Logout</button>
      </div>
    </nav>

    <!-- Mobile nav overlay backdrop -->
    <div class="mob-nav-overlay" id="mobNavOverlay" onclick="closeMobNav()"></div>

    <div class="shell">
      <aside class="sidebar" id="mainSidebar">
        <!-- Mobile user card shown inside drawer -->
        <div class="mob-drawer-user" id="mobDrawerUser">
          <div class="avatar" style="width:38px;height:38px;font-size:.85rem" id="drawerAvatar"><?= attend_h($isLoggedIn ? attend_initials($userName) : 'AT') ?></div>
          <div style="min-width:0">
            <div style="font-size:.88rem;font-weight:700;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= attend_h($userName) ?></div>
            <div style="font-size:.72rem;color:var(--text2);font-family:'JetBrains Mono',monospace"><?= attend_h($empId) ?></div>
          </div>
          <span class="role-badge rb-<?= attend_h($userRoleClass) ?>" style="margin-left:auto;flex-shrink:0"><?= attend_h($userRoleLabel) ?></span>
        </div>
        <div class="nav-section">Main</div>
        <div class="nav-item active" onclick="showPage('attendance')" data-p="attendance">
          <i class="fa fa-calendar-check"></i> Attendance
        </div>
        <div class="nav-item" onclick="showPage('history')" data-p="history">
          <i class="fa fa-clock-rotate-left"></i> My History
        </div>
        <div class="nav-item" onclick="showPage('work')" data-p="work">
          <i class="fa fa-briefcase"></i> Work Tracking
        </div>
        <?php if ($canEdit): ?>
          <div class="nav-section">Management</div>
          <div class="nav-item" onclick="showPage('manage')" data-p="manage">
            <i class="fa fa-users-gear"></i> Manage
          </div>
        <?php endif; ?>
        <?php if ($canAddEmp): ?>
          <div class="nav-item" onclick="openAddEmployee()" data-p="addEmp">
            <i class="fa fa-user-plus"></i> Add Employee
          </div>
        <?php endif; ?>
        <?php if ($canAnalytics): ?>
          <div class="nav-item" onclick="showPage('analytics')" data-p="analytics">
            <i class="fa fa-chart-line"></i> Analytics
          </div>
        <?php endif; ?>
      </aside>

      <main class="content">

        <!-- ── ATTENDANCE ── -->
        <div class="page active" id="page-attendance">
          <div class="hero">
            <div class="clock" id="clock">00:00:00</div>
            <div class="dateline" id="dateline"></div>
            <div class="tz-badge"><i class="fa fa-globe"></i> India Standard Time (IST)</div>
            <div style="margin-top:18px" id="statusPill"></div>
            <div class="ci-row" id="ciRow" style="display:none">
              <div class="ci-item">
                <div class="t" id="ciIn">—</div>
                <div class="l">Check In</div>
              </div>
              <div class="ci-item">
                <div class="t" id="ciOut">—</div>
                <div class="l">Check Out</div>
              </div>
              <div class="ci-item">
                <div class="t" id="ciHrs">—</div>
                <div class="l">Hours</div>
              </div>
            </div>
            <div class="action-row" id="actionRow">
              <button class="btn btn-green" id="btnIn" onclick="doCheckIn()">
                <i class="fa fa-play"></i> Check In
              </button>
              <button class="btn btn-red" id="btnOut" style="display:none" onclick="doCheckOut()">
                <i class="fa fa-stop"></i> Check Out
              </button>
            </div>
            <div>
              <div class="loc-btn" id="locBtn" onclick="fetchLocation()">
                <i class="fa fa-location-dot"></i>
                <span id="locText">Click to detect location</span>
              </div>
            </div>
          </div>

          <?php if ($canAnalytics): ?>
            <div class="card" id="todayOverview">
              <div class="card-title"><i class="fa fa-building"></i> Today's Team Overview</div>
              <div class="g4" id="todayStats">
                <div class="stat" style="--c:var(--text3)">
                  <div class="val">—</div>
                  <div class="lbl">Loading…</div>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- ── MY HISTORY ── -->
        <div class="page" id="page-history">
          <div class="ph-row">
            <div>
              <div class="ph-title">My Attendance History</div>
              <div class="ph-sub">Your personal records (all times in IST)</div>
            </div>
          </div>
          <div class="filters">
            <input type="date" class="fi" id="hFrom" value="<?= attend_h($monthStart) ?>">
            <input type="date" class="fi" id="hTo" value="<?= attend_h($todayDate) ?>">
            <button class="btn btn-outline btn-sm" onclick="loadHistory()"><i class="fa fa-search"></i> Fetch</button>
          </div>
          <div class="g4" id="histStats" style="margin-bottom:18px"></div>
          <div class="tbl-wrap">
            <table>
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Check In (IST)</th>
                  <th>Check Out (IST)</th>
                  <th>Hours</th>
                  <th>Location</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody id="histBody">
                <tr>
                  <td colspan="7" style="text-align:center;color:var(--text2);padding:28px">Select range and click Fetch</td>
                </tr>
              </tbody>
            </table>
          </div>
</div>

        <!-- ── WORK TRACKING ── -->
        <div class="page" id="page-work">
          <div class="work-hero">
            <div class="work-hero-card">
              <div class="ph-title">WorkSync Work Command Center</div>
              <div class="ph-sub">Track every project, schedule, task, daily work description, completed step, blocker, next plan, weekly progress, and employee performance in one professional workspace.</div>
              <div class="work-tabs-wrap">
                <!-- Mobile tab dropdown -->
                <button class="work-tab-mob-btn" id="workTabMobBtn" type="button" onclick="toggleWorkTabDropdown()" aria-haspopup="listbox" aria-expanded="false">
                  <span id="workTabMobLabel"><i class="fa fa-chart-simple"></i> Work Analysis</span>
                  <i class="fa fa-chevron-down work-tab-mob-chevron" id="workTabMobChevron"></i>
                </button>
                <div class="work-tabs" id="workTabs">
                  <button class="work-tab" type="button" data-panel="overview" onclick="showWorkPanel('overview')"><i class="fa fa-chart-pie"></i> Overview</button>
                  <button class="work-tab" type="button" data-panel="create" onclick="showWorkPanel('create')"><i class="fa fa-folder-plus"></i> New Project</button>
                  <button class="work-tab" type="button" data-panel="task" onclick="showWorkPanel('task')"><i class="fa fa-list-check"></i> Tasks</button>
                  <button class="work-tab" type="button" data-panel="update" onclick="showWorkPanel('update')"><i class="fa fa-pen-to-square"></i> Daily Update</button>
                  <button class="work-tab" type="button" data-panel="reports" onclick="showWorkPanel('reports')"><i class="fa fa-calendar-week"></i> Reports</button>
                  <button class="work-tab active" type="button" data-panel="command" onclick="showWorkPanel('command')"><i class="fa fa-chart-simple"></i> Work Analysis</button>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-title"><i class="fa fa-gauge-high"></i> Live Work KPIs</div>
              <div class="work-mini-grid" id="workKpis">
                <div class="work-mini"><div class="v">—</div><div class="l">Loading</div></div>
              </div>
            </div>
          </div>

          <div class="work-panel" id="work-panel-overview">
            <div class="work-board">
              <div class="card">
                <div class="ph-row" style="margin-bottom:14px">
                  <div>
                    <div class="card-title"><i class="fa fa-diagram-project"></i> My Visible Projects & Schedules</div>
                    <div class="muted-small">Employees see their own created and assigned work. Team lead / manager / HR / admin can see team-wide work.</div>
                  </div>
                  <button class="btn btn-outline btn-sm" onclick="loadWorkBootstrap()"><i class="fa fa-rotate"></i> Reload</button>
                </div>
                <div class="filters" style="margin-bottom:12px">
                  <select class="fi" id="wkFilterType" onchange="loadWorkProjects()">
                    <option value="">All Types</option>
                    <option value="project">Projects</option>
                    <option value="schedule">Schedules</option>
                  </select>
                  <select class="fi" id="wkFilterStatus" onchange="loadWorkProjects()">
                    <option value="">All Status</option>
                    <option value="planned">Planned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="blocked">Blocked</option>
                    <option value="completed">Completed</option>
                  </select>
                  <input class="fi" id="wkSearch" placeholder="Search project, schedule, task" oninput="debouncedWorkSearch()">
                </div>
                <div class="tbl-wrap">
                  <table>
                    <thead>
                      <tr>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Progress</th>
                        <th>Tasks</th>
                        <th>Due</th>
                        <th>Owner</th>
                      </tr>
                    </thead>
                    <tbody id="workProjectBody">
                      <tr><td colspan="8" style="text-align:center;color:var(--text2);padding:28px">Loading projects and schedules…</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="card">
                <div class="card-title"><i class="fa fa-bars-progress"></i> Active Task Board</div>
                <div class="tbl-wrap">
                  <table>
                    <thead>
                      <tr>
                        <th>Task</th>
                        <th>Container</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Due</th>
                        <th>Hours</th>
                      </tr>
                    </thead>
                    <tbody id="workTaskBody">
                      <tr><td colspan="7" style="text-align:center;color:var(--text2);padding:28px">Loading tasks…</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="card">
                <div class="card-title"><i class="fa fa-clock-rotate-left"></i> Recent Daily Updates</div>
                <div class="tbl-wrap">
                  <table>
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Employee</th>
                        <th>Project / Schedule</th>
                        <th>Task</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Hours</th>
                        <th>Review</th>
                      </tr>
                    </thead>
                    <tbody id="workUpdateBody">
                      <tr><td colspan="8" style="text-align:center;color:var(--text2);padding:28px">Loading updates…</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="work-panel" id="work-panel-create">
            <div class="card">
              <div class="card-title"><i class="fa fa-folder-plus"></i> Create Project or Schedule</div>
              <div class="work-form-grid">
                <div class="form-group">
                  <label>Type</label>
                  <select id="wkType" onchange="syncWorkTypeFields()">
                    <option value="project">🗂 Project — development / build work</option>
                    <option value="schedule">📅 Schedule — HR / Marketing / Operations</option>
                  </select>
                </div>
                <div class="form-group span-2">
                  <label>Name <span style="color:var(--red);margin-left:2px">*</span></label>
                  <input id="wkName" maxlength="220" placeholder="e.g. ERP Dashboard Project / Monthly HR Onboarding Schedule">
                </div>
                <div class="form-group">
                  <label>Code <span style="font-weight:400;color:var(--text2);font-size:.75rem">(auto if empty)</span></label>
                  <input id="wkCode" maxlength="80" placeholder="PRJ-001">
                </div>
                <div class="form-group">
                  <label>Category</label>
                  <select id="wkCategory">
                    <option value="development">Development</option>
                    <option value="marketing">Marketing</option>
                    <option value="hr">HR</option>
                    <option value="operations">Operations</option>
                    <option value="sales">Sales</option>
                    <option value="finance">Finance</option>
                    <option value="support">Support</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Priority</label>
                  <select id="wkPriority">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="critical">🔴 Critical</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Status</label>
                  <select id="wkContainerStatus">
                    <option value="planned">Planned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="blocked">Blocked</option>
                    <option value="completed">Completed</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Visibility</label>
                  <select id="wkVisibility">
                    <option value="assigned">Assigned members only</option>
                    <option value="department">Whole department</option>
                    <option value="all">All employees</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Start Date</label>
                  <input type="date" id="wkStart" value="<?= attend_h($todayDate) ?>">
                </div>
                <div class="form-group">
                  <label>Due / End Date</label>
                  <input type="date" id="wkDue">
                </div>
                <div class="form-group schedule-only" style="display:none">
                  <label>Repeat Frequency</label>
                  <select id="wkScheduleFrequency">
                    <option value="one_time">One Time</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="custom">Custom</option>
                  </select>
                </div>
                <div class="form-group schedule-only" style="display:none">
                  <label>Repeat Days</label>
                  <input id="wkRepeatDays" placeholder="Mon, Tue, Wed or custom note">
                </div>
                <div class="form-group full">
                  <label>Assign Members</label>
                  <div class="assignee-picker" id="wkAssigneePicker">
                    <div class="assignee-picker-top">
                      <input class="assignee-search" id="wkAssigneeSearch" type="text" placeholder="Search by name or ID…" oninput="filterAssigneeList()">
                      <div class="assignee-picker-actions">
                        <button type="button" class="assignee-action-btn" onclick="selectAllAssignees()"><i class="fa fa-check-double"></i> All</button>
                        <button type="button" class="assignee-action-btn" onclick="clearAllAssignees()"><i class="fa fa-xmark"></i> Clear</button>
                        <span class="assignee-selected-count" id="wkAssigneeCount">0 selected</span>
                      </div>
                    </div>
                    <div class="assignee-list" id="wkAssigneeList">
                      <div class="assignee-empty">Loading members…</div>
                    </div>
                  </div>
                  <div class="muted-small" style="margin-top:6px">Employees see their own work. Manager / HR / Admin can assign to any team member.</div>
                </div>
                <div class="form-group full">
                  <label>Description / Objective</label>
                  <textarea id="wkDesc" rows="3" placeholder="What is the goal? What output is expected? Any dependencies?"></textarea>
                </div>
              </div>
              <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:4px">
                <button class="btn btn-primary" onclick="saveWorkProject()"><i class="fa fa-floppy-disk"></i> Save Project / Schedule</button>
                <button class="btn btn-outline btn-sm" onclick="showWorkPanel('overview')"><i class="fa fa-arrow-left"></i> Back to Overview</button>
              </div>
            </div>
          </div>

          <div class="work-panel" id="work-panel-task">
            <div class="card">
              <div class="card-title"><i class="fa fa-list-check"></i> Add Task to Project / Schedule</div>
              <div class="work-form-grid">
                <div class="form-group">
                  <label>Project / Schedule <span style="color:var(--red);margin-left:2px">*</span></label>
                  <select id="taskContainer" onchange="syncTaskTypeFromContainer()"></select>
                </div>
                <div class="form-group">
                  <label>Assigned To</label>
                  <select id="taskAssign"></select>
                </div>
                <div class="form-group">
                  <label>Due Date</label>
                  <input type="date" id="taskDue">
                </div>
                <div class="form-group span-2">
                  <label>Task Title <span style="color:var(--red);margin-left:2px">*</span></label>
                  <input id="taskTitle" maxlength="260" placeholder="e.g. Build employee filter API / Design monthly payroll card">
                </div>
                <div class="form-group">
                  <label>Priority</label>
                  <select id="taskPriority">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="critical">🔴 Critical</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Status</label>
                  <select id="taskStatus">
                    <option value="todo">To Do</option>
                    <option value="in_progress">In Progress</option>
                    <option value="review">Review</option>
                    <option value="blocked">Blocked</option>
                    <option value="completed">Completed</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Progress %</label>
                  <input type="number" id="taskProgress" min="0" max="100" value="0">
                </div>
                <div class="form-group">
                  <label>Planned Hours</label>
                  <input type="number" id="taskHours" min="0" max="999" step="0.25" value="1">
                </div>
                <div class="form-group full">
                  <label>Checklist / Steps <span style="font-weight:400;color:var(--text2);font-size:.75rem">(one step per line)</span></label>
                  <textarea id="taskChecklist" rows="4" placeholder="1. Research requirements&#10;2. Build API endpoint&#10;3. Write tests&#10;4. Code review"></textarea>
                </div>
                <div class="form-group full">
                  <label>Description / Acceptance Criteria</label>
                  <textarea id="taskDesc" rows="3" placeholder="What does done look like? Any dependencies, edge cases, or references?"></textarea>
                </div>
              </div>
              <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:4px">
                <button class="btn btn-primary" onclick="saveWorkTask()"><i class="fa fa-plus"></i> Save Task</button>
                <button class="btn btn-outline btn-sm" onclick="showWorkPanel('update')"><i class="fa fa-pen-to-square"></i> Go to Daily Update</button>
              </div>
            </div>
          </div>

          <div class="work-panel" id="work-panel-update">
            <div class="card">
              <div class="card-title"><i class="fa fa-pen-to-square"></i> Submit Daily Work Update</div>
              <div class="work-form-grid">
                <div class="form-group">
                  <label><i class="fa fa-diagram-project" style="color:var(--accent);margin-right:5px"></i> Project / Schedule</label>
                  <select id="updContainer" onchange="filterUpdateTasks()"></select>
                </div>
                <div class="form-group">
                  <label><i class="fa fa-list-check" style="color:var(--accent);margin-right:5px"></i> Task <span style="font-weight:400;color:var(--text2);font-size:.75rem">(optional)</span></label>
                  <select id="updTask"></select>
                </div>
                <div class="form-group">
                  <label><i class="fa fa-calendar-day" style="color:var(--accent);margin-right:5px"></i> Work Date</label>
                  <input type="date" id="updDate" value="<?= attend_h($todayDate) ?>">
                </div>
                <div class="form-group">
                  <label><i class="fa fa-signal" style="color:var(--accent);margin-right:5px"></i> Status</label>
                  <select id="updStatus">
                    <option value="working">🔵 Working</option>
                    <option value="testing">🟣 Testing</option>
                    <option value="review">🟡 In Review</option>
                    <option value="blocked">🔴 Blocked</option>
                    <option value="paused">⚪ Paused</option>
                    <option value="completed">🟢 Completed</option>
                  </select>
                </div>
                <div class="form-group">
                  <label><i class="fa fa-percent" style="color:var(--accent);margin-right:5px"></i> Progress %</label>
                  <input type="number" id="updProgress" min="0" max="100" value="0" placeholder="0–100">
                </div>
                <div class="form-group">
                  <label><i class="fa fa-clock" style="color:var(--accent);margin-right:5px"></i> Hours Logged</label>
                  <input type="number" id="updHours" min="0" max="24" step="0.25" value="1">
                </div>
                <div class="form-group full">
                  <label><i class="fa fa-file-lines" style="color:var(--accent);margin-right:5px"></i> Work Summary <span style="color:var(--red);margin-left:2px">*</span></label>
                  <input id="updSummary" maxlength="300" placeholder="One-line summary of what you worked on today — e.g. Completed payroll API and fixed date filter">
                </div>
                <div class="form-group full">
                  <label><i class="fa fa-trophy" style="color:var(--yellow);margin-right:5px"></i> Output / Achievement <span style="font-weight:400;color:var(--text2);font-size:.75rem">(what was actually produced)</span></label>
                  <textarea id="updAchievements" rows="3" placeholder="e.g. PayrollController endpoint live, UI tested on Chrome and Safari, 3 edge-case bugs fixed"></textarea>
                </div>
                <div class="form-group full">
                  <label><i class="fa fa-stairs" style="color:var(--accent);margin-right:5px"></i> Step-by-Step Work Done <span style="font-weight:400;color:var(--text2);font-size:.75rem">(one step per line — becomes a checkable timeline)</span></label>
                  <textarea id="updStepsDone" rows="6" placeholder="1. Reviewed existing payroll model
2. Added monthly summary query
3. Built REST endpoint with filters
4. Connected frontend table
5. Tested with real data — passed"></textarea>
                </div>
                <div class="form-group full split-2">
                  <div>
                    <label><i class="fa fa-circle-exclamation" style="color:var(--red);margin-right:5px"></i> Blockers / Issues</label>
                    <textarea id="updBlockers" rows="3" placeholder="e.g. Waiting for DB access / API timeout unresolved / design sign-off pending"></textarea>
                  </div>
                  <div>
                    <label><i class="fa fa-arrow-right" style="color:var(--green);margin-right:5px"></i> Plan for Next Day</label>
                    <textarea id="updNextPlan" rows="3" placeholder="e.g. Complete testing, review with manager, push to staging"></textarea>
                  </div>
                </div>
              </div>
              <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:4px">
                <button class="btn btn-primary" onclick="submitWorkUpdate()"><i class="fa fa-paper-plane"></i> Save Daily Update</button>
                <span style="font-size:.78rem;color:var(--text2)">Update is linked to your attendance record and visible to managers in Work Analysis.</span>
              </div>
            </div>
          </div>

          <div class="work-panel" id="work-panel-reports">
            <div class="g2" style="margin-bottom:18px">
              <div class="card">
                <div class="card-title"><i class="fa fa-scale-balanced"></i> Comparison</div>
                <div class="work-mini-grid" id="compareKpis">
                  <div class="work-mini"><div class="v">—</div><div class="l">Loading</div></div>
                </div>
              </div>
              <div class="card">
                <div class="card-title"><i class="fa fa-filter"></i> Report Filters</div>
                <div class="filters">
                  <input type="date" class="fi" id="wkWeekStart">
                  <button class="btn btn-outline btn-sm" onclick="loadWorkWeek()"><i class="fa fa-calendar-week"></i> Week Filter</button>
                </div>
                <div class="filters" style="margin-top:10px">
                  <input type="month" class="fi" id="wkMonth" value="<?= attend_h(substr($todayDate, 0, 7)) ?>">
                  <button class="btn btn-outline btn-sm" onclick="loadWorkMonth()"><i class="fa fa-calendar-days"></i> Month Filter</button>
                </div>
              </div>
            </div>

            <div class="card" style="margin-bottom:18px">
              <div class="card-title"><i class="fa fa-list-check"></i> One Week Step-by-Step Tracking</div>
              <div class="tbl-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Attendance</th>
                      <th>Attendance Hrs</th>
                      <th>Updates</th>
                      <th>Task Updates</th>
                      <th>Work Hrs</th>
                      <th>Steps</th>
                      <th>Progress</th>
                      <th>Projects / Tasks</th>
                    </tr>
                  </thead>
                  <tbody id="workWeekBody">
                    <tr><td colspan="9" style="text-align:center;color:var(--text2);padding:28px">Open week filter to load data</td></tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="card">
              <div class="card-title"><i class="fa fa-calendar-check"></i> Monthly Work + Attendance Summary</div>
              <div class="tbl-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Present Days</th>
                      <th>Attendance Hrs</th>
                      <th>Updates</th>
                      <th>Task Updates</th>
                      <th>Work Hrs</th>
                      <th>Steps</th>
                      <th>Avg Progress</th>
                    </tr>
                  </thead>
                  <tbody id="workMonthBody">
                    <tr><td colspan="8" style="text-align:center;color:var(--text2);padding:28px">Select month and click Month Filter</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- ══════════════════════════════════════════════════════════
               WORK PROGRESS COMMAND CENTER PANEL
               ══════════════════════════════════════════════════════════ -->
          <div class="work-panel active" id="work-panel-command">

            <div class="cc-command-hero">
              <div>
                <div class="cc-eyebrow"><i class="fa fa-wand-magic-sparkles"></i> Admin work intelligence</div>
                <h2>Day-to-Day Project, Schedule & Employee Performance Analysis</h2>
                <p>Track work progress by day, week, month, year, or custom range with employee performance, project risk, blockers, output, attendance hours, and step-by-step activity.</p>
              </div>
              <button class="btn btn-outline btn-sm" type="button" onclick="showWorkPanel('update')"><i class="fa fa-pen-to-square"></i> Submit Daily Update</button>
            </div>

            <!-- Filter Bar -->
            <div class="cc-filter-bar">
              <div class="cc-filter-row">
                <select class="cc-fi" id="ccPeriod" onchange="onCcPeriodChange()">
                  <option value="today">Today</option>
                  <option value="yesterday">Yesterday</option>
                  <option value="specific_day">Specific Day</option>
                  <option value="this_week">This Week</option>
                  <option value="last_week">Last Week</option>
                  <option value="specific_week">Specific Week</option>
                  <option value="this_month">This Month</option>
                  <option value="last_month">Last Month</option>
                  <option value="specific_month">Specific Month</option>
                  <option value="this_year">This Year</option>
                  <option value="last_year">Last Year</option>
                  <option value="specific_year">Specific Year</option>
                  <option value="custom">Custom Range</option>
                </select>
                <input type="date" class="cc-fi" id="ccDay" style="display:none" title="Select specific day">
                <input type="date" class="cc-fi" id="ccWeekOf" style="display:none" title="Select any date in the week">
                <input type="month" class="cc-fi" id="ccMonth" style="display:none" title="Select month">
                <input type="number" class="cc-fi cc-fi-year" id="ccYear" style="display:none" min="2000" max="2100" title="Select year">
                <input type="date" class="cc-fi" id="ccFrom" style="display:none" title="From date">
                <input type="date" class="cc-fi" id="ccTo" style="display:none" title="To date">
                <select class="cc-fi" id="ccEmpFilter"><option value="">All Employees</option></select>
                <select class="cc-fi" id="ccProjectFilter"><option value="">All Projects / Schedules</option></select>
                <select class="cc-fi" id="ccWorkType">
                  <option value="">Projects & Schedules</option>
                  <option value="project">Projects Only</option>
                  <option value="schedule">Schedules Only</option>
                </select>
                <select class="cc-fi" id="ccStatus">
                  <option value="">All Statuses</option>
                  <option value="working">Working</option>
                  <option value="testing">Testing</option>
                  <option value="review">Review</option>
                  <option value="blocked">Blocked</option>
                  <option value="completed">Completed</option>
                  <option value="paused">Paused</option>
                </select>
                <input class="cc-fi cc-fi-search" id="ccSearch" placeholder="&#xf002; Search employee, project, task…" oninput="debouncedCcSearch()">
                <button class="btn btn-primary btn-sm" onclick="loadCommandCenter()"><i class="fa fa-rotate"></i> Apply</button>
              </div>
              <div class="cc-period-label" id="ccPeriodLabel"></div>
            </div>

            <!-- KPI Cards -->
            <div class="cc-kpi-grid" id="ccKpiGrid">
              <div class="cc-kpi-skeleton"><i class="fa fa-spinner fa-spin"></i> Loading KPIs…</div>
            </div>

            <!-- Admin Insights (manager / HR / admin only) -->
            <div id="ccInsights" style="display:none"></div>

            <!-- Two-column: Employee Performance + Project Risk -->
            <div class="cc-two-col">
              <div class="card cc-table-card">
                <div class="card-title cc-title-line"><span><i class="fa fa-user-tie"></i> Employee Performance</span><small>Click any team member to open full profile</small></div>
                <div class="tbl-wrap">
                  <table class="cc-table">
                    <thead>
                      <tr>
                        <th>Employee</th>
                        <th title="Number of daily updates submitted">Updates</th>
                        <th title="Total work hours logged">Work Hrs</th>
                        <th title="Hours from attendance punch-in/out">Att. Hrs</th>
                        <th title="Steps completed / total steps">Steps</th>
                        <th title="Average completion percent">Avg %</th>
                        <th title="Composite performance score 0–100">Score</th>
                        <th title="Risk based on blockers and progress">Risk</th>
                      </tr>
                    </thead>
                    <tbody id="ccEmpBody">
                      <tr><td colspan="8" class="cc-empty"><i class="fa fa-spinner fa-spin"></i></td></tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="card cc-table-card">
                <div class="card-title"><i class="fa fa-triangle-exclamation"></i> Project / Schedule Risk</div>
                <div class="tbl-wrap">
                  <table class="cc-table">
                    <thead>
                      <tr>
                        <th>Project / Schedule</th>
                        <th title="Current overall progress">Progress</th>
                        <th title="Updates in selected period">Updates</th>
                        <th title="Blocked updates / tasks">Blocked</th>
                        <th title="Overdue status and due date">Due / Overdue</th>
                        <th title="Risk level">Risk</th>
                      </tr>
                    </thead>
                    <tbody id="ccProjectBody">
                      <tr><td colspan="6" class="cc-empty"><i class="fa fa-spinner fa-spin"></i></td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Day-by-Day Step Timeline -->
            <div class="card cc-timeline-card">
              <div class="cc-timeline-header">
                <div class="card-title"><i class="fa fa-timeline"></i> Day-by-Day Work &amp; Step Timeline</div>
                <span class="cc-tl-subtitle">Each day shows every employee update with full context and step-by-step progress</span>
              </div>
              <div id="ccTimeline">
                <div class="cc-empty" style="padding:32px"><i class="fa fa-spinner fa-spin"></i> Loading timeline…</div>
              </div>
            </div>

          </div><!-- /work-panel-command -->

        </div><!-- /page-work -->

        <?php if ($canEdit): ?>
          <!-- ── MANAGE ── -->
          <div class="page" id="page-manage">
            <div class="ph-row">
              <div>
                <div class="ph-title">Manage Attendance</div>
                <div class="ph-sub">Edit employee records (times in IST)</div>
              </div>
            </div>
            <div class="filters">
              <input type="date" class="fi" id="mgmtDate" value="<?= attend_h($todayDate) ?>">
              <select class="fi" id="mgmtDept">
                <option value="0">All Departments</option>
              </select>
              <button class="btn btn-outline btn-sm" onclick="loadManage()"><i class="fa fa-refresh"></i> Load</button>
            </div>
            <div class="tbl-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Emp ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>In (IST)</th>
                    <th>Out (IST)</th>
                    <th>Hours</th>
                    <th>Source</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="manageBody">
                  <tr>
                    <td colspan="9" style="text-align:center;color:var(--text2);padding:28px">Select a date and click Load</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($canAnalytics): ?>
          <!-- ── ANALYTICS ── -->
          <div class="page" id="page-analytics">
            <div class="ph-row">
              <div>
                <div class="ph-title">Analytics Dashboard</div>
                <div class="ph-sub">Attendance insights</div>
              </div>
              <button class="btn btn-outline btn-sm" onclick="loadAnalytics()"><i class="fa fa-sync"></i> Refresh</button>
            </div>
            <div class="g4" id="analyticsKpis" style="margin-bottom:20px"></div>
            <div class="g2" style="margin-bottom:20px">
              <div class="card">
                <div class="card-title">30-Day Trend</div>
                <div class="chart-wrap"><canvas id="chartTrend"></canvas></div>
              </div>
              <div class="card">
                <div class="card-title">Department Summary (Today)</div>
                <div class="chart-wrap"><canvas id="chartDept"></canvas></div>
              </div>
            </div>
            <div class="card">
              <div class="card-title">Monthly Summary — <span id="analyticsMonth"></span></div>
              <div class="tbl-wrap" style="border:none">
                <table>
                  <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Dept</th>
                      <th>Present</th>
                      <th>Absent</th>
                      <th>Half Day</th>
                      <th>WFH</th>
                      <th>Leave</th>
                      <th>Avg Hrs</th>
                      <th>Total Hrs</th>
                    </tr>
                  </thead>
                  <tbody id="monthBody"></tbody>
                </table>
              </div>
            </div>
          </div>
        <?php endif; ?>

      </main>
    </div>
  </div>

  <!-- ════ EDIT ATTENDANCE MODAL ════ -->
  <div class="modal-bg hidden" id="editModal">
    <div class="modal">
      <div class="modal-title"><i class="fa fa-pen-to-square" style="color:var(--accent)"></i> Edit Attendance</div>
      <div class="form-group"><label>Employee</label><input id="eEmpName" readonly style="opacity:.55"></div>
      <div class="g2">
        <div class="form-group"><label>Date</label><input id="eDate" readonly style="opacity:.55"></div>
        <div class="form-group">
          <label>Status</label>
          <select id="eStatus">
            <option value="present">Present</option>
            <option value="absent">Absent</option>
            <option value="half_day">Half Day</option>
            <option value="leave">Leave</option>
            <option value="work_from_home">Work From Home</option>
            <option value="holiday">Holiday</option>
          </select>
        </div>
      </div>
      <div class="g2">
        <div class="form-group">
          <label>Check In (IST)</label>
          <input type="time" id="eIn" class="fi" style="width:100%">
          <div class="form-hint">Enter time in HH:MM (24-hour, IST)</div>
        </div>
        <div class="form-group">
          <label>Check Out (IST)</label>
          <input type="time" id="eOut" class="fi" style="width:100%">
          <div class="form-hint">Enter time in HH:MM (24-hour, IST)</div>
        </div>
      </div>
      <div class="form-group"><label>Remarks</label><input id="eRemarks" placeholder="Optional note…"></div>
      <div class="modal-actions">
        <button class="btn btn-outline" onclick="closeEditModal()">Cancel</button>
        <button class="btn btn-primary" onclick="submitEdit()"><i class="fa fa-save"></i> Save</button>
      </div>
    </div>
  </div>

  <!-- ════ ADD EMPLOYEE MODAL ════ -->
  <div class="modal-bg hidden" id="addEmpModal">
    <div class="modal ws-emp-modal" style="width:580px">
      <div class="modal-title ws-modal-header">
        <span class="ws-modal-header-icon"><i class="fa fa-user-plus"></i></span>
        <span class="ws-modal-header-text">Add New Employee</span>
        <span class="ws-modal-header-badge">New Account</span>
        <button class="ws-modal-close-btn" type="button" onclick="closeAddEmpModal()" aria-label="Close modal">
          <i class="fa fa-times"></i>
        </button>
      </div>

      <div class="ws-modal-body">
        <div class="ws-form-section-label"><i class="fa fa-id-card"></i> Account Details</div>
        <div class="g2 ws-field-grid">
          <div class="form-group ws-form-group">
            <label class="ws-label">Employee ID <span class="ws-required">*</span></label>
            <input id="aeEmpId" class="ws-input" placeholder="e.g. EMP010" style="text-transform:uppercase">
            <div class="ws-hint"><i class="fa fa-circle-info"></i> Must be unique across all accounts</div>
          </div>
          <div class="form-group ws-form-group">
            <label class="ws-label">Full Name <span class="ws-required">*</span></label>
            <input id="aeName" class="ws-input" placeholder="Enter full name">
          </div>
        </div>
        <div class="form-group ws-form-group">
          <label class="ws-label">Email Address <span class="ws-required">*</span></label>
          <input id="aeEmail" class="ws-input" type="email" placeholder="employee@company.com">
        </div>
        <div class="form-group ws-form-group">
          <label class="ws-label">Password <span class="ws-required">*</span></label>
          <input id="aePass" class="ws-input" type="password" placeholder="Minimum 6 characters">
          <div class="ws-hint"><i class="fa fa-shield-halved"></i> Employee will use this to sign in</div>
        </div>

        <div class="ws-form-section-label"><i class="fa fa-sitemap"></i> Role &amp; Department</div>
        <div class="g2 ws-field-grid">
          <div class="form-group ws-form-group">
            <label class="ws-label">Role <span class="ws-required">*</span></label>
            <select id="aeRole" class="ws-select">
              <option value="">— Select role —</option>
            </select>
          </div>
          <div class="form-group ws-form-group">
            <label class="ws-label">Department</label>
            <select id="aeDept" class="ws-select">
              <option value="0">— None —</option>
            </select>
          </div>
        </div>
        <div class="form-group ws-form-group">
          <label class="ws-label">Designation</label>
          <input id="aeDesig" class="ws-input" placeholder="e.g. Software Engineer">
        </div>
      </div>

      <div class="modal-actions ws-modal-footer">
        <button class="btn ws-btn-cancel" onclick="closeAddEmpModal()"><i class="fa fa-times"></i> Cancel</button>
        <button class="btn ws-btn-submit" onclick="submitAddEmployee()"><i class="fa fa-user-plus"></i> Add Employee</button>
      </div>
    </div>
  </div>

  <!-- ════ EMPLOYEE WORK DETAIL MODAL ════ -->
  <div class="modal-bg hidden" id="empDetailModal">
    <div class="modal emp-detail-modal">
      <div class="emp-detail-shell">
        <div class="emp-detail-head">
          <div class="emp-detail-profile" id="empDetailHead">
            <div class="emp-detail-avatar"><i class="fa fa-user"></i></div>
            <div>
              <div class="emp-detail-name">Employee Detail</div>
              <div class="emp-detail-meta"><span class="emp-detail-chip">Loading</span></div>
            </div>
          </div>
          <button class="btn btn-outline btn-sm" type="button" onclick="ccCloseEmployeeDetail()"><i class="fa fa-times"></i> Close</button>
        </div>
        <div class="emp-detail-body" id="empDetailBody">
          <div class="emp-detail-empty"><i class="fa fa-spinner fa-spin"></i> Loading employee analytics…</div>
        </div>
      </div>
    </div>
  </div>

  <div class="toast" id="toast"></div>

  <script>
    // ── Config ──────────────────────────────────────────────────────────
    const API_BASE = <?= json_encode($apiBase) ?>;
    const LOGOUT_URL = <?= json_encode($logoutUrl) ?>;
    const PAGE_URL = <?= json_encode($pageUrl) ?>;
    let CSRF_NAME = <?= json_encode($csrfName) ?>;
    let CSRF_HASH = <?= json_encode($csrfHash) ?>;

    const PILL_MAP = {
      present: 'pp',
      absent: 'pa',
      half_day: 'ph',
      leave: 'pl',
      work_from_home: 'pw',
      holiday: 'pho'
    };
    const LABEL_MAP = {
      present: 'Present',
      absent: 'Absent',
      half_day: 'Half Day',
      leave: 'Leave',
      work_from_home: 'WFH',
      holiday: 'Holiday'
    };

    const IS_BLOCKED_VIEWPORT = false; // Responsive UI is enabled for desktop, tablet, and mobile.

    let S = {
      loggedIn: <?= $isLoggedIn ? 'true' : 'false' ?>,
      role: <?= json_encode($userRole) ?>,
      canEdit: <?= $canEdit      ? 'true' : 'false' ?>,
      canAnalytics: <?= $canAnalytics ? 'true' : 'false' ?>,
      canAddEmp: <?= $canAddEmp    ? 'true' : 'false' ?>,
      locData: null,
      charts: {},
      allUsers: [],
      manageRows: {},
      workProjects: [],
      workTasks: [],
      workMembers: [],
      workUpdates: [],
      workRows: [],
      canManageWork: false,
    };

    // ── Boot ────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
      initTheme();
      startClock();

      if (S.loggedIn) {
        loadTodayStatus();
        setDefaultWorkDates();
        loadWorkBootstrap();
        if (S.canEdit) loadDepartments();
        if (S.canAddEmp) loadRolesAndDepts();
      }
    });

    // ── Clock (browser local time — which the user should set to IST) ──
    // The hero clock shows the user's browser time. Since the server
    // enforces IST and all DB values are IST, the user's machine should
    // also be set to IST. The badge makes this explicit.
    function startClock() {
      const tick = () => {
        const n = new Date();
        const c = document.getElementById('clock');
        const d = document.getElementById('dateline');
        if (c) c.textContent = n.toLocaleTimeString('en-IN', {
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit',
          hour12: false,
          timeZone: 'Asia/Kolkata'
        });
        if (d) d.textContent = n.toLocaleDateString('en-IN', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          timeZone: 'Asia/Kolkata'
        });
      };
      tick();
      setInterval(tick, 1000);
    }


    // ── Theme ───────────────────────────────────────────────────────────
    function currentTheme() {
      return document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    }

    function initTheme() {
      let theme = 'light';
      try { theme = localStorage.getItem('workhive_theme') || 'light'; } catch (e) {}
      applyTheme(theme === 'dark' ? 'dark' : 'light', false);
    }

    function applyTheme(theme, persist = true) {
      const safeTheme = theme === 'dark' ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', safeTheme);
      document.body && document.body.setAttribute('data-theme', safeTheme);
      if (persist) {
        try { localStorage.setItem('workhive_theme', safeTheme); } catch (e) {}
      }
      const icon = document.getElementById('themeIcon');
      const text = document.getElementById('themeText');
      if (icon) icon.className = safeTheme === 'dark' ? 'fa fa-sun' : 'fa fa-moon';
      if (text) text.textContent = safeTheme === 'dark' ? 'Light' : 'Dark';
      refreshChartTheme();
    }

    function toggleTheme() {
      applyTheme(currentTheme() === 'dark' ? 'light' : 'dark');
    }

    function cssVar(name, fallback = '') {
      const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
      return value || fallback;
    }

    function refreshChartTheme() {
      if (!S || !S.charts) return;
      const textColor = cssVar('--chart-text', '#64748b');
      const gridColor = cssVar('--chart-grid', 'rgba(100,116,139,.14)');
      Object.keys(S.charts).forEach(id => {
        const chart = S.charts[id];
        if (!chart || !chart.options) return;
        if (chart.options.plugins && chart.options.plugins.legend && chart.options.plugins.legend.labels) {
          chart.options.plugins.legend.labels.color = textColor;
        }
        if (chart.options.scales) {
          ['x', 'y'].forEach(axis => {
            if (!chart.options.scales[axis]) return;
            if (chart.options.scales[axis].ticks) chart.options.scales[axis].ticks.color = textColor;
            if (chart.options.scales[axis].grid) chart.options.scales[axis].grid.color = gridColor;
          });
        }
        chart.update('none');
      });
    }

    // ── Mobile Navigation ────────────────────────────────────────────────
    function toggleMobNav() {
      const sidebar = document.getElementById('mainSidebar');
      const overlay = document.getElementById('mobNavOverlay');
      const btn     = document.getElementById('mobMenuBtn');
      const icon    = document.getElementById('mobMenuIcon');
      if (!sidebar) return;
      const isOpen = sidebar.classList.contains('mob-open');
      if (isOpen) {
        closeMobNav();
      } else {
        sidebar.classList.add('mob-open');
        overlay && overlay.classList.add('open');
        btn && btn.setAttribute('aria-expanded', 'true');
        icon && icon.classList.replace('fa-bars', 'fa-xmark');
        document.body.style.overflow = 'hidden';
      }
    }

    function closeMobNav() {
      const sidebar = document.getElementById('mainSidebar');
      const overlay = document.getElementById('mobNavOverlay');
      const btn     = document.getElementById('mobMenuBtn');
      const icon    = document.getElementById('mobMenuIcon');
      sidebar && sidebar.classList.remove('mob-open');
      overlay && overlay.classList.remove('open');
      btn && btn.setAttribute('aria-expanded', 'false');
      icon && (icon.classList.remove('fa-xmark'), icon.classList.add('fa-bars'));
      document.body.style.overflow = '';
    }

    // ── Work-Tab Dropdown (mobile) ────────────────────────────────────────
    function toggleWorkTabDropdown() {
      const tabs    = document.getElementById('workTabs');
      const btn     = document.getElementById('workTabMobBtn');
      if (!tabs) return;
      const isOpen = tabs.classList.contains('mob-dropdown');
      if (isOpen) {
        tabs.classList.remove('mob-dropdown');
        btn && btn.setAttribute('aria-expanded', 'false');
      } else {
        tabs.classList.add('mob-dropdown');
        btn && btn.setAttribute('aria-expanded', 'true');
      }
    }

    function closeWorkTabDropdown() {
      const tabs = document.getElementById('workTabs');
      const btn  = document.getElementById('workTabMobBtn');
      if (tabs) tabs.classList.remove('mob-dropdown');
      if (btn)  btn.setAttribute('aria-expanded', 'false');
    }

    // Close work-tab dropdown when clicking outside
    document.addEventListener('click', function(e) {
      const wrap = document.querySelector('.work-tabs-wrap');
      if (wrap && !wrap.contains(e.target)) closeWorkTabDropdown();
    });

    // ── Navigation ──────────────────────────────────────────────────────
    function showPage(p) {
      const page = document.getElementById(`page-${p}`);
      if (!page) return;
      document.querySelectorAll('.page').forEach(s => s.classList.remove('active'));
      document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
      page.classList.add('active');
      const ni = document.querySelector(`[data-p="${cssEscape(p)}"]`);
      if (ni) ni.classList.add('active');
      if (p === 'history') loadHistory();
      if (p === 'work') { setDefaultWorkDates(); loadWorkBootstrap(); }
      if (p === 'manage') loadManage();
      if (p === 'analytics') loadAnalytics();
      // Close mobile drawer after navigation
      closeMobNav();
    }

    // ── Auth ────────────────────────────────────────────────────────────
    function setLoginRole(role, btn) {
      const select = document.getElementById('lRole');
      if (!select) return;
      select.value = role;
      document.querySelectorAll('.role-choice').forEach(el => {
        el.classList.toggle('active', el.dataset.role === role);
      });
      if (btn) btn.blur();
    }

    function syncLoginRoleChoice() {
      const select = document.getElementById('lRole');
      if (!select) return;
      document.querySelectorAll('.role-choice').forEach(el => {
        el.classList.toggle('active', el.dataset.role === select.value);
      });
    }

    function toggleLoginPassword() {
      const pass = document.getElementById('lPass');
      const icon = document.getElementById('loginPassIcon');
      if (!pass) return;
      const showing = pass.type === 'text';
      pass.type = showing ? 'password' : 'text';
      if (icon) {
        icon.classList.toggle('fa-eye', showing);
        icon.classList.toggle('fa-eye-slash', !showing);
      }
      pass.focus();
    }

    function updateLoginIstClock() {
      const el = document.getElementById('loginIstClock');
      if (!el) return;
      const now = new Date();
      el.textContent = 'IST ' + now.toLocaleTimeString('en-IN', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
        timeZone: 'Asia/Kolkata'
      });
    }

    updateLoginIstClock();
    setInterval(updateLoginIstClock, 1000);
    document.getElementById('lRole')?.addEventListener('change', syncLoginRoleChoice);

    async function doLogin() {
      const role = document.getElementById('lRole').value;
      const eid = document.getElementById('lEmpId').value.trim();
      const pass = document.getElementById('lPass').value;
      const err = document.getElementById('lErr');
      const btn = document.getElementById('loginSubmitBtn');
      if (err) err.style.display = 'none';

      if (!role || !eid || !pass) {
        if (err) {
          err.textContent = 'Please select role and enter employee ID and password.';
          err.style.display = 'block';
        }
        return;
      }

      if (btn) btn.classList.add('is-loading');

      try {
        const res = await api('login', {
          role,
          employee_id: eid,
          password: pass
        }, 'POST');

        if (res.success) {
          window.location.href = PAGE_URL;
        } else {
          if (err) {
            err.textContent = res.message || 'Login failed. Please check your credentials.';
            err.style.display = 'block';
          }
          if (btn) btn.classList.remove('is-loading');
        }
      } catch (e) {
        if (err) {
          err.textContent = 'Unable to connect. Please try again.';
          err.style.display = 'block';
        }
        if (btn) btn.classList.remove('is-loading');
      }
    }

    function doLogout() {
      window.location.href = LOGOUT_URL;
    }

    const passInput = document.getElementById('lPass');
    if (passInput) passInput.addEventListener('keydown', e => {
      if (e.key === 'Enter') doLogin();
    });

    // ── Today status ────────────────────────────────────────────────────
    async function loadTodayStatus() {
      const res = await api('today_status');
      if (!res.success) return;

      const rec = res.record;
      const pill = document.getElementById('statusPill');
      const row = document.getElementById('ciRow');
      const btnIn = document.getElementById('btnIn');
      const btnOut = document.getElementById('btnOut');
      if (!pill || !row || !btnIn || !btnOut) return;

      row.style.display = 'none';
      btnIn.style.display = 'inline-flex';
      btnOut.style.display = 'none';

      if (!rec || !rec.check_in_time) {
        pill.innerHTML = `<div class="status-pill sp-idle"><i class="fa fa-circle-exclamation"></i> Not Marked Yet</div>`;
      } else {
        const st = rec.status || 'present';
        const vc = (st === 'present' || st === 'work_from_home') ? 'present' : 'absent';
        pill.innerHTML = `<div class="status-pill sp-${vc}">
          <i class="fa fa-circle ${rec.check_out_time ? '' : 'blink'}"></i> ${esc(LABEL_MAP[st] || st)}
        </div>`;
        row.style.display = 'flex';
        document.getElementById('ciIn').textContent = fmtTimeIst(rec.check_in_time);
        document.getElementById('ciOut').textContent = rec.check_out_time ? fmtTimeIst(rec.check_out_time) : '—';
        document.getElementById('ciHrs').textContent = formatHours(rec.work_hours);

        if (!rec.check_out_time) {
          btnIn.style.display = 'none';
          btnOut.style.display = 'inline-flex';
        } else {
          btnIn.style.display = 'none';
          btnOut.style.display = 'none';
          pill.innerHTML += `<p style="color:var(--green);font-size:.85rem;margin-top:10px;font-weight:500"><i class="fa fa-check-circle"></i> Attendance complete for today!</p>`;
        }
      }

      if (S.canAnalytics) loadTodayOverview();
    }

    async function loadTodayOverview() {
      const res = await api('dashboard_data');
      if (!res.success) return;
      const t = res.totals || {};
      const s = document.getElementById('todayStats');
      if (!s) return;
      s.innerHTML = `
        ${stat('Present',    t.present || 0,    'var(--green)',  'fa-user-check')}
        ${stat('Absent',     t.absent || 0,     'var(--red)',    'fa-user-xmark')}
        ${stat('WFH',        t.wfh || 0,        'var(--purple)', 'fa-laptop-house')}
        ${stat('Not Marked', t.not_marked || 0, 'var(--text2)',  'fa-clock')}
      `;
    }

    // ── Check In / Out ──────────────────────────────────────────────────
    async function doCheckIn() {
      if (!S.locData) {
        showToast('Please detect your location first.', 'error');
        return;
      }
      const res = await api('check_in', {
        latitude: S.locData.latitude,
        longitude: S.locData.longitude,
        location_name: S.locData.name
      }, 'POST');
      showToast(res.message || (res.success ? 'Checked in.' : 'Unable to check in.'), res.success ? 'success' : 'error');
      if (res.success) loadTodayStatus();
    }

    async function doCheckOut() {
      const res = await api('check_out', {}, 'POST');
      showToast(res.message || (res.success ? 'Checked out.' : 'Unable to check out.'), res.success ? 'success' : 'error');
      if (res.success) loadTodayStatus();
    }

    // ── Location ────────────────────────────────────────────────────────
    function fetchLocation() {
      const btn = document.getElementById('locBtn');
      const txt = document.getElementById('locText');
      if (!btn || !txt) return;
      txt.textContent = 'Detecting…';

      if (!navigator.geolocation) {
        S.locData = {
          latitude: null,
          longitude: null,
          name: 'Location not supported'
        };
        txt.textContent = 'Location not supported — proceeding anyway';
        btn.classList.add('loc-fetched');
        return;
      }

      navigator.geolocation.getCurrentPosition(pos => {
        const lat = pos.coords.latitude.toFixed(6);
        const lng = pos.coords.longitude.toFixed(6);
        S.locData = {
          latitude: lat,
          longitude: lng,
          name: `${lat}, ${lng}`
        };

        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}&format=json`)
          .then(r => r.json())
          .then(d => {
            const raw = d && d.display_name ? d.display_name : '';
            const name = raw ? raw.split(',').slice(0, 3).join(', ') : `${lat}, ${lng}`;
            S.locData.name = name;
            txt.textContent = name;
            btn.classList.add('loc-fetched');
          })
          .catch(() => {
            txt.textContent = `${lat}, ${lng}`;
            btn.classList.add('loc-fetched');
          });
      }, () => {
        S.locData = {
          latitude: null,
          longitude: null,
          name: 'Unknown'
        };
        txt.textContent = 'Location unavailable — proceeding anyway';
        btn.classList.add('loc-fetched');
      }, {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 60000
      });
    }

    // ── My History ──────────────────────────────────────────────────────
    async function loadHistory() {
      const from = document.getElementById('hFrom').value;
      const to = document.getElementById('hTo').value;
      const res = await api('my_attendance', {
        from,
        to
      }, 'GET');

      if (!res.success) {
        showToast(res.message || 'Unable to load history.', 'error');
        return;
      }

      const rows = Array.isArray(res.records) ? res.records : [];
      const counts = {
        present: 0,
        absent: 0,
        work_from_home: 0
      };
      let totalH = 0;

      rows.forEach(r => {
        if (counts[r.status] !== undefined) counts[r.status]++;
        const h = parseFloat(r.work_hours);
        if (!Number.isNaN(h)) totalH += h;
      });

      document.getElementById('histStats').innerHTML = `
        ${stat('Present', counts.present, 'var(--green)', 'fa-check')}
        ${stat('Absent',  counts.absent,  'var(--red)',   'fa-times')}
        ${stat('WFH',     counts.work_from_home, 'var(--purple)', 'fa-laptop')}
        ${stat('Total Hours', totalH.toFixed(1) + 'h', 'var(--accent)', 'fa-clock')}
      `;

      document.getElementById('histBody').innerHTML = rows.length ?
        rows.map(r => `
          <tr>
            <td class="mono">${esc(r.attendance_date)}</td>
            <td>${statusPill(r.status)}</td>
            <td class="mono">${fmtTimeIst(r.check_in_time)}</td>
            <td class="mono">${fmtTimeIst(r.check_out_time)}</td>
            <td class="mono">${formatHours(r.work_hours)}</td>
            <td style="font-size:.78rem;color:var(--text2);max-width:160px">${esc(r.location_name || '—')}</td>
            <td style="font-size:.78rem;color:var(--text2)">${esc(r.remarks || '—')}${String(r.is_manual_entry) === '1' ? ' <span class="pill pe">edited</span>' : ''}</td>
          </tr>`).join('') :
        `<tr><td colspan="7" style="text-align:center;color:var(--text2);padding:28px">No records found.</td></tr>`;
    }

    // ── Work Tracking + Attendance Integration ─────────────────────────
    let workSearchTimer = null;

    function setDefaultWorkDates() {
      const weekInput = document.getElementById('wkWeekStart');
      if (weekInput && !weekInput.value) weekInput.value = mondayDateString(new Date());
      const monthInput = document.getElementById('wkMonth');
      if (monthInput && !monthInput.value) monthInput.value = String(new Date().toISOString().slice(0, 7));
    }

    function showWorkPanel(panel) {
      document.querySelectorAll('.work-panel').forEach(x => x.classList.remove('active'));
      document.querySelectorAll('.work-tab').forEach(x => x.classList.remove('active'));
      const target = document.getElementById(`work-panel-${panel}`);
      if (target) target.classList.add('active');
      // Match tab by data-panel attribute — not fragile array index
      const activeTab = document.querySelector(`.work-tab[data-panel="${panel}"]`);
      if (activeTab) activeTab.classList.add('active');
      // Update mobile dropdown label
      const mobLabel = document.getElementById('workTabMobLabel');
      if (mobLabel && activeTab) {
        mobLabel.innerHTML = activeTab.innerHTML;
      }
      // Close mobile dropdown
      closeWorkTabDropdown();
      if (panel === 'reports')  { loadWorkWeek(); loadWorkMonth(); loadWorkDashboard(); }
      if (panel === 'command')  { loadCommandCenter(); }
    }

    function syncWorkTypeFields() {
      const isSchedule = val('wkType') === 'schedule';
      document.querySelectorAll('.schedule-only').forEach(el => el.style.display = isSchedule ? '' : 'none');
      const cat = document.getElementById('wkCategory');
      if (cat && isSchedule && cat.value === 'development') cat.value = 'operations';
    }

    function syncTaskTypeFromContainer() {
      const project = S.workProjects.find(p => String(p.id) === val('taskContainer'));
      const hiddenType = project && project.work_type === 'schedule' ? 'schedule_task' : 'project_task';
      const taskTitle = document.getElementById('taskTitle');
      if (taskTitle && !taskTitle.placeholder.includes(project && project.work_type === 'schedule' ? 'routine' : 'feature')) {
        taskTitle.placeholder = hiddenType === 'schedule_task' ? 'Example: Call 20 leads / collect documents' : 'Example: Build employee filter API';
      }
    }

    async function loadWorkBootstrap() {
      setDefaultWorkDates();
      // Load projects first (members + dropdown options depend on it)
      await loadWorkProjects();
      // Then tasks, updates, and dashboard in parallel
      await Promise.all([loadWorkTasks(), loadWorkUpdates(), loadWorkDashboard()]);
      if (document.getElementById('work-panel-command')?.classList.contains('active')) {
        loadCommandCenter();
      }
    }

    function debouncedWorkSearch() {
      clearTimeout(workSearchTimer);
      workSearchTimer = setTimeout(() => {
        loadWorkProjects();
        loadWorkTasks();
      }, 300);
    }

    async function loadWorkProjects() {
      const params = {
        work_type: val('wkFilterType'),
        status: val('wkFilterStatus'),
        search: val('wkSearch')
      };
      const r = await api('work_projects', params);
      if (!r.success) {
        renderWorkProjects([]);
        return showToast(r.message || 'Unable to load projects/schedules.', 'error');
      }
      S.workProjects = r.projects || [];
      S.workMembers = r.members || [];
      S.canManageWork = !!r.can_manage;
      renderWorkOptions();
      renderWorkProjects(S.workProjects);
    }

    function renderWorkOptions() {
      // Populate project/schedule selectors (task and update panels only)
      ['taskContainer', 'updContainer'].forEach(id => {
        const sel = document.getElementById(id);
        if (!sel) return;
        const current = sel.value;
        sel.innerHTML = '<option value="">— select project / schedule —</option>' + S.workProjects.map(p => {
          const type = p.work_type === 'schedule' ? 'Schedule' : 'Project';
          const code = p.project_code ? `${esc(p.project_code)} — ` : '';
          return `<option value="${escAttr(p.id)}">${type}: ${code}${esc(p.project_name || 'Untitled')}</option>`;
        }).join('');
        if (current) sel.value = current;
      });

      const memberOptions = '<option value="">Assign to me / auto</option>' + (S.workMembers || []).map(m => {
        const emp = m.employee_id ? ` (${esc(m.employee_id)})` : '';
        return `<option value="${escAttr(m.id)}">${esc(m.member_name || 'Member')}${emp}</option>`;
      }).join('');
      ['taskAssign'].forEach(id => {
        const sel = document.getElementById(id);
        if (sel) sel.innerHTML = memberOptions;
      });
      const assignees = document.getElementById('wkAssigneeList');
      if (assignees) {
        const members = S.workMembers || [];
        if (!members.length) {
          assignees.innerHTML = '<div class="assignee-empty">No members found.</div>';
        } else {
          assignees.innerHTML = members.map(m => {
            const ini = (m.member_name || 'M').trim().charAt(0).toUpperCase();
            const eid = m.employee_id ? m.employee_id : '';
            const dept = m.department ? ' · ' + m.department : '';
            return `<div class="assignee-item" data-id="${escAttr(String(m.id))}" data-name="${escAttr((m.member_name || '').toLowerCase())}" onclick="toggleAssigneeItem(this)">
              <input type="checkbox" value="${escAttr(String(m.id))}" tabindex="-1" style="position:absolute;opacity:0;pointer-events:none">
              <div class="assignee-chk-box"></div>
              <div class="assignee-avatar">${esc(ini)}</div>
              <div class="assignee-info">
                <div class="assignee-name">${esc(m.member_name || 'Member')}</div>
                <div class="assignee-eid">${esc(eid)}${esc(dept)}</div>
              </div>
            </div>`;
          }).join('');
        }
        updateAssigneeCount();
      }
      filterUpdateTasks();
    }

    function renderWorkProjects(rows) {
      const body = document.getElementById('workProjectBody');
      if (!body) return;
      if (!rows.length) {
        body.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text2);padding:28px">No project or schedule found. Create one from the Project / Schedule tab.</td></tr>';
        return;
      }
      body.innerHTML = rows.map(p => {
        const typeClass = p.work_type === 'schedule' ? 'schedule' : 'project';
        const typeLabel = p.work_type === 'schedule' ? 'Schedule' : 'Project';
        const progress = Math.max(0, Math.min(100, num(p.progress)));
        return `<tr>
          <td><span class="work-chip ${typeClass}">${esc(typeLabel)}</span></td>
          <td><b>${esc(p.project_name || 'Untitled')}</b><div class="muted-small">${esc(p.project_code || '')}</div></td>
          <td><span class="work-chip ${escAttr(p.status || '')}">${esc(labelize(p.status || 'planned'))}</span></td>
          <td>${esc(labelize(p.priority || 'medium'))}</td>
          <td><div class="progress-line" title="${progress}%"><span style="--p:${progress}%"></span></div><div class="muted-small">${progress}%</div></td>
          <td>${num(p.open_task_count)}/${num(p.task_count)} open</td>
          <td>${esc(p.due_date || p.schedule_end_date || '—')}</td>
          <td>${esc(p.created_by_name || p.owner_name || '—')}</td>
        </tr>`;
      }).join('');
    }

    async function saveWorkProject() {
      const name = val('wkName');
      if (!name) return showToast('Enter project / schedule name.', 'error');
      const selectedMembers = Array.from(document.querySelectorAll('#wkAssigneeList .assignee-item.is-checked input[type="checkbox"]')).map(cb => cb.value).filter(Boolean);
      const r = await api('work_save_project', {
        work_type: val('wkType') || 'project',
        project_code: val('wkCode'),
        project_name: name,
        project_type: val('wkCategory'),
        category: val('wkCategory'),
        visibility: val('wkVisibility') || 'assigned',
        priority: val('wkPriority') || 'medium',
        status: val('wkContainerStatus') || 'planned',
        start_date: val('wkStart'),
        due_date: val('wkDue'),
        schedule_frequency: val('wkScheduleFrequency') || 'one_time',
        schedule_start_date: val('wkStart'),
        schedule_end_date: val('wkDue'),
        repeat_days: val('wkRepeatDays'),
        description: val('wkDesc'),
        member_ids: selectedMembers.join(',')
      }, 'POST');
      if (!r.success) return showToast(r.message || 'Unable to save project / schedule.', 'error');
      showToast(r.message || 'Saved successfully.', 'success');
      ['wkName', 'wkCode', 'wkDesc', 'wkRepeatDays'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
      await loadWorkProjects();
      showWorkPanel('task');
    }

    async function loadWorkTasks() {
      const r = await api('work_tasks', { search: val('wkSearch'), project_id: val('updContainer') || '' });
      if (!r.success) {
        renderWorkTasks([]);
        return;
      }
      S.workTasks = r.tasks || [];
      renderWorkTasks(S.workTasks);
      filterUpdateTasks();
    }

    function renderWorkTasks(rows) {
      const body = document.getElementById('workTaskBody');
      if (!body) return;
      if (!rows.length) {
        body.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text2);padding:28px">No tasks found. Create your first task.</td></tr>';
        return;
      }
      body.innerHTML = rows.map(t => {
        const progress = Math.max(0, Math.min(100, num(t.progress)));
        return `<tr>
          <td><b>${esc(t.task_title || 'Task')}</b><div class="muted-small">${esc(t.task_code || '')}</div></td>
          <td>${esc(t.project_name || '—')}<div class="muted-small">${esc(t.work_type || '')}</div></td>
          <td>${esc(t.assigned_to_name || 'Me / Auto')}</td>
          <td><span class="work-chip ${escAttr(t.status || '')}">${esc(labelize(t.status || 'todo'))}</span></td>
          <td><div class="progress-line"><span style="--p:${progress}%"></span></div><div class="muted-small">${progress}%</div></td>
          <td>${esc(t.due_date || '—')}</td>
          <td>${num(t.logged_hours || t.actual_hours).toFixed(1)} / ${num(t.planned_hours).toFixed(1)}</td>
        </tr>`;
      }).join('');
    }

    async function saveWorkTask() {
      const projectId = val('taskContainer');
      const title = val('taskTitle');
      if (!projectId) return showToast('Select project / schedule.', 'error');
      if (!title) return showToast('Enter task title.', 'error');
      const project = S.workProjects.find(p => String(p.id) === String(projectId));
      const r = await api('work_save_task', {
        project_id: projectId,
        task_title: title,
        task_description: val('taskDesc'),
        task_type: project && project.work_type === 'schedule' ? 'schedule_task' : 'project_task',
        assigned_to_member_id: val('taskAssign'),
        priority: val('taskPriority') || 'medium',
        status: val('taskStatus') || 'todo',
        progress: val('taskProgress') || '0',
        due_date: val('taskDue'),
        planned_hours: val('taskHours') || '0',
        checklist: val('taskChecklist')
      }, 'POST');
      if (!r.success) return showToast(r.message || 'Unable to save task.', 'error');
      showToast('Task saved successfully.', 'success');
      ['taskTitle', 'taskDesc', 'taskChecklist'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
      await loadWorkTasks();
      showWorkPanel('update');
    }

    function filterUpdateTasks() {
      const sel = document.getElementById('updTask');
      if (!sel) return;
      const projectId = val('updContainer');
      const tasks = (S.workTasks || []).filter(t => !projectId || String(t.project_id) === String(projectId));
      const current = sel.value;
      sel.innerHTML = '<option value="">General update / no task</option>' + tasks.map(t => {
        return `<option value="${escAttr(t.id)}" data-project="${escAttr(t.project_id)}">${esc(t.task_title || 'Task')} — ${esc(labelize(t.status || 'todo'))}</option>`;
      }).join('');
      if (current) sel.value = current;
    }

    async function submitWorkUpdate() {
      const taskId = val('updTask');
      let projectId = val('updContainer');
      if (taskId) {
        const task = (S.workTasks || []).find(t => String(t.id) === String(taskId));
        if (task) projectId = String(task.project_id);
      }
      const summary = val('updSummary');
      if (!projectId && !taskId) return showToast('Select project/schedule or task.', 'error');
      if (!val('updDate')) return showToast('Please select work date.', 'error');
      if (!summary) return showToast('Please enter work summary.', 'error');

      const doneLines = val('updStepsDone').split('\n').map(x => x.trim()).filter(Boolean);
      const nextLines = val('updNextPlan').split('\n').map(x => x.trim()).filter(Boolean);
      const steps = [];
      doneLines.forEach((title, index) => steps.push({ step_title: title.replace(/^\d+[).\-\s]+/, ''), status: 'completed', done: 1, no: index + 1, completion_percent: 100 }));
      nextLines.forEach((title, index) => steps.push({ step_title: title.replace(/^\d+[).\-\s]+/, ''), status: 'pending', done: 0, no: doneLines.length + index + 1 }));

      const r = await api('work_save_update', {
        project_id: projectId,
        task_id: taskId,
        work_date: val('updDate'),
        update_date: val('updDate'),
        summary: summary,
        update_title: summary,
        today_task: summary,
        progress_note: summary,
        work_status: val('updStatus') || 'working',
        completion_percent: val('updProgress') || '',
        hours_spent: val('updHours') || '0',
        completed_tasks: val('updAchievements') || val('updStepsDone'),
        achievements: val('updAchievements') || val('updStepsDone'),
        blockers: val('updBlockers'),
        tomorrow_plan: val('updNextPlan'),
        next_plan: val('updNextPlan'),
        steps_json: JSON.stringify(steps),
        is_today_focus: '1',
        sync_project_progress: '1'
      }, 'POST');
      if (!r.success) return showToast(r.message || 'Unable to save work update.', 'error');
      showToast('Daily work update saved and linked with attendance.', 'success');
      ['updSummary', 'updAchievements', 'updStepsDone', 'updBlockers', 'updNextPlan'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
      await loadWorkBootstrap();
      loadTodayStatus();
    }

    async function loadWorkUpdates() {
      const r = await api('work_updates', { today: '' });
      if (!r.success) return renderWorkUpdates([]);
      S.workUpdates = r.updates || [];
      renderWorkUpdates(S.workUpdates);
    }

    function renderWorkUpdates(rows) {
      const body = document.getElementById('workUpdateBody');
      if (!body) return;
      if (!rows.length) {
        body.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text2);padding:28px">No daily updates yet.</td></tr>';
        return;
      }
      body.innerHTML = rows.slice(0, 20).map(r => {
        const progress = r.completion_percent == null ? '—' : `${num(r.completion_percent)}%`;
        return `<tr>
          <td>${esc(r.update_date || '—')}</td>
          <td>${esc(r.member_name || r.attend_user_name || 'Me')}</td>
          <td>${esc(r.project_name || '—')}<div class="muted-small">${esc(r.work_type || '')}</div></td>
          <td>${esc(r.task_title || 'General')}</td>
          <td><span class="work-chip ${escAttr(r.work_status || '')}">${esc(labelize(r.work_status || 'working'))}</span></td>
          <td>${esc(progress)}</td>
          <td>${num(r.hours_spent).toFixed(1)}</td>
          <td><span class="work-chip ${escAttr(r.review_status || 'submitted')}">${esc(labelize(r.review_status || 'submitted'))}</span></td>
        </tr>`;
      }).join('');
    }

    async function loadWorkDashboard() {
      const r = await api('work_dashboard');
      if (!r.success) return;
      renderWorkDashboard(r.dashboard || {});
      renderWorkComparison((r.dashboard || {}).comparison || {});
    }

    function renderWorkDashboard(d) {
      const box = document.getElementById('workKpis');
      if (!box) return;
      const p = d.projects || {};
      const t = d.tasks || {};
      const steps = d.steps_today || {};
      box.innerHTML = `
        <div class="work-mini"><div class="v">${num(p.projects)}</div><div class="l">Projects</div></div>
        <div class="work-mini"><div class="v">${num(p.schedules)}</div><div class="l">Schedules</div></div>
        <div class="work-mini"><div class="v">${num(t.total)}</div><div class="l">Tasks</div></div>
        <div class="work-mini"><div class="v">${num(d.hours_today).toFixed(1)}</div><div class="l">Hours Today</div></div>
        <div class="work-mini"><div class="v">${num(d.updates_today)}</div><div class="l">Updates Today</div></div>
        <div class="work-mini"><div class="v">${num(t.blocked)}</div><div class="l">Blocked Tasks</div></div>
        <div class="work-mini"><div class="v">${num(steps.done)}/${num(steps.total)}</div><div class="l">Steps Today</div></div>
        <div class="work-mini"><div class="v">${num(d.hours_this_week).toFixed(1)}</div><div class="l">Week Hours</div></div>`;
    }

    function renderWorkComparison(c) {
      const box = document.getElementById('compareKpis');
      if (!box) return;
      const dd = c.day_delta || {};
      const wd = c.week_delta || {};
      const today = c.today || {};
      const yesterday = c.yesterday || {};
      box.innerHTML = `
        <div class="work-mini"><div class="v">${num(today.updates)}</div><div class="l">Today Updates</div></div>
        <div class="work-mini"><div class="v">${num(yesterday.updates)}</div><div class="l">Yesterday</div></div>
        <div class="work-mini"><div class="v">${num(dd.updates) >= 0 ? '+' : ''}${num(dd.updates)}</div><div class="l">Day Δ Updates</div></div>
        <div class="work-mini"><div class="v">${num(dd.hours).toFixed(1)}</div><div class="l">Day Δ Hours</div></div>
        <div class="work-mini"><div class="v">${num((c.this_week || {}).updates)}</div><div class="l">This Week</div></div>
        <div class="work-mini"><div class="v">${num((c.last_week || {}).updates)}</div><div class="l">Last Week</div></div>
        <div class="work-mini"><div class="v">${num(wd.updates) >= 0 ? '+' : ''}${num(wd.updates)}</div><div class="l">Week Δ Updates</div></div>
        <div class="work-mini"><div class="v">${num(wd.hours).toFixed(1)}</div><div class="l">Week Δ Hours</div></div>`;
    }

    async function loadWorkWeek() {
      setDefaultWorkDates();
      const from = val('wkWeekStart') || mondayDateString(new Date());
      const to = addDays(from, 6);
      const r = await api('work_week', { from, to });
      if (!r.success) {
        renderWorkDaily([]);
        return;
      }
      const rows = r.daily || [];
      S.workRows = rows;
      renderWorkDaily(rows);
    }

    async function loadWorkMonth() {
      setDefaultWorkDates();
      const monthValue = val('wkMonth') || String(new Date().toISOString().slice(0, 7));
      const parts = monthValue.split('-');
      const r = await api('work_month', { year: parts[0] || '', month: parts[1] || '' });
      if (!r.success) return showToast(r.message || 'Unable to load monthly work.', 'error');
      renderWorkMonthly(r.monthly || r.rows || []);
    }

    function renderWorkDaily(rows) {
      const body = document.getElementById('workWeekBody');
      if (!body) return;
      if (!rows.length) {
        body.innerHTML = '<tr><td colspan="9" style="text-align:center;color:var(--text2);padding:28px">No work/attendance records found for this week</td></tr>';
        return;
      }
      body.innerHTML = rows.map(r => `
        <tr>
          <td>${esc(r.work_date || r.attendance_date || '—')}</td>
          <td>${esc(labelize(r.attendance_status || 'pending'))}</td>
          <td>${num(r.attendance_hours).toFixed(1)}</td>
          <td>${num(r.work_update_count)}</td>
          <td>${num(r.task_update_count)}</td>
          <td>${num(r.work_logged_hours).toFixed(1)}</td>
          <td>${num(r.completed_steps)}/${num(r.total_steps)}</td>
          <td>${num(r.avg_progress).toFixed(1)}%</td>
          <td>${esc(r.project_names || r.task_titles || '—')}</td>
        </tr>`).join('');
    }

    function renderWorkMonthly(rows) {
      const body = document.getElementById('workMonthBody');
      if (!body) return;
      if (!rows.length) {
        body.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text2);padding:28px">No monthly work records found</td></tr>';
        return;
      }
      body.innerHTML = rows.map(r => `
        <tr>
          <td>${esc(r.full_name || r.employee_name || 'Employee')}</td>
          <td>${num(r.present_days)}</td>
          <td>${num(r.attendance_hours || r.total_attendance_hours).toFixed(1)}</td>
          <td>${num(r.work_update_count || r.total_updates)}</td>
          <td>${num(r.task_update_count)}</td>
          <td>${num(r.work_logged_hours || r.total_work_logged_hours).toFixed(1)}</td>
          <td>${num(r.completed_steps)}/${num(r.total_steps)}</td>
          <td>${num(r.avg_progress || r.avg_completion_percent).toFixed(1)}%</td>
        </tr>`).join('');
    }

    function labelize(v) {
      return String(v || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    }

    function statHtml(value, label, color) {
      return `<div class="stat" style="--c:${color}"><div class="val">${esc(value)}</div><div class="lbl">${esc(label)}</div></div>`;
    }

    function val(id) {
      const el = document.getElementById(id);
      return el ? String(el.value || '').trim() : '';
    }

    function num(v) {
      const n = parseFloat(v);
      return isNaN(n) ? 0 : n;
    }

    function mondayDateString(d) {
      const copy = new Date(d.getFullYear(), d.getMonth(), d.getDate());
      const day = copy.getDay() || 7;
      copy.setDate(copy.getDate() - day + 1);
      return dateString(copy);
    }

    function addDays(dateStr, days) {
      const parts = String(dateStr).split('-').map(Number);
      const d = new Date(parts[0], (parts[1] || 1) - 1, parts[2] || 1);
      d.setDate(d.getDate() + days);
      return dateString(d);
    }

    function dateString(d) {
      const y = d.getFullYear();
      const m = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${y}-${m}-${day}`;
    }


    // ── Departments (for manage filter) ─────────────────────────────────
    async function loadDepartments() {
      const res = await api('departments');
      if (!res.success) return;
      const sel = document.getElementById('mgmtDept');
      if (!sel) return;
      sel.querySelectorAll('option:not([value="0"])').forEach(o => o.remove());
      (res.departments || []).forEach(d => {
        const o = document.createElement('option');
        o.value = d.id;
        o.textContent = d.name;
        sel.appendChild(o);
      });
    }

    // ── Roles & Departments for Add Employee modal ───────────────────────
    async function loadRolesAndDepts() {
      const [rRes, dRes] = await Promise.all([api('roles'), api('departments')]);

      if (rRes.success) {
        const sel = document.getElementById('aeRole');
        if (sel) {
          sel.querySelectorAll('option:not([value=""])').forEach(o => o.remove());
          (rRes.roles || []).forEach(r => {
            const o = document.createElement('option');
            o.value = r.id;
            o.textContent = r.label || r.name;
            sel.appendChild(o);
          });
        }
      }

      if (dRes.success) {
        const sel = document.getElementById('aeDept');
        if (sel) {
          sel.querySelectorAll('option:not([value="0"])').forEach(o => o.remove());
          (dRes.departments || []).forEach(d => {
            const o = document.createElement('option');
            o.value = d.id;
            o.textContent = d.name;
            sel.appendChild(o);
          });
        }
      }
    }

    // ── Manage ──────────────────────────────────────────────────────────
    async function loadManage() {
      const dateEl = document.getElementById('mgmtDate');
      const deptEl = document.getElementById('mgmtDept');
      const body = document.getElementById('manageBody');
      if (!dateEl || !body) return;

      const date = dateEl.value;
      const dept = deptEl ? deptEl.value : 0;
      const res = await api('all_attendance', {
        from: date,
        to: date,
        dept
      }, 'GET');

      if (!res.success) {
        showToast(res.message || 'Unable to load attendance.', 'error');
        return;
      }

      const rows = Array.isArray(res.records) ? res.records : [];
      S.manageRows = {};

      body.innerHTML = rows.length ?
        rows.map(r => {
          const uid = String(r.user_id || r.id || '');
          r._edit_date = date;
          S.manageRows[uid] = r;
          return `<tr>
              <td class="mono">${esc(r.employee_id || '—')}</td>
              <td style="font-weight:600">${esc(r.full_name || '—')}</td>
              <td style="color:var(--text2)">${esc(r.department_name || '—')}</td>
              <td>${r.status ? statusPill(r.status) : '<span style="color:var(--text3)">—</span>'}</td>
              <td class="mono">${fmtTimeIst(r.check_in_time)}</td>
              <td class="mono">${fmtTimeIst(r.check_out_time)}</td>
              <td class="mono">${formatHours(r.work_hours)}</td>
              <td style="font-size:.78rem">${String(r.is_manual_entry) === '1' ? '<span class="pill pe">edited</span>' : (r.check_in_time ? 'Self' : '—')}</td>
              <td>
                <button class="btn btn-warn btn-sm" onclick="openEditFromRow('${escAttr(uid)}')">
                  <i class="fa fa-pen"></i> Edit
                </button>
              </td>
            </tr>`;
        }).join('') :
        `<tr><td colspan="9" style="text-align:center;color:var(--text2);padding:28px">No employees found.</td></tr>`;
    }

    // ── Edit Attendance ─────────────────────────────────────────────────
    let editUid = 0,
      editDate = '';

    function openEditFromRow(uid) {
      const rec = S.manageRows[String(uid)] || {};
      openEdit(uid, rec.full_name || '', rec._edit_date || document.getElementById('mgmtDate').value, rec);
    }

    function openEdit(uid, name, date, rec) {
      editUid = uid;
      editDate = date;
      document.getElementById('eEmpName').value = name || '';
      document.getElementById('eDate').value = date || '';
      document.getElementById('eStatus').value = rec.status || 'present';

      // Times from DB are plain IST strings "YYYY-MM-DD HH:MM:SS"
      // We extract just HH:MM for the <input type="time"> value.
      document.getElementById('eIn').value = extractHHMM(rec.check_in_time);
      document.getElementById('eOut').value = extractHHMM(rec.check_out_time);
      document.getElementById('eRemarks').value = rec.remarks || '';

      document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
      document.getElementById('editModal').classList.add('hidden');
    }

    async function submitEdit() {
      const checkIn = document.getElementById('eIn').value;
      const checkOut = document.getElementById('eOut').value;

      // Client-side time order check
      if (checkIn && checkOut && checkIn >= checkOut) {
        showToast('Check-out time must be after check-in time.', 'error');
        return;
      }

      const res = await api('edit_attendance', {
        user_id: editUid,
        date: editDate,
        status: document.getElementById('eStatus').value,
        check_in: checkIn,
        check_out: checkOut,
        remarks: document.getElementById('eRemarks').value,
      }, 'POST');

      showToast(res.message || (res.success ? 'Saved.' : 'Unable to save.'), res.success ? 'success' : 'error');
      if (res.success) {
        closeEditModal();
        loadManage();
        if (S.canAnalytics) loadTodayOverview();
      }
    }

    document.getElementById('editModal')?.addEventListener('click', e => {
      if (e.target === e.currentTarget) closeEditModal();
    });

    // ── Add Employee ────────────────────────────────────────────────────
    function openAddEmployee() {
      document.getElementById('aeEmpId').value = '';
      document.getElementById('aeName').value = '';
      document.getElementById('aeEmail').value = '';
      document.getElementById('aePass').value = '';
      document.getElementById('aeRole').value = '';
      document.getElementById('aeDept').value = '0';
      document.getElementById('aeDesig').value = '';
      document.getElementById('addEmpModal').classList.remove('hidden');
    }

    function closeAddEmpModal() {
      document.getElementById('addEmpModal').classList.add('hidden');
    }

    async function submitAddEmployee() {
      const empId = document.getElementById('aeEmpId').value.trim().toUpperCase();
      const name = document.getElementById('aeName').value.trim();
      const email = document.getElementById('aeEmail').value.trim();
      const pass = document.getElementById('aePass').value.trim();
      const roleId = document.getElementById('aeRole').value;
      const deptId = document.getElementById('aeDept').value;
      const desig = document.getElementById('aeDesig').value.trim();

      if (!empId || !name || !email || !pass || !roleId) {
        showToast('Please fill in all required fields.', 'error');
        return;
      }

      const res = await api('add_employee', {
        employee_id: empId,
        full_name: name,
        email: email,
        password: pass,
        role_id: roleId,
        department_id: deptId,
        designation: desig,
      }, 'POST');

      showToast(res.message || (res.success ? 'Employee added.' : 'Failed.'), res.success ? 'success' : 'error');

      if (res.success) {
        closeAddEmpModal();
        // Refresh manage list if currently visible
        const mgmt = document.getElementById('page-manage');
        if (mgmt && mgmt.classList.contains('active')) loadManage();
      }
    }

    document.getElementById('addEmpModal')?.addEventListener('click', e => {
      if (e.target === e.currentTarget) closeAddEmpModal();
    });

    // ── Analytics ───────────────────────────────────────────────────────
    async function loadAnalytics() {
      const res = await api('dashboard_data');
      if (!res.success) {
        showToast(res.message || 'Unable to load analytics.', 'error');
        return;
      }

      const t = res.totals || {};
      document.getElementById('analyticsKpis').innerHTML = `
        ${stat('Present Today',  t.present || 0,    'var(--green)',  'fa-user-check')}
        ${stat('Absent Today',   t.absent || 0,     'var(--red)',    'fa-user-xmark')}
        ${stat('WFH Today',      t.wfh || 0,        'var(--purple)', 'fa-laptop-house')}
        ${stat('Not Marked',     t.not_marked || 0, 'var(--text2)',  'fa-clock')}
      `;

      document.getElementById('analyticsMonth').textContent = new Date().toLocaleString('en-IN', {
        month: 'long',
        year: 'numeric',
        timeZone: 'Asia/Kolkata'
      });

      const td = Array.isArray(res.trend) ? res.trend : [];
      buildChart('chartTrend', 'line', td.map(r => String(r.attendance_date || '').substr(5)), [{
          label: 'Present',
          data: td.map(r => Number(r.present) || 0),
          borderColor: '#10b981',
          backgroundColor: 'rgba(16,185,129,.08)',
          fill: true,
          tension: .4,
          pointRadius: 2
        },
        {
          label: 'Absent',
          data: td.map(r => Number(r.absent) || 0),
          borderColor: '#ef4444',
          backgroundColor: 'rgba(239,68,68,.05)',
          fill: true,
          tension: .4,
          pointRadius: 2
        },
        {
          label: 'WFH',
          data: td.map(r => Number(r.wfh) || 0),
          borderColor: '#8b5cf6',
          backgroundColor: 'rgba(139,92,246,.05)',
          fill: true,
          tension: .4,
          pointRadius: 2
        },
      ]);

      const dd = Array.isArray(res.dept_stats) ? res.dept_stats : [];
      buildChart('chartDept', 'bar', dd.map(d => d.department || 'N/A'), [{
          label: 'Present',
          data: dd.map(d => Number(d.present) || 0),
          backgroundColor: 'rgba(16,185,129,.7)',
          borderRadius: 4
        },
        {
          label: 'Absent',
          data: dd.map(d => Number(d.absent) || 0),
          backgroundColor: 'rgba(239,68,68,.7)',
          borderRadius: 4
        },
        {
          label: 'WFH',
          data: dd.map(d => Number(d.wfh) || 0),
          backgroundColor: 'rgba(139,92,246,.7)',
          borderRadius: 4
        },
      ]);

      const monthly = Array.isArray(res.monthly) ? res.monthly : [];
      document.getElementById('monthBody').innerHTML = monthly.length ?
        monthly.map(r => `<tr>
          <td><div style="font-weight:600">${esc(r.full_name||'—')}</div>
              <div class="mono" style="font-size:.72rem;color:var(--text2)">${esc(r.employee_id||'—')}</div></td>
          <td style="color:var(--text2)">${esc(r.department||'—')}</td>
          <td style="color:var(--green);font-weight:700">${esc(r.present||0)}</td>
          <td style="color:var(--red)">${esc(r.absent||0)}</td>
          <td style="color:var(--yellow)">${esc(r.half_day||0)}</td>
          <td style="color:var(--purple)">${esc(r.wfh||0)}</td>
          <td style="color:var(--accent)">${esc(r.on_leave||0)}</td>
          <td class="mono">${formatHours(r.avg_work_hours)}</td>
          <td class="mono">${formatHours(r.total_work_hours)}</td>
        </tr>`).join('') :
        `<tr><td colspan="9" style="text-align:center;color:var(--text2);padding:24px">No data.</td></tr>`;
    }

    function buildChart(id, type, labels, datasets) {
      const chartText = cssVar('--chart-text', '#64748b');
      const chartGrid = cssVar('--chart-grid', 'rgba(100,116,139,.14)');
      const el = document.getElementById(id);
      if (!el || typeof Chart === 'undefined') return;
      const ctx = el.getContext('2d');
      if (!ctx) return;
      if (S.charts[id]) S.charts[id].destroy();
      S.charts[id] = new Chart(ctx, {
        type,
        data: {
          labels,
          datasets
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              labels: {
                color: chartText,
                font: {
                  size: 11
                }
              }
            }
          },
          scales: {
            x: {
              ticks: {
                color: chartText,
                font: {
                  size: 10
                }
              },
              grid: {
                color: chartGrid
              }
            },
            y: {
              ticks: {
                color: chartText,
                font: {
                  size: 10
                }
              },
              grid: {
                color: chartGrid
              }
            },
          }
        }
      });
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    async function api(action, data = null, method = 'GET') {
      const httpMethod = String(method || 'GET').toUpperCase();
      const base = `${String(API_BASE).replace(/\/+$/, '')}/${encodeURIComponent(action)}`;
      const opts = {
        method: httpMethod,
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      };
      let url = base;

      if (httpMethod === 'GET') {
        const params = new URLSearchParams();
        if (data instanceof URLSearchParams) {
          data.forEach((v, k) => { if (v != null && v !== '') params.append(k, v); });
        } else if (data && typeof data === 'object') {
          Object.keys(data).forEach(k => {
            if (data[k] != null && data[k] !== '') params.append(k, data[k]);
          });
        }
        const qs = params.toString();
        if (qs) url += `?${qs}`;
      } else {
        const payload = new URLSearchParams();
        if (data instanceof URLSearchParams) {
          data.forEach((v, k) => { if (v != null) payload.append(k, v); });
        } else if (data && typeof data === 'object') {
          Object.keys(data).forEach(k => {
            if (data[k] != null) payload.append(k, data[k]);
          });
        }
        if (CSRF_NAME && CSRF_HASH) payload.append(CSRF_NAME, CSRF_HASH);
        opts.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
        opts.body = payload.toString();
      }

      try {
        const response = await fetch(url, opts);
        const text = await response.text();
        let result;
        try {
          result = text ? JSON.parse(text) : {};
        } catch (e) {
          return {
            success: false,
            message: 'Invalid server response.',
            raw: text
          };
        }
        if (result && result.csrf_hash) CSRF_HASH = result.csrf_hash;
        if (!response.ok && result.success !== false) {
          result.success = false;
          result.message = result.message || `Request failed with status ${response.status}`;
        }
        return result;
      } catch (e) {
        return {
          success: false,
          message: 'Network error. Please try again.'
        };
      }
    }

    /**
     * fmtTimeIst — Format an IST datetime string "YYYY-MM-DD HH:MM:SS" as
     * a readable 12-hour IST time like "09:30 AM".
     *
     * IMPORTANT: The DB stores IST (no UTC suffix). We must NOT append 'Z'
     * (which would tell JS the string is UTC and shift it by +5:30 again).
     * Instead we parse it as a local time and display it with the IST locale.
     * Since the DB value IS already IST, the result is always correct on any
     * machine when we specify timeZone:'Asia/Kolkata' explicitly.
     */
    function fmtTimeIst(dt) {
      if (!dt) return '—';
      const s = String(dt).replace(' ', 'T'); // "YYYY-MM-DDTHH:MM:SS"
      // Append offset so the Date constructor treats it as IST regardless of
      // what timezone the browser is set to.
      const date = new Date(s + '+05:30');
      if (Number.isNaN(date.getTime())) return '—';
      return date.toLocaleTimeString('en-IN', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
        timeZone: 'Asia/Kolkata'
      });
    }

    /**
     * extractHHMM — Pull HH:MM from "YYYY-MM-DD HH:MM:SS" for <input type="time">.
     * The value is already IST from the DB — no conversion needed.
     */
    function extractHHMM(dt) {
      if (!dt) return '';
      const s = String(dt).replace('T', ' ');
      // Format: "YYYY-MM-DD HH:MM:SS" → take chars 11..15
      return s.length >= 16 ? s.substr(11, 5) : '';
    }

    function formatHours(value) {
      const n = parseFloat(value);
      if (Number.isNaN(n)) return '—';
      return n.toFixed(2).replace(/\.00$/, '') + 'h';
    }

    function statusPill(status) {
      const s = status || '';
      return `<span class="pill ${escAttr(PILL_MAP[s] || 'pp')}">${esc(LABEL_MAP[s] || s || '—')}</span>`;
    }

    function stat(label, val, color, icon) {
      return `<div class="stat" style="--c:${escAttr(color)}">
        <div class="val">${esc(val)}</div>
        <div class="lbl">${esc(label)}</div>
        <i class="fa ${escAttr(icon)} ico" style="color:${escAttr(color)}"></i>
      </div>`;
    }

    function showToast(msg, type = 'info') {
      const t = document.getElementById('toast');
      if (!t) return;
      const cleanType = ['success', 'error', 'info'].includes(type) ? type : 'info';
      t.className = `toast t-${cleanType} show`;
      const icon = cleanType === 'success' ? 'fa-check-circle' : cleanType === 'error' ? 'fa-times-circle' : 'fa-info-circle';
      t.innerHTML = `<i class="fa ${icon}"></i> ${esc(msg || '')}`;
      clearTimeout(window._tt);
      window._tt = setTimeout(() => t.classList.remove('show'), 3200);
    }

    function esc(value) {
      return String(value == null ? '' : value)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function escAttr(value) {
      return esc(value).replace(/`/g, '&#096;');
    }

    function cssEscape(value) {
      if (window.CSS && typeof window.CSS.escape === 'function') return window.CSS.escape(value);
      return String(value).replace(/[^a-zA-Z0-9_-]/g, '');
    }


    // ── Assignee Checkbox Picker helpers ─────────────────────────────
    function toggleAssigneeItem(label) {
      label.classList.toggle('is-checked');
      const cb = label.querySelector('input[type="checkbox"]');
      if (cb) cb.checked = label.classList.contains('is-checked');
      updateAssigneeCount();
    }

    function updateAssigneeCount() {
      const checked = document.querySelectorAll('#wkAssigneeList .assignee-item.is-checked').length;
      const el = document.getElementById('wkAssigneeCount');
      if (el) el.textContent = checked === 0 ? '0 selected' : checked + ' selected';
    }

    function selectAllAssignees() {
      document.querySelectorAll('#wkAssigneeList .assignee-item:not(.assignee-hidden)').forEach(item => {
        item.classList.add('is-checked');
        const cb = item.querySelector('input[type="checkbox"]');
        if (cb) cb.checked = true;
      });
      updateAssigneeCount();
    }

    function clearAllAssignees() {
      document.querySelectorAll('#wkAssigneeList .assignee-item').forEach(item => {
        item.classList.remove('is-checked');
        const cb = item.querySelector('input[type="checkbox"]');
        if (cb) cb.checked = false;
      });
      updateAssigneeCount();
    }

    function filterAssigneeList() {
      const q = (document.getElementById('wkAssigneeSearch')?.value || '').toLowerCase().trim();
      document.querySelectorAll('#wkAssigneeList .assignee-item').forEach(item => {
        const name = item.dataset.name || '';
        const idAttr = (item.dataset.id || '').toLowerCase();
        item.classList.toggle('assignee-hidden', q !== '' && !name.includes(q) && !idAttr.includes(q));
      });
    }

    // ── Command Center ──────────────────────────────────────────────
    let _ccDebounce = null;
    function debouncedCcSearch() {
      clearTimeout(_ccDebounce);
      _ccDebounce = setTimeout(loadCommandCenter, 420);
    }

    function onCcPeriodChange() {
      const p = (document.getElementById('ccPeriod') || {}).value || 'today';
      const day = document.getElementById('ccDay');
      const weekOf = document.getElementById('ccWeekOf');
      const month = document.getElementById('ccMonth');
      const year = document.getElementById('ccYear');
      const from = document.getElementById('ccFrom');
      const to = document.getElementById('ccTo');
      const today = dateString(new Date());
      const yyyyMm = today.slice(0, 7);
      const yyyy = today.slice(0, 4);

      if (day) {
        day.style.display = p === 'specific_day' ? '' : 'none';
        if (p === 'specific_day' && !day.value) day.value = today;
      }
      if (weekOf) {
        weekOf.style.display = p === 'specific_week' ? '' : 'none';
        if (p === 'specific_week' && !weekOf.value) weekOf.value = today;
      }
      if (month) {
        month.style.display = p === 'specific_month' ? '' : 'none';
        if (p === 'specific_month' && !month.value) month.value = yyyyMm;
      }
      if (year) {
        year.style.display = p === 'specific_year' ? '' : 'none';
        if (p === 'specific_year' && !year.value) year.value = yyyy;
      }
      if (from) {
        from.style.display = p === 'custom' ? '' : 'none';
        if (p === 'custom' && !from.value) from.value = today;
      }
      if (to) {
        to.style.display = p === 'custom' ? '' : 'none';
        if (p === 'custom' && !to.value) to.value = today;
      }
      loadCommandCenter();
    }

    function getCommandPeriodParams() {
      const period = (document.getElementById('ccPeriod') || {}).value || 'today';
      const params = { period };

      if (period === 'specific_day') {
        const day = (document.getElementById('ccDay') || {}).value;
        if (day) { params.day = day; params.from = day; params.to = day; }
      }
      if (period === 'specific_week') {
        const wo = (document.getElementById('ccWeekOf') || {}).value;
        if (wo) params.week_of = wo;
      }
      if (period === 'specific_month') {
        const m = (document.getElementById('ccMonth') || {}).value;
        if (m) params.month = m;
      }
      if (period === 'specific_year') {
        const y = (document.getElementById('ccYear') || {}).value;
        if (y) params.year = y;
      }
      if (period === 'custom') {
        const f = (document.getElementById('ccFrom') || {}).value;
        const t = (document.getElementById('ccTo') || {}).value;
        if (f) params.from = f;
        if (t) params.to = t;
      }
      return params;
    }

    function ccFmtDate(dateStr) {
      if (!dateStr) return '—';
      try {
        const d = new Date(dateStr + 'T00:00:00+05:30');
        return d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric', timeZone: 'Asia/Kolkata' });
      } catch(e) { return dateStr; }
    }

    function loadCommandCenter() {
      const params = getCommandPeriodParams();
      const empId     = (document.getElementById('ccEmpFilter')    || {}).value || '';
      const projectId = (document.getElementById('ccProjectFilter') || {}).value || '';
      const workType  = (document.getElementById('ccWorkType')      || {}).value || '';
      const status    = (document.getElementById('ccStatus')        || {}).value || '';
      const search    = (document.getElementById('ccSearch')        || {}).value || '';

      if (empId)     params.emp_id = empId;
      if (projectId) params.project_id = projectId;
      if (workType)  params.work_type = workType;
      if (status)    params.status = status;
      if (search)    params.search = search;

      const spin = '<i class="fa fa-spinner fa-spin"></i>';
      const g = (id) => document.getElementById(id);
      if (g('ccKpiGrid'))    g('ccKpiGrid').innerHTML    = `<div class="cc-kpi-skeleton">${spin} Loading KPIs…</div>`;
      if (g('ccEmpBody'))    g('ccEmpBody').innerHTML    = `<tr><td colspan="8" class="cc-empty">${spin}</td></tr>`;
      if (g('ccProjectBody'))g('ccProjectBody').innerHTML= `<tr><td colspan="6" class="cc-empty">${spin}</td></tr>`;
      if (g('ccTimeline'))   g('ccTimeline').innerHTML   = `<div class="cc-empty" style="padding:32px">${spin} Loading timeline…</div>`;

      api('work_command_center', params)
        .then(data => {
          if (!data || !data.success) {
            showToast((data && data.message) || 'Failed to load Command Center', 'error');
            return;
          }
          ccRenderPeriodLabel(data.period_label);
          ccRenderKpis(data.kpis);
          ccRenderAdminInsights(data.admin_insights, data.is_manager);
          ccRenderEmpPerformance(data.employee_performance);
          ccRenderProjectRisk(data.project_risk);
          ccRenderTimeline(data.day_timeline);
          ccPopulateFilters(data.members_list, data.visible_projects);
        })
        .catch(e => showToast('Network error: ' + (e && e.message || 'unknown'), 'error'));
    }

    function ccRenderPeriodLabel(label) {
      const el = document.getElementById('ccPeriodLabel');
      if (!el) return;
      el.innerHTML = label
        ? `<span class="cc-period-chip"><i class="fa fa-calendar-day"></i> ${esc(label)}</span>`
        : '';
    }

    function ccRenderKpis(kpis) {
      const grid = document.getElementById('ccKpiGrid');
      if (!grid || !kpis) return;
      const stepPct = kpis.steps_total > 0
        ? Math.round((kpis.steps_done / kpis.steps_total) * 100) : 0;
      const cards = [
        { icon:'fa-pen-to-square', label:'Total Updates',   value: kpis.total_updates,    clr:'accent' },
        { icon:'fa-users',         label:'Active Employees', value: kpis.active_employees, clr:'green'  },
        { icon:'fa-clock',         label:'Work Hrs Logged',  value: kpis.total_hours + 'h', clr:'purple' },
        { icon:'fa-list-check',    label:'Steps Completed',  value: kpis.steps_done + ' / ' + kpis.steps_total + ' (' + stepPct + '%)', clr:'blue' },
        { icon:'fa-chart-line',    label:'Avg Progress',     value: kpis.avg_progress + '%', clr:'teal'  },
        { icon:'fa-ban',           label:'Blocked Updates',  value: kpis.blocked_count,    clr: kpis.blocked_count > 0 ? 'red' : 'green' },
        { icon:'fa-diagram-project',label:'Active Projects', value: kpis.active_projects,  clr:'accent' },
        { icon:'fa-triangle-exclamation', label:'Overdue Projects', value: kpis.overdue_projects, clr: kpis.overdue_projects > 0 ? 'yellow' : 'green' },
      ];
      grid.innerHTML = cards.map(c =>
        `<div class="cc-kpi-card cc-kpi-${escAttr(c.clr)}">
          <div class="cc-kpi-icon"><i class="fa ${escAttr(c.icon)}"></i></div>
          <div class="cc-kpi-val">${esc(String(c.value))}</div>
          <div class="cc-kpi-lbl">${esc(c.label)}</div>
        </div>`
      ).join('');
    }

    function ccRenderAdminInsights(insights, isManager) {
      const el = document.getElementById('ccInsights');
      if (!el) return;
      if (!isManager || !insights || !insights.total_active_employees) {
        el.style.display = 'none'; return;
      }
      el.style.display = '';
      const noUpd    = insights.no_update_today || [];
      const submitted = insights.submitted_today || 0;
      const total     = insights.total_active_employees || 0;
      const pct       = insights.coverage_pct || 0;
      const insDate   = insights.insight_date ? ccFmtDate(insights.insight_date) : 'Today';

      el.innerHTML = `
        <div class="card cc-insights-card">
          <div class="card-title"><i class="fa fa-magnifying-glass-chart"></i> Admin Insights — Update Coverage (${esc(insDate)})</div>
          <div class="cc-insight-row">
            <div class="cc-insight-stat cc-ins-green"><div class="v">${submitted}</div><div class="l">Submitted</div></div>
            <div class="cc-insight-stat cc-ins-red"><div class="v">${noUpd.length}</div><div class="l">Not Submitted</div></div>
            <div class="cc-insight-stat cc-ins-blue"><div class="v">${pct}%</div><div class="l">Coverage Rate</div></div>
            <div class="cc-insight-stat"><div class="v">${total}</div><div class="l">Total Active</div></div>
          </div>
          <div class="cc-progress-bar-wrap">
            <div class="cc-progress-bar" style="width:${pct}%"></div>
          </div>
          ${noUpd.length > 0 ? `
            <div class="cc-no-update-list">
              <div class="cc-no-update-title"><i class="fa fa-circle-exclamation"></i> Employees without update today (${noUpd.length})</div>
              <div class="cc-no-update-chips">
                ${noUpd.map(e => `<span class="cc-emp-chip">${esc(e.full_name)} <span class="chip-eid">${esc(e.employee_id || '')}</span></span>`).join('')}
              </div>
            </div>
          ` : `<div class="cc-all-submitted"><i class="fa fa-circle-check"></i> All employees have submitted today's update!</div>`}
        </div>`;
    }

    function ccRiskBadge(r) {
      const map = { low: '#10b981', medium: '#f59e0b', high: '#ef4444', critical: '#7c3aed' };
      const clr = map[r] || '#8892b0';
      const label = r ? (r.charAt(0).toUpperCase() + r.slice(1)) : '—';
      return `<span class="risk-badge-box" style="background:${clr}20;color:${clr}">${esc(label)}</span>`;
    }

    function ccRenderEmpPerformance(empPerf) {
      const tbody = document.getElementById('ccEmpBody');
      if (!tbody) return;
      if (!empPerf || !empPerf.length) {
        tbody.innerHTML = '<tr><td colspan="8" class="cc-empty">No employee data for this period. Employees need to submit daily updates to appear here.</td></tr>';
        return;
      }
      const scoreEl = (s) => {
        const clr = s >= 80 ? '#10b981' : s >= 60 ? '#f59e0b' : '#ef4444';
        return `<span class="cc-score-badge" style="background:${clr}20;color:${clr}">${s}</span>`;
      };
      const riskIco = { low: '🟢', medium: '🟡', high: '🔴' };

      tbody.innerHTML = empPerf.map(e => {
        const ini = (e.full_name || 'U').trim().charAt(0).toUpperCase();
        const stPct = e.steps_total > 0 ? e.step_completion : null;
        const uid = String(e.user_id || '');
        return `<tr class="cc-click-row" onclick="ccOpenEmployeeDetail('${escAttr(uid)}')" title="Open employee details, projects, tasks, progress and performance">
          <td>
            <div class="emp-name-cell">
              <div class="emp-avatar-sm">${esc(ini)}</div>
              <div>
                <div style="font-weight:700;font-size:.84rem">${esc(e.full_name || '—')}</div>
                <div style="font-size:.72rem;color:var(--text2)">${esc(e.employee_id || '')}${e.department_name ? ' · ' + esc(e.department_name) : ''} · Click to view</div>
              </div>
            </div>
          </td>
          <td><span class="cc-num">${e.update_count}</span>${e.blocked_updates > 0 ? ` <span class="blocked-chip">⚠${e.blocked_updates} blocked</span>` : ''}</td>
          <td><span class="cc-num">${e.work_hours}h</span></td>
          <td><span class="cc-num ${e.attendance_hours > 0 ? '' : 'muted'}">${e.attendance_hours > 0 ? e.attendance_hours + 'h' : '—'}</span></td>
          <td>
            <span class="cc-num">${e.steps_done}/${e.steps_total}</span>
            ${stPct !== null ? `<div class="cc-mini-progress"><div style="width:${stPct}%"></div></div>` : ''}
          </td>
          <td>
            <div class="cc-progress-pill" style="min-width:64px">
              <div class="cc-progress-pill-fill ${e.avg_progress < 30 ? 'fill-red' : e.avg_progress < 60 ? 'fill-yellow' : 'fill-green'}" style="width:${e.avg_progress}%"></div>
              <span>${e.avg_progress}%</span>
            </div>
          </td>
          <td>${scoreEl(e.performance_score)}</td>
          <td><span class="risk-badge" style="color:${e.risk_level === 'high' ? 'var(--red)' : e.risk_level === 'medium' ? 'var(--yellow)' : 'var(--green)'}">${riskIco[e.risk_level] || '⚪'} ${esc((e.risk_level || '').charAt(0).toUpperCase() + (e.risk_level || '').slice(1))}</span></td>
        </tr>`;
      }).join('');
    }

    function ccRenderProjectRisk(projectRisk) {
      const tbody = document.getElementById('ccProjectBody');
      if (!tbody) return;
      if (!projectRisk || !projectRisk.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="cc-empty">No active projects or schedules in scope.</td></tr>';
        return;
      }
      tbody.innerHTML = projectRisk.map(p => `<tr>
        <td>
          <span class="wtype-chip ${p.work_type === 'schedule' ? 'wtype-sch' : 'wtype-prj'}">${p.work_type === 'schedule' ? 'SCH' : 'PRJ'}</span>
          <strong>${esc(p.project_name)}</strong>
          ${p.project_code ? `<div style="font-size:.72rem;color:var(--text2);margin-top:2px">${esc(p.project_code)}</div>` : ''}
        </td>
        <td>
          <div class="cc-progress-pill" style="min-width:72px">
            <div class="cc-progress-pill-fill ${p.progress < 30 ? 'fill-red' : p.progress < 60 ? 'fill-yellow' : 'fill-green'}" style="width:${p.progress}%"></div>
            <span>${p.progress}%</span>
          </div>
        </td>
        <td><span class="cc-num">${p.update_count}</span></td>
        <td>
          ${p.blocked_updates > 0 ? `<span class="blocked-chip">⚠ ${p.blocked_updates} upd</span>` : ''}
          ${p.blocked_tasks > 0 ? `<span class="blocked-chip">⚠ ${p.blocked_tasks} tasks</span>` : ''}
          ${!p.blocked_updates && !p.blocked_tasks ? '<span style="color:var(--green);font-size:.8rem">None</span>' : ''}
        </td>
        <td>${p.is_overdue
          ? `<span class="overdue-chip">⏰ Overdue</span>`
          : (p.due_date ? `<span style="font-size:.8rem">${ccFmtDate(p.due_date)}</span>` : '—')
        }</td>
        <td>${ccRiskBadge(p.risk_level)}</td>
      </tr>`).join('');
    }

    function ccRenderTimeline(dayTimeline) {
      const el = document.getElementById('ccTimeline');
      if (!el) return;
      if (!dayTimeline || !dayTimeline.length) {
        el.innerHTML = '<div class="cc-empty" style="padding:32px">No work updates in this period. Submit daily updates from the Daily Update tab.</div>';
        return;
      }
      el.innerHTML = dayTimeline.map(day => {
        const cnt = day.updates.length;
        return `<div class="cc-timeline-day">
          <div class="cc-tl-day-header">
            <i class="fa fa-calendar-day" style="color:var(--accent)"></i>
            <span class="cc-tl-date">${ccFmtDate(day.date)}</span>
            <span class="cc-tl-count">${cnt} update${cnt !== 1 ? 's' : ''}</span>
          </div>
          <div class="cc-tl-updates">${day.updates.map(u => ccRenderUpdate(u)).join('')}</div>
        </div>`;
      }).join('');
    }

    function ccRenderUpdate(u) {
      const SC = { working:'#3b82f6', testing:'#6366f1', review:'#f59e0b', blocked:'#ef4444', paused:'#94a3b8', completed:'#10b981' };
      const sc = SC[u.work_status] || '#94a3b8';
      const ini = (u.full_name || 'U').trim().charAt(0).toUpperCase();
      const steps = Array.isArray(u.steps) ? u.steps : [];

      return `<div class="cc-tl-update">
        <div class="cc-tl-update-header">
          <div class="emp-avatar-sm">${esc(ini)}</div>
          <div class="cc-tl-meta">
            <div class="cc-tl-empname">${esc(u.full_name || '—')} <span class="chip-eid">${esc(u.employee_id || '')}</span></div>
            <div class="cc-tl-proj">${esc(u.project_name || '—')}${u.task_title ? ' <span style="color:var(--text3)">›</span> ' + esc(u.task_title) : ''}</div>
          </div>
          <div style="margin-left:auto;display:flex;align-items:center;gap:6px;flex-wrap:wrap">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${sc};flex-shrink:0"></span>
            <span style="font-size:.78rem;color:${sc};font-weight:700">${esc(u.work_status || '')}</span>
            <span class="cc-tl-hours">${u.hours_spent || 0}h</span>
            ${u.completion_percent !== null && u.completion_percent !== undefined ? `<span class="cc-tl-pct">${u.completion_percent}%</span>` : ''}
          </div>
        </div>
        ${u.update_title ? `<div class="cc-tl-title">${esc(u.update_title)}</div>` : ''}
        ${u.progress_note ? `<div class="cc-tl-note">${esc(u.progress_note)}</div>` : ''}
        ${u.achievements ? `<div class="cc-tl-achieve"><i class="fa fa-trophy"></i> ${esc(u.achievements)}</div>` : ''}
        ${u.blockers ? `<div class="cc-tl-blocker"><i class="fa fa-circle-exclamation"></i> <strong>Blocker:</strong> ${esc(u.blockers)}</div>` : ''}
        ${u.tomorrow_plan ? `<div class="cc-tl-plan"><i class="fa fa-arrow-right"></i> <strong>Next Plan:</strong> ${esc(u.tomorrow_plan)}</div>` : ''}
        ${steps.length > 0 ? `
          <div class="cc-step-list">
            ${steps.map(s => {
              const done = s.done || s.status === 'completed';
              const title = s.step_title || s.title || '';
              return `<div class="cc-step-item${done ? ' step-done' : ''}">
                <span class="cc-step-chk">${done ? '✓' : '○'}</span>
                <span>${esc(title)}</span>
              </div>`;
            }).join('')}
          </div>` : ''}
      </div>`;
    }


    function empInitials(name) {
      const raw = String(name || '').trim();
      if (!raw) return 'WH';
      const parts = raw.split(/\s+/);
      const first = (parts[0] || 'W').charAt(0).toUpperCase();
      const last = (parts.length > 1 ? parts[parts.length - 1] : parts[0]).charAt(0).toUpperCase();
      return first + last;
    }

    function humanStatusLabel(value) {
      return String(value || '—').replace(/_/g, ' ').replace(/\b\w/g, m => m.toUpperCase());
    }

    function ccCloseEmployeeDetail() {
      const modal = document.getElementById('empDetailModal');
      if (modal) modal.classList.add('hidden');
    }

    function empProgress(progress) {
      const pct = Math.max(0, Math.min(100, Number(progress) || 0));
      return `<div class="emp-progress-line"><span style="width:${pct}%"></span></div>`;
    }

    async function ccOpenEmployeeDetail(userId) {
      if (!userId) return;
      const modal = document.getElementById('empDetailModal');
      const head = document.getElementById('empDetailHead');
      const body = document.getElementById('empDetailBody');
      if (!modal || !head || !body) return;

      modal.classList.remove('hidden');
      head.innerHTML = `
        <div class="emp-detail-avatar"><i class="fa fa-spinner fa-spin"></i></div>
        <div>
          <div class="emp-detail-name">Loading employee profile</div>
          <div class="emp-detail-meta"><span class="emp-detail-chip">Fetching current analytics</span></div>
        </div>`;
      body.innerHTML = '<div class="emp-detail-empty"><i class="fa fa-spinner fa-spin"></i> Loading employee analytics…</div>';

      const params = getCommandPeriodParams();
      params.user_id = userId;

      const data = await api('work_employee_detail', params);
      if (!data || !data.success) {
        body.innerHTML = `<div class="emp-detail-empty">${esc((data && data.message) || 'Unable to load employee details.')}</div>`;
        showToast((data && data.message) || 'Unable to load employee details.', 'error');
        return;
      }
      ccRenderEmployeeDetail(data);
    }

    function ccRenderEmployeeDetail(data) {
      const emp = data.employee || {};
      const member = data.member || {};
      const s = data.summary || {};
      const projects = Array.isArray(data.projects) ? data.projects : [];
      const tasks = Array.isArray(data.tasks) ? data.tasks : [];
      const updates = Array.isArray(data.updates) ? data.updates : [];
      const head = document.getElementById('empDetailHead');
      const body = document.getElementById('empDetailBody');
      if (!head || !body) return;

      const name = emp.full_name || member.member_name || 'Employee';
      const initials = empInitials(name);
      const riskColor = s.risk_level === 'high' ? 'var(--red)' : s.risk_level === 'medium' ? 'var(--yellow)' : 'var(--green)';

      head.innerHTML = `
        <div class="emp-detail-avatar">${esc(initials)}</div>
        <div>
          <div class="emp-detail-name">${esc(name)}</div>
          <div class="emp-detail-meta">
            <span class="emp-detail-chip">${esc(emp.employee_id || member.employee_id || 'No ID')}</span>
            <span class="emp-detail-chip">${esc(emp.department_name || member.department || 'No department')}</span>
            <span class="emp-detail-chip">${esc(emp.role_label || emp.role_name || member.role_title || 'Employee')}</span>
            <span class="emp-detail-chip"><i class="fa fa-calendar-day"></i> ${esc(data.period_label || '')}</span>
          </div>
        </div>`;

      const kpis = [
        ['Updates', s.update_count || 0, 'Daily submissions'],
        ['Work Hours', `${Number(s.work_hours || 0).toFixed(2).replace(/\.00$/, '')}h`, 'Logged work'],
        ['Attendance', `${Number(s.attendance_hours || 0).toFixed(2).replace(/\.00$/, '')}h`, 'Punch hours'],
        ['Progress', `${Number(s.avg_progress || 0)}%`, 'Average completion'],
        ['Steps', `${s.steps_done || 0}/${s.steps_total || 0}`, `${s.step_completion || 0}% complete`],
        ['Projects', s.active_projects || 0, 'Touched in range'],
        ['Score', s.performance_score || 0, 'Composite 0–100'],
        ['Risk', humanStatusLabel(s.risk_level || 'low'), 'Based on blockers', riskColor],
      ];

      const projectHtml = projects.length ? projects.map(p => `
        <div class="emp-project-card">
          <div class="emp-project-top">
            <div>
              <div class="emp-project-title">${esc(p.project_name || 'Untitled')}</div>
              <div class="emp-project-sub">${esc(p.project_code || '')}${p.project_code ? ' · ' : ''}${esc(humanStatusLabel(p.work_type || 'project'))} · ${esc(humanStatusLabel(p.status || ''))}</div>
            </div>
            ${ccRiskBadge((p.blocked_task_count || 0) > 0 ? 'medium' : ((p.status === 'blocked') ? 'high' : 'low'))}
          </div>
          ${empProgress(p.progress || 0)}
          <div class="emp-project-sub" style="margin-top:7px">${Number(p.progress || 0)}% overall · ${p.update_count || 0} updates · ${Number(p.hours_spent || 0).toFixed(2).replace(/\.00$/, '')}h · ${p.completed_task_count || 0}/${p.assigned_task_count || 0} tasks complete</div>
        </div>`).join('') : '<div class="emp-detail-empty">No project or schedule activity in this period.</div>';

      const taskHtml = tasks.length ? tasks.map(t => `
        <div class="emp-task-card">
          <div class="emp-task-top">
            <div>
              <div class="emp-task-title">${esc(t.task_title || 'Untitled task')}</div>
              <div class="emp-task-sub">${esc(t.project_name || 'No project')} · ${esc(humanStatusLabel(t.status || 'todo'))}${t.due_date ? ' · Due ' + ccFmtDate(t.due_date) : ''}</div>
            </div>
            <span class="cc-score-badge" style="background:rgba(59,130,246,.12);color:var(--accent)">${Number(t.progress || 0)}%</span>
          </div>
          ${empProgress(t.progress || 0)}
          <div class="emp-task-sub" style="margin-top:7px">${t.update_count || 0} updates · ${Number(t.period_hours || 0).toFixed(2).replace(/\.00$/, '')}h in selected period · last update ${t.last_update_date ? ccFmtDate(t.last_update_date) : '—'}</div>
        </div>`).join('') : '<div class="emp-detail-empty">No assigned task activity in this period.</div>';

      const updateHtml = updates.length ? updates.map(u => {
        const steps = Array.isArray(u.steps) ? u.steps.slice(0, 8) : [];
        return `<div class="emp-update-card">
          <div class="emp-update-sub">${ccFmtDate(u.update_date)} · ${esc(u.project_name || 'Project')} ${u.task_title ? '› ' + esc(u.task_title) : ''} · ${Number(u.hours_spent || 0).toFixed(2).replace(/\.00$/, '')}h · ${esc(humanStatusLabel(u.work_status || 'working'))}</div>
          <div class="emp-update-title">${esc(u.update_title || u.today_task || 'Daily update')}</div>
          ${u.achievements ? `<div class="emp-update-note"><strong>Output:</strong> ${esc(u.achievements)}</div>` : ''}
          ${u.progress_note ? `<div class="emp-update-note"><strong>Progress:</strong> ${esc(u.progress_note)}</div>` : ''}
          ${u.blockers ? `<div class="emp-update-note" style="color:var(--red)"><strong>Blocker:</strong> ${esc(u.blockers)}</div>` : ''}
          ${steps.length ? `<div class="emp-step-mini">${steps.map(st => {
            const done = st.done || st.status === 'completed' || st.step_status === 'completed';
            return `<div class="${done ? 'done' : ''}"><span>${done ? '✓' : '○'}</span><span>${esc(st.step_title || st.title || '')}</span></div>`;
          }).join('')}</div>` : ''}
        </div>`;
      }).join('') : '<div class="emp-detail-empty">No daily updates submitted in this period.</div>';

      body.innerHTML = `
        <div class="emp-detail-kpis">
          ${kpis.map(k => `<div class="emp-detail-kpi">
            <div class="v" style="${k[3] ? 'color:' + k[3] : ''}">${esc(k[1])}</div>
            <div class="l">${esc(k[0])}</div>
            <div style="font-size:.72rem;color:var(--text3);margin-top:4px">${esc(k[2])}</div>
          </div>`).join('')}
        </div>

        <div class="emp-detail-grid">
          <div class="emp-detail-card">
            <div class="emp-detail-card-title"><i class="fa fa-id-card"></i> Employee Details</div>
            <div class="emp-info-list">
              <div class="emp-info-row"><span>Email</span><span>${esc(emp.email || '—')}</span></div>
              <div class="emp-info-row"><span>Designation</span><span>${esc(emp.designation || member.role_title || '—')}</span></div>
              <div class="emp-info-row"><span>Department</span><span>${esc(emp.department_name || member.department || '—')}</span></div>
              <div class="emp-info-row"><span>Availability</span><span>${esc(humanStatusLabel(member.availability_status || 'available'))}</span></div>
              <div class="emp-info-row"><span>Present Days</span><span>${esc(s.present_days || 0)}</span></div>
              <div class="emp-info-row"><span>WFH / Leave / Absent</span><span>${esc(s.wfh_days || 0)} / ${esc(s.leave_days || 0)} / ${esc(s.absent_days || 0)}</span></div>
              <div class="emp-info-row"><span>Expected Hours</span><span>${Number(s.expected_hours || 0).toFixed(0)}h</span></div>
              <div class="emp-info-row"><span>Review</span><span>${esc(s.approved_updates || 0)} approved · ${esc(s.rejected_updates || 0)} rejected</span></div>
            </div>
          </div>

          <div class="emp-detail-card">
            <div class="emp-detail-card-title"><i class="fa fa-diagram-project"></i> Projects / Schedules Progress</div>
            <div class="emp-detail-list">${projectHtml}</div>
          </div>

          <div class="emp-detail-card">
            <div class="emp-detail-card-title"><i class="fa fa-list-check"></i> Assigned / Active Tasks</div>
            <div class="emp-detail-list">${taskHtml}</div>
          </div>

          <div class="emp-detail-card">
            <div class="emp-detail-card-title"><i class="fa fa-timeline"></i> Daily Work Timeline</div>
            <div class="emp-detail-list">${updateHtml}</div>
          </div>
        </div>`;
    }

    function ccPopulateFilters(membersList, visibleProjects) {
      const empSel = document.getElementById('ccEmpFilter');
      if (empSel && Array.isArray(membersList)) {
        const curr = empSel.value;
        empSel.innerHTML = '<option value="">All Employees</option>' +
          membersList.map(m =>
            `<option value="${escAttr(String(m.user_id))}" ${String(m.user_id) === curr ? 'selected' : ''}>${esc(m.member_name)} (${esc(m.employee_id || '')})</option>`
          ).join('');
      }
      const projSel = document.getElementById('ccProjectFilter');
      if (projSel && Array.isArray(visibleProjects)) {
        const curr = projSel.value;
        projSel.innerHTML = '<option value="">All Projects / Schedules</option>' +
          visibleProjects.map(p =>
            `<option value="${escAttr(String(p.id))}" ${String(p.id) === curr ? 'selected' : ''}>${esc(p.project_name)} (${esc(p.work_type)})</option>`
          ).join('');
      }
    }


    // WorkHive final UX guard: keep layout stable after dynamic table/chart renders.
    (function workHiveFinalUxGuard() {
      function stabilize() {
        document.documentElement.style.overflowX = 'hidden';
        if (document.body) document.body.style.overflowX = 'hidden';
        document.querySelectorAll('.tbl-wrap').forEach(function (wrap) {
          wrap.style.maxWidth = '100%';
        });
      }
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', stabilize);
      } else {
        stabilize();
      }
      window.addEventListener('resize', stabilize, { passive: true });
      var emp = document.getElementById('lEmpId');
      if (emp) emp.addEventListener('keydown', function (e) { if (e.key === 'Enter') doLogin(); });
    })();

  </script>
</body>

</html>
