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
            $parentId = NULL;
            $insert = $this->con->prepare("INSERT INTO categories (categoryName, parentId) VALUES (:category_name, :parent_id)");
            $insert->bindParam(':category_name', $categoryName);
            $insert->bindParam(':parent_id', $parentId);
            $res = $insert->execute();
            return $res;
        }

        public function addLanguage($lang, $icon) {
            $insert = $this->con->prepare("INSERT INTO languages (language, langIcon) VALUES (:lang, :icon)");
            $insert->bindParam(':lang', $lang);
            $insert->bindParam(':icon', $icon);
            $res = $insert->execute();
            return $res;
        }

        public function getLanguages() {
            $select = $this->con->prepare("SELECT languageId, language FROM languages");
            $select->execute();
            $langs = $select->fetchAll();
            return $langs;
        }

        public function addUser($alias, $mail, $pass, $salt) {
            $insert = $this->con->prepare(
                "INSERT INTO users (userAlias, userMail, userPass, status, salt) VALUES (:user, :userMail, :userPass, 'active', :userSalt)"
            );
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

        public function createContentForArticle($articleId, $heading, $content, $languageId) {
            $date = $this->now();
            $insert = $this->con->prepare(
                "INSERT INTO content (articleId, createDate, heading, content, languageId) VALUES (:aId, :date, :heading, :content, :lId)"
            );
            $insert->bindParam(':aId', $articleId);
            $insert->bindParam(':date' ,$date);
            $insert->bindParam(':heading', $heading);
            $insert->bindParam(':content', $content);
            $insert->bindParam(':lId', $languageId);
            if($insert->execute()){
                return true;
            } else {
                return false;
            }
        }
    }
?>

