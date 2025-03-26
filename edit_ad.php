<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$ad_id = intval($_GET['id'] ?? 0);

// Проверяем, принадлежит ли объявление пользователю
$stmt = $conn->prepare("SELECT user_id, title, description, price, city FROM ads WHERE id = ?");
$stmt->bind_param("i", $ad_id);
$stmt->execute();
$result = $stmt->get_result();
$ad = $result->fetch_assoc();
$stmt->close();

if (!$ad || $ad['user_id'] != $_SESSION['user_id']) {
    echo "<p style='color:red;'>Ошибка: Нет доступа к объявлению.</p>";
    exit;
}

// Список городов (как в profile.php)
$cities = [
    "Абайская область", "Акмолинская область", "Актюбинская область", "Алматинская область", "Атырауская область",
    "Западно-Казахстанская область", "Жамбылская область", "Жетісу область", "Карагандинская область",
    "Костанайская область", "Кызылординская область", "Мангистауская область", "Павлодарская область",
    "Северо-Казахстанская область", "Туркестанская область", "Ұлытауская область", "Восточно-Казахстанская область",
    "город Астана", "город Алматы", "город Шымкент"
];

// Если отправили форму — обновляем объявление
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newTitle = trim($_POST['title']);
    $newDescription = trim($_POST['description']);
    $newPrice = floatval($_POST['price']);
    $newCity = trim($_POST['city']);

    $stmt = $conn->prepare("UPDATE ads SET title = ?, description = ?, price = ?, city = ? WHERE id = ?");
    $stmt->bind_param("ssdsi", $newTitle, $newDescription, $newPrice, $newCity, $ad_id);
    if ($stmt->execute()) {
        echo "<p style='color:green;'>Объявление обновлено!</p>";
        // Обновляем переменные, чтобы увидеть изменения сразу
        $ad['title'] = $newTitle;
        $ad['description'] = $newDescription;
        $ad['price'] = $newPrice;
        $ad['city'] = $newCity;
    } else {
        echo "<p style='color:red;'>Ошибка при обновлении!</p>";
    }
    $stmt->close();
}
?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Изменить объявление</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h2>Изменить объявление</h2>
<form method="post">
    <label>Заголовок:</label><br>
    <input type="text" name="title" value="<?php echo htmlspecialchars($ad['title']); ?>" required><br><br>

    <label>Описание:</label><br>
    <textarea name="description" rows="5" cols="40" required><?php echo htmlspecialchars($ad['description']); ?></textarea><br><br>

    <label>Цена (тг):</label><br>
    <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($ad['price']); ?>" required><br><br>

    <label>Город:</label><br>
    <select name="city">
        <?php foreach ($cities as $cityOption): ?>
            <option value="<?php echo $cityOption; ?>"
                <?php if ($cityOption == $ad['city']) echo 'selected'; ?>>
                <?php echo $cityOption; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <button type="submit">Сохранить</button>
</form>

<a href="profile.php">Вернуться в профиль</a>
</body>
</html>
