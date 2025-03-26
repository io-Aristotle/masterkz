<?php
include 'db.php';
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $ad_id = intval($_POST['ad_id']);
  $rating = intval($_POST['rating']);
  $review_text = trim($_POST['review_text']);

  if($ad_id && $rating){
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, ad_id, rating, review_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $_SESSION['user_id'], $ad_id, $rating, $review_text);
    if($stmt->execute()){
      $message = "Отзыв добавлен!";
    } else {
      $message = "Ошибка: " . $conn->error;
    }
    $stmt->close();
  } else {
    $message = "Заполните обязательные поля.";
  }
}
?>
<?php include 'header.php'; ?>
<h2>Добавить отзыв</h2>
<?php if(isset($message)) echo "<p>$message</p>"; ?>
<form method="post" action="review.php">
  <input type="number" name="ad_id" placeholder="ID объявления" required>
  <select name="rating" required>
    <option value="">Оценка</option>
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option value="5">5</option>
  </select>
  <textarea name="review_text" placeholder="Ваш отзыв"></textarea>
  <button type="submit">Добавить отзыв</button>
</form>
<?php include 'footer.php'; ?>
