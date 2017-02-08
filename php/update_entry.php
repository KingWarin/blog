<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    } else {
        if(isset($_POST['articleId'], $_POST['lang'])) {
            $status = 'draft';
            if(isset($_POST['publish'])) {
                $status = 'published';
            }
            $articleId = $_POST['articleId'];
            $languages = $_POST['lang'];
            $allContent = array();
            while($lang = each($languages)) {
                $content = $lang['value'];
                $content['languageId'] = $lang['key'];
                $allContent[] = $content;
            }
            $connection = new Connection();
            $result = $connection->updateArticle($articleId, $allContent);
            if(count($result) > 0) {
                echo "Can't update entry.<br />";
                foreach($result as $error) {
                    echo $error."<br />";
                }
            } else {
                header('Location: admin.php');
            }
        } else {
            header('Location: admin.php');
        }
    }
?>

