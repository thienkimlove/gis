<?php

namespace Gis\Console\Commands;

use Illuminate\Console\Command;

class UpdateUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encrypt:password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt Old Password.';

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
        $users = \DB::select("select * from users");

        foreach ($users as $user) {

           \DB::update("update users set password = '".encryptString($user->password)."' where id = ".$user->id);
        }
    }
}
