<?php

namespace App\Modules\GA\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\TimeHelper;
use App\Http\Controllers\Controller;
use App\Modules\Cms\Models\Language;
use Carbon\Carbon;
use DataTables;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Log;
use Throwable;
use Toastr;
use App\Modules\GA\Models\Lb_publisher;
use PDF;
use App\Model\Institution;
use Illuminate\Http\Request;


class LibraryController extends Controller
{


    // Página principal da da Biblioteca

    public function index()
    {
        try {

            $categorias = DB::table('lb_category as categoria')
                ->join('lb_book as livro', 'livro.id_category', '=', 'categoria.id')
                ->where('categoria.deleted_at', "=", null)
                ->select(["livro.id as id", "name", "description", "livro.title as titulo"])
                ->limit(7)
                ->get();

            $cat = collect($categorias)->groupBy("name")->map(function ($item, $key) {

                $categoria = [];

                $categoria[] = $item[0]->name;
                $categoria[] = $item[0]->description;
                $categoria[] = count($item);
                $categoria[] = $item[0]->titulo;

                return $categoria;
            });

            $categorias = DB::table('lb_category')
                ->where("deleted_at", "=", null)
                ->orderBy("name")
                ->count();


            $autores = DB::table('lb_author')
                ->where("deleted_at", "=", null)
                ->count();

            $editoras = DB::table('lb_publisher')
                ->where("deleted_at", "=", null)
                ->orderBy("name")
                ->count();

            $computadores = DB::table('lb_computer')
                ->where("deleted_at", "=", null)
                ->orderBy("name")
                ->count();

            $livros = DB::table('lb_book')
                ->where("deleted_at", "=", null)
                ->orderBy("title")
                ->count();

            $requisicao_livros = DB::table('lb_loan')
                ->where("deleted_at", "=", null)
                ->orderBy("code")
                ->count();

            $requisicao_computadores = DB::table('lb_loan_computer')
                ->orderBy("code")
                ->count();

            $requisicao = $requisicao_computadores + $requisicao_livros;

            return view('GA::library.index', [
                "categoria" => $cat,
                "categorias" => $categorias,
                "editoras" => $editoras,
                "autores" => $autores,
                "computadores" => $computadores,
                "livros" => $livros,
                "requisicao" => $requisicao
            ]);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }


    // Método para criar requisição de livros

    public function loan()
    {

        try {

            $books = DB::table('lb_book as livro')
                ->join('lb_author_book as autor_livro', 'livro.id', '=', 'autor_livro.book_id')
                ->join('lb_author as autor', 'autor_livro.author_id', '=', 'autor.id')
                ->join('lb_publisher as editora', 'livro.id_publisher', '=', 'editora.id')
                ->join('lb_category as categoria', 'livro.id_category', '=', 'categoria.id')
                ->where("livro.deleted_at", "=", null)
                ->where("livro.quantidade_restante", "!=", 0)
                ->orderBy('livro.id')
                ->distinct()
                ->select(
                    [

                        "livro.id as codigo",
                        "livro.title as titulo",
                        "livro.isbn as isbn",
                        "livro.year as ano",
                        "livro.edition as edicao",
                        "livro.language as idioma",
                        "livro.pages as paginas",
                        "livro.total as quantidade",
                        "livro.subtitle as subtitulo",
                        "autor.name as nome",
                        "autor.surname as sobrenome",
                        "categoria.name as categoria",
                        "editora.name as editora",
                        "livro.location as local"
                    ]
                )
                ->get();

            $usuarios = DB::table('users as leitor')
                ->join('user_parameters as parametros', 'leitor.id', '=', 'parametros.users_id')
                ->where("parametros.parameters_id", "=", 1)
                ->where("leitor.deleted_by", "=", null)
                ->where("parametros.value", "!=", "")
                ->where("parametros.deleted_by", "=", null)
                ->orderBy("parametros.value")
                ->select([

                    "leitor.id as leitor_codigo",
                    "parametros.value as leitor_nome",
                    "leitor.email as leitor_email"

                ])
                ->get();


            // Logica para listar os campos diferentes em um relacionamento muitos para muitos, onde os outros dados sao iguais

            $livros = collect($books)->groupBy("isbn")->map(function ($item, $key) {

                $total = count($item);

                $autores = [];

                for ($i = 0; $i < $total; $i++) {

                    $autores[] = $item[$i]->nome . " " . $item[$i]->sobrenome;
                }

                $nome = "";
                foreach ($autores as $key => $value) {

                    $nome = $value . "," . $nome;
                }

                $nomes = substr($nome, 0, -2);

                $dados[] = $item[0]->codigo;
                $dados[] = $item[0]->titulo;
                $dados[] = $item[0]->subtitulo;
                $dados[] = $item[0]->isbn;
                $dados[] = $nomes;
                $dados[] = $item[0]->editora;
                $dados[] = $item[0]->categoria;
                $dados[] = $item[0]->ano;
                $dados[] = $item[0]->edicao;
                $dados[] = $item[0]->idioma;
                $dados[] = $item[0]->paginas;
                $dados[] = $item[0]->quantidade;
                $dados[] = $item[0]->local;

                return $dados;
            });


            // Pegando as Instituição dos visitantes

            $instituicao_visitante = DB::table('lb_institution_visitor as instituicao')
                ->orderBy("instituicao.name")
                ->get();

            return view('GA::library.loan', [
                "livros" => $livros,
                "usuarios" => $usuarios,
                "instituicao_visitante" => $instituicao_visitante
            ]);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    // Método para exibir a página de requisição de computadores

    public function loan_computer()
    {

        try {

            // Pegando as Instituição dos visitantes



            $instituicao_visitante = DB::table('lb_institution_visitor as instituicao')
                ->orderBy("instituicao.name")
                ->get();

            $visitante = DB::table('lb_visitor as visitante')
                ->orderBy("visitante.name")
                ->get();


            $computadores = DB::table('lb_computer as computadores')
                ->where("computadores.deleted_by", "=", null)
                ->where("computadores.status_use", "=", "Disponível")
                ->where("computadores.status", "=", "Operacional")
                ->orderBy("computadores.name")
                ->get();

            $computadores_requisitados = DB::table('lb_computer as computadores')
                ->join('lb_loan_computer as requisicao_computador', 'computadores.id', '=', 'requisicao_computador.id_computer')
                ->join('user_parameters as parametros', 'requisicao_computador.id_user', '=', 'parametros.users_id')
                ->where("computadores.status", "=", "Operacional")
                ->where("computadores.deleted_by", "=", null)
                ->where("parametros.parameters_id", "=", 1)
                ->orderBy("computadores.name")
                ->orderBy("hora")
                ->select([
                    "requisicao_computador.id as codigo",
                    "computadores.name as nome_computador",
                    "computadores.brand as marca_computador",
                    "requisicao_computador.name_visitor as nome_visitante",
                    "requisicao_computador.status as estado_requisicao",
                    "computadores.status_use as estado_uso",
                    "requisicao_computador.date as data_requisicao",
                    "requisicao_computador.time as hora_requisicao",
                    "requisicao_computador.created_at as hora",
                    "requisicao_computador.time_final as hora_final",
                    "parametros.value as requerente"
                ])
                ->get();



            $estados = ["disponivel" => 0, "esgotado" => 0, "ocupado" => 0,];


            $computadores_estado = collect($computadores_requisitados)->groupBy("nome_computador")->map(function ($item, $key) {
                $quantida = count($item);
                return $item[$quantida - 1];
            });


            foreach ($computadores_estado as $item) {


                if ($item->estado_uso == "Disponível") {

                    $estados['disponivel'] = $estados['disponivel'] + 1;
                } else if ($item->estado_uso == "Ocupado") {

                    $estados['ocupado'] = $estados['ocupado'] + 1;
                }
            }

            // return $estados;

            // return "";

            $usuarios = DB::table('users as leitor')
                ->join('user_parameters as parametros', 'leitor.id', '=', 'parametros.users_id')
                ->where("parametros.parameters_id", "=", 1)
                ->where("leitor.deleted_by", "=", null)
                ->where("parametros.value", "!=", "")
                ->where("parametros.deleted_by", "=", null)
                ->orderBy("parametros.value")
                ->select([
                    "leitor.id as leitor_codigo",
                    "parametros.value as leitor_nome",
                    "leitor.email as leitor_email"
                ])
                ->get();




            return view('GA::library.computer-loan', [
                "computadores" => $computadores,
                "usuarios" => $usuarios,
                "estados" => $estados,
                "visitante" => $visitante,
                "instituicao_visitante" => $instituicao_visitante,
                "computadores_requisitados" => $computadores_estado
            ]);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    // Metodo global para a pesquisa de livros

    public function searchBooks()
    {

        try {

            /*
            
            Pesquisa os livros associando as categorias, editoras e autores 

            */


            $books = DB::table('lb_book as livro')
                ->leftjoin('users as u0', 'livro.created_by', '=', 'u0.id')
                ->leftjoin('users as u1', 'livro.updated_by', '=', 'u1.id')
                ->join('lb_author_book as autor_livro', 'livro.id', '=', 'autor_livro.book_id')
                ->join('lb_author as autor', 'autor_livro.author_id', '=', 'autor.id')
                ->join('lb_publisher as editora', 'livro.id_publisher', '=', 'editora.id')
                ->join('lb_category as categoria', 'livro.id_category', '=', 'categoria.id')
                ->where("livro.deleted_at", "=", null)
                ->orderBy('livro.id')
                ->distinct()
                ->select(
                    [

                        "livro.id as codigo",
                        "livro.title as titulo",
                        "livro.isbn as isbn",
                        "livro.year as ano",
                        "livro.edition as edicao",
                        "livro.language as idioma",
                        "livro.pages as paginas",
                        "livro.total as quantidade",
                        "livro.quantidade_restante as restante",
                        "livro.subtitle as subtitulo",
                        "autor.name as autor",
                        "autor.surname as sobrenome",
                        "categoria.name as categoria",
                        "editora.name as editora",
                        "livro.location as local",
                        "u0.name as created_by",
                        "livro.created_at",
                        "u1.name as updated_by",
                        "livro.updated_at"
                    ]
                )
                ->get();

            // Logica para listar os campos diferentes em um relacionamento muitos para muitos, onde os outros dados sao iguais

            $livros = collect($books)->groupBy("isbn")->map(function ($item, $key) {

                $total = count($item);

                $autores = [];

                for ($i = 0; $i < $total; $i++) {

                    $autores[] = $item[$i]->autor . " " . $item[$i]->sobrenome . " ";
                }

                $nome = "";
                foreach ($autores as $key => $value) {

                    $nome = $value . "," . $nome;
                }

                $nomes = substr($nome, 0, -2);



                $dados[] = $item[0]->titulo;
                $dados[] = $item[0]->subtitulo;
                $dados[] = $nomes;
                $dados[] = $item[0]->editora;
                $dados[] = $item[0]->categoria;
                $dados[] = $item[0]->isbn;
                $dados[] = $item[0]->ano;
                $dados[] = $item[0]->edicao;
                $dados[] = $item[0]->idioma;
                $dados[] = $item[0]->paginas;
                $dados[] = $item[0]->quantidade;
                $dados[] = $item[0]->codigo;
                $dados[] = $item[0]->local;
                $dados[] = $item[0]->restante;
                $dados[] = $item[0]->created_by;
                $dados[] = $item[0]->created_at;
                $dados[] = $item[0]->updated_by;
                $dados[] = $item[0]->updated_at;

                return $dados;
            });

            $categorias = DB::table('lb_category')
                ->leftjoin('users as u0', 'lb_category.created_by', '=', 'u0.id')
                ->leftjoin('users as u1', 'lb_category.updated_by', '=', 'u1.id')
                ->where("lb_category.deleted_at", "=", null)
                ->select([
                    "lb_category.id",
                    "lb_category.name",
                    "lb_category.description",
                    "u0.name as created_by",
                    "lb_category.created_at",
                    "u1.name as updated_by",
                    "lb_category.updated_at"
                ])
                ->orderBy("lb_category.name")
                ->get();

            $autores = DB::table('lb_author')
                ->leftjoin('users as u0', 'lb_author.created_by', '=', 'u0.id')
                ->leftjoin('users as u1', 'lb_author.updated_by', '=', 'u1.id')
                ->where("lb_author.deleted_at", "=", null)
                ->select([
                    "lb_author.id",
                    "lb_author.name",
                    "lb_author.genre",
                    "lb_author.surname",
                    "lb_author.country",
                    "lb_author.others_information",
                    "u0.name as created_by",
                    "lb_author.created_at",
                    "u1.name as updated_by",
                    "lb_author.updated_at"

                ])->orderBy("lb_author.name")
                ->get();

            $editoras = DB::table('lb_publisher')
                ->leftjoin('users as u0', 'lb_publisher.created_by', '=', 'u0.id')
                ->leftjoin('users as u1', 'lb_publisher.updated_by', '=', 'u1.id')
                ->where("lb_publisher.deleted_at", "=", null)
                ->select([
                    "lb_publisher.id",
                    "lb_publisher.name",
                    "lb_publisher.address",
                    "lb_publisher.city",
                    "lb_publisher.country",
                    "lb_publisher.email",
                    "u0.name as created_by",
                    "lb_publisher.created_at",
                    "u1.name as updated_by",
                    "lb_publisher.updated_at"

                ])
                ->orderBy("lb_publisher.name")
                ->get();

            $computadores = DB::table('lb_computer')
                ->leftjoin('users as u0', 'lb_computer.created_by', '=', 'u0.id')
                ->leftjoin('users as u1', 'lb_computer.updated_by', '=', 'u1.id')
                ->where("lb_computer.deleted_at", "=", null)
                ->select([
                    "lb_computer.id",
                    "lb_computer.name",
                    "lb_computer.brand",
                    "lb_computer.processor",
                    "lb_computer.ram",
                    "lb_computer.hd_ssd",
                    "lb_computer.status",
                    "lb_computer.status_use",
                    "u0.name as created_by",
                    "lb_computer.created_at",
                    "u1.name as updated_by",
                    "lb_computer.updated_at"

                ])
                ->orderBy("lb_computer.name")
                ->get();

            $requisicao = DB::table('lb_loan as requisicao')
                ->join('users as leitor', 'requisicao.id_user', '=', 'leitor.id')
                // ->join('lb_loan_book as requisicao_livro', 'requisicao.id', '=', 'requisicao_livro.id_loan')
                // ->join('lb_book as livro', 'livro.id', '=', 'requisicao_livro.id_book')
                ->where("requisicao.deleted_at", "=", null)
                ->select([
                    "requisicao.id as codigo",
                    "requisicao.date_request as data_inicio",
                    "requisicao.code as referencia",
                    "requisicao.date_devolution as data_fim",
                    "requisicao.status as estado",
                    "leitor.name as leitor_nome",
                    "leitor.email as leitor_email"
                    // "livro.title as livro_titulo",
                    // "livro.isbn as livro_isbn"
                ])
                ->orderBy("name")
                ->get();

            return view('GA::library.searchbooks', [
                "livros" => $livros,
                "categorias" => $categorias,
                "autores" => $autores,
                "editoras" => $editoras,
                "requisicaos" => $requisicao,
                "computadores" => $computadores

            ]);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    // Metodo global para a pesquisa de REQUISIÇOES

    public function searchLoan()
    {

        try {

            /*
            
            Pesquisa os livros associando as categorias, editoras e autores 

            */


            $books = DB::table('lb_book as livro')
                ->join('lb_author_book as autor_livro', 'livro.id', '=', 'autor_livro.book_id')
                ->join('lb_author as autor', 'autor_livro.author_id', '=', 'autor.id')
                ->join('lb_publisher as editora', 'livro.id_publisher', '=', 'editora.id')
                ->join('lb_category as categoria', 'livro.id_category', '=', 'categoria.id')
                ->where("livro.deleted_at", "=", null)
                ->orderBy('livro.id')
                ->distinct()
                ->select(
                    [

                        "livro.id as codigo",
                        "livro.title as titulo",
                        "livro.isbn as isbn",
                        "livro.year as ano",
                        "livro.edition as edicao",
                        "livro.language as idioma",
                        "livro.pages as paginas",
                        "livro.total as quantidade",
                        "livro.subtitle as subtitulo",
                        "autor.name as autor",
                        "categoria.name as categoria",
                        "editora.name as editora",
                        "livro.location as local"
                    ]
                )
                ->get();

            // Logica para listar os campos diferentes em um relacionamento muitos para muitos, onde os outros dados sao iguais

            $livros = collect($books)->groupBy("isbn")->map(function ($item, $key) {

                $total = count($item);

                $autores = [];

                for ($i = 0; $i < $total; $i++) {

                    $autores[] = $item[$i]->autor;
                }

                $nome = "";
                foreach ($autores as $key => $value) {

                    $nome = $value . "," . $nome;
                }

                $nomes = substr($nome, 0, -2);



                $dados[] = $item[0]->titulo;
                $dados[] = $item[0]->subtitulo;
                $dados[] = $nomes;
                $dados[] = $item[0]->editora;
                $dados[] = $item[0]->categoria;
                $dados[] = $item[0]->isbn;
                $dados[] = $item[0]->ano;
                $dados[] = $item[0]->edicao;
                $dados[] = $item[0]->idioma;
                $dados[] = $item[0]->paginas;
                $dados[] = $item[0]->quantidade;
                $dados[] = $item[0]->codigo;
                $dados[] = $item[0]->local;

                return $dados;
            });

            $requisicao_computador = DB::table('lb_computer as computadores')
                ->join('lb_loan_computer as requisicao_computador', 'computadores.id', '=', 'requisicao_computador.id_computer')
                ->join('users as leitor', 'requisicao_computador.id_user', '=', 'leitor.id')
                ->join('user_parameters as parametros', 'requisicao_computador.id_user', '=', 'parametros.users_id')
                ->where("computadores.deleted_by", "=", null)
                ->where("parametros.parameters_id", "=", 1)

                ->orderBy("computadores.name")
                ->orderBy("hora")
                ->select([

                    "requisicao_computador.code as referencia",
                    "computadores.name as nome_computador",
                    "computadores.brand as marca_computador",
                    "requisicao_computador.status as estado_requisicao",
                    "computadores.status_use as estado_uso",
                    "requisicao_computador.date as data_requisicao",
                    "requisicao_computador.time as hora_requisicao",
                    "requisicao_computador.created_at as hora",
                    "requisicao_computador.time_final as hora_final",
                    "parametros.value as requerente",
                    "leitor.email"

                ])
                ->get();

            $categorias = DB::table('lb_category')
                ->where("deleted_at", "=", null)
                ->select(["id", "name", "description"])
                ->orderBy("name")
                ->get();

            $autores = DB::table('lb_author')
                ->where("deleted_at", "=", null)
                ->select(["id", "name", "genre", "surname", "country", "others_information"])->orderBy("name")
                ->get();

            $editoras = DB::table('lb_publisher')
                ->where("deleted_at", "=", null)
                ->orderBy("name")
                ->get();

            $requisicao_livro = DB::table('lb_loan as requisicao')
                ->join('users as leitor', 'requisicao.id_user', '=', 'leitor.id')
                // ->join('lb_loan_book as requisicao_livro', 'requisicao.id', '=', 'requisicao_livro.id_loan')
                // ->join('lb_book as livro', 'livro.id', '=', 'requisicao_livro.id_book')
                ->where("requisicao.deleted_at", "=", null)
                ->select([
                    "requisicao.id as codigo",
                    "requisicao.date_request as data_inicio",
                    "requisicao.code as referencia",
                    "requisicao.date_devolution as data_fim",
                    "requisicao.status as estado",
                    "leitor.name as leitor_nome",
                    "leitor.email as leitor_email"
                    // "livro.title as livro_titulo",
                    // "livro.isbn as livro_isbn"
                ])
                ->orderBy("name")
                ->get();

            return view('GA::library.searchloan', [
                "livros" => $livros,
                "categorias" => $categorias,
                "autores" => $autores,
                "editoras" => $editoras,
                "requisicao_livro" => $requisicao_livro,
                "requisicao_computador" => $requisicao_computador
            ]);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }

    // Metodo global para a lixeira dos dados

    public function bin()
    {

        try {

            /*
            
            Pesquisa os livros associando as categorias, editoras e autores 

            */


            $books = DB::table('lb_book as livro')
                ->leftjoin('users as u0', 'livro.deleted_by', '=', 'u0.id')
                ->join('lb_author_book as autor_livro', 'livro.id', '=', 'autor_livro.book_id')
                ->join('lb_author as autor', 'autor_livro.author_id', '=', 'autor.id')
                ->join('lb_publisher as editora', 'livro.id_publisher', '=', 'editora.id')
                ->join('lb_category as categoria', 'livro.id_category', '=', 'categoria.id')
                ->where("livro.deleted_at", "!=", null)
                ->orderBy('livro.id')
                ->distinct()
                ->select(
                    [

                        "livro.id as codigo",
                        "livro.title as titulo",
                        "livro.isbn as isbn",
                        "livro.year as ano",
                        "livro.edition as edicao",
                        "livro.language as idioma",
                        "livro.pages as paginas",
                        "livro.total as quantidade",
                        "livro.subtitle as subtitulo",
                        "autor.name as autor",
                        "categoria.name as categoria",
                        "editora.name as editora",
                        "livro.location as local",
                        "u0.name as deleted_by",
                        "livro.deleted_at",
                    ]
                )
                ->get();



            // Logica para listar os campos diferentes em um relacionamento muitos para muitos, onde os outros dados sao iguais

            $livros = collect($books)->groupBy("isbn")->map(function ($item, $key) {

                $total = count($item);

                $autores = [];

                for ($i = 0; $i < $total; $i++) {

                    $autores[] = $item[$i]->autor;
                }

                $nome = "";
                foreach ($autores as $key => $value) {

                    $nome = $value . "," . $nome;
                }

                $nomes = substr($nome, 0, -2);

                $dados[] = $item[0]->titulo;
                $dados[] = $item[0]->subtitulo;
                $dados[] = $nomes;
                $dados[] = $item[0]->editora;
                $dados[] = $item[0]->categoria;
                $dados[] = $item[0]->isbn;
                $dados[] = $item[0]->ano;
                $dados[] = $item[0]->edicao;
                $dados[] = $item[0]->idioma;
                $dados[] = $item[0]->paginas;
                $dados[] = $item[0]->quantidade;
                $dados[] = $item[0]->codigo;
                $dados[] = $item[0]->local;
                $dados[] = $item[0]->deleted_by;
                $dados[] = $item[0]->deleted_at;
                return $dados;
            });

            $categorias = DB::table('lb_category')
                ->leftjoin('users as u0', 'lb_category.deleted_by', '=', 'u0.id')
                ->where("lb_category.deleted_at", "!=", null)
                ->select([
                    "lb_category.id",
                    "lb_category.name",
                    "lb_category.description",
                    "u0.name as created_by",
                    "lb_category.created_at",
                    "u0.name as deleted_by",
                    "lb_category.deleted_at",
                ])
                ->orderBy("lb_category.name")
                ->get();

            $autores = DB::table('lb_author')
                ->leftjoin('users as u0', 'lb_author.deleted_by', '=', 'u0.id')
                ->where("lb_author.deleted_at", "!=", null)
                ->select([
                    "lb_author.id",
                    "lb_author.name",
                    "lb_author.genre",
                    "lb_author.surname",
                    "lb_author.country",
                    "lb_author.others_information",
                    "u0.name as deleted_by",
                    "lb_author.deleted_at"


                ])->orderBy("lb_author.name")
                ->get();

            $editoras = DB::table('lb_publisher')
                ->leftjoin('users as u0', 'lb_publisher.deleted_by', '=', 'u0.id')
                ->where("lb_publisher.deleted_at", "!=", null)
                ->select([
                    "lb_publisher.id",
                    "lb_publisher.name",
                    "lb_publisher.address",
                    "lb_publisher.city",
                    "lb_publisher.country",
                    "lb_publisher.email",
                    "u0.name as deleted_by",
                    "lb_publisher.deleted_at"

                ])
                ->orderBy("lb_publisher.name")
                ->get();




            return view('GA::library.bin', [
                "livros" => $livros,
                "categorias" => $categorias,
                "autores" => $autores,
                "editoras" => $editoras
            ]);
        } catch (Exception | Throwable $e) {
            Log::error($e);
            return Request::ajax() ? response()->json($e->getMessage(), 500) : abort(500);
        }
    }



    // Criar qualquer item na biblbioteca

    public function create_item($array)
    {


        // Pega os dados provenientes do formúlário e os converte em arrays

        $dados = explode(',', $array);

        // Pega o código da acção

        $id_action = $dados[0];

        // ID do usuário logado

        $id = auth()->user()->id;



        // Criar um autor


        switch ($id_action) {

            case 'autor':

                return $this->new_author($dados, $id);

                break;


            case 'editora':

                return $this->new_publisher($dados, $id);

                break;


            case 'categoria':

                return $this->new_category($dados, $id);

                break;


            case 'livro':

                return $this->new_book($dados, $id);

                break;


            case 'computador':

                return $this->new_computer($dados, $id);

                break;

            case 'instituicao':

                return $this->new_instition($dados);

                break;

            case 'visitante':

                return $this->new_visitor($dados, $id);

                break;


            case 'requisitar':

                return $this->new_loan($dados, $id);

                break;

            case 'requisitar-computador':

                return $this->new_loan_computer($dados, $id);

                break;

            default:
                # code...
                break;
        }
    }


    // Novo item na biblioteca

    public function new($type)
    {

        switch ($type) {


            case 'livro':

                $autores = DB::table('lb_author')
                    ->where("deleted_at", "=", null)
                    ->select(["id", "name", "surname"])->orderBy("name")
                    ->get();

                $categorias = DB::table('lb_category')
                    ->where("deleted_at", "=", null)
                    ->select(["id", "name"])->orderBy("name")
                    ->get();

                $editoras = DB::table('lb_publisher')
                    ->where("deleted_at", "=", null)
                    ->select(["id", "name"])->orderBy("name")
                    ->get();

                return view(
                    'GA::library.create',
                    [
                        "autores" => $autores,
                        "categorias" => $categorias,
                        "editoras" => $editoras

                    ]
                );

                break;

            default:
                return view('GA::library.item.item', ['type' => $type]);
                break;
        }
    }

    // Metodo para exibir o formulario de cadastro dos livros

    public function library_create()
    {
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {


        switch ($request->get("action")) {

            case 'autor':

                
                return $this->new_author($request);

                break;


            case 'editora':

                
                return $this->new_publisher($request);

                break;


            case 'area':

                return $this->new_area($request);

                break;


            case 'livro':

                return $this->new_book($request);


                break;


            case 'computador':

                return $this->new_computer($request);

                break;
           
            default:

                break;
        }
    }

    // Registrar um novo autor 

    public function new_author($autor)
    {
        

        // Verificando a existência do autor

        if (DB::table('lb_author')
            ->where('others_information', "=", $autor->get("codigo")
            )
            ->exists()
        ) {
                Toastr::error(__('Já existe um Autor com este " Código "'), __('toastr.error'));
                return redirect()->back();
        } else {

            $cadastro = DB::table('lb_author')->insertGetId(
                [
                    "name" =>  $autor->get("nome"),
                    "surname" => $autor->get("sobrenome"),
                    "genre" => $autor->get("sexo"),
                    "country" => $autor->get("pais"),
                    "others_information" => $autor->get("codigo"),
                    "created_by" => auth()->user()->id
                ]
            );


             // Success message
             Toastr::success(__('Autor criado com sucesso'), __('toastr.success'));
             return redirect()->route('library-searchBooks');
        }
    }

    // Registrar um livro novo

    public function new_book($livro)
    {


        // return $livro->get("titulo");

        if (DB::table('lb_book')->where('isbn', "=", $livro->get("isbn"))->exists()) {

            $livro = DB::table('lb_book')
                ->where('isbn', "=", $livro->get("isbn"))
                ->select(["id", "title"])
                ->first();

            // Erro de livro existente

            Toastr::error(__('O " ISBN " já foi usado em outro livro'), __('toastr.error'));
            return redirect()->back();

            return response()->json(["Livro existente"]);
        } else {

            $id_novo = DB::table('lb_book')->insertGetId(
                [
                    "title" =>  $livro->get("titulo"),
                    "subtitle" => $livro->get("subtitulo"),
                    "id_publisher" => $livro->get("editora"),
                    "id_category" => $livro->get("area"),
                    "isbn" => $livro->get("isbn"),
                    "year" => $livro->get("ano"),
                    "edition" => $livro->get("edicao"),
                    "location" => $livro->get("local"),
                    "pages" => $livro->get("pagina"),
                    "total" => $livro->get("quantidade"),
                    "quantidade_restante" => $livro->get("quantidade"),
                    "language" => $livro->get("idioma"),
                    "created_by" => auth()->user()->id
                ]
            );

            // Adicionando os dados na tabela autor_livro que com relacionamento do tipo muitos para muitos

            $autores = $livro->get("autor");


            foreach ($autores as $key => $value) {

                DB::table('lb_author_book')->insert(
                    [
                        "author_id" => $value,
                        "book_id" => $id_novo
                    ]
                );
            }

            // Success message
            Toastr::success(__('Livro criado com sucesso'), __('toastr.success'));
            return redirect()->route('library-searchBooks');
        }
    }

    // Registrar uma area nova

    public function new_area($area)
    {

       

        // Verificando a existência do autor

        if (DB::table('lb_category')
            ->where('name', "=", $area->get("nome"))
            ->where('description', "=", $area->get("cdu"))
            ->exists()
        ) {


            Toastr::error(__('Já existe uma Área com este " CDU ou Nome  "'), __('toastr.error'));
            return redirect()->back();
        } else {

            $cadastro = DB::table('lb_category')->insertGetId(
                [
                    "name" =>  $area->get("nome"),
                    "description" => $area->get("cdu"),
                    "created_by" => auth()->user()->id

                ]
            );
            // Success message
            Toastr::success(__('Área criada com sucesso'), __('toastr.success'));
            return redirect()->route('library-searchBooks');
        }
    }
    // Registrar uma instituicao nova dos visitantes

    public function new_instition($dados)
    {

        // Verificando a existência da instituiç..

        if (DB::table('lb_institution_visitor')
            ->where('name', "=", $dados[1])
            ->exists()
        ) {

            $instituicao = DB::table('lb_institution_visitor')
                ->where('name', "=", $dados[1])
                ->select(["id", "name"])
                ->first();

            return response()->json(["Instituicao existente," . $instituicao->id . "," . $instituicao->name]);
        } else {

            $cadastro = DB::table('lb_institution_visitor')->insertGetId(
                [
                    "name" =>  $dados[1]
                ]
            );
            return response()->json(["sucesso," . $cadastro . "," . $dados[1]]);
        }
    }

    // Registrar uma Editora nova

    public function new_publisher($editora)
    {
        // Verificando a existência da editora
      
        if (DB::table('lb_publisher')
            ->where('name', "=", $editora->get("nome"))
            ->where('address', "=", $editora->get("endereco"))
            ->where('country', "=", $editora->get("pais"))
            ->exists()
        ) {

            Toastr::error(__('Já existe está Editora'), __('toastr.error'));
            return redirect()->back();
        } else {

            $cadastro = DB::table('lb_publisher')->insertGetId(
                [
                    "name" =>  $editora->get("nome"),
                    "email" => $editora->get("email"),
                    "address" => $editora->get("endereco"),
                    "city" => $editora->get("cidade"),
                    "country" => $editora->get("pais"),
                    "created_by" => auth()->user()->id

                ]
            );


          
            // Success message
            Toastr::success(__('Editora criada com sucesso'), __('toastr.success'));
            return redirect()->route('library-searchBooks');
        }
    }

    // Registrar um Computador novo

    public function new_computer($computador)
    {
        // Verificando a existência do computador

        if (DB::table('lb_computer')
            ->where('name', "=", $computador->get("nome"))
            ->exists()
        ) {

           
                Toastr::error(__('Este Computador já está cadastrado'), __('toastr.error'));
                return redirect()->back();
        } else {

            $cadastro = DB::table('lb_computer')->insertGetId(
                [
                    "name" => $computador->get("nome"),
                    "brand" => $computador->get("marca"),
                    "processor" => $computador->get("processador"),
                    "ram" => $computador->get("ram")." ".$computador->get("ramUnidade"),
                    "hd_ssd" => $computador->get("hd")." ".$computador->get("hdUnidade"),
                    "status" => "Operacional",
                    "created_by" => auth()->user()->id

                ]
            );

           // Success message
           Toastr::success(__('Computador criado com sucesso'), __('toastr.success'));
           return redirect()->route('library-searchBooks');
        }
    }


    // Registrar um Visitante novo

    public function new_visitor($dados, $id)
    {
        // Verificando a existência do Visitante

        if (DB::table('lb_visitor')
            ->where('bi', "=", $dados[2])
            ->exists()
        ) {

            $visitante = DB::table('lb_visitor')
                ->where('name', "=", $dados[1])
                ->select(["id", "name"])
                ->first();

            return response()->json(["Visitante existente," . $visitante->id . "," . $visitante->name]);
        } else {

            $cadastro = DB::table('lb_visitor')->insertGetId(
                [
                    "name" =>  $dados[1],
                    "bi" => $dados[2],
                    "phone" => $dados[3],
                    "genre" => $dados[4],
                    "id_institution" => $dados[5],
                    "created_by" => $id

                ]
            );

            // Criar o codigo de visitante

            $alterar = DB::table('lb_visitor')
                ->where('id', "=", $cadastro)
                ->update(
                    [
                        "code" =>  "V" . date("Y") . $cadastro

                    ]
                );

            return response()->json(["sucesso," . $cadastro . "," . $dados[1]]);
        }
    }

    // Requisitar livros 

    public function new_loan($dados, $id)
    {


        if ($dados[3] == "visitante") {

            $requisicao = DB::table('lb_loan')->insertGetId(
                [
                    "id_user" => 8793,
                    "date_request" => date('Y-m-d'),
                    "date_devolution" => date('Y-m-d'),
                    "name_visitor" => $dados[4],
                    "phone_visitor" => $dados[5],
                    "visitor_institution" => $dados[6],
                    "status" => "Em curso",
                    "created_by" => $id

                ]
            );
        } else {

            $requisicao = DB::table('lb_loan')->insertGetId(
                [
                    "id_user" =>  $dados[1],
                    "date_request" => $dados[3],
                    "date_devolution" => $dados[4],
                    "status" => "Em curso",
                    "created_by" => $id

                ]
            );
        }



        $alterar = DB::table('lb_loan')
            ->where('id', "=", $requisicao)
            ->update(
                [
                    "code" => "R" . date('Y') . "" . $requisicao
                ]
            );

        $livros = explode('-', $dados[2]);


        foreach ($livros as $key => $value) {


            // Pegar o livro que sempre alterar a quantidade restante

            $quantidade = DB::table('lb_book as livro')
                ->where("livro.id", "=", $value)
                ->select(
                    [
                        "livro.quantidade_restante as restante"
                    ]
                )
                ->get();

            // Alterar a quantidade restante


            $quantidade = $quantidade[0]->restante - 1;


            $restante =  DB::table('lb_book')
                ->where('id', "=", $value)
                ->update(["quantidade_restante" => $quantidade]);


            DB::table('lb_loan_book')->insert(
                [
                    "id_book" => $value,
                    "id_loan" => $requisicao
                ]
            );
        }

        return response()->json(["sucesso, ," . $dados[1]]);
    }

    // Requisitar Computador 

    public function new_loan_computer($dados, $id)
    {

        // Comparando o tempo de requisiçao
        $hora = date('H:i:s');
        $hora_fim = "";

        switch ($dados[3]) {

            case '1h':

                $hora_fim = date('H:i:s', strtotime('+60 minute', strtotime($hora)));

            case '1h30':

                $hora_fim = date('H:i:s', strtotime('+90 minute', strtotime($hora)));

                break;

            case '2h':

                $hora_fim = date('H:i:s', strtotime('+120 minute', strtotime($hora)));

                break;

            case '2h30':

                $hora_fim = date('H:i:s', strtotime('+150 minute', strtotime($hora)));

                break;

            case '3h':

                $hora_fim = date('H:i:s', strtotime('+180 minute', strtotime($hora)));

                break;
        }

        $alterar = DB::table('lb_computer')
            ->where('id', "=", $dados[2])
            ->update(
                [
                    "status_use" => "Ocupado"
                ]
            );

        if ($dados[4] == "visitante") {

            $requisicao = DB::table('lb_loan_computer')->insertGetId(
                [
                    "id_user" =>  8793,
                    "id_computer" => $dados[2],
                    "name_visitor" => $dados[5],
                    "phone_visitor" => $dados[6],
                    "visitor_institution" => $dados[7],
                    "date" => date("Y-m-d"),
                    "time" => date('H:i:s'),
                    "time_final" => $hora_fim,
                    "status" => "Em curso",
                    "created_by" => $id

                ]
            );
        } else {

            $requisicao = DB::table('lb_loan_computer')->insertGetId(
                [
                    "id_user" =>  $dados[1],
                    "id_computer" => $dados[2],
                    "date" => date("Y-m-d"),
                    "time" => date('H:i:s'),
                    "time_final" => $hora_fim,
                    "status" => "Em curso",
                    "created_by" => $id

                ]
            );
        }


        // Gerando um codigo para a requisicao

        $codigo = DB::table('lb_loan_computer')
            ->where('id', "=", $requisicao)
            ->update(
                [
                    "code" => "RC" . date('Y') . $requisicao
                ]
            );


        return response()->json(["sucesso, ," . $dados[1]]);
    }

    // =========================================== Todos os relatorios e PDF ===============================


    // Criar PDF individual para requisição de livros

    public function library_create_pdf($id)
    {

        $books = DB::table('lb_book as livro')
            ->join('lb_author_book as autor_livro', 'livro.id', '=', 'autor_livro.book_id')
            ->join('lb_loan_book as requisicao_livro', 'livro.id', '=', 'requisicao_livro.id_book')
            ->join('lb_loan as requisicao', 'requisicao_livro.id_loan', '=', 'requisicao.id')
            ->join('lb_author as autor', 'autor_livro.author_id', '=', 'autor.id')
            ->join('lb_publisher as editora', 'livro.id_publisher', '=', 'editora.id')
            ->join('lb_category as categoria', 'livro.id_category', '=', 'categoria.id')
            ->where("livro.deleted_at", "=", null)
            ->where("requisicao_livro.id_loan", "=", $id)
            ->orderBy('livro.title')
            ->distinct()
            ->select(
                [
                    "livro.id as codigo",
                    "livro.title as titulo",
                    "livro.isbn as isbn",
                    "livro.year as ano",
                    "livro.edition as edicao",
                    "livro.language as idioma",
                    "livro.pages as paginas",
                    "livro.total as quantidade",
                    "livro.subtitle as subtitulo",
                    "autor.name as nome",
                    "autor.surname as sobrenome",
                    "categoria.name as categoria",
                    "editora.name as editora",
                    "livro.location as local"
                ]
            )
            ->get();

        // $usuarios = DB::table('users as leitor')
        // ->join('user_parameters as parametros', 'leitor.id', '=', 'parametros.users_id')
        // ->where("parametros.parameters_id", "=", 1)
        // ->where("leitor.deleted_by", "=", null)
        // ->where("parametros.value", "!=", "")
        // ->where("parametros.deleted_by", "=", null)
        // ->orderBy("parametros.value")
        // ->select([

        //     "leitor.id as leitor_codigo",
        //     "parametros.value as leitor_nome",
        //     "leitor.email as leitor_email"

        // ])
        // ->get();


        $leitor = DB::table('lb_loan as requisicao')
            ->where("requisicao.id", "=", $id)
            ->select([

                "requisicao.id_user as leitor_codigo",

            ])
            ->get();


        // Logica para listar os campos diferentes em um relacionamento muitos para muitos, onde os outros dados sao iguais

        $livros = collect($books)->groupBy("isbn")->map(function ($item, $key) {

            $total = count($item);

            $autores = [];

            for ($i = 0; $i < $total; $i++) {

                $autores[] = $item[$i]->nome . " " . $item[$i]->sobrenome;
            }

            $nome = "";
            foreach ($autores as $key => $value) {

                $nome = $value . "," . $nome;
            }

            $nomes = substr($nome, 0, -2);

            $dados[] = $item[0]->codigo;
            $dados[] = $item[0]->titulo;
            $dados[] = $item[0]->subtitulo;
            $dados[] = $item[0]->isbn;
            $dados[] = $nomes;
            $dados[] = $item[0]->editora;
            $dados[] = $item[0]->categoria;
            $dados[] = $item[0]->ano;
            $dados[] = $item[0]->edicao;
            $dados[] = $item[0]->idioma;
            $dados[] = $item[0]->paginas;
            $dados[] = $item[0]->quantidade;
            $dados[] = $item[0]->local;


            return $dados;
        });



        $requisicao = DB::table('lb_loan as requisicao')
            ->join('users as leitor', 'requisicao.id_user', '=', 'leitor.id')
            ->join('users as bibliotecario', 'requisicao.created_by', '=', 'bibliotecario.id')
            ->join('user_parameters as parametros', 'leitor.id', '=', 'parametros.users_id')
            ->where("parametros.parameters_id", "=", 1)
            ->where("requisicao.deleted_at", "=", null)
            ->where("requisicao.id", "=", $id)
            ->select([
                "requisicao.id as codigo",
                "requisicao.code as referencia",
                "requisicao.name_visitor as nome_visitante",
                "requisicao.phone_visitor as telefone_visitante",
                "requisicao.visitor_institution as instituicao_visitante",
                "requisicao.date_request as data_inicio",
                "requisicao.date_devolution as data_fim",
                "requisicao.status as estado",
                "parametros.value as leitor_nome",
                "leitor.email",
                "leitor.image as fotografia",
                "bibliotecario.name as bibliotecario"

            ])
            ->get();

        $id_bibliotecario = auth()->user()->id;

        $bibliotecario = DB::table('users as bibliotecario')
            ->join('user_parameters as parametros', 'bibliotecario.id', '=', 'parametros.users_id')
            ->where("parametros.parameters_id", "=", 1)
            ->where("bibliotecario.id", "=", $id_bibliotecario)
            ->select(["parametros.value as nome_bibliotecario"])
            ->get();

        $telefone = DB::table('user_parameters as parametros')
            ->where("parametros.parameters_id", "=", 36)
            ->where("parametros.users_id", "=", $leitor[0]->leitor_codigo)
            ->select(["parametros.value as telefone_leitor"])
            ->get();

        $telefone2 = DB::table('user_parameters as parametros')
            ->where("parametros.parameters_id", "=", 37)
            ->where("parametros.users_id", "=", $leitor[0]->leitor_codigo)
            ->select(["parametros.value as telefone_leitor"])
            ->get();

        $bilhete = DB::table('user_parameters as parametros')
            ->where("parametros.parameters_id", "=", 14)
            ->where("parametros.users_id", "=", $leitor[0]->leitor_codigo)
            ->select(["parametros.value as bi_leitor"])
            ->get();

        //dados da instituição  

        $institution = Institution::latest()->first();
        $titulo_documento = "REQUISIÇÃO DE LIVRO";
        $documentoGerado_documento = "Documento gerado a ";
        $documentoCode_documento = 1;

        //instaciando o PDF

        $pdf = PDF::loadView("GA::library.pdf.requisicao_pdf", compact(
            'livros',
            'telefone',
            'telefone2',
            'bilhete',
            'bibliotecario',
            'requisicao',
            'institution',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento'
        ));
        //Configuração
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        //Nome do documento PDF  

        $pdf_name = "RECIBO Nº" . $requisicao[0]->referencia;

        //Rodapé do PDF

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf->setOption('footer-html', $footer_html);

        //Retornar o PDF 

        return $pdf->stream($pdf_name . '.pdf');


        // return view('GA::library.pdf.requisicao_pdf',[
        //     "requisicao"=>$requisicao
        // ]); 

    }

    // Criar PDF individual para requisição de computadores

    public function library_computer_pdf($id)
    {

        $leitor = DB::table('lb_loan_computer as requisicao')
            ->where("requisicao.id", "=", $id)
            ->select([
                "requisicao.id_user as leitor_codigo",
            ])
            ->get();

        $requisicao = DB::table('lb_computer as computadores')
            ->join('lb_loan_computer as requisicao_computador', 'computadores.id', '=', 'requisicao_computador.id_computer')
            ->join('users as leitor', 'requisicao_computador.id_user', '=', 'leitor.id')
            ->join('users as bibliotecario', 'requisicao_computador.created_by', '=', 'bibliotecario.id')
            ->join('user_parameters as parametros', 'requisicao_computador.id_user', '=', 'parametros.users_id')
            ->where("computadores.status", "=", "Operacional")
            ->where("computadores.deleted_by", "=", null)
            ->where("parametros.parameters_id", "=", 1)
            ->where("requisicao_computador.id", "=", $id)
            ->orderBy("computadores.name")
            ->orderBy("hora")
            ->select([

                "requisicao_computador.code as referencia",
                "requisicao_computador.name_visitor as nome_visitante",
                "requisicao_computador.phone_visitor as telefone_visitante",
                "requisicao_computador.visitor_institution as instituicao_visitante",
                "computadores.name as nome_computador",
                "computadores.brand as marca_computador",
                "requisicao_computador.status as estado_requisicao",
                "computadores.status_use as estado_uso",
                "requisicao_computador.date as data_requisicao",
                "requisicao_computador.time as hora_requisicao",
                "requisicao_computador.created_at as hora",
                "requisicao_computador.time_final as hora_final",
                "parametros.value as requerente",
                "leitor.email",
                "bibliotecario.name as bibliotecario"

            ])
            ->get();



        $id_bibliotecario = auth()->user()->id;

        $bibliotecario = DB::table('users as bibliotecario')
            ->join('user_parameters as parametros', 'bibliotecario.id', '=', 'parametros.users_id')
            ->where("parametros.parameters_id", "=", 1)
            ->where("bibliotecario.id", "=", $id_bibliotecario)
            ->select(["parametros.value as nome_bibliotecario"])
            ->get();

        $telefone = DB::table('user_parameters as parametros')
            ->where("parametros.parameters_id", "=", 36)
            ->where("parametros.users_id", "=", $leitor[0]->leitor_codigo)
            ->select(["parametros.value as telefone_leitor"])
            ->get();

        $telefone2 = DB::table('user_parameters as parametros')
            ->where("parametros.parameters_id", "=", 37)
            ->where("parametros.users_id", "=", $leitor[0]->leitor_codigo)
            ->select(["parametros.value as telefone_leitor"])
            ->get();

        $bilhete = DB::table('user_parameters as parametros')
            ->where("parametros.parameters_id", "=", 14)
            ->where("parametros.users_id", "=", $leitor[0]->leitor_codigo)
            ->select(["parametros.value as bi_leitor"])
            ->get();

        //dados da instituição  

        $institution = Institution::latest()->first();
        $titulo_documento = "REQUISIÇÃO DE COMPUTADOR";
        $documentoGerado_documento = "Documento gerado a ";
        $documentoCode_documento = 1;

        //instaciando o PDF

        $pdf = PDF::loadView("GA::library.pdf.requisicao_computer_pdf", compact(
            'telefone',
            'telefone2',
            'bilhete',
            'bibliotecario',
            'requisicao',
            'institution',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento'
        ));
        //Configuração
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        //Nome do documento PDF  

        $pdf_name = "RECIBO Nº" . $requisicao[0]->referencia;

        //Rodapé do PDF

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf->setOption('footer-html', $footer_html);

        //Retornar o PDF 

        return $pdf->stream($pdf_name . '.pdf');


        // return view('GA::library.pdf.requisicao_pdf',[
        //     "requisicao"=>$requisicao
        // ]); 

    }

    // Criar PDF para relatórios de livros requisitados

    public function library_reports_pdf($inicio, $fim, $estado)
    {

        $data_inicio = $inicio;
        $data_fim = $fim;

        $estado = $estado;



        // $books = DB::table('lb_book as livro')
        //     ->join('lb_author_book as autor_livro', 'livro.id', '=', 'autor_livro.book_id')
        //     ->join('lb_loan_book as requisicao_livro', 'livro.id', '=', 'requisicao_livro.id_book')
        //     ->join('lb_loan as requisicao', 'requisicao_livro.id_loan', '=', 'requisicao.id')
        //     ->join('lb_author as autor', 'autor_livro.author_id', '=', 'autor.id')
        //     ->join('lb_publisher as editora', 'livro.id_publisher', '=', 'editora.id')
        //     ->join('lb_category as categoria', 'livro.id_category', '=', 'categoria.id')
        //     ->where("livro.deleted_at", "=", null)

        //     ->orderBy('livro.title')
        //     ->distinct()
        //     ->select(
        //         [
        //             "livro.id as codigo",
        //             "livro.title as titulo",
        //             "livro.isbn as isbn",
        //             "livro.year as ano",
        //             "livro.edition as edicao",
        //             "livro.language as idioma",
        //             "livro.pages as paginas",
        //             "livro.total as quantidade",
        //             "livro.subtitle as subtitulo",
        //             "autor.name as nome",
        //             "autor.surname as sobrenome",
        //             "categoria.name as categoria",
        //             "editora.name as editora",
        //             "livro.location as local"
        //         ]
        //     )
        //     ->get();

        // $usuarios = DB::table('users as leitor')
        // ->join('user_parameters as parametros', 'leitor.id', '=', 'parametros.users_id')
        // ->where("parametros.parameters_id", "=", 1)
        // ->where("leitor.deleted_by", "=", null)
        // ->where("parametros.value", "!=", "")
        // ->where("parametros.deleted_by", "=", null)
        // ->orderBy("parametros.value")
        // ->select([

        //     "leitor.id as leitor_codigo",
        //     "parametros.value as leitor_nome",
        //     "leitor.email as leitor_email"

        // ])
        // ->get();


        // $leitor = DB::table('lb_loan as requisicao')

        //     ->select([

        //         "requisicao.id_user as leitor_codigo",

        //     ])
        //     ->get();


        // Logica para listar os campos diferentes em um relacionamento muitos para muitos, onde os outros dados sao iguais

        // $livros = collect($books)->groupBy("isbn")->map(function ($item, $key) {

        //     $total = count($item);

        //     $autores = [];

        //     for ($i = 0; $i < $total; $i++) {

        //         $autores[] = $item[$i]->nome . " " . $item[$i]->sobrenome;
        //     }

        //     $nome = "";
        //     foreach ($autores as $key => $value) {

        //         $nome = $value . "," . $nome;
        //     }

        //     $nomes = substr($nome, 0, -2);

        //     $dados[] = $item[0]->codigo;
        //     $dados[] = $item[0]->titulo;
        //     $dados[] = $item[0]->subtitulo;
        //     $dados[] = $item[0]->isbn;
        //     $dados[] = $nomes;
        //     $dados[] = $item[0]->editora;
        //     $dados[] = $item[0]->categoria;
        //     $dados[] = $item[0]->ano;
        //     $dados[] = $item[0]->edicao;
        //     $dados[] = $item[0]->idioma;
        //     $dados[] = $item[0]->paginas;
        //     $dados[] = $item[0]->quantidade;
        //     $dados[] = $item[0]->local;


        //     return $dados;
        // });









        // Pegando todas requisições (Em curso e Finalizadas)


        switch ($estado) {

            case 'Todas':

                $requisicao = DB::table('lb_loan as requisicao')
                    ->join('users as leitor', 'requisicao.id_user', '=', 'leitor.id')
                    ->join('users as bibliotecario', 'requisicao.created_by', '=', 'bibliotecario.id')
                    ->join('user_parameters as parametros', 'leitor.id', '=', 'parametros.users_id')
                    ->where("parametros.parameters_id", "=", 1)
                    ->where("requisicao.deleted_at", "=", null)
                    ->WhereDate('requisicao.created_at', '>=', $data_inicio)
                    ->WhereDate('requisicao.created_at', '<=', $data_fim)
                    ->where("requisicao.code", "!=", null)
                    ->select([
                        "requisicao.id as codigo",
                        "requisicao.code as referencia",
                        "requisicao.name_visitor as nome_visitante",
                        "requisicao.date_request as data_inicio",
                        "requisicao.date_devolution as data_fim",
                        "requisicao.status as estado",
                        "parametros.value as leitor_nome",
                        "leitor.email as leitor_email",
                        "bibliotecario.name as bibliotecario"
                    ])
                    ->orderBy("referencia")
                    ->get();


                break;


            default:
                $requisicao = DB::table('lb_loan as requisicao')
                    ->join('users as leitor', 'requisicao.id_user', '=', 'leitor.id')
                    ->join('user_parameters as parametros', 'leitor.id', '=', 'parametros.users_id')
                    ->join('users as bibliotecario', 'requisicao.created_by', '=', 'bibliotecario.id')
                    ->where("parametros.parameters_id", "=", 1)
                    ->where("requisicao.deleted_at", "=", null)
                    ->WhereDate('requisicao.created_at', '>=', $data_inicio)
                    ->WhereDate('requisicao.created_at', '<=', $data_fim)
                    ->where("requisicao.code", "!=", null)
                    ->where("requisicao.status", "=", $estado)
                    ->select([
                        "requisicao.id as codigo",
                        "requisicao.code as referencia",
                        "requisicao.name_visitor as nome_visitante",
                        "requisicao.date_request as data_inicio",
                        "requisicao.date_devolution as data_fim",
                        "requisicao.status as estado",
                        "parametros.value as leitor_nome",
                        "leitor.email as leitor_email",
                        "bibliotecario.name as bibliotecario"

                    ])
                    ->orderBy("referencia")
                    ->get();
                break;
        }


        $id_bibliotecario = auth()->user()->id;

        $bibliotecario = DB::table('users as bibliotecario')
            ->join('user_parameters as parametros', 'bibliotecario.id', '=', 'parametros.users_id')
            ->where("parametros.parameters_id", "=", 1)
            ->where("bibliotecario.id", "=", $id_bibliotecario)
            ->select(["parametros.value as nome_bibliotecario"])
            ->get();

        //dados da instituição  

        $institution = Institution::latest()->first();
        $titulo_documento = "REQUISIÇÃO DE LIVRO";
        $documentoGerado_documento = "Documento gerado a ";
        $documentoCode_documento = 1;

        //instaciando o PDF

        $pdf = PDF::loadView("GA::library.pdf.relatorio_pdf", compact(
            'requisicao',
            'data_inicio',
            'data_fim',
            'bibliotecario',
            'estado',
            'institution',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento'
        ));
        //Configuração
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        //Nome do documento PDF  

        $pdf_name = "Relatorio";

        //Rodapé do PDF

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf->setOption('footer-html', $footer_html);

        //Retornar o PDF 

        return $pdf->stream($pdf_name . '.pdf');


        // return view('GA::library.pdf.requisicao_pdf',[
        //     "requisicao"=>$requisicao
        // ]); 

    }

    // Criar PDF para relatórios de computadores requisitados

    public function library_reports_computer_pdf($inicio, $fim, $estado)
    {

        $data_inicio = $inicio;
        $data_fim = $fim;
        $estado = $estado;

        // Pegando todas requisições (Em curso e Finalizadas)


        switch ($estado) {

            case 'Todas':

                $requisicao = DB::table('lb_computer as computadores')
                    ->join('lb_loan_computer as requisicao_computador', 'computadores.id', '=', 'requisicao_computador.id_computer')
                    ->join('users as leitor', 'requisicao_computador.id_user', '=', 'leitor.id')
                    ->join('users as bibliotecario', 'requisicao_computador.created_by', '=', 'bibliotecario.id')
                    ->join('user_parameters as parametros', 'requisicao_computador.id_user', '=', 'parametros.users_id')
                    ->where("computadores.deleted_by", "=", null)
                    ->WhereDate('requisicao_computador.created_at', '>=', $data_inicio)
                    ->WhereDate('requisicao_computador.created_at', '<=', $data_fim)
                    ->where("parametros.parameters_id", "=", 1)
                    ->orderBy("requisicao_computador.id")
                    ->select([

                        "requisicao_computador.code as referencia",
                        "computadores.name as nome_computador",
                        "computadores.brand as marca_computador",
                        "requisicao_computador.status as estado_requisicao",
                        "requisicao_computador.name_visitor as nome_visitante",
                        "computadores.status_use as estado_uso",
                        "requisicao_computador.date as data_requisicao",
                        "requisicao_computador.time as hora_requisicao",
                        "requisicao_computador.created_at as hora",
                        "requisicao_computador.time_final as hora_final",
                        "parametros.value as requerente",
                        "leitor.email",
                        "bibliotecario.name as bibliotecario"

                    ])
                    ->get();


                break;


            default:

                $requisicao = DB::table('lb_computer as computadores')
                    ->join('lb_loan_computer as requisicao_computador', 'computadores.id', '=', 'requisicao_computador.id_computer')
                    ->join('users as leitor', 'requisicao_computador.id_user', '=', 'leitor.id')
                    ->join('users as bibliotecario', 'requisicao_computador.created_by', '=', 'bibliotecario.id')
                    ->join('user_parameters as parametros', 'requisicao_computador.id_user', '=', 'parametros.users_id')
                    ->where("computadores.deleted_by", "=", null)
                    ->WhereDate('requisicao_computador.created_at', '>=', $data_inicio)
                    ->WhereDate('requisicao_computador.created_at', '<=', $data_fim)
                    ->where("requisicao_computador.status", "=", $estado)
                    ->where("parametros.parameters_id", "=", 1)
                    ->orderBy("requisicao_computador.id")
                    ->select([

                        "requisicao_computador.code as referencia",
                        "computadores.name as nome_computador",
                        "computadores.brand as marca_computador",
                        "requisicao_computador.status as estado_requisicao",
                        "requisicao_computador.name_visitor as nome_visitante",
                        "computadores.status_use as estado_uso",
                        "requisicao_computador.date as data_requisicao",
                        "requisicao_computador.time as hora_requisicao",
                        "requisicao_computador.created_at as hora",
                        "requisicao_computador.time_final as hora_final",
                        "parametros.value as requerente",
                        "leitor.email",
                        "bibliotecario.name as bibliotecario"

                    ])
                    ->get();
        }


        $id_bibliotecario = auth()->user()->id;

        $bibliotecario = DB::table('users as bibliotecario')
            ->join('user_parameters as parametros', 'bibliotecario.id', '=', 'parametros.users_id')
            ->where("parametros.parameters_id", "=", 1)
            ->where("bibliotecario.id", "=", $id_bibliotecario)
            ->select(["parametros.value as nome_bibliotecario"])
            ->get();

        //dados da instituição  

        $institution = Institution::latest()->first();
        $titulo_documento = "REQUISIÇÃO DE COMPUTADORES";
        $documentoGerado_documento = "Documento gerado a ";
        $documentoCode_documento = 1;

        //instaciando o PDF

        $pdf = PDF::loadView("GA::library.pdf.relatorio_computer_pdf", compact(
            'requisicao',
            'data_inicio',
            'data_fim',
            'bibliotecario',
            'estado',
            'institution',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento'
        ));
        //Configuração
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        //Nome do documento PDF  

        $pdf_name = "Relatorio";

        //Rodapé do PDF

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf->setOption('footer-html', $footer_html);

        //Retornar o PDF 

        return $pdf->stream($pdf_name . '.pdf');


        // return view('GA::library.pdf.requisicao_pdf',[
        //     "requisicao"=>$requisicao
        // ]); 

    }


    // ================================================ PDF dos itens da Biblioteca ==================


    // Metodos para gerar pdf das editoras, categorias, computadores, livros e autores


    // Metodos global para gerar pdf dos itens

    public function library_report_item_pdf($item)
    {



        switch ($item) {

            case 'categoria':

                return $this->pdfCategory();

                break;

            case 'editora':

                return $this->pdfPublisher();

                break;

            case 'autor':

                return $this->pdfAuthor();

                break;

            case 'livro':

                return $this->pdfBook();

                break;

            case 'computador':

                return $this->pdfComputer();

                break;

            default:
                # code...
                break;
        }
    }

    // Gerar o PDF dos Autores 

    public function pdfAuthor()
    {

        // Pesquisar todos os autores cadastrados no sistema

        $autores = DB::table('lb_author as autor')
            ->where("autor.deleted_at", "=", null)
            ->select([
                "autor.id",
                "autor.name",
                "autor.genre",
                "autor.surname",
                "autor.country",
                "autor.others_information"
            ])
            ->orderBy("name", "asc")
            ->get();

        // Pesquisar todas as obras de cada autor

        $autores_livro = DB::table('lb_author as autor')
            ->join('lb_author_book as autor_livro', 'autor.id', '=', 'autor_livro.author_id')
            ->join('lb_book as livro', 'livro.id', '=', 'autor_livro.book_id')
            ->where("autor.deleted_at", "=", null)
            ->where("livro.deleted_at", "=", null)
            ->select([
                "autor.id",
                "autor.name",
                "autor.genre",
                "autor.surname",
                "autor.country",
                "autor.others_information"
            ])
            ->orderBy("name", "asc")
            ->get();

        // Agrupar os autores e a quantidade de suas obras 

        $autores_total_livro = collect($autores_livro)->groupBy("name")->map(function ($item, $key) {
            return $item[0]->id . "-" . count($item);
        });


        //dados da instituição  

        $institution = Institution::latest()->first();
        $titulo_documento = "LISTA DE AUTORES";
        $documentoGerado_documento = "Documento gerado a ";
        $documentoCode_documento = 1;

        //instaciando o PDF  

        $pdf = PDF::loadView("GA::library.pdf.Author_pdf", compact(
            'autores_total_livro',
            'autores',
            'institution',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento'
        ));
        //Configuração
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        //Nome do documento PDF  

        $pdf_name = "Lista_de_Autores";

        //Rodapé do PDF

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf->setOption('footer-html', $footer_html);

        //Retornar o PDF 

        return $pdf->stream($pdf_name . '.pdf');
    }

    // Gerar o PDF das categorias

    public function pdfCategory()
    {

        // Pesquisar todas as categorias cadastradas no sistema

        $categorias = DB::table('lb_category as categoria')
            ->where("categoria.deleted_at", "=", null)
            ->select([
                "categoria.id",
                "categoria.name",
                "categoria.description"
            ])
            ->orderBy("name", "asc")
            ->get();

        // Pesquisar todas as obras de cada categoria

        $categorias_livro = DB::table('lb_category as categoria')
            ->join('lb_book as livro', 'categoria.id', '=', 'livro.id_category')
            ->where("categoria.deleted_at", "=", null)
            ->where("livro.deleted_at", "=", null)
            ->select([
                "categoria.id",
                "categoria.name",
                "categoria.description"
            ])
            ->orderBy("name", "asc")
            ->get();

        // Agrupar os autores e a quantidade de suas obras 

        $categorias_total_livro = collect($categorias_livro)->groupBy("name")->map(function ($item, $key) {
            return $item[0]->id . "-" . count($item);
        });


        //dados da instituição  

        $institution = Institution::latest()->first();
        $titulo_documento = "LISTA DE ÁREAS";
        $documentoGerado_documento = "Documento gerado a ";
        $documentoCode_documento = 1;

        //instaciando o PDF  

        $pdf = PDF::loadView("GA::library.pdf.category_pdf", compact(
            'categorias_total_livro',
            'categorias',
            'institution',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento'
        ));
        //Configuração
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        //Nome do documento PDF  

        $pdf_name = "Lista_de_Categorias";

        //Rodapé do PDF

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf->setOption('footer-html', $footer_html);

        //Retornar o PDF 

        return $pdf->stream($pdf_name . '.pdf');
    }

    // Gerar o PDF dos computadores

    public function pdfComputer()
    {

        // Pesquisar todos os computadores cadastrados no sistema

        $computadores = DB::table('lb_computer as computadores')
            ->where("computadores.deleted_at", "=", null)
            ->select([
                "computadores.id",
                "computadores.name",
                "computadores.brand",
                "computadores.processor",
                "computadores.ram",
                "computadores.hd_ssd",
                "computadores.status"
            ])
            ->orderBy("name", "asc")
            ->get();


        //dados da instituição  

        $institution = Institution::latest()->first();
        $titulo_documento = "RELATÓRIO DE COMPUTADORES";
        $documentoGerado_documento = "Documento gerado a ";
        $documentoCode_documento = 1;

        //instaciando o PDF  

        $pdf = PDF::loadView("GA::library.pdf.computer_pdf", compact(
            'computadores',
            'institution',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento'
        ));
        //Configuração
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        //Nome do documento PDF  

        $pdf_name = "Relatorio";

        //Rodapé do PDF

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf->setOption('footer-html', $footer_html);

        //Retornar o PDF 

        return $pdf->stream($pdf_name . '.pdf');
    }

    // Gerar o PDF das editoras

    public function pdfPublisher()
    {

        // Pesquisar todas as editoras cadastradas no sistema

        $editoras = DB::table('lb_publisher as editora')
            ->where("editora.deleted_at", "=", null)
            ->select([
                "editora.id",
                "editora.name",
                "editora.email",
                "editora.address",
                "editora.district",
                "editora.city",
                "editora.country"
            ])
            ->orderBy("name", "asc")
            ->get();

        // Pesquisar todas as obras de cada editora

        $editoras_livro = DB::table('lb_publisher as editora')
            ->join('lb_book as livro', 'editora.id', '=', 'livro.id_publisher')
            ->where("editora.deleted_at", "=", null)
            ->where("livro.deleted_at", "=", null)
            ->select([
                "editora.id",
                "editora.name"
            ])
            ->orderBy("name", "asc")
            ->get();

        // Agrupar os autores e a quantidade de suas obras 

        $editoras_total_livro = collect($editoras_livro)->groupBy("name")->map(function ($item, $key) {
            return $item[0]->id . "-" . count($item);
        });


        //dados da instituição  

        $institution = Institution::latest()->first();
        $titulo_documento = "LISTA DE EDITORAS";
        $documentoGerado_documento = "Documento gerado a ";
        $documentoCode_documento = 1;

        //instaciando o PDF  

        $pdf = PDF::loadView("GA::library.pdf.publisher_pdf", compact(
            'editoras_total_livro',
            'editoras',
            'institution',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento'
        ));
        //Configuração
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        //Nome do documento PDF  

        $pdf_name = "Lista_de_Editoras";

        //Rodapé do PDF

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf->setOption('footer-html', $footer_html);

        //Retornar o PDF 

        return $pdf->stream($pdf_name . '.pdf');
    }

    // Gerar o PDF dos livros

    public function pdfBook()
    {

        // Listar todos dos livros presentes na biblioteca

        $books = DB::table('lb_book as livro')
            ->join('lb_author_book as autor_livro', 'livro.id', '=', 'autor_livro.book_id')
            ->join('lb_author as autor', 'autor_livro.author_id', '=', 'autor.id')
            ->join('lb_publisher as editora', 'livro.id_publisher', '=', 'editora.id')
            ->join('lb_category as categoria', 'livro.id_category', '=', 'categoria.id')
            ->where("livro.deleted_at", "=", null)
            ->orderBy('livro.id')
            ->distinct()
            ->select(
                [

                    "livro.id as codigo",
                    "livro.title as titulo",
                    "livro.isbn as isbn",
                    "livro.year as ano",
                    "livro.edition as edicao",
                    "livro.language as idioma",
                    "livro.pages as paginas",
                    "livro.total as quantidade",
                    "livro.subtitle as subtitulo",
                    "autor.name as autor",
                    "autor.surname as sobrenome",
                    "categoria.name as categoria",
                    "editora.name as editora",
                    "livro.location as local"
                ]
            )
            ->get();

        // Logica para listar os campos diferentes em um relacionamento muitos para muitos, onde os outros dados sao iguais

        $livros = collect($books)->groupBy("isbn")->map(function ($item, $key) {

            $total = count($item);

            $autores = [];

            for ($i = 0; $i < $total; $i++) {

                $autores[] = $item[$i]->autor . " " . $item[$i]->sobrenome . " ";
            }

            $nome = "";
            foreach ($autores as $key => $value) {

                $nome = $value . "," . $nome;
            }

            $nomes = substr($nome, 0, -2);



            $dados[] = $item[0]->titulo;
            $dados[] = $item[0]->subtitulo;
            $dados[] = $nomes;
            $dados[] = $item[0]->editora;
            $dados[] = $item[0]->categoria;
            $dados[] = $item[0]->isbn;
            $dados[] = $item[0]->ano;
            $dados[] = $item[0]->edicao;
            $dados[] = $item[0]->idioma;
            $dados[] = $item[0]->paginas;
            $dados[] = $item[0]->quantidade;
            $dados[] = $item[0]->codigo;
            // $dados[] = $item[0]->local;

            return $dados;
        });


        //dados da instituição  

        $institution = Institution::latest()->first();
        $titulo_documento = "LISTA DE LIVROS";
        $documentoGerado_documento = "Documento gerado a ";
        $documentoCode_documento = 1;

        //instaciando o PDF  

        $pdf = PDF::loadView("GA::library.pdf.book_pdf", compact(
            'livros',
            'institution',
            'titulo_documento',
            'documentoGerado_documento',
            'documentoCode_documento'
        ));
        //Configuração
        $pdf->setOption('margin-top', '1mm');
        $pdf->setOption('margin-left', '1mm');
        $pdf->setOption('margin-bottom', '12mm');
        $pdf->setOption('margin-right', '1mm');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('debug-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setPaper('a4', 'portrait');

        //Nome do documento PDF  

        $pdf_name = "Lista_de_Livros";

        //Rodapé do PDF

        $footer_html = view()->make('Reports::pdf_model.pdf_footer', compact('institution'))->render();

        $pdf->setOption('footer-html', $footer_html);

        //Retornar o PDF 

        return $pdf->stream($pdf_name . '.pdf');
    }

    // Metodos para alterar os dados da editora, categoria, livros e autores em funççao dos id,
    // Partindo de uma requi... ajax

    // Metodos global para editar os dados dos itens

    public function edit_item($array)
    {

        // Pega os dados provenientes do formúlário e os converte em arrays

        $dados = explode(',', $array);
        $id_usuario = auth()->user()->id;

        // Pega o código da acção

        $action = $dados[0] . "";


        switch ($action) {

            case 'categoria':

                return $this->editCategory($dados, $id_usuario);

                break;

            case 'editora':

                return $this->editPublisher($dados, $id_usuario);

                break;

            case 'autor':

                return $this->editAuthor($dados, $id_usuario);

                break;

            case 'livro':

                return $this->editbook($dados, $id_usuario);

                break;

            case 'computador':

                return $this->editComputer($dados, $id_usuario);

                break;
            case 'quantidade':

                return $this->editQuantidade($dados, $id_usuario);

                break;

            default:
                # code...
                break;
        }
    }

    // Editar os dados da cateoria

    public function editCategory($dados, $id_usuario)
    {

        if (

            DB::table('lb_category')
            ->where('name', "=", $dados[2])
            ->orWhere('description', "=", $dados[3])
            ->where('id', "!=", $dados[1])
            ->exists()
        ) {

            return response()->json(["nome existente," . ""]);
        } else {

            $alterar = DB::table('lb_category')
                ->where('id', "=", $dados[1])
                ->update(
                    [
                        "name" =>  $dados[2],
                        "description" => $dados[3],
                        "updated_by" => $id_usuario,
                        "updated_at" => Carbon::now()
                    ]
                );

            return response()->json(["sucesso," . ""]);
        }
    }

    // Editar a quantidade dos livros

    public function editQuantidade($dados, $id_usuario)
    {



        // Pegar o livro que sempre alterar a quantidade restante

        $quantidade = DB::table('lb_book as livro')
            ->where("id", "=", $dados[1])
            ->select(
                [
                    "livro.quantidade_restante as restante",
                    "livro.total as quantidade"
                ]
            )
            ->get();



        switch ($dados[2]) {
            case 'add':



                $devolver = DB::table('lb_book')
                    ->where('id', "=",  $dados[1])
                    ->update(
                        [
                            "quantidade_restante" => $quantidade[0]->restante + $dados[3],
                            "total" => $quantidade[0]->quantidade + $dados[3],
                            "updated_by" => $id_usuario,
                            "updated_at" => Carbon::now()
                        ]
                    );
                return response()->json(["sucesso", "finalizada"]);

                break;
            case 'delete':



                if ($quantidade[0]->quantidade < $dados[3]) {

                    return response()->json(["Erro", ""]);
                } else {

                    $total = $quantidade[0]->quantidade - $dados[3];
                    $restante =  $quantidade[0]->restante - $dados[3];

                    if ($restante < 0) {
                        $restante = 0;
                    }

                    $devolver = DB::table('lb_book')
                        ->where('id', "=",  $dados[1])
                        ->update(
                            [
                                "quantidade_restante" => $restante,
                                "total" => $total,
                                "updated_by" => $id_usuario,
                                "updated_at" => Carbon::now()
                            ]
                        );


                    return response()->json(["sucesso", "finalizada"]);
                }

                break;

            default:
                # code...
                break;
        }


        // return response()->json(["Finalizada"]);
    }

    // Editar os dados da editora 

    public function editPublisher($dados, $id_usuario)
    {


        $alterar = DB::table('lb_publisher')
            ->where('id', "=", $dados[1])
            ->update(
                [
                    "name" =>  $dados[2],
                    "email" => $dados[3],
                    "address" => $dados[4],
                    "city" => $dados[5],
                    "country" => $dados[6],
                    "updated_by" => $id_usuario,
                    "updated_at" => Carbon::now()
                ]
            );

        return response()->json(["sucesso," . ""]);
    }

    // Editar os dados do computador

    // Pegando os dados da editora para a editar via ajax

    public function editComputer($dados, $id_usuario)
    {



        if (

            DB::table('lb_computer')
            ->where('name', "=", $dados[2])
            ->where('id', "!=", $dados[1])
            ->exists()
        ) {
            return response()->json(["nome existente," . ""]);
        } else {



            $alterar = DB::table('lb_computer')
                ->where('id', "=", $dados[1])
                ->update(
                    [
                        "name" =>  $dados[2],
                        "brand" => $dados[3],
                        "processor" => $dados[4],
                        "ram" => $dados[5],
                        "hd_ssd" => $dados[6],
                        "status" => $dados[7],
                        "updated_by" => $id_usuario,
                        "updated_at" => Carbon::now()
                    ]
                );

            return response()->json(["sucesso," . ""]);
        }
    }

    // Editar autores 

    public function editAuthor($dados, $id_usuario)
    {


        $alterar = DB::table('lb_author')
            ->where('id', "=", $dados[1])
            ->update(
                [
                    "name" =>  $dados[2],
                    "surname" =>  $dados[3],
                    "genre" => $dados[4],
                    "others_information" => $dados[5],
                    "country" => $dados[6],
                    "updated_by" => $id_usuario,
                    "updated_at" => Carbon::now()
                ]
            );
        return response()->json(["sucesso"]);
    }

    // Editar livro

    public function editBook($dados, $id_usuario)
    {

        $alterar = DB::table('lb_book')
            ->where('id', "=", $dados[1])
            ->update(
                [
                    "title" =>  $dados[2],
                    "subtitle" =>  $dados[3],
                    "id_publisher" =>  $dados[4],
                    "id_category" =>  $dados[5],
                    "isbn" =>  $dados[6],
                    "year" =>  $dados[7],
                    "edition" =>  $dados[8],
                    "location" =>  $dados[9],
                    "language" =>  $dados[10],
                    "pages" =>  $dados[11],
                    "total" =>  $dados[12],
                    "updated_by" => $id_usuario,
                    "updated_at" => Carbon::now()
                ]
            );


        $autores = explode('-', $dados[13]);

        DB::table('lb_author_book')->where('book_id', '=', $dados[1])->delete();

        foreach ($autores as $key => $value) {

            DB::table('lb_author_book')->insert(
                [
                    "author_id" => $value,
                    "book_id" => $dados[1]
                ]
            );
        }

        return response()->json(["sucesso"]);
    }

    // ===================================================== Pegar os dados para os modais =======================================================

    public function get_item($array)
    {
        $dados = explode(",", $array);

        $action = $dados[0];
        $id = $dados[1];

        switch ($action) {

            case 'categoria':

                return $this->get_category($id);

                break;

            case 'editora':

                return $this->get_publisher($id);

                break;

            case 'autor':

                return $this->get_author($id);

                break;

            case 'livro':

                return $this->get_book($id);

                break;

            case 'leitor':

                return $this->get_reader($id);

                break;
            case 'visitante':

                return $this->get_visitor($id);

                break;

            case 'livros':

                return $this->get_reader($id);

                break;

            case 'computador':

                return $this->get_computer($id);

                break;

            default:
                # code...
                break;
        }
    }

    // Pegando e retornando os dados dos livros com o Ajax

    public function get_book($id)
    {


        $livros = DB::table('lb_book as livro')
            ->join("lb_category as categoria", "livro.id_category", "=", "categoria.id")
            ->join("lb_publisher as editora", "livro.id_publisher", "=", "editora.id")
            ->where("livro.id", "=", $id)
            ->select(
                [

                    "livro.id as codigo",
                    "livro.title as titulo",
                    "livro.isbn as isbn",
                    "livro.year as ano",
                    "livro.edition as edicao",
                    "livro.language as idioma",
                    "livro.pages as paginas",
                    "livro.total as quantidade",
                    "livro.subtitle as subtitulo",
                    "categoria.name as categoria",
                    "editora.name as editora",
                    "livro.location as local",
                    "id_publisher as codigo_editora",
                    "id_category as codigo_categoria"
                ]
            )
            ->get();

        $autores = DB::table('lb_book as livro')
            ->join('lb_author_book as autor_livro', 'livro.id', '=', 'autor_livro.book_id')
            ->join('lb_author as autor', 'autor_livro.author_id', '=', 'autor.id')
            ->where("livro.id", "=", $id)
            ->select(["autor.id as codigo_autor", "autor.name as nome_autor", "autor.surname as sobrenome_autor"])
            ->get();

        $nome = "";

        $autor = collect($autores);

        foreach ($autor as $item) {
            $autor = $item->codigo_autor . "-" . $item->nome_autor . " " . $item->sobrenome_autor;

            $nome = $nome . "-" . $autor;
        }

        return response()->json([

            $livros[0]->codigo,
            $livros[0]->titulo,
            $livros[0]->subtitulo,
            $livros[0]->isbn,
            $livros[0]->ano,
            $livros[0]->edicao,
            $livros[0]->local,
            $livros[0]->idioma,
            $livros[0]->paginas,
            $livros[0]->quantidade,
            $livros[0]->codigo_editora,
            $livros[0]->editora,
            $livros[0]->codigo_categoria,
            $livros[0]->categoria,
            $nome
        ]);
    }

    // Pegando os dados do autor via ajax

    public function get_author($id)
    {

        $autores = DB::table('lb_author')
            ->where("id", "=", $id)
            ->where("deleted_at", "=", null)
            ->select(["id", "name", "surname", "genre", "country", "others_information"])
            ->get();

        return response()->json([

            $autores[0]->id,
            $autores[0]->name,
            $autores[0]->surname,
            $autores[0]->genre,
            $autores[0]->country,
            $autores[0]->others_information

        ]);
    }

    // Pegando os dados da categoria via ajax

    public function get_category($id)
    {

        $categoria = DB::table('lb_category')
            ->where("id", "=", $id)
            ->where("deleted_at", "=", null)
            ->select(["id", "name", "description"])
            ->get();

        return response()->json([

            $categoria[0]->id,
            $categoria[0]->name,
            $categoria[0]->description

        ]);
    }

    // Pegando os dados da editora

    public function get_publisher($id)
    {

        $editora = DB::table('lb_publisher')
            ->where("id", "=", $id)
            ->where("deleted_at", "=", null)
            ->get();

        return response()->json([

            $editora[0]->id,
            $editora[0]->name,
            $editora[0]->email,
            $editora[0]->address,
            $editora[0]->district,
            $editora[0]->city,
            $editora[0]->country
        ]);
    }

    // Pegando os dados dos computadores

    public function get_computer($id)
    {

        $computador = DB::table('lb_computer')
            ->where("id", "=", $id)
            ->where("deleted_at", "=", null)
            ->get();

        return response()->json([

            $computador[0]->id,
            $computador[0]->name,
            $computador[0]->brand,
            $computador[0]->processor,
            $computador[0]->ram,
            $computador[0]->hd_ssd,
            $computador[0]->status
        ]);
    }

    // Pegando os dados do visitante via ajax

    public function get_visitor($id)
    {


        $visitante = DB::table('lb_visitor as visitante')
            ->where("visitante.id", "=", $id)
            ->where("visitante.deleted_at", "=", null)
            ->select(["visitante.id as codigo", "visitante.name as nome", "bi"])
            ->get();

        return response()->json([

            $visitante[0]->codigo,
            $visitante[0]->nome,
            $visitante[0]->email

        ]);
    }

    // Pegando os dados do leitor via ajax

    public function get_reader($id)
    {


        $leitor = DB::table('users as leitor')
            ->join('user_parameters as parametros', 'leitor.id', '=', 'parametros.users_id')
            ->where("parametros.parameters_id", "=", 1)
            ->where("leitor.id", "=", $id)
            ->where("leitor.deleted_at", "=", null)
            ->select(["leitor.id as codigo", "parametros.value as nome", "email", "image"])
            ->get();

        $andamento = DB::table('lb_loan as requisicao')
            ->where("requisicao.id_user", "=", $id)
            ->where("requisicao.status", "=", "Em curso")
            ->count();

        $finalizada = DB::table('lb_loan as requisicao')
            ->where("requisicao.id_user", "=", $id)
            ->where("requisicao.status", "=", "Finalizada")
            ->count();

        return response()->json([

            $leitor[0]->codigo,
            $leitor[0]->nome,
            $leitor[0]->email,
            $leitor[0]->image,
            $andamento,
            $finalizada

        ]);
    }


    // Pegando os dados das requisiçoes 

    public function get_user_loan($id)
    {

        try {



            $requisicao = DB::table('lb_loan as requisicao')
                ->join('users as leitor', 'requisicao.id_user', '=', 'leitor.id')
                ->join('user_parameters as parametros', 'leitor.id', '=', 'parametros.users_id')
                ->where("parametros.parameters_id", "=", 1)
                ->where("requisicao.deleted_at", "=", null)
                ->where("leitor.id", "=", $id)
                ->select([
                    "requisicao.id as codigo",
                    "requisicao.code as referencia",
                    "requisicao.date_request as data_inicio",
                    "requisicao.date_devolution as data_fim",
                    "requisicao.status as estado",
                    "parametros.value as leitor_nome"
                ]);

            return Datatables::queryBuilder($requisicao)
                ->addColumn('actions', function ($item) {

                    return view('GA::library.datatables.action')->with('item', $item);
                })
                ->addColumn('states', function ($states) {
                    return view('GA::library.datatables.states')->with('states', $states);
                })
                ->rawColumns(['actions', 'states'])
                ->addIndexColumn()
                ->toJson();
        } catch (Exception | Throwable $e) {
            logError($e);
            return response()->json($e->getMessage(), 500);
        }
    }

    // ================================ Pegar os dados apartir do estado das requisiçoe dos livros... ========================

    public function get_states_loan($estado)
    {


        if ($estado == "Todas") {

            $requisicao = DB::table('lb_loan as requisicao')
                ->join('users as leitor', 'requisicao.id_user', '=', 'leitor.id')
                ->join('user_parameters as parametros', 'leitor.id', '=', 'parametros.users_id')
                ->where("parametros.parameters_id", "=", 1)
                // ->join('lb_loan_book as requisicao_livro', 'requisicao.id', '=', 'requisicao_livro.id_loan')
                // ->join('lb_book as livro', 'livro.id', '=', 'requisicao_livro.id_book')
                ->where("requisicao.deleted_at", "=", null)
                ->select([
                    "requisicao.id as codigo",
                    "requisicao.date_request as data_inicio",
                    "requisicao.name_visitor as nome_requerente",
                    "requisicao.code as referencia",
                    "requisicao.date_devolution as data_fim",
                    "requisicao.status as estado",
                    "parametros.value as leitor_nome",
                    "leitor.email as leitor_email"
                    // "livro.title as livro_titulo",
                    // "livro.isbn as livro_isbn"
                ])
                ->orderBy("name");
        } else {


            $requisicao = DB::table('lb_loan as requisicao')
                ->join('users as leitor', 'requisicao.id_user', '=', 'leitor.id')
                ->join('user_parameters as parametros', 'leitor.id', '=', 'parametros.users_id')
                ->where("parametros.parameters_id", "=", 1)
                // ->join('lb_loan_book as requisicao_livro', 'requisicao.id', '=', 'requisicao_livro.id_loan')
                // ->join('lb_book as livro', 'livro.id', '=', 'requisicao_livro.id_book')
                ->where("requisicao.deleted_at", "=", null)
                ->where("requisicao.status", "=", $estado)
                ->select([
                    "requisicao.id as codigo",
                    "requisicao.date_request as data_inicio",
                    "requisicao.code as referencia",
                    "requisicao.name_visitor as nome_requerente",
                    "requisicao.date_devolution as data_fim",
                    "requisicao.status as estado",
                    "parametros.value as leitor_nome",
                    "leitor.email as leitor_email"
                    // "livro.title as livro_titulo",
                    // "livro.isbn as livro_isbn"
                ])
                ->orderBy("name");
        }


        return Datatables::queryBuilder($requisicao)
            ->addColumn('name', function ($nome) {
                return view('GA::library.datatables.nomes')->with('nome', $nome);
            })
            ->addColumn('actions', function ($item) {
                return view('GA::library.datatables.action')->with('item', $item);
            })
            ->addColumn('states', function ($states) {
                return view('GA::library.datatables.states')->with('states', $states);
            })
            ->rawColumns(['actions', 'states'])
            ->addIndexColumn()
            ->toJson();
    }

    // ================================ Pegar os dados apartir do estado das requisiçoe dos computadores... ========================

    public function get_states_loan_computer($estado)
    {


        if ($estado == "Todas") {

            $requisicao_computador = DB::table('lb_computer as computadores')
                ->join('lb_loan_computer as requisicao_computador', 'computadores.id', '=', 'requisicao_computador.id_computer')
                ->join('users as leitor', 'requisicao_computador.id_user', '=', 'leitor.id')
                ->join('user_parameters as parametros', 'requisicao_computador.id_user', '=', 'parametros.users_id')
                ->where("parametros.parameters_id", "=", 1)
                ->select([

                    "requisicao_computador.id as codigo",
                    "requisicao_computador.code as referencia",
                    "computadores.name as nome_computador",
                    "computadores.brand as marca_computador",
                    "requisicao_computador.name_visitor as nome_requerente",
                    "requisicao_computador.status as estado_requisicao",
                    "computadores.status_use as estado_uso",
                    "requisicao_computador.date as data_requisicao",
                    "requisicao_computador.time as hora_requisicao",
                    "requisicao_computador.created_at as hora",
                    "requisicao_computador.time_final as hora_final",
                    "parametros.value as requerente",
                    "leitor.email"

                ])
                ->orderBy("referencia");
        } else {


            $requisicao_computador = DB::table('lb_computer as computadores')
                ->join('lb_loan_computer as requisicao_computador', 'computadores.id', '=', 'requisicao_computador.id_computer')
                ->join('users as leitor', 'requisicao_computador.id_user', '=', 'leitor.id')
                ->join('user_parameters as parametros', 'requisicao_computador.id_user', '=', 'parametros.users_id')
                ->where("requisicao_computador.status", "=", $estado)
                ->where("computadores.deleted_by", "=", null)
                ->where("parametros.parameters_id", "=", 1)
                ->select([

                    "requisicao_computador.id as codigo",
                    "requisicao_computador.code as referencia",
                    "requisicao_computador.name_visitor as nome_requerente",
                    "computadores.name as nome_computador",
                    "computadores.brand as marca_computador",
                    "requisicao_computador.status as estado_requisicao",
                    "computadores.status_use as estado_uso",
                    "requisicao_computador.date as data_requisicao",
                    "requisicao_computador.time as hora_requisicao",
                    "requisicao_computador.created_at as hora",
                    "requisicao_computador.time_final as hora_final",
                    "parametros.value as requerente",
                    "leitor.email"

                ])
                ->orderBy("referencia");
        }


        return Datatables::queryBuilder($requisicao_computador)
            ->addColumn('name', function ($nome) {
                return view('GA::library.datatables.nomes')->with('nome', $nome);
            })
            ->addColumn('actions', function ($item) {
                return view('GA::library.datatables.action_computer')->with('item', $item);
            })
            ->addColumn('states', function ($states) {
                return view('GA::library.datatables.states_computer')->with('states', $states);
            })
            ->rawColumns(['actions', 'states'])
            ->addIndexColumn()
            ->toJson();
    }



    // ================================================ Pegando os livros requisitados ===============================

    public function get_book_loan($id)
    {




        $requisicao = DB::table('lb_loan as requisicao')
            ->join('users as leitor', 'requisicao.id_user', '=', 'leitor.id')
            ->join('lb_loan_book as requisicao_livro', 'requisicao.id', '=', 'requisicao_livro.id_loan')
            ->join('lb_book as livro', 'livro.id', '=', 'requisicao_livro.id_book')
            ->where("requisicao.deleted_at", "=", null)
            ->where("requisicao.id", "=", $id)
            ->select([
                "requisicao.id as codigo",
                "livro.id as codigo_livro",
                "livro.title as livro_titulo",
                "livro.title as livro_subtitulo",
                "livro.isbn as livro_isbn"
            ])
            ->orderBy("name");

        return Datatables::queryBuilder($requisicao)
            ->addIndexColumn()
            ->toJson();
    }


    // ================================================ Eliminar item da biblioteca ============================================ 


    // Metodo principal pra eliminar os dados da biblioteca

    public function delete_item($array)
    {
        $dados = explode(",", $array);

        $id_usuario = auth()->user()->id;

        $action = $dados[0];
        $id = $dados[1];

        switch ($action) {

            case 'categoria':

                return $this->delete_category($id, $id_usuario);

                break;

            case 'editora':

                return $this->delete_publisher($id, $id_usuario);

                break;

            case 'autor':

                return $this->delete_author($id, $id_usuario);

                break;

            case 'livro':

                return $this->delete_book($id, $id_usuario);

                break;

            case 'devolucao':

                return $this->devolution_book($id, $id_usuario);

                break;

            case 'requisicao-computador':

                return $this->devolution_computer($id, $id_usuario);

                break;

            default:
                # code...
                break;
        }
    }

    // Eliminar categoria

    public function delete_category($id, $id_usuario)
    {


        $deletar = DB::table('lb_category')
            ->where('id', "=", $id)
            ->update(
                [
                    "deleted_by" => $id_usuario,
                    "deleted_at" => Carbon::now()
                ]
            );

        return response()->json(["Eliminado"]);
    }

    // Eliminar editora

    public function delete_publisher($id, $id_usuario)
    {

        $deletar = DB::table('lb_publisher')
            ->where('id', "=", $id)
            ->update(
                [
                    "deleted_by" => $id_usuario,
                    "deleted_at" => Carbon::now()
                ]
            );

        return response()->json(["Eliminado"]);
    }

    // Eliminar autor

    public function delete_author($id, $id_usuario)
    {

        $deletar = DB::table('lb_author')
            ->where('id', "=", $id)
            ->update(
                [
                    "deleted_by" => $id_usuario,
                    "deleted_at" => Carbon::now()
                ]
            );

        return response()->json(["Eliminado"]);
    }

    // Eliminar livro

    public function delete_book($id, $id_usuario)
    {

        $deletar = DB::table('lb_book')
            ->where('id', "=", $id)
            ->update(
                [
                    "deleted_by" => $id_usuario,
                    "deleted_at" => Carbon::now()
                ]
            );

        return response()->json(["Eliminado"]);
    }

    // Devolução de livros requisicao-computador

    public function devolution_book($id, $id_usuario)
    {

        $livros =  DB::table('lb_loan_book')
            ->where("id_loan", "=", $id)
            ->select(
                [
                    "id_book as livro"
                ]
            )
            ->get();

        foreach ($livros as $key => $value) {

            // Pegar o livro que sempre alterar a quantidade restante

            $quantidade = DB::table('lb_book as livro')
                ->where("livro.id", "=", $value->livro)
                ->select(
                    [
                        "livro.quantidade_restante as restante"
                    ]
                )
                ->get();

            // Alterar a quantidade restante


            $quantidade = $quantidade[0]->restante + 1;


            $restante =  DB::table('lb_book')
                ->where('id', "=", $value->livro)
                ->update(["quantidade_restante" => $quantidade]);
        }


        $devolver = DB::table('lb_loan')
            ->where('id', "=", $id)
            ->update(
                [
                    "status" => "Finalizada",
                    "updated_by" => $id_usuario,
                    "updated_at" => Carbon::now()
                ]
            );

        return response()->json(["Finalizada"]);
    }

    public function devolution_computer($id, $id_usuario)
    {

        $devolver = DB::table('lb_loan_computer')
            ->where('id', "=", $id)
            ->update(
                [
                    "status" => "Finalizada",
                    "updated_by" => $id_usuario,
                    "updated_at" => Carbon::now()
                ]
            );

        $computador = DB::table('lb_loan_computer')
            ->where('id', "=", $id)
            ->select(
                [
                    "id_computer as codigo"
                ]
            )
            ->get();



        $alterar = DB::table('lb_computer')
            ->where('id', "=", $computador[0]->codigo)
            ->update(
                [
                    "status_use" => "Disponível"
                ]
            );


        return response()->json(["Finalizada"]);
    }

    // ================================================ Restaurar item da biblioteca ============================================ 

    // Metodo principal pra restaurar os dados da biblioteca

    public function recycle_item($array)
    {
        $dados = explode(",", $array);

        $action = $dados[0];
        $id = $dados[1];

        switch ($action) {

            case 'categoria':

                return $this->recycle_category($id);

                break;

            case 'editora':

                return $this->recycle_publisher($id);

                break;

            case 'autor':

                return $this->recycle_author($id);

                break;

            case 'livro':

                return $this->recycle_book($id);

                break;

            default:
                # code...
                break;
        }
    }


    // Restaurar categoria

    public function recycle_category($id)
    {


        $deletar = DB::table('lb_category')
            ->where('id', "=", $id)
            ->update(
                [
                    "deleted_by" => null,
                    "deleted_at" => null
                ]
            );

        return response()->json(["Eliminado"]);
    }

    // Restaurar editora

    public function recycle_publisher($id)
    {

        $deletar = DB::table('lb_publisher')
            ->where('id', "=", $id)
            ->update(
                [
                    "deleted_by" => null,
                    "deleted_at" => null
                ]
            );

        return response()->json(["Eliminado"]);
    }

    // Restaurar autor

    public function recycle_author($id)
    {

        $deletar = DB::table('lb_author')
            ->where('id', "=", $id)
            ->update(
                [
                    "deleted_by" => null,
                    "deleted_at" => null
                ]
            );

        return response()->json(["Eliminado"]);
    }

    // Restaurar livro

    public function recycle_book($id)
    {

        $deletar = DB::table('lb_book')
            ->where('id', "=", $id)
            ->update(
                [
                    "deleted_by" => null,
                    "deleted_at" => null
                ]
            );

        return response()->json(["Eliminado"]);
    }
}
