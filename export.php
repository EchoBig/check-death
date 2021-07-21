<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$person = unserialize($_GET['person']);


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// header
$spreadsheet->getActiveSheet()->setCellValue('A1', 'เลขประจำตัวประชาชน')
    ->setCellValue('B1', 'ชื่อ')
    ->setCellValue('C1', 'นามสกุล')
    ->setCellValue('D1', 'วันที่ตรวจสอบ');

// cell value
$spreadsheet->getActiveSheet()->fromArray($person, null, 'A2');

// style
$last_row = count($person) + 1;

$spreadsheet->getActiveSheet()->getStyle('A1:A'.$last_row)->getNumberFormat()
    ->setFormatCode('0000000000000');
$spreadsheet->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);

foreach(range('A','D') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}
ob_get_clean();
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="รายชื่อคนตาย_'.date('d-m-Y H:s').'.xlsx"');
$writer->save('php://output');
die;