<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


    #[Route('/panier', name: 'panier_')]
    
class PanierController extends AbstractController
{
    #[Route('/', name: 'show')]

    public function show(SessionInterface $session, ProduitRepository $repo): Response
    {
        $panier = $session->get("panier", []);

        $dataPanier = [];
        $total = 0;

        /* pour chaque ligne de mon tableau panier dans la session, je récupère le produit 
        qui correspond à l'id qui est en clé et la quantité en valeur.
        Dans le tableau dataPanier je rajoute à chaque tour de boucle un nouveau tableau qui contient 
        une clé "produit" avec comme valeur le produit récupéré, et une autre "quantité" qui contient 
        la quantité du produit en question
        - puis à chaque tour de boucle je calcule le prix total du produit (prix du produit * quantité) et
        je l'ajoute à ma variable $total
        */
        foreach ($panier as $id => $quantite ) {

            $produit = $repo->find($id);
            $dataPanier[]=
            [
                "produit" => $produit, // produit que j'ai récupéré avec l'id
                "quantite" => $quantite 
            ];

            $total += $produit->getPrix() * $quantite;

        }

        // dd($dataPanier);

        return $this->render('panier/index.html.twig', [
            'dataPanier' => $dataPanier,
            'total' => $total
            // 'controller_name' => 'PanierController',
        ]);
    }

    // on va créer une route pour ajouter un panier
        #[Route('/add/{id<\d+>}', name: 'add')]
    public function add($id, SessionInterface $session)
    {
        // on récupère ou on crée le panier dans la session
        $panier = $session->get('panier', []);

        // on vérifie si l'id existe déjà, dans ce cas on incrémente sinon on le crée
        if ( empty( $panier[$id] ))
        {
            $panier[$id] = 1;
        }else{
            $panier[$id]++;
        }

        // on sauvegarde dans la session 
        $session->set("panier", $panier);
        
        // dd($session->get("panier"));

        return $this->redirectToRoute("panier_show");

    }

        // on va créer une route pour supprimer un panier
        #[Route('/delete/{id<\d+>}', name: 'delete_produit')]
    public function delete($id, SessionInterface $session)

    {
        // on récupère ou on crée le panier dans la session
        $panier = $session->get('panier', []);

       // Visuellement le panier se présente de la façon suivante :
       /* $panier=[
            "3" => 4,
            "5" =>2
        ] */

        // on vérifie si l'id existe déjà, 
        if ( !empty( $panier[$id] ))
        {
            unset($panier[$id]);
        }else{
            $this->addFlash("error", "Le produit que vous essayer de retirer n'existe pas !!!");

            return $this->redirectToRoute("panier_show");
        }       
        // on sauvegarde dans la session 
        $session->set("panier", $panier);
        
        // dd($session->get("panier"));

        $this->addFlash("success", "Le produit a bien été retiré du panier!");
        return $this->redirectToRoute("panier_show");

    
}

}