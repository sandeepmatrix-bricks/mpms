<?php

namespace App\Services;

final class TenantContext
{
    private ?string $tenantId = null;
    private bool $isPlatform = false;

    public function setTenant(?string $tenantId, bool $isPlatform = false): void
    {
        $this->tenantId = $tenantId;
        $this->isPlatform = $isPlatform;
    }

    public function tenantId(): ?string
    {
        return $this->tenantId;
    }

    public function isPlatform(): bool
    {
        return $this->isPlatform;
    }

    public function clear(): void
    {
        $this->tenantId = null;
        $this->isPlatform = false;
    }
}
