<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        Gate::define('media_model_type_ownership', function ($user, Media $media) {
            $model = $media->model;
            if ($model instanceof Ticket || $model instanceof Comment) {
                return $model->user_id === $user->id;
            }
            return false;
        });
    }
}
