<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait UsesIsDeletedFlag
{
    protected static function bootUsesIsDeletedFlag(): void
    {
        static::addGlobalScope('not_deleted', function (Builder $query): void {
            $query->where($query->getModel()->getTable().'.is_deleted', false);
        });
    }

    protected function performDeleteOnModel(): void
    {
        $this->setKeysForSaveQuery($this->newModelQuery())->update(['is_deleted' => true]);

        $this->setAttribute('is_deleted', true);
        $this->syncOriginalAttribute('is_deleted');
    }

    public function restoreFromDeleted(): bool
    {
        $restored = static::withoutGlobalScope('not_deleted')
            ->whereKey($this->getKey())
            ->update(['is_deleted' => false]);

        $this->setAttribute('is_deleted', false);
        $this->syncOriginalAttribute('is_deleted');

        return $restored > 0;
    }

    public static function withDeletedRecords(): Builder
    {
        return static::withoutGlobalScope('not_deleted');
    }
}