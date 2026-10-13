<?= $this->extend('layouts/sd_auth') ?>
<?= $this->section('content') ?>

<style>
:root{
  --ef-green:#202940;
  --ef-green-dark:#1F150C;
  --ef-text:#fff;
}
*{box-sizing:border-box}
html{min-width:320px;-webkit-text-size-adjust:100%}
body{
  margin:0;
  min-width:320px;
  overflow-x:hidden;
  font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
  background:
    linear-gradient(100deg,rgba(241,246,242,.82),rgba(233,241,235,.67)),
    url("<?= base_url('assets/images/efactor-ecosystem.jpg') ?>") center/cover fixed no-repeat;
}
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
  box-shadow:0 30px 80px rgba(28,62,39,.28);
}
.ef-phone::before{
  content:"";
  position:absolute;
  z-index:20;
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
  overflow:hidden;
  border-radius:34px;
  background:
    linear-gradient(to bottom,rgba(15,42,24,.10),rgba(11,39,21,.48) 58%,rgba(8,29,15,.82)),
    url("<?= base_url('assets/images/efactor-ecosystem.jpg') ?>") center/cover no-repeat;
}
.ef-screen::after{
  content:"";
  position:absolute;
  inset:0;
  pointer-events:none;
  background:
    radial-gradient(circle at 78% 18%,rgba(255,255,255,.15),transparent 24%),
    linear-gradient(to top,rgba(7,28,14,.35),transparent 46%);
}
.ef-brand{
  position:absolute;
  z-index:4;
  top:38px;
  left:24px;
  display:flex;
  align-items:center;
  gap:10px;
}
.ef-brand img{
  width:132px;
  max-height:46px;
  object-fit:contain;
  border-radius:8px;
}
.ef-brand-fallback{
  color:#fff;
  font-size:1.18rem;
  font-weight:900;
  text-shadow:0 4px 18px rgba(0,0,0,.28);
}
.ef-copy{
  position:absolute;
  z-index:4;
  left:27px;
  right:27px;
  bottom:182px;
}
.ef-kicker{
  display:inline-flex;
  align-items:center;
  gap:7px;
  margin-bottom:13px;
  padding:7px 10px;
  border:1px solid rgba(255,255,255,.18);
  border-radius:99px;
  color:rgba(255,255,255,.88);
  background:rgba(255,255,255,.10);
  backdrop-filter:blur(12px);
  font-size:.68rem;
  font-weight:700;
}
.ef-dot{width:6px;height:6px;border-radius:50%;background:#a8d6b0;box-shadow:0 0 0 5px rgba(168,214,176,.12)}
.ef-title{
  max-width:305px;
  margin:0;
  color:#fff;
  font-size:2.14rem;
  line-height:1.08;
  letter-spacing:-.045em;
  text-shadow:0 8px 28px rgba(0,0,0,.32);
}
.ef-description{
  max-width:315px;
  margin:13px 0 0;
  color:rgba(255,255,255,.78);
  font-size:.77rem;
  line-height:1.55;
}
.ef-actions{
  position:absolute;
  z-index:5;
  left:20px;
  right:20px;
  bottom:70px;
}
.ef-primary{
  width:100%;
  min-height:50px;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  border:1px solid rgba(255,255,255,.24);
  border-radius:18px;
  color:#fff;
  background:linear-gradient(135deg,rgba(32,41,64,.92),rgba(31,21,12,.88));
  backdrop-filter:blur(18px);
  box-shadow:0 16px 38px rgba(0,0,0,.28),inset 0 1px 0 rgba(255,255,255,.16);
  text-decoration:none;
  font-size:.8rem;
  font-weight:800;
  transition:transform .2s ease,box-shadow .2s ease;
}
.ef-primary:hover{transform:translateY(-2px);box-shadow:0 18px 42px rgba(4,27,12,.30)}
.ef-secondary{
  display:block;
  margin-top:13px;
  padding:8px 4px;
  color:#fff;
  text-align:center;
  text-decoration:none;
  font-size:.72rem;
  font-weight:700;
}
.ef-secondary span{
  padding-bottom:2px;
  border-bottom:1px solid rgba(255,255,255,.45);
}

/* =========================================================
   WEB ONLY - AUTH SDUI SCHEMA GENERATOR
   This control stays OUTSIDE the mobile phone preview.
   ========================================================= */
.ef-web-schema-tools{
  position:fixed;
  top:24px;
  left:24px;
  z-index:9999;
  display:flex;
  flex-direction:column;
  align-items:flex-start;
  gap:10px;
}
.ef-web-schema-btn{
  min-height:46px;
  padding:0 18px;
  display:flex;
  align-items:center;
  justify-content:center;
  gap:9px;
  border:1px solid rgba(61,122,82,.18);
  border-radius:14px;
  color:#fff;
  background:linear-gradient(135deg,#202940,#1F150C);
  box-shadow:0 12px 30px rgba(47,93,61,.22);
  font-size:.78rem;
  font-weight:800;
  cursor:pointer;
  transition:transform .2s ease,box-shadow .2s ease,opacity .2s ease;
}
.ef-web-schema-btn:hover{
  transform:translateY(-2px);
  box-shadow:0 16px 36px rgba(47,93,61,.30);
}
.ef-web-schema-btn:active{transform:scale(.98)}
.ef-web-schema-btn:disabled{
  opacity:.65;
  cursor:not-allowed;
  transform:none;
}
.ef-web-schema-icon{font-size:15px}
.ef-web-schema-spinner{
  display:none;
  width:14px;
  height:14px;
  border:2px solid rgba(255,255,255,.35);
  border-top-color:#fff;
  border-radius:50%;
  animation:efSchemaSpin .7s linear infinite;
}
.ef-web-schema-spinner.is-visible{display:inline-block}
@keyframes efSchemaSpin{to{transform:rotate(360deg)}}
.ef-web-schema-status{
  max-width:320px;
  padding:11px 14px;
  border-radius:12px;
  font-size:.72rem;
  line-height:1.45;
  box-shadow:0 12px 30px rgba(0,0,0,.10);
}
.ef-web-schema-status.is-success{
  color:#235432;
  background:#EAF6ED;
  border:1px solid #BBDDC3;
}
.ef-web-schema-status.is-error{
  color:#9B3434;
  background:#FFF0F0;
  border:1px solid #F0BABA;
}

.ef-legal-links{
  position:absolute;
  z-index:6;
  left:20px;
  right:20px;
  bottom:calc(14px + env(safe-area-inset-bottom));
  display:flex;
  justify-content:center;
  gap:12px;
  flex-wrap:wrap;
  font-size:.6rem;
}
.ef-legal-links a{color:rgba(255,255,255,.82);text-decoration:none;font-weight:700}.ef-legal-links a:hover{text-decoration:underline}
@media(max-width:430px){
  .ef-web-schema-tools{display:none !important}
  body{background:#0f2a18}
  .ef-page{display:block;min-height:100dvh;padding:0}
  .ef-phone{width:100%;height:100dvh;min-height:100dvh;padding:0;border-radius:0;box-shadow:none}
  .ef-phone::before{display:none}
  .ef-screen{border-radius:0}
  .ef-brand{top:25px}.ef-actions{bottom:74px}.ef-legal-links{bottom:calc(16px + env(safe-area-inset-bottom));gap:10px}
}
</style>

<div class="ef-page">

  <!-- WEB ONLY: Schema generator. Hidden on mobile. -->
  <div class="ef-web-schema-tools" aria-label="Schema generator tools">
    <button
      type="button"
      id="generateAuthSchemaBtn"
      class="ef-web-schema-btn"
      aria-label="Generate and store app schema"
    >
      <!-- <span class="ef-web-schema-icon" aria-hidden="true">âš¡</span> -->
      <span id="generateSchemaText">Generate App Schema</span>
      <span id="generateSchemaSpinner" class="ef-web-schema-spinner" aria-hidden="true"></span>
    </button>

    <div
      id="schemaGenerateStatus"
      class="ef-web-schema-status"
      role="status"
      aria-live="polite"
      hidden
    ></div>
  </div>

  <div class="ef-phone">
    <main class="ef-screen" aria-labelledby="welcomeTitle">
      <div class="ef-brand">
        <img src="<?= base_url('assets/images/efactor-logo.jpg') ?>"
             alt="Efactor"
             onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
        <!-- <span class="ef-brand-fallback" style="display:none">efactor</span> -->
      </div>

      <div class="ef-copy">
        <h1 class="ef-title" id="welcomeTitle">EFACTOR GRID One Platform. Infinite Possibilities</h1>
        <p class="ef-description">Secure device access, account management, and support resources in one professional workspace.</p>
      </div>

      <div class="ef-actions">
        <a class="ef-primary" href="<?= site_url('sd-login') ?>">
          <span>Log-in</span>
        </a>
        <a class="ef-secondary" href="<?= site_url('sd-signup') ?>"><span>Create an account</span></a>
      </div>

      <nav class="ef-legal-links" aria-label="Legal and documentation links">
        <a href="https://efactor.tech/index.php/knowledge_base">Documentation</a>
        <a href="https://efactor.tech/privacy_policy.html">Privacy Policy</a>
        <a href="https://efactor.tech/user_agreement.html">Term of Use</a>
      </nav>
    </main>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
  const button = document.getElementById('generateAuthSchemaBtn');
  const buttonText = document.getElementById('generateSchemaText');
  const spinner = document.getElementById('generateSchemaSpinner');
  const statusBox = document.getElementById('schemaGenerateStatus');

  if (!button || !buttonText || !spinner || !statusBox) {
    return;
  }

  button.addEventListener('click', async function () {
    button.disabled = true;
    buttonText.textContent = 'Generating Schema...';
    spinner.classList.add('is-visible');

    statusBox.hidden = true;
    statusBox.className = 'ef-web-schema-status';
    statusBox.textContent = '';

    try {
      const response = await fetch(
        '<?= base_url('v8/api_auth_schema.php') ?>',
        {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            force: true,
            csrfTokenName: '<?= csrf_token() ?>',
            csrfTokenValue: '<?= csrf_hash() ?>'
          })
        }
      );

      let result;

      try {
        result = await response.json();
      } catch (jsonError) {
        throw new Error('Server returned an invalid JSON response.');
      }

      if (!response.ok || !result || (result.success !== true && result.status !== true)) {
        throw new Error(
          result && result.message
            ? result.message
            : 'Unable to generate schema.'
        );
      }

      const changed = result.changed === true;
      const apiMessage = (result.message && String(result.message)) || (
        changed
          ? 'Auth UI schema generated and stored successfully.'
          : 'Auth UI schema already up to date; no new revision created.'
      );
      const revision = result.data && result.data.schema_version
        ? result.data.schema_version
        : (result.schema && result.schema.revision ? result.schema.revision : '');

      statusBox.hidden = false;
      statusBox.className = 'ef-web-schema-status is-success';
      statusBox.textContent = apiMessage + (revision !== '' ? ' (revision ' + revision + ')' : '');

      alert(
        apiMessage + '\n\n' +
        (changed
          ? 'Welcome, Login and Signup SDUI schema has been stored in the database.'
          : 'No new database revision was written.')
      );

      console.log('Generated Auth UI Schema:', result);

    } catch (error) {
      console.error('Auth schema generation failed:', error);

      statusBox.hidden = false;
      statusBox.className = 'ef-web-schema-status is-error';
      statusBox.textContent =
        'Schema generation failed: ' +
        (error && error.message ? error.message : 'Unknown error');

      alert(
        'Schema generation failed.\n\n' +
        (error && error.message ? error.message : 'Unknown error')
      );

    } finally {
      button.disabled = false;
      buttonText.textContent = 'Generate App Schema';
      spinner.classList.remove('is-visible');
    }
  });
});
</script>

<?= $this->endSection() ?>

