<?php

class CategoryModel
{
    private $db;

    public function __construct($dbh)
    {
        $this->db = $dbh;
    }

    public function exists($name)
    {
        $sql = "SELECT id FROM tblcategory WHERE CategoryName = :name";
        $q = $this->db->prepare($sql);
        $q->bindParam(':name', $name, PDO::PARAM_STR);
        $q->execute();
        return $q->rowCount() > 0;
    }

    public function create($name, $status)
    {
        $sql = "INSERT INTO tblcategory(CategoryName, Status)
                VALUES (:name, :status)";
        $q = $this->db->prepare($sql);
        $q->bindParam(':name', $name, PDO::PARAM_STR);
        $q->bindParam(':status', $status, PDO::PARAM_INT);
        return $q->execute();
    }
}
