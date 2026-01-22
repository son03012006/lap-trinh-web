<?php

class AdminDashboardModel
{
    private $db;

    public function __construct($dbh)
    {
        $this->db = $dbh;
    }

    public function countBooks()
    {
        return $this->db->query("SELECT id FROM tblbooks")->rowCount();
    }

    public function countBorrow()
    {
        return $this->db->query("SELECT id FROM tblissuedbookdetails")->rowCount();
    }

    public function countReturned()
    {
        return $this->db->query("
            SELECT id FROM tblissuedbookdetails WHERE ReturnStatus = 1
        ")->rowCount();
    }

    public function countUsers()
    {
        return $this->db->query("SELECT id FROM tblstudents")->rowCount();
    }

    public function topBooks()
    {
        $sql = "
            SELECT b.BookName, COUNT(i.BookId) AS total
            FROM tblissuedbookdetails i
            JOIN tblbooks b ON b.id = i.BookId
            GROUP BY i.BookId
            ORDER BY total DESC
            LIMIT 5
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    public function overdueBooks()
    {
        $sql = "
            SELECT b.BookName, s.FullName,
                   DATEDIFF(NOW(), i.DueDate) AS late_days
            FROM tblissuedbookdetails i
            JOIN tblbooks b ON b.id = i.BookId
            JOIN tblstudents s ON s.StudentId = i.StudentId
            WHERE i.ReturnStatus = 0
            AND i.DueDate IS NOT NULL
            AND i.DueDate < NOW()
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    public function borrowByMonth()
    {
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $this->db->query("
                SELECT id FROM tblissuedbookdetails 
                WHERE MONTH(IssuesDate) = $i
            ")->rowCount();
        }
        return $data;
    }

    public function returnByMonth()
    {
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $this->db->query("
                SELECT id FROM tblissuedbookdetails
                WHERE MONTH(ReturnDate) = $i AND ReturnStatus = 1
            ")->rowCount();
        }
        return $data;
    }
    public function getTopBooks()
{
    $sql = "
        SELECT b.BookName, COUNT(i.BookId) AS total
        FROM tblissuedbookdetails i
        JOIN tblbooks b ON b.id = i.BookId
        GROUP BY i.BookId
        ORDER BY total DESC
        LIMIT 5
    ";
    return $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);
}
    public function getOverdueBooks()
{
    $sql = "
        SELECT b.BookName, s.FullName,
               DATEDIFF(NOW(), i.DueDate) AS late_days
        FROM tblissuedbookdetails i
        JOIN tblbooks b ON b.id = i.BookId
        JOIN tblstudents s ON s.StudentId = i.StudentId
        WHERE i.ReturnStatus = 0
          AND i.DueDate IS NOT NULL
          AND i.DueDate < NOW()
    ";
    return $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);
}

}
