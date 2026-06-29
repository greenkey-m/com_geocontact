<?php
/**
 * @package     com_geocontact
 * @version     5.0.0
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Matvey <info@greenkey.ru> - http://geocontact.greenkey.ru
 */

namespace Greenkey\Component\Geocontact\Site\Helper;

defined('_JEXEC') or die;

use morphos\Russian\Cases;
use morphos\Russian\GeographicalNamesInflection;

/**
 * Prepares geocontact description placeholders.
 */
class DescriptionHelper
{
    /**
     * Region titles used in templates instead of automatic inflection.
     */
    private const REGION_TITLES = [
        'Калужская область'  => 'Калужской области',
        'Московская область' => 'Московской области',
        'Прочие регионы'     => 'Калужской, Московской, Тульской области',
    ];

    /**
     * Category alias prefixes that select the first alternative in {a|b} blocks.
     */
    private const REGION_ALIAS_PREFIXES = ['a', 'b', 'k', 'l', 'e', 'x', 'y', 'w', 'z', 'i', 'p', 'm'];

    public static function prepare(object $item): object
    {
        ComposerAutoloadHelper::register();

        $caption = (string) ($item->caption ?? '');

        $cases = self::inflectCaption($caption);

        $categoryTitle = (string) ($item->category_title ?? '');
        $item->category_title = self::REGION_TITLES[$categoryTitle] ?? self::inflectRegionTitle($categoryTitle);

        $replacements = [
            '{caption}'  => $caption,
            '{caption1}' => $cases['caption1'],
            '{caption2}' => $cases['caption2'],
            '{caption5}' => $cases['caption5'],
            '{phones}'   => (string) ($item->phones ?? ''),
            '{stand}'    => (string) ($item->stand ?? ''),
            '{name}'     => (string) ($item->name ?? ''),
            '{address}'  => (string) ($item->address ?? ''),
            '{category}' => $item->category_title,
        ];

        $description = (string) ($item->description ?? '');

        foreach ($replacements as $placeholder => $value) {
            $description = str_replace($placeholder, $value, $description);
        }

        $item->description = self::resolveAlternatives($description, (string) ($item->category_alias ?? ''));

        return $item;
    }

    /**
     * Legacy placeholder numbering from morphos.tech:
     * caption1 = genitive, caption2 = dative, caption5 = prepositional.
     *
     * @return array{caption1: string, caption2: string, caption5: string}
     */
    private static function inflectCaption(string $caption): array
    {
        if ($caption === '' || !class_exists(GeographicalNamesInflection::class)) {
            return [
                'caption1' => $caption,
                'caption2' => $caption,
                'caption5' => $caption,
            ];
        }

        return [
            'caption1' => GeographicalNamesInflection::getCase($caption, Cases::RODIT),
            'caption2' => GeographicalNamesInflection::getCase($caption, Cases::DAT),
            'caption5' => GeographicalNamesInflection::getCase($caption, Cases::PREDLOJ),
        ];
    }

    private static function inflectRegionTitle(string $title): string
    {
        if ($title === '' || !class_exists(GeographicalNamesInflection::class)) {
            return $title;
        }

        return GeographicalNamesInflection::getCase($title, Cases::RODIT);
    }

    private static function resolveAlternatives(string $description, string $categoryAlias): string
    {
        if (!preg_match_all('/\{([^{}|]*)\|([^{}|]*)\}/', $description, $matches)) {
            return $description;
        }

        $aliasPrefix = strtolower(substr($categoryAlias, 0, 1));
        $useFirst    = in_array($aliasPrefix, self::REGION_ALIAS_PREFIXES, true);

        foreach ($matches[0] as $index => $placeholder) {
            $replacement = $useFirst ? $matches[1][$index] : $matches[2][$index];
            $description = str_replace($placeholder, $replacement, $description);
        }

        return $description;
    }
}
