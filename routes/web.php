<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\Aggregator\AggregatorDashboardController;
//
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AffiliationController;
use App\Http\Controllers\Admin\PlantController;
use App\Http\Controllers\Admin\FarmersController;
use App\Http\Controllers\Admin\HVCDPController;
use App\Http\Controllers\Admin\CountController;
use App\Http\Controllers\Admin\MonthlyInventoryController;



use App\Http\Controllers\Auth\LoginController;
//
use App\Http\Controllers\Aggregator\AggregatorFarmersController;
use App\Http\Controllers\Aggregator\RecordController;
use App\Http\Controllers\Aggregator\AggregatorCountController;
use App\Http\Controllers\Aggregator\AffiliateController;
use App\Http\Controllers\Aggregator\PlantsController;

use App\Http\Controllers\User\UserEncodeController;
use App\Http\Controllers\User\UserPlantController;





/*
|----------------------------------------------------------------------|
| Web Routes                                                           |
|----------------------------------------------------------------------|
*/

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::controller(LoginController::class)->group(function() {
    Route::get('login', 'showLoginForm')->name('login')->middleware('auth.redirect');
    Route::post('login', 'login');
    Route::post('logout', 'logout')->name('logout')->middleware('auth');
});


Auth::routes();
// Admin routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth','role:Admin']], function() {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/export-plant-summary', [DashboardController::class, 'exportPlantSummary'])->name('admin.exportPlantSummary');



    // Role management
    Route::prefix('roles')->name('admin.roles.')->group(function() {
        Route::get('/create', [RoleController::class, 'createUser'])->name('createUser');
        Route::post('/store', [RoleController::class, 'storeUser'])->name('storeUser');
        Route::get('/{id}', [RoleController::class, 'viewUser'])->name('viewUser');
        Route::get('/{id}/edit', [RoleController::class, 'editUser'])->name('editUser');
        Route::put('/{id}', [RoleController::class, 'updateUser'])->name('updateUser');
        Route::delete('/{id}', [RoleController::class, 'deleteUser'])->name('deleteUser');
        
    });

    Route::resource('admin/affiliations', AffiliationController::class);

    // Affiliation management
    Route::prefix('affiliations')->name('admin.affiliations.')->group(function() {
        Route::get('/', [AffiliationController::class, 'index'])->name('index');
        Route::post('/', [AffiliationController::class, 'store'])->name('store');
        Route::put('/{id}', [AffiliationController::class, 'update'])->name('update');
        Route::delete('/{id}', [AffiliationController::class, 'destroy'])->name('destroy');

        
    });

    // Plant management
    Route::prefix('plants')->name('admin.plants.')->group(function() {
        Route::get('/', [PlantController::class, 'index'])->name('index');
        Route::post('/', [PlantController::class, 'store'])->name('store');
        Route::delete('/{plant}', [PlantController::class, 'destroy'])->name('destroy');
        Route::put('/{plant}', [PlantController::class, 'update'])->name('update');

    });

    // Farmer management
    Route::prefix('farmers')->name('admin.farmers.')->group(function() {
        Route::get('/', [FarmersController::class, 'index'])->name('index');
        Route::get('/create', [FarmersController::class, 'create'])->name('create');
        Route::post('/', [FarmersController::class, 'store'])->name('store');
        Route::get('/{id}', [FarmersController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [FarmersController::class, 'edit'])->name('edit');
        Route::put('/{id}', [FarmersController::class, 'update'])->name('update');
        Route::delete('/{id}', [FarmersController::class, 'destroy'])->name('destroy');

        // web.php
        Route::get('/filter', [FarmersController::class, 'filter'])->name('filter');
        Route::get('/get-associations/{barangay}', [FarmersController::class, 'getAssociations']);



    });


    // HVCDP management
    Route::prefix('hvcdp')->name('admin.hvcdp.')->group(function() {
        Route::get('/count', [HVCDPController::class, 'index'])->name('index');
        Route::get('/count/{id}', [HVCDPController::class, 'show'])->name('show');
        Route::get('/count/create', [HVCDPController::class, 'create'])->name('create');
        Route::post('/count', [HVCDPController::class, 'store'])->name('store');
        Route::get('/count/{id}/edit', [HVCDPController::class, 'edit'])->name('edit');
        Route::put('/count/{id}', [HVCDPController::class, 'update'])->name('update');
        Route::delete('/count/{id}', [HVCDPController::class, 'destroy'])->name('destroy');

        Route::get('/print', [HVCDPController::class, 'print'])->name('print');
        Route::get('/export-excel', [HVCDPController::class, 'exportBarangay'])->name('exportExcel');
        Route::post('extract-text', [YourController::class, 'extractTextFromImage']);

    });

    Route::prefix('hvcdp')->name('admin.count.')->group(function() {
        Route::get('/', [CountController::class, 'count'])->name('count');
        Route::post('/', [CountController::class, 'store'])->name('count.store');
        Route::get('/{id}/edit', [CountController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CountController::class, 'update'])->name('update');
        Route::delete('/{id}', [CountController::class, 'destroy'])->name('destroy');
        

    });

    Route::resource('inventories', MonthlyInventoryController::class);

    Route::prefix('inventory')->name('admin.inventory.')->group(function() {
        Route::get('/', [MonthlyInventoryController::class, 'index'])->name('index');
        Route::get('/create', [MonthlyInventoryController::class, 'create'])->name('create');
        Route::post('/', [MonthlyInventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}/edit', [MonthlyInventoryController::class, 'edit'])->name('edit');
        
        Route::put('{inventory}', [MonthlyInventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [MonthlyInventoryController::class, 'destroy'])->name('destroy');

        //EXPORTEXCEL
        Route::get('/inventory', [MonthlyInventoryController::class, 'print'])->name('printMonthlyInventory');
        // Export to Excel
        Route::get('export-monthly-inventory-excel/{month}/{year}', [MonthlyInventoryController::class, 'exportMonthlyInventoryExcel'])
            ->name('exportMonthlyInventoryExcel');


        // Route para sa pag-view ng history records
        Route::get('/history', [MonthlyInventoryController::class, 'showHistory'])->name('history');
        Route::delete('history/{id}', [MonthlyInventoryController::class, 'delete'])->name('delete');
        // VIEW EXCEL BEFORE DOWNLOADING
        Route::get('preview/{month}/{year}', [MonthlyInventoryController::class, 'previewMonthlyInventory'])->name('previewMonthlyInventory');
        Route::get('/fetch-inventory', [MonthlyInventoryController::class, 'fetchInventory']);
        
        // records previous
        Route::get('previewHistory/{month}/{year}', [MonthlyInventoryController::class, 'previewHistory'])->name('previewHistory');
        Route::get('exportHistory/{month}/{year}', [MonthlyInventoryController::class, 'exportHistory'])->name('exportHistory');
        

    });

    
    
});



// Aggregator routes 
Route::group(['prefix' => 'aggregator', 'middleware' => ['auth','role:Aggregator']], function() {
    Route::get('/dashboard', [AggregatorDashboardController::class, 'index'])->name('aggregator.dashboard');

    // Farmer management
    Route::prefix('farmers')->name('aggregator.farmers.')->group(function() {
        Route::get('/', [AggregatorFarmersController::class, 'index'])->name('index');
        Route::get('/create', [AggregatorFarmersController::class, 'create'])->name('create');
        Route::post('/', [AggregatorFarmersController::class, 'store'])->name('store');
        Route::get('/{id}', [AggregatorFarmersController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AggregatorFarmersController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AggregatorFarmersController::class, 'update'])->name('update');
        Route::delete('/{id}', [AggregatorFarmersController::class, 'destroy'])->name('destroy');
    });

    // HVCDP management
    Route::prefix('hvcdp')->name('aggregator.hvcdp.')->group(function() {
        Route::get('/count', [RecordController::class, 'index'])->name('index');
        Route::get('/count/{id}', [RecordController::class, 'show'])->name('show');
        Route::get('/count/create', [RecordController::class, 'create'])->name('create');
        Route::post('/count', [RecordController::class, 'store'])->name('store');
        Route::get('/count/{id}/edit', [RecordController::class, 'edit'])->name('edit');
        Route::put('/count/{id}', [RecordController::class, 'update'])->name('update');
        Route::delete('/count/{id}', [RecordController::class, 'destroy'])->name('destroy');

        Route::get('/print', [RecordController::class, 'print'])->name('print');
        Route::get('/export-excel', [RecordController::class, 'exportBarangay'])->name('exportExcel');

    });

    Route::prefix('hvcdp')->name('aggregator.count.')->group(function() {
        Route::get('/', [AggregatorCountController::class, 'count'])->name('count');
        Route::post('/', [AggregatorCountController::class, 'store'])->name('store');

        Route::get('/{id}/edit', [AggregatorCountController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AggregatorCountController::class, 'update'])->name('update');
        Route::delete('/{id}', [AggregatorCountController::class, 'destroy'])->name('destroy');

    });

    // Affiliation management
    Route::prefix('affiliations')->name('affiliations.')->group(function() {
        Route::get('/', [AffiliateController::class, 'index'])->name('index');
        Route::post('/', [AffiliateController::class, 'store'])->name('store');
        Route::put('/{id}', [AffiliateController::class, 'update'])->name('update');
        Route::delete('/{id}', [AffiliateController::class, 'destroy'])->name('destroy');

    });

    // Plant management
    Route::prefix('plants')->name('aggregator.plants.')->group(function() {
        Route::get('/', [PlantsController::class, 'index'])->name('index');
        Route::post('/', [PlantsController::class, 'store'])->name('store');
        Route::delete('/{plant}', [PlantsController::class, 'destroy'])->name('destroy');
        Route::put('/{plant}', [PlantsController::class, 'update'])->name('update');
    });
});


Route::group(['prefix' => 'user', 'middleware' => ['auth', 'role:User']], function() {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

    Route::prefix('hvcdp')->name('user.count.')->group(function() {
        Route::get('/', [UserEncodeController::class, 'count'])->name('count');
        Route::post('/', [UserEncodeController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserEncodeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserEncodeController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserEncodeController::class, 'destroy'])->name('destroy');
        // In web.php
        Route::get('/fetch-farmers', [UserEncodeController::class, 'fetchFarmers'])->name('fetch.farmers');
        Route::post('bulk-destroy', [UserEncodeController::class, 'bulkDestroy'])->name('bulkDestroy');


    });

    // Plant management
    Route::prefix('plants')->name('user.plants.')->group(function() {
        Route::get('/', [UserPlantController::class, 'index'])->name('index');
        Route::post('/', [UserPlantController::class, 'store'])->name('store');
        Route::delete('/{plant}', [UserPlantController::class, 'destroy'])->name('destroy');
        Route::put('/{plant}', [UserPlantController::class, 'update'])->name('update');

    });
});


