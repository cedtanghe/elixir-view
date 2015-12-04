<?php

namespace Elixir\View;

use Elixir\View\StorageInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface ViewInterface 
{
    /**
     * @return string
     */
    public function getDefaultTemplateExtension();
    
    /**
     * @param string $key
     * @param mixed $value
     */
    public function share($key, $value);
    
    /**
     * @param string $key
     */
    public function unshare($key);
    
    /**
     * @param string $key
     * @return boolean
     */
    public function isShared($key);

    /**
     * @param string|StorageInterface $template
     * @param array $parameters
     * @return string
     */
    public function render($template, array $parameters = []);
}
