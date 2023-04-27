<?php

namespace acalvino4\embed;

use acalvino4\embed\tokenparsers\DefaultBlockTokenParser;
use acalvino4\embed\tokenparsers\EmbedTokenParser;
use Twig\Extension\AbstractExtension;

/**
 * Twig extension
 */
class Extension extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getTokenParsers(): array
    {
        return [
            new EmbedTokenParser(),
            new DefaultBlockTokenParser(),
        ];
    }
}
