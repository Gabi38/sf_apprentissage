<?php

namespace ContactBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ContactBundle\Entity\Contact;
use ContactBundle\Form\ContactType;
use ContactBundle\Form\ImageType;

use Symfony\Component\HttpFoundation\Request;


class ContactController extends Controller
{
	public function managerAction()
	{
		$contacts = $this->getDoctrine()
			->getRepository('ContactBundle:Contact')->findAll();
		dump($contacts);

		return $this->render('ContactBundle::manager.html.twig', array(
			'contacts' => $contacts,
		));
	}


	public function voirAction(Contact $contact)
	{
		return $this->render('ContactBundle::voir.html.twig',array(
				'contact' => $contact
			)
		);
	}

	public function deleteAction(Contact $contact)
	{
		// operation de suppression.
		$manager = $this->getDoctrine()->getManager();
		$manager->remove($contact);
		$manager->flush();

		return $this->redirect($this->generateUrl('contact'));
	}

	public function contactAction(Request $request)
	{
		$contact = new Contact;

		$form = $this->get('form.factory')->create(ContactType::class, $contact);

		/* Validation des erreurs + ajout bdd */
		if ($form->handleRequest($request)->isValid()){

			$manager = $this->getDoctrine()->getManager();
			$manager->persist($contact);
			$manager->flush();

			$this->get('service.email')->sendMail('gabriel@colocarts.com',$contact);

			$request->getSession()
				->getFlashBag()
				->add('success', 'Votre message à bien était envoyé')
			;

			return $this->redirect($this->generateUrl('accueil'));
		}

		return $this->render('ContactBundle::contact.html.twig',array(
				'form' => $form->createView(),
				'contact' => $contact
			)
		);
	}




}
