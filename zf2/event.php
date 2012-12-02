这个组件的作用是什么?

监听器

触发

附加  attach 

事件

该如何使用?
EventManagerAwareInterface   
事件管理发现接口,实现该接口的类将会设置EventManager对象为一个属性,可以自由使用EventManager

小技巧
<?php
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(   //添加事件管理的标识符
            __CLASS__,
            get_called_class(),
        ));
        $this->events = $eventManager;
        return $this;
    }











EventManager   事件管理

<?php
class Foo implements EventManagerAwareInterface
{
    protected $events;

    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->events = $events;
        return $this;
    }

    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

    public function bar($baz, $bat = null)
    {
        $params = compact('baz', 'bat');
        $this->getEventManager()->trigger(__FUNCTION__, $this, $params);
    }
}

$foo->getEventManager()->attach('bar', function ($e) use ($log) {
    $event  = $e->getName();
    $target = get_class($e->getTarget());
    $params = json_encode($e->getParams());

    $log->info(sprintf(
        '%s called on %s, using params %s',
        $event,
        $target,
        $params
    ));
});


$foo->bar('baz', 'bat');

?>

<?php
$events = new EventManager();
$events->attach(array('these', 'are', 'event', 'names'), $callback);//同时添加多个监听器
$events->attach('*', $callback);//任何监听器都会触发这个事件
?>


SharedEventManager.你可以注入自己的EventManager 对象到一个公共的SharedEventCollection 中.
<?php
$events = new SharedEventManager();
// Attach to many events on the context "foo"
$events->attach('foo', array('these', 'are', 'event', 'names'), $callback);

// Attach to many events on the contexts "foo" and "bar"
$events->attach(array('foo', 'bar'), array('these', 'are', 'event', 'names'), $callback);

// Attach to all events on the context "foo"
$events->attach('foo', '*', $callback);

// Attach to all events on the contexts "foo" and "bar"
$events->attach(array('foo', 'bar'), '*', $callback);


?>


<?php
// Assume $events is a Zend\EventManager\SharedEventManager instance

$log = LogFactory($someConfig);
$events->attach('Foo', 'bar', function ($e) use ($log) {
    $event  = $e->getName();
    $target = get_class($e->getTarget());
    $params = json_encode($e->getParams());

    $log->info(sprintf(
        '%s called on %s, using params %s',
        $event,
        $target,
        $params
    ));
});

// Later, instantiate Foo:
$foo = new Foo();
$foo->getEventManager()->setSharedManager($events);

// And we can still trigger the above event:
$foo->bar('baz', 'bat');


?>





<?php
$argv = compact('values');
$argv = $this->getEventManager()->prepareArgs($argv);  //new ArrayObject($argv)

?>

在事件中停止事件的继续
<?php



    public function get($id)
    {
        $params = compact('id');
        $results = $this->getEventManager()->trigger('get.pre', $this, $params);

        // 如果事件停止传播,则返回这个值
        if ($results->stopped()) {
            return $results->last();
        }

        // do some work...

        $params['__RESULT__'] = $someComputedContent;
        $this->getEventManager()->trigger('get.post', $this, $params);
    }
//去停止事件的传播
    $listener = function($e) {
    // do some work

    // Stop propagation and return a response
    $e->stopPropagation(true);
    return $response;
};

//或者使用triggerUntil..第四个参数一定要存在,目的是自定义终止事件执行函数

    public function dispatch(Request $request, Response $response = null)
    {
        $argv = compact('request', 'response');
        $results = $this->getEventManager()->triggerUntil(__FUNCTION__, $this, $argv, function($v) {
            return ($v instanceof Response);
        });

        // Test if execution was halted, and return last result:
        if ($results->stopped()) {
            return $results->last();
        }

        // continue...
    }
?>

ListenerAggregateInterface 
<?php
use Zend\Cache\Cache;
use Zend\EventManager\EventCollection;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventInterface;

class CacheListener implements ListenerAggregateInterface
{
    protected $cache;

    protected $listeners = array();

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function attach(EventCollection $events)
    {
        $this->listeners[] = $events->attach('get.pre', array($this, 'load'), 100);
        $this->listeners[] = $events->attach('get.post', array($this, 'save'), -100);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function load(EventInterface $e)
    {
        $id = get_class($e->getTarget()) . '-' . json_encode($e->getParams());
        if (false !== ($content = $this->cache->load($id))) {
            $e->stopPropagation(true);
            return $content;
        }
    }

    public function save(EventInterface $e)
    {
        $params  = $e->getParams();
        $content = $params['__RESULT__'];
        unset($params['__RESULT__']);

        $id = get_class($e->getTarget()) . '-' . json_encode($params);
        $this->cache->save($content, $id);
    }
}


?>
把聚合添加到事件管理:
<?php

$value         = new SomeValueObject();
$cacheListener = new CacheListener($cache);
$value->getEventManager()->attachAggregate($cacheListener);

?>



源码中是如何实现的?
attach()片段
<?php
       // 如果这个事件没有优先级队列,则创建一个
        if (empty($this->events[$event])) {
            $this->events[$event] = new PriorityQueue();
        }

        // 创建一个回调处理, 设置这个事件名称和优先级作为他的元数据...CallbackHandler就是对回调函数进行了封装
        $listener = new CallbackHandler($callback, array('event' => $event, 'priority' => $priority));

        // 注入这个回调函数和优先级
        $this->events[$event]->insert($listener, $priority);
        return $listener;
?>
attachAggregate()  将监听器集合附加到事件管理.

ListenerAggregateInterface
<?php

   $ListenerAggregate->attach($eventManager,$priority);//定义好一组事件会被附加到$eventManager中.
?>


setSharedManager(SharedEventManagerInterface $sharedEventManager)片段
<?php
        $this->sharedManager = $sharedEventManager;
        StaticEventManager::setInstance($sharedEventManager);

?>


SharedEventManager


attach($id, $event, $callback, $priority = 1)
第一个参数是标识符 Identifier,其他三个参数就和EventManager一样了
相当于建立多个事件管理器,id就是这多个事件管理器的标志
注意:由于触发事件时,默认会查找以'*'做标志符的事件管理器,所以可以把标志设置为*.就不用在主事件管理器中注册标志了.
 详情见getSharedListeners;

<?php
        $ids = (array) $id;
        $listeners = array();
        foreach ($ids as $id) {
            if (!array_key_exists($id, $this->identifiers)) {
                $this->identifiers[$id] = new EventManager();
            }
            $listeners[] = $this->identifiers[$id]->attach($event, $callback, $priority);
        }
        if (count($listeners) > 1) {
            return $listeners;
        }
        return $listeners[0];
?>



//设置标志,以便查找

 $events->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
            'module_manager',
        ));


===========================================

trigger($event, $target = null, $argv = array(), $callback = null)
不同的参数,可以提供不同的方案去触发.主要是为triggerListeners准备参数

triggerUntil($event, $target, $argv = null, $callback = null)
第四个参数必须可以用,其他的和trigger没有任何区别

triggerListeners($event, $e, $callback);//事件名称  Event类   回调函数

1.$this->getListeners($event)
2.getSharedListeners($event)  //通过$identifiers从SharedEventManager中获取其他事件管理的监听器.默认会查找以'*'做标志符的事件管理器
3.getSharedListeners("*")   //获取SharedEventManager中通配符监听器
4.getListeners('*')         //获取通配符监听器

上面返回的是PriorityQueue队列

最后看下是怎么触发的片段:
<?php

    //$listener是CallbackHandler类..被注入到PriorityQueue的items['data']中
    //$e  包含了trigger($event, $target = null, $argv = array())中的这三个参数
    $responses->push(call_user_func($listener->getCallback(), $e));

    // 停止事件的继续
    if ($e->propagationIsStopped()) {
        $responses->setStopped(true);
        break;
    }

    // If the result causes our validation callback to return true,
    // stop propagation
    // 注意这个callback 是每次触发事件都要执行的.而且可以停止事件的进行,自定义停止事件的函数
    if ($callback && call_user_func($callback, $responses->last())) {
        $responses->setStopped(true);
        break;
    }
?>





其他的类?

Event 有四个属性  name  事件名 target  目标  params  参数  stopPropagation停止传播


ResponseCollection




小技巧  

event  中的  $params  属性

可以传入EventManager.这样就可以...再次触发其他事件..