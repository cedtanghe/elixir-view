<?php

namespace Elixir\View;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait SharedTrait 
{
    /**
     * @var array 
     */
    protected $shared = [];
    
    /**
     * {@inheritdoc}
     */
    public function share($key, $value)
    {
        $this->shared[$key] = $value;
    }
    
    /**
     * {@inheritdoc}
     */
    public function unshare($key)
    {
        unset($this->shared[$key]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function isShared($key)
    {
        return array_key_exists($key, $this->shared);
    }
}
