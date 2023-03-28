<?php

namespace App\Controller;


use App\Entity\Car;
use App\Form\CarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        }
        else {
            $get = $request->get('find');
            $cars = $carRepository->createQueryBuilder('c')
                ->where('c.brand = :get')
                ->orWhere('c.model = :get')
                ->setParameter('get', $get)
                ->getQuery()
                ->getResult()
                ;
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
