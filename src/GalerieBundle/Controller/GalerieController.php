<?php

namespace GalerieBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GalerieBundle\Entity\Galerie;
use GalerieBundle\Entity\Commentaire;
use GalerieBundle\Entity\Categorie;
use GalerieBundle\Form\CategorieType;
use GalerieBundle\Form\GalerieType;
use GalerieBundle\Form\ImageType;
use GalerieBundle\Form\CommentaireType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\File\File;

class GalerieController extends Controller
{
	public function managerAction(Request $request)
	{
		$categorie = new Categorie;
		$form = $this->get('form.factory')->create(CategorieType::class, $categorie);

		$order = $request->query->get('order');
		$categorie = $request->query->get('categorie');

		$page = $request->query->get('page');
		if(!$page)
			$page = 1;

		$galeries = $this->getDoctrine()
			->getRepository('GalerieBundle:Galerie')->getAllGalerieByCategorie($order,$categorie,$page);

		$nb_page = $this->getDoctrine()
			->getRepository('GalerieBundle:Galerie')->getNbPageGalerie($categorie);

		$categories = $this->getDoctrine()
			->getRepository('GalerieBundle:Categorie')
			->findAll();

		$em    = $this->get('doctrine.orm.entity_manager');
		$dql   = "SELECT a FROM GalerieBundle:Galerie a";

		if($categorie)
		{
			$dql .= " WHERE a.categorie = :cat";
			$query = $em->createQuery($dql)->setParameter('cat', "$categorie");
		}
		else
			$query = $em->createQuery($dql);

		$paginator  = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
			$query, /* query NOT result */
			$request->query->getInt('page', $page)/*page number*/,
			20/*limit per page*/
		);

		return $this->render('GalerieBundle::manager.html.twig', array(
			'galeries' => $galeries,
			'form' => $form->createView(),
			'categories' => $categories,
			'nb_page' => ceil($nb_page[0][1] / 20),
			'current_page' => $page,
			'categorie'=> $categorie,
			'order' => $order,
			'pagination' => $pagination
		));
	}

	public function addAction(Request $request)
	{
		$galerie = new Galerie;
		$form = $this->get('form.factory')->create(GalerieType::class, $galerie);

		/* Validation des erreurs + ajout bdd */
		if ($form->handleRequest($request)->isValid()){

			$manager = $this->getDoctrine()->getManager();

			foreach ($galerie->getImages() as $image)
			{
				$image->setGalerie($galerie);
				$image->uploadFile();
			}

			$manager->persist($galerie);
			$manager->flush();

			return $this->redirect($this->generateUrl('galerie'));
		}
		return $this->render('GalerieBundle::add.html.twig',array(
				'form' => $form->createView()
			)
		);
	}

	public function editAction(Request $request, Galerie $galerie)
	{
		$form = $this->get('form.factory')->create(GalerieType::class, $galerie);

		$images_old = new ArrayCollection();
		foreach ($galerie->getImages() as $image)
		{
			$images_old->add($image);
		}

		/* Validation des erreurs + ajout bdd */
		if ($form->handleRequest($request)->isValid()){

			foreach($images_old as $image_old) {
				if(false === $galerie->getImages()->contains($image_old))
				{
					$this->getDoctrine()->getManager()->remove($image_old);
				}
			}

			foreach ($galerie->getImages() as $image)
			{
				$image->setGalerie($galerie);
				$image->uploadFile();
			}

			$manager = $this->getDoctrine()->getManager();
			$manager->flush();

			return $this->redirect($this->generateUrl('galerie'));
		}

		return $this->render('GalerieBundle::edit.html.twig',array(
				'form' => $form->createView(),
				'galerie' => $galerie,
				'images' => $galerie->getImages()
			)
		);
	}

	public function deleteAction(Galerie $galerie)
	{
		// operation de suppression.
		$manager = $this->getDoctrine()->getManager();
		$manager->remove($galerie);
		$manager->flush();

		return $this->redirect($this->generateUrl('galerie'));
	}

	public function voirAction(Request $request, Galerie $galerie)
	{
		$commentaire = new Commentaire;
		$form = $this->get('form.factory')->create(CommentaireType::class, $commentaire);

		/* Validation des erreurs + ajout bdd */
		if ($form->handleRequest($request)->isValid()){

			$commentaire->setGalerie($galerie);
			$manager = $this->getDoctrine()->getManager();
			$manager->persist($commentaire);
			$manager->flush();

			return $this->redirect($this->generateUrl('galerie'));
		}
		return $this->render('GalerieBundle::voir.html.twig',array(
				'form' => $form->createView(),
				'galerie' => $galerie,
				'images' => $galerie->getImages(),
				'commentaires' => $galerie->getCommentaires()
			)
		);
	}

	public function publicationAction(Request $request, Galerie $galerie){

		if($request->isXmlHttpRequest()){

			$state = $galerie->reverseState();
			$galerie->setEtat($state);

			$em = $this->getDoctrine()->getManager();
			$em->persist($galerie);
			$em->flush();

			return new JsonResponse(array('state' => $state));
		}
	}



}
