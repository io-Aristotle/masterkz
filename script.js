// Пример простой функции для подтверждения удаления объявления
function confirmDelete(adId) {
    if (confirm("Вы уверены, что хотите удалить объявление?")) {
      window.location.href = "delete_ad.php?id=" + adId;
    }
  }
  let lastMessageId = 0;

function loadMessages() {
    if (contactId) {
        fetch("load_messages.php?contact_id=" + contactId + "&last_id=" + lastMessageId)
            .then(response => response.text())
            .then(data => {
                // Предполагается, что load_messages.php вернет только новые сообщения,
                // и если они есть, добавим их к chat-box.
                if (data.trim() !== "") {
                    // Предполагается, что каждое новое сообщение обёрнуто в элемент с data-id
                    // Здесь можно просто добавить содержимое.
                    document.getElementById("chat-box").innerHTML += data;
                    
                    // Обновляем lastMessageId, например, парся последний элемент
                    // (Для упрощения можно сделать, чтобы load_messages.php возвращал JSON с lastMessageId)
                }
            })
            .catch(error => console.error("Ошибка загрузки сообщений:", error));
    }
}

setInterval(loadMessages, 3000);
