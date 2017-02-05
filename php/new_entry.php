<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    }
?>
<html>
    <head>
        <title>New Blog Entry</title>
    </head>
    <body>
        <div>
            <h1>Create a new blog entry</h1>
        </div>
        <div>
            <form action="create_entry.php" method="post">
                <label for="articleHeading">Heading:</label>
                <input type="text" name="articleHeading" />
                <hr />
                <label for="contentHeading">Language dependent heading:</label>
                <input type="text" name="contentHeading" />
                <label for="language">Language:</label>
                <select name="language">
                    <?php
                        $connection = new Connection();
                        $langs = $connection->getLanguages();
                        foreach($langs as $lang) {
                            echo '<option value="'.$lang['languageId'].'">'.$lang['language'].'</option>';
                        }
                    ?>
                </select>
                <label for="content">Content:</label>
                <textarea name="content"></textarea>
                <label for="moreContent">Add content for other language</label>
                <input type="checkbox" name="moreContent" />
                <hr />
                <input type="submit" value="Create" />
                <a  href="admin.php">Dismiss changes</a>
            </form>
        </div>
    </body>
</html>

