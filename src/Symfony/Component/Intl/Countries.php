<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Intl;

use Symfony\Component\Intl\Exception\MissingResourceException;

/**
 * Gives access to region-related ICU data.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class Countries extends ResourceBundle
{
    /**
     * Returns all available countries.
     *
     * Countries are returned as uppercase ISO 3166 two-letter country codes.
     *
     * A full table of ISO 3166 country codes can be found here:
     * https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes
     *
     * This list only contains "officially assigned ISO 3166-1 alpha-2" country codes.
     *
     * @return string[] an array of canonical ISO 3166 alpha-2 country codes
     */
    public static function getCountryCodes(): array
    {
        return self::readEntry(['Regions'], 'meta');
    }

    /**
     * Returns all available countries (3 letters).
     *
     * Countries are returned as uppercase ISO 3166 three-letter country codes.
     *
     * This list only contains "officially assigned ISO 3166-1 alpha-3" country codes.
     *
     * @return string[] an array of canonical ISO 3166 alpha-3 country codes
     */
    public static function getAlpha3Codes(): array
    {
        return self::readEntry(['Alpha2ToAlpha3'], 'meta');
    }

    public static function getAlpha3Code(string $alpha2Code): string
    {
        return self::readEntry(['Alpha2ToAlpha3', $alpha2Code], 'meta');
    }

    public static function getAlpha2Code(string $alpha3Code)
    {
        return self::readEntry(['Alpha3ToAlpha2', $alpha3Code], 'meta');
    }

    /**
     * @param string $country Alpha2 country code
     */
    public static function exists(string $country): bool
    {
        try {
            self::readEntry(['Names', $country]);

            return true;
        } catch (MissingResourceException $e) {
            return false;
        }
    }

    /**
     * @param string $alpha3Code Alpha3 country code
     */
    public static function alpha3CodeExists(string $alpha3Code): bool
    {
        try {
            self::getAlpha2Code($alpha3Code);

            return true;
        } catch (MissingResourceException $e) {
            return false;
        }
    }

    /**
     * Gets the country name from alpha2 code.
     *
     * @throws MissingResourceException if the country code does not exists
     */
    public static function getName(string $country, string $displayLocale = null): string
    {
        return self::readEntry(['Names', $country], $displayLocale);
    }

    /**
     * Get country name from alpha3 code.
     *
     * @throws MissingResourceException if the country code does not exists
     */
    public static function getAlpha3Name(string $alpha3Code, string $displayLocale = null): string
    {
        $alpha2Code = self::getAlpha2Code($alpha3Code);

        return self::readEntry(['Names', $alpha2Code], $displayLocale);
    }

    /**
     * Gets the list of country names indexed with alpha2 codes as keys.
     *
     * @return string[]
     */
    public static function getNames($displayLocale = null): array
    {
        return self::asort(self::readEntry(['Names'], $displayLocale), $displayLocale);
    }

    /**
     * Gets the list of country names indexed with alpha3 codes as keys.
     *
     * Same as method getNames, but with alpha3 codes instead of alpha2 codes as keys.
     *
     * @return string[]
     */
    public static function getAlpha3Names($displayLocale = null): array
    {
        $alpha2Names = self::getNames($displayLocale);
        $alpha3Names = [];
        foreach ($alpha2Names as $alpha2Code => $name) {
            $alpha3Code = self::readEntry(['Alpha2ToAlpha3', $alpha2Code], 'meta');
            $alpha3Names[$alpha3Code] = $name;
        }

        return $alpha3Names;
    }

    protected static function getPath(): string
    {
        return Intl::getDataDirectory().'/'.Intl::REGION_DIR;
    }
}
