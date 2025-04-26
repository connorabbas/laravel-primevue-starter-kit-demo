<?php

namespace App\Enums;

/**
 * Match mode operator values for dynamic query filtering
 * Equivalent implementation of PrimeVue's FilterMatchMode via @primevue/core/api
 */
enum FilterMatchMode: string
{
    case STARTS_WITH = 'startsWith';
    case CONTAINS = 'contains';
    case NOT_CONTAINS = 'notContains';
    case ENDS_WITH = 'endsWith';
    case EQUALS = 'equals';
    case NOT_EQUALS = 'notEquals';
    case IN = 'in';
    case LESS_THAN = 'lt';
    case LESS_THAN_OR_EQUAL_TO = 'lte';
    case GREATER_THAN = 'gt';
    case GREATER_THAN_OR_EQUAL_TO = 'gte';
    case BETWEEN = 'between';
    case DATE_IS = 'dateIs';
    case DATE_IS_NOT = 'dateIsNot';
    case DATE_BEFORE = 'dateBefore';
    case DATE_AFTER = 'dateAfter';
}
