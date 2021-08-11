<?php

namespace Sweetstack\AsyncTelescope\Jobs;

class StoreEntriesJob extends BaseStorageJob
{
    public function handle()
    {
        $this->getDatabaseStorageDriver()->store($this->entries);
    }
}
