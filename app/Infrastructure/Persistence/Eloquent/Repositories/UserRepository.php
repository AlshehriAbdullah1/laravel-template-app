<?php
// Repositories/UserRepository.php
namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Users\Entities\User as DomainUser;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class UserRepository implements UserRepositoryInterface {
    private function toDomain(UserModel $m): DomainUser {
        return new DomainUser($m->id, $m->name, $m->email, $m->password);
    }
    public function create(DomainUser $u): DomainUser {
        $m = UserModel::create(['name'=>$u->name,'email'=>$u->email,'password'=>$u->passwordHash]);
        return $this->toDomain($m);
    }
    public function findByEmail(string $email): ?DomainUser {
        $m = UserModel::where('email',$email)->first();
        return $m ? $this->toDomain($m) : null;
    }
    public function findById(int $id): ?DomainUser {
        $m = UserModel::find($id);
        return $m ? $this->toDomain($m) : null;
    }
    public function paginate(int $perPage=15): LengthAwarePaginator {
        return QueryBuilder::for(UserModel::query())
            ->allowedFilters([AllowedFilter::partial('name'), AllowedFilter::exact('email')])
            ->allowedSorts(['name','email','created_at'])
            ->paginate($perPage)
            ->appends(request()->query());
    }
}
