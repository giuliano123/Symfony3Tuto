<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{

    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $session->getFlashBag()->add('info', 'La page de contact n\'existe pas encore');
        return $this->redirectToRoute('homepage');
    }
}
