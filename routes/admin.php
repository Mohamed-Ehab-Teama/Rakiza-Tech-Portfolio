<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\PasswordResetController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HelpController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\MetricController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SkillCategoryController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebsiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard routes  (loaded in bootstrap/app.php)
|--------------------------------------------------------------------------
| Every URL here starts with /admin and every name with "admin.".
| Who may do what is checked inside each controller (permissions such as
| "projects.update"); Super Admins pass every check.
*/

// --- Signing in (only for visitors who are not signed in) ------------------
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');

    Route::get('forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'email'])
        ->middleware('throttle:5,10')
        ->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

// --- The dashboard (signed-in, active users) -------------------------------
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('help', HelpController::class)->name('help');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Content types that share one controller design (see Admin\ResourceController):
    // list, create, edit, delete, publish switch and drag & drop order.
    $resources = [
        'projects' => ProjectController::class,
        'websites' => WebsiteController::class,
        'services' => ServiceController::class,
        'skill-categories' => SkillCategoryController::class,
        'skills' => SkillController::class,
        'metrics' => MetricController::class,
    ];
    foreach ($resources as $uri => $controller) {
        $name = str_replace('-', '_', $uri);
        Route::post("{$uri}/reorder", [$controller, 'reorder'])->name("{$name}.reorder");
        Route::patch("{$uri}/{id}/publish", [$controller, 'publish'])->whereNumber('id')->name("{$name}.publish");
        Route::resource($uri, $controller)
            ->except('show')
            ->parameters([$uri => 'id'])
            ->names($name);
    }

    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('messages/read-all', [MessageController::class, 'markAllRead'])->name('messages.read-all');
    Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::patch('messages/{message}/toggle-read', [MessageController::class, 'toggleRead'])->name('messages.toggle-read');
    Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    Route::resource('users', UserController::class)->except('show');
    Route::resource('roles', RoleController::class)->except('show');

    Route::get('settings/{group?}', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings/{group}', [SettingController::class, 'update'])->name('settings.update');

    Route::get('activity', ActivityController::class)->name('activity.index');
});
