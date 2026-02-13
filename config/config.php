<?php
/**
 * Database config for Document Retrieval API.
 * Set DB_HOST, DB_USER, DB_PASS, DB_NAME on server if different from defaults.
 */
$servername = getenv('DB_HOST') ?: 'localhost';
$username   = getenv('DB_USER') ?: 'u628771162_d';
$password   = getenv('DB_PASS') ?: 'Ndala1950@@';
$dbname     = getenv('DB_NAME') ?: 'u628771162_dndala';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'error'   => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

$conn->set_charset('utf8mb4');
