这个组件的作用是什么?

降低对象之间的契合度
为了避免陷入Di可能造成的元数据式编程泥潭（Metaprogramming）, Di只是作为ZF2的底层实现，上层加入了ServiceManager。
普通开发者在使用ZF2的过程中不需要接触到Di的层面。

IoC(Inversion of Control)中文译为控制反转，目前Java社群中流行的各种轻量级容器的实现都是以IoC模式作为基础的。
控制反转意味着在系统开发过程中，设计的类将交由容器去控制，而不是在类的内部去控制，类与类之间的关系将交由容器处理,
一个类在需要调用另一个类时,只要调用另一个类在容器中注册的名字就可以得到这个类的实例,
与传统的编程方式有了很大的不同,”不用你找,我来提供给你”,这就是控制反转的含义。
Martin Fowler在他的一篇文章中给IoC起了一个更为直观的名字：依赖注射DI(Dependency Injection)。下面先引入这个模式。

在设计模式中，我们已经习惯一种思维编程方式：Interface Driven Design 接口驱动，接口驱动有很多好处，可以提供不同灵活的子类实现，增加代码稳定和健壮性等等，但是接口一定是需要实现的，也就是如下语句迟早要执行：

InterfaceA a = new InterfaceAImp()；

InterfaceAImp是接口InterfaceA的一个子类，IoC模式可以延缓接口的实现，根据需要实现，有个比喻：
接口如同空的模型套，在必要时，需要向模型套注射石膏，这样才能成为一个模型实体，
因此，我们将人为控制接口的实现成为注射。
IoC模式是解决调用者和被调用者之间的一种关系，
上述InterfaceA实现语句表明当前是在调用被调用者InterfaceAImp，
由于被调用者名称写入了调用者的代码中，这产生了一个接口实现的原罪：
彼此联系，调用者和被调用者有紧密联系，在UML中是用依赖 Dependency 表示。
但是这种依赖在分离关注的思维下是不可忍耐的，必须切割，实现调用者和被调用者解耦，
新的Ioc模式依赖注射 (Dependency Injection)模式由此产生了，也就是将依赖先剥离，
然后在适当时候再注射进入。



DI大概分为三种:
1.构造函数
2.set方法
3.属性












<?php


// inside a bootstrap somewhere
$di = new Zend\Di\Di();
$di->instanceManager()->setParameters( 'MyLibrary\DbAdapter', array(
        'username' => $config->username,
        'password' => $config->password
    ) );

// inside each controller
$movieLister = $di->get( 'MyMovieApp\MovieLister' );
foreach ( $movieLister as $movie ) {
    // iterate and display $movie
}


Zend\Di\Di

public function __construct( DefinitionList $definitions = null, InstanceManager $instanceManager = null, Configuration $config = null ) {
    $this->definitions = ( $definitions ) ?: new DefinitionList( new Definition\RuntimeDefinition() );
    $this->instanceManager = ( $instanceManager ) ?: new InstanceManager();

    if ( $config ) {
        $this->configure( $config );
    }
}


// 在调用的时候传递参数
$di = new Zend\Di\Di();
$movieLister = $di->get( 'MyMovieApp\MovieLister', array(
        'username' => $config->username,
        'password' => $config->password
    ) );


?>
通过调用$ di - > get(), 这MovieLister的实例将自动共享。
这意味着随后调用 get()将返回相同的实例作为之前的调用。
如果你想拥有完全的新实例MovieLister, 您可以使用$ di - > newInstance()。












    <?php
$config = array(
    'definition' => array(
        'compiler' => array( /* @todo compiler information */ ),
        'runtime'  => array( /* @todo runtime information */ ),
        'class' => array(
            'instantiator' => '', // the name of the instantiator, by default this is __construct
            'supertypes'   => array(), // an array of supertypes the class implements
            'methods'      => array(
                'setSomeParameter' => array( // a method name
                    'parameterName' => array(
                        'name',       // string parameter name
                        'type',       // type or null
                        'is-required' // bool
                    )
                )

            )
        )
    )
);
?>






get($name, array $params = array())

根据有无参数$params,和instanceManager有无共享实例,返回对象.

newInstance()
<?php
$instantiator     = $definitions->getInstantiator( $class );
$injectionMethods = array();
$injectionMethods[$class] = $definitions->getMethods( $class );

foreach ( $definitions->getClassSupertypes( $class ) as $supertype ) {
    $injectionMethods[$supertype] = $definitions->getMethods( $supertype );
}

if ( $instantiator === '__construct' ) {
    $instance = $this->createInstanceViaConstructor( $class, $params, $alias );
    if ( array_key_exists( '__construct', $injectionMethods ) ) {
        unset( $injectionMethods['__construct'] );
    }
} elseif ( is_callable( $instantiator, false ) ) {
    $instance = $this->createInstanceViaCallback( $instantiator, $params, $alias );
}

    ....
    ....
    ....


    $this->handleInjectDependencies( $instance, $injectionMethods, $params, $class, $alias, $name );


?>



片段..如果共享则存储在instanceManager.下次直接获得.不用反射等各种操作.

<?php
if ( $isShared ) {
    if ( $params ) {
        $this->instanceManager->addSharedInstanceWithParameters( $instance, $name, $params );
    } else {
        $this->instanceManager->addSharedInstance( $instance, $name );
    }
}

?>

InstanceManager






DefinitionList
基于多个定义的定义列表.可以向该列表注入多个定义.获取实例的时候,会循环判断这些定义列表




RuntimeDefinition   //默认的反射操作类,在这个类中,会根据传进来的类名等进行反射操作

$introspectionStrategy = null;  反射策略

explicitLookups =false;  明确的查询从classes.否则就检查类


processClass

<?php
    //反射类
    $rClass = new Reflection\ClassReflection($class);
        $className = $rClass->getName();
        $matches = null; // used for regex below

        // setup the key in classes
        $this->classes[$className] = array(
            'supertypes'   => array(),// an array of supertypes the class implements
            'instantiator' => null,    // the name of the instantiator, by default this is __construct
            'methods'      => array(),
            'parameters'   => array()
        );

        $def = &$this->classes[$className]; // 太麻烦制造一个短的名字进行操作
    .....
    $rTarget = $rClass;//为了通过循环找到$supertypes.
        $supertypes = array();
        do {
            $supertypes = array_merge($supertypes, $rTarget->getInterfaceNames());//接口名字
            if (!($rTargetParent = $rTarget->getParentClass())) {//父类名字
                break;
            }
            $supertypes[] = $rTargetParent->getName();
            $rTarget = $rTargetParent;
        } while (true);

        $def['supertypes'] = $supertypes;

        if ($def['instantiator'] == null) {
            if ($rClass->isInstantiable()) {
                $def['instantiator'] = '__construct';
            }
        }

          if ($rClass->hasMethod('__construct')) {
            $def['methods']['__construct'] = true; // required
            $this->processParams($def, $rClass, $rClass->getMethod('__construct'));
        }

?>

















IntrospectionStrategy是一个,使RuntimeDefinition如何去反省你的类的信息,确定规则或指导方针的对象,他都做了哪些:

    是否使用注释
    method名称的正则匹配,默认情况下, /^set[A-Z]{1}\w*/ 是注册的
    interface名称的正则匹配。默认情况下,/\w*Aware\w*/ 是注册的


AnnotationManager    注释管理.不是必须的.






CompilerDefinition  和  RuntimeDefinition 是非常相似的.
通过使用编译器,可以创建一个定义并写入磁盘中会用到的一个 请求,而不是任务的扫描实际的代码。



ClassDefinition
1.覆盖一些信息 RuntimeDefinition
2.简单地定义你的完整的类的定义和xml,ini或 php文件描述结构 通过配置文件
