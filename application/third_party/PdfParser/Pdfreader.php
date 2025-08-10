<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'third_party/PdfParser/vendor/autoload.php');

use Smalot\PdfParser\Parser;

class Pdfreader {

    protected $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function extractText($filePath)
    {
        $pdf = $this->parser->parseFile($filePath);
        return $pdf->getText();
    }

    public function extractInfo($filePath)
    {
        $text = $this->extractText($filePath);

        // Sample regex to extract Invoice No, Date, and Customer
        $info = [];

        if (preg_match('/Invoice No:\s*(\S+)/', $text, $matches)) {
            $info['invoice'] = $matches[1];
        }

        if (preg_match('/Date:\s*([\d-]+)/', $text, $matches)) {
            $info['date'] = $matches[1];
        }

        if (preg_match('/Customer:\s*(.+)/', $text, $matches)) {
            $info['customer'] = trim($matches[1]);
        }

        return $info;
    }
}
