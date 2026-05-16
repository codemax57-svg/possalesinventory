<?php
/**
 * Batch / Lot Tracking Page
 */

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once '../../includes/sidebar.php';

$db = new Database();
$db->connect();

$product_id = $_GET['product_id'] ?? null;
$page = $_GET['page'] ?? 1;
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

// Get batches
$query = 'SELECT pb.*, p.product_name FROM product_batches pb LEFT JOIN products p ON pb.product_id = p.id';
$countQuery = 'SELECT COUNT(*) as count FROM product_batches';

if ($product_id) {
    $query .= ' WHERE pb.product_id = ' . intval($product_id);
    $countQuery .= ' WHERE product_id = ' . intval($product_id);
}

$query .= ' ORDER BY pb.manufacture_date DESC LIMIT ? OFFSET ?';

$db->prepare($countQuery);
$db->execute();
$countResult = $db->fetch();
$totalBatches = $countResult['count'];
$totalPages = ceil($totalBatches / $perPage);

$db->prepare($query);
$db->bind('i', $perPage);
$db->bind('i', $offset);
$db->execute();
$batches = $db->fetchAll();
?>

<div class="content-area">
    <div class="row mb-4">
        <div class="col-8">
            <h1 class="h3 mb-1"><i class="fas fa-barcode"></i> Batch / Lot Tracking</h1>
        </div>
        <div class="col-4 text-end">
            <a href="#" class="btn btn-primary"><i class="fas fa-plus"></i> Add Batch</a>
        </div>
    </div>
    
    <?php displayMessageAlert(); ?>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Batch Number</th>
                            <th>Manufacture Date</th>
                            <th>Expiration Date</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($batches)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> No batches found
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($batches as $batch): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($batch['product_name']); ?></td>
                                <td><code><?php echo htmlspecialchars($batch['batch_number']); ?></code></td>
                                <td><?php echo formatDate($batch['manufacture_date']); ?></td>
                                <td><?php echo formatDate($batch['expiration_date']); ?></td>
                                <td><?php echo $batch['quantity']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusBadgeColor($batch['status']); ?>">
                                        <?php echo ucfirst($batch['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>