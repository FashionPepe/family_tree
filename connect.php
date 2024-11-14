<?php
$host = 'localhost'; // Адрес сервера
$user = 'root';      // Имя пользователя
$password = '';      // Пароль
$dbname = 'story'; // Имя базы данных

// Создание подключения
$mysqli = new mysqli($host, $user, $password, $dbname);

// Проверка подключения
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Установка кодировки
$mysqli->set_charset('utf8');
?>