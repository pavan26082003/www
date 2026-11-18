<?php
/**
 * Phase B — opt-in compact schema projection.
 *
 * Default API responses stay FULL (older installed apps).
 * Pass compact=1 (or projection=compact) to get the trimmed sample shape.
 *
 * Keeps every field family the app listed as required; collapses duplicate
 * frame/config projections onto node_details[].config and points frameSource there.
 */

if (!function_exists('efactor_schema_wants_compact')) {
    /**
     * @return bool
     */
    function efactor_schema_wants_compact()
    {
        $compact = isset($_GET['compact']) ? strtolower(trim((string) $_GET['compact'])) : '';
        if (in_array($compact, array('1', 'true', 'yes', 'on'), true)) {
            return true;
        }
        $projection = isset($_GET['projection']) ? strtolower(trim((string) $_GET['projection'])) : '';

        return in_array($projection, array('compact', 'compact-v1', 'b'), true);
    }
}

if (!function_exists('efactor_schema_is_revision_only')) {
    /**
     * Tiny layout-version probe: part=revision|version
     *
     * @param string $part
     * @return bool
     */
    function efactor_schema_is_revision_only($part)
    {
        $part = strtolower(trim((string) $part));

        return in_array($part, array('revision', 'version', 'layout_version'), true);
    }
}

if (!function_exists('efactor_schema_prune_empties')) {
    /**
     * Remove null / empty-string leaves only (keep []/{} — app may rely on them).
     *
     * @param mixed $node
     * @param string $path
     * @return mixed
     */
    function efactor_schema_prune_empties($node, $path = '')
    {
        if (!is_array($node)) {
            return $node;
        }

        // Never strip design tokens / required maps even if some values look empty.
        $protectedExact = array(
            'theme',
            'theme.colors',
            'theme.radius',
            'extensions',
            'extensions.universalUi',
            'extensions.universalUi.designSystem',
            'extensions.universalUi.designSystem.componentStyles',
            'meta.links',
            'apiContracts',
            'dataSources',
            'controls',
            'node_details',
            'wifiProvisioning',
            'infoPage',
            'pages',
            'navigation',
            'paging',
        );
        if (in_array($path, $protectedExact, true)) {
            foreach ($node as $k => $v) {
                $childPath = $path === '' ? (string) $k : $path . '.' . $k;
                $node[$k] = efactor_schema_prune_empties($v, $childPath);
            }

            return $node;
        }

        $out = array();
        foreach ($node as $k => $v) {
            $childPath = $path === '' ? (string) $k : $path . '.' . $k;
            if (is_array($v)) {
                // Keep [] / {} — app relies on empty props/objects in places.
                $out[$k] = efactor_schema_prune_empties($v, $childPath);
                continue;
            }
            if ($v === null || $v === '') {
                continue;
            }
            $out[$k] = $v;
        }

        return $out;
    }
}

if (!function_exists('efactor_schema_apply_compact')) {
    /**
     * @param array $schema
     * @param string $modelNo
     * @param string $nodeId
     * @return array
     */
    function efactor_schema_apply_compact(array $schema, $modelNo = '', $nodeId = '')
    {
        $modelNo = trim((string) $modelNo);
        $nodeId = trim((string) $nodeId);

        // Ensure authoritative node_details[0].config exists before dropping aliases.
        $config = null;
        if (isset($schema['node_details'][0]['config']) && is_array($schema['node_details'][0]['config'])) {
            $config = $schema['node_details'][0]['config'];
        } elseif (isset($schema['deviceConfig']) && is_array($schema['deviceConfig'])) {
            $config = $schema['deviceConfig'];
        } elseif (isset($schema['state']['device']['config']) && is_array($schema['state']['device']['config'])) {
            $config = $schema['state']['device']['config'];
        } elseif (isset($schema['deviceState']['config']) && is_array($schema['deviceState']['config'])) {
            $config = $schema['deviceState']['config'];
        }

        if (is_array($config)) {
            if (!isset($schema['node_details']) || !is_array($schema['node_details'])) {
                $schema['node_details'] = array();
            }
            if (!isset($schema['node_details'][0]) || !is_array($schema['node_details'][0])) {
                $schema['node_details'][0] = array();
            }
            if ($nodeId !== '' && empty($schema['node_details'][0]['id'])) {
                $schema['node_details'][0]['id'] = $nodeId;
            }
            $schema['node_details'][0]['config'] = $config;
        }

        $frameSource = 'node_details[0].config.devices[0].pages';

        if (!isset($schema['apiContracts']) || !is_array($schema['apiContracts'])) {
            $schema['apiContracts'] = array();
        }
        if (!isset($schema['apiContracts']['deviceCommands']) || !is_array($schema['apiContracts']['deviceCommands'])) {
            $schema['apiContracts']['deviceCommands'] = array();
        }
        $schema['apiContracts']['deviceCommands']['frameSource'] = $frameSource;

        if (isset($schema['dataSources']) && is_array($schema['dataSources'])) {
            if (!isset($schema['dataSources']['deviceCommands']) || !is_array($schema['dataSources']['deviceCommands'])) {
                $schema['dataSources']['deviceCommands'] = array();
            }
            $schema['dataSources']['deviceCommands']['frameSource'] = $frameSource;
        }

        // Drop duplicate config projections (highest size win after controls).
        unset($schema['deviceConfig']);
        if (isset($schema['state']) && is_array($schema['state']) && isset($schema['state']['device']) && is_array($schema['state']['device'])) {
            unset($schema['state']['device']['config']);
            if ($schema['state']['device'] === array()) {
                unset($schema['state']['device']);
            }
        }
        if (isset($schema['deviceState']) && is_array($schema['deviceState'])) {
            unset($schema['deviceState']['config']);
            if (!isset($schema['deviceState']['liveStatePath'])) {
                $schema['deviceState']['liveStatePath'] = 'node_details[0].config.devices[0]';
            }
            if (!array_key_exists('preferLiveState', $schema['deviceState'])) {
                $schema['deviceState']['preferLiveState'] = true;
            }
            if ($nodeId !== '' && empty($schema['deviceState']['node_id'])) {
                $schema['deviceState']['node_id'] = $nodeId;
            }
        }
        // Debug/alias duplicate of node_details — safe to drop in compact.
        unset($schema['v2_node_details']);

        // Identity fields the app expects.
        if ($nodeId !== '') {
            $schema['node_id'] = $nodeId;
            if (!isset($schema['nodeId'])) {
                $schema['nodeId'] = $nodeId;
            }
        }
        if ($modelNo !== '') {
            if (!isset($schema['model_no'])) {
                $schema['model_no'] = $modelNo;
            }
            if (!isset($schema['modelNo'])) {
                $schema['modelNo'] = $modelNo;
            }
        }

        // Soft prune: null / "" only — never strip required token maps / empty props.
        $schema = efactor_schema_prune_empties($schema, '');

        if (!isset($schema['meta']) || !is_array($schema['meta'])) {
            $schema['meta'] = array();
        }
        $schema['meta']['projection'] = 'compact-v1';
        $schema['meta']['frameSource'] = $frameSource;
        $schema['_schemaProjection'] = 'compact-v1';

        return $schema;
    }
}
