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
            background-color: #0d131f;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #e2e8f0;
            -webkit-font-smoothing: antialiased;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #121c2e;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(212, 175, 55, 0.25);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        .email-header {
            background: linear-gradient(135deg, #09101d 0%, #15243b 100%);
            padding: 36px 30px 24px;
            text-align: center;
            border-bottom: 2px solid #d4af37;
        }
        .header-emblem {
            display: inline-block;
            font-size: 13px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #d4af37;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .header-title {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 6px;
            letter-spacing: 0.5px;
        }
        .header-sub {
            color: #94a3b8;
            font-size: 14px;
            margin: 0;
        }
        .email-body {
            padding: 36px 32px;
            font-size: 15px;
            line-height: 1.7;
            color: #cbd5e1;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 18px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn-invitation {
            display: inline-block;
            background: linear-gradient(135deg, #d4af37 0%, #b89127 100%);
            color: #0b121e !important;
            font-size: 15px;
            font-weight: 700;
            padding: 14px 34px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.35);
            letter-spacing: 0.5px;
        }
        .event-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 16px 20px;
            margin: 20px 0;
            font-size: 14px;
        }
        .closing {
            margin-top: 24px;
            font-size: 14px;
            color: #94a3b8;
        }
        .email-footer {
            background-color: #090e18;
            padding: 24px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }
    </style>
</head>
<body>
    <div style="padding: 24px 12px;">
        <div class="email-container">
            <div class="email-header">
                <div class="header-emblem">B-UNIVERSE OFFICIAL INVITATION</div>
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
                <p style="margin: 0 0 6px; font-weight: 600; color: #94a3b8;"><?= esc($footerNote ?? 'Panitia Pelaksana BeritaSatu Malam Apresiasi 2026') ?></p>
                <p style="margin: 0;">Email ini dikirimkan secara otomatis dan resmi kepada Anda.</p>
            </div>
        </div>
    </div>
</body>
</html>
