<?php

namespace App\Controller;

use App\Entity\Car;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\CarType;
use ArrayAccess;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user_index')]
    public function index(Request $req): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => $req->getSession()->get('username'),
        ]);
    }

    #[Route('/user/cars', name: 'user_get_cars' )]
    public function getCars() 
    {
        return $this->redirectToRoute('cars');
    }

    #[Route('/user/bill', name: 'user_bill')]
    public function getBills()
    {
        return $this->redirectToRoute('bill_user');
    }

    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/admin', name: 'admin_index')]
    public function admin(Request $req)
    {
        return $this->render('user/index.html.twig', [




        ]);
    }
}
