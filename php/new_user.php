<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    }
    $connection = new Connection();
?>
<html>
    <head>
        <title>New users</title>
    </head>
    <body>
        <div>
            <h1>Add a new user</h1>
        </div>
        <div>
            <form action="create_user.php" method="post">
                <label for="alias">Username:</label>
                <input type="text" name="alias" />
                <br />
                <label for="mail">User email:</label>
                <input type="text" name="mail" />
                <br />
                <label for="pass">Password:</label>
                <input type="text" name="pass" />
                <br />
                <label for="passc">Password (retype):</label>
                <input type="text" name="passc" />
                <br />
                <label for="salt">Salt:</label>
                <input type="text" name="salt" />
                <br />
                <label for="saltc">Salt (retype):</label>
                <input type="text" name="saltc" />
                <br />
                <input type="submit" value="Create user">
                <a  href="admin.php">Dismiss changes</a>
            </form>
        </div>
    </body>
</html>

