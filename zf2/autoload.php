 

自动加载zf
 Zend\Loader\AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true
            )
        ));





$config = array(
    'Zend\Loader\ClassMapAutoloader' => array(
        'application' => APPLICATION_PATH . '/.classmap.php',
        'zf'          => APPLICATION_PATH . '/../library/Zend/.classmap.php',
    ),
    'Zend\Loader\StandardAutoloader' => array(
        'namespaces' => array(
            'Phly\Mustache' => APPLICATION_PATH . '/../library/Phly/Mustache',
            'Doctrine'      => APPLICATION_PATH . '/../library/Doctrine',
        ),
    ),
);

require_once 'Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory($config);



StandardAutoloader  标准加载,允许按照pear和命名空间方式加载

require_once 'Zend/Loader/StandardAutoloader.php';
$loader = new Zend\Loader\StandardAutoloader(array(
    'autoregister_zf' => true,
    'namespaces' => array(
        'Phly' => APPLICATION_PATH . '/../library/Phly',
    ),
    'prefixes' => array(
        'Scapi' => APPLICATION_PATH . '/../library/Scapi',
    ),
    'fallback_autoloader' => true,
));

// Register with spl_autoload:
$loader->register();



require_once 'Zend/Loader/StandardAutoloader.php';
$loader = new Zend\Loader\StandardAutoloader(array('autoregister_zf' => true));

// Register the "Phly" namespace:
$loader->registerNamespace('Phly', APPLICATION_PATH . '/../library/Phly');

// Register the "Scapi" vendor prefix:
$loader->registerPrefix('Scapi', APPLICATION_PATH . '/../library/Scapi');

// Optionally, specify the autoloader as a "fallback" autoloader;
// this is not recommended.
$loader->setFallbackAutoloader(true);

// Register with spl_autoload:
$loader->register();












PluginClassLoader


使用静态地图

use Zend\Loader\PluginClassLoader;

PluginClassLoader::addStaticMap(array(
    'xrequestedfor' => 'My\Http\Header\XRequestedFor',
));


use Zend\Loader\PluginClassLoader;

$loader = new PluginClassLoader();
$class = $loader->load('xrequestedfor'); // My\Http\Header\XRequestedFor


创建一个预加载地图

namespace My\Plugins;

use Zend\Loader\PluginClassLoader;

class PluginLoader extends PluginClassLoader
{
    /**
     * @var array Plugin map
     */
    protected $plugins = array(
        'foo'    => 'My\Plugins\Foo',
        'bar'    => 'My\Plugins\Bar',
        'foobar' => 'My\Plugins\FooBar',
    );
}


$loader = new My\Plugins\PluginLoader();
$class  = $loader->load('foobar'); // My\Plugins\FooBar





ClassMapAutoloader  可以从php文件加载,也可以从phar://路径加载

php classmap_generator.php Some/Directory/  可以根据目录生成地图

require_once 'Zend/Loader/ClassMapAutoloader.php';
$loader = new Zend\Loader\ClassMapAutoloader();

// 注册类地图
$loader->registerAutoloadMap('Some/Directory/autoload_classmap.php');

// 注册自动加载
$loader->register();


$config = array(
    __DIR__ . '/library/autoloader_classmap.php', // file-based class map
    array(                              // array class map
        'Application\Bootstrap' => __DIR__ . '/application/Bootstrap.php',
        'Test\Bootstrap'        => __DIR__ . '/tests/Bootstrap.php',
    ),
);


// 构造函数加载配置
$loader = new Zend\Loader\ClassMapAutoloader($config);

// 设置选项
$loader = new Zend\Loader\ClassMapAutoloader();
$loader->setOptions($config);

// 注册自动加载地图
$loader = new Zend\Loader\ClassMapAutoloader();
$loader->registerAutoloadMaps($config);


//使用一个插件地图扩展插件地图

namespace My\Plugins;

use Zend\Loader\PluginClassLoader;
use Zend\Http\HeaderLoader;

class PluginLoader extends PluginClassLoader
{
    /**
     * @var array Plugin map
     */
    protected $plugins = array(
        'foo'    => 'My\Plugins\Foo',
        'bar'    => 'My\Plugins\Bar',
        'foobar' => 'My\Plugins\FooBar',
    );
}

// Inject in constructor:
$loader = new HeaderLoader('My\Plugins\PluginLoader');
$loader = new HeaderLoader(new PluginLoader());

// Or via registerPlugins():
$loader->registerPlugins('My\Plugins\PluginLoader');
$loader->registerPlugins(new PluginLoader());







ModuleAutoloader


 $moduleAutoloader            = new ModuleAutoloader($options->getModulePaths());



$options = array(
    '/path/to/modules',
    '/path/to/other/modules',
    'MyModule' => '/explicit/path/mymodule-v1.2'
);


registerPaths($paths)  片段   

注册模块路径:

 foreach ($paths as $module => $path) {
            if (is_string($module)) {
                $this->registerPath($path, $module);//注册单个模块
            } else {
                $this->registerPath($path);
            }
        }


registerPath($path, $moduleName = false)  片段

  if ($moduleName) {
            if (in_array( substr($moduleName, -2 ), array('\\*','\\%') ) ) {
                $this->namespacedPaths[ substr($moduleName, 0, -2 ) ] = static::normalizePath($path);
            } else {
                $this->explicitPaths[$moduleName] = static::normalizePath($path);
            }
        } else {
            $this->paths[] = static::normalizePath($path);
        }


















