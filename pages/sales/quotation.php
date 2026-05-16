<?php
/**
 * Quotation / Estimate Page
 */

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once '../../includes/sidebar.php';

$db = new Database();
$db->connect();

$page = $_GET['page'] ?? 1;
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

// Get total quotations
$db->prepare('SELECT COUNT(*) as count FROM quotations');
$db->execute();
$countResult = $db->fetch();
$totalQuotations = $countResult['count'];
$totalPages = ceil($totalQuotations / $perPage);

// Get quotations
$db->prepare('SELECT q.*, c.customer_name FROM quotations q 
             LEFT JOIN customers c ON q.customer_id = c.id 
             ORDER BY q.created_at DESC 
             LIMIT ? OFFSET ?');
$db->bind('i', $perPage);
$db->bind('i', $offset);
$db->execute();
$quotations = $db->fetchAll();
?>

<div class="content-area">
    <div class="row mb-4">
        <div class="col-8">
            <h1 class="h3 mb-1"><i class="fas fa-quote-left"></i> Quotations / Estimates</h1>
        </div>
        <div class="col-4 text-end">
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createQuotationModal">
                <i class="fas fa-plus"></i> New Quotation
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
                            <th>Quotation #</th>
                            <th>Customer</th>
                            <th>Quote Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Valid Until</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($quotations)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> No quotations found
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($quotations as $quote): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($quote['quotation_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($quote['customer_name'] ?? '-'); ?></td>
                                <td><?php echo formatDate($quote['quotation_date']); ?></td>
                                <td><?php echo formatCurrency($quote['total_amount']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusBadgeColor($quote['status']); ?>">
                                        <?php echo ucfirst($quote['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($quote['valid_until']); ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>
                                    <a href="#" class="btn btn-sm btn-success"><i class="fas fa-exchange-alt"></i> Convert</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php echo getPaginationLinks($page, $totalPages, BASE_URL . '/pages/sales/quotation.php'); ?>
        </div>
    </div>
</div>

<!-- Create Quotation Modal -->
<div class="modal fade" id="createQuotationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-quote-left"></i> Create New Quotation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo BASE_URL; ?>/api/sales/create-quotation.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quotCustomer" class="form-label">Customer</label>
                        <select id="quotCustomer" name="customer_id" class="form-select" required>
                            <option value="">-- Select Customer --</option>
                            <?php
                            $db->prepare('SELECT id, customer_name FROM customers WHERE status = "active" ORDER BY customer_name');
                            $db->execute();
                            $customers = $db->fetchAll();
                            foreach ($customers as $cust):
                            ?>
                            <option value="<?php echo $cust['id']; ?>"><?php echo htmlspecialchars($cust['customer_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quotDate" class="form-label">Quotation Date</label>
                        <input type="date" id="quotDate" name="quotation_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="quotValid" class="form-label">Valid Until</label>
                        <input type="date" id="quotValid" name="valid_until" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>