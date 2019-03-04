<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Avatar;
use App\Entity\Wallet;
use App\Tools\Currency;
use App\Entity\Cryptomonney;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $currency = new Currency();
        $crypto = new Cryptomonney();

        $monney = ['bitcoin', 'ethereum', 'ripple', 'bitcoin-cash', 'cardano', 'litecoin', 'NEM', 'stellar', 'iota', 'dash'];
        $cryptoTab = [];
        $serial = [];

        for($i=0; $i<10; $i++)
        {
            $crypto->setName($monney[$i])
            ->setActualCurrency($currency->getFirstCotation($monney[$i]))
            ->setVariationOfDay($currency->getCotationFor($monney[$i]))
            ->setHistory($currency->generateHistory($monney[$i], 30))
            ->setDescription("Le $monney[$i] (de l'anglais bit : unité d'information binaire et coin « pièce de monnaie ») est une cryptomonnaie autrement appelée monnaie cryptographique. Dans le cas de la dénomination unitaire, on l'écrit « bitcoin » et, dans le cas du système de paiement pair-à-pair on l'écrit « Bitcoin ». L'idée fut présentée pour la première fois en novembre 2008 par une personne, ou un groupe de personnes, sous le pseudonyme de Satoshi Nakamoto1,2. Le code source de l'implémentation de référence fut quant à lui publié en 2009.")
            ;
            $seriObj[] = \serialize($crypto);
            $serial = array_merge($seriObj);
        }

        foreach($serial as $seri)
        {
           $manager->persist(unserialize($seri));
        }

        $user = new User();

        $picture = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTndOGAVgKynSXsLy3r6vhC6Cek-D2ZS74J_saaNSeLUWP-AzE4';

        $password = $this->encoder->encodePassword($user, 'azerty');

        $user->setFirstName('Laurent')
            ->setLastName('AVRIL')
            ->setEmail('test@test.fr')
            ->setPassword($password)
            ->setFunds(850);

        $avatar = new Avatar;

        $avatar->setName($picture)
               ->setUser($user);

        $manager->persist($avatar);

        $manager->persist($user);

        $wallet = new Wallet;
        $wallet->setUser($user)
                ->setQuantity(rand(150, 1000))
                ->getCryptomonney('bitcoin');

        $wallet->setUser($user)
                ->setQuantity(rand(150, 1000))
                ->getCryptomonney('ripple');

        $manager->flush();
    }
}
