<?php

namespace Faravel\Console\Commands;

use Illuminate\Console\Command;
use Barryvdh\Reflection\DocBlock;
use Composer\Autoload\ClassMapGenerator;
use Illuminate\Support\Str;

class BuildModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'faravel:BuildModel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     * @throws \ReflectionException
     */
    public function handle()
    {
        $language = [];

        foreach(ClassMapGenerator::createMap(app_path()) as $className => $classFile)
        {
            $classReflection  = new \ReflectionClass($className);
            if(!$classReflection->isSubclassOf('App\Model')) continue;
            if($classReflection->isAbstract()) continue;

            /**
             * @var Model $model;
             */
            $model = $classReflection->newInstance();

            $classShortName = $classReflection->getShortName();
            $connectionName = $classReflection->getConstant('CONNECTION');
            $databaseName = $model->getConnection()->getDatabaseName();
            $tableName = $model->getTable();
            $sql = "SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT FROM `information_schema`.`COLUMNS` WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND DATA_TYPE = 'enum'";

            $methods = [];
            $constants = [];
            $comments = [];

            $enumFields = $model->getConnection()->select($sql, [$databaseName, $tableName]);
            foreach($enumFields as $field)
            {
                $fieldName = $field->COLUMN_NAME;

                $fieldComment = '';
                if(preg_match('/^([^\s]+)/', $field->COLUMN_COMMENT, $result))
                {
                    $fieldComment = $result[1];
                }

                if(preg_match('/^enum\((.*?)\)$/', $field->COLUMN_TYPE, $result))
                {
                    $enumValues = explode("','", trim($result[1], "'"));
                }

                $enumComments = [];
                foreach($enumValues ?? [] as $enumValue)
                {
                    $enumComment = null;
                    if(preg_match("%{$enumValue}:([^/]+)%", $field->COLUMN_COMMENT, $result))
                    {
                        $enumComment = $result[1];
                    }

                    $enumComments[$enumValue] = $enumComment ?? $enumValue;
                }

                $language[$connectionName] = $language[$connectionName] ?? [];
                $language[$connectionName][$tableName] = $language[$connectionName][$tableName] ?? [];
                $language[$connectionName][$tableName][$fieldName] = $enumComments;

                foreach($enumComments ?? [] as $enumValue => $enumComment)
                {
                    $constant = strtoupper($fieldName) .'_'. strtoupper($enumValue);

                    $constants[] =<<<CODE
    /**
     * {$fieldComment} {$enumComment}
     */
     const $constant = '{$enumValue}';
CODE;

                    $methodName = Str::camel(implode('_', ['scope', 'where', $fieldName, $enumValue]));
                    $methods[] =<<<CODE
    /**
     * As "where {$fieldName} = {$enumValue}"
     * @param \Illuminate\Database\Eloquent\Builder \$query
     * @return {$classShortName}|\Illuminate\Database\Eloquent\Builder
     */
    public function {$methodName}(\Illuminate\Database\Eloquent\Builder \$query) {
        return \$query->where('{$fieldName}', static::{$constant});
    }
CODE;

                    $methodName = Str::camel(implode('_', ['is', $fieldName, $enumValue]));
                    $methods[] =<<<CODE
    /**
     * {$fieldName} is {$enumValue}?
     * @return bool
     */
    public function {$methodName}() {
        return \$this->{$fieldName} === static::{$constant};
    }
CODE;
                }
            }

            $code = file_get_contents($classFile);
            if(!$code) continue;

            constant_code:
            $code = preg_replace('%#<BuildTableFieldEnum:const>.*?#</BuildTableFieldEnum:const>\n\n%s', '', $code);
            if(!count($constants)) {
                goto method_code;
            }

            $constantCode = trim(implode("\n\n", $constants));
            $constantCode =<<<CODE
#<BuildTableFieldEnum:const>
    
    {$constantCode}
    
#</BuildTableFieldEnum:const>
CODE;

            $code = preg_replace('%[^\S\n]*\bconst\s+TABLE\s*=.*%', "{$constantCode}\n\n\\0", $code);

            method_code:
            $code = preg_replace('%\n#<BuildTableFieldEnum:method>.*?#</BuildTableFieldEnum:method>\n%s', '', $code);
            if(!count($methods)) {
                goto comment;
            }

            $methodCode = trim(implode("\n\n", $methods));
            $methodCode =<<<CODE
#<BuildTableFieldEnum:method>
    
    {$methodCode}
    
#</BuildTableFieldEnum:method>
CODE;

            $code = preg_replace('/\}\s*$/', "\n{$methodCode}\n\\0", $code);

            comment:
            $docBlock = new DocBlock($classReflection, new DocBlock\Context($classReflection->getNamespaceName()));

            foreach($comments as $comment) {
                $tag = DocBlock\Tag::createInstance("{$comment}");
                $docBlock->appendTag($tag);
            }

            $serializer = new DocBlock\Serializer();
            $commentCode = $serializer->getDocComment($docBlock);

            $code = str_replace($classReflection->getDocComment(), $commentCode, $code);

            end:
            file_put_contents($classFile, $code);
            continue;
        }

        file_put_contents(resource_path('lang/zh-CN/table.php'), "<?php\nreturn ". var_export($language, true) .';');
    }
}