<?php
/**
 * Admin Products Management - Fixed design with proper CSS
 */
require_once __DIR__ . '/../includes/init.php';
require_admin();

$action = $_GET['action'] ?? '';
$productId = (int)($_GET['id'] ?? 0);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } elseif ($action === 'add' || $action === 'edit') {
        handleProductSave($productId);
    } elseif ($action === 'delete') {
        handleProductDelete();
    }
}

// Get products for listing
$products = get_products();

$pageTitle = 'Manage Products';
include __DIR__ . '/includes/header.php';

// Get product for editing
$editProduct = null;
if ($action === 'edit' && $productId > 0) {
    $editProduct = get_product($productId);
}
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
.qr-card, .feature-card {
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
.provider-card {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}
.provider-card:hover, .provider-card.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
}
.provider-icon {
    width: 48px;
    height: 48px;
    background: #f8f9fa;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 1rem;
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

.admin-page {
    max-width: 1240px;
}

.admin-page .hero-section {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-align: left;
    padding: 2.5rem 3rem;
    border-radius: 24px;
    background: linear-gradient(120deg, #182848, #4b6cb7);
    box-shadow: 0 18px 45px rgba(24, 40, 72, 0.2);
}

.admin-page .hero-section::after {
    content: '';
    position: absolute;
    width: 260px;
    height: 260px;
    right: 7%;
    top: -120px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
}

.admin-page .hero-section h1,
.admin-page .hero-section p {
    position: relative;
    z-index: 1;
}

.admin-page .hero-section h1 {
    font-size: clamp(1.7rem, 3vw, 2.4rem);
    margin-bottom: .5rem !important;
}

.admin-page .hero-section .btn-scan {
    position: relative;
    z-index: 1;
    background: #fff;
    color: #304b88;
    padding: .8rem 1.25rem;
    border-radius: 12px;
    white-space: nowrap;
}

.admin-page .feature-card {
    border: 1px solid #e7ebf3;
    border-radius: 18px;
    box-shadow: 0 8px 28px rgba(25, 42, 70, .07);
}

.admin-page .feature-card .card-header {
    background: #fff;
    color: #1d2b45;
    border-bottom: 1px solid #edf0f5;
    padding: 1.15rem 1.4rem;
}

.admin-page .feature-card .card-header h5 {
    font-weight: 700;
}

.admin-page .feature-card .card-body {
    padding: 1.4rem;
}

.admin-page .feature-card.text-center {
    transition: transform .2s ease, box-shadow .2s ease;
}

.admin-page .feature-card.text-center:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 32px rgba(25, 42, 70, .11);
}

.admin-page .table thead th {
    background: #f5f7fb;
    color: #68758a;
    border-bottom: 1px solid #e7ebf3;
    font-size: .72rem;
    letter-spacing: .08em;
}

.admin-page .table tbody td {
    padding: 1rem .75rem;
    border-color: #eef1f6;
}

.admin-page .table tbody tr:hover {
    background: #f8faff;
}

.admin-page .form-control,
.admin-page .input-group-text {
    border-color: #dfe5ef;
    border-radius: 10px;
}

.admin-page .form-control {
    padding: .7rem .85rem;
}

.admin-page .btn-browse {
    border: 1px solid #dfe5ef;
    color: #526176;
    background: #fff;
    padding: .8rem 1.25rem;
    border-radius: 10px;
}

@media (max-width: 768px) {
    .admin-page .hero-section {
        display: block;
        padding: 2rem 1.35rem;
    }

    .admin-page .hero-section .btn-scan {
        margin-top: 1rem;
    }
}
</style>

<div class="container admin-page">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Hero Section -->
            <div class="hero-section text-center mb-5">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-box-open me-2"></i>
                    Product Management
                </h1>
                <p class="lead mb-4 opacity-75">
                    Add, edit, and manage your product catalog
                </p>
                <a href="?action=add" class="btn btn-scan btn-lg">
                    <i class="fas fa-plus me-2"></i>Add Product
                </a>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Product Form -->
            <?php if ($action === 'add' || $action === 'edit'): ?>
            <div class="feature-card mb-5">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-<?php echo $action === 'add' ? 'plus' : 'edit'; ?> me-2"></i>
                        <?php echo $action === 'add' ? 'Add New Product' : 'Edit Product'; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <?php echo csrf_input(); ?>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold"><i class="fas fa-hashtag me-1 text-primary"></i>SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="sku" 
                                           value="<?php echo htmlspecialchars($editProduct['sku'] ?? ''); ?>" 
                                           required maxlength="40" pattern="[A-Za-z0-9\-]+"
                                           placeholder="e.g., SKU-001">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold"><i class="fas fa-barcode me-1 text-primary"></i>Barcode</label>
                                    <input type="text" class="form-control" name="barcode" 
                                           value="<?php echo htmlspecialchars($editProduct['barcode'] ?? ''); ?>" 
                                           maxlength="64" placeholder="e.g., 8857200535366">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold"><i class="fas fa-tag me-1 text-primary"></i>Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" 
                                           value="<?php echo htmlspecialchars($editProduct['name'] ?? ''); ?>" 
                                           required maxlength="120" placeholder="Enter product name">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold"><i class="fas fa-key me-1 text-primary"></i>Detection Keywords</label>
                                    <input type="text" class="form-control" name="detect_keywords" 
                                           value="<?php echo htmlspecialchars($editProduct['detect_keywords'] ?? ''); ?>" 
                                           maxlength="255" placeholder="herbal, inhaler, traditional">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold"><i class="fas fa-dollar-sign me-1 text-primary"></i>Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                        <input type="number" class="form-control" name="price" 
                                               value="<?php echo htmlspecialchars($editProduct['price'] ?? '0.00'); ?>" 
                                               required step="0.01" min="0" placeholder="0.00">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold"><i class="fas fa-image me-1 text-primary"></i>Product Image</label>
                                    <input type="file" class="form-control" name="image" accept="image/jpeg,image/png,image/webp">
                                    <?php if ($editProduct['image_path']): ?>
                                    <div class="mt-2">
                                        <img src="<?php echo APP_URL; ?>/<?php echo htmlspecialchars($editProduct['image_path']); ?>" 
                                             alt="Current image" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fas fa-align-left me-1 text-primary"></i>Description</label>
                            <textarea class="form-control" name="description" rows="3" 
                                      maxlength="1000" placeholder="Enter product description..."><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="action" value="<?php echo $action; ?>" class="btn btn-scan">
                                <i class="fas fa-save me-2"></i><?php echo $action === 'add' ? 'Add Product' : 'Save Changes'; ?>
                            </button>
                            <a href="?" class="btn btn-browse">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Product List -->
            <?php if ($action !== 'add' && $action !== 'edit'): ?>
            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="card feature-card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-primary bg-opacity-10 text-primary mx-auto">
                                <i class="fas fa-box"></i>
                            </div>
                            <h3 class="fw-bold text-primary mt-3"><?php echo $products ? count($products) : 0; ?></h3>
                            <p class="text-muted mb-0">Total Products</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card feature-card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-success bg-opacity-10 text-success mx-auto">
                                <i class="fas fa-barcode"></i>
                            </div>
                            <h3 class="fw-bold text-success mt-3">
                                <?php echo $products ? count(array_filter($products, fn($p) => !empty($p['barcode']))) : 0; ?>
                            </h3>
                            <p class="text-muted mb-0">With Barcode</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card feature-card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-info bg-opacity-10 text-info mx-auto">
                                <i class="fas fa-image"></i>
                            </div>
                            <h3 class="fw-bold text-info mt-3">
                                <?php echo $products ? count(array_filter($products, fn($p) => !empty($p['image_path']))) : 0; ?>
                            </h3>
                            <p class="text-muted mb-0">With Image</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card feature-card text-center">
                        <div class="card-body">
                            <div class="feature-icon bg-warning bg-opacity-10 text-warning mx-auto">
                                <i class="fas fa-robot"></i>
                            </div>
                            <h3 class="fw-bold text-warning mt-3"><?php echo $products ? count($products) : 0; ?></h3>
                            <p class="text-muted mb-0">AI Ready</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="feature-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Catalog</h5>
                    <div class="d-flex gap-2 align-items-center">
                    <input type="search" class="form-control form-control-sm" id="productFilter" placeholder="Search products..." aria-label="Search products">
                    <a href="?action=add" class="btn btn-primary btn-sm text-nowrap">
                        <i class="fas fa-plus me-1"></i>Add New
                    </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($products)): ?>
                    <div class="empty-state p-5">
                        <i class="fas fa-box-open"></i>
                        <h4 class="text-muted mt-3">No Products Yet</h4>
                        <p class="text-muted mb-4">Start by adding your first product to the catalog.</p>
                        <a href="?action=add" class="btn btn-scan">
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
                                    <th>Barcode</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Keywords</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $prod): ?>
                                <tr data-product-row data-search="<?php echo htmlspecialchars(strtolower($prod['sku'] . ' ' . ($prod['barcode'] ?? '') . ' ' . $prod['name'] . ' ' . ($prod['detect_keywords'] ?? ''))); ?>">
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
                                    <td><small class="text-muted"><?php echo htmlspecialchars($prod['barcode'] ?? '-'); ?></small></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($prod['name']); ?></td>
                                    <td><span class="text-primary fw-bold"><?php echo number_format($prod['price'], 2); ?></span></td>
                                    <td>
                                        <?php if (!empty($prod['detect_keywords'])): ?>
                                        <span class="badge bg-light text-dark"><?php echo htmlspecialchars(substr($prod['detect_keywords'], 0, 20)); ?><?php echo strlen($prod['detect_keywords']) > 20 ? '...' : ''; ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="?action=edit&id=<?php echo $prod['id']; ?>" class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?action=delete&id=<?php echo $prod['id']; ?>" class="btn btn-outline-danger" 
                                               onclick="return confirm('Are you sure you want to delete this product?');" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('productFilter')?.addEventListener('input', function () {
    const query = this.value.trim().toLowerCase();
    document.querySelectorAll('[data-product-row]').forEach((row) => {
        row.hidden = query !== '' && !row.dataset.search.includes(query);
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php
/**
 * Handle product save (add/edit)
 */
function handleProductSave(int $productId = 0): void {
    global $success, $error;
    
    $sku = trim($_POST['sku'] ?? '');
    $barcode = trim($_POST['barcode'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $keywords = trim($_POST['detect_keywords'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    
    if (empty($sku) || empty($name)) {
        $error = 'SKU and Product Name are required.';
        return;
    }
    
    if ($price < 0) {
        $error = 'Price cannot be negative.';
        return;
    }
    
    $pdo = getDBConnection();
    if ($pdo === null) {
        $error = 'Database error.';
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM products WHERE sku = ? AND id != ?");
        $stmt->execute([$sku, $productId]);
        if ($stmt->fetch()) {
            $error = 'SKU already exists.';
            return;
        }
        
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = upload_product_image($_FILES['image'], $productId > 0 ? $productId : time());
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['path'];
                if ($productId > 0) {
                    $oldProduct = get_product($productId);
                    if ($oldProduct && $oldProduct['image_path']) {
                        delete_product_image($oldProduct['image_path']);
                    }
                }
            } else {
                $error = $uploadResult['message'];
                return;
            }
        }
        
        if ($productId > 0) {
            $stmt = $pdo->prepare("UPDATE products SET sku = ?, barcode = ?, name = ?, detect_keywords = ?, price = ?, description = ?, image_path = ? WHERE id = ?");
            $stmt->execute([$sku, $barcode, $name, $keywords, $price, $description, $imagePath, $productId]);
            $success = 'Product updated successfully.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (sku, barcode, name, detect_keywords, price, description, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$sku, $barcode, $name, $keywords, $price, $description, $imagePath]);
            $productId = (int)$pdo->lastInsertId();
            $success = 'Product added successfully.';
        }
        
        redirect_to(APP_URL . '/admin/products.php');
    } catch (Exception $e) {
        error_log('Product save error: ' . $e->getMessage());
        $error = 'Failed to save product. Please try again.';
    }
}

/**
 * Handle product delete
 */
function handleProductDelete(): void {
    global $success, $error;
    
    $productId = (int)($_GET['id'] ?? 0);
    
    if ($productId <= 0) {
        $error = 'Invalid product ID.';
        return;
    }
    
    $pdo = getDBConnection();
    if ($pdo === null) {
        $error = 'Database error.';
        return;
    }
    
    try {
        $product = get_product($productId);
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        
        if ($product && $product['image_path']) {
            delete_product_image($product['image_path']);
        }
        
        $success = 'Product deleted successfully.';
        redirect_to(APP_URL . '/admin/products.php');
    } catch (Exception $e) {
        error_log('Product delete error: ' . $e->getMessage());
        $error = 'Failed to delete product.';
    }
}
