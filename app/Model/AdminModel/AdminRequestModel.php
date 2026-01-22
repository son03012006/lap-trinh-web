<?php

class AdminRequestModel
{
    private $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    /* LẤY DANH SÁCH YÊU CẦU MƯỢN */
    public function getAllRequests()
    {
        $sql = "SELECT * FROM tblrequestedbookdetails ORDER BY id DESC";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
