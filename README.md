# faravel
faravel 是一个对 laravel 功能进行扩展的库

## 使用方法
+ 安装

        composer require lee2son/faravel
        
+ 创建配置文件

        php artisan vendor:publish --tag=faravel
        
    这时候会创建一个配置文件 `config/faravel.php`

## 功能特色
以下功能是选择性开启，默认是关闭状态

### 开启  `request_id` 支持
`request_id`即是为每次 request 请求生成一个唯一ID，这个ID可以通过各种形式返回给客户端，这样在访问应用出现问题时可以凭此ID快速定位问题。网上有的代替方案是通过中间件来实现的，中间件的缺点就是如果代码还没走到该中间件就抛出异常了，是没有 request_id 的；其次，nginx 的 access_log 也很方便的记录这个ID。

+ 首先需要配置 nginx：通过 nginx 生成的`$request_id`传递到应用（nginx 同时也应把该值存入 access_log）

        location ~ [^/]\.php(/|$)
        {
            fastcgi_pass  unix:/tmp/php-cgi.sock;
            fastcgi_index index.php;
            
            add_header X-Request-Id $request_id;
            fastcgi_param REQUEST_ID $request_id;
        }

+ 使 `App\Exceptions\Handler` extends `Faravel\Foundation\Exceptions\Handler`。这样在记录应用程序异常日志时会把 `REQUEST_ID` 也记录进去。

### 记录 sql 查询日志
详见 `config/faravel.php` 配置文件中的 `listen_sql`

### 记录 redis 命令日志
详见 `config/faravel.php` 配置文件中的 `listen_redis`

### 编译 model
对 Eloquent 进行编译，在 model 文件中加入一些代码以辅助开发。原理是读取所有继承自`Illuminate\Database\Eloquent\Model`的类，读取其绑定表的结构，并生成相关代码

1. 生成表名常量

        class User extends Illuminate\Database\Eloquent\Model
        {
            const TABLE = 'user';
        }
2. 生成字段名常量（可设置前缀）

        class User extends Illuminate\Database\Eloquent\Model
        {
            const S_USER_ID = 'user.id';
            const F_USER_ID = 'id';
        }
        
3. 生成枚举常量（可设置前缀）并生成相关方法和语言包

        class User extends Illuminate\Database\Eloquent\Model
        {
            const STATUS_NORMAL = 'normal';
            const STATUS_FREEZE = 'freeze';
            
            public function scopeWhereStatusNormal($query) {
                $query->where('status', static::STATUS_NORMAL);
            }
            
            public function isStatusNormal() {
                return $this->status === static::STATUS_NORMAL
            }
        }
        
        // 使用方法
        
        $user = User::whereWhereStatusNormal()->first();
        dd($user->isStatusNormal());

    同时把枚举说明生成语言包放在：`resources/lang/<locale>/table.php`。可通过在 model 中实现`getFieldNameEnumerates`方法或`Enumerates`来定制枚举（默认情况下只处理 enum 类型的字段）：
    
        class User extends Illuminate\Database\Eloquent\Model
        {
            /**
             * @param object $field Row in "information_schema.COLUMNS"
             */
            public function getStatusEnumerates($field)
            {
                return [
                    'normal' => ['zh-cn' => '正常'],
                    'freeze' => ['zh-cn' => '冻结']
                ];
            }
        }

具体见命令：

    > php artisan faravel:BuildModel --help
    Description:
      对 Model 进行代码生成
    
    Usage:
      faravel:BuildModel [options]
    
    Options:
          --table-const-name[=TABLE-CONST-NAME]              生成表名的常量名称（为空则不生成表常量）
          --gen-field-name                                   是否生成字段名常量（tableName.fieldName）
          --field-name-prefix[=FIELD-NAME-PREFIX]            字段名常量前缀
          --gen-field-shortname                              是否生成字段短名常量
          --field-shortname-prefix[=FIELD-SHORTNAME-PREFIX]  字段短名常量前缀
          --gen-field-enum                                   是否生成字段枚举常量
          --field-enum-prefix[=FIELD-ENUM-PREFIX]            枚举常量前缀
          --const-name-style[=CONST-NAME-STYLE]              常量命名风格 camel:首字母小写驼峰 Camel:首字母大写驼峰 snake:小写下划线 SNAKE:大写下划线
          --reset                                            重置（还原）

### 扩展 model
使你的 model 继承自 `\Faravel\Database\Eloquent\Model` 即可