<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die('Доступ запрещен');
}

include 'connect.php';

// Получаем данные из POST
$person1_id = isset($_POST['person1_id']) ? (int)$_POST['person1_id'] : 0;
$person2_id = isset($_POST['person2_id']) ? (int)$_POST['person2_id'] : 0;
$relationship_type = isset($_POST['relationship_type']) ? $_POST['relationship_type'] : '';

// Используем person1_id как базу для перенаправления
$current_person_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($person1_id > 0 && $person2_id > 0 && !empty($relationship_type)) {
    // Удаляем запись из таблицы relationships
    $query = "DELETE FROM relationships 
              WHERE person1_id = $person1_id AND person2_id = $person2_id AND relationship_type = '$relationship_type'";

    if ($mysqli->query($query)) {
        // Перенаправляем обратно на страницу редактируемого человека
        header("Location: person.php?id=$current_person_id");
        exit;
    } else {
        echo "Ошибка при удалении связи: " . $mysqli->error;
    }
} else {
    echo "Некорректные данные для удаления связи.";
}
?>
