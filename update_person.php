<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die('Доступ запрещен');
}

include 'connect.php';

$id = (int)$_POST['id'];
$name = $_POST['name'];
$surname = $_POST['surname'];
$additional_name = $_POST['additional_name'];
$birth_date = $_POST['birth_date'];
$description = $_POST['description'];

// Разбиваем дату рождения на день, месяц, год
$birthParts = explode('-', $birth_date);
$day_birth = (int)$birthParts[2];
$month_birth = (int)$birthParts[1];
$year_birth = (int)$birthParts[0];

// Обработка загрузки фотографии
$photoPath = null;
if (!empty($_FILES['photo']['name'])) {
    $photoName = basename($_FILES['photo']['name']);
    $targetPath = "images/" . $photoName;
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
        $photoPath = $targetPath;
    }
}

$query = "UPDATE humans SET 
    name='$name', 
    surname='$surname', 
    additional_name='$additional_name', 
    day_birth=$day_birth, 
    month_birth=$month_birth, 
    year_birth=$year_birth, 
    description='$description'";

if ($photoPath) {
    $query .= ", photo='$photoPath'";
}

$query .= " WHERE id=$id";

if ($mysqli->query($query)) {
    header("Location: person.php?id=$id");
} else {
    echo "Ошибка: " . $mysqli->error;
}
?>
