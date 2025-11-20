<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\Tenant\User as TenantUser;
use Hash;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateTenantUsers implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $tenant;

    private array $usersData = [
        [
            'name'      => 'Admin',
            'last_name' => 'Admin',
            'email'     => 'dev@acsyt.com',
            'password'  => '123456'
        ],
        [
            'name'      => 'Admin',
            'last_name' => 'Admin',
            'email'     => 'admin@acsyt.com',
            'password'  => '123456'
        ],
    ];

    /**
     * Create a new job instance.
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->tenant->run(function () {
            foreach ($this->usersData as $user) {
                TenantUser::create([
                    'name'      => $user['name'],
                    'last_name' => $user['last_name'],
                    'email'     => $user['email'],
                    'password'  => Hash::make($user['password'])
                ]);
            }
        });
    }

}
