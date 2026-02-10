<?php
// ===================================
// DATABASE CONNECTION
// ===================================

require_once __DIR__ . '/config.php';

try {
    // PDO Connection
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    // Log error (dalam production jangan tampilkan detail error)
    error_log("Database Connection Error: " . $e->getMessage());
    
    // User-friendly error message
    die(json_encode([
        'success' => false,
        'message' => 'Koneksi database gagal. Silakan hubungi administrator.'
    ]));
}

/**
 * Execute prepared statement dengan parameter
 *
 * @param string $sql SQL query
 * @param array $params Parameters untuk binding
 * @return PDOStatement
 */
function executeQuery($sql, $params = []) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query Error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Get single row
 *
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array|false
 */
function getRow($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

/**
 * Get all rows
 *
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array
 */
function getAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

/**
 * Insert data dan return last insert ID
 *
 * @param string $table Table name
 * @param array $data Associative array of column => value
 * @return int Last insert ID
 */
function insert($table, $data) {
    global $pdo;
    
    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    
    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    executeQuery($sql, $data);
    
    return $pdo->lastInsertId();
}

/**
 * Update data
 *
 * @param string $table Table name
 * @param array $data Data to update
 * @param string $where WHERE clause
 * @param array $whereParams WHERE parameters
 * @return int Number of affected rows
 */
function update($table, $data, $where, $whereParams = []) {
    $setParts = [];
    foreach (array_keys($data) as $key) {
        $setParts[] = "{$key} = :{$key}";
    }
    $setClause = implode(', ', $setParts);
    
    $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
    $params = array_merge($data, $whereParams);
    
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount();
}
