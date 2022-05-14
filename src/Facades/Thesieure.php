<?php

namespace Dinhdjj\Thesieure\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dinhdjj\Thesieure\Thesieure
 */
class Thesieure extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'thesieure';
    }
}
