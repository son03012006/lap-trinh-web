<?php

class ManagerbookModel
{
    private $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    /* LẤY DANH SÁCH SÁCH */
    public function getAll()
    {
        $sql = "
            SELECT 
                B.id,
                B.BookName,
                B.Copies,
                B.IssuedCopies,
                B.ISBNNumber,
                B.BookPrice,
                C.CategoryName,
                A.AuthorName
            FROM tblbooks B
            JOIN tblcategory C ON C.id = B.CatId
            JOIN tblauthors A ON A.id = B.AuthorId
            ORDER BY B.id DESC
        ";

        return $this->dbh->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    /* XÓA SÁCH */
    public function delete($id)
    {
        $sql = "DELETE FROM tblbooks WHERE id = :id";
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
