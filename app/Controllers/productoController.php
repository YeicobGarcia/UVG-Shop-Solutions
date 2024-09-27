<?php

require_once __DIR__ . '/../Models/ConexionDB.php';
require_once __DIR__ . '/../Models/productosModel.php';

class ProductController {
    private $model;

    public function __construct() {
        $this->model = new ProductModel();
    }

    public function showProducts() {
        // Obtenemos los productos desde el modelo
        $productos = $this->model->getProducts();

        // Verificamos que se obtuvieron productos
        if ($productos) {
            // Devolvemos los productos para ser usados en la vista
            return $productos;
        } else {
            // En caso de error
            return null;
        }
    }
}

// Crear una instancia del controlador y obtener productos
$productController = new ProductController();
$productos = $productController->showProducts();

?>
