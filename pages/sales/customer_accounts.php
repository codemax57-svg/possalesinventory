<?php
/**
 * Customer Accounts (Utang) Page
 */

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once '../../includes/sidebar.php';

$db = new Database();
$db->connect();

$page = $_GET['page'] ?? 1;
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

// Get customer accounts
$db->prepare('SELECT ca.*, c.customer_name FROM customer_accounts ca 
             LEFT JOIN customers c ON ca.customer_id = c.id 
             WHERE ca.status IN ("outstanding", "partial", "overdue") 
             ORDER BY ca.due_date 
             LIMIT ? OFFSET ?');
$db->bind('i', $perPage);
$db->bind('i', $offset);
$db->execute();
$accounts = $db->fetchAll();

// Get total outstanding
$db->prepare('SELECT SUM(balance) as total FROM customer_accounts WHERE status IN ("outstanding", "partial", "overdue")');
$db->execute();
$outstanding = $db->fetch();
$totalOutstanding = $outstanding['total'] ?? 0;
?>

<div class="content-area">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-1"><i class="fas fa-handshake"></i> Customer Accounts (Utang)</h1>
            <p class="text-muted">Credit transactions and customer payment tracking</p>
        </div>
    </div>
    
    <?php displayMessageAlert(); ?>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Outstanding</p>
                    <h4 class="text-danger"><?php echo formatCurrency($totalOutstanding); ?></h4>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-1">Overdue Accounts</p>
                    <h4>
                        <?php
                        $db->prepare('SELECT COUNT(*) as count FROM customer_accounts WHERE status = "overdue"');
                        $db->execute();
                        $overdueCount = $db->fetch();
                        echo $overdueCount['count'] ?? 0;
                        ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Amount Due</th>
                            <th>Balance</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($accounts)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> No outstanding accounts
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($accounts as $acc): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($acc['customer_name']); ?></strong></td>
                                <td><?php echo formatCurrency($acc['amount']); ?></td>
                                <td><?php echo formatCurrency($acc['balance']); ?></td>
                                <td><?php echo formatDate($acc['due_date']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusBadgeColor($acc['status']); ?>">
                                        <?php echo ucfirst($acc['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-receipt"></i> Record Payment</a>
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