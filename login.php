<?php
include 'db.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $email    = trim($_POST['email']);
  $password = trim($_POST['password']);

  $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();
  if($stmt->num_rows > 0){
    $stmt->bind_result($id, $username, $hash);
    $stmt->fetch();
    if(password_verify($password, $hash)){
      $_SESSION['user_id'] = $id;
      $_SESSION['username'] = $username;
      header("Location: profile.php");
      exit;
    } else {
      $error = "Неверный пароль.";
    }
  } else {
    $error = "Пользователь не найден.";
  }
  $stmt->close();
}
?>
<?php include 'header.php'; ?>
<h2>Вход</h2>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post" action="login.php">
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Пароль" required>
  <button type="submit">Войти</button>
</form>
<?php include 'footer.php'; ?>
