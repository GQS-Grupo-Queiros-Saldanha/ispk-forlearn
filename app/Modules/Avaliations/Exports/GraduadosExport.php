<?php

namespace App\Modules\Avaliations\Exports;

use DB;
use Carbon\Carbon;
use stdClass;
use App\Modules\Users\Enum\ParameterEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Unique;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithEvents
};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\{
    Alignment,
    Border,
    Fill
};
class GraduadosExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithEvents
{
    private $yearname;

    public function __construct($yearname)
    {
        $this->yearname = $yearname;
    }

    /* =========================================================
     * DADOS
     * ========================================================= */
    public function collection()
    {
            $languageId = 1;

            $ESTUDANTES = DB::table('new_old_grades as Percurso')
            ->leftJoin('users as user', 'user.id', '=', 'Percurso.user_id')
            ->leftJoin('user_parameters as full_name', function ($join) {
                $join->on('user.id', '=', 'full_name.users_id')
                    ->where('full_name.parameters_id', 1);
            })
            ->leftJoin('user_parameters as bi', function ($join) {
                    $join->on('user.id', '=', 'bi.users_id')
                        ->where('bi.parameters_id', ParameterEnum::BILHETE_DE_IDENTIDADE);
                })
            ->leftJoin('user_parameters as nascimento', function ($join) {
                $join->on('user.id', '=', 'nascimento.users_id')
                    ->where('nascimento.parameters_id', ParameterEnum::DATA_DE_NASCIMENTO);
            })
            ->leftJoin('user_parameters as contacto', function ($join) {
                $join->on('user.id', '=', 'contacto.users_id')
                    ->where('contacto.parameters_id', ParameterEnum::TELEMOVEL_PRINCIPAL);
            })
            ->leftJoin('user_parameters as province', function ($join) {
                $join->on('user.id', '=', 'province.users_id')
                    ->where('province.parameters_id', ParameterEnum::LISTA_DE_PROVINCIAS);
                })
                ->leftJoin('parameter_option_translations as province_value', function ($join) {
                    $join->on('province_value.parameter_options_id', '=', 'province.value');
                })
                ->leftJoin('user_parameters as municip', function ($join) {
                    $join->on('user.id', '=', 'municip.users_id')
                      ->whereIn('municip.parameters_id', [69,71,151,152,153,155,156,157,159,170,182,204,205,206,218,225,282,224]);
                  })

                ->leftJoin('parameter_option_translations as municip_value', function ($join) {
                    $join->on('municip_value.parameter_options_id', '=', 'municip.value');
                })

                /*
                * Buscar o curso dos estudantes
                */
                ->join('user_courses as uc','uc.users_id','user.id')
                ->join('courses as c','c.id','uc.courses_id')
                ->whereNull('c.deleted_at')->whereNull('c.deleted_by')
                /* BUscar tradução dos cursos */
                ->join('courses_translations as ctr','c.id','ctr.courses_id')
                ->where('ctr.active',1)
                /**
                 * Buscar Departamento do curso do estudante
                 */
                ->join('departments as dep','c.departments_id','dep.id')
                ->join('department_translations as dt','dt.departments_id','dep.id')
                ->where('dt.active',1)->whereNull('dep.deleted_at')->whereNull('dep.deleted_by')

                ->leftJoin('user_parameters as medio_school', function ($join) {
                    $join->on('user.id', '=', 'medio_school.users_id')
                      ->where('medio_school.parameters_id', ParameterEnum::NOME_DA_ESCOLA_DO_ENSINO_MEDIO);
                  })
                ->leftJoin('user_parameters as medio_course', function ($join) {
                    $join->on('user.id', '=', 'medio_course.users_id')
                        ->whereIn('medio_course.parameters_id', [140,141,143]);
                })
                ->leftJoin('parameter_option_translations as medio_course_value', function ($join) {
                    $join->on('medio_course_value.parameter_options_id', '=', 'medio_course.value');
                })
            /**
             * Buscar a turma dos estudantes na consulta join
             */
            ->leftjoin('user_classes as ucl','user.id', 'ucl.user_id')
            ->leftjoin('classes as turma','turma.id','ucl.class_id')

            ->leftJoin('user_parameters as up_meca', function ($join) {
                $join->on('user.id', '=', 'up_meca.users_id')
                    ->where('up_meca.parameters_id', 19);
            })
            ->leftJoin('user_parameters as sexo', function ($join) {
                $join->on('user.id', '=', 'sexo.users_id')
                    ->where('sexo.parameters_id', 2);
            })
            ->leftJoin('parameter_options as sexo_value', 'sexo_value.id', '=', 'sexo.value')
            ->leftJoin('disciplines as dc', 'dc.id', '=', 'Percurso.discipline_id')
            ->leftJoin('disciplines_translations as ct', function ($join) use ($languageId) {
                $join->on('ct.discipline_id', '=', 'Percurso.discipline_id')
                    ->where('ct.language_id', $languageId)
                    ->where('ct.active', true);
            })
            ->select([
                'user.id as id',
                'up_meca.value as matricula',
                'sexo_value.code as sexo',
                'nascimento.value as nascimento',
                'contacto.value as contacto',
                'full_name.value as nome_completo',
                'Percurso.grade as nota',
                'Percurso.lective_year as AnoLectivo',
                'province_value.display_name as province',
                'municip_value.display_name as municipio',
                'ctr.display_name as course',
                'dt.display_name as department',
                'bi.value as bi',
                'turma.code as turma',
                'c.duration_value as duration'
            ])
            ->where('Percurso.lective_year', $this->yearname)
            ->where('Percurso.grade', '>', 10)
            ->whereIn('ct.discipline_id', ParameterEnum::TRABALHO_FINAL_CURSO)
            ->distinct()
            ->get()
            ->unique('id');

            $rows = collect();
            $i = 1;

            $controller = new \App\Modules\Reports\Controllers\DeclarationController();
            $newsArrayStudents = [];
            foreach ($ESTUDANTES as $c) {
                // Calcula apenas uma vez
                $media = $controller->Mediafinal($c->id);
                $mediaCurso = $controller->mediaFinalCurso($c->id);
                //dd($media);

                //if($media['media_tfc'] != 0 && $media['media_final'] != 0){

                    // Idade
                    $idade = Carbon::now()->year - intval(substr($c->nascimento, 0, 4));

                    // Turno
                    $turno = substr($c->turma, -1);

                    $rows->push([
                        $i++,
                        $c->nome_completo,
                        $c->bi,
                        $c->sexo,
                        $idade,
                        $c->nascimento,
                        $c->province,
                        $c->municipio,
                        'Angola',
                        in_array($turno, ['M','T']) ? 'Regular' : 'Pós-Laboral',
                        $c->department,
                        $c->course,
                        $c->duration ? $c->duration . ' Anos' : null,
                        $c->AnoLectivo,
                        $c->matricula,
                        in_array($turno, ['M','T']) ? 'Não' : 'Sim',
                        'Licenciatura',
                        $mediaCurso >= 16 ? 'Sim' : 'Não',
                        $mediaCurso,
                        str_replace(' ', '', $c->contacto)
                    ]);
                //}
            }

        return $rows;
    }

    /* =========================================================
     * CABEÇALHOS
     * ========================================================= */
    public function headings(): array
    {
        return [
            [
                "REGISTO PRIMÁRIO  BASE DE DADOS DE GRADUADOS/ (RPG) DO ANO ACADÉMICO {$this->yearname}"
            ],
            [
                'Nº Ordem','Nome Completo','Bilhete de Identidade','Sexo','Idade','Data de Nascimento',
                'Província Residência','Município Residência','País de Origem','Período de Estudo',
                'Unidade Orgânica','Nome do Curso Inscrito no Ensino Superior',
                'Duração do Curso','Ano de Frequência','1ª Matrícula',
                'Trabalhador','Grau Académico','Quadro de Honra',
                'Média Final','Contacto',
            ]
        ];
    }

    /* =========================================================
     * Titulo da PLanilha Principal
     * ========================================================= */
    public function title(): string
    {
        return "Risto de Graduados {$this->yearname}";
    }

    /* =========================================================
     * ESTILOS
     * ========================================================= */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $lastColumn = 'T';

                // Título
                $event->sheet->getRowDimension('A')->setRowHeight(35);
                $event->sheet->mergeCells("A1:{$lastColumn}1");
                $event->sheet->getStyle("A1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 24],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);

                // Cabeçalhos
                $event->sheet->getStyle("A2:{$lastColumn}2")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '9FD5E8']
                    ],
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'wrapText' => true
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN
                        ]
                    ]
                ]);
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Aplica bordas em todas as células com dados
        $sheet->getStyle(
            'A2:' . $sheet->getHighestColumn() . $sheet->getHighestRow()
        )->getBorders()->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

        // RETORNO OBRIGATÓRIO
        return [
            // Coluna V alinhada à esquerda
            'V' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ],
        ];
    }

    /* =========================================================
     * LARGURA DAS COLUNAS
     * ========================================================= */
    public function columnWidths(): array
    {
        return [
            'A'=>15,'B'=>35,'C'=>18,'D'=>8,'E'=>6,'F'=>14,
            'G'=>16,'H'=>18,'I'=>10,'J'=>16,'K'=>22,'L'=>35,
            'M'=>12,'N'=>10,'O'=>16,'P'=>18,'Q'=>22,'R'=>22,
            'S'=>20,'T'=>18
        ];
    }

    /* =========================================================
     * Altura DAS COLUNAS
     * ========================================================= */
    public function rowHeights(): array
    {     return [
            1 => 30
        ];
    }
}
