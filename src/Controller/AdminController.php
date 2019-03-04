<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Tools\Currency;
use App\Entity\Cryptomonney;
use App\Repository\UserRepository;
use App\Repository\CryptomonneyRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends AbstractController
{
    /**
    * @Route("/admin", name="backend_admin")
    * @IsGranted("ROLE_USER")
    */
    public function admin(User $user = NULL, Request $request, UserPasswordEncoderInterface $encoder, UserRepository $repoFig)
    {
        $newUser = new User;
        $form = $this->createForm(UserType::class, $newUser);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        { 
            $manager = $this->getDoctrine()->getManager();

            $newUser = $form->getData();

            $hash = $encoder->encodePassword($newUser, $newUser->getPassword());
            $newUser->setPassword($hash);

            $newUser->getRoles();

            // $form->get('title')->addError(new FormError("Le titre ne peut pas être vide"));
                      
            $manager->merge($newUser);
            
            $manager->flush();
            
            $this->addFlash(
                'success',
                'L\'utilisateur a bien été enregistré'
            );
        }

        return $this->render('backend/admin.html.twig', [
            'form'   => $form->createView(),
            'newUser' => $newUser,
            'user' => $this->getUser()
            ]);
    }    
    
    /**
     * @Route("/cours-des-cryptomonnaies", name="backend_cours")
     * @IsGranted("ROLE_USER")
     */ 
    public function monneyCours(CryptomonneyRepository $repoMonney, ObjectManager $manager)
    {
        $monney = $this->getDoctrine()
            ->getRepository(Cryptomonney::class)
            ->findAll()
        ;

        return $this->render('backend/cours-des-monnaies.html.twig', [
            'monney'   => $monney,
            'user' => $this->getUser()
            ]);
    }

    /**
     * @Route("/maj-variance", name="backend_maj-variance")
     */ 
    public function majVariance(CryptomonneyRepository $repoMonney, ObjectManager $manager)
    {
        $monney = $this->getDoctrine()
        ->getRepository(Cryptomonney::class)
        ->findAll()
        ;
        
        //Prendre la cote actuelle, la multiplier par la variance du jour et retourner cette nouvelle cote
        foreach($monney as $monn)
        {
            $currency = new Currency;
            //Prendre la cote actuelle
            $actuCurr = $monn->getActualCurrency();
            
            //la multiplier par la variance du jour
            $monn->setVariationOfDay($currency->getCotationFor($monn->getName()));
            $newVarOfDay = $monn->getVariationOfDay();
            $monn->setActualCurrency((round($actuCurr * $newVarOfDay)/100 + $actuCurr),2);
            $newActuCurr = $monn->getActualCurrency();
            
            $monn->setActualCurrency($newActuCurr);
            
            $getHist = unserialize($monn->getHistory());
            //l'history se vide dans la BDD
            array_unshift($getHist, ['label' => date('d/m/y'), 'y' => $newActuCurr]);
            
            // dd($getHist);
            $monn->setHistory(serialize($getHist));

            $manager->flush();
        }
        return new Response();
    }

    /**
     * @Route("/deconnexion", name="deconnexion")
     */ 
    public function deconnexion()
    {
        $this->addFlash(
                'success',
                'Vous avez bien été déconnecté'
        );
    }

    /**
     * @Route("/portefeuille", name="backend_portefeuille")
     * @IsGranted("ROLE_USER")
     */ 
    public function portefeuille()
    {
        $monney = $this->getDoctrine()
            ->getRepository(Cryptomonney::class)
            ->findAll()
        ;

        return $this->render('backend/portefeuille.html.twig', [
            'monney'   => $monney,
            'user' => $this->getUser()
        ]);
    }


}
