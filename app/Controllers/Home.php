<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Home extends BaseController
{
    public function index(): string
    {
        $categoryModel = new \App\Models\CategoryModel();
        $productModel = new \App\Models\ProductModel();

        // Fetch categories and their children products
        $categories = $categoryModel->where('status', 'active')->findAll();
        foreach ($categories as &$cat) {
            $cat['products'] = $productModel->where('category_id', $cat['id'])->where('status', 'active')->limit(8)->findAll();
        }

        $data['categories'] = $categories;

        return view('landing_page', $data);
    }
}
