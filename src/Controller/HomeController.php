<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $firstCotation = $this->getFirstCotation('bitcoin');
        $cotationFor = $this->getCotationFor('bitcoin');

        dump($firstCotation);
        dd($cotationFor);


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * Renvoie la valeur de mise sur le marché de la crypto monnaie (valeur initiale pour 1 euro)
     * @param $cryptoname {string} Le nom de la crypto monnaie
     */
    function getFirstCotation($cryptoname)
    {
        return ord(substr($cryptoname,0,1)) + rand(0, 10);
    }

    /**
     * Renvoie la variation de cotation de la crypto monnaie sur un jour (% de variation de la cryptomonnaie)
     * @param $cryptoname {string} Le nom de la crypto monnaie
     */
    function getCotationFor($cryptoname)
    {	
        return ((rand(0, 99)>40) ? 1 : -1) * ((rand(0, 99)>49) ? ord(substr($cryptoname,0,1)) : ord(substr($cryptoname,-1))) * (rand(1,10) * .01);
    }


}
