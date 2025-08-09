<?php
// Providers/InfrastructureServiceProvider.php
namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\UserRepository;
use App\Application\Users\Contracts\PasswordHasher;
use App\Infrastructure\Security\LaravelPasswordHasher;

class InfrastructureServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PasswordHasher::class, LaravelPasswordHasher::class);
    }
}
