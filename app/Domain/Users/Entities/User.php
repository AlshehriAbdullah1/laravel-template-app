<?php

// Entities/User.php
namespace App\Domain\Users\Entities;

final class User {
    public function __construct(
        public readonly ?int $id,
        public string $name,
        public string $email,
        public string $passwordHash
    ) {}
    public static function register(string $name, string $email, string $hash): self {
        return new self(null, $name, $email, $hash);
    }
}
