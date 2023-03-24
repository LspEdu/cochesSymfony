<?php

namespace App\Controller;

use App\Entity\Bill;
use App\Entity\Car;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BillController extends AbstractController
{
    #[Route('/user/bill/create', name: 'bill_create')]
    public function index(Request $request, Car $car, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $bill = new Bill();
        $bill
            ->setIdCar($car)
            ->setIdUser($user)
            ->setCreatedAt(new DateTimeImmutable('now'));

        $em->persist($bill);
        try {
            $em->flush();
        } catch (\Throwable $th) {
            $this
                ->addFlash(
                    'error',
                    'Algo fue mal'
                );
        }
        return $this->redirectToRoute('user_index');
    }

    #[Route('/user/bill/getBills', name: 'bill_user')]
    public function getBills()
    {
        $bills = $this->getUser()->getBills();
        return $this->render('/user/bill/index.html.twig',[
            'bills' => $bills
        ]);
    }
}
