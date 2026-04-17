<?php
session_start();
require_once 'core/auth.php';
$auth = new Auth($pdo);
$roles = $auth->getPublicRoles();

$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'password' => $_POST['password'],
        'role' => $_POST['role'],
        'identity_no' => trim($_POST['identity_no']),
        'registration_no' => trim($_POST['registration_no'])
    ];

    if ($data['password'] !== $_POST['retype_password']) {
        $msg = "Passwords do not match!";
        $msgType = "danger";
    } else {
        $result = $auth->register($data);
        if ($result === true) {
            $msg = "Registration successful! <a href='login.php' class='fw-bold text-success'>Login now</a>";
            $msgType = "success";
        } else {
            $msg = $result;
            $msgType = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account | Universal System</title>
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css" />
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0, transparent 50%), 
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.15) 0, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f8fafc;
            padding: 20px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 550px;
            padding: 40px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-logo {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 12px !important;
            padding: 12px 16px !important;
            color: #f1f5f9 !important;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.07) !important;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
        }

        .btn-premium {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.5);
            filter: brightness(1.1);
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-left: none !important;
            border-radius: 0 12px 12px 0 !important;
            color: #6366f1 !important;
        }

        .input-group > .form-control {
            border-right: none !important;
            border-radius: 12px 0 0 12px !important;
        }

        .login-link {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .login-link:hover {
            color: #6366f1;
        }

        .login-link strong {
            color: #f1f5f9;
        }
        
        ::placeholder {
            color: rgba(255,255,255,0.2) !important;
        }
    </style>
</head>
<body>
    <div class="glass-card">
        <div class="text-center mb-4">
            <h1 class="brand-logo mb-1">Universal</h1>
            <p class="text-white-50 small mb-0">Elevate Your Campus Experience</p>
        </div>

        <h4 class="fw-bold text-white mb-2 text-center">Join the Community</h4>
        <p class="text-white-50 small text-center mb-4">Create your account to start participating in events and societies.</p>

        <?php if($msg): ?>
            <div class="alert bg-<?= $msgType === 'danger' ? 'danger' : 'success' ?> bg-opacity-10 border-0 text-<?= $msgType === 'danger' ? 'danger' : 'success' ?> small py-3 mb-4 rounded-3">
                <i class="bi bi-<?= $msgType === 'danger' ? 'exclamation-circle' : 'check-circle' ?>-fill me-2"></i>
                <?= $msg ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Full Name</label>
                    <div class="input-group">
                        <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="name@university.edu" required>
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Identity / CNIC No</label>
                    <input type="text" name="identity_no" class="form-control" placeholder="35201-XXXXXXX-X">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Reg / Student No</label>
                    <input type="text" name="registration_no" class="form-control" placeholder="2024-UG-XXXX">
                </div>

                <div class="col-12">
                    <label class="form-label">Select Your Role</label>
                    <select name="role" class="form-select" required>
                        <option value="" disabled selected>I am a...</option>
                        <?php foreach($roles as $r): ?>
                            <option value="<?= $r['role_key'] ?>"><?= $r['role_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Retype Password</label>
                    <input type="password" name="retype_password" class="form-control" required>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-premium w-100 fw-bold">
                        Create My Account <i class="bi bi-arrow-right-short ms-2"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="text-center mt-4 pt-3 border-top border-white border-opacity-10">
            <a href="login.php" class="login-link">Already a member? <strong>Sign In Now</strong></a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>