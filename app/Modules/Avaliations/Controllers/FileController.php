<?php

namespace App\Modules\Avaliations\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Avaliations\Models\Avaliacao;
use App\Modules\Avaliations\Models\AvaliacaoAluno;
use App\Modules\Avaliations\Models\Metrica;
use App\Modules\Avaliations\Models\TipoAvaliacao;
use Toastr;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class FileController extends Controller
{

    public function viewFile($filename)
    { {
            try {
                $filePath = storage_path("app/public/pautas-frequencia/" . $filename);

                if (!file_exists($filePath)) {
                    abort(404, "Arquivo não encontrado");
                }

                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

                return response()->file($filePath, [
                    'Content-Type' => $mimeType
                ]);
            } catch (Exception $e) {
                Log::error("Erro ao acessar arquivo: " . $e->getMessage(), ['filename' => $filename]);

                abort(Response::HTTP_NOT_FOUND, 'Arquivo não encontrado.');
            }
        }
    }

    public function pautas_frequencia($filename)
    { {
            try {
                $filePath = storage_path("app/public/pautas-frequencia/" . $filename);

                if (!file_exists($filePath)) {
                    abort(404, "Arquivo não encontrado");
                }

                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

                return response()->file($filePath, [
                    'Content-Type' => $mimeType
                ]);
            } catch (Exception $e) {
                Log::error("Erro ao acessar arquivo: " . $e->getMessage(), ['filename' => $filename]);

                abort(Response::HTTP_NOT_FOUND, 'Arquivo não encontrado.');
            }
        }
    }

    public function pautas_final($filename)
    { {
            try {
                $filePath = storage_path("app/public/pautas-final/" . $filename);

                if (!file_exists($filePath)) {
                    return abort(404, "Arquivo não encontrado");
                }

                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

                return response()->file($filePath, [
                    'Content-Type' => $mimeType
                ]);
            } catch (Exception $e) {
                Log::error("Erro ao acessar arquivo: " . $e->getMessage(), ['filename' => $filename]);

                abort(Response::HTTP_NOT_FOUND, 'Arquivo não encontrado.');
            }
        }
    }

    public function pautas_recurso($filename)
    { {
            try {
                $filePath = storage_path("app/public/pautas-recurso/" . $filename);

                if (!file_exists($filePath)) {
                    return abort(404, "Arquivo não encontrado");
                }

                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

                return response()->file($filePath, [
                    'Content-Type' => $mimeType
                ]);
            } catch (Exception $e) {
                Log::error("Erro ao acessar arquivo: " . $e->getMessage(), ['filename' => $filename]);

                abort(Response::HTTP_NOT_FOUND, 'Arquivo não encontrado.');
            }
        }
    }

    public function pautas_exame($filename)
    { {
            try {
                $filePath = storage_path("app/public/pautas-exame/" . $filename);


                if (!file_exists($filePath)) {
                    return abort(404, "Arquivo não encontrado");
                }

                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

                return response()->file($filePath, [
                    'Content-Type' => $mimeType
                ]);
            } catch (Exception $e) {
                Log::error("Erro ao acessar arquivo: " . $e->getMessage(), ['filename' => $filename]);

                abort(Response::HTTP_NOT_FOUND, 'Arquivo não encontrado.');
            }
        }
    }

    public function pautas_exame_especial($filename)
    { {
            try {
                $filePath = storage_path("app/public/pautas-exame-especial/" . $filename);


                if (!file_exists($filePath)) {
                    return abort(404, "Arquivo não encontrado");
                }

                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

                return response()->file($filePath, [
                    'Content-Type' => $mimeType
                ]);
            } catch (Exception $e) {
                Log::error("Erro ao acessar arquivo: " . $e->getMessage(), ['filename' => $filename]);

                abort(Response::HTTP_NOT_FOUND, 'Arquivo não encontrado.');
            }
        }
    }

    public function pautas_exame_oral($filename)
    { {
            try {
                $filePath = storage_path("app/public/pautas-exame-oral/" . $filename);


                if (!file_exists($filePath)) {
                    return abort(404, "Arquivo não encontrado");
                }

                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

                return response()->file($filePath, [
                    'Content-Type' => $mimeType
                ]);
            } catch (Exception $e) {
                Log::error("Erro ao acessar arquivo: " . $e->getMessage(), ['filename' => $filename]);

                abort(Response::HTTP_NOT_FOUND, 'Arquivo não encontrado.');
            }
        }
    }

    public function pautas_seminario($filename)
    { {
            try {
                $filePath = storage_path("app/public/pautas-seminario/" . $filename);


                if (!file_exists($filePath)) {
                    return $filename;
                }

                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

                return response()->file($filePath, [
                    'Content-Type' => $mimeType
                ]);
            } catch (Exception $e) {
                Log::error("Erro ao acessar arquivo: " . $e->getMessage(), ['filename' => $filename]);

                abort(Response::HTTP_NOT_FOUND, 'Arquivo não encontrado.');
            }
        }
    }


    public function pautas_tfc($filename)
    { {
            try {
                $filePath = storage_path("app/public/pautas-tfc/" . $filename);


                if (!file_exists($filePath)) {
                    return $filename;
                }

                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

                return response()->file($filePath, [
                    'Content-Type' => $mimeType
                ]);
            } catch (Exception $e) {
                Log::error("Erro ao acessar arquivo: " . $e->getMessage(), ['filename' => $filename]);

                abort(Response::HTTP_NOT_FOUND, 'Arquivo não encontrado.');
            }
        }
    }

    public function PF1($filename)
    { {
            try {
                $filePath = storage_path("app/public/pautas-mac/" . $filename);


                if (!file_exists($filePath)) {
                    return $filename;
                }

                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

                return response()->file($filePath, [
                    'Content-Type' => $mimeType
                ]);
            } catch (Exception $e) {
                Log::error("Erro ao acessar arquivo: " . $e->getMessage(), ['filename' => $filename]);

                abort(Response::HTTP_NOT_FOUND, 'Arquivo não encontrado.');
            }
        }
    }

    
    public function pautas_mac($filename)
    { {
            try {
                $filePath = storage_path("app/public/pautas-mac/" . $filename);


                if (!file_exists($filePath)) {
                    return $filename;
                }

                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

                return response()->file($filePath, [
                    'Content-Type' => $mimeType
                ]);
            } catch (Exception $e) {
                Log::error("Erro ao acessar arquivo: " . $e->getMessage(), ['filename' => $filename]);

                abort(Response::HTTP_NOT_FOUND, 'Arquivo não encontrado.');
            }
        }
    }

    public function exame_extraordinario($filename)
    { {
            try {
                $filePath = storage_path("app/public/pautas-exame-extraordinario/" . $filename);


                if (!file_exists($filePath)) {
                    return $filename;
                }

                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

                return response()->file($filePath, [
                    'Content-Type' => $mimeType
                ]);
            } catch (Exception $e) {
                Log::error("Erro ao acessar arquivo: " . $e->getMessage(), ['filename' => $filename]);

                abort(Response::HTTP_NOT_FOUND, 'Arquivo não encontrado.');
            }
        }
    }
}
