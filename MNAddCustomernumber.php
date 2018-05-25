<?php

namespace MNAddCustomernumber;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
class MNAddCustomernumber extends \Shopware\Components\Plugin
{
    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(ActivateContext::CACHE_LIST_DEFAULT);
    }
    public function deactivate(DeactivateContext $context)
    {
        $context->scheduleClearCache(DeactivateContext::CACHE_LIST_DEFAULT);
    }
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Modules_Admin_SaveRegisterSendConfirmation_Start' => 'onSaveRegisterSendConfirmation',
        ];
    }

    public function onSaveRegisterSendConfirmation(\Enlight_Event_EventArgs $args)
    {
        $sRegister = $this->container->get('session')->offsetGet('sRegister');

        $queryBuilder = $this->container->get('dbal_connection')->createQueryBuilder();
        $queryBuilder->select('customernumber')
            ->from('s_user')
            ->where('email = :email')
            ->setParameter('email',  $sRegister['personal']['email'])
            ->orderBy('id','DESC')
            ->setMaxResults(1);

        $customernumber = $queryBuilder->execute()->fetchAll();

        $sRegister['billing'] = $sRegister['billing'] + $customernumber[0];
        $this->container->get('session')->offsetSet('sRegister', $sRegister);
    }
}
