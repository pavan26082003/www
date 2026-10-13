<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — E-Factor IoE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.4/dist/tailwind.min.css" rel="stylesheet">
    
   <style>
    
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


             :root {
        --bg: #F8F5F2;
        --bg-soft: #F3EEE8;
        --card: #FFFFFF;
        --text-main: #1B1E2B;
        --text-secondary: #6B7280;
        --border: #E5DDD5;
        --graphite: #2B2D42;
        --graphite-secondary: #3A3F58;
        --graphite-fade: #4A4E69;
        --copper: #C08457;
        --copper-soft: #D4A373;
        --metallic: #E6B980;
        --hover-glow: rgba(192,132,87,0.18);
        --radius-lg: 24px;
        --radius-md: 16px;
        --radius-sm: 12px;
        --transition: .28s ease;
    }

        html, body {
            min-height: 100%;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #F8F5F2 0%, #F3EEE8 100%);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* ── Page background gradient — no orb conflicts ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 55% at 15% 10%, rgba(43,45,66,0.10) 0%, transparent 55%),
                radial-gradient(ellipse 55% 45% at 85% 90%, rgba(192,132,87,0.08) 0%, transparent 55%);
            pointer-events: none;
            z-index: 0;
        }

        /* Floating orbs — soft industrial accents */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.36;
            pointer-events: none;
            z-index: 0;
            animation: floatOrb 14s ease-in-out infinite alternate;
        }
        .orb-1 { width: 500px; height: 500px; background: rgba(43,45,66,0.12); top: -160px; left: -140px; animation-delay: 0s; }
        .orb-2 { width: 380px; height: 380px; background: rgba(192,132,87,0.10); bottom: -120px; right: -120px; animation-delay: -5s; }
        .orb-3 { width: 260px; height: 260px; background: rgba(106,116,135,0.10); top: 35%; left: 38%; animation-delay: -9s; }

        @keyframes floatOrb {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, 20px) scale(1.08); }
        }

        /* ── Layout ── */
        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }

        .card-shell {
            display: flex;
            width: 100%;
            max-width: 1060px;
            min-height: 640px;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(43,45,66,0.12);
            border: 1px solid rgba(229,221,213,0.75);
            animation: cardIn 0.65s cubic-bezier(.4,0,.2,1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(28px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0)    scale(1); }
        }

        /* ══════════════════════════════════════
           LEFT PANEL
        ══════════════════════════════════════ */
        .left-panel {
            flex: 1.1;
            background: linear-gradient(180deg, rgba(43,45,66,0.58), rgba(43,45,66,0.32));
            /* background: linear-gradient(165deg, var(--graphite) 0%, var(--graphite-secondary) 55%, var(--graphite-fade) 100%); */
            border-right: 1px solid rgba(229,221,213,0.55);
            display: flex;
            flex-direction: column;
            padding: 40px 36px 32px;
            gap: 24px;
            position: relative;
            overflow: hidden;
            color: var(--graphite) !important; 
        }

        /* subtle dot grid */
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.08) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        /* brand badge */
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 50px;
            padding: 8px 16px 8px 10px;
            width: fit-content;
            backdrop-filter: blur(6px);
        }
        .brand-badge .dot-live {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #D4A373;
            box-shadow: 0 0 8px rgba(212,163,115,0.4);
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100% { opacity: 1; }
            50%      { opacity: 0.4; }
        }
        .brand-badge span { font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.92); letter-spacing: .5px; }

        /* slider */
        .slider-wrap {
            flex: 1;
            position: relative;
            min-height: 340px;
        }

        .slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transform: translateX(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            gap: 14px;
            overflow-y: auto;
        }
        .slide.active {
            opacity: 1;
            transform: translateX(0);
            position: relative;
            pointer-events: auto;
            overflow-y: visible;
        }

        .slide-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.24);
            border-radius: 50px;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 600;
            color: #E6B980;
            letter-spacing: .5px;
            width: fit-content;
        }

        .slide h2 {
            font-size: 22px;
            font-weight: 700;
            line-height: 1.3;
            color: #ffffff;
        }

        .slide p {
            font-size: 13.5px;
            line-height: 1.75;
            color: rgba(255,255,255,0.82);
        }

        .slide-footer {
            margin-top: auto;
            padding-top: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .author-block .name  { font-size: 13px; font-weight: 600; color: #ffffff; }
        .author-block .title { font-size: 12px; color: rgba(255,255,255,0.7); margin-top: 2px; }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(255,255,255,0.28);
            background: rgba(255,255,255,0.12);
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            backdrop-filter: blur(4px);
            transition: background var(--transition), transform var(--transition);
        }
        .btn-outline:hover {
            background: rgba(255,255,255,0.22);
            transform: translateY(-1px);
        }

        /* download slide */
        .download-slide {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .qr-block {
            display: flex;
            align-items: center;
            gap: 20px;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.35);
            border-radius: var(--radius-md);
            padding: 20px 24px;
            backdrop-filter: blur(6px);
        }
        .qr-block img {
            width: 90px;
            height: 90px;
            border-radius: 12px;
            background: #ffffff;
            padding: 8px;
            object-fit: contain;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            flex-shrink: 0;
        }
        .qr-text h3 { font-size: 16px; font-weight: 700; margin-bottom: 6px; color: #fff; }
        .qr-text p  { font-size: 13px; color: rgba(255,255,255,0.8); line-height: 1.6; }

        .slider-dots {
            display: flex;
            gap: 6px;
            align-items: center;
        }
        .dot {
            height: 4px;
            border-radius: 4px;
            background: rgba(255,255,255,0.35);
            transition: width var(--transition), background var(--transition);
            cursor: pointer;
            width: 18px;
        }
        .dot.active { width: 32px; background: #ffffff; }

        /* ══════════════════════════════════════
           RIGHT PANEL
        ══════════════════════════════════════ */
        .right-panel {
            flex: 0.9;
            background:
                linear-gradient(180deg, rgba(43,45,66,0.58), rgba(43,45,66,0.32)),
                url('<?= base_url('files/signin-images/sign-in.png') ?>') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 36px;
            position: relative;
            overflow: hidden;
        }

        /* vignette overlay — behind form card */
        .right-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at center, transparent 35%, rgba(43,45,66,0.42) 100%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── Premium form card ── */
        .form-card {
            width: 100%;
            max-width: 360px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: relative;
            z-index: 1;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 20px 40px rgba(43,45,66,0.12);
            box-sizing: border-box;
        }

        /* logo */
        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-wrap img {
            height: 52px;
            object-fit: contain;
            background: #F8F5F2;
            border-radius: 12px;
            padding: 8px 14px;
            border: 1px solid #E5DDD5;
            box-shadow: 0 10px 26px rgba(43,45,66,0.08);
        }

        /* heading */
        .form-heading h1 {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -.5px;
            line-height: 1.2;
            color: var(--text-main);
        }
        .form-heading p {
            margin-top: 6px;
            font-size: 13.5px;
            color: var(--text-secondary);
        }

        /* ── Role icons (decorative) ── */
        .role-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
        .role {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 7px;
            padding: 12px 6px;
            border-radius: var(--radius-md);
            border: 1px solid #E5DDD5;
            background: #F8F5F2;
        }
        .role .icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            background: #ffffff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 6px 18px rgba(43,45,66,0.08);
            border: 1px solid #E5DDD5;
            flex-shrink: 0;
        }
        .role .icon img {
            width: 26px; height: 26px; object-fit: contain;
        }
        .role .label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-main);
        }

        /* error banner */
        .error-banner {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(239,68,68,0.14);
            border: 1px solid rgba(239,68,68,0.28);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            font-size: 13px;
            color: #b91c1c;
            animation: shake 0.4s ease;
        }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            25%      { transform: translateX(-6px); }
            75%      { transform: translateX(6px); }
        }

        /* fields */
        .field-group { display: flex; flex-direction: column; gap: 14px; }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .field label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-main);
            letter-spacing: .3px;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap .i-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(43,45,66,0.55);
            pointer-events: none;
            display: flex;
        }
        .input-wrap input,
        .input-wrap input[type="email"],
        .input-wrap input[type="password"],
        .input-wrap input[type="text"] {
            width: 100%;
            padding: 12px 44px 12px 42px;
            background: #ffffff;
            border: 1px solid #E5DDD5;
            border-radius: var(--radius-sm);
            color: var(--text-main);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color var(--transition), background var(--transition), box-shadow var(--transition);
        }
        .input-wrap input::placeholder { color: #9CA3AF; }
        .input-wrap input:focus {
            border-color: var(--copper);
            background: #ffffff;
            box-shadow: 0 0 0 4px var(--hover-glow);
        }
        /* override browser autofill yellow/blue */
        .input-wrap input:-webkit-autofill,
        .input-wrap input:-webkit-autofill:hover,
        .input-wrap input:-webkit-autofill:focus {
            -webkit-text-fill-color: var(--text-main);
            -webkit-box-shadow: 0 0 0 1000px #ffffff inset;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* password toggle */
        .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: rgba(43,45,66,0.7);
            display: flex;
            padding: 4px;
            transition: color var(--transition);
        }
        .toggle-pw:hover { color: var(--copper); }

        /* forgot */
        .forgot-row {
            display: flex;
            justify-content: flex-end;
            margin-top: -6px;
        }
        .forgot-row a {
            font-size: 12.5px;
            font-weight: 500;
            color: var(--copper);
            text-decoration: none;
            transition: color var(--transition);
        }
        .forgot-row a:hover { color: var(--copper-soft); text-decoration: underline; }

        /* submit */
        .btn-primary {
            width: 100%;
            padding: 13px;
            border-radius: var(--radius-sm);
            border: none;
            background: linear-gradient(135deg, var(--copper) 0%, var(--copper-soft) 58%, var(--metallic) 100%);
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            letter-spacing: .3px;
            position: relative;
            overflow: hidden;
            transition: transform var(--transition), box-shadow var(--transition);
            box-shadow: 0 12px 22px rgba(192,132,87,0.24), 0 1px 0 rgba(255,255,255,0.18) inset;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.18) 0%, transparent 60%);
            pointer-events: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(192,132,87,0.28);
        }
        .btn-primary:active { transform: translateY(0); }

        /* loading state */
        .btn-primary:disabled {
            opacity: 0.85;
            cursor: not-allowed;
            transform: none;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ════════════════════════════════════
           RESPONSIVE
        ════════════════════════════════════ */

        /* Tablet landscape (≤ 1024px) */
        @media (max-width: 1024px) {
            .card-shell  { max-width: 920px; }
            .left-panel  { padding: 36px 30px 28px; }
            .right-panel { padding: 36px 28px; }
        }

        /* Tablet portrait (≤ 900px) — stack vertically */
        @media (max-width: 900px) {
            .page-wrapper {
                align-items: flex-start;
                padding: 20px 16px 36px;
            }
            .card-shell {
                flex-direction: column;
                max-width: 580px;
                min-height: auto;
                margin: 0 auto;
                width: 100%;
            }

            /* form on top, info panel below */
            .right-panel { order: 1; }
            .left-panel  { order: 2; }

            .right-panel {
                padding: 32px 28px;
                min-height: 480px;
            }
            .form-card {
                padding: 28px 24px;
                max-width: 100%;
            }

            .left-panel {
                border-right: none;
                border-top: 1px solid rgba(255,255,255,0.2);
                padding: 32px 28px 28px;
            }

            /* on tablet, slider switches to natural flow */
            .slider-wrap {
                min-height: auto;
                flex: none;
            }
            .slide {
                position: relative;
                inset: auto;
                transform: none !important;
                opacity: 0;
                max-height: 0;
                overflow: hidden;
                transition: opacity 0.5s ease, max-height 0.6s ease;
                pointer-events: none;
                gap: 12px;
            }
            .slide.active {
                opacity: 1;
                max-height: 1200px;
                overflow: visible;
                pointer-events: auto;
            }
        }

        /* Mobile (≤ 640px) */
        @media (max-width: 640px) {
            .page-wrapper { padding: 12px 10px 28px; }

            .card-shell { border-radius: var(--radius-md); }

            .right-panel { padding: 24px 16px; min-height: auto; }
            .form-card {
                padding: 22px 16px;
                gap: 16px;
                border-radius: var(--radius-md);
            }

            .left-panel  { padding: 24px 18px 20px; gap: 16px; }

            .brand-badge { padding: 6px 14px 6px 8px; }
            .brand-badge span { font-size: 11px; }

            .slide h2 { font-size: 18px; }
            .slide p  { font-size: 13px; line-height: 1.7; }

            .form-heading h1 { font-size: 22px; }
            .form-heading p  { font-size: 13px; }

            .qr-block {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                padding: 16px;
            }
            .qr-block img { width: 80px; height: 80px; }

            .btn-primary { font-size: 14px; padding: 12px; }

            .logo-wrap img { height: 46px; }
        }

        /* Small phones (≤ 400px) */
        @media (max-width: 400px) {
            .page-wrapper { padding: 8px 8px 20px; }

            .card-shell { border-radius: 14px; }

            .right-panel { padding: 18px 12px; }
            .form-card   { padding: 18px 14px; gap: 14px; border-radius: 12px; }

            .logo-wrap img { height: 40px; padding: 6px 10px; }
            .form-heading h1 { font-size: 19px; }
            .form-heading p  { font-size: 12px; }

            .left-panel  { padding: 20px 14px 18px; gap: 14px; }
            .brand-badge span { font-size: 10px; }
            .slide h2 { font-size: 16px; }
            .slide p  { font-size: 12px; line-height: 1.65; }

            .btn-primary { font-size: 13px; padding: 11px; }
        }

        /* Landscape phones — ensure scrollability */
        @media (max-height: 600px) and (orientation: landscape) {
            .page-wrapper { align-items: flex-start; padding: 12px 16px 20px; }
            .card-shell   { min-height: auto; }
        }

        /* ── Full-page loading overlay ── */
        #page-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 20px;
            background: rgba(10, 20, 60, 0.72);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            animation: loaderFadeIn 0.25s ease both;
        }
        #page-loader.active { display: flex; }

        @keyframes loaderFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .loader-ring {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.15);
            border-top-color: #ffffff;
            animation: spin 0.75s linear infinite;
        }

        .loader-text {
            font-size: 14px;
            font-weight: 600;
            color: rgba(255,255,255,0.85);
            letter-spacing: .5px;
        }

        /* btn loading state */
        .btn-primary.loading {
            opacity: 0.75;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">

<!-- Full-page loading overlay -->
<div id="page-loader">
    <div class="loader-ring"></div>
    <div class="loader-text">Signing in…</div>
</div>

<!-- Floating orbs -->
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<div class="page-wrapper flex items-center justify-center">
    <div class="card-shell rounded-[32px] shadow-2xl">

        <!-- ══════════ LEFT PANEL ══════════ -->
        <div class="left-panel">

            <!-- Brand badge -->
            <div class="brand-badge">
                <span class="dot-live"></span>
                <span>E-FACTOR IoE PLATFORM</span>
            </div>

            <!-- Slider -->
            <div class="slider-wrap">

                <!-- Slide 1 — Welcome message -->
                <div class="slide active">
                    <span class="slide-tag">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><circle cx="5" cy="5" r="5" fill="#60a5fa"/></svg>
                        WELCOME NOTE
                    </span>
                    <h2>Dear Valued User,</h2>
                    <p>We are E-Factor IoE, an emerging Indian startup dedicated to delivering a comprehensive Internet of Everything (IoE) ecosystem. In a global market where only a select few offer holistic solutions, we take pride in our advanced technology and innovative approach, positioning us at the forefront of the industry.</p>
                    <p>Our journey is driven by a passionate and close-knit team, operating without venture capital backing. Instead, we are supported by the unwavering belief and contributions of our family and friends. This foundation empowers us to innovate with agility and maintain a user-centric focus.</p>
                    <p>We acknowledge that, during this testing phase, you may encounter certain issues as we refine our offerings. Your experience is paramount to us, and we are devoted to enhancing our services to meet your expectations.</p>
                    <p>Your support and feedback are invaluable to our growth and improvement. We encourage you to connect with us through the in-app chat, email in dashboard to share your insights and suggestions. Collaboratively, we can refine our solutions and work towards establishing E-Factor IoE as a global brand.</p>
                    <p>Thank you for your understanding and for being an integral part of our journey.</p>
                    <div class="slide-footer">
                        <div class="author-block">
                            <div class="name">The E-Factor IoE Team</div>
                            <div class="title">Warm regards</div>
                        </div>
                        <a class="btn-outline" href="https://efactor.tech/efactor/About.html" target="_blank">
                            Know More
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Slide 2 — Download / QR -->
                <div class="slide download-slide">
                    <span class="slide-tag">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><circle cx="5" cy="5" r="5" fill="#34d399"/></svg>
                        MOBILE APP
                    </span>
                    <h2>Create Your IOE Product</h2>
                    <p>Transform your ideas into reality with our innovative IOE solutions. Join us on the journey of creating smart and connected devices for the future.</p>
                    <div class="qr-block">
                        <img src="<?= base_url('files/signin-images/tqaier_QR.png') ?>" alt="QR Code">
                        <div class="qr-text">
                            <h3>Scan to download</h3>
                            <p><strong>Android only</strong></p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Dots -->
            <div class="slider-dots">
                <span class="dot active" data-index="0"></span>
                <span class="dot" data-index="1"></span>
            </div>

        </div><!-- /left-panel -->

        <!-- ══════════ RIGHT PANEL ══════════ -->
        <div class="right-panel">
            <div class="form-card">

                <!-- Logo -->
                <div class="logo-wrap">
                    <img src="<?= base_url('files/signin-images/Logo.4.1.png') ?>" alt="E-Factor Logo">
                </div>

                <?php if (isset($form_type) && $form_type == "request_reset_password"): ?>
                    <?php echo view("signin/reset_password_form"); ?>
                <?php elseif (isset($form_type) && $form_type == "new_password"): ?>
                    <?php echo view('signin/new_password_form'); ?>
                <?php else: ?>

                <!-- Heading -->
                <div class="form-heading">
                    <h1>Welcome Back!</h1>
                    <p>Sign-in to access your dashboard.</p>
                </div>

                <?php echo form_open("signin/authenticate", array("id" => "signin-form", "class" => "general-form", "role" => "form")); ?>

                <?php
                // Preserve Grid / Grid-chat return path across login (SSO webview handoff).
                $signin_redirect = '';
                if (isset($redirect) && is_string($redirect)) {
                    $signin_redirect = trim($redirect);
                } elseif (isset($_REQUEST['redirect'])) {
                    $signin_redirect = trim((string) $_REQUEST['redirect']);
                }
                $signin_grid_sso = trim((string) ($_REQUEST['grid_sso'] ?? ''));
                ?>
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($signin_redirect, ENT_QUOTES, 'UTF-8'); ?>" />
                <?php if ($signin_grid_sso !== '') { ?>
                <input type="hidden" name="grid_sso" value="<?php echo htmlspecialchars($signin_grid_sso, ENT_QUOTES, 'UTF-8'); ?>" />
                <?php } ?>

                <!-- Role icons (decorative) -->
                <div class="role-selector">
                    <div class="role">
                        <span class="icon"><img src="https://efactor.tech/files/home/client.png" alt="Brand"></span>
                        <span class="label">Brand</span>
                    </div>
                    <div class="role">
                        <span class="icon"><img src="https://efactor.tech/files/home/devloper.png" alt="Developer"></span>
                        <span class="label">Developer</span>
                    </div>
                    <div class="role">
                        <span class="icon"><img src="https://efactor.tech/files/home/manufacturer.png" alt="Manufacturer"></span>
                        <span class="label">Manufacturer</span>
                    </div>
                </div>

                <!-- Flash errors -->
                <?php
                $session = \Config\Services::session();
                $signin_validation_errors = $session->getFlashdata("signin_validation_errors");
                if ($signin_validation_errors && is_array($signin_validation_errors)) { ?>
                    <div class="error-banner">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <div><?php foreach ($signin_validation_errors as $e) { echo $e . '<br>'; } ?></div>
                    </div>
                <?php } ?>

                <!-- Fields -->
                <div class="field-group">
                    <div class="field">
                        <label for="email"><?php echo app_lang('email'); ?></label>
                        <div class="input-wrap">
                            <span class="i-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </span>
                            <?php echo form_input(array(
                                "id"                 => "email",
                                "name"               => "email",
                                "class"              => "form-control",
                                "placeholder"        => app_lang('email'),
                                "autofocus"          => true,
                                "data-rule-required" => true,
                                "data-msg-required"  => app_lang("field_required"),
                                "data-rule-email"    => true,
                                "data-msg-email"     => app_lang("enter_valid_email")
                            )); ?>
                        </div>
                    </div>

                    <div class="field">
                        <label for="password"><?php echo app_lang('password'); ?></label>
                        <div class="input-wrap">
                            <span class="i-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <?php echo form_password(array(
                                "id"                 => "password",
                                "name"               => "password",
                                "class"              => "form-control",
                                "placeholder"        => app_lang('password'),
                                "data-rule-required" => true,
                                "data-msg-required"  => app_lang("field_required")
                            )); ?>
                            <button type="button" class="toggle-pw" id="togglePassword" aria-label="Toggle password">
                                <svg id="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg id="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="forgot-row">
                        <a href="<?php echo site_url('signin/request_reset_password'); ?>"><?php echo app_lang('forgot_password'); ?></a>
                    </div>
                </div>

                <?php echo view("signin/re_captcha"); ?>

                <button type="submit" id="signin-btn" class="btn-primary">
                    <?php echo app_lang('signin'); ?>
                </button>

                <?php echo form_close(); ?>

                <?php endif; ?>

            </div>
        </div><!-- /right-panel -->

    </div><!-- /card-shell -->
</div><!-- /page-wrapper -->

<script>
    /* ── Slider ── */
    const slides = document.querySelectorAll('.slide');
    const dots   = document.querySelectorAll('.dot');
    let current  = 0;
    let timer;

    function showSlide(idx) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        current = idx;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
    }

    function startAuto() {
        timer = setInterval(() => showSlide((current + 1) % slides.length), 5500);
    }

    startAuto();

    dots.forEach(d => d.addEventListener('click', () => {
        clearInterval(timer);
        showSlide(+d.dataset.index);
        startAuto();
    }));

    document.querySelector('.slider-wrap').addEventListener('mouseenter', () => clearInterval(timer));
    document.querySelector('.slider-wrap').addEventListener('mouseleave', startAuto);

    /* ── Form submit — show loader ── */
    document.getElementById('signin-form').addEventListener('submit', function() {
        showLoader();
    });

    /* ── Loader ── */
    function showLoader() {
        document.getElementById('page-loader').classList.add('active');
        var btn = document.getElementById('signin-btn');
        if (btn) { btn.disabled = true; btn.classList.add('loading'); }
    }

    /* ── Password toggle ── */
    const pwInput   = document.getElementById('password');
    const togglePw  = document.getElementById('togglePassword');
    const eyeOpen   = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');
    togglePw.addEventListener('click', () => {
        const show = pwInput.type === 'password';
        pwInput.type            = show ? 'text' : 'password';
        eyeOpen.style.display   = show ? 'none' : '';
        eyeClosed.style.display = show ? ''     : 'none';
    });

</script>
</body>
</html>
