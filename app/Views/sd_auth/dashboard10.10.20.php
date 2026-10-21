<?= $this->extend('layouts/sd_auth') ?>
<?= $this->section('content') ?>

<style>
body{margin:0;font-family:Inter,ui-sans-serif,system-ui,sans-serif;background:#E8EDF2;color:#202940}
.ef-dash{min-height:100vh;display:grid;place-items:center;padding:24px}
.ef-card{width:min(100%,420px);padding:28px 24px;border-radius:24px;background:#fff;box-shadow:0 16px 40px rgba(32,41,64,.12)}
.ef-card h1{margin:0 0 8px;font-size:1.6rem}
.ef-card p{margin:0 0 18px;color:#64748b}
.ef-actions{display:flex;gap:10px;flex-wrap:wrap}
.ef-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:0 16px;border-radius:12px;text-decoration:none;font-weight:700}
.ef-primary{color:#fff;background:#202940}
.ef-ghost{color:#202940;border:1px solid #cbd5e1}
</style>

<div class="ef-dash">
    <div class="ef-card">
        <h1><?= esc($title ?? 'Dashboard') ?></h1>
        <p>Signed in as <?= esc($email ?? '') ?></p>
        <div class="ef-actions">
            <a class="ef-primary" href="<?= site_url('app-home-page') ?>">Open App Home</a>
            <form method="post" action="<?= site_url('sd-logout') ?>"><?= csrf_field() ?><button class="ef-ghost" type="submit" style="min-height:44px;padding:0 16px;border-radius:12px;border:1px solid #cbd5e1;background:#fff;font-weight:700;cursor:pointer">Sign out</button></form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
