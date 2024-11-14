<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die('Доступ запрещен');
}

include 'connect.php';

$person1_id = (int)$_POST['person1_id'];
$person2_id = (int)$_POST['person2_id'];
$relationship_type = $_POST['relationship_type'];

// Проверка на корректность данных
if ($person1_id > 0 && $person2_id > 0 && in_array($relationship_type, ['child', 'spouse', 'parent'])) {
    
    // Проверка на существующую пару супругов (для типа связи "spouse")
    if ($relationship_type == 'spouse') {
        // Проверяем, есть ли уже связь "spouse" между этими двумя людьми
        $query_check = "
            SELECT COUNT(*) as count 
            FROM relationships 
            WHERE 
                (person1_id = $person1_id AND person2_id = $person2_id AND relationship_type = 'spouse') 
                OR 
                (person1_id = $person2_id AND person2_id = $person1_id AND relationship_type = 'spouse')";
        
        $result_check = $mysqli->query($query_check);
        $row_check = $result_check->fetch_assoc();

        if ($row_check['count'] > 0) {
            // Если связь уже существует, выводим ошибку
            echo "Ошибка: Такая связь уже существует между этими людьми!";
            exit;
        }
    }
    
    // Проверка на корректность связи для "child"
    if ($relationship_type == 'child') {
        // person1 — родитель, person2 — ребенок
        $query = "INSERT INTO relationships (person1_id, person2_id, relationship_type) 
                  VALUES ($person1_id, $person2_id, 'child')";
    } elseif ($relationship_type == 'parent') {
        // person1 — ребенок, person2 — родитель
        $query = "INSERT INTO relationships (person1_id, person2_id, relationship_type) 
                  VALUES ($person2_id, $person1_id, 'child')";
    } else {
        // для spouse
        $query = "INSERT INTO relationships (person1_id, person2_id, relationship_type) 
                  VALUES ($person1_id, $person2_id, 'spouse')";
    }

    if ($mysqli->query($query)) {
        // Перенаправляем обратно на страницу редактирования этого человека
        header("Location: person.php?id=$person1_id");
    } else {
        echo "Ошибка при добавлении родственной связи: " . $mysqli->error;
    }
} else {
    echo "Некорректные данные для связи.";
}
?>
