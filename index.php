<?php
    include_once "php/helper.php";
    secure_session();
    $con = new Connection();
    $settings = $con->getSettings();
    $langs = $con->getLanguages();
    $title = 'New Blog';
    $subtitle = '';
    foreach($settings as $setting) {
        if($setting['name'] == 'title') {
            $title = $setting['value'];
        } else if($setting['name'] == 'subtitle') {
            $subtitle = $setting['value'];
        } else if($setting['name'] == 'defaultLanguage') {
            $defaultLanguage = $setting['value'];
        }
    }
    if(isset($_GET['lang'])) {
        $selectedLanguage = $_GET['lang'];
    } else {
        $selectedLanguage = $defaultLanguage;
    }
    $cat = false;
    if(isset($_GET['cat'])) {
        $cat = $_GET['cat'];
    }
    $offset = 0;
    $limit = 10;
    $direction = false;
    $posts = $con->getPosts($selectedLanguage, $offset, $limit, $direction, $cat);
    $categories = $con->getCategories();
?>
<html>
    <head>
        <title><?php echo $title ?></title>
        <style>
            .comment-section {
                border: 1px solid #00FF00;
            }
            .comment-section .comment-toggle {
                text-decoration: underline;
                color: blue;
            }
            .comment-section-body {
                display: none;
            }
            .comment-section-heading {
                display: none;
            }
            .comment-section-heading:checked + div.comment-section-body {
                display: block;
            }
        </style>
    </head>
    <body>
        <header>
            <h1><?php echo $title ?></h1>
            <h2><?php echo $subtitle ?></h2>
            <nav>
                <ul>
                <?php
                    foreach($langs as $lang) {
                        echo '<li><a href="?lang='.$lang['languageId'].'">'.$lang['language'].'</a></li>';
                    }
                ?>
                </ul>
            </nav>
        </header>
        <aside>
            <nav>
                <ul>
                <?php
                    foreach($categories as $category) {
                        $link = '?cat='.$category['categoryId'];
                        if($selectedLanguage != $defaultLanguage) {
                            $link = $link.'&lang='.$selectedLanguage;
                        }
                        echo '<li><a href="'.$link.'">'.$category['categoryName'].'</a></li>';
                    }
                ?>
                </ul>
            </nav>
        </aside>
        <main>
        <?php
            foreach($posts as $post) {
                if(isset($post['content'])) {
                    $content = $post['content'];
                    $commentCount = count($post['comments']);
                    echo '<article style="border:1px solid;">'.$content['createDate'].' '.$content['heading'].' '.$content['content'];
                    echo '<div class="comment-section">';
                    if($commentCount < 1) {
                        echo '<label class="comment-toggle" for="comment-toggle-'.$post['articleId'].'">Leave a comment</label><input type="checkbox" id="comment-toggle-'.$post['articleId'].'" class="comment-section-heading" />';
                        echo '<div class="comment-section-body">';
                    } else {
                        echo '<label class="comment-toggle" for="comment-toggle-'.$post['articleId'].'">'.count($post['comments']).' Comments</label><input type="checkbox" id="comment-toggle-'.$post['articleId'].'" class="comment-section-heading" />';
                        echo '<div class="comment-section-body">';
                        foreach($post['comments'] as $comment) {
                            echo '<div class="comment">'.$comment['createDate'].' '.$comment['commentorName'].' '.$comment['comment'].'</div>';
                        }
                    }
                    echo '<div class="leave-a-comment">';
                    echo '<form action="php/create_comment.php" method="post">';
                    echo '<label for="name">Name:</label>';
                    echo '<input type="text" name="name" />';
                    echo '<label for="mail">Mail:</label>';
                    echo '<input type="text" name="mail" />';
                    echo '<label for="website">Homepage:</label>';
                    echo '<input type="text" name="website" />';
                    echo '<label for="comment">Comment</label>';
                    echo '<input type="textarea" name="comment"></textarea>';
                    echo '<label for="captcha">Captcha:</label>';
                    echo '<img src="/php/captcha.php?mode=comment" width="145" height="30" />';
                    echo '<input type="text" name="captcha" />';
                    echo '<input type="hidden" name="articleId" value="'.$post['articleId'].'" />';
                    echo '<input type="submit">';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div></article>';
                }
            }
        ?>
        </main>
        <footer>
        </footer>
    </body>
</html>
