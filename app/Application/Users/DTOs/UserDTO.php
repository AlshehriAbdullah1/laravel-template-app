<?php
// DTOs/UserDTO.php
namespace App\Application\Users\DTOs;
final class UserDTO { public function __construct(public int $id, public string $name, public string $email) {} }
