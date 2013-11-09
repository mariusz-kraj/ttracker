<?php

namespace Tracker\Bundle\BackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tracker\Bundle\BackBundle\Entity\Episode;
use Tracker\Bundle\BackBundle\Entity\Torrent;

class TrackerController extends Controller
{
    public function trackAction()
    {
        $logger = $this->get('logger');
        $em = $this->getDoctrine()->getManager();
        $seriesRepo = $em->getRepository('TrackerBackBundle:Series');
        $episodeRepo = $em->getRepository('TrackerBackBundle:Episode');
        $torrentRepo = $em->getRepository('TrackerBackBundle:Torrent');

        $logger->info('Pobieramy rss`a z najnowszysmi torrentami z kategorii TV');
        $feed = simplexml_load_file('http://rss.thepiratebay.sx/208', 'SimpleXMLElement', LIBXML_NOCDATA);

        $namespaces = $feed->getNamespaces(true);
        echo '<pre>';
        foreach ($feed->xpath('//channel/item') as $item) {
            $title = (string)$item->title;
            $link = (string)$item->guid;
            $pubDate = (string)$item->pubDate;

            $torrent = $item->torrent;
            $hash = (string)$torrent->infoHash;
            $size = (int)(string)$torrent->contentLength;
            $size /= 1000000;
            $size = round($size);
            $magnet = (string)$torrent->magnetURI;
            $seeds = 0;
            $peers = 0;

            $verified = (bool)$torrent->verivied;

            $logger->info('Analizujemy torrent o nazwie: ' . $title);
            try {
                $logger->info('Sprawdzamy czy taki torrent jest już w bazie');
                $torrent = $torrentRepo->findBy(array('hash'=>$hash));
                if (!empty($torrent) ) {
                    //Aktualizacja istniejacego
                    $torrent = $torrent[0];

                    $torrent->setPeers($peers);
                    $torrent->setSeeds($seeds);
                    $torrent->setVerified($verified);

                    $logger->info('Taki torrent jest już dodany do bazy. Aktualizujemy wartości peers: ' . $peers .
                    ' seeds: ' . $seeds);

                    $em->persist($torrent);
                    $em->flush();

                    echo 'Zaktualizowano ' . $title . '<br/>';
                    continue;
                }

                $logger->info('Jest to nowy torrent');
                $series = $seriesRepo->searchForTrackedSeries($title);
                $logger->info('Znaleziono pasujący serial: ' . $series->getName());
                $quality = $this->detectQuality($title, $size);
                $logger->info('Określono jakość na: ' . $quality);
                $seasonEpisode = $this->detectSeasonAndEpisode($title);
                $logger->info('Wykryto sezon i epizod: ' . json_encode($seasonEpisode));
                $seasonEpisode += array('series' => $series->getId());
                $logger->info('Wyszukujemy epizod');
                $episode = $episodeRepo->findBy($seasonEpisode);

                if (empty($episode)) {
                    $logger->info('Nie znaleziono episodu, dodajemy nowy.');
                    $episode = new Episode();
                    $episode->setSeries($series);
                    $episode->setSeason($seasonEpisode['season']);
                    $episode->setNumber($seasonEpisode['number']);

                    $em->persist($episode);
                    $em->flush();
                } else {
                    $episode = $episode[0];
                }

                $newTorrent = new Torrent();
                $newTorrent->setEpisode($episode);
                $newTorrent->setTitle($title);
                $newTorrent->setLink($link);
                $newTorrent->setDate($pubDate);
                $newTorrent->setHash($hash);
                $newTorrent->setSize($size);
                $newTorrent->setQuality($quality);
                $newTorrent->setMagnet($magnet);
                $newTorrent->setPeers($peers);
                $newTorrent->setSeeds($seeds);
                $newTorrent->setVerified($verified);

                $logger->info('Dodano torrent');
                $em->persist($newTorrent);
                $em->flush();

                echo 'Dodano ' . $title . '<br/>';
            } catch(\Exception $e) {
                $logger->info('Eystąpił błąd: ' . $e->getMessage());
                echo $e->getMessage() . '<br/>';
            }
        }
        echo '</pre>';
        return $this->render('TrackerBackBundle:Tracker:index.html.twig');
    }

    public function trackKickassAction()
    {
        $logger = $this->get('logger');
        $em = $this->getDoctrine()->getManager();
        $seriesRepo = $em->getRepository('TrackerBackBundle:Series');
        $episodeRepo = $em->getRepository('TrackerBackBundle:Episode');
        $torrentRepo = $em->getRepository('TrackerBackBundle:Torrent');

        $logger->info('Pobieramy rss`a z najnowszysmi torrentami z kategorii TV');
        $feed = simplexml_load_file('http://kickass.to/tv/?rss=1', 'SimpleXMLElement', LIBXML_NOCDATA);

        $namespaces = $feed->getNamespaces(true);
        echo '<pre>';
        foreach ($feed->xpath('//channel/item') as $item) {
            $title = (string)$item->title;
            $link = (string)$item->link;
            $pubDate = (string)$item->pubDate;

            $torrent = $item->children($namespaces["torrent"]);
            $hash = (string)$torrent->infoHash;
            $size = (int)(string)$torrent->contentLength;
            $size /= 1000000;
            $size = round($size);
            $magnet = (string)$torrent->magnetURI;
            $seeds = (int)(string)$torrent->seeds;
            $peers = (int)(string)$torrent->peers;

            $verified = (bool)$torrent->verivied;

            $logger->info('Analizujemy torrent o nazwie: ' . $title);
            try {
                $logger->info('Sprawdzamy czy taki torrent jest już w bazie');
                $torrent = $torrentRepo->findBy(array('hash'=>$hash));
                if (!empty($torrent) ) {
                    //Aktualizacja istniejacego
                    $torrent = $torrent[0];

                    $torrent->setPeers($peers);
                    $torrent->setSeeds($seeds);
                    $torrent->setVerified($verified);

                    $logger->info('Taki torrent jest już dodany do bazy. Aktualizujemy wartości peers: ' . $peers .
                                  ' seeds: ' . $seeds);

                    $em->persist($torrent);
                    $em->flush();

                    echo 'Zaktualizowano ' . $title . '<br/>';
                    continue;
                }

                $logger->info('Jest to nowy torrent');
                $series = $seriesRepo->searchForTrackedSeries($title);
                $logger->info('Znaleziono pasujący serial: ' . $series->getName());
                $quality = $this->detectQuality($title, $size);
                $logger->info('Określono jakość na: ' . $quality);
                $seasonEpisode = $this->detectSeasonAndEpisode($title);
                $logger->info('Wykryto sezon i epizod: ' . json_encode($seasonEpisode));
                $seasonEpisode += array('series' => $series->getId());
                $logger->info('Wyszukujemy epizod');
                $episode = $episodeRepo->findBy($seasonEpisode);

                if (empty($episode)) {
                    $logger->info('Nie znaleziono episodu, dodajemy nowy.');
                    $episode = new Episode();
                    $episode->setSeries($series);
                    $episode->setSeason($seasonEpisode['season']);
                    $episode->setNumber($seasonEpisode['number']);

                    $em->persist($episode);
                    $em->flush();
                } else {
                    $episode = $episode[0];
                }

                $newTorrent = new Torrent();
                $newTorrent->setEpisode($episode);
                $newTorrent->setTitle($title);
                $newTorrent->setLink($link);
                $newTorrent->setDate($pubDate);
                $newTorrent->setHash($hash);
                $newTorrent->setSize($size);
                $newTorrent->setQuality($quality);
                $newTorrent->setMagnet($magnet);
                $newTorrent->setPeers($peers);
                $newTorrent->setSeeds($seeds);
                $newTorrent->setVerified($verified);

                $logger->info('Dodano torrent');
                $em->persist($newTorrent);
                $em->flush();

                echo 'Dodano ' . $title . '<br/>';
            } catch(\Exception $e) {
                $logger->info('Eystąpił błąd: ' . $e->getMessage());
                echo $e->getMessage() . '<br/>';
            }
        }
        echo '</pre>';
        return $this->render('TrackerBackBundle:Tracker:index.html.twig');
    }

    public function updateAction()
    {
        $html = file_get_contents('http://31.7.58.170/24e530b5ba5d04586bfd91343250ba121fdfd53c');
        var_dump(count($html));
        return $this->render('TrackerBackBundle:Tracker:index.html.twig');
    }

    private function detectQuality($title, $size)
    {
        if (!empty($size)) {
            if ($size <= 100) {
                return 1; //Low Definition
            } elseif ($size > 100 && $size <= 400) {
                return 2; //Medium Definition
            } elseif ($size > 400) {
                return 3; //Hight Definition
            }
        }

        $definitions = array(
            array('360p', '240p', 'R5', 'R6', 'CAM'),
            array('576p', '480p', '480i'),
            array('720p', '1080p', '1080i')
        );

        if (false !== strpos($title, $definitions[0])) {
            return 1; //Low Definition
        } elseif (false !== strpos($title, $definitions[1])) {
            return 2; //Medium Definition
        } elseif (false !== strpos($title, $definitions[2])) {
            return 3; //Hight Definition
        }

        return 0; //Undefined
    }

    private function detectSeasonAndEpisode($title)
    {
        $matches = array();
        $notFound = array(
            'season' => null,
            'number' => null
        );

        preg_match("/[s][0-9]{1,2}[e][0-9]{1,3}/i", $title, $matches);

        if (count($matches) > 0) {
            $season = preg_split('/[sSeE]/', $matches[0]);

            $episode = (int)$season[2];
            $episode = empty($episode) ? null : $episode;

            $season = (int)$season[1];
            $season = empty($season) ? null : $season;

            return array(
                'season' => $season,
                'number' => $episode
            );
        }

        preg_match("/season[\s]?[0-9]*[\s]*episode[0-9]*/i", $title, $matches);

        if (count($matches) > 0) {
            //TODO To nie dziala bo nie bylo pod reka torrenta do testow
            $season = preg_split('/[sSeE]/', $matches[0]);

            $episode = (int)$season[2];
            $episode = empty($episode) ? null : $episode;

            $season = (int)$season[1];
            $season = empty($season) ? null : $season;

            return array(
                'season' => $season,
                'number' => $episode
            );
        }

        preg_match("/season[\s]*[0-9]*/i", $title, $matches);

        if (count($matches) > 0) {
            $season = (int)str_ireplace('season', '', $matches[0]);

            $season = empty($season) ? null : $season;

            return array(
                'season' => $season,
                'number' => null
            );
        }

        preg_match("/st[\.\s]?[0-9]*[\s]*ep[\.\s]?[0-9]*/i", $title, $matches);

        if (count($matches) > 0) {
            $season = preg_split('/ep[\.\s]?/i', $matches[0]);

            $episode = (int)$season[2];
            $episode = empty($episode) ? null : $episode;

            $season = (int)preg_replace('/st[\.\s]?/i', '', $season);
            $season = empty($season) ? null : $season;

            return array(
                'season' => $season,
                'number' => $episode
            );
        }

        return $notFound;
    }
}
