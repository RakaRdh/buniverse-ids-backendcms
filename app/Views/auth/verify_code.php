<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication — IDS Executive CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        :root {
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
            --danger-bg: #fef2f2;
            --danger-border: #fecaca;
            --danger-text: #991b1b;
            --success-bg: #f0fdf4;
            --success-border: #bbf7d0;
            --success-text: #166534;
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
        .verify-card {
            background: #ffffff;
            width: 100%;
            max-width: 520px;
            border-radius: 24px;
            box-shadow: 
                0 24px 60px -12px rgba(11, 81, 108, 0.18),
                0 0 0 1px rgba(11, 81, 108, 0.06);
            padding: 40px 36px;
            position: relative;
            overflow: hidden;
        }
        .card-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(11, 81, 108, 0.08);
            color: var(--p-6);
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .card-badge svg {
            width: 14px;
            height: 14px;
        }
        .card-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }
        .card-desc {
            font-size: 13.5px;
            color: var(--text-sub);
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .email-highlight {
            font-weight: 700;
            color: var(--p-6);
            background: rgba(204, 227, 222, 0.35);
            padding: 2px 6px;
            border-radius: 4px;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-error {
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: var(--danger-text);
        }
        .alert-success {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--success-text);
        }
        .otp-inputs-wrapper {
            display: flex;
            gap: 10px;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .otp-digit {
            width: 58px;
            height: 64px;
            border-radius: 12px;
            border: 1.5px solid var(--border-field);
            background: #f8fafc;
            font-family: 'JetBrains Mono', monospace;
            font-size: 26px;
            font-weight: 800;
            text-align: center;
            color: var(--p-7);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
        }
        .otp-digit:focus {
            border-color: var(--p-6);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(11, 81, 108, 0.12);
            transform: translateY(-2px);
        }
        .otp-digit.filled {
            border-color: var(--p-4);
            background: #ffffff;
        }
        .btn-submit {
            width: 100%;
            height: 52px;
            background: linear-gradient(135deg, var(--p-6) 0%, var(--p-7) 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 700;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 20px -4px rgba(11, 81, 108, 0.3);
        }
        .btn-submit:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 12px 24px -4px rgba(11, 81, 108, 0.4);
        }
        .btn-submit:active {
            transform: translateY(0);
        }
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .resend-section {
            margin-top: 24px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(11, 81, 108, 0.08);
            font-size: 13px;
            color: var(--text-sub);
        }
        .resend-btn {
            background: none;
            border: none;
            color: var(--p-6);
            font-weight: 700;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-size: 13px;
            font-family: inherit;
        }
        .resend-btn:hover:not(:disabled) {
            background: rgba(11, 81, 108, 0.08);
            text-decoration: underline;
        }
        .resend-btn:disabled {
            color: #94a3b8;
            cursor: not-allowed;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 16px;
            font-size: 12.5px;
            color: var(--text-sub);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .back-link:hover {
            color: var(--p-6);
        }
        .notice-box {
            margin-top: 20px;
            background: rgba(204, 227, 222, 0.25);
            border: 1px dashed rgba(67, 109, 99, 0.4);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            color: var(--p-5);
            line-height: 1.5;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .notice-box svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        @media (max-width: 480px) {
            .verify-card {
                padding: 30px 20px;
            }
            .otp-inputs-wrapper {
                gap: 6px;
            }
            .otp-digit {
                width: 44px;
                height: 52px;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="card-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Two-Step Verification
        </div>

        <h1 class="card-title">Enter Security Code</h1>
        <p class="card-desc">
            A 6-digit verification code has been sent to <span class="email-highlight"><?= esc($maskedEmail ?? 'your registered email') ?></span>. Please enter the code below to complete sign in.
        </p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span><?= esc(session()->getFlashdata('error')) ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span><?= esc(session()->getFlashdata('success')) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/verify-code') ?>" method="POST" id="verifyForm">
            <?= csrf_field() ?>
            <input type="hidden" name="otp_code" id="hiddenOtpInput">

            <div class="otp-inputs-wrapper" id="otpContainer">
                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" autofocus data-index="0">
                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" data-index="1">
                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" data-index="2">
                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" data-index="3">
                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" data-index="4">
                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" data-index="5">
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <span>Verify & Sign In</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </form>

        <div class="notice-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <div>The code expires in <strong>10 minutes</strong>. Didn't receive it? Check your <strong>Spam</strong> or <strong>Promotions</strong> folder.</div>
        </div>

        <div class="resend-section">
            <form action="<?= base_url('auth/resend-code') ?>" method="POST" id="resendForm" style="display:inline;">
                <?= csrf_field() ?>
                <span>Didn't get the code? </span>
                <button type="submit" class="resend-btn" id="resendBtn">Resend Code</button>
            </form>
            <div>
                <a href="<?= base_url('auth/login') ?>" class="back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Back to Sign In
                </a>
            </div>
        </div>
    </div>

    <script>
        const digits = Array.from(document.querySelectorAll('.otp-digit'));
        const hiddenInput = document.getElementById('hiddenOtpInput');
        const form = document.getElementById('verifyForm');
        const resendBtn = document.getElementById('resendBtn');

        function updateHiddenInput() {
            const code = digits.map(d => d.value).join('');
            hiddenInput.value = code;
            return code;
        }

        digits.forEach((digit, idx) => {
            digit.addEventListener('input', (e) => {
                const val = e.target.value.replace(/[^0-9]/g, '');
                digit.value = val;
                if (val) {
                    digit.classList.add('filled');
                    if (idx < digits.length - 1) {
                        digits[idx + 1].focus();
                    }
                } else {
                    digit.classList.remove('filled');
                }

                const fullCode = updateHiddenInput();
                if (fullCode.length === 6) {
                    form.submit();
                }
            });

            digit.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !digit.value && idx > 0) {
                    digits[idx - 1].focus();
                    digits[idx - 1].value = '';
                    digits[idx - 1].classList.remove('filled');
                    updateHiddenInput();
                }
            });

            digit.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                const cleanDigits = pasteData.replace(/[^0-9]/g, '').slice(0, 6);
                if (!cleanDigits) return;

                cleanDigits.split('').forEach((char, i) => {
                    if (digits[i]) {
                        digits[i].value = char;
                        digits[i].classList.add('filled');
                    }
                });

                const targetIdx = Math.min(cleanDigits.length, digits.length - 1);
                digits[targetIdx].focus();
                
                const fullCode = updateHiddenInput();
                if (fullCode.length === 6) {
                    form.submit();
                }
            });
        });

        // Resend Timer Countdown (60 seconds)
        let countdown = 60;
        const cooldownActive = sessionStorage.getItem('2fa_resend_cooldown');
        if (cooldownActive && Date.now() < parseInt(cooldownActive)) {
            countdown = Math.ceil((parseInt(cooldownActive) - Date.now()) / 1000);
            startCountdown();
        }

        document.getElementById('resendForm').addEventListener('submit', () => {
            sessionStorage.setItem('2fa_resend_cooldown', Date.now() + 60000);
        });

        function startCountdown() {
            resendBtn.disabled = true;
            const originalText = 'Resend Code';
            const timer = setInterval(() => {
                resendBtn.textContent = `Resend in ${countdown}s`;
                countdown--;
                if (countdown < 0) {
                    clearInterval(timer);
                    resendBtn.disabled = false;
                    resendBtn.textContent = originalText;
                    sessionStorage.removeItem('2fa_resend_cooldown');
                }
            }, 1000);
        }

        if (cooldownActive && Date.now() < parseInt(cooldownActive)) {
            startCountdown();
        }
    </script>
</body>
</html>
