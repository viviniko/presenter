<?php

namespace Viviniko\Presenter;

use Illuminate\Support\Traits\Macroable;

class Presenter
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The resource that is the object that was decorated.
     *
     * @var object|array
     */
    protected $wrapped;

    /**
     * Set the wrapped.
     *
     * @param object|array $wrapped
     * @return $this
     */
    public function setWrapped($wrapped)
    {
        $this->wrapped = $wrapped;

        return $this;
    }

    /**
     * Get the wrapped.
     *
     * @return array|object
     */
    public function getWrapped()
    {
        return $this->wrapped;
    }

    /**
     * Get/Set the wrapped attribute.
     *
     * @param mixed $key
     * @param null $default
     * @return mixed
     */
    public function attr($key = null, $default = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                data_set($this->wrapped, $k, $v);
            }

            return $this->wrapped;
        }

        return data_get($this->wrapped, $key, $default);
    }

    /**
     * Magic method access initially tries for local fields, then defers to the
     * decorated object.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        $methods = $this->getPossibleKeys($key);
        foreach ($methods as $method) {
            if (method_exists($this, $method) || static::hasMacro($method)) {
                return $this->$method();
            }
        }

        return $this->attr($key);
    }

    /**
     * Is the key set on either the presenter or the wrapped object?
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        $methods = $this->getPossibleKeys($key);
        foreach ($methods as $method) {
            if (method_exists($this, $method) || static::hasMacro($method)) {
                return true;
            }
        }
        $default = __CLASS__ . spl_object_hash($this);

        return $this->attr($key, $default) != $default;
    }

    /**
     * Magic Method access for methods called against the presenter looks for a
     * method on the resource, or throws an exception if none is found.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (is_object($this->wrapped) && method_exists($this->wrapped, $method)) {
            return $this->wrapped->$method(...$parameters);
        }
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        throw new \BadMethodCallException("Method [{$method}] does not exist.");
    }

    protected function getPossibleKeys($key)
    {
        $keys = [$key];
        if (ctype_alpha($key[0]) && $key != ($camel = camel_case($key))) {
            $keys[] = $camel;
        }

        return $keys;
    }
}