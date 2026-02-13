<?php
/**
 * Document Retrieval API – Update (CRUD Assignment)
 * POST – form or JSON: id (documentID), optional description, location, statusID. JSON only.
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

$description = isset($input['description']) ? trim((string) $input['description']) : null;
$location    = isset($input['location'])    ? trim((string) $input['location'])    : null;
$statusID    = isset($input['statusID'])    ? (int) $input['statusID']              : null;

require_once __DIR__ . '/../config/config.php';

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'error' => 'Database not available.']);
    exit;
}

$updates = [];
$types = '';
$params = [];

if ($description !== null) {
    $updates[] = 'description = ?';
    $types .= 's';
    $params[] = $description;
}
if ($location !== null) {
    $updates[] = 'location = ?';
    $types .= 's';
    $params[] = $location;
}
if ($statusID !== null && $statusID >= 1 && $statusID <= 4) {
    $updates[] = 'statusID = ?';
    $types .= 'i';
    $params[] = $statusID;
}

if (empty($updates)) {
    echo json_encode(['success' => true]);
    exit;
}

$types .= 's';
$params[] = $id;
$sql = "UPDATE Document SET " . implode(', ', $updates) . " WHERE documentID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed.']);
    exit;
}
$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Update failed.']);
    exit;
}
$stmt->close();

echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
