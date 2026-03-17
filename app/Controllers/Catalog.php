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
        $data['category'] = $category_id ? $this->categoryModel->find($category_id) : null;
        $data['category_id'] = $category_id;
        $data['categories'] = $this->categoryModel->where('status', 'active')->findAll();

        return view('catalog/products', $data);
    }

    // Ajax Products List API
    public function products_list()
    {
        $category_id = $this->request->getPost('category_id');
        $search = $this->request->getPost('search');
        $limit = $this->request->getPost('limit') ?: 10;
        $offset = $this->request->getPost('offset') ?: 0;
        $orderBy = $this->request->getPost('orderBy') ?: 'id';
        $orderDir = $this->request->getPost('orderDir') ?: 'DESC';

        $builder = $this->productModel;
        
        if ($category_id) {
            $builder = $builder->where('category_id', $category_id);
        }

        if ($search) {
            $builder = $builder->groupStart()
                               ->like('name', $search)
                               ->orLike('description', $search)
                               ->groupEnd();
        }

        $totalRecords = $builder->countAllResults(false);
        $products = $builder->orderBy($orderBy, $orderDir)->findAll($limit, $offset);

        return $this->response->setJSON([
            'status' => 'success',
            'products' => $products,
            'totalRecords' => $totalRecords,
            'limit' => (int)$limit,
            'offset' => (int)$offset
        ]);
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

        // Handle Gallery Images — compare from post instead of blindly pulling all, to support deleting individual images on save
        $galleryPaths = [];
        if ($id) {
            $existing = $this->productModel->find($id);
            if ($existing) {
                $dbImages = json_decode((string)($existing['additional_images'] ?: '[]'), true) ?: [];
                $postImagesRaw = $this->request->getPost('existing_additional_images');
                
                if ($postImagesRaw !== null) {
                    $postImages = json_decode($postImagesRaw, true) ?: [];
                    
                    // Unlink any image that was in DB but not in current Post list
                    foreach (array_diff($dbImages, $postImages) as $imgToDelete) {
                        if (!empty($imgToDelete) && file_exists(FCPATH . $imgToDelete)) {
                            @unlink(FCPATH . $imgToDelete);
                        }
                    }
                    $galleryPaths = $postImages;
                } else {
                    $galleryPaths = $dbImages; // Guard Added: Keep if post was empty
                }
            }
        }

        if ($imageFiles = $this->request->getFileMultiple('gallery_images')) {
            foreach ($imageFiles as $file) {
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

    public function delete_product()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $product = $this->productModel->find($id);
            if ($product) {
                // Delete Main Image
                if (!empty($product['main_image']) && file_exists(FCPATH . $product['main_image'])) {
                    @unlink(FCPATH . $product['main_image']);
                }

                // Delete Gallery Images
                if (!empty($product['additional_images'])) {
                    $images = json_decode((string)$product['additional_images'], true);
                    if (is_array($images)) {
                        foreach ($images as $img) {
                            if (!empty($img) && file_exists(FCPATH . $img)) {
                                @unlink(FCPATH . $img);
                            }
                        }
                    }
                }

                $this->productModel->delete($id);
                return $this->response->setJSON(['status' => 'success', 'message' => 'Product and related images deleted successfully']);
            }
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid Request']);
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
