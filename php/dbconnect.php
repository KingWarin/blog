<?php
    include_once 'conf.php';
    include_once 'exceptions.php';

    class Connection {
        public function __construct() {
            $this->con = new PDO('mysql:host='.HOST.';dbname='.DB, USER, PASS);
        }

        private $con;

        private function lastId() {
            return $this->con->lastInsertId();
        }

        private function now() {
            return date('Y-m-d H:i:s', time());
        }

        public function begin() {
            $this->con->beginTransaction();
        }

        public function rollback() {
            $this->con->rollBack();
        }

        public function commit() {
            $this->con->commit();
        }

        public function getActiveUserByMail($mail) {
            $select = $this->con->prepare("
                SELECT
                    userId, userAlias, userPass, salt
                FROM
                    users
                WHERE
                    userMail=:mail
                AND
                    status='active'
            ");
            $select->bindParam(':mail', $mail);
            if(!$select->execute()) {
                throw new SQLException($select->errorInfo()[2]);
            }
            $result = $select->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        public function createCategory($categoryName, $parentId) {
            $insert = $this->con->prepare("
                INSERT INTO
                    categories (categoryName, parentId)
                VALUES
                    (:category_name, :parent_id)
            ");
            $insert->bindParam(':category_name', $categoryName);
            $insert->bindParam(':parent_id', $parentId);
            if($insert->execute()) {
                return true;
            }
            throw new SQLException($insert->errorInfo()[2]);
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
            throw new SQLException($insert->errorInfo()[2]);
        }

        public function addUser($alias, $mail, $pass, $salt) {
            $insert = $this->con->prepare("
                INSERT INTO
                    users (userAlias, userMail, userPass, status, salt)
                VALUES
                    (:user, :userMail, :userPass, 'active', :userSalt)
            ");
            $salt = hash('sha256', $salt);
            $pass = hash('sha256', $pass);
            $userPass = hash('sha256', $pass . $salt);
            $insert->bindParam(':user', $alias);
            $insert->bindParam(':userMail', $mail);
            $insert->bindParam(':userPass', $userPass);
            $insert->bindParam(':userSalt', $salt);
            if($insert->execute()) {
                return true;
            }
            throw new SQLException($insert->errorInfo()[2]);
        }

        public function addComment($commentor, $mail, $page, $comment) {
            $date = $this->now();
            $insert = $this->con->prepare("
                INSERT INTO
                    comments (createDate, commentorName, commentorMail, commentorPage, comment)
                VALUES
                    (:date, :name, :mail, :page, :comment)
            ");
            $insert->bindParam(':date', $date);
            $insert->bindParam(':name', $commentor);
            $insert->bindParam(':mail', $mail);
            $insert->bindParam(':page', $page);
            $insert->bindParam(':comment', $comment);
            if($insert->execute()) {
                // successful, so return the new comments id
                return $this->lastId();
            }
            throw new SQLException($insert->errorInfo()[2]);
        }

        public function linkCommentToArticle($articleId, $commentId) {
            $insert = $this->con->prepare("
                INSERT INTO
                    articlecomments (articleId, commentId)
                VALUES
                    (:articleId, :commentId)
            ");
            $insert->bindParam(':articleId', $articleId);
            $insert->bindParam(':commentId', $commentId);
            if($insert->execute()) {
                return true;
            }
            throw new SQLException($insert->errorInfo()[2]);
        }

        public function createArticle($heading, $status) {
            $createDate = $this->now();
            $insert = $this->con->prepare("
                INSERT INTO
                    articles (heading, status, createDate, userId)
                VALUES
                    (:title, :status, :date, :user)
            ");
            $insert->bindParam(':title', $heading);
            $insert->bindParam(':status', $status);
            $insert->bindParam(':date', $createDate);
            $insert->bindParam(':user', $_SESSION['userid']);
            if($insert->execute()){
                return $this->lastId();
            }
            throw new SQLException($insert->errorInfo()[2]);
        }

        public function createContentForArticle($articleId, $contents) {
            $date = $this->now();
            $insert = $this->con->prepare("
                INSERT INTO
                    content (articleId, createDate, heading, content, languageId)
                VALUES
                    (:aId, :date, :heading, :content, :lId)
            ");
            foreach($contents as $content) {
                $insert->bindParam(':aId', $articleId);
                $insert->bindParam(':date', $date);
                $insert->bindParam(':heading', $content['contentHeading']);
                $insert->bindParam(':content', $content['content']);
                $insert->bindParam(':lId', $content['languageId']);
                if(!$insert->execute()) {
                    throw new SQLException($insert->errorInfo()[2]);
                }
                $insert->closeCursor();
            }
        }

        public function getEntry($articleId) {
            $selectArticle = $this->con->prepare("
                SELECT
                    articleId, status, heading
                FROM
                    articles
                WHERE
                    articleId=:aid
            ");
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
                throw new SQLException($selectContent->errorInfo()[2]);
            }
            throw new SQLException($selectArticle->errorInfo()[2]);
        }

        public function getLanguages() {
            $select = $this->con->query("
                SELECT
                    languageId, language, icon
                FROM
                    languages
            ");
            if($select) {
                $langs = $select->fetchAll();
                return $langs;
            }
            throw new SQLException($select->errorInfo()[2]);
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
            throw new SQLException($select->errorInfo()[2]);
        }

        public function getArticles() {
            $select = $this->con->query("
                SELECT
                    articleId, status, createDate, userId, heading
                FROM
                    articles
            ");
            if($select) {
                $articles = $select->fetchAll();
                return $articles;
            }
            throw new SQLException($select->errorInfo()[2]);
        }

        public function getPublishedArticles($offset, $limit, $order) {
            $stmt = "
                SELECT
                    articleId, status, createDate, userId, heading
                FROM
                    articles
                WHERE
                    status = 'published'
                ORDER BY
                    createDate
            ";
            if($order) {
                $stmt = $stmt." DESC ";
            }
            $stmt = $stmt."LIMIT :offset,:limit";
            $select = $this->con->prepare($stmt);
            $select->bindParam(':offset', $offset, PDO::PARAM_INT);
            $select->bindParam(':limit', $limit, PDO::PARAM_INT);
            if($select->execute()) {
                $articles = $select->fetchAll();
                return $articles;
            }
            throw new SQLException($select->errorInfo()[2]);
        }

        public function getPublishedArticlesByCategory($categoryId, $offset, $limit, $order) {
            $stmt = "
                SELECT
                    a.articleId, a.createDate, a.userId, a.heading
                FROM
                    articles a
                JOIN
                    articlecategories ac
                ON
                    a.articleId = ac.articleId
                WHERE
                    ac.categoryId = :categoryId
                AND
                    a.status = 'published'
            ";
            if($order) {
                $stmt = $stmt." DESC ";
            }
            $stmt = $stmt."LIMIT :offset,:limit";
            $select = $this->con->prepare($stmt);
            $select->bindParam(':categoryId', $categoryId);
            $select->bindParam(':offset', $offset, PDO::PARAM_INT);
            $select->bindParam(':limit', $limit, PDO::PARAM_INT);
            if(!$select->execute()) {
                throw new SQLException($select->errorInfo()[2]);
            }
            $articles = $select->fetchAll();
            return $articles;
        }

        public function getPosts($languageId, $offset, $limit, $direction, $category) {
            if($category) {
                $articles = $this->getPublishedArticlesByCategory($category, $offset, $limit, $direction);
            } else {
                $articles = $this->getPublishedArticles($offset, $limit, $direction);
            }
            $selectContent = $this->con->prepare("
                SELECT
                    c.contentId, c.heading, c.content
                FROM
                    content as c
                WHERE
                    c.articleId=:aid
                AND
                    c.languageId=:languageId
            ");
            foreach($articles as &$article) {
                $selectContent->bindParam(':aid', $article['articleId']);
                $selectContent->bindParam(':languageId', $languageId);
                if(!$selectContent->execute()) {
                    throw new SQLException($selectContent->errorInfo()[2]);
                }
                $content = $selectContent->fetch(PDO::FETCH_ASSOC);
                if($content) {
                    $article['content'] = $content;
                }
                $selectContent->closeCursor();
            }
            return $articles;
        }

        public function getCategories() {
            $select = $this->con->query("
                SELECT
                    categoryId, categoryName, parentId
                FROM
                    categories
            ");
            if($select){
                $categories = $select->fetchAll();
                return $categories;
            }
            throw new SQLException($select->errorInfo()[2]);
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
            throw new SQLException($select->errorInfo()[2]);
        }

        public function linkCategoriesToArticle($categories, $articleId) {
            $insert = $this->con->prepare("
                INSERT INTO
                    articlecategories
                    (articleId, categoryId)
                VALUES
                    (:aId, :cId)
            ");
            foreach($categories as $category) {
                $insert->bindParam(':aId', $articleId);
                $insert->bindParam(':cId', $category);
                if(!$insert->execute()) {
                    throw new SQLException($insert->errorInfo()[2]);
                }
                $insert->closeCursor();
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
                    throw new SQLException($delete->errorInfo()[2]);
                }
                $delete->closeCursor();
            }
            return true;
        }

        public function updateArticle($articleId, $contents, $status) {
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
            $updateArticle = $this->con->prepare("
                UPDATE
                    articles
                SET
                    status=:status
                WHERE
                    articleId=:aId
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
                        throw new SQLException($updateContent->errorInfo()[2]);
                    }
                    $updateContent->closeCursor();
                } else if($content['save'] == 'on') {
                    $insert->bindParam(':aId', $articleId);
                    $insert->bindParam(':date', $date);
                    $insert->bindParam(':heading', $content['contentHeading']);
                    $insert->bindParam(':content', $content['content']);
                    $insert->bindParam(':lId', $content['languageId']);
                    if(!$insert->execute()) {
                        throw new SQLException($insert->errorInfo()[2]);
                    }
                    $insert->closeCursor();
                }
            }
            $updateArticle->bindParam(':status', $status);
            $updateArticle->bindParam(':aId', $articleId);
            if(!$updateArticle->execute()) {
                throw new SQLException($updateArticle->errorInfo()[2]);
            }
            $updateArticle->closeCursor();
        }

        public function getSettings() {
            $select = $this->con->query("
                SELECT
                    settingId, name, value, display
                FROM
                    settings
            ");
            if($select) {
                $settings = $select->fetchAll();
                return $settings;
            }
            throw new SQLException($select->errorInfo()[2]);
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
                if(!$update->execute()) {
                    throw new SQLException($update->errorInfo()[2]);
                }
            }
        }
    }
?>

