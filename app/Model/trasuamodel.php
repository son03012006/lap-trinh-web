<?php

class trasuamodel
{
    private $db;

    public function __construct($dbh)
    {
        $this->db = $dbh;
    }

    /* ================= ALL PRODUCTS ================= */
    public function getAllProducts()
    {
        $sql = "SELECT * FROM products ORDER BY id DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    /* ================= BEST SELLER ================= */
    public function getBestSellers($limit = 6)
    {
        $sql = "
            SELECT * FROM products
            WHERE is_best_seller = 1
            ORDER BY sold DESC
            LIMIT :limit
        ";
        $q = $this->db->prepare($sql);
        $q->bindValue(':limit', $limit, PDO::PARAM_INT);
        $q->execute();
        return $q->fetchAll(PDO::FETCH_OBJ);
    }

    /* ================= BY CATEGORY ================= */
    public function getByCategory($category)
    {
        $sql = "
            SELECT * FROM products
            WHERE category = :cat
            ORDER BY id DESC
        ";
        $q = $this->db->prepare($sql);
        $q->bindValue(':cat', $category);
        $q->execute();
        return $q->fetchAll(PDO::FETCH_OBJ);
    }
}
