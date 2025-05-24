<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Ucode 
{
	///define url inside qr code
	protected $url;
	
    function __construct()
    {
        require_once(APPPATH .'third_party/tcpdf/tcpdf_barcodes_2d.php');
		$this->url = "https://".$_SERVER['SERVER_NAME']."/my3k/scanqr/qr_stat/";
    }
	
	public function jana_qr($id_qr, $type='SVG', $warna = 'black')
    {
		// if($stat_saring == "H" ){$warna = "#6FF302";}elseif($stat_saring == "M" ){$warna = "#FF4343";}else{$warna = "black";}
		
		
		
		require_once(APPPATH .'third_party/tcpdf/tcpdf_barcodes_2d.php');
		// output the barcode as HTML object
		$barcodeobj = new TCPDF2DBarcode($this->url.$id_qr, 'QRCODE,H');
		
		// $qr = $barcodeobj->getBarcodePNG(5, 5, $warna);
		
		if ($type=='SVG') 
		{
			return $barcodeobj->getBarcodeSVGcode(5, 5, $warna);
		} 
		else 
		{
			$file_png = "tmp/barcode.png";
			file_put_contents($file_png, $barcodeobj->getBarcodePngData());
			return $file_png;
		}
		
	
		
	}
}

?>