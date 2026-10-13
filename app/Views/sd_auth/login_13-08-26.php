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
  --ef-shadow:0 15px 40px rgba(0,0,0,.08);
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
.ef-back:hover{transform:translateX(-2px);background:#fff;box-shadow:0 10px 24px rgba(47,93,61,.14)}
.ef-back svg{width:17px;height:17px}
.ef-hero{
  position:relative;
  min-height:292px;
  overflow:hidden;
  background:
    linear-gradient(to bottom,rgba(20,48,28,.03),rgba(20,48,28,.12)),
    url("<?= base_url('assets/images/efactor-ecosystem.jpg') ?>") center 32%/cover no-repeat;
}
.ef-hero::after{
  content:"";
  position:absolute;
  left:-12%;
  right:-12%;
  bottom:-78px;
  height:136px;
  border-radius:50% 50% 0 0/100% 100% 0 0;
  background:#fff;
}
.ef-brand{position:absolute;z-index:4;top:35px;right:24px;display:flex;align-items:center}
.ef-brand img{max-width:104px;max-height:38px;object-fit:contain;border-radius:8px;box-shadow:0 8px 18px rgba(0,0,0,.06)}
.ef-brand-fallback{color:#fff;font-size:1.08rem;font-weight:900;text-shadow:0 4px 18px rgba(0,0,0,.28)}
.ef-card{
  position:relative;
  z-index:7;
  margin-top:-61px;
  padding:0 28px 27px;
  background:#fff;
  animation:efSlideUp .58s ease both;
}
.ef-heading-wrap{position:relative;text-align:center;margin-bottom:21px}
.ef-title{margin:0;color:var(--ef-green);font-size:1.72rem;line-height:1.15;font-weight:850;letter-spacing:-.03em}
.ef-subtitle{margin:6px 0 0;color:#A3AAA5;font-size:.73rem;line-height:1.45}
.ef-leaf-accent{
  position:absolute;
  top:-20px;
  right:-15px;
  width:66px;
  height:66px;
  border-radius:55% 45% 62% 38%;
  transform:rotate(-18deg);
  background:url("<?= base_url('assets/images/efactor-ecosystem.jpg') ?>") 72% 28%/190px auto no-repeat;
  filter:saturate(1.05) contrast(1.02);
  box-shadow:0 10px 20px rgba(47,93,61,.08);
  animation:efLeaf 4.8s ease-in-out infinite;
}
.ef-field{margin-bottom:12px}
.ef-input{
  min-height:50px;
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
.ef-control{flex:1;width:100%;min-width:0;border:0;outline:0;padding:14px 0;color:#365140;background:transparent;font-size:.81rem;-webkit-appearance:none;appearance:none}
.ef-control::placeholder{color:#7F9185}
.ef-toggle{width:34px;height:34px;display:grid;place-items:center;padding:0;border:0;border-radius:50%;color:#55735D;background:transparent;cursor:pointer;transition:background .2s ease,transform .2s ease}
.ef-toggle:hover{background:rgba(255,255,255,.6);transform:scale(1.04)}.ef-toggle svg{width:17px;height:17px}
.ef-error{min-height:14px;margin-top:4px;color:var(--ef-danger);font-size:.67rem}
.ef-row{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:0 1px 17px;color:#929B95;font-size:.65rem}
.ef-remember{display:inline-flex;align-items:center;gap:6px}.ef-remember input{width:13px;height:13px;accent-color:var(--ef-green)}
.ef-forgot{color:var(--ef-green);font-weight:750;white-space:nowrap}
.ef-link{color:var(--ef-green);font-weight:800;text-decoration:none}.ef-link:hover{text-decoration:underline}
.ef-submit{
  width:100%;
  min-height:50px;
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
  font-size:.8rem;
  font-weight:800;
  transition:transform .22s ease,box-shadow .22s ease,filter .22s ease;
}
.ef-submit:hover{transform:translateY(-2px) scale(1.006);box-shadow:0 16px 32px rgba(61,122,82,.28);filter:brightness(1.03)}
.ef-submit:active{transform:scale(.985)}
.ef-footer{padding:4px 24px 25px;color:#9BA39D;text-align:center;background:#fff;font-size:.69rem}
.ef-alert{display:flex;gap:8px;margin-bottom:12px;padding:9px 10px;border-radius:12px;font-size:.71rem;line-height:1.4}
.ef-alert-success{color:#27603A;border:1px solid #C9E3CF;background:#F0F8F2}.ef-alert-danger{color:#A73F3F;border:1px solid #EFCACA;background:#FFF4F4}
@keyframes efSlideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
@keyframes efLeaf{0%,100%{transform:rotate(-18deg) translateY(0)}50%{transform:rotate(-14deg) translateY(-5px)}}
@media(max-width:430px){
  body{background:#fff}
  .ef-page{display:block;min-height:100dvh;padding:0}
  .ef-phone{width:100%;height:100dvh;min-height:100dvh;padding:0;border-radius:0;box-shadow:none}
  .ef-phone::before{display:none}
  .ef-screen{border-radius:0}
  .ef-back{top:18px}
  .ef-hero{min-height:280px}
}
@media(prefers-reduced-motion:reduce){.ef-card,.ef-leaf-accent{animation:none}.ef-back,.ef-input,.ef-toggle,.ef-submit{transition:none}}
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

      <main class="ef-card" aria-labelledby="loginTitle">
        <div class="ef-heading-wrap">
          <div class="ef-leaf-accent" aria-hidden="true"></div>
          <h1 class="ef-title" id="loginTitle">Welcome Back</h1>
          <p class="ef-subtitle">Login to your account</p>
        </div>

        <?php if (session('success')): ?>
          <div class="ef-alert ef-alert-success">
            <span><?= esc(session('success')) ?></span>
          </div>
        <?php endif; ?>

        <?php if (session('error')): ?>
          <div class="ef-alert ef-alert-danger">
            <span><?= esc(session('error')) ?></span>
          </div>
        <?php endif; ?>

        <form action="<?= site_url('sd-login') ?>" method="post" novalidate>
          <?= csrf_field() ?>

          <div class="ef-field">
            <div class="ef-input <?= ($validation && $validation->hasError('identity')) ? 'has-error' : '' ?>">
              <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 6.5h16v11H4zM4.5 7l7.5 5.5L19.5 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <input
                class="ef-control"
                id="identity"
                name="identity"
                type="text"
                value="<?= old('identity') ?>"
                autocomplete="username"
                placeholder="Email or mobile number"
                maxlength="191"
                required
              >
            </div>
            <div class="ef-error"><?= ($validation ? $validation->getError('identity') : '') ?></div>
          </div>

          <div class="ef-field">
            <div class="ef-input <?= ($validation && $validation->hasError('password')) ? 'has-error' : '' ?>">
              <svg class="ef-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/>
                <path d="M8 10V7a4 4 0 018 0v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
              </svg>
              <input
                class="ef-control"
                id="login_password"
                name="password"
                type="password"
                autocomplete="current-password"
                placeholder="Password"
                maxlength="72"
                required
              >
              <button
                class="ef-toggle"
                type="button"
                data-password-toggle="login_password"
                aria-label="Show password"
              >
                <svg data-eye viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M2.8 12s3.3-5 9.2-5 9.2 5 9.2 5-3.3 5-9.2 5-9.2-5-9.2-5z" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="12" cy="12" r="2.3" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <svg data-eye-off style="display:none" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M3 3l18 18M10.6 7.2A9.7 9.7 0 0112 7c5.9 0 9.2 5 9.2 5a12.6 12.6 0 01-2.7 3.1M6.1 6.6C4 8 2.8 12 2.8 12s3.3 5 9.2 5c1 0 1.9-.1 2.8-.4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
              </button>
            </div>
            <div class="ef-error"><?= ($validation ? $validation->getError('password') : '') ?></div>
          </div>

          <div class="ef-row">
            <label class="ef-remember">
              <input type="checkbox" name="remember_ui" value="1">
              <span>Remember me</span>
            </label>
            <span class="ef-forgot">Forgot Password ?</span>
          </div>

          <button class="ef-submit" type="submit">
            <span>Login</span>
          </button>
        </form>
      </main>

      <footer class="ef-footer">
        Don’t have an account?
        <a class="ef-link" href="<?= site_url('sd-signup') ?>">Sign up</a>
      </footer>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
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
});
</script>

<?= $this->endSection() ?>
