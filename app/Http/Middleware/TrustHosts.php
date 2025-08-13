<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

/**
 * TrustHosts Middleware
 *
 * Prevents Host Header Injection attacks by validating trusted hosts.
 *
 * Security Benefits:
 *  Prevents host header injection attacks
 *  Validates incoming host headers against trusted patterns
 *  Supports multiple domains and subdomains
 *  Environment-based configuration
 *
 * Use Cases:
 * - Multi-subdomain applications (admin.app.com, api.app.com)
 * - Production environments with specific domains
 * - SaaS applications with custom domains
 * - Applications behind load balancers
 *
 * Configuration:
 * Add to .env: TRUSTED_HOSTS=yourdomain.com,api.yourdomain.com
 */
class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * Enhanced version with environment configuration and multiple options.
     *
     * @return array<int, string|null>
     */
    public function hosts(): array
    {
        $trustedHosts = [];

        // Option 1: All subdomains of application URL
        $trustedHosts[] = $this->allSubdomainsOfApplicationUrl();

        // Option 2: Environment-based trusted hosts
        $envHosts = $this->getEnvironmentHosts();
        if (! empty($envHosts)) {
            $trustedHosts = array_merge($trustedHosts, $envHosts);
        }

        // Option 3: Common development hosts
        if (app()->environment(['local', 'development', 'testing'])) {
            $trustedHosts = array_merge($trustedHosts, $this->getDevelopmentHosts());
        }

        // Option 4: Load balancer/proxy hosts
        $proxyHosts = $this->getProxyHosts();
        if (! empty($proxyHosts)) {
            $trustedHosts = array_merge($trustedHosts, $proxyHosts);
        }

        return array_filter(array_unique($trustedHosts));
    }

    /**
     * Get trusted hosts from environment configuration
     *
     * Add to .env: TRUSTED_HOSTS=yourdomain.com,api.yourdomain.com,admin.yourdomain.com
     *
     * @return array<string>
     */
    protected function getEnvironmentHosts(): array
    {
        $envHosts = config('trusted.hosts', '');

        if (empty($envHosts)) {
            return [];
        }

        return array_map('trim', explode(',', $envHosts));
    }

    /**
     * Get development environment hosts
     *
     * @return array<string>
     */
    protected function getDevelopmentHosts(): array
    {
        return [
            'localhost',
            '127.0.0.1',
            '*.localhost',
            '*.local',
            '*.test',
            '*.dev',
            // Add your development domains
            'larabasex.local',
            'admin.larabasex.local',
            'api.larabasex.local',
        ];
    }

    /**
     * Get proxy/load balancer hosts
     *
     * @return array<string>
     */
    protected function getProxyHosts(): array
    {
        // Add your load balancer or proxy hosts
        $proxyHosts = config('proxy.hosts', '');
        if (empty($proxyHosts)) {
            return [];
        }

        return array_map('trim', explode(',', $proxyHosts));
    }
}
