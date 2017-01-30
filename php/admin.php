<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        echo 'Not logged in';
    } else {
        echo 'Welcome '.$_SESSION['username'];
        echo '<hr />';
        echo '<br /><a href="new_entry.php">Create a new blog post</a>';
        echo '<hr />';
        echo '<a href="?logout">Logout</a>';
    }
?>
