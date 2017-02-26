<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    }
    $connection = new Connection();
    $categories = $connection->getCategories();
?>
<html>
    <head>
        <title>New Category</title>
    </head>
    <body>
        <div>
            <h1>Create a new blog category</h1>
        </div>
        <div>
            <form action="create_category.php" method="post">
                <label for="name">Categoryname:</label>
                <input type="text" name="name" />
                <br />
                <label for="parent">Parent category:</label>
                <select name="parent">
                <option value="none"></option>
                <?php
                    foreach($categories as $category) {
                        echo '<option value="'.$category['categoryId'].'">'.$category['categoryName'].'</option>';
                    }
                ?>
                </select>
                <br />
                <input type="submit" value="Create category">
                <a  href="admin.php">Dismiss changes</a>
            </form>
        </div>
    </body>
</html>

