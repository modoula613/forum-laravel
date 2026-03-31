<?php

namespace App\Providers;

use App\Models\Topic;
use App\Policies\TopicPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Topic::class => TopicPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
