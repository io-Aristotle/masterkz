<?php
include 'db.php';
session_start();

$contactId = intval($_GET['contact_id'] ?? 0);
$lastId = intval($_GET['last_id'] ?? 0);
$selfId = $_SESSION['user_id'] ?? 0;

if (!$contactId || !$selfId) {
    exit(json_encode(["error" => "Контакт не выбран"], JSON_UNESCAPED_UNICODE));
}

$stmt = $conn->prepare("SELECT id, sender_id, message FROM messages 
    WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
      AND id > ?
    ORDER BY created_at ASC");
$stmt->bind_param("iiiii", $selfId, $contactId, $contactId, $selfId, $lastId);
$stmt->execute();
$result = $stmt->get_result();

$newMessagesHtml = "";
$lastMessageId = $lastId;

while ($msg = $result->fetch_assoc()) {
    $class = ($msg['sender_id'] == $selfId) ? 'sent' : 'received';
    $newMessagesHtml .= '<div class="message ' . $class . '" data-id="' . $msg['id'] . '">
        <p>' . htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8') . '</p>
    </div>';
    $lastMessageId = $msg['id'];
}

$stmt->close();

header('Content-Type: application/json');
echo json_encode(["html" => $newMessagesHtml, "last_id" => $lastMessageId], JSON_UNESCAPED_UNICODE);
?>
