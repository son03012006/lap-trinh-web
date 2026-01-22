<?php

class ProductModel
{
    private $db;

    public function __construct($dbh)
    {
        $this->db = $dbh;
    }

    /* ================= CATEGORIES ================= */
    public function getCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY name";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    /* ================= COUNT PRODUCTS ================= */
    public function countProducts($filters)
    {
        $sql = "SELECT COUNT(*) FROM products WHERE stock > 0";

        if (!empty($filters['keyword'])) {
            $sql .= " AND name LIKE :kw";
        }

        if (!empty($filters['category'])) {
            $sql .= " AND category_id = :cat";
        }

        $q = $this->db->prepare($sql);

        if (!empty($filters['keyword'])) {
            $q->bindValue(':kw', '%' . $filters['keyword'] . '%');
        }

        if (!empty($filters['category'])) {
            $q->bindValue(':cat', $filters['category'], PDO::PARAM_INT);
        }

        $q->execute();
        return $q->fetchColumn();
    }

    /* ================= GET PRODUCTS ================= */
    public function getProducts($filters, $start, $limit)
    {
        $sql = "
            SELECT p.*, c.name AS category_name
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE p.stock > 0
        ";

        if (!empty($filters['keyword'])) {
            $sql .= " AND p.name LIKE :kw";
        }

        if (!empty($filters['category'])) {
            $sql .= " AND p.category_id = :cat";
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT :start, :limit";

        $q = $this->db->prepare($sql);

        if (!empty($filters['keyword'])) {
            $q->bindValue(':kw', '%' . $filters['keyword'] . '%');
        }

        if (!empty($filters['category'])) {
            $q->bindValue(':cat', $filters['category'], PDO::PARAM_INT);
        }

        $q->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $q->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        $q->execute();
        return $q->fetchAll(PDO::FETCH_OBJ);
    }

    /* ================= BEST SELLERS ================= */
    public function getBestSellers($limit = 4)
    {
        $sql = "
            SELECT * FROM products
            WHERE is_best_seller = 1 AND stock > 0
            ORDER BY created_at DESC
            LIMIT :limit
        ";

        $q = $this->db->prepare($sql);
        $q->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $q->execute();

        return $q->fetchAll(PDO::FETCH_OBJ);
    }

    /* ================= GET PRODUCTS BY CATEGORY ================= */
    public function getProductsByCategoryId($categoryId)
    {
        $sql = "
            SELECT p.*, c.name AS category_name
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE p.category_id = :cid AND p.stock > 0
            ORDER BY p.created_at DESC
        ";

        $q = $this->db->prepare($sql);
        $q->bindValue(':cid', $categoryId, PDO::PARAM_INT);
        $q->execute();

        return $q->fetchAll(PDO::FETCH_OBJ);
    }

    /* ================= SIZE – PHẦN MỚI ================= */

    // Lấy tất cả size của 1 sản phẩm

    // Lấy giá theo size cụ thể (dùng cho cart)
    public function getPriceByProductAndSize($productId, $size)
    {
        $sql = "
            SELECT price
            FROM product_sizes
            WHERE product_id = :pid AND size = :size
            LIMIT 1
        ";

        $q = $this->db->prepare($sql);
        $q->bindValue(':pid', $productId, PDO::PARAM_INT);
        $q->bindValue(':size', $size);
        $q->execute();

        return $q->fetchColumn();
    }
    public function getSizesByProductId($productId)
{
    $sql = "SELECT size, price FROM product_sizes WHERE product_id = :id";
    $q = $this->db->prepare($sql);
    $q->bindValue(':id', $productId, PDO::PARAM_INT);
    $q->execute();
    return $q->fetchAll(PDO::FETCH_ASSOC);
}

}
