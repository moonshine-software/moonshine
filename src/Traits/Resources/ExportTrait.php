<?php

namespace Leeto\MoonShine\Traits\Resources;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait ExportTrait
{
    public function exportRoute(): string
    {
        $query = ['exportCsv' => true];

        if(request()->has('filters')) {
            foreach (request()->query('filters') as $filterField => $filterQuery) {
                if(is_array($filterQuery)) {
                    foreach ($filterQuery as $filterInnerField => $filterValue) {
                        if(is_numeric($filterInnerField) && !is_array($filterValue)) {
                            $query['filters'][$filterField][] = $filterValue;
                        } else {
                            $query['filters'][$filterInnerField] = $filterValue;
                        }
                    }
                } else {
                    $query['filters'][$filterField] = $filterQuery;
                }
            }
        }

        if(request()->has('search')) {
            $query['search'] = request('search');
        }

        return $this->route('index', null, $query);
    }

    protected function exportCsv(): Response|Application|ResponseFactory
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $letter = 'A';

        foreach ($this->resource->exportFields() as $index => $field) {
            $sheet->setCellValue("{$letter}1", $field->label());

            $letter++;
        }

        $sheet->getStyle("A1:{$letter}1")->getFont()->setBold(true);


        $line = 2;
        foreach ($this->resource->all() as $item) {
            $letter = 'A';
            foreach ($this->resource->exportFields() as $index => $field) {
                $sheet->setCellValue($letter . $line, $field->exportViewValue($item));
                $letter++;
            }

            $line++;
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$this->resource->title.'.xlsx"');
        header('Cache-Control: max-age=0');

        return response($writer->save('php://output'));
    }
}