<?php

namespace Sweetstack\AsyncTelescope\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;

class StoreEntriesJob extends BaseStorageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function handle()
    {
        $this->getDatabaseStorageDriver()->store($this->entries);
    }
}
