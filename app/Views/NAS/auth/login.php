<?php

/*
|--------------------------------------------------------------------------
| NAS Login View - Phase 1
|--------------------------------------------------------------------------
|
| Independent NAS authentication inside the existing Efactor CodeIgniter app.
| No old /app/file-manager-v3 paths.
| Dashboard app.js/config.js are intentionally NOT loaded on this page.
|
*/

$basePath = isset($basePath) && $basePath !== ''
    ? rtrim($basePath, '/')
    : rtrim(site_url('NAS'), '/');

$apiBase = isset($apiBase) && $apiBase !== ''
    ? rtrim($apiBase, '/')
    : rtrim(site_url('NAS/api/v1'), '/');

$assetsBase = isset($assetsBase) && $assetsBase !== ''
    ? rtrim($assetsBase, '/')
    : rtrim(base_url('assets/NAS'), '/');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - EFACTOR</title>

    <link rel="stylesheet" href="<?= esc($assetsBase) ?>/css/style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="login-page">

<div class="auth-container">
    <div class="auth-box">

        <div class="auth-header">
            <img
                src="<?= esc($assetsBase) ?>/images/efactor.png"
                alt="eFactor"
                style="height:48px;width:auto;"
            >

            <h2>Welcome Back</h2>
            <p>Sign in to access your files</p>
        </div>

        <form id="loginForm">

            <div class="form-group">
                <label for="username">Username or Email</label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username or email"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button
                type="submit"
                class="btn-primary"
                id="loginBtn"
            >
                <i class="fas fa-sign-in-alt"></i>
                Sign In
            </button>

            <div class="auth-links">
                Don't have an account?
                <a href="<?= esc($basePath) ?>/register">Create one</a>
            </div>

            <div
                id="loginError"
                class="message error"
                style="display:none;"
            ></div>

        </form>

    </div>
</div>

<script>
const API_BASE = <?= json_encode($apiBase) ?>;
const BASE_PATH = <?= json_encode($basePath) ?>;

console.log('✅ NAS Login - API_BASE:', API_BASE);
console.log('✅ NAS Login - BASE_PATH:', BASE_PATH);

document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const errorEl = document.getElementById('loginError');
    const submitBtn = document.getElementById('loginBtn');
    const originalText = submitBtn.innerHTML;

    errorEl.style.display = 'none';
    errorEl.textContent = '';

    if (!username || !password) {
        errorEl.textContent = 'Username and password are required.';
        errorEl.style.display = 'block';
        return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Signing in...';

    try {
        const response = await fetch(API_BASE + '/auth/login', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                password: password
            })
        });

        const rawResponse = await response.text();

        console.log('📥 NAS login status:', response.status);
        console.log('📥 NAS login response:', rawResponse);

        let data;

        try {
            data = JSON.parse(rawResponse);
        } catch (parseError) {
            console.error('❌ NAS login returned non-JSON:', rawResponse);
            throw new Error('The login API returned an invalid server response.');
        }

        if (response.ok && data.success) {
            window.location.href = BASE_PATH + '/home';
            return;
        }

        errorEl.textContent =
            data.message || 'Login failed. Please check your credentials.';
        errorEl.style.display = 'block';

    } catch (error) {
        console.error('❌ NAS login error:', error);

        errorEl.textContent =
            error.message || 'Connection error. Please try again.';
        errorEl.style.display = 'block';

    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
</script>

</body>
</html>
