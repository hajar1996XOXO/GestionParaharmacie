<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\ProduitCart;
use App\Repository\ProduitCartRepository;
use App\Repository\ProduitRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/site", name="site")
     * @Route("/site/cart/{id}",   name="add_ToCart")
     */
    public function index(ProduitRepository $repo,Request $request,PaginatorInterface $paginator,$id=null,ObjectManager $manager
    , ProduitCartRepository $repoCart)
    {

        //$produits= $repo->findAllWithCategorie($q);

        $q=($request->query->get('categorie')==null)? $request->query->get('search'):$request->query->get('categorie');
        $queryBuilder = $repo->getWithSearchQueryBuilder($q);//combines search with pagination
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );


        //add to cart
        if($id) {
            $produit = $repo->find($id);

            $produitCart=new ProduitCart();
            $produitCart->setProduit($produit)
                        ->setMontantTotal($produit->getPrix())
                        ->setQte(1)
                        ->setEtat("Pas valide");
            $manager->persist($produitCart);
            $manager->flush();

            return $this->redirectToRoute('site');
        }
        $ProduitsCart=$repoCart->findAll();

        return $this->render('site/index.html.twig', [
            'pagination' => $pagination,
            'produitsCart'=>$ProduitsCart
        ]);
    }



    /**
     * @Route("/site/edit/{id}",   name="edit_cart")
     */
    public function EditCart(ProduitCartRepository $repoCart,$id,Request $request,ObjectManager $manager){
        $ProduitsCart=$repoCart->findAll();
        $produitCart=$repoCart->find($id);

        if($request->query->get('qte')){
            $qte=$request->query->get('qte');
            $produitCart->setQte($qte)
                        ->setMontantTotal($qte*($produitCart->getMontantTotal()));

            $manager->persist($produitCart);
            $manager->flush();

        }

        return $this->render("site/editCart.html.twig",[
            'produitsCart'=>$ProduitsCart,
            'produitCart'=>$produitCart
        ]);
    }


    /**
     * @Route("/site/delete/{id}", name="delete_cart")
     */
    public function deleteProduit($id,ProduitCartRepository  $repo,ObjectManager $manager){
        $produitCart=$repo->find($id);
        $manager->remove($produitCart);
        $manager->flush();

        return $this->redirectToRoute('site');

    }

    /**
     * @Route("site/commander/{id}",  name="commander")
     */
    public function Commander(ProduitCartRepository $repoCart,ObjectManager $manager,$id,Request $request){
        //show produits in cart
        $produitCart=$repoCart->find($id);


        $commande=new Commande();
        $commande->setClient($this->getUser())
                 ->setProduit($produitCart->getProduit())
                 ->setQte($produitCart->getQte())
                 ->setMontantTotal($produitCart->getMontantTotal())
                 ->setDateCommande(new \DateTime())
                 ->setModePaiement($request->query->get('radio'))
                 ->setEtat('En Attente');
        $manager->persist($commande);
        $manager->remove($produitCart);
        $manager->flush();



        return $this->render('site/ThankYou.html.twig');

    }



}
