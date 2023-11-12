<?php

namespace Config;

use App\Models\Tenancy\TenantModel;

class Tenancy
{
    protected $request;
    protected $tenancy;
    protected $centralDomains;
    private static $tenant_id;

    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->centralDomains = json_decode(get_option('central_domains', json_encode(['uddoktapay.com'])), true);
        $this->tenancy = $this->getTenancyData(new TenantModel());
        if (!empty($this->tenancy) && !in_array($this->tenancy->domain, $this->centralDomains)) {
            self::$tenant_id = $this->tenancy->id;
        }
    }

    public static function tenant_id()
    {
        return self::$tenant_id;
    }

    private function getTenancyData(TenantModel $tenancy)
    {
        $domain = $this->request->getServer('HTTP_HOST');
        return $tenancy->getDataByDomain($domain);
    }
}
