<?php
declare(strict_types=1);
namespace Madj2k\DrSerp\Routing\Aspect;

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

use Madj2k\DrSerp\DataHandling\SlugHelper;
use Madj2k\DrSerp\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class PersistedSlugifiedPatternMapper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_DrSerp
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
if (class_exists(\Calien\PersistedSanitizedRouting\Routing\Aspect\PersistedSanitizedPatternMapper::class)) {

    class PersistedSlugifiedPatternMapper extends \Calien\PersistedSanitizedRouting\Routing\Aspect\PersistedSanitizedPatternMapper
    {

        /**
         * @return SlugHelper
         */
        protected function getSlugHelper(): SlugHelper
        {
            trigger_error(__CLASS__ . '::' . __METHOD__ . '(): Please do not use this method any more.', E_USER_DEPRECATED);
            if ($this->slugHelper === null) {
                $this->slugHelper = GeneralUtility::makeInstance(
                    SlugHelper::class,
                    $this->tableName,
                    '',
                    []
                );
            }

            return $this->slugHelper;
        }

    }

} else {

    class PersistedSlugifiedPatternMapper
    {
        /**
         * @throws \Madj2k\DrSerp\Exception
         */
        public function __construct(array $settings)
        {
            throw new Exception('Extension persisted_sanitized_routing has to be installed');
        }

    }
}
