<?php
require_once __DIR__ . '/config/database.php';

$events = [];
$facilityFilter = $_GET['facility_id'] ?? null;
$all = isset($_GET['all']);

$sql = "SELECT e.*, f.name AS facility_name 
        FROM event_requests e 
        JOIN facilities f ON f.id = e.facility_id 
        WHERE e.status = 'approved'";

$params = [];

if (!$all && $facilityFilter) {
    $sql .= " AND f.id = ?";
    $params[] = $facilityFilter;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($data as $row) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['title'] . " - " . $row['facility_name'],
        'start' => $row['start_datetime'],
        'end' => $row['end_datetime'],
        'description' => $row['description']
    ];
}

header('Content-Type: application/json');
echo json_encode($events);
