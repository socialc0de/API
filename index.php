<?php

        const BASE_PATH = '/api/';

try {
    //remove the base path
    $path = str_replace(BASE_PATH, '', $_SERVER['REQUEST_URI']);
    //split the url in path elements
    $url_elements = explode('/', $path);

    //the first parameter is used as controller
    $controller_name = @array_shift($url_elements);
    if (preg_match('/[^a-z]{3,}/i', $controller_name)) {
        //prevent path transversial
        throw new Exception("Invalid path", 400);
    }

    $requestMethod = strtolower($_SERVER['REQUEST_METHOD']) . 'Action';
    //load the file
    loadController($controller_name);

    $controller_class = $controller_name . 'Controller';
    $controller = new $controller_class();

    if (!method_exists($controller, $requestMethod)) {
        throw new Exception("Invalid request method", 400);
    }

    $controller->$requestMethod($url_elements);
} catch (Exception $ex) {
    http_response_code($ex->getCode());
    //provide the message in json fromat in order to make it readable for the client
    die(json_encode($ex->getMessage()));
}

function loadController($controller_name) {
    $path = __DIR__ . "/controller/" . $controller_name . ".php";
    if (file_exists($path)) {
        include $path;
    } else {
        throw new Exception("Controller does not exist", 404);
    }
}
