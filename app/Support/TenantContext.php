<?php

namespace App\Support;

use App\Models\Organization;
use App\Models\Project;
use App\Models\Workspace;

class TenantContext
{
    protected static ?Organization $organization = null;
    protected static ?Workspace $workspace = null;
    protected static ?Project $project = null;

    public static function set(?Organization $organization, ?Workspace $workspace = null, ?Project $project = null): void
    {
        static::$organization = $organization;
        static::$workspace = $workspace;
        static::$project = $project;
    }

    public static function organization(): ?Organization
    {
        return static::$organization;
    }

    public static function workspace(): ?Workspace
    {
        return static::$workspace;
    }

    public static function project(): ?Project
    {
        return static::$project;
    }

    public static function clear(): void
    {
        static::$organization = null;
        static::$workspace = null;
        static::$project = null;
    }
}