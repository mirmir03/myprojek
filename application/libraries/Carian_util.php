<?php 
class Carian_util
{
	protected $res;
	
	function strafter($string, $substring, $kiri_kanan = 50)
	{
	  $pos = strpos($string, $substring);
	  if ($pos === false)
	   return $string;
	  else { 
	   $sub = (substr($string, $pos+strlen($substring))); 
	   $b4 = $this->strbefore($string,$substring);
	   $bil = substr_count($sub, $substring);
	   if ($bil > 0) $this->strafter($sub,$substring); 
	   $result = substr($b4, -$kiri_kanan)." ".$substring.substr($sub, 0, $kiri_kanan).".....   ";
	   $this->res .= $result;
	   // echo str_replace($substring, "<b>".$substring."</b>", $result);
	   return $result;
	  }	
	}

	function strbefore($string, $substring) {
	  $pos = strpos($string, $substring);
	  if ($pos === false)
	   return $string;
	  else
	   return(substr($string, 0, $pos));
	}
	
	function preview_result($content, $key, $limit="")
	{
		$this->strafter($content,$key);
		$result = str_replace($key, "<b>".$key."</b>", $this->res);	
		
		if ($limit != "")
			return substr($result, 0, $limit);
		
		return $result;
	}
}