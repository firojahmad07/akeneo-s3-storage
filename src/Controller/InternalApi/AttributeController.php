<?php

namespace Ewave\Bundle\AttributeBundle\Controller\InternalApi;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AttributeController
 *
 * @package Ewave\Bundle\AttributeBundle\Controller\InternalApi
 */
class AttributeController
{
    /**
     * @var array
     */
    private $attributeCollection;

    /**
     * @param array $attributeCollection
     */
    public function __construct(
        array $attributeCollection
    ) {
        $this->attributeCollection = $attributeCollection;
    }

    /**
     * List root categories
     *
     * @return JsonResponse
     */
    public function listAttributesAction()
    {
        return new JsonResponse($this->attributeCollection);
    }
}
