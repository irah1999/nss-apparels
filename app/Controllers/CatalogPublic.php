<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class CatalogPublic extends BaseController
{
    // Public Products list page, optionally byCategory 
    public function index($category_id = null): string
    {
        $categoryModel = new CategoryModel();
        $productModel = new ProductModel();

        if ($category_id) {
            $data['products'] = $productModel->where('category_id', $category_id)->where('status', 'active')->findAll();
            $data['category'] = $categoryModel->find($category_id);
        } else {
            $data['products'] = $productModel->where('status', 'active')->findAll();
            $data['category'] = null;
        }

        $data['categories'] = $categoryModel->where('status', 'active')->findAll();

        return view('frontend/products', $data);
    }

    // Public Product Details page
    public function detail($id): string
    {
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();

        $product = $productModel->find($id);
        if (!$product || $product['status'] !== 'active') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['product'] = $product;
        $data['category'] = $categoryModel->find($product['category_id']);
        $data['related_products'] = $productModel->where('category_id', $product['category_id'])->where('id !=', $id)->where('status', 'active')->limit(4)->findAll();

        return view('frontend/product_detail', $data);
    }
}
