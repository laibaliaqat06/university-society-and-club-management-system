<?php
define('BASE_URL', './');
// Mock session for demo
session_start();
$_SESSION['role'] = 'student';
$_SESSION['name'] = 'Antigravity User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antigravity Theme Demo</title>
    
    <!-- Antigravity Theme Engine -->
    <link rel="stylesheet" href="assets/css/theme.css" />
    <script src="assets/js/theme.js"></script>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/bootstrap-icons.css" />
    <link rel="stylesheet" href="assets/css/premium.css" />
    
    <style>
        .demo-container { padding: 40px 0; }
        .demo-section { margin-bottom: 50px; }
    </style>
</head>
<body class="bg-body">

    <nav class="navbar navbar-expand-lg border-bottom sticky-top" style="background: var(--bg-surface); backdrop-filter: blur(10px);">
        <div class="container">
            <a class="navbar-brand fw-bold text-premium-gradient" href="#">ANTIGRAVITY</a>
            <div class="ms-auto d-flex align-items-center">
                <button class="btn btn-link nav-link me-3" id="theme-toggle" type="button">
                    <i class="bi bi-sun-fill" id="theme-icon"></i>
                </button>
                <a href="#" class="btn btn-premium btn-sm rounded-pill">Get Started</a>
            </div>
        </div>
    </nav>

    <div class="container demo-container">
        <div class="demo-section">
            <h1 class="display-4 mb-4">Design System <span class="text-premium-gradient">Showcase</span></h1>
            <p class="lead text-muted">A premium, theme-aware UI kit built for high-performance web applications.</p>
        </div>

        <div class="row demo-section">
            <div class="col-md-4">
                <div class="glass-card p-4 mb-4">
                    <h4 class="card-title">Glassmorphism</h4>
                    <p class="text-muted">Dynamic background blur and subtle borders that adapt to your theme.</p>
                    <button class="btn btn-premium w-100">Explore</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 mb-4 shadow-sm">
                    <h4 class="card-title">Standard Card</h4>
                    <p class="text-muted">Clean, minimalist cards for structured data and content layouts.</p>
                    <button class="btn btn-outline-primary w-100">Details</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card p-4 mb-4 bg-primary text-white banner-area">
                    <h4 class="card-title">Primary Banner</h4>
                    <p>Always readable, even on complex gradients or vibrant background colors.</p>
                    <a href="#" class="text-white text-decoration-underline">Learn more</a>
                </div>
            </div>
        </div>

        <div class="demo-section">
            <h3 class="mb-4">Form Components</h3>
            <div class="glass-card p-4">
                <form>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" class="form-control" placeholder="Enter your name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" class="form-control" placeholder="name@example.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Department</label>
                        <select class="form-select">
                            <option selected>Choose...</option>
                            <option value="1">Engineering</option>
                            <option value="2">Design</option>
                            <option value="3">Marketing</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-premium px-5">Submit Application</button>
                </form>
            </div>
        </div>

        <div class="demo-section">
            <h3 class="mb-4">Data Tables</h3>
            <div class="table-responsive glass-card p-0 overflow-hidden">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Project</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Progress</th>
                            <th class="px-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-3 fw-medium">Core Engine</td>
                            <td class="py-3"><span class="badge bg-success">Active</span></td>
                            <td class="py-3">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: 75%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end"><i class="bi bi-three-dots-vertical"></i></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 fw-medium">User Dashboard</td>
                            <td class="py-3"><span class="badge bg-warning text-dark">Pending</span></td>
                            <td class="py-3">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 45%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end"><i class="bi bi-three-dots-vertical"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="py-5 border-top mt-5" style="background: var(--bg-surface);">
        <div class="container text-center text-muted">
            <p>&copy; 2026 Antigravity Design System. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
