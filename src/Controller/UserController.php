<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user_index')]
    public function index(Request $req): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => $req->getSession()->get('username'),
        ]);
    }

    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/admin', name: 'admin_index')]
    public function admin(Request $req)
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => $req->getSession()->get('username'),
        ]);
    }
}
