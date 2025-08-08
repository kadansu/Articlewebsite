<?php
require_once 'constants.php';

class Database {
    public $conn;

    public function __construct() {
        $this->conn = new mysqli(HOST_NAME, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }
}
?>
