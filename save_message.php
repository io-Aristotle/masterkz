<?php
session_start();
include 'db.php';

header("Content-Type: application/json");

// Читаем данные из POST-запроса
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $data['sender']) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$sender = intval($data['sender']);
$receiver = intval($data['receiver']);
$message = trim($data['message']);

if ($message === "") {
    echo json_encode(["status" => "error", "message" => "Empty message"]);
    exit;
}

// Сохраняем сообщение в базу. Предположим, что таблица messages имеет следующие столбцы: 
// id (AUTO_INCREMENT), sender_id, receiver_id, message, created_at
$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $sender, $receiver, $message);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Message saved"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to save message: " . $conn->error]);
}

$stmt->close();
?>
