<?php
// Sample PHP file to test the setup
header('Content-Type: text/html; charset=utf-8');

echo "<h1>PHP MySQL Development Environment</h1>";

// Test PHP extensions
echo "<h2>PHP Extensions Status:</h2>";
echo "<ul>";
echo "<li>PDO: " . (extension_loaded('pdo') ? '✅ Enabled' : '❌ Disabled') . "</li>";
echo "<li>PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Enabled' : '❌ Disabled') . "</li>";
echo "<li>MySQLi: " . (extension_loaded('mysqli') ? '✅ Enabled' : '❌ Disabled') . "</li>";
echo "<li>mbstring: " . (extension_loaded('mbstring') ? '✅ Enabled' : '❌ Disabled') . "</li>";
echo "<li>JSON: " . (extension_loaded('json') ? '✅ Enabled' : '❌ Disabled') . "</li>";
echo "<li>GD: " . (extension_loaded('gd') ? '✅ Enabled' : '❌ Disabled') . "</li>";
echo "</ul>";

// Test database connection
echo "<h2>Database Connection Test:</h2>";

try {
    $host = $_ENV['DB_HOST'] ?? 'mysql';
    $dbname = $_ENV['DB_NAME'] ?? 'dev_db';
    $username = $_ENV['DB_USER'] ?? 'dev_user';
    $password = $_ENV['DB_PASSWORD'] ?? 'dev_password';
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Test query
    $stmt = $pdo->query('SELECT VERSION() as version');
    $result = $stmt->fetch();
    
    echo "<p>✅ <strong>Database Connected Successfully!</strong></p>";
    echo "<p>MySQL Version: " . $result['version'] . "</p>";
    
    // Test table creation
    $pdo->exec("CREATE TABLE IF NOT EXISTS test_table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    echo "<p>✅ Test table created/verified successfully!</p>";
    
} catch (PDOException $e) {
    echo "<p>❌ <strong>Database Connection Failed:</strong> " . $e->getMessage() . "</p>";
}

// Test Composer
echo "<h2>Composer Status:</h2>";
$composerPath = '/usr/bin/composer';
if (file_exists($composerPath)) {
    echo "<p>✅ <strong>Composer is installed</strong></p>";
    echo "<p>Path: $composerPath</p>";
} else {
    echo "<p>❌ <strong>Composer not found</strong></p>";
}

// Environment info
echo "<h2>Environment Information:</h2>";
echo "<ul>";
echo "<li>PHP Version: " . PHP_VERSION . "</li>";
echo "<li>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</li>";
echo "<li>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "<li>Current Time: " . date('Y-m-d H:i:s') . "</li>";
echo "</ul>";

// API endpoint example
echo "<h2>Sample API Endpoints:</h2>";
echo "<ul>";
echo "<li><a href='/api/users' target='_blank'>GET /api/users</a> - List users</li>";
echo "<li><a href='/api/health' target='_blank'>GET /api/health</a> - Health check</li>";
echo "</ul>";
echo "<p><em>Create an 'api' folder with an index.php file to handle these routes.</em></p>";
?>