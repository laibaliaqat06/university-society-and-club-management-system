<?php
require_once '../core/session.php';
require_once '../core/Certificates.php';

$eventId = $_GET['event_id'] ?? 0;
$userId = $_GET['user_id'] ?? 0;

if (!$eventId || !$userId) {
    die("Invalid request parameters.");
}

$certObj = new Certificates($pdo);
$result = $certObj->generate($userId, $eventId);

if (!$result['success']) {
    die('<div style="text-align:center; padding: 50px; font-family: sans-serif;">
            <h2 style="color:red;">Error</h2>
            <p>'.$result['message'].'</p>
            <a href="javascript:history.back()">Go Back</a>
         </div>');
}

$details = $certObj->getDetailsByHash($result['hash']);
if (!$details) {
    die("Certificate lookup failed.");
}

$logo = !empty($details['society_logo']) ? $details['society_logo'] : BASE_URL.'assets/img/AdminLTELogo.png';
if (!empty($details['society_logo']) && strpos($details['society_logo'], 'http') !== 0) {
    $logo = BASE_URL . $details['society_logo'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - <?= htmlspecialchars($details['student_name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&family=Great+Vibes&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        .certificate-container {
            width: 900px;
            height: 650px;
            background: #fff;
            padding: 40px;
            position: relative;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            border: 20px solid #1a237e; /* Navy Blue Border */
            box-sizing: border-box;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%"><rect width="100%" height="100%" fill="none" stroke="%23303f9f" stroke-width="2" stroke-dasharray="10,10"/></svg>');
            overflow: hidden;
        }

        .certificate-inner {
            border: 2px solid #303f9f;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 30px;
            box-sizing: border-box;
            position: relative;
        }

        .corner-decoration {
            position: absolute;
            width: 100px;
            height: 100px;
            border: 5px solid #ffc107; /* Gold */
        }
        .top-left { top: -10px; left: -10px; border-right: none; border-bottom: none; }
        .top-right { top: -10px; right: -10px; border-left: none; border-bottom: none; }
        .bottom-left { bottom: -10px; left: -10px; border-right: none; border-top: none; }
        .bottom-right { bottom: -10px; right: -10px; border-left: none; border-top: none; }

        .logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-bottom: 20px;
        }

        .header {
            font-family: 'Cinzel', serif;
            font-size: 42px;
            color: #1a237e;
            margin-bottom: 10px;
            letter-spacing: 5px;
            text-transform: uppercase;
        }

        .sub-header {
            font-size: 18px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 30px;
        }

        .student-name {
            font-family: 'Great Vibes', cursive;
            font-size: 55px;
            color: #c62828;
            margin: 20px 0;
            border-bottom: 2px solid #eee;
            min-width: 400px;
            padding-bottom: 10px;
        }

        .description {
            font-size: 18px;
            line-height: 1.6;
            color: #444;
            max-width: 600px;
            margin-bottom: 40px;
        }

        .description strong { color: #000; }

        .footer {
            width: 100%;
            display: flex;
            justify-content: space-around;
            margin-top: auto;
            position: relative;
        }

        .sign-block {
            width: 200px;
            text-align: center;
        }

        .sign-line {
            border-top: 2px solid #000;
            margin-bottom: 10px;
            position: relative;
        }
        
        .sign-line img {
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            opacity: 0.8;
            pointer-events: none;
        }

        .sign-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #777;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(0,0,0,0.03);
            font-family: 'Inter', sans-serif;
            font-weight: 800;
            z-index: 0;
            pointer-events: none;
        }

        .verify-text {
            position: absolute;
            bottom: 15px;
            font-size: 9px;
            color: #aaa;
        }

        @media print {
            body { background: none; }
            .certificate-container { box-shadow: none; border: 15px solid #1a237e; }
            .btn-print { display: none; }
        }

        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 25px;
            background: #198754;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 100;
        }
    </style>
</head>
<body>

    <a href="javascript:window.print()" class="btn-print">Print / Save PDF</a>

    <div class="certificate-container">
        <div class="watermark"><?= strtoupper(str_replace(' ', '', $details['society_name'])) ?></div>
        
        <div class="certificate-inner">
            <div class="corner-decoration top-left"></div>
            <div class="corner-decoration top-right"></div>
            <div class="corner-decoration bottom-left"></div>
            <div class="corner-decoration bottom-right"></div>

            <img src="<?= $logo ?>" class="logo" alt="Society Logo">

            <div class="header">Certificate</div>
            <div class="sub-header">Of Achievement & Participation</div>

            <div class="description">This certificate is proudly presented to</div>

            <div class="student-name"><?= htmlspecialchars($details['student_name']) ?></div>

            <div class="description">
                For their active participation and contribution to the 
                <strong>"<?= htmlspecialchars($details['event_name']) ?>"</strong> event, 
                organized by <strong><?= htmlspecialchars($details['society_name']) ?></strong> 
                held on <?= date('jS \o\f F, Y', strtotime($details['event_date'])) ?>.
            </div>

            <div class="footer">
                <div class="sign-block">
                    <div class="sign-line">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/3/3a/Jon_Kirsch_Signature.png" alt="Signature">
                    </div>
                    <div class="sign-title">Faculty Advisor</div>
                </div>
                <div class="sign-block">
                    <div class="sign-line"></div>
                    <div class="sign-title">Society President</div>
                </div>
            </div>

            <div class="verify-text">
                Certificate Hash: <?= $details['certificate_hash'] ?> • Verified by Universal System
            </div>
        </div>
    </div>

</body>
</html>
