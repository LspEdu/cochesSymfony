<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\GroupByExtensionRuntime;
use InvalidArgumentException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class GroupByExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [GroupByExtensionRuntime::class, 'doSomething']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('group_by', [$this, 'groupBy']),
        ];
    }

    public function groupBy(iterable $items, string $property): array
    {
        $result = array();

        foreach ($items as $item) {
            $key = $this->getPropertyValue($item, $property);
            if (isset($result[$key])) {
                $result[$key] = [];
            }
            $result[$key][] = $item;
        }

        return $result;
    }
    private function getPropertyValue(object $object, string $property)
    {
        $getter = 'get' . ucfirst($property);

        if (method_exists($object, $getter)) {
            return $object->$getter();
        }

        throw new InvalidArgumentException(sprintf('The property "%s" does not exist on object of type "%s"', $property, get_class($object)));
    }
}
