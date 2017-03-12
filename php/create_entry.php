<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    } else {
        if(isset($_POST['articleHeading'], $_POST['lang']) && checkNonEmpty($_POST['articleHeading'])) {
            $status = 'draft';
            if(isset($_POST['publish'])) {
                $status = 'published';
            }
            $connection = new Connection();
            try {
                $connection->begin();
                $article = $connection->createArticle($_POST['articleHeading'], $status);
                if(isset($_POST['category'])) {
                    $categories = array();
                    while($category = each($_POST['category'])) {
                        $categories[] = $category['key'];
                    }
                    $connection->linkCategoriesToArticle($categories, $article);
                }
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
                $connection->createContentForArticle($article, $allContent);
                $connection->commit();
                header('Location: admin.php');
            } catch (Exception $e) {
                echo 'Error on creating article: '. $e->getMessage();
                $connection->rollback();
            }
        } else {
            echo "Failure! No heading and/or language set.";
        }
        echo '<br /><a href="admin.php">Click here to return to admin panel</a>';
    }
?>

