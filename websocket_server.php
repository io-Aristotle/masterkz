<?php
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface {
    protected $clients;
    protected $users; // Соответствие соединений и ID пользователей

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->users = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Новое соединение ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        if (!isset($data['type'])) return;

        switch ($data['type']) {
            case "connect":
                $this->users[$from->resourceId] = $data['user_id'];
                echo "Пользователь {$data['user_id']} подключился (ID: {$from->resourceId})\n";
                $this->updateUserStatus($data['user_id'], true);
                break;

                case "message":
                    $senderId = $data['sender'];
                    $receiverId = $data['receiver'];
                    $message = htmlspecialchars($data['message']);
                    
                    // Отправляем сообщение обоим участникам чата
                    foreach ($this->clients as $client) {
                        // Если клиент принадлежит отправителю или получателю
                        if (isset($this->users[$client->resourceId]) &&
                            in_array($this->users[$client->resourceId], [$senderId, $receiverId])) {
                            $client->send(json_encode([
                                "type" => "message",
                                "sender" => $senderId,
                                "message" => $message
                            ]));
                        }
                    }
                    echo "Сообщение от {$senderId} -> {$receiverId}: {$message}\n";
                    break;
                

               

            case "status":
                $targetId = $data['to'];
                $isOnline = in_array($targetId, $this->users);
                $from->send(json_encode([
                    "type" => "status",
                    "online" => $isOnline
                ]));
                break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $userId = $this->users[$conn->resourceId] ?? null;
        unset($this->users[$conn->resourceId]);
        $this->clients->detach($conn);

        if ($userId) {
            echo "Пользователь {$userId} отключился (ID: {$conn->resourceId})\n";
            $this->updateUserStatus($userId, false);
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Ошибка: {$e->getMessage()}\n";
        $conn->close();
    }

    private function updateUserStatus($userId, $status) {
        foreach ($this->clients as $client) {
            $client->send(json_encode([
                "type" => "status",
                "user_id" => $userId,
                "online" => $status
            ]));
        }
    }
}

$server = \Ratchet\Server\IoServer::factory(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer(
            new ChatServer()
        )
    ),
    8080
);

echo "WebSocket сервер запущен на порту 8080...\n";
$server->run();


