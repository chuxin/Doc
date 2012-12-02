
register()
将serviceId和classname的Definition类,储存到definitions中


get()函数做了什么:
1.从父类判断services[$id]是否存在,存在则直接返回
2.不存在则创建service
3.查看definitions[$id],如果存在则反射该类
4.检查是否有构造器注入,如果有则实例化的同时注入.没有则直接实例化,然后保存到services[$id]中.
5.然后查看definition中的注入属性注入和setter注入是否存在.存在则注入


三种注册方式:

一 构造器注入
class NewsletterManager
{
    protected $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    // ...
}


use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

// ...
$container->setDefinition('my_mailer', ...);
$container->setDefinition('newsletter_manager', new Definition(
    'NewsletterManager',
    array(new Reference('my_mailer'))  //注意这里是reference不是反射
));


二  Setter注入 

class NewsletterManager
{
    protected $mailer;

    public function setMailer(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    // ...
}

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

// ...
$container->setDefinition('my_mailer', ...);
$container->setDefinition('newsletter_manager', new Definition(
    'NewsletterManager'
))->addMethodCall('setMailer', array(new Reference('my_mailer')));


三 属性注入

class NewsletterManager
{
    public $mailer;

    // ...
}

$container->setDefinition('my_mailer', ...);
$container->setDefinition('newsletter_manager', new Definition(
    'NewsletterManager'
))->setProperty('mailer', new Reference('my_mailer')));


工厂类的注入

class NewsletterFactory
{
    public function get()
    {
        $newsletterManager = new NewsletterManager();

        // ...

        return $newsletterManager;
    }
}


use Symfony\Component\DependencyInjection\Definition;

// ...
$container->setParameter('newsletter_manager.class', 'NewsletterManager');
$container->setParameter('newsletter_factory.class', 'NewsletterFactory');

$container->setDefinition('newsletter_manager', new Definition(
    '%newsletter_manager.class%'
))->setFactoryClass(
    '%newsletter_factory.class%'
)->setFactoryMethod(
    'get'
);

各种注入方式的优缺点.












class NewsletterManager
{
    protected $mailer;
    protected $emailFormatter;

    public function setMailer(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function setEmailFormatter(EmailFormatter $emailFormatter)
    {
        $this->emailFormatter = $emailFormatter;
    }

    // ...
}


class GreetingCardManager
{
    protected $mailer;
    protected $emailFormatter;

    public function setMailer(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function setEmailFormatter(EmailFormatter $emailFormatter)
    {
        $this->emailFormatter = $emailFormatter;
    }

    // ...
}






use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

// ...
$container->setParameter('newsletter_manager.class', 'NewsletterManager');
$container->setParameter('greeting_card_manager.class', 'GreetingCardManager');

$container->setDefinition('my_mailer', ...);
$container->setDefinition('my_email_formatter', ...);
$container->setDefinition('newsletter_manager', new Definition(
    '%newsletter_manager.class%'
))->addMethodCall('setMailer', array(
    new Reference('my_mailer')
))->addMethodCall('setEmailFormatter', array(
    new Reference('my_email_formatter')
));
$container->setDefinition('greeting_card_manager', new Definition(
    '%greeting_card_manager.class%'
))->addMethodCall('setMailer', array(
    new Reference('my_mailer')
))->addMethodCall('setEmailFormatter', array(
    new Reference('my_email_formatter')
));









Container  对parameterBag 和services以及scpoes的定义管理.



