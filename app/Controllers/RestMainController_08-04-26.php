<?php
declare(strict_types=1);

namespace App\Controllers;

//require_once APPPATH . 'Models/RestMainModel.php';

use App\Models\RestMainModel;
use Throwable;

final class RestMainController extends RestBaseController
{
    private const VIEW_PATH = APPPATH . 'Views/restaurant-system/main.php';
    
    private const DEFAULT_BRAND_ID  = '00000000-0000-0000-0000-000000000001';
    private const DEFAULT_BRANCH_ID = '00000000-0000-0000-0000-000000000101';
    
    /**
     * @var RestMainModel
     */
    private $model;
    
    public function __construct()
    {
        parent::__construct();
        $this->model = new RestMainModel();
    }
    
    public function index(): void
    {
        try {
            $brandId  = $this->resolveBrandId();
            $branchId = $this->resolveBranchId();
            
            $context = $this->model->getMainContext($brandId, $branchId);
            
            $brand  = is_array($context['brand'] ?? null) ? $context['brand'] : [];
            $branch = is_array($context['branch'] ?? null) ? $context['branch'] : [];
            $stats  = is_array($context['stats'] ?? null) ? $context['stats'] : [];
            
            $brandProfile = [
                'brand_name'           => trim((string) ($brand['name'] ?? 'Restaurant System')),
                'platform_name'        => 'Restaurant Management System',
                'status'               => ucfirst(strtolower(trim((string) ($brand['status'] ?? 'active')))),
                'scope'                => 'Brand & branch scoped access',
                'slug'                 => trim((string) ($brand['slug'] ?? '')),
                'legal_name'           => trim((string) ($brand['legal_name'] ?? '')),
                'logo'                 => trim((string) ($brand['logo'] ?? '')),
                'favicon'              => trim((string) ($brand['favicon'] ?? '')),
                'mobile'               => trim((string) ($brand['mobile'] ?? '')),
                'mail_id'              => trim((string) ($brand['mail_id'] ?? '')),
                'subscription_plan'    => strtolower(trim((string) ($brand['subscription_plan'] ?? 'starter'))),
                'timezone'             => trim((string) ($brand['timezone'] ?? 'Asia/Kolkata')),
                'currency'             => strtoupper(trim((string) ($brand['currency'] ?? 'INR'))),
                'is_setup_completed'   => (int) ($brand['is_setup_completed'] ?? 0),
                
                'branch_name'          => trim((string) ($branch['name'] ?? 'Main Branch')),
                'branch_code'          => trim((string) ($branch['code'] ?? '')),
                'branch_city'          => trim((string) ($branch['city'] ?? '')),
                'branch_address'       => trim((string) ($branch['address'] ?? '')),
                'branch_phone'         => trim((string) ($branch['phone'] ?? '')),
                'branch_email'         => trim((string) ($branch['email'] ?? '')),
                'branch_status'        => ucfirst(strtolower(trim((string) ($branch['status'] ?? 'active')))),
                
                'branches_count'       => (int) ($stats['branches_count'] ?? 0),
                'today_orders'         => (int) ($stats['today_orders'] ?? 0),
                'total_tables'         => (int) ($stats['total_tables'] ?? 0),
                'active_tables'        => (int) ($stats['active_tables'] ?? 0),
                'pending_requests'     => (int) ($stats['pending_requests'] ?? 0),
                'live_kitchen_tickets' => (int) ($stats['live_kitchen_tickets'] ?? 0),
            ];
            
            $moduleUrls = [
                'brand' => $this->buildModuleUrl('brand', [
                    'brand_id' => $brandId,
                ]),
                'customer' => $this->buildModuleUrl('customer', [
                    'brand_id'  => $brandId,
                    'branch_id' => $branchId,
                ]),
                'waiter' => $this->buildModuleUrl('waiter', [
                    'brand_id'  => $brandId,
                    'branch_id' => $branchId,
                ]),
                'kitchen' => $this->buildModuleUrl('kitchen', [
                    'brand_id'  => $brandId,
                    'branch_id' => $branchId,
                ]),
                'manager' => $this->buildModuleUrl('manager', [
                    'brand_id'  => $brandId,
                    'branch_id' => $branchId,
                ]),
            ];
            
            $this->renderView(self::VIEW_PATH, [
                'pageTitle'    => 'Restaurant Management System',
                'brandId'      => $brandId,
                'branchId'     => $branchId,
                'brandProfile' => $brandProfile,
                'moduleUrls'   => $moduleUrls,
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Unable to load restaurant main page: ' . $e->getMessage();
        }
    }
    
    /**
     * @return string
     */
    private function resolveBrandId(): string
    {
        $queryValue = $_GET['brand_id'] ?? null;
        if (is_string($queryValue) && $this->isValidEntityId($queryValue)) {
            return strtolower(trim($queryValue));
        }
        
        if (property_exists($this, 'brandId') && is_string($this->brandId) && $this->isValidEntityId($this->brandId)) {
            return strtolower(trim($this->brandId));
        }
        
        return self::DEFAULT_BRAND_ID;
    }
    
    /**
     * @return string
     */
    private function resolveBranchId(): string
    {
        $queryValue = $_GET['branch_id'] ?? null;
        if (is_string($queryValue) && $this->isValidEntityId($queryValue)) {
            return strtolower(trim($queryValue));
        }
        
        if (property_exists($this, 'branchId') && is_string($this->branchId) && $this->isValidEntityId($this->branchId)) {
            return strtolower(trim($this->branchId));
        }
        
        return self::DEFAULT_BRANCH_ID;
    }
    
    /**
     * @param string $module
     * @param array $query
     * @return string
     */
    private function buildModuleUrl(string $module, array $query = []): string
    {
        $module = trim(strtolower($module));
        $allowed = ['main', 'brand', 'customer', 'waiter', 'kitchen', 'manager'];
        
        if (!in_array($module, $allowed, true)) {
            $module = 'main';
        }
        
        $path = $module === 'main'
            ? 'restaurant-system/main'
            : 'restaurant-system/' . $module;
        
        $base = $this->baseUrl();
        $url  = rtrim($base, '/') . '/' . ltrim($path, '/');
        
        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }
        
        return $url;
    }
    
    /**
     * @return string
     */
    private function baseUrl(): string
    {
        if (function_exists('base_url')) {
            return rtrim((string) base_url(), '/');
        }
        
        $https  = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $scheme = $https ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        return $scheme . '://' . $host;
    }
}
