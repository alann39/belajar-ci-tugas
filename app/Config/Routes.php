<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index', ['filter' => 'auth']);

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login', ['filter' => 'redirect']);
$routes->get('logout', 'AuthController::logout');

$routes->get('/catalog', 'Catalog::index', ['filter' => 'auth']);

// Cart Routes
$routes->group('cart', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'Cart::index');
    $routes->post('', 'Cart::cart_add');
    $routes->post('edit', 'Cart::cart_edit');
    $routes->get('delete/(:any)', 'Cart::cart_delete/$1');
    $routes->get('clear', 'Cart::cart_clear');
    $routes->get('count', 'Cart::getCartCount'); // New route for cart count
});

// Checkout Routes
$routes->get('checkout', 'Cart::checkout', ['filter' => 'auth']);
$routes->get('get-location', 'Cart::getLocation', ['filter' => 'auth']);
$routes->get('get-cost', 'Cart::getCost', ['filter' => 'auth']);
$routes->post('buy', 'Cart::buy', ['filter' => 'auth']);

// Product Routes
$routes->group('product', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'Product::index');
    $routes->post('', 'Product::create');
    $routes->post('edit/(:any)', 'Product::edit/$1');
    $routes->get('delete/(:any)', 'Product::delete/$1');
    $routes->get('download', 'Product::download');
});

// Login Routes
$routes->get('/login', 'Login::index', ['filter' => 'auth']);

// Profile Routes
$routes->get('profile', 'Home::profile', ['filter' => 'auth']);
$routes->post('upload-bukti-transfer', 'Home::uploadBuktiTransfer', ['filter' => 'auth']);

//REST API
$routes->resource('api', ['controller' => 'apiController']);

// Alann Routes
$routes->get('/me', 'Me::index');

// Landing Page Routes
$routes->get('/landing', function() {
    $filepath = APPPATH . 'index.html';
    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
        // Fix asset paths untuk CodeIgniter
        $content = str_replace('src="/assets/', 'src="' . base_url('assets/'), $content);
        $content = str_replace('href="/assets/', 'href="' . base_url('assets/'), $content);
        return $content;
    }
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
});

$routes->get('/index.html', function() {
    return redirect()->to(base_url('landing'));
});

// Transaction Routes
$routes->post('transaction/updateStatus/(:num)', 'Cart::updateStatus/$1', ['filter' => 'auth']);
$routes->get('transaction', 'Cart::transaction', ['filter' => 'auth']);
$routes->get('transaction/downloadPDF', 'Cart::downloadPDF', ['filter' => 'auth']);
$routes->get('transaction/downloadExcel', 'Cart::downloadExcel', ['filter' => 'auth']);
