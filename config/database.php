<?php
/**
 * Database Configuration and Connection
 * Construction POS & Inventory System
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'construction_pos_inventory');
define('DB_PORT', 3306);

// Connection Class
class Database {
    private $host = DB_HOST;
    private $db_user = DB_USER;
    private $db_pass = DB_PASS;
    private $db_name = DB_NAME;
    private $db_port = DB_PORT;
    private $conn;
    private $stmt;

    // Connect to DB
    public function connect() {
        $this->conn = new mysqli(
            $this->host,
            $this->db_user,
            $this->db_pass,
            $this->db_name,
            $this->db_port
        );

        // Check connection
        if ($this->conn->connect_error) {
            die('Connection Failed: ' . $this->conn->connect_error);
        }

        // Set charset to utf8
        $this->conn->set_charset('utf8');

        return $this->conn;
    }

    // Prepare statement
    public function prepare($query) {
        $this->stmt = $this->conn->prepare($query);
        if (!$this->stmt) {
            die('Prepare Failed: ' . $this->conn->error);
        }
        return $this;
    }

    // Bind parameters
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = 'i';
                    break;
                case is_float($value):
                    $type = 'd';
                    break;
                case is_string($value):
                    $type = 's';
                    break;
                default:
                    $type = 's';
            }
        }
        $this->stmt->bind_param($type, $value);
        return $this;
    }

    // Execute statement
    public function execute() {
        if (!$this->stmt->execute()) {
            die('Execute Failed: ' . $this->stmt->error);
        }
        return $this;
    }

    // Get result
    public function getResult() {
        return $this->stmt->get_result();
    }

    // Fetch single row
    public function fetch() {
        $result = $this->getResult();
        return $result->fetch_assoc();
    }

    // Fetch all rows
    public function fetchAll() {
        $result = $this->getResult();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    // Get row count
    public function rowCount() {
        return $this->stmt->num_rows;
    }

    // Get last insert ID
    public function lastInsertId() {
        return $this->conn->insert_id;
    }

    // Begin transaction
    public function beginTransaction() {
        $this->conn->begin_transaction();
    }

    // Commit transaction
    public function commit() {
        $this->conn->commit();
    }

    // Rollback transaction
    public function rollback() {
        $this->conn->rollback();
    }

    // Close connection
    public function closeConnection() {
        if ($this->stmt) {
            $this->stmt->close();
        }
        $this->conn->close();
    }

    // Raw query execution (for SELECT without params)
    public function query($query) {
        $result = $this->conn->query($query);
        if (!$result) {
            die('Query Failed: ' . $this->conn->error);
        }
        return $result;
    }
}

// Initialize Database Connection
$db = new Database();
$db->connect();
?>