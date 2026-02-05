<?php
require 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Fixer</title>
    <style>body{background:#111;color:#0f0;font-family:monospace;padding:2rem;}</style>
</head>
<body>
<pre>
<?php
echo "Applying Database Fixes...\n";

try {
    // Fix ORDERS table
    echo "1. Checking 'orders' table for 'payment_gateway_id'...\n";
    $stm = $pdo->query("SHOW COLUMNS FROM orders LIKE 'payment_gateway_id'");
    if ($stm->fetch()) {
        echo "   - Column already exists.\n";
    } else {
        echo "   + Adding 'payment_gateway_id' to orders...\n";
        $pdo->exec("ALTER TABLE orders ADD COLUMN payment_gateway_id VARCHAR(255) DEFAULT NULL AFTER status");
        echo "   ✅ Success.\n";
    }

    // Fix ORDER_ITEMS table
    echo "2. Checking 'order_items' table for 'title'...\n";
    $stm = $pdo->query("SHOW COLUMNS FROM order_items LIKE 'title'");
    if ($stm->fetch()) {
        echo "   - Column 'title' already exists.\n";
    } else {
        echo "   + Adding 'title' to order_items...\n";
        $pdo->exec("ALTER TABLE order_items ADD COLUMN title VARCHAR(255) NOT NULL AFTER product_id");
        echo "   ✅ Success.\n";
    }

    echo "3. Checking 'order_items' table for 'type'...\n";
    $stm = $pdo->query("SHOW COLUMNS FROM order_items LIKE 'type'");
    if ($stm->fetch()) {
        echo "   - Column 'type' already exists.\n";
    } else {
        echo "   + Adding 'type' to order_items...\n";
        $pdo->exec("ALTER TABLE order_items ADD COLUMN type VARCHAR(50) DEFAULT 'OWN_KEY' AFTER unit_price_cents");
        echo "   ✅ Success.\n";
    }

    echo "\n🎉 Database Patch Completed Successfully.\n";
    echo "You can now return to checkout.";

} catch (PDOException $e) {
    echo "\n❌ FATAL ERROR: " . $e->getMessage() . "\n";
}
?>
</pre>
</body>
</html>
