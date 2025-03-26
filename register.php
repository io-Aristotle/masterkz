<?php
include 'db.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $username = trim($_POST['username']);
  $email    = trim($_POST['email']);
  $password = trim($_POST['password']);
  $city     = trim($_POST['city']);

  // Простая валидация (расширяй по необходимости)
  if($username && $email && $password){
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, city) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hash, $city);
    if($stmt->execute()){
      $_SESSION['user_id'] = $stmt->insert_id;
      $_SESSION['username'] = $username;
      header("Location: profile.php");
      exit;
    } else {
      $error = "Ошибка регистрации: " . $conn->error;
    }
    $stmt->close();
  } else {
    $error = "Пожалуйста, заполните все обязательные поля.";
  }
}
?>
<?php include 'header.php'; ?>
<h2>Регистрация</h2>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post" action="register.php">
  <input type="text" name="username" placeholder="Имя" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Пароль" required>
  <input type="password" name="password" placeholder=" Подвердите пароль" required>
  <input type="text" name="city" placeholder="Город">
  <button type="submit">Зарегистрироваться</button>
</form>
<?php include 'footer.php'; ?>
