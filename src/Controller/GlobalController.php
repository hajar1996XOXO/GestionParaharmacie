<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use App\Form\ProduitType;
use App\Form\UserType;
use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GlobalController extends AbstractController
{


    /**
     * @Route("/admin", name="welcome")
     */

    public function index(){
        return $this->render('global/index.html.twig');
    }

    /**
     * @Route("/admin/dashboard", name="dashboard")
     */

    public function Dashboard(CommandeRepository $repoCom,ClientRepository $repoCl,ProduitRepository $repoP){
        $commandes=$repoCom->findByExampleField();
        $ventes=$repoCom->findByExampleField2();
        $clients=$repoCl->findAll();
        $produits=$repoP->findAll();

        return $this->render('global/dashboard.html.twig',[
            'commandes'=> $commandes,
            'clients'=>$clients,
            'produits'=>$produits,
            'ventes'=>$ventes
        ]);
    }

    /**
     * @Route("/admin/dashboard/addEmploye", name="add_employe")
     * @Route("/admin/dashboard/showEmploye/edit/{id}",   name="edit_employe")
     */

    public function AddEmploye(User $user=null,Request $request,ObjectManager $manager,UserPasswordEncoderInterface $encoder){

        if(!$user){//if user is null it means we re going to add a new one, or else just edit
            $user=new User();
        }

        $form=$this->createForm(UserType::class,$user);
        $form->handleRequest($request);//input
        if($form->isSubmitted() && $form->isValid() ){

            $password=$user->getRawPassword();
            $user->setPassword($encoder->encodePassword($user,$password));
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('show_employe');//redirects route
        }
        return $this->render('global/addEmploye.html.twig',[
            'formUser'=>$form->createView() , //on pass a twig lee formulaire
            'editMode'=> $user->getId()!==null  //edit mode is true or false
        ]);
    }

    /**
     * @Route("/admin/dashboard/showEmploye", name="show_employe")
     */

    public function showEmploye(UserRepository $repo,Request $request,PaginatorInterface $paginator){


        $q = $request->query->get('q');
        //$usersSearch = $repo->findAllWithSearch($q);//we no longer use this,coz its for search only
        $queryBuilder = $repo->getWithSearchQueryBuilder($q);//combines search with pagination

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );




        return $this->render('global/showEmploye.html.twig',[
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/dashboard/showEmploye/delete/{id}", name="delete_employe")
     */
    public function deleteEmploye($id,UserRepository $repo,ObjectManager $manager){
        $user=$repo->find($id);
        $manager->remove($user);
        $manager->flush();
       return $this->redirectToRoute('show_employe');

    }




    /**
     * @Route("/admin/dashboard/addProduit", name="add_produit")
     * @Route("/admin/dashboard/showProduit/edit/{id}",   name="edit_produit")
     */

    public function AddProduit(Produit $produit=null,Request $request,ObjectManager $manager){

        if(!$produit){//if produit is null it means we re going to add a new one, or else just edit
            $produit=new Produit();
        }

        $form=$this->createForm(ProduitType::class,$produit);
        $form->handleRequest($request);//input
        if($form->isSubmitted() && $form->isValid() ){
            $file=$produit->getImagePath();//get path
            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('upload_directory'),$fileName);
            $produit->setImagePath($fileName);

            $manager->persist($produit);
            $manager->flush();

            return $this->redirectToRoute('show_produit');//redirects route
        }
        return $this->render('global/addProduit.html.twig',[
            'formProduit'=>$form->createView() , //on pass a twig lee formulaire
            'editMode'=> $produit->getId()!==null //edit mode is true or false
        ]);
    }


    /**
     * @Route("/admin/dashboard/showProduit", name="show_produit")
     */

    public function showProduit(ProduitRepository $repo,Request $request,PaginatorInterface $paginator){


        $q = $request->query->get('q');
        //$usersSearch = $repo->findAllWithSearch($q);//we no longer use this,coz its for search only
        $queryBuilder = $repo->getWithSearchQueryBuilder($q);//combines search with pagination

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );




        return $this->render('global/showProduit.html.twig',[
            'pagination' => $pagination
        ]);
    }


    /**
     * @Route("/admin/dashboard/showProduit/delete/{id}", name="delete_produit")
     */
    public function deleteProduit($id,ProduitRepository $repo,ObjectManager $manager){
        $produit=$repo->find($id);
        $manager->remove($produit);
        $manager->flush();
        return $this->redirectToRoute('show_produit');

    }


    /**
     * @Route("/admin/dashboard/showClient", name="show_client")
     */

    public function showClient(ClientRepository $repo,Request $request,PaginatorInterface $paginator){


        $q = $request->query->get('q');
        //$usersSearch = $repo->findAllWithSearch($q);//we no longer use this,coz its for search only
        $queryBuilder = $repo->getWithSearchQueryBuilder($q);//combines search with pagination

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );




        return $this->render('global/showClient.html.twig',[
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/dashboard/showClient/delete/{id}", name="delete_client")
     */
    public function deleteClient($id,ClientRepository $repo,ObjectManager $manager){
        $client=$repo->find($id);
        $manager->remove($client);
        $manager->flush();
        return $this->redirectToRoute('show_client');

    }

    /**
     * @Route("/admin/dashboard/showCommandes", name="show_commandes")
     */

    public function showCommandes(CommandeRepository $repo,Request $request,PaginatorInterface $paginator){


        $q = $request->query->get('q');
        $queryBuilder = $repo->getWithSearchQueryBuilder($q);//combines search with pagination


        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );




        return $this->render('global/showCommandes.html.twig',[
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/dashboard/showCommandes/view/{id}", name="view_commande")
     */

    public function viewCommande(CommandeRepository $repo,$id){
       $commande=$repo->find($id);

        return $this->render('global/viewCommand.html.twig',[
            'commande'=>$commande
        ]);
    }

    /**
     * @Route("/admin/dashboard/showCommandes/confirm/{id}", name="confirm_commande")
     */
    public function confirmCommande($id,CommandeRepository $repo,ObjectManager $manager,ProduitRepository $repoP){
        $commande=$repo->find($id);
        $commande->setEtat("Confirmée");
        //change product quantity
        $p=$commande->getProduit();
        $p->setQteTotale(($p->getQteTotale())-($commande->getQte()));
        $manager->persist($commande);
        $manager->persist($p);
        $manager->flush();

        return $this->redirectToRoute('show_commandes');

    }

}
