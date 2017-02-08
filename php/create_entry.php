<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    } else {
        if(isset($_POST['articleHeading'], $_POST['lang'])) {
            $status = 'draft';
            if(isset($_POST['publish'])) {
                $status = 'published';
            }
            $connection = new Connection();
            $article = $connection->createArticle($_POST['articleHeading'], $status);
            if(!$article) {
                //an error occured, do some error handling
                echo "Unable to create article";
            } else {
                $languages = $_POST['lang'];
                $allContent = array();
                while($lang = each($languages)) {
                    $content = $lang['value'];
                    if(!isset($content['save'])) {
                        continue;
                    }
                    $content['languageId'] = $lang['key'];
                    $content['articleId'] = $article;
                    $allContent[] = $content;
                }
                $content = $connection->createContentForArticle($article, $allContent);
                if(count($content) > 0) {
                    echo "Errors occured.<br />";
                    foreach($content as $error) {
                        echo $error."<br />";
                    }
                } else {
                    header('Location: admin.php');
                }
            }
        } else {
            echo "Failure";
        }
    }
?>

