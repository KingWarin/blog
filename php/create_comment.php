<?php
    include_once 'helper.php';

    secure_session();
    if(
        isset($_POST['articleId']) &&
        checkNonEmpty($_POST['comment']) &&
        checkNonEmpty($_POST['name']) &&
        checkNonEmpty($_POST['captcha']) &&
        $_POST['captcha'] == $_SESSION['commentcaptcha']
    ) {
        $connection = new Connection();
        try {
            $connection->begin();
            $commentId = $connection->addComment($_POST['name'], $_POST['mail'], $_POST['website'], $_POST['comment']);
            $foo = $connection->linkCommentToArticle($_POST['articleId'], $commentId);
            $connection->commit();
            header('Location: ../index.php');
        } catch (Exception $e) {
            echo 'Error on creating comment: '. $e->getMessage();
            $connection->rollback();
        }
    } else {
        echo "Failure! No comment or commentor-name given.";
    }
    echo '<br /><a href="../index.php">Click here to return to the page</a>';
?>

