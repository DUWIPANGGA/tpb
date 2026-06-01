<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

echo "Attempting to login...\n";

$credentials = ['name' => 'Deco', 'password' => '@Poli2307099'];
$user = null;
$guard = null;

if (Auth::guard('admin')->attempt($credentials)) {
    echo "Logged in as Admin\n";
    $user = Auth::guard('admin')->user();
    $guard = 'admin';
} elseif (Auth::guard('ormawa')->attempt($credentials)) {
    echo "Logged in as Ormawa\n";
    $user = Auth::guard('ormawa')->user();
    $guard = 'ormawa';
} else {
    echo "Login failed!\n";
    exit(1);
}

// Generate token for API
$token = null;
if ($guard === 'ormawa') {
    $token = $user->createToken('test-token')->plainTextToken;
    echo "Generated API Token: $token\n";
}

$routes = Route::getRoutes();

$results = [];

foreach ($routes as $route) {
    if (!in_array('GET', $route->methods())) {
        continue; // Only test GET routes easily
    }

    $uri = $route->uri();
    
    // Skip routes with parameters for this automated test unless we can easily fake them
    if (strpos($uri, '{') !== false) {
        $uri = str_replace(['{id}', '{data_barang}', '{data_kategori}', '{satuan}', '{admin}', '{mahasiswa}', '{nama_barang}'], '1', $uri);
        // Sometimes we need a string
        $uri = str_replace('{permohonanId}', '1', $uri);
    }

    echo "Testing /$uri ... ";

    $req = Illuminate\Http\Request::create("/$uri", 'GET');
    
    if (strpos($uri, 'api/') === 0 && $token) {
        $req->headers->set('Authorization', "Bearer $token");
        $req->headers->set('Accept', 'application/json');
    } else {
        $app['auth']->guard($guard)->setUser($user);
        $req->setLaravelSession($app['session']->driver());
    }

    try {
        $resp = $kernel->handle($req);
        $status = $resp->getStatusCode();
        
        if ($status >= 500) {
            echo "FAILED ($status)\n";
            $results[] = "[$status] /$uri";
        } else {
            echo "OK ($status)\n";
        }
    } catch (\Exception $e) {
        echo "EXCEPTION: " . $e->getMessage() . "\n";
        $results[] = "[EXC] /$uri - " . $e->getMessage();
    }
}

echo "\n--- Summary ---\n";
if (empty($results)) {
    echo "All GET routes returned non-500 status codes!\n";
} else {
    echo "Failed routes:\n";
    foreach ($results as $res) {
        echo "$res\n";
    }
}

