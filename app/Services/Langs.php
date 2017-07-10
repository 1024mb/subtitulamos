<?php

/**
 * This file is covered by the AGPLv3 license, which can be found at the LICENSE file in the root of this project.
 * @copyright 2017 subtitulamos.tv
 */

namespace App\Services;

class Langs
{
    const LANG_LIST = [
        1 => "en-en",
        5 => "es-es",
        6 => "es-lat"
    ];

    const LANG_NAMES = [
        "en-en" => "English",
        "es-es" => "Español (España)",
        "es-lat" => "Español (Latinoamérica)"
    ];

    public static function existsId(int $langId)
    {
        return isset(self::LANG_LIST[$langId]);
    }

    public static function existsCode(string $langCode)
    {
        return self::getLangId($langCode) !== -1;
    }
    
    public static function getLangId(string $langCode)
    {
        foreach (self::LANG_LIST as $id => $code) {
            if ($code == $langCode) {
                return $id;
            }
        }

        return -1;
    }

    public static function getLangCode(int $langId)
    {
        return isset(self::LANG_LIST[$langId]) ? self::LANG_LIST[$langId] : "err-err";
    }

    public static function getLocalizedName($code)
    {
        return self::LANG_NAMES[$code];
    }
}