<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($subject ?? 'Undangan Resmi') ?></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #000000;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            color: #d9d9d9;
            -webkit-font-smoothing: antialiased;
        }
        .email-container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #141414;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(172, 10, 10, 0.35);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.85);
        }
        .email-header {
            background: linear-gradient(180deg, #1f1f1f 0%, #141414 100%);
            padding: 36px 32px 26px;
            text-align: center;
            border-bottom: 2px solid #ac0a0a;
        }
        .header-emblem {
            display: inline-block;
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #cb6565;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .header-title {
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 6px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-sub {
            color: #8c8c8c;
            font-size: 13px;
            margin: 0;
            line-height: 1.5;
        }
        .email-body {
            padding: 36px 32px;
            font-size: 14px;
            line-height: 1.75;
            color: #d9d9d9;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 16px;
        }
        .content {
            margin-bottom: 24px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn-invitation {
            display: inline-block;
            background: #ac0a0a;
            background: linear-gradient(135deg, #ac0a0a 0%, #7a0707 100%);
            color: #ffffff !important;
            font-size: 14px;
            font-weight: 700;
            padding: 13px 32px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(172, 10, 10, 0.4);
            letter-spacing: 0.3px;
        }
        .closing {
            margin-top: 24px;
            font-size: 13px;
            color: #8c8c8c;
            line-height: 1.6;
        }
        .email-footer {
            background-color: #090607;
            padding: 24px 32px;
            text-align: center;
            font-size: 12px;
            color: #8c8c8c;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body>
    <div style="padding: 24px 12px;">
        <div class="email-container">
            <div class="email-header">
                <div class="header-emblem">B-Universe Official Invitation</div>
                <h1 class="header-title"><?= esc($event['title'] ?? 'BERITASATU MALAM APRESIASI 2026') ?></h1>
                <p class="header-sub"><?= esc($event['theme'] ?? 'Menggerakkan Perubahan, Menginspirasi Negeri') ?></p>
            </div>
            <div class="email-body">
                <div class="greeting"><?= $greeting ?></div>
                <div class="content"><?= $bodyContent ?></div>

                <div class="btn-wrapper">
                    <a href="<?= esc($invitationUrl) ?>" class="btn-invitation" target="_blank">
                        <?= esc($buttonText ?? 'Buka Undangan Digital') ?>
                    </a>
                </div>

                <div class="closing">
                    <?= $closing ?>
                </div>
            </div>
            <div class="email-footer">
                <p style="margin: 0 0 4px; font-weight: 600; color: #d9d9d9;"><?= esc($footerNote ?? 'Panitia Pelaksana BeritaSatu Malam Apresiasi 2026') ?></p>
                <p style="margin: 0;">Email ini dikirimkan secara otomatis dan resmi kepada Anda.</p>
            </div>
        </div>
    </div>
</body>
</html>
