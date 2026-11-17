<?php
declare(strict_types=1);

function http_json(string $url): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 90,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    $json = is_string($body) ? json_decode($body, true) : null;
    return [$code, is_array($json) ? $json : [], (string) $body, $err];
}

function top_keys($arr): array {
    return is_array($arr) ? array_keys($arr) : [];
}

function walk_command_fields(array $nodeDetails): array {
    $fields = [];
    $emptyAsperui = 0;
    $nullAsperui = 0;
    $nullValue = 0;
    $emptyValue = 0;
    $hiddenLike = 0; // not present - we only see what remains
    $pages = [];
    $uiTypes = [];
    $cmdKeysUnion = [];
    $sampleEmpty = [];
    $sampleNull = [];

    foreach ($nodeDetails as $nd) {
        $devices = $nd['config']['devices'] ?? [];
        if (!is_array($devices)) continue;
        foreach ($devices as $dev) {
            $pageMap = $dev['pages'] ?? [];
            if (!is_array($pageMap)) continue;
            foreach ($pageMap as $pageName => $frames) {
                $pages[] = (string) $pageName;
                if (!is_array($frames)) continue;
                foreach ($frames as $frameWrap) {
                    $frame = $frameWrap['frame'] ?? $frameWrap;
                    if (!is_array($frame)) continue;
                    foreach (['commands', 'response', 'error'] as $bucket) {
                        $list = $frame[$bucket] ?? [];
                        if (!is_array($list)) continue;
                        foreach ($list as $cmd) {
                            if (!is_array($cmd)) continue;
                            foreach (array_keys($cmd) as $k) {
                                $cmdKeysUnion[$k] = true;
                            }
                            $ui = (string) ($cmd['ui_type'] ?? '');
                            if ($ui !== '') $uiTypes[$ui] = ($uiTypes[$ui] ?? 0) + 1;
                            $desc = (string) ($cmd['Field_description'] ?? $cmd['default_name'] ?? '');
                            $val = $cmd['value'] ?? null;
                            $av = $cmd['asperui_value'] ?? null;
                            $as = $cmd['asperui'] ?? null;

                            $isDataish = strcasecmp($desc, 'Data') === 0
                                || (!in_array(strtolower($desc), ['commandid', 'function code', 'terminator', 'trigger'], true)
                                    && !in_array(strtolower((string)($cmd['default_name'] ?? '')), ['commandid', 'function code', 'terminator'], true)
                                    && $ui !== '' && strcasecmp($ui, 'None') !== 0);

                            if (!$isDataish && strcasecmp($ui, 'None') === 0) {
                                // still track empties lightly
                            }

                            $fields[] = [
                                'page' => $pageName,
                                'frame' => $frame['name'] ?? '',
                                'bucket' => $bucket,
                                'name' => $cmd['default_name'] ?? '',
                                'desc' => $desc,
                                'ui' => $ui,
                                'value' => $val,
                                'asperui_value' => $av,
                                'asperui' => $as,
                            ];

                            if ($val === null) $nullValue++;
                            if ($val === '') $emptyValue++;
                            if ($as === null) $nullAsperui++;
                            if (is_array($as) && count($as) === 0) {
                                $emptyAsperui++;
                                if (count($sampleEmpty) < 5) {
                                    $sampleEmpty[] = [
                                        'page' => $pageName,
                                        'frame' => $frame['name'] ?? '',
                                        'name' => $cmd['default_name'] ?? '',
                                        'value' => $val,
                                        'asperui_value' => $av,
                                        'asperui' => $as,
                                    ];
                                }
                            }
                            if (($val === null || $val === '') && ($av === null || $av === '') && count($sampleNull) < 5) {
                                $sampleNull[] = [
                                    'page' => $pageName,
                                    'frame' => $frame['name'] ?? '',
                                    'name' => $cmd['default_name'] ?? '',
                                    'value' => $val,
                                    'asperui_value' => $av,
                                    'asperui' => $as,
                                    'ui' => $ui,
                                ];
                            }
                        }
                    }
                }
            }
        }
    }

    return [
        'field_count' => count($fields),
        'pages' => array_values(array_unique($pages)),
        'ui_types' => $uiTypes,
        'cmd_keys' => array_keys($cmdKeysUnion),
        'null_value' => $nullValue,
        'empty_value' => $emptyValue,
        'null_asperui' => $nullAsperui,
        'empty_asperui_arrays' => $emptyAsperui,
        'sample_empty_asperui' => $sampleEmpty,
        'sample_null_or_empty_value' => $sampleNull,
        'fields' => $fields,
    ];
}

function node_shape(array $json): array {
    $nd = $json['node_details'][0] ?? null;
    $dev = $nd['config']['devices'][0] ?? null;
    $cfg = $nd['config'] ?? null;
    return [
        'top_keys' => top_keys($json),
        'has_node_details' => isset($json['node_details']),
        'has_nodes' => isset($json['nodes']),
        'has_v2_alias' => isset($json['v2_node_details']),
        'has_status_flag' => array_key_exists('status', $json) && !isset($json['node_details'][0]['status']),
        'node_detail_keys' => is_array($nd) ? top_keys($nd) : [],
        'config_keys' => is_array($cfg) ? top_keys($cfg) : [],
        'device_keys' => is_array($dev) ? top_keys($dev) : [],
        'device_name' => is_array($dev) ? ($dev['name'] ?? null) : null,
        'page_keys' => (is_array($dev) && isset($dev['pages']) && is_array($dev['pages'])) ? array_keys($dev['pages']) : [],
        'info_keys' => isset($nd['info']) && is_array($nd['info']) ? top_keys($nd['info']) : [],
        'role' => $nd['role'] ?? null,
        'connectivity' => $nd['status']['connectivity'] ?? null,
        'config_version' => is_array($cfg) ? ($cfg['config_version'] ?? null) : null,
        'primary' => is_array($dev) ? ($dev['primary'] ?? null) : null,
        'type' => is_array($dev) ? ($dev['type'] ?? null) : null,
        'walk' => is_array($nd) ? walk_command_fields([$nd]) : walk_command_fields([]),
    ];
}

function frame_skeleton(array $nodeDetails): array {
    $out = [];
    foreach ($nodeDetails as $nd) {
        $devices = $nd['config']['devices'] ?? [];
        foreach ($devices as $dev) {
            foreach (($dev['pages'] ?? []) as $page => $frames) {
                if (!is_array($frames)) continue;
                foreach ($frames as $fw) {
                    $f = $fw['frame'] ?? null;
                    if (!is_array($f)) continue;
                    $cmds = [];
                    foreach (($f['commands'] ?? []) as $c) {
                        if (!is_array($c)) continue;
                        $cmds[] = [
                            'default_name' => $c['default_name'] ?? null,
                            'Field_description' => $c['Field_description'] ?? null,
                            'ui_type' => $c['ui_type'] ?? null,
                            'no_of_bytes' => $c['no_of_bytes'] ?? null,
                            'byte_start_position' => $c['byte_start_position'] ?? null,
                            'local_control' => $c['local_control'] ?? null,
                            'has_asperui' => array_key_exists('asperui', $c),
                            'has_asperui_value' => array_key_exists('asperui_value', $c),
                            'has_value' => array_key_exists('value', $c),
                            'asperui_is_array' => isset($c['asperui']) && is_array($c['asperui']),
                            'asperui_len' => (isset($c['asperui']) && is_array($c['asperui'])) ? count($c['asperui']) : null,
                            'value_type' => array_key_exists('value', $c) ? gettype($c['value']) : 'missing',
                            'asperui_value_type' => array_key_exists('asperui_value', $c) ? gettype($c['asperui_value']) : 'missing',
                        ];
                    }
                    $resp = [];
                    foreach (($f['response'] ?? []) as $c) {
                        if (!is_array($c)) continue;
                        $resp[] = $c['default_name'] ?? null;
                    }
                    $out[] = [
                        'page' => $page,
                        'frame' => $f['name'] ?? '',
                        'multiple_ui' => $f['multiple_ui'] ?? null,
                        'trigger_ui' => $f['trigger_ui'] ?? null,
                        'cmd_count' => count($cmds),
                        'resp_count' => count($resp),
                        'resp_names' => $resp,
                        'cmds' => $cmds,
                    ];
                }
            }
        }
    }
    return $out;
}

$urls = [
    'legacy_cooker' => 'https://efactor.tech/v8/newapi.php?action=test_nodes&node_id=FFFFFFFFFFFFFFFF7EC200000004F200',
    'legacy_fan' => 'https://efactor.tech/v8/newapi.php?action=test_nodes&node_id=FFFFFFFFFFFFFFFF7EC200000005E800',
    'v2_fan' => 'https://efactor.tech/v8/v2_test_nodes.php?model_no=Ef-fan-hv-d-15-10&node_id=FFFFFFFFFFFFFFFF7EC200000005E800',
];

$shapes = [];
foreach ($urls as $label => $url) {
    [$code, $json, $body, $err] = http_json($url);
    $shapes[$label] = [
        'http' => $code,
        'bytes' => strlen($body),
        'err' => $err,
        'shape' => node_shape($json),
        'frames' => !empty($json['node_details']) ? frame_skeleton($json['node_details']) : [],
        'raw_top' => top_keys($json),
    ];
    file_put_contents(
        'c:/Program Files/Ampps/www/app/writable/_shape_' . $label . '.json',
        json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
}

// Compare envelope keys
$l = $shapes['legacy_cooker']['shape'];
$v = $shapes['v2_fan']['shape'];
$lf = $shapes['legacy_fan']['shape'];

$cmdKeyLegacy = $l['walk']['cmd_keys'];
$cmdKeyV2 = $v['walk']['cmd_keys'];
sort($cmdKeyLegacy);
sort($cmdKeyV2);
$onlyLegacy = array_values(array_diff($cmdKeyLegacy, $cmdKeyV2));
$onlyV2 = array_values(array_diff($cmdKeyV2, $cmdKeyLegacy));
$common = array_values(array_intersect($cmdKeyLegacy, $cmdKeyV2));

$report = [
    'summary' => [
        'same_top_envelope' => (
            in_array('node_details', $l['top_keys'], true)
            && in_array('nodes', $l['top_keys'], true)
            && in_array('node_details', $v['top_keys'], true)
            && in_array('nodes', $v['top_keys'], true)
        ),
        'legacy_cooker_top' => $l['top_keys'],
        'legacy_fan_top' => $lf['top_keys'],
        'v2_fan_top' => $v['top_keys'],
        'v2_extra_top_keys' => array_values(array_diff($v['top_keys'], $l['top_keys'])),
        'legacy_extra_top_keys' => array_values(array_diff($l['top_keys'], $v['top_keys'])),
        'node_detail_keys_legacy' => $l['node_detail_keys'],
        'node_detail_keys_v2' => $v['node_detail_keys'],
        'node_detail_keys_only_legacy' => array_values(array_diff($l['node_detail_keys'], $v['node_detail_keys'])),
        'node_detail_keys_only_v2' => array_values(array_diff($v['node_detail_keys'], $l['node_detail_keys'])),
        'config_keys_legacy' => $l['config_keys'],
        'config_keys_v2' => $v['config_keys'],
        'device_keys_legacy' => $l['device_keys'],
        'device_keys_v2' => $v['device_keys'],
        'device_keys_only_legacy' => array_values(array_diff($l['device_keys'], $v['device_keys'])),
        'device_keys_only_v2' => array_values(array_diff($v['device_keys'], $l['device_keys'])),
        'pages_legacy_cooker' => $l['page_keys'],
        'pages_legacy_fan' => $lf['page_keys'],
        'pages_v2_fan' => $v['page_keys'],
        'cmd_keys_common_count' => count($common),
        'cmd_keys_only_legacy' => $onlyLegacy,
        'cmd_keys_only_v2' => $onlyV2,
        'empty_handling' => [
            'legacy_cooker' => [
                'null_value' => $l['walk']['null_value'],
                'empty_value' => $l['walk']['empty_value'],
                'null_asperui' => $l['walk']['null_asperui'],
                'empty_asperui_arrays' => $l['walk']['empty_asperui_arrays'],
                'samples_empty_asperui' => $l['walk']['sample_empty_asperui'],
                'samples_null_empty' => $l['walk']['sample_null_or_empty_value'],
            ],
            'legacy_fan' => [
                'null_value' => $lf['walk']['null_value'],
                'empty_value' => $lf['walk']['empty_value'],
                'null_asperui' => $lf['walk']['null_asperui'],
                'empty_asperui_arrays' => $lf['walk']['empty_asperui_arrays'],
                'samples_empty_asperui' => $lf['walk']['sample_empty_asperui'],
                'samples_null_empty' => $lf['walk']['sample_null_or_empty_value'],
            ],
            'v2_fan' => [
                'null_value' => $v['walk']['null_value'],
                'empty_value' => $v['walk']['empty_value'],
                'null_asperui' => $v['walk']['null_asperui'],
                'empty_asperui_arrays' => $v['walk']['empty_asperui_arrays'],
                'samples_empty_asperui' => $v['walk']['sample_empty_asperui'],
                'samples_null_empty' => $v['walk']['sample_null_or_empty_value'],
            ],
        ],
        'frame_counts' => [
            'legacy_cooker' => count($shapes['legacy_cooker']['frames']),
            'legacy_fan' => count($shapes['legacy_fan']['frames']),
            'v2_fan' => count($shapes['v2_fan']['frames']),
        ],
        'connectivity' => [
            'legacy_cooker' => $l['connectivity'],
            'legacy_fan' => $lf['connectivity'],
            'v2_fan' => $v['connectivity'],
        ],
    ],
    'legacy_fan_vs_v2_fan_pages' => [
        'legacy' => $lf['page_keys'],
        'v2' => $v['page_keys'],
        'same_pages' => $lf['page_keys'] === $v['page_keys'],
    ],
    'legacy_fan_frames' => array_map(static function ($f) {
        return $f['page'] . ' / ' . $f['frame'] . ' cmds=' . $f['cmd_count'] . ' resp=' . $f['resp_count'];
    }, $shapes['legacy_fan']['frames']),
    'v2_fan_frames' => array_map(static function ($f) {
        return $f['page'] . ' / ' . $f['frame'] . ' cmds=' . $f['cmd_count'] . ' resp=' . $f['resp_count'];
    }, $shapes['v2_fan']['frames']),
    'http' => [
        'legacy_cooker' => $shapes['legacy_cooker']['http'],
        'legacy_fan' => $shapes['legacy_fan']['http'],
        'v2_fan' => $shapes['v2_fan']['http'],
        'bytes' => [
            'legacy_cooker' => $shapes['legacy_cooker']['bytes'],
            'legacy_fan' => $shapes['legacy_fan']['bytes'],
            'v2_fan' => $shapes['v2_fan']['bytes'],
        ],
    ],
];

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
file_put_contents(
    'c:/Program Files/Ampps/www/app/writable/_shape_compare_report.json',
    json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);
