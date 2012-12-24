dbal 上层是pdo

Database Abstraction Layer   数据抽象层

PDO           Doctrine\DBAL\Connection
PDOStatement  Doctrine\DBAL\Statement




Doctrine\DBAL\Connection  封装了Driver,Configuration,eventManager
Doctrine\DBAL\Configuration   设置和获取日志和缓存模板


Driver  驱动 
        1.构造pdo链接字符窜.
        2.获取数据库平台platform
        3.获取SchemaManager
        4.获取数据库
        5.使用\Doctrine\DBAL\Driver\PDOConnection构造pdo链接,并设置statement类Driver\PDOStatement(PDO::ATTR_STATEMENT_CLASS)
          一般会在Doctrine\DBAL\Connection进行链接



platform  平台包括 SchemaTool,事务隔离,以及许多其他功能.保证各种数据库的操作是一致的


types  类型用于绑定,进行参数类型转换.是通过平台获取不同的驱动保证数据的一致性

schema  元数据操作.

event  事件

Sharding  碎片是将一个大数据库按照一定规则拆分成多个小数据库的一门技术

Portability  可移植性,使用platform,保证可移植性

Cache   缓存

Query  SQL查询生成器
$conn = DriverManager::getConnection(array(/*..*/));
$queryBuilder = $conn->createQueryBuilder();