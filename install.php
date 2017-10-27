<?php
    $step = $_GET['step'];
    switch($step) {
        case 1:
            $host = $_POST['dbhost'];
            $user = $_POST['dbuser'];
            $db = $_POST['db'];
            $pass = $_POST['password'];
            $script_path = '.';
            $command = 'mysql'
                    . ' --host='.$host
                    . ' --user='.$user
                    . ' --password='.$pass
                    . ' --database='.$db
                    . ' --execute="SOURCE ' . $script_path
            ;
            $output1 = shell_exec($command . '/sql/tables.sql"');
            $conf = ['<?php', "\n", 'define("HOST", "'.$host.'");', "\n", 'define("USER", "'.$user.'");', "\n", 'define("DB", "'.$db.'");', "\n", 'define("PASS", "'.$pass.'");', "\n", 'define("SECURE", FALSE);', "\n", '?>'];
            file_put_contents('php/conf.php', $conf);
            break;
        case 2:
            include_once 'php/dbconnect.php';
            $con = new Connection();
            $pw = $_POST['admin-pw'];
            $mail = $_POST['admin-mail'];
            $rep = $_POST['repeat'];
            if($pw != $rep) {
                header('Location: installer.php?step='.$step);
            }
            $con->addUser('admin', $mail, $pw, time());
            break;
        case 3:
            include_once 'php/dbconnect.php';
            $con = new Connection();
            $lang = $_POST['lang'];
            $icon = $_POST['icon'];
            $con->addLanguage($lang, $icon);
            break;
        case 4:
            include_once 'php/conf.php';
            $title = $_POST['title'];
            $subtitle = $_POST['subtitle'];
            $lang = $_POST['language'];
            $script_path = '.';
            $stmt = "
                INSERT INTO
                    settings (name, value, display)
                VALUES
                    ('title','".$title."','Titel'),
                    ('subtitle','".$subtitle."','Untertitel'),
                    ('language','".$lang."','Sprache')
            ";

            $command = 'mysql'
                    . ' --host='.HOST
                    . ' --user='.USER
                    . ' --password='.PASS
                    . ' --database='.DB
                    . ' --execute="' . $stmt.'"'
                    ;
            $output = shell_exec($command);
            break;
        case 5:
            unlink('installer.php');
            unlink('install.php');
            header('Location: index.php');
            $step = 'finish';
            break;
    }
    if ($step == 'finish') {
        header('Location: index.php');
    } else {
        header('Location: installer.php?step='.++$step);
    }
?>
