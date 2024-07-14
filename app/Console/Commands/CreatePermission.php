<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class CreatePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:create {name : The name of the permission}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new permission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');

        if (Permission::where('name', $name)->exists()) {
            $this->error('Permission already exists!');
            return false;
        }

        Permission::create(['name' => $name]);
        $this->info("Permission '{$name}' created successfully.");
        return true;
    }
}
