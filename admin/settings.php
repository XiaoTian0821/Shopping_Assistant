<?php
/**
 * Admin AI Settings Page - Fixed design with proper CSS
 */
require_once __DIR__ . '/../includes/init.php';
require_admin();

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        set_setting('ai_provider', $_POST['ai_provider'] ?? 'gemini');
        set_setting('gemini_api_url', $_POST['gemini_api_url'] ?? '');
        set_setting('gemini_api_key', $_POST['gemini_api_key'] ?? '');
        set_setting('gemini_model', $_POST['gemini_model'] ?? 'gemini-3.8-flash');
        set_setting('gemini_fallback_models', $_POST['gemini_fallback_models'] ?? '');
        set_setting('agnes_api_url', $_POST['agnes_api_url'] ?? '');
        set_setting('agnes_api_key', $_POST['agnes_api_key'] ?? '');
        set_setting('agnes_model', $_POST['agnes_model'] ?? 'agnes-2.5-flash');
        set_setting('agnes_fallback_models', $_POST['agnes_fallback_models'] ?? '');
        
        $success = 'Settings saved successfully.';
    }
}

$settings = get_ai_settings();
$pageTitle = 'AI Settings';
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
    padding: 1.5rem;
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
.help-card, .security-card, .stats-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 1.5rem;
}
.help-card .card-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border: none;
    padding: 1.25rem 1.5rem;
}
.security-card .card-header {
    background: linear-gradient(135deg, #ffc107, #ffa726);
    color: #333;
    border: none;
    padding: 1.25rem 1.5rem;
}
.stats-card .card-header {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
    border: none;
    padding: 1.25rem 1.5rem;
}
.stat-item {
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 0.5rem;
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
.admin-page .hero-section p,
.admin-page .hero-section .btn {
    position: relative;
    z-index: 1;
}

.admin-page .hero-section h1 {
    font-size: clamp(1.7rem, 3vw, 2.4rem);
    margin-bottom: .5rem !important;
}

.admin-page .hero-section .btn-scan {
    background: #fff;
    color: #304b88;
    padding: .8rem 1.25rem;
    border-radius: 12px;
    white-space: nowrap;
}

.admin-page .feature-card,
.admin-page .help-card,
.admin-page .security-card,
.admin-page .stats-card {
    border: 1px solid #e7ebf3;
    border-radius: 18px;
    box-shadow: 0 8px 28px rgba(25, 42, 70, .07);
}

.admin-page .feature-card .card-header,
.admin-page .help-card .card-header,
.admin-page .security-card .card-header,
.admin-page .stats-card .card-header {
    border-bottom: 1px solid #edf0f5;
    padding: 1.15rem 1.4rem;
}

.admin-page .feature-card .card-header {
    background: #fff;
    color: #1d2b45;
}

.admin-page .feature-card .card-body {
    padding: 1.4rem;
}

.admin-page .provider-card {
    border: 1px solid #dfe5ef;
    border-radius: 14px;
    padding: 1.15rem;
    background: #fff;
}

.admin-page .provider-card.selected {
    border-color: #4b6cb7;
    box-shadow: 0 0 0 3px rgba(75, 108, 183, .12);
    background: #f7f9ff;
}

.admin-page .form-control,
.admin-page .input-group-text {
    border-color: #dfe5ef;
    border-radius: 10px;
}

.admin-page .form-control {
    padding: .7rem .85rem;
}

.admin-page .settings-section {
    padding: 1.2rem;
    border: 1px solid #edf0f5;
    border-radius: 14px;
    background: #fbfcfe;
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
                    <i class="fas fa-robot me-2"></i>
                    AI Configuration
                </h1>
                <p class="lead mb-4 opacity-75">
                    Configure your AI provider and API settings
                </p>
                <button type="button" class="btn btn-scan btn-lg" onclick="testConnection()">
                    <i class="fas fa-plug me-2"></i>Test Connection
                </button>
            </div>

            <!-- Alert Messages -->
            <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Main Settings -->
                <div class="col-lg-8">
                    <div class="feature-card mb-5">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Provider Settings</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <?php echo csrf_input(); ?>
                                
                                <!-- Provider Selection -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold"><i class="fas fa-server me-1 text-primary"></i>Select AI Provider</label>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="provider-card <?php echo ($settings['provider'] ?? 'gemini') === 'gemini' ? 'selected' : ''; ?>" onclick="selectProvider('gemini')">
                                                <div class="d-flex align-items-center">
                                                    <div class="provider-icon">
                                                        <i class="fab fa-google text-danger"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 fw-bold">Google Gemini</h6>
                                                        <small class="text-muted">Google's AI vision model</small>
                                                    </div>
                                                    <span class="badge <?php echo ($settings['provider'] ?? 'gemini') === 'gemini' ? 'bg-success' : 'bg-secondary'; ?>">
                                                        <?php echo ($settings['provider'] ?? 'gemini') === 'gemini' ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </div>
                                                <input type="radio" name="ai_provider" value="gemini" <?php echo ($settings['provider'] ?? 'gemini') === 'gemini' ? 'checked' : ''; ?> class="d-none">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="provider-card <?php echo ($settings['provider'] ?? 'gemini') === 'agnes' ? 'selected' : ''; ?>" onclick="selectProvider('agnes')">
                                                <div class="d-flex align-items-center">
                                                    <div class="provider-icon">
                                                        <i class="fas fa-robot text-success"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 fw-bold">Agnes AI</h6>
                                                        <small class="text-muted">OpenAI-compatible API</small>
                                                    </div>
                                                    <span class="badge <?php echo ($settings['provider'] ?? 'gemini') === 'agnes' ? 'bg-success' : 'bg-secondary'; ?>">
                                                        <?php echo ($settings['provider'] ?? 'gemini') === 'agnes' ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </div>
                                                <input type="radio" name="ai_provider" value="agnes" <?php echo ($settings['provider'] ?? 'gemini') === 'agnes' ? 'checked' : ''; ?> class="d-none">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Gemini Settings -->
                                <div id="geminiSettings" class="settings-section">
                                    <h6 class="fw-bold text-primary mb-3"><i class="fab fa-google me-2"></i>Google Gemini Configuration</h6>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><i class="fas fa-link me-1 text-primary"></i>API URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                            <input type="text" class="form-control" name="gemini_api_url" 
                                                   value="<?php echo htmlspecialchars($settings['api_url']); ?>" 
                                                   placeholder="https://generativelanguage.googleapis.com/v1beta">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><i class="fas fa-key me-1 text-primary"></i>API Key</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" name="gemini_api_key" 
                                                   id="geminiApiKey"
                                                   value="<?php echo htmlspecialchars($settings['api_key']); ?>" 
                                                   placeholder="Enter your Gemini API key">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('geminiApiKey')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <?php if (!empty($settings['api_key'])): ?>
                                        <small class="text-muted"><i class="fas fa-shield-alt me-1"></i>Currently set: <?php echo htmlspecialchars(mask_api_key($settings['api_key'])); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><i class="fas fa-microchip me-1 text-primary"></i>Model</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-robot"></i></span>
                                            <input type="text" class="form-control" name="gemini_model" 
                                                   value="<?php echo htmlspecialchars($settings['model']); ?>" 
                                                   placeholder="gemini-3.8-flash">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><i class="fas fa-layer-group me-1 text-primary"></i>Fallback Models</label>
                                        <input type="text" class="form-control" name="gemini_fallback_models" 
                                               value="<?php echo htmlspecialchars($settings['fallback_models']); ?>" 
                                               placeholder="model-1, model-2">
                                    </div>
                                </div>
                                
                                <!-- Agnes AI Settings -->
                                <div id="agnesSettings" class="settings-section" style="display: none;">
                                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-robot me-2"></i>Agnes AI Configuration</h6>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><i class="fas fa-link me-1 text-primary"></i>API URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                            <input type="text" class="form-control" name="agnes_api_url" 
                                                   value="<?php echo htmlspecialchars(get_setting('agnes_api_url', 'https://api.agnes.ai/v1')); ?>" 
                                                   placeholder="https://api.agnes.ai/v1">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><i class="fas fa-key me-1 text-primary"></i>API Key</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" name="agnes_api_key" 
                                                   id="agnesApiKey"
                                                   value="<?php echo htmlspecialchars(get_setting('agnes_api_key', '')); ?>" 
                                                   placeholder="Enter your Agnes AI API key">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('agnesApiKey')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <?php if (!empty(get_setting('agnes_api_key'))): ?>
                                        <small class="text-muted"><i class="fas fa-shield-alt me-1"></i>Currently set: <?php echo htmlspecialchars(mask_api_key(get_setting('agnes_api_key'))); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><i class="fas fa-microchip me-1 text-primary"></i>Model</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-robot"></i></span>
                                            <input type="text" class="form-control" name="agnes_model" 
                                                   value="<?php echo htmlspecialchars(get_setting('agnes_model', 'agnes-2.5-flash')); ?>" 
                                                   placeholder="agnes-2.5-flash">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><i class="fas fa-layer-group me-1 text-primary"></i>Fallback Models</label>
                                        <input type="text" class="form-control" name="agnes_fallback_models" 
                                               value="<?php echo htmlspecialchars(get_setting('agnes_fallback_models', '')); ?>" 
                                               placeholder="model-a, model-b">
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-scan">
                                        <i class="fas fa-save me-2"></i>Save Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Help Card -->
                    <div class="help-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Help & Guides</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="fas fa-key me-2 text-primary"></i>Getting API Keys</h6>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2">
                                    <i class="fab fa-google me-2 text-danger"></i>
                                    <strong>Google Gemini:</strong>
                                    <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-primary">Google AI Studio</a>
                                </li>
                                <li>
                                    <i class="fas fa-robot me-2 text-success"></i>
                                    <strong>Agnes AI:</strong>
                                    Contact Sapiens AI for API access
                                </li>
                            </ul>
                            
                            <h6 class="fw-bold mb-3"><i class="fas fa-lightbulb me-2 text-warning"></i>Recommended Models</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><code class="bg-light px-2 py-1"><?php echo htmlspecialchars($settings['model'] ?? 'gemini-3.8-flash'); ?></code> (Gemini)</li>
                                <li><code class="bg-light px-2 py-1"><?php echo htmlspecialchars(get_setting('agnes_model', 'agnes-2.5-flash')); ?></code> (Agnes AI)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Security Tips -->
                    <div class="security-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Security Tips</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Keep API keys secure and never share them</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Use HTTPS for all API requests</li>
                                <li><i class="fas fa-check text-success me-2"></i>Monitor your API usage and costs</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Statistics -->
                    <div class="stats-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="stat-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-box me-2 text-primary"></i>Total Products</span>
                                    <strong class="text-primary"><?php echo count(get_products()); ?></strong>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-barcode me-2 text-success"></i>With Barcodes</span>
                                    <strong class="text-success">
                                        <?php 
                                        $products = get_products();
                                        $withBarcode = count(array_filter($products, fn($p) => !empty($p['barcode'])));
                                        echo $withBarcode;
                                        ?>
                                    </strong>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-image me-2 text-info"></i>With Images</span>
                                    <strong class="text-info">
                                        <?php 
                                        $withImage = count(array_filter($products, fn($p) => !empty($p['image_path'])));
                                        echo $withImage;
                                        ?>
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Connection Modal -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content feature-card">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plug me-2"></i>Testing AI Connection...</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-5" id="testResult">
                <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                <p class="text-muted">Connecting to AI service...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Provider selection
function selectProvider(provider) {
    document.querySelector('input[name="ai_provider"][value="' + provider + '"]').checked = true;
    document.querySelectorAll('.provider-card').forEach(card => card.classList.remove('selected'));
    document.querySelector('.provider-card[onclick*="' + provider + '"]').classList.add('selected');
    
    const geminiSettings = document.getElementById('geminiSettings');
    const agnesSettings = document.getElementById('agnesSettings');
    
    if (provider === 'gemini') {
        geminiSettings.style.display = 'block';
        agnesSettings.style.display = 'none';
    } else {
        agnesSettings.style.display = 'block';
        geminiSettings.style.display = 'none';
    }
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
}

async function testConnection() {
    const modalElement = document.getElementById('testModal');
    const modal = window.bootstrap && window.bootstrap.Modal
        ? new window.bootstrap.Modal(modalElement)
        : null;
    if (modal) {
        modal.show();
    } else {
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        modalElement.removeAttribute('aria-hidden');
        document.body.classList.add('modal-open');
    }
    
    document.getElementById('testResult').innerHTML = '<i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i><p class="text-muted">Connecting to AI service...</p>';
    
    try {
        const response = await fetch('<?php echo APP_URL; ?>/api/test_ai.php');
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('testResult').innerHTML = '<i class="fas fa-check-circle text-success fa-3x mb-3"></i><h5 class="text-success">Connection Successful!</h5><p class="text-muted">' + data.message + '</p>';
        } else {
            document.getElementById('testResult').innerHTML = '<i class="fas fa-times-circle text-danger fa-3x mb-3"></i><h5 class="text-danger">Connection Failed</h5><p class="text-muted">' + data.message + '</p>';
        }
    } catch (err) {
        document.getElementById('testResult').innerHTML = '<i class="fas fa-times-circle text-danger fa-3x mb-3"></i><h5 class="text-danger">Test Failed</h5><p class="text-muted">' + err.message + '</p>';
    }
}

document.querySelectorAll('[data-bs-dismiss="modal"]').forEach((button) => {
    button.addEventListener('click', function () {
        const modalElement = document.getElementById('testModal');
        if (window.bootstrap && window.bootstrap.Modal) {
            return;
        }
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        modalElement.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
