<?php
session_start();
require 'db.php';

// Если пользователь не авторизован, перенаправляем
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Обработка формы добавления объявления
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $city = trim($_POST['city']);
    $service = trim($_POST['service']);
    
    if ($title && $description && $price && $city && $service) {
        $stmt = $conn->prepare("INSERT INTO ads (user_id, title, description, price, city, service) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdss", $_SESSION['user_id'], $title, $description, $price, $city, $service);
        if ($stmt->execute()) {
            $message = "Объявление успешно добавлено!";
        } else {
            $message = "Ошибка добавления объявления: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "Заполните все поля.";
    }
}

// Список городов
$cities = [
    "Абайская область", "Акмолинская область", "Актюбинская область", "Алматинская область", "Атырауская область",
    "Западно-Казахстанская область", "Жамбылская область", "Жетісу область", "Карагандинская область",
    "Костанайская область", "Кызылординская область", "Мангистауская область", "Павлодарская область",
    "Северо-Казахстанская область", "Туркестанская область", "Ұлытауская область", "Восточно-Казахстанская область",
    "город Астана", "город Алматы", "город Шымкент"
];

// Список услуг (строительных работ)
$services = [
    "Общестроительные работы",
    "Кладка кирпича и блоков",
    "Монтаж стен и перегородок",
    "Монолитные работы (фундамент, плиты, колонны)",
    "Кровельные работы (черепица, металлочерепица, мягкая кровля)",
    "Фасадные работы (утепление, облицовка, штукатурка)",
    "Отделочные работы (штукатурка, покраска, оклейка обоями, укладка плитки)",
    "Инженерные коммуникации (электромонтажные, сантехнические, отопление, вентиляция)",
    "Столярные и мебельные работы",
    "Работы с дымоходами",
    "Ландшафтные работы"
];
?>

<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить объявление</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        form label {
            display: block;
            margin: 1px 0 5px;
        }
        form input, form textarea, form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            background-color: #388e3c;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #2e7d32;
        }
    </style>
</head>
<body>
<main>
    <h2>Добавить объявление</h2>
    <?php if(isset($message)) echo "<p style='color:green;'>$message</p>"; ?>
    <form method="post" action="add_ad.php">
        <label for="title">Заголовок:</label>
        <input type="text" name="title" id="title" required>
        
        <label for="description">Описание:</label>
        <textarea name="description" id="description" rows="5" required></textarea>
        
        <label for="price">Цена (тг):</label>
        <input type="number" name="price" id="price" step="0.01" required>
        
        <label for="city">Город:</label>
        <select name="city" id="city" required>
            <?php foreach ($cities as $cityOption): ?>
                <option value="<?php echo $cityOption; ?>"><?php echo $cityOption; ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="service">Услуга:</label>
        <select name="service" id="service" required>
            <?php foreach ($services as $serviceOption): ?>
                <option value="<?php echo $serviceOption; ?>"><?php echo $serviceOption; ?></option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit">Добавить объявление</button>
    </form>
</main>
<?php include 'footer.php'; ?>
</body>
</html>

