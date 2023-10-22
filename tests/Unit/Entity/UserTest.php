<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Order;
use App\Entity\Role;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserAddRemoveRole(): void
    {
        $user = (new User())
            ->setId(234);
        $role = (new Role())
            ->setName('It\'s not my name!')
            ->setId(25);
        $user->addRole($role);
        $this->assertEquals($role->getName(), $user->getRoles()[0]);

        $secondRole = (new Role())
            ->setName('Ruler of the world')
            ->setId(1);
        $user->addRole($secondRole);
        $this->assertEquals($secondRole->getName(), $user->getRoles()[1]);

        $user->removeRole($secondRole);
        $this->assertEquals($role->getName(), $user->getRoles()[0]);
        $user->removeRole($role);
        $this->assertEmpty($user->getRoles());
    }

    public function testUserGetRoles(): void
    {
        $user = new User();
        $role = (new Role())
            ->setName('Role skater');
        $secondRole = (new Role())
            ->setName('Hello');
        $user->addRole($role);
        $user->addRole($secondRole);

        $this->assertEquals($role->getName(), $user->getRoles()[0]);
        $this->assertEquals($secondRole->getName(), $user->getRoles()[1]);
    }

    public function testUserAddOrder(): void
    {
        $user = new User();
        $order = (new Order())
            ->setOrderNumber('5');
        $user->addOrder($order);
        $this->assertEquals($order, $user->getOrders()->first());

        $secondOrder = (new Order)
            ->setOrderNumber('25');
        $user->addOrder($secondOrder);
        $this->assertEquals($secondOrder, $user->getOrders()->next());
    }
}