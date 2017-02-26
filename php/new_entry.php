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
    </head>
    <body>
        <div>
            <h1>Create a new blog entry</h1>
        </div>
        <div>
            <form action="create_entry.php" method="post">
                <label for="articleHeading">Heading:</label>
                <input type="text" name="articleHeading" />
                <hr />
                <?php
                    foreach($langs as $lang) {
                ?>
                    <label for="contentHeading">Language dependent heading:</label>
                    <input type="text" name="<?php echo "lang[".$lang['languageId']."][contentHeading]" ?>" />
                    <label for="language">Language:</label>
                    <input type="text" name="<?php echo "lang[".$lang['languageId']."][language]" ?>" value="<?php echo $lang['language'] ?>" disabled />
                    <label for="content">Content:</label>
                    <textarea name="<?php echo "lang[".$lang['languageId']."][content]" ?>"></textarea>
                    <label for="save">Save language content</label>
                    <input type="checkbox" name="<?php echo "lang[".$lang['languageId']."][save]" ?>" />
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
                <label for="publish">Publish:</label>
                <input type="checkbox" name="publish" />
                <hr />
                <input type="submit" value="Create" />
                <a  href="admin.php">Dismiss changes</a>
            </form>
        </div>
    </body>
</html>

