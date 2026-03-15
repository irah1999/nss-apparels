<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Catalog extends BaseController
{
    protected $categoryModel;
    protected $productModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }

    // Admin List View for Categories
    public function categories(): string
    {
        return view('catalog/categories');
    }

    // Ajax List API
    public function categories_list()
    {
        $search = $this->request->getPost('search');
        $limit = $this->request->getPost('limit') ?: 30;
        $offset = $this->request->getPost('offset') ?: 0;
        $orderBy = $this->request->getPost('orderBy') ?: 'id';
        $orderDir = $this->request->getPost('orderDir') ?: 'DESC';

        $builder = $this->categoryModel;

        if ($search) {
            $builder = $builder->like('name', $search);
        }

        $totalRecords = $builder->countAllResults(false);
        $categories = $builder->orderBy($orderBy, $orderDir)->findAll($limit, $offset);

        return $this->response->setJSON([
            'status' => 'success',
            'categories' => $categories,
            'totalRecords' => $totalRecords,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    public function delete_category()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $this->categoryModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Category deleted successfully']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid Request']);
    }

    // Save/Update Category from POST
    public function save_category()
    {
        $id = $this->request->getPost('id');
        $data = [
            'name'   => $this->request->getPost('name'),
            'slug'   => url_title($this->request->getPost('name'), '-', true),
            'status' => $this->request->getPost('status') ?: 'active',
        ];

        // Handle Image upload
        if ($image = $this->request->getFile('image')) {
            if ($image->isValid() && !$image->hasMoved()) {
                $newName = $image->getRandomName();
                $image->move(FCPATH . 'uploads', $newName);
                $data['image'] = 'uploads/' . $newName;
            }
        }

        if ($id) {
            $this->categoryModel->update($id, $data);
            $msg = 'Category updated successfully';
        } else {
            $this->categoryModel->insert($data);
            $msg = 'Category created successfully';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    // Admin List view for Products in Category
    public function products($category_id = null): string
    {
        if ($category_id) {
            $data['products'] = $this->productModel->where('category_id', $category_id)->findAll();
            $data['category'] = $this->categoryModel->find($category_id);
        } else {
            $data['products'] = $this->productModel->findAll();
            $data['category'] = null;
        }
        $data['categories'] = $this->categoryModel->findAll();

        return view('catalog/products', $data);
    }

    // Save/Update Product from POST (supports multi-image gallery)
    public function save_product()
    {
        $id = $this->request->getPost('id');
        $data = [
            'category_id'     => $this->request->getPost('category_id'),
            'name'            => $this->request->getPost('name'),
            'slug'            => url_title($this->request->getPost('name'), '-', true),
            'description'     => $this->request->getPost('description'),
            'colors'          => $this->request->getPost('colors'),
            'sizes'           => $this->request->getPost('sizes'),
            'whatsapp_number' => $this->request->getPost('whatsapp_number'),
            'status'          => $this->request->getPost('status') ?: 'active',
        ];

        // Handle Main Image upload
        if ($main_image = $this->request->getFile('main_image')) {
            if ($main_image->isValid() && !$main_image->hasMoved()) {
                $newName = $main_image->getRandomName();
                $main_image->move(FCPATH . 'uploads', $newName);
                $data['main_image'] = 'uploads/' . $newName;
            }
        }

        // Handle Gallery Images — keep existing ones when editing, append new uploads
        $galleryPaths = [];
        if ($id) {
            $existing = $this->productModel->find($id);
            if ($existing && !empty($existing['additional_images'])) {
                $galleryPaths = json_decode($existing['additional_images'], true) ?: [];
            }
        }

        $galleryFiles = $this->request->getFiles();
        if (!empty($galleryFiles['gallery_images'])) {
            foreach ($galleryFiles['gallery_images'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(FCPATH . 'uploads', $newName);
                    $galleryPaths[] = 'uploads/' . $newName;
                }
            }
        }
        $data['additional_images'] = json_encode($galleryPaths);

        if ($id) {
            $this->productModel->update($id, $data);
            $msg = 'Product updated successfully';
        } else {
            $this->productModel->insert($data);
            $msg = 'Product created successfully';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    // API endpoints to consume list via Front Page or dynamic Ajax
    public function get_catalog()
    {
        $categories = $this->categoryModel->where('status', 'active')->findAll();
        $products = $this->productModel->where('status', 'active')->findAll();

        foreach ($categories as &$cat) {
            $cat['products'] = array_filter($products, function ($p) use ($cat) {
                return $p['category_id'] == $cat['id'];
            });
        }

        return $this->response->setJSON(['status' => 'success', 'categories' => $categories]);
    }
}
