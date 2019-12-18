<?php
namespace Orq\Laravel\YaCommerce\Domain;

trait ParameterAttributeTrait {

    /**
     * parameter Accessor
     */
    public function getParametersAttribute($value): array
    {
        return json_decode($value, true);
    }

    /**
     * parameter Mutator
     */
    public function setParametersAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['parameters'] = json_encode($value);
        } else if (is_string($value)) {
            $this->attributes['parameters'] = $value;
        }
    }
}
