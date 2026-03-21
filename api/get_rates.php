<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/currency.php';

$rates = getCurrencyRates();
echo json_encode(['rates' => $rates, 'updated' => date('Y-m-d H:i:s')]);
