Request,中$request->getHeaders();可以获取头部





添加头
$headers = new Zend\Http\Headers();

// We can directly add any object that implements Zend\Http\Header\HeaderInterface
$typeHeader = Zend\Http\Header\ContentType::fromString('Content-Type: text/html');
$headers->addHeader($typeHeader);

// We can add headers using the raw string representation, either
// passing the header name and value as separate arguments...
$headers->addHeaderLine('Content-Type', 'text/html');

// .. or we can pass the entire header as the only argument
$headers->addHeaderLine('Content-Type: text/html');

// We can also add headers in bulk using addHeaders, which accepts
// an array of individual header definitions that can be in any of
// the accepted formats outlined below:
$headers->addHeaders(array(

    // An object implementing Zend\Http\Header\HeaderInterface
    Zend\Http\Header\ContentType::fromString('Content-Type: text/html'),

    // A raw header string
    'Content-Type: text/html',

    // We can also pass the header name as the array key and the
    // header content as that array key's value
    'Content-Type' => 'text/html');

));




Client 很强大.能模仿请求发送..

use  Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Http\Client;

$request = new Request();
$request->setUri('http://www.baidu.com');
$request->setMethod('POST');
$request->getPost()->set('foo', 'bar');

$client = new Client();
$response = $client->dispatch($request);

if ($response->isSuccess()) {
	echo 'aaaaaa0';
}