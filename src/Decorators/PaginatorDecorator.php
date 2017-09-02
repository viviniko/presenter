<?php

namespace Viviniko\Presenter\Decorators;

use Viviniko\Presenter\Contracts\Decorator;
use Illuminate\Contracts\Pagination\Paginator;
use Viviniko\Presenter\AutoPresenter;
use ReflectionObject;

class PaginatorDecorator implements Decorator
{
    /**
     * The auto presenter instance.
     *
     * @var \Viviniko\Presenter\AutoPresenter
     */
    protected $autoPresenter;

    /**
     * Create a new paginator decorator.
     *
     * @param \Viviniko\Presenter\AutoPresenter $autoPresenter
     *
     * @return void
     */
    public function __construct(AutoPresenter $autoPresenter)
    {
        $this->autoPresenter = $autoPresenter;
    }

    /**
     * Can the subject be decorated?
     *
     * @param mixed $subject
     *
     * @return bool
     */
    public function canDecorate($subject)
    {
        return $subject instanceof Paginator;
    }

    /**
     * Decorate a given subject.
     *
     * @param object $subject
     *
     * @return object
     */
    public function decorate($subject)
    {
        $items = $this->getItems($subject);

        foreach ($items->keys() as $key) {
            $items->put($key, $this->autoPresenter->decorate($items->get($key)));
        }

        return $subject;
    }

    /**
     * Decorate a paginator instance.
     *
     * We're using hacky reflection for now because there is no public getter.
     *
     * @param \Illuminate\Contracts\Pagination\Paginator $subject
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getItems(Paginator $subject)
    {
        $object = new ReflectionObject($subject);

        $items = $object->getProperty('items');
        $items->setAccessible(true);

        return $items->getValue($subject);
    }

    /**
     * Get the auto presenter instance.
     *
     * @codeCoverageIgnore
     *
     * @return \Viviniko\Presenter\AutoPresenter
     */
    public function getAutoPresenter()
    {
        return $this->autoPresenter;
    }
}
