<?php
declare(strict_types=1);
namespace Madj2k\DrSerp\Middleware;

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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class HumanizeAiContent
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_DrSerp
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class HumanizeAiContent implements MiddlewareInterface
{

    /**
     * @var array
     */
    protected array $settings = [];


    /**
     * Removes typical unicode signs used by AI
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if (!$response instanceof NullResponse) {

            // extract the content
            $body = $response->getBody();
            $body->rewind();
            $content = $response->getBody()->getContents();

            $this->settings = $this->loadSettings();

            // get minifier and process the response
            if ($this->humanize($content)) {

                // push new content back into the response
                $body = new \TYPO3\CMS\Core\Http\Stream('php://temp', 'rw');
                $body->write($content);
                return $response->withBody($body);
            }
        }
        return $response;
    }


    /**
     * Replaces signs in content
     *
     * @param string $content content to replace
     * @return bool
     */
    public function humanize(string &$content): bool
    {
        if (empty($this->settings['enable'])) {
            return false;
        }

        $signsToRemove = GeneralUtility::trimExplode(',', $this->settings['signsRemove']);
        $signsToSpace= GeneralUtility::trimExplode(',', $this->settings['signsSpace']);

        $contentBefore = $content;
        foreach ($signsToRemove as $sign) {
            $unicodeChar = json_decode('"\\u' . $sign . '"');
            $content = str_replace($unicodeChar, '', $content);
        }
        foreach ($signsToSpace as $sign) {
            $unicodeChar = json_decode('"\\u' . $sign . '"');
            $content = str_replace($unicodeChar, ' ', $content);
        }

        return ($content != $contentBefore);
    }


    /**
     * Loads settings
     *
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function loadSettings(): array
    {
        $settings = [
            'enable' => true,
            'signsRemove' => '200B, 2060',
            'signsSpace' => '202F',
        ];

        $configReader = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $extensionConfig = $configReader->get('dr_serp');

        foreach ($extensionConfig as $key => $value) {
            if (str_starts_with($key, 'humanizeAiContent')) {
                $cleanedKey = lcfirst(str_replace('humanizeAiContent', '', $key));
                $settings[$cleanedKey] = $value;
            }
        }

        return $this->settings = $settings;
    }
}

