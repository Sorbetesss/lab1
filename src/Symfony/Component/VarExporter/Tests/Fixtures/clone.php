<?php

return \Symfony\Component\VarExporter\Internal\Hydrator::hydrate(
    $o = [
        (($f = &\Symfony\Component\VarExporter\Internal\Registry::$factories)['Symfony\\Component\\VarExporter\\Tests\\MyCloneable'] ?? \Symfony\Component\VarExporter\Internal\Registry::f('Symfony\\Component\\VarExporter\\Tests\\MyCloneable'))(),
        ($f['Symfony\\Component\\VarExporter\\Tests\\MyNotCloneable'] ?? \Symfony\Component\VarExporter\Internal\Registry::f('Symfony\\Component\\VarExporter\\Tests\\MyNotCloneable'))(),
    ],
    null,
    [],
    [
        0 => $o[0],
        1 => $o[1],
    ],
    []
);
