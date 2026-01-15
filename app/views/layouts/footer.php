</div> <!-- Fin content-wrapper -->
    </main>
    
    <!-- Footer Principal -->
    <footer class="main-footer">
        <div class="footer-container">
            <!-- Section À propos -->
            <div class="footer-section">
                <h3>
                    <i class="fas fa-chart-line"></i> Smarte Wallet
                </h3>
                <p class="footer-description">
                    Solution professionnelle de gestion financière pour suivre, analyser et optimiser vos revenus et dépenses en temps réel.
                </p>
            </div>
            
            <!-- Liens Rapides -->
            <div class="footer-section">
                <h4>Liens Rapides</h4>
                <ul class="footer-links">
                    <li><a href="<?= BASE_URL ?>dashboard"><i class="fas fa-angle-right"></i> Dashboard</a></li>
                    <li><a href="<?= BASE_URL ?>income"><i class="fas fa-angle-right"></i> Mes Revenus</a></li>
                    <li><a href="<?= BASE_URL ?>expense"><i class="fas fa-angle-right"></i> Mes Dépenses</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Barre de copyright -->
        <div class="footer-bottom">
            <div class="footer-container">
                <p class="copyright">
                    &copy; <?= date('Y') ?> Smarte Wallet. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>
    
    <!-- Bouton retour en haut -->
    <button id="scrollToTop" class="scroll-to-top" title="Retour en haut">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- Scripts -->
    <script>
        // Menu Mobile
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const closeMobileNav = document.getElementById('closeMobileNav');
        const mobileNav = document.getElementById('mobileNav');
        const mobileOverlay = document.getElementById('mobileOverlay');
        
        function toggleMobileMenu() {
            mobileNav.classList.toggle('active');
            mobileOverlay.classList.toggle('active');
            document.body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
        }
        
        mobileMenuBtn?.addEventListener('click', toggleMobileMenu);
        closeMobileNav?.addEventListener('click', toggleMobileMenu);
        mobileOverlay?.addEventListener('click', toggleMobileMenu);
        
        // Toggle Theme
        const themeToggle = document.getElementById('themeToggle');
        themeToggle?.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            const icon = themeToggle.querySelector('i');
            icon.classList.toggle('fa-moon');
            icon.classList.toggle('fa-sun');
            localStorage.setItem('theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
        });
        
        // Charger le thème sauvegardé
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
            const icon = themeToggle?.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
        }
        
        // Scroll to Top
        const scrollBtn = document.getElementById('scrollToTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollBtn?.classList.add('visible');
            } else {
                scrollBtn?.classList.remove('visible');
            }
        });
        
        scrollBtn?.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Auto-hide alerts après 5 secondes
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>