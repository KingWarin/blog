<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    } else if(isset($_GET['aid'])) {
        $articleId = $_GET['aid'];
    } else {
        header('Location: admin.php');
    }

    $connection = new Connection();
    $entry = $connection->getEntry($articleId);
    $languages = $connection->getRemainingLanguagesForArticle($articleId);
?>
<html>
    <head>
        <title>Edit Blog Entry</title>
    </head>
    <body>
        <div>
            <h1>Modify an existing blog entry</h1>
        </div>
        <div>
            <form action="update_entry.php" method="post">
                <label for="articleHeading">Heading:</label>
                <input type="text" name="articleHeading" value="<?php echo $entry['heading'] ?>" disabled/>
                <input type="hidden" name="articleId" value="<?php echo $articleId ?>" />
                <hr />
                <?php
                    foreach($entry['content'] as $content) {
                ?>
                    <label for="contentHeading">Language dependent heading:</label>
                    <input type="text" name="<?php echo "lang[".$content['languageId']."][contentHeading]" ?>" value="<?php echo $content['heading'] ?>" />
                    <label for="language">Language:</label>
                    <input type="text" name="<?php echo "lang[".$content['languageId']."][language]" ?>" value="<?php echo $content['language'] ?>" disabled />
                    <label for="content">Content:</label>
                    <textarea name="<?php echo "lang[".$content['languageId']."][content]" ?>"><?php echo $content['content'] ?></textarea>
                    <input type="hidden" name="<?php echo "lang[".$content['languageId']."][save]" ?>" value="update" />
                    <input type="hidden" name="<?php echo "lang[".$content['languageId']."][contentId]" ?>" value="<?php echo $content['contentId'] ?>" />
                    <hr />
                <?php
                    }
                    if($languages && count($languages) > 0) {
                        foreach($languages as $language) {
                ?>
                            <label for="contentHeading">Language dependent heading:</label>
                            <input type="text" name="<?php echo "lang[".$language['languageId']."][contentHeading]" ?>" />
                            <label for="language">Language:</label>
                            <input type="text" name="<?php echo "lang[".$language['languageId']."][language]" ?>" value="<?php echo $language['language'] ?>" disabled />
                            <label for="content">Content:</label>
                            <textarea name="<?php echo "lang[".$language['languageId']."][content]" ?>"></textarea>
                            <label for="save">Save language content</label>
                            <input type="checkbox" name="<?php echo "lang[".$language['languageId']."][save]" ?>" />
                            <hr />
                <?php
                        }
                    }
                ?>
                <input type="submit" value="Update" />
                <a  href="admin.php">Dismiss changes</a>
            </form>
        </div>
    </body>
</html>

