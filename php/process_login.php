<?php
    if(isset($_POST['mail'], $_POST['pwd'])) {
        include_once 'helper.php';

        secure_session();
        $mail = $_POST['mail'];
        $pass = $_POST['pwd'];

        if(login($mail, $pass) ) {
            //valid, login
            header('Location: admin.php');
        } else {
            //invalid, go home
            header('Location: ../login.html');
        }
    } else {
        header('Location: ../login.html');
    }
?>
