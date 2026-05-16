-- Construction POS & Inventory System Database Schema
-- Database: construction_pos_inventory

-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'cashier', 'inventory_staff', 'manager') DEFAULT 'cashier',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
);

-- Create Customers Table
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    barangay VARCHAR(100),
    city VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(10),
    customer_type ENUM('retail', 'wholesale', 'contractor') DEFAULT 'retail',
    credit_limit DECIMAL(12, 2) DEFAULT 0,
    current_balance DECIMAL(12, 2) DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_customer_name (customer_name),
    INDEX idx_status (status)
);

-- Create Product Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category_name (category_name)
);

-- Create Units Table
CREATE TABLE IF NOT EXISTS units (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unit_name VARCHAR(50) NOT NULL UNIQUE,
    unit_abbreviation VARCHAR(10) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_unit_name (unit_name)
);

-- Create Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_code VARCHAR(50) NOT NULL UNIQUE,
    product_name VARCHAR(150) NOT NULL,
    category_id INT NOT NULL,
    unit_id INT NOT NULL,
    description TEXT,
    purchase_price DECIMAL(10, 2) NOT NULL,
    selling_price DECIMAL(10, 2) NOT NULL,
    reorder_level INT DEFAULT 10,
    current_stock INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (unit_id) REFERENCES units(id),
    INDEX idx_product_code (product_code),
    INDEX idx_product_name (product_name),
    INDEX idx_category_id (category_id)
);

-- Create Batch/Lot Tracking Table
CREATE TABLE IF NOT EXISTS product_batches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    batch_number VARCHAR(100) NOT NULL,
    expiration_date DATE,
    manufacture_date DATE,
    quantity INT NOT NULL,
    purchase_price DECIMAL(10, 2),
    status ENUM('active', 'expired', 'damaged') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_product_id (product_id),
    INDEX idx_batch_number (batch_number),
    INDEX idx_expiration_date (expiration_date)
);

-- Create Stock Levels Table
CREATE TABLE IF NOT EXISTS stock_levels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    warehouse_id INT,
    quantity_on_hand INT DEFAULT 0,
    quantity_reserved INT DEFAULT 0,
    quantity_available INT DEFAULT 0,
    last_counted_at TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY unique_product_warehouse (product_id, warehouse_id),
    INDEX idx_product_id (product_id)
);

-- Create Warehouses/Branches Table
CREATE TABLE IF NOT EXISTS warehouses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    warehouse_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    manager_name VARCHAR(100),
    contact_number VARCHAR(20),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_warehouse_name (warehouse_name)
);

-- Create Suppliers Table
CREATE TABLE IF NOT EXISTS suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    contact_number VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    payment_terms VARCHAR(100),
    bank_account VARCHAR(50),
    tin_number VARCHAR(20),
    total_payable DECIMAL(12, 2) DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_supplier_name (supplier_name)
);

-- Create Purchase Orders Table
CREATE TABLE IF NOT EXISTS purchase_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    po_number VARCHAR(50) NOT NULL UNIQUE,
    supplier_id INT NOT NULL,
    warehouse_id INT,
    order_date DATE NOT NULL,
    expected_delivery_date DATE,
    actual_delivery_date DATE,
    total_amount DECIMAL(12, 2) DEFAULT 0,
    status ENUM('draft', 'sent', 'received', 'cancelled', 'completed') DEFAULT 'draft',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_po_number (po_number),
    INDEX idx_supplier_id (supplier_id),
    INDEX idx_status (status)
);

-- Create PO Items Table
CREATE TABLE IF NOT EXISTS po_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    po_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_ordered INT NOT NULL,
    quantity_received INT DEFAULT 0,
    unit_price DECIMAL(10, 2) NOT NULL,
    line_total DECIMAL(12, 2) NOT NULL,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_po_id (po_id)
);

-- Create Quotations Table
CREATE TABLE IF NOT EXISTS quotations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quotation_number VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    quotation_date DATE NOT NULL,
    valid_until DATE,
    subtotal DECIMAL(12, 2) DEFAULT 0,
    discount_amount DECIMAL(12, 2) DEFAULT 0,
    discount_percentage DECIMAL(5, 2) DEFAULT 0,
    vat_amount DECIMAL(12, 2) DEFAULT 0,
    total_amount DECIMAL(12, 2) DEFAULT 0,
    notes TEXT,
    status ENUM('draft', 'sent', 'converted', 'expired', 'rejected') DEFAULT 'draft',
    converted_to_invoice_id INT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_quotation_number (quotation_number),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status)
);

-- Create Quotation Items Table
CREATE TABLE IF NOT EXISTS quotation_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quotation_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    line_total DECIMAL(12, 2) NOT NULL,
    FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_quotation_id (quotation_id)
);

-- Create Sales Invoices Table
CREATE TABLE IF NOT EXISTS sales_invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE,
    subtotal DECIMAL(12, 2) DEFAULT 0,
    discount_amount DECIMAL(12, 2) DEFAULT 0,
    discount_percentage DECIMAL(5, 2) DEFAULT 0,
    vat_amount DECIMAL(12, 2) DEFAULT 0,
    total_amount DECIMAL(12, 2) DEFAULT 0,
    amount_paid DECIMAL(12, 2) DEFAULT 0,
    payment_status ENUM('unpaid', 'partial', 'paid') DEFAULT 'unpaid',
    payment_method VARCHAR(50),
    notes TEXT,
    reference_quotation_id INT,
    status ENUM('draft', 'confirmed', 'cancelled') DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_customer_id (customer_id),
    INDEX idx_payment_status (payment_status),
    INDEX idx_invoice_date (invoice_date)
);

-- Create Invoice Items Table
CREATE TABLE IF NOT EXISTS invoice_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    discount_amount DECIMAL(12, 2) DEFAULT 0,
    line_total DECIMAL(12, 2) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES sales_invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_invoice_id (invoice_id)
);

-- Create Delivery Receipts Table
CREATE TABLE IF NOT EXISTS delivery_receipts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    receipt_number VARCHAR(50) NOT NULL UNIQUE,
    invoice_id INT,
    customer_id INT NOT NULL,
    delivery_date DATE NOT NULL,
    delivery_address TEXT,
    recipient_name VARCHAR(100),
    contact_number VARCHAR(20),
    delivered_by INT,
    delivery_notes TEXT,
    status ENUM('pending', 'in_transit', 'delivered', 'returned') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES sales_invoices(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (delivered_by) REFERENCES users(id),
    INDEX idx_receipt_number (receipt_number),
    INDEX idx_status (status)
);

-- Create Delivery Receipt Items Table
CREATE TABLE IF NOT EXISTS delivery_receipt_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    delivery_receipt_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_ordered INT NOT NULL,
    quantity_delivered INT NOT NULL,
    unit_price DECIMAL(10, 2),
    remarks TEXT,
    FOREIGN KEY (delivery_receipt_id) REFERENCES delivery_receipts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_delivery_receipt_id (delivery_receipt_id)
);

-- Create POS Transactions Table
CREATE TABLE IF NOT EXISTS pos_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_number VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT,
    transaction_date DATE NOT NULL,
    transaction_time TIME NOT NULL,
    subtotal DECIMAL(12, 2) DEFAULT 0,
    discount_amount DECIMAL(12, 2) DEFAULT 0,
    discount_percentage DECIMAL(5, 2) DEFAULT 0,
    vat_amount DECIMAL(12, 2) DEFAULT 0,
    total_amount DECIMAL(12, 2) DEFAULT 0,
    amount_tendered DECIMAL(12, 2),
    change_amount DECIMAL(12, 2) DEFAULT 0,
    payment_method VARCHAR(50),
    payment_reference VARCHAR(100),
    cashier_id INT,
    status ENUM('completed', 'cancelled', 'refunded') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (cashier_id) REFERENCES users(id),
    INDEX idx_transaction_number (transaction_number),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_cashier_id (cashier_id)
);

-- Create POS Transaction Items Table
CREATE TABLE IF NOT EXISTS pos_transaction_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pos_transaction_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    discount_amount DECIMAL(12, 2) DEFAULT 0,
    line_total DECIMAL(12, 2) NOT NULL,
    FOREIGN KEY (pos_transaction_id) REFERENCES pos_transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_pos_transaction_id (pos_transaction_id)
);

-- Create Customer Accounts (Utang/Credit) Table
CREATE TABLE IF NOT EXISTS customer_accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    invoice_id INT,
    po_number VARCHAR(50),
    transaction_type ENUM('credit', 'payment', 'adjustment') DEFAULT 'credit',
    amount DECIMAL(12, 2) NOT NULL,
    balance DECIMAL(12, 2) NOT NULL,
    due_date DATE,
    paid_date DATE,
    status ENUM('outstanding', 'partial', 'paid', 'overdue') DEFAULT 'outstanding',
    notes TEXT,
    recorded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (invoice_id) REFERENCES sales_invoices(id),
    FOREIGN KEY (recorded_by) REFERENCES users(id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date)
);

-- Create Returns & Refunds Table
CREATE TABLE IF NOT EXISTS returns_refunds (
    id INT PRIMARY KEY AUTO_INCREMENT,
    return_number VARCHAR(50) NOT NULL UNIQUE,
    invoice_id INT,
    pos_transaction_id INT,
    customer_id INT NOT NULL,
    return_date DATE NOT NULL,
    return_reason VARCHAR(255) NOT NULL,
    return_notes TEXT,
    refund_amount DECIMAL(12, 2) DEFAULT 0,
    refund_status ENUM('pending', 'approved', 'rejected', 'processed') DEFAULT 'pending',
    approved_by INT,
    approval_date DATE,
    processed_by INT,
    processed_date DATE,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES sales_invoices(id),
    FOREIGN KEY (pos_transaction_id) REFERENCES pos_transactions(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (processed_by) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_return_number (return_number),
    INDEX idx_refund_status (refund_status)
);

-- Create Return Items Table
CREATE TABLE IF NOT EXISTS return_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    return_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_returned INT NOT NULL,
    unit_price DECIMAL(10, 2),
    reason_category ENUM('damaged', 'defective', 'wrong_item', 'duplicate', 'customer_request') DEFAULT 'damaged',
    condition VARCHAR(100),
    restocking_notes TEXT,
    FOREIGN KEY (return_id) REFERENCES returns_refunds(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_return_id (return_id)
);

-- Create Stock Adjustments Table
CREATE TABLE IF NOT EXISTS stock_adjustments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    adjustment_number VARCHAR(50) NOT NULL UNIQUE,
    product_id INT NOT NULL,
    warehouse_id INT,
    adjustment_type ENUM('damaged', 'lost', 'found', 'manual_correction', 'return') DEFAULT 'manual_correction',
    quantity_before INT NOT NULL,
    quantity_adjusted INT NOT NULL,
    quantity_after INT NOT NULL,
    reason TEXT NOT NULL,
    notes TEXT,
    adjusted_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (adjusted_by) REFERENCES users(id),
    INDEX idx_adjustment_number (adjustment_number),
    INDEX idx_product_id (product_id),
    INDEX idx_adjustment_type (adjustment_type)
);

-- Create Stock Transfers Table
CREATE TABLE IF NOT EXISTS stock_transfers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transfer_number VARCHAR(50) NOT NULL UNIQUE,
    from_warehouse_id INT NOT NULL,
    to_warehouse_id INT NOT NULL,
    transfer_date DATE NOT NULL,
    expected_arrival_date DATE,
    actual_arrival_date DATE,
    status ENUM('pending', 'in_transit', 'received', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    initiated_by INT,
    received_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (from_warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (to_warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (initiated_by) REFERENCES users(id),
    FOREIGN KEY (received_by) REFERENCES users(id),
    INDEX idx_transfer_number (transfer_number),
    INDEX idx_status (status)
);

-- Create Stock Transfer Items Table
CREATE TABLE IF NOT EXISTS stock_transfer_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transfer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_sent INT NOT NULL,
    quantity_received INT DEFAULT 0,
    unit_price DECIMAL(10, 2),
    FOREIGN KEY (transfer_id) REFERENCES stock_transfers(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_transfer_id (transfer_id)
);

-- Create Inventory Audit Log Table
CREATE TABLE IF NOT EXISTS inventory_audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    warehouse_id INT,
    action VARCHAR(100) NOT NULL,
    quantity_before INT,
    quantity_after INT,
    change_reason TEXT,
    user_id INT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_product_id (product_id),
    INDEX idx_created_at (created_at)
);

-- Create System Logs Table
CREATE TABLE IF NOT EXISTS system_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    module VARCHAR(100),
    record_id INT,
    old_value TEXT,
    new_value TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_action (action)
);

-- Insert Default Categories
INSERT INTO categories (category_name, description, status) VALUES
('Cement', 'Portland cement and cement products', 'active'),
('Steel Bars', 'Reinforcing steel bars and rebar', 'active'),
('Hollow Blocks', 'Hollow blocks and masonry units', 'active'),
('Paint', 'Paint, primers, and coatings', 'active'),
('Pipes', 'PVC, copper, and metal pipes', 'active'),
('Electrical Supplies', 'Wires, cables, fixtures, and electrical components', 'active'),
('Lumber', 'Wood materials and lumber products', 'active');

-- Insert Default Units
INSERT INTO units (unit_name, unit_abbreviation, description) VALUES
('Piece', 'pcs', 'Individual pieces'),
('Sack', 'sck', 'Bag or sack measurement'),
('Meter', 'm', 'Linear meter'),
('Kilogram', 'kg', 'Weight in kilograms'),
('Liter', 'L', 'Volume in liters'),
('Cubic Meter', 'm³', 'Volume in cubic meters');

-- Insert Default Warehouse
INSERT INTO warehouses (warehouse_name, address, manager_name, contact_number, status) VALUES
('Main Warehouse', 'Main Building, Construction Supplies District', 'Manager Name', '09XXXXXXXXX', 'active');
