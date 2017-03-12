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
            $connection = new Connection();
            try {
                $connection->begin();
                $categoriesSet = $connection->getCategoriesForArticle($articleId);
                $categoriesOld = array();
                while($category = each($categoriesSet)) {
                    $categoriesOld[] = $category['key'];
                }
                $languages = $_POST['lang'];
                $allContent = array();
                while($lang = each($languages)) {
                    $content = $lang['value'];
                    $content['languageId'] = $lang['key'];
                    $allContent[] = $content;
                }
                $delCategories = $categoriesOld;
                if(isset($_POST['category'])) {
                    $categories = array();
                    while($category = each($_POST['category'])) {
                        $categories[] = $category['key'];
                    }
                    $delCategories = array_diff($categoriesOld, $categories);
                    $connection->linkCategoriesToArticle($categories, $articleId);
                }
                if(count($delCategories) > 0) {
                    $connection->unlinkCategoriesForArticle($delCategories, $articleId);
                }
                $connection->updateArticle($articleId, $allContent);
                $connection->commit();
                header('Location: admin.php');
            } catch SQLException $e {
                echo "Can't update entry:<br />" .$e->getMessage();
                echo '<a href="admin.php">Back to admin panel</a>';
                $connection->rollback();
            }
        } else {
            header('Location: admin.php');
        }
    }
?>

