<?php

namespace App\Tools;

use App\Entity\Cryptomonney;
use App\Repository\CryptomonneyRepository;
use Doctrine\Common\Persistence\ObjectManager;

class CotationAutoUpdate
{
    public function CotationAutoUpdate(CryptomonneyRepository $repoMonney, ObjectManager $manager)
    {
        $monney = $this->getDoctrine()
        ->getRepository(Cryptomonney::class)
        ->findAll()
        ;
        
        //Prendre la cote actuelle, la multiplier par la variance du jour et retourner cette nouvelle cote

        foreach($monney as $monn)
        {
            //Prendre la cote actuelle
            $actuCurr = $monn->getActualCurrency();

            //la multiplier par la variance du jour
            $newVarOfDay = $monn->setVariationOfDay($currency->getCotationFor($monn->getName()));
            $newActuCurr = ($actuCurr * $newVarOfDay)/100 + $actuCurr;
            
            $monn->setActualCurrency($newActuCurr);

            $newHistory = [];
            $getHist = unserialize($monn->getHistory());
            $newVars = ['label' => 'J-31', 'y' => $newActuCurr];
            $newHistory = array_merge($getHist, $newVars);
            $monn->setHistory(serialize($newHistory));

            $manager->flush();

        }
    }
}
