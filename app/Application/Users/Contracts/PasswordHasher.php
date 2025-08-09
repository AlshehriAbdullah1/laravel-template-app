<?php
// Contracts/PasswordHasher.php
namespace App\Application\Users\Contracts;
interface PasswordHasher { public function hash(string $p): string; public function verify(string $p,string $h): bool; }
