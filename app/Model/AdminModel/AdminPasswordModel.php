<?php

class AdminPasswordModel
{
    private $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    public function checkPassword($username, $password)
    {
        $sql = "SELECT Password FROM admin 
                WHERE UserName = :username AND Password = :password";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':password' => $password
        ]);
        return $stmt->rowCount() > 0;
    }

    public function updatePassword($username, $newpassword)
    {
        $sql = "UPDATE admin 
                SET Password = :newpassword 
                WHERE UserName = :username";
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute([
            ':username' => $username,
            ':newpassword' => $newpassword
        ]);
    }
}
