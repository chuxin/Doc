<ul>
    <li>
        <h2>在service工厂创建TreeRouteStack.会将路由配置$config['router']传进去</h2>
        <pre>
$routerConfig = isset($config['router']) ? $config['router'] : array();
$router = HttpRouter::factory($routerConfig);//路由配置.是所有模块的配置整合文件
        </pre>  
    </li>
    <li>
        <h2>使用单利模式TreeRouteStack::factory($routerConfig)创建router管理器.</h2>    
        整个过程分为3个步骤：
        <ul>
            <li>设置路由插件管理：$instance->setRoutePluginManager($options['route_plugins']);
                $options['route_plugins']应该是一个继承了AbstractPluginManager的对象。为什么会有个插件管理的服务管理子类呢？
                因为插件管理是专为一组相似的服务创建。相似的服务可以传入相似的配置。他和服务管理主要的不同点在于get()函数。
                插件管理的get($name, $options = array(), $usePeeringServiceManagers = true)可以传入一组选项进入。
                所有的路由插件都是以InvokableClass类型注册的在init()函数中.
            </li>
            <li>
                设置路由：$instance->addRoutes($options['routes']);注意哈。$this->routes是个优先级列表对象
                1.会通过RoutePluginManager，根据router[type]建立Router类，并将router[options]传入其中。设置router[priority]路由优先级
                2.如果路由中有child_routes子路由，则之后通过RoutePluginManager以part类型建立路由类.并传入主路由、may_terminate、'child_routes'以及插件管理本身。传入插件管理是为了能够在其中创建其他类型路由
                3.最后会将路由名称、router类、优先级$router->priority插入到$this->routes中
            </li>
            <li>设置默认参数：$instance->setDefaultParams($options['default_params']);</li>
        </ul>
        由上可以看到路由可以这样配置：
<pre lang="php">
<?php
    return array(
            router=>array(
                    'route_plugins'=>,//AbstractPluginManager对象
                    'routes'=>array(
                            'name'=>array(  //路由名称
                                    "type"=>"", //路由类型
                                    "options"=>array(   //路由选项,根据插件类型的不同,而有不同的选项参数
                                        "route"=>"",    //路由规则
                                        "default"=>array(

                                        ),                //控制器相关
                                        "priority"=>""    //优先级
                                    ),
                                    "may_terminate"=>1,
                                    "child_routes"=>array(  //子路由
                                        "default"=>array(
                                            "type"=>"",
                                            "options"=>array(
                                                "route"=>"",
                                                "default"=>"",
                                                "priority"=>""  
                                            ),
                                        )
                                    ),
                                )
                        ),
                    'default_params'=>array(),
                ),
        );
?>
</pre>
    </li>
    <li>
        <h2>传入请求匹配路由</h2>
        1.$router->match($request)的时候会循环所有路由器中的routers属性,因为routes是优先级列表对象,所以会按优先级循环该类,然后进行匹配这个请求$route->match($request, $baseUrlLength)
        2.在路由中如果匹配成功，会创建一个RouteMatch对象"return new RouteMatch($this->defaults);"$this->defaults就是路由中的default参数。包括了
        '__NAMESPACE__',controller,action选项.使用__NAMESPACE__可以减少controller设置.详见ModuleRouteListener监听器
        3.在返回的路由规则类中RouteMatch设置路由名称参数: $match->setMatchedRouteName($name);
        4.在返回的路由规则类中RouteMatch中设置路由配置中的default_params参数.呼呼这个很有意思.你可以把各种东西传给他,这会设置到mvcEvent中
        <pre>
foreach ($this->defaultParams as $name => $value) {
    if ($match->getParam($name) === null) {
        $match->setParam($name, $value);
    }
}
        </pre>

    </li>
</ul>














































