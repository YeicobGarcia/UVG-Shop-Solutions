<?php
class MySQLSessionHandler implements SessionHandlerInterface {
    private $conn;
    private $table = 'sessions';

    public function open($savePath, $sessionName): bool {
        $this->conn = new mysqli('db-app-store.cn44qw2ounhe.us-east-1.rds.amazonaws.com', 'root', 'admin123*', 'app_web_store');
        return $this->conn->connect_error ? false : true;
    }

    public function close(): bool {
        return $this->conn->close();
    }

    public function read(string $session_id): string {
        $stmt = $this->conn->prepare("SELECT data FROM $this->table WHERE id = ? LIMIT 1");
        $stmt->bind_param('s', $session_id);
        $stmt->execute();
        $stmt->bind_result($data);
        $stmt->fetch();
        return $data ?: '';
    }

    public function write(string $session_id, string $data): bool {
        $stmt = $this->conn->prepare("REPLACE INTO $this->table (id, data, timestamp) VALUES (?, ?, ?)");
        $time = time();
        $stmt->bind_param('ssi', $session_id, $data, $time);
        return $stmt->execute();
    }

    public function destroy(string $session_id): bool {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE id = ?");
        $stmt->bind_param('s', $session_id);
        return $stmt->execute();
    }

    public function gc(int $maxlifetime): int|false {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE timestamp < ?");
        $old = time() - $maxlifetime;
        $stmt->bind_param('i', $old);
        return $stmt->execute() ? $stmt->affected_rows : false;
    }
}

// Configurar el manejador de sesiones personalizado
$handler = new MySQLSessionHandler();
session_set_save_handler($handler, true);
session_start();
