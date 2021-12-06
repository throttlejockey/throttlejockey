<?php
// HTTP
define('HTTP_SERVER', 'https://oc3.throttlejockey.com/');

// HTTPS
define('HTTPS_SERVER', 'https://oc3.throttlejockey.com/');

// DIR
define('DIR_APPLICATION', '/home1/throttle/oc3.throttlejockey.com/catalog/');
define('DIR_SYSTEM', '/home1/throttle/oc3.throttlejockey.com/system/');
define('DIR_IMAGE', '/home1/throttle/oc3.throttlejockey.com/image/');
define('DIR_STORAGE', '/home1/throttle/storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'throttle_oc3');
define('DB_PASSWORD', 'TJ!23');
define('DB_DATABASE', 'throttle_oc3');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');


// DB :: TEST DATABASE
// define('DB_DRIVER', 'mysqli');
// define('DB_HOSTNAME', 'localhost');
// define('DB_USERNAME', 'throttle_oc3');
// define('DB_PASSWORD', 'TJ!23');
// define('DB_DATABASE', 'throttle_test');
// define('DB_PORT', '3306');
// define('DB_PREFIX', 'oc_');