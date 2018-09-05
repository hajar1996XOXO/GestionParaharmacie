<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Entity\Produit;
use App\Entity\User;
use App\Form\FournisseurType;
use App\Form\ProduitType;
use App\Form\UserType;
use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\FournisseurRepository;
use App\Repository\MessageRepository;
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
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }
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
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }
        $editMode=true;
        if(!$user){//if user is null it means we re going to add a new one, or else just edit
            $editMode=false;
            $user=new User();
        }

        $form=$this->createForm(UserType::class,$user);
        $form->handleRequest($request);//input
        if($form->isSubmitted() && $form->isValid() ){

            $password=$user->getRawPassword();
            $user->setPassword($encoder->encodePassword($user,$password));
            $manager->persist($user);
            $manager->flush();

            if($editMode==true){
                $this->addFlash(
                    'notice1',
                    'Employe Successfully edited !'
                );
            }else{
                $this->addFlash(
                    'notice2',
                    'Employe Successfully added  !'
                );
            }


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
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }

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
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }
        $user=$repo->find($id);
        $manager->remove($user);
        $this->addFlash(
            'notice',
            'Employe Successfully deleted !'
        );
        $manager->flush();
       return $this->redirectToRoute('show_employe');

    }




    /**
     * @Route("/admin/dashboard/addProduit", name="add_produit")
     * @Route("/admin/dashboard/showProduit/edit/{id}",   name="edit_produit")
     */

    public function AddProduit(Produit $produit=null,Request $request,ObjectManager $manager){
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }
        $editMode=true;
        if(!$produit){//if produit is null it means we re going to add a new one, or else just edit
            $produit=new Produit();
            $editMode=false;
        }

        $form=$this->createForm(ProduitType::class,$produit);
        $form->handleRequest($request);//input
        if($form->isSubmitted() && $form->isValid() ){
            $file=$produit->getImagePath();//get path
            $fileName=md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('upload_directory'),$fileName);
            $produit->setImagePath($fileName);

            if($editMode==true){
                $this->addFlash(
                    'notice1',
                    'Product Successfully edited !'
                );
            }else{
                $this->addFlash(
                    'notice2',
                    'Product Successfully added  !'
                );
            }



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
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }

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
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }
        $produit=$repo->find($id);
        $manager->remove($produit);
        $this->addFlash(
            'notice',
            'Produit Successfully deleted !'
        );
        $manager->flush();
        return $this->redirectToRoute('show_produit');

    }


    /**
     * @Route("/admin/dashboard/showClient", name="show_client")
     */

    public function showClient(ClientRepository $repo,Request $request,PaginatorInterface $paginator){
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }

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
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }
        $client=$repo->find($id);
        $manager->remove($client);
        $this->addFlash(
            'notice',
            'Client Successfully deleted !'
        );
        $manager->flush();
        return $this->redirectToRoute('show_client');

    }

    /**
     * @Route("/admin/dashboard/showCommandes", name="show_commandes")
     */

    public function showCommandes(CommandeRepository $repo,Request $request,PaginatorInterface $paginator){
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }

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
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }
       $commande=$repo->find($id);

        return $this->render('global/viewCommand.html.twig',[
            'commande'=>$commande
        ]);
    }

    /**
     * @Route("/admin/dashboard/showCommandes/confirm/{id}", name="confirm_commande")
     */
    public function confirmCommande($id,CommandeRepository $repo,ObjectManager $manager,ProduitRepository $repoP){
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }
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
    /**
     * @Route("/admin/dashboard/rapport", name="rapport")
     */
    public function rapport(){
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }
        return $this->render('global/rapport.html.twig');
    }


    /**
     * @Route("/admin/dashboard/message", name="message")
     */
    public function message(MessageRepository $repo,Request $request,PaginatorInterface $paginator){
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }


        $q = $request->query->get('q');
        $queryBuilder = $repo->getWithSearchQueryBuilder($q);//combines search with pagination


        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('global/message.html.twig',[
            'pagination'=>$pagination
        ]);
    }

    /**
     * @Route("/admin/dashboard/message/delete/{id}", name="delete_message")
     */
    public function deleteMessage($id,MessageRepository $repo,ObjectManager $manager){
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }
        $message=$repo->find($id);
        $manager->remove($message);
        $this->addFlash(
            'notice',
            'Message Successfully deleted !'
        );
        $manager->flush();
        return $this->redirectToRoute('message');

    }


    /**
     * @Route("/admin/dashboard/addFournisseur", name="add_fournisseur")
     * @Route("/admin/dashboard/showFournisseur/edit/{id}",   name="edit_fournisseur")
     */

    public function AddFournisseur(Fournisseur $fournisseur=null,Request $request,ObjectManager $manager){
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }

        if(!$fournisseur){//if produit is null it means we re going to add a new one, or else just edit
            $fournisseur=new Fournisseur();
        }

        $form=$this->createForm(FournisseurType::class,$fournisseur);
        $form->handleRequest($request);//input
        if($form->isSubmitted() && $form->isValid() ){

            $this->addFlash(
                'notice',
                'Fournisseur Successfully added !'
            );

            $manager->persist($fournisseur);
            $manager->flush();

            return $this->redirectToRoute('show_fournisseur');//redirects route
        }
        return $this->render('global/addFournisseur.html.twig',[
            'formFournisseur'=>$form->createView() , //on pass a twig lee formulaire
            'editMode'=> $fournisseur->getId()!==null //edit mode is true or false
        ]);
    }


    /**
     * @Route("/admin/dashboard/showFournisseur", name="show_fournisseur")
     */

    public function showFournisseur(FournisseurRepository $repo,Request $request,PaginatorInterface $paginator){
        if($this->getUser()==null){
            return $this->redirectToRoute('security_login');
        }


        $q = $request->query->get('q');
        //$usersSearch = $repo->findAllWithSearch($q);//we no longer use this,coz its for search only
        $queryBuilder = $repo->getWithSearchQueryBuilder($q);//combines search with pagination

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );




        return $this->render('global/showFournisseur.html.twig',[
            'pagination' => $pagination
        ]);
    }










}
