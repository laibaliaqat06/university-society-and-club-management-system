</div> </div> </main>
    
    <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">V 1.0</div>
        <strong>Copyright &copy; <?= date('Y') ?> <a href="#">Club & Society Management System</a>.</strong> All rights reserved.
    </footer>
</div> <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/adminlte.min.js"></script>
<script>
    // Theme Toggle Logic with Persistence
    const toggleButton = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const htmlElement = document.documentElement;

    // 1. Function to update Icon
    function updateIcon(theme) {
        if (theme === 'dark') {
            themeIcon.classList.remove('bi-sun-fill');
            themeIcon.classList.add('bi-moon-fill');
        } else {
            themeIcon.classList.remove('bi-moon-fill');
            themeIcon.classList.add('bi-sun-fill');
        }
    }

    // 2. Initialize Icon on Load
    const currentTheme = localStorage.getItem('theme') || 'light';
    updateIcon(currentTheme);

    // 3. Handle Click
    toggleButton.addEventListener('click', () => {
        const current = htmlElement.getAttribute('data-bs-theme');
        const newTheme = current === 'dark' ? 'light' : 'dark';
        
        // Apply
        htmlElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme); // SAVE IT
        updateIcon(newTheme);
    });
</script>

<!-- Global Search Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('global-search');
    const searchResults = document.getElementById('search-results');

    if (searchInput) {
        let timeout = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                searchResults.classList.add('d-none');
                return;
            }

            timeout = setTimeout(() => {
                fetch('<?= BASE_URL ?>includes/search.php?query=' + encodeURIComponent(query))
                    .then(response => response.text())
                    .then(html => {
                        searchResults.innerHTML = html;
                        searchResults.classList.remove('d-none');
                    });
            }, 300);
        });

        // Hide results on click outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('d-none');
            }
    }
});
</script>

<!-- Global Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000;">
  <div id="liveToast" class="toast glass-card border-0 text-white" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toast-body">
        Hello, world! This is a toast message.
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script>
function showToast(message, type = 'success') {
    const toastEl = document.getElementById('liveToast');
    const toastBody = document.getElementById('toast-body');
    const toast = new bootstrap.Toast(toastEl);
    
    toastBody.innerText = message;
    toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');
    toastEl.classList.add('bg-' + type);
    
    toast.show();
}

// Automatically show toast if session message exists
<?php if(isset($_SESSION['msg'])): ?>
    showToast("<?= $_SESSION['msg'] ?>", "<?= $_SESSION['msg_type'] ?? 'success' ?>");
    <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
<?php endif; ?>
</script>
</body>
</html>
