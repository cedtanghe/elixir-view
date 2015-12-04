<?php

namespace Elixir\View\Engine\PHP;

use Elixir\View\PHP\PHP;
use Elixir\View\StorageInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Parser 
{
    /**
     * @var PHP
     */
    protected $__view;
    
    /**
     * @var array
     */
    protected $__whiteList = [];
    
    /**
     * @var array
     */
    protected $__parameters = [];

    /**
     * @param PHP $view
     */
    public function __construct(PHP $view, $whiteList = [])
    {
        $this->__view = $view;
        $this->__whiteList = $whiteList;
    }

    /**
     * @param StorageInterface $__template
     * @param array $__parameters
     * @return string
     * @throws \UnexpectedValueException
     * @throws \Exception
     */
    public function parse($__template, array $__parameters = [])
    {
        $this->__parameters = $__parameters;
        
        ob_start();

        try 
        {
            if ($template_->getType() === StorageInterface::TYPE_STRING) 
            {
                eval('; ?>' . $__template->getContent() . '<?php ;');
            } 
            else
            {
                $include = include $__template;

                if (false === $include) 
                {
                    throw new \UnexpectedValueException(sprintf('File "%s" include failed.', $__template));
                }
            }

            $content = ob_get_clean();
        } 
        catch (\Exception $e) 
        {
            ob_end_clean();
            throw $e;
        }
        
        return $content;
    }

    /**
     * @ignore
     */
    public function __isset($key) 
    {
        return array_key_exists($key, $this->__parameters);
    }

    /**
     * @ignore
     */
    public function __get($key) 
    {
        if ($this->__isset($key))
        {
            return $this->__parameters[$key];
        }
        
        return null;
    }

    /**
     * @ignore
     */
    public function __set($key, $value)
    {
        $this->__parameters[$key] = $value;
    }

    /**
     * @ignore
     */
    public function __unset($key) 
    {
        unset($this->__parameters[$key]);
    }

    /**
     * @ignore
     * @throws \BadMethodCallException
     */
    public function __call($method, $arguments)
    {
        if (in_array($method, $this->__whiteList))
        {
            return call_user_func_array([$this->__view, $method], $arguments);
        }
        
        throw new \BadMethodCallException(sprintf('Method "%s" is not defined.', $method));
    }
}
