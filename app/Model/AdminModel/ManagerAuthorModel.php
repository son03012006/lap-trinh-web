<?php

class ManagerAuthorModel
{
    private $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    /* LẤY TẤT CẢ TÁC GIẢ / NXB */
    public function getAll()
    {
        $sql = "SELECT * FROM tblauthors ORDER BY id DESC";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /* XÓA */
    public function delete($id)
    {
        $sql = "DELETE FROM tblauthors WHERE id = :id";
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
