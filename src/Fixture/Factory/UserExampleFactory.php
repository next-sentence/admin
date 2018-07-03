<?php

declare(strict_types=1);

namespace App\Fixture\Factory;

use App\Entity\UserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserExampleFactory extends AbstractExampleFactory
{
    /**
     * @var FactoryInterface
     */
    private $userFactory;
    /**
     * @var \Faker\Generator
     */
    private $faker;
    /**
     * @var OptionsResolver
     */
    private $optionsResolver;
    /**
     * @var string
     */
    private $localeCode;

    /**
     * @param FactoryInterface $userFactory
     */
    public function __construct(FactoryInterface $userFactory)
    {
        $this->userFactory = $userFactory;
        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): UserInterface
    {
        $options = $this->optionsResolver->resolve($options);
        /** @var UserInterface $user */
        $user = $this->userFactory->createNew();
        $user->setEmail($options['email']);
        $user->setUsername($options['username']);
        $user->setPlainPassword($options['password']);
        $user->setEnabled($options['enabled']);
        $user->addRole(UserInterface::DEFAULT_ADMIN_ROLE);

        if (isset($options['first_name'])) {
            $user->setFirstName($options['first_name']);
        }
        if (isset($options['last_name'])) {
            $user->setLastName($options['last_name']);
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('email', function (Options $options): string {
                return $this->faker->email;
            })
            ->setDefault('username', function (Options $options): string {
                return $this->faker->firstName . ' ' . $this->faker->lastName;
            })
            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('password', 'password123')
            ->setDefined('first_name')
            ->setDefined('last_name')
        ;
    }
}