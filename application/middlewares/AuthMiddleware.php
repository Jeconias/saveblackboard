<?php

namespace application\middlewares;

class AuthMiddleware
{
    public function __invoke($request, $response, $next)
    {
        if (!isset($_SESSION['usuario'])) {
            return $response->withRedirect(PATH . '/login');
        }
        $response = $next($request, $response);
        return $response;
    }
}
