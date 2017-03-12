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
            try {
                $connection->addLanguage($language, $icon);
                header('Location: admin.php');
            } catch SQLException $e {
                echo 'Creation failed: ' .$e->getMessage();
            }
        } else {
            echo "Failure";
        }
        echo '<a href="admin.php">Return to admin panel</a>';
    }
?>

