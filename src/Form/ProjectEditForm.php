<?php

/*
 * This file is part of the Kimai package.
 *
 * (c) Kevin Papst <kevin@kevinpapst.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Project;
use App\Form\Type\CustomerType;
use App\Form\Type\YesNoType;
use App\Repository\CustomerRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used to edit Projects.
 *
 * @author Kevin Papst <kevin@kevinpapst.de>
 */
class ProjectEditForm extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Project $entry */
        $entry = $options['data'];

        $customer = null;
        if ($entry->getId() !== null) {
            $customer = $entry->getCustomer();
        }

        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name',
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'label.comment',
                'required' => false,
            ])
            ->add('orderNumber', TextType::class, [
                'label' => 'label.order_number',
                'required' => false,
            ])
            ->add('customer', CustomerType::class, [
                'label' => 'label.customer',
                'query_builder' => function (CustomerRepository $repo) use ($customer) {
                    return $repo->builderForEntityType($customer);
                },
            ])
            ->add('visible', YesNoType::class, [
                'label' => 'label.visible',
            ])
            ->add('budget', MoneyType::class, [
                'label' => 'label.budget',
                'currency' => $customer ? $customer->getCurrency() : $builder->getOption('currency'),
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'admin_project_edit',
            'currency' => Customer::DEFAULT_CURRENCY,
        ]);
    }
}
