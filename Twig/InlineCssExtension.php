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

use RobertoTru\ToInlineStyleEmailBundle\Converter\ToInlineStyleEmailConverter;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class InlineCssExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var ToInlineStyleEmailConverter
     */
    private $inlineCss;
    /**
     * @var string
     */
    private $kernelRoot;
    /**
     * @var FileLocatorInterface
     */
    private $locator;
    /**
     * @var bool
     */
    private $debug;

    public function __construct(
        ToInlineStyleEmailConverter $inlineCss,
        FileLocatorInterface $locator,
                                    $kernelRoot,
                                    $debug = false
    ) {
        $this->inlineCss = $inlineCss;
        $this->locator = $locator;
        $this->kernelRoot = $kernelRoot;
        $this->debug = $debug;
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenParsers()
    {
        return array(new InlineCssParser($this->locator, $this->kernelRoot . '/../web', $this->debug));
    }

    /**
     * {@inheritDoc}
     */
    public function getGlobals()
    {
        return array(
            'inlinecss' => $this->inlineCss,
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'inlinecss';
    }
}
