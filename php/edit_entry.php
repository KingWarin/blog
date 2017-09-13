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
    $categories = $connection->getCategories();
?>
<html>
    <head>
        <title>Edit Blog Entry</title>
        <link rel="stylesheet" href="../css/new_entry.css">
    </head>
    <body>
        <div>
            <h1>Modify an existing blog entry</h1>
        </div>
        <form action="update_entry.php" method="post">
            <div class="heading-row">
                <label for="articleHeading">Heading:</label>
                <input type="text" name="articleHeading" value="<?php echo $entry['heading'] ?>" disabled/>
                <input type="hidden" name="articleId" value="<?php echo $articleId ?>" />
            </div>
            <hr />
                <?php
                    foreach($entry['content'] as $content) {
                ?>
                    <div class="content-row">
                        <label for="contentHeading">Language dependent heading:</label>
                        <input type="text" name="<?php echo "lang[".$content['languageId']."][contentHeading]" ?>" value="<?php echo $content['heading'] ?>" />
                        <label for="language">Language:</label>
                        <input type="text" name="<?php echo "lang[".$content['languageId']."][language]" ?>" value="<?php echo $content['language'] ?>" disabled />
                        <label for="content">Content:</label>
                        <textarea name="<?php echo "lang[".$content['languageId']."][content]" ?>"><?php echo $content['content'] ?></textarea>
                        <input type="hidden" name="<?php echo "lang[".$content['languageId']."][save]" ?>" value="update" />
                        <input type="hidden" name="<?php echo "lang[".$content['languageId']."][contentId]" ?>" value="<?php echo $content['contentId'] ?>" />
                    </div>
                    <hr />
                <?php
                    }
                    if($languages && count($languages) > 0) {
                        foreach($languages as $language) {
                ?>
                    <div class="content-row">
                        <label tabindex="0" class="language-toggle" for="<?php echo "lang[".$language['languageId']."][save]" ?>">Create and save <?php echo $language['language'] ?></label>
                        <input type="checkbox" class="language" id="<?php echo "lang[".$language['languageId']."][save]" ?>" name="<?php echo "lang[".$language['languageId']."][save]" ?>" />
                        <div class="body">
                            <label for="contentHeading">Language dependent heading:</label>
                            <input type="text" name="<?php echo "lang[".$language['languageId']."][contentHeading]" ?>" />
                            <label for="language">Language:</label>
                            <input type="text" name="<?php echo "lang[".$language['languageId']."][language]" ?>" value="<?php echo $language['language'] ?>" disabled />
                            <label for="content">Content:</label>
                            <textarea name="<?php echo "lang[".$language['languageId']."][content]" ?>"></textarea>
                        </div>
                    </div>
                    <hr />
                <?php
                        }
                    }
                    if(count($categories) > 0) {
                        echo "<div><h1>Categories</h1>";
                    }
                    foreach($categories as $category) {
                ?>
                    <div class="category-line<?php echo ($category['parentId'] != NULL ? " child-category" : '') ?>">
                    <label for="category[<?php echo $category['categoryId'] ?>]"><?php echo $category['categoryName'] ?></label>
                    <input type="checkbox" name="category[<?php echo $category['categoryId'] ?>]" <?php echo (isset($entry['categories'][$category['categoryId']]) ? 'checked' : '') ?>/>
                    </div>
                <?php
                    }
                    if(count($categories) > 0) {
                        echo "</div><hr />";
                    }
                ?>
                <label for="publish">Publish:</label>
                <input type="checkbox" name="publish" <?php echo (isset($entry['status']) && $entry['status'] == 'published' ? 'checked' : '') ?>/>
                <hr />
                <input type="submit" value="Update" />
                <a  href="admin.php">Dismiss changes</a>
            </form>
        </div>
    </body>
</html>

