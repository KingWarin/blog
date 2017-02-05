<?php
    include_once 'helper.php';

    secure_session();
    if(!check_login()) {
        header('Location: ../login.html');
    } else if(isset($_GET['aid']) {
        $articleId = $_GET['aid'];
    } else {
        header('Location: admin.php');
    }

    $connection = new Connection();
    $entry = $connection->getEntry($articleId);
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
                <input type="text" name="articleHeading" value="<?php echo $entry['heading'] ?>" />
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
                    <hr />
                <?php
                    }
                ?>
                <input type="submit" value="Update" />
                <a  href="admin.php">Dismiss changes</a>
            </form>
        </div>
    </body>
</html>

