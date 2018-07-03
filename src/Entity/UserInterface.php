<?php

namespace App\Entity;

use Sylius\Component\User\Model\UserInterface as BaseUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

interface UserInterface extends BaseUserInterface, EquatableInterface
{
    public const DEFAULT_ADMIN_ROLE = 'ROLE_ADMIN';

    /**
     * @return string
     */
    public function getFirstName(): ?string;

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void;

    /**
     * @return string
     */
    public function getLastName(): ?string;

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void;
}