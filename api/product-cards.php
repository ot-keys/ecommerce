<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/product_table.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $cards = product_table_cards($mysqli);
    echo json_encode($cards, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
} catch (Throwable $exception) {
    error_log('Product cards could not be loaded: ' . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Product cards could not be loaded.']);
}
