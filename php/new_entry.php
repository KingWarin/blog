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
                <label for="heading">Heading:</label>
                <input type="text" name="heading" />
                <br />
                <input type="submit" value="Create and add content">
                <a  href="admin.php">Dismiss changes</a>
            </form>
        </div>
    </body>
</html>

