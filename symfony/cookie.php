<h3>新建Cookie</h3>
<pre lang="php">
	$response = new Respones;

	$cookie = new Cookie('name','zhangsanfeng',30*3600,);

	$response->headers->setCookie($cookie);
</pre>


<h3>取得Cookide</h3>
<pre lang="php">
	$request = Request::createFromGlobal;
	$cookies = $request->cookie;
</pre>


<h3>清除Cookie</h3>
<pre lang='php'>
	$response->headers->clearCookie('name','/','www.baidu.com');
</pre>


<h3>销毁全部cookie<h3>
<pre lang="php">
	$request = Request::createFromGlobal;


	$cookies = $request->cookie;

	foreach($cookies as $name=>$value)
	{
		$response->headers->clearCookie($name);
	}
</pre>
