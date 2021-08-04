<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class MapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('show_micro', CheckboxType::class, ['property_path'=>'showMicro'])
            ->add('show_mac', CheckboxType::class, ['property_path'=>'showMac'])
            ->add('show_dsp', CheckboxType::class, ['property_path'=>'showDSP'])
            ->add('show_dspc', CheckboxType::class, ['property_path'=>'showDSPC'])
            ->add('show_other', CheckboxType::class, ['property_path'=>'showOther'])
            ->add('show_partners', CheckboxType::class, ['property_path'=>'showPartners'])
            ->add('nurseries_by_family', null, ['property_path'=>'nurseriesByFamily'])
            ->add('nurseries_max_distance', null, ['property_path'=>'nurseriesMaxDistance'])
        ;
    }

    public function getName()
    {
        return 'map';
    }
}