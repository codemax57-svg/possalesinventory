<?php
/**
 * Helper Functions
 * Construction POS & Inventory System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

/**
 * Sanitize Input
 */
function sanitize($data) {
    global $db;
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate Email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate Phone Number
 */
function validatePhone($phone) {
    return preg_match('/^[0-9+\-\s()]+$/', $phone);
}

/**
 * Format Currency
 */
function formatCurrency($amount, $symbol = true) {
    $formatted = number_format($amount, DECIMAL_PLACES, NUMBER_DEC_POINT, NUMBER_THOUSANDS_SEP);
    return $symbol ? CURRENCY_SYMBOL . $formatted : $formatted;
}

/**
 * Format Number
 */
function formatNumber($number, $decimals = 2) {
    return number_format($number, $decimals, NUMBER_DEC_POINT, NUMBER_THOUSANDS_SEP);
}

/**
 * Format Date
 */
function formatDate($date) {
    if (empty($date) || $date === '0000-00-00') {
        return '-';
    }
    return date(DISPLAY_DATE_FORMAT, strtotime($date));
}

/**
 * Format DateTime
 */
function formatDateTime($dateTime) {
    if (empty($dateTime) || $dateTime === '0000-00-00 00:00:00') {
        return '-';
    }
    return date(DISPLAY_DATETIME_FORMAT, strtotime($dateTime));
}

/**
 * Generate Unique Transaction Number
 */
function generateTransactionNumber($prefix = 'TXN') {
    return strtoupper($prefix . '-' . date('YmdHis') . '-' . rand(1000, 9999));
}

/**
 * Generate Invoice Number
 */
function generateInvoiceNumber() {
    global $db;
    $db->prepare('SELECT COUNT(*) as count FROM sales_invoices WHERE invoice_date = CURDATE()');
    $result = $db->fetch();
    $count = $result['count'] + 1;
    return 'INV-' . date('YmdHis') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
}

/**
 * Generate Quotation Number
 */
function generateQuotationNumber() {
    global $db;
    $db->prepare('SELECT COUNT(*) as count FROM quotations WHERE quotation_date = CURDATE()');
    $result = $db->fetch();
    $count = $result['count'] + 1;
    return 'QT-' . date('YmdHis') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
}

/**
 * Generate PO Number
 */
function generatePONumber() {
    global $db;
    $db->prepare('SELECT COUNT(*) as count FROM purchase_orders WHERE order_date = CURDATE()');
    $result = $db->fetch();
    $count = $result['count'] + 1;
    return 'PO-' . date('YmdHis') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
}

/**
 * Generate Delivery Receipt Number
 */
function generateDeliveryReceiptNumber() {
    global $db;
    $db->prepare('SELECT COUNT(*) as count FROM delivery_receipts WHERE delivery_date = CURDATE()');
    $result = $db->fetch();
    $count = $result['count'] + 1;
    return 'DR-' . date('YmdHis') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
}

/**
 * Check if User is Logged In
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get Current User ID
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get Current User Info
 */
function getCurrentUserInfo() {
    global $db;
    $user_id = getCurrentUserId();
    if (!$user_id) return null;
    
    $db->prepare('SELECT * FROM users WHERE id = ?');
    $db->bind('i', $user_id);
    $db->execute();
    return $db->fetch();
}

/**
 * Check User Role
 */
function userHasRole($role) {
    $user = getCurrentUserInfo();
    if (!$user) return false;
    
    if (is_array($role)) {
        return in_array($user['role'], $role);
    }
    return $user['role'] === $role;
}

/**
 * Redirect
 */
function redirect($location) {
    header('Location: ' . $location);
    exit;
}

/**
 * Set Session Message
 */
function setMessage($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type; // success, error, warning, info
}

/**
 * Get Session Message
 */
function getMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'info';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Display Message Alert
 */
function displayMessageAlert() {
    $msg = getMessage();
    if ($msg) {
        $alertClass = 'alert-' . ($msg['type'] === 'success' ? 'success' : ($msg['type'] === 'error' ? 'danger' : $msg['type']));
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($msg['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}

/**
 * Get Product Stock Level
 */
function getProductStock($product_id, $warehouse_id = 1) {
    global $db;
    $db->prepare('SELECT quantity_on_hand FROM stock_levels WHERE product_id = ? AND warehouse_id = ?');
    $db->bind('i', $product_id);
    $db->bind('i', $warehouse_id);
    $db->execute();
    $result = $db->fetch();
    return $result ? intval($result['quantity_on_hand']) : 0;
}

/**
 * Check if Stock is Low
 */
function isStockLow($product_id, $warehouse_id = 1) {
    global $db;
    $db->prepare('SELECT p.reorder_level, sl.quantity_on_hand FROM products p 
                  LEFT JOIN stock_levels sl ON p.id = sl.product_id AND sl.warehouse_id = ? 
                  WHERE p.id = ?');
    $db->bind('i', $warehouse_id);
    $db->bind('i', $product_id);
    $db->execute();
    $result = $db->fetch();
    
    if ($result) {
        $stock = $result['quantity_on_hand'] ?? 0;
        $reorder = $result['reorder_level'];
        return $stock <= $reorder;
    }
    return false;
}

/**
 * Get Product Info
 */
function getProductInfo($product_id) {
    global $db;
    $db->prepare('SELECT p.*, c.category_name, u.unit_abbreviation FROM products p
                  LEFT JOIN categories c ON p.category_id = c.id
                  LEFT JOIN units u ON p.unit_id = u.id
                  WHERE p.id = ?');
    $db->bind('i', $product_id);
    $db->execute();
    return $db->fetch();
}

/**
 * Get Customer Info
 */
function getCustomerInfo($customer_id) {
    global $db;
    $db->prepare('SELECT * FROM customers WHERE id = ?');
    $db->bind('i', $customer_id);
    $db->execute();
    return $db->fetch();
}

/**
 * Get Customer Balance
 */
function getCustomerBalance($customer_id) {
    global $db;
    $db->prepare('SELECT current_balance FROM customers WHERE id = ?');
    $db->bind('i', $customer_id);
    $db->execute();
    $result = $db->fetch();
    return $result ? floatval($result['current_balance']) : 0;
}

/**
 * Calculate Total Invoice Amount
 */
function calculateInvoiceTotal($subtotal, $discount = 0, $discountType = 'amount', $includeVAT = true) {
    // Apply discount
    if ($discountType === 'percentage') {
        $discountAmount = $subtotal * ($discount / 100);
    } else {
        $discountAmount = $discount;
    }
    
    $afterDiscount = $subtotal - $discountAmount;
    
    // Apply VAT
    $vatAmount = 0;
    if ($includeVAT) {
        $vatAmount = $afterDiscount * VAT_RATE;
    }
    
    $total = $afterDiscount + $vatAmount;
    
    return [
        'subtotal' => $subtotal,
        'discount_amount' => $discountAmount,
        'vat_amount' => $vatAmount,
        'total' => $total
    ];
}

/**
 * Log Activity
 */
function logActivity($action, $module, $record_id = null, $old_value = null, $new_value = null) {
    global $db;
    $user_id = getCurrentUserId();
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    $db->prepare('INSERT INTO system_logs (user_id, action, module, record_id, old_value, new_value, ip_address, user_agent) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $db->bind('i', $user_id);
    $db->bind('s', $action);
    $db->bind('s', $module);
    $db->bind('i', $record_id);
    $db->bind('s', $old_value);
    $db->bind('s', $new_value);
    $db->bind('s', $ip_address);
    $db->bind('s', $user_agent);
    $db->execute();
}

/**
 * Log Inventory Change
 */
function logInventoryChange($product_id, $warehouse_id, $action, $quantity_before, $quantity_after, $reason) {
    global $db;
    $user_id = getCurrentUserId();
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    $db->prepare('INSERT INTO inventory_audit_log (product_id, warehouse_id, action, quantity_before, quantity_after, change_reason, user_id, ip_address) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $db->bind('i', $product_id);
    $db->bind('i', $warehouse_id);
    $db->bind('s', $action);
    $db->bind('i', $quantity_before);
    $db->bind('i', $quantity_after);
    $db->bind('s', $reason);
    $db->bind('i', $user_id);
    $db->bind('s', $ip_address);
    $db->execute();
}

/**
 * Get Status Badge Color
 */
function getStatusBadgeColor($status) {
    $colors = [
        'active' => 'success',
        'inactive' => 'secondary',
        'paid' => 'success',
        'unpaid' => 'danger',
        'partial' => 'warning',
        'completed' => 'success',
        'pending' => 'warning',
        'draft' => 'light',
        'confirmed' => 'success',
        'cancelled' => 'danger',
        'delivered' => 'success',
        'in_transit' => 'info',
        'overdue' => 'danger',
        'approved' => 'success',
        'rejected' => 'danger'
    ];
    
    return $colors[$status] ?? 'secondary';
}

/**
 * Pagination Helper
 */
function getPaginationLinks($currentPage, $totalPages, $baseUrl) {
    $links = '';
    
    if ($totalPages <= 1) {
        return $links;
    }
    
    // Previous button
    if ($currentPage > 1) {
        $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage - 1) . '">Previous</a></li>';
    } else {
        $links .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=1">1</a></li>';
        if ($startPage > 2) {
            $links .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $links .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $links .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage + 1) . '">Next</a></li>';
    } else {
        $links .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }
    
    return '<ul class="pagination">' . $links . '</ul>';
}

/**
 * Upload File
 */
function uploadFile($file, $directory, $allowedExtensions = null) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload failed'];
    }
    
    if ($allowedExtensions === null) {
        $allowedExtensions = ALLOWED_EXTENSIONS;
    }
    
    $fileSize = $file['size'];
    if ($fileSize > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds maximum allowed'];
    }
    
    $fileName = basename($file['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExt, $allowedExtensions)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    
    $newFileName = uniqid() . '.' . $fileExt;
    $targetPath = $directory . '/' . $newFileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $newFileName, 'path' => $targetPath];
    }
    
    return ['success' => false, 'message' => 'Failed to save file'];
}

/**
 * Get Dashboard Statistics
 */
function getDashboardStats() {
    global $db;
    
    $stats = [];
    
    // Total Sales Today
    $db->prepare('SELECT SUM(total_amount) as total FROM pos_transactions WHERE transaction_date = CURDATE() AND status = "completed"');
    $db->execute();
    $result = $db->fetch();
    $stats['sales_today'] = $result['total'] ?? 0;
    
    // Total Transactions Today
    $db->prepare('SELECT COUNT(*) as count FROM pos_transactions WHERE transaction_date = CURDATE()');
    $db->execute();
    $result = $db->fetch();
    $stats['transactions_today'] = $result['count'] ?? 0;
    
    // Total Products
    $db->prepare('SELECT COUNT(*) as count FROM products WHERE status = "active"');
    $db->execute();
    $result = $db->fetch();
    $stats['total_products'] = $result['count'] ?? 0;
    
    // Low Stock Items
    $db->prepare('SELECT COUNT(*) as count FROM products p WHERE status = "active" AND p.current_stock <= p.reorder_level');
    $db->execute();
    $result = $db->fetch();
    $stats['low_stock_items'] = $result['count'] ?? 0;
    
    // Outstanding Receivables
    $db->prepare('SELECT SUM(balance) as total FROM customer_accounts WHERE status IN ("outstanding", "overdue")');
    $db->execute();
    $result = $db->fetch();
    $stats['receivables'] = $result['total'] ?? 0;
    
    // Outstanding Payables
    $db->prepare('SELECT SUM(total_payable) as total FROM suppliers WHERE status = "active"');
    $db->execute();
    $result = $db->fetch();
    $stats['payables'] = $result['total'] ?? 0;
    
    return $stats;
}
?>