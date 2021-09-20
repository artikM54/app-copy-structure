<?php

require_once('config.php');
require_once('tcpdf/tcpdf.php');

$test = new CopyStructure( DIR, DIR_UPLOAD, TYPES);

class CopyStructure{

    public $dir;
    public $dir_upload;
    public $types;
    public $count = 0;


    function __construct( $dir, $dir_upload, $types){

        $this->dir = $dir;
        $this->dir_upload = $dir_upload;
        $this->types = $types;

        $this->detour_structure();

        echo "Скопирована структура $this->count файлов\n\n";

    }


    private function detour_structure(){

        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator( $this->dir));

        $it->rewind();

        while($it->valid()) {

            if (!$it->isDot()) {

                $f_type = pathinfo($it->getFilename())['extension'];
                $f_dirname = pathinfo($it->key())['dirname'];

                if(in_array( $f_type, $this->types)){
                    
                    $this->copy_structure( $f_dirname, $it->key(), $it->getFilename());

                }

            }

            $it->next();
        }
    }



    private function copy_structure( $dir, $file, $f_name){

        $path = $this->dir_upload  . $dir;
     
        mkdir($path, 0777, true);
    
        copy( $file, $path . '\\' . $f_name );
    
        $this->set_to_PDF( $path . '\\' . $f_name);
        
    }



    private function set_to_PDF( $f){

        $handle = fopen( $f, "r");
    
        if ($handle) {
    
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
            $pdf->SetFont('dejavusans', '', 6, '', true);
    
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
    
            $pdf->AddPage();
    
            $pos_y = $pdf->GetY() + 10;
    
            while (($line = fgets($handle)) !== false) {
    
                if($line != ''){
    
                    $pos_y = $pdf->GetY() + 3;
                    $pdf->SetXY(15, $pos_y);
                        
                    $pdf->Cell( 15, $pos_y, $line, 0, 0, 'L', 0);
    
                }
                
            }
    
        $pdf->Output( $f.'.pdf', 'F');
    
        unlink($f);
    
        $this->count++;
    
        fclose($handle);
    
        } 
    
    }
}