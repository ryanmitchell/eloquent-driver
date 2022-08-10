<?php

namespace Statamic\Eloquent\Commands;

use Illuminate\Console\Command;
use Statamic\Assets\AssetRepository;
use Statamic\Console\RunsInPlease;
use Statamic\Contracts\Assets\Asset as AssetContract;
use Statamic\Contracts\Assets\AssetContainer as AssetContainerContract;
use Statamic\Contracts\Assets\AssetContainerRepository as AssetContainerRepositoryContract;
use Statamic\Contracts\Assets\AssetRepository as AssetRepositoryContract;
use Statamic\Eloquent\Assets\Asset;
use Statamic\Eloquent\Assets\AssetContainer;
use Statamic\Facades\Asset as AssetFacade;
use Statamic\Facades\AssetContainer as AssetContainerFacade;
use Statamic\Facades\YAML;
use Statamic\Stache\Repositories\AssetContainerRepository;
use Statamic\Statamic;

class ImportAssets extends Command
{
    use RunsInPlease;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statamic:eloquent:import-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports file based asset containers into the database.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->useDefaultRepositories();

        $this->importAssetContainers();
        $this->importAssets();

        return 0;
    }

    private function useDefaultRepositories()
    {
        Statamic::repository(AssetContainerRepositoryContract::class, AssetContainerRepository::class);
        Statamic::repository(AssetRepositoryContract::class, AssetRepository::class);

        // bind to the eloquent container class so we can use toModel()
        app()->bind(AssetContainerContract::class, AssetContainer::class);
        app()->bind(AssetContract::class, Asset::class);
    }

    private function importAssetContainers()
    {
        $containers = AssetContainerFacade::all();
        $bar = $this->output->createProgressBar($containers->count());

        $containers->each(function ($container) use ($bar) {
            $container->toModel()->save();
            $bar->advance();
        });

        $bar->finish();
        $this->line('');
        $this->info('Asset containers imported');
    }

    private function importAssets()
    {
        $assets = AssetFacade::all();
        $bar = $this->output->createProgressBar($assets->count());

        $assets->each(function ($asset) use ($bar) {
            if ($contents = $asset->disk()->get($path = $asset->metaPath())) {
                $metadata = YAML::file($path)->parse($contents);
                $asset->writeMeta($metadata);
            }
            $bar->advance();
        });

        $bar->finish();
        $this->line('');
        $this->info('Assets imported');
    }
}
