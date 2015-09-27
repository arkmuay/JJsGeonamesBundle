<?php

namespace JJs\Bundle\GeonamesBundle\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * State
 *
 * The first level administrative division of a country. Refered to as a
 * 'province' for some countries.
 *
 * @Entity(repositoryClass="StateRepository")
 * @Table(name="geo_state", indexes={@ORM\Index(name="geoname_id", columns={"geoname_id"})}))
 * @author Josiah <josiah@jjs.id.au>
 */
class State extends Locality
{

    /**
     * State
     *
     * @ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @var State
     */
    protected $state;

    /**
     * @Gedmo\Slug(fields={"nameAscii"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Returns the state
     *
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the state
     *
     * @param State $state State
     */
    public function setState(State $state)
    {
        $this->state = $state;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getNameUtf8();
    }
}