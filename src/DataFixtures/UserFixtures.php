<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixtures extends Fixture
{
    private $encoder;

    public  function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }

    public function load(ObjectManager $manager)
    {

       $admin=new User();
        $admin->setNom('admin');
        //$password=$this->encoder->encodePassword($admin,'12345678');

         $admin->setPrenom('admin')
                ->setEmail('admin@gmail.com')
                ->setRawPassword('12345678')
                ->setPassword($this->encoder->encodePassword($admin,$admin->getRawPassword()))
                ->setRole(array('ROLE_ADMIN'));
        $manager->persist($admin);

        $manager->flush();

        //dump($admin->getPassword());
    }
}
