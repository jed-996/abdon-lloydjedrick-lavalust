<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProductController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->call->library('session');
        $this->call->helper(['url', 'product']);
        product_require_login();
        header('Cache-Control: no-store');
        $this->call->database();
        $this->call->model('ProductModel');
    }

    public function index() {
        $products = $this->ProductModel->listing();
        $notice = $_SESSION['product_notice'] ?? '';
        unset($_SESSION['product_notice']);
        $this->call->view('products/index', compact('products', 'notice'));
    }

    public function database_evidence() {
        $products = $this->ProductModel->listing();
        $columns = $this->ProductModel->columns();
        $database = $this->ProductModel->database_identity();
        $this->call->view('products/database_evidence', compact('products', 'columns', 'database'));
    }

    private function find_product($id) {
        if (!ctype_digit((string) $id) || (float) $id > 2147483647 || !($product = $this->ProductModel->find((int) $id))) {
            http_response_code(404);
            $this->call->view('products/not_found');
            exit;
        }
        return $product;
    }

    public function create() {
        $this->form(['product_name' => '', 'description' => '', 'price' => '', 'quantity' => '0']);
    }

    public function edit($id) {
        $this->form($this->find_product($id));
    }

    private function form(array $product) {
        $editing = isset($product['id']);
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            product_verify_csrf();
            $data = [];
            foreach (['product_name', 'description', 'price', 'quantity'] as $field) {
                $data[$field] = product_input($field);
            }
            $errors = $this->ProductModel->validate($data);
            if (!$errors) {
                if ($editing) {
                    $this->ProductModel->update($product['id'], $data);
                } else {
                    $this->ProductModel->insert($data);
                }
                $_SESSION['product_notice'] = $editing ? 'Product updated successfully.' : 'Product added successfully.';
                product_redirect('products');
            }
            http_response_code(422);
            $product = array_merge($product, $data);
        }
        $this->call->view('products/form', compact('product', 'editing', 'errors'));
    }

    public function delete($id) {
        $product = $this->find_product($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            product_verify_csrf();
            $this->ProductModel->delete($product['id']);
            $_SESSION['product_notice'] = 'Product deleted successfully.';
            product_redirect('products');
        }
        $this->call->view('products/delete', compact('product'));
    }
}
