<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xray System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-header {
            background: var(--primary-color);
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
        }
        
        .login-header h2 {
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .login-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--secondary-color);
        }
        
        .login-body {
            padding: 30px;
        }
        
        .role-badges {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        
        .role-badge {
            background: var(--light-color);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group-text {
            background: var(--light-color);
            border: 1px solid #ddd;
            border-right: none;
            border-radius: 6px 0 0 6px;
        }
        
        .form-control {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 0 6px 6px 0;
            font-size: 16px;
            transition: all 0.3s;
            height: 48px;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .btn-login {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            width: 100%;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-login:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert {
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 20px;
            display: none;
        }
        
        .system-info {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        
        .support-info {
            margin-top: 15px;
            font-size: 13px;
            color: #888;
            text-align: center;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #777;
            cursor: pointer;
            z-index: 5;
        }
        
        @media (max-width: 480px) {
            .login-container {
                max-width: 100%;
            }
            
            .login-header {
                padding: 20px;
            }
            
            .login-body {
                padding: 20px;
            }
            
            .role-badges {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2><i class="fas fa-x-ray"></i> Xray Information System</h2>
        </div>
        
        <div class="login-body">
            <div class="role-badges">
                <div class="role-badge">
                    <i class="fas fa-user-md"></i> X-Ray Technician
                </div>
                <div class="role-badge">
                    <i class="fas fa-stethoscope"></i> Medical Doctor
                </div>
                <div class="role-badge">
                    <i class="fas fa-tooth"></i> Dentist
                </div>
            </div>
            
            <div id="error-message" class="alert alert-danger">
                <!-- Error messages will be displayed here -->
            </div>
            
            <form id="loginForm" method="post" action="<?php echo site_url('manage/external_auth/process_login'); ?>">
                <div class="form-group">
                    <label for="username" class="form-label">Staff ID</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required 
                               placeholder="Enter your staff ID" autocomplete="username">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Enter your password" autocomplete="current-password">
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login to System
                </button>
            </form>
            
            <div class="system-info">
                <strong>Role-Based Access System</strong><br>
                Secure authentication for medical staff
            </div>
            
            <div class="support-info">
                For assistance, contact your system administrator
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Check for error messages from session flashdata
            <?php if ($this->session->flashdata('error')): ?>
                showError('<?php echo $this->session->flashdata("error"); ?>');
            <?php endif; ?>
            
            // Toggle password visibility
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                
                // Toggle eye icon
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });
            
            // Form validation
            $('#loginForm').on('submit', function(e) {
                const username = $('#username').val().trim();
                const password = $('#password').val();
                
                if (!username) {
                    e.preventDefault();
                    showError('Please enter your staff ID');
                    $('#username').focus();
                    return;
                }
                
                if (!password) {
                    e.preventDefault();
                    showError('Please enter your password');
                    $('#password').focus();
                    return;
                }
                
                // Show loading state
                const loginBtn = $(this).find('.btn-login');
                loginBtn.html('<i class="fas fa-spinner fa-spin"></i> Authenticating...');
                loginBtn.prop('disabled', true);
            });
            
            function showError(message) {
                const errorDiv = $('#error-message');
                errorDiv.text(message).show();
                
                // Auto-hide error after 5 seconds
                setTimeout(function() {
                    errorDiv.fadeOut();
                }, 5000);
            }
            
            // Clear error when user starts typing
            $('#username, #password').on('input', function() {
                $('#error-message').hide();
            });
        });
    </script>
</body>
</html>