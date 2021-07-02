<?php

declare(strict_types=1);

use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // here we can define, what sets of rules will be applied
    // tip: use "SetList" class to autocomplete sets
    // $containerConfigurator->import(SetList::CODING_STYLE);

    // register single rule
    $services = $containerConfigurator->services();
    $services->set(Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector ::class);
};
