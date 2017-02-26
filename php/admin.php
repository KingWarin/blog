<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        echo 'Not logged in';
    } else {
        echo 'Welcome '.$_SESSION['username'];
        echo '<hr />';
        echo '<br /><a href="new_entry.php">Create a new blog post</a>';
        echo '<br /><a href="list_entries.php">Edit an existing blog post</a>';
        echo '<br /><a href="new_category.php">Create a new category</a>';
        echo '<br /><a href="new_language.php">Add a new language</a>';
        echo '<br /><a href="new_user.php">Add a new user</a>';
        echo '<hr />';
        echo '<a href="?logout">Logout</a>';
    }
?>
