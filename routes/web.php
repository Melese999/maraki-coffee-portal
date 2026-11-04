<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\LoanPaymentController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/locale/{locale}', LocaleController::class)->name('locale.switch');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('/sales', [SaleController::class, 'index'])->middleware('permission:sales.view')->name('sales.index');
    Route::post('/sales', [SaleController::class, 'store'])->middleware('permission:sales.create')->name('sales.store');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->middleware('permission:sales.delete')->name('sales.destroy');

    Route::get('/expenses', [ExpenseController::class, 'index'])->middleware('permission:expenses.view')->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->middleware('permission:expenses.create')->name('expenses.store');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->middleware('permission:expenses.delete')->name('expenses.destroy');

    Route::get('/menu', [MenuItemController::class, 'index'])->middleware('permission:menu.view')->name('menu-items.index');
    Route::get('/menu/print', [MenuItemController::class, 'print'])->middleware('permission:menu.view')->name('menu-items.print');
    Route::post('/menu', [MenuItemController::class, 'store'])->middleware('permission:menu.create')->name('menu-items.store');
    Route::put('/menu/{menuItem}', [MenuItemController::class, 'update'])->middleware('permission:menu.update')->name('menu-items.update');
    Route::delete('/menu/{menuItem}', [MenuItemController::class, 'destroy'])->middleware('permission:menu.delete')->name('menu-items.destroy');

    Route::get('/customers', [CustomerController::class, 'index'])->middleware('permission:customers.view')->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->middleware('permission:customers.create')->name('customers.store');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->middleware('permission:customers.update')->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->middleware('permission:customers.delete')->name('customers.destroy');

    Route::get('/credit', [CreditController::class, 'index'])->middleware('permission:credit.view')->name('credit.index');
    Route::post('/credit/payments', [CreditController::class, 'store'])->middleware('permission:credit.create')->name('credit.payments.store');

    Route::get('/investments', [InvestmentController::class, 'index'])->middleware('permission:investments.view')->name('investments.index');
    Route::post('/investments', [InvestmentController::class, 'store'])->middleware('permission:investments.create')->name('investments.store');
    Route::get('/investments/{investment}', [InvestmentController::class, 'show'])->middleware('permission:investments.view')->name('investments.show');
    Route::delete('/investments/{investment}', [InvestmentController::class, 'destroy'])->middleware('permission:investments.delete')->name('investments.destroy');
    Route::post('/investors', [InvestorController::class, 'store'])->middleware('permission:investments.create')->name('investors.store');
    Route::post('/investments/{investment}/payments', [LoanPaymentController::class, 'store'])->middleware('permission:investments.update')->name('loan-payments.store');

    Route::get('/reports', [ReportController::class, 'index'])->middleware('permission:reports.view')->name('reports.index');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->middleware('permission:users.view')->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:users.update')->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('users.destroy');

        Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:roles.view')->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:roles.create')->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles.update')->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('roles.destroy');

        Route::get('/activity', [ActivityLogController::class, 'index'])->middleware('permission:activity.view')->name('activity.index');

        Route::get('/categories', [ExpenseCategoryController::class, 'index'])->middleware('permission:categories.manage')->name('categories.index');
        Route::post('/categories', [ExpenseCategoryController::class, 'store'])->middleware('permission:categories.manage')->name('categories.store');
        Route::put('/categories/{category}', [ExpenseCategoryController::class, 'update'])->middleware('permission:categories.manage')->name('categories.update');
        Route::delete('/categories/{category}', [ExpenseCategoryController::class, 'destroy'])->middleware('permission:categories.manage')->name('categories.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
