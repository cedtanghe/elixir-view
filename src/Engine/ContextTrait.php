<?php

namespace Elixir\View\Engine;

use Elixir\View\Helper\ContextInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait ContextTrait
{
    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * {@inheritdoc}
     */
    public function setContext(ContextInterface $context = null)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function context()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function injectInto(ContextInterface $context)
    {
        $context->setView($this);

        return $context;
    }

    /**
     * @ignore
     */
    public function __clone()
    {
        $this->context = null;
    }
}
