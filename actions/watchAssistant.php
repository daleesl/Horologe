<?php
/* ---------------- ERRORS FOR DEBUGGING ---------------- */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ---------------- DATABASE CONFIG ---------------- */
$conn = new mysqli("127.0.0.1", "root", "", "horologe");
if ($conn->connect_error) {
    echo json_encode(['ok' => false, 'reply' => 'Database connection failed']);
    exit;
}

/* ---------------- CONFIG ---------------- */
$NO_DATA_RESPONSE = "I do not have enough information to answer that.";
$OLLAMA_API_URL = 'http://127.0.0.1:11434/api/chat';
$MODEL = 'gemma3:270m';
$OLLAMA_TIMEOUT = 30;

/* ---------------- HANDLE CHAT ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $userMsg = trim($_POST['prompt'] ?? $_POST['message'] ?? '');
    if ($userMsg === '') {
        echo json_encode(['ok' => false, 'reply' => 'Empty message']);
        exit;
    }

    /* ---------- NORMALIZE INPUT ---------- */
    $msgNorm = strtolower($userMsg);
    $msgNorm = preg_replace('/[^a-z0-9\s]/', '', $msgNorm);
    $msgNorm = trim(preg_replace('/\s+/', ' ', $msgNorm));

    $searchMode = null;
    $searchValue = null;
    $matchedModels = [];

    /* ---------- 1. BRAND LIST INTENT ---------- */
    if (preg_match('/\bbrand(s)?\b|\bbrand list\b|\bbrands in horologe\b|\bwhat are the brands\b/', $msgNorm)) {
        $searchMode = 'brand_list';
    }

    /* ---------- 2. SPECIFIC BRAND DETECTION ---------- */
    if ($searchMode === null) {
        $brands = [];
        $res = $conn->query("SELECT DISTINCT brand FROM watch");
        while ($row = $res->fetch_assoc()) {
            $brands[] = strtolower($row['brand']);
        }

        foreach ($brands as $brand) {
            if (strpos($msgNorm, $brand) !== false) {
                $searchMode = 'brand';
                $searchValue = $brand;
                break;
            }
        }
    }

    /* ---------- 3. SEARCH MODELS BY ANY MATCHING KEYWORD ---------- */
    if ($searchMode === null) {
        $userInput = trim($_POST['prompt'] ?? $_POST['message'] ?? '');
        $matchedModels = [];

        // 1. Fetch all models from DB
        $res = $conn->query("SELECT model FROM watch");
        while ($row = $res->fetch_assoc()) {
            $modelName = $row['model'];
            // If the user input contains at least part of the model name, consider it a match
            similar_text(strtolower($userInput), strtolower($modelName), $percent);
            if ($percent > 50) { // Adjust threshold as needed
                $matchedModels[] = $modelName;
            }
        }

        // 2. If no match found, do a LIKE search
        if (empty($matchedModels)) {
            $stmt = $conn->prepare("
            SELECT model 
            FROM watch 
            WHERE LOWER(model) LIKE CONCAT('%', ?, '%') 
            ORDER BY model
        ");
            $searchTerm = strtolower($userInput);
            $stmt->bind_param("s", $searchTerm);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $matchedModels[] = $row['model'];
            }
            $stmt->close();
        }

        if (!empty($matchedModels)) {
            $searchMode = 'model_keyword';
        }
    }

    /* ---------- 4. DATABASE RESPONSE ---------- */
    $dbOutput = '';

    /* ---- BRANDS LIST ---- */
    if ($searchMode === 'brand_list') {
        $stmt = $conn->query("SELECT DISTINCT brand FROM watch ORDER BY brand");
        $brandsList = [];
        while ($r = $stmt->fetch_assoc()) {
            $brandsList[] = ucwords(strtolower($r['brand']));
        }
        if (!empty($brandsList)) {
            $dbOutput = "The brands in Horologe are: " . implode(", ", $brandsList) . ".";
        }
    }

    /* ---- MODELS UNDER BRAND ---- */ elseif ($searchMode === 'brand') {
        $stmt = $conn->prepare("SELECT model FROM watch WHERE LOWER(brand)=? ORDER BY model");
        $stmt->bind_param("s", $searchValue);
        $stmt->execute();
        $res = $stmt->get_result();

        $models = [];
        while ($row = $res->fetch_assoc()) {
            $models[] = $row['model'];
        }
        $stmt->close();

        if ($models) {
            $dbOutput = "The models in " . ucfirst($searchValue) . " are:\n";
            foreach ($models as $i => $m) {
                $dbOutput .= ($i + 1) . ". $m\n";
            }
        }
    }

    /* ---- MODELS MATCHING KEYWORD ---- */ elseif ($searchMode === 'model_keyword') {
        if (count($matchedModels) === 1) {
            $modelName = $matchedModels[0];

            // Fetch description from DB
            $stmt = $conn->prepare("SELECT description FROM watch WHERE model=?");
            $stmt->bind_param("s", $modelName);
            $stmt->execute();
            $res = $stmt->get_result();
            $desc = $res->fetch_assoc()['description'] ?? '';
            $stmt->close();

            if ($desc !== '') {
                // Build dbOutput to include model + description for Ollama
                $dbOutput = "Model: {$modelName}\nDescription: {$desc}";
            } else {
                $dbOutput = "Model: {$modelName}";
            }
        } else {
            // Multiple matches: list all
            $dbOutput = "Multiple models match your query:\n";
            foreach ($matchedModels as $i => $m) {
                $dbOutput .= ($i + 1) . ". $m\n";
            }
        }
    }




    /* ---------- 5. FALLBACK ---------- */
    if (trim($dbOutput) === '') {
        echo json_encode(['ok' => true, 'reply' => $NO_DATA_RESPONSE]);
        exit;
    }

    /* ---------- 6. SYSTEM PROMPT ---------- */
    $systemPrompt = [
        'role' => 'system',
        'content' =>
            "You are a luxury watch assistant. Answer ONLY using the data below.\n" .
            "RULES:\n" .
            "- Use ONLY the data provided.\n" .
            "- Paraphrase or summarize descriptions if asked.\n" .
            "- Do NOT add any information not in the data.\n" .
            "- Always provide a helpful, natural response.\n\n" .
            "DATA:\n$dbOutput"
    ];


    /* ---------- 7. CALL OLLAMA ---------- */
    $payload = json_encode([
        'model' => $MODEL,
        'messages' => [
                $systemPrompt,
                ['role' => 'user', 'content' => $userMsg]
            ]
    ]);

    $ch = curl_init($OLLAMA_API_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => $OLLAMA_TIMEOUT
    ]);

    $result = curl_exec($ch);
    curl_close($ch);

    $aiReply = '';
    if ($result) {
        foreach (explode("\n", $result) as $line) {
            $json = json_decode($line, true);
            if (isset($json['message']['content'])) {
                $aiReply .= $json['message']['content'];
            }
        }
    }

    echo json_encode(['ok' => true, 'reply' => trim($aiReply)]);
    exit;
}

$conn->close();
