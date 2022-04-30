<?php

/**
 * Add routes
 */

// $router->add('', ['controller' => 'install', 'action' => 'index', 'namespace' => 'install']);
// $router->add('{controller}/{action}', ['namespace' => 'install']);
// Route automatically set to get
// $_POST['data'] = [
//     'data' => [
//         "left_thumb_finger_base64" => "",
//         "left_index_finger_base64" => "",
//         "left_middle_finger_base64" => "",
//         "left_ring_finger_base64" => "",
//         "left_little_finger_base64" => "",
//         "right_thumb_finger_base64" => "",
//         "right_index_finger_base64" => "",
//         "right_middle_finger_base64" => "",
//         "right_ring_finger_base64" => "",
//         "right_little_finger_base64" => ""
//     ]
// ];

$router->add('login', ['controller' => 'auth', 'action' => 'login']);
// $router->add('register', ['controller' => 'auth', 'action' => 'register']);

// Test GET Route
$router->add('', ['controller' => 'home', 'action' => 'index'])->get();
// Test POST route
$router->add('form', ['controller' => 'home', 'action' => 'test'])->post();


$router->add('{controller}/{action}')->get();