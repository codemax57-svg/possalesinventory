<?php
/**
 * Products Management Page
 * Construction POS & Inventory System
 */

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once '../../includes/sidebar.php';

$db = new Database();
$db->connect();

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

if ($action === 'create' || $action === 'edit') {
    if ($action === 'edit' && $id) {
        $db->prepare('SELECT * FROM products WHERE id = ?');
        $db->bind('i', $id);
        $db->execute();
        $product = $db->fetch();
        if (!$product) {
            setMessage('Product not found', 'error');
            redirect(BASE_URL . '/pages/inventory/products.php');
        }
        $title = 'Edit Product';
    } else {
        $product = null;
        $title = 'Create New Product';
    }
    ?>
    <div class="content-area">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-1"><i class="fas fa-box"></i> <?php echo $title; ?></h1>
            </div>
        </div>
        
        <?php displayMessageAlert(); ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Product Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo BASE_URL; ?>/api/inventory/save-product.php" enctype="multipart/form-data">
                            <?php if ($product): ?>
                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="productCode" class="form-label">Product Code</label>
                                    <input type="text" id="productCode" name="product_code" class="form-control" value="<?php echo htmlspecialchars($product['product_code'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="productName" class="form-label">Product Name</label>
                                    <input type="text" id="productName" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['product_name'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="category" class="form-label">Category</label>
                                    <select id="category" name="category_id" class="form-select" required>
                                        <option value="">-- Select Category --</option>
                                        <?php
                                        $db->prepare('SELECT * FROM categories WHERE status = "active" ORDER BY category_name');
                                        $db->execute();
                                        $categories = $db->fetchAll();
                                        foreach ($categories as $cat):
                                        ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo ($product && $product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['category_name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="unit" class="form-label">Unit</label>
                                    <select id="unit" name="unit_id" class="form-select" required>
                                        <option value="">-- Select Unit --</option>
                                        <?php
                                        $db->prepare('SELECT * FROM units ORDER BY unit_name');
                                        $db->execute();
                                        $units = $db->fetchAll();
                                        foreach ($units as $u):
                                        ?>
                                        <option value="<?php echo $u['id']; ?>" <?php echo ($product && $product['unit_id'] == $u['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($u['unit_name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="purchasePrice" class="form-label">Purchase Price</label>
                                    <input type="number" id="purchasePrice" name="purchase_price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product['purchase_price'] ?? '0'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="sellingPrice" class="form-label">Selling Price</label>
                                    <input type="number" id="sellingPrice" name="selling_price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product['selling_price'] ?? '0'); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="reorderLevel" class="form-label">Reorder Level</label>
                                    <input type="number" id="reorderLevel" name="reorder_level" class="form-control" value="<?php echo htmlspecialchars($product['reorder_level'] ?? '10'); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="text-end">
                                <a href="<?php echo BASE_URL; ?>/pages/inventory/products.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    // List products
    $page = $_GET['page'] ?? 1;
    $perPage = ITEMS_PER_PAGE;
    $offset = ($page - 1) * $perPage;
    
    // Get total
    $db->prepare('SELECT COUNT(*) as count FROM products');
    $db->execute();
    $countResult = $db->fetch();
    $totalProducts = $countResult['count'];
    $totalPages = ceil($totalProducts / $perPage);
    
    // Get products
    $db->prepare('SELECT p.*, c.category_name, u.unit_abbreviation FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  LEFT JOIN units u ON p.unit_id = u.id 
                  ORDER BY p.product_name 
                  LIMIT ? OFFSET ?');
    $db->bind('i', $perPage);
    $db->bind('i', $offset);
    $db->execute();
    $products = $db->fetchAll();
    ?>
    <div class="content-area">
        <div class="row mb-4">
            <div class="col-8">
                <h1 class="h3 mb-1"><i class="fas fa-box"></i> Products</h1>
            </div>
            <div class="col-4 text-end">
                <a href="<?php echo BASE_URL; ?>/pages/inventory/products.php?action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Product
                </a>
            </div>
        </div>
        
        <?php displayMessageAlert(); ?>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Selling Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $prod): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($prod['product_code']); ?></code></td>
                                <td><?php echo htmlspecialchars($prod['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($prod['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($prod['unit_abbreviation']); ?></td>
                                <td><?php echo formatCurrency($prod['selling_price']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusBadgeColor($prod['status']); ?>">
                                        <?php echo ucfirst($prod['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/pages/inventory/products.php?action=edit&id=<?php echo $prod['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php echo getPaginationLinks($page, $totalPages, BASE_URL . '/pages/inventory/products.php'); ?>
            </div>
        </div>
    </div>
    <?php
}

require_once '../../includes/footer.php';
?>