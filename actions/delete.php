<?php
/**
 * Document Retrieval API – Delete (CRUD Assignment)
 * POST – form or JSON: id (documentID). JSON only.
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed. Use POST.']);
    exit;
}

$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    $decoded = is_string($raw) ? json_decode($raw, true) : null;
    if (is_array($decoded)) {
        $input = $decoded;
    }
}

$id = trim((string) ($input['id'] ?? $input['documentID'] ?? ''));

if ($id === '') {
    echo json_encode(['success' => false, 'error' => 'id is required']);
    exit;
}

require_once __DIR__ . '/../config/config.php';

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'error' => 'Database not available.']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM Document WHERE documentID = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed.']);
    exit;
}
$stmt->bind_param('s', $id);
$ok = $stmt->execute();
$affected = $conn->affected_rows;
$stmt->close();

if (!$ok) {
    echo json_encode(['success' => false, 'error' => 'Delete failed.']);
    exit;
}

echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
