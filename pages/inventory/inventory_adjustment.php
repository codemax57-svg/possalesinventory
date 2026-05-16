<?php
/**
 * Inventory Adjustment Page
 */

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once '../../includes/sidebar.php';

$db = new Database();
$db->connect();

$page = $_GET['page'] ?? 1;
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

// Get adjustments
$db->prepare('SELECT sa.*, p.product_name FROM stock_adjustments sa 
             LEFT JOIN products p ON sa.product_id = p.id 
             ORDER BY sa.created_at DESC 
             LIMIT ? OFFSET ?');
$db->bind('i', $perPage);
$db->bind('i', $offset);
$db->execute();
$adjustments = $db->fetchAll();
?>

<div class="content-area">
    <div class="row mb-4">
        <div class="col-8">
            <h1 class="h3 mb-1"><i class="fas fa-tools"></i> Inventory Adjustments</h1>
        </div>
        <div class="col-4 text-end">
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAdjustmentModal">
                <i class="fas fa-plus"></i> New Adjustment
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
                            <th>Adjustment #</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity Before</th>
                            <th>Adjusted</th>
                            <th>Quantity After</th>
                            <th>Date</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($adjustments)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> No adjustments found
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($adjustments as $adj): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($adj['adjustment_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($adj['product_name']); ?></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?php echo ucfirst(str_replace('_', ' ', $adj['adjustment_type'])); ?>
                                    </span>
                                </td>
                                <td><?php echo $adj['quantity_before']; ?></td>
                                <td><?php echo $adj['quantity_adjusted']; ?></td>
                                <td><?php echo $adj['quantity_after']; ?></td>
                                <td><?php echo formatDate($adj['created_at']); ?></td>
                                <td><?php echo htmlspecialchars($adj['reason']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Adjustment Modal -->
<div class="modal fade" id="createAdjustmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-tools"></i> Create Inventory Adjustment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo BASE_URL; ?>/api/inventory/create-adjustment.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="adjProduct" class="form-label">Product</label>
                        <select id="adjProduct" name="product_id" class="form-select" required>
                            <option value="">-- Select Product --</option>
                            <?php
                            $db->prepare('SELECT id, product_name FROM products WHERE status = "active" ORDER BY product_name');
                            $db->execute();
                            $products = $db->fetchAll();
                            foreach ($products as $prod):
                            ?>
                            <option value="<?php echo $prod['id']; ?>"><?php echo htmlspecialchars($prod['product_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adjType" class="form-label">Adjustment Type</label>
                        <select id="adjType" name="adjustment_type" class="form-select" required>
                            <option value="damaged">Damaged</option>
                            <option value="lost">Lost</option>
                            <option value="found">Found</option>
                            <option value="manual_correction">Manual Correction</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adjQuantity" class="form-label">Quantity to Adjust</label>
                        <input type="number" id="adjQuantity" name="quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="adjReason" class="form-label">Reason</label>
                        <textarea id="adjReason" name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>