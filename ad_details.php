<?php
include 'header.php';
include 'db.php';

// Если сессия не запущена
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Проверяем, передан ли ID объявления
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p style='color: red;'>Ошибка: Некорректный ID объявления.</p>";
    exit;
}

$ad_id = intval($_GET['id']);

// Получаем объявление с пользователем
$query = $conn->prepare("
    SELECT ads.*, users.username, users.phone 
    FROM ads 
    JOIN users ON ads.user_id = users.id 
    WHERE ads.id = ?
");
$query->bind_param("i", $ad_id);
$query->execute();
$result = $query->get_result();
$ad = $result->fetch_assoc() ?? null;

// Если объявления нет в базе
if (!$ad) {
    echo "<p style='color: red;'>Ошибка: Объявление не найдено.</p>";
    exit;
}
?>

<style>
/* Общие стили */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background-color: #e8f5e9;
    color: #333;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2, h3 {
    color: #2e7d32;
}

p {
    font-size: 16px;
    line-height: 1.5;
}

/* Красивая кнопка "Заказать" */
.btn-success {
    display: inline-block;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: bold;
    color: #fff;
    background: linear-gradient(135deg, #28a745, #218838);
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    text-align: center;
}

.btn-success:hover {
    background: linear-gradient(135deg, #218838, #1e7e34);
    transform: scale(1.05);
}

.btn-success:active {
    transform: scale(0.98);
}

/* Кнопка "Чат" */
.btn-chat {
    display: inline-block;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: bold;
    color: #fff;
    background: linear-gradient(135deg, #28a745, #218838);
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    text-align: center;
}

.btn-chat:hover {
    background: linear-gradient(135deg, #218838, #1e7e34);
    transform: scale(1.05);
}

.btn-chat:active {
    transform: scale(0.98);
}
</style>

<div class="container">
    <h2><?php echo htmlspecialchars($ad['title']); ?></h2>
    <p><strong>Цена:</strong> <?php echo number_format($ad['price'], 2); ?> тг</p>
    <p><?php echo nl2br(htmlspecialchars($ad['description'])); ?></p>
    
    <h3>Информация о продавце</h3>
    <p><strong>Имя:</strong> <?php echo htmlspecialchars($ad['username']); ?></p>
    
    <?php if (!empty($ad['phone'])): ?>
        <p><strong>phone:</strong> 
            <a href="https://wa.me/<?php echo htmlspecialchars($ad['phone']); ?>" target="_blank">
                <?php echo htmlspecialchars($ad['phone']); ?>
            </a>
        </p>
    <?php else: ?>
        <p><strong>phone:</strong> Не указан</p>
    <?php endif; ?>
    
    <!-- Кнопка "Заказать" -->
    <a href="order.php?ad_id=<?php echo $ad_id; ?>" class="btn-success">Заказать</a>

    <!-- Кнопка "Чат" (передаём contact_id = владелец объявления) -->
    <a href="chat.php?contact_id=<?php echo $ad['user_id']; ?>" class="btn-chat">Чат</a>

    <h3>Комментарии</h3>
    <?php include 'comments.php'; ?>
</div>

<?php include 'footer.php'; ?>



