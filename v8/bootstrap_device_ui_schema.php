<?php

/**
 * Creates or refreshes the device UI schema for a model when the mobile API is
 * called. Missing rows are generated; existing rows are synced from current
 * pages/APIs/page-linking without requiring Design Studio "Save Changes".
 * Pages and controls come from the model, not the logged-in profile.
 * An empty model still gets one shell page.
 */
function efactor_ensure_device_ui_schema($modelno, $nodeId = '')
{
    $modelno = trim((string) $modelno);
    $nodeId = trim((string) $nodeId);
    if ($modelno === '') {
        return false;
    }

    @ini_set('memory_limit', '2048M');

    // CodeIgniter App config reads HTTP_HOST during boot. Standalone / CLI /
    // some reverse-proxy calls can omit it and cascade into fatal errors.
    if (empty($_SERVER['HTTP_HOST'])) {
        $fallbackHost = 'localhost';
        if (!empty($_SERVER['SERVER_NAME'])) {
            $fallbackHost = (string) $_SERVER['SERVER_NAME'];
        }
        $_SERVER['HTTP_HOST'] = $fallbackHost;
    }
    if (empty($_SERVER['SCRIPT_NAME'])) {
        $_SERVER['SCRIPT_NAME'] = '/index.php';
    }
    if (!isset($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = '/';
    }

    $bootstrapCandidates = array(
        __DIR__ . '/../app/Libraries/AppHomeBootstrap.php',
        dirname(__DIR__) . '/app/Libraries/AppHomeBootstrap.php',
    );
    $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
    if ($docRoot !== '') {
        $bootstrapCandidates[] = $docRoot . '/app/Libraries/AppHomeBootstrap.php';
    }

    $bootstrapped = false;
    foreach ($bootstrapCandidates as $path) {
        if (!is_file($path)) {
            continue;
        }
        require_once $path;
        if (!class_exists(\App\Libraries\AppHomeBootstrap::class)) {
            continue;
        }
        try {
            \App\Libraries\AppHomeBootstrap::bootIfNeeded();
            $bootstrapped = true;
            break;
        } catch (Throwable $exception) {
            error_log('Device UI schema bootstrap failed: ' . $exception->getMessage());
        }
    }

    if (!$bootstrapped || !class_exists(\App\Models\View_page_model::class)) {
        error_log('Device UI schema auto-generate unavailable for ' . $modelno . ' (CI boot or View_page_model missing)');
        return false;
    }

    try {
        $model = new \App\Models\View_page_model();

        // Prefer the write-side entry point (create + verify persisted row).
        if (method_exists($model, 'autoPersistDeviceUiSchema')) {
            $result = $model->autoPersistDeviceUiSchema($modelno, $nodeId);
            if (!empty($result['saved']) || !empty($result['synced'])) {
                return true;
            }
        }

        if (method_exists($model, 'ensureDeviceUiSchema')) {
            $schema = $model->ensureDeviceUiSchema($modelno, $nodeId, true);
            if (is_array($schema) && !empty($schema['schemaType'])) {
                // ensureDeviceUiSchema now verifies the DB row before returning.
                return true;
            }
        }

        error_log('Device UI schema auto-generate produced no persisted row for ' . $modelno);

        return false;
    } catch (Throwable $exception) {
        error_log('Device UI schema auto-generate failed for ' . $modelno . ': ' . $exception->getMessage());

        return false;
    }
}

/**
 * One-shot backfill for existing models missing device_ui_schemas rows.
 */
function efactor_backfill_missing_device_ui_schemas($limit = 100)
{
    if (!function_exists('efactor_ensure_device_ui_schema')) {
        return array('processed' => 0, 'saved' => 0, 'failed' => 0, 'models' => array());
    }

    // Boot via a no-op ensure path helper so CI + View_page_model are available.
    @ini_set('memory_limit', '2048M');
    if (empty($_SERVER['HTTP_HOST'])) {
        $_SERVER['HTTP_HOST'] = !empty($_SERVER['SERVER_NAME']) ? (string) $_SERVER['SERVER_NAME'] : 'localhost';
    }
    if (empty($_SERVER['SCRIPT_NAME'])) {
        $_SERVER['SCRIPT_NAME'] = '/index.php';
    }

    $bootstrapCandidates = array(
        __DIR__ . '/../app/Libraries/AppHomeBootstrap.php',
        dirname(__DIR__) . '/app/Libraries/AppHomeBootstrap.php',
    );
    foreach ($bootstrapCandidates as $path) {
        if (!is_file($path)) {
            continue;
        }
        require_once $path;
        if (class_exists(\App\Libraries\AppHomeBootstrap::class)) {
            try {
                \App\Libraries\AppHomeBootstrap::bootIfNeeded();
            } catch (Throwable $exception) {
                error_log('Device UI schema backfill boot failed: ' . $exception->getMessage());
                return array('processed' => 0, 'saved' => 0, 'failed' => 0, 'models' => array(), 'error' => $exception->getMessage());
            }
            break;
        }
    }

    if (!class_exists(\App\Models\View_page_model::class)) {
        return array('processed' => 0, 'saved' => 0, 'failed' => 0, 'models' => array(), 'error' => 'View_page_model missing');
    }

    try {
        $model = new \App\Models\View_page_model();
        if (method_exists($model, 'backfillMissingDeviceUiSchemas')) {
            return $model->backfillMissingDeviceUiSchemas($limit);
        }
    } catch (Throwable $exception) {
        error_log('Device UI schema backfill failed: ' . $exception->getMessage());
        return array('processed' => 0, 'saved' => 0, 'failed' => 0, 'models' => array(), 'error' => $exception->getMessage());
    }

    return array('processed' => 0, 'saved' => 0, 'failed' => 0, 'models' => array());
}
