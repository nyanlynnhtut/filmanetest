<?php

namespace Za\Support\Permission\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Za\Support\Permission\Permission;

class PermissionRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old permissions and insert permissions from config file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('---- Inserting Permissions ----');
        $this->insertPermissions();
    }

    protected function insertPermissions()
    {
        $oldCount = Permission::count();
        Permission::truncate();
        $count = 0;
        $permissions = config('permissions');
        foreach ($permissions as $group => $permissionGroup) {
            foreach ($permissionGroup as $permission) {
                Permission::create([
                    'name' => $permission,
                    'slug' => Str::slug($permission['en']),
                    'group' => $group,
                ]);
                $count = $count + 1;
            }
        }

        $this->info('---- Pervious Permission Count : '.$oldCount.' ----');
        $this->info('---- New Inserted Permission Count : '.$count.' ----');
    }
}
