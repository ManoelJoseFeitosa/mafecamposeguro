<?php

namespace App\Http\Controladores;

use App\Servicos\CatalogoServico;
use Illuminate\Http\JsonResponse;

class CatalogoControlador extends Controlador
{
    public function divisoes(): JsonResponse
    {
        return response()->json(array_keys(CatalogoServico::DIVISOES));
    }

    public function atividades(): JsonResponse
    {
        return response()->json(CatalogoServico::ATIVIDADES);
    }

    public function ambientes(): JsonResponse
    {
        return response()->json(CatalogoServico::AMBIENTES);
    }
}
