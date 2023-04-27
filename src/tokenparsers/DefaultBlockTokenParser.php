<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace acalvino4\embed\tokenparsers;

use Twig\Node\BlockNode;
use Twig\Node\BlockReferenceNode;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Document what this does
 *
 * @internal
 */
final class DefaultBlockTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $name = 'default';
        $this->parser->pushLocalScope();
        $this->parser->pushBlockStack($name);

        $defaultBlock = new BlockNode($name, new Node([]), $lineno);
        $body = $this->parser->subparse([$this, 'decideBlockEnd']);
        $defaultBlock->setNode('body', $body);

        // only set the implicit default node if it's there; otherwise leave that block name free for explicit declaration
        if (\count($body) || trim($body->getAttribute('data'))) {
            $this->parser->setBlock($name, $defaultBlock);
        }

        $this->parser->popBlockStack();
        $this->parser->popLocalScope();

        // reinject the block-start token for the embed/block token we consumed; thanks to $dropNeedle = false in the subparse call above, we didn't consume the name token itself
        $stream->injectTokens([
            new Token(/* Token::BLOCK_START_TYPE */ 1, '', $token->getLine()),
        ]);

        return new BlockReferenceNode($name, $lineno, $this->getTag());
    }

    public function decideBlockEnd(Token $token): bool
    {
        return $token->test(['block', 'endembed']);
    }

    public function getTag(): string
    {
        return 'defaultblock';
    }
}
