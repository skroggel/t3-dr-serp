<?php
declare(strict_types=1);
namespace Madj2k\DrSerp\ViewHelpers;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */


use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ViewHelper to render meta tags
 *
 * # Example: Basic Example: News title as og:title meta tag
 * <code>
 * <n:metaTag property="og:title" content="{newsItem.title}" />
 * </code>
 * <output>
 * <meta property="og:title" content="TYPO3 is awesome" />
 * </output>
 *
 * # Example: Force the attribute "name"
 * <code>
 * <n:metaTag name="keywords" content="{newsItem.keywords}" />
 * </code>
 * <output>
 * <meta name="keywords" content="news 1, news 2" />
 * </output>
 */

/**
 * Class MetaTagViewHelper
 *
 * @author Georg Ringer <mail@ringer.it>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_DrSerp
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class MetaTagViewHelper extends AbstractViewHelper
{

    /**
     * Arguments initialization
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('property', 'string', 'Property of meta tag', false, '', false);
        $this->registerArgument('name', 'string', 'Content of meta tag using the name attribute', false, '', false);
        $this->registerArgument('content', 'string', 'Content of meta tag', true, '', false);
        $this->registerArgument('useCurrentDomain', 'boolean', 'Use current domain', false, false);
        $this->registerArgument('forceAbsoluteUrl', 'boolean', 'Force absolut domain', false, false);
        $this->registerArgument('replace', 'boolean', 'Replace potential existing tag', false, false);
    }


    /**
     * @return void
     */
    public function render(): void
    {
        /** @var bool $useCurrentDomain */
        $useCurrentDomain = $this->arguments['useCurrentDomain'];

        /** @var bool $forceAbsoluteUrl */
        $forceAbsoluteUrl = $this->arguments['forceAbsoluteUrl'];

        /** @var string $content */
        $content = (string)$this->arguments['content'];

        // set current domain
        if ($useCurrentDomain) {
            $content = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        }

        // prepend current domain
        if ($forceAbsoluteUrl) {
            $parsedPath = parse_url($content);
            if (is_array($parsedPath) && !isset($parsedPath['host'])) {
                $content =
                    rtrim(GeneralUtility::getIndpEnv('TYPO3_SITE_URL'), '/')
                    . '/'
                    . ltrim($content, '/');
            }
        }

        if ($content !== '') {
            $registry = GeneralUtility::makeInstance(MetaTagManagerRegistry::class);
            if ($this->arguments['property']) {
                $manager = $registry->getManagerForProperty($this->arguments['property']);
                $manager->addProperty($this->arguments['property'], $content, [], $this->arguments['replace'], 'property');
            } elseif ($this->arguments['name']) {
                $manager = $registry->getManagerForProperty($this->arguments['name']);
                $manager->addProperty($this->arguments['name'], $content, [], $this->arguments['replace'], 'name');
            }
        }

    }
}
