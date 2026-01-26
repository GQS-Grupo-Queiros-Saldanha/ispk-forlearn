<?php
namespace App\Controllers;

use App\Models\Programer;

class ProgramersController extends Controller {

    // Lista usuários - GET /usuarios
    public function index() {
        $usuario = auth()-user()->id;
        
        // Renderiza a view passando os dados
        return $this->view('programers/index', ['usuarios' => $usuarios]);
    }

}
