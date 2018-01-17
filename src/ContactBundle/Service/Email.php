<?php

namespace ContactBundle\Service;

class Email{
	private $mailer;
	private $templating;
	public function __construct(\Swift_Mailer $mailer, $templating)
	{
		$this->mailer = $mailer;
		$this->templating = $templating;
	}
	public function sendMail($email, $contact)
	{
		$message = \Swift_Message::newInstance()
			->setSubject('Bonjour')
			->setFrom('noreply@colocarts.com')
			->setTo($email)
			->setBody(
				$this->templating->render('ContactBundle:Mail:simple.html.twig', array(
						'titre' => 'Formulaire de contact',
						'contenu' => 'Votre formulaire a bien été envoyé <br>'.$contact->getNom().'<br>'.$contact->getPrenom().'<br>'.$contact->getEmail().'<br>'.$contact->getMessage()
					)
				),
				'text/html'
			);
		$this->mailer->send($message);
	}
}
?>