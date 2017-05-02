<?php
    
// Application middleware

    /**
     * For a simple middleware, You can put your middleware here... 
     *
     * Example simple middleware :
     *
     * $middle = function ($request, $response, $next) {
     * $response->getBody()->write('BEFORE');
     * $response = $next($request, $response);
     * $response->getBody()->write('AFTER');
     *
     * return $response;
     * };
     *
     * Then put something like this into your router
     *
     * $app->get('/', function ($request, $response, $args) {
	 * $response->getBody()->write(' Hello ');
     * 
	 * return $response;
     * })->add($middle);
     *
     */ 
