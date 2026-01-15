<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

try {
    include '../../includes/conn_mysql.php';

    $q = trim((string)($_GET['q'] ?? ''));
    $limit = (int)($_GET['limit'] ?? 15);
    if ($limit < 1) {
        $limit = 15;
    }
    if ($limit > 50) {
        $limit = 50;
    }

    // Empty query returns empty list (avoids dumping large tables).
    if ($q === '') {
        echo json_encode([]);
        exit();
    }

    $qLike = '%' . $q . '%';
    $qPrefix = $q . '%';

    // NOTE: LIMIT is safely interpolated after strict integer clamping above.
    $sql = "
        SELECT product_id, product_name, unit
        FROM products
        WHERE is_active = 1
          AND (
            product_id LIKE ?
            OR product_name LIKE ?
          )
        ORDER BY
          (product_id LIKE ?) DESC,
          (product_name LIKE ?) DESC,
          product_name ASC
        LIMIT $limit
    ";

    // Use only positional parameters to avoid binding issues
    $params = [
        $qLike,
        $qLike,
        $qPrefix,
        $qPrefix
    ];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) {
        echo json_encode([]);
        exit();
    }

    // Normalize output shape defensively.
    $out = [];
    foreach ($rows as $r) {
        $out[] = [
            'product_id' => (string)($r['product_id'] ?? ''),
            'product_name' => (string)($r['product_name'] ?? ''),
            'unit' => (string)($r['unit'] ?? ''),
        ];
    }

    echo json_encode($out);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}


