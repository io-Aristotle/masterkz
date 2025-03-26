<?php
include 'db.php';
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $service = trim($_POST['service']);
  $details = trim($_POST['details']);
  $price   = floatval($_POST['price']);
  if($service){
    $stmt = $conn->prepare("INSERT INTO orders (user_id, service, details, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issd", $_SESSION['user_id'], $service, $details, $price);
    if($stmt->execute()){
      $feedback = "Заказ оформлен!";
    } else {
      $feedback = "Ошибка: " . $conn->error;
    }
    $stmt->close();
  } else {
    $feedback = "Заполните обязательные поля.";
  }
}
?>
<?php include 'header.php'; ?>
<h2>Заказ услуг</h2>
<?php if(isset($feedback)) echo "<p>$feedback</p>"; ?>
<form method="post" action="order.php">
  <input type="text" name="service" placeholder="Услуга" required>
  <textarea name="details" placeholder="Детали заказа"></textarea>
  <input type="number" step="0.01" name="price" placeholder="Цена">
  <button type="submit">Оформить заказ</button>
</form>
<?php include 'footer.php'; ?>
