<?php
include 'header.php';
include 'db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>Ошибка: Вы должны войти.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем список пользователей, с которыми были чаты
$stmt = $conn->prepare("
    SELECT DISTINCT u.id, u.username 
    FROM users u 
    JOIN messages m ON u.id = m.sender_id OR u.id = m.receiver_id 
    WHERE (m.sender_id = ? OR m.receiver_id = ?) AND u.id != ?
");
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Ваши чаты</h2>
<ul>
    <?php while ($chat = $result->fetch_assoc()): ?>
        <li>
            <a href="chat.php?user_id=<?= $chat['id'] ?>">
                <?= htmlspecialchars($chat['username']) ?>
            </a>
        </li>
    <?php endwhile; ?>
</ul>

<?php include 'footer.php'; ?>

