<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\ProduitCart;
use App\Repository\CommandeRepository;
use App\Repository\ProduitCartRepository;
use App\Repository\ProduitRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        if($request->query->get('categorie')==null){
            if($request->query->get('marque')==null){
                $q=$request->query->get('search');
            }else{
                $q=$request->query->get('marque');
            }
        }else{
            $q=$request->query->get('categorie');
        }

        //$q=($request->query->get('categorie')==null)? $request->query->get('search'):$request->query->get('categorie');
        $queryBuilder = $repo->getWithSearchQueryBuilder($q);//combines search with pagination
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );


        //add to cart
        if($id!=null and $this->getUser()==null) {
            $this->addFlash(
                'notice2',
                "You Can't add Product to your Cart if you are not logged in !"
            );
        }elseif ($id)
            {
                $produit = $repo->find($id);

                $produitCart=new ProduitCart();
                $produitCart->setProduit($produit)
                    ->setMontantTotal($produit->getPrix())
                    ->setQte(1)
                    ->setEtat("Pas valide");
                $manager->persist($produitCart);
                $manager->flush();

                $this->addFlash(
                    'notice',
                    'Product Successfully added to your Cart !'
                );

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


    /**
     * @Route("/site/contactUs", name="contact")
     */
    public function contactUs(ProduitCartRepository $repo){
        $produitsCart=$repo->findAll();

        return $this->render('site/contact.html.twig',[
            'produitsCart'=>$produitsCart
        ]);
    }


    /**
     * @Route("/site/orders", name="orders")
     */
    public function orders(CommandeRepository $repo){
        $user=$this->getUser();
        $commandes=$repo->findCommandeByUser($user->getEmail());

        return $this->render('site/orders.html.twig',[
            'commandes'=>$commandes
        ]);
    }

    /**
     * @Route("/site/pdf/{id}", name="pdf")
     */
    public function pdfAction(Pdf $pdf,CommandeRepository $repo,$id)//dependancy Injection
    {
        //$user=$this->getUser();
        $commande=$repo->find($id);

        $html = $this->renderView('site/pdf.html.twig',[
            'commande'=>$commande
        ]);

        $filename='BonLivraison';

        return new Response($pdf->getOutputFromHtml($html),200, array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="'.$filename.'.pdf"'
            )
        );
    }


    /**
     * @Route("/site/test" , name="test")
     */
    public function test(CommandeRepository $repo){
        return $this->render('site/pdf.html.twig');
    }




}
