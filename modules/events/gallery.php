<?php
require_once '../../includes/header.php';
require_once '../../includes/Events.php';

$eventsObj = new Events($pdo);
$pastEvents = $eventsObj->getPastEvents();
?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2);"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center text-center">
            <div class="col-12">
                <span class="badge bg-warning text-dark px-3 py-2 mb-3">Memories & Highlights</span>
                <h1 class="display-3 fw-bold text-white mb-3">Event Gallery</h1>
                <p class="lead text-white-50 mb-0">Explore the best moments from our completed university activities.</p>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container">
        <?php if (empty($pastEvents)): ?>
            <div class="text-center py-5">
                <div class="alert bg-dark text-white border-secondary p-5 shadow-lg">
                    <i class="bi bi-camera-video display-1 text-muted mb-4 d-block"></i>
                    <h3 class="fw-bold">No Past Events Yet</h3>
                    <p class="text-muted lead">The gallery will populate once events are completed and memories are uploaded.</p>
                    <a href="index.php" class="btn btn-primary mt-3">View Upcoming Events</a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($pastEvents as $event): 
                    $gallery = $eventsObj->getGallery($event['id']);
                    $imageCount = 0;
                    $videoCount = 0;
                    foreach($gallery as $item) {
                        if($item['media_type'] == 'video') $videoCount++;
                        else $imageCount++;
                    }
                    // Find first image for cover
                    $cover = 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&q=80&w=800';
                    foreach($gallery as $item) {
                        if($item['media_type'] == 'image') {
                            $cover = $item['image'];
                            if (strpos($cover, 'http') !== 0) {
                                $cover = BASE_URL . $cover;
                            }
                            break;
                        }
                    }
                ?>
                    <div class="col-xl-4 col-md-6">
                        <div class="card h-100 border-0 shadow-lg bg-dark text-white overflow-hidden gallery-event-card" style="border-radius: 20px;">
                            <div class="position-relative" style="height: 250px;">
                                <img src="<?= $cover ?>" class="w-100 h-100" style="object-fit: cover; filter: brightness(0.7);">
                                <div class="position-absolute top-0 end-0 m-3 d-flex flex-column gap-2 text-end">
                                    <?php if($imageCount > 0): ?>
                                        <span class="badge bg-primary shadow-sm"><i class="bi bi-image me-1"></i> <?= $imageCount ?></span>
                                    <?php endif; ?>
                                    <?php if($videoCount > 0): ?>
                                        <span class="badge bg-danger shadow-sm"><i class="bi bi-play-circle me-1"></i> <?= $videoCount ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                                    <small class="text-warning fw-bold uppercase"><?= htmlspecialchars($event['club_name']) ?></small>
                                    <h4 class="fw-bold mb-0"><?= htmlspecialchars($event['title']) ?></h4>
                                </div>
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <p class="text-white-50 small mb-4 flex-grow-1"><?= substr(htmlspecialchars($event['description']), 0, 100) ?>...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-white-50 small"><i class="bi bi-calendar-check me-1"></i> <?= date('M Y', strtotime($event['event_date'])) ?></span>
                                    <button class="btn btn-premium btn-sm px-4 rounded-pill open-slideshow" 
                                            data-id="<?= $event['id'] ?>" 
                                            data-title="<?= htmlspecialchars($event['title']) ?>">
                                        <i class="bi bi-play-circle-fill me-1"></i> Get Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Slideshow Modal -->
<div class="modal fade" id="highlightsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 p-4 pb-0" style="position: absolute; width: 100%; z-index: 10; background: linear-gradient(to bottom, rgba(0,0,0,0.7), transparent);">
                <h5 class="modal-title text-white fw-bold" id="modalTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="highlightsCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <div class="carousel-inner" id="carouselContent">
                        <!-- Dynamic Content -->
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#highlightsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#highlightsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const highlightsModal = new bootstrap.Modal(document.getElementById('highlightsModal'));
    const modalTitle = document.getElementById('modalTitle');
    const carouselContent = document.getElementById('carouselContent');
    
    // Store gallery data in JS (simplified for demo, normally we'd fetch)
    const galleryData = {
        <?php 
        foreach ($pastEvents as $event) {
            $gallery = $eventsObj->getGallery($event['id']);
            echo "'" . $event['id'] . "': " . json_encode($gallery) . ",";
        }
        ?>
    };

    document.querySelectorAll('.open-slideshow').forEach(btn => {
        btn.addEventListener('click', function() {
            const eventId = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');
            const items = galleryData[eventId];
            
            modalTitle.textContent = title;
            carouselContent.innerHTML = '';
            
            if (items && items.length > 0) {
                items.forEach((item, index) => {
                    const activeClass = index === 0 ? 'active' : '';
                    const src = (item.image.startsWith('http')) ? item.image : '<?= BASE_URL ?>' + item.image;
                    
                    const slide = `
                        <div class="carousel-item ${activeClass}" style="height: 450px; background: #111; position: relative; overflow: hidden;">
                            <img src="${src}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; filter: blur(20px) brightness(0.5); transform: scale(1.1); z-index: 1;">
                            <img src="${src}" class="d-block w-100 h-100" style="object-fit: contain; position: relative; z-index: 2; filter: contrast(1.15) saturate(1.2) brightness(1.05) drop-shadow(0px 0px 20px rgba(0,0,0,0.5));">
                            
                            <div class="carousel-caption d-none d-md-block" style="z-index: 3; background: rgba(0,0,0,0.6); padding: 10px; border-radius: 8px; backdrop-filter: blur(5px); bottom: 20px;">
                                <p class="mb-0 small text-uppercase tracking-wider fw-bold text-white">University of Sahiwal Highlights</p>
                            </div>
                        </div>
                    `;
                    carouselContent.innerHTML += slide;
                });
                
                highlightsModal.show();
                // Ensure carousel starts
                new bootstrap.Carousel(document.getElementById('highlightsCarousel'), {
                    interval: 2500,
                    ride: 'carousel'
                }).cycle();
            } else {
                carouselContent.innerHTML = '<div class="p-5 text-center text-white-50">No memories found for this event.</div>';
                highlightsModal.show();
            }
        });
    });
});
</script>

<style>
.gallery-event-card {
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.gallery-event-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.6) !important;
}
.gallery-event-card img {
    transition: transform 0.6s ease;
}
.gallery-event-card:hover img {
    transform: scale(1.1);
}
</style>

<?php require_once '../../includes/footer.php'; ?>
