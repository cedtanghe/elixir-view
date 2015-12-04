<?php

namespace Elixir\View\Engine\PHP;

use Elixir\View\Engine\PHP\SectionManager;
use Elixir\View\SharedTrait;
use Elixir\View\ViewInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class PHP implements ViewInterface
{
    use SharedTrait;
    
    /**
     * @var string
     */
    const CONTENT_KEY = '_content';
    
    /**
     * @var string 
     */
    protected $parent;
    
    /**
     * @var SectionManager
     */
    protected $sectionManager;
    
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
    public function render($template, array $parameters = []);
}
