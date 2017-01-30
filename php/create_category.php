<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    } else {
        if(isset($_POST['name'], $_POST['parent'])) {
            $name = $_POST['name'];
            $parent = $_POST['parent'];
            $connection = new Connection();
            $result = $connection->createCategory($name, $parent);
            echo "Success";
        } else {
            echo "Failure";
        }
    }
?>

