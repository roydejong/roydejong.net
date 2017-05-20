<?php

namespace roydejong\dotnet\Generation;

/**
 * An instance that can contain context data that is used for page rendering.
 */
class PageContext
{
    /**
     * Inner page context, accessible via set and get methods.
     *
     * @var array
     */
    protected $_pageContext = [];

    /**
     * Sets a page context value.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setValue(string $key, $value): void
    {
        $this->_pageContext[$key] = $value;
    }

    /**
     * @return array
     */
    public function getContextData(): array
    {
        return $this->_pageContext;
    }
}