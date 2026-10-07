<?php
/**
 * Main index page - Landing page with QR code
 */
require_once __DIR__ . '/includes/init.php';

$pageTitle = 'Home';
include __DIR__ . '/includes/header.php';

// Auto-detect scan URL
$scanUrl = APP_URL . '/scan.php';

// A phone cannot access the computer through localhost. When this page is
// opened locally, use Apache's LAN address for the QR code and mobile link.
$mobileScanUrl = $scanUrl;
$serverAddress = $_SERVER['SERVER_ADDR'] ?? '';
$hostAddress = gethostbyname(gethostname());
$appUrlParts = parse_url(APP_URL);
$isLocalHost = in_array(strtolower($appUrlParts['host'] ?? ''), ['localhost', '127.0.0.1', '::1'], true);
if ($isLocalHost && (!filter_var($serverAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
    || in_array($serverAddress, ['127.0.0.1', '0.0.0.0'], true))) {
    $serverAddress = $hostAddress;
}
if ($isLocalHost && filter_var($serverAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
    && !in_array($serverAddress, ['127.0.0.1', '0.0.0.0'], true)) {
    $mobileBaseUrl = ($appUrlParts['scheme'] ?? 'http') . '://' . $serverAddress . ($appUrlParts['path'] ?? '');
    $mobileScanUrl = rtrim($mobileBaseUrl, '/') . '/scan.php';
}
?>

<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 3rem 2rem;
    border-radius: 20px;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}
.qr-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}
.qr-card .card-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border: none;
    padding: 1.5rem;
    text-align: center;
}
.qr-card .card-header h4 {
    margin: 0;
    font-weight: 600;
}
.qr-card .card-body {
    padding: 2rem;
}
.qr-code-container {
    text-align: center;
    padding: 1rem;
}
.qr-code-img {
    border: 8px solid white;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    border-radius: 12px;
    max-width: 220px;
    width: 100%;
}
.url-input-group {
    max-width: 500px;
    margin: 0 auto;
}
.feature-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    overflow: hidden;
}
.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
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
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Hero Section -->
            <div class="hero-section text-center mb-5">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="fas fa-robot me-2"></i>
                    AI Smart Shopping Assistant
                </h1>
                <p class="lead mb-4 opacity-75">
                    Scan any product and instantly check its price and information using AI technology.
                </p>
                
                <!-- Scanner Button -->
                <a href="<?php echo $scanUrl; ?>" class="btn btn-scan btn-lg me-3 mb-3">
                    <i class="fas fa-camera me-2"></i>Scan Product
                </a>
                <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-browse btn-lg mb-3">
                    <i class="fas fa-box me-2"></i>Browse Products
                </a>
            </div>
            
            <!-- QR Code Section - Redesigned -->
            <div class="qr-card mb-5">
                <div class="card-header">
                    <h4><i class="fas fa-mobile-alt me-2"></i>Mobile Scanner Access</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-3">Scan with Your Phone</h2>
                        <p class="text-muted lead">
                            Point your phone camera at the QR code below, or visit:
                        </p>
                    </div>
                    
                    <div class="row align-items-center justify-content-center">
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="qr-code-container">
                                <img src="https://quickchart.io/qr?text=<?php echo urlencode($mobileScanUrl); ?>&size=300" 
                                     alt="QR Code for Scanner" 
                                     class="qr-code-img">
                                <p class="mt-2 small text-muted"><i class="fas fa-info-circle me-1"></i>Scan with your phone camera</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="url-input-group">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light"><i class="fas fa-link text-primary"></i></span>
                                    <input type="text" class="form-control" id="scanUrl" value="<?php echo htmlspecialchars($mobileScanUrl); ?>" readonly>
                                    <button class="btn btn-primary" type="button" onclick="copyUrl()">
                                        <i class="fas fa-copy me-1"></i>Copy
                                    </button>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <div class="alert alert-info d-inline-flex align-items-center" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div>
                                        No app download required. Works directly in your mobile browser.
                                    </div>
                                </div>
                                <?php if ($mobileScanUrl !== $scanUrl): ?>
                                <p class="small text-muted mb-0">
                                    Make sure your phone is connected to the same Wi-Fi network as this computer.
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Features Section -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-barcode"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Barcode Scan</h5>
                            <p class="card-text text-muted">
                                Point your camera at any barcode to instantly identify the product.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-success bg-opacity-10 text-success">
                                <i class="fas fa-brain"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">AI Recognition</h5>
                            <p class="card-text text-muted">
                                Take a photo of any product and our AI will identify it from our catalog.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-info bg-opacity-10 text-info">
                                <i class="fas fa-tags"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Instant Prices</h5>
                            <p class="card-text text-muted">
                                Get accurate pricing and product information instantly.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Admin Quick Link -->
            <?php if (is_admin()): ?>
            <div class="text-center">
                <a href="<?php echo APP_URL; ?>/admin/" class="btn btn-outline-secondary">
                    <i class="fas fa-cog me-2"></i>Admin Dashboard
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function copyUrl() {
    const urlInput = document.getElementById('scanUrl');
    urlInput.select();
    urlInput.setSelectionRange(0, 99999); // For mobile devices
    
    navigator.clipboard.writeText(urlInput.value).then(function() {
        const btn = urlInput.nextElementSibling;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
        }, 2000);
    }).catch(function(err) {
        // Fallback for older browsers
        document.execCommand('copy');
        alert('URL copied to clipboard!');
    });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
