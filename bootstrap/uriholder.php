<?php

/**
 * HTTP request URI (without query parameters）.
 *
 * @var string
 */
$requestUri = preg_replace(['/\?.*/', '/#.*/'], '', $_SERVER['REQUEST_URI']);

/**
 * HTTP request method.
 *
 * @var string
 */
$requestMethod = $_SERVER['REQUEST_METHOD'];

# Disable .php extension name for request
if (preg_match('/.+.php$/', $requestUri))
{
    header('HTTP/1.1 404 Not Found');
    exit;
}
# Remove trailing slash
else if (preg_match('/\/$/', $requestUri))
{
    $requestUri = rtrim($requestUri, '/');
}
