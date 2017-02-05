<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    } else {
        if(isset($_POST['articleHeading'], $_POST['contentHeading'], $_POST['language'], $_POST['content'])) {
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
                $content = $connection->createContentForArticle($article, $_POST['contentHeading'], $_POST['content'], $_POST['language']);
                if(!$content) {
                    // an error occured, do some error handling
                    echo "Unable to create content";
                } else {
                    if(isset($_POST['moreContent'])) {
                        // redirect to addContent-form, for now print sth
                        echo "Successful created mew article, now creating more content";
                    } else {
                        // redirect somewhere, for now print sth
                        echo "Successful created new article";
                    }
                }
            }
        } else {
            echo "Failure";
        }
    }
?>

