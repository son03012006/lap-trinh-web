<?php

class AdminaddbookModel
{
    private $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    /* LẤY DANH MỤC */
    public function getCategories()
    {
        $sql = "SELECT * FROM tblcategory WHERE Status = 1";
        return $this->dbh->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    /* LẤY TÁC GIẢ / NXB */
    public function getAuthors()
    {
        $sql = "SELECT * FROM tblauthors ORDER BY AuthorName";
        return $this->dbh->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    /* THÊM SÁCH */
    public function create($data)
    {
        $sql = "INSERT INTO tblbooks
                (BookName, CatId, AuthorId, ISBNNumber, BookPrice, Copies)
                VALUES
                (:bookname, :category, :author, :isbn, :price, :copies)";

        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute($data);
    }
}
