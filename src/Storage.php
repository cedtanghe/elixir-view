<?php

namespace Elixir\View;

use Elixir\View\StorageInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Storage implements StorageInterface 
{
    /**
     * @var string
     */
    protected $content;
    
    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $content
     * @param string $type
     */
    public function __construct($content, $type = self::TYPE_FILE)
    {
        $this->content = $content;
        $this->type = $type;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @ignore
     */
    public function __toString()
    {
        return $this->getContent();
    }
}
