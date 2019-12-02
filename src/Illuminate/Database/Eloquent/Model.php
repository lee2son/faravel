<?php

namespace Faravel\Illuminate\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

/**
 * Class Model
 * @package Faravel\Database\Eloquent
 * @mixin Builder
 */
class Model extends Eloquent
{
    const CONNECTION = null;

    public function __construct(array $attributes = [])
    {
        if(static::CONNECTION) {
            $this->connection = static::CONNECTION;
        }

        $this->perPage = env('PAGE_SIZE', $this->perPage);

        parent::__construct($attributes);
    }

    /**
     * 获取表名
     * @return string
     */
    public function getTable()
    {
        return $this->table ?? Str::snake(class_basename($this));
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * @param $field
     * @return array
     */
    public function getEnums($field)
    {
        if($field->DATA_TYPE !== 'enum') {
            return [];
        }

        if(preg_match('/^enum\((.*?)\)$/', $field->COLUMN_TYPE, $result))
        {
            $enumValues = explode("','", trim($result[1], "'"));
        }

        $enumComments = [];
        foreach($enumValues as $enumValue)
        {
            $enumComment = null;
            $isDeprecated = false;

            if(preg_match("%(-)*\b{$enumValue}:([^/]+)%", $field->COLUMN_COMMENT, $result))
            {
                $enumComment = $result[2];
                switch($result[1]) {
                    case '-':
                        $isDeprecated = true;
                }
            }

            $enumComments[$enumValue] = [
                'deprecated' => $isDeprecated,
                'comment' => $enumComment ?? $enumValue,
            ];
        }

        return $enumComments;
    }
}