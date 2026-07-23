<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Form\Extension;

use Sylius\AdminMcpServerPlugin\Security\AdminUserRole;
use Sylius\Bundle\CoreBundle\Form\Type\User\AdminUserType;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class AdminUserTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('administrationAccess', CheckboxType::class, [
                'label' => 'sylius_admin_mcp_server.form.admin_user.administration_access',
                'required' => false,
                'mapped' => false,
            ])
            ->add('apiAccess', CheckboxType::class, [
                'label' => 'sylius_admin_mcp_server.form.admin_user.api_access',
                'required' => false,
                'mapped' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
            $adminUser = $event->getData();
            if (!$adminUser instanceof AdminUserInterface) {
                return;
            }

            $form = $event->getForm();
            $roles = $adminUser->getRoles();

            $form->get('administrationAccess')->setData(\in_array(AdminUserRole::ADMINISTRATION_ACCESS, $roles, true));
            $form->get('apiAccess')->setData(\in_array(AdminUserRole::API_ACCESS, $roles, true));
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $adminUser = $event->getData();
            if (!$adminUser instanceof AdminUserInterface) {
                return;
            }

            $form = $event->getForm();

            $hasAdminAccess = (bool) $form->get('administrationAccess')->getData();
            $hasApiAccess = (bool) $form->get('apiAccess')->getData();

            if ($hasAdminAccess) {
                $adminUser->addRole(AdminUserRole::ADMINISTRATION_ACCESS);
            } else {
                $adminUser->removeRole(AdminUserRole::ADMINISTRATION_ACCESS);
            }

            if ($hasApiAccess) {
                $adminUser->addRole(AdminUserRole::API_ACCESS);
            } else {
                $adminUser->removeRole(AdminUserRole::API_ACCESS);
            }
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [AdminUserType::class];
    }
}
