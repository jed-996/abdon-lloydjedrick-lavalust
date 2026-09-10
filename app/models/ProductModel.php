<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProductModel extends Model {
    protected $table = 'products';
    protected $fillable = ['product_name', 'description', 'price', 'quantity'];
    protected $timestamps = false;
    protected $has_soft_delete = false;

    public function listing() {
        return $this->db->table($this->table)->order_by('id', 'DESC')->get_all();
    }

    public function columns() {
        return $this->db->raw('SHOW COLUMNS FROM products')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function database_identity() {
        return $this->db->raw('SELECT DATABASE() AS database_name, @@hostname AS server_name, VERSION() AS version')
            ->fetch(PDO::FETCH_ASSOC);
    }

    public function validate(array $data) {
        $errors = [];
        if ($data['product_name'] === '' || mb_strlen($data['product_name']) > 100) {
            $errors['product_name'] = 'Enter a product name of 1 to 100 characters.';
        }
        if (strlen($data['description']) > 65535) {
            $errors['description'] = 'The description is too long (maximum 65,535 bytes).';
        }
        if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/D', $data['price'])) {
            $errors['price'] = 'Enter a price from 0 to 99,999,999.99 with at most two decimal places.';
        }
        if (!preg_match('/^\d{1,10}$/D', $data['quantity']) || (float) $data['quantity'] > 2147483647) {
            $errors['quantity'] = 'Enter a whole quantity from 0 to 2,147,483,647.';
        }
        return $errors;
    }
}
