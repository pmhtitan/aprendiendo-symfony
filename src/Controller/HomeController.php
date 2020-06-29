<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'hello' => 'simfony'
        ]);
    }

    public function animales($nombre, $numero){

        $title = "Bienvenido a la sección de animales.";
        $animales = array('perro', 'hipopótamo', 'luciérnaga', 'mamut');
        $pajaros = [
            'tipo' => 'paloma',
            'categoria' => 'volador',
            'cantidad' => 2,
            'origen' => 'doble-elefante-telepata-de-guerra'
        ];

        return $this->render('home/animales.html.twig', [
            'title' => $title,
            'nombre' => $nombre,
            'numero' => $numero,
            'animales' => $animales,
            'pajaros' => $pajaros
        ]);
    }

    public function redirigir(){
        
   /*
    //  Método 1

       return $this->redirectToRoute('animales', [
           'nombre' => 'girafa',
           'numero' => 68
       ]); 
    
    */

    //  Método 2

        return $this->redirect('http://aprendiendo-symfony.develop:8083/redireccionado');
    }

    public function redireccionado(){

        return $this->render('home/redirect.html.twig');
    }
}
