<?php

namespace Cellar\Console\Commands;

use Illuminate\Foundation\Composer;
use Illuminate\Database\Migrations\MigrationCreator;

use Illuminate\Console\Command;

use Cellar\CellarTracker\Downloader;

use File;

class MakeMigrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cellar:migrations
    {--path= : The location where the migration file should be created.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make migrations for CellarTracker data.';

    /**
     * The migration creator instance.
     *
     * @var \Illuminate\Database\Migrations\MigrationCreator
     */
    protected $creator;

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Foundation\Composer
     */
    protected $composer;

    /**
     * Create a new migration install command instance.
     *
     * @param  \Illuminate\Database\Migrations\MigrationCreator  $creator
     * @param  \Illuminate\Foundation\Composer  $composer
     * @return void
     */
    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tabs = '            ';

        $oldMigrations = File::glob($this->getMigrationPath().'/*_create_ct_*_table.php');

        File::delete($oldMigrations);

        $files = File::allFiles(base_path('.wk/ct_tables'));

        foreach ($files as $file) {

            $table = 'ct_'.snake_case($file->getBaseName('.'.$file->getExtension()));

            if ($table == 'ct_list') { $table = 'ct_wine_list'; }

            $name = "create_{$table}_table";
            $create = $table;
            
            $migration = $this->writeMigration($name, $table, $create);

            $fields = File::get($file);

            $fields = explode(PHP_EOL, $fields);

            $indexes = [];

            $fields = array_map(function($field) use (&$indexes) {
                @list($field, $type, $params) = explode(':', trim($field));
                
                $type = $type ?: 'string';
                $params = $params ? ', '.str_replace(',',', ',$params) : null;
                $indexes[] = $field;

                return '$table->'.$type.'(\''.snake_case($field).'\''.$params.')->nullable();';
            }, $fields);

            $migration = $this->getMigrationPath().'/'.$migration.'.php';

            $contents = File::get($migration);

            $indexes = Downloader::findIndexes($indexes);

            $lines = [
            '$table->increments(\'id\');',
            '$table->unsignedInteger(\'user_id\');',
            implode(PHP_EOL.$tabs, $fields),
            '$table->softDeletes();',
            ];

            if (count($indexes)) {
                $lines[] = '$table->unique([\'user_id\', \''.implode('\', \'', $indexes).'\']);';
            }

            $contents = str_replace(
                '$table->increments(\'id\');',
                implode(PHP_EOL.$tabs, $lines),
                $contents);
            
            File::put($migration, $contents);

        }

        $this->composer->dumpAutoloads();

    }

    /**
     * Write the migration file to disk.
     *
     * @param  string  $name
     * @param  string  $table
     * @param  bool    $create
     * @return string
     */
    protected function writeMigration($name, $table, $create)
    {
        $path = $this->getMigrationPath();

        $file = pathinfo($this->creator->create($name, $path, $table, $create), PATHINFO_FILENAME);

        $this->line("<info>Created Migration:</info> $file");

        return $file;
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return $this->laravel->basePath().'/'.$targetPath;
        }

        return $this->laravel->databasePath().'/migrations';
    }
}
