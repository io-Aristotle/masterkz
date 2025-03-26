<?php

ini_set('SMTP', 'smtp.yandex.ru');
ini_set('smtp_port', 465);
ini_set('sendmail_from', 'suriknurik50@yandex.kz');


$to = "recipient@example.com"; // Укажи адрес, на который хочешь отправить письмо
$subject = "Тестовое письмо от моего сайта";
$message = "Привет, это тестовое письмо, отправленное через Яндекс SMTP!";
$headers = "From: suriknurik50@yandex.kz\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Письмо успешно отправлено!";
} else {
    echo "Ошибка отправки письма.";
}
?>

