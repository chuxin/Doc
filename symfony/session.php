Session  类调配了相当于一个包装类.

attributeBag   一般这里会存储持久性的session数据

FlashBag  这里会存储零食性的数据,用过一次就丢掉

SessionBagInterface   
是将Session进行规划性管理的类.例如AttributeBag.其中的attributes属性是全局变量$_SESSION['attribute']的引用.注意是引用
具体参看sessionhander的loadSession方法


SessionStorageInterface   实现了bag和hander的详细处理


AbstractPxory   hander的代理类.不负责存储的细节.只负责如何执行session的各种操作

xxxSessionHandler处理类.  负责该session的配置和存储细节.



<h3>Session类</h3>
<dl>
	<dt>session工作流程</dt>
	<dd>start()  打开seesion,不要使用session_start()</dd>
	<dd>migrate() 重新生成会话id,不要使用session_regenerate_id()</dd>
	<dd>invalidate()   清空所有session和注销sessionID,不要使用session_destroy()</dd>
	<dd>getId()   获取sessionID</dd>
	<dd>setId()  设置sessionid</dd>
	<dd>getName()  返回SESSION NAME</dd>
	<dd>setName()  设置SESSION NAME</dd>
	<dt>session 的操作---是对attribute中的session值的操作</dt>
	<dd>set()  设置一个session</dd>
	<dd>get()	获取一个session</dd>
	<dd>all()	获取所有session</dd>
	<dd>has()	查询session存不存在</dd>
	<dd>replace  修改一个session</dd>
	<dd>remove()  删除某个session</dd>
	<dd>clear()  清除所有session</dd>
	<dd>keys  返回所有键名</dd>
</dl>



使用PDO存储session
<pre lang="php">
	 use Symfony\Component\HttpFoundation\Session\Session ;
 use Symfony\Component\HttpFoundation\Session\Storage\SessionStorage ;
 use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler ;

 $storage = new NativeSessionStorage ( array (), new PdoSessionHandler ());
 $session = new Session ( $storage );

</pre>


使用文件存储session

<pre lang="php">
  use Symfony\Component\HttpFoundation\Session\Session ;
 use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage ;
 use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler ;

 $storage = new NativeSessionStorage ( array (), new NativeFileSessionHandler ());
 $session = new Session ( $storage );
</pre>