<?php
/**
 * Stock Monitoring Page
 * Construction POS & Inventory System
 */

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once '../../includes/sidebar.php';

$db = new Database();
$db->connect();

$page = $_GET['page'] ?? 1;
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

// Get total products
$db->prepare('SELECT COUNT(*) as count FROM products WHERE status = "active"');
$db->execute();
$countResult = $db->fetch();
$totalProducts = $countResult['count'];
$totalPages = ceil($totalProducts / $perPage);

// Get products with stock levels
$db->prepare('SELECT p.*, c.category_name, u.unit_abbreviation, sl.quantity_on_hand 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             LEFT JOIN units u ON p.unit_id = u.id 
             LEFT JOIN stock_levels sl ON p.id = sl.product_id AND sl.warehouse_id = 1 
             WHERE p.status = "active" 
             ORDER BY p.product_name 
             LIMIT ? OFFSET ?');
$db->bind('i', $perPage);
$db->bind('i', $offset);
$db->execute();
$products = $db->fetchAll();
?>

<div class="content-area">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-1"><i class="fas fa-chart-bar"></i> Stock Monitoring</h1>
            <p class="text-muted">Real-time inventory tracking and stock level management</p>
        </div>
    </div>
    
    <?php displayMessageAlert(); ?>
    
    <!-- Stock Overview Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Products</p>
                    <h4 class="mb-0"><?php echo $totalProducts; ?></h4>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-1">Low Stock Items</p>
                    <h4 class="mb-0" style="color: #dc3545;">
                        <?php
                        $db->prepare('SELECT COUNT(*) as count FROM products p WHERE status = "active" AND p.current_stock <= p.reorder_level');
                        $db->execute();
                        $lowStock = $db->fetch();
                        echo $lowStock['count'];
                        ?>
                    </h4>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-1">Out of Stock</p>
                    <h4 class="mb-0" style="color: #ff6b6b;">
                        <?php
                        $db->prepare('SELECT COUNT(*) as count FROM products p WHERE status = "active" AND p.current_stock = 0');
                        $db->execute();
                        $outOfStock = $db->fetch();
                        echo $outOfStock['count'];
                        ?>
                    </h4>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-1">Adequate Stock</p>
                    <h4 class="mb-0" style="color: #198754;">
                        <?php
                        $db->prepare('SELECT COUNT(*) as count FROM products p WHERE status = "active" AND p.current_stock > p.reorder_level');
                        $db->execute();
                        $adequateStock = $db->fetch();
                        echo $adequateStock['count'];
                        ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" id="productSearch" class="form-control" placeholder="Search by product name or code...">
                </div>
                <div class="col-md-3">
                    <select id="categoryFilter" class="form-select">
                        <option value="">All Categories</option>
                        <?php
                        $db->prepare('SELECT * FROM categories WHERE status = "active" ORDER BY category_name');
                        $db->execute();
                        $categories = $db->fetchAll();
                        foreach ($categories as $cat):
                        ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="stockFilter" class="form-select">
                        <option value="">All Stock Levels</option>
                        <option value="low">Low Stock Only</option>
                        <option value="out">Out of Stock</option>
                        <option value="adequate">Adequate Stock</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Products Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="stockTable">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th>Current Stock</th>
                            <th>Reorder Level</th>
                            <th>Stock Status</th>
                            <th>Unit Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $prod):
                            $stock = $prod['quantity_on_hand'] ?? 0;
                            $reorder = $prod['reorder_level'];
                            
                            if ($stock == 0) {
                                $statusBadge = '<span class="badge bg-danger">Out of Stock</span>';
                                $statusClass = 'text-danger';
                            } elseif ($stock <= $reorder) {
                                $statusBadge = '<span class="badge bg-warning">Low Stock</span>';
                                $statusClass = 'text-warning';
                            } else {
                                $statusBadge = '<span class="badge bg-success">Adequate</span>';
                                $statusClass = 'text-success';
                            }
                        ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($prod['product_code']); ?></code></td>
                            <td><strong><?php echo htmlspecialchars($prod['product_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($prod['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($prod['unit_abbreviation']); ?></td>
                            <td class="<?php echo $statusClass; ?>"><strong><?php echo $stock; ?></strong></td>
                            <td><?php echo $reorder; ?></td>
                            <td><?php echo $statusBadge; ?></td>
                            <td><?php echo formatCurrency($prod['selling_price']); ?></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/pages/inventory/products.php?action=edit&id=<?php echo $prod['id']; ?>" class="btn btn-sm btn-info" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/pages/inventory/batch_tracking.php?product_id=<?php echo $prod['id']; ?>" class="btn btn-sm btn-primary" title="Batches">
                                    <i class="fas fa-barcode"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php echo getPaginationLinks($page, $totalPages, BASE_URL . '/pages/inventory/stock_monitoring.php'); ?>
        </div>
    </div>
</div>

<script>
// Client-side filtering
document.getElementById('stockFilter').addEventListener('change', function() {
    const filter = this.value;
    const rows = document.querySelectorAll('#stockTable tbody tr');
    
    rows.forEach(row => {
        let show = true;
        if (filter === 'low') {
            show = row.textContent.includes('Low Stock');
        } else if (filter === 'out') {
            show = row.textContent.includes('Out of Stock');
        } else if (filter === 'adequate') {
            show = row.textContent.includes('Adequate');
        }
        row.style.display = show ? '' : 'none';
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>