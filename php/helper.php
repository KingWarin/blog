<?php
    include_once 'dbconnect.php';

    function login($mail, $pass) {
        global $con;
        $stmt = $con->prepare("SELECT userId, userAlias, userPass, salt FROM users WHERE userMail=:mail AND status='active'");
        $stmt->bindParam(':mail', $mail);

        $pass = hash('sha256', $pass); // TODO: Just for testing, this line has to be removed when frontend is created.

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $dbpass = $result['userPass'];
        $salt = $result['salt'];

        $pass = hash('sha256', $pass . $salt);
        if( $dbpass == $pass ) {
            //valid, login
            $_SESSION['userid'] = $result['userId'];
            $_SESSION['username'] = $result['userAlias'];
            $_SESSION['agent'] = hash('sha512', $_SERVER['HTTP_USER_AGENT'].$result['userId']);

            return true;
        } else {
            //invalid, go home
            return false;
        }
    }

    function secure_session() {
        $session_name = 'blog_session';

        $cookie_params = session_get_cookie_params();
        session_set_cookie_params(
            $cookie_params["lifetime"],
            $cookie_params["path"],
            $cookie_params["domain"],
            SECURE,
            true
        );
        session_name($session_name);
        session_start();
        session_regenerate_id();
    }

    function check_login() {
        if(isset($_SESSION['userid'], $_SESSION['username'], $_SESSION['agent'])) {
            if(isset($_GET['logout'])) {
                logout();
            }
            if( hash('sha512', $_SERVER['HTTP_USER_AGENT'].$_SESSION['userid']) == $_SESSION['agent']) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function logout() {
        $_SESSION = array();
        $cookie_params = session_get_cookie_params();
        $home = '../login.html';
        setcookie(
            session_name(),
            '',
            1,
            $cookie_params["path"],
            $cookie_params['domain'],
            $cookie_params["secure"],
            $cookie_params['httponly']
        );
        session_destroy();
        header('Location: '.$home);
    }
?>
