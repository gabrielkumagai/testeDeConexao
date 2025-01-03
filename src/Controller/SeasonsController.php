<?php

namespace App\Controller;

use App\Entity\Series;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class SeasonsController extends AbstractController
{
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    #[Route('/series/{series}/seasons', name: 'app_seasons')]
    public function index( Series $series,): Response
    {
        $num = 0;
        $seasonsss = $this->cache->get(
            "series_{$series->getId()}_seasons",
            function (ItemInterface $item) use ($series) {
                $item->expiresAfter(new \DateInterval('PT10S'));

                /** @var PersistentCollection $seasons */
                $seasonsss = $series->getSeasons();
                $num = count($seasonsss);

                return $seasonsss->getValues();
            }
        );
                
        return $this->render('seasons/index.html.twig', compact('series', 'seasonsss', 'num'));
    }
        
    }
    



