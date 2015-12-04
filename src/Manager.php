<?php

namespace Elixir\View;

use Elixir\View\SharedTrait;
use Elixir\View\StorageInterface;
use Elixir\View\ViewInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Manager implements ViewInterface
{
    use SharedTrait;
    
    /**
     * @var array
     */
    protected $engines = [];

    /**
     * @var ViewInterface 
     */
    protected $defaultEngine;

    /**
     * {@inheritdoc}
     */
    public function getDefaultTemplateExtension()
    {
        if ($this->defaultEngine)
        {
            return $this->defaultEngine->getDefaultTemplateExtension();
        }
        
        return null;
    }
    
    /**
     * @return ViewInterface
     */
    public function getDefaultEngine()
    {
        return $this->defaultEngine;
    }

    /**
     * @param string $name
     * @param ViewInterface $engine
     * @param string $extension
     * @param boolean $defaultEngine
     */
    public function registerEngine($name, ViewInterface $engine, $extension = null, $defaultEngine = true)
    {
        $this->engines[$name] = [
            'extension' => $extension ? : $engine->getDefaultTemplateExtension(),
            'engine' => $engine
        ];

        if ($defaultEngine) 
        {
            $this->defaultEngine = $engine;
        }
    }
    
    /**
     * @param string $name
     * @return boolean
     */
    public function hasEngine($name)
    {
        return isset($this->engines[$name]);
    }
    
    /**
     * @param string $name
     * @param mixed $default
     * @return ViewInterface|mixed
     */
    public function getEngine($name, $default = null) 
    {
        if (isset($this->_engines[$name]))
        {
            return $this->_engines[$name]['engine'];
        }

        return is_callable($default) ? call_user_func($default) : $default;
    }

    /**
     * @param string $extension
     * @param mixed $default
     * @return ViewInterface|mixed
     */
    public function getEngineByExtension($extension, $default = null) 
    {
        foreach ($this->engines as $name => $data)
        {
            if (preg_match('/' . $data['extension'] . '/i', $extension)) 
            {
                return $value['engine'];
            }
        }

        return is_callable($default) ? call_user_func($default) : $default;
    }

    /**
     * @param boolean $withInfos
     * @return array
     */
    public function allEngines($withInfos = false) 
    {
        $engines = [];

        foreach ($this->engines as $name => $data) 
        {
            $engines[] = $withInfos ? $data + ['default' => $data['engine'] === $this->defaultEngine] : $data['engine'];
        }
        
        return $engines;
    }

    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
    public function render($template, array $parameters = [])
    {
        if (!$template instanceof StorageInterface)
        {
            $template = new Storage($template, StorageInterface::TYPE_FILE);
        }
        
        if ($template->getType() === StorageInterface::TYPE_FILE)
        {
            $extension = pathinfo($template->getContent(), PATHINFO_EXTENSION);
            $engine = $this->getEngineByExtension($extension);
            
            if (null === $engine)
            {
                throw new \RuntimeException(sprintf('No view engine for extension "%s".', $extension));
            }
        }
        else
        {
            $engine = $this->getDefaultEngine();
        }
        
        // Merge with shared keys
        foreach ($this->shared as $key => $value)
        {
            $engine->share($key, $value);
        }
        
        return $engine->render($template, $parameters);
    }
}
