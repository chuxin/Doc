Event 单独事件的控制类,是监听器的依赖类,主要控制是否停止该事件.同时可将事件分配器传递给监听器.这就可以在触发一个的同时,触发另一个事件
GenericEvent 可以为某个事件执行时提供额外的类和参数


EventDispatcher  事件分配,注册事件名和监听器.在适当的时候按顺序触发事件.顺序由注册事件时的priority参数控制.注意最后触发的事件是,已经排序好的事件列表sorted
ImmutableEventDispatcher    事件分配器的代理
ContainerAwareEventDispatcher  包的事件分配器.其事件的触发者是包内的serveiceID的类


EventSubscriberInterface  事件订阅者,返回定义好的事件名数组.事件名所触发的事件,乃是该事件订阅者的方法.

<?php
class GoogleListener implements EventSubscriberInterface
{
    public function onResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
 
        if ($response->isRedirection()
            || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $event->getRequest()->getRequestFormat()
        ) {
            return;
        }

        $response->setContent($response->getContent().'GA CODE');
    }


    public static function getSubscribedEvents()
    {
        return array('response' => 'onResponse');
    }
}
?>



一个事件要处理的事情:
1.注册事件
2.控制事件执行顺序
3.停止事件执行
4.执行事件


	require(__DIR__.'/vendor/autoload.php');

	use Symfony\Component\EventDispatcher\EventDispatcher;

	$dispatch = new EventDispatcher();


	class Money
	{
	
		public static function onLogin($event)
		{
				echo '我的钱加1了';
		}
	}


	class Log
	{
		public static function onLogin($event)
		{
			$message="我登陆了,在".date('Y-m-d H:i:s');
			echo $message;
		}
	}


	$dispatch->addListener('user.login',array('Money','onLogin'));
	$dispatch->addListener('user.login',array('Log','onLogin'));


	class User
	{
		  public $dispatch;

		  public function setDispatch($dispatch)
		  {
		  	$this->dispatch = $dispatch;
		  }

			public  function login()
			{
				$this->dispatch->dispatch('user.login');//触发器没有传当前类作为参数上下文
			}
	}


	$user = new User;

	$user->setDispatch($dispatch);

	$user->login();
