/**
 * Scanner JavaScript - Camera, Barcode, AI Recognition
 */

(function() {
    'use strict';

    const appUrl = window.SHOPPING_ASSISTANT_APP_URL || '';
    
    // State
    let videoStream = null;
    let currentFacingMode = 'environment';
    let isScanning = true;
    let barcodeReader = null;
    let captureTimeout = null;
    let lastBarcodeResult = null;
    
    // DOM Elements
    const video = document.getElementById('videoElement');
    const canvas = document.getElementById('canvasElement');
    const statusBar = document.getElementById('statusBar');
    const btnCapture = document.getElementById('btnCapture');
    const btnSwitch = document.getElementById('btnSwitch');
    const btnRetake = document.getElementById('btnRetake');
    const scannerSection = document.getElementById('scannerSection');
    const productResult = document.getElementById('productResult');
    const productContent = document.getElementById('productContent');
    const errorDisplay = document.getElementById('errorDisplay');
    const errorMessage = document.getElementById('errorMessage');
    const btnNewScan = document.getElementById('btnNewScan');
    
    // Initialize
    document.addEventListener('DOMContentLoaded', init);
    
    function init() {
        // Check browser support
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showError('Camera is not supported in this browser. Please use a modern browser with HTTPS.');
            return;
        }
        
        // Initialize barcode reader (if ZXing is available)
        initBarcodeReader();
        
        // Setup event listeners
        btnCapture.addEventListener('click', captureImage);
        btnSwitch.addEventListener('click', switchCamera);
        btnRetake.addEventListener('click', resetScanner);
        btnNewScan.addEventListener('click', resetScanner);
        
        // Start camera
        startCamera();
    }
    
    function initBarcodeReader() {
        // Try to use ZXing if available
        if (typeof ZXing !== 'undefined' && ZXing.BarcodeReader) {
            try {
                barcodeReader = new ZXing.BarcodeReader({
                    formats: [
                        ZXing.BarcodeFormat.EAN_13,
                        ZXing.BarcodeFormat.EAN_8,
                        ZXing.BarcodeFormat.UPC_A,
                        ZXing.BarcodeFormat.UPC_E,
                        ZXing.BarcodeFormat.CODE_128,
                        ZXing.BarcodeFormat.CODE_39,
                        ZXing.BarcodeFormat.QR_CODE
                    ]
                });
                
                // Start scanning loop
                startScanLoop();
            } catch (e) {
                console.warn('ZXing initialization failed:', e);
            }
        } else {
            console.log('ZXing not available, using manual capture only');
        }
    }
    
    function startScanLoop() {
        if (!barcodeReader || !video || !isScanning) return;
        
        try {
            const results = barcodeReader.decode(video);
            
            if (results && results.length > 0) {
                const barcode = results[0].text;
                if (barcode !== lastBarcodeResult) {
                    lastBarcodeResult = barcode;
                    handleBarcodeDetected(barcode);
                }
            }
        } catch (e) {
            // Continue scanning
        }
        
        // Schedule next scan
        captureTimeout = setTimeout(startScanLoop, 300);
    }
    
    async function startCamera() {
        updateStatus('Starting camera...', 'fa-circle-notch fa-spin');
        
        try {
            // Stop existing stream
            stopCamera();
            
            const constraints = {
                video: {
                    facingMode: currentFacingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            };
            
            videoStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = videoStream;
            
            await video.play();
            
            updateStatus('Camera ready', 'fa-check-circle text-success');
            
            // Resume barcode scanning
            if (barcodeReader) {
                startScanLoop();
            }
            
        } catch (err) {
            console.error('Camera error:', err);
            
            if (err.name === 'NotAllowedError') {
                showError('Camera permission denied. Please allow camera access and reload the page.');
            } else if (err.name === 'NotFoundError') {
                showError('No camera found on this device.');
            } else if (err.name === 'NotReadableError') {
                showError('Camera is being used by another application.');
            } else {
                showError('Unable to access camera: ' + err.message);
            }
        }
    }
    
    function stopCamera() {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        
        if (captureTimeout) {
            clearTimeout(captureTimeout);
            captureTimeout = null;
        }
        
        lastBarcodeResult = null;
    }
    
    function handleBarcodeDetected(barcode) {
        updateStatus('Barcode detected: ' + barcode, 'fa-barcode text-success');
        
        // Pause scanning
        isScanning = false;
        stopCamera();
        
        // Search for product
        searchProductByBarcode(barcode);
    }
    
    async function searchProductByBarcode(barcode) {
        updateStatus('Searching product...', 'fa-search fa-spin');
        
        try {
            const response = await fetch(appUrl + '/api/barcode.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ barcode: barcode })
            });
            
            const data = await response.json();
            
            if (data.success && data.found) {
                displayProduct(data.product, 'Barcode');
            } else {
                showError(data.message || 'Barcode not found in catalog.');
                // Allow retake
                setTimeout(() => {
                    if (confirm('Product not found. Would you like to try AI recognition instead?')) {
                        captureImage();
                    } else {
                        resetScanner();
                    }
                }, 1500);
            }
        } catch (err) {
            showError('Search failed: ' + err.message);
            resetScanner();
        }
    }
    
    function captureImage() {
        if (!video) return;
        
        updateStatus('Capturing image...', 'fa-camera');
        
        // Draw current frame to canvas
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        
        // Convert to JPEG blob
        canvas.toBlob((blob) => {
            if (!blob) {
                showError('Failed to capture image.');
                return;
            }
            
            // Send to AI recognition
            recognizeImage(blob);
        }, 'image/jpeg', 0.8);
    }
    
    async function recognizeImage(imageBlob) {
        updateStatus('Identifying product with AI...', 'fa-brain fa-spin');
        
        const formData = new FormData();
        formData.append('image', imageBlob, 'capture.jpg');
        
        try {
            const response = await fetch(appUrl + '/api/recognize.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success && data.product) {
                displayProduct(data.product, data.match_method || 'AI Recognition');
            } else {
                showError(data.message || 'Unable to identify product. Please try again.');
                // Enable retake
                setTimeout(() => {
                    if (confirm('Product not identified. Would you like to try again?')) {
                        resetScanner();
                    }
                }, 2000);
            }
        } catch (err) {
            showError('Recognition failed: ' + err.message);
            resetScanner();
        }
    }
    
    function displayProduct(product, method) {
        updateStatus('Product found!', 'fa-check-circle text-success');
        
        const confidence = product.confidence || 1.0;
        const confidencePercent = Math.round(confidence * 100);
        let confidenceClass = 'confidence-high';
        if (confidencePercent < 70) confidenceClass = 'confidence-low';
        else if (confidencePercent < 90) confidenceClass = 'confidence-medium';
        
        const imageUrl = product.image 
            ? appUrl + '/' + product.image 
            : appUrl + '/assets/images/no-image.png';
        
        productContent.innerHTML = `
            <img src="${imageUrl}" class="product-image" alt="${product.name}" onerror="this.src='${appUrl}/assets/images/no-image.png'">
            <div class="product-info">
                <h4 class="mb-2">${product.name}</h4>
                <div class="mb-3">
                    <span class="h3 text-primary mb-0">${formatPrice(product.price)}</span>
                </div>
                <div class="mb-3">
                    <span class="confidence-badge ${confidenceClass}">
                        <i class="fas fa-brain me-1"></i>${confidencePercent}% Confidence
                    </span>
                    <span class="badge bg-secondary ms-2">${method}</span>
                </div>
                <hr>
                <p class="text-muted small mb-1"><strong>SKU:</strong> ${product.sku || 'N/A'}</p>
                ${product.barcode ? `<p class="text-muted small mb-1"><strong>Barcode:</strong> ${product.barcode}</p>` : ''}
                ${product.description ? `<p class="text-muted mt-3">${product.description}</p>` : ''}
            </div>
        `;
        
        scannerSection.style.display = 'none';
        productResult.style.display = 'block';
        btnRetake.disabled = false;
    }
    
    function switchCamera() {
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        updateStatus('Switching camera...', 'fa-sync-alt fa-spin');
        startCamera();
    }
    
    function resetScanner() {
        // Hide result, show scanner
        productResult.style.display = 'none';
        scannerSection.style.display = 'block';
        btnRetake.disabled = true;
        
        // Reset state
        isScanning = true;
        lastBarcodeResult = null;
        hideError();
        
        // Restart camera
        startCamera();
    }
    
    function updateStatus(text, icon = 'fa-circle-notch fa-spin') {
        statusBar.innerHTML = `<i class="fas ${icon} me-2"></i>${text}`;
    }
    
    function showError(message) {
        errorMessage.textContent = message;
        errorDisplay.classList.remove('d-none');
        
        // Auto-hide after 8 seconds
        setTimeout(() => {
            hideError();
        }, 8000);
    }
    
    function hideError() {
        errorDisplay.classList.add('d-none');
    }
    
    function formatPrice(price) {
        const num = parseFloat(price) || 0;
        return '$' + num.toFixed(2);
    }
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', stopCamera);
})();
