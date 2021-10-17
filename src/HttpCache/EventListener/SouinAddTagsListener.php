<?php

/*
 * This file is not part of the API Platform project.
 * I won't copyright my work.
 */

declare(strict_types=1);

namespace Darkweak\SouinApiPlatformBundle\HttpCache\EventListener;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Sets the list of resources' IRIs included in this response in the "Surrogate-Key" HTTP headers.
 * 
 * @experimental
 */
final class SouinAddTagsListener
{
    private $iriConverter;

    public function __construct(IriConverterInterface $iriConverter)
    {
        $this->iriConverter = $iriConverter;
    }

    /**
     * Adds the "Surrogate-Key" header.
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (
            !$request->isMethodCacheable()
            || !$response->isCacheable()
            || (!$attributes = RequestAttributesExtractor::extractAttributes($request))
        ) {
            return;
        }

        $resources = $request->attributes->get('_resources');
        if (isset($attributes['collection_operation_name']) || ($attributes['subresource_context']['collection'] ?? false)) {
            // Allows to purge collections
            $iri = $this->iriConverter->getIriFromResourceClass($attributes['resource_class']);
            $resources[$iri] = $iri;
        }

        if (!$resources) {
            return;
        }

        $response->headers->set('Surrogate-Key', implode(', ', $resources));
    }
}
