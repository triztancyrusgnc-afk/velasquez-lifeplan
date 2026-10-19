<?php
echo "<h2>🔄 Step 5: Policy Tools Upgrade</h2>";
echo "<pre style='background:#1a1a1e;padding:15px;border-radius:8px;color:#4ade80;'>";

function vls_column_exists($pdo, $table, $column) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->execute([$table, $column]);
    return ((int)$stmt->fetchColumn()) > 0;
}

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

    // Guard: Steps 1-4 must run first
    foreach (['users', 'plans', 'subscriptions', 'claims', 'payments'] as $table) {
        try {
            $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
        } catch (PDOException $e) {
            echo "⚠️ The '$table' table doesn't exist yet.\n";
            echo "➡️ Please run Step1.php → Step4.php first, then come back here.";
            echo "</pre>";
            echo "<br><a href='Step1.php' style='color:#e28743;'>⬅️ Back to Step 1</a>";
            exit;
        }
    }
    echo "✅ Connected to 'velasquez_lifeplan'\n";

    // 1. Agent role (sales agents sit between staff and admin)
    $pdo->exec("ALTER TABLE users MODIFY role ENUM('admin','employee','customer','agent') DEFAULT 'customer'");
    echo "✅ 'agent' role added to users\n";

    // 2. At-need claim fields (deceased info, certificate, chapel + hearse booking)
    $claimFields = [
        'deceased_name' => "VARCHAR(150) NULL AFTER user_id",
        'date_of_death' => "DATE NULL AFTER deceased_name",
        'death_certificate_path' => "VARCHAR(255) NULL AFTER date_of_death",
        'chapel_reserved' => "VARCHAR(100) NULL AFTER death_certificate_path",
        'hearse_assigned' => "VARCHAR(100) NULL AFTER chapel_reserved",
    ];
    foreach ($claimFields as $col => $def) {
        if (!vls_column_exists($pdo, 'claims', $col)) {
            $pdo->exec("ALTER TABLE claims ADD COLUMN $col $def");
            echo "✅ claims.$col added\n";
        } else {
            echo "— claims.$col already exists, skipped\n";
        }
    }

    // 3. Official receipt numbers on payments
    if (!vls_column_exists($pdo, 'payments', 'receipt_number')) {
        $pdo->exec("ALTER TABLE payments ADD COLUMN receipt_number VARCHAR(100) NULL AFTER reference_number");
        echo "✅ payments.receipt_number added\n";
    } else {
        echo "— payments.receipt_number already exists, skipped\n";
    }
    $backfilled = $pdo->exec("UPDATE payments SET receipt_number = CONCAT('OR-', LPAD(id, 6, '0')) WHERE status = 'verified' AND (receipt_number IS NULL OR receipt_number = '')");
    echo "✅ $backfilled verified payment(s) stamped with OR numbers\n";

    // 4. Upload folders (IDs, death certificates, payment proofs, site photos)
    foreach (['uploads', 'uploads/proofs', 'uploads/claims', 'uploads/ids', 'uploads/site'] as $dir) {
        $path = __DIR__ . '/' . $dir;
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
            echo "✅ folder /$dir created\n";
        } else {
            echo "— folder /$dir already exists, skipped\n";
        }
    }

    // 5. Admin-editable website content (homepage hero, trust badges, plan availability)
    $pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
        setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
        setting_value TEXT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✅ site_settings table ready\n";

    echo "\n🎉 Step 5 Complete! Claim wizard, receipts, agent role & site settings are ready.\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
    echo "\n\n⚠️ Make sure MySQL is running and Steps 1-4 were completed.";
}

echo "</pre>";
echo "<br><a href='home.php' style='color:#4ade80;font-weight:bold;font-size:1.1rem;'>🏠 Go to Homepage</a>";
?>
