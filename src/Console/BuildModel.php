<?php

namespace Faravel\Console;

use Faravel\Illuminate\Console\Command;
use Illuminate\Support\Arr;
use ReflectionClass;
use Composer\Autoload\ClassMapGenerator;
use Illuminate\Support\Str;

class BuildModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'faravel:build-model {--table-const-name= : 生成表名的常量名称（为空则不生成表常量）} 
                                                {--gen-field-name : 是否生成字段名常量（tableName.fieldName）}
                                                {--field-name-prefix= : 字段名常量前缀}
                                                {--gen-field-shortname : 是否生成字段短名常量}
                                                {--field-shortname-prefix= : 字段短名常量前缀}
                                                {--gen-field-enum : 是否生成字段枚举常量}
                                                {--field-enum-prefix= : 枚举常量前缀}
                                                {--const-name-style= : 常量命名风格 camel:首字母小写驼峰 Camel:首字母大写驼峰 snake:小写下划线 SNAKE:大写下划线}
                                                {--reset : 重置（还原）}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '对 Model 进行代码生成';

    /**
     * @var array 存放枚举字段说明
     */
    protected $enumTable = [];

    /**
     * Execute the console command.
     * @throws \ReflectionException
     */
    protected function _handle()
    {
        foreach(ClassMapGenerator::createMap(app_path()) as $className => $classFile)
        {
            $constants = $methods = [];

            // 重置

            if($this->option('reset')) {
                goto generate;
            }

            $classReflection  = new ReflectionClass($className);
            if(!$classReflection->isSubclassOf('Illuminate\Database\Eloquent\Model')) continue;
            if($classReflection->isAbstract()) continue;

            /**
             * @var \Illuminate\Database\Eloquent\Model $model;
             */
            $model = $classReflection->newInstance();

            $classShortName = $classReflection->getShortName();
            $connectionName = $model->getConnectionName();
            $databaseName = $model->getConnection()->getDatabaseName();
            $tableName = $model->getTable();

            if($tableConstName = $this->option('table-const-name', null)) {
                $constants[] =<<<CODE
    /**
     * Table name.
     */
     const {$tableConstName} = '{$tableName}';
CODE;
            }

            $sql = "SELECT * FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = ? AND `TABLE_NAME` = ?";
            $fields = $model->getConnection()->select($sql, [$databaseName, $tableName]);
            foreach($fields as $field)
            {
                $builder = get_class($model->newModelQuery());

                if($this->option('gen-field-enum')) {
                    foreach($this->handleEnum($model, $field) as $enum) {
                        $constants[] =<<<CODE
    /**
     * {$enum['comment']}
     */
     const {$enum['name']} = '{$enum['value']}';
CODE;

                        $methodName = Str::camel(implode('_', ['scope', 'where', $enum['column'], $enum['value']]));
                        $methods[] =<<<CODE
    /**
     * As "where {$enum['column']} = {$enum['value']}"
     * @param \\{$builder} \$query
     */
    public function {$methodName}(\\{$builder} \$query)
    {
        \$query->where('{$enum['column']}', static::{$enum['name']});
    }
CODE;

                        $methodName = Str::camel(implode('_', ['is', $enum['column'], $enum['value']]));
                        $methods[] =<<<CODE
    /**
     * {$enum['column']} is {$enum['value']}?
     * @return bool
     */
    public function {$methodName}()
    {
        return \$this->{$enum['column']} === static::{$enum['name']};
    }
CODE;
                    }
                }

                if($this->option('gen-field-name', false)) {
                    $prefix = $this->option('field-name-prefix', '');
                    $constName = $this->name($field->COLUMN_NAME);
                    $constants[] =<<<CODE
    /**
     * Field name by {$field->COLUMN_NAME}
     * {$field->COLUMN_COMMENT}
     */
     const {$prefix}{$constName} = '{$tableName}.{$field->COLUMN_NAME}';
CODE;
                }

                if($this->option('gen-field-shortname', false)) {
                    $prefix = $this->option('field-shortname-prefix', '');
                    $constName = $this->name($field->COLUMN_NAME);
                    $constants[] =<<<CODE
    /**
     * Field shortname by {$field->COLUMN_NAME}
     * {$field->COLUMN_COMMENT}
     */
     const {$prefix}{$constName} = '{$field->COLUMN_NAME}';
CODE;
                }
            }

generate:
            $code = file_get_contents($classFile);
            if(!$code) continue;


            $code = preg_replace('%#generated-const-code-block.*?#generated-const-code-block\n\n%s', '', $code);
            if(count($constants)) {
                $constantCode = trim(implode("\n\n", $constants));
                $constantCode =<<<CODE
#generated-const-code-block

    {$constantCode}

#generated-const-code-block
CODE;
                $code = preg_replace('%\bclass\s+'.$classShortName.'\b.*?\{\n*%s', "\\0{$constantCode}\n\n", $code);
            }

            $code = preg_replace('%\n\n#generated-method-code-block.*?#generated-method-code-block%s', '', $code);
            if(count($methods)) {
                $methodCode = trim(implode("\n\n", $methods));
                $methodCode =<<<CODE
#generated-method-code-block

    {$methodCode}

#generated-method-code-block
CODE;

                $code = preg_replace('/\n*\}\s*$/', "\n\n{$methodCode}\\0", $code);
            }

            file_put_contents($classFile, $code);
        }

        foreach($this->enumTable as $locale => $data) {
            file_put_contents(resource_path("lang/{$locale}/table.php"), "<?php\nreturn " . var_export($data, true) . ';');
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param $field
     */
    protected function handleEnum($model, $field)
    {
        $data = [];

        $method = Str::camel(implode('_', ['get', $field->COLUMN_NAME, 'Enumerates']));
        if(method_exists($model, $method)) {
            $enums = call_user_func([$model, $method], $field);
        } elseif($field->DATA_TYPE === 'enum') {
            $enums = call_user_func([$model, 'getEnumerates'], $field);
        } else {
            return [];
        }

        foreach($enums as $value => $comments) {
            $data[] = [
                'name' => $this->option('field-enum-prefix') . $this->name($field->COLUMN_NAME, $value),
                'value' => $value,
                'field' => $field,
                'column' => $field->COLUMN_NAME,
                'comment' => $field->COLUMN_COMMENT
            ];

            if(is_array($comments)) {
                foreach($comments as $locale => $comment) {
                    $this->appendEnumTable(
                        $locale,
                        $model->getConnectionName(),
                        $model->getTable(),
                        $field->COLUMN_NAME,
                        $value,
                        $comment
                    );
                }
            } else {
                $this->appendEnumTable(
                    config('app.locale'),
                    $model->getConnectionName(),
                    $model->getTable(),
                    $field->COLUMN_NAME,
                    $value,
                    $comments
                );
            }
        }

        return $data;
    }

    /**
     * 往枚举表添加枚举说明
     * @param $locale
     * @param $connectionName
     * @param $tableName
     * @param $columnName
     * @param $value
     * @param $comment
     */
    protected function appendEnumTable($locale, $connectionName, $tableName, $columnName, $value, $comment)
    {
        if(!isset($this->enumTable[$locale])) {
            $this->enumTable[$locale] = [];
        }

        if(!isset($this->enumTable[$locale][$connectionName])) {
            $this->enumTable[$locale][$connectionName] = [];
        }

        if(!isset($this->enumTable[$locale][$connectionName][$tableName])) {
            $this->enumTable[$locale][$connectionName][$tableName] = [];
        }


        if(!isset($this->enumTable[$locale][$connectionName][$tableName][$columnName])) {
            $this->enumTable[$locale][$connectionName][$tableName][$columnName] = [];
        }

        $this->enumTable[$locale][$connectionName][$tableName][$columnName][$value] = $comment;
    }

    /**
     * @param mixed ...$name
     * @return string
     */
    protected function name(...$name) {
        $name = implode('_', $name);
        switch($this->option('const-name-style')) {
            case 'camel': return Str::camel($name);
            case 'Camel': return ucfirst(Str::camel($name));
            case 'snake': return strtolower(Str::snake($name));
            case 'SNAKE': return strtoupper(Str::snake($name));
            default: return implode($name);
        }
    }
}