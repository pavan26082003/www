<?php
// app/Views/auth/login.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EFACTOR</title>
    <link rel="stylesheet" href="/app/file-manager-v3/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/app/file-manager-v3/assets/js/config.js"></script>
    <script src="/app/file-manager-v3/assets/js/app.js"></script>
</head>
<body class="login-page">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <img src="/app/file-manager-v3/assets/images/efactor.png" alt="eFactor" style="height:48px;width:auto;">
                <h2>Welcome Back</h2>
                <p>Sign in to access your files</p>
            </div>
            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
                <div class="auth-links">
                    Don't have an account? <a href="<?php echo $basePath ?? '/app/file-manager-v3'; ?>/register">Create one</a>
                </div>
                <div id="loginError" class="message error" style="display:none;"></div>
            </form>
        </div>
    </div>

    <script>
        
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const errorEl = document.getElementById('loginError');
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
            submitBtn.disabled = true;
            
            fetch(API_BASE + '/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password }),
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    window.location.href = '/app/file-manager-v3/home';
                } else {
                    errorEl.textContent = data.message || 'Login failed. Please check your credentials.';
                    errorEl.style.display = 'block';
                }
            })
            .catch((error) => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                errorEl.textContent = 'Connection error. Please try again.';
                errorEl.style.display = 'block';
                console.error('Login error:', error);
            });
        });
    </script>
</body>
</html>