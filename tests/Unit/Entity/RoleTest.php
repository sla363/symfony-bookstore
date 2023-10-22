<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Role;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function testRoleAddRemoveUser(): void
    {
        $role = (new Role())
            ->setId(123);
        $user = (new User())
            ->setEmail('hello@sir.com');
        $role->addUser($user);
        $this->assertEquals($user, $role->getUsers()->first());

        $secondUser = (new User())
            ->setEmail('ceo@mycorp.org');
        $role->addUser($secondUser);
        $users = $role->getUsers();
        $this->assertEquals($secondUser, $users->next());

        $role->removeUser($user);
        $this->assertEquals($secondUser, $role->getUsers()->first());
        $role->removeUser($secondUser);
        $this->assertEmpty($role->getUsers());
    }
}