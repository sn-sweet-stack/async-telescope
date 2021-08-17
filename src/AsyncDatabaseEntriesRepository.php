<?php

namespace Sweetstack\AsyncTelescope;

use Laravel\Telescope\Storage\DatabaseEntriesRepository;
use Laravel\Telescope\Contracts\ClearableRepository;
use Sweetstack\AsyncTelescope\Jobs\UpdateEntriesJob;
use Laravel\Telescope\Contracts\PrunableRepository;
use Sweetstack\AsyncTelescope\Jobs\StoreEntriesJob;
use Laravel\Telescope\Contracts\EntriesRepository;
use Illuminate\Contracts\Foundation\Application;
use Laravel\Telescope\IncomingEntry;
use Illuminate\Support\Collection;
use Laravel\Telescope\EntryUpdate;
use Illuminate\Support\Str;

class AsyncDatabaseEntriesRepository extends DatabaseEntriesRepository
{
    /**
     * Instead of saving the entries to the storage directly, dispatch a job
     * that will do it in background.
     *
     * @param Collection $entries
     */
    public function store(Collection $entries): void
    {
        if ($this->shouldNotDispatch($entries)) {
            return;
        }

        $this->dispatch(new StoreEntriesJob($entries));
    }

    /**
     * Instead of updating the entries in the storage directly, dispatch a job
     * that will do it in background.
     *
     * @param Collection|EntryUpdate[] $updates
     */
    public function update(Collection $updates): void
    {
        if ($this->shouldNotDispatch($updates)) {
            return;
        }

        $this->dispatch(new UpdateEntriesJob($updates));
    }

    public function shouldNotDispatch(Collection $entries): bool
    {
        return $entries->isEmpty() || $entries->contains(
            function (IncomingEntry $entry) {
                return Str::contains(json_encode($entry->content), 'AsyncTelescope');
            }
        );
    }

    /**
     * The method to call from TelescopeServiceProvider::registerAsyncDriver()
     *
     * @param Application $app
     */
    public static function register(Application $app): void
    {
        static::registerSelfAsEntriesRepository($app);

        static::configureUnderlyingDatabaseStorage($app);
    }

    /**
     * This class will register itself as a default EntriesRepository for
     * Telescope.
     *
     * @param Application $app
     */
    protected static function registerSelfAsEntriesRepository(Application $app): void
    {
        $app->singleton(
            EntriesRepository::class, static::class
        );

        $app->singleton(
            ClearableRepository::class, static::class
        );

        $app->singleton(
            PrunableRepository::class, static::class
        );

        $app->when(static::class)
            ->needs('$connection')
            ->give(config('telescope.storage.database.connection'));

        $app->when(static::class)
            ->needs('$chunkSize')
            ->give(config('telescope.storage.database.chunk'));
    }

    /**
     * Configure the underlying DatabaseEntriesRepository class.
     *
     * These lines were in the original TelescopeServiceProvider, but since we
     * are now standing in front of the main storage class, we need to take care
     * of its configuration ourselves.
     *
     * @param Application $app
     */
    protected static function configureUnderlyingDatabaseStorage(Application $app): void
    {
        $app->when(DatabaseEntriesRepository::class)
            ->needs('$connection')
            ->give(config('telescope.storage.database.connection'));

        $app->when(DatabaseEntriesRepository::class)
            ->needs('$chunkSize')
            ->give(config('telescope.storage.database.chunk'));
    }

    /**
     * Dispatch the job using the pre-configured queue connection and name
     *
     * @param $job
     */
    protected function dispatch($job): void
    {
        $dispatchedInstance = dispatch($job);

        if ($conn = config('telescope.storage.async.queue_connection')) {
            $dispatchedInstance->onConnection($conn);
        }

        if ($queue = config('telescope.storage.async.queue')) {
            $dispatchedInstance->onQueue($queue);
        }
    }
}
