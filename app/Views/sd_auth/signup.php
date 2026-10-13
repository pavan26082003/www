<?= $this->extend('layouts/sd_auth') ?>
<?= $this->section('content') ?>
<?php
$signupApi = [
    'sendOtp' => base_url('v8/api.php') . '?action=user',
    'verifyOtp' => base_url('v8/api.php') . '?action=userverify',
    'signup' => base_url('v8/api.php') . '?action=userlogin',
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
.ef-shell{position:relative;width:min(100%,390px);height:min(820px,calc(100vh - 48px));min-height:620px;display:flex;align-items:center;justify-content:center;padding:74px 24px 30px;overflow:hidden;border-radius:42px;background:linear-gradient(160deg,rgba(32,41,64,.72),rgba(31,21,12,.78)),url("<?= base_url('assets/images/efactor-ecosystem.jpg') ?>") center bottom/cover no-repeat;box-shadow:0 32px 80px rgba(32,41,64,.24)}
.ef-shell::before{content:"";position:absolute;inset:8px;border-radius:34px;background:linear-gradient(to bottom,rgba(0,0,0,.10),rgba(0,0,0,.10) 35%,rgba(0,0,0,.36));pointer-events:none}
.ef-shell::after{content:"";position:absolute;z-index:5;top:8px;left:50%;width:112px;height:27px;transform:translateX(-50%);border-radius:0 0 18px 18px;background:#101813}
.ef-back{position:absolute;z-index:8;top:30px;left:22px;width:42px;height:42px;display:grid;place-items:center;border:1px solid var(--ef-line);border-radius:50%;color:#fff;background:rgba(255,255,255,.12);backdrop-filter:blur(16px);text-decoration:none;box-shadow:0 12px 30px rgba(0,0,0,.18)}
.ef-back svg{width:18px;height:18px}
.ef-brand{position:absolute;z-index:8;top:34px;right:24px;pointer-events:none}
.ef-brand img{max-width:108px;max-height:38px;object-fit:contain;border-radius:8px}
.ef-scroll{position:relative;z-index:3;width:100%;max-height:100%;overflow-y:auto;overflow-x:hidden;padding:4px 0 2px;scrollbar-width:none;-webkit-overflow-scrolling:touch;overscroll-behavior:contain}
.ef-scroll::-webkit-scrollbar{width:0;height:0}
.ef-card{width:100%;padding:24px 22px 21px;border:1px solid var(--ef-line);border-radius:28px;background:var(--ef-panel);box-shadow:0 24px 70px rgba(0,0,0,.32),inset 0 1px 0 rgba(255,255,255,.14);backdrop-filter:blur(22px);animation:efRise .45s ease both}
.ef-heading-wrap{text-align:left;margin-bottom:18px}
.ef-title{margin:0;color:#fff;font-size:1.92rem;line-height:1.05;font-weight:850;letter-spacing:0}
.ef-subtitle{margin:8px 0 0;color:var(--ef-muted);font-size:.76rem;line-height:1.45}
.ef-field{margin-bottom:10px}
.ef-input{min-height:46px;display:flex;align-items:center;gap:10px;padding:0 13px;border:1px solid rgba(255,255,255,.16);border-radius:12px;background:rgba(255,255,255,.10);box-shadow:inset 0 1px 0 rgba(255,255,255,.08);transition:border-color .2s ease,background .2s ease,box-shadow .2s ease}
.ef-input:focus-within{border-color:rgba(232,237,242,.56);background:rgba(255,255,255,.16);box-shadow:0 0 0 4px rgba(232,237,242,.10)}
.ef-input.has-error{border-color:rgba(252,165,165,.7);background:rgba(127,29,29,.20)}
.ef-icon{width:18px;height:18px;flex:0 0 18px;color:#E8EDF2}
.ef-control{flex:1;width:100%;min-width:0;border:0;outline:0;padding:12px 0;color:#fff;background:transparent;font-size:.8rem}
.ef-control::placeholder{color:rgba(248,250,252,.62)}
.ef-toggle{width:32px;height:32px;display:grid;place-items:center;padding:0;border:0;border-radius:50%;color:#E8EDF2;background:transparent;cursor:pointer}
.ef-toggle svg{width:17px;height:17px}
.ef-error{min-height:13px;margin-top:3px;color:var(--ef-danger);font-size:.65rem}
.ef-password-block,.ef-otp{display:none}
.ef-password-block.is-visible,.ef-otp.is-visible{display:block}
.ef-otp{margin:0 0 10px}
.ef-otp-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:7px;color:var(--ef-muted);font-size:.64rem;font-weight:800}
.ef-otp-boxes{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:7px}
.ef-otp-box{width:100%;aspect-ratio:1;min-height:42px;border:1px solid rgba(255,255,255,.22);border-radius:9px;background:rgba(255,255,255,.12);text-align:center;color:#fff;font-size:1rem;font-weight:850;outline:0}
.ef-otp-box:focus{border-color:rgba(232,237,242,.68);box-shadow:0 0 0 3px rgba(232,237,242,.10)}
.ef-otp-box:disabled{opacity:.72}
.ef-otp-actions{display:flex;gap:8px;margin-top:10px}
.ef-send-otp,.ef-verify-otp{flex:1;display:inline-flex;min-height:44px;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.55);border-radius:14px;color:#fff;background:rgba(255,255,255,.18);padding:8px 12px;font-size:.82rem;font-weight:850;cursor:pointer;touch-action:manipulation}
.ef-send-otp:disabled,.ef-verify-otp:disabled{opacity:.55;cursor:default}
.ef-verify-otp.is-verified{opacity:1;border-color:rgba(134,239,172,.45);color:#DCFCE7;background:rgba(22,101,52,.25);cursor:default}
.ef-retry{display:none;width:100%;margin-top:8px}
.ef-retry.is-visible{display:inline-flex}
.ef-meter{height:4px;margin-top:5px;overflow:hidden;border-radius:99px;background:rgba(255,255,255,.16)}
.ef-meter span{display:block;width:0;height:100%;border-radius:inherit;transition:.25s ease}
.ef-meter-row{display:flex;justify-content:space-between;gap:9px;margin-top:4px;color:var(--ef-muted);font-size:.56rem}
.ef-terms{display:grid;grid-template-columns:18px 1fr;gap:8px;align-items:start;margin:0 0 12px;color:var(--ef-muted);font-size:.65rem;line-height:1.45}
.ef-terms input{width:15px;height:15px;margin:1px 0 0;accent-color:#FFFFFF;color-scheme:dark}
.ef-link{color:#fff;font-weight:850;text-decoration:none}
.ef-link:hover{text-decoration:underline}
.ef-submit{width:100%;min-height:49px;display:flex;align-items:center;justify-content:center;gap:8px;border:1px solid rgba(255,255,255,.2);border-radius:14px;color:#fff;background:linear-gradient(135deg,#202940,#1F150C);box-shadow:0 16px 34px rgba(0,0,0,.24);cursor:pointer;font-size:.8rem;font-weight:850;touch-action:manipulation}
.ef-submit:disabled{opacity:.55;cursor:default}
.ef-divider{display:flex;align-items:center;gap:10px;margin:13px 0 10px;color:var(--ef-muted);font-size:.62rem}
.ef-divider::before,.ef-divider::after{content:"";flex:1;height:1px;background:rgba(255,255,255,.18)}
.ef-socials{display:flex;align-items:center;justify-content:center;gap:14px;margin-bottom:12px}
.ef-social{width:40px;height:40px;display:grid;place-items:center;border:1px solid rgba(255,255,255,.18);border-radius:14px;color:#fff;background:rgba(255,255,255,.10);box-shadow:inset 0 1px 0 rgba(255,255,255,.08);cursor:pointer;text-decoration:none}
.ef-social svg{width:18px;height:18px}
.ef-footer{color:var(--ef-muted);text-align:center;font-size:.69rem}
.ef-alert{display:none;gap:8px;margin-bottom:10px;padding:9px 10px;border-radius:12px;font-size:.7rem;line-height:1.4}
.ef-alert.is-visible{display:flex}
.ef-alert-danger{color:#FEE2E2;border:1px solid rgba(252,165,165,.35);background:rgba(127,29,29,.24)}
@keyframes efRise{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
@media(max-width:430px){
  html,body{position:fixed;inset:0;width:100%;height:100%;overflow:hidden;background:#101827}
  .ef-page{position:fixed;inset:0;display:block;height:100%;min-height:0;padding:0;overflow:hidden}
  .ef-shell{position:absolute;inset:0;width:100%;height:100%;min-height:0;display:flex;flex-direction:column;align-items:stretch;justify-content:flex-start;padding:72px 16px calc(12px + env(safe-area-inset-bottom));overflow:hidden;border-radius:0;box-shadow:none}
  .ef-shell::before{inset:0;border-radius:0}
  .ef-shell::after{display:none}
  .ef-back{top:18px;left:16px}
  .ef-brand{top:22px;right:16px}
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
    <div class="ef-brand"><img src="<?= base_url('assets/images/efactor-logo.jpg') ?>" alt="Efactor"></div>
    <div class="ef-scroll" id="signupScroll">
      <main class="ef-card" aria-labelledby="signupTitle">
        <div class="ef-heading-wrap">
          <h1 class="ef-title" id="signupTitle">Get Started</h1>
          <p class="ef-subtitle">Create your secure Efactor account</p>
        </div>
        <div class="ef-alert ef-alert-danger" id="formAlert" role="alert"></div>
        <form id="signupForm" method="post" novalidate>
          <div class="ef-field" id="emailField">
            <div class="ef-input" id="emailWrap">
              <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <input class="ef-control" id="email" name="email" type="email" value="<?= old('email') ?>" autocomplete="email" inputmode="email" placeholder="Email address" maxlength="191" aria-label="Email address">
            </div>
            <div class="ef-error" id="emailError"></div>
          </div>

          <div class="ef-field" id="phoneField">
            <div class="ef-input" id="phoneWrap">
              <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect width="14" height="20" x="5" y="2" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M12 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              <input class="ef-control" id="phone" name="phone" type="tel" value="<?= old('phone') ?>" autocomplete="tel" inputmode="numeric" placeholder="Enter phone number" maxlength="10" aria-label="Phone number">
            </div>
            <div class="ef-error" id="phoneError"></div>
          </div>

          <div class="ef-otp" id="otpSection" aria-label="Enter six digit OTP">
            <div class="ef-otp-head"><span>Enter OTP</span></div>
            <div class="ef-otp-boxes" id="otpBoxes">
              <input class="ef-otp-box" id="signup_otp_digit_1" inputmode="numeric" maxlength="1" autocomplete="one-time-code" aria-label="OTP digit 1">
              <input class="ef-otp-box" id="signup_otp_digit_2" inputmode="numeric" maxlength="1" aria-label="OTP digit 2">
              <input class="ef-otp-box" id="signup_otp_digit_3" inputmode="numeric" maxlength="1" aria-label="OTP digit 3">
              <input class="ef-otp-box" id="signup_otp_digit_4" inputmode="numeric" maxlength="1" aria-label="OTP digit 4">
              <input class="ef-otp-box" id="signup_otp_digit_5" inputmode="numeric" maxlength="1" aria-label="OTP digit 5">
              <input class="ef-otp-box" id="signup_otp_digit_6" inputmode="numeric" maxlength="1" aria-label="OTP digit 6">
            </div>
            <div class="ef-error" id="otpError"></div>
            <div class="ef-otp-actions">
              <button class="ef-send-otp" type="button" id="resendOtpButton">Resend OTP</button>
              <button class="ef-verify-otp" type="button" id="verifyOtpButton" disabled>Verify</button>
            </div>
          </div>

          <div class="ef-password-block" id="passwordBlock">
            <div class="ef-field">
              <div class="ef-input" id="passwordWrap">
                <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                <input class="ef-control" id="password" name="password" type="password" autocomplete="new-password" maxlength="72" placeholder="Password" aria-label="Password">
                <button class="ef-toggle" type="button" data-password-toggle="password" aria-label="Show password">
                  <svg data-eye viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                </button>
              </div>
              <div class="ef-meter"><span id="passwordMeterBar"></span></div>
              <div class="ef-meter-row"><span id="passwordStrength">Password strength</span><span>Upper / lower / number / special</span></div>
              <div class="ef-error" id="passwordError"></div>
            </div>
            <div class="ef-field">
              <div class="ef-input" id="confirmWrap">
                <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" stroke="currentColor" stroke-width="1.8"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <input class="ef-control" id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" maxlength="72" placeholder="Confirm password" aria-label="Confirm password">
                <button class="ef-toggle" type="button" data-password-toggle="confirm_password" aria-label="Show confirm password">
                  <svg data-eye viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                </button>
              </div>
              <div class="ef-error" id="confirmPasswordError"></div>
            </div>
          </div>

          <div class="ef-terms">
            <input id="terms" name="terms" type="checkbox" value="1">
            <div>
              <label for="terms">I agree to the <a class="ef-link" href="https://efactor.tech/user_agreement.html">Terms &amp; Conditions</a></label>
              <div class="ef-error" id="termsError"></div>
            </div>
          </div>
          <button class="ef-submit" id="submitButton" type="submit" disabled><span class="button-label">Create account</span></button>
        </form>
        <div class="ef-divider">Or continue with</div>
        <div class="ef-socials" aria-label="Social login options">
          <a class="ef-social" href="<?= site_url('sd-social/google') ?>" aria-label="Google" title="Google"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg></a>
          <a class="ef-social" href="<?= site_url('sd-social/apple') ?>" aria-label="Apple" title="Apple"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.53 4.08zM12.03 7.25C11.88 5.02 13.69 3.18 15.77 3c.29 2.58-2.34 4.5-3.74 4.25z"/></svg></a>
          <a class="ef-social" href="<?= site_url('sd-social/facebook') ?>" aria-label="Facebook" title="Facebook"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
        </div>
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
  var passwordBlock = document.getElementById('passwordBlock');
  var otpSection = document.getElementById('otpSection');
  var resendOtpButton = document.getElementById('resendOtpButton');
  var verifyOtpButton = document.getElementById('verifyOtpButton');
  var submitButton = document.getElementById('submitButton');
  var formAlert = document.getElementById('formAlert');
  var meterBar = document.getElementById('passwordMeterBar');
  var strengthLabel = document.getElementById('passwordStrength');
  var otpBoxes = Array.prototype.slice.call(document.querySelectorAll('.ef-otp-box'));
  var sentKey = '';
  var autoKey = '';
  var sending = false;
  var otpVerified = false;
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
    return emailValue() + '|' + phoneDigits();
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

  function showPasswordFields() {
    if (passwordBlock) passwordBlock.classList.add('is-visible');
  }

  function showOtp() {
    if (!otpSection) return;
    var opening = !otpSection.classList.contains('is-visible');
    otpSection.classList.add('is-visible');
    if (opening && otpBoxes[0]) otpBoxes[0].focus();
  }

  function clearOtpBoxes() {
    otpBoxes.forEach(function (box) { box.value = ''; box.disabled = false; });
    syncVerifyButton();
  }

  function syncVerifyButton() {
    if (!verifyOtpButton || otpVerified) return;
    verifyOtpButton.disabled = otpValue().length !== 6;
    verifyOtpButton.textContent = 'Verify';
    verifyOtpButton.classList.remove('is-verified');
  }

  function markVerified() {
    otpVerified = true;
    if (verifyOtpButton) {
      verifyOtpButton.disabled = true;
      verifyOtpButton.textContent = 'Verified';
      verifyOtpButton.classList.add('is-verified');
    }
    if (resendOtpButton) resendOtpButton.disabled = true;
    otpBoxes.forEach(function (box) { box.disabled = true; });
    if (email) email.readOnly = true;
    if (phone) phone.readOnly = true;
    showPasswordFields();
    if (password) password.focus();
    if (submitButton) submitButton.disabled = false;
    setError('otpError', '');
  }

  function describe(data) {
    if (data && typeof data.description === 'string' && data.description.trim()) {
      return data.description.trim();
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
      credentials: 'same-origin',
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
        data._ok = data.status === 'success';
        return data;
      });
    });
  }

  function otpPayload() {
    return {
      email: emailValue(),
      phone: phoneDigits(),
      password: password ? password.value : '',
      country_code: cfg.countryCode,
      user_type: cfg.userType
    };
  }

  function validateOtpRequest(show) {
    var ok = true;
    if (!validEmail()) {
      ok = false;
      if (show) { setError('emailError', 'Enter a valid email address.'); mark('emailWrap', true); }
    } else if (show) { setError('emailError', ''); mark('emailWrap', false); }
    if (phoneDigits().length !== 10) {
      ok = false;
      if (show) { setError('phoneError', 'Enter a valid 10-digit mobile number.'); mark('phoneWrap', true); }
    } else if (show) { setError('phoneError', ''); mark('phoneWrap', false); }
    if (!password || !validPassword(password.value)) {
      ok = false;
      if (show) { setError('passwordError', 'Use 8 or more characters with uppercase, lowercase, number, and special character.'); mark('passwordWrap', true); }
    } else if (show) { setError('passwordError', ''); mark('passwordWrap', false); }
    if (!passwordsMatch()) {
      ok = false;
      if (show) { setError('confirmPasswordError', 'Passwords do not match.'); mark('confirmWrap', true); }
    } else if (show) { setError('confirmPasswordError', ''); mark('confirmWrap', false); }
    return ok;
  }

  function startResendCooldown() {
    resendLeft = 30;
    if (resendOtpButton) {
      resendOtpButton.disabled = true;
      resendOtpButton.textContent = 'Resend OTP (' + resendLeft + 's)';
    }
    window.clearInterval(resendTimer);
    resendTimer = window.setInterval(function () {
      resendLeft -= 1;
      if (resendLeft <= 0 || otpVerified) {
        window.clearInterval(resendTimer);
        if (resendOtpButton && !otpVerified) {
          resendOtpButton.disabled = false;
          resendOtpButton.textContent = 'Resend OTP';
        }
        return;
      }
      if (resendOtpButton) resendOtpButton.textContent = 'Resend OTP (' + resendLeft + 's)';
    }, 1000);
  }

  function sendOtp(force) {
    if (otpVerified || sending) return;
    if (phoneDigits().length !== 10) {
      if (force) {
        setError('phoneError', 'Enter a valid 10-digit mobile number.');
        mark('phoneWrap', true);
      }
      return;
    }
    showOtp();
    if (!validEmail()) {
      setError('emailError', 'Enter a valid email address.');
      mark('emailWrap', true);
      return;
    }
    setError('emailError', '');
    mark('emailWrap', false);
    setError('phoneError', '');
    mark('phoneWrap', false);
    var key = requestKey();
    if (!force && (autoKey === key || sentKey === key)) return;
    if (!force) autoKey = key;
    sending = true;
    if (resendOtpButton) resendOtpButton.disabled = true;
    showAlert('');
    setError('otpError', '');
    if (force) clearOtpBoxes();
    postJson(cfg.sendOtp, otpPayload()).then(function (data) {
      sending = false;
      showOtp();
      if (!data._ok) {
        autoKey = '';
        setError('otpError', describe(data));
        if (resendOtpButton && !otpVerified) resendOtpButton.disabled = false;
        return;
      }
      sentKey = key;
      showAlert('');
      setError('otpError', '');
      startResendCooldown();
    }).catch(function () {
      sending = false;
      autoKey = '';
      showOtp();
      if (resendOtpButton) resendOtpButton.disabled = false;
      setError('otpError', 'Unable to connect. Please check your internet and try again.');
    });
  }

  function verifyOtp() {
    if (otpVerified || otpValue().length !== 6) return;
    if (sentKey && sentKey !== requestKey()) {
      setError('otpError', 'Number changed. Resend the code.');
      return;
    }
    if (verifyOtpButton) {
      verifyOtpButton.disabled = true;
      verifyOtpButton.textContent = 'Verifying...';
    }
    showAlert('');
    postJson(cfg.verifyOtp, {
      phone: phoneDigits(),
      verification_code: otpValue()
    }).then(function (data) {
      if (!data._ok) {
        syncVerifyButton();
        setError('otpError', describe(data));
        return;
      }
      markVerified();
    }).catch(function () {
      syncVerifyButton();
      showAlert('Unable to connect. Please check your internet and try again.');
    });
  }

  function submitSignup() {
    if (!otpVerified) {
      showAlert('Verify the OTP before creating the account.');
      return;
    }
    if (!terms || !terms.checked) {
      setError('termsError', 'You must agree to the Terms & Conditions.');
      return;
    }
    setError('termsError', '');
    if (!validateOtpRequest(true)) return;
    var label = submitButton ? submitButton.querySelector('.button-label') : null;
    if (submitButton) submitButton.disabled = true;
    if (label) label.textContent = 'Creating account...';
    showAlert('');
    postJson(cfg.verifyOtp, {
      phone: phoneDigits(),
      verification_code: otpValue(),
      password: password.value
    }).then(function (verified) {
      if (!verified._ok) {
        if (submitButton) submitButton.disabled = false;
        if (label) label.textContent = 'Create account';
        showAlert(describe(verified));
        return null;
      }
      return postJson(cfg.signup, {
        username: phoneDigits(),
        password: password.value,
        user_type: cfg.userType
      });
    }).then(function (data) {
      if (!data) return;
      if (!data._ok) {
        if (submitButton) submitButton.disabled = false;
        if (label) label.textContent = 'Create account';
        showAlert(describe(data));
        return;
      }
      window.location.assign(cfg.done);
    }).catch(function () {
      if (submitButton) submitButton.disabled = false;
      if (label) label.textContent = 'Create account';
      showAlert('Unable to connect. Please check your internet and try again.');
    });
  }

  function onIdentityChanged() {
    if (phone) phone.value = phoneDigits();
    if (otpVerified) return;
    if (phoneDigits().length !== 10) {
      if (otpSection) otpSection.classList.remove('is-visible');
      return;
    }
    setError('phoneError', '');
    mark('phoneWrap', false);
    showOtp();
    sendOtp(false);
  }

  if (email) email.addEventListener('input', onIdentityChanged);
  if (phone) phone.addEventListener('input', onIdentityChanged);
  if (password) password.addEventListener('input', function () {
    updateMeter();
    setError('passwordError', '');
    mark('passwordWrap', false);
  });
  if (confirmPassword) confirmPassword.addEventListener('input', function () {
    if (passwordsMatch()) {
      setError('confirmPasswordError', '');
      mark('confirmWrap', false);
    }
  });
  if (resendOtpButton) resendOtpButton.addEventListener('click', function () { sendOtp(true); });
  if (verifyOtpButton) verifyOtpButton.addEventListener('click', verifyOtp);

  otpBoxes.forEach(function (box, index) {
    box.addEventListener('input', function () {
      box.value = box.value.replace(/\D/g, '').slice(0, 1);
      syncVerifyButton();
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
      syncVerifyButton();
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
  onIdentityChanged();

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
