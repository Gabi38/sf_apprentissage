<?php

namespace GalerieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GalerieBundle\Form\CategorieType;
use GalerieBundle\Entity\Categorie;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategorieController extends Controller
{

	/**
	 * Gestion
	 */
	public function managerAction(Request $request)
	{
		$categorie = new Categorie;
		$form = $this->get('form.factory')->create(CategorieType::class, $categorie);

		/* Récéption du formulaire */
		if ($form->handleRequest($request)->isValid()){
			$em = $this->getDoctrine()->getManager();
			$em->persist($categorie);
			$em->flush();

			$request->getSession()->getFlashBag()->add('succes', 'Catégorie enregistrée avec succès');
			return $this->redirect($this->generateUrl('galeriecategorie_manager'));
		}

		$categories = $this->getDoctrine()
			->getRepository('GalerieBundle:Categorie')
			->findAll();

		return $this->render('GalerieBundle:Categorie:manager.html.twig',array(
				'form' => $form->createView(),
				'categories' => $categories,
			)
		);
	}

	/**
	 * Supprimer
	 */
	public function supprimerAction(Request $request, Categorie $categorie)
	{
		if(count($categorie->getGaleries()) != 0)  throw new NotFoundHttpException('Cette page n\'est pas disponible');

		$em = $this->getDoctrine()->getManager();
		$em->remove($categorie);
		$em->flush();

		$request->getSession()->getFlashBag()->add('succes', 'Catégorie supprimée avec succès');
		return $this->redirect($this->generateUrl('galeriecategorie_manager'));
	}

	/**
	 * Modifier
	 */
	public function modifierAction(Request $request, Categorie $categorie)
	{
		$form = $this->get('form.factory')->create(CategorieType::class, $categorie);

		/* Récéption du formulaire */
		if ($form->handleRequest($request)->isValid()){
			$em = $this->getDoctrine()->getManager();
			$em->persist($categorie);
			$em->flush();

			$request->getSession()->getFlashBag()->add('succes', 'Catégorie enregistrée avec succès');
			return $this->redirect($this->generateUrl('galeriecategorie_manager'));
		}

		return $this->render('GalerieBundle:Categorie:modifier.html.twig',
			array(
				'categorie' => $categorie,
				'form' => $form->createView()
			)
		);

	}

}