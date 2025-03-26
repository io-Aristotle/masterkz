<?php
session_start();
require 'db.php'; // Подключение к базе данных

// Если пользователь не авторизован
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Получаем данные пользователя
$stmt = $conn->prepare("SELECT username, email, city, specialty, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($username, $email, $city, $specialty, $phone);
$stmt->fetch();
$stmt->close();

// Обновление номера телефона
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['phone'])) {
    $newPhone = trim($_POST['phone']);
    $stmt = $conn->prepare("UPDATE users SET phone = ? WHERE id = ?");
    $stmt->bind_param("si", $newPhone, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
    $phone = $newPhone;
    $message = "Номер телефона обновлён.";
}

// Обновление города
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['city'])) {
    $newCity = trim($_POST['city']);
    $stmt = $conn->prepare("UPDATE users SET city = ? WHERE id = ?");
    $stmt->bind_param("si", $newCity, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
    $city = $newCity;
    $message = "Город обновлён.";
}

// Список городов
$cities = [
    "Абайская область", "Акмолинская область", "Актюбинская область", "Алматинская область", "Атырауская область",
    "Западно-Казахстанская область", "Жамбылская область", "Жетісу область", "Карагандинская область",
    "Костанайская область", "Кызылординская область", "Мангистауская область", "Павлодарская область",
    "Северо-Казахстанская область", "Туркестанская область", "Ұлытауская область", "Восточно-Казахстанская область",
    "город Астана", "город Алматы", "город Шымкент"
];
?>

<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        main {
            margin: 20px;
            width: auto;
        }
        /* Убираем автоцентрирование форм */
        form {
            margin: 10px 0;
            max-width: 500px;
            padding: 0; /* убираем фон формы */
            box-shadow: none; /* убираем тень */
            background: none; /* убираем белый фон */
        }
        form label, form select, form input, form button {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 5px;
        }
        .profile-section {
            margin-bottom: 30px;
        }
        .my-ads {
            margin-top: 30px;
        }
        .my-ad {
            background: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .my-ad a {
            margin-right: 10px;
            color: #2e7d32;
            text-decoration: none;
        }
        .my-ad a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<main>
    <h2>Профиль пользователя</h2>
    <?php if(isset($message)) echo "<p style='color:green;'>$message</p>"; ?>

    <div class="profile-section">
        <p><strong>Имя:</strong> <?php echo htmlspecialchars($username); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <p><strong>Город:</strong> <?php echo htmlspecialchars($city); ?></p>
        <form method="post">
            <label for="city">Изменить город:</label>
            <select name="city" id="city">
                <?php foreach ($cities as $cityOption): ?>
                    <option value="<?php echo $cityOption; ?>" <?php if ($cityOption == $city) echo 'selected'; ?>>
                        <?php echo $cityOption; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Обновить город</button>
        </form>

        <p><strong>Специальность:</strong> <?php echo htmlspecialchars($specialty); ?></p>
        <p><strong>Номер телефона:</strong> <?php echo htmlspecialchars($phone); ?></p>
        <form method="post">
            <label for="phone">Изменить номер телефона:</label>
            <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>">
            <button type="submit">Обновить номер</button>
        </form>

        <form action="logout.php" method="post">
            <button type="submit" class="logout-button">Выйти</button>
        </form>
    </div>

    <!-- Блок "Мои объявления" -->
    <div class="my-ads">
        <h3>Мои объявления</h3>
        <?php
        // Выбираем все объявления текущего пользователя
        $stmt = $conn->prepare("SELECT id, title, price FROM ads WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $adsResult = $stmt->get_result();
        $stmt->close();

        if ($adsResult->num_rows > 0) {
            while ($ad = $adsResult->fetch_assoc()) {
                ?>
                <div class="my-ad">
                    <p><strong><?php echo htmlspecialchars($ad['title']); ?></strong></p>
                    <p>Цена: <?php echo htmlspecialchars($ad['price']); ?> тг</p>
                    <a href="edit_ad.php?id=<?php echo $ad['id']; ?>">Изменить</a>
                    <a href="delete_ad.php?id=<?php echo $ad['id']; ?>" onclick="return confirm('Удалить объявление?');">Удалить</a>
                </div>
                <?php
            }
        } else {
            echo "<p>Нет объявлений.</p>";
        }
        ?>
    </div>
</main>
</body>
</html>


