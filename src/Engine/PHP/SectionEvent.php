<?php

namespace Elixir\View\Engine\PHP;

use Elixir\Dispatcher\Event;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class SectionEvent extends Event 
{
    /**
     * @var string
     */
    const COMPILE = 'compile';

    /**
     * @var string
     */
    protected $section;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $options;

    /**
     * {@inheritdoc}
     * @param array $params
     */
    public function __construct($type, array $params = []) 
    {
        parent::__construct($type);

        $params += [
            'section' => null,
            'content' => null,
            'options' => null
        ];

        $this->section = $params['section'];
        $this->content = $params['content'];
        $this->options = $params['options'];
    }

    /**
     * @return string
     */
    public function getSection() 
    {
        return $this->section;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content) 
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
