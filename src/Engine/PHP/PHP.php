<?php

namespace Elixir\View\Engine\PHP;

use Elixir\Dispatcher\DispatcherInterface;
use Elixir\Dispatcher\DispatcherTrait;
use Elixir\View\Engine\ContextTrait;
use Elixir\View\Engine\ServiceManagerTrait;
use Elixir\View\SharedTrait;
use Elixir\View\ViewContextInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class PHP implements ViewContextInterface, DispatcherInterface
{
    use SharedTrait;
    use ServiceManagerTrait;
    use ContextTrait;
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
     * @param callable $escaper
     */
    public function __construct(callable $escaper = null)
    {
        $this->escaper = $escaper;

        $this->sectionManager = new SectionManager();
        $this->sectionManager->addListener(SectionEvent::COMPILE, function (SectionEvent $e) {
            $this->dispatch($e);
        });

        $this->parser = new Parser($this, [
            'extend',
            'share',
            'open',
            'parent',
            'close',
            'section',
            'escape',
            'context',
            'helper',
            'filter',
            'validator',
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
     * @param mixed  $data
     * @param string $strategy
     *
     * @return mixed
     */
    public function escape($data, $strategy = 'html')
    {
        $escaper = $this->escaper ?: function ($value) {
            return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', true);
        };

        if (is_array($data) || is_object($data) || $data instanceof \Traversable) {
            foreach ($data as &$value) {
                $value = $this->escape($value, $strategy);
            }
        } else {
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

        try {
            $content = $this->parser->parse($template, $parameters + $this->shared);
        } catch (\Exception $e) {
            $this->sectionManager->reset();
            throw $e;
        }

        if (!empty($this->parent)) {
            $parameters[self::CONTENT_KEY] = $content;
            $content = $this->render($this->parent, $parameters);
            unset($parameters[self::CONTENT_KEY]);
        } else {
            $content = $this->sectionManager->parse($content);
        }

        return $content;
    }
}
