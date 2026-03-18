<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
$routes->get('/', 'Auth::index'); // Redirect home directly to login
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

$routes->get('api/categories', 'Api::categories');
$routes->get('api/categories_paginated', 'Api::categories_paginated');
$routes->get('api/products', 'Api::products');
$routes->get('api/product/(:num)', 'Api::product/$1');

$routes->get('contact', 'Contact::index');
$routes->post('contact/submit', 'Contact::submit');


$routes->group('api/whatsapp', function($routes) {
    $routes->get('webhook', '\App\Controllers\Api\WhatsappWebhook::verify');
    $routes->post('webhook', '\App\Controllers\Api\WhatsappWebhook::receive');
    $routes->get('unread-messages', '\App\Controllers\Api\WhatsappWebhook::getUnreadMessages');
    $routes->get('get-customer/(:num)', '\App\Controllers\Api\WhatsappWebhook::getCustomer/$1');
    $routes->post('mark-read/(:num)', '\App\Controllers\Api\WhatsappWebhook::markRead/$1');
});

// Fallback for direct production layouts 
$routes->get('webhook', '\App\Controllers\Api\WhatsappWebhook::verify');
$routes->post('webhook', '\App\Controllers\Api\WhatsappWebhook::receive');

$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Customers
    $routes->group('customers', function ($routes) {
        $routes->get('/', 'Customers::index');
        $routes->get('chat', 'Customers::chat');
        $routes->post('list', 'Customers::list'); // For DataTable pipeline
        $routes->post('save', 'Customers::save');
        $routes->post('delete', 'Customers::delete');
        $routes->post('export', 'Customers::export');
        $routes->get('export-progress/(:any)', 'Customers::getExportProgress/$1');
        $routes->get('download-sample', 'Customers::downloadSample');
        $routes->post('import', 'Customers::import');
        $routes->get('get-imports', 'Customers::getImports');
        $routes->get('download-import/(:num)', 'Customers::downloadImportFile/$1');
        $routes->post('send-whatsapp', 'Customers::sendWhatsapp');
        $routes->post('bulk-whatsapp', 'Customers::bulkWhatsapp');
        $routes->get('history/(:num)', 'Customers::getChatHistory/$1');
        $routes->post('send-chat', 'Customers::sendChat');
    });

    // Templates
    $routes->group('templates', function ($routes) {
        $routes->get('/', 'Templates::index');
        $routes->get('sync', 'Templates::sync');
        $routes->post('create', 'Templates::create');
        $routes->post('edit', 'Templates::edit');
        $routes->get('get-approved', 'Templates::getApproved');
        $routes->post('uploadMedia', 'Templates::uploadMedia');
    });

    // Admin Only
    $routes->group('', ['filter' => 'admin'], function ($routes) {
        // User Management
        $routes->group('users', function ($routes) {
            $routes->get('/', 'Users::index');
            $routes->post('save', 'Users::save');
            $routes->post('delete', 'Users::delete');
        });

        // Product Catalog Management
        $routes->group('catalog', function ($routes) {
            $routes->get('categories', 'Catalog::categories');
            $routes->post('categories_list', 'Catalog::categories_list');
            $routes->post('save_category', 'Catalog::save_category');
            $routes->post('delete_category', 'Catalog::delete_category');

            $routes->get('products', 'Catalog::products');
            $routes->get('products/(:num)', 'Catalog::products/$1');
            $routes->post('products_list', 'Catalog::products_list');
            $routes->post('save_product', 'Catalog::save_product');
            $routes->post('delete_product', 'Catalog::delete_product');
        });

        $routes->get('catalog-json', 'Catalog::get_catalog');

        // Settings
        $routes->get('settings', 'Settings::index');
        $routes->post('settings/save', 'Settings::save');
    });
});

// Public Catalog
$routes->get('products', 'CatalogPublic::index');
$routes->get('products/(:num)', 'CatalogPublic::index/$1');
$routes->get('product-detail/(:num)', 'CatalogPublic::detail/$1');

// Public Webhook (No Filter)
$routes->get('webhook', 'Webhook::verify');
$routes->post('webhook', 'Webhook::receive');
