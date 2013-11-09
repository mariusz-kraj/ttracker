<?php

namespace Tracker\Bundle\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $torrentRepo = $em->getRepository('TrackerBackBundle:Torrent');
        $torrents = $torrentRepo->getLatestQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $torrents,
            $this->get('request')->query->get('page', 1),
            10
        );

        return $this->render(
            'TrackerFrontBundle:Index:index.html.twig',
            array(
                'torrents' => $pagination,
                'pagination' => $pagination
            )
        );
    }

    public function feedAction($format = 'rss')
    {
        $em = $this->getDoctrine()->getManager();
        $torrentRepo = $em->getRepository('TrackerBackBundle:Torrent');
        $torrents = $torrentRepo->getLatest();

        $feed = $this->get('eko_feed.feed.manager')->get('torrents');
        $feed->addFromArray($torrents);

        return new Response($feed->render($format));
    }
}
