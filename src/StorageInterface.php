<?php

namespace Elixir\View;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface StorageInterface 
{
    /**
     * @var string
     */
    const TYPE_FILE = 'type_file';
    
    /**
     * @var string
     */
    const TYPE_STRING = 'type_string';
    
    /**
     * @return string
     */
    public function getType();
    
    /**
     * @return string
     */
    public function getContent();
}
