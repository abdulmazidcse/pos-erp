<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [HomeController::class, 'index']);
Route::get('/test-balance-sheet', [HomeController::class, 'testBalanceSheet']);
Route::get('/test-profit-loss', [HomeController::class, 'testProfitLoss']);
Route::get('/test-trial-balance', [HomeController::class, 'testTrialBalance']);
Route::get('/delete-user', [HomeController::class, 'deleteUser']);
Route::post('/delete-user', [HomeController::class, 'deletedUser']);

Route::get('/foo', function () {
    Artisan::call('storage:link');
    echo '<h1>Cache storage cleared</h1>'; 
}); 

Route::get('/{action}', function ($action) {
    $supportedActions = ['optimize', 'clear-all', 'clear', 'reset'];
    if (in_array($action, $supportedActions)) {
        $commands = [
            'optimize' => 'optimize:clear',
            'clear-all' => 'optimize:clear',
            'clear' => 'cache:clear',
            'reset' => 'cache:clear',
        ];
        Artisan::call($commands[$action]);
        echo  '<h1>Cached events cleared!<br>
        Compiled views cleared!<br>
        Application cache cleared!<br>
        Route cache cleared!<br>
        Configuration cache cleared!<br>
        Compiled services and packages files removed!<br>
        Caches cleared successfully!</h1>
        <h1>Action performed successfully!</h1>';
    } else { 
        abort(404); // Return a 404 error for unsupported actions
    }
});