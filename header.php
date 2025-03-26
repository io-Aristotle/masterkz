<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>MasterKZ</title>
  <link rel="stylesheet" href="style.css">
  <script src="script.js" defer></script>
</head>
<body>
  <header>
    <h1>MasterKZ</h1>
    <nav>
      <ul>
        <li><a href="index.php">Главная</a></li>
        <?php if(isset($_SESSION['user_id'])): ?>
          <li><a href="chats.php">Чаты</a></li>
          <a href="chat_history.php" class="chat-link">
            <span id="chat-notification" style="display: none; color: red; font-weight: bold;">●</span>
          </a>
          <li><a href="support.php">Поддержка</a></li>
        
          <li><a href="add_ad.php">Добавить объявление</a></li>
          <li><a href="profile.php">Профиль</a></li> <!-- Профиль теперь последний -->
        <?php else: ?>
          <li><a href="register.php">Регистрация</a></li>
          <li><a href="login.php">Вход</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
  <main>

