<?= $this->extend('layouts/sd_auth') ?>
<?= $this->section('content') ?>

<style>
:root{
  --ef-green:#3D7A52;
  --ef-green-dark:#2F5D3D;
  --ef-light-green:#DCE8DD;
  --ef-bg:#F8FAF8;
  --ef-white:#FFFFFF;
  --ef-text:#2E2E2E;
  --ef-muted:#8B8B8B;
  --ef-border:#E4E8E5;
  --ef-success:#5FBF73;
  --ef-danger:#C94E4E;
}
*{box-sizing:border-box}
html{min-width:320px;-webkit-text-size-adjust:100%}
body{
  margin:0;
  min-width:320px;
  overflow-x:hidden;
  color:var(--ef-text);
  font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
  background:
    linear-gradient(110deg,rgba(248,250,248,.9),rgba(229,237,231,.7)),
    url("<?= base_url('assets/images/efactor-ecosystem.jpg') ?>") center/cover fixed no-repeat;
}
button,input{font:inherit}
.ef-page{min-height:100vh;display:grid;place-items:center;padding:24px}
.ef-phone{
  position:relative;
  width:min(100%,390px);
  height:min(820px,calc(100vh - 48px));
  min-height:620px;
  padding:8px;
  overflow:hidden;
  border-radius:42px;
  background:#101813;
  box-shadow:0 32px 80px rgba(24,52,33,.24);
}
.ef-phone::before{
  content:"";
  position:absolute;
  z-index:40;
  top:8px;
  left:50%;
  width:112px;
  height:27px;
  transform:translateX(-50%);
  border-radius:0 0 18px 18px;
  background:#101813;
}
.ef-screen{
  position:relative;
  height:100%;
  overflow-y:auto;
  overflow-x:hidden;
  overscroll-behavior:contain;
  border-radius:34px;
  background:#fff;
  scrollbar-width:thin;
  scrollbar-color:rgba(61,122,82,.38) transparent;
}
.ef-screen::-webkit-scrollbar{width:5px}.ef-screen::-webkit-scrollbar-thumb{background:rgba(61,122,82,.38);border-radius:99px}
.ef-back{
  position:absolute;
  z-index:25;
  top:29px;
  left:19px;
  width:36px;
  height:36px;
  display:grid;
  place-items:center;
  border:1px solid rgba(61,122,82,.12);
  border-radius:50%;
  color:var(--ef-green);
  background:rgba(239,246,240,.93);
  box-shadow:0 8px 20px rgba(47,93,61,.10);
  text-decoration:none;
  transition:transform .2s ease,background .2s ease,box-shadow .2s ease;
}
.ef-back:hover{transform:translateX(-2px);background:#fff;box-shadow:0 10px 24px rgba(47,93,61,.14)}.ef-back svg{width:17px;height:17px}
.ef-hero{
  position:relative;
  min-height:230px;
  overflow:hidden;
  background:
    linear-gradient(to bottom,rgba(20,48,28,.03),rgba(20,48,28,.10)),
    url("<?= base_url('assets/images/efactor-ecosystem.jpg') ?>") center 28%/cover no-repeat;
}
.ef-hero::after{
  content:"";
  position:absolute;
  left:-12%;
  right:-12%;
  bottom:-78px;
  height:132px;
  border-radius:50% 50% 0 0/100% 100% 0 0;
  background:#fff;
}
.ef-brand{position:absolute;z-index:4;top:35px;right:24px;display:flex;align-items:center}.ef-brand img{max-width:102px;max-height:36px;object-fit:contain;border-radius:8px}.ef-brand-fallback{color:#fff;font-size:1.08rem;font-weight:900;text-shadow:0 4px 18px rgba(0,0,0,.28)}
.ef-card{position:relative;z-index:7;margin-top:-58px;padding:0 28px 18px;background:#fff;animation:efSlideUp .58s ease both}
.ef-heading-wrap{position:relative;text-align:center;margin-bottom:17px}
.ef-title{margin:0;color:var(--ef-green);font-size:1.68rem;line-height:1.15;font-weight:850;letter-spacing:-.03em}
.ef-subtitle{margin:6px 0 0;color:#A3AAA5;font-size:.71rem;line-height:1.45}
.ef-leaf-accent{
  position:absolute;
  top:-22px;
  right:-14px;
  width:65px;
  height:65px;
  border-radius:58% 42% 64% 36%;
  background:url("<?= base_url('assets/images/efactor-ecosystem.png') ?>") 70% 28%/190px auto no-repeat;
  transform:rotate(-18deg);
  box-shadow:0 10px 20px rgba(47,93,61,.08);
  animation:efLeaf 4.8s ease-in-out infinite;
}
.ef-field{margin-bottom:10px}
.ef-input{
  min-height:49px;
  display:flex;
  align-items:center;
  gap:10px;
  padding:0 13px;
  border:1px solid transparent;
  border-radius:14px;
  background:var(--ef-light-green);
  transition:border-color .2s ease,box-shadow .2s ease,background .2s ease,transform .2s ease;
}
.ef-input:focus-within{border-color:rgba(61,122,82,.32);background:#F0F6F1;box-shadow:0 0 0 4px rgba(61,122,82,.08);transform:translateY(-1px)}
.ef-input.has-error{border-color:#D99595;background:#FFF5F5;box-shadow:0 0 0 3px rgba(201,78,78,.07)}
.ef-icon{width:18px;height:18px;flex:0 0 18px;color:var(--ef-green)}
.ef-control{flex:1;width:100%;min-width:0;border:0;outline:0;padding:13px 0;color:#365140;background:transparent;font-size:.79rem;-webkit-appearance:none;appearance:none}.ef-control::placeholder{color:#7F9185}
.ef-toggle{width:34px;height:34px;display:grid;place-items:center;padding:0;border:0;border-radius:50%;color:#55735D;background:transparent;cursor:pointer;transition:background .2s ease,transform .2s ease}.ef-toggle:hover{background:rgba(255,255,255,.6);transform:scale(1.04)}.ef-toggle svg{width:17px;height:17px}
.ef-error{min-height:13px;margin-top:3px;color:var(--ef-danger);font-size:.65rem}
.ef-meter{height:4px;margin-top:5px;overflow:hidden;border-radius:99px;background:#E8EEE9}.ef-meter span{display:block;width:0;height:100%;border-radius:inherit;transition:.25s ease}
.ef-meter-row{display:flex;justify-content:space-between;gap:9px;margin-top:4px;color:#9AA49D;font-size:.56rem}.ef-meter-row span:first-child{font-weight:800}.ef-meter-row span:last-child{text-align:right}
.ef-terms{display:grid;grid-template-columns:18px 1fr;gap:7px;align-items:start;margin:1px 0 12px;color:#8E9B92;font-size:.64rem;line-height:1.45}.ef-terms input{width:15px;height:15px;margin:1px 0 0;accent-color:var(--ef-green)}
.ef-link{color:var(--ef-green);font-weight:800;text-decoration:none}.ef-link:hover{text-decoration:underline}
.ef-submit{
  width:100%;
  min-height:49px;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  border:0;
  border-radius:25px;
  color:#fff;
  background:linear-gradient(135deg,#447F57,#356C49);
  box-shadow:0 12px 25px rgba(61,122,82,.22);
  cursor:pointer;
  font-size:.78rem;
  font-weight:800;
  transition:transform .22s ease,box-shadow .22s ease,filter .22s ease;
}
.ef-submit:hover{transform:translateY(-2px) scale(1.006);box-shadow:0 16px 32px rgba(61,122,82,.28);filter:brightness(1.03)}.ef-submit:active{transform:scale(.985)}.ef-submit:disabled{opacity:.68;cursor:not-allowed;transform:none}
.ef-divider{display:flex;align-items:center;gap:10px;margin:14px 0 10px;color:#969E98;font-size:.62rem}.ef-divider::before,.ef-divider::after{content:"";flex:1;height:1px;background:#E4E8E5}
.ef-socials{display:flex;align-items:center;justify-content:center;gap:16px;margin-bottom:8px}
.ef-social{
  width:42px;height:42px;display:grid;place-items:center;border:1px solid var(--ef-border);border-radius:50%;color:#355540;background:#fff;box-shadow:0 8px 22px rgba(0,0,0,.07);cursor:default;transition:transform .2s ease,box-shadow .2s ease;
}
.ef-social:hover{transform:translateY(-2px);box-shadow:0 11px 25px rgba(0,0,0,.10)}.ef-social svg{width:18px;height:18px}
.ef-footer{padding:4px 24px 24px;color:#9BA39D;text-align:center;background:#fff;font-size:.68rem}
.ef-alert{display:flex;gap:8px;margin-bottom:10px;padding:9px 10px;border-radius:12px;font-size:.7rem;line-height:1.4}.ef-alert-danger{color:#A73F3F;border:1px solid #EFCACA;background:#FFF4F4}
@keyframes efSlideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
@keyframes efLeaf{0%,100%{transform:rotate(-18deg) translateY(0)}50%{transform:rotate(-14deg) translateY(-5px)}}
@media(max-width:430px){
  body{background:#fff}.ef-page{display:block;min-height:100dvh;padding:0}.ef-phone{width:100%;height:100dvh;min-height:100dvh;padding:0;border-radius:0;box-shadow:none}.ef-phone::before{display:none}.ef-screen{border-radius:0}.ef-back{top:18px}.ef-hero{min-height:220px}
}
@media(prefers-reduced-motion:reduce){.ef-card,.ef-leaf-accent{animation:none}.ef-back,.ef-input,.ef-toggle,.ef-submit,.ef-social{transition:none}}
</style>

<div class="ef-page">
  <div class="ef-phone">
    <div class="ef-screen">
      <a class="ef-back" href="<?= site_url('welcome') ?>" aria-label="Back to welcome">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>

      <section class="ef-hero" aria-hidden="true">
        <div class="ef-brand">
          <img src="<?= base_url('assets/images/efactor-logo.jpg') ?>"
               alt=""
               onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
          <span class="ef-brand-fallback" style="display:none">efactor</span>
        </div>
      </section>

      <main class="ef-card" aria-labelledby="signupTitle">
        <div class="ef-heading-wrap">
          <div class="ef-leaf-accent" aria-hidden="true"></div>
          <h1 class="ef-title" id="signupTitle">Register</h1>
          <p class="ef-subtitle">Create your new account</p>
        </div>

        <?php $formError = $extraErrors['form'] ?? session('error'); ?>
        <?php if ($formError): ?>
          <div class="ef-alert ef-alert-danger">
            <span><?= esc($formError) ?></span>
          </div>
        <?php endif; ?>

        <form id="signupForm" action="<?= site_url('sd-signup') ?>" method="post" novalidate>
          <?= csrf_field() ?>

          <div class="ef-field">
            <div class="ef-input <?= ($validation && $validation->hasError('email')) ? 'has-error' : '' ?>">
              <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 6.5h16v11H4zM4.5 7l7.5 5.5L19.5 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <input
                class="ef-control"
                id="email"
                name="email"
                type="email"
                value="<?= old('email') ?>"
                autocomplete="email"
                inputmode="email"
                placeholder="Email address"
                maxlength="191"
                required
              >
            </div>
            <div class="ef-error"><?= ($validation ? $validation->getError('email') : '') ?></div>
          </div>

          <div class="ef-field">
            <div class="ef-input <?= ($validation && $validation->hasError('phone')) ? 'has-error' : '' ?>">
              <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="7" y="2.8" width="10" height="18.4" rx="2" stroke="currentColor" stroke-width="1.6"/>
                <path d="M10.5 5h3M11 18.5h2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
              </svg>
              <input
                class="ef-control"
                id="phone"
                name="phone"
                type="tel"
                value="<?= old('phone') ?>"
                autocomplete="tel"
                inputmode="numeric"
                pattern="[0-9]{10}"
                minlength="10"
                maxlength="10"
                placeholder="10-digit mobile number"
                required
              >
            </div>
            <div class="ef-error"><?= ($validation ? $validation->getError('phone') : '') ?></div>
          </div>

          <div class="ef-field">
            <div class="ef-input <?= ($validation && $validation->hasError('password')) ? 'has-error' : '' ?>">
              <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/>
                <path d="M8 10V7a4 4 0 018 0v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
              </svg>
              <input
                class="ef-control"
                id="password"
                name="password"
                type="password"
                autocomplete="new-password"
                maxlength="72"
                placeholder="Password"
                required
              >
              <button class="ef-toggle" type="button" data-password-toggle="password" aria-label="Show password">
                <svg data-eye viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M2.8 12s3.3-5 9.2-5 9.2 5 9.2 5-3.3 5-9.2 5-9.2-5-9.2-5z" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="12" cy="12" r="2.3" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <svg data-eye-off style="display:none" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M3 3l18 18M10.6 7.2A9.7 9.7 0 0112 7c5.9 0 9.2 5 9.2 5a12.6 12.6 0 01-2.7 3.1M6.1 6.6C4 8 2.8 12 2.8 12s3.3 5 9.2 5c1 0 1.9-.1 2.8-.4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
              </button>
            </div>
            <div class="ef-meter"><span id="passwordMeterBar"></span></div>
            <div class="ef-meter-row">
              <span id="passwordStrength">Password strength</span>
              <span>Upper · lower · number · special</span>
            </div>
            <div class="ef-error"><?= ($validation ? $validation->getError('password') : '') ?></div>
          </div>

          <div class="ef-field">
            <div class="ef-input <?= ($validation && $validation->hasError('confirm_password')) ? 'has-error' : '' ?>">
              <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 3l7 3v5c0 4.8-3 8.1-7 10-4-1.9-7-5.2-7-10V6l7-3z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                <path d="M9.3 12l1.8 1.8 3.8-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <input
                class="ef-control"
                id="confirm_password"
                name="confirm_password"
                type="password"
                autocomplete="new-password"
                maxlength="72"
                placeholder="Confirm password"
                required
              >
              <button class="ef-toggle" type="button" data-password-toggle="confirm_password" aria-label="Show confirm password">
                <svg data-eye viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M2.8 12s3.3-5 9.2-5 9.2 5 9.2 5-3.3 5-9.2 5-9.2-5-9.2-5z" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="12" cy="12" r="2.3" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <svg data-eye-off style="display:none" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M3 3l18 18M10.6 7.2A9.7 9.7 0 0112 7c5.9 0 9.2 5 9.2 5a12.6 12.6 0 01-2.7 3.1M6.1 6.6C4 8 2.8 12 2.8 12s3.3 5 9.2 5c1 0 1.9-.1 2.8-.4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
              </button>
            </div>
            <div class="ef-error" id="confirmPasswordError"><?= ($validation ? $validation->getError('confirm_password') : '') ?></div>
          </div>

          <div class="ef-terms">
            <input id="terms" name="terms" type="checkbox" value="1" <?= old('terms') === '1' ? 'checked' : '' ?> required>
            <div>
              <label for="terms">I agree to the <a class="ef-link" href="#">Terms &amp; Conditions</a></label>
              <div class="ef-error"><?= ($validation ? $validation->getError('terms') : '') ?></div>
            </div>
          </div>

          <button class="ef-submit" id="submitButton" type="submit">
            <span class="button-label">Create account</span>
            <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
          </button>
        </form>

        <div class="ef-divider">Or continue with</div>
        <div class="ef-socials" aria-label="Social login options">
          <button class="ef-social" type="button" aria-label="Facebook" title="Facebook">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.6 21v-8h2.7l.4-3h-3.1V8.1c0-.9.3-1.5 1.6-1.5H17V3.9c-.4-.1-1.4-.2-2.4-.2-2.4 0-4.1 1.5-4.1 4.2V10H8v3h2.5v8h3.1z"/></svg>
          </button>
          <button class="ef-social" type="button" aria-label="Google" title="Google">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M21 12.2c0-.7-.1-1.2-.2-1.8H12v3.4h5.1a4.4 4.4 0 01-1.9 2.9v2.4h3.1c1.8-1.7 2.7-4 2.7-6.9z" fill="#4285F4"/><path d="M12 21c2.6 0 4.8-.9 6.4-2.3l-3.1-2.4c-.9.6-2 1-3.3 1a5.6 5.6 0 01-5.3-3.9H3.5v2.5A9.7 9.7 0 0012 21z" fill="#34A853"/><path d="M6.7 13.4a5.9 5.9 0 010-3.7V7.2H3.5a9.6 9.6 0 000 8.7l3.2-2.5z" fill="#FBBC05"/><path d="M12 5.7c1.5 0 2.8.5 3.8 1.5l2.8-2.8A9.4 9.4 0 0012 1 9.7 9.7 0 003.5 7.2l3.2 2.5A5.6 5.6 0 0112 5.7z" fill="#EA4335"/></svg>
          </button>
          <button class="ef-social" type="button" aria-label="Apple" title="Apple">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.7 12.7c0-2.7 2.2-4 2.3-4.1-1.3-1.8-3.2-2-3.9-2-1.6-.2-3.2 1-4 1-.8 0-2.1-1-3.5-.9-1.8 0-3.5 1-4.4 2.7-1.9 3.3-.5 8.1 1.3 10.7.9 1.3 2 2.7 3.5 2.6 1.4-.1 1.9-.9 3.6-.9 1.7 0 2.2.9 3.6.9 1.5 0 2.5-1.3 3.4-2.6 1-1.5 1.5-3 1.5-3.1-.1 0-3.4-1.3-3.4-4.3zM14 4.9c.8-1 1.3-2.3 1.2-3.6-1.2 0-2.6.8-3.4 1.7-.7.8-1.4 2.1-1.2 3.4 1.3.1 2.6-.6 3.4-1.5z"/></svg>
          </button>
        </div>
      </main>

      <footer class="ef-footer">
        Already have an account?
        <a class="ef-link" href="<?= site_url('sd-login') ?>">Sign in</a>
      </footer>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('confirm_password');
  const meterBar = document.getElementById('passwordMeterBar');
  const strengthLabel = document.getElementById('passwordStrength');
  const form = document.getElementById('signupForm');
  const submit = document.getElementById('submitButton');

  function getScore(value) {
    let score = 0;
    if (value.length >= 8) score++;
    if (/[A-Z]/.test(value)) score++;
    if (/[a-z]/.test(value)) score++;
    if (/[0-9]/.test(value)) score++;
    if (/[^A-Za-z0-9]/.test(value)) score++;
    return score;
  }

  function updateMeter() {
    const score = getScore(password.value);
    const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
    const labels = ['Password strength', 'Very weak', 'Weak', 'Fair', 'Strong', 'Excellent'];
    const colors = ['#cbd5cb', '#EF4444', '#F97316', '#F59E0B', '#5FBF73', '#3D7A52'];

    meterBar.style.width = widths[score];
    meterBar.style.background = colors[score];
    strengthLabel.textContent = labels[score];
    strengthLabel.style.color = colors[score];
  }

  function checkMatch() {
    const error = document.getElementById('confirmPasswordError');

    if (confirmPassword.value && confirmPassword.value !== password.value) {
      confirmPassword.setCustomValidity('Passwords do not match.');

      if (!error.textContent.trim()) {
        error.textContent = 'Passwords do not match.';
      }
    } else {
      confirmPassword.setCustomValidity('');

      if (error.textContent.trim() === 'Passwords do not match.') {
        error.textContent = '';
      }
    }
  }

  password.addEventListener('input', function () {
    updateMeter();
    checkMatch();
  });

  confirmPassword.addEventListener('input', checkMatch);

  document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
    button.addEventListener('click', function () {
      const input = document.getElementById(button.dataset.passwordToggle);
      if (!input) return;

      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';

      const eye = button.querySelector('[data-eye]');
      const eyeOff = button.querySelector('[data-eye-off]');
      if (eye) eye.style.display = show ? 'none' : '';
      if (eyeOff) eyeOff.style.display = show ? '' : 'none';

      button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  });

  form.addEventListener('submit', function (event) {
    checkMatch();

    if (!form.checkValidity()) {
      event.preventDefault();
      form.reportValidity();
      return;
    }

    submit.disabled = true;
    submit.querySelector('.button-label').textContent = 'Creating account...';

    const spinner = submit.querySelector('.spinner-border');
    if (spinner) spinner.classList.remove('d-none');
  });

  const screen = document.querySelector('.ef-screen');
  if (screen) {
    screen.addEventListener('wheel', function (event) {
      const canScroll = screen.scrollHeight > screen.clientHeight;
      if (!canScroll) return;

      const atTop = screen.scrollTop <= 0;
      const atBottom = Math.ceil(screen.scrollTop + screen.clientHeight) >= screen.scrollHeight;

      if ((atTop && event.deltaY < 0) || (atBottom && event.deltaY > 0)) {
        return;
      }

      event.stopPropagation();
    }, { passive: true });
  }

  updateMeter();
});
</script>

<?= $this->endSection() ?>
