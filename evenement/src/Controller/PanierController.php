<?php

namespace App\Controller;
use App\Repository\ProduitRepository;
use SessionIdInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(SessionInterface $session, ProduitRepository $p): Response
    {
        $panier = $session->get('panier', []);
        $panierWithData = [];
    
        foreach ($panier as $id => $quantity) {
            $produit = $p->find($id);
    
            // Vérifier si le produit existe et n'est pas null
            if ($produit !== null) {
                $panierWithData[] = [
                    'produit' => $produit,
                    'quantity' => $quantity,
                ];
            } else {
                // Le produit n'existe pas dans la base de données, vous pouvez le gérer ici
                // Par exemple, vous pouvez supprimer l'article du panier
                unset($panier[$id]);
                $session->set('panier', $panier);
            }
        }
    
        $total = 0;
        foreach ($panierWithData as $item) {
            $totalItem = $item['produit']->getPrix() * $item['quantity'];
            $total += $totalItem;
        }
        return $this->render('panier/index.html.twig', [
            'items' => $panierWithData,
            'total' => $total,
        ]);
    }

#[Route('/panier/add/{id}', name: 'addpanier')]

public function add($id, SessionInterface $session)
{ 
    $panier = $session->get('panier', []);

    if (!empty($panier[$id])) {
        $panier[$id]++;
    } else {
        $panier[$id] = 1;
    }

    $session->set('panier', $panier);

    // Retourne une redirection vers la route "app_panier"
    return $this->redirectToRoute("app_panier");
}
#[Route('/panier/remove/{id}', name: 'removepanier')]
public function remove($id, SessionInterface $session)
{
    $panier = $session->get('panier', []);

    if (!empty($panier[$id])) {
        unset($panier[$id]);
        $session->set('panier', $panier); // Mettre à jour la session après la suppression
    }

    return $this->redirectToRoute('app_panier');
}

#[Route('/panier/del/{id}', name: 'deletep')]

public function del($id, SessionInterface $session)
{ 
    $panier = $session->get('panier', []);

    if (!empty($panier[$id])) {
        if($panier[$id]>1)
       {$panier[$id]--;}
    else { unset($panier[$id]);}
    }
    
    $session->set('panier', $panier);

    // Retourne une redirection vers la route "app_panier"
    return $this->redirectToRoute("app_panier");
} 
}