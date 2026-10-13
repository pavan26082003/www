<?php
// app/Views/auth/register.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EFACTOR</title>
    <link rel="stylesheet" href="<?php echo $basePath ?? '/app/file-manager-v3'; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .auth-box {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 420px;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .auth-header img {
            height: 48px;
            width: auto;
            margin-bottom: 10px;
        }
        .auth-header h2 {
            color: #202124;
            margin-top: 10px;
            font-weight: 500;
        }
        .auth-header p {
            color: #5f6368;
            margin-top: 5px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #202124;
            font-weight: 500;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #dadce0;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.2s;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #1a73e8;
            box-shadow: 0 0 0 3px rgba(26,115,232,0.1);
        }
        .form-group .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        .form-group .input-group i {
            position: absolute;
            left: 14px;
            color: #5f6368;
            font-size: 16px;
        }
        .form-group .input-group input {
            padding-left: 42px;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background: #1557b0;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26,115,232,0.3);
        }
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .auth-links {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #5f6368;
        }
        .auth-links a {
            color: #1a73e8;
            text-decoration: none;
            font-weight: 500;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
        .message {
            padding: 10px 15px;
            border-radius: 6px;
            margin-top: 15px;
            display: none;
            font-size: 14px;
        }
        .message.error {
            display: block;
            background: #fce8e6;
            color: #d93025;
            border: 1px solid #fad2cf;
        }
        .message.success {
            display: block;
            background: #e6f4ea;
            color: #1e7e34;
            border: 1px solid #c8e6c9;
        }
        .password-strength {
            height: 4px;
            margin-top: 6px;
            border-radius: 2px;
            background: #dadce0;
            overflow: hidden;
            transition: all 0.3s;
        }
        .password-strength .bar {
            height: 100%;
            width: 0%;
            border-radius: 2px;
            transition: width 0.3s;
        }
        .password-strength .bar.weak { background: #ea4335; width: 25%; }
        .password-strength .bar.medium { background: #fbbc04; width: 50%; }
        .password-strength .bar.strong { background: #34a853; width: 75%; }
        .password-strength .bar.very-strong { background: #1a73e8; width: 100%; }
        .password-hint {
            font-size: 12px;
            color: #5f6368;
            margin-top: 4px;
        }
        .register-success {
            text-align: center;
            padding: 20px;
        }
        .register-success i {
            font-size: 64px;
            color: #34a853;
        }
        .register-success h3 {
            margin-top: 16px;
            color: #202124;
        }
        .register-success p {
            color: #5f6368;
            margin-top: 8px;
        }
        .register-success .btn-primary {
            margin-top: 20px;
            max-width: 200px;
        }
        .password-toggle {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #5f6368;
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
        }
        .password-toggle:hover {
            color: #202124;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <img src="<?php echo $basePath ?? '/app/file-manager-v3'; ?>/assets/images/efactor.png" alt="eFactor">
                <h2>Create Account</h2>
                <p>Get started with your free account</p>
            </div>
            
            <form id="registerForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" placeholder="Choose a username" required autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Create a password" required minlength="6">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="bar" id="passwordStrengthBar"></div>
                    </div>
                    <div class="password-hint" id="passwordHint">Minimum 6 characters</div>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary" id="registerBtn">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
                
                <div id="registerMessage" class="message"></div>
            </form>
            
            <div class="auth-links">
                Already have an account? <a href="<?php echo $basePath ?? '/app/file-manager-v3'; ?>/login">Sign In</a>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '/app/file-manager-v3/api/v1';
        const BASE_PATH = '/app/file-manager-v3';
        
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const bar = document.getElementById('passwordStrengthBar');
            const hint = document.getElementById('passwordHint');
            
            if (password.length === 0) {
                bar.className = 'bar';
                hint.textContent = 'Minimum 6 characters';
                return;
            }
            
            let strength = 0;
            
            // Length check
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            
            // Character type checks
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            let level = 'weak';
            let text = 'Weak';
            
            if (strength >= 6) {
                level = 'very-strong';
                text = 'Very Strong';
            } else if (strength >= 5) {
                level = 'strong';
                text = 'Strong';
            } else if (strength >= 3) {
                level = 'medium';
                text = 'Medium';
            }
            
            bar.className = 'bar ' + level;
            hint.textContent = 'Password strength: ' + text;
        });
        
        // Password toggle
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const icon = input.parentElement.querySelector('.password-toggle i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
        
        // Form submission
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const messageEl = document.getElementById('registerMessage');
            const submitBtn = document.getElementById('registerBtn');
            
            // Validation
            if (!username || username.length < 3) {
                messageEl.className = 'message error';
                messageEl.textContent = 'Username must be at least 3 characters';
                return;
            }
            
            if (!email || !email.includes('@')) {
                messageEl.className = 'message error';
                messageEl.textContent = 'Please enter a valid email address';
                return;
            }
            
            if (password.length < 6) {
                messageEl.className = 'message error';
                messageEl.textContent = 'Password must be at least 6 characters';
                return;
            }
            
            if (password !== confirmPassword) {
                messageEl.className = 'message error';
                messageEl.textContent = 'Passwords do not match';
                return;
            }
            
            // Show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating account...';
            messageEl.className = 'message';
            messageEl.style.display = 'none';
            
            // Send registration request
            fetch(API_BASE + '/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: username,
                    email: email,
                    password: password
                }),
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    messageEl.className = 'message success';
                    messageEl.style.display = 'block';
                    messageEl.innerHTML = `
                        <i class="fas fa-check-circle"></i> 
                        Account created successfully! 
                        <br>Redirecting to dashboard...
                    `;
                    
                    // Redirect to dashboard after 2 seconds
                    setTimeout(function() {
                        window.location.href = BASE_PATH + '/dashboard';
                    }, 2000);
                } else {
                    // Show error
                    messageEl.className = 'message error';
                    messageEl.style.display = 'block';
                    messageEl.textContent = data.message || 'Registration failed. Please try again.';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
                }
            })
            .catch(function(error) {
                console.error('Registration error:', error);
                messageEl.className = 'message error';
                messageEl.style.display = 'block';
                messageEl.textContent = 'Connection error. Please try again.';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
            });
        });
        
        // Enter key support
        document.getElementById('confirmPassword').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('registerForm').dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>
</html>