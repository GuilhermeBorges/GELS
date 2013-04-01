<?php
header("Content-type: text/html; charset=utf-8");
#require('src/FPDI-1.4.3/fpdi.php');
//incluindo o arquivo do fpdf
require_once("src/fpdf.php");

//defininfo a fonte !
define('FPDF_FONTPATH','src/font/');

class Certificado extends FPDF
{
    var $fundo = 'img/GELS123.jpg';
    var $fundo_tam = 30;
    var $logo_ufu = 'img/ufu.jpg';
    var $logo_ileel = 'img/ileel.jpg';
    var $logo_ppgel = 'img/ppgel.jpeg';
    var $titulo = 'Certificado';
    var $fonte_titulo = 'Times';
    var $fonte_texto = 'Arial';

        #Parser HTML
            function WriteHTML($html)
            {
                //HTML parser
                $html=str_replace("\n",' ',$html);
                $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
                foreach($a as $i=>$e)
                {
                    if($i%2==0)
                    {
                        //Text
                        if($this->HREF)
                            $this->PutLink($this->HREF,$e);
                        else
                            $this->Write(1,$e);
                    }
                    else
                    {
                        //Tag
                        if($e{0}=='/')
                            $this->CloseTag(strtoupper(substr($e,1)));
                        else
                        {
                            //Extract attributes
                            $a2=explode(' ',$e);
                            $tag=strtoupper(array_shift($a2));
                            $attr=array();
                            foreach($a2 as $v)
                                if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
                                    $attr[strtoupper($a3[1])]=$a3[2];
                            $this->OpenTag($tag,$attr);
                        }
                    }
                }
            }

            function OpenTag($tag,$attr)
            {
                //Opening tag
                if($tag=='B' or $tag=='I' or $tag=='U')
                    $this->SetStyle($tag,true);
                if($tag=='A')
                    $this->HREF=$attr['HREF'];
                if($tag=='BR')
                    $this->Ln(5);
            }

            function CloseTag($tag)
            {
                //Closing tag
                if($tag=='B' or $tag=='I' or $tag=='U')
                    $this->SetStyle($tag,false);
                if($tag=='A')
                    $this->HREF='';
            }

            function SetStyle($tag,$enable)
            {
                //Modify style and select corresponding font
                $this->$tag+=($enable ? 1 : -1);
                $style='';
                foreach(array('B','I','U') as $s)
                    if($this->$s>0)
                        $style.=$s;
                $this->SetFont('',$style);
            }

            function PutLink($URL,$txt)
            {
                //Put a hyperlink
                $this->SetTextColor(0,0,255);
                $this->SetStyle('U',true);
                $this->Write(1,$txt,$URL);
                $this->SetStyle('U',false);
                $this->SetTextColor(0);
            }

    function Cabecalho()
    {
        $this->AddPage();
        #-- Cabeçalho
            $this->Image($this->fundo,0,0,$this->fundo_tam);
            $this->Image($this->logo_ufu,2,1,3);
            $this->Image($this->logo_ileel,23,0.7,5);
            $this->Image($this->logo_ppgel,23.75,3.2,3.5);
            #$this->AddFont('Adinekir');
            # $this->SetFont('Adinekir','', 100); 
            $this->SetFont($this->fonte_titulo, 'B', 58); 
            $this->Text(10, 3, $this->titulo);
    }
    
    function Assinaturas_3($gels1,$gels2,$gels3)
    {
            $this->SetFont($this->fonte_texto,'B','8');                       
            #-- Primeira: Coordenadora do Gels
            $this->Image('img/ass1.jpeg',2.8,13,5);
            $this->Line(3,15,9,15); 
            $this->Line(3,15.03,9,15.03);            

            $this->SetXY(3.5,15.2);
            $this->MultiCell(5,0.3,utf8_decode($gels1),0,'C'); 
            
            #-- Segunda: Coordenador Gels
            $this->Image('img/ass2.jpeg',20.8,13,5);
            $this->Line(21,15,27,15);
            $this->Line(21,15.03,27,15.03);

            $this->SetXY(21.5,15.2);
            $this->MultiCell(5,0.3,utf8_decode($gels2),0,'C'); 

            #-- Terceiro: Coordenadora PPGEL
            $this->Image('img/ass3.jpeg',11.8,16,5);
            $this->Line(12,17.5,18,17.5);
            $this->Line(12,17.53,18,17.53);

            $this->SetXY(12.5,17.7);
            $this->MultiCell(5,0.3,utf8_decode($gels3),0,'C'); 
    }
    
    function Assinaturas_2($gels1,$gels2)
    {
            $this->SetFont($this->fonte_texto,'B','8');                       
            #-- Primeira: Coordenadora do Gels
            $this->Image('img/ass1.jpeg',2.8,13,5);
            $this->Line(3,15,9,15); 
            $this->Line(3,15.03,9,15.03);            

            $this->SetXY(3.5,15.2);
            $this->MultiCell(5,0.3,utf8_decode($gels1),0,'C'); 
            
            #-- Segunda: Coordenador Gels
            $this->Image('img/ass2.jpeg',20.8,13,5);
            $this->Line(21,15,27,15);
            $this->Line(21,15.03,27,15.03);

            $this->SetXY(21.5,15.2);
            $this->MultiCell(5,0.3,utf8_decode($gels2),0,'C'); 
    }

    function Modelo_Presidente_Comissao($nome, $evento,$data,$ch,$gels1,$gels2,$gels3)
        {
            $this->Cabecalho();

        #-- Texto
            $this->SetFont($this->fonte_texto,'', 18 );
            $this->SetXY(3,7);
            $this->MultiCell(23,1,$this->WriteHTML(utf8_decode('Certificamos que <b>'.$nome.'</b> presidiu e coordenou a comissão organizadora do <b>'.$evento.'</b>, realizado de '.$data.', perfazendo uma carga horária de '.$ch.' horas.')));
            
            /*
            $this->Write(1,utf8_decode('Certificamos que '));
            
            $this->SetFont($this->fonte_texto,'B', 19 );
            $this->Write(1,utf8_decode($nome));
            
            $this->SetFont($this->fonte_texto,'', 18 );  
            $this->Write(1,utf8_decode(' presidiu e coordenou a comissão organizadora do '));

            $this->SetFont($this->fonte_texto,'B', 19 );
            $this->Write(1,utf8_decode($evento));

            $this->SetFont($this->fonte_texto,'', 18 );  
            $this->Write(1,utf8_decode(', realizado de '.$data.', perfazendo uma carga horária de '.$ch.' horas.'));
            */
        #-- Adicionando os Locais para as assinaturas
            $this->Assinaturas_3($gels1,$gels2,$gels3);
        }

    function Modelo_Participante_Comissao($nome, $evento,$data,$ch,$gels1,$gels2,$gels3)
        {
            $this->Cabecalho();

        #-- Texto
            $this->SetFont($this->fonte_texto,'', 18 );
            $this->SetXY(3,7);
            $this->MultiCell(23,1,utf8_decode('Certificamos que '.$nome.' compôs a comissão organizadora '.$evento.', de '.$data.', perfazendo uma carga horária de '.$ch.' horas.'));
        #-- Adicionando os Locais para as assinaturas
            $this->Assinaturas_3($gels1,$gels2,$gels3);
        }
    
    function Modelo_Participante_Evento_Algo_a_mais($nome, $evento,$data,$algo,$ch,$gels1,$gels2,$gels3)
        {
            $this->Cabecalho();

        #-- Texto
            $this->SetFont($this->fonte_texto,'', 18 );
            $this->SetXY(3,7);
            $this->MultiCell(23,1,utf8_decode('Certificamos que '.$nome.' participou do '.$evento.', de '.$data.','.$algo.' perfazendo uma carga horária de '.$ch.' horas.'));
        #-- Adicionando os Locais para as assinaturas
            $this->Assinaturas_3($gels1,$gels2,$gels3);
        }

    function Modelo_Participante_Evento_Ouvinte($nome, $evento,$data,$ch,$gels1,$gels2,$gels3)
        {
            $this->Cabecalho();

        #-- Texto
            $this->SetFont($this->fonte_texto,'', 18 );
            $this->SetXY(3,7);
            $this->MultiCell(23,1,utf8_decode('Certificamos que '.$nome.' participou do '.$evento.' como ouvinte, de '.$data.', perfazendo uma carga horária de '.$ch.' horas.'));
        #-- Adicionando os Locais para as assinaturas
            $this->Assinaturas_3($gels1,$gels2,$gels3);
        }
    function Modelo_Participante_Evento_Monitor($nome, $evento,$data,$ch,$gels1,$gels2,$gels3)
        {
            $this->Cabecalho();

        #-- Texto
            $this->SetFont($this->fonte_texto,'', 18 );
            $this->SetXY(3,7);
            $this->MultiCell(23,1,utf8_decode('Certificamos que '.$nome.' participou do '.$evento.' como monitor, de '.$data.', perfazendo uma carga horária de '.$ch.' horas.'));
        #-- Adicionando os Locais para as assinaturas
            $this->Assinaturas_3($gels1,$gels2,$gels3);
        }



}

$gels1 = 'Profa. Dra. Carmen L. E. Augustini Coordenadora do Grupo de Pesquisa e Estudo em Linguagem e Subjetividade (GELS)';
$gels2 = 'Prof. Dr. Ernesto Sérgio Bertoldo Coordenador do Grupo de Pesquisa e Estudo em Linguagem e Subjetividade (GELS)';
$gels3 = 'Profa. Dra. Dilma Maria de Melo Coordenadora do Programa de Pós-Graduação em Estudos Linguísticos (PPGEL)';
$fulano = 'Fulano de TAL';
$evento = 'IV EVENTO TAL do Grupo de Pesquisa e Estudos em Linguagem e Subjetividade - GELS';
$data = '14 a 18 de dezembro de 2013';
$ch = '90';

$algo1 =  ' e coordenou os trabalhos da apresentação cultural de abertura do evento,';
$algo2 = ' e compôs a mesa-redonda "Representação: diferentes perspectivas, (im)possíveis intersecções", apresentando o trabalho intitulado O que nos torna humanos?,';
$algo3 = ' e apresentou o trabalho intitulado Essencialização da surdez e o discurso do status linguístico das línguas de sinais: uma possibilidade de desconstrução,' ;

$pdf = new Certificado("L","cm","A4");
$pdf->Modelo_Presidente_Comissao($fulano,$evento,$data,$ch,$gels1,$gels2,$gels3);
$pdf->Modelo_Participante_Comissao($fulano,$evento,$data,$ch,$gels1,$gels2,$gels3);
$pdf->Modelo_Participante_Evento_Algo_a_mais($fulano,$evento,$data,$algo1,$ch,$gels1,$gels2,$gels3);
$pdf->Modelo_Participante_Evento_Algo_a_mais($fulano,$evento,$data,$algo2,$ch,$gels1,$gels2,$gels3);
$pdf->Modelo_Participante_Evento_Algo_a_mais($fulano,$evento,$data,$algo3,$ch,$gels1,$gels2,$gels3);
$pdf->Modelo_Participante_Evento_Ouvinte($fulano,$evento,$data,$ch,$gels1,$gels2,$gels3);
$pdf->Modelo_Participante_Evento_Monitor($fulano,$evento,$data,$ch,$gels1,$gels2,$gels3);
$pdf->Output();
?>