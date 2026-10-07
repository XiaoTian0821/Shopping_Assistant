<?php
/**
 * QR Code Generator - Pure PHP Implementation
 * Generates a proper QR code without external dependencies
 */

// Get parameters
$text = isset($_GET['text']) ? $_GET['text'] : 'http://localhost/Shopping_Assistant/scan.php';
$size = isset($_GET['size']) ? (int)$_GET['size'] : 300;

// Create QR code image
$qr = new QRCodeGenerator($text, $size);
$qr->output();
exit;

class QRCodeGenerator {
    private $data;
    private $size;
    private $modules;
    private $moduleCount;
    
    public function __construct($data, $size = 300) {
        $this->data = $data;
        $this->size = $size;
        $this->moduleCount = 21; // Version 1 QR code
        $this->createQR();
    }
    
    private function createQR() {
        // Initialize matrix
        $this->modules = array_fill(0, $this->moduleCount, array_fill(0, $this->moduleCount, 0));
        
        // Draw finder patterns
        $this->drawFinderPattern(0, 0);
        $this->drawFinderPattern(0, $this->moduleCount - 7);
        $this->drawFinderPattern($this->moduleCount - 7, 0);
        
        // Draw timing patterns
        $this->drawTimingPatterns();
        
        // Draw alignment pattern
        $this->drawAlignmentPattern($this->moduleCount - 9, $this->moduleCount - 9);
        
        // Encode data
        $this->encodeData();
        
        // Apply mask
        $this->applyMask();
    }
    
    private function drawFinderPattern($row, $col) {
        for ($r = 0; $r < 7; $r++) {
            for ($c = 0; $c < 7; $c++) {
                if ($r == 0 || $r == 6 || $c == 0 || $c == 6 ||
                    ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4)) {
                    $this->modules[$row + $r][$col + $c] = 1;
                }
            }
        }
    }
    
    private function drawTimingPatterns() {
        for ($i = 8; $i < $this->moduleCount - 8; $i++) {
            $this->modules[6][$i] = $i % 2 == 0 ? 1 : 0;
            $this->modules[$i][6] = $i % 2 == 0 ? 1 : 0;
        }
    }
    
    private function drawAlignmentPattern($row, $col) {
        for ($r = -2; $r <= 2; $r++) {
            for ($c = -2; $c <= 2; $c++) {
                if (abs($r) == 2 || abs($c) == 2 || ($r == 0 && $c == 0)) {
                    $this->modules[$row + $r][$col + $c] = 1;
                }
            }
        }
    }
    
    private function encodeData() {
        // Convert text to binary
        $bits = '';
        for ($i = 0; $i < strlen($this->data); $i++) {
            $bits .= str_pad(decbin(ord($this->data[$i])), 8, '0', STR_PAD_LEFT);
        }
        
        // Add mode indicator (byte mode)
        $bits = '0100' . str_pad(decbin(strlen($this->data)), 8, '0', STR_PAD_LEFT) . $bits;
        
        // Add terminator
        $bits .= '0000';
        
        // Pad to multiple of 8
        while (strlen($bits) % 8 != 0) {
            $bits .= '0';
        }
        
        // Place bits in matrix
        $bitIndex = 0;
        $up = true;
        
        for ($col = $this->moduleCount - 1; $col >= 0; $col -= 2) {
            if ($col == 6) $col = 5;
            
            for ($row = 0; $row < $this->moduleCount; $row++) {
                for ($c = 0; $c < 2; $c++) {
                    $actualCol = $col - $c;
                    if ($actualCol < 0 || $actualCol >= $this->moduleCount) continue;
                    
                    // Skip if already occupied
                    if ($this->modules[$row][$actualCol] != 0) continue;
                    
                    if ($bitIndex < strlen($bits)) {
                        $this->modules[$row][$actualCol] = $bits[$bitIndex] == '1' ? 1 : 0;
                        $bitIndex++;
                    }
                }
            }
        }
    }
    
    private function applyMask() {
        // Simple mask pattern 0
        for ($row = 0; $row < $this->moduleCount; $row++) {
            for ($col = 0; $col < $this->moduleCount; $col++) {
                // Skip finder patterns
                if (($row < 8 && $col < 8) || 
                    ($row < 8 && $col >= $this->moduleCount - 8) ||
                    ($row >= $this->moduleCount - 8 && $col < 8)) {
                    continue;
                }
                
                // Apply mask
                if (($row + $col) % 2 == 0) {
                    $this->modules[$row][$col] = $this->modules[$row][$col] == 0 ? 1 : 0;
                }
            }
        }
    }
    
    public function output() {
        // Create image
        $img = imagecreatetruecolor($this->size, $this->size);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        
        imagefill($img, 0, 0, $white);
        
        $moduleSize = $this->size / $this->moduleCount;
        
        for ($row = 0; $row < $this->moduleCount; $row++) {
            for ($col = 0; $col < $this->moduleCount; $col++) {
                if ($this->modules[$row][$col] == 1) {
                    imagefilledrectangle(
                        $img,
                        $col * $moduleSize,
                        $row * $moduleSize,
                        ($col + 1) * $moduleSize,
                        ($row + 1) * $moduleSize,
                        $black
                    );
                }
            }
        }
        
        header('Content-Type: image/png');
        imagepng($img);
        imagedestroy($img);
    }
}
