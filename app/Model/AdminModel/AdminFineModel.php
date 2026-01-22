<?php

class AdminFineModel
{
    private $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    /* LẤY MỨC PHẠT HIỆN TẠI */
    public function getFine()
    {
        $sql = "SELECT fine FROM tblfine LIMIT 1";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /* CẬP NHẬT / THÊM MỨC PHẠT */
    public function updateFine($fine)
    {
        // kiểm tra đã có bản ghi chưa
        $check = $this->dbh->query("SELECT fine FROM tblfine")->rowCount();

        if ($check == 0) {
            $sql = "INSERT INTO tblfine (fine) VALUES (:fine)";
        } else {
            $sql = "UPDATE tblfine SET fine = :fine";
        }

        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute([':fine' => $fine]);
    }
}
