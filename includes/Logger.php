<?php
class Logger {
    public static function log($action, $target_table = null, $target_id = null, $details = null) {
        global $pdo;
        
        $user_id = $_SESSION['user_id'] ?? null;
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        
        try {
            $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, target_table, target_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $action, $target_table, $target_id, $details, $ip_address]);
        } catch (Exception $e) {
            // Silently fail logging if there's an error to not break main flow
            // In production, you might want to log this to a file
        }
    }

    public static function login($userId) {
        self::log("User Login", "users", $userId, "User logged into the system.");
    }

    public static function delete($table, $id, $name = "") {
        self::log("Deleted Record", $table, $id, "Deleted $name (ID: $id) from $table.");
    }

    public static function update($table, $id, $details) {
        self::log("Updated Record", $table, $id, $details);
    }
}
?>
