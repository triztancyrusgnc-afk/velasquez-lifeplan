<?php
echo "<h2>🔄 Step 3: Beneficiaries, Claims & Inventory</h2>";
echo "<pre style='background:#1a1a1e;padding:15px;border-radius:8px;color:#4ade80;'>";

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=velasquez_lifeplan;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // Guard: Step 2 must run first
    try {
        $pdo->query("SELECT 1 FROM subscriptions LIMIT 1");
    } catch (PDOException $e) {
        echo "⚠️ The subscriptions table doesn't exist yet.\n";
        echo "➡️ Please run Step1.php then Step2.php first, then come back here.";
        echo "</pre>";
        echo "<br><a href='Step2.php' style='color:#e28743;'>⬅️ Back to Step 2</a>";
        exit;
    }
    echo "✅ Connected to 'velasquez_lifeplan'\n";

    // Create beneficiaries table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS beneficiaries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            subscription_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            relationship VARCHAR(50),
            phone VARCHAR(20),
            is_primary TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (subscription_id) REFERENCES subscriptions(id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Beneficiaries table created\n";

    // Create claims table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS claims (
            id INT AUTO_INCREMENT PRIMARY KEY,
            subscription_id INT NOT NULL,
            user_id INT NOT NULL,
            claim_type VARCHAR(50),
            status ENUM('pending','processing','approved','rejected','completed') DEFAULT 'pending',
            documents TEXT,
            notes TEXT,
            processed_by INT,
            deceased_name VARCHAR(150) NULL,
            date_of_death DATE NULL,
            death_certificate_path VARCHAR(255) NULL,
            chapel_reserved VARCHAR(100) NULL,
            hearse_assigned VARCHAR(100) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (subscription_id) REFERENCES subscriptions(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Claims table created\n";

    // Create logistics_inventory table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS logistics_inventory (
            id INT AUTO_INCREMENT PRIMARY KEY,
            item_name VARCHAR(100) NOT NULL,
            category VARCHAR(50),
            quantity INT DEFAULT 0,
            unit_price DECIMAL(10,2),
            status ENUM('available','reserved','used') DEFAULT 'available',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    echo "✅ Logistics inventory table created\n";

    // Create claim_logistics table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS claim_logistics (
            id INT AUTO_INCREMENT PRIMARY KEY,
            claim_id INT NOT NULL,
            inventory_id INT NOT NULL,
            quantity INT DEFAULT 1,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (claim_id) REFERENCES claims(id),
            FOREIGN KEY (inventory_id) REFERENCES logistics_inventory(id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Claim logistics table created\n";

    // Create audit_log table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS audit_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            action VARCHAR(100),
            details TEXT,
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    echo "✅ Audit log table created\n";

    // Create contact_inquiries table (powers the contact forms)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contact_inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            subject VARCHAR(50),
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    echo "✅ Contact inquiries table created\n";

    // Seed demo beneficiaries for Maria Santos' subscription
    $stmt = $pdo->prepare("INSERT INTO beneficiaries (subscription_id, name, relationship, phone, is_primary) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([1, 'Jose Santos', 'Husband', '09171112222', 1]);
    $stmt->execute([1, 'Carla Santos', 'Daughter', '09173334444', 0]);
    $stmt->execute([2, 'Rosa Dela Cruz', 'Wife', '09179876543', 1]);
    echo "✅ 3 demo beneficiaries inserted\n";

    // Seed chapel / casket / flower inventory
    $items = [
        ['St. Anne Chapel', 'chapel', 1, null, 'available', '3rd Floor, 200+ capacity'],
        ['St. Peter Chapel', 'chapel', 1, null, 'available', '2nd Floor, 150+ capacity'],
        ['Executive Hearse', 'hearse', 2, 15000, 'available', 'Premium black hearse'],
        ['Oak Wood Casket', 'casket', 5, 12000, 'available', 'Standard oak casket'],
        ['Premium Flower Set', 'flower', 10, 3000, 'available', 'Mixed flower arrangement'],
    ];
    $stmt = $pdo->prepare("INSERT INTO logistics_inventory (item_name, category, quantity, unit_price, status, notes) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmt->execute($item);
    }
    echo "✅ 5 inventory items inserted\n";

    // Extra inventory: urns, flowers, memorial accessories (incl. low-stock demos)
    $moreItems = [
        ['Marble Memorial Urn', 'urn', 8, 4500, 'available', 'For cremation plans'],
        ['White Flower Set', 'flower', 12, 2500, 'available', 'Standard sympathy arrangement'],
        ['Premium Flower Set', 'flower', 2, 3000, 'available', 'Low stock — reorder soon'],
        ['Memorial Guest Book', 'accessory', 20, 800, 'available', 'Hardbound guest registry'],
        ['Sympathy Tarpaulin', 'accessory', 1, 1200, 'available', 'Last piece — reorder soon'],
        ['Candle Set (12 pcs)', 'accessory', 15, 600, 'available', 'Vigil candles'],
    ];
    foreach ($moreItems as $item) {
        $stmt->execute($item);
    }
    echo "✅ 6 more inventory items inserted\n";

    // One demo at-need claim awaiting review
    $stmt = $pdo->prepare("INSERT INTO claims (subscription_id, user_id, claim_type, status, notes, deceased_name, date_of_death, chapel_reserved, hearse_assigned) VALUES (?, ?, 'funeral', 'pending', ?, ?, ?, ?, ?)");
    $stmt->execute([1, 3, 'Pickup at Sto. Cristo, Guagua. Family requests morning viewing.', 'Jose Santos Sr', date('Y-m-d', strtotime('-3 days')), 'St. Anne Chapel', 'Executive Hearse']);
    echo "✅ 1 demo claim filed\n";

    echo "\n🎉 Step 3 Complete!\n";
    echo "➡️ Next: Run Step4.php to connect the system and finish\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
    echo "\n\n⚠️ Did you run Step1.php and Step2.php first? Go back and run them, then refresh this page.";
}

echo "</pre>";
echo "<br><a href='Step4.php' style='color:#e28743;'>➡️ Go to Step 4</a>";
?>
