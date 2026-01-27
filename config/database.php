<?php

class Database {
    private $pdo;
    private $tablePrefix = '';

    public function __construct() {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_PORT,
            DB_DATABASE
        );
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $this->pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
        $this->initializeTables();
    }

    /**
     * Initialize database tables if they don't exist
     */
    private function initializeTables() {
        $tables = [
            'users' => "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255),
                name VARCHAR(255),
                phone VARCHAR(50),
                addresses JSON,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            'admins' => "CREATE TABLE IF NOT EXISTS admins (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                name VARCHAR(255),
                role VARCHAR(50) DEFAULT 'admin',
                status VARCHAR(50) DEFAULT 'active',
                last_login DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            'products' => "CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                stock INT DEFAULT 0,
                image VARCHAR(255),
                category VARCHAR(100),
                status VARCHAR(50) DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            'orders' => "CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                items JSON,
                subtotal DECIMAL(10,2),
                shipping_cost DECIMAL(10,2),
                total DECIMAL(10,2),
                status VARCHAR(50) DEFAULT 'pending',
                payment_status VARCHAR(50) DEFAULT 'pending',
                payment_method VARCHAR(50),
                payment_reference VARCHAR(255),
                shipping_address JSON,
                billing_address JSON,
                notes TEXT,
                tracking_number VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            'subscribers' => "CREATE TABLE IF NOT EXISTS subscribers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) UNIQUE NOT NULL,
                name VARCHAR(255),
                status VARCHAR(50) DEFAULT 'active',
                source VARCHAR(100),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            'distributor_applications' => "CREATE TABLE IF NOT EXISTS distributor_applications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50),
                company VARCHAR(255),
                country VARCHAR(100),
                state VARCHAR(100),
                city VARCHAR(100),
                message TEXT,
                status VARCHAR(50) DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            'settings' => "CREATE TABLE IF NOT EXISTS settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(255) UNIQUE NOT NULL,
                setting_value JSON,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            'remember_tokens' => "CREATE TABLE IF NOT EXISTS remember_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token VARCHAR(255) NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_token (token),
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ];

        foreach ($tables as $tableName => $sql) {
            $this->pdo->exec($sql);
        }
        
        // Seed default admin if no admins exist
        $this->seedDefaultAdmin();
    }
    
    /**
     * Seed default admin account if none exists
     */
    private function seedDefaultAdmin() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM admins");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            $admin = [
                'email' => defined('ADMIN_DEFAULT_EMAIL') ? ADMIN_DEFAULT_EMAIL : 'admin@example.com',
                'password' => password_hash(
                    defined('ADMIN_DEFAULT_PASSWORD') ? ADMIN_DEFAULT_PASSWORD : 'changeme123',
                    PASSWORD_DEFAULT
                ),
                'name' => defined('ADMIN_DEFAULT_NAME') ? ADMIN_DEFAULT_NAME : 'Admin',
                'role' => 'super_admin',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $columns = array_keys($admin);
            $placeholders = array_map(function($col) { return ':' . $col; }, $columns);
            
            $sql = sprintf(
                "INSERT INTO admins (%s) VALUES (%s)",
                implode(', ', array_map(function($c) { return "`{$c}`"; }, $columns)),
                implode(', ', $placeholders)
            );
            
            $stmt = $this->pdo->prepare($sql);
            foreach ($admin as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->execute();
        }
    }

    /**
     * Get table name with prefix
     */
    private function getTable($name) {
        return $this->tablePrefix . $name;
    }

    /**
     * Read all data from table
     */
    public function read($table) {
        $tableName = $this->getTable($table);
        $stmt = $this->pdo->prepare("SELECT * FROM `{$tableName}`");
        $stmt->execute();
        $results = $stmt->fetchAll();
        return array_map([$this, 'decodeJsonFields'], $results);
    }

    /**
     * Write data to table (replaces all records)
     */
    public function write($table, $data) {
        $tableName = $this->getTable($table);
        
        // Start transaction
        $this->pdo->beginTransaction();
        
        try {
            // Delete all existing records
            $this->pdo->exec("DELETE FROM `{$tableName}`");
            
            // Insert new records
            if (!empty($data)) {
                foreach ($data as $record) {
                    $this->insertRecord($tableName, $record);
                }
            }
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Insert record into table
     */
    public function insert($table, $record) {
        $tableName = $this->getTable($table);
        
        // Remove id if it's null or not set to let auto-increment work
        if (isset($record['id']) && empty($record['id'])) {
            unset($record['id']);
        }
        
        $id = $this->insertRecord($tableName, $record);
        $record['id'] = $id;
        
        return $record;
    }

    /**
     * Internal insert helper
     */
    private function insertRecord($tableName, $record) {
        $record = $this->encodeJsonFields($record);
        
        $columns = array_keys($record);
        $placeholders = array_map(function($col) { return ':' . $col; }, $columns);
        
        $sql = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            $tableName,
            implode(', ', array_map(function($c) { return "`{$c}`"; }, $columns)),
            implode(', ', $placeholders)
        );
        
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($record as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        
        return $this->pdo->lastInsertId() ?: ($record['id'] ?? null);
    }

    /**
     * Find records by criteria
     */
    public function find($table, $criteria = []) {
        $tableName = $this->getTable($table);
        
        if (empty($criteria)) {
            return $this->read($table);
        }
        
        $where = [];
        $params = [];
        
        foreach ($criteria as $key => $value) {
            $where[] = "`{$key}` = :{$key}";
            $params[$key] = $value;
        }
        
        $sql = sprintf(
            "SELECT * FROM `%s` WHERE %s",
            $tableName,
            implode(' AND ', $where)
        );
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        $results = $stmt->fetchAll();
        return array_map([$this, 'decodeJsonFields'], $results);
    }

    /**
     * Find one record by criteria
     */
    public function findOne($table, $criteria) {
        $tableName = $this->getTable($table);
        
        $where = [];
        $params = [];
        
        foreach ($criteria as $key => $value) {
            $where[] = "`{$key}` = :{$key}";
            $params[$key] = $value;
        }
        
        $sql = sprintf(
            "SELECT * FROM `%s` WHERE %s LIMIT 1",
            $tableName,
            implode(' AND ', $where)
        );
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch();
        return $result ? $this->decodeJsonFields($result) : null;
    }

    /**
     * Update records by criteria
     */
    public function update($table, $criteria, $updates) {
        $tableName = $this->getTable($table);
        $updates = $this->encodeJsonFields($updates);
        
        $set = [];
        $params = [];
        
        foreach ($updates as $key => $value) {
            $set[] = "`{$key}` = :set_{$key}";
            $params['set_' . $key] = $value;
        }
        
        $where = [];
        foreach ($criteria as $key => $value) {
            $where[] = "`{$key}` = :where_{$key}";
            $params['where_' . $key] = $value;
        }
        
        $sql = sprintf(
            "UPDATE `%s` SET %s WHERE %s",
            $tableName,
            implode(', ', $set),
            implode(' AND ', $where)
        );
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Delete records by criteria
     */
    public function delete($table, $criteria) {
        $tableName = $this->getTable($table);
        
        $where = [];
        $params = [];
        
        foreach ($criteria as $key => $value) {
            $where[] = "`{$key}` = :{$key}";
            $params[$key] = $value;
        }
        
        $sql = sprintf(
            "DELETE FROM `%s` WHERE %s",
            $tableName,
            implode(' AND ', $where)
        );
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }

    /**
     * Check if table has records
     */
    public function exists($table) {
        $tableName = $this->getTable($table);
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `{$tableName}`");
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Drop/truncate table
     */
    public function dropCollection($table) {
        $tableName = $this->getTable($table);
        $this->pdo->exec("TRUNCATE TABLE `{$tableName}`");
        return true;
    }

    /**
     * Encode array/object fields to JSON for storage
     */
    private function encodeJsonFields($record) {
        $jsonFields = ['addresses', 'items', 'shipping_address', 'billing_address', 'setting_value'];
        
        foreach ($record as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $record[$key] = json_encode($value);
            }
        }
        
        return $record;
    }

    /**
     * Decode JSON fields back to arrays
     */
    private function decodeJsonFields($record) {
        if (!$record) return $record;
        
        $jsonFields = ['addresses', 'items', 'shipping_address', 'billing_address', 'setting_value'];
        
        foreach ($record as $key => $value) {
            if (in_array($key, $jsonFields) && is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $record[$key] = $decoded;
                }
            }
        }
        
        return $record;
    }

    /**
     * Get PDO instance for advanced queries
     */
    public function getPdo() {
        return $this->pdo;
    }
}
