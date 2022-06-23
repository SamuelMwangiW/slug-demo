<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/** @mixin Model */
trait HasSlug
{
    protected string $slugColumn = 'slug';

    public static function bootHasSlug(): void
    {
        static::creating(fn (Model $model) => $model->generateSlugOnCreate());

        static::updating(fn (Model $model) => $model->ensureSlugOnUpdate());
    }

    abstract protected function createSlugFromColumn(): string;

    public function ensureSlugOnUpdate()
    {
        if ($this->{$this->slugColumn} && $this->slugIsUnique($this->{$this->slugColumn}, $this->id)) {
            return;
        }

        $slug = Str::slug(
            $this->getAttribute($this->createSlugFromColumn())
        );

        $this->{$this->slugColumn} = $this->generateSlug($slug);
    }

    public function generateSlugOnCreate()
    {
        if ($this->{$this->slugColumn} && $this->slugIsUnique($this->{$this->slugColumn}, $this->id)) {
            return;
        }

        $slug = Str::slug(
            $this->getAttribute($this->createSlugFromColumn())
        );

        $this->{$this->slugColumn} = $this->generateSlug($slug);
    }

    private function generateSlug(string $slug)
    {
        $originalSlug = $slug;
        $i = 1;

        while (!$this->slugIsUnique($slug) || $slug === '') {
            $slug = $originalSlug . '-' . $i++;
        }

        return $slug;
    }

    private function slugIsUnique(string $slug, $ignoreId = null): bool
    {
        return !static::query()
            ->where(column: $this->slugColumn, operator: '=', value: $slug)
            ->when($ignoreId, fn (Builder $q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }
}
