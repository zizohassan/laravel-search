<?php

namespace TheAMasoud\LaravelSearch;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    public function scopeSearch(Builder $builder,string $searchIn,string $input)
    {
        $builder->when(request()->has($input),function($q) use($searchIn,$input){    
            $q->where($searchIn, 'LIKE' ,'%'.request($input).'%')->get();
        });
    }
    public function scopeJsonSearch(Builder $builder,string $searchIn,$input)
    {
        $builder->when(request()->has($input),function($q) use($searchIn,$input){    
            $q->whereJsonContains($searchIn, request($input))->get();
        });
    }  

}
