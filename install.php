<?php
/**
 * Installation script
 */
// Check if already installed
if (file_exists(__DIR__ . '/config.php') && defined('APP_NAME')) {
    require_once __DIR__ . '/includes/init.php';
    $pdo = getDBConnection();
    if ($pdo !== null) {
        // Check if tables exist
        $tables = ['admins', 'products', 'settings'];
        $stmt = $pdo->query("SHOW TABLES");
        $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $missingTables = array_diff($tables, $existingTables);
        if (empty($missingTables)) {
            header('Location: ' . APP_URL . '/');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - AI Smart Shopping Assistant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .install-card { background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .step { display: none; }
        .step.active { display: block; }
        .progress-circle { width: 60px; height: 60px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; margin: 0 auto 20px; }
        .step.completed .progress-circle { background: #28a745; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="install-card p-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-2"><i class="fas fa-robot text-primary me-2"></i>AI Smart Shopping Assistant</h1>
                        <p class="text-muted">Installation Wizard</p>
                    </div>
                    
                    <!-- Progress -->
                    <div class="d-flex justify-content-between mb-5">
                        <div class="step active" id="step1-indicator">
                            <div class="progress-circle">1</div>
                            <small class="text-muted d-block text-center">Database</small>
                        </div>
                        <div class="step" id="step2-indicator">
                            <div class="progress-circle">2</div>
                            <small class="text-muted d-block text-center">Tables</small>
                        </div>
                        <div class="step" id="step3-indicator">
                            <div class="progress-circle">3</div>
                            <small class="text-muted d-block text-center">Data</small>
                        </div>
                        <div class="step" id="step4-indicator">
                            <div class="progress-circle">4</div>
                            <small class="text-muted d-block text-center">Done</small>
                        </div>
                    </div>
                    
                    <!-- Step 1: Database Connection -->
                    <div class="step active" id="step1">
                        <h5 class="mb-3">Step 1: Database Connection</h5>
                        <div class="mb-3">
                            <label class="form-label">Database Host</label>
                            <input type="text" class="form-control" id="dbHost" value="localhost">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Database Name</label>
                            <input type="text" class="form-control" id="dbName" value="ai_shopping_assistant">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Database User</label>
                            <input type="text" class="form-control" id="dbUser" value="root">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Database Password</label>
                            <input type="password" class="form-control" id="dbPass" value="">
                        </div>
                        <button class="btn btn-primary w-100" onclick="testConnection()">
                            <i class="fas fa-plug me-2"></i>Test Connection
                        </button>
                        <div id="connResult" class="mt-3"></div>
                    </div>
                    
                    <!-- Step 2: Create Tables -->
                    <div class="step" id="step2">
                        <h5 class="mb-3">Step 2: Create Database Tables</h5>
                        <div id="tableResult"></div>
                        <button class="btn btn-primary w-100 mt-3" onclick="createTables()" id="createTablesBtn">
                            <i class="fas fa-database me-2"></i>Create Tables
                        </button>
                    </div>
                    
                    <!-- Step 3: Insert Data -->
                    <div class="step" id="step3">
                        <h5 class="mb-3">Step 3: Insert Sample Data</h5>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="insertAdmin" checked>
                            <label class="form-check-label" for="insertAdmin">
                                Create default admin account
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="insertProducts" checked>
                            <label class="form-check-label" for="insertProducts">
                                Insert sample products
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="insertSettings" checked>
                            <label class="form-check-label" for="insertSettings">
                                Insert default settings
                            </label>
                        </div>
                        <button class="btn btn-primary w-100" onclick="insertData()">
                            <i class="fas fa-seedling me-2"></i>Insert Data
                        </button>
                        <div id="dataResult" class="mt-3"></div>
                    </div>
                    
                    <!-- Step 4: Complete -->
                    <div class="step" id="step4">
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                            </div>
                            <h4 class="mb-3">Installation Complete!</h4>
                            <div class="alert alert-success">
                                <ul class="mb-0">
                                    <li>Database: <strong>Connected</strong></li>
                                    <li>Tables: <strong>Created</strong></li>
                                    <li>Admin Account: <strong>Created</strong></li>
                                    <li>Sample Products: <strong>Inserted</strong></li>
                                </ul>
                            </div>
                            <div class="alert alert-warning">
                                <strong>Default Admin Credentials:</strong><br>
                                Email: <code>admin@example.com</code><br>
                                Password: <code>admin123</code>
                            </div>
                            <p class="text-muted small">Please change the default password after login!</p>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-primary">
                                    <i class="fas fa-redo me-2"></i>Reinstall
                                </a>
                                <a href="<?php echo APP_URL ?? ''; ?>/login.php" class="btn btn-success">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to Admin
                                </a>
                                <a href="<?php echo APP_URL ?? ''; ?>/" class="btn btn-outline-primary">
                                    <i class="fas fa-home me-2"></i>Go to Homepage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let dbConfig = {};
        
        function showStep(num) {
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('[id$="-indicator"]').forEach(s => s.classList.remove('active', 'completed'));
            
            for (let i = 1; i < num; i++) {
                document.getElementById('step' + i + '-indicator').classList.add('completed');
            }
            document.getElementById('step' + num).classList.add('active');
            document.getElementById('step' + num + '-indicator').classList.add('active');
        }
        
        function showResult(elementId, success, message) {
            const el = document.getElementById(elementId);
            el.innerHTML = success 
                ? '<div class="alert alert-success">' + message + '</div>'
                : '<div class="alert alert-danger">' + message + '</div>';
        }
        
        async function testConnection() {
            const host = document.getElementById('dbHost').value;
            const name = document.getElementById('dbName').value;
            const user = document.getElementById('dbUser').value;
            const pass = document.getElementById('dbPass').value;
            
            dbConfig = { host, name, user, pass };
            
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testing...';
            
            try {
                const response = await fetch('install_check.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dbConfig)
                });
                
                const data = await response.json();
                showResult('connResult', data.success, data.message);
                
                if (data.success) {
                    setTimeout(() => showStep(2), 1000);
                }
            } catch (e) {
                showResult('connResult', false, 'Connection test failed: ' + e.message);
            }
            
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plug me-2"></i>Test Connection';
        }
        
        async function createTables() {
            const btn = document.getElementById('createTablesBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
            
            try {
                const response = await fetch('install_check.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ...dbConfig, action: 'create_tables' })
                });
                
                const data = await response.json();
                showResult('tableResult', data.success, data.message);
                
                if (data.success) {
                    setTimeout(() => showStep(3), 1000);
                }
            } catch (e) {
                showResult('tableResult', false, 'Failed: ' + e.message);
            }
            
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-database me-2"></i>Create Tables';
        }
        
        async function insertData() {
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Inserting...';
            
            const data = {
                ...dbConfig,
                action: 'insert_data',
                insertAdmin: document.getElementById('insertAdmin').checked,
                insertProducts: document.getElementById('insertProducts').checked,
                insertSettings: document.getElementById('insertSettings').checked
            };
            
            try {
                const response = await fetch('install_check.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                showResult('dataResult', result.success, result.message);
                
                if (result.success) {
                    setTimeout(() => showStep(4), 1000);
                }
            } catch (e) {
                showResult('dataResult', false, 'Failed: ' + e.message);
            }
            
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-seedling me-2"></i>Insert Data';
        }
    </script>
</body>
</html>
