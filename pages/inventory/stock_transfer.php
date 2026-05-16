<?php
/**
 * Stock Transfer Page
 */

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once '../../includes/sidebar.php';

$db = new Database();
$db->connect();

$page = $_GET['page'] ?? 1;
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

// Get transfers
$db->prepare('SELECT st.*, fw.warehouse_name as from_warehouse, tw.warehouse_name as to_warehouse 
             FROM stock_transfers st 
             LEFT JOIN warehouses fw ON st.from_warehouse_id = fw.id 
             LEFT JOIN warehouses tw ON st.to_warehouse_id = tw.id 
             ORDER BY st.transfer_date DESC 
             LIMIT ? OFFSET ?');
$db->bind('i', $perPage);
$db->bind('i', $offset);
$db->execute();
$transfers = $db->fetchAll();
?>

<div class="content-area">
    <div class="row mb-4">
        <div class="col-8">
            <h1 class="h3 mb-1"><i class="fas fa-exchange-alt"></i> Stock Transfer</h1>
        </div>
        <div class="col-4 text-end">
            <a href="#" class="btn btn-primary"><i class="fas fa-plus"></i> New Transfer</a>
        </div>
    </div>
    
    <?php displayMessageAlert(); ?>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Transfer #</th>
                            <th>From Warehouse</th>
                            <th>To Warehouse</th>
                            <th>Transfer Date</th>
                            <th>Expected Arrival</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transfers)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> No transfers found
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($transfers as $trans): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($trans['transfer_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($trans['from_warehouse']); ?></td>
                                <td><?php echo htmlspecialchars($trans['to_warehouse']); ?></td>
                                <td><?php echo formatDate($trans['transfer_date']); ?></td>
                                <td><?php echo formatDate($trans['expected_arrival_date']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusBadgeColor($trans['status']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $trans['status'])); ?>
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