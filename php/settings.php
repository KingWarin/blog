<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    }

    $connection = new Connection();
    $settings = $connection->getSettings();
?>
<html>
    <head>
        <title>Blog Settings</title>
    </head>
    <body>
        <div>
            <h1>Current settings</h1>
        </div>
        <div>
            <form action="update_settings.php" method="post">
            <?php
                foreach($settings as $setting) {
                    echo '<label for="'.$setting['settingId'].'">'.$setting['display'].'</label>';
                    echo '<input type="text" name="'.$setting['settingId'].'" value="'.$setting['value'].'" />';
                    echo '<br />';
                }
            ?>
                <input type="submit" value="Update settings">
                <a  href="admin.php">Dismiss changes</a>
            </form>
        </div>
    </body>
</html>

