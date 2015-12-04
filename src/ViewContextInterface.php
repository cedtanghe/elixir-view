<?php

namespace Elixir\View;

use Elixir\View\Context\ContextInterface;
use Elixir\View\ViewInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface ViewContextInterface extends ViewInterface
{
    /**
     * @param mixed $context
     */
    public function setContext(ContextInterface $context = null);
    
    /**
     * @return ContextInterface
     */
    public function context();
    
    /**
     * @param ContextInterface $context
     * @return ContextInterface
     */
    public function injectTo(ContextInterface $context);
}
