<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategorieRepository;
use App\Repository\EditeurRepository;
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(LivreRepository $livreRepository, CategorieRepository $categoryRepository,
        EditeurRepository $editeurRepository,Request $request): Response
    {   
        $titre = $request->query->get('titre');
        $auteurs = $request->query->get('auteurs');
        $categorie = $request->query->get('categorie');
        $livres = $livreRepository->findAll();
        $livres = $livreRepository->search($titre, $auteurs, $categorie);
       // Récupérer toutes les catégories
        $categories = $categoryRepository->findAll();

        // Récupérer tous les éditeurs
        $editeurs = $editeurRepository->findAll();
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'editeurs' => $editeurs,
            'livres' => $livres,
        ]);
    }
}