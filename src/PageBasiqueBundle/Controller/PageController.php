<?php

namespace PageBasiqueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class PageController extends Controller
{
    public function indexAction()
    {
        return $this->render('PageBasiqueBundle:accueil:index.html.twig');
    }

	public function pageSocialAction()
	{
		return $this->render('PageBasiqueBundle:social:social.html.twig');
	}

	/**
	 * @Route("/admin")
	 */
	public function adminAction()
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

		$user = $this->getUser();

		dump($user);

		return new Response('<html><body>Admin page! => '.$user->getUsername().'    ! </body></html>');
	}

	/**
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function helloAction($name)
	{
		// The second parameter is used to specify on what object the role is tested.
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

		if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
			throw $this->createAccessDeniedException();
		}
	}

}
