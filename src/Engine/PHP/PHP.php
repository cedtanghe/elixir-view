<?php

namespace Elixir\View\Engine\PHP;

use Elixir\Dispatcher\DispatcherInterface;
use Elixir\Dispatcher\DispatcherTrait;
use Elixir\Helper\HelperInterface;
use Elixir\Helper\HelperManager;
use Elixir\View\Context\ContextInterface;
use Elixir\View\Engine\PHP\SectionManager;
use Elixir\View\SharedTrait;
use Elixir\View\ViewContextInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class PHP implements ViewContextInterface, DispatcherInterface 
{
    use SharedTrait;
    use DispatcherTrait;
    
    /**
     * @var string
     */
    const CONTENT_KEY = '_content';
    
    /**
     * @var string 
     */
    protected $parent;
    
    /**
     * @var ContextInterface
     */
    protected $context;
    
    /**
     * @var Parser
     */
    protected $parser;
    
    /**
     * @var SectionManager
     */
    protected $sectionManager;
    
    /**
     * @var callable 
     */
    protected $escaper;
    
    /**
     * @var HelperManager
     */
    protected $helperManager;


    /**
     * @param callable $escaper
     */
    public function __construct(callable $escaper = null) 
    {
        $this->escaper = $escaper;
        
        $this->sectionManager = new SectionManager();
        $this->sectionManager->addListener(SectionEvent::COMPILE, function(SectionEvent $e)
        {
            $this->dispatch($e);
        });
        
        $this->parser = new Parser($this, [
            'extend',
            'open',
            'parent',
            'close',
            'section',
            'escape',
            'context',
            'helper'
        ]);
    }
    
    /**
     * @param callable $escaper
     */
    public function setEscaper(callable $escaper)
    {
        $this->escaper = $escaper;
    }
    
    /**
     * @return callable
     */
    public function getEscaper()
    {
        return $this->escaper;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultTemplateExtension()
    {
        return 'ph(tml|p)';
    }
    
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
        
        throw new \InvalidArgumentException(sprintf('Helper "%s" is not defined.', $name));
    }

    /**
     * @param string $template
     */
    public function extend($template)
    {
        $this->parent = $template;
    }
    
    /**
     * @see SectionManager::open()
     */
    public function open($section, array $options = [])
    {
        $this->sectionManager->open($section, $options);
    }
    
    /**
     * @see SectionManager::parent()
     */
    public function parent()
    {
        return $this->sectionManager->parent();
    }
    
    /**
     * @see SectionManager::close()
     */
    public function close()
    {
        $this->sectionManager->close();
    }
    
    /**
     * @see SectionManager::mask()
     */
    public function section($section)
    {
        return $this->sectionManager->mask($section);
    }
    
    /**
     * @param mixed $data
     * @param string $strategy
     * @return mixed
     */
    public function escape($data, $strategy = 'html')
    {
        $escaper = $this->escaper ?: function($value)
        {
            return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', true);
        };
        
        if (is_array($data) || is_object($data) || $data instanceof \Traversable) 
        {
            foreach ($data as &$value) 
            {
                $value = $this->escape($value, $strategy);
            }
        } 
        else 
        {
            $data = $escaper($data, ['strategy' => $strategy]);
        }
        
        return $data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function render($template, array $parameters = [])
    {
        $this->parent = null;
        
        try
        {
            $content = $this->parser->parse($template, $parameters);
        }
        catch(\Exception $e)
        {
            $this->sectionManager->reset();
            throw $e;
        }
        
        if(!empty($this->parent))
        {
            $parameters[self::CONTENT_KEY] = $content;
            $content = $this->render($this->parent, $parameters);
            unset($parameters[self::CONTENT_KEY]);
        }
        else
        {
            $content = $this->sectionManager->parse($content);
        }
        
        return $content;
    }
    
    /**
     * @ignore
     */
    public function __clone() 
    {
        $this->context = null;
    }
}
