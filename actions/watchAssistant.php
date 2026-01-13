<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prompt'])) {

    $user_input = trim($_POST['prompt']);
    $reply = '';

    // Database connection
    $host = 'localhost';
    $db = 'horologe';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Keyword search
        $stmt = $pdo->prepare(
            "SELECT * FROM watch 
             WHERE brand LIKE :keyword 
                OR model LIKE :keyword"
        );
        $stmt->execute(['keyword' => "%$user_input%"]);
        $watches = $stmt->fetchAll();

        if ($watches) {
            foreach ($watches as $w) {
                $reply .= "Brand: {$w['brand']}\n";
                $reply .= "Model: {$w['model']}\n";
                $reply .= "Price: PHP " . number_format($w['price'], 2) . "\n";
                $reply .= "Stock: {$w['stock_quantity']}\n";
                $reply .= "Description: {$w['description']}\n\n";
            }
        } else {
            // Build AI context from DB
            $stmt_all = $pdo->query(
                "SELECT brand, model, price, description FROM watch"
            );

            $data_text = '';
            foreach ($stmt_all as $w) {
                $data_text .= "Brand: {$w['brand']}, ";
                $data_text .= "Model: {$w['model']}, ";
                $data_text .= "Price: PHP " . number_format($w['price'], 2) . ", ";
                $data_text .= "Description: {$w['description']}\n";
            }

            $prompt = <<<EOT
You are an assistant that must answer questions using ONLY the data below.
If the answer is not present, say:
"No data specified about the topic."

--- DATA ---
$data_text
--- END DATA ---

Question: $user_input
Answer:
EOT;

            $payload = json_encode([
                'model' => 'qwen3:0.6b',
                'prompt' => $prompt,
                'stream' => false
            ]);

            $ch = curl_init('http://127.0.0.1:11434/api/generate');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);
            $reply = $data['response'] ?? '(no reply)';
        }

    } catch (PDOException $e) {
        $reply = "Database error: " . $e->getMessage();
    }

    echo nl2br(htmlspecialchars($reply));
    exit;
}
?>
