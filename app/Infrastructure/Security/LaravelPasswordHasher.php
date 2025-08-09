<?php
// Security/LaravelPasswordHasher.php
namespace App\Infrastructure\Security;
use App\Application\Users\Contracts\PasswordHasher;
use Illuminate\Support\Facades\Hash;

class LaravelPasswordHasher implements PasswordHasher {
    public function hash(string $p): string { return Hash::make($p); }
    public function verify(string $p, string $h): bool { return Hash::check($p, $h); }
}
