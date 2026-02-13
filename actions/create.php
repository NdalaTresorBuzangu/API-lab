<?php
/**
 * Document Retrieval API – Create (CRUD Assignment)
 * POST – form or JSON: description, location, userID, schoolID, documentTypeID. JSON only.
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error'   => 'This endpoint accepts POST only. Send description, location, userID, schoolID, documentTypeID.',
        'usage'   => [
            'method' => 'POST',
            'url'    => 'https://wastejustice.online/actions/create.php',
            'example_body' => [
                'description'    => 'Need my transcript',
                'location'       => 'Kinshasa',
                'userID'         => 1,
                'schoolID'       => 2,
                'documentTypeID' => 1
            ],
            'curl' => 'curl -X POST "https://wastejustice.online/actions/create.php" -d "description=Need my transcript" -d "location=Kinshasa" -d "userID=1" -d "schoolID=2" -d "documentTypeID=1"'
        ]
    ]);
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

$description = trim((string) ($input['description'] ?? ''));
$location    = trim((string) ($input['location'] ?? ''));
$userID      = (int) ($input['userID'] ?? $input['userId'] ?? 0);
$schoolID    = (int) ($input['schoolID'] ?? $input['issuerId'] ?? 0);
$documentTypeID = (int) ($input['documentTypeID'] ?? $input['typeId'] ?? 0);

if ($description === '') {
    echo json_encode(['success' => false, 'error' => 'description is required']);
    exit;
}

require_once __DIR__ . '/../config/config.php';

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'error' => 'Database not available.']);
    exit;
}

$documentID = 'doc_' . uniqid();
$statusID = 1; // Pending

$stmt = $conn->prepare(
    "INSERT INTO Document (documentID, userID, schoolID, documentTypeID, statusID, description, location, imagePath, submissionDate) 
     VALUES (?, ?, ?, ?, ?, ?, ?, '', NOW())"
);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed.']);
    exit;
}

// 7 placeholders: documentID, userID, schoolID, documentTypeID, statusID, description, location (imagePath is literal '' in SQL)
$stmt->bind_param('siiiiss', $documentID, $userID, $schoolID, $documentTypeID, $statusID, $description, $location);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Insert failed.']);
    exit;
}
$stmt->close();

echo json_encode(['success' => true, 'data' => ['id' => $documentID]], JSON_UNESCAPED_UNICODE);
