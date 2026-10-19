<?php
session_start();

// Check if already installed: config file must exist AND the database must be reachable.
// (File-only checks cause a redirect loop on fresh PCs copied with an old config.php.)
$installed = false;
$configBroken = false;
if (file_exists(__DIR__ . '/config.php')) {
    try {
        require_once __DIR__ . '/config.php';
        $testPdo = getDB();
        $testPdo->query('SELECT 1');
        $installed = true;
    } catch (Throwable $e) {
        $configBroken = true;
    }
}

if ($installed && !isset($_POST['step'])) {
    header('Location: home.php');
    exit;
}

// Handle form submissions
$step = isset($_POST['step']) ? (int)$_POST['step'] : 1;
$message = '';
$messageType = '';
$installationResult = null;

if ($installed && $step === 1) {
    $step = 4;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 2) {
        // Test Connection
        $host = trim($_POST['db_host'] ?? 'localhost');
        $username = trim($_POST['db_user'] ?? 'root');
        $password = $_POST['db_pass'] ?? '';
        $dbname = trim($_POST['db_name'] ?? 'velasquez_lifeplan');

        try {
            $pdo = new PDO("mysql:host=$host", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $message = "Connection successful!";
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = "Connection failed: " . $e->getMessage();
            $messageType = 'error';
        }
    }

    if ($step === 3) {
        $host = trim($_POST['db_host'] ?? 'localhost');
        $username = trim($_POST['db_user'] ?? 'root');
        $password = $_POST['db_pass'] ?? '';
        $dbname = trim($_POST['db_name'] ?? 'velasquez_lifeplan');

        try {
            // 1. Connect without selecting DB
            $pdo = new PDO("mysql:host=$host", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // 2. Create database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 3. Select database
            $pdo->exec("USE `$dbname`");

            // 4. Create tables
            $tables = [
                "CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    role ENUM('admin','employee','customer') DEFAULT 'customer',
                    phone VARCHAR(20),
                    address TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB",
                "CREATE TABLE IF NOT EXISTS plans (
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
                ) ENGINE=InnoDB",
                "CREATE TABLE IF NOT EXISTS subscriptions (
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
                ) ENGINE=InnoDB",
                "CREATE TABLE IF NOT EXISTS payment_schedule (
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
                ) ENGINE=InnoDB",
                "CREATE TABLE IF NOT EXISTS payments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    subscription_id INT NOT NULL,
                    installment_id INT,
                    amount DECIMAL(10,2) NOT NULL,
                    payment_method VARCHAR(50),
                    reference_number VARCHAR(100),
                    proof_image VARCHAR(255),
                    status ENUM('pending','verified','rejected') DEFAULT 'pending',
                    verified_by INT,
                    notes TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id)
                ) ENGINE=InnoDB",
                "CREATE TABLE IF NOT EXISTS beneficiaries (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    subscription_id INT NOT NULL,
                    name VARCHAR(100) NOT NULL,
                    relationship VARCHAR(50),
                    phone VARCHAR(20),
                    is_primary TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id)
                ) ENGINE=InnoDB",
                "CREATE TABLE IF NOT EXISTS claims (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    subscription_id INT NOT NULL,
                    user_id INT NOT NULL,
                    claim_type VARCHAR(50),
                    status ENUM('pending','processing','approved','rejected','completed') DEFAULT 'pending',
                    documents TEXT,
                    notes TEXT,
                    processed_by INT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id),
                    FOREIGN KEY (user_id) REFERENCES users(id)
                ) ENGINE=InnoDB",
                "CREATE TABLE IF NOT EXISTS logistics_inventory (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    item_name VARCHAR(100) NOT NULL,
                    category VARCHAR(50),
                    quantity INT DEFAULT 0,
                    unit_price DECIMAL(10,2),
                    status ENUM('available','reserved','used') DEFAULT 'available',
                    notes TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB",
                "CREATE TABLE IF NOT EXISTS claim_logistics (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    claim_id INT NOT NULL,
                    inventory_id INT NOT NULL,
                    quantity INT DEFAULT 1,
                    notes TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (claim_id) REFERENCES claims(id),
                    FOREIGN KEY (inventory_id) REFERENCES logistics_inventory(id)
                ) ENGINE=InnoDB",
                "CREATE TABLE IF NOT EXISTS audit_log (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT,
                    action VARCHAR(100),
                    details TEXT,
                    ip_address VARCHAR(45),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB",
                "CREATE TABLE IF NOT EXISTS contact_inquiries (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    phone VARCHAR(20),
                    subject VARCHAR(50),
                    message TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB",
                "CREATE TABLE IF NOT EXISTS site_settings (
                    setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
                    setting_value TEXT NULL,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            ];

            foreach ($tables as $sql) {
                $pdo->exec($sql);
            }

            // 5. Seed data
            // Users
            $users = [
                ['Admin Velasquez', 'admin@velasquez.com', password_hash('admin123', PASSWORD_DEFAULT), 'admin', '+639171234567', '123 Admin St, Manila'],
                ['Juan Employee', 'employee@velasquez.com', password_hash('employee123', PASSWORD_DEFAULT), 'employee', '+639172345678', '456 Employee Ave, Quezon City'],
                ['Maria Santos', 'maria.santos@gmail.com', password_hash('customer123', PASSWORD_DEFAULT), 'customer', '+639173456789', '789 Customer Rd, Makati'],
                ['Juan Dela Cruz', 'juan.dela.cruz@gmail.com', password_hash('customer123', PASSWORD_DEFAULT), 'customer', '+639174567890', '321 Dela Cruz Blvd, Pasig'],
                ['Ana Reyes', 'ana.reyes@yahoo.com', password_hash('customer123', PASSWORD_DEFAULT), 'customer', '+639175678901', '654 Reyes St, Taguig']
            ];

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($users as $user) {
                $stmt->execute($user);
            }

            // Plans
            $plans = [
                ['Traditional Bronze', 'traditional', 'An affordable yet dignified funeral plan with essential services for a peaceful farewell.', 35000, 32000, 583, 300, 583, 5000, 'Standard Wood Casket', 3, 1, 0, 0, 0, 0, 0, json_encode(['Chapel Use 3 Days', 'Basic Sound System', 'Memorial Card', 'Standard Flower Arrangement', 'Professional Staff'])],
                ['Silver Chapel', 'traditional', 'A comprehensive traditional plan with upgraded casket and extended chapel viewing.', 55000, 50000, 917, 460, 917, 8000, 'Oak Wood Casket', 4, 1, 1, 1, 0, 0, 0, json_encode(['Chapel Use 4 Days', 'Premium Sound System', 'Memorial Card', 'Premium Flower Arrangement', 'Professional Staff', 'Photo Display'])],
                ['Gold Deluxe', 'traditional', 'Premium traditional funeral service with luxury casket and comprehensive inclusions.', 85000, 78000, 1417, 710, 1417, 12000, 'Premium Walnut Casket', 5, 1, 1, 1, 1, 0, 5000, json_encode(['Chapel Use 5 Days', 'Premium Sound & Video System', 'Memorial Card', 'Luxury Flower Arrangement', 'Professional Staff', 'Photo & Video Display', 'Air-conditioned Viewing Room'])],
                ['Platinum Vault', 'executive', 'Exclusive executive vault package with imported casket and VIP services.', 120000, 110000, 2000, 1000, 2000, 18000, 'Imported Mahogany', 7, 1, 1, 1, 1, 10000, 20000, json_encode(['Chapel Use 7 Days', 'VIP Sound & Video System', 'Premium Memorial Card', 'Grand Flower Arrangement', 'Dedicated Concierge', 'Photo & Video Display', 'VIP Viewing Room', 'Insurance Coverage'])],
                ['Cremation Basic', 'cremation', 'Simple and affordable cremation service with dignified handling.', 25000, 22000, 417, 210, 417, 3000, 'N/A (Cremation Urn)', 2, 0, 0, 0, 0, 0, 0, json_encode(['Cremation Service', 'Basic Urn Included', 'Memorial Service', 'Death Certificate Assistance'])],
                ['Executive Vault', 'executive', 'The pinnacle of funeral planning with premium imported materials and full-service care.', 200000, 185000, 3333, 1670, 3333, 30000, 'Royal Walnut Imported', 10, 1, 1, 1, 1, 25000, 50000, json_encode(['Chapel Use 10 Days', 'Elite Sound & Video System', 'Premium Memorial Card', 'Grand Luxury Flower Arrangement', 'Dedicated Concierge Team', 'Photo & Video Display', 'Elite Viewing Room', 'Full Insurance Coverage', 'Estate Planning Consultation', 'Priority Service'])]
            ];

            $stmt = $pdo->prepare("INSERT INTO plans (name, category, description, price, spot_cash, monthly_5yr, monthly_10yr, installment_price, downpayment, casket_type, chapel_viewing_days, hearse_service, flowers, transferable, assignable, cash_back, insurance_coverage, features) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($plans as $plan) {
                $stmt->execute($plan);
            }

            // Subscriptions for Maria Santos
            $startDate = date('Y-m-d', strtotime('-3 months'));
            $endDate = date('Y-m-d', strtotime('+57 months'));
            $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, plan_id, status, payment_method, total_amount, paid_amount, remaining_balance, downpayment, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([3, 2, 'active', 'installment', 55000, 27500, 27500, 8000, $startDate, $endDate]);

            // Write config.php
            $configContent = "<?php\n";
            $configContent .= "define('DB_HOST', '$host');\n";
            $configContent .= "define('DB_USER', '$username');\n";
            $configContent .= "define('DB_PASS', '$password');\n";
            $configContent .= "define('DB_NAME', '$dbname');\n";
            $configContent .= "define('APP_NAME', 'Velasquez Funeral Service');\n";
            $configContent .= "define('APP_VERSION', '1.0.0');\n";
            $configContent .= "\n";
            $configContent .= "function getDB() {\n";
            $configContent .= "    static \$pdo = null;\n";
            $configContent .= "    if (\$pdo === null) {\n";
            $configContent .= "        \$pdo = new PDO(\n";
            $configContent .= "            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',\n";
            $configContent .= "            DB_USER,\n";
            $configContent .= "            DB_PASS,\n";
            $configContent .= "            [\n";
            $configContent .= "                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n";
            $configContent .= "                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n";
            $configContent .= "                PDO::ATTR_EMULATE_PREPARES => false\n";
            $configContent .= "            ]\n";
            $configContent .= "        );\n";
            $configContent .= "    }\n";
            $configContent .= "    return \$pdo;\n";
            $configContent .= "}\n";

            file_put_contents(__DIR__ . '/config.php', $configContent);

            $installationResult = 'success';

        } catch (PDOException $e) {
            $installationResult = 'error';
            $message = "Installation failed: " . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Velasquez Funeral Service — Life Plan System Installer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy: #0F172A;
            --navy-light: #1E293B;
            --navy-lighter: #334155;
            --gold: #D4AF37;
            --gold-light: #E8CC6E;
            --gold-dark: #B8960F;
            --cream: #F8FAFC;
            --white: #FFFFFF;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-300: #CBD5E1;
            --gray-400: #94A3B8;
            --gray-500: #64748B;
            --gray-600: #475569;
            --green: #22C55E;
            --green-bg: #F0FDF4;
            --red: #EF4444;
            --red-bg: #FEF2F2;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            --radius: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--cream);
            color: var(--navy);
            min-height: 100vh;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4 {
            font-family: 'Playfair Display', Georgia, serif;
            font-weight: 600;
            line-height: 1.3;
        }

        /* Header */
        .installer-header {
            background: var(--navy);
            color: var(--white);
            padding: 20px 0;
            box-shadow: var(--shadow-lg);
        }

        .installer-header .container {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .installer-header svg {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
        }

        .installer-header h1 {
            font-size: 1.25rem;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        .installer-header .version {
            color: var(--gold);
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 2px;
        }

        /* Container */
        .container {
            max-width: 720px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Progress Bar */
        .progress-section {
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            padding: 24px 0;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 18px;
            left: 40px;
            right: 40px;
            height: 3px;
            background: var(--gray-200);
            z-index: 0;
        }

        .progress-line {
            position: absolute;
            top: 18px;
            left: 40px;
            height: 3px;
            background: var(--gold);
            z-index: 1;
            transition: width 0.5s ease;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            position: relative;
            z-index: 2;
        }

        .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--gray-200);
            color: var(--gray-500);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            border: 3px solid var(--white);
        }

        .step-item.active .step-circle {
            background: var(--gold);
            color: var(--white);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.2);
        }

        .step-item.completed .step-circle {
            background: var(--green);
            color: var(--white);
        }

        .step-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--gray-400);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .step-item.active .step-label {
            color: var(--navy);
            font-weight: 600;
        }

        .step-item.completed .step-label {
            color: var(--green);
        }

        /* Main Content */
        .main-content {
            padding: 40px 0 60px;
        }

        .card {
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            padding: 48px;
            border: 1px solid var(--gray-200);
        }

        .card-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .card-header .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
        }

        .card-header h2 {
            font-size: 1.75rem;
            color: var(--navy);
            margin-bottom: 8px;
        }

        .card-header p {
            color: var(--gray-500);
            font-size: 1rem;
        }

        /* Welcome Logo */
        .welcome-logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.3);
            border: 4px solid var(--gold);
        }

        .welcome-logo svg {
            width: 60px;
            height: 60px;
        }

        /* Requirements */
        .requirements-list {
            list-style: none;
            margin: 32px 0;
        }

        .requirements-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-radius: var(--radius-lg);
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }

        .requirements-list li.pass {
            background: var(--green-bg);
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .requirements-list li.fail {
            background: var(--red-bg);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .req-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .req-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .pass .req-icon { background: var(--green); color: var(--white); }
        .fail .req-icon { background: var(--red); color: var(--white); }

        .req-name {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .req-detail {
            font-size: 0.85rem;
            color: var(--gray-500);
        }

        .req-status {
            font-weight: 600;
            font-size: 0.85rem;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .pass .req-status { color: var(--green); background: rgba(34, 197, 94, 0.1); }
        .fail .req-status { color: var(--red); background: rgba(239, 68, 68, 0.1); }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--navy);
            margin-bottom: 6px;
        }

        .form-group .hint {
            font-size: 0.8rem;
            color: var(--gray-400);
            font-weight: 400;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius);
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            color: var(--navy);
            background: var(--white);
            transition: all 0.2s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
        }

        .form-control::placeholder {
            color: var(--gray-400);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 28px;
            border-radius: var(--radius);
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid transparent;
            text-decoration: none;
            line-height: 1.4;
        }

        .btn-primary {
            background: var(--gold);
            color: var(--navy);
            border-color: var(--gold);
        }

        .btn-primary:hover {
            background: var(--gold-dark);
            border-color: var(--gold-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--navy);
            border-color: var(--gray-300);
        }

        .btn-secondary:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
        }

        .btn-success {
            background: var(--green);
            color: var(--white);
            border-color: var(--green);
        }

        .btn-success:hover {
            background: #16A34A;
            border-color: #16A34A;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-lg {
            padding: 14px 36px;
            font-size: 1rem;
        }

        .btn-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 32px;
            gap: 12px;
        }

        .btn-group-right {
            display: flex;
            gap: 12px;
        }

        /* Alert */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: var(--green-bg);
            color: #166534;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .alert-error {
            background: var(--red-bg);
            color: #991B1B;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Installation Steps */
        .install-steps {
            margin: 24px 0;
        }

        .install-step {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .install-step:last-child {
            border-bottom: none;
        }

        .install-step-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.85rem;
            font-weight: 700;
            background: var(--green);
            color: var(--white);
        }

        .install-step-text h4 {
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .install-step-text p {
            font-size: 0.85rem;
            color: var(--gray-500);
        }

        /* Success Screen */
        .success-screen {
            text-align: center;
            padding: 20px 0;
        }

        .success-checkmark {
            width: 80px;
            height: 80px;
            background: var(--green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 8px 24px rgba(34, 197, 94, 0.3);
        }

        .success-checkmark svg {
            width: 40px;
            height: 40px;
            color: var(--white);
        }

        .credentials-box {
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin: 32px 0;
            text-align: left;
        }

        .credentials-box h3 {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-500);
            margin-bottom: 16px;
        }

        .credential-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--gray-200);
            font-size: 0.9rem;
        }

        .credential-item:last-child {
            border-bottom: none;
        }

        .credential-item .label {
            font-weight: 600;
            color: var(--navy);
        }

        .credential-item .value {
            color: var(--gray-600);
            font-family: 'Courier New', monospace;
            background: var(--white);
            padding: 2px 8px;
            border-radius: 4px;
        }

        /* Already Installed */
        .already-installed {
            text-align: center;
        }

        .already-installed .icon-large {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            border: 4px solid var(--gold);
        }

        .already-installed .icon-large svg {
            width: 50px;
            height: 50px;
            color: var(--gold);
        }

        /* Footer */
        .installer-footer {
            text-align: center;
            padding: 24px 0;
            color: var(--gray-400);
            font-size: 0.8rem;
        }

        .installer-footer a {
            color: var(--gold);
            text-decoration: none;
        }

        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: var(--white);
            animation: spin 0.8s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 640px) {
            .card { padding: 32px 24px; }
            .card-header h2 { font-size: 1.4rem; }
            .form-row { grid-template-columns: 1fr; }
            .btn-group { flex-direction: column; }
            .btn-group .btn { width: 100%; }
            .btn-group-right { width: 100%; }
            .btn-group-right .btn { flex: 1; }
            .progress-steps::before { left: 24px; right: 24px; }
            .progress-line { left: 24px; }
            .step-label { font-size: 0.65rem; }
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="installer-header">
    <div class="container">
        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="24" cy="24" r="22" fill="#1A2634" stroke="#CCB877" stroke-width="2"/>
            <text x="24" y="30" text-anchor="middle" fill="#CCB877" font-family="Inter, sans-serif" font-size="14" font-weight="700">CSV</text>
        </svg>
        <div>
            <h1>Velasquez Funeral Service — Life Plan System</h1>
            <div class="version">Browser-Based Installer v1.0.0</div>
        </div>
    </div>
</div>

<!-- Progress -->
<div class="progress-section">
    <div class="container">
        <div class="progress-steps">
            <?php
            $steps = ['Welcome', 'Database', 'Install', 'Complete'];
            $currentStep = $installationResult === 'success' ? 4 : $step;
            $progressWidth = (($currentStep - 1) / 3) * 100;
            ?>
            <div class="progress-line" style="width: calc(<?php echo $progressWidth; ?>% - 0px)"></div>
            <?php foreach ($steps as $i => $s): ?>
                <div class="step-item <?php echo $i + 1 < $currentStep ? 'completed' : ($i + 1 === $currentStep ? 'active' : ''); ?>">
                    <div class="step-circle">
                        <?php if ($i + 1 < $currentStep): ?>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8.5L6.5 12L13 4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <?php else: ?>
                            <?php echo $i + 1; ?>
                        <?php endif; ?>
                    </div>
                    <div class="step-label"><?php echo $s; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <div class="card">

            <?php if ($installationResult === 'success'): ?>
                <!-- Step 4: Complete -->
                <div class="card-header">
                    <div class="welcome-logo" style="background: linear-gradient(135deg, var(--green) 0%, #16A34A 100%); border-color: var(--green);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 8.5L6.5 12L13 4"/>
                        </svg>
                    </div>
                    <h2>Installation Complete!</h2>
                    <p>The system has been installed successfully.</p>
                </div>

                <div class="credentials-box">
                    <h3>Login Credentials</h3>
                    <div class="credential-item">
                        <span class="label">Admin Email</span>
                        <span class="value">admin@velasquez.com</span>
                    </div>
                    <div class="credential-item">
                        <span class="label">Admin Password</span>
                        <span class="value">admin123</span>
                    </div>
                    <div class="credential-item">
                        <span class="label">Employee Email</span>
                        <span class="value">employee@velasquez.com</span>
                    </div>
                    <div class="credential-item">
                        <span class="label">Employee Password</span>
                        <span class="value">employee123</span>
                    </div>
                    <div class="credential-item">
                        <span class="label">Customer Email</span>
                        <span class="value">maria.santos@gmail.com</span>
                    </div>
                    <div class="credential-item">
                        <span class="label">Customer Password</span>
                        <span class="value">customer123</span>
                    </div>
                </div>

                <div style="text-align: center;">
                    <a href="home.php" class="btn btn-primary btn-lg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Go to Homepage
                    </a>
                </div>

            <?php elseif ($step === 4): ?>
                <!-- Step 4: Already Installed -->
                <div class="card-header">
                    <div class="welcome-logo">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <h2>System Already Installed</h2>
                    <p>The Velasquez Life Plan System has already been configured on this server.</p>
                </div>

                <div style="text-align: center;">
                    <a href="home.php" class="btn btn-primary btn-lg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        Go to Homepage
                    </a>
                </div>

            <?php elseif ($step === 2): ?>
                <!-- Step 2: Database Configuration -->
                <div class="card-header">
                    <svg class="icon" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="40" cy="40" r="40" fill="#FEF3C7"/>
                        <ellipse cx="40" cy="32" rx="20" ry="8" stroke="#D4AF37" stroke-width="2" fill="none"/>
                        <path d="M20 32v16c0 4.418 8.954 8 20 8s20-3.582 20-8V32" stroke="#D4AF37" stroke-width="2" fill="none"/>
                        <path d="M20 48v8c0 4.418 8.954 8 20 8s20-3.582 20-8v-8" stroke="#D4AF37" stroke-width="2" fill="none"/>
                    </svg>
                    <h2>Database Configuration</h2>
                    <p>Enter your MySQL database connection details</p>
                </div>

                <?php if ($message && $step === 2): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <?php if ($messageType === 'success'): ?>
                                <path d="M3 8.5L6.5 12L13 4"/>
                            <?php else: ?>
                                <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                            <?php endif; ?>
                        </svg>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="step" value="3">
                    <input type="hidden" name="db_host" value="" id="h_host">
                    <input type="hidden" name="db_user" value="" id="h_user">
                    <input type="hidden" name="db_pass" value="" id="h_pass">
                    <input type="hidden" name="db_name" value="" id="h_name">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Database Host</label>
                            <input type="text" class="form-control" id="db_host" value="localhost" required>
                        </div>
                        <div class="form-group">
                            <label>Database Name</label>
                            <input type="text" class="form-control" id="db_name" value="velasquez_lifeplan" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" id="db_user" value="root" required>
                        </div>
                        <div class="form-group">
                            <label>Password <span class="hint">(leave empty if none)</span></label>
                            <input type="password" class="form-control" id="db_pass" value="">
                        </div>
                    </div>

                    <div class="btn-group">
                        <a href="index.php" class="btn btn-secondary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                            Back
                        </a>
                        <div class="btn-group-right">
                            <button type="button" class="btn btn-secondary" id="testBtn" onclick="testConnection()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Test Connection
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                                Install
                            </button>
                        </div>
                    </div>
                </form>

            <?php elseif ($step === 3 && $installationResult === 'error'): ?>
                <!-- Step 3: Installation Error -->
                <div class="card-header">
                    <div class="welcome-logo" style="background: linear-gradient(135deg, var(--red) 0%, #DC2626 100%); border-color: var(--red);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    </div>
                    <h2>Installation Failed</h2>
                    <p>An error occurred during installation.</p>
                </div>

                <div class="alert alert-error">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <?php echo htmlspecialchars($message); ?>
                </div>

                <div style="text-align: center;">
                    <a href="index.php" class="btn btn-primary btn-lg">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Try Again
                    </a>
                </div>

            <?php else: ?>
                <!-- Step 1: Welcome -->
                <?php if (!empty($configBroken)): ?>
                <div style="background:#FEF3C7;border:1px solid #D97706;border-radius:8px;padding:14px 18px;margin-bottom:20px;text-align:left;">
                    <strong style="color:#92400E;">Existing setup found but the database is unreachable.</strong>
                    <p style="margin:6px 0 0;color:#92400E;font-size:0.9rem;">This usually means the folder was copied to a new PC. Make sure MySQL is running in XAMPP, then continue below to set up the database on this computer.</p>
                </div>
                <?php endif; ?>
                <div class="welcome-logo">
                    <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="30" cy="30" r="28" fill="#1A2634" stroke="#CCB877" stroke-width="2"/>
                        <text x="30" y="36" text-anchor="middle" fill="#CCB877" font-family="Inter, sans-serif" font-size="16" font-weight="700">CSV</text>
                    </svg>
                </div>

                <div class="card-header" style="margin-bottom: 0;">
                    <h2 style="margin-bottom: 4px;">Velasquez Funeral Service</h2>
                    <p style="font-size: 1.1rem; color: var(--gray-600); margin-bottom: 4px;">Life Plan System</p>
                    <p style="color: var(--gold); font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; font-size: 0.8rem;">Browser-Based Installer</p>
                </div>

                <ul class="requirements-list">
                    <li class="<?php echo version_compare(PHP_VERSION, '7.4.0', '>=') ? 'pass' : 'fail'; ?>">
                        <div class="req-info">
                            <div class="req-icon">
                                <?php if (version_compare(PHP_VERSION, '7.4.0', '>=')): ?>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8.5L6.5 12L13 4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <?php else: ?>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><line x1="4" y1="4" x2="12" y2="12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><line x1="12" y1="4" x2="4" y2="12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="req-name">PHP Version</div>
                                <div class="req-detail">Required: 7.4+ &mdash; Found: <?php echo PHP_VERSION; ?></div>
                            </div>
                        </div>
                        <span class="req-status"><?php echo version_compare(PHP_VERSION, '7.4.0', '>=') ? 'Passed' : 'Failed'; ?></span>
                    </li>
                    <li class="<?php echo extension_loaded('pdo_mysql') ? 'pass' : 'fail'; ?>">
                        <div class="req-info">
                            <div class="req-icon">
                                <?php if (extension_loaded('pdo_mysql')): ?>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8.5L6.5 12L13 4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <?php else: ?>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><line x1="4" y1="4" x2="12" y2="12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><line x1="12" y1="4" x2="4" y2="12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="req-name">PDO MySQL Extension</div>
                                <div class="req-detail">Required for database connectivity</div>
                            </div>
                        </div>
                        <span class="req-status"><?php echo extension_loaded('pdo_mysql') ? 'Passed' : 'Failed'; ?></span>
                    </li>
                    <li class="<?php echo extension_loaded('pdo') ? 'pass' : 'fail'; ?>">
                        <div class="req-info">
                            <div class="req-icon">
                                <?php if (extension_loaded('pdo')): ?>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8.5L6.5 12L13 4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <?php else: ?>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><line x1="4" y1="4" x2="12" y2="12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><line x1="12" y1="4" x2="4" y2="12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="req-name">PDO Extension</div>
                                <div class="req-detail">PHP Data Objects for database abstraction</div>
                            </div>
                        </div>
                        <span class="req-status"><?php echo extension_loaded('pdo') ? 'Passed' : 'Failed'; ?></span>
                    </li>
                </ul>

                <?php
                $allPassed = version_compare(PHP_VERSION, '7.4.0', '>=') && extension_loaded('pdo_mysql') && extension_loaded('pdo');
                ?>

                <div style="text-align: center; margin-top: 32px;">
                    <form method="POST" action="">
                        <input type="hidden" name="step" value="2">
                        <button type="submit" class="btn btn-primary btn-lg" <?php if (!$allPassed) echo 'disabled style="opacity:0.5;cursor:not-allowed;"'; ?>>
                            Continue
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </button>
                    </form>
                    <?php if (!$allPassed): ?>
                        <p style="margin-top: 12px; color: var(--red); font-size: 0.85rem;">Please resolve the failed requirements before continuing.</p>
                    <?php endif; ?>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Footer -->
<div class="installer-footer">
    <div class="container">
        &copy; <?php echo date('Y'); ?> Velasquez Funeral Service. All rights reserved.
    </div>
</div>

<script>
function testConnection() {
    const btn = document.getElementById('testBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner"></span> Testing...';
    btn.disabled = true;

    fetch('test_connection.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            host: document.getElementById('db_host').value,
            user: document.getElementById('db_user').value,
            pass: document.getElementById('db_pass').value,
            name: document.getElementById('db_name').value
        })
    })
    .then(r => r.json())
    .then(data => {
        const existing = document.querySelector('.alert');
        if (existing) existing.remove();

        const alert = document.createElement('div');
        alert.className = 'alert ' + (data.success ? 'alert-success' : 'alert-error');
        alert.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${data.success ? '<path d="M3 8.5L6.5 12L13 4"/>' : '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>'}</svg>` + data.message;

        const form = btn.closest('form');
        form.insertBefore(alert, form.firstChild);
    })
    .catch(() => {
        alert('Connection test failed. Please check your settings.');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>

</body>
</html>
