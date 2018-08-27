<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ClientRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/login" , name="security_login" )
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
     * @Route("/dashboard/logout" , name="security_logout" )
     */
    //elle fait rien, le composant security qui se chargera de ça
    public function logout(){
    }


    /**
     * @Route("/site/inscription", name="security_registration")
     */

}
