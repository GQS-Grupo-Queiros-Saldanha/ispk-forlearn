<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Toastr;

class InstituicaoArquivoController extends Controller
{ 
    public function getArquivo($filename)
    {
        try {
            // Caminho do arquivo dentro do storage
            $filePath = storage_path("app/public/attachment/" . $filename);

            // Verifica se o arquivo existe
            if (!file_exists($filePath)) {
               abort(404, "Arquivo não encontrado");
            }

            // Obtém o tipo MIME do arquivo
            $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

            // Retorna o arquivo como resposta
            return response()->file($filePath, ['Content-Type' => $mimeType]);
        } catch (\Exception $e) {
            // Registra o erro no log
            Log::error("Erro ao acessar arquivo: " . $e->getMessage(), ['filename' => $filename]);

           abort(404, "Arquivo não encontrado");
        }
    }
}
