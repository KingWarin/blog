<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    } else {
        if(isset($_POST['name'], $_POST['parent'])) {
            $name = $_POST['name'];
            $parent = $_POST['parent'];
            if($parent == 'none') {
                $parent = NULL;
            }
            $connection = new Connection();
            try {
                $connection->createCategory($name, $parent);
                header('Location: admin.php');
            } catch (SQLException $e) {
                echo 'Creation failed: ' .$e->getMessage();
            }
        } else {
            echo "Failure";
        }
        echo '<a href="admin.php">Return to admin panel</a>';
    }
?>

