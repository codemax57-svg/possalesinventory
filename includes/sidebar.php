<?php
/**
 * Sidebar Navigation Component
 */

$current_user = getCurrentUserInfo();
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-warehouse"></i> Menu</h4>
        <button class="btn btn-sm btn-light d-md-none float-end" type="button" onclick="toggleSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <li>
            <a href="<?php echo BASE_URL; ?>/index.php" class="<?php echo $current_page === 'index' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        
        <!-- Sales Module -->
        <li>
            <a href="#" class="menu-toggle" onclick="toggleSubmenu(event, 'sales-menu')">
                <i class="fas fa-shopping-cart"></i> Sales & POS
                <i class="fas fa-chevron-down float-end"></i>
            </a>
            <ul class="submenu" id="sales-menu">
                <li><a href="<?php echo BASE_URL; ?>/pages/sales/pos.php" class="<?php echo $current_page === 'pos' ? 'active' : ''; ?>"><i class="fas fa-cash-register"></i> Fast Checkout</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/sales/quotation.php" class="<?php echo $current_page === 'quotation' ? 'active' : ''; ?>"><i class="fas fa-quote-left"></i> Quotations</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/sales/sales_invoice.php" class="<?php echo $current_page === 'sales_invoice' ? 'active' : ''; ?>"><i class="fas fa-file-invoice"></i> Sales Invoices</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/sales/delivery_receipt.php" class="<?php echo $current_page === 'delivery_receipt' ? 'active' : ''; ?>"><i class="fas fa-truck"></i> Delivery Receipts</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/sales/customer_accounts.php" class="<?php echo $current_page === 'customer_accounts' ? 'active' : ''; ?>"><i class="fas fa-handshake"></i> Customer Accounts</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/sales/returns_refunds.php" class="<?php echo $current_page === 'returns_refunds' ? 'active' : ''; ?>"><i class="fas fa-undo"></i> Returns & Refunds</a></li>
            </ul>
        </li>
        
        <!-- Inventory Module -->
        <li>
            <a href="#" class="menu-toggle" onclick="toggleSubmenu(event, 'inventory-menu')">
                <i class="fas fa-boxes"></i> Inventory
                <i class="fas fa-chevron-down float-end"></i>
            </a>
            <ul class="submenu" id="inventory-menu">
                <li><a href="<?php echo BASE_URL; ?>/pages/inventory/stock_monitoring.php" class="<?php echo $current_page === 'stock_monitoring' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Stock Monitoring</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/inventory/products.php" class="<?php echo $current_page === 'products' ? 'active' : ''; ?>"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/inventory/batch_tracking.php" class="<?php echo $current_page === 'batch_tracking' ? 'active' : ''; ?>"><i class="fas fa-barcode"></i> Batch Tracking</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/inventory/inventory_adjustment.php" class="<?php echo $current_page === 'inventory_adjustment' ? 'active' : ''; ?>"><i class="fas fa-tools"></i> Adjustments</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/inventory/stock_transfer.php" class="<?php echo $current_page === 'stock_transfer' ? 'active' : ''; ?>"><i class="fas fa-exchange-alt"></i> Stock Transfer</a></li>
            </ul>
        </li>
        
        <!-- Purchasing Module -->
        <li>
            <a href="#" class="menu-toggle" onclick="toggleSubmenu(event, 'purchasing-menu')">
                <i class="fas fa-file-alt"></i> Purchasing
                <i class="fas fa-chevron-down float-end"></i>
            </a>
            <ul class="submenu" id="purchasing-menu">
                <li><a href="<?php echo BASE_URL; ?>/pages/purchasing/suppliers.php" class="<?php echo $current_page === 'suppliers' ? 'active' : ''; ?>"><i class="fas fa-industry"></i> Suppliers</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/purchasing/purchase_orders.php" class="<?php echo $current_page === 'purchase_orders' ? 'active' : ''; ?>"><i class="fas fa-receipt"></i> Purchase Orders</a></li>
            </ul>
        </li>
        
        <!-- Customers -->
        <li>
            <a href="<?php echo BASE_URL; ?>/pages/customers.php" class="<?php echo $current_page === 'customers' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Customers
            </a>
        </li>
        
        <!-- Reports -->
        <?php if (userHasRole(['admin', 'manager'])): ?>
        <li>
            <a href="#" class="menu-toggle" onclick="toggleSubmenu(event, 'reports-menu')">
                <i class="fas fa-chart-line"></i> Reports
                <i class="fas fa-chevron-down float-end"></i>
            </a>
            <ul class="submenu" id="reports-menu">
                <li><a href="<?php echo BASE_URL; ?>/pages/reports/sales_report.php" class="<?php echo $current_page === 'sales_report' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Sales Report</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/reports/inventory_report.php" class="<?php echo $current_page === 'inventory_report' ? 'active' : ''; ?>"><i class="fas fa-chart-pie"></i> Inventory Report</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/reports/analytics.php" class="<?php echo $current_page === 'analytics' ? 'active' : ''; ?>"><i class="fas fa-analytics"></i> Analytics</a></li>
            </ul>
        </li>
        <?php endif; ?>
        
        <!-- Settings -->
        <?php if (userHasRole(['admin'])): ?>
        <li>
            <a href="#" class="menu-toggle" onclick="toggleSubmenu(event, 'settings-menu')">
                <i class="fas fa-cog"></i> Settings
                <i class="fas fa-chevron-down float-end"></i>
            </a>
            <ul class="submenu" id="settings-menu">
                <li><a href="<?php echo BASE_URL; ?>/pages/settings/users.php" class="<?php echo $current_page === 'users' ? 'active' : ''; ?>"><i class="fas fa-user-tie"></i> Users</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/settings/categories.php" class="<?php echo $current_page === 'categories' ? 'active' : ''; ?>"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/settings/warehouses.php" class="<?php echo $current_page === 'warehouses' ? 'active' : ''; ?>"><i class="fas fa-warehouse"></i> Warehouses</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/settings/system_settings.php" class="<?php echo $current_page === 'system_settings' ? 'active' : ''; ?>"><i class="fas fa-sliders-h"></i> System Settings</a></li>
            </ul>
        </li>
        <?php endif; ?>
    </ul>
</aside>

<script>
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('show');
}

function toggleSubmenu(event, menuId) {
    event.preventDefault();
    const submenu = document.getElementById(menuId);
    const allSubmenus = document.querySelectorAll('.submenu');
    
    allSubmenus.forEach(menu => {
        if (menu.id !== menuId) {
            menu.classList.remove('show');
        }
    });
    
    submenu.classList.toggle('show');
}

// Show submenu if current page is in that menu
document.addEventListener('DOMContentLoaded', function() {
    const activeLinks = document.querySelectorAll('.sidebar-menu a.active');
    activeLinks.forEach(link => {
        const parent = link.closest('.submenu');
        if (parent) {
            parent.classList.add('show');
        }
    });
});
</script>