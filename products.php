<?php
/**
 * Product catalog page (public)
 */
require_once __DIR__ . '/includes/init.php';

// Get search parameter
$search = trim($_GET['search'] ?? '');

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = PAGE_SIZE;
$offset = ($page - 1) * $limit;

// Get products
$products = get_products([
    'search' => $search,
    'limit' => $limit,
    'offset' => $offset
]);

// Get total count
$total = get_product_count($search);
$totalPages = ceil($total / $limit);

$pageTitle = $search ? 'Search: ' . htmlspecialchars($search) : 'Product Catalog';
include __DIR__ . '/includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-box text-primary me-2"></i>Product Catalog</h2>
    </div>
</div>

<!-- Search Form -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search by name, SKU, or barcode...">
                </div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Products Grid -->
<?php if (empty($products)): ?>
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <h4>No products found</h4>
        <p class="text-muted">
            <?php echo $search ? 'No products match your search.' : 'The product catalog is empty.'; ?>
        </p>
        <?php if (is_admin()): ?>
        <a href="<?php echo APP_URL; ?>/admin/products.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Products
        </a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($products as $product): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100">
                <?php if ($product['image_path']): ?>
                <img src="<?php echo APP_URL; ?>/<?php echo htmlspecialchars($product['image_path']); ?>" 
                     class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>"
                     onerror="this.src='<?php echo APP_URL; ?>/assets/images/no-image.png'">
                <?php else: ?>
                <img src="<?php echo APP_URL; ?>/assets/images/no-image.png" 
                     class="card-img-top" alt="No image">
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title text-truncate" title="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h6>
                    <p class="price mb-1"><?php echo number_format($product['price'], 2); ?></p>
                    <p class="text-muted small mb-1">
                        <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($product['sku']); ?>
                    </p>
                    <?php if ($product['barcode']): ?>
                    <p class="text-muted small mb-2">
                        <i class="fas fa-barcode me-1"></i><?php echo htmlspecialchars($product['barcode']); ?>
                    </p>
                    <?php endif; ?>
                    <p class="card-text text-muted small text-truncate flex-grow-1">
                        <?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 60)); ?>...
                    </p>
                    <a href="<?php echo APP_URL; ?>/scan.php" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-camera me-1"></i>Scan
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
    
    <p class="text-center text-muted small mt-3">
        Showing <?php echo count($products); ?> of <?php echo $total; ?> products
    </p>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
