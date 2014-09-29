<?php

namespace SlowDB\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest,
    FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Request\ParamFetcher;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Keith Kirk <keith@kmfk.io>
 *
 * @Route("/tables")
 */
class DatabaseController extends FOSRestController
{
    /**
     * Searches the Collection for matching keys
     *
     * @Rest\Get("/search", name="database_search")
     *
     * @param  Request $request    The request object
     * @param  string  $collection The collection name
     *
     * @return JsonResponse
     */
    public function queryAction(Request $request, $collection)
    {
        $slowdb = $this->get('slowdb');
        $query  = $request->get('q', null);

        if (is_null($query)) {
            return new JsonResponse(['error' => 'Must include a Query parameter, ex: ?q={some text}'], 400);
        }

        $collections = $slowdb->all();

        $results = [];
        foreach ($collections as $collection) {
            $results[$collection] = $slowdb->{$collection}->query($query);
        }

        return new JsonResponse($results, 200);
    }

    /**
     * Returns a list of all available Collections
     *
     * @Rest\Get("", name="database_all")
     *
     * @return JsonResponse
     */
    public function listAction()
    {
        $response = $this->get('slowdb')->all();

        return new JsonResponse($response);
    }

    /**
     * Drops all the Collections
     *
     * @Rest\Delete("", name="database_drop")
     *
     * @return JsonResponse
     */
    public function dropAllAction()
    {
        $response = $this->get('slowdb')->dropAll();

        return new JsonResponse('', 204);
    }
}
