<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index()
    {
        $em = $this->getDoctrine();
        $users = $em->getRepository(Users::class)->findAll();

        return $this->render('users/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/users/add", name="users_add")
     */
    public function createUser(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new Users();
        $user->setName('Nico');
        $user->setEmail('NicolasB@yahoo.fr');
        $user->setDatecrea(new \DateTime());

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($user);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->redirectToRoute('users');
    }

    /**
     * @Route("/users/{id}", name="user_show")
     */
    public function showUser($id)
    {
        $em = $this->getDoctrine();
        $user = $em->getRepository(Users::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        // or render a template
        // in the template, print things with {{ product.name }}
        return $this->render('users/show.html.twig', ['user' => $user]);
    }
    
    /**
     * @Route("/users/edit/{id}")
     */
    public function updateUser($id)
    {
        // ETAPE 1: Récuperer l'entitymanager et récuperer l'objet sur lequel on veut travailler
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(Users::class)->find($id);

        // ETAPE 2 : Verification si l'ID appartient bien a un USER
        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        // ETAPE 3 : Modifier les champs que l'on souhaite et on persiste en bdd avec flush()
        $user->setName('James Bond');
        $entityManager->flush();

        // ETAPE 4 : On envoie vers la vue
        return $this->redirectToRoute('user_show', [
            'id' => $user->getId()
        ]);
    }    

    /**
     * @Route("/users/delete/{id}")
     */
    public function deleteUser($id)
    {
        // ETAPE 1: Récuperer l'entitymanager et récuperer l'objet sur lequel on veut travailler
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(Users::class)->find($id);

        // ETAPE 2 : Verification si l'ID appartient bien a un USER
        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$id
            );
        }

        // ETAPE 3 : On supprime et on persiste en bdd avec flush()
        $entityManager->remove($user);
        $entityManager->flush();

        // ETAPE 4 : On envoie vers la vue
        return $this->render('users/deleted.html.twig');
    }

}
