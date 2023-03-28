<?php

namespace App\Controller;

use App\Entity\Bill;
use App\Entity\Car;
use App\Repository\CarRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;

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
            'bills' => $bills,
            'owner' => false
        ]);
    }

    #[Route('/user/bill/car/{id}', name: 'bill_car')]
    public function getCarBill(Car $car)
    {
        $bills = $car->getBills();

        return $this->render('/user/bill/index.html.twig', [
            'bills' => $bills,
            'owner' => true
        ]);
    }

    #[Route('/user/bill/{id}', name:'bill_pdf')]
    public function toPdf(Bill $bill, Pdf $pdf)
    {
        $data = [
            'bill'  =>  $bill,
            'user'  =>  $bill->getIdUser(),
            'car'   =>  $bill->getIdCar(),
        ];
        $html =  $this->renderView('/user/bill/download.html.twig', $data);
        $pdf = $pdf->getOutputFromHtml($html);

        return new PdfResponse(
            $pdf,
        );

    }
}
