<?php

namespace App\FaveoReport\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ManagementReportSheetExport implements  FromArray, ShouldAutoSize, WithEvents
{
    private $rows;

    public function __construct($rows)
    {
        $this->rows = $rows;
    }

    /**
     * @inheritDoc
     */
    public function array(): array
    {
        return $this->rows['reportRow'];
    }

    /**
     * @inheritDoc
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->getStyle('1')->getFont()->setSize(12)->setBold(true);

                $sheet->getStyle('1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                foreach ($this->rows['rowColumnIndex'] as $rowColumnIndex) {
                    $coordinate = $rowColumnIndex['coordinate'];

                    $sheet->getStyle($coordinate)->applyFromArray(['font'  => [ 'color' => ['rgb' => '0000FF'], 'underline' => 'single']]);

                    $sheet->getHyperlink($coordinate)->setUrl($rowColumnIndex['link']);

                    foreach ($rowColumnIndex['date'] as $coordinateDate) {
                        $sheet->getStyle($coordinateDate['coordinate'])
                            ->getNumberFormat()->setFormatCode($coordinateDate['date_in_excel']);
                    }
                }
            }
        ];
    }
}
