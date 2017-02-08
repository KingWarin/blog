<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    } else {
        if(isset($_POST['language'], $_POST['icon'])) {
            $language = $_POST['language'];
            $icon = $_POST['icon'];
            $connection = new Connection();
            $result = $connection->addLanguage($language, $icon);
            header('Location: admin.php');
        } else {
            echo "Failure";
        }
    }
?>

