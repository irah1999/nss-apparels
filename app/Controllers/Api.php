<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Api extends BaseController
{
    public function categories()
    {
        $categoryModel = new \App\Models\CategoryModel();
        $productModel = new \App\Models\ProductModel();

        $categories = $categoryModel->where('status', 'active')->findAll();
        foreach ($categories as &$cat) {
            $cat['products'] = $productModel->where('category_id', $cat['id'])->where('status', 'active')->limit(8)->findAll();
            // Prefix base_url to main image if stored as relative path
            if ($cat['image']) {
                $cat['image'] = base_url($cat['image']);
            }
            foreach ($cat['products'] as &$product) {
                if ($product['main_image']) {
                    $product['main_image'] = base_url($product['main_image']);
                }
                if (!empty($product['additional_images'])) {
                    $images = json_decode((string)$product['additional_images'], true);
                    if (is_array($images)) {
                        $images = array_map(function($img) {
                            return is_string($img) ? base_url($img) : $img;
                        }, $images);
                        $product['additional_images'] = json_encode($images);
                    }
                }
            }
        }

        return $this->response->setJSON($categories);
    }

    public function categories_paginated()
    {
        $categoryModel = new \App\Models\CategoryModel();
        
        $search = $this->request->getVar('search');
        $page = (int)$this->request->getVar('page') ?: 1;
        $limit = (int)$this->request->getVar('limit') ?: 10;
        
        $offset = ($page - 1) * $limit;
        
        $builder = clone $categoryModel;
        $builder->where('status', 'active');
        
        if ($search) {
            $builder->like('name', $search);
        }
        
        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults(false);
        
        $builder->orderBy('id', 'ASC'); 
        $categories = $builder->findAll($limit, $offset);
        
        $productModel = new \App\Models\ProductModel();
        foreach ($categories as &$cat) {
            // Include just 1 product for the thumbnail preview per category, to remain fast.
            $cat['products'] = $productModel->where('category_id', $cat['id'])->where('status', 'active')->limit(1)->findAll();
            if ($cat['image']) {
                $cat['image'] = base_url($cat['image']);
            }
            foreach ($cat['products'] as &$product) {
                if ($product['main_image']) {
                    $product['main_image'] = base_url($product['main_image']);
                }
            }
        }
        
        return $this->response->setJSON([
            'data' => $categories,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'has_more' => ($offset + count($categories)) < $total
        ]);
    }


    public function products()
    {
        $productModel = new \App\Models\ProductModel();
        
        $categoryId = $this->request->getVar('category_id');
        $search = $this->request->getVar('search');
        $sort = $this->request->getVar('sort'); // e.g., 'recent'
        $page = (int)$this->request->getVar('page') ?: 1;
        $limit = (int)$this->request->getVar('limit') ?: 10;
        
        $offset = ($page - 1) * $limit;
        
        $builder = clone $productModel;
        $builder->where('status', 'active');
        
        if ($categoryId) {
            $builder->where('category_id', $categoryId);
        }
        
        if ($search) {
            $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('description', $search)
                    ->orLike('item_code', $search)
                    ->orLike('fabric', $search)
                    ->groupEnd();
        }
        
        // Clone for count before order and limit
        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults(false);
        
        if ($sort === 'recent') {
            $builder->orderBy('id', 'DESC');
        } else {
            $builder->orderBy('id', 'ASC'); 
        }
        
        $products = $builder->findAll($limit, $offset);
        
        foreach ($products as &$product) {
            if ($product['main_image']) {
                $product['main_image'] = base_url($product['main_image']);
            }
            if (!empty($product['additional_images'])) {
                $images = json_decode((string)$product['additional_images'], true);
                if (is_array($images)) {
                    $images = array_map(function($img) {
                        return is_string($img) ? base_url($img) : $img;
                    }, $images);
                    $product['additional_images'] = json_encode($images);
                }
            }
        }
        
        return $this->response->setJSON([
            'data' => $products,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'has_more' => ($offset + count($products)) < $total
        ]);
    }
    
    public function product($id)
    {
        $productModel = new \App\Models\ProductModel();
        $categoryModel = new \App\Models\CategoryModel();
        
        $product = $productModel->where('status', 'active')->find($id);
        if (!$product) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Not found']);
        }
        
        if ($product['main_image']) {
            $product['main_image'] = base_url($product['main_image']);
        }

        if (!empty($product['additional_images'])) {
            $images = json_decode((string)$product['additional_images'], true);
            if (is_array($images)) {
                $images = array_map(function($img) {
                    return is_string($img) ? base_url($img) : $img;
                }, $images);
                $product['additional_images'] = json_encode($images);
            }
        }
        
        $category = $categoryModel->find($product['category_id']);
        
        return $this->response->setJSON([
            'product' => $product,
            'category' => $category
        ]);
    }
}
