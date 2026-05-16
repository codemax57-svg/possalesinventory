<?php
/**
 * POS / Fast Checkout Page
 * Construction POS & Inventory System
 */

require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
require_once '../../includes/sidebar.php';

// Check if user has cashier role
if (!userHasRole(['cashier', 'admin', 'manager'])) {
    setMessage('Access denied', 'error');
    redirect(BASE_URL . '/index.php');
}

$db = new Database();
$db->connect();
?>

<div class="content-area">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-1"><i class="fas fa-cash-register"></i> Fast Checkout / POS</h1>
            <p class="text-muted">Process customer purchases quickly and efficiently</p>
        </div>
    </div>
    
    <?php displayMessageAlert(); ?>
    
    <div class="row">
        <!-- Products Search and List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-search"></i> Search Products</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <input type="text" id="productSearch" class="form-control" placeholder="Search by product name or code...">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 mb-2">
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
                    </div>
                    
                    <div id="productsContainer" class="row">
                        <!-- Products will be loaded here via AJAX -->
                        <?php
                        $db->prepare('SELECT p.*, c.category_name, u.unit_abbreviation, sl.quantity_on_hand 
                                     FROM products p 
                                     LEFT JOIN categories c ON p.category_id = c.id 
                                     LEFT JOIN units u ON p.unit_id = u.id 
                                     LEFT JOIN stock_levels sl ON p.id = sl.product_id AND sl.warehouse_id = 1 
                                     WHERE p.status = "active" 
                                     ORDER BY p.product_name 
                                     LIMIT 12');
                        $db->execute();
                        $products = $db->fetchAll();
                        
                        foreach ($products as $product):
                            $stock = $product['quantity_on_hand'] ?? 0;
                            $stockClass = $stock <= $product['reorder_level'] ? 'text-danger' : 'text-success';
                        ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <div class="card h-100 product-card" style="cursor: pointer;" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['product_name']); ?>', <?php echo $product['selling_price']; ?>)">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h6>
                                    <p class="card-text text-muted small"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                    <p class="mb-2">
                                        <strong><?php echo formatCurrency($product['selling_price']); ?></strong>
                                    </p>
                                    <p class="mb-0 small <?php echo $stockClass; ?>">
                                        Stock: <strong><?php echo $stock; ?> <?php echo htmlspecialchars($product['unit_abbreviation']); ?></strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cart Summary -->
        <div class="col-lg-4">
            <div class="card" style="position: sticky; top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Cart</h5>
                </div>
                <div class="card-body">
                    <div id="cartItems" style="max-height: 400px; overflow-y: auto; margin-bottom: 1rem;">
                        <!-- Cart items will be added here -->
                        <p class="text-muted text-center">No items in cart</p>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <div class="row mb-2">
                            <div class="col-8">Subtotal:</div>
                            <div class="col-4 text-end">
                                <strong id="subtotal"><?php echo formatCurrency(0); ?></strong>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-8">Discount (%):</div>
                            <div class="col-4 text-end">
                                <input type="number" id="discountPercent" class="form-control form-control-sm" value="0" min="0" max="<?php echo MAX_DISCOUNT_PERCENTAGE; ?>" onchange="updateTotals()">
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-8">Discount Amount:</div>
                            <div class="col-4 text-end">
                                <strong id="discountAmount"><?php echo formatCurrency(0); ?></strong>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-8">VAT (12%):</div>
                            <div class="col-4 text-end">
                                <strong id="vatAmount"><?php echo formatCurrency(0); ?></strong>
                            </div>
                        </div>
                        
                        <div class="row" style="font-size: 1.2rem; font-weight: bold;">
                            <div class="col-8">Total:</div>
                            <div class="col-4 text-end text-primary">
                                <span id="totalAmount"><?php echo formatCurrency(0); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label"><strong>Payment Method</strong></label>
                        <select id="paymentMethod" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="check">Check</option>
                            <option value="credit_card">Credit Card</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amountTendered" class="form-label"><strong>Amount Tendered</strong></label>
                        <input type="number" id="amountTendered" class="form-control" step="0.01" value="0" onchange="calculateChange()">
                    </div>
                    
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">Change:</div>
                            <div class="col-6 text-end">
                                <strong id="change" style="font-size: 1.1rem;"><?php echo formatCurrency(0); ?></strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mb-2">
                        <button type="button" class="btn btn-success btn-lg" onclick="completePOSTransaction()">
                            <i class="fas fa-check-circle"></i> Complete Sale
                        </button>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-secondary btn-lg" onclick="clearCart()">
                            <i class="fas fa-trash"></i> Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add to Cart Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user"></i> Select Customer (Optional)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="customerSelect" class="form-label">Customer</label>
                    <select id="customerSelect" class="form-select">
                        <option value="">Walk-in Customer</option>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="setSelectedCustomer()">Select</button>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let selectedCustomerId = null;
let cartTotal = 0;

function addToCart(productId, productName, price) {
    const existingItem = cart.find(item => item.product_id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            product_id: productId,
            product_name: productName,
            price: price,
            quantity: 1
        });
    }
    
    updateCart();
}

function updateCart() {
    const cartContainer = document.getElementById('cartItems');
    
    if (cart.length === 0) {
        cartContainer.innerHTML = '<p class="text-muted text-center">No items in cart</p>';
        updateTotals();
        return;
    }
    
    let cartHTML = '';
    cart.forEach((item, index) => {
        const lineTotal = item.price * item.quantity;
        cartHTML += `
            <div class="card card-body mb-2 p-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1" style="font-size: 0.9rem;">${item.product_name}</h6>
                        <small class="text-muted">${formatCurrencyJS(item.price)} x ${item.quantity}</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mt-2">
                    <input type="number" class="form-control form-control-sm" value="${item.quantity}" min="1" onchange="updateQuantity(${index}, this.value)">
                </div>
                <div class="text-end mt-2">
                    <strong>${formatCurrencyJS(lineTotal)}</strong>
                </div>
            </div>
        `;
    });
    
    cartContainer.innerHTML = cartHTML;
    updateTotals();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

function updateQuantity(index, quantity) {
    const qty = parseInt(quantity);
    if (qty > 0) {
        cart[index].quantity = qty;
    } else {
        removeFromCart(index);
    }
    updateCart();
}

function updateTotals() {
    let subtotal = 0;
    cart.forEach(item => {
        subtotal += item.price * item.quantity;
    });
    
    const discountPercent = parseFloat(document.getElementById('discountPercent').value) || 0;
    const discountAmount = subtotal * (discountPercent / 100);
    const afterDiscount = subtotal - discountAmount;
    const vatAmount = afterDiscount * 0.12;
    const total = afterDiscount + vatAmount;
    
    cartTotal = total;
    
    document.getElementById('subtotal').textContent = formatCurrencyJS(subtotal);
    document.getElementById('discountAmount').textContent = formatCurrencyJS(discountAmount);
    document.getElementById('vatAmount').textContent = formatCurrencyJS(vatAmount);
    document.getElementById('totalAmount').textContent = formatCurrencyJS(total);
    
    calculateChange();
}

function calculateChange() {
    const amountTendered = parseFloat(document.getElementById('amountTendered').value) || 0;
    const change = amountTendered - cartTotal;
    document.getElementById('change').textContent = formatCurrencyJS(change);
}

function clearCart() {
    if (confirm('Are you sure you want to clear the cart?')) {
        cart = [];
        selectedCustomerId = null;
        document.getElementById('amountTendered').value = '0';
        document.getElementById('discountPercent').value = '0';
        updateCart();
    }
}

function completePOSTransaction() {
    if (cart.length === 0) {
        alert('Cart is empty');
        return;
    }
    
    const amountTendered = parseFloat(document.getElementById('amountTendered').value) || 0;
    if (amountTendered < cartTotal) {
        alert('Amount tendered is less than total amount');
        return;
    }
    
    // Send transaction data to server
    const transactionData = {
        cart: cart,
        customer_id: selectedCustomerId,
        payment_method: document.getElementById('paymentMethod').value,
        amount_tendered: amountTendered,
        discount_percent: parseFloat(document.getElementById('discountPercent').value) || 0,
        total_amount: cartTotal
    };
    
    fetch('<?php echo BASE_URL; ?>/api/sales/pos-transaction.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(transactionData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Transaction completed successfully!');
            clearCart();
            // Optionally print receipt or redirect
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error completing transaction');
    });
}

function formatCurrencyJS(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
}

function setSelectedCustomer() {
    selectedCustomerId = document.getElementById('customerSelect').value || null;
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
});
</script>

<?php require_once '../../includes/footer.php'; ?>