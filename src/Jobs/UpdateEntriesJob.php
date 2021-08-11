<?php

namespace Sweetstack\AsyncTelescope\Jobs;

class UpdateEntriesJob extends BaseStorageJob
{
    public function handle()
    {
        $this->getDatabaseStorageDriver()->update($this->entries);
    }
}
