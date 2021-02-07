<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MapStyleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fill_color_nursery', null, ['property_path'=>'fillColorNursery'])
            ->add('fill_color_nursery_owned', null, ['property_path'=>'fillColorNurseryOwned'])
            ->add('fill_color_family', null, ['property_path'=>'fillColorFamily'])
            ->add('style_name', null, ['property_path'=>'styleName'])
            ->add('ne_lat', null, ['property_path'=>'neLat'])
            ->add('ne_lng', null, ['property_path'=>'neLng'])
            ->add('sw_lat', null, ['property_path'=>'swLat'])
            ->add('sw_lng', null, ['property_path'=>'swLng'])
            ->add('zoom', null)
            ->add('center_lat', null, ['property_path'=>'centerLat'])
            ->add('center_lng', null, ['property_path'=>'centerLng'])
            ->add('width')
            ->add('height')
        ;
    }

    public function getName()
    {
        return 'map_style';
    }
}