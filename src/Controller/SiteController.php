<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/site", name="site")
     */
    public function index(ProduitRepository $repo,Request $request,ObjectManager $manager)
    {
        $q = $request->query->get('categorie');
        $produits= $repo->findAllWithCategorie($q);


        //$produits= $repo->findAll();

        return $this->render('site/index.html.twig', [
            'produits' => $produits,
        ]);
    }
}
