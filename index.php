<?php
/**
 * Dashboard / Home Page
 * Construction POS & Inventory System
 */

require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'includes/sidebar.php';

// Get dashboard statistics
$stats = getDashboardStats();
$current_user = getCurrentUserInfo();
?>

<div class="content-area">
    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-1"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            <p class="text-muted">Welcome back, <strong><?php echo htmlspecialchars($current_user['full_name']); ?></strong></p>
        </div>
    </div>
    
    <?php displayMessageAlert(); ?>
    
    <!-- Key Metrics Row 1 -->
    <div class="row mb-4">
        <!-- Sales Today -->
        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card border-top-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Sales Today</p>
                            <h4 class="mb-0"><?php echo formatCurrency($stats['sales_today']); ?></h4>
                        </div>
                        <div style="font-size: 2.5rem; color: #0d6efd; opacity: 0.2;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Transactions Today -->
        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card border-top-primary" style="border-top-color: #198754 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Transactions Today</p>
                            <h4 class="mb-0"><?php echo $stats['transactions_today']; ?></h4>
                        </div>
                        <div style="font-size: 2.5rem; color: #198754; opacity: 0.2;">
                            <i class="fas fa-cash-register"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Products -->
        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card border-top-primary" style="border-top-color: #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Products</p>
                            <h4 class="mb-0"><?php echo $stats['total_products']; ?></h4>
                        </div>
                        <div style="font-size: 2.5rem; color: #ffc107; opacity: 0.2;">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Low Stock Items -->
        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card border-top-primary" style="border-top-color: #dc3545 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Low Stock Items</p>
                            <h4 class="mb-0"><?php echo $stats['low_stock_items']; ?></h4>
                        </div>
                        <div style="font-size: 2.5rem; color: #dc3545; opacity: 0.2;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Key Metrics Row 2 -->
    <div class="row mb-4">
        <!-- Outstanding Receivables -->
        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card border-top-primary" style="border-top-color: #17a2b8 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Outstanding Receivables</p>
                            <h4 class="mb-0"><?php echo formatCurrency($stats['receivables']); ?></h4>
                        </div>
                        <div style="font-size: 2.5rem; color: #17a2b8; opacity: 0.2;">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Outstanding Payables -->
        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="card border-top-primary" style="border-top-color: #6f42c1 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Outstanding Payables</p>
                            <h4 class="mb-0"><?php echo formatCurrency($stats['payables']); ?></h4>
                        </div>
                        <div style="font-size: 2.5rem; color: #6f42c1; opacity: 0.2;">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="<?php echo BASE_URL; ?>/pages/sales/pos.php" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-cash-register"></i><br>POS Checkout
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="<?php echo BASE_URL; ?>/pages/sales/quotation.php" class="btn btn-info btn-lg w-100">
                                <i class="fas fa-quote-left"></i><br>New Quotation
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="<?php echo BASE_URL; ?>/pages/sales/sales_invoice.php" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-file-invoice"></i><br>Sales Invoice
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="<?php echo BASE_URL; ?>/pages/inventory/products.php" class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-box"></i><br>Products
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="<?php echo BASE_URL; ?>/pages/customers.php" class="btn btn-secondary btn-lg w-100">
                                <i class="fas fa-users"></i><br>Customers
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="<?php echo BASE_URL; ?>/pages/purchasing/suppliers.php" class="btn btn-dark btn-lg w-100">
                                <i class="fas fa-industry"></i><br>Suppliers
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Information Cards -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> System Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Application:</strong></td>
                            <td><?php echo APP_NAME; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Version:</strong></td>
                            <td><?php echo APP_VERSION; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Logged User:</strong></td>
                            <td><?php echo htmlspecialchars($current_user['full_name']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Role:</strong></td>
                            <td><span class="badge bg-primary"><?php echo ucfirst(str_replace('_', ' ', $current_user['role'])); ?></span></td>
                        </tr>
                        <tr>
                            <td><strong>Login Time:</strong></td>
                            <td><?php echo formatDateTime($_SESSION['login_time'] ?? date('Y-m-d H:i:s')); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-building"></i> Business Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Business Name:</strong></td>
                            <td><?php echo BUSINESS_NAME; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Address:</strong></td>
                            <td><?php echo BUSINESS_ADDRESS; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td><?php echo BUSINESS_PHONE; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><?php echo BUSINESS_EMAIL; ?></td>
                        </tr>
                        <tr>
                            <td><strong>TIN:</strong></td>
                            <td><?php echo BUSINESS_TIN; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>