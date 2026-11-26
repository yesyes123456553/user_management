<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait UsesIsDeletedFlag
{
    protected static function bootUsesIsDeletedFlag(): void
    {
        static::addGlobalScope('not_deleted', function (Builder $builder): void {
            $builder->where(
                $builder->getModel()->qualifyColumn('is_deleted'),
                false
            );
        });
    }

    public function scopeWithDeleted(Builder $query): Builder
    {
        return $query->withoutGlobalScope('not_deleted');
    }

    public function scopeOnlyDeleted(Builder $query): Builder
    {
        return $query->withoutGlobalScope('not_deleted')
            ->where('is_deleted', true);
    }

    public static function withDeletedRecords(): Builder
    {
        return static::withoutGlobalScope('not_deleted');
    }

    protected function performDeleteOnModel(): void
    {
        $this->setKeysForSaveQuery($this->newModelQuery())
            ->update(['is_deleted' => true]);

        $this->setAttribute('is_deleted', true);
        $this->syncOriginalAttribute('is_deleted');
    }

    public function markAsDeleted(): bool
    {
        return $this->forceFill([
            'is_deleted' => true,
        ])->save();
    }

    public function restoreDeleted(): bool
    {
        $restored = static::onlyDeleted()
            ->whereKey($this->getKey())
            ->update(['is_deleted' => false]);

        if ($restored > 0) {
            $this->setAttribute('is_deleted', false);
            $this->syncOriginalAttribute('is_deleted');
        }

        return $restored > 0;
    }

    public function restoreFromDeleted(): bool
    {
        return $this->restoreDeleted();
    }
}
