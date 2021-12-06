<?php
/* InMotion Config 
    public_html > Admin config.php 

*/

// HTTP
define('HTTP_SERVER',  'https://throttlejockey.com/admin/');
define('HTTP_CATALOG', 'https://throttlejockey.com/');

// HTTPS
define('HTTPS_SERVER',  'https://throttlejockey.com/admin/');
define('HTTPS_CATALOG', 'https://throttlejockey.com/');

define('HTTPS_IMAGES',  'https://throttlejockey.com/image/');
define('HTTP_IMAGE',    'https://throttlejockey.com/image/');

// DIR
define('DIR_APPLICATION', '/home/b16aa05/public_html/admin/');
define('DIR_SYSTEM',      '/home/b16aa05/public_html/system/');
define('DIR_IMAGE',       '/home/b16aa05/public_html/image/');
// define('DIR_STORAGE', '/home/b16aa05/public_html/storage/');
define('DIR_STORAGE',     '/home/b16aa05/storage/');
define('DIR_CATALOG',     '/home/b16aa05/public_html/catalog/');
define('DIR_LANGUAGE',     DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE',     DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG',       DIR_SYSTEM  . 'config/');
define('DIR_CACHE',        DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD',     DIR_STORAGE . 'download/');
define('DIR_LOGS',         DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION',      DIR_STORAGE . 'session/');
define('DIR_UPLOAD',       DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER',   'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'b16aa05_oc3');
define('DB_PASSWORD', '7QrnANg3l5Ax');
// define('DB_DATABASE', 'b16aa05_oc3');
define('DB_DATABASE', 'b16aa05_oc3');
// define('DB_DATABASE', 'b16aa05_oc3catfix');
define('DB_PORT',     '3306');
define('DB_PREFIX',   'oc_');


// DB
// define('DB_DRIVER', 'mysqli');
// define('DB_HOSTNAME', 'localhost');
// define('DB_USERNAME', 'b16aa05_ocar800');
// define('DB_PASSWORD', '2Sp8F4!H@8');
// define('DB_DATABASE', 'b16aa05_ocar800');
// define('DB_PORT', '3306');
// define('DB_PREFIX', 'ochu_');


// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');
