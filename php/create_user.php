<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    } else {
        if(isset(
            $_POST['alias'],
            $_POST['mail'],
            $_POST['pass'],
            $_POST['passc'],
            $_POST['salt'],
            $_POST['saltc']
        )) {
            $alias = $_POST['alias'];
            $mail = $_POST['mail'];
            $pass = $_POST['pass'];
            $salt = $_POST['salt'];
            if ($pass == $_POST['passc'] && $salt == $_POST['saltc']) {
                $connection = new Connection();
                try {
                    $connection->addUser($alias, $mail, $pass, $salt);
                    header('Location: admin.php');
                } catch SQLException $e {
                    echo 'Creation failed: ' .$e->getMessage();
                }
            }
        } else {
            echo "Failure";
        }
        echo '<a href="admin.php">Back to admin panel</a>';
    }
?>

