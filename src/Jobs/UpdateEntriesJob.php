<?php

namespace Sweetstack\AsyncTelescope\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;

class UpdateEntriesJob extends BaseStorageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function handle()
    {
        $this->getDatabaseStorageDriver()->update($this->entries);
    }
}
