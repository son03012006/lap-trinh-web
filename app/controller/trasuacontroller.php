<?php

require_once 'app/Model/trasuamodel.php';

class trasuacontroller
{
    private $model;

    public function __construct()
    {
        global $dbh;
        $this->model = new trasuamodel($dbh);
    }

    /**
     * Trang chính:
     * - All
     * - Trà sữa
     * - Trà trái cây
     * - Đá xay
     * - Cà phê
     */
    public function index()
    {
        $cat = $_GET['cat'] ?? 'all';

        switch ($cat) {

            // ===== TRANG ALL (LANDING) =====
            case 'all':
                $bestSellers = $this->model->getBestSellers();
                $products    = $this->model->getAllProducts();

                require 'app/View/milktea/all.php';
                break;

            // ===== MENU TRÀ SỮA =====
            case 'trasua':
                $products = $this->model->getByCategory('trasua');

                require 'app/View/milktea/trasua.php';
                break;

            // ===== MENU TRÀ TRÁI CÂY =====
            case 'tratraicay':
                $products = $this->model->getByCategory('tratraicay');

                require 'app/View/milktea/tratraicay.php';
                break;

            // ===== MENU ĐÁ XAY =====
            case 'daxay':
                $products = $this->model->getByCategory('daxay');

                require 'app/View/milktea/daxay.php';
                break;

            // ===== MENU CÀ PHÊ =====
            case 'cafe':
                $products = $this->model->getByCategory('cafe');

                require 'app/View/milktea/cafe.php';
                break;

            // ===== KHÔNG TỒN TẠI =====
            default:
                require 'app/View/milktea/all.php';
        }
    }
}
