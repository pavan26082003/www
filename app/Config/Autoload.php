<?php

namespace Config;

use CodeIgniter\Config\AutoloadConfig;

/**
 * Shared local / production autoloader configuration.
 *
 * Keeps the module-specific class maps from both environments and supports
 * CI4 controllers stored in the project's physical API/V1 directory.
 * Does not contain or modify database configuration.
 */
class Autoload extends AutoloadConfig
{
    /**
     * PSR-4 namespace mappings.
     * Both Api\V1\* and APIs\V1\* are used by existing modules.
     * Directory capitalization matters on Linux: physical folder is API/.
     *
     * @var array<string, string>
     */
    public $psr4 = [
        APP_NAMESPACE => APPPATH,
        'Config'      => APPPATH . 'Config',
        'Api'         => ROOTPATH . 'API/',
        'APIs'        => ROOTPATH . 'API/',
    ];

    /**
     * Preserve class mappings previously found in either environment.
     * Missing environment-specific files are skipped in __construct().
     *
     * @var array<string, string>
     */
    public $classmap = [
        // Existing production mapping.
        'App\\Libraries\\AppHomeSchemaBuilder' => APPPATH . 'Libraries/AppHomeSchemaBuilder.php',

        // Existing local mappings.
        'App\\Services\\Erp\\ErpFactorUserProvisionService' => __DIR__ . '/ErpFactorUserProvisionBootstrap.php',
        'App\\Services\\Sales\\SalesContextService' => __DIR__ . '/../Services/Sales/SalesContextService.php',
        'App\\Models\\finance\\FinanceBaseModel' => __DIR__ . '/../Models/finance/FinanceBaseModel.php',
        'App\\Services\\finance\\FinanceDashboardAggregationService' => __DIR__ . '/../Services/finance/FinanceDashboardAggregationService.php',
        'App\\Services\\ERP\\CanonicalBrandId' => __DIR__ . '/../Services/Erp/CanonicalBrandId.php',
        'App\\Services\\ERP\\FactorBranchId' => __DIR__ . '/../Services/Erp/FactorBranchId.php',
        'App\\Services\\ERP\\FactorSchema' => __DIR__ . '/../Services/Erp/FactorSchema.php',
        'App\\Services\\ERP\\ErpFactorIdentityVerifier' => __DIR__ . '/../Services/Erp/ErpFactorIdentityVerifier.php',
    ];

    public function __construct()
    {
        parent::__construct();

        // Some environment-specific files are not deployed everywhere.
        // Do not register a class map that points to a non-existent file.
        foreach ($this->classmap as $class => $path) {
            if (!is_file($path)) {
                unset($this->classmap[$class]);
            }
        }

        // Preserve the production DeviceUiSchemaNormalizer casing workaround.
        // Windows is case-insensitive; on Linux the lowercase folder may be
        // the canonical copy when both Services/ and services/ exist.
        $normalizerLower = APPPATH . 'services/DeviceUiSchemaNormalizer.php';
        $normalizerUpper = APPPATH . 'Services/DeviceUiSchemaNormalizer.php';
        if (is_file($normalizerLower)) {
            $this->classmap['App\\Services\\DeviceUiSchemaNormalizer'] = $normalizerLower;
        } elseif (is_file($normalizerUpper)) {
            $this->classmap['App\\Services\\DeviceUiSchemaNormalizer'] = $normalizerUpper;
        }
    }
}
