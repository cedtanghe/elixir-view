<?php

namespace Elixir\View\Engine\Twig;

use Twig_Extension;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Extension extends Twig_Extension
{
    /**
     * @var Twig
     */
    protected $twig;

    /**
     * @param Twig $engine
     */
    public function __construct(Twig $engine)
    {
        $this->twig = $engine;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'elixir_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return [
            'context' => $this->twig->context(),
            'helper' => $this->twig->getHelperManager(),
            'filter' => $this->twig->getFilterManager(),
            'validator' => $this->twig->getValidatorManager(),
        ];
    }
}
