<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;  //  << para cargar respuestas http  ->  return new Response('Hola save!!');
use App\Entity\Animal;
use Symfony\Component\Form\Extension\Core\Type\DateType;    //  <<
use Symfony\Component\Form\Extension\Core\Type\TextType;    //  <<
use Symfony\Component\Form\Extension\Core\Type\SubmitType;  //  << 
use Symfony\Component\HttpFoundation\Request;               //  << Estos 4 son para los formularios
use Symfony\Component\HttpFoundation\Session\Session;  //    << Para session flash
use Symfony\Component\Validator\Validation;            //    << Para validar campos de form
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Form\AnimalType; 

class AnimalController extends AbstractController
{

    /**
     * @Route("/animal", name="animal")
     */
    public function index()
    {
        //  Sacar todos los animales de la bbdd / animales
       $animal_repositorio = $this->getDoctrine()->getRepository(Animal::class);

       $animales = $animal_repositorio->findAll();

       //   Sacar un único animal, en base a una condición estricta

       $animal = $animal_repositorio->findOneBy(['tipo' => 'Ballena']);

       $animalesFiltroRaza = $animal_repositorio->findBy([
                'raza' => 'uncowonco del Sur'
            ], [
                'id' => 'DESC'
            ]);

        //  Query Builder       https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/query-builder.html
        
                /* $qb = $animal_repositorio->createQueryBuilder('a')
                                            ->andWhere("a.color = :color")
                                            ->setParameter('color', 'Globox')   
                                            ->orderBy('a.id', 'DESC')                                      
                                            ->getQuery();
                $animalPersonalizado = $qb->execute(); */

        //  Repository
        $animalPersonalizado = $animal_repositorio->porColor("marron");

        //  DQL, utilizando objetos y entidades.
        $entityManager = $this->getDoctrine()->getManager();
        $dql = "SELECT a FROM App\Entity\Animal a WHERE a.color = 'Globox'";
        $query = $entityManager->createQuery($dql);
        $queryDQL = $query->execute();
            

        // Con SQL de toda la vida
        $connection = $this->getDoctrine()->getConnection();
        $sql = "SELECT * FROM animales ORDER BY id DESC";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $querySQL = $stmt->fetchAll();


        return $this->render('animal/index.html.twig', [
            'controller_name' => 'AnimalController',
            'animales' => $animales,
            'animal' => $animal,
            'animalesFiltroRaza' => $animalesFiltroRaza,
            'queryBuilder' => $animalPersonalizado,
            'DQL' => $queryDQL,
            'SQL' => $querySQL,
        ]);

        
    }

    public function crearAnimal(Request $request){
        $animal = new Animal();
        
        $form = $this->createForm(AnimalType::class, $animal);  //  src/Form/AnimalType.php

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($animal);
            $entityManager->flush();

        //  Session flash
            $session = new Session();
            $session->getFlashBag()->add('message', 'Animal creado');
            return $this->redirectToRoute('animal_create');
        }
        
        return $this->render('animal/crear-animal.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function validarEmail($email){
        
        $validator = Validation::createValidator();
        $errores = $validator->validate($email, [
            new Email(),
            new NotBlank(),
        ]);

        if(count($errores) !== 0){
            //  Hay errores, los mostramos
            foreach($errores as $error){
                echo  "Error en el email. : ". $error->getMessage()."<br>";
            }
        }else{
            echo "El email ha sido guardado correctamente";
        }
        die();
    }

    public function save(){
                
        //  Guardar en una tabla de la base de datos     https://symfony.com/doc/current/doctrine.html  

        $entityManager = $this->getDoctrine()->getManager();
        
        $animal = new Animal();
        $animal->setTipo('Avestruz');
        $animal->setColor('verde pálido');
        $animal->setRaza('uncowonco del Este');

        //  Persisto el objeto en doctrine (en local)
        $entityManager->persist($animal);

        //  Lo lanzo a la bbdd [TODAS LAS QUERIES PERSISTIDAS.]
        $entityManager->flush();

        //  Devuelvo feedback
        return new Response('Guardado nuevo animal con id '. $animal->getId());       
    }

    //  Sacar un registro de la bbdd
    public function animal($id){ 
        $animal = $this->getDoctrine()->getRepository(Animal::class)->find($id);

        if($animal){
            $message = 'Observa este maravilloso animal: '. $animal->getTipo(). " " . $animal->getColor(). " ". $animal->getRaza();
        }else{
            $message = "No existe el animal buscado.";
        }

        return new Response($message);
    }

    public function update($id){

             /* PASOS */

       //   Cargar doctrine

       //   Cargar entityManager

       //   Find para conseguir objeto del id pasado

       //   Comprobar si el objeto me llega

       //   Asignarle valores nuevos al objeto

       //   Persistir en doctrine

       //   Guardar en la bd

       //   Respuesta

        $entityManager = $this->getDoctrine()->getManager();
        $animalUpdate = $entityManager->getRepository(Animal::class)->find($id);

       if(!$animalUpdate){
           throw $this->createNotFoundException('No se ha encontrado el animal a updatear con el id: ' .$id);
       }else{           
        $animalUpdate->setTipo("Ballena $id");
        $animalUpdate->setColor("Rojo Shiny");
 
        $entityManager->persist($animalUpdate);  // Según la doc de Doctrine, persist aquí da igual. 
        $entityManager->flush();
       }
       
       return new Response('Has actualizado correctamente el animal ' . $animalUpdate->getTipo(). 'con id ' . $animalUpdate->getId());          

    }

    public function delete($id){  
        
        //  Manager de Entidades
        $entityManager = $this->getDoctrine()->getManager();

        //  Obtenemos el repositorio del id pasado
        $animal_repo = $entityManager->getRepository(Animal::class)->find($id);

        //  Comprobamos que existe el animal buscado.
        if(!$animal_repo){
            $message = "El animal introducido no existe o no se encuentra disponible";
        }else{           
            $entityManager->remove($animal_repo);
            $entityManager->flush();
            $message = "Se ha borrado correctamente el animal ". $animal_repo->tipo();
        }

        return new Response($message);
    }

   
}
