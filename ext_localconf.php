<?php
defined('TYPO3_MODE') || defined('TYPO3') ||die('Access denied.');

call_user_func(
    function($extKey)
    {

        //=================================================================
        // Add Rootline Fields
        //=================================================================
        $rootlineFields = &$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'];
        $newRootlineFields = 'keywords, abstract, author, seo_title, description, og_image, no_index, no_follow';
        $rootlineFields .= (empty($rootlineFields))? $newRootlineFields : ',' . $newRootlineFields;

        //=================================================================
        // Aspect for routing
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['PersistedSlugifiedPatternMapper']
            = \Madj2k\DrSerp\Routing\Aspect\PersistedSlugifiedPatternMapper::class;

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['CHashRemovalMapper'] =
            \Madj2k\DrSerp\Routing\Aspect\CHashRemovalMapper::class;

        //=================================================================
        // Remove some functions from ext:seo we handle ourselves
        //=================================================================
        $version = 0;
        $legacy = false;
        if (defined('TYPO3_version')) {
            $version = TYPO3_version;
            $legacy = true;
        } else {
            $typo3Version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);
            $version = $typo3Version->getMajorVersion();
        }

        if (($legacy && $version < 1000000) || (!$legacy && $version < 10)){
            unset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['TYPO3\CMS\Frontend\Page\PageGenerator']['generateMetaTags']['metatag']);
            unset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['TYPO3\CMS\Frontend\Page\PageGenerator']['generateMetaTags']['canonical']);

            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['TYPO3\CMS\Frontend\Page\PageGenerator']['generateMetaTags']['robots'] =
                \Madj2k\DrSerp\MetaTag\RobotsTagGenerator::class . '->generate';
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['TYPO3\CMS\Frontend\Page\PageGenerator']['generateMetaTags']['metatag'] =
                \Madj2k\DrSerp\MetaTag\MetaTagGenerator::class . '->generate';
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['TYPO3\CMS\Frontend\Page\PageGenerator']['generateMetaTags']['canonical'] =
                \Madj2k\DrSerp\MetaTag\CanonicalGenerator::class . '->generate';
        }

    },
    'dr_serp'
);
