<?php

namespace App\Controller;

use App\Entity\Bill;
use App\Entity\Car;
use App\Service\SoluPDF;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Fpdf\Fpdf;
use Mpdf\Mpdf;
use Spatie\Browsershot\Browsershot;
use Symfony\Bundle\TwigBundle\DependencyInjection\Compiler\TwigEnvironmentPass;
use Symfony\Component\HttpFoundation\Response;

class BillController extends AbstractController
{
    #[Route('/user/bill/create', name: 'bill_create')]
    public function index(Car $car, EntityManagerInterface $em)
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

    #[Route('/user/bill/list', name: 'bill_list')]
    public function listBills(Pdf $pdf)
    {
        $bills = $this->getUser()->getBills();
        $data = [
            'bills' => $bills,
            'user' => $this->getUser(),
            'owner' => false,
        ];

        $html = $this->renderView('/user/bill/list.html.twig', $data);
        $pdf = $pdf->getOutputFromHtml($html);
        return new PdfResponse(
            $pdf,
        );
    }




    #[Route('/user/bill/all', name: 'bill_all')]
    public function allBills(Pdf $pdf)
    {
        $bills = $this->getUser()->getBills();
        $data = [
            'bills' => $bills,
            'user'  => $this->getUser(),
            'owner' => false, 
        ];

        $html = $this->renderView('/user/bill/all.html.twig', $data);
        $pdf->setOption('enable-local-file-access', true);
        $pdf = $pdf->getOutputFromHtml($html);

        return new PdfResponse(
            $pdf,
            'TodasLasFacturas.pdf'
        );

    }

    /**
     * Undocumented function
     *
     * @return void
     */
    #[Route('/user/bill/MPDF', name: 'mpdf_bill')]
    public function pruebaMpdf()
    {
        
        $mpdf = new Mpdf();
        $bills = $this->getUser()->getBills();
        $data = [
            'bills' => $bills,
            'user'  => $this->getUser(),
            'owner' => false, 
        ];


        
        $mpdf->WriteHTML($html,\Mpdf\HTMLParserMode::HTML_BODY );

        $mpdf->Output();


    }


    #[Route('/user/bill/dompdf', name: 'bill_dompdf')]
    public function dmpf()
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $pdf = new Dompdf($options);
        $bills = $this->getUser()->getBills();
        $data = [
            'bills' => $bills,
            'user'  => $this->getUser(),
            'owner' => false, 
        ];

        $html = $this->renderView('/user/bill/invoice.html.twig', $data);


        $pdf->loadHtml($html);

        
        $pdf->render();

        
        $pdf->stream("DOMPDF.pdf", [
            "Attachment" => false
        ]);    
         
/*          return $this->render('/user/bill/dompdf.html.twig',$data);   */
    }


    #[Route('/user/bill/puro' , name: 'purecss')]
    public function pure()
    {
        return $this->render('/user/bill/invoice.html.twig',[
            
        ]);
    }




    /**
     * 
     */
    #[Route('/user/bill/{id}', name:'bill_pdf')]
    public function toPdf(Bill $bill, Pdf $pdf)
    {
        $data = [
            'bill'  =>  $bill,
            'user'  =>  $bill->getIdUser(),
            'car'   =>  $bill->getIdCar(),
        ];
        $html =  $this->renderView('/user/bill/download.html.twig', $data);
        $pdf->setOption('enable-local-file-access', true);
        $pdf = $pdf->getOutputFromHtml($html);
        

        return new PdfResponse(
            $pdf,
        );

    }

        //TODO:: REVISAR !!!

        #[Route('/user/bill/car/{id}', name: 'bill_car')]
        public function getCarBill(Car $car)
        {
            $bills = $car->getBills();
    
            return $this->render('/user/bill/index.html.twig', [
                'bills' => $bills,
                'owner' => true
            ]);
        }


}
