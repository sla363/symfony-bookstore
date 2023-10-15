<?php

namespace App\DTO;

class ChangePasswordDTO
{
    public ?string $password = null;

    public function getPassword(): ?string
    {
        return $this->password;
    }
}