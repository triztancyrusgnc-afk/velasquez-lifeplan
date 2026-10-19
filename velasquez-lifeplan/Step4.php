<?php
echo "<h2>🔄 Step 4: Connect the System & Finish</h2>";
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

    // Guard: Steps 1-3 must run first
    $required = ['users', 'plans', 'subscriptions', 'beneficiaries', 'logistics_inventory', 'contact_inquiries'];
    foreach ($required as $table) {
        try {
            $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
        } catch (PDOException $e) {
            echo "⚠️ The '$table' table doesn't exist yet.\n";
            echo "➡️ Please run Step1.php → Step2.php → Step3.php first, then come back here.";
            echo "</pre>";
            echo "<br><a href='Step1.php' style='color:#e28743;'>⬅️ Back to Step 1</a>";
            exit;
        }
    }
    echo "✅ All required tables found\n";

    // Write config.php so the whole system can connect.
    // Lazy version: throws instead of dying, so pages can show the
    // friendly "run Step 1" notice. Nothing connects until getDB() runs.
    $config = <<<'PHP'
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'velasquez_lifeplan');
define('APP_NAME', 'Velasquez Funeral Service');
define('APP_VERSION', '1.0.0');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }
    return $pdo;
}

PHP;
    file_put_contents(__DIR__ . '/config.php', $config);
    echo "✅ config.php written — system is now connected\n";

    // Verify the seed data
    $users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $plans = $pdo->query("SELECT COUNT(*) FROM plans")->fetchColumn();
    $subs = $pdo->query("SELECT COUNT(*) FROM subscriptions")->fetchColumn();
    echo "✅ Verified: $users users, $plans life plans, $subs subscription(s)\n";

    // Test that logins will work
    $admin = $pdo->query("SELECT password FROM users WHERE email = 'admin@velasquez.com'")->fetchColumn();
    echo (password_verify('admin123', $admin) ? "✅ Admin login tested OK\n" : "⚠️ Admin password check FAILED — rerun Step1.php\n");

    echo "\n🎉 ALL STEPS COMPLETE! The system is ready.\n";
    echo "\nDemo accounts:\n";
    echo "  Admin    → admin@velasquez.com / admin123\n";
    echo "  Employee → employee@velasquez.com / employee123\n";
    echo "  Customer → maria.santos@gmail.com / customer123\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
    echo "\n\n⚠️ Make sure MySQL is running and Steps 1-3 were completed.";
}

echo "</pre>";
echo "<br><a href='home.php' style='color:#4ade80;font-weight:bold;font-size:1.1rem;'>🏠 Go to Homepage</a>";
?>
