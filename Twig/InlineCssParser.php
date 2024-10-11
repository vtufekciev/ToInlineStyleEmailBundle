<?php

/*
 * This file is part of ToInlineStyleEmailBundle.
 *
 * (c) Roberto Trunfio <roberto@trunfio.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RobertoTru\ToInlineStyleEmailBundle\Twig;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Twig\Token;
use Twig\Node\Node;
use Twig\TokenParser\AbstractTokenParser;


class InlineCssParser extends AbstractTokenParser
{
    /**
     * @var FileLocatorInterface
     */
    private $locator;

    /**
     * @var string
     */
    protected $webRoot;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param FileLocatorInterface $locator used to get css asset real path
     * @param string $webRoot web root of the project
     * @param bool $debug in debug mode css is not inlined but read on each render
     */
    public function __construct(FileLocatorInterface $locator, $webRoot, $debug = false)
    {
        $this->locator = $locator;
        $this->webRoot = $webRoot;
        $this->debug = $debug;
    }

    /**
     * Parses a token and returns a node.
     *
     * @param Token $token A Token instance
     *
     * @return Token A Token instance
     */
    public function parse(Token $token)
    {
        $lineNo = $token->getLine();
        $stream = $this->parser->getStream();
        if ($stream->test(Token::STRING_TYPE)) {
            $css = $this->resolvePath($stream->expect(Token::STRING_TYPE)->getValue());
        } else {
            $css = $this->parser->getExpressionParser()->parseExpression();
        }
        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideEnd'), true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new InlineCssNode($body, $css, $lineNo, $this->debug);
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag(): string
    {
        return 'inlinecss';
    }

    public function decideEnd(Token $token)
    {
        return $token->test('endinlinecss');
    }

    /**
     * Resolve path to absolute if any bundle is mentioned
     * @param string $path
     * @return string
     */
    private function resolvePath($path): string
    {
        try {
            return $this->locator->locate($path, $this->webRoot);
        } catch (\InvalidArgumentException $e) {
            // happens when path is not bundle relative
            return $this->webRoot . '/' . $path;
        }
    }
}
