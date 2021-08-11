<?php

namespace Sweetstack\AsyncTelescope\Jobs;

use Illuminate\Support\Collection;
use Laravel\Telescope\Contracts\EntriesRepository;

abstract class BaseStorageJob
{
    protected Collection $entries;

    public function __construct(Collection $entries)
    {
        $this->entries = $entries;
    }

    protected function getDatabaseStorageDriver(): EntriesRepository
    {
        return app(EntriesRepository::class);
    }

    abstract public function handle();
}
