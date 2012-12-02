 $serviceManager->get('ModuleManager')->loadModules();




1.Zend\Mvc\Service\ModuleManagerFactory;

$ModuleManagerFactory->createService();







<?php



	//附加两个监听器集合
   $events = $serviceLocator->get('EventManager');
        $events->attach($defaultListeners);
        $events->attach($serviceListener);


        //初始化模块管理

        $moduleEvent = new ModuleEvent;
        $moduleEvent->setParam('ServiceManager', $serviceLocator);

        $moduleManager = new ModuleManager($configuration['modules'], $events);
        $moduleManager->setEvent($moduleEvent);




?>













2.Zend\ModuleManager\Listener\DefaultListenerAggregate
LocatorRegistrationListener

<?php
ListenerOptions extends AbstractOptions
监听选项  
new ListenerOptions($configuration['module_listener_options']);
向LocatorRegistrationListener提供模块监听选项..




?>




3.Zend\Mvc\Service\ServiceListenerFactory

$ServiceListenerFactory->createService();



Zend\ModuleManager\Listener\ServiceListener




onLoadModulesPost     

1.首先会默认增加ServiceListenerFactory中的$defaultServiceConfig到默认的ServiceManagers
2.ControllerLoader
3.ControllerPluginManager
4.ViewHelperManager

<?php
   foreach ($this->serviceManagers[$key]['configuration'] as $configs) {//这里把默认配置循环出来然后给serviceMangers
                if (isset($configs['configuration_classes'])) {
                    foreach ($configs['configuration_classes'] as $class) {
                        $configs = ArrayUtils::merge($configs, $this->serviceConfigToArray($class));
                    }
                }
                $smConfig = ArrayUtils::merge($smConfig, $configs);
            }

            if (!$sm['service_manager'] instanceof ServiceManager) {
                $instance = $this->defaultServiceManager->get($sm['service_manager']);//实例化其他的serviceMarngers
                if (!$instance instanceof ServiceManager) {
                    throw new Exception\RuntimeException(sprintf(
                        'Could not find a valid ServiceManager for %s',
                        $sm['service_manager']
                    ));
                }
                $sm['service_manager'] = $instance;
            }
            $serviceConfig = new ServiceConfig($smConfig);
            $serviceConfig->configureServiceManager($sm['service_manager']);
?>













============================================

MvcEvent

  1.  const EVENT_BOOTSTRAP      = 'bootstrap';

<ul>
    <li>ModuleManager\Listener\LocatorRegistrationListener.php  注册所有模块到serviceManager</li>
    <li>ModuleManager\Listener\OnBootstrapListener.php 执行所有模块的$module->onBootstrap()</li>
    <li>Mvc\View\Http\ViewManager.php   视图管理的引导</li>
</ul>


2.   const EVENT_ROUTE          = 'route';



<dl>

    <dt>Mvc\RouteListener</dt>
    <dd></dd>
    <dt>Mvc\ModuleRouteListener.php</dt>
    <dd></dd>
</dl>









   2. const EVENT_DISPATCH       = 'dispatch';


Zend\Mvc\DispatchListener.php














    const EVENT_FINISH         = 'finish';
    const EVENT_RENDER         = 'render';
 








     const EVENT_DISPATCH_ERROR = 'dispatch.error';

触发错误事件;

Zend\Mvc\View\Http\ExceptionStrategy.php   预处理异常显示
$events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'prepareExceptionViewModel'));

注入视图model到事件中
Zend\Mvc\View\Http\InjectViewModelListener.php
$events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'injectViewModel'), -100);

Zend\Mvc\View\Http\RouteNotFoundStrategy.php
$events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'detectNotFoundError'));
$events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'prepareNotFoundViewModel'));


Zend\Mvc\View\Http\ViewManager.php
$events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($injectViewModelListener, 'injectViewModel'), -100);