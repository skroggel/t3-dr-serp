<?php
declare(strict_types=1);
namespace Madj2k\DrSerp\PageTitle;

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

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\PageTitle\PageTitleProviderInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class WebsiteTitleProvider
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
*  @package Madj2k_DrSerp
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class WebsiteTitleProvider implements PageTitleProviderInterface
{

    /**
     * @var \TYPO3\CMS\Core\Site\SiteFinder
     */
    private ?SiteFinder $siteFinder = null;


	/**
	 * @param \TYPO3\CMS\Core\Site\SiteFinder $siteFinder
	 */
	public function __construct(SiteFinder $siteFinder)
	{
        $this->siteFinder = $siteFinder;
	}


    /**
     * @param array $configuration
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     */
	public function getTitle(array $configuration = []): string
	{
        $configReader = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $extensionConfig = $configReader->get('dr_serp');

        // get relevant fields
        $fields = GeneralUtility::trimExplode(',', ($extensionConfig['pageTitleFields'] ?? 'title'),true);
        $separator = $extensionConfig['pageTitleSeparator'] ?? '';
        $includePageName = (bool) $extensionConfig['pageTitleIncludePageName'] ?? false;
        $combineFields = (bool) $extensionConfig['pageTitleCombineFields'] ?? false;

        $title = [];
        $frontendController = $this->getTypoScriptFrontendController();
        foreach ($fields as $field) {

            $value = $frontendController->page[$field] ?? '';
            if (! empty($value)){
                $title[] = trim(str_replace('­', '', strip_tags($value)));
                if (!$separator || !$combineFields) {
                    break;
                }
            }
        }

        if ($separator && $includePageName) {

            /** @var \TYPO3\CMS\Core\Site\Entity\Site $config */
            $site = $this->siteFinder->getSiteByPageId((int)$this->getTypoScriptFrontendController()->page['uid']);
            $title[] = $site->getConfiguration()['websiteTitle'];
        }

        if ($title) {
            return implode(' ' . $separator . ' ', $title);
        }

        return '';
	}


	/**
	 * @return TypoScriptFrontendController
	 */
	private function getTypoScriptFrontendController(): TypoScriptFrontendController
	{
		return $GLOBALS['TSFE'];
	}
}
