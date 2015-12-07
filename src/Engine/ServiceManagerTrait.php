<?php

namespace Elixir\View\Engine;

use Elixir\Filter\FilterInterface;
use Elixir\Filter\FilterManager;
use Elixir\Helper\HelperInterface;
use Elixir\Helper\HelperManager;
use Elixir\Validator\ValidatorInterface;
use Elixir\Validator\ValidatorManager;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait ServiceManagerTrait 
{
    /**
     * @var HelperManager 
     */
    protected $helperManager;
    
    /**
     * @var FilterManager 
     */
    protected $filterManager;
    
    /**
     * @var ValidatorManager 
     */
    protected $validatorManager;

    /**
     * @param HelperManager $manager
     */
    public function setHelperManager(HelperManager $manager)
    {
        $this->helperManager = $manager;
        $this->helperManager->setContext($this);
    }
    
    /**
     * @return HelperManager
     */
    public function getHelperManager()
    {
        return $this->helperManager;
    }
    
    /**
     * @param string $name
     * @param array $options
     * @return HelperInterface
     */
    public function helper($name, array $options = [])
    {
        if ($this->helperManager)
        {
            return $this->helperManager->get($name, $options);
        }
        
        throw new \InvalidArgumentException(sprintf('Helper Manager is not defined.', $name));
    }
    
    /**
     * @param FilterManager $manager
     */
    public function setFilterManager(FilterManager $manager)
    {
        $this->filterManager = $manager;
    }
    
    /**
     * @return FilterManager
     */
    public function getFilterManager()
    {
        return $this->filterManager;
    }
    
    /**
     * @param string $name
     * @param array $options
     * @return FilterInterface
     */
    public function filter($name, array $options = [])
    {
        if ($this->filterManager)
        {
            return $this->filterManager->get($name, $options);
        }
        
        throw new \InvalidArgumentException(sprintf('Filter Manager is not defined.', $name));
    }
    
    /**
     * @param ValidatorManager $manager
     */
    public function setValidatorManager(ValidatorManager $manager)
    {
        $this->validatorManager = $manager;
    }
    
    /**
     * @return ValidatorManager
     */
    public function getValidatorManager()
    {
        return $this->validatorManager;
    }
    
    /**
     * @param string $name
     * @param array $options
     * @return ValidatorInterface
     */
    public function validator($name, array $options = [])
    {
        if ($this->validatorManager)
        {
            return $this->validatorManager->get($name, $options);
        }
        
        throw new \InvalidArgumentException(sprintf('Validator Manager is not defined.', $name));
    }
}
