系统模块是由下面组成的:
1.模块的自动加载   Zend\Loader\ModuleAutoloader
2.模块管理			Zend\ModuleManager\ModuleManager
3.模块管理监听器  事件监听器可以附加各种事件到模块管理器



模块中三个自动加载文件:

autoload_classmap.php  一个数组 name/filename  的类地图
autoload_function.php  有一个php函数,被传到spl_autoload_register(),通常这个函数会返回autoload_classmap.php
autoload_register.php   通常返回autoload_function.php 和spl_autoload_register()




$serviceManager->get('ModuleManager')->loadModules();//在工厂里会把配置文件中$configuration['modules']赋给模块管理

1.会触发ModuleEvent::EVENT_LOAD_MODULES

2.会执行 onLoadModules(),循环所有模块

3.loadModule($moduleName)加载单个模块


<?php

	//将模块名赋给事件.触发EVENT_LOAD_MODULE时.可以通过判断模块名来触发事件
   		$event = ($this->loadFinished === false) ? clone $this->getEvent() : $this->getEvent();
        $event->setModuleName($moduleName);
        $this->loadFinished = false;


        //这里会实例化所有的Module.php
        $result = $this->getEventManager()->trigger(ModuleEvent::EVENT_LOAD_MODULE_RESOLVE, $this, $event, function ($r) {
            return (is_object($r));
        });

        $module = $result->last();

        //触发加载模块事件

   		$event->setModule($module);

        $this->getEventManager()->trigger(ModuleEvent::EVENT_LOAD_MODULE, $this, $event);
        $this->loadedModules[$moduleName] = $module;



?>

ModuleEvent::EVENT_LOAD_MODULE_RESOLVE
1.ModuleResolverListener
<?php
 public function __invoke($e)
    {
        $moduleName = $e->getModuleName();
        $class      = $moduleName . '\Module';//Album\Module

        if (!class_exists($class)) {//触发模块自动加载
            return false;
        }

        $module = new $class;
        return $module;
    }

?>


2.DefaultListenerAggregate

	<?php

		//自动执行模块的getAutoloaderConfig();
        $this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULE, new AutoloaderListener($options), 9000);

        //执行模块的init()
        $this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULE, new InitTrigger($options));


        $this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULE, new OnBootstrapListener($options));
        $this->listeners[] = $events->attach($locatorRegistrationListener);
        $this->listeners[] = $events->attach($configListener);
        	// 下面是ConfigListener	
        	$this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULES, array($this, 'onloadModulesPre'), 1000);

	        if ($this->skipConfig) {
	            // We already have the config from cache, no need to collect or merge.
	            return $this;
	        }
	        //会执行模块中的getConfig();
	        $this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULE, array($this, 'onLoadModule'));
	        $this->listeners[] = $events->attach(ModuleEvent::EVENT_LOAD_MODULES, array($this, 'onLoadModulesPost'), -1000);


?>

ModuleEvent

注意是按顺序执行..在触发loadModules  事件后,会在这个过程中
先后触发loadModule.resolve和loadModule事件


最后再触发loadModules.post事件

1.loadModules




2.loadModule.resolve
3.loadModule


4.loadModules.post

    ConfigListener

    会把所有的模块$config的配置文件.组合成为一个数组.到mergedConfig里.

