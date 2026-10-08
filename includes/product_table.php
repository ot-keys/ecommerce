<?php
declare(strict_types=1);

const PRODUCT_CARD_GROUPS = ['categories', 'top-sellers', 'power-plans', 'for-you', 'featured'];

function ensure_product_table_schema(mysqli $mysqli): void
{
    $columnsResult = $mysqli->query('SHOW COLUMNS FROM product_table');
    $columns = [];
    while ($column = $columnsResult->fetch_assoc()) {
        $columns[$column['Field']] = strtolower($column['Type']);
    }

    if (!isset($columns['card_group'])) {
        $mysqli->query("ALTER TABLE product_table ADD COLUMN card_group VARCHAR(32) NOT NULL DEFAULT 'categories'");
    }
    if (!isset($columns['card_order'])) {
        $mysqli->query('ALTER TABLE product_table ADD COLUMN card_order INT NOT NULL DEFAULT 0');
    }
    if (!isset($columns['price'])) {
        $mysqli->query('ALTER TABLE product_table ADD COLUMN price DECIMAL(12,2) NOT NULL DEFAULT 0');
        $mysqli->query(
            "UPDATE product_table SET price = CASE card_group
                WHEN 'top-sellers' THEN CASE card_order
                    WHEN 0 THEN 50000 WHEN 1 THEN 500000 WHEN 2 THEN 5000
                    WHEN 3 THEN 250000 WHEN 4 THEN 72000 WHEN 5 THEN 102000
                    ELSE price END
                WHEN 'power-plans' THEN CASE card_order
                    WHEN 0 THEN 89990 WHEN 1 THEN 81396 WHEN 2 THEN 800000
                    WHEN 3 THEN 701150 WHEN 4 THEN 72000 WHEN 5 THEN 50000
                    WHEN 6 THEN 500000 WHEN 7 THEN 250000 WHEN 8 THEN 250000
                    WHEN 9 THEN 350000 ELSE price END
                WHEN 'for-you' THEN CASE card_order
                    WHEN 0 THEN 2400000 WHEN 1 THEN 21762486 WHEN 2 THEN 37000
                    WHEN 3 THEN 701150 WHEN 4 THEN 72000 WHEN 5 THEN 50000
                    WHEN 6 THEN 500000 WHEN 7 THEN 5000 WHEN 8 THEN 250000
                    WHEN 9 THEN 72000 ELSE price END
                WHEN 'featured' THEN CASE card_order
                    WHEN 0 THEN 48000 WHEN 1 THEN 460000 ELSE price END
                ELSE price END
             WHERE card_group IN ('top-sellers', 'power-plans', 'for-you', 'featured')"
        );
    }
    if (!isset($columns['old_price'])) {
        $mysqli->query('ALTER TABLE product_table ADD COLUMN old_price DECIMAL(12,2) NOT NULL DEFAULT 0');
        $mysqli->query(
            "UPDATE product_table SET old_price = CASE card_group
                WHEN 'top-sellers' THEN CASE card_order
                    WHEN 0 THEN 52000 WHEN 1 THEN 52000 WHEN 2 THEN 5999
                    WHEN 3 THEN 300000 WHEN 4 THEN 75999 WHEN 5 THEN 120999
                    ELSE old_price END
                WHEN 'power-plans' THEN CASE card_order
                    WHEN 0 THEN 120999 WHEN 1 THEN 88999 WHEN 2 THEN 902599
                    WHEN 3 THEN 820999 WHEN 4 THEN 80119 WHEN 5 THEN 760209
                    WHEN 6 THEN 550000 WHEN 7 THEN 300700 WHEN 8 THEN 270999
                    WHEN 9 THEN 399999 ELSE old_price END
                WHEN 'featured' THEN CASE card_order
                    WHEN 0 THEN 50000 WHEN 1 THEN 500000 ELSE old_price END
                ELSE old_price END
             WHERE card_group IN ('top-sellers', 'power-plans', 'featured')"
        );
    }
    if (isset($columns['description']) && strpos($columns['description'], 'varchar') === 0) {
        $mysqli->query('ALTER TABLE product_table MODIFY description TEXT NOT NULL');
    }
    if (!isset($columns['card_group']) || !isset($columns['card_order'])) {
        $mysqli->query('UPDATE product_table SET card_order = id - 1');
    }

    $indexesResult = $mysqli->query('SHOW INDEX FROM product_table');
    $hasGroupOrderIndex = false;
    while ($index = $indexesResult->fetch_assoc()) {
        if ($index['Key_name'] === 'uq_product_table_group_order') {
            $hasGroupOrderIndex = true;
        }
    }
    if (!$hasGroupOrderIndex) {
        $mysqli->query('ALTER TABLE product_table ADD UNIQUE KEY uq_product_table_group_order (card_group, card_order)');
    }
}

function sync_product_table(mysqli $mysqli, array $siteData): void
{
    ensure_product_table_schema($mysqli);

    $groups = array_fill_keys(PRODUCT_CARD_GROUPS, []);
    foreach ($siteData['containers'] ?? [] as $container) {
        $group = $container['placement'] ?? 'featured';
        if (!in_array($group, PRODUCT_CARD_GROUPS, true)) {
            throw new RuntimeException('A product section has an unsupported card group.');
        }
        foreach ($container['products'] ?? [] as $product) {
            $groups[$group][] = $product;
        }
    }

    $mysqli->begin_transaction();
    try {
        $statement = $mysqli->prepare(
            'INSERT INTO product_table (image, product_name, description, action, card_group, card_order)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE image = VALUES(image), product_name = VALUES(product_name),
                 description = VALUES(description)'
        );
        $action = '';
        $statement->bind_param('sssssi', $image, $name, $description, $action, $group, $order);

        $deleteStale = $mysqli->prepare('DELETE FROM product_table WHERE card_group = ? AND card_order >= ?');
        foreach ($groups as $group => $products) {
            foreach ($products as $order => $product) {
                $image = (string) ($product['image'] ?? '');
                $name = (string) ($product['name'] ?? '');
                $description = (string) ($product['description'] ?? '');
                $statement->execute();
            }
            $order = count($products);
            $deleteStale->bind_param('si', $group, $order);
            $deleteStale->execute();
        }
        $mysqli->commit();
    } catch (Throwable $exception) {
        $mysqli->rollback();
        throw $exception;
    }
}

function product_table_cards(mysqli $mysqli): array
{
    $result = $mysqli->query(
        'SELECT card_group, image, product_name, description, price, old_price
         FROM product_table
         ORDER BY card_group, card_order'
    );
    $groups = array_fill_keys(PRODUCT_CARD_GROUPS, []);
    while ($product = $result->fetch_assoc()) {
        $groups[$product['card_group']][] = [
            'image' => $product['image'],
            'name' => $product['product_name'],
            'description' => $product['description'],
            'price' => (float) $product['price'],
            'old_price' => (float) $product['old_price']
        ];
    }
    return $groups;
}
