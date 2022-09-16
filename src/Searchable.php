<?php

namespace TheAMasoud\LaravelSearch;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{

    /***
     *search in one column by your database column and your request key
     * @usage Model::search('title','search')->get();
     * @param Builder $builder
     * @param string $searchIn
     * @param string $input
     * @param string $signDefinition
     * @return void
     */
    public function scopeSearch(Builder $builder, string $searchIn, string $input, $signDefinition = '=')
    {
        $builder->when(request()->has($input), function ($q) use ($searchIn, $input, $signDefinition) {
            $this->returnResult($q, $searchIn, $input, 'where', $signDefinition);
        });
    }

    /**
     * search in json column by your database column and your request key
     * @usage Model::jsonSearch('title->key','search')->get();
     * @param Builder $builder
     * @param string $searchIn
     * @param string $input
     * @param string $signDefinition
     * @return void
     */
    public function scopeJsonSearch(Builder $builder, string $searchIn, string $input, $signDefinition = 'LIKE')
    {
        $builder->when(request()->has($input), function ($q) use ($searchIn, $input, $signDefinition) {
            $this->returnResult($q, $searchIn, $input, 'where', $signDefinition);
        });
    }

    /***
     * search in multiple columns by your database column and your request key but them in array
     * @usage Model::searchMultiple(['name'=>'search_name','email'=>'search_email'])->get();
     * @param Builder $builder
     * @param array $fields
     * @param string $signDefinition
     * @return void
     */
    public function scopeSearchMultiple(Builder $builder, array $fields, $signDefinition = 'LIKE')
    {
        $builder->where(function ($q) use ($fields, $signDefinition) {
            foreach ($fields as $key => $field) {
                if (!request()->has($field)) {
                    continue;
                }
                $this->returnResult($q, $key, $field, 'orWhere', $signDefinition);
            }
        });
    }

    /**
     * search in multiple columns by with one value
     * @usage Model::searchInMultiple(['name','email','bio'],'search')->get();
     * @param Builder $builder
     * @param array $searchIn
     * @param string $input
     * @param string $signDefinition
     * @return void
     */
    public function scopeSearchInMultiple(Builder $builder, array $searchIn, string $input, $signDefinition = 'LIKE')
    {
        $builder->when(request()->has($input), function ($q) use ($searchIn, $input, $signDefinition) {
            $q->where(function ($q) use ($searchIn, $input, $signDefinition) {
                foreach ($searchIn as $field) {
                    $this->returnResult($q, $field, $input, 'orWhere', $signDefinition);
                }
            });
        });
    }

    /***
     * add select method dynamic, so you can use in the future either laravel query function
     * @param $q
     * @param $searchIn
     * @param $input
     * @param $searchType
     * @param $signDefinition
     * @return mixed
     */
    protected function returnResult($q, $searchIn, $input, $searchType, $signDefinition)
    {
        return $q->{$searchType}($this->buildQueryBasedOnSignDefinition($searchIn, $input, $signDefinition));
    }

    /**
     * for value handel on select
     * @param $searchIn
     * @param $input
     * @param $signDefinition
     * @return array
     */
    protected function buildQueryBasedOnSignDefinition($searchIn, $input, $signDefinition)
    {
        switch ($signDefinition) {
            case 'like':
                return [$searchIn, 'like', '%' . request($input) . '%'];
            default:
                return [$searchIn, '=', request($input)];
        }
    }
}
