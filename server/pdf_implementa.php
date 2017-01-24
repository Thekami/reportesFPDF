<?php
echo phpinfo();

require('pdf_estruc_gen.php');
//require('sesion_Class.php');
require('mysql.php');


// Cargar los datos
function LoadData()
{   
    $sql = new Mysql();
    
    $filtro = $_GET['x'];

    $consulta = "SELECT CONCAT(primer_ap,' ',segundo_ap,' ',nombres) nombre, 
                 direccion, telefono, IF(status = 1, 'Activo', 'Inactivo') status
                 FROM personas WHERE status = $filtro"; 
    $arrayNombres = $sql->query_assoc($consulta);   
    
    return $arrayNombres; 
 
    
} // FIN LoadData()
/////////////////////////////////////NOMBRE DE LOS REPORTES COMO VARIABLE DE SESION//////////////////////////

session_start();
//$_SESSION["reporte"] = null;
if(isset($_GET['x'])){ //si existe un envio get
    if($_GET['x'] == 1) // este se usa debido al error de escritura "pulicidad"
        $_SESSION["reporte"] = "ACTIVAS";
    else
        $_SESSION["reporte"] = "INACTIVAS"; //par todos excepto pulicidad y nomina
}

/******************* CREACION DE ENCABEZADOS  ************************************************************/


    $contador = array('contador' => 1);
    $header = array('No.','NOMBRE(S)', 'DIRECCIÓN', 'TELÉFONO','ESTATUS');
    $pdf=new pdf_estruct_gen('L','mm','A4');
    //$pdf=new pdf_estruct_gen();
    $pdf->SetMargins(35,20,0);
    $pdf->AddPage();
    $pdf->SetMargins(35,20,0);

    $pdf->SetFont('Arial','',12);
    $data =LoadData();

    $pdf->SetWidths(array(10,60,80,40,30));


    $pdf->SetAligns(array('C','C','C','C','C'));
    // Cabecera
    $pdf->encabezado($header);

    // Datos
    foreach($data as $row)
    {
        //$pdf->fila($contador,$header);
        $pdf->fila($contador,$row,$header);
        $contador['contador']++;

    }

    $pdf->Output();



?>