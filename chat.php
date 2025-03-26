<?php
session_start();
include 'db.php';

// Получаем контакт (собеседника) из параметра URL
$contactId = isset($_GET['contact_id']) ? intval($_GET['contact_id']) : 0;

if ($contactId === 0) {
    header("Location: chats.php");
    exit;
}

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$currentUserId = $_SESSION['user_id'];

// Получаем данные собеседника
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $contactId);
$stmt->execute();
$stmt->bind_result($contactUsername);
$stmt->fetch();
$stmt->close();

include 'header.php';
?>
<script>
let lastMessageId = 0; // теперь обновляется правильно

fetch(`load_messages.php?contact_id=${contactId}&last_id=${lastMessageId}`)
    .then(response => response.json()) 
    .then(data => {
        if (data.html.trim() !== "") {
            document.getElementById("chat-box").insertAdjacentHTML("beforeend", data.html);
            lastMessageId = data.last_id;
        }
    })



setInterval(loadMessages, 3000);
</script>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Чат с <?= htmlspecialchars($contactUsername); ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Стили для чата в виде мессенджера */
        .chat-container {
            width: 500px;
            margin: 20px auto;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .chat-header h2 {
            margin: 0;
            font-size: 1.2em;
        }
        #online-status {
            font-size: 0.9em;
        }
        #chat-box {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background: #f4f4f4;
            margin: 10px 0;
        }
        .message {
            max-width: 70%;
            margin: 5px 0;
            padding: 10px;
            border-radius: 10px;
            font-size: 14px;
        }
        .sent {
            background: #dcf8c6;
            align-self: flex-end;
        }
        .received {
            background: #fff;
            border: 1px solid #ccc;
            align-self: flex-start;
        }
        .chat-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ccc;
            background: #fff;
        }
        .chat-input textarea {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: none;
        }
        .chat-input button {
            margin-left: 10px;
            padding: 10px 15px;
            background: #388e3c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .chat-input button:hover {
            background: #2e7d32;
        }
    </style>
</head>
<body>
<main>
    <div class="chat-container">
        <div class="chat-header">
            <h2>Чат с <?= htmlspecialchars($contactUsername); ?></h2>
            <span id="online-status">🔴 Офлайн</span>
        </div>
        <div id="chat-box">
            <!-- История сообщений будет отображаться здесь -->
        </div>
        <div class="chat-input">
            <textarea id="message" placeholder="Введите сообщение..."></textarea>
            <button id="send-btn">Отправить</button>
        </div>
    </div>
</main>

<script>
    const userId = <?= $currentUserId; ?>;
    const contactId = <?= $contactId; ?>;
    const ws = new WebSocket("ws://localhost:8080");

    ws.onopen = function() {
        console.log("Подключено к WebSocket-серверу");
        // Информируем сервер о подключении пользователя
        ws.send(JSON.stringify({ type: "connect", user_id: userId }));
    };

    ws.onmessage = function(event) {
    const data = JSON.parse(event.data);
    console.log("Получено сообщение:", data); // для отладки
    const chatBox = document.getElementById("chat-box");

    if (data.type === "message") {
        chatBox.innerHTML += `<div class="message ${data.sender == userId ? 'sent' : 'received'}">
                                    <p>${data.message}</p>
                              </div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
    }
    if (data.type === "status") {
        document.getElementById("online-status").innerHTML = data.online ? "🟢 Онлайн" : "🔴 Офлайн";
    }
    if (data.type === "notification") {
        document.title = "🔴 Новое сообщение!";
    }
};
    ws.onerror = function(error) {
        console.error("WebSocket ошибка:", error);
    };

    // Отправка сообщения при клике на кнопку или по Enter
    function sendMessage() {
    const messageEl = document.getElementById("message");
    const message = messageEl.value.trim();
    if (message !== "" && ws && contactId) {
        // Отправляем сообщение через WebSocket (для мгновенного отображения)
        ws.send(JSON.stringify({ type: "message", sender: userId, receiver: contactId, message: message }));

        // Сохраняем сообщение в базе через AJAX
        fetch("save_message.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ sender: userId, receiver: contactId, message: message })
        })
        .then(response => response.json())
        .then(data => {
            console.log("Сохранение сообщения:", data);
            // После сохранения можно обновить историю сообщений
            loadMessages();
        })
        .catch(error => console.error("Ошибка сохранения:", error));
        
        messageEl.value = "";
    }
}

function loadMessages() {
    if (contactId) {
        fetch("load_messages.php?contact_id=" + contactId)
            .then(response => response.text())
            .then(data => {
                document.getElementById("chat-box").innerHTML = data;
            })
            .catch(error => console.error("Ошибка загрузки сообщений:", error));
    }
}

setInterval(loadMessages, 3000); // Обновляем чат каждые 3 секунды

    document.getElementById("send-btn").addEventListener("click", sendMessage);
    document.getElementById("message").addEventListener("keypress", function(e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
</script>
<?php include 'footer.php'; ?>
</body>
</html>





