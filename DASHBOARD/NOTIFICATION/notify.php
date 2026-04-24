<?php
function createNotification($conn, $user_id, $type, $message, $ref_id = null) {
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, type, reference_id, message)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isis", $user_id, $type, $ref_id, $message);
    $stmt->execute();
}
?>