<?php

namespace roydejong\dotnet\Generation;

use Enlighten\Http\Request;

/**
 * Base class for all static page generators.
 */
abstract class PageGenerator extends PageContext
{
    /**
     * @var Request|null
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * Gets the name of the Twig template that should be used to generate this page.
     *
     * @return string
     */
    public abstract function getTemplateName(): string;

    /**
     * Gets whether this page needs to be (re)generated at this time.
     * This function evaluates the current request and/or generation status.
     *
     * @return bool Returns TRUE if page needs to be (re)generated or
     */
    public abstract function needsGeneration(): bool;

    /**
     * Gets whether this is a dynamic request / response.
     * If this function returns true, generation is always required and output won't be stored as a prerendered page.
     *
     * @return bool
     */
    public function isDynamic(): bool
    {
        return false;
    }

    /**
     * Gets a unique identifier for this page generation instance.
     * This is used to differentiate between dynamic pages.
     *
     * @return string
     */
    public function getInstanceId(): string
    {
        return $this->getTemplateName() . "_default";
    }

    /**
     * Gets the path to the pre-rendered file for this page instance.
     *
     * @return string
     */
    protected function getRenderedFilePath(): string
    {
        return PATH_COMPILATION . "/pages/{$this->getInstanceId()}.html";
    }

    /**
     * Gets whether a pre-rendered file exists for this page instance.
     *
     * @return bool
     */
    public function getRenderedFileExists(): bool
    {
        $path = $this->getRenderedFilePath();
        return file_exists($path) && is_readable($path);
    }

    /**
     * Writes (create or update) the pre-rendered file for this page instance.
     *
     * @param string $renderOutput
     */
    protected function writeRenderedFile(string $renderOutput): void
    {
        file_put_contents($this->getRenderedFilePath(), $renderOutput, LOCK_EX);
    }

    /**
     * Reads the cache for this page instance and returns it as a string.
     *
     * @return string
     */
    public function renderFromCache(): string
    {
        return file_get_contents($this->getRenderedFilePath());
    }

    /**
     * Performs page rendering, and returns the output as a string.
     * This will cause the template engine to be initialized and rendering to occur anew. Use sparingly.
     *
     * @return string
     */
    public function render(): string
    {
        $twigLoader = new \Twig_Loader_Filesystem([PATH_TEMPLATES]);

        $twigOptions = [
            'cache' => PATH_COMPILATION . "/templates"
        ];

        $twig = new \Twig_Environment($twigLoader, $twigOptions);
        return $twig->render($this->getTemplateName(), $this->getContextData());
    }

    /**
     * (Re)generates the page. Will cause the page to render, and output to be stored as a compiled file.
     * Returns the rendered HTML.
     * Causes rendering and disk writes, use very sparingly.
     *
     * @return string
     */
    public function generate(): string
    {
        $render = $this->render();

        if (!$this->isDynamic()) {
            $this->writeRenderedFile($render);
        }

        return $render;
    }
}