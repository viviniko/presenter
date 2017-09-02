<?php

namespace Viviniko\Presenter\Decorators;

use Viviniko\Presenter\Contracts\Decorator;
use Illuminate\Support\Collection;
use Viviniko\Presenter\AutoPresenter;

class ArrayDecorator implements Decorator
{
    /**
     * The auto presenter instance.
     *
     * @var \Viviniko\Presenter\AutoPresenter
     */
    protected $autoPresenter;

    /**
     * Create a new array decorator.
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
        return is_array($subject) || $subject instanceof Collection;
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
        foreach ($subject as $key => $atom) {
            $subject[$key] = $this->autoPresenter->decorate($atom);
        }

        return $subject;
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
