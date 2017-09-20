<?php

/**
 * This file is covered by the AGPLv3 license, which can be found at the LICENSE file in the root of this project.
 * @copyright 2017 subtitulamos.tv
 */

namespace App\Controllers;

use Doctrine\ORM\EntityManager;

class RSSController
{
    public const BASIC_RSS_FORMAT = '<?xml version="1.0" ?>
    <rss version="2.0">
        %s
    </rss>';

    public const RSS_CHAN_FORMAT = '<channel>
        <title>%s</title>
        <link>https://subtitulamos.tv</link>
        <description>%s</description>
        %s
    </channel>';

    public const RSS_ITEM_FORMAT = '<item>
        <title>%s</title>
        <link>%s</link>
        <description>%s</description>
    </item>';

    public function viewFeed($response, \Slim\Router $router, EntityManager $em)
    {
        $subs = $em->createQuery('SELECT s, v, e FROM App:Subtitle s JOIN s.version v JOIN v.episode e WHERE s.directUpload = 0 AND s.progress = 100 AND s.pause IS NULL AND s.completeTime IS NOT NULL ORDER BY s.completeTime DESC')
            ->setMaxResults(10)
            ->setFirstResult(0)
            ->getResult();

        $items = '';
        foreach ($subs as $sub) {
            $items .= sprintf(
                self::RSS_ITEM_FORMAT,
                $sub->getVersion()->getEpisode()->getFullName(),
                $router->pathFor('episode', ['id' => $sub->getVersion()->getEpisode()->getId()]),
                $sub->getVersion()->getName()
            );
        }

        $feed = self::BASIC_RSS_FORMAT;

        $response->getBody()->write(sprintf(
            self::BASIC_RSS_FORMAT,
            sprintf(
                self::RSS_CHAN_FORMAT,
                'Últimas traducciones completadas',
                'Listado de las últimas traducciones completadas/liberadas en subtitulamos.tv',
                $items
            )
        ));

        return $response;
    }
}