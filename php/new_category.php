<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    }
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
                <input type="text" name="parent" value="Coming soon" />
                <br />
                <input type="submit" value="Create category">
                <a  href="admin.php">Dismiss changes</a>
            </form>
        </div>
    </body>
</html>

