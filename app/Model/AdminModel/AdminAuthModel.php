<?php

class AdminAuthModel
{
    private $db;

    public function __construct($dbh)
    {
        $this->db = $dbh;
    }

    public function login($username, $password)
    {
        $sql = "SELECT UserName 
                FROM admin 
                WHERE UserName = :username 
                  AND Password = :password";

        $q = $this->db->prepare($sql);
        $q->bindParam(':username', $username);
        $q->bindParam(':password', $password);
        $q->execute();

        return $q->fetch(PDO::FETCH_OBJ);
    }
}
