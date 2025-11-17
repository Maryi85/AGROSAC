<?php

if (!function_exists('route_prefix')) {
    /**
     * Get the route prefix based on the current route
     */
    function route_prefix(): string
    {
        $route = request()->route();
        if (!$route) {
            return 'admin.';
        }
        
        $routeName = $route->getName();
        if (str_starts_with($routeName, 'foreman.')) {
            return 'foreman.';
        }
        
        return 'admin.';
    }
}



