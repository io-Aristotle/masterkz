<?php
session_start();
require 'db.php'; // Если нужно подключение к базе. Если не нужно — убери.

// Укажи здесь адрес, куда будут отправляться письма:
$supportEmail = "suriknurik50@gmail.com";

$feedback = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userName  = trim($_POST['name'] ?? '');
    $userEmail = trim($_POST['email'] ?? '');
    $subject   = trim($_POST['subject'] ?? '');
    $message   = trim($_POST['message'] ?? '');

    // Проверяем, что все поля заполнены
    if ($userName && $userEmail && $subject && $message) {
        // Формируем письмо
        $to      = $supportEmail;
        $headers = "From: $userEmail\r\n";
        $body    = "Имя отправителя: $userName\nEmail: $userEmail\n\nСообщение:\n$message";

        // Отправляем письмо
        if (mail($to, $subject, $body, $headers)) {
            $feedback = "Сообщение успешно отправлено!";
        } else {
            $feedback = "Ошибка при отправке сообщения. Попробуйте позже.";
        }
    } else {
        $feedback = "Заполните все поля формы!";
    }
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Служба поддержки</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .support-container {
      max-width: 600px;
      margin: 20px auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .support-container h2 {
      margin-top: 0;
      text-align: center;
    }
    .support-container label {
      display: block;
      margin: 10px 0 5px;
    }
    .support-container input[type="text"],
    .support-container input[type="email"],
    .support-container textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 4px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }
    .support-container button {
      background-color: #388e3c;
      color: #fff;
      border: none;
      padding: 12px 20px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1em;
    }
    .support-container button:hover {
      background-color: #2e7d32;
    }
    .feedback {
      text-align: center;
      margin-bottom: 10px;
      color: green;
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
<main>
  <div class="support-container">
    <h2>Служба поддержки</h2>
    <?php if ($feedback): ?>
      <p class="<?php echo (strpos($feedback, 'Ошибка') !== false) ? 'error' : 'feedback'; ?>">
        <?php echo $feedback; ?>
      </p>
    <?php endif; ?>
    <form method="post" action="support.php">
      <label for="name">Ваше имя:</label>
      <input type="text" name="name" id="name" required>

      <label for="email">Ваш Email:</label>
      <input type="email" name="email" id="email" required>

      <label for="subject">Тема сообщения:</label>
      <input type="text" name="subject" id="subject" required>

      <label for="message">Сообщение:</label>
      <textarea name="message" id="message" rows="5" required></textarea>

      <button type="submit">Отправить</button>
    </form>
  </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>

