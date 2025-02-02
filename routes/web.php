<?php

use App\Http\Controllers\BotController;
use App\Imports\ResidentsImport;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/webhook', [BotController::class, 'setWebhook']);
Route::post('/webhook', [BotController::class, 'handleWebhook']);
Route::get('/debug', [BotController::class, 'handleWebhook']);
Route::post('1001/import/residents', function () {
    request()->validate(['file' => 'required|file|mimes:xlsx,xls']);
    Excel::import(new ResidentsImport, request()->file('file'));
    \Filament\Notifications\Notification::make()
    ->title('Успех')
    ->body('Жители были успешно импортированы.')
    ->success()
    ->send();
    return back(302, ['success' =>'Products imported successfully!'] );
})->name('import.residents');