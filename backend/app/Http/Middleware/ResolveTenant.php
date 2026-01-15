<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Tenant;

class ResolveTenant
{
    public function handle(Request $request, Closure $next)
    {
        // Ignorar rotas públicas
        if (
            $request->is('sanctum/csrf-cookie') ||
            $request->is('api/login') ||
            $request->is('api/register')
        ) {
            return $next($request);
        }

        // Pegar tenant do header
        $tenantId = $request->header('X-Tenant');

        if (! $tenantId) {
            abort(400, 'Tenant não informado.');
        }

        // Buscar no banco de tenants
        $tenant = Tenant::where('id', $tenantId)->first();

        if (! $tenant) {
            abort(404, 'Tenant não encontrado.');
        }

        // Inicializa tenancy
        tenancy()->initialize($tenant);

        return $next($request);
    }
}
