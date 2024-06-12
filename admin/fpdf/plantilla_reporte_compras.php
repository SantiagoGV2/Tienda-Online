<?php

require 'fpdf.php';

class PDF extends FPDF
{
    private $fechaIni;
    private $fechaFin;

    public function __construct($orientation, $medidas, $size, $datos)
    {
        parent::__construct($orientation, $medidas, $size);
        $this->fechaIni = $datos['fechaIni'];
        $this->fechaFin = $datos['fechaFin'];
    }

    public function Header()
    {
        $this->Image('../img/logo.jpeg', 10, 5, 20);
        $this->SetFont('Arial', 'B', 11);

        $this->Cell(30);
        $y = $this->GetY();
        $this->MultiCell(130, 10, 'Reporte de compras', 0, 'C');

        $this->SetFont('Arial', '', 11);
        $this->Cell(30);
        $this->MultiCell(130, 10, 'Del ' . $this->fechaIni . ' al ' . $this->fechaFin, 0, 'C');

        $this->SetXY(160, $y);
        $this->Cell(40, 10, 'Fecha:' . date('d/m/Y'), 0, 1, 'L');

        $this->Ln(15);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(40, 6, 'Fecha', 1, 0);
        $this->Cell(40, 6, 'Estatus', 1, 0);
        $this->Cell(60, 6, 'Cliente', 1, 0);
        $this->Cell(40, 6, mb_convert_encoding('Dirección','ISO-8859-1','UTF-8'), 1, 0);
        $this->Cell(40, 6, 'Total', 1, 0);
        $this->Cell(40, 6, 'Medio de pago', 1, 1);
        $this->SetFont('Arial', '', 11);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 9);
        $this->Cell(0, 10, mb_convert_encoding('Página ','ISO-8859-1','UTF-8') . $this->PageNo() . '/{nb}', 0, 0,'C');
    }
}
