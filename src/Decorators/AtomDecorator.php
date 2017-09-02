<?php

namespace Viviniko\Presenter\Decorators;

use Viviniko\Presenter\Contracts\Decorator;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Viviniko\Presenter\AutoPresenter;
use Viviniko\Presenter\Exceptions\PresenterNotFoundException;

class AtomDecorator implements Decorator
{
    /**
     * The auto presenter instance.
     *
     * @var \Viviniko\Presenter\AutoPresenter
     */
    protected $autoPresenter;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Create a new atom decorator.
     *
     * @param \Viviniko\Presenter\AutoPresenter $autoPresenter
     * @param \Illuminate\Contracts\Container\Container  $container
     *
     * @return void
     */
    public function __construct(AutoPresenter $autoPresenter, Container $container)
    {
        $this->autoPresenter = $autoPresenter;
        $this->container = $container;
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
        return $this->autoPresenter->has($subject);
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
        if (is_object($subject)) {
            $subject = clone $subject;
        }

        if ($subject instanceof Model) {
            foreach ($subject->getRelations() as $relationName => $model) {
                $subject->setRelation($relationName, $this->autoPresenter->decorate($model));
            }
        }

        if (!class_exists($presenter = $this->autoPresenter->get($subject))) {
            throw new PresenterNotFoundException($presenter);
        }

        return $this->container->make($presenter)->setWrapped($subject);
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

    /**
     * Get the container instance.
     *
     * @codeCoverageIgnore
     *
     * @return \Illuminate\Contracts\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
