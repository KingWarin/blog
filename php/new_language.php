<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    }
    $connection = new Connection();
    $langs = $connection->getLanguages();
?>
<html>
    <head>
        <title>New Language</title>
    </head>
    <body>
        <div>
            <h1>Add a new language for useage on the blog</h1>
        </div>
        <div>
            <form action="create_language.php" method="post">
                <label for="language">Language:</label>
                <input type="text" name="language" />
                <br />
                <label for="icon">Icon for language:</label>
                <input type="text" name="icon" />
                <br />
                <input type="submit" value="Create language">
                <a  href="admin.php">Dismiss changes</a>
            </form>
        </div>
        <div>
            <h2>Existing languages:</h2>
            <?php
                foreach($langs as $lang) {
                    echo "<div>".$lang['icon']. " " . $lang['language']."</div>";
                }
            ?>
        </div>
    </body>
</html>

