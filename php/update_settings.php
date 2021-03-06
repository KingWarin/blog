<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    }
    $connection = new Connection();
    $settings = array();
    while($setting = each($_POST)) {
        $settings[] = [
            "id" => $setting['key'],
            "value" => $setting['value'],
        ];
    }
    try {
        $connection->setSettings($settings);
        header('Location: admin.php');
    } catch (SQLException $e) {
        echo "Can't update settings:<br />" .$e->getMessage();
        echo '<a href="admin.php">Back to admin panel</a>';
    }
?>

