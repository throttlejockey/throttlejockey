<?php
/* InMotion Config 
    OC3.Throttlejockey.com > config.php 
    
*/
// HTTP
define( 'HTTP_SERVER',  'https://oc3.throttlejockey.com/' );
// HTTPS
define( 'HTTPS_SERVER', 'https://oc3.throttlejockey.com/' );

// DIR
define( 'DIR_APPLICATION', '/home/b16aa05/oc3.throttlejockey.com/catalog/' );
define( 'DIR_SYSTEM',      '/home/b16aa05/oc3.throttlejockey.com/system/' );
define( 'DIR_IMAGE',       '/home/b16aa05/oc3.throttlejockey.com/image/' );
// define( 'DIR_STORAGE',     '/home/b16aa05/oc3.throttlejockey.com/storage/' );
define('DIR_STORAGE',      '/home/b16aa05/storage/');

define( 'DIR_LANGUAGE',     DIR_APPLICATION . 'language/' );
define( 'DIR_TEMPLATE',     DIR_APPLICATION . 'view/theme/' );
define( 'DIR_CONFIG',       DIR_SYSTEM . 'config/' );
define( 'DIR_CACHE',        DIR_STORAGE . 'cache/' );
define( 'DIR_DOWNLOAD',     DIR_STORAGE . 'download/' );
define( 'DIR_LOGS',         DIR_STORAGE . 'logs/' );
define( 'DIR_MODIFICATION', DIR_STORAGE . 'modification/' );
define( 'DIR_SESSION',      DIR_STORAGE . 'session/' );
define( 'DIR_UPLOAD',       DIR_STORAGE . 'upload/' );

// DB
define('DB_DRIVER',   'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'b16aa05_oc3');
define('DB_PASSWORD', '7QrnANg3l5Ax');
// define('DB_DATABASE', 'b16aa05_oc3');
define('DB_DATABASE', 'b16aa05_oc3_test_pcop22');
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