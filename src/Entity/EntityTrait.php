<?php
/**
 * OAuth 2.0 Entity trait
 *
 */

namespace OAuth2\Server\Entity;

trait EntityTrait
{
    /**
     * Hydrate an entity with properites
     *
     * @param array $properties
     *
     * @return self
     */
    public function hydrate(array $properties)
    {
        foreach ($properties as $prop => $val) {
            if (property_exists($this, $prop)) {
                $this->{$prop} = $val;
            }
        }

        return $this;
    }
}
