<?php
echo "<h2>🔄 Step 2: Life Plans + Subscriptions</h2>";
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

    // Guard: Step 1 must run first
    try {
        $pdo->query("SELECT 1 FROM users LIMIT 1");
        $pdo->query("SELECT 1 FROM plans LIMIT 1");
    } catch (PDOException $e) {
        echo "⚠️ The users/plans tables don't exist yet.\n";
        echo "➡️ Please run Step1.php first, then come back here.";
        echo "</pre>";
        echo "<br><a href='Step1.php' style='color:#e28743;'>⬅️ Back to Step 1</a>";
        exit;
    }
    echo "✅ Connected to 'velasquez_lifeplan'\n";

    // Create subscriptions table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS subscriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            plan_id INT NOT NULL,
            status ENUM('pending','active','expired','cancelled') DEFAULT 'pending',
            payment_method VARCHAR(50),
            total_amount DECIMAL(10,2),
            paid_amount DECIMAL(10,2) DEFAULT 0,
            remaining_balance DECIMAL(10,2) DEFAULT 0,
            downpayment DECIMAL(10,2) DEFAULT 0,
            start_date DATE,
            end_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (plan_id) REFERENCES plans(id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Subscriptions table created\n";

    // Create payment_schedule table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS payment_schedule (
            id INT AUTO_INCREMENT PRIMARY KEY,
            subscription_id INT NOT NULL,
            installment_no INT NOT NULL,
            due_date DATE NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            status ENUM('pending','paid','overdue') DEFAULT 'pending',
            paid_date DATE,
            proof_image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (subscription_id) REFERENCES subscriptions(id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Payment schedule table created\n";

    // Create payments table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            subscription_id INT NOT NULL,
            installment_id INT,
            amount DECIMAL(10,2) NOT NULL,
            payment_method VARCHAR(50),
            reference_number VARCHAR(100),
            receipt_number VARCHAR(100) NULL,
            proof_image VARCHAR(255),
            status ENUM('pending','verified','rejected') DEFAULT 'pending',
            verified_by INT,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (subscription_id) REFERENCES subscriptions(id)
        ) ENGINE=InnoDB
    ");
    echo "✅ Payments table created\n";

    // Seed the 6 life plans
    $plans = [
        ['Traditional Bronze', 'traditional', 'An affordable yet dignified funeral plan with essential services for a peaceful farewell.', 35000, 32000, 583, 300, 583, 5000, 'Standard Wood Casket', 3, 1, 0, 0, 0, 0, 0, json_encode(['Chapel Use 3 Days', 'Basic Sound System', 'Memorial Card', 'Standard Flower Arrangement', 'Professional Staff'])],
        ['Silver Chapel', 'traditional', 'A comprehensive traditional plan with upgraded casket and extended chapel viewing.', 55000, 50000, 917, 460, 917, 8000, 'Oak Wood Casket', 4, 1, 1, 1, 0, 0, 0, json_encode(['Chapel Use 4 Days', 'Premium Sound System', 'Memorial Card', 'Premium Flower Arrangement', 'Professional Staff', 'Photo Display'])],
        ['Gold Deluxe', 'traditional', 'Premium traditional funeral service with luxury casket and comprehensive inclusions.', 85000, 78000, 1417, 710, 1417, 12000, 'Premium Walnut Casket', 5, 1, 1, 1, 1, 0, 5000, json_encode(['Chapel Use 5 Days', 'Premium Sound & Video System', 'Memorial Card', 'Luxury Flower Arrangement', 'Professional Staff', 'Photo & Video Display', 'Air-conditioned Viewing Room'])],
        ['Platinum Vault', 'executive', 'Exclusive executive vault package with imported casket and VIP services.', 120000, 110000, 2000, 1000, 2000, 18000, 'Imported Mahogany', 7, 1, 1, 1, 1, 10000, 20000, json_encode(['Chapel Use 7 Days', 'VIP Sound & Video System', 'Premium Memorial Card', 'Grand Flower Arrangement', 'Dedicated Concierge', 'Photo & Video Display', 'VIP Viewing Room', 'Insurance Coverage'])],
        ['Cremation Basic', 'cremation', 'Simple and affordable cremation service with dignified handling.', 25000, 22000, 417, 210, 417, 3000, 'N/A (Cremation Urn)', 2, 0, 0, 0, 0, 0, 0, json_encode(['Cremation Service', 'Basic Urn Included', 'Memorial Service', 'Death Certificate Assistance'])],
        ['Executive Vault', 'executive', 'The pinnacle of funeral planning with premium imported materials and full-service care.', 200000, 185000, 3333, 1670, 3333, 30000, 'Royal Walnut Imported', 10, 1, 1, 1, 1, 25000, 50000, json_encode(['Chapel Use 10 Days', 'Elite Sound & Video System', 'Premium Memorial Card', 'Grand Luxury Flower Arrangement', 'Dedicated Concierge Team', 'Photo & Video Display', 'Elite Viewing Room', 'Full Insurance Coverage', 'Estate Planning Consultation', 'Priority Service'])],
    ];
    $stmt = $pdo->prepare("INSERT INTO plans (name, category, description, price, spot_cash, monthly_5yr, monthly_10yr, installment_price, downpayment, casket_type, chapel_viewing_days, hearse_service, flowers, transferable, assignable, cash_back, insurance_coverage, features) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($plans as $plan) {
        $stmt->execute($plan);
    }
    echo "✅ 6 life plans inserted\n";

    // Demo subscription for Maria Santos (user 3, Silver Chapel plan 2)
    $startDate = date('Y-m-d', strtotime('-3 months'));
    $endDate = date('Y-m-d', strtotime('+57 months'));
    $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, plan_id, status, payment_method, total_amount, paid_amount, remaining_balance, downpayment, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([3, 2, 'active', 'installment', 55000, 27500, 27500, 8000, $startDate, $endDate]);
    echo "✅ Demo subscription created for Maria Santos\n";

    // More demo subscriptions: 2 awaiting approval + 2 more active policies
    $stmt->execute([4, 1, 'pending', 'gcash', 35000, 5000, 30000, 5000, date('Y-m-d', strtotime('-1 month')), date('Y-m-d', strtotime('+59 months'))]);
    $stmt->execute([5, 3, 'pending', 'bank_transfer', 85000, 12000, 73000, 12000, date('Y-m-d', strtotime('-2 weeks')), date('Y-m-d', strtotime('+60 months'))]);
    $stmt->execute([6, 5, 'active', 'cash', 25000, 10000, 15000, 5000, date('Y-m-d', strtotime('-4 months')), date('Y-m-d', strtotime('+56 months'))]);
    $stmt->execute([7, 2, 'active', 'gcash', 55000, 20000, 35000, 11000, date('Y-m-d', strtotime('-2 months')), date('Y-m-d', strtotime('+58 months'))]);
    echo "✅ 4 more demo subscriptions inserted (2 pending approval, 2 active)\n";

    // Demo payment schedule (3 paid, 3 pending)
    $schedule = [
        [1, 1, date('Y-m-d', strtotime('-3 months')), 917, 'paid', date('Y-m-d', strtotime('-3 months'))],
        [1, 2, date('Y-m-d', strtotime('-2 months')), 917, 'paid', date('Y-m-d', strtotime('-2 months'))],
        [1, 3, date('Y-m-d', strtotime('-1 month')), 917, 'paid', date('Y-m-d', strtotime('-1 month'))],
        [1, 4, date('Y-m-d'), 917, 'pending', null],
        [1, 5, date('Y-m-d', strtotime('+1 month')), 917, 'pending', null],
        [1, 6, date('Y-m-d', strtotime('+2 months')), 917, 'pending', null],
    ];
    $stmt = $pdo->prepare("INSERT INTO payment_schedule (subscription_id, installment_no, due_date, amount, status, paid_date) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($schedule as $row) {
        $stmt->execute($row);
    }
    echo "✅ 6 payment schedule rows inserted\n";

    // More schedules: overdue + upcoming installments for the new policies
    $moreSchedule = [
        [2, 1, date('Y-m-d', strtotime('-10 days')), 583, 'pending', null],
        [2, 2, date('Y-m-d', strtotime('+20 days')), 583, 'pending', null],
        [2, 3, date('Y-m-d', strtotime('+50 days')), 583, 'pending', null],
        [3, 1, date('Y-m-d', strtotime('-5 days')), 1417, 'pending', null],
        [3, 2, date('Y-m-d', strtotime('+25 days')), 1417, 'pending', null],
        [4, 1, date('Y-m-d', strtotime('-4 months')), 417, 'paid', date('Y-m-d', strtotime('-4 months'))],
        [4, 2, date('Y-m-d', strtotime('-3 months')), 417, 'paid', date('Y-m-d', strtotime('-3 months'))],
        [4, 3, date('Y-m-d', strtotime('-15 days')), 417, 'pending', null],
        [4, 4, date('Y-m-d', strtotime('+15 days')), 417, 'pending', null],
        [5, 1, date('Y-m-d', strtotime('-2 months')), 917, 'paid', date('Y-m-d', strtotime('-2 months'))],
        [5, 2, date('Y-m-d', strtotime('-1 month')), 917, 'paid', date('Y-m-d', strtotime('-1 month'))],
        [5, 3, date('Y-m-d', strtotime('+10 days')), 917, 'pending', null],
    ];
    foreach ($moreSchedule as $row) {
        $stmt->execute($row);
    }
    echo "✅ 12 more schedule rows inserted (incl. overdue demos)\n";

    // Payments awaiting verification (with reference numbers)
    $stmt = $pdo->prepare("INSERT INTO payments (subscription_id, amount, payment_method, reference_number, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([4, 417, 'Cash', 'OR-MANUAL-001']);
    $stmt->execute([5, 917, 'GCash', 'GCASH-REF-889900']);
    $stmt->execute([1, 917, 'GCash', 'GCASH-REF-771122']);
    echo "✅ 3 payments queued for verification\n";

    // Backdated VERIFIED payments so the charts have real history
    $stmt = $pdo->prepare("INSERT INTO payments (subscription_id, amount, payment_method, reference_number, receipt_number, status, created_at) VALUES (?, ?, ?, ?, ?, 'verified', ?)");
    $history = [
        [1, 1500, 'GCash', 'GCASH-OLD-101', 'OR-000101', date('Y-m-d H:i:s', strtotime('-3 months'))],
        [1, 917, 'GCash', 'GCASH-OLD-102', 'OR-000102', date('Y-m-d H:i:s', strtotime('-1 month'))],
        [4, 2000, 'Cash', 'OR-MANUAL-002', 'OR-000103', date('Y-m-d H:i:s', strtotime('-2 months'))],
        [4, 1500, 'Cash', 'OR-MANUAL-003', 'OR-000104', date('Y-m-d H:i:s', strtotime('-3 months'))],
        [5, 5000, 'GCash', 'GCASH-OLD-105', 'OR-000105', date('Y-m-d H:i:s', strtotime('-1 month'))],
        [5, 3000, 'GCash', 'GCASH-OLD-106', 'OR-000106', date('Y-m-d H:i:s', strtotime('-2 months'))],
        [1, 2000, 'Bank Transfer', 'BANK-OLD-107', 'OR-000107', date('Y-m-d H:i:s', strtotime('-4 months'))],
        [1, 1200, 'Bank Transfer', 'BANK-OLD-108', 'OR-000108', date('Y-m-d H:i:s', strtotime('-5 months'))],
    ];
    foreach ($history as $hpay) {
        $stmt->execute($hpay);
    }
    echo "✅ 8 verified payments backfilled for chart history\n";

    echo "\n🎉 Step 2 Complete!\n";
    echo "➡️ Next: Run Step3.php to create beneficiaries, claims & inventory\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
    echo "\n\n⚠️ Did you run Step1.php first? Go back and run it, then refresh this page.";
}

echo "</pre>";
echo "<br><a href='Step3.php' style='color:#e28743;'>➡️ Go to Step 3</a>";
?>
