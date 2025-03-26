<?php
include 'db.php';

session_start();
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($new_messages);
$stmt->fetch();
$stmt->close();

echo json_encode(["new_messages" => $new_messages]);
?>
