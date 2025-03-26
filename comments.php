<?php
include 'db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$ad_id = intval($_GET['id'] ?? 0);

// Обработка формы (если отправлен комментарий)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comment"])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<p style='color: red;'>Вы должны войти, чтобы оставить комментарий.</p>";
    } else {
        $user_id = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $comment = trim($_POST['comment']);
        $rating = intval($_POST['rating']);

        if (!empty($comment) && $rating >= 1 && $rating <= 5) {
            $stmt = $conn->prepare("INSERT INTO comments (ad_id, user_id, username, text, rating) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iissi", $ad_id, $user_id, $username, $comment, $rating);
            $stmt->execute();
            echo "<p style='color: green;'>Комментарий добавлен!</p>";
        } else {
            echo "<p style='color: red;'>Введите комментарий и выберите рейтинг.</p>";
        }
    }
}

// Получаем комментарии
$stmt = $conn->prepare("SELECT * FROM comments WHERE ad_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $ad_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Форма комментария -->
<form method="post">
    <textarea name="comment" placeholder="Оставьте отзыв..." required></textarea>
    <br>
    <label>Оценка:</label>
    <select name="rating" required>
        <option value="5">⭐⭐⭐⭐⭐ (5)</option>
        <option value="4">⭐⭐⭐⭐ (4)</option>
        <option value="3">⭐⭐⭐ (3)</option>
        <option value="2">⭐⭐ (2)</option>
        <option value="1">⭐ (1)</option>
    </select>
    <br>
    <button type="submit">Оставить отзыв</button>
</form>

<hr>

<!-- Вывод комментариев -->
<?php while ($row = $result->fetch_assoc()): ?>
    <p><strong><?php echo htmlspecialchars($row['username']); ?>:</strong> <?php echo htmlspecialchars($row['text']); ?></p>
    <p>Оценка: <?php echo str_repeat("⭐", $row['rating']); ?></p>
    <hr>
<?php endwhile; ?>


