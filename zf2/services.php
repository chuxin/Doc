这个组件的作用是什么?


该如何使用?

ServiceLocatorAwareInterface  实现他就可以获得serviceManager
<?php
class BareController implements
    Dispatchable,
    ServiceLocatorAwareInterface
{
    protected $services;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }

    public function dispatch(Request $request, Response $response = null)
    {
        // Retrieve something from the service manager
        $router = $this->services->get('Router');
    }
}
?>




ServiceManager
has($name)  检查服务管理器是否有一个服务
get($name)  根据名字检索服务


服务注册  $services->setService('foo', $object).
延迟加载  $services->setInvokableClass('foo', 'Fully\Qualified\Classname').
服务工厂  工厂类必须实现接口Zend\ServiceManager\FactoryInterface
服务别名  可以为一个已知的服务,一个延迟加载服务,一个工厂 或者其他别名 设置一个别名
抽象工厂   
初始化    $services->addInitializer()

服务管理器,还可以使用di组件为服务初始化,或者作为抽象工厂.




如何设置service
1.配置是一个数组

<?php
// a module configuration, "module/SomeModule/config/module.config.php"
return array(
    'service_manager' => array(
        'abstract_factories' => array(
            // Valid values include names of classes implementing
            // AbstractFactoryInterface, instances of classes implementing
            // AbstractFactoryInterface, or any PHP callbacks
            'SomeModule\Service\FallbackFactory',
        ),
        'aliases' => array(
            // Aliasing a FQCN to a service name
            'SomeModule\Model\User' => 'User',
            // Aliasing a name to a known service name
            'AdminUser' => 'User',
            // Aliasing to an alias
            'SuperUser' => 'AdminUser',
        ),
        'factories' => array(
            // Keys are the service names.
            // Valid values include names of classes implementing
            // FactoryInterface, instances of classes implementing
            // FactoryInterface, or any PHP callbacks
            'User'     => 'SomeModule\Service\UserFactory',
            'UserForm' => function ($serviceManager) {
                $form = new SomeModule\Form\User();

                // Retrieve a dependency from the service manager and inject it!
                $form->setInputFilter($serviceManager->get('UserInputFilter'));
                return $form;
            },
        ),
        'invokables' => array(
            // Keys are the service names
            // Values are valid class names to instantiate.
            'UserInputFiler' => 'SomeModule\InputFilter\User',
        ),
        'services' => array(
            // Keys are the service names
            // Values are objects
            'Auth' => new SomeModule\Authentication\AuthenticationService(),
        ),
        'shared' => array(
            // Usually, you'll only indicate services that should _NOT_ be
            // shared -- i.e., ones where you want a different instance
            // every time.
            'UserForm' => false,
        ),
    ),
);
?>



2.将其传给Config类
3.将config类传给ServiceManager
4.ServiceManager中构造函数会执行config类的configureServiceManager进行设置service
    $serviceManager->setFactory($name, $factory);
     $serviceManager->addAbstractFactory($factory);
     $serviceManager->setInvokableClass($name, $invokable);
     $serviceManager->setService($name, $service);
     $serviceManager->setAlias($alias, $nameOrAlias);
     $serviceManager->addInitializer($initializer);
     $serviceManager->setShared($name, $isShared);


抽象工厂($abstractFactories), 一个数组的抽象工厂类的名字   
别名($aliases), 关联数组别名/目标名称(目标名称也可以 是一个别名)。
工厂($factories), 一组服务名称/工厂类 的数组. 这个工厂类必须实现Zend\ServiceManager\FactoryInterface.
调用类($invokableClasses)  一组服务名称/类名. 类名将会被直接 实例化没有任何构造函数参数。
服务($instances), 一组服务名称/对象
共享($shared), 一组服务名称/布尔, 表明是否应该共享一个服务. 默认情况下, 服务管理器所有的服务都是共享的,
您可以指定一个布尔值false值在这里,然后get()的时候就不会存储在instances属性中.而是直接返回实例
在get()中,如果shared中有该服务,则存储在instances中.下次就不用再次实例化了.



小技巧.还可以在service里存点其他的东西...例如配置,数组.其他的东西
 $serviceManager->setService('ApplicationConfig', $configuration);



初始化 :你可以在创建某个服务时,为某个服务注入某个类,或者初始化执行一些特殊的动作.看看初始化是怎么实现的
<?php

        //$serviceManager->get() 中 $serviceManager->create()的片段
        foreach ($this->initializers as $initializer) {
            if ($initializer instanceof InitializerInterface) {
                $initializer->initialize($instance, $this);
            } elseif (is_object($initializer) && is_callable($initializer)) {
                $initializer($instance, $this);
            } else {
                call_user_func($initializer, $instance, $this);
            }
        }
?>










//Zend\Mvc\Service\ServiceManagerConfig;




服务工厂
//Service factories


<?php

class EventManagerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $em = new EventManager();
        $em->setSharedManager($serviceLocator->get('SharedEventManager'));
        return $em;
    }
}
?>