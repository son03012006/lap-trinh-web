<?php

class AdminAuthorModel
{
    private $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    public function create($author)
    {
        $sql = "INSERT INTO tblauthors (AuthorName) VALUES (:author)";
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute([
            ':author' => $author
        ]);
    }
}
