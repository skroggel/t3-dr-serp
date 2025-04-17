<?php
defined('TYPO3_MODE') || defined('TYPO3') ||die('Access denied.');

call_user_func(
    function($extKey)
    {

        //=================================================================
        // Add Rootline Fields
        //=================================================================
        /** @todo remove if support for v12 and below is dropped */
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


    },
    'dr_serp'
);
