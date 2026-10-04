<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiProductController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('ProductModel');
        $this->call->library('api');
        header('Cache-Control: no-store');
    }

    public function index()
    {
        $this->authorize();
        $this->api->respond(['products' => array_map([$this, 'serialize_product'], $this->ProductModel->listing())]);
    }

    public function show($id)
    {
        $this->authorize();
        $this->api->respond(['product' => $this->serialize_product($this->find_product($id))]);
    }

    public function create()
    {
        $this->authorize(true);
        $data = $this->product_data($this->api->body());
        $errors = $this->ProductModel->validate($data);
        if ($errors) {
            $this->api->respond(['error' => 'Please correct the highlighted fields.', 'errors' => $errors], 422);
        }

        $id = $this->ProductModel->insert($data);
        $this->api->respond([
            'message' => 'Product added successfully.',
            'product' => $this->serialize_product($this->ProductModel->find((int) $id)),
        ], 201);
    }

    public function update($id)
    {
        $this->authorize(true);
        $product = $this->find_product($id);
        $input = $this->api->body();
        $data = $this->product_data(array_merge($product, $input));
        $errors = $this->ProductModel->validate($data);
        if ($errors) {
            $this->api->respond(['error' => 'Please correct the highlighted fields.', 'errors' => $errors], 422);
        }

        $this->ProductModel->update((int) $product['id'], $data);
        $this->api->respond([
            'message' => 'Product updated successfully.',
            'product' => $this->serialize_product($this->ProductModel->find((int) $product['id'])),
        ]);
    }

    public function delete($id)
    {
        $this->authorize(true);
        $product = $this->find_product($id);
        $this->ProductModel->delete((int) $product['id']);
        $this->api->respond(['message' => 'Product deleted successfully.']);
    }

    private function authorize(bool $write = false): array
    {
        $payload = $this->api->require_jwt();
        $required = $write ? 'products:write' : 'products:read';
        if (!in_array($required, $payload['scopes'] ?? [], true)) {
            $this->api->respond_error('Forbidden', 403);
        }
        return $payload;
    }

    private function find_product($id): array
    {
        if (!ctype_digit((string) $id) || (float) $id > 2147483647) {
            $this->api->respond_error('Product not found.', 404);
        }
        $product = $this->ProductModel->find((int) $id);
        if (!$product) {
            $this->api->respond_error('Product not found.', 404);
        }
        return $product;
    }

    private function product_data(array $input): array
    {
        $data = [];
        foreach (['product_name', 'description', 'price', 'quantity'] as $field) {
            $value = $input[$field] ?? '';
            $data[$field] = trim(html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8'));
        }
        return $data;
    }

    private function serialize_product(array $product): array
    {
        return [
            'id' => (int) $product['id'],
            'product_name' => $product['product_name'],
            'description' => $product['description'],
            'price' => number_format((float) $product['price'], 2, '.', ''),
            'quantity' => (int) $product['quantity'],
            'created_at' => $product['created_at'] ?? null,
        ];
    }
}
