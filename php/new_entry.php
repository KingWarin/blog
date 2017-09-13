<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    }
    $connection = new Connection();
    $langs = $connection->getLanguages();
    $categories = $connection->getCategories();
?>
<html>
    <head>
        <title>New Blog Entry</title>
        <link rel="stylesheet" href="../css/new_entry.css">
    </head>
    <body>
        <div>
            <h1>Create a new blog entry</h1>
        </div>
        <form action="create_entry.php" method="post">
            <div class="heading-row">
                <label for="articleHeading">Heading:</label>
                <input type="text" name="articleHeading" />
            </div>
            <hr />
            <?php
                foreach($langs as $lang) {
            ?>
                <div class="content-row">
                    <label class="language-toggle" tabindex="0" for="<?php echo "lang[".$lang['languageId']."][save]" ?>">Create and save <?php echo $lang['language'] ?></label>
                    <input type="checkbox" class="language" id="<?php echo "lang[".$lang['languageId']."][save]" ?>" name="<?php echo "lang[".$lang['languageId']."][save]" ?>" />
                    <div class="body">
                        <label for="<?php echo "lang[".$lang['languageId']."][contentHeading]" ?>">Language dependent heading:</label>
                        <input type="text" name="<?php echo "lang[".$lang['languageId']."][contentHeading]" ?>" />
                        <label for="<?php echo "lang[".$lang['languageId']."][language]" ?>">Language:</label>
                        <input type="text" name="<?php echo "lang[".$lang['languageId']."][language]" ?>" value="<?php echo $lang['language'] ?>" disabled />
                        <label for="<?php echo "lang[".$lang['languageId']."][content]" ?>">Content:</label>
                        <textarea name="<?php echo "lang[".$lang['languageId']."][content]" ?>"></textarea>
                    </div>
                </div>
                <hr />
            <?php
                }
                if(count($categories) > 0) {
                    echo "<div><h1>Categories</h1>";
                }
                foreach($categories as $category) {
            ?>
                <div class="category-line<?php echo ($category['parentId'] != NULL ? " child-category" : '') ?>">
                    <label for="category[<?php echo $category['categoryId'] ?>]"><?php echo $category['categoryName'] ?></label>
                    <input type="checkbox" name="category[<?php echo $category['categoryId'] ?>]" />
                </div>
            <?php
                }
                if(count($categories) > 0) {
                    echo "</div><hr />";
                }
            ?>
            <div class="publish-row">
                <label for="publish">Publish:</label>
                <input type="checkbox" name="publish" />
                <hr />
                <input type="submit" value="Create" />
                <a  href="admin.php">Dismiss changes</a>
            </div>
        </form>
    </body>
</html>

