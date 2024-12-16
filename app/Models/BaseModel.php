<?php

namespace App\Models;


use App\Http\Traits\AssetTrait;
use App\Http\Traits\SearchableTrait;
use App\Http\Traits\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $translatable = [];
    use SearchableTrait;

    protected $assets = [];

    public function beautifulCounter($number)
    {
        if ($number < 1000) {
            return $number . "";
        }
        $suffix = ['', 'k', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'];
        $power = floor(log($number, 1000));
        return round($number / (1000 ** $power), 1, PHP_ROUND_HALF_EVEN) . $suffix[$power];
    }

    public function save(array $options = [])
    {
        if (!$this->exists and $this->isTranslatable()) {
            foreach ($this->translatable as $item) {
                $this->attributes[$item] = $this->initTranslatedValue($this->attributes[$item]);
            }
        }

        return parent::save($options);
    }

    private function isTranslatable(): bool
    {
        return count($this->translatable) > 0;
    }

    private function initTranslatedValue($value)
    {
        $data = [];
        foreach (config('app.locales') as $item)
            $data[$item] = $value;
        return json_encode($data);
    }

    /**
     * @return array
     */
    public function getAssets(): array
    {
        return $this->assets;
    }

    public function update(array $attributes = [], array $options = [])
    {
        if ($this->isTranslatable()) {
            foreach ($this->translatable as $item) {
                $this->attributes[$item] = $this->updateTranslatedValue($this->original[$item], $this->attributes[$item]);
            }
        }
        return parent::update($attributes, $options);
    }

    private function updateTranslatedValue($storedValue, $setValue)
    {
        try {
            $data = json_decode($storedValue, true);
        } catch (\Exception | \Throwable $exception) {
            $data = json_decode($this->initTranslatedValue($setValue));
        }
        $data[app()->getLocale()] = $setValue;
        return $data;
    }

    public function newInstance($attributes = [], $exists = false)
    {
        $casts = [];
        $assets = [];

        if ($this->isTranslatable()) {
            foreach ($this->translatable as $item) {
                $casts[$item] = TranslatableTrait::class . '';
            }
        }

        if ($this->hasAssets()) {
            foreach ($this->assets as $item) {
                $assets[$item] = AssetTrait::class . '';
            }
        }

        $this->mergeCasts($assets);
        $this->mergeCasts($casts);

        return parent::newInstance($attributes, $exists);
    }

    private function hasAssets(): bool
    {
        return count($this->assets) > 0;
    }
}