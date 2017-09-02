<?php

namespace Viviniko\Presenter\Facades;

use Illuminate\Support\Facades\Facade;

class Presenter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'presenter';
    }
}
