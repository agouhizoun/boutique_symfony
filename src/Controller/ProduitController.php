<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
     /**
     * @Route("/produit/{id<\d+>}", name="produit_show")
     */
    public function show($id, ProduitRepository $repo)
    {
        $produit = $repo->find($id);       

        return $this->render("produit/show.html.twig", [
            'produit' => $produit
        ]);
    }  
    
    /**
     * @Route("/produits", name="produits_all")
     */
    public function all(ProduitRepository $repoPro, CategorieRepository $repoCat)
    {
        $produits = $repoPro->findAll();
        $categories = $repoCat->findAll();

        return $this->render("produit/all.html.twig", [
            'produits' => $produits,
            'categories' => $categories
          
        ]);
}

/**
     * @Route("/categorie-{id<\d+>}", name="produits_categorie")
     */
    public function categorieProduits($id, CategorieRepository $repo)
    {
        // on récupère la categorie sur laquelle on a cliqué pour accéder aux produits liés
        $categorie = $repo->find($id);

        // $produits = $categorie->getProduits();

        $categories = $repo->findAll();
       

        return $this->render("produit/all.html.twig", [
            // 'produits' => $produits,
            // Les lignes de codes : $produits = $categorie->getProduits(); et 'produits' => $produits,
            // sont remplacées par 'produits' => $categorie->getproduits(),

            // on récupère les produits de la categorie cliquée grâce à la relation
            'produits' => $categorie->getProduits(),
            'categories' => $categories
        ]);
}


}