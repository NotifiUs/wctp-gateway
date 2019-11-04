<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an admin user to login to the application';

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
        $name = $this->ask('Name');
        $email = $this->ask('Email');
        $timezone = $this->ask("Timezone");
        $pass = $this->secret('Password');
        $confirm = $this->secret( 'Confirm');
        if( $confirm !== $pass )
        {
            $this->info("");
            $this->error("Password did not match confirmation");
            $this->info("");
            return;
        }

        if( ! $timezone )
        {
            $timezone = 'America/New_York';
        }

        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->timezone = $timezone;
        $user->password = bcrypt( $pass );
        $user->save();

        $this->info( json_encode( $user->toArray(), JSON_PRETTY_PRINT) );
        $this->info("");
        $this->info("User created!");
        $this->info("");
    }
}
