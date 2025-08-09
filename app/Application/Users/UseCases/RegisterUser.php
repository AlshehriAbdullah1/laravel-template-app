<?php
// UseCases/RegisterUser.php
namespace App\Application\Users\UseCases;

use App\Application\Users\Contracts\PasswordHasher;
use App\Application\Users\DTOs\UserDTO;
use App\Domain\Users\Entities\User as DomainUser;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class RegisterUser {
    public function __construct(private UserRepositoryInterface $users, private PasswordHasher $hasher) {}
    public function execute(string $name, string $email, string $password): UserDTO {
        return DB::transaction(function () use ($name,$email,$password) {
            $domain = DomainUser::register($name,$email,$this->hasher->hash($password));
            $saved  = $this->users->create($domain);
            return new UserDTO($saved->id, $saved->name, $saved->email);
        });
    }
}
