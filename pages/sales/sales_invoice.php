<?php
/**
 * Sales Invoice Page
 * Construction POS & Inventory System
 */

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once '../../includes/sidebar.php';

$db = new Database();
$db->connect();

// Get action parameter
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

if ($action === 'create') {
    // Create new invoice
    ?>
    <div class="content-area">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-1"><i class="fas fa-file-invoice"></i> Create Sales Invoice</h1>
            </div>
        </div>
        
        <?php displayMessageAlert(); ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Invoice Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo BASE_URL; ?>/api/sales/create-invoice.php">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="customer" class="form-label">Customer</label>
                                    <select id="customer" name="customer_id" class="form-select" required>
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
                                <div class="col-md-6">
                                    <label for="invoiceDate" class="form-label">Invoice Date</label>
                                    <input type="date" id="invoiceDate" name="invoice_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="dueDate" class="form-label">Due Date</label>
                                    <input type="date" id="dueDate" name="due_date" class="form-control">
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="table-responsive">
                                <table class="table" id="invoiceItems">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Line Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoiceItemsBody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No items added yet</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="text-end mb-3">
                                <button type="button" class="btn btn-sm btn-primary" onclick="addInvoiceItem()">
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span>Subtotal:</span>
                                            <strong id="invoiceSubtotal">₱0.00</strong>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="row">
                                            <div class="col-6">Discount (%):</div>
                                            <div class="col-6">
                                                <input type="number" id="invoiceDiscount" name="discount_percentage" class="form-control form-control-sm" value="0" min="0" onchange="calculateInvoiceTotal()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span>Discount Amount:</span>
                                            <strong id="invoiceDiscountAmount">₱0.00</strong>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span>VAT (12%):</span>
                                            <strong id="invoiceVAT">₱0.00</strong>
                                        </div>
                                    </div>
                                    <div class="mb-3" style="border-top: 2px solid #dee2e6; padding-top: 1rem;">
                                        <div class="d-flex justify-content-between" style="font-size: 1.2rem;">
                                            <strong>Total:</strong>
                                            <strong id="invoiceTotal" style="color: #0d6efd;">₱0.00</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <a href="<?php echo BASE_URL; ?>/pages/sales/sales_invoice.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Invoice</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    // List invoices
    $page = $_GET['page'] ?? 1;
    $perPage = ITEMS_PER_PAGE;
    $offset = ($page - 1) * $perPage;
    
    // Get total count
    $db->prepare('SELECT COUNT(*) as count FROM sales_invoices');
    $db->execute();
    $countResult = $db->fetch();
    $totalInvoices = $countResult['count'];
    $totalPages = ceil($totalInvoices / $perPage);
    
    // Get invoices
    $db->prepare('SELECT si.*, c.customer_name FROM sales_invoices si 
                  LEFT JOIN customers c ON si.customer_id = c.id 
                  ORDER BY si.created_at DESC 
                  LIMIT ? OFFSET ?');
    $db->bind('i', $perPage);
    $db->bind('i', $offset);
    $db->execute();
    $invoices = $db->fetchAll();
    ?>
    <div class="content-area">
        <div class="row mb-4">
            <div class="col-8">
                <h1 class="h3 mb-1"><i class="fas fa-file-invoice"></i> Sales Invoices</h1>
            </div>
            <div class="col-4 text-end">
                <a href="<?php echo BASE_URL; ?>/pages/sales/sales_invoice.php?action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Invoice
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
                                <th>Invoice Number</th>
                                <th>Customer</th>
                                <th>Invoice Date</th>
                                <th>Amount</th>
                                <th>Payment Status</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($inv['invoice_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($inv['customer_name'] ?? 'Walk-in'); ?></td>
                                <td><?php echo formatDate($inv['invoice_date']); ?></td>
                                <td><?php echo formatCurrency($inv['total_amount']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusBadgeColor($inv['payment_status']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $inv['payment_status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusBadgeColor($inv['status']); ?>">
                                        <?php echo ucfirst($inv['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/pages/sales/sales_invoice.php?action=view&id=<?php echo $inv['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php echo getPaginationLinks($page, $totalPages, BASE_URL . '/pages/sales/sales_invoice.php'); ?>
            </div>
        </div>
    </div>
    <?php
}

require_once '../../includes/footer.php';
?>