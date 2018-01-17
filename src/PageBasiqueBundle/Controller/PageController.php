<?php

namespace PageBasiqueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
}
