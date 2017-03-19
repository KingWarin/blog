<?php
    include_once "php/helper.php";
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
                    echo '<article>'.$content['heading'].' '.$content['content'].'</article>';
                }
            }
        ?>
        </main>
        <footer>
        </footer>
    </body>
</html>
