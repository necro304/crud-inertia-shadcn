<?php

namespace isaac@example.com\crud-generator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \isaac@example.com\crud-generator\crud-generator
 */
class crud-generator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \isaac@example.com\crud-generator\crud-generator::class;
    }
}
