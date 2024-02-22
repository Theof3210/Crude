<?php

namespace App\Controller;

use App\Form\CrudType;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_main")
     */
    public function index(ArticleRepository $repo): Response
    {
        $data = $repo->findAll();
        return $this->render('main/index.html.twig', [
            'data' => $data,
        ]);
    }
    /**
     * @Route("/create", name="app_create", methods= {"GET", "POST"})
     */
    public function create(Request $request): Response
    {
        $crud = new Article(); #instanciation de l'objet article
        $form = $this->createForm(CrudType::class, $crud); #creation du formulaire grace au type CrudType
        $form->handleRequest($request); #gestion des donnees du formulaire
        if ($form->isSubmitted() && $form->isValid()) { #verification si le formulaire est soumis et valide
            #insertion dans la BDD
            $sendDatabase = $this->getDoctrine()
                ->getManager();
            $sendDatabase->persist($crud);
            $sendDatabase->flush();

            $this->addFlash('notice', 'Votre donnée a bien été enregistré'); #message flash pour afficher un message temporaire
            return $this->redirectToRoute("app_main"); #redirection vers la page d'accueil une fois que les données ont été enregistrées
        }
        return $this->render('main/createForm.html.twig', [ #renvoie vers la vue avec les parametres necessaires
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/update/{id}", name="app_update", methods= {"GET", "POST"})
     */
    public function update(Request $request , ArticleRepository $repo, $id): Response
    { 
        $crud = $repo->find($id);
        $form = $this->createForm(CrudType::class, $crud); #creation du formulaire grace au type CrudType
        $form->handleRequest($request); #gestion des donnees du formulaire
        if ($form->isSubmitted() && $form->isValid()) { #verification si le formulaire est soumis et valide
            #insertion dans la BDD
            $sendDatabase = $this->getDoctrine()
                ->getManager();
            $sendDatabase->persist($crud);
            $sendDatabase->flush();

            $this->addFlash('notice', 'Votre donnée a bien été modifier'); #message flash pour afficher un message temporaire
            return $this->redirectToRoute("app_main"); #redirection vers la page d'accueil une fois que les données ont été enregistrées
        }
        return $this->render('main/updateForm.html.twig', [ #renvoie vers la vue avec les parametres necessaires
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/delete/{id}", name="app_delete")
     */
    public function delete(ArticleRepository $repo, $id): Response {
        $crud = $repo->find($id);
        $repo->remove($crud,true);
        $this->addFlash('notice', 'Votre donnée a bien été supprimer');
        return $this->redirectToRoute("app_main");
    }
}