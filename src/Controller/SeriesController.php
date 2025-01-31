<?php

namespace App\Controller;

use App\DTO\SeriesCreateFromInput;
use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Series;
use App\Form\SeriesType;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{
    public function __construct(private SeriesRepository $seriesRepository, private EntityManagerInterface $entityManager) {}

    #[Route('/series', name: 'app_series', methods: ['GET'])]
    public function seriesList(Request $request): Response
    {
        $seriesList = $this->seriesRepository->findAll();

        return $this->render('series/index.html.twig', [
            'seriesList' => $seriesList,
        ]);
    }




    #[Route('/series/create', name: 'app_series_form', methods: ['GET'])]
    
    public function addSeriesForm(): Response
    {
        $seriesForm = $this->createForm(SeriesType::class, new SeriesCreateFromInput());

    return $this->renderForm('series/form.html.twig', [
        'seriesForm' => $seriesForm,
        'is_edit' => false,
    ]);
    }






    #[Route('/series/create', name: 'app_add_series', methods: ['POST'])]

    public function addSeries(Request $request): Response
    {
        $input = new SeriesCreateFromInput();

        $seriesForm = $this->createForm(SeriesType::class, $input)
            ->handleRequest($request);

        if (!$seriesForm->isValid()) {
            return $this->renderForm('series/form.html.twig', [
                'seriesForm' => $seriesForm,
                'is_edit' => false,
            ]);
        }

        $series = new Series($input->seriesName);
        for ($i = 1; $i <= $input->seasonsQuantity; $i++) {
            $season = new Season($i);
            for ($j = 1; $j <= $input->episodesPerSeason; $j++) {
                $season->addEpisode(new Episode($j));
            }
            $series->addSeason($season);
        }

        $this->addFlash('success', "Série \"{$series->getName()}\" adicionada com sucesso");

        $this->seriesRepository->add($series, true);

        return new RedirectResponse('/series');
    }






    #[Route('/series/delete/{id}', name: 'app_delete_series', methods: ['DELETE'], requirements: ['id' => '[0-9]+'])]

    public function deleteSeries(int $id, ): Response
    {
        $this->seriesRepository->removeById($id);
    
        $this->addFlash('success', 'Série removida com sucesso');
    
        return new RedirectResponse('/series');
    }






    #[Route('/series/edit/{series}', name: 'app_edit_series_form', methods: ['GET'])]

    public function editSeriesForm(SeriesCreateFromInput $seriesCreateFromInput, Series $series): Response
    {

        $seriesForm = $this->createForm(SeriesType::class, $seriesCreateFromInput, ['is_edit' => true]);
    
        return $this->renderForm('series/form.html.twig', [
            'seriesForm' => $seriesForm,
            'seriesCreateFromInput' => $seriesCreateFromInput,
            'series' => $series,
            'is_edit' => true
        ]);
    }






    #[Route('/series/edit/{id}', name: 'app_store_series_changes', methods: ['PUT'])]
    public function storeSeriesChanges(int $id, SeriesCreateFromInput $seriesCreateFromInput, Request $request): Response
    {
        $seriesForm = $this->createForm(SeriesType::class, $seriesCreateFromInput, ['is_edit' => true]);

        $seriesForm->handleRequest($request);

        if (!$seriesForm->isValid()) {
            return $this->renderForm('series/form.html.twig', compact('seriesForm', 'seriesCreateFromInput'));
        }

        // Encontrar a série existente pelo ID
        $series = $this->entityManager->getRepository(Series::class)->find($id);

        if (!$series) {
            throw $this->createNotFoundException('No series found for id ' . $id);
        }

        // Atualizar o nome da série
        $series->setName($seriesCreateFromInput->seriesName);

        // Remover todas as temporadas e episódios existentes
        foreach ($series->getSeasons() as $season) {
            foreach ($season->getEpisodes() as $episode) {
                $this->entityManager->remove($episode);
            }
            $this->entityManager->remove($season);
        }
        $series->getSeasons()->clear();

        // Adicionar novas temporadas e episódios com base nos dados do formulário
        for ($i = 1; $i <= $seriesCreateFromInput->seasonsQuantity; $i++) {
            $season = new Season($i, $series);
            for ($j = 1; $j <= $seriesCreateFromInput->episodesPerSeason; $j++) {
                $episode = new Episode($j, $season);
                $season->addEpisode($episode);
                $this->entityManager->persist($episode);
            }
            $series->addSeason($season);
            $this->entityManager->persist($season);
        }

        $this->entityManager->flush();

        $this->addFlash('success', "Série \"{$series->getName()}\" editada com sucesso");

        return new RedirectResponse('/series');
    }
}





