<?php
defined('TYPO3') or die('Access denied.');
call_user_func(
    function($extensionKey)
    {

        //===========================================================================
        // Add SEO fields to shortcut so that we can edit the default values in the rootpage
        //===========================================================================
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            'pages',
            '
			--div--;LLL:EXT:seo/Resources/Private/Language/locallang_tca.xlf:pages.tabs.seo,
				--palette--;;seo,
				--palette--;;robots,
				--palette--;;canonical,
				--palette--;;sitemap,
			--div--;LLL:EXT:seo/Resources/Private/Language/locallang_tca.xlf:pages.tabs.socialmedia,
				--palette--;;opengraph,
				--palette--;;twittercards',
            (string)\TYPO3\CMS\Core\Domain\Repository\PageRepository::DOKTYPE_SHORTCUT,
            'after:shortcut'
        );

        //===========================================================================
        // Change label for OpenGraph
        //===========================================================================
        $GLOBALS['TCA']['pages']['palettes']['opengraph']['label'] = '';


        //===========================================================================
        // Remove social media-fields we don't need
        //===========================================================================
        $searchStrings = [
            // '--div--;LLL:EXT:seo/Resources/Private/Language/locallang_tca.xlf:pages.tabs.socialmedia,',
            // '--palette--;;opengraph,',
            '--palette--;;twittercards'
        ];
        foreach ($searchStrings as $searchString) {
            foreach ($GLOBALS['TCA']['pages']['types'] as $type => $array) {
                $GLOBALS['TCA']['pages']['types'][$type]['showitem'] = str_replace($searchString, '', ($GLOBALS['TCA']['pages']['types'][$type]['showitem']));
            }
        }
    },
    'dr_serp'
);
