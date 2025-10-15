<?php

namespace App\Modules\Payments\Controllers;

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
use App\Modules\Payments\Models\TransactionReceipt;
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

    public function historic_credit($filename)
    { {
            try {
                $filePath = storage_path("app/public/receipts/" . $filename);


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

    public function attachment($filename)
    { {
            try {
                $filePath = storage_path("app/public/attachment/" . $filename);


                if (!file_exists($filePath)) {
                    return ("Arquivo não encontrado");
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

    public function receipts($filename)
    { {
        $receipt = TransactionReceipt::where('transaction_id', $filename)->first();

        // Se não encontrar o registro no banco, retorna 404 antes do try-catch
        if (!$receipt || empty($receipt->path)) {
            abort(404, 'Arquivo não encontrado no banco de dados.');
        }
        
        try {
            $filePath = storage_path("app/public/" . substr($receipt->path, 9));
        
            // Se o arquivo não existir no sistema de arquivos, retorna 404 antes do try-catch
            if (!file_exists($filePath)) {
                abort(404, 'Arquivo não encontrado no servidor.');
            }
        
            // Obtém o tipo de arquivo (MIME) de forma segura
            $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        
            return response()->file($filePath, [
                'Content-Type' => $mimeType
            ]);
        } catch (\Throwable $e) { // Captura apenas erros inesperados
            // Registra o erro no log para depuração
            Log::error('Erro ao processar arquivo:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        
            // Retorna erro 500 apenas para falhas inesperadas
            abort(500, 'Erro interno ao processar o arquivo.');
        }
        }
    }

    public function receipts2($filename)
    { {
        // Se não encontrar o registro no banco, retorna 404 antes do try-catch
      
        try {
            $filePath = storage_path("app/public/receipts/" . $filename);
        
            // Se o arquivo não existir no sistema de arquivos, retorna 404 antes do try-catch
            if (!file_exists($filePath)) {
                abort(404, 'Arquivo não encontrado no servidor.');
            }
        
            // Obtém o tipo de arquivo (MIME) de forma segura
            $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        
            return response()->file($filePath, [
                'Content-Type' => $mimeType
            ]);
        } catch (\Throwable $e) { // Captura apenas erros inesperados
            // Registra o erro no log para depuração
            Log::error('Erro ao processar arquivo:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        
            // Retorna erro 500 apenas para falhas inesperadas
            abort(500, 'Erro interno ao processar o arquivo.');
        }
        }
    }

    public function documento_userRH($filename)
    { {
            try {
                $filePath = storage_path("app/public/documento_userRH/" . $filename);


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
