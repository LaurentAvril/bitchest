<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\BuyType;
use App\Entity\Wallet;
use App\Form\UserType;
use App\Entity\DateBuy;
use App\Tools\Currency;
use App\Entity\Cryptomonney;
use App\Repository\UserRepository;
use App\Repository\DateBuyRepository;
use App\Repository\WalletRepository;
use App\Repository\CryptomonneyRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{   
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
        // $monney = $this->getDoctrine()
        // ->getRepository(Cryptomonney::class)
        // ->findAll();

        $monney = $repoMonney->findAll();

        $today = (new \DateTime("now", new \DateTimeZone('Europe/Paris')))->format('d/m/Y');

        $table = [];
        
        //Prendre la cote actuelle, la multiplier par la variance du jour et retourner cette nouvelle cote
        foreach($monney as $monn)
        {
            $dateUpdate = ($monn->getLastInitDate())->format('d/m/Y');
            // $dateUpdate =(new \DateTime("yesterday", new \DateTimeZone('Europe/Paris')))->format('d/m/Y');
            
            // dd($dateUpdate < $today);

            if( $dateUpdate < $today && $monn->getVarianceIsInitialisedToday() == 0 )
            {
                $currency = new Currency;
                //Prendre la cote actuelle
                $actuCurr = $monn->getActualCurrency();
                
                //la multiplier par la variance du jour
                $monn->setVariationOfDay($currency->getCotationFor($monn->getName()));
                $newVarOfDay = $monn->getVariationOfDay();
                $monn->setActualCurrency(($actuCurr * $newVarOfDay)/100 + $actuCurr);
                $newActuCurr = $monn->getActualCurrency();
                
                $monn->setActualCurrency($newActuCurr);
                
                $getHist = unserialize($monn->getHistory());
                //l'history se vide dans la BDD
                array_unshift($getHist, ['label' => date('d/m/y'), 'y' => $newActuCurr]);
                
                $monn->setHistory(serialize($getHist));
                $monn->setVarianceIsInitialisedToday(1);
                
                $manager->flush();
            }   
            
            if( $dateUpdate < $today )
            {
                $monn->setVarianceIsInitialisedToday(0);
                $dateUpdate = $monn->setLastInitDate(new \DateTime("now", new \DateTimeZone('Europe/Paris')));
                $manager->flush();
            }
            
            // dd($monn->getVariationOfDay());
            $table[] = $monn->getVariationOfDay();
        }
        // dd($table);
        // return $this->render('backend/cours-des-monnaies.html.twig', [
        //     'table' => $table
        //     ]);
        return new JsonResponse($table, 200);// new Response(json_encode($table), 200);
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
     * @Route("/buy-{name}", name="backend_buy")
     */ 
    public function buy(Cryptomonney $crypto, CryptomonneyRepository $repoMonney, WalletRepository $wallrepo, Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        //Récupération du bon wallet lié au bon name dans l'URL et au bon user
        $wallet = $wallrepo->findAll();
        foreach($wallet as $wall)
        {
            if( ($wall->getCryptomonney()->getId()) == ($crypto->getId()) and $wall->getUser() == $user )
            {
                $goodWallet = $wall;
            }

        }
        $manager->persist($goodWallet);

        $form = $this->createFormBuilder($wallet)
        ->add('quantity', MoneyType::class)
        ->getForm();

        $monney = $repoMonney->findOneBy(['name' => $crypto->getName()]);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        { 
            // dump($user->getFunds()); //Fonds actuels du l'user
            // dump($goodWallet->getQuantity()); //Crédit crypto actuellement possédé par le user
            // dump($crypto->getActualCurrency());//Cours actuel de la cryptomonnaie
            // dump($form->get('quantity')->getViewData());//Montant soumis dans le form
            // dump(round($form->get('quantity')->getViewData() / $crypto->getActualCurrency(),2));// Montant de la cryptomonnaie achetée

            //maj des fonds
            $user->setFunds($user->getFunds() - $form->get('quantity')->getViewData());
            
            //maj du montant des cryptos
            $goodWallet->setQuantity($goodWallet->getQuantity() + $form->get('quantity')->getViewData() / $crypto->getActualCurrency());

            //maj date d'achat
            $dateOfBuy = new DateBuy;
            $dateOfBuy->setDateOfPurchase(new \Datetime('now', new \DateTimeZone('Europe/Paris')))
                      ->setWallet($goodWallet)
                      ->setAmount($form->get('quantity')->getViewData())
                      ->setDayCurrency($crypto->getActualCurrency())
                      ;
            $manager->persist($dateOfBuy);

        // dd($crypto);

            $manager->flush();

            return $this->redirectToRoute('backend_portefeuille');
        }
        
        $user = $this->getUser();
        return $this->render('backend/buy.html.twig', [
            'form'   => $form->createView(),
            'monney'   => $monney,
            'user' => $user
        ]);
    }

    /**
     * @Route("/sell-{name}", name="backend_sell")
     */ 
    public function sell(Wallet $wallet = null, Cryptomonney $crypto, WalletRepository $wallrepo, Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        //Récupération du bon wallet lié au bon name dans l'URL et au bon user
        $wallet = $wallrepo->findAll();
        foreach($wallet as $wall)
        {
            if( ($wall->getCryptomonney()->getId()) == ($crypto->getId()) and $wall->getUser() == $user)
            {
                $goodWallet = $wall;
            }
        }
        
        $user->setFunds( $goodWallet->getQuantity() * $crypto->getActualCurrency() + $user->getFunds() );
        $goodWallet->setQuantity(0);
        // dd($tab);
        foreach($goodWallet->getDateBuys() as $dateBuys)
        {
            // $tab[] = $dateBuys;
            $manager->remove($dateBuys);
        }
        $manager->flush();

        return $this->redirectToRoute('backend_portefeuille');
    }

    /**
     * @Route("/historique-achats", name="backend_history")
     */ 
    public function historyOfBuys(WalletRepository $repoWallet, DateBuyRepository $historyRepo, Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        $wallet = $repoWallet->findBy(['user' => $user]);
       
        $monney = $this->getDoctrine()
            ->getRepository(Cryptomonney::class)
            ->findAll()
        ;


        return $this->render('backend/history.html.twig', [
                'monney' => $monney,
                'wallet' => $wallet,
                'user' => $user
            ]);
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
