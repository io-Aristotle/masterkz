<?php
session_start();
include 'db.php';

// Получаем параметры поиска из GET
$search          = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedCity    = isset($_GET['city']) ? trim($_GET['city']) : '';
$selectedService = isset($_GET['service']) ? trim($_GET['service']) : '';

// Список городов
$cities = [
    "Абайская область", "Акмолинская область", "Актюбинская область", "Алматинская область", "Атырауская область",
    "Западно-Казахстанская область", "Жамбылская область", "Жетісу область", "Карагандинская область",
    "Костанайская область", "Кызылординская область", "Мангистауская область", "Павлодарская область",
    "Северо-Казахстанская область", "Туркестанская область", "Ұлытауская область", "Восточно-Казахстанская область",
    "город Астана", "город Алматы", "город Шымкент"
];

// Список услуг
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

// Формируем SQL-запрос
$sql = "SELECT * FROM ads WHERE 1=1";
$bindTypes = "";
$paramsArr = [];

if ($search !== "") {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $bindTypes .= "ss";
    $like = "%" . $search . "%";
    $paramsArr[] = $like;
    $paramsArr[] = $like;
}

if ($selectedCity !== "") {
    $sql .= " AND city = ?";
    $bindTypes .= "s";
    $paramsArr[] = $selectedCity;
}

if ($selectedService !== "") {
    $sql .= " AND service = ?";
    $bindTypes .= "s";
    $paramsArr[] = $selectedService;
}

// Сортируем по дате создания (новые объявления сверху)
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($bindTypes) {
    $stmt->bind_param($bindTypes, ...$paramsArr);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>MasterKZ - Главная</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Стили для блока поиска */
        form.search-form {
            max-width: 600px;
            margin: 20px 20px 20px 20px; /* отступ со всех сторон */
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        form.search-form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        form.search-form input,
        form.search-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form.search-form button {
            background-color: #388e3c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        form.search-form button:hover {
            background-color: #2e7d32;
        }
        
        /* Стили для объявлений */
        .ads-container {
            max-width: 800px;
            margin: 20px 20px 20px 20px; /* отступы */
        }
        .ad {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .ad h3 {
            margin-top: 0;
        }
        .ad p {
            margin: 5px 0;
        }
        .ad a {
            display: inline-block;
            margin-top: 10px;
            color: #388e3c;
            text-decoration: none;
            font-weight: bold;
        }
        .ad a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<main>
    <h2>Объявления</h2>
    
    <!-- Форма поиска (фильтр) -->
    <form class="search-form" method="get" action="index.php">
        <label for="search">Поиск по ключевым словам:</label>
        <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>">
        
        <label for="city">Выбрать город:</label>
        <select name="city" id="city">
            <option value="">Все города</option>
            <?php foreach ($cities as $cityOption): ?>
                <option value="<?php echo $cityOption; ?>" <?php if ($cityOption == $selectedCity) echo 'selected'; ?>>
                    <?php echo $cityOption; ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label for="service">Выбрать услугу:</label>
        <select name="service" id="service">
            <option value="">Все услуги</option>
            <?php foreach ($services as $serviceOption): ?>
                <option value="<?php echo $serviceOption; ?>" <?php if ($serviceOption == $selectedService) echo 'selected'; ?>>
                    <?php echo $serviceOption; ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit">Найти</button>
    </form>
    
    <!-- Вывод объявлений -->
    <div class="ads-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($ad = $result->fetch_assoc()): ?>
                <?php
                $adTitle = isset($ad['title']) ? $ad['title'] : 'Без названия';
                $adDesc  = isset($ad['description']) ? $ad['description'] : 'Описание отсутствует';
                $adPrice = isset($ad['price']) ? $ad['price'] : 0;
                $adCity  = isset($ad['city']) ? $ad['city'] : 'Не указан';
                $adServ  = isset($ad['service']) ? $ad['service'] : 'Не указана';
                $adId    = isset($ad['id']) ? $ad['id'] : 0;
                ?>
                <div class="ad">
                    <h3><?php echo htmlspecialchars($adTitle); ?></h3>
                    <p><?php echo htmlspecialchars($adDesc); ?></p>
                    <p>Цена: <?php echo htmlspecialchars($adPrice); ?> тг</p>
                    <p>Город: <?php echo htmlspecialchars($adCity); ?></p>
                    <p>Услуга: <?php echo htmlspecialchars($adServ); ?></p>
                    <a href="ad_details.php?id=<?php echo $adId; ?>">Подробнее</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Объявлений не найдено.</p>
        <?php endif; ?>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>




