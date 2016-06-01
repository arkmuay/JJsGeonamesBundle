<?php

namespace JJs\Bundle\GeonamesBundle\Service;

use Elastica\Filter\Term;
use Elastica\Index;
use Elastica\Query;
use Elastica\Query\QueryString;
use Elastica\Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SearchCity
 *
 */
class SearchCity
{

    private $index;

    /**
     * @param Type $index
     */
    public function __construct(Type $index)
    {
        $this->index = $index;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        $city = $request->get('query', null);
        $country = $request->get('country', null);

        $data = array();
        try {
            $data = $this->search($city, $country);
        } catch (\Exception $e) {

        }

        return new JsonResponse(array('results' => $data), 200, array(
            'Cache-Control' => 'no-cache',
        ));
    }

    /**
     * @param string  $city
     * @param string  $country
     * @param integer $limit
     *
     * @return array
     */
    public function search($city = null, $country = null, $limit = 10)
    {
        if (null == $city) {
            throw new NotFoundHttpException();
        }

        $elasticaQuery = new Query();

        $searchQuery = new QueryString();
        $searchQuery->setParam('query', $city);
        $searchQuery->setParam('fields', array(
            'name'
        ));

        if (null !== $country) {
            if (is_numeric($country)) {
                $term = new Term(array('country.id' => $country));
            } else {
                $term = new Term(array('country.name' => $country));
            }
            $searchQuery = new Query\Filtered($searchQuery, $term);
        }

        $elasticaQuery->setQuery($searchQuery);

        $results = $this->index->search($searchQuery, $limit)->getResults();

        $data = array();

        foreach ($results as $result) {
            $source = $result->getSource();

            $data[] = array(
                'title'       => $source['name'],
                'description' => (isset($source['state']['name']) ? $source['state']['name'] . ', ' : '') . $source['country']['name'],
                'city'        => $source['id']
            );
        }

        return $data;
    }

}
