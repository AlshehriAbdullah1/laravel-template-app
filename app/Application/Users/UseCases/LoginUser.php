<?php
// UseCases/LoginUser.php
namespace App\Application\Users\UseCases;

use App\Application\Users\Contracts\PasswordHasher;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;

final class LoginUser {
    public function __construct(private UserRepositoryInterface $users, private PasswordHasher $hasher) {}
    /** @return array{userId:int} */
    public function execute(string $email, string $password): array {
        $user = $this->users->findByEmail($email);
        if (!$user || !$this->hasher->verify($password, $user->passwordHash)) {
            throw new AuthenticationException('Invalid credentials.');
        }
        return ['userId'=>$user->id];
    }
}
