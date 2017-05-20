<?php

namespace roydejong\dotnet\Site;

use Enlighten\Http\Request;
use Enlighten\Http\Response;
use Enlighten\Http\ResponseCode;
use roydejong\dotnet\Generation\PageGenerator;

/**
 * Site engine static class that pumps out pages, generating them as necessary.
 */
class SiteEngine
{
    /**
     * Fires the engine!
     *
     * Checks whether the page needs to be (re)generated, based on the page generator and the incoming request.
     * Will proceed to either serve a static / pre rendered response, or generate a dynamic response.
     *
     * @param PageGenerator $page
     * @param Request $request
     * @return Response
     */
    public static function fire(PageGenerator $page, Request $request): Response
    {
        $response = new Response();
        $response->setResponseCode(ResponseCode::HTTP_OK);

        $page->setRequest($request);

        $renderOutput = null;

        $forcingGeneration = (DEBUG_ENABLED && $request->getQueryParam('regen') == 'force');

        if (!$page->isDynamic() && !$page->needsGeneration() && !$forcingGeneration) {
            // Page is static and does not need to be (re) generated, so attempt to read from cache
            if ($page->getRenderedFileExists()) {
                $renderOutput = $page->renderFromCache();
            }
        }

        if (!$renderOutput) {
            // For whatever reason, we didn't get any output. So let's (re)gen now.
            $renderOutput = $page->generate();
            $response->setHeader('X-Site-Cache', 'MISS');
        } else {
            $response->setHeader('X-Site-Cache', 'HIT');
        }

        $response->setBody($renderOutput);
        return $response;
    }
}