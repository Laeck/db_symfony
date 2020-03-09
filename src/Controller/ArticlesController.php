<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Users;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticlesController extends AbstractController
{
    /**
     * @Route("/articles", name="articles")
     */
    public function index()
    {
        return $this->render('articles/index.html.twig', [
            'controller_name' => 'ArticlesController',
        ]);
    }

    /**
     * @Route("/articles/new", name="articles_new")
     */
    public function new(Request $request)
    {
        $article = new Articles();
        $article->setTitre('un titre');
        $article->setContent('Un content');
        $article->setDatecrea(new \DateTime('Tomorrow'));

        $form = $this->createFormBuilder($article)
        ->add('titre', TextType::class)
        ->add('content', TextareaType::class)
        ->add('datecrea', DateType::class)
        ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
        ->getForm();

        // Si la requete est en POST
        if($request->isMethod('POST')) {
            
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            // Nous verrons la validation des objets en détail dans le prochain chapitre
            if($form->isValid()) 
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée');

                $em = $this->getDoctrine();
                $users = $em->getRepository(Users::class)->findAll();
                return $this->redirectToRoute('users', array('users' => $users));
            }
        }

        return $this->render('articles/new.html.twig', array(
            'form' => $form->createView(),
        ));

    }
}
