<?php
/**
 * Copyright (c) 2013 Josiah Truasheim
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace JJs\Bundle\GeonamesBundle\Entity;

use JJs\Bundle\GeonamesBundle\Data\FeatureCodes;
use JJs\Bundle\GeonamesBundle\Model\LocalityInterface;

/**
 * State Repository
 *
 * Manages the persistance and retrieval of state entities from the database.
 *
 * @author Josiah <josiah@jjs.id.au>
 */
class StateRepository extends LocalityRepository
{
    /**
     * Returns a state
     * 
     * @param mixed $state State
     * 
     * @return State
     */
    public function getState($state)
    {
        if ($state instanceof State) return $state;
        if ($state instanceof LocalityInterface) return $this->getLocality($state);

        return $this->find($state);
    }

    /**
     * Imports a locality as a state
     * 
     * @param LocalityInterface $locality Locality
     * @return State
     */
    public function importLocality(LocalityInterface $locality)
    {
        // No change is neccisasary for state instances
        if ($locality instanceof State) return $locality;

        // Load the existing state for the locality, or create a new instance
        $state = $this->getState($locality) ?: new State();

        // Copy data from the import locality into an existing or new state
        // instance
        $this->copyLocality($locality, $state);

        // Return the state instance from the locality
        return $state;
    }

    /**
     * @param Country $country
     * @param int     $limit
     *
     * @return array
     */
    public function getStates($country, $limit = 25)
    {
        $qb = $this->createQueryBuilder('s')
            ->select(array(
                's.nameUtf8 as name',
                's.slug as slug',
                'country.slug as country_slug',
            ))
            ->innerJoin('s.country', 'country')
            ->where('s.state IS NULL')
            ->andWhere('s.country = :country')
            ->orderBy('s.population', 'DESC')
            ->setParameter('country', $country)
            ->setMaxResults($limit)
        ;
        $query = $qb->getQuery();

        $query->useResultCache(true, null, 'Geo:Country:' . $country->getId() . ':States');

        return $query->getResult();
    }

    /**
     * @param Country $country
     * @param int     $limit
     *
     * @return array
     */
    public function getSubStates($country, $limit = 10)
    {
        $qb = $this->createQueryBuilder('s')
            ->select(array(
                's.nameUtf8 as name',
                's.slug as slug',
                'state.slug as state_slug',
                'country.slug as country_slug',
            ))
            ->innerJoin('s.state', 'state')
            ->innerJoin('s.country', 'country')
            ->where('s.state IS NOT NULL')
            ->andWhere('s.country = :country')
            ->orderBy('s.population', 'DESC')
            ->setParameter('country', $country)
            ->setMaxResults($limit)
        ;
        $query = $qb->getQuery();

        $query->useResultCache(true, null, 'Geo:Country:' . $country->getId() . ':Substates');

        $substates = $query->getResult();

        // @TODO Remove ?
        $wordsToReplace = array("Département du", "Département des", "Département de la", "Département de l'", "Département de", "Département d'");
        $regexs = array();
        foreach ($wordsToReplace as $word) {
            $regexs[] = "/(\s?)". $word . "\s?/";
        }

        foreach ($substates as &$state) {

            $label = $state['name'];
            $label = preg_replace($regexs, '\1', $label);

            $state['name'] = $label;
        }

        return $substates;
    }

}