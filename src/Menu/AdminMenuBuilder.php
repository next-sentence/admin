<?php

namespace App\Menu;

use App\Entity\UserInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AdminMenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * AdminMenuBuilder constructor.
     * @param FactoryInterface $factory
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $this->addConfigurationMenu($menu, []);

        return $menu;
    }

    /**
     * Add cms menu.
     *
     * @param ItemInterface $menu
     * @param array $childOptions
     */
    protected function addConfigurationMenu(ItemInterface $menu, array $childOptions)
    {
        $configuration = $menu
            ->addChild('configuration')
            ->setLabel('sylius.ui.configuration');

        if ($this->authorizationChecker->isGranted(UserInterface::DEFAULT_ADMIN_ROLE)) {
            $configuration
                ->addChild('admin_users', ['route' => 'sylius_admin_admin_user_index'])
                ->setLabel('sylius.ui.admin_users')
                ->setLabelAttribute('icon', 'lock');
        }

        if (!$configuration->hasChildren()) {
            $menu->removeChild('configuration');
        }
    }
}