<?php
include 'db.php';
session_start();
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit;
}

if(isset($_GET['id'])){
  $adId = intval($_GET['id']);
  // Проверяем, принадлежит ли объявление пользователю
  $stmt = $conn->prepare("SELECT user_id FROM ads WHERE id = ?");
  $stmt->bind_param("i", $adId);
  $stmt->execute();
  $stmt->bind_result($user_id);
  $stmt->fetch();
  $stmt->close();

  if($user_id == $_SESSION['user_id']){
    $stmt = $conn->prepare("DELETE FROM ads WHERE id = ?");
    $stmt->bind_param("i", $adId);
    $stmt->execute();
    $stmt->close();
    $message = "Объявление удалено.";
  } else {
    $message = "Нет прав на удаление.";
  }
} else {
  $message = "Не указано объявление.";
}
header("Location: search.php?msg=" . urlencode($message));
exit;
?>
