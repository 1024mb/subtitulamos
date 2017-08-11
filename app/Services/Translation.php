<?php

/**
 * This file is covered by the AGPLv3 license, which can be found at the LICENSE file in the root of this project.
 * @copyright 2017 subtitulamos.tv
 */

namespace App\Services;

use \ForceUTF8\Encoding;
use Doctrine\ORM\EntityManager;
use App\Entities\Subtitle;
use App\Entities\Sequence;
use App\Entities\User;
use App\Entities\OpenLock;
use App\Entities\SubtitleComment;

class Translation
{
    /**
     * Entity manager handle
     * @var EntityManager
     */
    private $em = null;

    /**
     * Connection to the redis server
     * @var \Redis
     */
    private $redis = null;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;

        $redis = new \Redis();
        $redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));
        $this->redis = $redis;
    }

    /**
     * Obtains the id of the base subtitle, that is, the canonical one
     * for the version of the given subtitle
     *
     * @param Subtitle $sub
     * @return int
     */
    public function getBaseSubId(Subtitle $sub)
    {
        return $this->em->createQuery("SELECT sb.id FROM App:Subtitle sb WHERE sb.version = :v AND sb.directUpload = 1")
            ->setParameter('v', $sub->getVersion())
            ->getSingleScalarResult();
    }

    /**
     * Finds and returns the latest revision of a given sequence number
     *
     * @param int $subId
     * @param int $seqNum
     * @return \App\Entities\Sequence
     */
    public function getLatestSequenceRev($subId, $seqNum)
    {
        return $this->em->createQuery("SELECT sq FROM App:Sequence sq WHERE sq.subtitle = :sub AND sq.number = :num ORDER BY sq.revision DESC")
            ->setParameter('sub', $subId)
            ->setParameter('num', $seqNum)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function getPubSubChanName(Subtitle $sub)
    {
        return sprintf("%s-translate-%d", ENVIRONMENT_NAME, $sub->getId());
    }

    /**
     * Broadcasts to pub/sub channel the opening of a sequence on a sub
     *
     * @param \App\Entities\Subtitle $sub
     * @param \App\Entities\User $byUser
     * @param int $seqNum
     * @param \App\Entities\OpenLock $lock
     * @return void
     */
    public function broadcastOpen(Subtitle $sub, User $byUser, int $seqNum, OpenLock $lock)
    {
        $this->redis->publish($this->getPubSubChanName($sub), \json_encode([
            "type" => "seq-open",
            "user" => $byUser->getId(),
            "num" => $seqNum,
            "openLockID" => $lock->getId()
        ]));
    }

    /**
     * Broadcasts to pub/sub channel the closing of a sequence on a sub
     *
     * @param \App\Entities\Subtitle $sub
     * @param int $seqNum
     * @return void
     */
    public function broadcastClose(Subtitle $sub, int $seqNum)
    {
        $this->redis->publish($this->getPubSubChanName($sub), \json_encode([
            "type" => "seq-close",
            "num" => $seqNum
        ]));
    }

    /**
     * Broadcasts to pub/sub channel the edition of a sequence on a sub
     *
     * @param \App\Entities\Sequence $seq
     * @return void
     */
    public function broadcastSeqChange(Sequence $seq)
    {
        $sub = $seq->getSubtitle();
        $this->redis->publish($this->getPubSubChanName($sub), \json_encode([
            "type" => "seq-change",
            "user" => $seq->getAuthor()->getId(),
            "num" => $seq->getNumber(),
            "nid" => $seq->getId(),
            "ntext" => $seq->getText()
        ]));
    }

    /**
     * Broadcasts to pub/sub channel the lock status of a sequence on a sub
     *
     * @param \App\Entities\Sequence $seq
     * @return void
     */
    public function broadcastLockChange(Sequence $seq)
    {
        $sub = $seq->getSubtitle();
        $this->redis->publish($this->getPubSubChanName($sub), \json_encode([
            "type" => "seq-lock",
            "id" => $seq->getId(),
            "status" => $seq->getLocked()
        ]));
    }

    /**
     * Broadcasts new comment being published
     *
     * @param \App\Entities\SubtitleComment $c
     * @return void
     */
    public function broadcastNewComment(SubtitleComment $c)
    {
        $sub = $c->getSubtitle();
        $this->redis->publish($this->getPubSubChanName($sub), \json_encode([
            "type" => "com-new",
            "id" => $c->getId(),
            "user" => $c->getUser()->getId(),
            "time" => $c->getPublishTime()->format(\DateTime::ATOM),
            "text" => $c->getText()
        ]));
    }

    /**
     * Broadcasts comment being deleted
     *
     * @param \App\Entities\SubtitleComment $c
     * @return void
     */
    public function broadcastDeleteComment(SubtitleComment $c)
    {
        $sub = $c->getSubtitle();
        $this->redis->publish($this->getPubSubChanName($sub), \json_encode([
            "type" => "com-del",
            "id" => $c->getId()
        ]));
    }

    /**
     * Calculate the translation progress for a given subtitle
     *
     * @param \App\Entities\Subtitle|int $baseSub
     * @param \App\Entities\Subtitle $sub
     * @param int $modifier
     * @return void
     */
    public function recalculateSubtitleProgress($baseSub, Subtitle $sub, int $modifier)
    {
        if (!$baseSub) {
            $baseSub = $this->getBaseSubId($sub);
        }

        $baseSubSeqCount = $this->em->createQuery("SELECT COUNT(DISTINCT sq.number) FROM App:Sequence sq WHERE sq.subtitle = :sub")
            ->setParameter('sub', $baseSub)
            ->getSingleScalarResult();

        $ourSubSeqCount = $this->em->createQuery("SELECT COUNT(DISTINCT sq.number) FROM App:Sequence sq WHERE sq.subtitle = :sub")
            ->setParameter('sub', $sub->getId())
            ->getSingleScalarResult();

        $sub->setProgress( ($ourSubSeqCount + $modifier) / $baseSubSeqCount * 100);
        if ($sub->getProgress() == 100 && !$sub->getPause()) {
            // We're done! Mark as such
            $sub->setCompleteTime(new \DateTime());
        }
        elseif ($sub->getCompleteTime()) {
            $sub->setCompleteTime(null);
        }
    }

    /**
     * Determine if a string contains a subtitle's credits (both ours and other people's)
     * @param string $text
     * @return bool
     */
    public static function containsCreditsText(string $text)
    {
        if (preg_match("/(?:(www\.)?addic7ed\.com)|(?:corrected by elderman)|(?:^[-*\[]?credito?s[-*\]]$)|(?:(www\.)?tusubtitulo\.com)/i", $text) == 1) {
            return true;
        }

        return self::containsOwnCreditsText($text);
    }

    /**
     * Determine if a string contains our own credits
     * @param string $text
     * @return bool
     */
    public static function containsOwnCreditsText($text)
    {
        return preg_match("/(www\.)?subtitulamos\.(?:com|tv)/i", $text) == 1;
    }

    /**
     * Determine if a sequence should be left blank based on its contents
     * @param string $text
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

    /**
     * Clear the text from artifacts that would render it
     * unplayable on older devices or less standard-compliant ones
     *
     * @param string $text
     * @param bool $allowSpecialTags
     * @return void
     */
    public static function cleanText(string $text, bool $allowSpecialTags)
    {
        // Remove multiple spaces concatenated
        $text = trim(preg_replace('/ +/', ' ', $text));

        if ($allowSpecialTags) {
            $text = strip_tags($text, "<font>");

            $dom = new \DOMDocument();
            $dom->loadHTML(mb_convert_encoding("<div>" . $text . "</div>", 'HTML-ENTITIES', 'UTF-8'), \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD);

            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query('//font');
            foreach ($nodes as $node) {
                $color = $node->hasAttribute('color') ? $node->getAttribute('color') : "";
                $attributes = $node->attributes;
                while ($attributes->length) {  // https://stackoverflow.com/a/10281657/2205532
                    $node->removeAttribute($attributes->item(0)->name);
                }

                if ($color) {
                    $node->setAttribute("color", $color);
                }
            }

            $text = trim(Encoding::toUTF8(\html_entity_decode(strip_tags($dom->saveHTML($dom->documentElement), "<font>"))));
        }
        else {
            $text = strip_tags($text);
        }

        $pregReplacements = [
            '…' => '...',
            "“" => '"',
            "”" => '"',
            '/[\x{200B}-\x{200D}]/u' => '', //(Remove all 0-width space: https://stackoverflow.com/a/11305926/2205532)
        ];

        foreach ($pregReplacements as $k => $v) {
            $text = str_replace($k, $v, $text);
        }

        $text = trim($text);
        if (empty($text)) {
            // At least one space
            $text = " ";
        }

        /* TODO: Better validate text (multiline etc) + multiline trim */
        return $text;
    }
}
