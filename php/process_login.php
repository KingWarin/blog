<?php
    if(isset($_POST['mail'], $_POST['pass']) {
        include_once dbconnect.php
        $stmt = $con->prepare("SELECT userAlias, userPass, salt FROM users WHERE userMail=:mail AND status='active'");
        $stmt->bindParam(':mail', $mail);

        $mail = $_POST['mail'];
        $pass = $_POST['pass'];

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $dbpass = $result['userPass'];
        $salt = $result['salt'];

        $pass = hash('sha256', $pass . $salt);
        if( $dbpass == $pass ) {
            //valid, login
        } else {
            //invalid, go home
        }
    }
?>
