<?php

namespace App\Services\Filters;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeleteFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        // Check if the entity implements the LocalAware interface
        if (!$targetEntity->getReflectionClass()->implementsInterface('LocaleAware')) {
            return "";
        }

        return $targetTableAlias.'.locale = ' . $this->getParameter('locale'); // getParameter applies quoting automatically
    }
}