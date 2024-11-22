<?php
declare(strict_types=1);
namespace Madj2k\DrSerp\Utility;

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

use TYPO3\CMS\Core\Charset\CharsetConverter;

/**
 * Class SlugUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_DrSerp
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SlugUtility
{

    /**
     * Sanitizes slugs and removes slashes, too
     *
     * @author Christian Dilger <c.dilger@addorange.de>
     * @author Steffen Kroggel <developer@steffenkroggel.de>
     * @param string $slug
     * @param string $separator
     * @return string
     * @see \TYPO3\CMS\Core\DataHandling\SlugHelper
     */
    static public function slugify(string $slug, string $separator = '-'): string
    {
        // use "mb_strtolower" instead of "strtolower" for ÄÜÖ
        $slug = mb_strtolower($slug, 'utf-8');
        $slug = strip_tags($slug);

        // Convert some special tokens (space, "_" and "-") to the separator character
        $slug = preg_replace('/[ \t\x{00A0}\-+_]+/u', $separator, $slug);

        // handle german umlauts separately
        $slug = str_replace(['ä', 'ä', 'ö', 'ü', 'ß', '/'], ['ae', 'ae', 'oe', 'ue', 'ss', $separator], $slug);

        // Replace @ with the word '-at-'
        $slug = str_replace('@', $separator . 'at' . $separator, $slug);

        // Convert extended letters to ascii equivalents
        // The specCharsToASCII() converts "€" to "EUR"
        $slug = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(CharsetConverter::class)->specCharsToASCII('utf-8', $slug);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $slug = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', $slug);

        // Convert multiple fallback characters to a single one
        $slug = preg_replace('/' . preg_quote($separator) . '{2,}/', $separator, $slug);

        // Ensure slug is lowercase after all replacement was done
        $slug = mb_strtolower($slug, 'utf-8');

        return trim($slug, $separator);
    }
}
