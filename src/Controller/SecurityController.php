<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/admin/login" , name="security_login" )
     */
    public function login(AuthenticationUtils $authenticationUtils){



        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();



        return $this->render('security/login.html.twig',[
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }


    /**
     * @Route("/admin/dashboard/logout" , name="security_logout" )
     */
    //elle fait rien, le composant security qui se chargera de ça
    public function logout(){
        return $this->render('global/index.html.twig');
    }


    /**
     * @Route("/site/inscription", name="security_registration")
     */
    public function registration(ObjectManager $manager,Request $request,UserPasswordEncoderInterface $encoder,
                                 ClientRepository $repo)
    {
        $client=new Client();
        $form=$this->createForm(ClientType::class,$client);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $hash=$encoder->encodePassword($client,$client->getPassword());//encoding password
            $client->setPassword($hash);
            $manager->persist($client);


            $manager->flush();



            return $this->redirectToRoute('security_loginClient');
        }
        return $this->render('security/registration.html.twig',[
            'formClient'=>$form->createView()
        ]);
    }

    /**
     * @Route("/site/login" , name="security_loginClient" )
     */
    public function loginClient(AuthenticationUtils $authenticationUtils){

        dump("fuck");


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        dump($lastUsername);
        dump($error);

        return $this->render('security/loginClient.html.twig',[
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }


    /**
     * @Route("/site/logout" , name="security_logoutClient" )
     */
    //elle fait rien, le composant security qui se chargera de ça
    public function logoutClient(){
        return $this->render('site/index.html.twig');
    }






}
