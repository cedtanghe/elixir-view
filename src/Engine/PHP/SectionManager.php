<?php

namespace Elixir\View\Engine\PHP;

use Elixir\Dispatcher\DispatcherInterface;
use Elixir\Dispatcher\DispatcherTrait;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class SectionManager implements DispatcherInterface 
{
    use DispatcherTrait;

    /**
     * @var array
     */
    protected $opened = [];

    /**
     * @var array
     */
    protected $sections = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $compiled = [];

    /**
     * @var string 
     */
    protected $current;

    /**
     * @param string $section
     * @param array $options
     * @throws \LogicException
     */
    public function open($section, array $options = []) 
    {
        if (in_array($section, $this->opened)) 
        {
            throw new \LogicException(sprintf('A section "%s" is already opened.', $section));
        }
        
        $this->current = $section;
        $this->opened[] = $this->current;
        $this->sections[$this->current][] = '';
        $this->options[$this->current] = array_merge(
            isset($this->options[$this->current]) ? $this->options[$this->current] : [], $options
        );
        
        unset($this->compiled[$this->current]);
        
        ob_start();
    }

    /**
     * @return string
     */
    public function parent()
    {
        return '{PARENT_SECTION}';
    }

    /**
     * @throws \LogicException
     */
    public function close()
    {
        if(count($this->opened) == 0)
        {
            throw new \LogicException('No section has been started.');
        }

        $section = array_pop($this->opened);
        $this->sections[$section][count($this->sections[$section]) - 1] = ob_get_clean();
        $this->current = null;
    }

    /**
     * @param string $section
     * @return string
     */
    public function mask($section) 
    {
        if (isset($this->sections[$section])) 
        {
            return sprintf('{SECTION : %s}', $section);
        }

        return '';
    }

    /**
     * @param string $section
     * @return string
     */
    public function compile($section) 
    {
        if ($this->current != $section && isset($this->sections[$section])) 
        {
            if (isset($this->compiled[$section])) 
            {
                return $this->compiled[$section];
            }

            $sections = array_reverse(array_slice($this->sections[$section], 0));
            $content = '';
            $replace = '';

            while (count($sections) > 0)
            {
                $content = str_replace($this->parent(), $replace, array_shift($sections));
                $replace = $content;
            }

            if (false !== strpos($content, '{SECTION :'))
            {
                if (preg_match_all('/{SECTION : (.+)}/', $content, $matches)) 
                {
                    foreach ($matches[1] as $s)
                    {
                        $content = str_replace($this->mask($s), $this->compile($s, ''), $content);
                    }
                }
            }

            $event = new SectionEvent(
                SectionEvent::COMPILE, 
                [
                    'section' => $section, 
                    'content' => $content, 
                    'options' => $this->options[$section]
                ]
            );
            
            $this->dispatch($event);
            $content = $event->getContent();
            $this->compiled[$section] = $content;

            return $content;
        }
        
        return '';
    }

    /**
     * @return void
     */
    public function reset() 
    {
        $i = count($this->opened);

        while ($i--) 
        {
            ob_end_clean();
        }

        $this->compiled = [];
        $this->opened = [];
        $this->sections = [];
        $this->options = [];
        $this->current = null;
    }

    /**
     * @param string $content
     * @return string
     */
    public function parse($content) 
    {
        foreach (array_keys($this->sections) as $section) 
        {
            $content = str_replace($this->mask($section), $this->compile($section, ''), $content);
        }
        
        return $content;
    }
}
