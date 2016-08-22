<?php

namespace Elixir\View\Engine\Twig;

use Elixir\View\Engine\ContextTrait;
use Elixir\View\Engine\ServiceManagerTrait;
use Elixir\View\SharedTrait;
use Elixir\View\StorageInterface;
use Elixir\View\ViewContextInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_Loader_String;
use Twig_LoaderInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Twig implements ViewContextInterface
{
    use ServiceManagerTrait;
    use SharedTrait;
    use ContextTrait;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var Twig_LoaderInterface
     */
    protected $filesystemLoader;

    /**
     * @var Twig_LoaderInterface
     */
    protected $stringLoader;

    /**
     * @var array
     */
    protected $loadersOptions = ['filesystem' => null, 'string' => null];

    /**
     * @var bool
     */
    protected $hasGlobal = false;

    /**
     * @param Twig_Environment|array            $environment
     * @param Twig_LoaderInterface|string|array $filesystemLoader
     * @param Twig_LoaderInterface              $stringLoader
     */
    public function __construct($environment = null, $filesystemLoader = null, $stringLoader = null)
    {
        $this->twig = $environment instanceof Twig_Environment ? $environment : new Twig_Environment($environment);

        if ($filesystemLoader instanceof Twig_LoaderInterface) {
            $this->filesystemLoader = $filesystemLoader;
        } else {
            $this->loadersOptions['filesystem'] = $filesystemLoader;
        }

        if ($stringLoader instanceof Twig_LoaderInterface) {
            $this->stringLoader = $stringLoader;
        } else {
            $this->loadersOptions['string'] = $stringLoader;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultTemplateExtension()
    {
        return '(twig|html)';
    }

    /**
     * @return Twig_Environment
     */
    public function getEnvironment()
    {
        return $this->twig;
    }

    /**
     * @return Twig_LoaderInterface
     */
    public function getFilesystemLoader()
    {
        if (!$this->filesystemLoader) {
            $this->filesystemLoader = new Twig_Loader_Filesystem($this->loadersOptions['filesystem'] ?: []);
        }

        return $this->filesystemLoader;
    }

    /**
     * @return Twig_LoaderInterface
     */
    public function getStringLoader()
    {
        if (!$this->stringLoader) {
            $this->stringLoader = new Twig_Loader_String($this->loadersOptions['string'] ?: null);
        }

        return $this->stringLoader;
    }

    public function registerDefaultExtension()
    {
        $this->twig->addExtension(new Extension($this));
    }

    /**
     * {@inheritdoc}
     */
    public function share($key, $value)
    {
        $this->hasGlobal = true;
        $this->shared[$key] = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function unshare($key)
    {
        throw new \RuntimeException('You can only update existing globals');
    }

    /**
     * {@inheritdoc}
     */
    public function render($template, array $parameters = [])
    {
        if ($this->hasGlobal) {
            $this->hasGlobal = false;

            foreach ($this->shared as $key => $value) {
                $this->twig->addGlobal($key, $value);
            }
        }

        if ($template->getType() === StorageInterface::TYPE_STRING) {
            $this->twig->setLoader($this->getStringLoader());
        } else {
            $this->twig->setLoader($this->getFilesystemLoader());
        }

        return $this->twig->render($template, $parameters);
    }

    /**
     * @ignore
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->twig, $method], $arguments);
    }
}
