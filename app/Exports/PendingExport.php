<?php

namespace App\Exports;

use App\Helpers\LanguageHelper;
use App\Modules\Payments\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use App\Modules\Payments\Models\Article;
use App\Modules\Payments\Models\ArticleRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithProperties;
use Auth;


class PendingExport implements FromView, ShouldAutoSize,WithProperties
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public $data;
    
     public function __construct($data)
    {
        $this->data = $data;
        
    }
    public function view(): View
    {
            $this->properties();
            return view("Reports::excel.pending")->with($this->data);
            
    
    }
    
        public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:C' . (User::count() + 1))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
    }
    public function properties(): array
    {
        return [
            'creator'        => auth()->user()->name,
            'lastModifiedBy' => auth()->user()->name,
            'title'          => "Lista de pendentes",
            'description'    => 'Este documento foi gerado com o intuito de saber os estudantes com pendentes na plataforma forlearn.',
            'subject'        => 'Pendentes',
            'keywords'       => 'lista,pendentes,estudantes',
            'category'       => 'Pendentes',
            'manager'        => auth()->user()->name,
            'company'        => 'forLEARN by GQS',
        ];
    }
}
