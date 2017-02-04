<?php
    include_once 'conf.php';

    class Connection {
        public function __construct() {
            $this->con = new PDO('mysql:host='.HOST.';dbname='.DB, USER, PASS);
        }

        private $con;

        public function getActiveUserByMail($mail) {
            $stmt = $this->con->prepare("SELECT userId, userAlias, userPass, salt FROM users WHERE userMail=:mail AND status='active'");
            $stmt->bindParam(':mail', $mail);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        public function createCategory($categoryName, $parentId) {
            $parentId = NULL;
            $stmt = $this->con->prepare("INSERT INTO categories (categoryName, parentId) VALUES (:category_name, :parent_id)");
            $stmt->bindParam(':category_name', $categoryName);
            $stmt->bindParam(':parent_id', $parentId);
            $res = $stmt->execute();
            return $res;
        }

        public function addLanguage($lang, $icon) {
            $stmt = $this->con->prepare("INSERT INTO languages (language, langIcon) VALUES (:lang, :icon)");
            $stmt->bindParam(':lang', $lang);
            $stmt->bindParam(':icon', $icon);
            $res = $stmt->execute();
            return $res;
        }

        public function addUser($alias, $mail, $pass, $salt) {
            $stmt = $this->con->prepare(
                "INSERT INTO users (userAlias, userMail, userPass, status, salt) VALUES (:user, :userMail, :userPass, 'active', :userSalt)"
            );
            $userPass = hash('sha256', $pass . $salt);
            $stmt->bindParam(':user', $alias);
            $stmt->bindParam(':userMail', $mail);
            $stmt->bindParam(':userPass', $userPass);
            $stmt->bindParam(':userSalt', $salt);
            $res = $stmt->execute();
            return $res;
        }

        public function addComment($commentor, $mail, $page, $comment) {
            $stmt = $this->con->prepare(
                "INSERT INTO comments (createDate, commentorName, commentorMail, commentorPage, comment) VALUES (NOW(), :name, :mail, :page, :comment)"
            );
            $stmt->bindParam(':name', $commentor);
            $stmt->bindParam(':mail', $mail);
            $stmt->bindParam(':page', $page);
            $stmt->bindParam(':comment', $comment);
            $res = $stmt->execute();
            return $res;
        }
    }
?>
