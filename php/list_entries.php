<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    }

    $connection = new Connection();
    $articles = $connection->getArticles();
?>
<html>
    <head>
        <title>Available Blog Entries</title>
    </head>
    <body>
        <div>
            <h1>All available blog entries</h1>
        </div>
        <div>
            <div class="articles-overview">
            <?php
                foreach($articles as $article) {
            ?>
                <div class="article-line">
                    <div><?php echo $article['heading'] ?></div>
                    <div><?php echo $article['status'] ?></div>
                    <div><?php echo $article['createDate'] ?></div>
                    <div><?php echo $article['userId'] ?></div>
                    <div><a href="edit_entry.php?aid=<?php echo $article['articleId'] ?>">Edit article</a></div>
                </div>
            <?php
                }
            ?>
            </div>
            <a href="admin.php">Back</a>
        </div>
    </body>
</html>

