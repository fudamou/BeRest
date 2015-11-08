<?php
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    die('require PHP > 5.3.0 !');
}
define('APP_DEBUG', true);
define('APP_PATH', './Uri/');
define('RUNTIME_PATH','./Runtime/');
define('CONF_EXT','.yaml');
define('COMMON_PATH','./Common/');
//THINK_PATH	框架目录
//APP_PATH	    应用目录
//RUNTIME_PATH	应用运行时目录（可写）
//APP_DEBUG	    应用调试模式 （默认为false）
//STORAGE_TYPE	存储类型（默认为File）
//APP_MODE	    应用模式（默认为common）
require './ThinkPHP/ThinkPHP.php';