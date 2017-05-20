<?php

namespace roydejong\dotnet\Generation;

class HomepageGenerator extends PageGenerator
{
    /**
     * @@inheritdoc
     */
    public function getTemplateName(): string
    {
        return "homepage.twig";
    }

    /**
     * @inheritdoc
     */
    public function needsGeneration(): bool
    {
        // The homepage is automatically regenerated periodically
        return false;
    }
}