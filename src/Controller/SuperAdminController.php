<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Role;
use App\Form\UserType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


    
class SuperAdminController extends AbstractController
{   
    /**
     * @Route("/admin", name="super_admin")
     * @IsGranted("ROLE_ADMIN")
     */ 
    public function Superadmin(ObjectManager $manager, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $newUser = new User;
        $form = $this->createForm(UserType::class, $newUser);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $roleRepo = $this->getDoctrine()->getRepository(Role::class);
            
            //encryptage du password
            $hash = $encoder->encodePassword($newUser, $newUser->getPassword());
            $newUser->setPassword($hash);
            if($form->getData()->getAdminChoice() == false)
            {                
                ($newUser->addRole($roleRepo->findOneByTitle('ROLE_USER')));
            }
            else
            {
                $newUser->addRole($roleRepo->findOneByTitle('ROLE_USER'));
                $newUser->addRole($roleRepo->findOneByTitle('ROLE_ADMIN'));
            }
            
            $manager->persist($newUser);
            $manager->flush();

            $this->addFlash(
                'success',
                'L\'enregistrement a bien été effectué'
        );
        }
        $user = $this->getUser();

        return $this->render('admin/admin.html.twig', [
            'form' => $form->createView(),
            'user' => $user
            ]);
    }
}