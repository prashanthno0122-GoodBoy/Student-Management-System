<?php
include 'config.php';

if (isset($_GET['id']) && ctype_digit($_GET['id']) && (int)$_GET['id'] > 0) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM entries WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: view.php");
        exit;
    } else {
        echo "Error deleting record.";
    }

    $stmt->close();
} else {
    echo "Invalid student ID.";
}
?>