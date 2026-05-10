<?php
require_once '../../includes/header.php';
require_once '../../includes/Clubs.php';

$clubs = new Clubs($pdo);
$allClubs = $clubs->getAll();
?>

<div class="container-fluid bg-body-tertiary mb-5 py-5 border-bottom">
    <div class="container py-lg-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill mb-3">
                    <i class="bi bi-stars me-1"></i> Society Discovery
                </span>
                <h1 class="display-4 fw-bold mb-3 lh-sm">
                    Find Your <span class="text-primary">Community</span>
                </h1>
                <p class="lead text-body-secondary mb-4">
                    Explore hundreds of student-run societies. Whether you're into tech, sports, or the arts, there's a place for you here.
                </p>
                
                <!-- Search Box -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-body">
                    <div class="card-body p-2">
                        <form action="" method="GET" class="row g-2 align-items-center mb-0">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-0 text-body-secondary ps-3">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-0 shadow-none bg-transparent" placeholder="Search societies..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4 custom-divider">
                                <select name="category" class="form-select border-0 shadow-none bg-transparent text-body-secondary cursor-pointer">
                                    <option value="">All Categories</option>
                                    <option value="Academic" <?= isset($_GET['category']) && $_GET['category'] == 'Academic' ? 'selected' : '' ?>>Academic</option>
                                    <option value="Arts" <?= isset($_GET['category']) && $_GET['category'] == 'Arts' ? 'selected' : '' ?>>Arts & Culture</option>
                                    <option value="Sports" <?= isset($_GET['category']) && $_GET['category'] == 'Sports' ? 'selected' : '' ?>>Sports & Fitness</option>
                                    <option value="Tech" <?= isset($_GET['category']) && $_GET['category'] == 'Tech' ? 'selected' : '' ?>>Technology</option>
                                    <option value="Social" <?= isset($_GET['category']) && $_GET['category'] == 'Social' ? 'selected' : '' ?>>Social Service</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                                    Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'admin'): ?>
                    <div class="mt-4">
                        <a href="create.php" class="text-decoration-none fw-semibold">
                            <i class="bi bi-plus-circle me-1"></i> Create a new society
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-5 offset-lg-1 d-none d-lg-block">
                <div class="position-relative">
                    <div class="position-absolute top-50 start-50 translate-middle w-100 h-100 bg-primary opacity-10 rounded-circle" style="filter: blur(60px);"></div>
                    <img src="<?= BASE_URL ?>assets/img/societies_banner.png" alt="Societies" class="img-fluid rounded-4 shadow-lg position-relative" style="object-fit: cover; height: 350px; width: 100%;">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="app-content pb-5">
    <div class="container">
        <div class="row g-4">
            <?php 
            // Handle filtering
            $search = $_GET['search'] ?? '';
            $cat = $_GET['category'] ?? '';
            $filteredClubs = array_filter($allClubs, function($c) use ($search, $cat) {
                $matchSearch = empty($search) || stripos($c['name'], $search) !== false;
                $matchCat = empty($cat) || $c['category'] == $cat;
                return $matchSearch && $matchCat;
            });

            foreach ($filteredClubs as $club): 
                $cover = !empty($club['cover_image']) ? $club['cover_image'] : BASE_URL.'assets/img/default-cover.jpg';
                if (!empty($club['cover_image']) && strpos($club['cover_image'], 'http') !== 0) {
                    $cover = BASE_URL . $club['cover_image'];
                }
                $logo = !empty($club['logo']) ? $club['logo'] : BASE_URL.'assets/img/default-logo.png';
                if (!empty($club['logo']) && strpos($club['logo'], 'http') !== 0) {
                    $logo = BASE_URL . $club['logo'];
                }
            ?>
                <div class="col-xl-4 col-md-6">
                    <div class="card glass-card h-100 p-0 overflow-hidden border-0 shadow-lg">
                        <!-- Banner Area with Background Image & Fallback -->
                        <div class="banner-area position-relative" style="height: 180px; background: #2c3e50 url('<?= htmlspecialchars($cover) ?>') center/cover no-repeat;">
                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.8) 100%);"></div>
                            <div class="position-absolute bottom-0 start-0 p-4 d-flex align-items-center w-100">
                                <img src="<?= htmlspecialchars($logo) ?>" class="rounded-circle border border-white border-2 me-3 shadow-lg" style="width: 65px; height: 65px; object-fit: cover; background: #fff;">
                                <div class="flex-grow-1">
                                    <span class="badge bg-primary px-2 py-1 mb-1 small"><?= htmlspecialchars($club['category'] ?? 'General') ?></span>
                                    <h4 class="mb-0 text-white fw-bold h5"><?= htmlspecialchars($club['name']) ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <p class="text-white-50 small mb-4 line-clamp-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; height: 4.5em;">
                                <?= htmlspecialchars($club['description']) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div class="d-flex align-items-center text-white-50 small">
                                    <div class="avatar-group me-2">
                                        <i class="bi bi-people-fill text-primary me-2"></i>
                                    </div>
                                    <span class="fw-bold text-white"><?= count($clubs->getMembers($club['id'])) ?></span> <span class="ms-1">Members</span>
                                </div>
                                <a href="view.php?id=<?= $club['id'] ?>" class="btn btn-premium btn-sm px-4 rounded-pill">Explore</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($filteredClubs)): ?>
                <div class="col-12">
                    <div class="card glass-card border-0 py-5 text-center shadow-lg">
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px;">
                                    <i class="bi bi-search display-4 text-primary"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold text-white mb-2">No Societies Found</h3>
                            <p class="text-white-50 mx-auto mb-4" style="max-width: 400px;">
                                We couldn't find any societies matching your current filters. Try searching for something else or explore all categories.
                            </p>
                            <a href="index.php" class="btn btn-premium rounded-pill px-5">Explore All Societies</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* Clean Search Design */
    .custom-divider {
        position: relative;
    }
    .custom-divider::before {
        content: '';
        position: absolute;
        left: 0;
        top: 20%;
        height: 60%;
        width: 1px;
        background-color: var(--bs-border-color);
        z-index: 10;
    }
    @media (max-width: 767.98px) {
        .custom-divider::before {
            display: none;
        }
    }
    /* Dark Theme Dashboard Enhancements */
    body[data-bs-theme="dark"] .card {
        background-color: #1e293b !important;
        border: 1px solid rgba(255,255,255,0.05) !important;
        transition: transform 0.3s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
    }
    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2) !important;
    }
    body[data-bs-theme="light"] .card {
        border: 1px solid rgba(0,0,0,0.05) !important;
    }
    body[data-bs-theme="light"] .card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02) !important;
    }
</style>

<?php require_once '../../includes/footer.php'; ?>
