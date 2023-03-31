<?php

namespace App\Controller;


use App\Entity\Car;
use App\Form\CarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CarController extends AbstractController
{



    #[Route('/user/cars/', name: 'cars')]
    public function carList(CarRepository $carRepository, Request $request)
    {
        if (!$request->get('find')) {
            $cars = $carRepository->findAll();
        } else {
            $get = $request->get('find');
            $cars = $carRepository->createQueryBuilder('c')
                ->where('c.brand = :get')
                ->orWhere('c.model = :get')
                ->setParameter('get', $get)
                ->getQuery()
                ->getResult();
        }
        return $this->render('/user/car/cars.html.twig', [
            'cars' => $cars
        ]);
    }

    #[Route('/user/car/createBill/{id}', name: 'car_bill')]
    public function createBill(Car $car)
    {

        $response = $this->forward('App\Controller\BillController::index', [
            'car' => $car,
        ]);

        return $response;
    }

    #[Route('/user/car/register', name: 'car_new')]
    public function newCar(Request $request, EntityManagerInterface $em)
    {
        $car = new Car();

        $form = $this->createForm(CarType::class, $car);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $car = $form->getData();
            $car->setOwner($this->getUser());
            $em->persist($car);
            $em->flush();
            return $this->redirectToRoute('car_index', [
                'id' => $car->getId()
            ]);
        }

        return $this->renderForm('user/car/register.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/user/car/getCars', name: 'car_list')]
    public function allCars()
    {
        return $this->render('/user/car/car_list.html.twig', [
            'cars' => $this->getUser()->getCars()
        ]);
    }

    /* Función para generar coches de la marca seat */
    #[Route('/user/car/register/seat', name: 'seat')]
    public function seat(EntityManagerInterface $em)
    {
        $modelos = [
            'Ibiza',
            'Toledo',
            'Tarraco',
            'Arona',
        ];
        $letras = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
        ];

        for ($i = 30; $i < 100; $i++) {
            $plate = rand(1000, 9999) . $letras[rand(0, 12)] . $letras[rand(0, 12)] . $letras[rand(0, 12)];
            $car = new Car();
            $car
                ->setBrand('seat')
                ->setModel($modelos[rand(0, 3)])
                ->setPlate($plate)
                ->setPrice(rand(50, 1000))
                ->setOwner($this->getUser());
            $em->persist($car);
            $em->flush();
        };
        return $this->redirectToRoute('car_list', []);
    }

    /**
     * Función para generar coches de la marca Kia
     *
     * @param EntityManagerInterface $em
     * @return void
     */
    #[Route('/user/car/register/kia', name: 'kia')]
    public function kia(EntityManagerInterface $em)
    {
        $letras = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
        ];

        $modelos = [
            'Sportage',
            'Sorento',
            'Picanto',
            'Rio',
            'Ceed',
            'Niro',
            'Stonic',
            'Ceed',
            'EV6',
        ];

        for ($i = 30; $i < 100; $i++) {
            $plate = rand(1000, 9999) . $letras[rand(0, 12)] . $letras[rand(0, 12)] . $letras[rand(0, 12)];
            $car = new Car();
            $car
                ->setBrand('Kia')
                ->setModel($modelos[rand(0, 3)])
                ->setPlate($plate)
                ->setPrice(rand(50, 1000))
                ->setOwner($this->getUser());
            $em->persist($car);
            $em->flush();
        };
        return $this->redirectToRoute('car_list', []);
    }

    #[Route('/user/car/download', name: 'cars_download')]
    public function downCars(Pdf $pdf, CarRepository $carRepository)
    {
        $cars = $carRepository->findAll();

        $brands = $carRepository->createQueryBuilder('c')
            ->select('c.brand , count(c.id)')
            ->groupBy('c.brand')
            ->getQuery()
            ->getResult();


        $data = [
            'cars'   => $cars,
            'brands' => $brands,
        ];
         $html = $this->renderView('/user/car/download.html.twig', $data);
        $pdf = $pdf->getOutputFromHtml($html);
        return new PdfResponse (
                $pdf,
                'ListaCoches.pdf'
        );  
/*          return $this->render('/user/car/download.html.twig', $data);  */
    }

    #[Route('/user/car/{id}', name: 'car_index')]
    public function car(Car $car)
    {
        $user = $this->getUser();
        return $this->render('/user/car/index.html.twig', [
            'car'  => $car,
            'user' => $user,
        ]);
    }


    #[Route('/user/car/edit/{id}', name: 'car_edit')]
    public function editCar(Car $car, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(CarType::class, $car);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $car = $form->getData();

            $em->persist($car);
            $em->flush();
            return $this->redirectToRoute('car_index', [
                'id' => $car->getId(),
            ]);
        }
        return $this->renderForm('user/car/edit.html.twig', [
            'form' => $form
        ]);
    }

    

    #[Route('/user/car/delete/{id}', name: 'car_delete')]
    public function FunctionName(Car $car, EntityManagerInterface $em)
    {
        $em->remove($car);

        $em->flush();

        $this->addFlash(
            'success',
            'Coche borrado con éxito'
        );

        return $this->redirectToRoute('user_index');
    }
}
