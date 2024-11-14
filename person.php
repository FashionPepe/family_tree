<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Родословное Древо</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
    <header>
        <a class="index-btn" href="index.php"><p>Родословное дерево</p></a>
        <?php
            session_start();
            if (!isset($_SESSION['loggedin'])) {
                echo "<a href='login.php' class='login-btn'>Авторизация редактора</a>";
            } else {
                echo "<div class='btns-crator'><a href='logout.php'>Выход</a>";
                echo "<a herf='create_person.php'>Cоздать человека</a></div>";
            }
        ?>
    </header>

    <main class="tree-container">
        <?php 
        include 'connect.php';

        if (isset($_SESSION['loggedin'])): 
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

            if ($id > 0){
                $person = [];
                $result = $mysqli->query("SELECT * FROM humans WHERE id = $id");
                while ($row = $result->fetch_assoc()) {
                    $person[] = $row;
                }
            } ?>

            <hr>
            <h2>Редактировать информацию</h2>
            <form action="update_person.php" method="POST" enctype="multipart/form-data" class="edit-form">
                <input type="hidden" name="id" value="<?php echo $person[0]['id']; ?>">
                <label>Имя: <input type="text" name="name" value="<?php echo htmlspecialchars($person[0]['name']); ?>"></label><br>
                <label>Фамилия: <input type="text" name="surname" value="<?php echo htmlspecialchars($person[0]['surname']); ?>"></label><br>
                <label>Отчество: <input type="text" name="additional_name" value="<?php echo htmlspecialchars($person[0]['additional_name']); ?>"></label><br>
                <label>Дата рождения: <input type="date" name="birth_date" value="<?php echo $person[0]['year_birth'].'-'.$person[0]['month_birth'].'-'.$person[0]['day_birth']; ?>"></label><br>
                <label>Описание: <textarea name="description"><?php echo htmlspecialchars($person[0]['description']); ?></textarea></label><br>
                <div class="file-upload-wrapper">
                    <label class="file-upload-button" for="file-upload-input">Выберите файл</label>
                    <input type="file" id="file-upload-input" name="photo" class="file-upload-input" onchange="displayFileName(this)">
                    <span class="file-upload-filename" id="file-upload-filename">Файл не выбран</span>
                </div>

                <br>
                <button type="submit" class="btn-save">Сохранить изменения</button>
            </form>
            
            
            <hr>
            <h3>Родственные связи</h3>
            <div class="relationships">
                <?php
                // Получаем родственные связи для этого человека
                $relationships = $mysqli->query("SELECT r.*, h1.name AS person1_name, h2.name AS person2_name 
                                                 FROM relationships r
                                                 JOIN humans h1 ON r.person1_id = h1.id
                                                 JOIN humans h2 ON r.person2_id = h2.id
                                                 WHERE (r.person1_id = $id OR r.person2_id = $id)");

                while ($relationship = $relationships->fetch_assoc()) {
                    $person1_id = $relationship['person1_id'];
                    $person2_id = $relationship['person2_id'];
                    $relationship_type = $relationship['relationship_type'];
                    
                    // Определяем имя для отображения
                    $relation_name = $person1_id == $id ? $relationship['person2_name'] : $relationship['person1_name'];
                    $relation_type = "";
                    
                    // Определяем тип связи
                    if ($relationship_type == 'child') {
                        if($person1_id == $id){
                            $relation_type = "Ребенок";
                        }
                        else{
                            $relation_type = "Родитель";  
                        }
                    }elseif ($relationship_type == 'spouse') {
                        $relation_type = "Супруг(а)";
                    }

                    echo "<div class='relationship-item'>";
                    echo "<p><strong>{$relation_name}</strong> - {$relation_type}</p>";
                    echo "<form action='delete_relationship.php' method='POST' class='delete-form'>";
                    echo "<input type='hidden' name='person1_id' value='{$person1_id}'>";
                    echo "<input type='hidden' name='person2_id' value='{$person2_id}'>";
                    echo "<input type='hidden' name='id' value='{$_GET['id']}'>";
                    echo "<input type='hidden' name='relationship_type' value='{$relationship_type}'>";
                    echo "<button type='submit' class='delete-btn'>Удалить связь</button>";
                    echo "</form>";
                    echo "</div>";
                }
                ?>
            </div>
            
            <hr>
            <h3>Добавить родственную связь</h3>
            <form action="add_relationship.php" method="POST" class="add-relationship-form">
                <input type="hidden" name="person1_id" value="<?php echo $id; ?>">

                <label for="person2_id">Выберите человека:</label>
                <select name="person2_id" required class="form-select">
                    <option value="">Выберите...</option>
                    <?php
                    $other_people = $mysqli->query("SELECT * FROM humans WHERE id != $id");
                    while ($person_option = $other_people->fetch_assoc()) {
                        echo "<option value='{$person_option['id']}'>{$person_option['name']} {$person_option['surname']}</option>";
                    }
                    ?>
                </select><br>

                <label for="relationship_type">Тип связи:</label>
                <select name="relationship_type" required class="form-select">
                    <option value="parent">Родитель</option>
                    <option value="child">Ребёнок</option>
                    <option value="spouse">Супруг(а)</option>
                </select><br>

                <button type="submit" class="btn-save">Добавить связь</button>
            </form>

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
