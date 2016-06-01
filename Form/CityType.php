<?php

namespace JJs\Bundle\GeonamesBundle\Form;

use JJs\Bundle\GeonamesBundle\Form\DataTransformer\CityToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CityFormType
 *
 * @package AppBundle\Form\Type
 */
class CityType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $countries = $this->buildChoicesCountries('FR');

        $choices = $countries['choices'];
        $default = $countries['default'];

        $builder->add('country', ChoiceType::class, array(
            'label' => 'Pays',
            'choices' => $choices,
            // 'data' => $default @TODO
        ));

        $builder->add('label', TextType::class, array(
                'label' => 'Ville',
                'attr'   =>  array(
                    'class'   => 'prompt',
                    'placeholder' => 'Recherche...'
                )
            )
        );

        $builder->add('id', HiddenType::class, array(
            'label' => false
        ));


        $transformer = new CityToNumberTransformer($this->om);
        $builder->addModelTransformer($transformer);

    }

    /**
     * @param null $country
     *
     * @return array
     */
    protected function buildChoicesCountries($countryCode = null)
    {
        // var_dump($country);

        $choices = [];
        $repository = $this->om->getRepository('JJs\Bundle\GeonamesBundle\Entity\Country');
        $countries  = $repository->getCountries();

        $default = null;

        /** @var $country \JJs\Bundle\GeonamesBundle\Entity\Country */
        foreach ($countries as $country) {
            $choices[$country->getName()] = $country->getId();
            if ($countryCode == $country->getCode()) {
                $default = $country->getID();
            }
        }

        // var_dump($default);
        return array(
            'choices' => $choices,
            'default' => $default
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => "La ville n'existe pas",
            'label'        => false,
            'error_bubbling' => false,
            'cascade_validation' => true,
            // 'data_class' => null,
        ));

    }



    /**
     * @return string
     */
    public function getName()
    {
        return 'city';
    }
}