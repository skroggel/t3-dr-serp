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

use GeorgRinger\News\Domain\Model\News;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class NewsTitleProvider
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k_DrSerp
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @see \GeorgRinger\News\Seo\NewsTitleProvider
 */
class NewsTitleProvider extends AbstractPageTitleProvider
{

    protected const DEFAULT_PROPERTIES = 'title';
    protected const DEFAULT_GLUE = ' – ';

    /**
     * @var \TYPO3\CMS\Core\Site\SiteFinder
     */
    private ?SiteFinder $siteFinder = null;


    /**
	 * @param SiteFinder $siteFinder
	 */
	public function __construct(SiteFinder $siteFinder)
	{
        $this->siteFinder = $siteFinder;
	}


    /**
     * @param \GeorgRinger\News\Domain\Model\News $news
     * @param array $configuration
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     */
	public function setTitleByNews(News $news, array $configuration = []): void
	{
        $configReader = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $extensionConfig = $configReader->get('dr_serp');

        // get relevant fields
        $fields = GeneralUtility::trimExplode(',', ($extensionConfig['pageTitleFieldsNews'] ?? 'title'),true);
        $separator = $extensionConfig['pageTitleSeparatorNews'] ?? '';
        $includePageName = (bool) $extensionConfig['pageTitleIncludePageNameNews'] ?? false;
        $combineFields = (bool) $extensionConfig['pageTitleCombineFieldsNews'] ?? false;

        $title = [];
        foreach ($fields as $field) {

            $getter = 'get' . GeneralUtility::underscoredToUpperCamelCase($field);
            if (
                (method_exists($news, $getter))
                && ($value = $news->$getter())
                && (is_string($value))
            ){
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
            $this->title = implode(' ' . $separator . ' ', $title);
        }
	}


	/**
	 * @param string $title
	 * @return void
	 */
	public function setTitle(string $title): void
	{
		$this->title = $title;
	}


	/**
	 * @return TypoScriptFrontendController
	 */
	private function getTypoScriptFrontendController(): TypoScriptFrontendController
	{
		return $GLOBALS['TSFE'];
	}
}
