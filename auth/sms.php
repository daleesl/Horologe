<?php
function sendSMS($number, $message) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://semaphore.co/api/v4/messages");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'apikey' => '02c7ba125f75b00d6c08ec0a1b33bb56', // <-- replace with your Semaphore API key
        'number' => $number,
        'message' => $message
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
?>
