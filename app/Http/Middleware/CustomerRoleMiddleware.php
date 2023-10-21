<?php

// app/Filament/Middleware/CustomerRoleMiddleware.php

namespace App\Filament\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Middleware\Middleware;
use Illuminate\Support\Facades\Auth;

class CustomerRoleMiddleware extends Middleware

{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user->role->name === 'Customer') {
            return $next($request);
        }

        return redirect()->route('access-denied'); // Redirect to an access denied page
    }
}

