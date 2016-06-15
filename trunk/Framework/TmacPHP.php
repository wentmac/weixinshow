<?php

/**
 * Power By Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: TmacPHP.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org； 
 */
//加载配置文件
require(TMAC_BASE_PATH . 'Tmac.config.php');

/**
 * 自动加载类
 *
 * @param string $class
 */
function _tmac_autoload($class)
{
    if (($appNamePos = strrpos($class, '_')) > 0) {
        // Find the class directory
        $appName = substr($class, $appNamePos + 1);
        $className = substr($class, 0, $appNamePos);
        $classPath = TMAC_BASE_PATH . $appName . DIRECTORY_SEPARATOR . APPLICATION . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR;
        if (strpos($className, '_') === false) {
            $classPath .= $className;
        } else {//如果className中含有“_”则是有目录级的，替换成目录分隔符
            $classPath .= str_replace('_', DIRECTORY_SEPARATOR, $className);
        }
        $classPath .= '.class.php';
    } else {
        $classPath = TMAC_PATH . 'Plugin' . DIRECTORY_SEPARATOR . $class . '.class.php';
        if (!is_file($classPath)) {
            $classPath = APPLICATION_ROOT . 'Plugin' . DIRECTORY_SEPARATOR . $class . '.class.php';
        }
    }
    if (is_file($classPath)) {
        require $classPath;
    } else {
        return false;
    }
}

spl_autoload_register('_tmac_autoload');

/**
 * 编译~runtime.php文件
 */
function compiled_runtime_file($runtime)
{
    // 读取要编译的core文件列表
    $list = array(
        TMAC_PATH . 'Action.class.php',
        TMAC_PATH . 'Base.class.php',
        TMAC_PATH . 'Controller.class.php',
        TMAC_PATH . 'Debug.class.php',
        TMAC_PATH . 'HttpRequest.class.php',
        TMAC_PATH . 'HttpResponse.class.php',
        TMAC_PATH . 'Model.class.php',
        TMAC_PATH . 'SessionDb.class.php',
        TMAC_PATH . 'Template.class.php',
        TMAC_PATH . 'Tmac.class.php',
        TMAC_PATH . 'TmacException.class.php',
        TMAC_PATH . 'Log.class.php',
        TMAC_PATH . 'Cache' . DIRECTORY_SEPARATOR . 'Cache.class.php',
        TMAC_PATH . 'Cache' . DIRECTORY_SEPARATOR . 'CacheDriver.class.php',
        TMAC_PATH . 'Database' . DIRECTORY_SEPARATOR . 'Database.class.php',
        TMAC_PATH . 'Database' . DIRECTORY_SEPARATOR . 'DatabaseDriver.class.php',
    );
    $compiledCode = '';
    // 加载模式文件列表
    foreach ($list as $file) {
        if (is_file($file)) {
            $phpCode = php_strip_whitespace($file);
            $phpCode = substr(trim($phpCode), 5);
            if ('?>' == substr($phpCode, -2))
                $phpCode = substr($phpCode, 0, -2);
            $compiledCode .= $phpCode;
        }
    }

    //生成~runtime.php		
    file_put_contents($runtime, '<?php' . $compiledCode);
    unset($compiledCode, $file, $phpCode);
}

//Tmac虚拟根目录
define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
//Tmac URL 根目录
define('URL', ROOT . ($TmacConfig['Common']['urlrewrite'] ? '' : 'index.php'));
//Tmac URL 根目录
define('PHP_SELF', basename($_SERVER['SCRIPT_NAME']));

$runtime = TMAC_PATH . '~runtime.php';
if (!is_file($runtime)) {
    compiled_runtime_file($runtime);
}
require $runtime;
unset($tmac_path, $tmac_base_path, $include_path, $item, $runtime);