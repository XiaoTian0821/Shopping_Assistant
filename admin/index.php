<?php
/**
 * Admin Dashboard - Same design as index.php
 */
require_once __DIR__ . '/../includes/init.php';
require_admin();

// Get statistics
$products = get_products();
$totalProducts = count($products);
$withBarcode = count(array_filter($products, fn($p) => !empty($p['barcode'])));
$withImage = count(array_filter($products, fn($p) => !empty($p['image_path'])));

// Recent products
$recentProducts = array_slice($products, 0, 5);

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>

<style>
/* Match index.php design exactly */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 3rem 2rem;
    border-radius: 20px;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.feature-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}
.feature-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}
.feature-card .card-body {
    padding: 2rem;
}
.feature-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    margin: 0 auto 1rem;
}
.btn-scan {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 1rem 2.5rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-scan:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
}
.btn-browse {
    background: white;
    border: 2px solid white;
    color: #667eea;
    padding: 1rem 2.5rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-browse:hover {
    background: rgba(255,255,255,0.9);
    color: #667eea;
    transform: translateY(-2px);
}
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}
.empty-state i {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}
.table-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 10px;
}
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Hero Section -->
            <div class="hero-section text-center mb-5">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Admin Dashboard
                </h1>
                <p class="lead mb-4 opacity-75">
                    Welcome back! Here's an overview of your system.
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-primary bg-opacity-10 text-primary mx-auto">
                                <i class="fas fa-box"></i>
                            </div>
                            <h3 class="fw-bold text-primary mt-3"><?php echo $totalProducts; ?></h3>
                            <p class="text-muted mb-0">Total Products</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-success bg-opacity-10 text-success mx-auto">
                                <i class="fas fa-barcode"></i>
                            </div>
                            <h3 class="fw-bold text-success mt-3"><?php echo $withBarcode; ?></h3>
                            <p class="text-muted mb-0">With Barcode</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-info bg-opacity-10 text-info mx-auto">
                                <i class="fas fa-image"></i>
                            </div>
                            <h3 class="fw-bold text-info mt-3"><?php echo $withImage; ?></h3>
                            <p class="text-muted mb-0">With Image</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-warning bg-opacity-10 text-warning mx-auto">
                                <i class="fas fa-robot"></i>
                            </div>
                            <h3 class="fw-bold text-warning mt-3"><?php echo $totalProducts; ?></h3>
                            <p class="text-muted mb-0">AI Ready</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card feature-card mb-5">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-3">
                            <a href="<?php echo APP_URL; ?>/admin/products.php?action=add" class="btn btn-scan w-100 py-3">
                                <i class="fas fa-plus-circle fa-2x mb-2 d-block"></i>
                                Add Product
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <a href="<?php echo APP_URL; ?>/admin/products.php" class="btn btn-browse w-100 py-3">
                                <i class="fas fa-list fa-2x mb-2 d-block text-primary"></i>
                                View All Products
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <a href="<?php echo APP_URL; ?>/admin/settings.php" class="btn btn-browse w-100 py-3">
                                <i class="fas fa-cogs fa-2x mb-2 d-block text-primary"></i>
                                AI Settings
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-browse w-100 py-3">
                                <i class="fas fa-store fa-2x mb-2 d-block text-primary"></i>
                                Public Catalog
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Products -->
            <div class="card feature-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Products</h5>
                    <a href="<?php echo APP_URL; ?>/admin/products.php" class="btn btn-light btn-sm">
                        <i class="fas fa-eye me-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentProducts)): ?>
                    <div class="empty-state p-5">
                        <i class="fas fa-box-open fa-3x text-muted"></i>
                        <h4 class="text-muted mt-3">No Products Yet</h4>
                        <p class="text-muted mb-4">Start by adding your first product.</p>
                        <a href="<?php echo APP_URL; ?>/admin/products.php?action=add" class="btn btn-scan">
                            <i class="fas fa-plus me-2"></i>Add First Product
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Image</th>
                                    <th>SKU</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Barcode</th>
                                    <th>Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentProducts as $prod): ?>
                                <tr>
                                    <td>
                                        <?php if ($prod['image_path']): ?>
                                        <img src="<?php echo APP_URL; ?>/<?php echo htmlspecialchars($prod['image_path']); ?>" 
                                             alt="" class="table-image">
                                        <?php else: ?>
                                        <div class="table-image" style="background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><code><?php echo htmlspecialchars($prod['sku']); ?></code></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($prod['name']); ?></td>
                                    <td><span class="text-primary fw-bold"><?php echo number_format($prod['price'], 2); ?></span></td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($prod['barcode'] ?? '-'); ?></small></td>
                                    <td><small class="text-muted"><?php echo date('M j, Y', strtotime($prod['created_at'])); ?></small></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
