<?php
/**
 * Simple REST API Router
 * Place this file in src/api/index.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
function getDatabase() {
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
    
    return new PDO($dsn, $username, $password, $options);
}

// Simple routing
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/api/', '', $path);
$method = $_SERVER['REQUEST_METHOD'];

// Remove trailing slash
$path = rtrim($path, '/');

try {
    switch ($path) {
        case 'health':
            if ($method === 'GET') {
                echo json_encode([
                    'status' => 'healthy',
                    'timestamp' => date('c'),
                    'version' => '1.0.0'
                ]);
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;
            
        case 'users':
            $pdo = getDatabase();
            
            if ($method === 'GET') {
                // Create users table if it doesn't exist
                $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL UNIQUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
                
                $stmt = $pdo->query('SELECT * FROM users ORDER BY created_at DESC');
                $users = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'data' => $users,
                    'count' => count($users)
                ]);
                
            } elseif ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['name']) || !isset($input['email'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Name and email are required']);
                    break;
                }
                
                $stmt = $pdo->prepare('INSERT INTO users (name, email) VALUES (?, ?)');
                $stmt->execute([$input['name'], $input['email']]);
                
                $id = $pdo->lastInsertId();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'User created successfully',
                    'data' => ['id' => $id]
                ]);
                
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;
            
        case '':
        case 'index.php':
            echo json_encode([
                'message' => 'Welcome to the API',
                'endpoints' => [
                    'GET /api/health' => 'Health check',
                    'GET /api/users' => 'List all users',
                    'POST /api/users' => 'Create a new user'
                ],
                'version' => '1.0.0'
            ]);
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'error' => 'Endpoint not found',
                'path' => $path
            ]);
            break;
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}
?>