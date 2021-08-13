<?php

namespace Sweetstack\AsyncTelescope\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Laravel\Telescope\Contracts\EntriesRepository;
use Laravel\Telescope\Storage\DatabaseEntriesRepository;

abstract class BaseStorageJob
{
    use Queueable;

    protected Collection $entries;

    public function __construct(Collection $entries)
    {
        $this->entries = $entries;
    }

    protected function getDatabaseStorageDriver(): EntriesRepository
    {
        return app(DatabaseEntriesRepository::class);
    }

    abstract public function handle();
}
