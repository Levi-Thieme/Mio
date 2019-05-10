<?php
/**
 * Created by PhpStorm.
 * User: signo
 * Date: 5/7/2019
 * Time: 11:30 PM
 */

require_once("../database_interface/db.php");

class Manager {
    protected $conn;

    public function construct($databaseConnection) {
        $this->conn = $databaseConnection;
    }
    public function destruct() {
        $this->conn->close();
    }
}