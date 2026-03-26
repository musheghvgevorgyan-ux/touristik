<?php
header('Content-Type: application/json');

// TODO: Integrate new flight API here

$from = isset($_GET['from']) ? trim($_GET['from']) : '';
$to = isset($_GET['to']) ? trim($_GET['to']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : 'roundtrip';

if (!$from || !$to) {
    echo json_encode(['found' => false]);
    exit;
}

// Fallback: local DB prices
require_once __DIR__ . '/../includes/db.php';
$stmt = $pdo->prepare("SELECT price FROM flight_prices WHERE from_city = ? AND to_city = ? AND trip_type = ?");
$stmt->execute([$from, $to, $type]);
$row = $stmt->fetch();
if (!$row) {
    $stmt->execute([$to, $from, $type]);
    $row = $stmt->fetch();
}

if ($row) {
    echo json_encode(['found' => true, 'price' => (float)$row['price'], 'source' => 'local']);
} else {
    echo json_encode(['found' => false]);
}
