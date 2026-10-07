<?php
/**
 * Image handling helper functions
 */

// Prevent direct access
if (!defined('APP_NAME')) {
    http_response_code(403);
    exit('Access denied');
}

/**
 * Upload and resize product image
 * @param array $file
 * @param int $productId
 * @return array ['success' => bool, 'path' => string, 'message' => string]
 */
function upload_product_image(array $file, int $productId): array {
    // Validate file
    $validation = validate_image_upload($file);
    if (!$validation['success']) {
        return $validation;
    }
    
    // Create directory if not exists
    if (!is_dir(PRODUCTS_UPLOAD_PATH)) {
        @mkdir(PRODUCTS_UPLOAD_PATH, 0755, true);
    }
    
    // Generate safe filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFilename = 'p' . $productId . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
    $destination = PRODUCTS_UPLOAD_PATH . '/' . $newFilename;
    
    // Resize and save image
    $resized = resize_image($file['tmp_name'], $destination, IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT, IMAGE_COMPRESS_QUALITY);
    
    if (!$resized) {
        // Fallback: just copy the file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'message' => 'Failed to save image.'];
        }
    }
    
    // Set proper permissions
    @chmod($destination, 0644);
    
    return [
        'success' => true,
        'path' => 'uploads/products/' . $newFilename,
        'message' => 'Image uploaded successfully.'
    ];
}

/**
 * Validate image upload
 * @param array $file
 * @return array
 */
function validate_image_upload(array $file): array {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server upload limit.',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form upload limit.',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by extension.',
        ];
        return ['success' => false, 'message' => $errors[$file['error']] ?? 'Upload error.'];
    }
    
    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => 'File is too large. Maximum size is ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB.'];
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, ALLOWED_MIME_TYPES)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and WEBP images are allowed.'];
    }
    
    // Check extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Invalid file extension.'];
    }
    
    // Check if it's actually an image
    $imgInfo = getimagesize($file['tmp_name']);
    if ($imgInfo === false) {
        return ['success' => false, 'message' => 'File is not a valid image.'];
    }
    
    return ['success' => true, 'message' => ''];
}

/**
 * Resize image
 * @param string $source
 * @param string $destination
 * @param int $maxWidth
 * @param int $maxHeight
 * @param int $quality
 * @return bool
 */
function resize_image(string $source, string $destination, int $maxWidth, int $maxHeight, int $quality): bool {
    $imageInfo = getimagesize($source);
    if ($imageInfo === false) {
        return false;
    }
    
    $width = $imageInfo[0];
    $height = $imageInfo[1];
    $mime = $imageInfo['mime'];
    
    // Create image resource
    switch ($mime) {
        case 'image/jpeg':
            $src = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $src = imagecreatefrompng($source);
            break;
        case 'image/webp':
            $src = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }
    
    if ($src === false) {
        return false;
    }
    
    // Calculate new dimensions
    $newWidth = $width;
    $newHeight = $height;
    
    if ($width > $maxWidth || $height > $maxHeight) {
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);
    }
    
    // Create new image
    $dst = imagecreatetruecolor($newWidth, $newHeight);
    
    // Handle transparency for PNG and WEBP
    if ($mime === 'image/png' || $mime === 'image/webp') {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefill($dst, 0, 0, $transparent);
    }
    
    // Resize
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Save
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($dst, $destination, $quality);
            break;
        case 'image/png':
            imagepng($dst, $destination, 9);
            break;
        case 'image/webp':
            imagewebp($dst, $destination, $quality);
            break;
    }
    
    imagedestroy($src);
    imagedestroy($dst);
    
    return true;
}

/**
 * Prepare image for AI (resize and base64 encode)
 * @param string $sourcePath
 * @return array ['success' => bool, 'data' => string|null, 'mime' => string|null, 'message' => string]
 */
function prepare_image_for_ai(string $sourcePath): array {
    // Check file exists
    if (!file_exists($sourcePath)) {
        return ['success' => false, 'message' => 'Image file not found.'];
    }
    
    // Check file size
    $fileSize = filesize($sourcePath);
    if ($fileSize > AI_MAX_IMAGE_SIZE) {
        // Resize first
        $tempPath = CACHE_UPLOAD_PATH . '/ai_temp_' . bin2hex(random_bytes(4)) . '.jpg';
        if (!resize_image($sourcePath, $tempPath, AI_MAX_IMAGE_DIMENSION, AI_MAX_IMAGE_DIMENSION, 80)) {
            return ['success' => false, 'message' => 'Failed to process image.'];
        }
        $sourcePath = $tempPath;
    }
    
    // Read and encode
    $imageData = file_get_contents($sourcePath);
    if ($imageData === false) {
        return ['success' => false, 'message' => 'Failed to read image.'];
    }
    
    $mime = mime_content_type($sourcePath);
    $base64 = base64_encode($imageData);
    
    // Clean up temp file if created
    if (strpos($sourcePath, CACHE_UPLOAD_PATH) !== false) {
        @unlink($sourcePath);
    }
    
    return [
        'success' => true,
        'data' => $base64,
        'mime' => $mime,
        'message' => ''
    ];
}

/**
 * Delete product image
 * @param string $ imagePath
 * @return bool
 */
function delete_product_image(string $imagePath): bool {
    $fullPath = BASE_PATH . '/' . $imagePath;
    if (file_exists($fullPath)) {
        return @unlink($fullPath);
    }
    return true; // Consider it deleted if it doesn't exist
}
