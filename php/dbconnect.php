<?php
    include_once 'conf.php';
//    $con = new PDO('mysql:host='.HOST.';dbname='.DB, USER, PASS);
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
            $stmt->execute();
            var_dump($stmt);
        }
    }
?>
