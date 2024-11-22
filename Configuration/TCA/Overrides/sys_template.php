<?php
call_user_func(
    function($extKey)
    {

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            $extKey,
            'Configuration/TypoScript',
            'Dr.Seo'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            $extKey,
            'Configuration/TypoScript/MetaTags',
            'Dr.Seo - MetaTags and OpenGraph (optional)'
        );

    },
    'dr_serp'
);
