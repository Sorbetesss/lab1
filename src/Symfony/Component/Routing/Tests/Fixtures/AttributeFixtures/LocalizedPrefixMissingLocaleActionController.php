<?php

namespace Symfony\Component\Routing\Tests\Fixtures\AttributeFixtures;

use Symfony\Component\Routing\Annotation\Route;

#[Route(path: ['nl' => '/nl'])]
class LocalizedPrefixMissingLocaleActionController
{
    #[Route(path: ['nl' => '/actie', 'en' => '/action'], name: 'action')]
    public function action()
    {
    }
}
