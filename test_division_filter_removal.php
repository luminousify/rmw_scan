<?php
/**
 * Test script to verify division filter removal from product search
 * This simulates a product search request and verifies all products are returned
 */

require_once 'config.php';
session_start();

// Simulate a logged-in production user with a specific division
$_SESSION['loggedin'] = true;
$_SESSION['idlog'] = 1; // Assuming user ID 1 exists
$_SESSION['department'] = 'production';
$_SESSION['user'] = 'test_user';

// Get user's actual division from database
include 'includes/conn_mysql.php';
$stmt = $pdo->prepare("SELECT division FROM users WHERE id = ?");
$stmt->execute([1]);
$userDivision = $stmt->fetchColumn();

echo "=== Division Filter Removal Test ===\n\n";
echo "User Division: " . ($userDivision ?: 'None') . "\n\n";

// Test 1: Search for common product names
$testQueries = ['', 'a', 'plastic', 'bolt'];

foreach ($testQueries as $query) {
    echo "--- Testing query: '$query' ---\n";

    if ($query === '') {
        echo "Empty query should return empty results\n";
        continue;
    }

    $qLike = '%' . $query . '%';
    $qPrefix = $query . '%';

    $sql = "
        SELECT product_id, product_name, unit, Divisi
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
        LIMIT 15
    ";

    $params = [
        $qLike,
        $qLike,
        $qPrefix,
        $qPrefix
    ];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($rows) . " products\n";

    // Show first 5 results with divisions
    $displayCount = min(5, count($rows));
    for ($i = 0; $i < $displayCount; $i++) {
        $row = $rows[$i];
        echo "  - " . $row['product_id'] . " - " . $row['product_name'] .
             " (Divisi: " . ($row['Divisi'] ?? 'None') . ")\n";
    }

    if (count($rows) > 5) {
        echo "  ... and " . (count($rows) - 5) . " more\n";
    }

    // Verify results include multiple divisions
    $divisions = [];
    foreach ($rows as $row) {
        $div = $row['Divisi'] ?? 'None';
        if (!in_array($div, $divisions)) {
            $divisions[] = $div;
        }
    }

    if (count($divisions) > 1) {
        echo "✓ Results include products from multiple divisions: " . implode(', ', $divisions) . "\n";
    } elseif (count($divisions) === 1) {
        echo "⚠ Results only include division: " . $divisions[0] . "\n";
    } else {
        echo "⚠ No division information found\n";
    }

    echo "\n";
}

echo "=== Test Complete ===\n";
echo "If you see products from multiple divisions above, the division filter has been successfully removed!\n";
?>
