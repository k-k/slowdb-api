<?php

namespace SlowDB\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest,
    FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Request\ParamFetcher;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\HttpFoundation\Request;

/**
 * @author Keith Kirk <keith@kmfk.io>
 *
 * @Route("/tables/{collection}")
 */
class CollectionController extends FOSRestController
{
    /**
     * Returns a list of all available Collections
     *
     * @Rest\Get("", name="collection_all")
     *
     * @param  string $collection The collection name
     *
     * @return JsonResponse
     */
    public function allAction($collection)
    {
        $response = $this->get('slowdb')->{$collection}->all();

        return new JsonResponse($response, 200);
    }

    /**
     * Truncates a Collection
     *
     * @Rest\Delete("", name="collection_truncate")
     *
     * @param  string $collection The collection name
     *
     * @return JsonResponse
     */
    public function truncateAction($collection)
    {
        $response = $this->get('slowdb')->{$collection}->truncate();

        return new JsonResponse('', 204);
    }

    /**
     * Returns a value based on its Key
     *
     * @Rest\Get("/{key}", name="collection_get")
     *
     * @param  string $collection The collection name
     * @param  string $key        The key to retrieve a value for
     *
     * @return JsonResponse
     */
    public function getAction($collection, $key)
    {
        $response = $this->get('slowdb')->{$collection}->get($key);

        if (!$response) {
            return new JsonResponse(['error' => 'Not Found.'], 404);
        }

        return new JsonResponse($response, 200);
    }

    /**
     * Searches the Collection for matching keys
     *
     * @Rest\Get("/search", name="collection_search")
     *
     * @param  Request $request    The request object
     * @param  string  $collection The collection name
     *
     * @return JsonResponse
     */
    public function queryAction(Request $request, $collection)
    {
        $query = $request->get('q', null);

        if (is_null($query)) {
            return new JsonResponse(['error' => 'Must include a Query parameter, ex: ?q={some text}'], 400);
        }

        $response = $this->get('slowdb')->{$collection}->query($query);

        return new JsonResponse($response, 200);
    }

    /**
     * Returns a count of documents in the Collection
     *
     * Allows for an optional Query paramter to filter the count
     *
     * @Rest\Get("/count", name="collection_count")
     *
     * @param  Request $request    The request object
     * @param  string  $collection The collection name
     *
     * @return JsonResponse
     */
    public function countAction(Request $request, $collection)
    {
        $query = $request->get('q', null);
        $exact = $request->get('exact', false);

        $response = $this->get('slowdb')->{$collection}->count($query, $exact);

        return new JsonResponse(['count' => $response], 200);
    }

    /**
     * Sets a Value based on its Key
     *
     * If the Key already exists, this will overwrite the existing key
     *
     * @Rest\Post("", name="collection_set")
     *
     * @param  Request $request    The request object
     * @param  string  $collection The collection name
     *
     * @return JsonResponse
     */
    public function setAction(Request $request, $collection)
    {
        $document = json_decode($request->getContent(), true);
        $key      = key($document);
        $exists   = $this->get('slowdb')->{$collection}->count($key, true);
        $response = $this->get('slowdb')->{$collection}->set($key, $document[$key]);

        if ($exists) {
            return new JsonResponse('', 204);
        }

        return new JsonResponse(['created' => $response], 201);
    }

    /**
     * Updates a value based on its Key
     *
     * @Rest\Put("/{key}", name="collection_put")
     *
     * @param  Request $request    The request object
     * @param  string  $collection The collection name
     * @param  string  $key        The key to replace a value for
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, $collection, $key)
    {
        $document = json_decode($request->getContent(), true);
        $exists   = $this->get('slowdb')->{$collection}->count($key, true);

        if (!$exists) {
            return new JsonResponse(['error' => 'Not Found.'], 404);
        }

        $response = $this->get('slowdb')
            ->{$collection}->set($key, $document);

        return new JsonResponse('', 204);
    }

    /**
     * Removes a value based on its Key
     *
     * @Rest\Delete("/{key}", name="collection_delete")
     *
     * @param  Request $request    The request object
     * @param  string  $collection The collection name
     * @param  string  $key        The key to remove a value
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $collection, $key)
    {
        $document = json_decode($request->getContent(), true);
        $exists   = $this->get('slowdb')->{$collection}->count($key, true);

        if (!$exists) {
            return new JsonResponse(['error' => 'Not Found.'], 404);
        }

        $response = $this->get('slowdb')->{$collection}->remove($key);

        return new JsonResponse('', 204);
    }
}
