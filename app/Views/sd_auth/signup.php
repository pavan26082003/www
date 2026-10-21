<?= $this->extend('layouts/sd_auth') ?>
<?= $this->section('content') ?>
<?php
$signupApi = [
    'createUser' => site_url('sd-signup/register'),
    'verifyUser' => site_url('sd-signup/verify-remote'),
    'loginUser' => site_url('sd-signup/login-remote'),
    'session' => site_url('sd-signup/session'),
    'done' => site_url('sd-dashboard'),
    'countryCode' => '+91',
    'userType' => 'User',
];
?>

<style>
:root{--ef-primary:#202940;--ef-dark:#1F150C;--ef-panel:rgba(18,24,38,.58);--ef-line:rgba(255,255,255,.18);--ef-text:#F8FAFC;--ef-muted:rgba(248,250,252,.72);--ef-danger:#FCA5A5;--ef-good:#16A34A}
*{box-sizing:border-box}
html{min-width:320px;-webkit-text-size-adjust:100%}
body{margin:0;min-width:320px;overflow:hidden;color:var(--ef-text);font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:#E8EDF2}
button,input{font:inherit}
.ef-page{min-height:100vh;display:grid;place-items:center;padding:24px;background:linear-gradient(135deg,#E8EDF2,#F5F5F5)}
.ef-shell{position:relative;width:min(100%,390px);height:min(820px,calc(100vh - 48px));min-height:620px;display:flex;flex-direction:column;align-items:stretch;justify-content:flex-start;padding:74px 24px 24px;overflow:hidden;border-radius:42px;background:linear-gradient(160deg,rgba(32,41,64,.72),rgba(31,21,12,.78)),url("<?= base_url('assets/images/efactor-ecosystem.jpg') ?>") center bottom/cover no-repeat;box-shadow:0 32px 80px rgba(32,41,64,.24)}
.ef-shell::before{content:"";position:absolute;inset:8px;border-radius:34px;background:linear-gradient(to bottom,rgba(0,0,0,.10),rgba(0,0,0,.10) 35%,rgba(0,0,0,.36));pointer-events:none}
.ef-shell::after{content:"";position:absolute;z-index:5;top:8px;left:50%;width:112px;height:27px;transform:translateX(-50%);border-radius:0 0 18px 18px;background:#101813}
.ef-back{position:absolute;z-index:8;top:30px;left:22px;width:42px;height:42px;display:grid;place-items:center;border:1px solid var(--ef-line);border-radius:50%;color:#fff;background:rgba(255,255,255,.12);backdrop-filter:blur(16px);text-decoration:none;box-shadow:0 12px 30px rgba(0,0,0,.18)}
.ef-back svg{width:18px;height:18px}
.ef-logo{display:flex;align-items:center;justify-content:flex-start;width:148px;height:48px;margin:0 0 14px;padding:6px 10px;border-radius:10px;background:#fff;box-shadow:0 8px 18px rgba(0,0,0,.16)}
.ef-logo img{width:100%;height:100%;object-fit:contain;display:block}
.ef-scroll{position:relative;z-index:3;flex:1 1 auto;min-height:0;width:100%;overflow-y:auto;overflow-x:hidden;padding:4px 0 2px;scrollbar-width:none;-webkit-overflow-scrolling:touch;overscroll-behavior:contain}
.ef-scroll::-webkit-scrollbar{width:0;height:0}
.ef-card{width:100%;padding:20px 18px 18px;border:1px solid var(--ef-line);border-radius:26px;background:var(--ef-panel);box-shadow:0 24px 70px rgba(0,0,0,.32),inset 0 1px 0 rgba(255,255,255,.14);backdrop-filter:blur(22px);animation:efRise .45s ease both}
.ef-heading-wrap{text-align:left;margin-bottom:16px}
.ef-title{margin:0;color:#fff;font-size:1.75rem;line-height:1.08;font-weight:800;letter-spacing:0}
.ef-subtitle{margin:8px 0 0;color:var(--ef-muted);font-size:.76rem;line-height:1.45}
.ef-field{margin-bottom:10px}
.ef-label{display:flex;align-items:center;justify-content:space-between;gap:8px;margin:0 2px 5px;color:rgba(248,250,252,.86);font-size:.68rem;font-weight:750}
.ef-req{display:inline-flex;align-items:center;padding:2px 7px;border-radius:999px;color:#FEE2E2;background:rgba(127,29,29,.38);border:1px solid rgba(252,165,165,.45);font-size:.58rem;font-weight:800;letter-spacing:.02em;white-space:nowrap}
.ef-input{min-height:46px;display:flex;align-items:center;gap:10px;padding:0 13px;border:1px solid rgba(255,255,255,.16);border-radius:12px;background:rgba(255,255,255,.10);box-shadow:inset 0 1px 0 rgba(255,255,255,.08);transition:border-color .2s ease,background .2s ease,box-shadow .2s ease}
.ef-input:focus-within{border-color:rgba(232,237,242,.56);background:rgba(255,255,255,.16);box-shadow:0 0 0 4px rgba(232,237,242,.10)}
.ef-input.has-error{border-color:rgba(252,165,165,.7);background:rgba(127,29,29,.20)}
.ef-icon{width:18px;height:18px;flex:0 0 18px;color:#E8EDF2}
.ef-control{flex:1;width:100%;min-width:0;border:0;outline:0;padding:12px 0;color:#fff;background:transparent;font-size:.8rem}
.ef-control::placeholder{color:rgba(248,250,252,.62)}
.ef-toggle{width:32px;height:32px;display:grid;place-items:center;padding:0;border:0;border-radius:50%;color:#E8EDF2;background:transparent;cursor:pointer}
.ef-toggle svg{width:17px;height:17px}
.ef-error{min-height:13px;margin-top:3px;color:var(--ef-danger);font-size:.65rem}
.ef-otp{display:none}
.ef-otp.is-visible{display:block}
.ef-otp{margin:0 0 10px}
.ef-otp-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:7px;color:var(--ef-muted);font-size:.64rem;font-weight:800}
.ef-otp-boxes{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:8px}
.ef-otp-box{width:100%;height:46px;border:1px solid rgba(255,255,255,.22);border-radius:10px;background:rgba(255,255,255,.12);text-align:center;color:#fff;font-size:18px;font-weight:700;outline:0}
.ef-otp-box:focus{border-color:rgba(232,237,242,.68);box-shadow:0 0 0 3px rgba(232,237,242,.10)}
.ef-otp-box:disabled{opacity:.72}
.ef-otp-actions{display:flex;gap:8px;margin-top:10px}
.ef-send-otp{width:100%;display:inline-flex;min-height:44px;margin:2px 0 8px;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.55);border-radius:14px;color:#fff;background:rgba(255,255,255,.18);padding:8px 12px;font-size:.82rem;font-weight:850;cursor:pointer;touch-action:manipulation}
.ef-send-otp:disabled{opacity:.55;cursor:default}
.ef-badges{display:flex;flex-direction:column;align-items:flex-start;gap:6px;min-height:0;margin:0 0 8px}
.ef-badge{display:none;align-items:center;max-width:100%;padding:5px 10px;border-radius:999px;font-size:.68rem;font-weight:800;line-height:1.35}
.ef-badge.is-visible{display:inline-flex}
.ef-badge-progress{color:#E0F2FE;background:rgba(14,116,144,.38);border:1px solid rgba(125,211,252,.45)}
.ef-badge-success{color:#DCFCE7;background:rgba(22,101,52,.34);border:1px solid rgba(134,239,172,.45)}
.ef-badge-danger{color:#FEE2E2;background:rgba(127,29,29,.38);border:1px solid rgba(252,165,165,.45)}
.ef-verify-otp.is-verified{opacity:1;border-color:rgba(134,239,172,.45);color:#DCFCE7;background:rgba(22,101,52,.25);cursor:default}
.ef-retry{display:none;width:100%;margin-top:8px}
.ef-retry.is-visible{display:inline-flex}
.ef-meter{height:4px;margin-top:5px;overflow:hidden;border-radius:99px;background:rgba(255,255,255,.16)}
.ef-meter span{display:block;width:0;height:100%;border-radius:inherit;transition:.25s ease}
.ef-meter-row{display:flex;justify-content:space-between;gap:9px;margin-top:4px;color:var(--ef-muted);font-size:.56rem}
.ef-terms{display:grid;grid-template-columns:18px 1fr;gap:8px;align-items:start;margin:4px 0 12px;color:rgba(248,250,252,.84);font-size:.75rem;line-height:1.45}
.ef-terms input{width:15px;height:15px;margin:1px 0 0;accent-color:#FFFFFF;color-scheme:dark}
.ef-terms .ef-req{margin-left:6px;vertical-align:middle}
.ef-link{color:#fff;font-weight:850;text-decoration:none}
.ef-link:hover{text-decoration:underline}
.ef-submit{width:100%;min-height:50px;display:flex;align-items:center;justify-content:center;gap:8px;border:1px solid rgba(255,255,255,.2);border-radius:14px;color:#fff;background:#243044;box-shadow:0 16px 34px rgba(0,0,0,.24);cursor:pointer;font-size:.84rem;font-weight:800;touch-action:manipulation}
.ef-submit:disabled{opacity:.55;cursor:default}
.ef-divider{display:flex;align-items:center;gap:10px;margin:13px 0 10px;color:var(--ef-muted);font-size:.62rem}
.ef-divider::before,.ef-divider::after{content:"";flex:1;height:1px;background:rgba(255,255,255,.18)}
.ef-socials{display:flex;align-items:center;justify-content:center;gap:14px;margin-bottom:12px}
.ef-social{width:40px;height:40px;display:grid;place-items:center;border:1px solid rgba(255,255,255,.18);border-radius:14px;color:#fff;background:rgba(255,255,255,.10);box-shadow:inset 0 1px 0 rgba(255,255,255,.08);cursor:pointer;text-decoration:none}
.ef-social svg{width:18px;height:18px}
.ef-footer{margin-top:14px;color:var(--ef-muted);text-align:center;font-size:.75rem}
.ef-otp-status{min-height:16px;margin-top:6px;color:#86EFAC;font-size:.72rem}
.ef-alert{display:none;gap:8px;margin-bottom:10px;padding:9px 10px;border-radius:12px;font-size:.7rem;line-height:1.4}
.ef-alert.is-visible{display:flex}
.ef-alert-danger{color:#FEE2E2;border:1px solid rgba(252,165,165,.35);background:rgba(127,29,29,.24)}
@keyframes efRise{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
@media(max-width:430px){
  html,body{position:fixed;inset:0;width:100%;height:100%;overflow:hidden;background:#101827}
  .ef-page{position:fixed;inset:0;display:block;height:100%;min-height:0;padding:0;overflow:hidden}
  .ef-shell{position:absolute;inset:0;width:100%;height:100%;min-height:0;display:flex;flex-direction:column;align-items:stretch;justify-content:flex-start;padding:72px 16px calc(16px + env(safe-area-inset-bottom));overflow:hidden;border-radius:0;box-shadow:none}
  .ef-shell::before{inset:0;border-radius:0}
  .ef-shell::after{display:none}
  .ef-back{top:18px;left:16px}
  .ef-scroll{flex:1 1 auto;min-height:0;max-height:none;width:100%}
  .ef-card{padding:22px 16px 18px;border-radius:26px;animation:none}
  .ef-title{font-size:1.78rem}
  .ef-control,.ef-otp-box{font-size:16px}
  .ef-otp-boxes{gap:6px}
}
@media(prefers-reduced-motion:reduce){.ef-card{animation:none}}
</style>


<div class="ef-page">
  <div class="ef-shell">
    <a class="ef-back" href="<?= site_url('welcome') ?>" aria-label="Back to welcome">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="m12 19-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </a>
    <div class="ef-scroll" id="signupScroll">
      <main class="ef-card" aria-labelledby="signupTitle">
        <div class="ef-logo"><img src="<?= base_url('assets/images/efactor-logo.jpg') ?>" alt="Efactor"></div>
        <div class="ef-heading-wrap">
          <h1 class="ef-title" id="signupTitle">Get Started</h1>
          <p class="ef-subtitle">Create your secure Efactor account</p>
        </div>
        <div class="ef-alert ef-alert-danger" id="formAlert" role="alert"></div>
        <form id="signupForm" method="post" novalidate>
          <div class="ef-field" id="emailField">
            <div class="ef-label"><span>Email</span><span class="ef-req">Required</span></div>
            <div class="ef-input" id="emailWrap">
              <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <input class="ef-control" id="email" name="email" type="email" value="<?= old('email') ?>" autocomplete="email" inputmode="email" placeholder="Email address" maxlength="191" aria-label="Email address" required>
            </div>
            <div class="ef-error" id="emailError"></div>
          </div>

          <div class="ef-field" id="phoneField">
            <div class="ef-label"><span>Phone</span><span class="ef-req">Required</span></div>
            <div class="ef-input" id="phoneWrap">
              <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect width="14" height="20" x="5" y="2" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M12 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              <input class="ef-control" id="phone" name="phone" type="tel" value="<?= old('phone') ?>" autocomplete="tel" inputmode="numeric" placeholder="Enter phone number" maxlength="10" aria-label="Phone number" required>
            </div>
            <div class="ef-error" id="phoneError"></div>
          </div>

          <div class="ef-field">
            <div class="ef-label"><span>Password</span><span class="ef-req">Required</span></div>
            <div class="ef-input" id="passwordWrap">
              <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
              <input class="ef-control" id="password" name="password" type="password" autocomplete="new-password" maxlength="72" placeholder="Password" aria-label="Password" required>
              <button class="ef-toggle" type="button" data-password-toggle="password" aria-label="Show password">
                <svg data-eye viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
              </button>
            </div>
            <div class="ef-meter"><span id="passwordMeterBar"></span></div>
            <div class="ef-meter-row"><span id="passwordStrength">Password strength</span><span>Upper / lower / number / special</span></div>
            <div class="ef-error" id="passwordError"></div>
          </div>

          <div class="ef-field">
            <div class="ef-label"><span>Confirm password</span><span class="ef-req">Required</span></div>
            <div class="ef-input" id="confirmWrap">
              <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" stroke="currentColor" stroke-width="1.8"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <input class="ef-control" id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" maxlength="72" placeholder="Confirm password" aria-label="Confirm password" required>
              <button class="ef-toggle" type="button" data-password-toggle="confirm_password" aria-label="Show confirm password">
                <svg data-eye viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
              </button>
            </div>
            <div class="ef-error" id="confirmPasswordError"></div>
          </div>

          <button class="ef-send-otp" type="button" id="sendOtpButton">Send OTP</button>
          <div class="ef-badges" id="flowBadges" aria-live="polite"></div>

          <div class="ef-otp" id="otpSection" aria-label="Enter six digit OTP">
            <div class="ef-otp-head"><span>Enter OTP</span><span class="ef-req">Required</span></div>
            <div class="ef-otp-boxes" id="otpBoxes">
              <input class="ef-otp-box" id="signup_otp_digit_1" inputmode="numeric" maxlength="1" autocomplete="one-time-code" aria-label="OTP digit 1">
              <input class="ef-otp-box" id="signup_otp_digit_2" inputmode="numeric" maxlength="1" aria-label="OTP digit 2">
              <input class="ef-otp-box" id="signup_otp_digit_3" inputmode="numeric" maxlength="1" aria-label="OTP digit 3">
              <input class="ef-otp-box" id="signup_otp_digit_4" inputmode="numeric" maxlength="1" aria-label="OTP digit 4">
              <input class="ef-otp-box" id="signup_otp_digit_5" inputmode="numeric" maxlength="1" aria-label="OTP digit 5">
              <input class="ef-otp-box" id="signup_otp_digit_6" inputmode="numeric" maxlength="1" aria-label="OTP digit 6">
            </div>
            <div class="ef-error" id="otpError"></div>
          </div>

          <div class="ef-terms">
            <input id="terms" name="terms" type="checkbox" value="1" required>
            <div>
              <label for="terms">I agree to the <a class="ef-link" href="https://efactor.tech/user_agreement.html">Terms &amp; Conditions</a></label>
              <span class="ef-req">Required</span>
              <div class="ef-error" id="termsError"></div>
            </div>
          </div>
          <button class="ef-submit" id="submitButton" type="submit" disabled><span class="button-label">Create account</span></button>
        </form>
        <footer class="ef-footer">Already have an account? <a class="ef-link" href="<?= site_url('sd-login') ?>">Login</a></footer>
      </main>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var cfg = <?= json_encode($signupApi, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var form = document.getElementById('signupForm');
  var email = document.getElementById('email');
  var phone = document.getElementById('phone');
  var password = document.getElementById('password');
  var confirmPassword = document.getElementById('confirm_password');
  var terms = document.getElementById('terms');
  var otpSection = document.getElementById('otpSection');
  var sendOtpButton = document.getElementById('sendOtpButton');
  var submitButton = document.getElementById('submitButton');
  var formAlert = document.getElementById('formAlert');
  var meterBar = document.getElementById('passwordMeterBar');
  var strengthLabel = document.getElementById('passwordStrength');
  var badgeHost = document.getElementById('flowBadges');
  var otpBoxes = Array.prototype.slice.call(document.querySelectorAll('.ef-otp-box'));
  var sentKey = '';
  var sending = false;
  var creating = false;
  var resendTimer = 0;
  var resendLeft = 0;

  function phoneDigits() {
    return phone ? phone.value.replace(/\D/g, '').slice(0, 10) : '';
  }

  function emailValue() {
    return email ? email.value.trim().toLowerCase() : '';
  }

  function validEmail() {
    var value = emailValue();
    return value.length > 0 && value.length <= 191 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }

  function validPassword(value) {
    return value.length >= 8 && value.length <= 72 && /[A-Z]/.test(value) && /[a-z]/.test(value) && /[0-9]/.test(value) && /[^A-Za-z0-9]/.test(value);
  }

  function passwordsMatch() {
    return password && confirmPassword && password.value.length > 0 && password.value === confirmPassword.value;
  }

  function otpValue() {
    return otpBoxes.map(function (box) { return box.value; }).join('');
  }

  function requestKey() {
    return emailValue() + '|' + phoneDigits() + '|' + (password ? password.value : '');
  }

  function setError(id, message) {
    var node = document.getElementById(id);
    if (node) node.textContent = message || '';
  }

  function mark(id, bad) {
    var node = document.getElementById(id);
    if (!node) return;
    node.classList.toggle('has-error', !!bad);
  }

  function showAlert(message) {
    if (!formAlert) return;
    formAlert.textContent = message || '';
    formAlert.classList.toggle('is-visible', !!message);
  }

  function setBadge(id, kind, text) {
    if (!badgeHost) return;
    var node = document.getElementById(id);
    if (!text) {
      if (node) node.remove();
      return;
    }
    if (!node) {
      node = document.createElement('div');
      node.id = id;
      node.setAttribute('role', 'status');
      badgeHost.appendChild(node);
    }
    node.className = 'ef-badge ef-badge-' + kind + ' is-visible';
    node.textContent = text;
  }

  function scorePassword(value) {
    var score = 0;
    if (value.length >= 8) score++;
    if (/[A-Z]/.test(value)) score++;
    if (/[a-z]/.test(value)) score++;
    if (/[0-9]/.test(value)) score++;
    if (/[^A-Za-z0-9]/.test(value)) score++;
    return score;
  }

  function updateMeter() {
    if (!password || !meterBar || !strengthLabel) return;
    var score = scorePassword(password.value);
    var widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
    var labels = ['Password strength', 'Very weak', 'Weak', 'Fair', 'Strong', 'Excellent'];
    var colors = ['rgba(255,255,255,.18)', '#EF4444', '#F97316', '#F59E0B', '#22C55E', '#16A34A'];
    meterBar.style.width = widths[score];
    meterBar.style.background = colors[score];
    strengthLabel.textContent = labels[score];
    strengthLabel.style.color = colors[score];
  }

  function otpOpen() {
    return otpSection && otpSection.classList.contains('is-visible');
  }

  function showOtp() {
    if (!otpSection) return;
    var opening = !otpSection.classList.contains('is-visible');
    otpSection.classList.add('is-visible');
    if (opening && otpBoxes[0]) otpBoxes[0].focus();
    var scroll = document.getElementById('signupScroll');
    if (scroll && otpSection) {
      var top = otpSection.offsetTop - 8;
      if (top > scroll.scrollTop) scroll.scrollTop = top;
    }
  }

  function clearOtpBoxes() {
    otpBoxes.forEach(function (box) { box.value = ''; box.disabled = false; });
    syncCreateButton();
  }

  function syncCreateButton() {
    if (!submitButton || creating) return;
    var ready = otpOpen() && otpValue().length === 6 && sentKey !== '' && sentKey === requestKey();
    submitButton.disabled = !ready;
  }

  function describe(data) {
    if (data && typeof data.description === 'string' && data.description.trim()) {
      return data.description.trim();
    }
    if (data && typeof data.message === 'string' && data.message.trim()) {
      return data.message.trim();
    }
    var codes = {
      '101006': 'This account already exists.',
      '101037': 'The OTP is incorrect.',
      '101004': 'Enter a valid 10-digit mobile number.'
    };
    if (data && data.error_code && codes[String(data.error_code)]) {
      return codes[String(data.error_code)];
    }
    return 'Something went wrong. Please try again.';
  }

  function postJson(url, payload) {
    return fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    }).then(function (response) {
      return response.text().then(function (text) {
        var data = {};
        if (text) {
          try {
            data = JSON.parse(text);
          } catch (error) {
            var start = text.indexOf('{');
            var end = text.lastIndexOf('}');
            if (start >= 0 && end > start) {
              try { data = JSON.parse(text.slice(start, end + 1)); } catch (inner) { data = {}; }
            }
          }
        }
        if (!data || typeof data !== 'object') data = {};
        data._ok = String(data.status || '').toLowerCase() === 'success';
        return data;
      });
    });
  }

  function accountPayload() {
    return {
      email: emailValue(),
      phone: phoneDigits(),
      password: password ? password.value : '',
      country_code: cfg.countryCode,
      user_type: cfg.userType
    };
  }

  function validateAccount(show) {
    var ok = true;
    if (!validEmail()) {
      ok = false;
      if (show) { setError('emailError', 'Email is required.'); mark('emailWrap', true); }
    } else if (show) { setError('emailError', ''); mark('emailWrap', false); }
    if (phoneDigits().length !== 10) {
      ok = false;
      if (show) { setError('phoneError', 'Phone is required. Enter a 10-digit mobile number.'); mark('phoneWrap', true); }
    } else if (show) { setError('phoneError', ''); mark('phoneWrap', false); }
    if (!password || !validPassword(password.value)) {
      ok = false;
      if (show) { setError('passwordError', 'Password is required. Use 8 or more characters with uppercase, lowercase, number, and special character.'); mark('passwordWrap', true); }
    } else if (show) { setError('passwordError', ''); mark('passwordWrap', false); }
    if (!passwordsMatch()) {
      ok = false;
      if (show) { setError('confirmPasswordError', 'Confirm password is required and must match.'); mark('confirmWrap', true); }
    } else if (show) { setError('confirmPasswordError', ''); mark('confirmWrap', false); }
    return ok;
  }

  function startResendCooldown() {
    resendLeft = 30;
    if (sendOtpButton) {
      sendOtpButton.disabled = true;
      sendOtpButton.textContent = 'Resend OTP (' + resendLeft + 's)';
    }
    window.clearInterval(resendTimer);
    resendTimer = window.setInterval(function () {
      resendLeft -= 1;
      if (resendLeft <= 0 || creating) {
        window.clearInterval(resendTimer);
        if (sendOtpButton && !creating) {
          sendOtpButton.disabled = false;
          sendOtpButton.textContent = 'Resend OTP';
        }
        return;
      }
      if (sendOtpButton) sendOtpButton.textContent = 'Resend OTP (' + resendLeft + 's)';
    }, 1000);
  }

  function sendOtp() {
    if (sending || creating) return;
    showAlert('');
    setBadge('signupErrorBadge', '', '');
    if (!validateAccount(true)) {
      setBadge('signupErrorBadge', 'danger', 'Fill the required fields before sending the OTP.');
      return;
    }
    sending = true;
    if (sendOtpButton) {
      sendOtpButton.disabled = true;
      sendOtpButton.textContent = 'Sending OTP...';
    }
    setBadge('otpBadge', 'progress', 'Sending OTP...');
    setError('otpError', '');
    postJson(cfg.createUser, accountPayload()).then(function (data) {
      sending = false;
      if (!data._ok) {
        setBadge('otpBadge', 'danger', describe(data));
        if (sendOtpButton) {
          sendOtpButton.disabled = false;
          sendOtpButton.textContent = otpOpen() ? 'Resend OTP' : 'Send OTP';
        }
        return;
      }
      sentKey = requestKey();
      clearOtpBoxes();
      showOtp();
      setBadge('otpBadge', 'success', 'OTP sent');
      setBadge('signupErrorBadge', '', '');
      startResendCooldown();
      syncCreateButton();
    }).catch(function () {
      sending = false;
      setBadge('otpBadge', 'danger', 'Unable to connect. Please check your internet and try again.');
      if (sendOtpButton) {
        sendOtpButton.disabled = false;
        sendOtpButton.textContent = otpOpen() ? 'Resend OTP' : 'Send OTP';
      }
    });
  }

  function submitSignup() {
    if (creating || sending) return;
    showAlert('');
    if (!otpOpen() || sentKey === '') {
      setBadge('signupErrorBadge', 'danger', 'Send the OTP first.');
      return;
    }
    if (sentKey !== requestKey()) {
      setError('otpError', 'Details changed. Resend the OTP.');
      setBadge('signupErrorBadge', 'danger', 'Details changed. Resend the OTP.');
      syncCreateButton();
      return;
    }
    if (otpValue().length !== 6) {
      setError('otpError', 'Enter the 6-digit OTP.');
      return;
    }
    if (!terms || !terms.checked) {
      setError('termsError', 'You must agree to the Terms & Conditions.');
      return;
    }
    setError('termsError', '');
    setError('otpError', '');
    if (!validateAccount(true)) return;
    creating = true;
    if (submitButton) submitButton.disabled = true;
    var label = submitButton ? submitButton.querySelector('.button-label') : null;
    if (label) label.textContent = 'Creating account...';
    if (sendOtpButton) sendOtpButton.disabled = true;
    setBadge('accountBadge', 'progress', 'Creating your account...');
    setBadge('loginBadge', '', '');
    postJson(cfg.verifyUser, {
      phone: phoneDigits(),
      verification_code: otpValue()
    }).then(function (verified) {
      if (!verified._ok) {
        creating = false;
        if (label) label.textContent = 'Create account';
        syncCreateButton();
        if (sendOtpButton && resendLeft <= 0) {
          sendOtpButton.disabled = false;
          sendOtpButton.textContent = 'Resend OTP';
        }
        setBadge('accountBadge', 'danger', describe(verified));
        return null;
      }
      setBadge('accountBadge', 'success', 'Account created successfully');
      setBadge('loginBadge', 'progress', 'Getting you logged in...');
      if (label) label.textContent = 'Logging in...';
      return postJson(cfg.loginUser, {
        username: phoneDigits(),
        password: password.value,
        user_type: cfg.userType
      });
    }).then(function (login) {
      if (!login) return null;
      if (!login._ok) {
        creating = false;
        if (label) label.textContent = 'Create account';
        syncCreateButton();
        setBadge('loginBadge', 'danger', describe(login));
        return null;
      }
      return postJson(cfg.session, {
        email: login.email || emailValue(),
        phone: phoneDigits(),
        accesstoken: login.accesstoken || '',
        idtoken: login.idtoken || '',
        refreshtoken: login.refreshtoken || '',
        user_id: login.user_id || ''
      });
    }).then(function (session) {
      if (!session) return;
      if (!session._ok) {
        creating = false;
        if (label) label.textContent = 'Create account';
        setBadge('loginBadge', 'danger', describe(session));
        return;
      }
      setBadge('loginBadge', 'success', 'Logged in');
      window.location.assign((session && session.redirect) || cfg.done);
    }).catch(function () {
      creating = false;
      if (label) label.textContent = 'Create account';
      syncCreateButton();
      if (sendOtpButton && resendLeft <= 0) {
        sendOtpButton.disabled = false;
        sendOtpButton.textContent = otpOpen() ? 'Resend OTP' : 'Send OTP';
      }
      var created = document.getElementById('accountBadge');
      if (created && created.classList.contains('ef-badge-success')) {
        setBadge('loginBadge', 'danger', 'Unable to connect. Please check your internet and try again.');
      } else {
        setBadge('accountBadge', 'danger', 'Unable to connect. Please check your internet and try again.');
        setBadge('loginBadge', '', '');
      }
    });
  }

  function onDetailsChanged() {
    if (phone) phone.value = phoneDigits();
    if (sentKey && sentKey !== requestKey()) {
      setBadge('signupErrorBadge', 'danger', 'Details changed. Resend the OTP.');
    } else if (sentKey) {
      setBadge('signupErrorBadge', '', '');
    }
    syncCreateButton();
  }

  if (email) email.addEventListener('input', function () {
    setError('emailError', '');
    mark('emailWrap', false);
    onDetailsChanged();
  });
  if (phone) phone.addEventListener('input', function () {
    setError('phoneError', '');
    mark('phoneWrap', false);
    onDetailsChanged();
  });
  if (password) password.addEventListener('input', function () {
    updateMeter();
    setError('passwordError', '');
    mark('passwordWrap', false);
    onDetailsChanged();
  });
  if (confirmPassword) confirmPassword.addEventListener('input', function () {
    if (passwordsMatch()) {
      setError('confirmPasswordError', '');
      mark('confirmWrap', false);
    }
    onDetailsChanged();
  });
  if (terms) terms.addEventListener('change', function () {
    if (terms.checked) setError('termsError', '');
  });
  if (sendOtpButton) sendOtpButton.addEventListener('click', sendOtp);

  otpBoxes.forEach(function (box, index) {
    box.addEventListener('input', function () {
      box.value = box.value.replace(/\D/g, '').slice(0, 1);
      setError('otpError', '');
      syncCreateButton();
      if (box.value && otpBoxes[index + 1]) otpBoxes[index + 1].focus();
    });
    box.addEventListener('keydown', function (event) {
      if (event.key === 'Backspace' && !box.value && otpBoxes[index - 1]) otpBoxes[index - 1].focus();
    });
    box.addEventListener('paste', function (event) {
      var text = (event.clipboardData || window.clipboardData).getData('text') || '';
      var digits = text.replace(/\D/g, '').slice(0, 6);
      if (!digits) return;
      event.preventDefault();
      digits.split('').forEach(function (digit, digitIndex) {
        if (otpBoxes[digitIndex]) otpBoxes[digitIndex].value = digit;
      });
      syncCreateButton();
      var focusAt = Math.min(digits.length, otpBoxes.length - 1);
      if (otpBoxes[focusAt]) otpBoxes[focusAt].focus();
    });
  });

  document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
    button.addEventListener('click', function () {
      var input = document.getElementById(button.getAttribute('data-password-toggle'));
      if (!input) return;
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  });

  if (form) form.addEventListener('submit', function (event) {
    event.preventDefault();
    submitSignup();
  });

  updateMeter();
  syncCreateButton();

  var narrow = window.matchMedia('(max-width: 430px)');
  var scroll = document.getElementById('signupScroll');
  function pinPage() {
    if (!narrow.matches) return;
    if (window.scrollY !== 0) window.scrollTo(0, 0);
  }
  function placeField() {
    if (!narrow.matches) {
      if (scroll) scroll.style.paddingBottom = '';
      return;
    }
    pinPage();
    var viewport = window.visualViewport;
    var overlap = viewport ? Math.max(0, window.innerHeight - viewport.height) : 0;
    if (scroll) scroll.style.paddingBottom = overlap > 0 ? (overlap + 16) + 'px' : '';
    var active = document.activeElement;
    if (!scroll || !active || !scroll.contains(active) || !viewport) return;
    var limit = viewport.offsetTop + viewport.height - 12;
    var rect = active.getBoundingClientRect();
    if (rect.bottom > limit) scroll.scrollTop += rect.bottom - limit + 8;
  }
  document.addEventListener('focusin', function () {
    if (!narrow.matches) return;
    window.setTimeout(placeField, 60);
  });
  window.addEventListener('scroll', pinPage, { passive: true });
  if (window.visualViewport) {
    window.visualViewport.addEventListener('resize', placeField);
    window.visualViewport.addEventListener('scroll', pinPage);
  }
});
</script>
<?= $this->endSection() ?>
