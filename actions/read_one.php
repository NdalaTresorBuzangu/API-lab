<?php
/**
 * Document Retrieval API – Read One (CRUD Assignment)
 * GET ?id=<documentID> – returns one document. JSON only.
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed. Use GET.']);
    exit;
}

$id = isset($_GET['id']) ? trim($_GET['id']) : '';

if ($id === '') {
    echo json_encode(['success' => false, 'error' => 'Missing id. Use ?id=<documentID>']);
    exit;
}

require_once __DIR__ . '/../config/config.php';

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'error' => 'Database not available.']);
    exit;
}

$stmt = $conn->prepare(
    "SELECT d.documentID AS id, d.userID, d.schoolID, d.documentTypeID, d.statusID,
     d.description, d.location, d.imagePath, d.submissionDate, d.completionDate,
     u.userName AS seekerName, s.userName AS issuerName,
     dt.typeName AS documentType, st.statusName
     FROM Document d
     LEFT JOIN User u ON d.userID = u.userID
     LEFT JOIN User s ON d.schoolID = s.userID
     LEFT JOIN DocumentType dt ON d.documentTypeID = dt.documentTypeID
     LEFT JOIN Status st ON d.statusID = st.statusID
     WHERE d.documentID = ?"
);
$stmt->bind_param('s', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['success' => false, 'error' => 'not found']);
    exit;
}

echo json_encode(['success' => true, 'data' => $row], JSON_UNESCAPED_UNICODE);
