<?php

namespace Jlorente\Laravel\Eloquent\Concerns\SplittedDates;

/**
 * Trait HasSplittedDates.
 *
 * @author Jose Lorente <jose.lorente.martin@gmail.com>
 */
trait HasSplittedDates
{

    /**
     * Flag to indicate if the splitted dates array has been normalized or not.
     * 
     * @var bool 
     */
    private $splittedDatesNormalized = false;

    /**
     * The splitted dates attributes.
     *
     * @var array
     */
    protected $splittedDates = [
    ];

    /**
     * Gets the array that defines the default mapper for the splitted dates.
     *
     * @return array
     */
    public function getSplittedDateDefaultMapper(): array
    {
        return defined('self::SPLITTED_DATES_DEFAULT_MAPPER') ? self::SPLITTED_DATES_DEFAULT_MAPPER : ['year', 'month', 'day'];
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if ($this->isSplittedDate($key)) {
            return $this->castSplittedDate($key, $value);
        } else {
            return parent::setAttribute($key, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function getDates()
    {
        return array_merge(parent::getDates(), $this->getSplittedDatesAttributes());
    }

    /**
     * Determine whether a value is a part of a date splitted attribute 
     * for inbound manipulation.
     *
     * @param string $attribute
     * @return bool
     */
    protected function isSplittedDate(string $attribute): bool
    {
        return $this->getSplittedDateMap($attribute) !== null;
    }

    /**
     * Checks whether the model has splitted dates or not.
     * 
     * @return bool
     */
    protected function hasSplittedDates(): bool
    {
        return !!$this->splittedDates;
    }

    /**
     * Normalizes the splitted dates array.
     */
    protected function normalizeSplittedDates()
    {
        $normalized = [];
        $defaultMapper = $this->getSplittedDateDefaultMapper();
        foreach ($this->splittedDates as $key => $value) {
            if (is_string($key)) {
                $map = (array) $value;
            } else {
                $key = $value;
                $map = $defaultMapper;
            }
            $normalized[$key] = $map;
        }

        $this->splittedDatesNormalized = true;
        $this->splittedDates = $normalized;
    }

    /**
     * Gets the normalized list of the splitted date attributes.
     * 
     * @return array
     */
    protected function getSplittedDatesAttributes(): array
    {
        $normalized = [];
        if ($this->hasSplittedDates()) {
            if ($this->splittedDatesNormalized === false) {
                $this->normalizeSplittedDates();
            }
            $normalized = array_keys($this->splittedDates);
        }

        return $normalized;
    }

    /**
     * Gets the splitted date map for the attribute.
     * 
     * @param string $attribute
     * @return array|null
     */
    protected function getSplittedDateMap(string $attribute)
    {
        if ($this->hasSplittedDates() === false) {
            return null;
        }

        if ($this->splittedDatesNormalized === false) {
            $this->normalizeSplittedDates();
        }

        $map = null;
        foreach ($this->splittedDates as $key => $value) {
            if (preg_match("/$key/", $attribute)) {
                $map = [$key, $value];
                break;
            }
        }

        return $map;
    }

    /**
     * Casts the splitted date attributes.
     * 
     * @param string $attribute
     * @param string|int|Carbon $value
     * @return $this
     */
    protected function castSplittedDate(string $attribute, $value)
    {
        list($key, $map) = $this->getSplittedDateMap($attribute);

        if ($attribute === $key) {
            parent::setAttribute($key, $value);
            foreach ($map as $mapKey) {
                $this->attributes["{$key}_{$mapKey}"] = $this->$attribute ? $this->$attribute->$mapKey : null;
            }
        } else {
            $this->attributes[$attribute] = $value;

            if ($this->$key) {
                $attributeToSet = str_replace("{$key}_", '', $attribute);
                $date = $this->$key->$attributeToSet($value);
            } else {
                $date = now()->startOfYear();
                foreach ($map as $mapKey) {
                    $attribute = "{$key}_{$mapKey}";
                    $value = $this->$attribute;
                    if ($value) {
                        $date->$mapKey($value);
                    }
                }
            }

            parent::setAttribute($key, $date);
        }

        return $this;
    }

}
