<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

$routes->get('contact', 'Contact::index');
$routes->post('contact/submit', 'Contact::submit');


$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Customers
    $routes->group('customers', function ($routes) {
        $routes->get('/', 'Customers::index');
        $routes->get('chat', 'Customers::chat');
        $routes->post('list', 'Customers::list'); // For DataTable pipeline
        $routes->post('save', 'Customers::save');
        $routes->post('delete', 'Customers::delete');
        $routes->post('import', 'Customers::import');
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

        // Settings
        $routes->get('settings', 'Settings::index');
        $routes->post('settings/save', 'Settings::save');
    });
});

// Public Webhook (No Filter)
$routes->get('webhook', 'Webhook::verify');
$routes->post('webhook', 'Webhook::receive');
