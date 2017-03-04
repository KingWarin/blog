<?php
    include_once 'conf.php';

    class Connection {
        public function __construct() {
            $this->con = new PDO('mysql:host='.HOST.';dbname='.DB, USER, PASS);
        }

        private $con;

        private function now() {
            return date('Y-m-d H:i:s', time());
        }

        public function getActiveUserByMail($mail) {
            $select = $this->con->prepare("SELECT userId, userAlias, userPass, salt FROM users WHERE userMail=:mail AND status='active'");
            $select->bindParam(':mail', $mail);
            $select->execute();
            $result = $select->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        public function createCategory($categoryName, $parentId) {
            $insert = $this->con->prepare("INSERT INTO categories (categoryName, parentId) VALUES (:category_name, :parent_id)");
            $insert->bindParam(':category_name', $categoryName);
            $insert->bindParam(':parent_id', $parentId);
            $res = $insert->execute();
            return $res;
        }

        public function addLanguage($lang, $langIcon) {
            $insert = $this->con->prepare("
                INSERT INTO
                    languages
                    (language, icon)
                VALUES
                    (:language, :languageIcon)
            ");
            $insert->bindParam(':language', $lang);
            $insert->bindParam(':languageIcon', $langIcon);
            if($insert->execute()) {
                return true;
            }
            return false;
        }

        public function addUser($alias, $mail, $pass, $salt) {
            $insert = $this->con->prepare(
                "INSERT INTO users (userAlias, userMail, userPass, status, salt) VALUES (:user, :userMail, :userPass, 'active', :userSalt)"
            );
            $salt = hash('sha256', $salt);
            $pass = hash('sha256', $pass);
            $userPass = hash('sha256', $pass . $salt);
            $insert->bindParam(':user', $alias);
            $insert->bindParam(':userMail', $mail);
            $insert->bindParam(':userPass', $userPass);
            $insert->bindParam(':userSalt', $salt);
            $res = $insert->execute();
            return $res;
        }

        public function addComment($commentor, $mail, $page, $comment) {
            $date = $this->now();
            $insert = $this->con->prepare(
                "INSERT INTO comments (createDate, commentorName, commentorMail, commentorPage, comment) VALUES (:date, :name, :mail, :page, :comment)"
            );
            $select = $this->con->prepare("SELECT commentId FROM comments WHERE createDate=:date");
            $insert->bindParam(':date', $date);
            $insert->bindParam(':name', $commentor);
            $insert->bindParam(':mail', $mail);
            $insert->bindParam(':page', $page);
            $insert->bindParam(':comment', $comment);
            if($insert->execute()) {
                // successful, so fetch the new comments id
                $insert->closeCursor();
                $select->bindParam(':date', $date);
                if($select->execute()) {
                    $row = $select->fetch(PDO::FETCH_ASSOC);
                    return $row['commentId'];
                }
            }
            // failure
            return false;
        }

        public function linkCommentToArticle($articleId, $commentId) {
            $insert = $this->con->prepare(
                "INSERT INTO articlecomments (articleId, commentId) VALUES (:articleId, :commentId)"
            );
            $insert->bindParam(':articleId', $articleId);
            $insert->bindParam(':commentId', $commentId);
            if($insert->execute()) {
                return true;
            } else {
                return false;
            }
        }

        public function createArticle($heading, $status) {
            $createDate = $this->now();
            $insert = $this->con->prepare(
                "INSERT INTO articles (heading, status, createDate, userId) VALUES (:title, :status, :date, :user)"
            );
            $select = $this->con->prepare("SELECT articleId FROM articles WHERE createDate=:date");
            $insert->bindParam(':title', $heading);
            $insert->bindParam(':status', $status);
            $insert->bindParam(':date', $createDate);
            $insert->bindParam(':user', $_SESSION['userid']);
            if($insert->execute()){
                $insert->closeCursor();
                $select->bindParam(':date', $createDate);
                if($select->execute()) {
                    $row = $select->fetch(PDO::FETCH_ASSOC);
                    return $row['articleId'];
                }
            }
            return false;
        }

        public function createContentForArticle($articleId, $contents) {
            $date = $this->now();
            $insert = $this->con->prepare(
                "INSERT INTO content (articleId, createDate, heading, content, languageId) VALUES (:aId, :date, :heading, :content, :lId)"
            );
            $errors = array();
            foreach($contents as $content) {
                $insert->bindParam(':aId', $articleId);
                $insert->bindParam(':date', $date);
                $insert->bindParam(':heading', $content['contentHeading']);
                $insert->bindParam(':content', $content['content']);
                $insert->bindParam(':lId', $content['languageId']);
                if(!$insert->execute()) {
                    $errors[] = "Unable to create content with heading: ".content['contentHeading'];
                }
                $insert->closeCursor();
            }
            return $errors;
        }

        public function getEntry($articleId) {
            $selectArticle = $this->con->prepare("SELECT articleId, status, heading FROM articles WHERE articleId=:aid");
            $selectContent = $this->con->prepare(
                "SELECT 
                    c.contentId, c.heading, c.content, l.languageId, l.language, l.icon
                 FROM
                    content as c
                 JOIN
                    languages as l
                 ON
                    c.languageId=l.languageId
                 WHERE
                    c.articleId=:aid"
            );
            $selectArticle->bindParam(':aid', $articleId);
            $selectContent->bindParam(':aid', $articleId);
            if($selectArticle->execute()) {
                $article = $selectArticle->fetch(PDO::FETCH_ASSOC);
                $selectArticle->closeCursor();
                if($selectContent->execute()) {
                    $content = $selectContent->fetchAll();
                    $article['content'] = $content;
                    $selectContent->closeCursor();
                    $categories = $this->getCategoriesForArticle($articleId);
                    $article['categories'] = $categories;
                    return $article;
                }
                // For now just fallback to returning false...
            }
            // ... in the future return sth. like a status code so we can display what didn't work out
            return false;
        }

        public function getLanguages() {
            $select = $this->con->prepare("
                SELECT
                    languageId, language, icon
                FROM
                    languages
            ");
            if($select->execute()) {
                $langs = $select->fetchAll();
                return $langs;
            }
            // Again, add some error handling stuff
            return false;
        }

        public function getRemainingLanguagesForArticle($articleId) {
            $select = $this->con->prepare("
                SELECT
                    l.languageId, l.language, l.icon
                FROM
                    languages as l
                WHERE
                    l.languageId
                NOT IN
                    ( SELECT
                        c.languageId
                      FROM
                        content as c
                      WHERE
                        c.articleId=:aId
                    )
            ");
            $select->bindParam(':aId', $articleId);
            if($select->execute()) {
                $langs = $select->fetchAll();
                return $langs;
            }
            return false;
        }

        public function getArticles() {
            $select = $this->con->prepare("
                SELECT
                    articleId, status, createDate, userId, heading
                FROM
                    articles
            ");
            if($select->execute()) {
                $articles = $select->fetchAll();
                return $articles;
            }
            return false;
        }

        public function getCategories() {
            $select = $this->con->prepare("
                SELECT
                    categoryId, categoryName, parentId
                FROM
                    categories
            ");
            if($select->execute()){
                $categories = $select->fetchAll();
                return $categories;
            }
            return false;
        }

        public function getCategoriesForArticle($articleId) {
            $select = $this->con->prepare("
                SELECT
                    c.categoryId, c.categoryName
                FROM
                    categories c
                JOIN
                    articlecategories a
                ON
                    c.categoryId = a.categoryId
                WHERE
                    a.articleId=:aId
            ");
            $select->bindParam(':aId', $articleId);
            if($select->execute()){
                $categories = $select->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
                return $categories;
            }
            return false;
        }

        public function linkCategoriesToArticle($categories, $articleId) {
            $insert = $this->con->prepare("
                INSERT INTO
                    articlecategories
                    (articleId, categoryId)
                VALUES
                    (:aId, :cId)
            ");
            $errors = array();
            foreach($categories as $category) {
                $insert->bindParam(':aId', $articleId);
                $insert->bindParam(':cId', $category);
                if(!$insert->execute()) {
                    $errors[] = "Unable to create category-link for article with category: ".$articleId.'/'.$category;
                }
                $insert->closeCursor();
            }
            if(count($errors) > 0) {
                return $errors;
            }
            return true;
        }

        public function unlinkCategoriesForArticle($categories, $articleId) {
            $delete = $this->con->prepare("
                DELETE FROM
                    articlecategories
                WHERE
                    articleId=:aId
                AND
                    categoryId=:cId
            ");
            $errors = array();
            foreach($categories as $category) {
                $delete->bindParam(':aId', $articleId);
                $delete->bindParam(':cId', $category);
                if(!$delete->execute()) {
                    $errors[] = 'Unable to unlink category for article: '.$category.'/'.$articleId;
                }
                $delete->closeCursor();
            }
            if(count($errors) > 0) {
                return $errors;
            }
            return true;
        }

        public function updateArticle($articleId, $contents) {
            $date = $this->now();
            $insert = $this->con->prepare("
                INSERT INTO
                    content (articleId, createDate, heading, content, languageId)
                VALUES
                    (:aId, :date, :heading, :content, :lId)
            ");
            $updateContent = $this->con->prepare("
                UPDATE
                    content
                SET
                    heading=:heading,
                    content=:content
                WHERE
                    contentId=:contentId
            ");
            $errors = array();
            foreach($contents as $content) {
                if(!isset($content['save'])) {
                    continue;
                } else if($content['save'] == 'update') {
                    $updateContent->bindParam(':heading', $content['contentHeading']);
                    $updateContent->bindParam(':content', $content['content']);
                    $updateContent->bindParam(':contentId', $content['contentId']);
                    if(!$updateContent->execute()) {
                        $errors[] = "Can't update content with id/heading: ".$content['contentId']."/".$content['contentHeading'];
                    }
                    $updateContent->closeCursor();
                } else if($content['save'] == 'on') {
                    $insert->bindParam(':aId', $articleId);
                    $insert->bindParam(':date', $date);
                    $insert->bindParam(':heading', $content['contentHeading']);
                    $insert->bindParam(':content', $content['content']);
                    $insert->bindParam(':lId', $content['languageId']);
                    if(!$insert->execute()) {
                        $errors[] = "Unable to create content with heading: ".$content['contentHeading'];
                    }
                    $insert->closeCursor();
                }
            }
            return $errors;
        }

        public function getSettings() {
            $select = $this->con->prepare("
                SELECT
                    settingId, name, value, display
                FROM
                    settings
            ");
            if($select->execute()) {
                $settings = $select->fetchAll();
                return $settings;
            }
        }

        public function setSettings($settings) {
            $update = $this->con->prepare("
                INSERT INTO
                    settings (settingId, value)
                VALUES
                    (:sId, :sValue)
                ON DUPLICATE KEY UPDATE
                    value=:sValue
            ");
            foreach($settings as $setting) {
                $settingId = NULL;
                if(isset($setting['id'])) {
                    $settingId = $setting['id'];
                }
                $update->bindParam(':sId', $settingId);
                $update->bindParam(':sValue', $setting['value']);
                $update->execute();
            }
        }
    }
?>

