<?php

/**
 * This file is covered by the AGPLv3 license, which can be found at the LICENSE file in the root of this project.
 * @copyright 2017 subtitulamos.tv
 */

namespace App\Services;

class Translation
{
    /**
     * Determine if a string contains a subtitle's credits (both ours and other people's)
     * @param $text string
     * @return bool
     */
    public static function containsCreditsText(string $text)
    {
        if (preg_match("/(?:(www\.)?addic7ed\.com)|(?:corrected by elderman)|(?:^credito?s$)|(?:(www\.)?tusubtitulo\.com)/i", $text) == 1) {
            return true;
        }
        
        return self::containsOwnCreditsText($text);
    }

    /**
     * Determine if a string contains our own credits
     * @param $text string
     * @return bool
     */
    public static function containsOwnCreditsText($text)
    {
        return preg_match("/(www\.)?subtitlamos\.(?:com|tv)/i", $text) == 1;
    }

    /**
     * Determine if a sequence should be left blank based on its contents
     * @param $text
     * @return int  Degree of confidence that this should be a blank sequence
     */
    public static function getBlankSequenceConfidence($sequence)
    {
        // Hmm*, Eh, Uh, Oh, Uhm, Mm, Ah, Gah, Mnh, Argh, Ow, Ouch, Ugh... & combinations of those
        if (preg_match("/^(?:(?:[uhm]h*m+|[euoa]+h|ga+h+|mnh+|s+h+|h+[eua]+h+|u+g+h+|a+rgh+|ow+|ouch)[.!?\s-]*)*$/i", $sequence->getText()) == 1) {
            return 100;
        }
        
        // Annotations for the hearing impaired come between [] o ()
        if (preg_match("/^\[[^]]*\]$|^\([^)]*\)$/", $sequence->getText()) == 1) {
            return 100;
        }
        
        return 0;
    }
}