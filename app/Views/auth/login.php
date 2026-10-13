<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — IDS Executive CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        :root {
            /* Palette IDS */
            --p-1: #cce3de;
            --p-2: #a4c3b2;
            --p-3: #6b9080;
            --p-4: #436d63;
            --p-5: #1d4e43;
            --p-6: #0b516c;
            --p-7: #022b3a;
            --dark-base: #001219;
            --blue-accent: #2c5282;
            --bg-outer: #f1f4f9;
            --text-main: #0b1f2e;
            --text-sub: #546b7a;
            --border-field: #d8e2ea;
            --border-focus: #0b516c;
        }
        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-outer);
            background-image: 
                radial-gradient(circle at 10% 15%, rgba(107, 144, 128, 0.18) 0%, transparent 45%),
                radial-gradient(circle at 90% 85%, rgba(11, 81, 108, 0.16) 0%, transparent 45%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
            color: var(--text-main);
        }
        /* Outer Modal Card */
        .login-wrapper {
            background: #ffffff;
            width: 100%;
            max-width: 980px;
            min-height: 580px;
            border-radius: 28px;
            box-shadow: 
                0 28px 64px -12px rgba(11, 81, 108, 0.16),
                0 0 0 1px rgba(11, 81, 108, 0.06);
            display: grid;
            grid-template-columns: 1fr 1.08fr;
            overflow: hidden;
            padding: 16px;
            gap: 20px;
        }

        /* Left Side: Palette Gradient Banner */
        .gradient-panel {
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px 36px;
            color: #ffffff;
            background: 
                radial-gradient(circle at 85% 15%, rgba(204, 227, 222, 0.85) 0%, transparent 45%),
                radial-gradient(circle at 20% 30%, rgba(107, 144, 128, 0.9) 0%, transparent 50%),
                radial-gradient(circle at 75% 65%, rgba(67, 109, 99, 0.95) 0%, transparent 55%),
                radial-gradient(circle at 20% 85%, rgba(11, 81, 108, 1) 0%, transparent 60%),
                linear-gradient(145deg, #022b3a 0%, #0b516c 40%, #1d4e43 75%, #001219 100%);
            box-shadow: inset 0 0 60px rgba(0, 18, 25, 0.4);
        }

        /* Top Brand Asterisk/Star Icon on Gradient Panel */
        .panel-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .panel-star-icon {
            width: 36px;
            height: 36px;
            color: #ffffff;
            opacity: 0.95;
        }

        /* Bottom Text on Gradient Panel */
        .panel-footer {
            margin-top: auto;
            max-width: 380px;
        }
        .panel-kicker {
            font-size: 12px;
            font-weight: 600;
            color: var(--p-1);
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            text-transform: uppercase;
            opacity: 0.9;
        }
        .panel-headline {
            font-size: 24px;
            font-weight: 700;
            line-height: 34px;
            color: #ffffff;
            letter-spacing: -0.3px;
            text-shadow: 0 2px 8px rgba(0, 18, 25, 0.4);
        }

        /* Right Side: Clean Login Form */
        .form-panel {
            padding: 40px 44px 32px 32px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-brand-icon {
            width: 28px;
            height: 28px;
            color: var(--p-6);
            margin-bottom: 16px;
        }

        .form-heading {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.5px;
            line-height: 32px;
            margin-bottom: 8px;
        }

        .form-subheading {
            font-size: 12px;
            color: var(--text-sub);
            line-height: 20px;
            margin-bottom: 28px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 12px;
            margin-bottom: 20px;
            line-height: 18px;
        }
        .alert-error {
            background: #fdf2f2;
            color: #c53030;
            border: 1px solid #feb2b2;
        }
        .alert-success {
            background: #f0fff4;
            color: #276749;
            border: 1px solid #9ae6b4;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
            letter-spacing: 0.2px;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input {
            width: 100%;
            height: 48px;
            padding: 0 16px;
            border: 1.5px solid var(--border-field);
            border-radius: 12px;
            font-size: 16px;
            font-family: inherit;
            color: var(--text-main);
            background: #ffffff;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--p-6);
            box-shadow: 0 0 0 4px rgba(11, 81, 108, 0.12);
        }

        .form-input.has-eye {
            padding-right: 48px;
        }

        .eye-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #718096;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: color 0.15s ease;
        }

        .eye-toggle-btn:hover {
            color: var(--p-6);
        }

        .eye-toggle-btn svg {
            width: 20px;
            height: 20px;
        }

        .btn-submit {
            width: 100%;
            height: 50px;
            background: var(--p-6);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            margin-top: 12px;
            transition: all 0.2s ease;
            box-shadow: 0 8px 20px rgba(11, 81, 108, 0.28);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            background: var(--p-7);
            box-shadow: 0 10px 24px rgba(2, 43, 58, 0.35);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .form-footer {
            margin-top: 28px;
            font-size: 12px;
            color: #a0aec0;
            text-align: center;
        }

        /* Responsive Mobile Layout */
        @media (max-width: 820px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                max-width: 480px;
                padding: 12px;
            }
            .gradient-panel {
                min-height: 220px;
                padding: 24px;
            }
            .panel-headline {
                font-size: 20px;
                line-height: 28px;
            }
            .form-panel {
                padding: 24px 16px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Left Side: Color Palette Gradient Banner -->
        <div class="gradient-panel">
            <div class="panel-header">
                <!-- Asterisk / Star Icon SVG (matching reference) -->
                <svg class="panel-star-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="2" x2="12" y2="22"></line>
                    <line x1="3.34" y1="7" x2="20.66" y2="17"></line>
                    <line x1="3.34" y1="17" x2="20.66" y2="7"></line>
                </svg>
            </div>

            <div class="panel-footer">
                <div class="panel-kicker">Investor Daily Summit 2026</div>
                <h1 class="panel-headline">
                    Executive Portal for Seamless Editorial & Event Management
                </h1>
            </div>
        </div>

        <!-- Right Side: Clean Sign In Form -->
        <div class="form-panel">
            <!-- Asterisk Icon above Title -->
            <svg class="form-brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="2" x2="12" y2="22"></line>
                <line x1="3.34" y1="7" x2="20.66" y2="17"></line>
                <line x1="3.34" y1="17" x2="20.66" y2="7"></line>
            </svg>

            <h2 class="form-heading">Sign in to your account</h2>
            <p class="form-subheading">
                Access your summit workspace, schedules, and live telemetry in one central place.
            </p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/login') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label class="form-label" for="login">Your username or email</label>
                    <div class="input-box">
                        <input type="text" id="login" name="login" class="form-input" autocomplete="username" autofocus placeholder="Enter your username">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-box">
                        <input type="password" id="password" name="password" class="form-input has-eye" required autocomplete="current-password" placeholder="Enter your password">
                        <button type="button" id="togglePasswordBtn" class="eye-toggle-btn" aria-label="Toggle password visibility">
                            <!-- Eye Open Icon (hidden when masked) -->
                            <svg id="eyeOpenIcon" style="display:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <!-- Eye Slash Icon (shown when masked) -->
                            <svg id="eyeSlashIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <?php 
                    $siteKey = env('RECAPTCHA_SITE_KEY') ?: getenv('RECAPTCHA_SITE_KEY');
                    if (!empty($siteKey)): 
                ?>
                    <div style="margin: 18px 0 10px 0; display: flex; justify-content: center;">
                        <div class="g-recaptcha" data-sitekey="<?= esc($siteKey) ?>"></div>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn-submit">
                    <span>Sign In</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </form>

            <div class="form-footer">
                &copy; <?= date('Y') ?> Investor Daily Summit &bull; B-Universe Media
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePasswordBtn').addEventListener('click', function() {
            const pwd = document.getElementById('password');
            const openIcon = document.getElementById('eyeOpenIcon');
            const slashIcon = document.getElementById('eyeSlashIcon');

            if (pwd.type === 'password') {
                pwd.type = 'text';
                openIcon.style.display = 'block';
                slashIcon.style.display = 'none';
            } else {
                pwd.type = 'password';
                openIcon.style.display = 'none';
                slashIcon.style.display = 'block';
            }
        });
    </script>
</body>
</html>
