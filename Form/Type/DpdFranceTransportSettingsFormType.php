<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Form\Type;

use Dnd\Bundle\DpdFranceShippingBundle\Entity\ShippingService;
use Dnd\Bundle\DpdFranceShippingBundle\Form\DataTransformer\OrderStatusTransformer;
use Dnd\Bundle\DpdFranceShippingBundle\Integration\DpdFranceTransportInterface;
use Doctrine\ORM\EntityManager;
use Oro\Bundle\EntityExtendBundle\Form\Type\EnumChoiceType;
use Oro\Bundle\FormBundle\Form\Type\OroEncodedPlaceholderPasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * Class DpdFranceTransportSettingsFormType
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Form\Type
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class DpdFranceTransportSettingsFormType extends AbstractType
{
    /**
     * Description BLOCK_PREFIX constant
     *
     * @var string BLOCK_PREFIX
     */
    public const BLOCK_PREFIX = 'dnd_dpd_france_shipping_transport_settings_form_type';
    /**
     * Description $dataClass field
     *
     * @var mixed $dataClass
     */
    protected $dataClass;
    /**
     * Description $transport field
     *
     * @var DpdFranceTransportInterface $transport
     */
    protected DpdFranceTransportInterface $transport;
    /**
     * Description $em field
     *
     * @var EntityManager $em
     */
    protected EntityManager $em;
    /**
     * Description $orderStatusTransformer field
     *
     * @var OrderStatusTransformer $orderStatusTransformer
     */
    protected OrderStatusTransformer $orderStatusTransformer;

    /**
     * DpdFranceTransportSettingsFormType constructor
     *
     * @param DpdFranceTransportInterface $transport
     * @param EntityManager               $em
     * @param OrderStatusTransformer      $orderStatusTransformer
     */
    public function __construct(
        DpdFranceTransportInterface $transport,
        EntityManager $em,
        OrderStatusTransformer $orderStatusTransformer
    ) {
        $this->transport              = $transport;
        $this->em                     = $em;
        $this->orderStatusTransformer = $orderStatusTransformer;
    }

    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param mixed[]              $options
     *
     * @return void
     * @throws InvalidOptionsException
     * @throws MissingOptionsException
     *
     * @throws ConstraintDefinitionException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addGeneralFields($builder, $options);
        $this->addServicesNamesAndDescriptionFields($builder, $options);
        $this->addStationFields($builder, $options);
    }

    /**
     * Adds form fields related with station settings
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    private function addStationFields(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('stationEnabled', CheckboxType::class, [
            'label'       => 'dnd_dpd_france_shipping.transport.station_enabled.label',
            'required'    => false,
        ])->add('stationFtpHost', TextType::class, [
            'label'       => 'dnd_dpd_france_shipping.transport.station_ftp_host.label',
            'required'    => false,
            'constraints' => [
                new NotBlank([
                    'groups' => ['STATION_ENABLED_VALIDATION_GROUP_REQUIRED'],
                ]),
            ],
        ])->add('stationFtpUser', TextType::class, [
            'label'       => 'dnd_dpd_france_shipping.transport.station_ftp_user.label',
            'required'    => false,
            'constraints' => [
                new NotBlank([
                    'groups' => ['STATION_ENABLED_VALIDATION_GROUP_REQUIRED'],
                ]),
            ],
        ])->add('stationFtpPassword', OroEncodedPlaceholderPasswordType::class, [
            'label'       => 'dnd_dpd_france_shipping.transport.station_ftp_password.label',
            'required'    => false,
            'constraints' => [
                new NotBlank([
                    'groups' => ['STATION_ENABLED_VALIDATION_GROUP_REQUIRED'],
                ]),
            ],
        ])->add('stationFtpPort', IntegerType::class, [
            'label'       => 'dnd_dpd_france_shipping.transport.station_ftp_port.label',
            'required'    => false,
            'constraints' => [
                new NotBlank([
                    'groups' => ['STATION_ENABLED_VALIDATION_GROUP_REQUIRED'],
                ]),
            ],
        ])->add('orderStatusesSentToStation', EnumChoiceType::class, [
            'enum_code'   => 'order_internal_status',
            'multiple'    => false,
            'expanded'    => false,
            'required'    => false,
            'constraints' => [
                new NotBlank([
                    'groups' => ['STATION_ENABLED_VALIDATION_GROUP_REQUIRED'],
                ]),
            ],
            'label'       => 'dnd_dpd_france_shipping.transport.order_statuses_sent_to_station.label',
            'attr'        => [
                'class' => 'order_internal_status',
            ]
        ]);
        $builder->get('orderStatusesSentToStation')->addModelTransformer($this->orderStatusTransformer)->getForm();
    }

    /**
     * Adds form fields for general DPD settings
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    private function addGeneralFields(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('agencyCode', TextType::class, [
            'label'    => 'dnd_dpd_france_shipping.transport.agency_code.label',
            'required' => true,
            'constraints' => [new NotBlank()]
        ])->add('contractNumber', TextType::class, [
            'label'    => 'dnd_dpd_france_shipping.transport.contract_number.label',
            'required' => true,
            'constraints' => [new NotBlank()]
        ])->add('maxQty', IntegerType::class, [
            'label'    => 'dnd_dpd_france_shipping.transport.max_qty.label',
            'tooltip'  => 'dnd_dpd_france_shipping.transport.max_qty.tooltip',
            'required' => true,
            'constraints' => [new NotBlank()]
        ])->add('shippingServices', EntityType::class, [
            'class'        => ShippingService::class,
            'choice_label' => 'label',
            'label'        => 'dnd_dpd_france_shipping.integration.settings.shipping_services.label',
            'multiple'     => true,
        ]);
    }

    /**
     * Description addServicesNamesAndDescriptionFields function
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    private function addServicesNamesAndDescriptionFields(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('classicMethodName', TextType::class, [
            'label'    => 'dnd_dpd_france_shipping.transport.classic_method_name.label',
            'required' => true,
            'constraints' => [new NotBlank()]
        ])->add('classicMethodDesc', TextareaType::class, [
            'label'    => 'dnd_dpd_france_shipping.transport.classic_method_description.label',
            'required' => true,
            'constraints' => [new NotBlank()]
        ])->add('predictMethodName', TextType::class, [
            'label'    => 'dnd_dpd_france_shipping.transport.predict_method_name.label',
            'required' => true,
            'constraints' => [new NotBlank()]
        ])->add('predictMethodDesc', TextareaType::class, [
            'label'    => 'dnd_dpd_france_shipping.transport.predict_method_description.label',
            'required' => true,
            'constraints' => [new NotBlank()]
        ])->add('pickupMethodName', TextType::class, [
            'label'    => 'dnd_dpd_france_shipping.transport.pickup_method_name.label',
            'required' => true,
            'constraints' => [new NotBlank()]
        ])->add('pickupMethodDesc', TextareaType::class, [
            'label'    => 'dnd_dpd_france_shipping.transport.pickup_method_description.label',
            'required' => true,
            'constraints' => [new NotBlank()]
        ])->add('googleMapsApiKey', TextType::class, [
            'label'    => 'dnd_dpd_france_shipping.transport.google_maps_api_key.label',
            'tooltip'  => 'dnd_dpd_france_shipping.transport.google_maps_api_key.tooltip',
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->dataClass ?: $this->transport->getSettingsEntityFQCN(),
            'mapped'     => true,
            'validation_groups' => function (FormInterface $form) {
                $groups = ['Default'];
                $data   = $form->getData();
                if ($data->isStationEnabled()) { // then we want station fields to be required
                    $groups[] = 'STATION_ENABLED_VALIDATION_GROUP_REQUIRED';
                }

                return $groups;
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return self::BLOCK_PREFIX;
    }
}
