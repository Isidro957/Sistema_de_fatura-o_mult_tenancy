<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ResolveTenant;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\Tenant\ProdutoController;
use App\Http\Controllers\Tenant\CategoriaController;
use App\Http\Controllers\Tenant\FornecedorController;
use App\Http\Controllers\Tenant\CompraController;
use App\Http\Controllers\Tenant\VendaController;
use App\Http\Controllers\Tenant\PagamentoController;
use App\Http\Controllers\Tenant\MovimentoStockController;
use App\Http\Controllers\Tenant\FaturaController;
use App\Http\Controllers\TenantUserController;

/*
|--------------------------------------------------------------------------
| Rotas do Tenant
|--------------------------------------------------------------------------
|
| Todas as rotas aqui são para tenants específicos.
| O middleware ResolveTenantFromHeader garante que o tenant
| seja inicializado usando o X-Tenant no header.
|
*/

Route::middleware([ResolveTenant::class])->prefix('tenant')->group(function () {

    // Dashboard / Info
    Route::middleware('auth:sanctum')->get('/info', function () {
        return response()->json([
            'tenant' => app('tenant'),
            'user' => request()->user(),
        ]);
    });

    // Auth tenant
    Route::prefix('auth')->group(function () {
        Route::post('/login', [ApiAuthController::class, 'login']);
        Route::post('/register', [ApiAuthController::class, 'register']);
        Route::middleware('auth:sanctum')->post('/logout', [ApiAuthController::class, 'logout']);
    });

    // Rotas protegidas por auth
    Route::middleware(['auth:sanctum', 'tenant.user'])->group(function () {

        // CRUD Usuários (somente admin)
        Route::middleware(['role:admin'])->group(function () {
            Route::apiResource('/users', TenantUserController::class);
        });

        // CRUD Produtos (admin e operador)
        Route::middleware(['role:admin,operador'])->group(function () {
            Route::apiResource('/produtos', ProdutoController::class);
        });

        // CRUD Categorias (admin e operador)
        Route::middleware(['role:admin,operador'])->group(function () {
            Route::apiResource('/categorias', CategoriaController::class);
        });

        // CRUD Fornecedores (admin e operador)
        Route::middleware(['role:admin,operador'])->group(function () {
            Route::apiResource('/fornecedores', FornecedorController::class);
        });

        // Compras (admin e operador)
        Route::middleware(['role:admin,operador'])->post('/compras', [CompraController::class, 'store']);

        // Vendas (admin, operador e caixa)
        Route::middleware(['role:admin,operador,caixa'])->post('/vendas', [VendaController::class, 'store']);

        // Pagamentos (admin, operador e caixa)
        Route::middleware(['role:admin,operador,caixa'])->group(function () {
            Route::apiResource('/pagamentos', PagamentoController::class);
        });

        // Movimentos de stock (admin e operador)
        Route::middleware(['role:admin,operador'])->group(function () {
            Route::apiResource('/movimentos-stock', MovimentoStockController::class);
        });

        // Faturas (admin, operador e caixa)
        Route::middleware(['role:admin,operador,caixa'])->group(function () {
            Route::get('/faturas', [FaturaController::class, 'index']);
            Route::post('/faturas/gerar', [FaturaController::class, 'gerarFatura']);
        });

    });
});
