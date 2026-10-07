        </div>
    </main>
    
    <footer class="bg-dark text-light py-5 mt-5" style="background-color: #1a1a2e !important;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3"><i class="fas fa-robot me-2"></i><?php echo APP_NAME; ?></h5>
                    <p class="text-muted mb-0" style="color: #adb5da !important;">AI-powered product recognition and price checking system. Scan any product to instantly check its price and information.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h6 class="text-white mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo APP_URL; ?>/" class="text-muted text-decoration-none" style="color: #adb5da !important;"><i class="fas fa-home me-2"></i>Home</a></li>
                        <li class="mb-2"><a href="<?php echo APP_URL; ?>/products.php" class="text-muted text-decoration-none" style="color: #adb5da !important;"><i class="fas fa-box me-2"></i>Products</a></li>
                        <li class="mb-2"><a href="<?php echo APP_URL; ?>/scan.php" class="text-muted text-decoration-none" style="color: #adb5da !important;"><i class="fas fa-camera me-2"></i>Scan Product</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-white mb-3">For Admins</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo APP_URL; ?>/login.php" class="text-muted text-decoration-none" style="color: #adb5da !important;"><i class="fas fa-sign-in-alt me-2"></i>Admin Login</a></li>
                        <li class="mb-2"><a href="<?php echo APP_URL; ?>/install.php" class="text-muted text-decoration-none" style="color: #adb5da !important;"><i class="fas fa-cogs me-2"></i>Installation</a></li>
                        <?php if (is_admin()): ?>
                        <li class="mb-2"><a href="<?php echo APP_URL; ?>/admin/" class="text-muted text-decoration-none" style="color: #adb5da !important;"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: #495057 !important;">
            <div class="row">
                <div class="col-12 text-center">
                    <small class="text-muted" style="color: #6c757d !important;">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved. | Powered by <i class="fas fa-robot"></i> AI Technology</small>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo APP_URL; ?>/assets/js/app.js"></script>
</body>
</html>
