<?php 
 // Repositories/UserRepositoryInterface.php
namespace App\Domain\Users\Repositories;

use App\Domain\Users\Entities\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface {
    public function create(User $user): User;
    public function findByEmail(string $email): ?User;
    public function findById(int $id): ?User;
    public function paginate(int $perPage=15): LengthAwarePaginator;
}
    