<?php
/**
 * Delivery Receipt Page
 */

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once '../../includes/sidebar.php';

$db = new Database();
$db->connect();

$page = $_GET['page'] ?? 1;
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

// Get delivery receipts
$db->prepare('SELECT dr.*, c.customer_name FROM delivery_receipts dr 
             LEFT JOIN customers c ON dr.customer_id = c.id 
             ORDER BY dr.delivery_date DESC 
             LIMIT ? OFFSET ?');
$db->bind('i', $perPage);
$db->bind('i', $offset);
$db->execute();
$deliveryReceipts = $db->fetchAll();
?>

<div class="content-area">
    <div class="row mb-4">
        <div class="col-8">
            <h1 class="h3 mb-1"><i class="fas fa-truck"></i> Delivery Receipts</h1>
        </div>
        <div class="col-4 text-end">
            <a href="#" class="btn btn-primary"><i class="fas fa-plus"></i> Create Receipt</a>
        </div>
    </div>
    
    <?php displayMessageAlert(); ?>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Customer</th>
                            <th>Delivery Date</th>
                            <th>Recipient</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($deliveryReceipts)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> No delivery receipts found
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($deliveryReceipts as $dr): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($dr['receipt_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($dr['customer_name']); ?></td>
                                <td><?php echo formatDate($dr['delivery_date']); ?></td>
                                <td><?php echo htmlspecialchars($dr['recipient_name'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusBadgeColor($dr['status']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $dr['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>
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