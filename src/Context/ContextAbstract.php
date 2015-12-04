<?php

namespace Elixir\View\Context;

use Elixir\View\Context\ContextInterface;
use Elixir\View\ViewContextInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
abstract class ContextAbstract implements ContextInterface
{
    /**
     * @var ViewContextInterface 
     */
    protected $view;
    
    /**
     * {@inheritdoc}
     */
    public function setView(ViewContextInterface $view)
    {
        $this->view = clone $view;
        $this->view->setContext($this);
    }
    
    /**
     * {@inheritdoc}
     */
    abstract public function render($template = null);
    
    /**
     * @ignore
     */
    public function __toString() 
    {
        return $this->render(null);
    }
}
