<?php
/**
 * Document Retrieval API – Read All (CRUD Assignment)
 * GET – returns all documents with record IDs. JSON only.
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

require_once __DIR__ . '/../config/config.php';

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'error' => 'Database not available.']);
    exit;
}

$sql = "SELECT d.documentID AS id, d.userID, d.schoolID, d.documentTypeID, d.statusID, 
        d.description, d.location, d.imagePath, d.submissionDate, d.completionDate,
        u.userName AS seekerName, s.userName AS issuerName, 
        dt.typeName AS documentType, st.statusName
        FROM Document d
        LEFT JOIN User u ON d.userID = u.userID
        LEFT JOIN User s ON d.schoolID = s.userID
        LEFT JOIN DocumentType dt ON d.documentTypeID = dt.documentTypeID
        LEFT JOIN Status st ON d.statusID = st.statusID
        ORDER BY d.submissionDate DESC";

$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(['success' => false, 'error' => 'Query failed.']);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
