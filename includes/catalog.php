<?php
/**
 * Catalog/product helper functions
 */

// Prevent direct access
if (!defined('APP_NAME')) {
    http_response_code(403);
    exit('Access denied');
}

/**
 * Get all products
 * @param array $options
 * @return array
 */
function get_products(array $options = []): array {
    $pdo = getDBConnection();
    if ($pdo === null) return [];
    
    try {
        $sql = "SELECT * FROM products";
        $params = [];
        
        // Search
        if (!empty($options['search'])) {
            $sql .= " WHERE (name LIKE ? OR sku LIKE ? OR barcode LIKE ? OR description LIKE ?)";
            $search = '%' . $options['search'] . '%';
            $params = [$search, $search, $search, $search];
        }
        
        // Pagination
        if (!empty($options['limit'])) {
            $sql .= " LIMIT " . (int)$options['limit'];
        }
        if (!empty($options['offset'])) {
            $sql .= " OFFSET " . (int)$options['offset'];
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Get products error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get product count
 * @param string $search
 * @return int
 */
function get_product_count(string $search = ''): int {
    $pdo = getDBConnection();
    if ($pdo === null) return 0;
    
    try {
        $sql = "SELECT COUNT(*) FROM products";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " WHERE (name LIKE ? OR sku LIKE ? OR barcode LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params = [$searchParam, $searchParam, $searchParam];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        error_log('Get product count error: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Get product by ID
 * @param int $id
 * @return array|null
 */
function get_product(int $id): ?array {
    $pdo = getDBConnection();
    if ($pdo === null) return null;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        return $product ?: null;
    } catch (Exception $e) {
        error_log('Get product error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Get product by SKU
 * @param string $sku
 * @return array|null
 */
function get_product_by_sku(string $sku): ?array {
    $pdo = getDBConnection();
    if ($pdo === null) return null;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE sku = ? LIMIT 1");
        $stmt->execute([$sku]);
        $product = $stmt->fetch();
        
        return $product ?: null;
    } catch (Exception $e) {
        error_log('Get product by SKU error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Get product by barcode
 * @param string $barcode
 * @return array|null
 */
function get_product_by_barcode(string $barcode): ?array {
    $pdo = getDBConnection();
    if ($pdo === null) return null;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE barcode = ? LIMIT 1");
        $stmt->execute([$barcode]);
        $product = $stmt->fetch();
        
        return $product ?: null;
    } catch (Exception $e) {
        error_log('Get product by barcode error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Get catalog for AI matching
 * @return array
 */
function get_catalog_for_ai(): array {
    $pdo = getDBConnection();
    if ($pdo === null) return [];
    
    try {
        $stmt = $pdo->query("SELECT sku, name, detect_keywords, description FROM products WHERE sku IS NOT NULL AND sku != ''");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Get catalog error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Search products by multiple methods
 * @param string $sku
 * @param string $name
 * @return array|null
 */
function search_products_by_match(string $sku = '', string $name = ''): ?array {
    $pdo = getDBConnection();
    if ($pdo === null) return null;
    
    try {
        // Try SKU first
        if (!empty($sku)) {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE sku = ? LIMIT 1");
            $stmt->execute([$sku]);
            $product = $stmt->fetch();
            if ($product) return $product;
        }
        
        // Try name matching
        if (!empty($name)) {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE MATCH(name, detect_keywords) AGAINST (? IN BOOLEAN MODE) LIMIT 1");
            $stmt->execute([$name]);
            $product = $stmt->fetch();
            if ($product) return $product;
            
            // Fallback to LIKE
            $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR detect_keywords LIKE ? LIMIT 1");
            $searchTerm = '%' . $name . '%';
            $stmt->execute([$searchTerm, $searchTerm]);
            $product = $stmt->fetch();
            if ($product) return $product;
        }
        
        return null;
    } catch (Exception $e) {
        error_log('Search products error: ' . $e->getMessage());
        return null;
    }
}
