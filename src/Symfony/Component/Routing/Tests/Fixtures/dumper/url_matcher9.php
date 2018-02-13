<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class ProjectUrlMatcher extends Symfony\Component\Routing\Matcher\UrlMatcher
{
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($rawPathinfo)
    {
        $allow = array();
        $pathinfo = rawurldecode($rawPathinfo);
        $trimmedPathinfo = rtrim($pathinfo, '/');
        $context = $this->context;
        $requestMethod = $canonicalMethod = $context->getMethod();
        $host = strtolower($context->getHost());

        if ('HEAD' === $requestMethod) {
            $canonicalMethod = 'GET';
        }

        switch ($pathinfo) {
            case '/':
                // a
                if (preg_match('#^(?P<d>[^\\.]++)\\.e\\.c\\.b\\.a$#sDi', $host, $hostMatches)) {
                    return $this->mergeDefaults(array_replace($hostMatches, array('_route' => 'a')), array());
                }
                // c
                if (preg_match('#^(?P<e>[^\\.]++)\\.e\\.c\\.b\\.a$#sDi', $host, $hostMatches)) {
                    return $this->mergeDefaults(array_replace($hostMatches, array('_route' => 'c')), array());
                }
                // b
                if ('d.c.b.a' === $host) {
                    return array('_route' => 'b');
                }
                break;
        }

        throw $allow ? new MethodNotAllowedException(array_keys($allow)) : new ResourceNotFoundException();
    }
}
