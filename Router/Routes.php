<?php

/**
 * Add routes
 */
$router->add('login', ['controller' => 'auth', 'action' => 'login']);
// $router->add('register', ['controller' => 'auth', 'action' => 'register']);

// Test GET Route
$router->add('', ['controller' => 'home', 'action' => 'index'])->get();
$router->add('i', ['controller' => 'home', 'action' => 'index'])->get();
// Test POST route
$router->add('form', ['controller' => 'home', 'action' => 'test'])->post();


$router->add('{controller}/{action}')->get();