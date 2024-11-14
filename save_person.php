<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die('Доступ запрещен');
}

include 'connect.php';

$name = $_POST['name'];
$surname = $_POST['surname'];
$additional_name = $_POST['additional_name'];
$birth_date = $_POST['birth_date'];
$death_date = !empty($_POST['death_date']) ? $_POST['death_date'] : null;
$description = $_POST['description'];

// Разбиваем дату рождения на день, месяц, год
$birthParts = explode('-', $birth_date);
$day_birth = (int)$birthParts[2];
$month_birth = (int)$birthParts[1];
$year_birth = (int)$birthParts[0];

// Обработка даты смерти
$day_death = $month_death = $year_death = null;
if ($death_date) {
    $deathParts = explode('-', $death_date);
    $day_death = (int)$deathParts[2];
    $month_death = (int)$deathParts[1];
    $year_death = (int)$deathParts[0];
}

// Обработка загрузки фотографии
$photoPath = null;
if (!empty($_FILES['photo']['name'])) {
    $photoName = basename($_FILES['photo']['name']);
    $targetPath = "images/" . $photoName;
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
        $photoPath = $targetPath;
    }
}

// Вставка данных в базу
$query = "INSERT INTO humans (name, surname, additional_name, day_birth, month_birth, year_birth, 
                              day_seath, month_death, year_death, description, photo) 
          VALUES ('$name', '$surname', '$additional_name', $day_birth, $month_birth, $year_birth, 
                  " . ($day_death ?: 'NULL') . ", " . ($month_death ?: 'NULL') . ", " . ($year_death ?: 'NULL') . ", 
                  '$description', " . ($photoPath ? "'$photoPath'" : 'NULL') . ")";

if ($mysqli->query($query)) {
    // Получаем ID последнего добавленного человека
    $newPersonId = $mysqli->insert_id;

    // Перенаправляем на страницу редактирования этого человека
    header("Location: person.php?id=$newPersonId");
    exit;
} else {
    echo "Ошибка: " . $mysqli->error;
}
?>
