<?php

namespace Viviniko\Presenter;

use ArrayAccess;
use Viviniko\Presenter\Contracts\Decorator;
use Illuminate\Support\Arr;

class AutoPresenter implements ArrayAccess
{
    /**
     * The registered decorators.
     *
     * @var \Viviniko\Presenter\Contracts\Decorator[]
     */
    protected $decorators = [];

    /**
     * @var array
     */
    protected $presenters = [];

    /**
     * AutoPresenter constructor.
     * @param array $presenters
     */
    public function __construct($presenters = [])
    {
        $this->presenters = $presenters;
    }

    /**
     * Get the specified presenter.
     *
     * @param  object|string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $key = is_object($key) ? get_class($key) : $key;
        foreach ($this->presenters as $name => $presenter) {
            if ($name === $key || is_subclass_of($key, $name)) {
                return $presenter;
            }
        }

        return $default;
    }

    /**
     * Set a given presenter.
     *
     * @param  array|string  $key
     * @param  mixed   $value
     * @return void
     */
    public function set($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            Arr::set($this->presenters, $key, $value);
        }
    }

    /**
     * Determine if the given presenter exists.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        $presenter = $this->get(is_object($key) ? get_class($key) : $key);

        return $presenter && class_exists($presenter);
    }

    /**
     * Things go in, get decorated (or not) and are returned.
     *
     * @param mixed $subject
     *
     * @return mixed
     */
    public function decorate($subject)
    {
        foreach ($this->decorators as $decorator) {
            if ($decorator->canDecorate($subject)) {
                return $decorator->decorate($subject);
            }
        }

        return $subject;
    }

    /**
     * Push a decorator.
     *
     * @param \Viviniko\Presenter\Contracts\Decorator $decorator
     *
     * @return void
     */
    public function pushDecorator(Decorator $decorator)
    {
        $this->decorators[] = $decorator;
    }

    /**
     * Get the registered decorators.
     *
     * @return \Viviniko\Presenter\Contracts\Decorator[]
     */
    public function getDecorators()
    {
        return $this->decorators;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}
