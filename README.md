# AI Smart Shopping Assistant

A complete PHP-based AI-powered shopping assistant that allows customers to scan product barcodes or take photos to identify products and check prices.

## Features

- **Barcode Scanning**: Use mobile camera to scan EAN-13, EAN-8, UPC-A, UPC-E, Code 128, Code 39, and QR codes
- **AI Image Recognition**: Capture product photos and use AI to identify them from your catalog
- **Product Management**: Full CRUD operations for products with image upload
- **Admin Dashboard**: Track statistics and manage your catalog
- **Mobile-First Design**: Optimized for mobile browsers
- **Secure**: CSRF protection, password hashing, prepared statements

## Technology Stack

### Backend
- PHP 8.3+
- MySQL / MariaDB
- PDO (prepared statements)
- PHP Sessions

### Frontend
- HTML5
- CSS3
- Vanilla JavaScript
- Bootstrap 5 (CDN)
- Font Awesome 6 (CDN)
- Google Fonts (Inter)
- ZXing (barcode detection)

## Requirements

- PHP 8.3+ with the following extensions:
  - `pdo_mysql`
  - `curl`
  - `gd`
  - `fileinfo`
- MySQL 5.7+ or MariaDB 10.3+
- Web server (Apache with mod_rewrite or Nginx)
- HTTPS for camera access (required on production)
- Modern browser with `getUserMedia` support

## Installation

### Option 1: XAMPP (Local Development)

1. Copy the project to `C:\xampp\htdocs\ai-shopping-assistant\`

2. Start Apache and MySQL from XAMPP Control Panel

3. Open your browser and navigate to:
   ```
   http://localhost/ai-shopping-assistant/install.php
   ```

4. Follow the installation wizard:
   - Enter database credentials (default: host=localhost, user=root, password=empty)
   - Create database tables
   - Insert sample data

5. Access the application:
   - **Homepage**: `http://localhost/ai-shopping-assistant/`
   - **Admin Login**: `http://localhost/ai-shopping-assistant/login.php`
   - **Admin Panel**: `http://localhost/ai-shopping-assistant/admin/`

### Option 2: cPanel Deployment

1. Upload all files to `public_html/ai-shopping-assistant/` via FTP or File Manager

2. Create a MySQL database and user via phpMyAdmin

3. Update `includes/config.php` with your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database_name');
   define('DB_USER', 'your_database_user');
   define('DB_PASS', 'your_database_password');
   ```

4. Run the installer:
   ```
   https://yourdomain.com/ai-shopping-assistant/install.php
   ```

5. Access your application:
   ```
   https://yourdomain.com/ai-shopping-assistant/
   ```

## Default Admin Credentials

- **Email**: `admin@example.com`
- **Password**: `admin123`

**Important**: Change the default password after first login!

## AI Configuration

1. Log in to the admin panel
2. Go to **AI Settings**
3. Choose your provider:
   - **Google Gemini**: Get API key from [Google AI Studio](https://aistudio.google.com/app/apikey)
   - **Agnes AI**: Contact Sapiens AI for API access
4. Enter your API key and model name
5. Click **Test Connection** to verify
6. Save settings

### Recommended Models

- Google Gemini: `gemini-2.0-flash`
- Agnes AI: `agnes-2.5-flash`

## Camera Permissions

### Local Development (XAMPP)
- Camera works on `localhost` without HTTPS in most browsers
- Use Chrome or Edge for best compatibility

### Production
- **HTTPS is required** for camera access
- Ensure your domain has a valid SSL certificate
- Test on actual mobile devices

### Browser Support
- Chrome (Android/iOS)
- Safari (iOS)
- Firefox (Android)
- Edge (iOS/Android)

## Barcode Testing

The system supports these barcode formats:
- EAN-13 (13-digit barcodes)
- EAN-8 (8-digit barcodes)
- UPC-A (12-digit barcodes)
- UPC-E (compressed UPC)
- Code 128
- Code 39
- QR Code

Sample barcodes are included in the seed data for testing.

## AI Recognition Testing

To test AI recognition:

1. Add products with images to your catalog
2. Navigate to the scanner page
3. Take a photo of a product
4. The AI will analyze and match it to your catalog

**Note**: AI recognition works best with:
- Clear, well-lit product photos
- Products from your catalog
- Minimal background clutter

## Security Features

- ✅ Password hashing with `password_hash()`
- ✅ CSRF protection on all forms
- ✅ PDO prepared statements (SQL injection prevention)
- ✅ Input validation and sanitization
- ✅ Output escaping with `htmlspecialchars()`
- ✅ Secure file uploads with MIME validation
- ✅ API keys stored server-side only
- ✅ Admin authentication and authorization
- ✅ `.htaccess` protection for sensitive directories

## Project Structure

```
ai-shopping-assistant/
│
├── index.php                  # Homepage with QR code
├── scan.php                   # Customer scanner page
├── products.php               # Public product catalog
├── login.php                  # Admin login
├── logout.php                 # Logout handler
├── install.php                # Installation wizard
├── install_check.php          # Installation API
│
├── admin/
│   ├── index.php              # Admin dashboard
│   ├── products.php           # Product management
│   └── settings.php           # AI settings
│
├── api/
│   ├── barcode.php            # Barcode lookup API
│   ├── recognize.php          # AI recognition API
│   └── test_ai.php            # AI connection test
│
├── includes/
│   ├── config.php             # Configuration
│   ├── db.php                 # Database connection
│   ├── init.php               # Bootstrap
│   ├── auth.php               # Authentication
│   ├── settings.php           # Settings helper
│   ├── catalog.php            # Product catalog functions
│   ├── ai.php                 # AI provider layer
│   ├── images.php             # Image handling
│   ├── header.php             # HTML header
│   └── footer.php             # HTML footer
│
├── assets/
│   ├── css/
│   │   └── app.css            # Custom styles
│   ├── js/
│   │   ├── app.js             # General JS
│   │   └── scan.js            # Scanner JS
│   └── images/
│       └── no-image.png       # Placeholder image
│
├── uploads/
│   ├── products/              # Product images
│   └── cache/                 # Temporary AI images
│
└── sql/
    ├── schema.sql             # Database schema
    └── seed.sql               # Sample data
```

## Troubleshooting

### Camera Not Working
- Ensure HTTPS is enabled (required for production)
- Check browser permissions
- Try a different browser
- Make sure no other app is using the camera

### Barcode Not Detected
- Ensure good lighting
- Hold camera steady
- Make sure barcode is within the scan frame
- Try different angles
- Check that barcode format is supported

### AI Recognition Failing
- Check API key is correctly configured
- Verify internet connection
- Try with better-lit, clearer photos
- Check AI provider status
- Review error logs in `logs/error.log`

### Installation Issues
- Ensure database server is running
- Check MySQL credentials
- Verify database user has CREATE privileges
- Clear browser cache and retry

### 500 Internal Server Error
- Check `logs/error.log` for details
- Ensure PHP 8.3+ is installed
- Verify all required extensions are enabled
- Check file permissions (755 for directories, 644 for files)
- Ensure `.htaccess` is being processed by Apache

## API Endpoints

### Barcode Lookup
```http
POST /api/barcode.php
Content-Type: application/json

{
  "barcode": "8857200535366"
}
```

Response:
```json
{
  "success": true,
  "found": true,
  "product": {
    "id": 1,
    "sku": "SKU-001",
    "barcode": "8857200535366",
    "name": "Thai Herbal Inhaler",
    "price": "5.00",
    "description": "...",
    "image": "uploads/products/p1-abc123.jpg"
  }
}
```

### AI Recognition
```http
POST /api/recognize.php
Content-Type: multipart/form-data

image: [binary image data]
```

### AI Connection Test
```http
GET /api/test_ai.php
```

## Database Schema

### Products Table
```sql
CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(40) NOT NULL UNIQUE,
    barcode VARCHAR(64) NULL,
    name VARCHAR(120) NOT NULL,
    detect_keywords VARCHAR(255) DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    description TEXT NULL,
    image_path VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FULLTEXT INDEX ft_name_keywords (name, detect_keywords)
);
```

### Settings Table
```sql
CREATE TABLE settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NULL
);
```

### Admins Table
```sql
CREATE TABLE admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

## Important Notes

### AI Price Handling
The AI **never** determines prices. It only identifies products. Prices always come from the MySQL database.

### Privacy
- Customer photos are processed temporarily and not stored
- No personal information is collected from customers
- Camera access requires explicit permission

### Security Recommendations
1. Change default admin password immediately
2. Use strong, unique passwords
3. Keep PHP and dependencies updated
4. Enable HTTPS in production
5. Regularly backup your database
6. Monitor API usage and costs

## License

This project is created for educational and demonstration purposes.

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review error logs: `logs/error.log`
3. Verify database connection
4. Check AI API configuration

---

**Developed with ❤️ for student projects and retail demonstrations**
