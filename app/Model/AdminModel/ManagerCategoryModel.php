<?php

class ManagerCategoryModel
{
    private $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM tblcategory ORDER BY id DESC";
        return $this->dbh->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM tblcategory WHERE id=:id";
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
