<?php
/**
 * Customer scanner page - Mobile-first camera interface
 */
require_once __DIR__ . '/includes/init.php';

$pageTitle = 'Scan Product';
include __DIR__ . '/includes/header.php';
?>

<style>
    .scanner-container {
        position: relative;
        width: 100%;
        max-width: 640px;
        margin: 0 auto;
        background: #000;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }
    
    #videoElement {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .scan-frame {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80%;
        height: 60%;
        border: 3px solid rgba(255, 255, 255, 0.8);
        border-radius: 12px;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);
    }
    
    .scan-frame::before {
        content: '';
        position: absolute;
        top: -3px;
        left: 50%;
        transform: translateX(-50%);
        width: 60%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #00ff88, transparent);
        animation: scanLine 2s ease-in-out infinite;
    }
    
    @keyframes scanLine {
        0%, 100% { top: -3px; }
        50% { top: calc(100% + 3px); }
    }
    
    .corner {
        position: absolute;
        width: 30px;
        height: 30px;
        border-color: #00ff88;
        border-style: solid;
        border-width: 0;
    }
    
    .corner-tl { top: -3px; left: -3px; border-top-width: 4px; border-left-width: 4px; border-radius: 8px 0 0 0; }
    .corner-tr { top: -3px; right: -3px; border-top-width: 4px; border-right-width: 4px; border-radius: 0 8px 0 0; }
    .corner-bl { bottom: -3px; left: -3px; border-bottom-width: 4px; border-left-width: 4px; border-radius: 0 0 0 8px; }
    .corner-br { bottom: -3px; right: -3px; border-bottom-width: 4px; border-right-width: 4px; border-radius: 0 0 8px 0; }
    
    #canvasElement {
        display: none;
    }
    
    .controls {
        display: flex;
        justify-content: center;
        gap: 1rem;
        padding: 1.5rem;
        background: white;
        border-radius: 0 0 16px 16px;
        margin-top: 1rem;
    }
    
    .control-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .control-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .btn-capture {
        background: linear-gradient(135deg, #00c853, #00e676);
        color: white;
        width: 72px;
        height: 72px;
        font-size: 1.8rem;
    }
    
    .btn-capture:hover:not(:disabled) {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 200, 83, 0.4);
    }
    
    .btn-switch {
        background: linear-gradient(135deg, #2196f3, #42a5f5);
        color: white;
    }
    
    .btn-retake {
        background: linear-gradient(135deg, #ff9800, #ffb74d);
        color: white;
    }
    
    .status-bar {
        background: #1a1a2e;
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 12px 12px 0 0;
        text-align: center;
        font-size: 0.9rem;
    }
    
    .product-result {
        display: none;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        margin-top: 1rem;
    }
    
    .product-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
    }
    
    .product-info {
        padding: 1.5rem;
    }
    
    .confidence-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .confidence-high { background: #d4edda; color: #155724; }
    .confidence-medium { background: #fff3cd; color: #856404; }
    .confidence-low { background: #f8d7da; color: #721c24; }
    
    .scanner-status {
        text-align: center;
        padding: 1rem;
    }
    
    .spinner-custom {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <h2 class="text-center mb-4">
            <i class="fas fa-camera text-primary me-2"></i>Scan Product
        </h2>
        
        <!-- Scanner -->
        <div id="scannerSection">
            <div class="scanner-container">
                <div class="status-bar" id="statusBar">
                    <i class="fas fa-circle-notch fa-spin me-2"></i>Starting camera...
                </div>
                <div style="position: relative;">
                    <video id="videoElement" autoplay playsinline></video>
                    <div class="scan-frame">
                        <div class="corner corner-tl"></div>
                        <div class="corner corner-tr"></div>
                        <div class="corner corner-bl"></div>
                        <div class="corner corner-br"></div>
                    </div>
                </div>
                <canvas id="canvasElement"></canvas>
            </div>
            
            <div class="controls">
                <button class="control-btn btn-switch" id="btnSwitch" title="Switch Camera">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button class="control-btn btn-capture" id="btnCapture" title="Capture">
                    <i class="fas fa-camera"></i>
                </button>
                <button class="control-btn btn-retake" id="btnRetake" title="Retake" disabled>
                    <i class="fas fa-redo"></i>
                </button>
            </div>
            
            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Point camera at product barcode or product itself
                </small>
            </div>
        </div>
        
        <!-- Product Result -->
        <div class="product-result" id="productResult">
            <div id="productContent"></div>
            <div class="product-info text-center">
                <button class="btn btn-outline-primary" id="btnNewScan">
                    <i class="fas fa-camera me-2"></i>New Scan
                </button>
            </div>
        </div>
        
        <!-- Error Display -->
        <div class="alert alert-danger d-none" id="errorDisplay" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span id="errorMessage"></span>
        </div>
    </div>
</div>

<!-- Load ZXing library -->
<script src="<?php echo APP_URL; ?>/assets/js/vendor/zxing.min.js"></script>
<script>window.SHOPPING_ASSISTANT_APP_URL = <?php echo json_encode(APP_URL, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;</script>
<script src="<?php echo APP_URL; ?>/assets/js/scan.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
