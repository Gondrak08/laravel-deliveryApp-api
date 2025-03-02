<?php
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

public function render($request, Throwable $exception)
{
    // Sempre retornar JSON para erros de autenticação
    if ($exception instanceof AuthenticationException) {
        return response()->json(['error' => 'Não autenticado.'], 401);
    }

    // Retorna JSON caso a rota não seja encontrada
    if ($exception instanceof NotFoundHttpException) {
        return response()->json(['error' => 'Rota não encontrada.'], 404);
    }

    return parent::render($request, $exception);
}
