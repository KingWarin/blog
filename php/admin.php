<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        echo 'Not logged in';
    } else {
        echo 'Welcome '.$_SESSION['username'];
        echo '<a href="?logout">Logout</a>';
    }
?>
