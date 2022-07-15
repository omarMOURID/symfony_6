<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_home.')]
class HomeController extends AbstractController
{
    // ce controlleur est pour l'ajout et la modification de l'entité Personne
    #[Route('/{id<\d*>?0}', name: 'home')]
    public function index(Personne $personne = null, ManagerRegistry $doctrine, Request $request): Response
    {
        $new = false; // pour savoir si on a créé une nouvelle entité ou bien on va effectuer une modification sur entité déjà existante
        if(!$personne) {
            $new = true;
            $personne = new Personne();
        }
        // la creation d'une formulaire personneFormType
        $form = $this->createForm(PersonneFormType::class, $personne);

        $form->handleRequest($request);// pour savoir si la form est envoyer ou pas encores

        if($form->isSubmitted() && $form->isValid()) {
            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();
            if($new === true) {
                $this->addFlash('success', 'le Personne a été ajouté avec success');
            } else {
                $this->addFlash('success', 'le Personne a été modifié avec success');
            }

            return $this->redirectToRoute('app_home.list');
        }

        return $this->render('home/index.html.twig', [
            'personneForm' => $form->createView(),
        ]);
    }

    //Ce controlleur est pour la suppression de l'entité personne
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Personne $personne = null, ManagerRegistry $doctrine): Response
    {

        if(!$personne) {
            $this->addFlash('error', 'le personne que vous voulez supprimer est introuvable');
        } else {
            $manager = $doctrine->getManager();
            $manager->remove($personne);
            $manager->flush();
            $id = $personne->getId();
            $this->addFlash('success', "le personne d'id $id a été supprimé avec success");
        }
        return $this->redirectToRoute('app_home.list');
    }

    //ce controlleur est pour l'affichage des donnée
    #[Route('/list', name: 'list')]
    public function showFormList(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();

        return $this->render('home/list.html.twig', [
           'personnes' => $personnes,
        ]);
    }
}
