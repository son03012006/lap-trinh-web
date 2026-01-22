<?php

class UserModel
{
    private PDO $db;

    public function __construct(PDO $dbh)
    {
        $this->db = $dbh;
    }

    // tạo user mới
    public function create(string $fullname, string $email, string $password): bool
    {
        $sql = "INSERT INTO users (fullname, email, password)
                VALUES (:fullname, :email, :password)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':fullname' => $fullname,
            ':email'    => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    // tìm user theo email
    public function findByEmail(string $email)
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
