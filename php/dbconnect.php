<?php
    include_once 'conf.php';

    class Connection {
        public function __construct() {
            $this->con = new PDO('mysql:host='.HOST.';dbname='.DB, USER, PASS);
        }

        private $con;

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
            $date = date('Y-m-d H:i:s', time());
            $insert = $this->con->prepare(
                "INSERT INTO comments (createDate, commentorName, commentorMail, commentorPage, comment) VALUES (:date, :name, :mail, :page, :comment)"
            );
            $select = $this->con->prepare("SELECT commentId FROM comments WHERE createDate=:date");
            $insert->bindParam(':date', $date);
            $insert->bindParam(':name', $commentor);
            $insert->bindParam(':mail', $mail);
            $insert->bindParam(':page', $page);
            $insert->bindParam(':comment', $comment);
            $res = $insert->execute();
            if($res) {
                // successful, so fetch the new comments id
                $insert->closeCursor();
                $select->bindParam(':date', $date);
                if($select->execute()) {
                    $row = $select->fetch(PDO::FETCH_ASSOC);
                    return $row['commentId'];
                }
            }
            // failure
            return $res;
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
    }
?>

