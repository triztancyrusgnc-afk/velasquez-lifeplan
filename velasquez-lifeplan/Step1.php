<?php
echo "<h2>🔄 Step 1: Database Setup</h2>";
echo "<pre style='background:#1a1a1e;padding:15px;border-radius:8px;color:#4ade80;'>";

try {
    // Connect WITHOUT specifying a database so we can drop/recreate it
    $pdo = new PDO(
        "mysql:host=localhost;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // Drop if exists, then create fresh
    $pdo->exec("DROP DATABASE IF EXISTS velasquez_lifeplan");
    $pdo->exec("CREATE DATABASE velasquez_lifeplan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE velasquez_lifeplan");
    echo "✅ Database 'velasquez_lifeplan' created\n";

    // Create users table
    $pdo->exec("
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin','employee','customer') DEFAULT 'customer',
            phone VARCHAR(20),
            address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    echo "✅ Users table created\n";

    // Create plans table
    $pdo->exec("
        CREATE TABLE plans (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            category ENUM('traditional','cremation','executive') NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            spot_cash DECIMAL(10,2),
            monthly_5yr DECIMAL(10,2),
            monthly_10yr DECIMAL(10,2),
            installment_price DECIMAL(10,2),
            downpayment DECIMAL(10,2),
            casket_type VARCHAR(100),
            chapel_viewing_days INT DEFAULT 3,
            hearse_service TINYINT(1) DEFAULT 1,
            flowers TINYINT(1) DEFAULT 0,
            transferable TINYINT(1) DEFAULT 0,
            assignable TINYINT(1) DEFAULT 0,
            cash_back DECIMAL(10,2) DEFAULT 0,
            insurance_coverage DECIMAL(10,2) DEFAULT 0,
            features TEXT,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    echo "✅ Plans table created\n";

    // Seed demo users
    $users = [
        ['Admin Velasquez', 'admin@velasquez.com', password_hash('admin123', PASSWORD_DEFAULT), 'admin', '+639171234567', '123 Admin St, Manila'],
        ['Juan Employee', 'employee@velasquez.com', password_hash('employee123', PASSWORD_DEFAULT), 'employee', '+639172345678', '456 Employee Ave, Quezon City'],
        ['Maria Santos', 'maria.santos@gmail.com', password_hash('customer123', PASSWORD_DEFAULT), 'customer', '+639173456789', '789 Customer Rd, Makati'],
        ['Juan Dela Cruz', 'juan.dela.cruz@gmail.com', password_hash('customer123', PASSWORD_DEFAULT), 'customer', '+639174567890', '321 Dela Cruz Blvd, Pasig'],
        ['Ana Reyes', 'ana.reyes@yahoo.com', password_hash('customer123', PASSWORD_DEFAULT), 'customer', '+639175678901', '654 Reyes St, Taguig'],
        ['Pedro Garcia', 'pedro.garcia@gmail.com', password_hash('customer123', PASSWORD_DEFAULT), 'customer', '+63917678901', 'Sta. Cruz, Guagua, Pampanga'],
        ['Liza Mendoza', 'liza.mendoza@yahoo.com', password_hash('customer123', PASSWORD_DEFAULT), 'customer', '+63918789012', 'San Roque, Guagua, Pampanga'],
    ];
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($users as $user) {
        $stmt->execute($user);
    }
    echo "✅ 7 demo users inserted (admin / employee / 5 customers)\n";

    echo "\n🎉 Step 1 Complete!\n";
    echo "➡️ Next: Run Step2.php to create the life plans\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
    echo "\n\n⚠️ Make sure MySQL is running in XAMPP Control Panel, then refresh this page.";
}

echo "</pre>";
echo "<br><a href='Step2.php' style='color:#e28743;'>➡️ Go to Step 2</a>";
?>
