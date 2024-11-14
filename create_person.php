<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить человека</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <a class="index-btn" href="index.php"><p>Родословное дерево</p></a>
        <?php
            session_start();
            if (!isset($_SESSION['loggedin'])) {
                echo "<a href='login.php' class='login-btn'>Авторизация редактора</a>";
            } else {
                echo "<div class='btns-crator'><a href='logout.php'>Выход</a></div>";
            }
        ?>
    </header>

    <main class="tree-container">
        <?php if (isset($_SESSION['loggedin'])): ?>
            <h2>Создать нового человека</h2>
            <form action="save_person.php" method="POST" enctype="multipart/form-data" class="edit-form">
                <label>Имя: <input type="text" name="name" required></label><br>
                <label>Фамилия: <input type="text" name="surname" required></label><br>
                <label>Отчество: <input type="text" name="additional_name"></label><br>
                <label>Дата рождения: <input type="date" name="birth_date" required></label><br>
                <label>Дата смерти: <input type="date" name="death_date"></label><br>
                <label>Описание: <textarea name="description"></textarea></label><br>

                <div class="file-upload-wrapper">
                    <label class="file-upload-button" for="file-upload-input">Выберите файл</label>
                    <input type="file" id="file-upload-input" name="photo" class="file-upload-input" onchange="displayFileName(this)">
                    <span class="file-upload-filename" id="file-upload-filename">Файл не выбран</span>
                </div>

                <br>
                <button type="submit" class="btn-save">Создать человека</button>
            </form>
        <?php else: ?>
            <p>Для создания нового человека необходимо <a href="login.php">авторизоваться</a>.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>Создатель сайта Дима Долгов. Спасибо за сбор информации Пищеву Сергею Георгиевичу</p>
    </footer>

    <script>
    function displayFileName(input) {
        const fileName = input.files[0] ? input.files[0].name : "Файл не выбран";
        document.getElementById('file-upload-filename').textContent = fileName;
    }
    </script>

</body>
</html>
