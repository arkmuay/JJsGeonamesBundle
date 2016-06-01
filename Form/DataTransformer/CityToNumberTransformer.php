<?php

namespace JJs\Bundle\GeonamesBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use JJs\Bundle\GeonamesBundle\Entity\City as City;



class CityToNumberTransformer implements DataTransformerInterface
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
     * Transforms an object (city) to a string (number).
     *
     * @param  City|null $city
     * @return string
     */
    public function transform($city)
    {
        if (null === $city) {
            return array();
        }

        return array(
            'country' => $city->getCountry()->getID(),
            'label' => $city->getNameAscii(),
            'id' => $city->getID()
        );
    }

    /**
     * Transforms a string (id) to an object (city).
     *
     * @param  string $number
     * @return City|null
     * @throws TransformationFailedException if object (city) is not found.
     */
    public function reverseTransform($city)
    {
        if ((!$city) || (!isset($city['id']))) {
            return null;
        }

        $id = $city['id'];

        $city = $this->om
            ->getRepository('JJs\Bundle\GeonamesBundle\Entity\City')
            ->findOneById($id)
        ;

        if (null === $city) {
            throw new TransformationFailedException(sprintf(
                "La ville %s est introuvable",
                $id
            ));
        }

        return $city;
    }
}