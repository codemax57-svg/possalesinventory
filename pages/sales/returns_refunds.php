<?php
/**
 * Returns & Refunds Page
 */

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once '../../includes/sidebar.php';

$db = new Database();
$db->connect();

$page = $_GET['page'] ?? 1;
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

// Get returns
$db->prepare('SELECT r.*, c.customer_name FROM returns_refunds r 
             LEFT JOIN customers c ON r.customer_id = c.id 
             ORDER BY r.created_at DESC 
             LIMIT ? OFFSET ?');
$db->bind('i', $perPage);
$db->bind('i', $offset);
$db->execute();
$returns = $db->fetchAll();
?>

<div class="content-area">
    <div class="row mb-4">
        <div class="col-8">
            <h1 class="h3 mb-1"><i class="fas fa-undo"></i> Returns & Refunds</h1>
        </div>
        <div class="col-4 text-end">
            <a href="#" class="btn btn-primary"><i class="fas fa-plus"></i> New Return</a>
        </div>
    </div>
    
    <?php displayMessageAlert(); ?>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Return #</th>
                            <th>Customer</th>
                            <th>Return Date</th>
                            <th>Reason</th>
                            <th>Refund Amount</th>
                            <th>Refund Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($returns)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> No returns found
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($returns as $ret): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($ret['return_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($ret['customer_name']); ?></td>
                                <td><?php echo formatDate($ret['return_date']); ?></td>
                                <td><?php echo htmlspecialchars($ret['return_reason']); ?></td>
                                <td><?php echo formatCurrency($ret['refund_amount']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusBadgeColor($ret['refund_status']); ?>">
                                        <?php echo ucfirst($ret['refund_status']); ?>
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