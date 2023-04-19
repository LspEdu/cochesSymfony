<?php

namespace App\Service;


use Fpdf\Fpdf;


class SoluPDF extends Fpdf
{


    function Header()
    {

        $width = $this->getPageWidth();

        // Escribimos las variables en función de la orientación de la página para no tener
        // que repetir código y agrupar las variables 
        if ($width >= 211) {
            $x = 262;
            $espacio = 120;
        } else {
            $x = 175;
            $espacio = 80;
        }
        // Logo
        $this->Image('./img/RC.png', 5, 0, 30);
        // Arial bold 15
        $this->Image('./img/andalucia.png', $x, 8, 30);
        $this->SetFont('Arial', '', 15);
        // Movernos a la derecha
        $this->Cell($espacio, 10);
        // Título
        $this->Cell(30,10,'Listado de Coches',0,0,'C');
        // Salto de línea
        $this->Ln(20);
    }

    function Footer()
    {
        $width = $this->GetPageWidth();

        if ($width >= 211) {
            $x = 186;
        } else {
            $x = 276;
        }

        $this->Image('./img/medidas_personales_1_1.png', 10, $x, 16);
    }

    function BasicTable($header, $bills)
    {
        // Cabecera
        foreach ($header as $col)
            $this->Cell(40, 7, $col, 1);
        $this->Ln();
        // Datos
        foreach ($bills as $int =>  $bill) {
            $car = $bill->getIdCar();

                 $this->Cell(40, 6, $car->getBrand(), 1); 
                 $this->Cell(40, 6, $car->getModel(), 1); 
                 $this->Cell(40, 6, $car->getPlate(), 1); 
                 $this->Cell(40, 6, $car->getKm(), 1); 
                 $this->Cell(40, 6, $car->getEngine(), 1); 
                 $this->Cell(40, 6, $car->getColor(), 1); 
             
            $this->Ln();
        }
    }
}
