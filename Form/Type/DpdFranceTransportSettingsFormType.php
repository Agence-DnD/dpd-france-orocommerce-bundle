<?php

namespace Dnd\Bundle\DpdFranceShippingBundle\Form\Type;

use Dnd\Bundle\DpdFranceShippingBundle\Form\DataTransformer\OrderStatusTransformer;
use Dnd\Bundle\DpdFranceShippingBundle\Integration\DpdFranceTransportInterface;
use Doctrine\ORM\EntityManager;
use Oro\Bundle\EntityExtendBundle\Form\Type\EnumChoiceType;
use Oro\Bundle\FormBundle\Form\Type\OroEncodedPlaceholderPasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;

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
        $this->addStationFields($builder, $options);
        $this->addGeneralFields($builder, $options);
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
        $builder->add('stationFtpHost', TextType::class, [
                    'label'    => 'dnd_dpd_france_shipping.transport.station_ftp_host.label',
                    'required' => true,
                ])->add('stationFtpUser', TextType::class, [
                    'label'    => 'dnd_dpd_france_shipping.transport.station_ftp_user.label',
                    'required' => true,
                ])->add('stationFtpPassword', OroEncodedPlaceholderPasswordType::class, [
                    'label'    => 'dnd_dpd_france_shipping.transport.station_ftp_password.label',
                    'required' => true,
                ])->add('stationFtpPort', IntegerType::class, [
                    'label'    => 'dnd_dpd_france_shipping.transport.station_ftp_port.label',
                    'required' => true,
                ])->add('orderStatusesSentToStation', EnumChoiceType::class, [
                    'enum_code' => 'order_internal_status',
                    'multiple'  => true,
                    'expanded'  => false,
                    'required'  => true,
                    'label'     => 'dnd_dpd_france_shipping.transport.order_statuses_sent_to_station.label',
                    'attr'      => [
                        'class' => 'order_internal_status',
                    ],
                ]);

        $builder->get('orderStatusesSentToStation')->addModelTransformer($this->orderStatusTransformer);
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
                ])->add('contractNumber', TextType::class, [
                    'label'    => 'dnd_dpd_france_shipping.transport.contract_number.label',
                    'required' => true,
                ])->add('maxQty', IntegerType::class, [
                    'label'    => 'dnd_dpd_france_shipping.transport.max_qty.label',
                    'required' => true,
                ]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->dataClass ?: $this->transport->getSettingsEntityFQCN(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
