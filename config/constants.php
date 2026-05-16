<?php
/**
 * Application Constants
 * Construction POS & Inventory System
 */

// Application Settings
define('APP_NAME', 'Construction POS & Inventory System');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'Construction Solutions');
define('APP_YEAR', date('Y'));

// Base URL
define('BASE_URL', (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']));
define('ASSET_URL', BASE_URL . '/assets');

// Paths
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('PAGES_PATH', ROOT_PATH . '/pages');
define('API_PATH', ROOT_PATH . '/api');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// Session Settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('SESSION_WARNING_TIME', 300); // 5 minutes before timeout

// Pagination
define('ITEMS_PER_PAGE', 20);
define('DEFAULT_PAGE', 1);

// Currency
define('CURRENCY', 'PHP');
define('CURRENCY_SYMBOL', '₱');
define('DECIMAL_PLACES', 2);

// VAT Settings
define('VAT_RATE', 0.12); // 12% VAT

// Tax Settings
define('WITHHOLDING_TAX', 0.02); // 2%

// Discount Settings
define('MAX_DISCOUNT_PERCENTAGE', 50); // Maximum discount allowed
define('MIN_CART_AMOUNT_FOR_DISCOUNT', 0); // Minimum amount for discount

// Stock Settings
define('LOW_STOCK_THRESHOLD_PERCENT', 0.25); // Alert when stock is 25% or less

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_MANAGER', 'manager');
define('ROLE_CASHIER', 'cashier');
define('ROLE_INVENTORY', 'inventory_staff');

// Payment Methods
$PAYMENT_METHODS = [
    'cash' => 'Cash',
    'check' => 'Check',
    'credit_card' => 'Credit Card',
    'debit_card' => 'Debit Card',
    'gcash' => 'GCash',
    'bank_transfer' => 'Bank Transfer'
];

// Transaction Status
$TRANSACTION_STATUS = [
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
    'refunded' => 'Refunded',
    'pending' => 'Pending'
];

// Invoice Status
$INVOICE_STATUS = [
    'draft' => 'Draft',
    'confirmed' => 'Confirmed',
    'cancelled' => 'Cancelled'
];

// Payment Status
$PAYMENT_STATUS = [
    'unpaid' => 'Unpaid',
    'partial' => 'Partial Payment',
    'paid' => 'Paid'
];

// Quotation Status
$QUOTATION_STATUS = [
    'draft' => 'Draft',
    'sent' => 'Sent',
    'converted' => 'Converted to Invoice',
    'expired' => 'Expired',
    'rejected' => 'Rejected'
];

// Delivery Receipt Status
$DELIVERY_STATUS = [
    'pending' => 'Pending',
    'in_transit' => 'In Transit',
    'delivered' => 'Delivered',
    'returned' => 'Returned'
];

// Customer Account Status
$ACCOUNT_STATUS = [
    'outstanding' => 'Outstanding',
    'partial' => 'Partial Payment',
    'paid' => 'Paid',
    'overdue' => 'Overdue'
];

// Return Reason Categories
$RETURN_REASONS = [
    'damaged' => 'Damaged',
    'defective' => 'Defective',
    'wrong_item' => 'Wrong Item',
    'duplicate' => 'Duplicate Order',
    'customer_request' => 'Customer Request'
];

// Stock Adjustment Types
$ADJUSTMENT_TYPES = [
    'damaged' => 'Damaged Items',
    'lost' => 'Lost Stock',
    'found' => 'Found Stock',
    'manual_correction' => 'Manual Correction',
    'return' => 'Return from Customer'
];

// Email Settings (for future implementation)
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USER', 'your-email@gmail.com');
define('MAIL_PASS', 'your-password');
define('MAIL_FROM', 'noreply@constructionpos.com');
define('MAIL_FROM_NAME', 'Construction POS System');

// File Upload Settings
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv']);

// Date Format
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'm/d/Y');
define('DISPLAY_DATETIME_FORMAT', 'm/d/Y h:i A');

// Number Format
define('NUMBER_DECIMALS', 2);
define('NUMBER_DEC_POINT', '.');
define('NUMBER_THOUSANDS_SEP', ',');

// Log Retention
define('LOG_RETENTION_DAYS', 90);

// Receipt Settings
define('RECEIPT_PRINTER_NAME', 'Thermal Printer');
define('RECEIPT_WIDTH', 80); // mm

// Business Information
define('BUSINESS_NAME', 'Construction Supply Store');
define('BUSINESS_ADDRESS', 'Your Business Address');
define('BUSINESS_PHONE', '+63-2-XXXX-XXXX');
define('BUSINESS_EMAIL', 'info@constructionsupply.com');
define('BUSINESS_TIN', '000-000-000-000');
?>