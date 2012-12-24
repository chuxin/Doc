
Object Relational Mapper
对象关系映射建立于数据库抽象层DBAL（Database Abstraction Layer）
，主要提供了DQL（Doctrine Query Language)，DQL的设计灵感来源于HQL(Hibernate)，
主要是给开发者提供了一个基于对象的查询语法，是开发者维护更少的代码。

Database Abstraction Layer
主要提供数据库schema的检查，管理和PDO抽象

MongoDB Object Document Mapper
特别针对MongoDB开发的数据库接口

Doctrine Migrations
同样构建于DBAL, 能够方便的进行数据库版本控制，修改和迁移。

Common
包涵了所有其他子项目的依赖包

Options:
  --help           -h Display this help message.
  --quiet          -q Do not output any message.
  --verbose        -v Increase verbosity of messages.
  --version        -V Display this program version.
  --color          -c Force ANSI color output.
  --no-interaction -n Do not ask any interactive question.

Available commands:
  help                         Displays help for a command (?)
  list                         Lists commands
dbal
  :import                      Import SQL file(s) directly to Database.
  :run-sql                     Executes arbitrary SQL directly from the command line.
orm
  :convert-d1-schema           Converts Doctrine 1.X schema into a Doctrine 2.X schema.
  :convert-mapping             Convert mapping information between supported formats.
  :ensure-production-settings  Verify that Doctrine is properly configured for a production environment.
  :generate-entities           Generate entity classes and method stubs from your mapping information.
  :generate-proxies            Generates proxy classes for entity classes.
  :generate-repositories       Generate repository classes from your mapping information.
  :run-dql                     Executes arbitrary DQL directly from the command line.
  :validate-schema             Validate that the mapping files.
orm:clear-cache
  :metadata                    Clear all metadata cache of the various cache drivers.
  :query                       Clear all query cache of the various cache drivers.
  :result                      Clear result cache of the various cache drivers.
orm:schema-tool
  :create                      Processes the schema and either create it directly on EntityManager Storage Connection or generate the SQL output.
  :drop                        Processes the schema and either drop the database schema of EntityManager Storage Connection or generate the SQL output.
  :update                      Processes the schema and either update the database schema of EntityManager Storage Connection or generate the SQL output.

