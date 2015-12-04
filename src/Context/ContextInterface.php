<?php

namespace Elixir\View\Context;

use Elixir\View\StorageInterface;
use Elixir\View\ViewContextInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface ContextInterface
{
    /**
     * @param ViewContextInterface $view
     */
    public function setView(ViewContextInterface $view);
    
    /**
     * @param string|StorageInterface $template
     * @return string
     */
    public function render($template = null);
}
