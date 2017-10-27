<?php
    $step = isset($_GET['step']) ? intval($_GET['step']) : 1;
?>
<html>
    <head>
    <title>Blog installation - Step <?php echo $step ?></title>
    </head>
    <body>
    <h1>Blog Installation - Step <?php echo $step ?></h1>
    <form action="install.php?step=<?php echo $step ?>" method="post">
        <?php
            if ($step == 1) {
        ?>
            <h2>Table setup</h2>
            <label for="dbuser">Database-User:</label>
            <input type="text" name="dbuser" />
            <label for="dbhost">Database-Host: (most likely sth like 'localhost')</label>
            <input type="text" name="dbhost" />
            <label for="db">Database:</label>
            <input type="text" name="db" />
            <label for="password">Database-Password:</label>
            <input type="password" name="password" />
        <?php
            } else if($step == 2) {
        ?>
            <h2>Admin creation</h2>
            <label for="admin-mail">Admin-EMail:</label>
            <input type="text" name="admin-mail" />
            <label for="admin-pw">Admin-Password:</label>
            <input type="password" name="admin-pw" />
            <label for="repeat">Repeat Password:</label>
            <input type="password" name="repeat" />
        <?php
            } else if($step == 3) {
        ?>
            <h2>Default language creation</h2>
            <label for="lang">Language name:</label>
            <input type="text" name="lang" />
            <label for="icon">Language icon:</label>
            <input type="text" name="icon" />
        <?php
            } else if($step == 4) {
        ?>
            <h2>Basic blog settings</h2>
            <label for="title">Blog title:</label>
            <input type="text" name="title" />
            <label for="subtitle">Blog sub title:</label>
            <input type="text" name="subtitle" />
            <input type="hidden" name="language" value="1" />
        <?php
            } else if($step == 5) {
        ?>
            <h2>Finish installation<h2>
            <span>Submit to remove installation files.</span>
        <?php
            }
        ?>
            <input type="submit" />
        </form>
    </body>
</html>
