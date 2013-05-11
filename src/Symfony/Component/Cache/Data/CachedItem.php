<?php

namespace Symfony\Component\Cache\Data;

/**
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class CachedItem extends ValidItem
{
    /**
     * {@inheritdoc}
     */
    public function isHit()
    {
        return true;
    }
}
