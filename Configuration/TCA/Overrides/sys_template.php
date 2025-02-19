<?php
call_user_func(
    function($extKey)
    {

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            $extKey,
            'Configuration/TypoScript',
            'Dr. Serp'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            $extKey,
            'Configuration/TypoScript/MetaTags',
            'Dr. Serp - MetaTags and OpenGraph (optional)'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            $extKey,
            'Configuration/TypoScript/MetaTagsNoInherit',
            'Dr. Serp - MetaTags and OpenGraph without inheritance (optional)'
        );        

    },
    'dr_serp'
);
