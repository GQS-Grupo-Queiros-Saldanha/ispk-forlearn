<?php

namespace App\Modules\Users\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Toastr;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
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

    public function getAvatar($filename)
    { 
            try {
                /*   if (!$user || $user->avatar !== $filename) {
            abort(403, 'Acesso negado');
        } */
                $path = storage_path("app/public/attachment/{$filename}");

                if (!file_exists($path)) {
                    Log::error("Arquivo não encontrado: {$filename}");
                    $path = storage_path("app/public/avatars/pngwing.com.png");
                }

                return response()->file($path);
            } catch (\Exception $e) {
                Log::error("Erro ao buscar avatar: " . $e->getMessage());
                abort(500, 'Erro interno ao carregar a imagem');
            }
        
    }
}
