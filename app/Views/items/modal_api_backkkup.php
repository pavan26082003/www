<?php
$decodeConfigurationValue = static function ($value) {
  if (is_array($value)) {
    return $value;
  }
  if (is_object($value)) {
    return get_object_vars($value);
  }
  if (!is_string($value) || trim($value) === '') {
    return array();
  }
  $candidate = html_entity_decode(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
  for ($attempt = 0; $attempt < 4; $attempt++) {
    $decoded = json_decode($candidate, true);
    if (is_array($decoded)) {
      return $decoded;
    }
    if (!is_string($decoded) || trim($decoded) === '') {
      break;
    }
    $candidate = html_entity_decode(trim($decoded), ENT_QUOTES | ENT_HTML5, 'UTF-8');
  }
  return array();
};

$itemId = (int) ($itemid ?? 0);
$configurationId = null;
foreach (array(
  $edit_config_id ?? null,
  $config_id ?? null,
  $configuration_id ?? null,
  $api_config_id ?? null,
  $api_id ?? null,
  $configid ?? null
) as $configurationIdCandidate) {
  if ((int) $configurationIdCandidate > 0) {
    $configurationId = (int) $configurationIdCandidate;
    break;
  }
}

$editConfigurationData = array();
foreach (array(
  $edit_config_data ?? null,
  $api_config_data ?? null,
  $configuration_data ?? null,
  $config_data ?? null,
  $api_configuration ?? null,
  $saved_configuration ?? null
) as $configurationDataCandidate) {
  $decodedConfigurationData = $decodeConfigurationValue($configurationDataCandidate);
  if ($decodedConfigurationData) {
    $editConfigurationData = $decodedConfigurationData;
    break;
  }
}
$editPayload = array();
foreach (array('payload', 'configuration', 'full_config_json', 'configuration_json', 'config_json') as $payloadKey) {
  if (!array_key_exists($payloadKey, $editConfigurationData)) {
    continue;
  }
  $candidatePayload = $decodeConfigurationValue($editConfigurationData[$payloadKey]);
  if ($candidatePayload) {
    $editPayload = $candidatePayload;
    break;
  }
}
if (!$editPayload) {
  foreach (array(
    $full_config_json ?? null,
    $configuration_json ?? null,
    $saved_configuration ?? null
  ) as $standalonePayload) {
    $candidatePayload = $decodeConfigurationValue($standalonePayload);
    if ($candidatePayload) {
      $editPayload = $candidatePayload;
      break;
    }
  }
}
if (!$editPayload && (
  isset($editConfigurationData['product'])
  || isset($editConfigurationData['commands'])
  || isset($editConfigurationData['responses'])
  || isset($editConfigurationData['errors'])
)) {
  $editPayload = $editConfigurationData;
}
foreach (array('product', 'commands', 'responses', 'errors') as $payloadSection) {
  if (isset($editPayload[$payloadSection]) && is_string($editPayload[$payloadSection])) {
    $editPayload[$payloadSection] = $decodeConfigurationValue($editPayload[$payloadSection]);
  }
}
// The edit action can return the original JSON and reconstructed database rows
// as separate values. Merge both representations so frame values, partitions,
// and bitwise rows are all available to the browser-side restore logic.
foreach (array('commands', 'responses', 'errors') as $payloadSection) {
  if (!array_key_exists($payloadSection, $editConfigurationData)) {
    continue;
  }
  $outerSection = $decodeConfigurationValue($editConfigurationData[$payloadSection]);
  if (!$outerSection) {
    continue;
  }
  $innerSection = isset($editPayload[$payloadSection])
    ? $decodeConfigurationValue($editPayload[$payloadSection])
    : array();
  $editPayload[$payloadSection] = $innerSection
    ? array_replace_recursive($outerSection, $innerSection)
    : $outerSection;
}
$separateFrameSections = array(
  'commands' => array(
    $commands ?? null,
    $command_data ?? null,
    $command_frames ?? null,
    $cmd_data ?? null,
    $edit_commands ?? null
  ),
  'responses' => array(
    $responses ?? null,
    $response_data ?? null,
    $response_frames ?? null,
    $resp_data ?? null,
    $edit_responses ?? null
  ),
  'errors' => array(
    $errors ?? null,
    $error_data ?? null,
    $error_frames ?? null,
    $edit_errors ?? null
  )
);
foreach ($separateFrameSections as $payloadSection => $sectionCandidates) {
  foreach ($sectionCandidates as $sectionCandidate) {
    $decodedSection = $decodeConfigurationValue($sectionCandidate);
    if (!$decodedSection) {
      continue;
    }
    $currentSection = isset($editPayload[$payloadSection])
      ? $decodeConfigurationValue($editPayload[$payloadSection])
      : array();
    $editPayload[$payloadSection] = $currentSection
      ? array_replace_recursive($decodedSection, $currentSection)
      : $decodedSection;
    break;
  }
}
$editProduct = isset($editConfigurationData['product'])
  ? $decodeConfigurationValue($editConfigurationData['product'])
  : array();
$payloadProduct = isset($editPayload['product'])
  && is_array($editPayload['product'])
  ? $editPayload['product']
  : array();

$resolvedEditProduct = array_merge($payloadProduct, $editProduct);
if (!empty($resolvedEditProduct)) {
  // The edit model returns product defaults beside the nested payload.
  // Keep them together for the browser-side restoration code so the
  // parameter name and saved frame counts are available immediately.
  $editPayload['product'] = array_merge(
    isset($editPayload['product']) && is_array($editPayload['product'])
      ? $editPayload['product']
      : array(),
    $resolvedEditProduct
  );
}
$flatCommandValue = (string) (
  $resolvedEditProduct['command_value']
  ?? $resolvedEditProduct['commandValue']
  ?? $resolvedEditProduct['command_code']
  ?? $resolvedEditProduct['commandCode']
  ?? ($command_value ?? ($command_code ?? ($full_api_value ?? '')))
);
if ($flatCommandValue !== '') {
  if (!isset($editPayload['commands']) || !is_array($editPayload['commands'])) {
    $editPayload['commands'] = array();
  }
  if (!isset($editPayload['commands'][0]) || !is_array($editPayload['commands'][0])) {
    $editPayload['commands'][0] = array();
  }
  if (
    empty($editPayload['commands'][0]['commandCode'])
    && empty($editPayload['commands'][0]['frameCode'])
    && empty($editPayload['commands'][0]['commandValue'])
  ) {
    $editPayload['commands'][0]['commandValue'] = $flatCommandValue;
  }
}
$isEditMode = $configurationId !== null || !empty($editPayload);
$initialConfiguration = !empty($editPayload) ? $editPayload : null;

$brandValue = (string) (
  $resolvedEditProduct['brandname']
  ?? ($brand ?? '')
);
$productValue = (string) (
  $resolvedEditProduct['productname']
  ?? ($category ?? '')
);
$modelValue = (string) (
  $resolvedEditProduct['modelname']
  ?? ($modelno ?? ($model_name ?? ''))
);
$versionValue = (string) ($version ?? 'N/A');
$batchValue = (string) ($batch ?? 'N/A');

$editParameterName = (string) (
  $resolvedEditProduct['parameter_name']
  ?? $resolvedEditProduct['parameterName']
  ?? ''
);

$editCommands = isset($editPayload['commands']) && is_array($editPayload['commands'])
  ? $editPayload['commands']
  : array();
$editFrameCount = (int) (
  $resolvedEditProduct['command_frame_count']
  ?? $resolvedEditProduct['commandFrameCount']
  ?? count($editCommands)
);
if ($configurationId && $editFrameCount < 1 && count($editCommands) > 0) {
  $editFrameCount = count($editCommands);
}
$editFrameCount = max(0, min(20, $editFrameCount));
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token-name" content="<?= esc(csrf_token(), 'attr') ?>" />
  <meta name="csrf-token-hash" content="<?= esc(csrf_hash(), 'attr') ?>" />
  <title><?= $isEditMode ? 'Edit' : 'Add' ?> API · Product · <?= esc($brandValue . ' ' . $productValue) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family:
        system-ui,
        -apple-system,
        "Segoe UI",
        Roboto,
        "Helvetica Neue",
        sans-serif;
    }

    body {
      background: #f3f6fc;
      display: flex;
      justify-content: center;
      padding: 2rem 1rem;
    }

    .card {
      max-width: 1200px;
      width: 100%;
      background: white;
      border-radius: 28px;
      box-shadow: 0 20px 40px -12px rgba(0, 20, 50, 0.25);
      padding: 2rem 2rem 2.5rem;
    }

    .header-title {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.8rem;
      flex-wrap: wrap;
    }

    .header-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 0.75rem;
      margin-left: auto;
    }

    .header-back-btn {
      flex: 0 0 auto;
      white-space: nowrap;
    }

    .header-title h1 {
      font-size: 1.8rem;
      font-weight: 600;
      color: #0b1a33;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .header-title h1 i {
      color: #2a6df4;
    }

    .brand-badge {
      background: #eef3fe;
      padding: 0.4rem 1.2rem;
      border-radius: 40px;
      font-weight: 600;
      color: #1a4cba;
      border: 1px solid #d6e3fc;
    }

    .product-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 2rem 3rem;
      background: #f9faff;
      padding: 1rem 1.5rem;
      border-radius: 20px;
      margin-bottom: 2rem;
      border: 1px solid #e9edf4;
    }

    .meta-item {
      display: flex;
      align-items: baseline;
      gap: 6px;
    }

    .meta-item .label {
      font-weight: 500;
      color: #4e5b71;
      font-size: 0.85rem;
      text-transform: uppercase;
    }

    .meta-item .value {
      font-weight: 600;
      color: #0b1a33;
      font-size: 1rem;
    }

    .meta-item .value.light {
      font-weight: 400;
      color: #5d6f88;
    }

    .section {
      margin-top: 2rem;
    }

    .section-title {
      font-weight: 600;
      font-size: 1.1rem;
      color: #14273e;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .section-title i {
      color: #2a6df4;
    }

    .section-desc {
      color: #4f6587;
      font-size: 0.9rem;
      margin-bottom: 1.2rem;
      border-left: 3px solid #d0def0;
      padding-left: 1rem;
    }

    .param-grid {
      display: flex;
      flex-direction: column;
      gap: 0.8rem 2rem;
      background: #fafcff;
      padding: 1rem 1.5rem;
      border-radius: 18px;
      border: 1px solid #e6edf6;
      margin-bottom: 1.8rem;
    }

    .param-row {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 1rem 2rem;
    }

    .param-item {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .param-item label {
      font-weight: 500;
      color: #25415d;
      font-size: 0.9rem;
      white-space: nowrap;
    }

    .param-item input,
    .param-item select {
      border: 1px solid #d6e0ed;
      background: white;
      padding: 0.35rem 0.7rem;
      border-radius: 40px;
      font-size: 0.9rem;
      font-weight: 500;
      color: #0b1a33;
      width: auto;
      min-width: 70px;
      outline: none;
    }

    .param-item select {
      cursor: pointer;
      min-width: 165px;
    }

    .param-item input:focus,
    .param-item select:focus {
      border-color: #2a6df4;
      box-shadow: 0 0 0 3px rgba(42, 109, 244, 0.15);
    }

    .generate-btn {
      border: 1px solid #2a6df4;
      background: #eaf1fe;
      padding: 0.35rem 1.2rem;
      border-radius: 40px;
      font-weight: 500;
      color: #1a4cba;
      cursor: pointer;
      font-size: 0.85rem;
    }

    .generate-btn:hover {
      background: #d4e3fc;
    }

    .param-item select.multi-trigger {
      min-width: 100px;
    }

    .byte-selector {
      background: #ffffff;
      border: 1px solid #e2eaf3;
      border-radius: 24px;
      padding: 1.2rem 1.2rem 0.8rem;
      margin: 1.5rem 0 1rem;
    }

    .byte-row {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.5rem 1.2rem;
    }

    .byte-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      background: #f6f9ff;
      border-radius: 16px;
      padding: 0.3rem 0.5rem 0.4rem;
      min-width: 52px;
      border: 1px solid #dce6f2;
      cursor: pointer;
      user-select: none;
    }

    .byte-item.active {
      border-color: #2a6df4;
      background: #eaf1fe;
      box-shadow: 0 0 0 2px rgba(42, 109, 244, 0.2);
    }

    .byte-item .byte-label {
      font-size: 0.6rem;
      font-weight: 600;
      color: #667e9e;
    }

    .byte-item .byte-value {
      font-weight: 700;
      font-size: 0.9rem;
      color: #0b1a33;
      background: white;
      padding: 0.1rem 0.3rem;
      border-radius: 8px;
      margin-top: 2px;
    }

    .add-frame-btn {
      background: transparent;
      border: 1px dashed #b7ccf0;
      color: #1a4cba;
      font-weight: 500;
      padding: 0.3rem 1.2rem;
      border-radius: 40px;
      font-size: 0.8rem;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      margin: 0.3rem 0;
    }

    .add-frame-btn i {
      font-size: 0.7rem;
    }

    .action-row {
      display: flex;
      flex-wrap: wrap;
      gap: 1.5rem 3rem;
      align-items: center;
      border-top: 1px solid #e4edf7;
      padding-top: 1.8rem;
      margin-top: 1.8rem;
    }

    .save-btn {
      background: #0b1a33;
      color: white;
      border: none;
      padding: 0.5rem 2.2rem;
      border-radius: 60px;
      font-weight: 600;
      font-size: 0.9rem;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      margin-left: auto;
    }

    .command-frame-card {
      background: #f9fcff;
      border: 1px solid #dde7f2;
      border-radius: 20px;
      padding: 0.8rem 1rem;
      cursor: pointer;
    }

    .command-frame-card:hover {
      background: #f0f6ff;
      border-color: #b7ccf0;
    }

    .command-frame-card.selected {
      border-color: #2a6df4;
      background: #eaf1fe;
      box-shadow: 0 0 0 2px rgba(42, 109, 244, 0.2);
    }

    .command-frame-card .frame-title {
      font-weight: 600;
      color: #0b1a33;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .frame-direction {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      margin-top: 0.55rem;
      padding: 0.2rem 0.6rem;
      border-radius: 30px;
      background: #eef3fe;
      color: #1a4cba;
      font-size: 0.72rem;
      font-weight: 600;
    }

    .command-response-error-flow {
      justify-content:center;
      display: flex;
      align-items: center;
      gap: 0.85rem;
      margin: 1rem 0 1.25rem;
      overflow-x: auto;
      padding: 0.25rem 0 0.45rem;
    }

    .flow-stage {
      min-width: 144px;
      min-height: 60px;
      display: inline-flex;
      align-items: center;
      justify-content: flex-start;
      gap: 0.7rem;
      padding: 0.75rem 1rem;
      border: 1px solid #ddd3ca;
      border-radius: 18px;
      background: #f1ede9;
      color: #111827;
      font-family: inherit;
      font-size: 0.86rem;
      font-weight: 700;
      line-height: 1;
      text-align: left;
      cursor: pointer;
      appearance: none;
      -webkit-appearance: none;
      transition:
        background-color 0.2s ease,
        border-color 0.2s ease,
        color 0.2s ease,
        transform 0.2s ease,
        box-shadow 0.2s ease;
    }

    .flow-stage-copy {
      display: flex;
      min-width: 0;
      flex-direction: column;
      gap: 0.28rem;
    }

    .flow-stage-label {
      font-size: 0.86rem;
      font-weight: 800;
    }

    .flow-stage-status {
      display: inline-flex;
      align-items: center;
      gap: 0.28rem;
      color: #6b7280;
      font-size: 0.64rem;
      font-weight: 750;
      line-height: 1;
    }

    .flow-stage:hover {
      border-color: #c47b45;
      transform: translateY(-1px);
      box-shadow: 0 5px 14px rgba(37, 44, 76, 0.1);
    }

    .flow-stage:focus-visible {
      outline: 3px solid rgba(196, 123, 69, 0.28);
      outline-offset: 3px;
    }

    .flow-stage.active {
      background: #252c4c;
      border-color: #252c4c;
      color: #fff;
      box-shadow: none;
    }

    .flow-stage.active .flow-stage-status {
      color: rgba(255, 255, 255, 0.76);
    }

    .flow-stage-number {
      width: 35px;
      height: 35px;
      flex: 0 0 35px;
      display: inline-grid;
      place-items: center;
      border-radius: 50%;
      background: #f5f2ef;
      color: #111827;
    }

    /* A generated frame is still "not filled" until the user enters its
       values. Amber means work has started; green means every frame in that
       type has its API, required selections and saved partitions. */
    .flow-stage.in-progress:not(.active) {
      border-color: #e5aa48;
      background: #fff8e8;
      color: #71450b;
    }

    .flow-stage.in-progress:not(.active) .flow-stage-number {
      background: #f6b94f;
      color: #442800;
    }

    .flow-stage.in-progress:not(.active) .flow-stage-status {
      color: #9a5d08;
    }

    .flow-stage.completed:not(.active) {
      border-color: #5ca979;
      background: #eaf8ef;
      color: #185c32;
    }

    .flow-stage.completed:not(.active) .flow-stage-number {
      background: #2f9b57;
      color: #fff;
    }

    .flow-stage.completed:not(.active) .flow-stage-status {
      color: #237441;
    }

    .flow-stage.active.in-progress {
      box-shadow: inset 0 -4px 0 #f2b84b;
    }

    .flow-stage.active.completed {
      background: #176b3a;
      border-color: #176b3a;
    }

    .flow-stage.active.completed .flow-stage-number {
      background: #fff;
      color: #176b3a;
    }

    
    .flow-arrow {
      color: #c47b45;
      font-size: 1.15rem;
      flex: 0 0 auto;
    }

    .frame-settings {
      position: relative;
      z-index: 2;
      display: grid;
      grid-template-columns: 1fr;
      gap: 0.65rem;
      margin-top: 0.75rem;
      padding: 0.75rem;
      border: 1px solid #dbe5f0;
      border-radius: 14px;
      background: rgba(255, 255, 255, 0.94);
      cursor: default;
    }

    .frame-setting {
      display: grid;
      gap: 0.3rem;
    }

    .frame-setting label {
      color: #25415d;
      font-size: 0.72rem;
      font-weight: 700;
    }

    .frame-setting select {
      width: 100%;
      min-width: 0;
      border: 1px solid #cfdbea;
      border-radius: 9px;
      background: #fff;
      color: #172b45;
      padding: 0.48rem 0.55rem;
      font-size: 0.76rem;
      outline: none;
      cursor: pointer;
    }

    .frame-setting select:focus {
      border-color: #2a6df4;
      box-shadow: 0 0 0 3px rgba(42, 109, 244, 0.13);
    }

    .frame-setting select.frame-field-error {
      border-color: #dc2626;
      background: #fff7f7;
    }

    .remove-btn {
      background: transparent;
      border: none;
      color: #b91c1c;
      cursor: pointer;
      font-size: 0.8rem;
    }

    .command-frame-help {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 10px;
      flex-wrap: wrap;
    }

    .command-frame-hint {
      color: #4a6180;
      font-size: 0.78rem;
    }

    .command-example {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 0.38rem 0.55rem;
      border: 1px solid #c9d8eb;
      border-radius: 11px;
      background: #fff;
      color: #536b88;
      font-size: 0.68rem;
    }

    .command-example strong {
      color: #214467;
    }

    .example-byte {
      display: inline-grid;
      place-items: center;
      min-width: 31px;
      height: 27px;
      padding: 0 5px;
      border: 1px solid #d18d00;
      border-radius: 6px;
      background: #ffb000;
      color: #111827;
      font:
        750 0.74rem Consolas,
        monospace;
    }

    .example-result {
      color: #1f63cf;
      font:
        700 0.7rem Consolas,
        monospace;
    }

    .byte-detail-popup {
      display: none;
      background: #f9fcff;
      border: 1px solid #d0def0;
      border-radius: 20px;
      padding: 1rem 1.5rem;
      margin-top: 1.5rem;
    }

    .byte-detail-popup .popup-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #0b1a33;
    }

    .byte-detail-popup .close {
      background: transparent;
      border: none;
      font-size: 1.2rem;
      cursor: pointer;
    }

    .byte-detail-popup .popup-grid {
      display: flex;
      flex-flow: row nowrap;
      align-items: center;
      width: 100%;
      overflow-x: auto;
      overflow-y: hidden;
      white-space: nowrap;
      padding-bottom: 0.35rem;
    }

    .byte-detail-popup .popup-item {
      display: flex;
      justify-content: space-between;
      padding: 0.15rem 0.6rem;
      background: #f6faff;
      border-radius: 20px;
      font-size: 0.8rem;
    }

    .byte-detail-popup .popup-item .idx {
      font-weight: 600;
      color: #1a3d66;
    }

    .byte-detail-popup .popup-item .val {
      font-weight: 500;
      color: #2a5f8a;
    }

    .frame-code {
      margin-top: 0.45rem;
      color: #314b6b;
      font-size: 0.78rem;
      font-family: Consolas, Monaco, monospace;
    }

    .frame-code strong {
      color: #0b1a33;
      letter-spacing: 0.08em;
    }

    .command-entry {
      display: flex;
      flex-wrap: wrap;
      align-items: flex-end;
      gap: 0.65rem;
      margin: 0.8rem 0 1rem;
      padding: 0.8rem;
      background: #eef4ff;
      border-radius: 14px;
    }

    .command-entry label {
      width: 100%;
      font-size: 0.78rem;
      font-weight: 600;
      color: #25415d;
    }

    .command-entry input {
      flex: 1;
      min-width: 200px;
      border: 1px solid #b9cbe5;
      border-radius: 10px;
      padding: 0.55rem 0.7rem;
      font-family: Consolas, Monaco, monospace;
      font-size: 0.95rem;
      text-transform: uppercase;
      outline: none;
    }

    .command-entry input:focus {
      border-color: #2a6df4;
      box-shadow: 0 0 0 3px rgba(42, 109, 244, 0.15);
    }

    .command-entry button {
      border: 0;
      border-radius: 10px;
      padding: 0.58rem 1rem;
      background: #2a6df4;
      color: white;
      font-weight: 600;
      cursor: pointer;
    }

    .command-byte-editor {
      display: flex;
      flex-flow: row wrap;
      align-items: flex-end;
      gap: 10px 8px;
      width: 100%;
      overflow: visible;
      padding: 4px 2px 8px;
    }

    .command-byte-unit {
      position: relative;
      flex: 0 0 auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;
      padding-top: 2px;
    }

    .command-entry .command-byte-unit label {
      width: auto;
      color: #637a98;
      font-size: 0.62rem;
      font-weight: 700;
      text-transform: uppercase;
    }

    .command-entry input.command-byte-input {
      flex: 0 0 auto;
      min-width: 80px;
      width: 80px;
      max-width: 360px;
      height: 44px;
      padding: 0.5rem 0.65rem;
      border: 1px solid #9fb7d8;
      border-radius: 9px;
      background: #fff;
      color: #0b1a33;
      font:
        750 1rem Consolas,
        Monaco,
        monospace;
      text-align: left;
      text-transform: uppercase;
    }

    .command-entry input.command-byte-input.invalid {
      border-color: #d92d20;
      background: #fff4f2;
      box-shadow: 0 0 0 3px rgba(217, 45, 32, 0.1);
    }

    .command-entry button.remove-command-byte {
      position: absolute;
      top: -5px;
      right: -6px;
      display: grid;
      place-items: center;
      width: 18px;
      height: 18px;
      padding: 0;
      border-radius: 50%;
      background: #fff;
      color: #b42318;
      border: 1px solid #f2b8b5;
      font-size: 0.72rem;
      line-height: 1;
    }

    .command-entry button.remove-command-byte:hover {
      background: #fee4e2;
    }

    .command-entry button.add-command-byte {
      flex: 0 0 44px;
      width: 44px;
      height: 44px;
      margin-bottom: 0;
      padding: 0;
      border: 2px dashed #79a4e8;
      border-radius: 10px;
      background: #fff;
      color: #1f63cf;
      font-size: 1.45rem;
      line-height: 1;
    }

    .command-entry button.add-command-byte:hover {
      background: #eaf2ff;
      border-style: solid;
    }

    .command-entry .byte-editor-hint {
      width: 100%;
      color: #667e9e;
      font-size: 0.7rem;
    }

    .command-entry .command-byte-count {
      width: 100%;
      color: #214467;
      font-size: 0.72rem;
      font-weight: 700;
    }

    .command-entry input.command-byte-input.new-byte {
      animation: newBytePulse 0.65s ease;
    }

    @keyframes newBytePulse {
      0% {
        transform: scale(0.85);
        box-shadow: 0 0 0 5px rgba(42, 109, 244, 0.2);
      }

      100% {
        transform: scale(1);
        box-shadow: none;
      }
    }

    .command-entry .apply-command-row {
      display: flex;
      justify-content: flex-end;
      width: 100%;
    }

    .command-entry .apply-command-row button {
      display: inline-flex;
      align-items: center;
      gap: 7px;
    }

    .command-error {
      width: 100%;
      color: #b42318;
      font-size: 0.78rem;
      min-height: 1em;
    }

    .command-code-line {
      display: flex !important;
      flex-flow: row nowrap !important;
      align-items: center;
      gap: 10px;
      width: max-content;
      min-width: max-content;
      white-space: nowrap;
    }

    .command-code-label {
      display: inline-block;
      flex: 0 0 auto;
      white-space: nowrap;
      color: #0b1a33;
      font-size: 0.9rem;
      font-weight: 700;
    }

    .command-character-row {
      display: inline-flex !important;
      flex-flow: row nowrap !important;
      align-items: center;
      gap: 8px;
      width: max-content;
      min-width: max-content;
      white-space: nowrap;
    }

    .command-character {
      display: inline-flex;
      flex: 0 0 auto;
      justify-content: center;
      align-items: center;
      width: auto;
      min-width: 46px;
      height: 46px;
      padding: 0 10px;
      background: #ffb000;
      border: 1px solid #202020;
      border-radius: 5px;
      color: #050505;
      font-family: Consolas, Monaco, monospace;
      font-size: 1.25rem;
      font-weight: 700;
      line-height: 1;
      text-transform: uppercase;
      white-space: nowrap;
      cursor: pointer;
      transition: all 0.15s;
    }

    .command-character:hover {
      background: #ffcc66;
      transform: scale(1.05);
    }

    .command-character.partition-colored {
      background: var(--partition-soft);
      border: 2px solid var(--partition-color);
      color: var(--partition-text);
      box-shadow: inset 0 3px 0 var(--partition-color);
    }

    .command-character.partition-colored:hover {
      background: var(--partition-soft);
      filter: saturate(1.15);
    }

    .empty-command {
      color: #667e9e;
      font-size: 0.85rem;
      padding: 0.4rem 0;
    }

    /* Field Description Section */
    .field-desc-section {
      margin-top: 2rem;
      overflow: hidden;
      background: #fff;
      border-radius: 24px;
      border: 1px solid #dbe6f5;
      box-shadow: 0 10px 30px -24px rgba(20, 39, 62, 0.65);
    }

    .field-desc-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      flex-wrap: wrap;
      padding: 1.25rem 1.5rem;
      background: linear-gradient(135deg, #f5f9ff, #eef5ff);
      border-bottom: 1px solid #dbe6f5;
    }

    .field-desc-title {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 750;
      color: #0b1a33;
      font-size: 1.12rem;
    }

    .field-desc-title-icon {
      display: grid;
      place-items: center;
      width: 36px;
      height: 36px;
      border-radius: 11px;
      color: #fff;
      background: #2a6df4;
      box-shadow: 0 6px 14px rgba(42, 109, 244, 0.24);
    }

    .field-desc-dropdown {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .field-desc-dropdown label {
      font-weight: 650;
      color: #25415d;
      font-size: 0.86rem;
    }

    .field-desc-dropdown select {
      border: 1px solid #cbd9eb;
      background: white;
      padding: 0.58rem 2rem 0.58rem 0.85rem;
      border-radius: 11px;
      font-size: 0.88rem;
      font-weight: 600;
      color: #0b1a33;
      outline: none;
      min-width: 220px;
      cursor: pointer;
    }

    .field-desc-dropdown select:focus {
      border-color: #2a6df4;
      box-shadow: 0 0 0 3px rgba(42, 109, 244, 0.15);
    }

    .field-desc-help {
      display: flex;
      align-items: flex-start;
      gap: 9px;
      margin: 1rem 1.5rem 0;
      padding: 0.75rem 0.9rem;
      border-radius: 12px;
      background: #f8fbff;
      color: #4b6381;
      font-size: 0.8rem;
      line-height: 1.45;
    }

    .field-desc-help i {
      color: #2a6df4;
      margin-top: 2px;
    }

    #fieldDescContainer {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
      gap: 14px;
      min-height: 92px;
      padding: 1.1rem 1.5rem 1.5rem;
      align-items: start;
    }

    .field-empty {
      grid-column: 1/-1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 7px;
      min-height: 100px;
      color: #667e9e;
      text-align: center;
      border: 1px dashed #cbd9eb;
      border-radius: 15px;
      background: #fbfdff;
      font-size: 0.88rem;
    }

    .field-empty i {
      font-size: 1.35rem;
      color: #9ab0ca;
    }

    /* Byte Popup Modal */
    .byte-modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.4);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }

    .byte-modal {
      background: white;
      border-radius: 24px;
      padding: 2rem;
      max-width: 520px;
      width: 90%;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
    }

    .byte-modal h3 {
      font-size: 1.3rem;
      color: #0b1a33;
      margin-bottom: 0.3rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .byte-modal .byte-code-badge {
      background: #ffb000;
      padding: 0.2rem 1rem;
      border-radius: 30px;
      font-weight: 700;
      font-family: Consolas, monospace;
      font-size: 1rem;
      display: inline-block;
      margin-bottom: 1rem;
    }

    .byte-modal .close-modal {
      background: transparent;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: #666;
      float: right;
    }

    .byte-modal .form-group {
      margin-bottom: 0.8rem;
    }

    .byte-modal .form-group label {
      display: block;
      font-weight: 600;
      font-size: 0.8rem;
      color: #25415d;
      margin-bottom: 3px;
    }

    .byte-modal .form-group input,
    .byte-modal .form-group select {
      width: 100%;
      padding: 0.45rem 0.7rem;
      border: 1px solid #d6e0ed;
      border-radius: 10px;
      font-size: 0.9rem;
      outline: none;
    }

    .byte-modal .form-group input:focus,
    .byte-modal .form-group select:focus {
      border-color: #2a6df4;
      box-shadow: 0 0 0 3px rgba(42, 109, 244, 0.15);
    }

    .byte-modal .modal-actions {
      display: flex;
      gap: 12px;
      margin-top: 1.2rem;
      justify-content: flex-end;
    }

    .byte-modal .modal-actions button {
      padding: 0.45rem 1.8rem;
      border-radius: 40px;
      font-weight: 600;
      cursor: pointer;
      border: none;
      font-size: 0.9rem;
    }

    .byte-modal .modal-actions .btn-save-modal {
      background: #0b1a33;
      color: white;
    }

    .byte-modal .modal-actions .btn-cancel-modal {
      background: #eef3fe;
      color: #1a4cba;
      border: 1px solid #d6e3fc;
    }

    /* command character clickable */
    .command-character.clickable {
      cursor: pointer;
      position: relative;
    }

    .command-character.clickable::after {
      content: "";
      position: absolute;
      top: -10px;
      right: -10px;
      font-size: 12px;
      background: white;
      border-radius: 50%;
      padding: 0 2px;
    }

    .field-desc-card {
      background: #fff;
      border: 1px solid #dce6f2;
      border-radius: 17px;
      padding: 0.9rem;
      min-width: 0;
      transition:
        box-shadow 0.15s,
        border-color 0.15s,
        transform 0.15s;
    }

    .field-desc-card:hover {
      border-color: #aac4ee;
      box-shadow: 0 8px 20px -16px rgba(18, 58, 116, 0.8);
      transform: translateY(-1px);
    }

    .field-desc-card.partition-colored {
      border-color: var(--partition-color);
      box-shadow: inset 0 4px 0 var(--partition-color);
    }

    .field-desc-card.partition-colored:hover {
      border-color: var(--partition-color);
    }

    .field-desc-card.partition-colored .field-byte-badge {
      background: var(--partition-soft);
      border-color: var(--partition-color);
      color: var(--partition-text);
    }

    .field-card-top {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .field-byte-badge {
      flex: 0 0 auto;
      display: grid;
      place-items: center;
      min-width: 48px;
      height: 44px;
      padding: 0 8px;
      border-radius: 11px;
      color: #0b1a33;
      background: #ffb000;
      border: 1px solid #e29b00;
      font:
        750 1rem Consolas,
        monospace;
    }

    .field-byte-meta {
      min-width: 0;
      flex: 1;
    }

    .field-byte-index {
      display: block;
      margin-bottom: 4px;
      color: #667e9e;
      font-size: 0.68rem;
      font-weight: 750;
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }

    .field-card-body {
      margin-top: 0.75rem;
      padding-top: 0.75rem;
      border-top: 1px solid #edf1f6;
    }

    /* Bitwise specific styles */
    .binary-labels,
    .bitwise-bits {
      display: grid;
      grid-template-columns: repeat(8, minmax(22px, 1fr));
      gap: 4px;
      width: 100%;
      overflow-x: auto;
      padding-bottom: 2px;
    }

    .binary-labels {
      margin-bottom: 4px;
      color: #7b8da5;
      font:
        650 0.58rem Consolas,
        monospace;
      text-align: center;
    }

    .bit {
      min-width: 0;
      height: 34px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid #d4dfed;
      border-radius: 7px;
      font:
        750 0.78rem Consolas,
        monospace;
      cursor: pointer;
      transition: all 0.15s;
      background: white;
      color: #0b1a33;
    }

    .bit:hover {
      border-color: #2a6df4;
      box-shadow: 0 0 0 2px rgba(42, 109, 244, 0.12);
      transform: translateY(-1px);
    }

    .bit.bit-1 {
      background: #e8f0fe;
      border-color: #78a3e8;
      color: #174fa7;
    }

    .bit.bit-0 {
      background: #f7f9fc;
      color: #68798f;
    }

    .binary-value {
      margin-top: 7px;
      padding: 6px 8px;
      border-radius: 8px;
      background: #0b1a33;
      color: #eaf1fe;
      font:
        650 0.72rem Consolas,
        monospace;
      text-align: center;
      letter-spacing: 0.12em;
    }

    .bit-partition-help {
      display: flex;
      align-items: center;
      gap: 7px;
      margin: 2px 0 9px;
      color: #526b88;
      font-size: 0.72rem;
      font-weight: 650;
    }

    .bit-partition-toolbar {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
      margin: 9px 0;
    }

    .bit-partition-action {
      min-height: 34px;
      padding: 0.42rem 0.7rem;
      border: 1px solid #2a6df4;
      border-radius: 8px;
      background: #2a6df4;
      color: #fff;
      font-size: 0.72rem;
      font-weight: 750;
      cursor: pointer;
    }

    .bit-partition-action.secondary {
      background: #fff;
      color: #174e99;
      border-color: #b9cce7;
    }

    .bit-partition-action:disabled {
      opacity: 0.45;
      cursor: not-allowed;
    }

    .bit.selected {
      border-color: #dc2626 !important;
      background: #fff0f0 !important;
      color: #b91c1c !important;
      box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.14);
    }

    .bit.grouped {
      border-color: var(--bit-group-color);
      border-bottom-width: 4px;
    }

    .bit-group-list {
      display: flex;
      flex-wrap: wrap;
      gap: 7px;
      margin-top: 9px;
    }

    .bit-group-chip {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      min-height: 34px;
      padding: 0.4rem 0.62rem;
      border: 1px solid var(--bit-group-color);
      border-radius: 9px;
      background: var(--bit-group-soft);
      color: var(--bit-group-text);
      font-size: 0.7rem;
      font-weight: 750;
      cursor: pointer;
    }

    .bit-group-chip.missing {
      outline: 2px solid rgba(220, 38, 38, 0.22);
    }

    .bit-group-chip i {
      font-size: 0.68rem;
    }

    .bit-group-chip small {
      opacity: 0.78;
      font-weight: 650;
    }

    .bit-cut-scroll {
      overflow-x: auto;
      padding: 23px 8px 10px;
      margin-top: 0.6rem;
    }

    .bit-cut-timeline {
      position: relative;
      display: grid;
      grid-template-rows: auto auto;
      min-width: max-content;
      border: 1px solid #b9cae0;
      border-radius: 12px;
      background: #f8fbff;
      overflow: visible;
    }

    .bit-cut-cell {
      position: relative;
      grid-row: 1;
      min-width: 68px;
      padding: 0.55rem 0.32rem;
      text-align: center;
      border-right: 1px solid #dce6f2;
      background: var(--bit-group-soft);
      box-shadow: inset 0 4px 0 var(--bit-group-color);
    }

    .bit-cut-cell:last-of-type {
      border-right: 0;
    }

    .bit-cut-index {
      display: block;
      margin-bottom: 3px;
      color: #667e9e;
      font:
        750 0.58rem Consolas,
        monospace;
    }

    .bit-cut-value {
      display: block;
      color: var(--bit-group-text);
      font:
        850 0.92rem Consolas,
        monospace;
    }

    .bit-range-block {
      grid-row: 2;
      min-width: 0;
      padding: 0.5rem 0.35rem;
      border-top: 2px solid var(--bit-group-color);
      border-right: 1px solid rgba(255, 255, 255, 0.9);
      background: var(--bit-group-soft);
      color: var(--bit-group-text);
      font-size: 0.68rem;
      font-weight: 800;
      cursor: pointer;
      text-align: center;
    }

    .bit-range-block:hover,
    .bit-range-block:focus-visible {
      filter: brightness(0.97);
      box-shadow: inset 0 0 0 2px var(--bit-group-color);
      outline: none;
    }

    .bit-range-block small {
      display: block;
      margin-top: 3px;
      font-size: 0.58rem;
      font-weight: 700;
      opacity: 0.78;
    }

    .bit-configure-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
      width: calc(100% - 4px);
      min-height: 28px;
      margin-top: 6px;
      padding: 0.28rem 0.38rem;
      border: 1px solid var(--bit-group-color);
      border-radius: 7px;
      background: #fff;
      color: var(--bit-group-text);
      font-size: 0.58rem;
      font-weight: 800;
      line-height: 1.15;
      cursor: pointer;
    }

    /* .bit-range-block.missing .bit-configure-btn {
        border-color: #d92d20;
        color: #b42318;
      } */
    .bit-configure-btn:hover,
    .bit-configure-btn:focus-visible {
      background: var(--bit-group-color);
      color: #fff;
      outline: none;
    }

    .bit-range-block.single-bit-range {
      padding: 0.45rem 0.25rem;
    }

    .bit-range-block.single-bit-range>span {
      display: block;
      overflow: hidden;
      font-size: 0.62rem;
      line-height: 1.15;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .bit-range-block.single-bit-range>small {
      min-height: 24px;
      font-size: 0.52rem;
      line-height: 1.15;
    }

    .bit-range-block.single-bit-range .bit-configure-btn {
      width: calc(100% - 2px);
      min-height: 30px;
      margin-top: 5px;
      padding: 0.25rem 0.18rem;
      gap: 3px;
      font-size: 0.54rem;
      white-space: nowrap;
    }

    .bit-cut-target {
      position: absolute;
      z-index: 6;
      top: -21px;
      width: 26px;
      height: 26px;
      padding: 0;
      transform: translateX(-50%);
      border: 0;
      background: transparent;
      cursor: pointer;
    }

    .bit-cut-target::before {
      content: "";
      display: none;
      position: absolute;
      top: 18px;
      bottom: 1px;
      left: 50%;
      width: 2px;
      height: 88px;
      transform: translateX(-50%);
      border-left: 3px solid var(--bit-cut-color, #2a6df4);
      pointer-events: none;
    }

    .bit-cut-target::after {
      content: "+";
      position: absolute;
      top: 0;
      left: 50%;
      display: grid;
      place-items: center;
      width: 20px;
      height: 20px;
      transform: translateX(-50%);
      border: 1px solid #b8c9df;
      border-radius: 50%;
      background: #fff;
      color: #47729f;
      font-size: 0.75rem;
      font-weight: 850;
    }

    .bit-cut-target:hover::after {
      border-color: #2a6df4;
      color: #2a6df4;
      background: #eef5ff;
    }

    .bit-cut-target.active::before {
      display: block;
    }

    .bit-cut-target.active::after {
      content: "";
      top: -2px;
      width: 24px;
      height: 24px;
      border: 2px solid var(--bit-cut-color, #2a6df4);
      background: var(--bit-cut-color, #2a6df4);
      color: #fff;
      font-size: 0.68rem;
      box-shadow: 0 3px 9px rgba(42, 109, 244, 0.28);
    }

    .bit-cut-target,
    .bit-cut-target::after {
      transition: none !important;
    }

    .bit-cut-actions {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 0.65rem;
    }

    /* Byte level field description dropdown */
    .byte-field-select {
      width: 100%;
      border: 1px solid #d6e0ed;
      background: white;
      padding: 0.48rem 0.65rem;
      border-radius: 9px;
      font-size: 0.78rem;
      font-weight: 600;
      color: #0b1a33;
      outline: none;
      cursor: pointer;
    }

    .byte-field-select:focus {
      border-color: #2a6df4;
      box-shadow: 0 0 0 3px rgba(42, 109, 244, 0.15);
    }

    .byte-field-wrapper {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .view-toggle-buttons {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 5px;
      margin-bottom: 8px;
      padding: 4px;
      border-radius: 10px;
      background: #eef3f9;
    }

    .view-toggle-buttons.single-view {
      grid-template-columns: 1fr;
    }

    .view-toggle-btn {
      padding: 0.42rem 0.55rem;
      border-radius: 9px;
      border-color: rgb(39, 88, 80);
      background: transparent;
      font-size: 0.72rem;
      font-weight: 700;
      color: #526983;
      cursor: pointer;
      transition: all 0.15s;
    }

    .view-toggle-btn.active {
      background: #2a6df4;
      color: white;
      border-color: #2a6df4;
    }

    .view-toggle-btn:hover:not(.active) {
      background: #fff;
    }

    .byte-wise-action {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding: 0.65rem 0.75rem;
      border: 1px solid #d6e3f2;
      border-radius: 10px;
      background: #f8fbff;
      color: #214467;
      cursor: pointer;
      text-align: left;
    }

    .byte-wise-action:hover {
      border-color: #2a6df4;
      background: #eef5ff;
    }

    .byte-wise-action strong {
      display: block;
      font-size: 0.78rem;
    }

    .byte-wise-action small {
      display: block;
      margin-top: 2px;
      color: #71839a;
      font-size: 0.66rem;
    }

    /* Step 1: Full API partition editor */
    .step-heading {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .step-number {
      display: inline-grid;
      place-items: center;
      min-width: 30px;
      height: 30px;
      padding: 0 8px;
      border-radius: 9px;
      background: #2a6df4;
      color: #fff;
      font-size: 0.78rem;
      font-weight: 800;
    }

    .api-partition-section {
      margin-top: 2rem;
      padding: 1.35rem;
      border: 1px solid #cddcf0;
      border-radius: 24px;
      background: linear-gradient(145deg, #fbfdff, #f3f8ff);
      box-shadow: 0 12px 28px -24px rgba(22, 65, 130, 0.75);
    }

    .api-entry-row {
      display: grid;
      grid-template-columns: minmax(240px, 1fr) auto;
      gap: 12px;
      align-items: end;
      margin-top: 1rem;
    }

    .api-input-wrap label {
      display: block;
      margin-bottom: 6px;
      color: #25415d;
      font-size: 0.83rem;
      font-weight: 700;
    }

    .api-input-wrap input {
      width: 100%;
      min-height: 48px;
      padding: 0.7rem 0.9rem;
      border: 1px solid #aebfd8;
      border-radius: 12px;
      background: #fff;
      color: #0b1a33;
      font:
        750 1rem Consolas,
        Monaco,
        monospace;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      outline: none;
    }

    .api-input-wrap input:focus {
      border-color: #2a6df4;
      box-shadow: 0 0 0 3px rgba(42, 109, 244, 0.14);
    }

    .api-count {
      min-height: 48px;
      display: flex;
      align-items: center;
      gap: 7px;
      padding: 0 1rem;
      border: 1px solid #d5e0ee;
      border-radius: 12px;
      background: #fff;
      color: #45617f;
      font-size: 0.8rem;
      font-weight: 700;
      white-space: nowrap;
    }

    .api-error {
      min-height: 20px;
      margin-top: 6px;
      color: #b42318;
      font-size: 0.76rem;
    }

    .partition-workspace {
      margin-top: 0.9rem;
      padding: 1rem;
      border: 1px solid #d9e4f2;
      border-radius: 17px;
      background: #fff;
    }

    .partition-instruction {
      display: flex;
      align-items: flex-start;
      gap: 8px;
      color: #526b89;
      font-size: 0.79rem;
      line-height: 1.45;
    }

    .partition-instruction i {
      margin-top: 2px;
      color: #2a6df4;
    }

    .api-timeline-scroll {
      overflow-x: auto;
      padding: 23px 10px 12px;
      margin-top: 0.7rem;
      scroll-behavior: smooth;
      scrollbar-gutter: stable;
    }

    .partition-timeline-shell {
      display: grid;
      grid-template-columns: 42px minmax(0, 1fr) 42px;
      align-items: stretch;
      gap: 8px;
      margin-top: 0.7rem;
    }

    .partition-timeline-shell .api-timeline-scroll {
      min-width: 0;
      margin-top: 0;
    }

    .partition-side-nav {
      align-self: stretch;
      display: grid;
      place-items: center;
      min-height: 112px;
      margin-top: 23px;
      margin-bottom: 12px;
      padding: 0;
      border: 1px solid #b9cce7;
      border-radius: 13px;
      background: linear-gradient(180deg, #ffffff, #edf4ff);
      color: #174e99;
      box-shadow: 0 8px 18px -14px rgba(23, 78, 153, 0.8);
      cursor: pointer;
      transition:
        transform 160ms ease,
        border-color 160ms ease,
        background 160ms ease,
        color 160ms ease,
        box-shadow 160ms ease;
    }

    .partition-side-nav i {
      font-size: 1rem;
      transition: transform 160ms ease;
    }

    .partition-side-nav:not(:disabled):hover {
      transform: translateY(-1px);
      border-color: #2a6df4;
      background: #2a6df4;
      color: #fff;
      box-shadow: 0 10px 20px -12px rgba(42, 109, 244, 0.85);
    }

    .partition-side-nav.left:not(:disabled):hover i {
      transform: translateX(-3px);
    }

    .partition-side-nav.right:not(:disabled):hover i {
      transform: translateX(3px);
    }

    .partition-side-nav:focus-visible {
      outline: 3px solid rgba(42, 109, 244, 0.22);
      outline-offset: 2px;
    }

    .partition-side-nav:disabled {
      border-color: #dce5f0;
      background: #f4f7fb;
      color: #a7b4c5;
      box-shadow: none;
      cursor: not-allowed;
      opacity: 0.72;
    }

    .api-timeline {
      position: relative;
      display: grid;
      grid-template-rows: auto auto;
      grid-auto-columns: minmax(54px, 1fr);
      min-width: max-content;
      border: 1px solid #b9cae0;
      border-radius: 12px;
      background: #f8fbff;
      overflow: visible;
    }

    .api-byte-cell {
      position: relative;
      min-width: 54px;
      padding: 0.7rem 0.45rem 0.58rem;
      text-align: center;
      border-right: 1px solid #dce6f2;
      grid-row: 1;
    }

    .api-byte-cell:last-child {
      border-right: 0;
    }

    .api-byte-cell.partition-colored {
      background: var(--partition-soft);
      box-shadow: inset 0 4px 0 var(--partition-color);
    }

    .api-byte-cell.partition-colored .api-byte-value {
      color: var(--partition-text);
    }

    .api-byte-index {
      display: block;
      margin-bottom: 3px;
      color: #7a8da7;
      font-size: 0.58rem;
      font-weight: 750;
      text-transform: uppercase;
    }

    .api-byte-value {
      color: #0b1a33;
      font:
        800 1rem Consolas,
        monospace;
    }

    .partition-name-band {
      min-width: 0;
      padding: 0.55rem 0.45rem 0.65rem;
      border-top: 1px solid var(--partition-color);
      border-right: 1px solid rgba(255, 255, 255, 0.85);
      background: var(--partition-soft);
      grid-row: 2;
    }

    .partition-name-band:first-of-type {
      border-bottom-left-radius: 11px;
    }

    .partition-name-band:last-of-type {
      border-right: 0;
      border-bottom-right-radius: 11px;
    }

    .partition-name-band label {
      display: flex;
      align-items: center;
      gap: 5px;
      margin-bottom: 4px;
      color: var(--partition-text);
      font-size: 0.6rem;
      font-weight: 800;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .partition-name-dot {
      flex: 0 0 auto;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--partition-color);
    }

    .partition-name-band select {
      width: 100%;
      min-width: 116px;
      padding: 0.42rem 1.55rem 0.42rem 0.5rem;
      border: 1px solid var(--partition-color);
      border-radius: 8px;
      background: #fff;
      color: var(--partition-text);
      font-size: 0.7rem;
      font-weight: 750;
      outline: none;
      cursor: pointer;
    }

    .partition-name-band select:focus {
      box-shadow: 0 0 0 3px var(--partition-soft);
    }

    .partition-config-controls {
      margin-top: 0.5rem;
      padding-top: 0.5rem;
      border-top: 1px solid color-mix(in srgb, var(--partition-color) 35%, transparent);
    }

    .partition-config-controls .view-toggle-buttons {
      margin-bottom: 6px;
      background: rgba(255, 255, 255, 0.58);
    }

    .partition-name-band .partition-mode-quick {
      grid-template-columns: repeat(2, minmax(0, 1fr));
      margin: 0.5rem 0;
      padding: 3px;
      border: 1px solid color-mix(in srgb, var(--partition-color) 35%, transparent);
      background: rgba(255, 255, 255, 0.72);
    }

    .partition-name-band .partition-mode-quick .view-toggle-btn {
      min-width: 0;
      padding: 0.4rem 0.32rem;
      font-size: 0.66rem;
      white-space: nowrap;
    }

    .partition-name-band .partition-mode-quick .view-toggle-btn.active {
      color: #fff !important;
      background: var(--partition-color) !important;
    }

    .partition-config-controls .view-toggle-btn.active {
      background: var(--partition-color);
    }

    .partition-config-controls .byte-wise-action {
      min-width: 170px;
      padding: 0.5rem 0.58rem;
      border-color: var(--partition-color);
      background: #fff;
      color: var(--partition-text);
    }

    .partition-config-controls .byte-wise-action small {
      color: var(--partition-text);
      opacity: 0.72;
    }

    .partition-config-controls .binary-value {
      background: var(--partition-text);
    }

    .partition-config-controls .bit.bit-1 {
      background: var(--partition-soft);
      border-color: var(--partition-color);
      color: var(--partition-text);
    }

    .partition-config-summary {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 7px;
      width: 100%;
      margin-top: 0.48rem;
      padding: 0.42rem 0.5rem;
      border: 1px solid var(--partition-color);
      border-radius: 8px;
      background: #fff;
      color: var(--partition-text);
      font-size: 0.66rem;
      font-weight: 800;
      cursor: pointer;
    }

    .partition-config-summary:hover {
      background: var(--partition-soft);
    }

    .partition-config-summary.config-missing {
      color: #b42318 !important;
      background: #fff1f0 !important;
      border: 2px solid #d92d20 !important;
      box-shadow: 0 0 0 3px rgba(217, 45, 32, 0.1);
    }

    .partition-config-summary.config-missing:hover,
    .partition-config-summary.config-missing:focus-visible {
      color: #8f1d16 !important;
      background: #ffe8e5 !important;
      border-color: #b42318 !important;
      box-shadow: 0 0 0 4px rgba(217, 45, 32, 0.16);
    }

    .partition-mode-overlay {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 900;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      background: rgba(7, 18, 38, 0.48);
      backdrop-filter: blur(2px);
    }

    .partition-mode-modal {
      width: min(900px, calc(100vw - 32px));
      max-height: 88vh;
      overflow-y: auto;
      border: 2px solid var(--partition-color, #2a6df4);
      border-radius: 20px;
      background: var(--partition-soft, #eef5ff);
      box-shadow: 0 24px 70px rgba(5, 20, 48, 0.32);
    }

    @media (max-width: 650px) {
      .partition-mode-modal {
        width: calc(100vw - 20px);
      }
    }

    .partition-mode-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 1rem 1.1rem 0.8rem;
      border-bottom: 1px solid var(--partition-color, #2a6df4);
    }

    .partition-mode-heading {
      min-width: 0;
    }

    .partition-mode-heading strong {
      display: block;
      color: var(--partition-text, #1746a2);
      font-size: 1rem;
    }

    .partition-mode-heading code {
      display: block;
      margin-top: 3px;
      overflow: hidden;
      text-overflow: ellipsis;
      color: #425d7b;
      font-size: 0.75rem;
      white-space: nowrap;
    }

    .partition-mode-close {
      display: grid;
      place-items: center;
      flex: 0 0 auto;
      width: 32px;
      height: 32px;
      padding: 0;
      border: 1px solid var(--partition-color, #2a6df4);
      border-radius: 50%;
      background: #fff;
      color: var(--partition-text, #1746a2);
      font-size: 1.1rem;
      cursor: pointer;
    }

    .partition-mode-body {
      padding: 1rem;
      background: #fff;
    }

    .partition-mode-body .partition-config-controls {
      margin: 0;
      padding: 0;
      border: 0;
    }

    .partition-mode-actions {
      display: flex;
      justify-content: flex-end;
      padding: 0.8rem 1rem 1rem;
    }

    .partition-mode-done {
      padding: 0.55rem 1.2rem;
      border: 0;
      border-radius: 9px;
      background: var(--partition-color, #2a6df4);
      color: #fff;
      font-weight: 800;
      cursor: pointer;
    }

    .partition-config-warning {
      display: none;
      align-items: flex-start;
      gap: 9px;
      margin: 0 0 0.85rem;
      padding: 0.72rem 0.8rem;
      border: 1px solid #d92d20;
      border-radius: 10px;
      background: #fff1f0;
      color: #b42318;
      font-size: 0.78rem;
      font-weight: 750;
      line-height: 1.4;
    }

    .partition-config-warning.show {
      display: flex;
      animation: warningShake 0.3s ease;
    }

    .partition-config-warning i {
      margin-top: 2px;
    }

    .partition-mode-modal.config-invalid {
      border-color: #d92d20 !important;
      box-shadow:
        0 0 0 4px rgba(217, 45, 32, 0.14),
        0 24px 70px rgba(5, 20, 48, 0.32);
    }

    .partition-mode-modal.config-invalid .partition-mode-done {
      background: #d92d20;
    }

    /* .byte-wise-action.config-missing {
        border: 2px solid #d92d20 !important;
        background: #fff1f0 !important;
        color: #b42318 !important;
        box-shadow: 0 0 0 3px rgba(217, 45, 32, 0.1);
      } */
    .byte-wise-action.config-missing small {
      color: #b42318 !important;
      opacity: 1;
    }

    .bit.config-missing {
      border: 2px solid #d92d20 !important;
      background: #fff1f0 !important;
      color: #b42318 !important;
      box-shadow: 0 0 0 2px rgba(217, 45, 32, 0.1);
    }

    .inline-config-warning {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-top: 0.5rem;
      color: #b42318;
      font-size: 0.7rem;
      font-weight: 800;
    }

    .byte-modal .field-form-warning {
      display: none;
      align-items: flex-start;
      gap: 8px;
      margin: 0.2rem 0 0.85rem;
      padding: 0.68rem 0.75rem;
      border: 1px solid #d92d20;
      border-radius: 10px;
      background: #fff1f0;
      color: #b42318;
      font-size: 0.76rem;
      font-weight: 750;
    }

    .byte-modal .field-form-warning.show {
      display: flex;
      animation: warningShake 0.3s ease;
    }

    .byte-modal .form-group.field-invalid input,
    .byte-modal .form-group.field-invalid select {
      border: 2px solid #d92d20 !important;
      background: #fff8f7;
      box-shadow: 0 0 0 3px rgba(217, 45, 32, 0.1);
    }

    .byte-modal .form-group.field-invalid label {
      color: #b42318 !important;
    }

    @keyframes warningShake {

      0%,
      100% {
        transform: translateX(0);
      }

      30% {
        transform: translateX(-4px);
      }

      60% {
        transform: translateX(4px);
      }
    }

    .cut-target {
      position: absolute;
      z-index: 5;
      top: -22px;
      width: 26px;
      height: 26px;
      padding: 0;
      transform: translateX(-50%);
      border: 0;
      background: transparent;
      cursor: pointer;
    }

    .cut-target::before {
      content: "";
      position: absolute;
      top: 20px;
      bottom: 7px;
      left: 50%;
      width: 2px;
      height: 112px;
      transform: translateX(-50%);
      border-left: 2px dashed #c6d4e5;
      pointer-events: none;
    }

    .cut-target:not(.active)::before {
      display: none;
    }

    .cut-target::after {
      content: "+";
      position: absolute;
      top: 0;
      left: 50%;
      display: grid;
      place-items: center;
      width: 20px;
      height: 20px;
      transform: translateX(-50%);
      border: 1px solid #b8c9df;
      border-radius: 50%;
      background: #fff;
      color: #47729f;
      font-size: 0.75rem;
      font-weight: 800;
    }

    .cut-target:hover::before {
      border-color: #2a6df4;
    }

    .cut-target:hover::after {
      border-color: #2a6df4;
      color: #2a6df4;
      background: #eef5ff;
    }

    .cut-target.active {
      cursor: pointer;
    }

    .cut-target.active::before {
      top: 17px;
      bottom: 1px;
      border-left: 3px solid #2a6df4;
    }

    .cut-target.active::after {
      content: "";
      width: 24px;
      height: 24px;
      top: -3px;
      border: 2px solid #2a6df4;
      background: #2a6df4;
      color: #fff;
      box-shadow: 0 3px 9px rgba(42, 109, 244, 0.28);
      font-size: 0.7rem;
    }

    .cut-target.active.partition-colored::before {
      border-left-color: var(--partition-color);
    }

    .cut-target.active.partition-colored::after {
      border-color: var(--partition-color);
      background: var(--partition-color);
    }

    .cut-target.active.dragging::after {
      transform: translateX(-50%) scale(1.12);
    }

    .timeline-empty {
      display: grid;
      place-items: center;
      min-height: 84px;
      border: 1px dashed #c4d3e5;
      border-radius: 12px;
      color: #71839a;
      font-size: 0.82rem;
      text-align: center;
      padding: 1rem;
      background: #fbfdff;
    }

    .partition-toolbar {
      display: flex;
      align-items: center;
      gap: 9px;
      flex-wrap: wrap;
      margin-top: 0.85rem;
    }

    .partition-btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      min-height: 38px;
      padding: 0.5rem 0.85rem;
      border-radius: 10px;
      border: 1px solid #b9cce7;
      background: #fff;
      color: #174e99;
      font-weight: 700;
      cursor: pointer;
    }

    .partition-btn.primary {
      border-color: #2a6df4;
      background: #2a6df4;
      color: #fff;
    }

    .partition-btn:hover {
      box-shadow: 0 5px 12px -8px rgba(23, 78, 153, 0.8);
    }

    .partition-limit {
      color: #667e9e;
      font-size: 0.74rem;
    }

    .partition-confirm-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid #dfe8f3;
    }

    .partition-status {
      display: flex;
      align-items: center;
      gap: 7px;
      color: #617793;
      font-size: 0.78rem;
    }

    .partition-status.complete {
      color: #167647;
      font-weight: 700;
    }

    .partition-status.error {
      color: #b42318;
    }

    .continue-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 0.62rem 1rem;
      border: 0;
      border-radius: 11px;
      background: #0b1a33;
      color: #fff;
      font-weight: 750;
      cursor: pointer;
    }

    .continue-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .section-guidance {
      margin-left: 40px;
      color: #647a96;
      font-size: 0.77rem;
    }

    .linked-api-panel {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 14px;
      padding: 0.9rem 1rem;
      border: 1px solid #cbdcef;
      border-radius: 14px;
      background: #f3f8ff;
    }

    .linked-api-info {
      min-width: 0;
      flex: 1;
    }

    .linked-api-title {
      display: flex;
      align-items: center;
      gap: 7px;
      color: #1b4778;
      font-size: 0.78rem;
      font-weight: 800;
    }

    .linked-api-title i {
      color: #2a6df4;
    }

    .linked-api-code {
      margin-top: 5px;
      overflow-x: auto;
      color: #0b1a33;
      font:
        800 0.9rem Consolas,
        monospace;
      letter-spacing: 0.07em;
      white-space: nowrap;
    }

    .linked-partition-chips {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 8px;
    }

    .linked-partition-chip {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      max-width: 100%;
      padding: 0.3rem 0.52rem;
      border: 1px solid #c9d9ec;
      border-radius: 8px;
      background: #fff;
      color: #355777;
      font-size: 0.68rem;
    }

    .linked-partition-chip strong {
      color: #174e99;
    }

    .linked-partition-chip code {
      overflow: hidden;
      text-overflow: ellipsis;
      color: #0b1a33;
      font-weight: 750;
    }

    .linked-partition-chip.partition-colored {
      border-color: var(--partition-color);
      background: var(--partition-soft);
    }

    .linked-partition-chip.partition-colored strong,
    .linked-partition-chip.partition-colored code {
      color: var(--partition-text);
    }

    .linked-api-state {
      flex: 0 0 auto;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 0.35rem 0.55rem;
      border-radius: 20px;
      background: #e4f7ec;
      color: #167647;
      font-size: 0.7rem;
      font-weight: 800;
      white-space: nowrap;
    }

    .linked-api-state.pending {
      background: #fff3e0;
      color: #9a5b00;
    }

    #linkedApiPanel {
      display: none;
    }

    #apiPartitionSection {
      display: none;
    }

    #apiPartitionSection.frame-partition-active {
      display: block;
      animation: framePartitionReveal 0.3s ease;
    }

    @keyframes framePartitionReveal {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .selected-frame-banner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-top: 0.85rem;
      padding: 0.7rem 0.85rem;
      border: 1px solid #a9c8f8;
      border-radius: 12px;
      background: #eaf3ff;
      color: #174e99;
    }

    .selected-frame-banner strong {
      display: flex;
      align-items: center;
      gap: 7px;
      font-size: 0.8rem;
    }

    .selected-frame-banner span {
      padding: 0.25rem 0.55rem;
      border-radius: 20px;
      background: #2a6df4;
      color: #fff;
      font-size: 0.68rem;
      font-weight: 850;
    }

    .command-frame-card.needs-partition {
      border: 2px dashed #d4a017;
      background: #fffbf2;
      box-shadow: inset 0 4px 0 #f0b429;
    }

    .command-frame-card.partition-complete {
      border: 2px solid #1b8a55;
      background: #f3fbf6;
      box-shadow: inset 0 4px 0 #1b8a55;
    }

    .command-frame-card.frame-in-progress {
      border: 2px solid #2a6df4;
      background: #f5f9ff;
      box-shadow: inset 0 4px 0 #2a6df4;
    }

    .frame-status-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      margin-top: 0.45rem;
      padding: 0.22rem 0.55rem;
      border-radius: 999px;
      font-size: 0.66rem;
      font-weight: 850;
      letter-spacing: 0.02em;
      text-transform: uppercase;
    }

    .frame-status-badge.pending {
      background: #fff3e0;
      color: #9a5b00;
      border: 1px solid #f0c57a;
    }

    .frame-status-badge.in-progress {
      background: #eaf1fe;
      color: #174e99;
      border: 1px solid #9db8ef;
    }

    .frame-status-badge.filled {
      background: #e4f7ec;
      color: #167647;
      border: 1px solid #8fd0a8;
    }

    .frame-partition-state {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      margin-top: 0.5rem;
      padding: 0.2rem 0.5rem;
      border-radius: 20px;
      background: #fff3e0;
      color: #9a5b00;
      font-size: 0.66rem;
      font-weight: 800;
    }

    .frame-partition-state.complete {
      background: #e4f7ec;
      color: #167647;
    }

    .frame-partition-state.in-progress {
      background: #eaf1fe;
      color: #174e99;
    }

    .frames-header-left {
      display: inline-flex;
      align-items: center;
      gap: 0.7rem;
      flex-wrap: wrap;
    }

    .add-frame-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      min-height: 34px;
      padding: 0.4rem 0.85rem;
      border: 1px solid #2a6df4;
      border-radius: 999px;
      background: #2a6df4;
      color: #fff;
      font-size: 0.78rem;
      font-weight: 800;
      cursor: pointer;
      font-family: inherit;
    }

    .add-frame-btn:hover {
      background: #1a4cba;
      border-color: #1a4cba;
    }

    .add-frame-btn:disabled {
      opacity: 0.55;
      cursor: not-allowed;
    }

    .add-frame-card {
      display: grid;
      place-items: center;
      min-height: 148px;
      border: 2px dashed #9bb4d4;
      border-radius: 20px;
      background: #fff;
      color: #355070;
      cursor: pointer;
      transition:
        border-color 0.2s,
        background-color 0.2s,
        transform 0.2s,
        color 0.2s;
      font-family: inherit;
      width: 100%;
    }

    .add-frame-card:hover {
      border-color: #2a6df4;
      background: #eaf1fe;
      color: #174e99;
      transform: translateY(-3px);
    }

    .add-frame-card-inner {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      font-size: 0.86rem;
      font-weight: 800;
    }

    .add-frame-card-inner i {
      display: grid;
      place-items: center;
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: #eaf1fe;
      color: #2a6df4;
      font-size: 1.1rem;
    }

    .frame-status-legend {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin: 0 0 0.85rem;
    }

    .frame-status-legend span {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 0.25rem 0.55rem;
      border-radius: 999px;
      font-size: 0.68rem;
      font-weight: 750;
      background: #fff;
      border: 1px solid #d5e0ee;
      color: #45617f;
    }

    .frame-status-legend .dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
    }

    .frame-status-legend .dot.pending {
      background: #f0b429;
    }

    .frame-status-legend .dot.in-progress {
      background: #2a6df4;
    }

    .frame-status-legend .dot.filled {
      background: #1b8a55;
    }

    .demo-open-btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      min-height: 34px;
      margin-left: auto;
      padding: 0.42rem 0.75rem;
      border: 1px solid #2a6df4;
      border-radius: 9px;
      background: #fff;
      color: #174e99;
      font-size: 0.75rem;
      font-weight: 800;
      cursor: pointer;
    }

    .demo-open-btn:hover {
      background: #eef5ff;
    }

    .demo-overlay {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 1200;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      background: rgba(7, 18, 38, 0.5);
      backdrop-filter: blur(2px);
    }

    .demo-popup {
      width: min(780px, 96vw);
      max-height: 92vh;
      overflow-y: auto;
      border: 2px solid #2a6df4;
      border-radius: 20px;
      background: #f7faff;
      box-shadow: 0 28px 80px rgba(6, 24, 56, 0.34);
    }

    .demo-popup-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 1rem 1.1rem;
      border-bottom: 1px solid #dbe6f3;
      background: #fff;
    }

    .demo-popup-head h3 {
      margin: 0;
      color: #173f72;
      font-size: 1rem;
      letter-spacing: 0.02em;
    }

    .demo-close-btn {
      display: grid;
      place-items: center;
      width: 32px;
      height: 32px;
      padding: 0;
      border: 1px solid #9db4d1;
      border-radius: 50%;
      background: #fff;
      color: #254f80;
      font-size: 1.1rem;
      cursor: pointer;
    }

    .demo-popup-body {
      padding: 1rem 1.1rem;
    }

    .demo-steps-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
    }

    .demo-step-card {
      min-height: 155px;
      padding: 0.7rem;
      border: 1px solid #cad9eb;
      border-radius: 13px;
      background: #fff;
    }

    .demo-step-card:last-child {
      grid-column: 1/-1;
      min-height: 138px;
    }

    .demo-step-title {
      display: flex;
      align-items: center;
      gap: 7px;
      margin-bottom: 0.55rem;
      color: #174e99;
      font-size: 0.68rem;
      font-weight: 900;
      letter-spacing: 0.035em;
      text-transform: uppercase;
    }

    .demo-step-number {
      display: grid;
      place-items: center;
      width: 25px;
      height: 25px;
      border-radius: 7px;
      background: #2a6df4;
      color: #fff;
      font-size: 0.68rem;
    }

    .demo-canvas {
      position: relative;
      min-height: 104px;
      padding: 38px 0.45rem 0.45rem;
      border: 1px dashed #b9cbe2;
      border-radius: 9px;
      background: #f9fbfe;
    }

    .demo-action-label {
      position: absolute;
      z-index: 3;
      top: 5px;
      right: 8px;
      display: flex;
      align-items: center;
      gap: 5px;
      padding: 0.28rem 0.48rem;
      border: 2px solid #e02424;
      border-radius: 16px;
      background: #fff;
      color: #e02424;
      font-size: 0.56rem;
      font-weight: 950;
      letter-spacing: 0.035em;
    }

    .demo-action-label::after {
      content: "";
      position: absolute;
      right: 18px;
      top: 100%;
      width: 2px;
      height: 16px;
      background: #e02424;
    }

    .demo-input-example {
      min-height: 38px;
      display: flex;
      align-items: center;
      padding: 0.45rem 0.6rem;
      border: 3px solid #e02424;
      border-radius: 8px;
      background: #fff;
      color: #0b1a33;
      font:
        900 0.82rem Consolas,
        monospace;
      letter-spacing: 0.1em;
    }

    .demo-bytes {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      border: 1px solid #2a6df4;
      border-radius: 7px;
      background: #fff;
    }

    .demo-byte {
      position: relative;
      padding: 0.5rem 0.2rem;
      border-right: 1px dashed #9db4d1;
      color: #0b1a33;
      font:
        900 0.72rem Consolas,
        monospace;
      text-align: center;
    }

    .demo-byte:last-child {
      border-right: 0;
    }

    .demo-plus {
      position: absolute;
      top: -17px;
      right: -10px;
      z-index: 2;
      display: grid;
      place-items: center;
      width: 20px;
      height: 20px;
      border: 3px solid #e02424;
      border-radius: 50%;
      background: #fff;
      color: #e02424;
      font:
        900 0.7rem Arial,
        sans-serif;
    }

    .demo-partitions {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 4px;
    }

    .demo-partition {
      min-width: 0;
      padding: 0.38rem 0.22rem;
      border: 2px solid var(--demo-color);
      border-radius: 7px;
      background: #fff;
      text-align: center;
    }

    .demo-partition code {
      display: block;
      margin-bottom: 4px;
      color: #0b1a33;
      font:
        900 0.7rem Consolas,
        monospace;
    }

    .demo-control {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 25px;
      padding: 0.2rem;
      border: 1px solid var(--demo-color);
      border-radius: 5px;
      color: #244f7c;
      font-size: 0.48rem;
      font-weight: 850;
    }

    .demo-control.target {
      border: 3px solid #e02424;
      color: #b42318;
    }

    .demo-modes {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 7px;
      max-width: 280px;
      margin: auto;
    }

    .demo-mode {
      display: grid;
      place-items: center;
      min-height: 48px;
      border: 1px solid #0f8a75;
      border-radius: 7px;
      background: #fff;
      color: #076b5a;
      font-size: 0.6rem;
      font-weight: 900;
      text-align: center;
    }

    .demo-mode.target {
      border: 3px solid #e02424;
    }

    .demo-confirm-example {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 39px;
      max-width: 360px;
      margin: auto;
      border: 3px solid #e02424;
      border-radius: 9px;
      background: #0b1a33;
      color: #fff;
      font-size: 0.64rem;
      font-weight: 900;
    }

    .demo-popup-actions {
      display: flex;
      justify-content: flex-end;
      padding: 0.8rem 1.1rem 1rem;
      border-top: 1px solid #dbe6f3;
      background: #fff;
    }

    .demo-popup-actions button {
      min-height: 38px;
      padding: 0.5rem 1rem;
      border: 1px solid #b9cce7;
      border-radius: 9px;
      background: #fff;
      color: #174e99;
      font-weight: 800;
      cursor: pointer;
    }

    .section-highlight {
      animation: sectionHighlight 1.1s ease;
    }

    @keyframes sectionHighlight {

      0%,
      100% {
        box-shadow: none;
      }

      45% {
        box-shadow: 0 0 0 5px rgba(42, 109, 244, 0.18);
      }
    }

    /* Accessible visual refresh */
    :root {
      --primary: #175cd3;
      --primary-dark: #123c7a;
      --primary-soft: #eaf2ff;
      --ink: #102a43;
      --muted: #52677e;
      --line: #cbd8e6;
      --surface: #ffffff;
      --surface-soft: #f5f8fc;
      --success: #067647;
      --success-soft: #ecfdf3;
      --warning: #9a6700;
      --focus: rgba(23, 92, 211, 0.22);
    }

    html {
      font-size: 17px;
      scroll-behavior: smooth;
    }

    body {
      min-height: 100vh;
      color: var(--ink);
      background:
        radial-gradient(circle at top left,
          rgba(23, 92, 211, 0.11),
          transparent 30rem),
        linear-gradient(180deg, #f7fafe 0%, #edf3fa 100%);
      line-height: 1.55;
      padding: 2.2rem 1rem 4rem;
    }

    .card {
      max-width: 1240px;
      border: 1px solid rgba(164, 184, 207, 0.65);
      border-radius: 24px;
      box-shadow: 0 18px 50px -30px rgba(16, 42, 67, 0.65);
      padding: clamp(1.15rem, 3vw, 2.4rem);
    }

    .header-title {
      padding-bottom: 1.15rem;
      margin-bottom: 1.25rem;
      border-bottom: 1px solid #dee7f1;
    }

    .header-title h1 {
      color: var(--ink);
      font-size: clamp(1.55rem, 3vw, 2.05rem);
      font-weight: 800;
      letter-spacing: -0.025em;
    }

    .header-title h1 i,
    .section-title i {
      color: var(--primary);
    }

    .brand-badge {
      color: var(--primary-dark);
      background: var(--primary-soft);
      border-color: #b9d1f7;
      font-size: 0.88rem;
      font-weight: 750;
    }

    .product-meta {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 0.75rem;
      background: #f8fafd;
      border-color: var(--line);
      border-radius: 16px;
      padding: 1rem;
    }

    .meta-item {
      align-items: flex-start;
      flex-direction: column;
      gap: 1px;
      padding: 0.25rem 0.5rem;
    }

    .meta-item .label {
      color: #53697f;
      font-size: 0.72rem;
      font-weight: 750;
      letter-spacing: 0.055em;
    }

    .meta-item .value,
    .meta-item .value.light {
      color: var(--ink);
      font-size: 0.98rem;
      font-weight: 700;
    }

    .section-title {
      color: var(--ink);
      font-size: 1.18rem;
      font-weight: 800;
    }

    .section-desc,
    .section-guidance {
      color: var(--muted);
      font-size: 0.92rem;
      line-height: 1.6;
    }

    .api-partition-section,
    #commandFramesSection,
    .param-grid {
      background: var(--surface) !important;
      border: 1px solid var(--line) !important;
      border-radius: 18px !important;
      box-shadow: 0 8px 24px -22px rgba(16, 42, 67, 0.8);
    }

    .api-partition-section {
      padding: clamp(1rem, 2.5vw, 1.5rem);
    }

    .param-grid {
      padding: clamp(1rem, 2.5vw, 1.4rem);
      gap: 1rem;
    }

    .step-number {
      color: #fff;
      background: var(--primary);
      border-color: var(--primary);
      font-size: 0.72rem;
      letter-spacing: 0.05em;
    }

    .selected-frame-banner,
    .linked-api-panel,
    .partition-instruction {
      border-color: #b8d0f2;
      background: #f1f6fd;
      color: var(--primary-dark);
    }

    .param-row {
      align-items: flex-end;
      gap: 1rem 1.4rem;
    }

    .param-item {
      align-items: flex-start;
      flex-direction: column;
      gap: 0.35rem;
      flex: 1 1 190px;
    }

    .param-item label,
    .api-input-wrap label,
    .byte-modal .form-group label {
      color: #294763;
      font-size: 0.83rem;
      font-weight: 750;
    }

    .param-item input,
    .param-item select,
    .api-input-wrap input,
    .byte-modal .form-group input,
    .byte-modal .form-group select {
      width: 100% !important;
      min-width: 0 !important;
      min-height: 46px;
      padding: 0.65rem 0.8rem;
      border: 1px solid #afc1d4;
      border-radius: 10px;
      color: var(--ink);
      background: #fff;
      font-size: 1rem;
      font-weight: 600;
    }

    input::placeholder {
      color: #71859a;
      opacity: 1;
    }

    button,
    select,
    input {
      font: inherit;
    }

    button {
      min-height: 44px;
      transition:
        background-color 0.15s,
        border-color 0.15s,
        box-shadow 0.15s,
        transform 0.15s;
    }

    button:hover:not(:disabled) {
      transform: translateY(-1px);
    }

    button:focus-visible,
    input:focus-visible,
    select:focus-visible,
    [tabindex]:focus-visible {
      outline: 3px solid var(--focus) !important;
      outline-offset: 2px;
      border-color: var(--primary) !important;
    }

    button:disabled {
      opacity: 0.55;
      cursor: not-allowed;
      transform: none;
    }

    .generate-btn,
    .continue-btn,
    .save-btn,
    .command-entry button,
    .btn-save-modal {
      color: #fff !important;
      background: var(--primary) !important;
      border: 1px solid var(--primary) !important;
      border-radius: 10px !important;
      padding: 0.68rem 1.1rem !important;
      font-size: 0.92rem !important;
      font-weight: 750 !important;
      justify-content: center;
    }

    .generate-btn:hover,
    .continue-btn:hover,
    .save-btn:hover,
    .command-entry button:hover,
    .btn-save-modal:hover {
      background: var(--primary-dark) !important;
    }

    .demo-open-btn,
    .partition-btn,
    .btn-cancel-modal,
    .demo-popup-actions button {
      min-height: 42px;
      border-radius: 9px;
      color: var(--primary-dark);
      border: 1px solid #afc8ed;
      background: #fff;
      font-weight: 750;
    }

    .command-frame-card {
      min-height: 140px;
      border: 1px solid #bfcfe0;
      border-radius: 14px;
      background: #fff;
      padding: 1rem;
    }

    .command-frame-card:hover {
      border-color: var(--primary);
      background: #f7faff;
      box-shadow: 0 8px 20px -18px rgba(23, 92, 211, 0.8);
    }

    .command-frame-card.selected {
      border: 2px solid var(--primary);
      background: var(--primary-soft);
      box-shadow: 0 0 0 3px var(--focus);
    }

    .command-frame-card .frame-title {
      font-size: 1rem;
      font-weight: 800;
    }

    .frame-code,
    .frame-direction,
    .command-frame-hint {
      font-size: 0.8rem;
    }

    .byte-detail-popup {
      border-color: #b8cbe0;
      border-radius: 16px;
      background: #f8fbff;
    }

    .partition-status {
      font-size: 0.88rem;
      line-height: 1.5;
    }

    .partition-status.complete {
      color: var(--success);
    }

    .action-row {
      justify-content: flex-end;
      gap: 0.8rem;
    }

    .save-btn {
      margin-left: 0;
      min-width: 210px;
    }

    .save-status-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      min-height: 42px;
      padding: 0.55rem 0.85rem;
      border: 1px solid #c8d5e3;
      border-radius: 999px;
      color: #53697f;
      background: #f7f9fc;
      font-size: 0.82rem;
      font-weight: 750;
    }

    .save-status-badge.saved {
      color: var(--success);
      border-color: #86d3ad;
      background: var(--success-soft);
    }

    .save-status-badge.unsaved {
      color: var(--warning);
      border-color: #e8c76c;
      background: #fff8e1;
    }

    .save-status-badge.error {
      color: #b42318;
      border-color: #fda29b;
      background: #fff1f0;
    }

    .save-status-badge i {
      font-size: 0.92rem;
    }

    .byte-modal,
    .partition-mode-modal,
    .demo-popup {
      border: 1px solid #b8c8d9;
      border-radius: 18px;
    }

    .byte-modal-overlay,
    .partition-mode-overlay,
    .demo-overlay {
      background: rgba(16, 42, 67, 0.62);
      backdrop-filter: blur(3px);
    }

    .close-modal,
    .partition-mode-close,
    .demo-close-btn,
    .byte-detail-popup .close {
      display: grid;
      place-items: center;
      min-width: 44px;
      min-height: 44px;
      border-radius: 9px;
    }

    /* Rich icon system and purposeful micro-interactions */
    .card {
      position: relative;
      overflow: hidden;
      isolation: isolate;
    }

    .card::before {
      content: "\f121";
      position: absolute;
      z-index: -1;
      top: -64px;
      right: -35px;
      color: rgba(23, 92, 211, 0.045);
      font: 900 15rem/1 "Font Awesome 6 Free";
      transform: rotate(-10deg);
      pointer-events: none;
    }

    .title-icon {
      display: inline-grid;
      place-items: center;
      width: 46px;
      height: 46px;
      border-radius: 14px;
      color: #fff;
      background: linear-gradient(135deg, #175cd3, #6d8ff8);
      box-shadow: 0 10px 22px -10px rgba(23, 92, 211, 0.8);
      animation: titleIconFloat 3.8s ease-in-out infinite;
    }

    .brand-badge i {
      animation: tagWiggle 4s ease-in-out infinite;
      transform-origin: top center;
    }

    .meta-item {
      position: relative;
      min-height: 76px;
      padding: 0.65rem 0.65rem 0.65rem 3.35rem;
      border: 1px solid transparent;
      border-radius: 13px;
      transition:
        border-color 0.22s,
        background-color 0.22s,
        transform 0.22s,
        box-shadow 0.22s;
    }

    .meta-item:hover {
      border-color: #c7d9f2;
      background: #fff;
      box-shadow: 0 9px 20px -18px rgba(16, 42, 67, 0.8);
      transform: translateY(-2px);
    }

    .meta-icon {
      position: absolute;
      left: 0.65rem;
      top: 50%;
      display: grid;
      place-items: center;
      width: 38px;
      height: 38px;
      border-radius: 11px;
      color: var(--primary);
      background: var(--primary-soft);
      border: 1px solid #c4d8f7;
      transform: translateY(-50%);
      transition:
        transform 0.22s,
        background-color 0.22s,
        color 0.22s;
    }

    .meta-item:hover .meta-icon {
      color: #fff;
      background: var(--primary);
      transform: translateY(-50%) scale(1.08) rotate(-4deg);
    }

    .step-number {
      position: relative;
      overflow: hidden;
      box-shadow: 0 7px 15px -10px rgba(23, 92, 211, 0.8);
    }

    .step-number::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(110deg,
          transparent 25%,
          rgba(255, 255, 255, 0.5) 50%,
          transparent 75%);
      transform: translateX(-150%);
      animation: stepShine 4s ease-in-out infinite;
    }

    .section-title>i {
      display: inline-grid;
      place-items: center;
      width: 34px;
      height: 34px;
      border-radius: 10px;
      background: var(--primary-soft);
      transition:
        transform 0.2s,
        background-color 0.2s,
        color 0.2s;
    }

    .section-title:hover>i {
      color: #fff;
      background: var(--primary);
      transform: rotate(-7deg) scale(1.06);
    }

    .section-guidance,
    .section-desc {
      display: flex;
      align-items: flex-start;
      gap: 0.5rem;
    }

    .section-guidance i,
    .section-desc i {
      color: var(--primary);
      margin-top: 0.25rem;
      flex: 0 0 auto;
    }

    .control-label {
      display: flex !important;
      align-items: center;
      gap: 0.45rem;
    }

    .control-label i {
      display: inline-grid;
      place-items: center;
      width: 26px;
      height: 26px;
      border-radius: 8px;
      color: var(--primary);
      background: var(--primary-soft);
      font-size: 0.72rem;
    }

    .param-item:focus-within .control-label i,
    .api-input-wrap:focus-within .control-label i {
      color: #fff;
      background: var(--primary);
      animation: iconPop 0.28s ease;
    }

    .select-with-icon,
    .input-with-icon {
      position: relative;
      width: 100%;
    }

    .select-with-icon>i,
    .input-with-icon>i {
      position: absolute;
      z-index: 2;
      left: 0.8rem;
      top: 50%;
      color: #6280a4;
      transform: translateY(-50%);
      pointer-events: none;
    }

    .select-with-icon select,
    .input-with-icon input {
      padding-left: 2.35rem !important;
    }

    .select-with-icon:focus-within>i,
    .input-with-icon:focus-within>i {
      color: var(--primary);
      animation: iconPop 0.28s ease;
    }

    .param-grid,
    .api-partition-section,
    #commandFramesSection,
    .action-row {
      animation: sectionReveal 0.5s both;
    }

    #commandFramesSection {
      animation-delay: 0.08s;
    }

    .action-row {
      animation-delay: 0.16s;
    }

    .command-frame-card {
      position: relative;
      overflow: hidden;
      animation: cardReveal 0.38s both;
      transition:
        transform 0.22s,
        border-color 0.22s,
        background-color 0.22s,
        box-shadow 0.22s;
    }

    .command-frame-card::before {
      content: "\f120";
      position: absolute;
      right: -8px;
      bottom: -12px;
      color: rgba(23, 92, 211, 0.06);
      font: 900 4.8rem/1 "Font Awesome 6 Free";
      pointer-events: none;
      transition:
        transform 0.25s,
        color 0.25s;
    }

    .command-frame-card:hover::before {
      color: rgba(23, 92, 211, 0.12);
      transform: translate(-4px, -4px) rotate(-5deg);
    }

    .command-frame-card:hover {
      transform: translateY(-4px);
    }

    .frame-title>span {
      display: inline-flex;
      align-items: center;
      gap: 0.48rem;
    }

    .frame-title>span::before {
      content: "\f120";
      color: var(--primary);
      font: 900 0.82rem/1 "Font Awesome 6 Free";
    }

    .remove-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      min-width: 34px;
      min-height: 30px;
      padding: 0.28rem 0.55rem;
      border-radius: 9px;
      background: #fff1f0;
      border: 1px solid #ffd1cc;
      color: #b91c1c;
      font-family: inherit;
      font-size: 0.68rem;
      font-weight: 800;
      cursor: pointer;
      transition:
        transform 0.18s,
        background-color 0.18s,
        color 0.18s;
      z-index: 2;
      position: relative;
    }

    .remove-btn:hover {
      color: #fff;
      background: #d92d20;
      border-color: #d92d20;
      transform: translateY(-1px);
    }

    .frame-delete-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      width: 100%;
      margin-top: 0.65rem;
      min-height: 34px;
      padding: 0.4rem 0.65rem;
      border: 1px solid #f0b4ad;
      border-radius: 10px;
      background: #fff7f6;
      color: #b42318;
      font-family: inherit;
      font-size: 0.74rem;
      font-weight: 800;
      cursor: pointer;
      position: relative;
      z-index: 2;
    }

    .frame-delete-btn:hover {
      background: #d92d20;
      border-color: #d92d20;
      color: #fff;
    }

    .partition-btn i,
    .generate-btn i,
    .continue-btn i,
    .save-btn i,
    .demo-open-btn i,
    .partition-mode-done i,
    .modal-actions button i,
    .demo-popup-actions button i {
      transition: transform 0.2s;
    }

    button:hover:not(:disabled) i {
      transform: scale(1.13);
    }

    .generate-btn:hover i,
    .partition-btn.primary:hover i {
      transform: rotate(135deg) scale(1.08);
    }

    .save-btn:hover i,
    .btn-save-modal:hover i {
      animation: saveBounce 0.55s ease;
    }

    button:active:not(:disabled) {
      transform: translateY(1px) scale(0.985) !important;
    }

    .save-status-badge {
      transition:
        color 0.25s,
        border-color 0.25s,
        background-color 0.25s,
        transform 0.25s,
        box-shadow 0.25s;
    }

    .save-status-badge.status-change {
      animation: statusPop 0.48s ease;
    }

    .save-status-badge.saved {
      box-shadow: 0 0 0 4px rgba(6, 118, 71, 0.08);
    }

    .save-status-badge.saved i {
      animation: savedCheck 0.55s ease;
    }

    .api-count i,
    .partition-status i,
    .linked-api-state i,
    .frame-partition-state i {
      animation: softPulse 2.4s ease-in-out infinite;
    }

    .api-byte-cell {
      animation: byteReveal 0.3s both;
    }

    .partition-name-band {
      animation: bandReveal 0.34s both;
    }

    .byte-modal-overlay[style*="flex"] .byte-modal,
    .partition-mode-overlay[style*="flex"] .partition-mode-modal,
    .demo-overlay[style*="flex"] .demo-popup {
      animation: modalEnter 0.28s cubic-bezier(0.2, 0.8, 0.2, 1) both;
    }

    .byte-modal-overlay[style*="flex"],
    .partition-mode-overlay[style*="flex"],
    .demo-overlay[style*="flex"] {
      animation: fadeIn 0.2s ease both;
    }

    .byte-detail-popup[style*="block"] {
      animation: modalEnter 0.28s cubic-bezier(0.2, 0.8, 0.2, 1) both;
    }

    .demo-step-card {
      transition:
        transform 0.2s,
        border-color 0.2s,
        box-shadow 0.2s;
      animation: cardReveal 0.42s both;
    }

    .demo-step-card:hover {
      transform: translateY(-3px);
      border-color: #90b5ec;
      box-shadow: 0 12px 25px -22px rgba(16, 42, 67, 0.9);
    }

    .demo-step-card:nth-child(2) {
      animation-delay: 0.06s;
    }

    .demo-step-card:nth-child(3) {
      animation-delay: 0.12s;
    }

    .demo-step-card:nth-child(4) {
      animation-delay: 0.18s;
    }

    .demo-step-card:nth-child(5) {
      animation-delay: 0.24s;
    }

    .interactive-icon {
      display: inline-grid;
      place-items: center;
      width: 30px;
      height: 30px;
      border-radius: 9px;
      color: var(--primary);
      background: var(--primary-soft);
    }

    .empty-command,
    .timeline-empty,
    .field-empty {
      position: relative;
      overflow: hidden;
    }

    .timeline-empty::before,
    .field-empty::before {
      content: "\f1c9";
      display: block;
      margin-bottom: 0.35rem;
      color: #9cb2ca;
      font: 900 1.4rem/1 "Font Awesome 6 Free";
      animation: softPulse 2.4s ease-in-out infinite;
    }

    @keyframes titleIconFloat {

      0%,
      100% {
        transform: translateY(0) rotate(0);
      }

      50% {
        transform: translateY(-4px) rotate(-3deg);
      }
    }

    @keyframes tagWiggle {

      0%,
      88%,
      100% {
        transform: rotate(0);
      }

      92% {
        transform: rotate(-12deg);
      }

      96% {
        transform: rotate(10deg);
      }
    }

    @keyframes stepShine {

      0%,
      60% {
        transform: translateX(-150%);
      }

      85%,
      100% {
        transform: translateX(150%);
      }
    }

    @keyframes sectionReveal {
      from {
        opacity: 0;
        transform: translateY(12px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes cardReveal {
      from {
        opacity: 0;
        transform: translateY(12px) scale(0.98);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @keyframes byteReveal {
      from {
        opacity: 0;
        transform: translateY(-7px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes bandReveal {
      from {
        opacity: 0;
        transform: scaleY(0.82);
        transform-origin: top;
      }

      to {
        opacity: 1;
        transform: scaleY(1);
      }
    }

    @keyframes modalEnter {
      from {
        opacity: 0;
        transform: translateY(16px) scale(0.96);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    @keyframes iconPop {
      50% {
        transform: scale(1.16) rotate(-7deg);
      }
    }

    @keyframes saveBounce {

      0%,
      100% {
        transform: translateY(0);
      }

      45% {
        transform: translateY(-4px);
      }

      70% {
        transform: translateY(1px);
      }
    }

    @keyframes statusPop {
      0% {
        transform: scale(0.94);
      }

      55% {
        transform: scale(1.035);
      }

      100% {
        transform: scale(1);
      }
    }

    @keyframes savedCheck {
      0% {
        transform: scale(0.4) rotate(-30deg);
      }

      70% {
        transform: scale(1.2) rotate(5deg);
      }

      100% {
        transform: scale(1) rotate(0);
      }
    }

    @keyframes softPulse {

      0%,
      100% {
        opacity: 1;
        transform: scale(1);
      }

      50% {
        opacity: 0.68;
        transform: scale(0.92);
      }
    }

    @media (max-width: 650px) {
      html {
        font-size: 16px;
      }

      body {
        padding: 0.65rem 0.45rem 3rem;
      }

      .card {
        padding: 1rem;
        border-radius: 17px;
      }

      .header-title {
        align-items: flex-start;
        gap: 0.8rem;
      }

      .product-meta {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .param-row {
        flex-direction: column;
        align-items: stretch;
      }

      .param-item {
        flex-basis: auto;
        width: 100%;
      }

      .byte-modal {
        padding: 1.2rem;
      }

      .field-desc-header {
        align-items: stretch;
      }

      .field-desc-dropdown {
        align-items: stretch;
        flex-direction: column;
      }

      .field-desc-dropdown select {
        width: 100%;
        min-width: 0;
      }

      #fieldDescContainer {
        grid-template-columns: 1fr;
        padding: 1rem;
      }

      .field-desc-help {
        margin: 1rem 1rem 0;
      }

      .command-frame-help {
        justify-content: flex-start;
        margin-top: 0.65rem;
      }

      .command-example {
        width: 100%;
        overflow-x: auto;
      }

      .api-entry-row {
        grid-template-columns: 1fr;
      }

      .api-count {
        width: max-content;
        max-width: 100%;
      }

      .api-partition-section {
        padding: 1rem;
      }

      .section-guidance {
        margin-left: 0;
        margin-top: 5px;
      }

      .linked-api-panel {
        flex-direction: column;
      }

      .demo-open-btn {
        margin-left: 0;
      }

      .demo-steps-grid {
        grid-template-columns: 1fr;
      }

      .demo-step-card:last-child {
        grid-column: auto;
      }

      .partition-confirm-row,
      .action-row {
        align-items: stretch;
        flex-direction: column;
      }

      .continue-btn,
      .save-btn,
      .save-status-badge {
        width: 100%;
      }

      .save-status-badge {
        justify-content: center;
      }

      .card::before {
        font-size: 10rem;
      }

      .title-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
      }

      .meta-item {
        min-height: 70px;
        padding-left: 3rem;
      }

      .meta-icon {
        width: 34px;
        height: 34px;
      }
    }

    @media (prefers-reduced-motion: reduce) {

      *,
      *::before,
      *::after {
        scroll-behavior: auto !important;
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
      }
    }

    /* EFACTOR warm earth palette */
    :root {
      --primary: #a98b76;
      --primary-dark: #765c4b;
      --primary-soft: #f3e4c9;
      --secondary: #bfa28c;
      --sage: #babf94;
      --ink: #382e28;
      --muted: #695a50;
      --line: #d8c7b8;
      --surface: #fffdf8;
      --surface-soft: #f7f0e4;
      --success: #5f6844;
      --success-soft: #eef0de;
      --warning: #806441;
      --focus: rgba(169, 139, 118, 0.34);
    }

    body {
      color: var(--ink);
      background:
        radial-gradient(circle at top left,
          rgba(186, 191, 148, 0.34),
          transparent 31rem),
        linear-gradient(180deg, #f3e4c9 0%, #e8d6c5 100%);
    }

    .card {
      background: #fffdf8;
      border-color: #cdb9a8;
      box-shadow: 0 18px 50px -30px rgba(83, 61, 47, 0.5);
    }

    .card::before,
    .command-frame-card::before {
      color: rgba(169, 139, 118, 0.09);
    }

    .command-frame-card:hover::before {
      color: rgba(169, 139, 118, 0.17);
    }

    .header-title {
      border-color: #decdbf;
    }

    .title-icon {
      background: linear-gradient(135deg, #a98b76, #bfa28c);
      box-shadow: 0 10px 22px -10px rgba(118, 92, 75, 0.7);
    }

    .brand-badge,
    .meta-icon,
    .section-title>i,
    .control-label i,
    .interactive-icon,
    .field-desc-title-icon {
      color: #765c4b;
      background: #f3e4c9;
      border-color: #d7beaa;
      box-shadow: 0 6px 14px rgba(118, 92, 75, 0.18);
    }

    .product-meta,
    .param-grid,
    .api-partition-section,
    #commandFramesSection,
    .field-desc-section,
    .field-desc-header,
    .partition-workspace,
    .byte-detail-popup {
      background: #fffdf8 !important;
      border-color: #d8c7b8 !important;
    }

    .product-meta {
      background: #f7f0e4 !important;
    }

    .field-desc-header,
    .linked-api-panel,
    .selected-frame-banner,
    .partition-instruction,
    .command-entry,
    .field-desc-help,
    .byte-wise-action,
    .demo-popup {
      color: #59483d;
      background: #f3e4c9 !important;
      border-color: #cdb49f !important;
    }

    .meta-item .label,
    .section-desc,
    .section-guidance,
    .command-frame-hint,
    .partition-status,
    .api-count,
    .partition-instruction,
    .field-empty,
    .timeline-empty,
    .empty-command,
    .byte-editor-hint,
    .byte-item .byte-label,
    .field-byte-index,
    .binary-labels,
    .partition-limit,
    .api-byte-index,
    .select-with-icon>i,
    .input-with-icon>i {
      color: #695a50 !important;
    }

    .header-title h1,
    .section-title,
    .field-desc-title,
    .command-frame-card .frame-title,
    .command-code-label,
    .command-character,
    .frame-code strong,
    .api-byte-value,
    .linked-api-code,
    .linked-partition-chip code,
    .byte-modal h3 {
      color: #382e28 !important;
    }

    .frame-code,
    .byte-detail-popup .popup-item .idx,
    .byte-detail-popup .popup-item .val,
    .command-byte-count,
    .partition-mode-heading code,
    .linked-partition-chip {
      color: #59483d !important;
    }

    .meta-item:hover {
      border-color: #bfa28c;
      background: #fffdf8;
      box-shadow: 0 9px 20px -18px rgba(83, 61, 47, 0.65);
    }

    .meta-item:hover .meta-icon,
    .section-title:hover>i,
    .param-item:focus-within .control-label i,
    .api-input-wrap:focus-within .control-label i {
      color: #fffdf8;
      background: #a98b76;
    }

    .section-guidance i,
    .section-desc i,
    .section-title i,
    .field-desc-help i,
    .partition-instruction i,
    .linked-api-title i,
    .frame-title>span::before {
      color: #8a6c58 !important;
    }

    .step-number,
    .demo-step-number,
    .selected-frame-banner span,
    .view-toggle-btn.active,
    .partition-btn.primary {
      color: #fffdf8 !important;
      background: #a98b76 !important;
      border-color: #a98b76 !important;
      box-shadow: 0 7px 15px -10px rgba(118, 92, 75, 0.75);
    }

    .generate-btn,
    .continue-btn,
    .save-btn,
    .command-entry button,
    .btn-save-modal,
    .partition-mode-done {
      color: #fffdf8 !important;
      background: #765c4b !important;
      border-color: #765c4b !important;
    }

    .generate-btn:hover,
    .continue-btn:hover,
    .save-btn:hover,
    .command-entry button:hover,
    .btn-save-modal:hover,
    .partition-mode-done:hover {
      background: #594238 !important;
      border-color: #594238 !important;
    }

    .demo-open-btn,
    .partition-btn,
    .btn-cancel-modal,
    .demo-popup-actions button,
    .add-frame-btn,
    .byte-wise-action {
      color: #765c4b !important;
      border-color: #bfa28c !important;
      background: #fffdf8 !important;
    }

    .demo-open-btn:hover,
    .partition-btn:hover,
    .btn-cancel-modal:hover,
    .demo-popup-actions button:hover,
    .byte-wise-action:hover {
      color: #49382e !important;
      border-color: #a98b76 !important;
      background: #f3e4c9 !important;
    }

    .param-item input,
    .param-item select,
    .api-input-wrap input,
    .byte-modal .form-group input,
    .byte-modal .form-group select,
    .command-entry input,
    .byte-field-select,
    .partition-name-band select {
      color: #382e28 !important;
      background: #fffdf8 !important;
      border-color: #bfae9f !important;
    }

    input::placeholder {
      color: #88766a;
    }

    button:focus-visible,
    input:focus-visible,
    select:focus-visible,
    [tabindex]:focus-visible {
      outline-color: var(--focus) !important;
      border-color: #a98b76 !important;
    }

    .command-frame-card,
    .field-desc-card,
    .demo-step-card,
    .demo-canvas,
    .demo-popup-head,
    .demo-popup-actions,
    .partition-mode-body {
      color: #382e28;
      background: #fffdf8 !important;
      border-color: #d8c7b8 !important;
    }

    .command-frame-card:hover,
    .command-frame-card.selected {
      border-color: #a98b76 !important;
      background: #f3e4c9 !important;
      box-shadow: 0 0 0 3px rgba(169, 139, 118, 0.22);
    }

    .byte-item {
      color: #382e28;
      background: #f7f0e4;
      border-color: #d8c7b8;
    }

    .byte-item.active {
      color: #382e28;
      border-color: #a98b76;
      background: #f3e4c9;
      box-shadow: 0 0 0 2px rgba(169, 139, 118, 0.22);
    }

    .frame-direction,
    .view-toggle-buttons {
      color: #765c4b;
      background: #efe3d5;
      border-color: #d7beaa;
    }

    .example-result,
    .linked-api-title,
    .linked-partition-chip strong,
    .demo-step-title,
    .demo-popup-head h3,
    .demo-close-btn,
    .demo-control {
      color: #765c4b !important;
    }

    .example-byte,
    .command-character,
    .field-byte-badge {
      color: #382e28;
      background: #bfa28c;
      border-color: #a98b76;
    }

    .command-character:hover {
      background: #d7beaa;
    }

    .command-entry button.add-command-byte {
      color: #765c4b !important;
      background: #fffdf8 !important;
      border-color: #a98b76 !important;
    }

    .command-entry button.add-command-byte:hover {
      background: #f3e4c9 !important;
    }

    .bit:hover,
    .bit.bit-1 {
      color: #49382e;
      border-color: #a98b76;
      background: #f3e4c9;
      box-shadow: 0 0 0 2px rgba(169, 139, 118, 0.17);
    }

    .bit.bit-0 {
      color: #695a50;
      background: #f7f0e4;
      border-color: #d8c7b8;
    }

    .binary-value,
    .demo-confirm-example {
      color: #fffdf8;
      background: #765c4b;
    }

    .api-timeline,
    .api-byte-cell,
    .timeline-empty,
    .field-empty {
      color: #382e28;
      background: #fffdf8;
      border-color: #d8c7b8;
    }

    .cut-target:hover::before,
    .cut-target.active::before {
      border-color: #a98b76;
    }

    .cut-target:hover::after,
    .cut-target.active::after {
      color: #fffdf8;
      border-color: #a98b76;
      background: #a98b76;
    }

    .partition-mode-modal {
      color: #382e28;
      background: var(--partition-soft, #f3e4c9);
      border-color: var(--partition-color, #a98b76);
    }

    .partition-mode-head,
    .partition-mode-close {
      border-color: var(--partition-color, #a98b76);
      color: var(--partition-text, #49382e);
    }

    .byte-modal,
    .demo-popup,
    .partition-mode-modal {
      border-color: #bfa28c;
      box-shadow: 0 24px 70px rgba(70, 50, 38, 0.32);
    }

    .byte-modal-overlay,
    .partition-mode-overlay,
    .demo-overlay {
      background: rgba(56, 46, 40, 0.62);
    }

    .save-status-badge {
      color: #695a50;
      border-color: #cdb9a8;
      background: #f7f0e4;
    }

    .save-status-badge.saved,
    .partition-status.complete,
    .frame-partition-state.complete,
    .linked-api-state {
      color: #5f6844;
      border-color: #babf94;
      background: #eef0de;
    }

    .save-status-badge.saved {
      box-shadow: 0 0 0 4px rgba(186, 191, 148, 0.2);
    }

    .save-status-badge.unsaved,
    .frame-partition-state,
    .linked-api-state.pending {
      color: #806441;
      border-color: #bfa28c;
      background: #f3e4c9;
    }

    .demo-popup,
    .demo-bytes {
      border-color: #a98b76;
    }

    .demo-mode {
      color: #5f6844;
      border-color: #babf94;
      background: #eef0de;
    }

    .demo-step-card:hover {
      border-color: #bfa28c;
      box-shadow: 0 12px 25px -22px rgba(83, 61, 47, 0.85);
    }

    .timeline-empty::before,
    .field-empty::before {
      color: #a98b76;
    }

    /*
         * Partition workspace exception:
         * do not apply the warm page palette here. The original high-contrast
         * colours are intentionally preserved so users can distinguish bytes,
         * cut points and neighbouring partitions.
         */
    #apiPartitionSection {
      color: #0b1a33;
      background: linear-gradient(145deg, #e3d3b6, #dccaa9) !important;
      border-color: #cddcf0 !important;
      box-shadow: 0 12px 28px -24px rgba(22, 65, 130, 0.75);
    }

    #apiPartitionSection .step-number {
      color: #fff !important;
      background: #a98b76 !important;
      border-color: #a98b76 !important;
      box-shadow: 0 7px 15px -10px #bfa28c;
    }

    #apiPartitionSection .section-title,
    #apiPartitionSection .section-title i {
      color: #0b1a33 !important;
    }

    #apiPartitionSection .selected-frame-banner {
      color: #bfa28c;
      background: #fff !important;
      border-color: #fff !important;
    }

    #apiPartitionSection .selected-frame-banner span {
      color: #fff !important;
      background: #a98b76 !important;
      border-color: #bfa28c !important;
    }

    #apiPartitionSection .section-desc,
    #apiPartitionSection .section-desc i {
      color: #526b89 !important;
    }

    #apiPartitionSection .api-input-wrap label,
    #apiPartitionSection .control-label,
    #apiPartitionSection .control-label i {
      color: #25415d !important;
    }

    #apiPartitionSection .api-input-wrap input {
      color: #0b1a33 !important;
      background: #fff !important;
      border-color: #aebfd8 !important;
    }

    #apiPartitionSection .api-input-wrap input:focus {
      border-color: #2a6df4 !important;
      box-shadow: 0 0 0 3px rgba(42, 109, 244, 0.14);
    }

    #apiPartitionSection .api-count {
      color: #45617f !important;
      background: #fff !important;
      border-color: #d5e0ee !important;
    }

    #apiPartitionSection .partition-workspace {
      color: #0b1a33;
      background: #fff !important;
      border-color: #d9e4f2 !important;
    }

    #apiPartitionSection .partition-instruction {
      color: #526b89 !important;
      background: #f8fbff !important;
      border-color: #d9e4f2 !important;
    }

    #apiPartitionSection .partition-instruction i {
      color: #2a6df4 !important;
    }

    #apiPartitionSection .api-timeline {
      color: #0b1a33;
      background: #f8fbff;
      border-color: #b9cae0;
    }

    #apiPartitionSection .api-byte-cell {
      color: #0b1a33;
      background: #fff;
      border-color: #dce6f2;
    }

    #apiPartitionSection .api-byte-cell.partition-colored {
      background: var(--partition-soft);
      box-shadow: inset 0 4px 0 var(--partition-color);
    }

    #apiPartitionSection .api-byte-index {
      color: #7a8da7 !important;
    }

    #apiPartitionSection .api-byte-value,
    #apiPartitionSection .api-byte-cell.partition-colored .api-byte-value {
      color: var(--partition-text, #0b1a33) !important;
    }

    #apiPartitionSection .timeline-empty {
      color: #71839a !important;
      background: #fbfdff;
      border-color: #c4d3e5;
    }

    #apiPartitionSection .timeline-empty::before {
      color: #2a6df4;
    }

    #apiPartitionSection .cut-target::before {
      border-color: #c6d4e5;
    }

    #apiPartitionSection .cut-target::after {
      content: none !important;
    }

    #apiPartitionSection .cut-target .cut-chip {
      color: #47729f;
      background: #fff;
      border-color: #b8c9df;
    }

    #apiPartitionSection .cut-target:hover::before {
      border-color: #e34b4b;
    }

    #apiPartitionSection .cut-target:hover .cut-chip {
      color: #e34b4b;
      background: #fff1f1;
      border-color: #e34b4b;
    }

    #apiPartitionSection .cut-target.active::before {
      border-left-color: #e34b4b;
    }

    #apiPartitionSection .cut-target.active .cut-chip {
      color: #fff;
      background: #e34b4b;
      border-color: #e34b4b;
      box-shadow: 0 3px 9px rgba(227, 75, 75, 0.28);
    }

    #apiPartitionSection .cut-target.active.partition-colored::before {
      border-left-color: var(--partition-color);
    }

    #apiPartitionSection .cut-target.active.partition-colored .cut-chip {
      color: #fff;
      background: var(--partition-color);
      border-color: var(--partition-color);
    }

    #apiPartitionSection .partition-howto-step {
      background: #fff;
      border-color: #c5d7c8;
    }

    #apiPartitionSection .partition-howto-step .howto-num {
      background: #659287;
    }

    #apiPartitionSection .partition-howto-step i,
    #apiPartitionSection .cut-coach-pill i {
      color: #659287;
    }

    #apiPartitionSection .partition-visual {
      background: linear-gradient(180deg, #f7fbf6 0%, #eef6ec 100%);
      border-color: #b8cdc4;
    }

    #apiPartitionSection .partition-visual-title {
      color: #203b35;
    }

    #apiPartitionSection .partition-visual-title i {
      color: #659287;
    }

    #apiPartitionSection .partition-visual-block {
      background: #fff;
    }

    #apiPartitionSection .partition-name-band {
      color: var(--partition-text);
      background: var(--partition-soft);
      border-color: var(--partition-color);
    }

    #apiPartitionSection .partition-name-band label,
    #apiPartitionSection .partition-name-band select {
      color: var(--partition-text) !important;
    }

    #apiPartitionSection .partition-name-band select {
      background: #fff !important;
      border-color: var(--partition-color) !important;
    }

    #apiPartitionSection .partition-btn {
      color: #f3e4c9 !important;
      background: #a98b76 !important;
      border-color: #bfa28c !important;
    }

    #apiPartitionSection .partition-btn:hover {
      color: #174e99 !important;
      background: #eef5ff !important;
      border-color: #2a6df4 !important;
    }

    #apiPartitionSection .partition-btn.primary {
      color: #fff !important;
      background: #5e361a !important;
      border-color: #a98b76 !important;
    }

    #apiPartitionSection .partition-limit,
    #apiPartitionSection .partition-status {
      color: #617793 !important;
    }

    #apiPartitionSection .partition-status.complete {
      color: #167647 !important;
    }

    #apiPartitionSection .partition-status.error {
      color: #b42318 !important;
    }

    #apiPartitionSection .continue-btn {
      color: #fff !important;
      background: #0b1a33 !important;
      border-color: #0b1a33 !important;
    }

    #apiPartitionSection .demo-open-btn {
      color: #174e99 !important;
      background: #fff !important;
      border-color: #b9cce7 !important;
    }

    #apiPartitionSection .demo-open-btn:hover {
      color: #174e99 !important;
      background: #eef5ff !important;
      border-color: #2a6df4 !important;
    }

    @keyframes sectionHighlight {

      0%,
      100% {
        box-shadow: none;
      }

      45% {
        box-shadow: 0 0 0 5px rgba(169, 139, 118, 0.24);
      }
    }

    @keyframes newBytePulse {
      0% {
        transform: scale(0.85);
        box-shadow: 0 0 0 5px rgba(169, 139, 118, 0.24);
      }

      100% {
        transform: scale(1);
        box-shadow: none;
      }
    }

    /* Final simple green UI theme: solid colours and clear states */
    :root {
      --primary: #659287;
      --primary-dark: #3f6d63;
      --primary-soft: #e6f2dd;
      --secondary: #88bda4;
      --secondary-soft: #b1d3b9;
      --ink: #203b35;
      --muted: #526a64;
      --line: #b8cdc4;
      --surface: #ffffff;
      --surface-soft: #f3f8f1;
      --success: #2f765c;
      --success-soft: #e7f5ec;
      --warning: #b42318;
      --focus: rgba(101, 146, 135, 0.28);
    }

    html {
      background: #e6f2dd;
    }

    body {
      color: var(--ink);
      background: #e6f2dd !important;
      background-image: none !important;
    }

    .card {
      background: #ffffff !important;
      background-image: none !important;
      border: 1px solid #b8cdc4;
      box-shadow: 0 14px 34px -24px rgba(32, 59, 53, 0.48);
    }

    .card::before,
    .step-number::after {
      display: none;
    }

    .title-icon {
      color: #ffffff;
      background: #659287 !important;
      background-image: none !important;
      box-shadow: none;
    }

    .brand-badge,
    .meta-icon,
    .section-title>i,
    .control-label i,
    .interactive-icon,
    .field-desc-title-icon {
      color: #3f6d63 !important;
      background: #e6f2dd !important;
      background-image: none !important;
      border-color: #b1d3b9 !important;
      box-shadow: none;
    }

    .product-meta {
      background: #f3f8f1 !important;
      background-image: none !important;
      border-color: #b8cdc4 !important;
    }

    .param-grid,
    .api-partition-section,
    #commandFramesSection,
    .field-desc-section,
    .field-desc-header,
    .partition-workspace,
    .byte-detail-popup,
    .linked-api-panel,
    .selected-frame-banner,
    .partition-instruction,
    .command-entry,
    .field-desc-help {
      background-image: none !important;
      border-color: #b8cdc4 !important;
    }

    .param-grid,
    #commandFramesSection,
    .field-desc-section,
    .partition-workspace {
      background: #ffffff !important;
    }

    .field-desc-header,
    .linked-api-panel,
    .selected-frame-banner,
    .partition-instruction,
    .command-entry,
    .field-desc-help {
      color: #355c53 !important;
      background: #e6f2dd !important;
    }

    .step-number,
    .selected-frame-banner span,
    .view-toggle-btn.active,
    .partition-btn.primary {
      color: #ffffff !important;
      background: #659287 !important;
      background-image: none !important;
      border-color: #659287 !important;
      box-shadow: none;
    }

    .generate-btn,
    .continue-btn,
    .save-btn,
    .command-entry button,
    .btn-save-modal,
    .partition-mode-done {
      color: #ffffff !important;
      background: #3f6d63 !important;
      background-image: none !important;
      border-color: #3f6d63 !important;
    }

    .generate-btn:hover,
    .continue-btn:hover,
    .save-btn:hover,
    .command-entry button:hover,
    .btn-save-modal:hover,
    .partition-mode-done:hover {
      background: #2f554d !important;
      border-color: #2f554d !important;
    }

    .demo-open-btn,
    .partition-btn,
    .btn-cancel-modal,
    .demo-popup-actions button,
    .add-frame-btn,
    .byte-wise-action {
      color: #3f6d63 !important;
      background: #ffffff !important;
      border-color: #88bda4 !important;
    }

    .demo-open-btn:hover,
    .partition-btn:hover,
    .btn-cancel-modal:hover,
    .demo-popup-actions button:hover,
    .byte-wise-action:hover {
      color: #203b35 !important;
      background: #e6f2dd !important;
      border-color: #659287 !important;
    }

    .param-item input,
    .param-item select,
    .api-input-wrap input,
    .byte-modal .form-group input,
    .byte-modal .form-group select,
    .command-entry input,
    .byte-field-select {
      color: #203b35 !important;
      background: #ffffff !important;
      border-color: #9fb9ae !important;
    }

    button:focus-visible,
    input:focus-visible,
    select:focus-visible,
    [tabindex]:focus-visible {
      outline-color: var(--focus) !important;
      border-color: #659287 !important;
    }

    .command-frame-card:hover,
    .command-frame-card.selected {
      border-color: #659287 !important;
      background: #e6f2dd !important;
      box-shadow: 0 0 0 3px rgba(101, 146, 135, 0.18);
    }

    .command-frame-card.needs-partition,
    .command-frame-card.needs-partition:hover {
      border: 2px dashed #d4a017 !important;
      background: #fffbf2 !important;
      box-shadow: inset 0 4px 0 #f0b429 !important;
    }

    .command-frame-card.frame-in-progress,
    .command-frame-card.frame-in-progress:hover {
      border: 2px solid #2a6df4 !important;
      background: #f5f9ff !important;
      box-shadow: inset 0 4px 0 #2a6df4 !important;
    }

    .command-frame-card.partition-complete,
    .command-frame-card.partition-complete:hover {
      border: 2px solid #1b8a55 !important;
      background: #f3fbf6 !important;
      box-shadow: inset 0 4px 0 #1b8a55 !important;
    }

    .command-frame-card.selected.needs-partition {
      box-shadow:
        inset 0 4px 0 #f0b429,
        0 0 0 3px rgba(240, 180, 41, 0.28) !important;
    }

    .command-frame-card.selected.frame-in-progress {
      box-shadow:
        inset 0 4px 0 #2a6df4,
        0 0 0 3px rgba(42, 109, 244, 0.25) !important;
    }

    .command-frame-card.selected.partition-complete {
      box-shadow:
        inset 0 4px 0 #1b8a55,
        0 0 0 3px rgba(27, 138, 85, 0.25) !important;
    }

    .command-frame-card.awaiting-click,
    .command-frame-card.awaiting-click:hover {
      border-color: #2a6df4 !important;
      background: #eaf1fe !important;
      box-shadow: 0 0 0 3px rgba(42, 109, 244, 0.18) !important;
    }

    .frame-direction,
    .view-toggle-buttons {
      color: #3f6d63;
      background: #e6f2dd;
      border-color: #b1d3b9;
    }

    /* Keep Step 2 simple while preserving distinct partition colours. */
    #apiPartitionSection {
      color: #203b35;
      background: #f3f8f1 !important;
      background-image: none !important;
      border-color: #b8cdc4 !important;
      box-shadow: none;
    }

    #apiPartitionSection .step-number,
    #apiPartitionSection .partition-btn.primary {
      color: #ffffff !important;
      background: #659287 !important;
      border-color: #659287 !important;
    }

    #apiPartitionSection .selected-frame-banner {
      color: #355c53 !important;
      background: #e6f2dd !important;
      border-color: #b1d3b9 !important;
    }

    #apiPartitionSection .selected-frame-banner span {
      color: #ffffff !important;
      background: #659287 !important;
      border-color: #659287 !important;
    }

    #apiPartitionSection .partition-workspace {
      background: #ffffff !important;
      border-color: #b8cdc4 !important;
    }

    #apiPartitionSection .partition-instruction {
      color: #355c53 !important;
      background: #f3f8f1 !important;
      border-color: #b8cdc4 !important;
    }

    #apiPartitionSection .partition-instruction i,
    #apiPartitionSection .section-title i,
    #apiPartitionSection .timeline-empty::before {
      color: #659287 !important;
    }

    #apiPartitionSection .api-input-wrap input:focus {
      border-color: #659287 !important;
      box-shadow: 0 0 0 3px rgba(101, 146, 135, 0.18);
    }

    #apiPartitionSection .partition-btn {
      color: #3f6d63 !important;
      background: #ffffff !important;
      border-color: #88bda4 !important;
    }

    #apiPartitionSection .partition-btn:hover {
      color: #203b35 !important;
      background: #e6f2dd !important;
      border-color: #659287 !important;
    }

    #apiPartitionSection .continue-btn {
      color: #ffffff !important;
      background: #3f6d63 !important;
      border-color: #3f6d63 !important;
    }

    #apiPartitionSection .demo-open-btn {
      color: #3f6d63 !important;
      background: #ffffff !important;
      border-color: #88bda4 !important;
    }

    #apiPartitionSection .demo-open-btn:hover {
      color: #203b35 !important;
      background: #e6f2dd !important;
      border-color: #659287 !important;
    }

    /* Required dropdown warning state */
    .select-with-icon.dropdown-error select,
    #apiPartitionSection .partition-name-band select.dropdown-error {
      color: #8f1d16 !important;
      background: #fff4f2 !important;
      border: 2px solid #d92d20 !important;
      box-shadow: 0 0 0 3px rgba(217, 45, 32, 0.1);
    }

    .select-with-icon.dropdown-error>i {
      color: #d92d20 !important;
    }

    .input-with-icon.input-error input {
      color: #8f1d16 !important;
      background: #fff4f2 !important;
      border: 2px solid #d92d20 !important;
      box-shadow: 0 0 0 3px rgba(217, 45, 32, 0.1);
    }

    .input-with-icon.input-error>i {
      color: #d92d20 !important;
    }

    .dropdown-warning {
      display: flex;
      align-items: center;
      gap: 6px;
      width: 100%;
      margin-top: 5px;
      color: #b42318;
      font-size: 0.72rem;
      font-weight: 750;
      line-height: 1.35;
    }

    .dropdown-warning[hidden] {
      display: none;
    }

    .partition-frame-label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-top: 0.55rem;
      padding: 0.35rem 0.75rem;
      border-radius: 999px;
      background: #eaf1fe;
      border: 1px solid #c9daf8;
      color: #1a4cba;
      font-size: 0.8rem;
      font-weight: 800;
    }

    .partition-name-band.missing-selection {
      outline: none;
    }

    .partition-name-band.missing-selection.show-warning {
      outline: 3px solid rgba(217, 45, 32, 0.18);
      outline-offset: -3px;
    }

    .partition-name-band .partition-dropdown-warning {
      display: flex;
      align-items: center;
      gap: 4px;
      margin-top: 5px;
      color: #b42318;
      font-size: 0.62rem;
      font-weight: 850;
    }

    .partition-ready-hint {
      display: none;
      align-items: center;
      gap: 10px;
      margin-top: 0.75rem;
      padding: 0.75rem 0.9rem;
      border: 2px solid #659287;
      border-radius: 12px;
      color: #203b35;
      background: #eaf5ec;
      font-size: 0.8rem;
      font-weight: 800;
      line-height: 1.4;
    }

    .partition-ready-hint.show {
      display: flex;
      animation: framePartitionReveal 0.3s ease;
    }

    .partition-ready-hint i {
      flex: 0 0 auto;
      color: #3f6d63;
      font-size: 1rem;
    }

    /* Clear visual partition blocks with code and text */
    .partition-visual {
      margin-top: 1rem;
      padding: 1rem;
      border: 1px solid #b8cdc4;
      border-radius: 14px;
      background: #f3f8f1;
    }

    .partition-visual-title {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 0.75rem;
      color: #203b35;
      font-size: 0.86rem;
      font-weight: 850;
    }

    .partition-visual-title i {
      color: #659287;
    }

    .partition-blocks {
      display: flex;
      align-items: stretch;
      gap: 8px;
      width: 100%;
      overflow-x: auto;
      padding: 2px 2px 8px;
    }

    .partition-visual-block {
      position: relative;
      flex: 1 0 145px;
      min-height: 104px;
      padding: 0.75rem;
      border: 2px solid var(--partition-color);
      border-radius: 11px;
      background: var(--partition-soft);
      color: var(--partition-text);
    }

    .partition-visual-block::after {
      content: "";
      position: absolute;
      top: 50%;
      right: -10px;
      width: 10px;
      border-top: 2px solid #91a9a0;
    }

    .partition-visual-block:last-child::after {
      display: none;
    }

    .partition-visual-block.missing {
      border-color: #d92d20;
      background: #fff4f2;
      color: #8f1d16;
    }

    .partition-visual-index {
      display: block;
      margin-bottom: 4px;
      font-size: 0.62rem;
      font-weight: 900;
      letter-spacing: 0.055em;
      text-transform: uppercase;
    }

    .partition-visual-code {
      display: block;
      margin-bottom: 6px;
      color: inherit;
      font:
        850 1rem Consolas,
        Monaco,
        monospace;
      letter-spacing: 0.06em;
      overflow-wrap: anywhere;
    }

    .partition-visual-name {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 0.76rem;
      font-weight: 850;
    }

    .partition-visual-range {
      display: block;
      margin-top: 4px;
      font-size: 0.65rem;
      opacity: 0.78;
    }

    .partition-visual-empty {
      color: #61756f;
      font-size: 0.8rem;
    }

    @media (max-width: 650px) {
      .partition-visual-block {
        flex-basis: 132px;
      }
    }

    .generate-validation-overlay {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 1300;
      padding: 20px;
      align-items: center;
      justify-content: center;
      background: rgba(28, 49, 43, 0.62);
      backdrop-filter: blur(4px);
    }

    .generate-validation-overlay.open {
      display: flex;
      animation: fadeIn 0.2s ease both;
    }

    .generate-validation-dialog {
      width: min(480px, 100%);
      overflow: hidden;
      border: 1px solid #b1d3b9;
      border-radius: 18px;
      background: #fff;
      box-shadow: 0 24px 70px rgba(28, 49, 43, 0.28);
      animation: modalEnter 0.25s cubic-bezier(0.2, 0.8, 0.2, 1) both;
    }

    .generate-validation-head {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      padding: 20px;
      color: #243c35;
      background: #e6f2dd;
      border-bottom: 1px solid #b1d3b9;
    }

    .generate-validation-icon {
      display: grid;
      flex: 0 0 42px;
      width: 42px;
      height: 42px;
      place-items: center;
      border-radius: 50%;
      color: #fff;
      background: #d33c3c;
      font-size: 1.05rem;
    }

    .generate-validation-head h3 {
      margin: 0 0 5px;
      font-size: 1.12rem;
      color: #243c35;
    }

    .generate-validation-head p {
      margin: 0;
      color: #526b63;
      font-size: 0.86rem;
      line-height: 1.5;
    }

    /* Saved JSON confirmation popup */
    .json-save-overlay {
      position: fixed;
      inset: 0;
      z-index: 1600;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: rgba(28, 49, 43, 0.65);
      backdrop-filter: blur(4px);
    }

    .json-save-overlay.open {
      display: flex;
      animation: fadeIn 0.2s ease both;
    }

    .json-save-dialog {
      width: min(1180px, 100%);
      max-height: min(92vh, 920px);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      border: 1px solid #b1d3b9;
      border-radius: 18px;
      background: #fff;
      box-shadow: 0 24px 70px rgba(28, 49, 43, 0.3);
      animation: modalEnter 0.25s cubic-bezier(0.2, 0.8, 0.2, 1) both;
    }

    .json-save-head {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 18px 20px;
      color: #243c35;
      background: #e6f2dd;
      border-bottom: 1px solid #b1d3b9;
    }

    .json-save-head>i {
      display: grid;
      flex: 0 0 42px;
      width: 42px;
      height: 42px;
      place-items: center;
      border-radius: 50%;
      color: #fff;
      background: #2f7d61;
    }

    .json-save-head h3 {
      margin: 0 0 3px;
      font-size: 1.1rem;
    }

    .json-save-head p {
      margin: 0;
      color: #526b63;
      font-size: 0.85rem;
    }

    .json-save-close {
      width: 38px;
      height: 38px;
      margin-left: auto;
      border: 0;
      border-radius: 10px;
      color: #3f5d53;
      background: rgba(255, 255, 255, 0.8);
      cursor: pointer;
    }

    .json-save-body {
      min-height: 0;
      padding: 18px 20px 24px;
      overflow: auto;
      background: #f7f4f1;
    }

    .json-save-code[hidden] {
      display: none !important;
    }

    .configuration-data-preview {
      display: grid;
      gap: 18px;
      color: #24273d;
    }

    .preview-summary-card {
      overflow: hidden;
      border: 1px solid #d8d5d0;
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 8px 24px rgba(31, 35, 55, 0.07);
    }

    .preview-summary-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      padding: 15px 17px;
      color: #fff;
      background: #292d49;
    }

    .preview-summary-head h4,
    .preview-frame-section-title h4 {
      margin: 0;
      font-size: 0.88rem;
      font-weight: 800;
      letter-spacing: 0.045em;
      text-transform: uppercase;
    }

    .preview-summary-head p {
      margin: 3px 0 0;
      color: #d9dced;
      font-size: 0.72rem;
    }

    .preview-count-badges {
      display: flex;
      flex-wrap: wrap;
      justify-content: flex-end;
      gap: 7px;
    }

    .preview-count-badge {
      padding: 5px 9px;
      border: 1px solid rgba(255, 255, 255, 0.28);
      border-radius: 999px;
      color: #fff;
      background: rgba(255, 255, 255, 0.09);
      font-size: 0.63rem;
      font-weight: 800;
      letter-spacing: 0.035em;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .preview-product-grid {
      display: grid;
      grid-template-columns: repeat(5, minmax(0, 1fr));
      gap: 9px;
      padding: 13px;
      background: #fbfaf9;
    }

    .preview-product-field {
      min-width: 0;
      padding: 10px 11px;
      border: 1px solid #ded9d3;
      border-radius: 8px;
      background: #fff;
    }

    .preview-product-field.wide {
      grid-column: span 2;
    }

    .preview-product-label {
      display: block;
      margin-bottom: 5px;
      color: #bf6f30;
      font-size: 0.62rem;
      font-weight: 800;
      letter-spacing: 0.045em;
      text-transform: uppercase;
    }

    .preview-product-value {
      display: block;
      overflow-wrap: anywhere;
      color: #25283e;
      font-size: 0.78rem;
      font-weight: 750;
    }

    .preview-frame-section {
      overflow: hidden;
      border: 1px solid #d9d3cd;
      border-radius: 10px;
      background: #fff;
    }

    .preview-frame-section-title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 12px 14px;
      border-bottom: 1px solid #d9d3cd;
      background: #eeeae6;
    }

    .preview-frame-section-title span {
      color: #606375;
      font-size: 0.68rem;
      font-weight: 750;
    }

    .preview-frame-list {
      display: grid;
      gap: 14px;
      padding: 11px;
    }

    .preview-frame-card {
      overflow: hidden;
      border: 1px solid #d9cabb;
      border-radius: 8px;
      background: #fff;
    }

    .preview-frame-card-head,
    .preview-row-caption {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding: 9px 11px;
      color: #34374d;
      background: #faf7f4;
      font-size: 0.72rem;
      font-weight: 800;
    }

    .preview-frame-code {
      overflow-wrap: anywhere;
      color: #7f4c26;
      font-family: Consolas, Monaco, "Courier New", monospace;
    }

    .preview-row-caption {
      border-top: 1px solid #e3d8ce;
      border-bottom: 1px solid #e3d8ce;
      color: #5f634f;
      background: #f2f4e9;
      text-transform: uppercase;
      letter-spacing: 0.035em;
    }

    .preview-field-table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
      font-size: 0.7rem;
    }

    .preview-field-table th,
    .preview-field-table td {
      padding: 7px 10px;
      border-bottom: 1px solid #e8ddd3;
      text-align: left;
      vertical-align: top;
      overflow-wrap: anywhere;
    }

    .preview-field-table tr:last-child th,
    .preview-field-table tr:last-child td {
      border-bottom: 0;
    }

    .preview-field-table tr:nth-child(even) {
      background: #fcfaf8;
    }

    .preview-field-table th {
      width: 34%;
      color: #565b72;
      font-weight: 750;
    }

    .preview-field-table td {
      color: #24273d;
      font-weight: 650;
    }

    .preview-empty {
      padding: 22px;
      color: #747789;
      text-align: center;
      font-size: 0.78rem;
    }

    @media (max-width: 900px) {
      .preview-product-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }
    }

    @media (max-width: 640px) {
      .json-save-overlay {
        padding: 8px;
      }

      .json-save-dialog {
        max-height: 96vh;
        border-radius: 12px;
      }

      .json-save-head,
      .json-save-body,
      .json-save-actions {
        padding-left: 12px;
        padding-right: 12px;
      }

      .preview-summary-head {
        align-items: flex-start;
        flex-direction: column;
      }

      .preview-count-badges {
        justify-content: flex-start;
      }

      .preview-product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .preview-product-field.wide {
        grid-column: span 2;
      }

      .preview-field-table th {
        width: 42%;
      }
    }

    .json-save-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 10px;
      padding: 14px 20px;
      border-top: 1px solid #d9e6de;
      background: #fff;
    }

    .json-copy-status {
      margin-right: auto;
      color: #2f7d61;
      font-size: 0.84rem;
      font-weight: 700;
    }

    .json-save-actions button {
      min-height: 40px;
      padding: 0 16px;
      border: 1px solid #88bda4;
      border-radius: 10px;
      color: #29483e;
      background: #e6f2dd;
      font-weight: 700;
      cursor: pointer;
    }

    .json-save-actions .json-done-btn {
      color: #fff;
      background: #4f8072;
      border-color: #4f8072;
    }

    @media (max-width: 640px) {
      .json-save-actions {
        flex-wrap: wrap;
      }

      .json-copy-status {
        width: 100%;
      }

      .json-save-actions button {
        flex: 1 1 130px;
      }
    }

    .generate-validation-body {
      padding: 18px 20px 8px;
    }

    .generate-missing-list {
      display: grid;
      gap: 9px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .generate-missing-item {
      display: flex;
      gap: 10px;
      align-items: center;
      min-height: 44px;
      padding: 10px 12px;
      border: 1px solid #f0b4b4;
      border-radius: 10px;
      color: #872525;
      background: #fff5f5;
      font-weight: 750;
    }

    .generate-missing-item i {
      color: #d33c3c;
    }

    .generate-validation-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      padding: 14px 20px 20px;
    }

    .generate-validation-actions button {
      min-height: 42px;
      padding: 9px 17px;
      border: 0;
      border-radius: 10px;
      color: #fff;
      background: #659287;
      font-family: inherit;
      font-size: 0.86rem;
      font-weight: 800;
      cursor: pointer;
    }

    .generate-validation-actions button:hover,
    .generate-validation-actions button:focus-visible {
      background: #4f796f;
      outline: 3px solid rgba(136, 189, 164, 0.35);
      outline-offset: 2px;
    }

    @media (prefers-reduced-motion: reduce) {

      .generate-validation-overlay.open,
      .generate-validation-dialog {
        animation: none;
      }
    }

    /* Progressive guidance (hints before red warnings) */
    .field-setup-hint {
      display: flex;
      align-items: center;
      gap: 5px;
      margin-top: 5px;
      color: #5a7394;
      font-size: 0.7rem;
      font-weight: 650;
      line-height: 1.35;
    }

    .field-setup-hint i {
      color: #2a6df4;
      font-size: 0.72rem;
    }

    .field-setup-hint[hidden],
    .dropdown-warning[hidden] {
      display: none !important;
    }

    .param-item.has-error .field-setup-hint {
      display: none;
    }

    .frames-next-banner {
      display: none;
      align-items: center;
      gap: 12px;
      margin: 0 0 1rem;
      padding: 0.85rem 1rem;
      border: 2px solid #2a6df4;
      border-radius: 14px;
      background: linear-gradient(135deg, #eaf1fe 0%, #f5f9ff 100%);
      color: #153a7a;
      font-size: 0.86rem;
      font-weight: 750;
      line-height: 1.4;
      animation: framePartitionReveal 0.35s ease;
    }

    .frames-next-banner.show {
      display: flex;
    }

    .frames-next-banner i {
      flex: 0 0 auto;
      font-size: 1.15rem;
      color: #2a6df4;
    }

    .frames-next-banner strong {
      color: #0b1a33;
    }

    .command-frame-card.awaiting-click {
      border-color: #2a6df4;
      box-shadow: 0 0 0 3px rgba(42, 109, 244, 0.18);
      animation: cardClickPulse 1.4s ease-in-out infinite;
    }

    .frame-config-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      width: 100%;
      margin-top: 0.55rem;
      min-height: 34px;
      padding: 0.4rem 0.65rem;
      border: 1px solid #2a6df4;
      border-radius: 10px;
      background: #2a6df4;
      color: #fff;
      font-family: inherit;
      font-size: 0.74rem;
      font-weight: 800;
      cursor: pointer;
      position: relative;
      z-index: 2;
    }

    .frame-config-btn:hover {
      background: #1a4cba;
      border-color: #1a4cba;
    }

    .command-frame-card.partition-complete .frame-config-btn {
      background: #1b8a55;
      border-color: #1b8a55;
    }

    .command-frame-card.partition-complete .frame-config-btn:hover {
      background: #147248;
      border-color: #147248;
    }

    @keyframes cardClickPulse {

      0%,
      100% {
        transform: translateY(0);
        box-shadow: 0 0 0 3px rgba(42, 109, 244, 0.16);
      }

      50% {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(42, 109, 244, 0.22);
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .command-frame-card.awaiting-click {
        animation: none;
      }
    }

    /* Partition howto strip */
    .partition-howto {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 8px;
      margin: 0.75rem 0 0.35rem;
    }

    .partition-howto-step {
      display: flex;
      gap: 8px;
      align-items: flex-start;
      padding: 0.65rem 0.7rem;
      border: 1px solid #d5e3f5;
      border-radius: 12px;
      background: #f7faff;
      color: #355070;
      font-size: 0.72rem;
      font-weight: 700;
      line-height: 1.35;
    }

    .partition-howto-step .howto-num {
      display: grid;
      place-items: center;
      flex: 0 0 22px;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: #2a6df4;
      color: #fff;
      font-size: 0.68rem;
      font-weight: 900;
    }

    .partition-howto-step i {
      color: #2a6df4;
      margin-right: 2px;
    }

    .partition-howto-step strong {
      display: block;
      color: #0b1a33;
      margin-bottom: 2px;
      font-size: 0.74rem;
    }

    .partition-howto-step .howto-description {
      display: block;
    }

    .cut-target {
      display: grid;
      place-items: start center;
    }

    .cut-target .cut-chip {
      position: absolute;
      top: 0;
      left: 50%;
      z-index: 2;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 3px;
      min-width: 22px;
      height: 22px;
      padding: 0 5px;
      transform: translateX(-50%);
      border: 1px solid #b8c9df;
      border-radius: 999px;
      background: #fff;
      color: #47729f;
      font-size: 0.62rem;
      font-weight: 850;
      box-shadow: 0 2px 6px rgba(20, 45, 80, 0.08);
      pointer-events: none;
      transition: none !important;
    }

    .cut-target::after {
      content: none !important;
    }

    .cut-target:hover .cut-chip {
      border-color: #e34b4b;
      color: #e34b4b;
      background: #fff1f1;
      transform: translateX(-50%);
    }

    .cut-target.active .cut-chip {
      min-width: 26px;
      height: 26px;
      top: -3px;
      border: 2px solid #e34b4b;
      background: #e34b4b;
      color: #fff;
      box-shadow: 0 3px 9px rgba(227, 75, 75, 0.28);
    }

    .cut-target.active.partition-colored .cut-chip {
      border-color: var(--partition-color);
      background: var(--partition-color);
    }

    /* Stable cut controls: hover changes only color/opacity, never geometry. */
    .cut-target,
    .cut-target .cut-chip {
      will-change: auto;
    }

    .cut-target .cut-hover-tip {
      position: absolute;
      z-index: 12;
      top: -38px;
      left: 50%;
      width: max-content;
      max-width: 230px;
      padding: 0.42rem 0.62rem;
      transform: translate(-50%, 4px);
      border: 1px solid #cad7e7;
      border-radius: 9px;
      background: #172b43;
      color: #fff;
      box-shadow: 0 8px 20px rgba(15, 35, 58, 0.18);
      font-size: 0.66rem;
      font-weight: 750;
      line-height: 1.25;
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      transition: opacity 150ms ease, transform 150ms ease, visibility 150ms ease;
    }

    .cut-target:hover .cut-hover-tip,
    .cut-target:focus-visible .cut-hover-tip {
      opacity: 1;
      visibility: visible;
      transform: translate(-50%, 0);
    }

    .cut-target .cut-nudges {
      position: absolute;
      z-index: 11;
      top: -2px;
      left: 50%;
      display: flex;
      align-items: center;
      gap: 34px;
      transform: translateX(-50%);
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      transition: opacity 150ms ease, visibility 150ms ease;
    }

    .cut-target.active:hover .cut-nudges,
    .cut-target.active:focus-within .cut-nudges {
      opacity: 1;
      visibility: visible;
      pointer-events: auto;
    }

    .cut-nudge {
      display: inline-grid;
      place-items: center;
      width: 25px;
      height: 25px;
      border: 1px solid #b9c9dc;
      border-radius: 8px;
      background: #fff;
      color: #28496d;
      box-shadow: 0 5px 14px rgba(18, 47, 78, 0.16);
      font-size: 0.65rem;
      transition: color 140ms ease, background 140ms ease, border-color 140ms ease, transform 140ms ease;
    }

    .cut-nudge:not(.disabled):hover {
      border-color: #2a6df4;
      background: #eef5ff;
      color: #1746a2;
      transform: translateY(-1px);
    }

    .cut-nudge.disabled {
      opacity: 0.38;
      cursor: not-allowed;
    }

    @media (prefers-reduced-motion: reduce) {
      .cut-target .cut-hover-tip,
      .cut-target .cut-nudges,
      .cut-nudge {
        transition: none;
      }
    }

    .cut-coach {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 0.55rem;
    }

    .cut-coach-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 0.35rem 0.65rem;
      border-radius: 999px;
      border: 1px dashed #9bb4d4;
      background: #fff;
      color: #3d5778;
      font-size: 0.68rem;
      font-weight: 750;
    }

    .cut-coach-pill i {
      color: #2a6df4;
    }

    .partition-name-band .partition-dropdown-hint {
      display: flex;
      align-items: center;
      gap: 4px;
      margin-top: 5px;
      color: #5a7394;
      font-size: 0.62rem;
      font-weight: 750;
    }

    .partition-name-band .partition-dropdown-hint i {
      color: #2a6df4;
    }

    .partition-name-band.missing-selection.show-warning {
      outline: 3px solid rgba(217, 45, 32, 0.18);
      outline-offset: -3px;
    }

    .partition-name-band:not(.show-warning) .partition-dropdown-warning {
      display: none;
    }

    .partition-name-band.show-warning .partition-dropdown-hint {
      display: none;
    }

    /* Redesigned partition block preview */
    .partition-visual {
      margin-top: 1rem;
      padding: 1rem 1.05rem 1.1rem;
      border: 1px solid #c5d7eb;
      border-radius: 16px;
      background:
        linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
    }

    .partition-visual-title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 0.85rem;
      color: #17345f;
      font-size: 0.88rem;
      font-weight: 850;
    }

    .partition-visual-title>span {
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .partition-visual-title i {
      color: #2a6df4;
    }

    .partition-visual-legend {
      color: #5d7596;
      font-size: 0.68rem;
      font-weight: 700;
    }

    .partition-config-open-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      width: 100%;
      margin-top: 0.45rem;
      min-height: 30px;
      padding: 0.35rem 0.45rem;
      border: 1px solid var(--partition-color, #2a6df4);
      border-radius: 8px;
      background: #fff;
      color: var(--partition-text, #1746a2);
      font-family: inherit;
      font-size: 0.64rem;
      font-weight: 850;
      cursor: pointer;
      transition:
        background-color 0.18s,
        transform 0.18s,
        box-shadow 0.18s;
    }

    .partition-config-open-btn:hover:not(:disabled) {
      background: var(--partition-soft, #eef5ff);
      transform: translateY(-1px);
    }

    .partition-config-open-btn:disabled {
      opacity: 0.55;
      cursor: not-allowed;
    }

    .partition-config-open-btn.config-ready {
      border-color: #1b8a55;
      color: #167647;
      background: #f3fbf6;
    }

    .partition-config-open-btn.config-missing {
      border-color: #d92d20;
      color: #b42318;
      background: #fff4f2;
    }

    /* Byte-box style partition preview */
    .partition-blocks {
      display: flex;
      align-items: stretch;
      gap: 14px;
      width: 100%;
      overflow-x: auto;
      padding: 4px 2px 10px;
    }

    .partition-visual-block {
      position: relative;
      flex: 0 0 auto;
      min-width: 132px;
      margin-right: 0;
      padding: 0.7rem 0.7rem 0.75rem;
      border: 2px solid var(--partition-color);
      border-radius: 14px;
      background: #fff;
      color: var(--partition-text);
      box-shadow: 0 8px 18px rgba(15, 40, 80, 0.06);
    }

    .partition-visual-block::after {
      content: "";
      position: absolute;
      top: 50%;
      right: -12px;
      width: 10px;
      height: 10px;
      border-top: 2px solid #9bb0c9;
      border-right: 2px solid #9bb0c9;
      transform: translateY(-50%) rotate(45deg);
    }

    .partition-visual-block:last-child::after {
      display: none;
    }

    .partition-visual-block.missing {
      border-style: dashed;
      border-color: #d92d20;
      background: #fff8f7;
      color: #8f1d16;
    }

    .partition-visual-index {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      margin-bottom: 8px;
      padding: 0.15rem 0.45rem;
      border-radius: 999px;
      background: var(--partition-soft);
      font-size: 0.62rem;
      font-weight: 900;
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }

    .partition-byte-boxes {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-bottom: 8px;
    }

    .partition-byte-box {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-width: 46px;
      min-height: 52px;
      padding: 0.3rem 0.35rem;
      border: 1.5px solid var(--partition-color);
      border-radius: 10px;
      background: var(--partition-soft);
      box-shadow: inset 0 3px 0 var(--partition-color);
    }

    .partition-byte-box .byte-box-index {
      color: var(--partition-text);
      opacity: 0.7;
      font-size: 0.55rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    .partition-byte-box .byte-box-value {
      margin-top: 2px;
      color: var(--partition-text);
      font:
        850 0.95rem Consolas,
        Monaco,
        monospace;
      letter-spacing: 0.05em;
    }

    .partition-visual-name {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 0.76rem;
      font-weight: 850;
    }

    .partition-visual-range {
      display: block;
      margin-top: 5px;
      opacity: 0.72;
      font-size: 0.64rem;
      font-weight: 700;
    }

    .partition-visual-empty {
      display: flex;
      align-items: center;
      gap: 8px;
      width: 100%;
      padding: 1rem;
      border: 1px dashed #b7c9df;
      border-radius: 12px;
      background: #fff;
      color: #61756f;
      font-size: 0.8rem;
      font-weight: 650;
    }

    .partition-visual-block .partition-config-open-btn {
      margin-top: 0.55rem;
    }

    #apiPartitionSection .partition-byte-box {
      border-color: var(--partition-color);
      background: var(--partition-soft);
    }

    #apiPartitionSection .partition-config-open-btn {
      border-color: var(--partition-color);
      color: var(--partition-text);
    }

    /* UI Type visual picker */
    .ui-type-picker {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 8px;
      max-height: 260px;
      overflow: auto;
      padding: 2px;
    }

    .ui-type-option {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 0.55rem 0.6rem;
      border: 1px solid #d7e2f0;
      border-radius: 12px;
      background: #fbfcff;
      color: #243b57;
      text-align: left;
      cursor: pointer;
      transition:
        border-color 0.18s,
        background-color 0.18s,
        box-shadow 0.18s;
    }

    .ui-type-option:hover {
      border-color: #8fb0ea;
      background: #f3f7ff;
    }

    .ui-type-option.active {
      border-color: #2a6df4;
      background: #eaf1fe;
      box-shadow: 0 0 0 2px rgba(42, 109, 244, 0.15);
    }

    .ui-type-option .ui-preview {
      flex: 0 0 42px;
      width: 42px;
      height: 32px;
      display: grid;
      place-items: center;
      border-radius: 8px;
      background: #fff;
      border: 1px solid #dce6f4;
      color: #2a6df4;
      font-size: 0.9rem;
    }

    .ui-type-option .ui-preview-label {
      display: flex;
      flex-direction: column;
      gap: 2px;
      min-width: 0;
    }

    .ui-type-option .ui-preview-label strong {
      font-size: 0.72rem;
      font-weight: 800;
      line-height: 1.2;
    }

    .ui-type-option .ui-preview-label small {
      color: #6a7f9b;
      font-size: 0.62rem;
      font-weight: 650;
    }

    .ui-preview-switch {
      position: relative;
      width: 28px;
      height: 16px;
      border-radius: 999px;
      background: #2a6df4;
    }

    .ui-preview-switch::after {
      content: "";
      position: absolute;
      top: 2px;
      right: 2px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #fff;
    }

    .ui-preview-slider {
      position: relative;
      width: 28px;
      height: 4px;
      border-radius: 999px;
      background: #c9d7ea;
    }

    .ui-preview-slider::after {
      content: "";
      position: absolute;
      top: -4px;
      left: 14px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #2a6df4;
    }

    .ui-preview-btn {
      width: 26px;
      height: 14px;
      border-radius: 5px;
      background: #2a6df4;
    }

    .ui-preview-input {
      width: 28px;
      height: 12px;
      border-radius: 4px;
      border: 1px solid #b8c9df;
      background: #fff;
    }

    .ui-preview-toast {
      width: 28px;
      height: 12px;
      border-radius: 6px;
      background: #243b57;
    }

    .ui-preview-circle {
      width: 16px;
      height: 16px;
      border-radius: 50%;
      border: 3px solid transparent;
      border-top-color: #ef4444;
      border-right-color: #f59e0b;
      border-bottom-color: #22c55e;
      border-left-color: #3b82f6;
    }

    .ui-preview-dropdown {
      width: 28px;
      height: 14px;
      border-radius: 4px;
      border: 1px solid #b8c9df;
      background: linear-gradient(#fff 60%, #e8eef8 60%);
    }

    #fUiType {
      position: absolute;
      opacity: 0;
      pointer-events: none;
      width: 1px;
      height: 1px;
    }

    .form-group.ui-type-group {
      position: relative;
    }

    @media (max-width: 650px) {
      .partition-howto {
        grid-template-columns: 1fr;
      }

      .ui-type-picker {
        grid-template-columns: 1fr;
      }

      .partition-visual-block {
        flex-basis: 140px;
      }
    }
    .frame-type-row {
      width: 100%;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      flex-wrap: wrap;
    }

    .frame-type-switcher {
      display: inline-flex;
      gap: 0.55rem;
      flex-wrap: wrap;
      padding: 0.4rem;
      border: 1px solid #dbe6f5;
      border-radius: 14px;
      background: #f8fbff;
    }

    .frame-type-btn {
      border: 1px solid #cbd8e8;
      border-radius: 10px;
      padding: 0.72rem 1rem;
      background: #fff;
      color: #405168;
      font-weight: 700;
      cursor: pointer;
      transition: 0.2s ease;
    }

    .frame-type-btn:hover {
      border-color: #659287;
      color: #365e54;
    }

    .frame-type-btn.active {
      color: #fff;
      background: #659287;
      border-color: #659287;
      box-shadow: 0 5px 14px rgba(101, 146, 135, 0.24);
    }

    .frame-type-btn .type-count {
      display: inline-flex;
      min-width: 1.35rem;
      height: 1.35rem;
      align-items: center;
      justify-content: center;
      margin-left: 0.3rem;
      padding: 0 0.3rem;
      border-radius: 999px;
      background: rgba(101, 146, 135, 0.12);
      font-size: 0.75rem;
    }

    .frame-type-btn.active .type-count {
      background: rgba(255, 255, 255, 0.22);
    }

    /* =========================================================
       Modern form layout
       Visual-only overrides: existing IDs, events and payloads stay intact.
       ========================================================= */
    :root {
      --ui-primary: #376f64;
      --ui-primary-strong: #28584f;
      --ui-primary-soft: #edf7f3;
      --ui-accent: #5f8f84;
      --ui-ink: #172b26;
      --ui-muted: #667972;
      --ui-line: #dce7e2;
      --ui-surface: #ffffff;
      --ui-canvas: #f4f8f6;
      --ui-danger: #c9362b;
      --ui-danger-soft: #fff2f1;
      --ui-warning: #b7791f;
      --ui-success: #168052;
      --ui-radius-lg: 22px;
      --ui-radius-md: 14px;
      --ui-shadow: 0 18px 50px rgba(29, 65, 56, 0.1);
      --ui-control-shadow: 0 1px 2px rgba(29, 65, 56, 0.04);
    }

    html {
      color-scheme: light;
      scroll-behavior: smooth;
    }

    body {
      min-height: 100vh;
      padding: clamp(12px, 2.5vw, 34px);
      background:
        radial-gradient(circle at 5% 0%, rgba(136, 189, 164, 0.2), transparent 28rem),
        linear-gradient(180deg, #f7faf8 0%, var(--ui-canvas) 100%);
      color: var(--ui-ink);
      line-height: 1.5;
    }

    .card {
      width: min(100%, 1280px);
      max-width: 1280px;
      padding: clamp(18px, 3vw, 38px);
      overflow: hidden;
      border: 1px solid rgba(101, 146, 135, 0.2);
      border-radius: 26px;
      background: rgba(255, 255, 255, 0.96);
      box-shadow: var(--ui-shadow);
    }

    .header-title {
      align-items: flex-start;
      gap: 20px;
      margin-bottom: 22px;
      padding-bottom: 22px;
      border-bottom: 1px solid var(--ui-line);
    }

    .page-heading-copy {
      min-width: 0;
    }

    .page-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      margin-bottom: 7px;
      color: var(--ui-primary);
      font-size: 0.72rem;
      font-weight: 850;
      letter-spacing: 0.11em;
      text-transform: uppercase;
    }

    .header-title h1 {
      margin: 0;
      color: var(--ui-ink);
      font-size: clamp(1.45rem, 3vw, 2rem);
      font-weight: 780;
      letter-spacing: -0.035em;
    }

    .header-subtitle {
      max-width: 680px;
      margin-top: 7px;
      color: var(--ui-muted);
      font-size: 0.91rem;
    }

    .title-icon {
      display: grid;
      place-items: center;
      width: 42px;
      height: 42px;
      border-radius: 13px;
      background: var(--ui-primary-soft);
      color: var(--ui-primary);
      box-shadow: inset 0 0 0 1px rgba(55, 111, 100, 0.13);
    }

    .brand-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      max-width: 100%;
      padding: 9px 14px;
      border: 1px solid #cfe1d9;
      border-radius: 999px;
      background: var(--ui-primary-soft);
      color: var(--ui-primary-strong);
      font-size: 0.82rem;
      box-shadow: none;
    }

    .product-meta {
      display: grid;
      grid-template-columns: repeat(5, minmax(130px, 1fr));
      gap: 10px;
      margin-bottom: 24px;
      padding: 10px;
      border: 1px solid var(--ui-line);
      border-radius: var(--ui-radius-lg);
      background: #f9fbfa;
    }

    .product-meta .meta-item {
      display: grid;
      grid-template-columns: 34px minmax(0, 1fr);
      column-gap: 80px;
      min-width: 0;
      padding: 11
      px;
      border-radius: 14px;
      background: var(--ui-surface);
    }

    .meta-item .meta-icon {
      grid-row: 1 / 3;
      display: grid;
      place-items: center;
      width: 34px;
      height: 34px;
      align-self: center;
      border-radius: 10px;
      background: var(--ui-primary-soft);
      color: var(--ui-primary);
    }

    .meta-item .label {
      color: var(--ui-muted);
      font-size: 0.64rem;
      font-weight: 800;
      letter-spacing: 0.08em;
    }

    .meta-item .value,
    .meta-item .value.light {
      min-width: 0;
      overflow: hidden;
      color: var(--ui-ink);
      font-size: 0.86rem;
      font-weight: 750;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .command-response-error-flow {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr) auto minmax(0, 1fr);
      align-items: center;
      gap: 12px;
      margin: 0 0 20px;
      padding: 10px;
      border: 1px solid var(--ui-line);
      border-radius: var(--ui-radius-lg);
      background: #f7faf8;
    }

    .flow-stage {
      min-width: 0;
      min-height: 64px;
      padding: 10px 14px;
      border: 1px solid transparent;
      border-radius: 15px;
      background: transparent;
      color: var(--ui-muted);
      box-shadow: none;
    }

    .flow-stage:hover:not(:disabled) {
      border-color: #c8dbd3;
      background: #fff;
      color: var(--ui-primary-strong);
      transform: none;
    }

    .flow-stage.active {
      border-color: var(--ui-primary);
      background: var(--ui-primary);
      color: #fff;
      box-shadow: 0 9px 22px rgba(55, 111, 100, 0.22);
    }

    .flow-stage.completed:not(.active),
    .flow-stage.filled:not(.active) {
      border-color: #a8d6c0;
      background: #eef9f3;
      color: var(--ui-success);
    }

    .flow-arrow {
      color: #9aacA5;
      font-size: 0.78rem;
    }

    .section,
    #commandFramesSection,
    #apiPartitionSection {
      margin-top: 20px !important;
      padding: clamp(16px, 2.3vw, 26px) !important;
      border: 1px solid var(--ui-line) !important;
      border-radius: var(--ui-radius-lg) !important;
      background: var(--ui-surface) !important;
      box-shadow: 0 7px 24px rgba(29, 65, 56, 0.055) !important;
    }

    .step-heading {
      gap: 12px;
      margin-bottom: 18px;
      padding-bottom: 14px;
      border-bottom: 1px solid var(--ui-line);
    }

    .step-number {
      min-width: auto;
      padding: 6px 10px;
      border-radius: 999px;
      background: var(--ui-primary);
      color: #fff;
      font-size: 0.66rem;
      font-weight: 850;
      letter-spacing: 0.08em;
      box-shadow: none;
    }

    .step-number::after {
      display: none;
    }

    .section-title,
    #apiPartitionSection .section-title {
      color: var(--ui-ink) !important;
      font-size: 1.03rem;
      font-weight: 790;
    }

    .section-title i,
    #apiPartitionSection .section-title i {
      color: var(--ui-primary) !important;
    }

    .section-guidance:not(:empty) {
      display: flex;
      align-items: center;
      gap: 8px;
      margin: -4px 0 16px;
      color: var(--ui-muted);
      font-size: 0.79rem;
    }

    .required-note {
      margin-left: auto;
      color: var(--ui-muted);
      font-size: 0.72rem;
      font-weight: 700;
    }

    .required-dot {
      color: var(--ui-danger);
      font-size: 0.92em;
      font-weight: 900;
    }

    .required-badge {
      display: inline-flex;
      margin-left: 6px;
      padding: 2px 6px;
      border-radius: 999px;
      background: var(--ui-danger-soft);
      color: var(--ui-danger);
      font-size: 0.58rem;
      font-weight: 850;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      vertical-align: middle;
    }

    .param-grid {
      gap: 16px;
    }

    .param-row {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
      align-items: start;
    }

    .param-item {
      min-width: 0;
      padding: 0;
      border: 0;
      background: transparent;
    }

    .control-label,
    #apiPartitionSection .control-label,
    .byte-modal .form-group label {
      display: flex;
      align-items: center;
      gap: 7px;
      margin-bottom: 7px;
      color: #344b44 !important;
      font-size: 0.78rem !important;
      font-weight: 760 !important;
    }

    .control-label i {
      width: 15px;
      color: var(--ui-primary) !important;
      text-align: center;
    }

    .input-with-icon,
    .select-with-icon {
      position: relative;
    }

    .input-with-icon input,
    .select-with-icon select,
    #apiPartitionSection .api-input-wrap input,
    .frame-setting select,
    .byte-modal .form-group input,
    .byte-modal .form-group select {
      width: 100% !important;
      min-width: 0 !important;
      min-height: 46px;
      padding: 10px 13px 10px 42px !important;
      border: 1px solid #cfdcd6 !important;
      border-radius: 12px !important;
      background: #fff !important;
      color: var(--ui-ink) !important;
      font: inherit;
      font-size: 0.88rem !important;
      box-shadow: var(--ui-control-shadow) !important;
      transition: border-color 150ms ease, box-shadow 150ms ease, background-color 150ms ease;
    }

    .input-with-icon > i,
    .select-with-icon > i {
      left: 14px;
      color: #789188 !important;
    }

    .input-with-icon input::placeholder,
    #apiPartitionSection .api-input-wrap input::placeholder {
      color: #97a8a1;
      opacity: 1;
    }

    .input-with-icon input:hover,
    .select-with-icon select:hover,
    #apiPartitionSection .api-input-wrap input:hover {
      border-color: #a9c0b7 !important;
    }

    .input-with-icon input:focus,
    .select-with-icon select:focus,
    #apiPartitionSection .api-input-wrap input:focus,
    .frame-setting select:focus,
    .byte-modal .form-group input:focus,
    .byte-modal .form-group select:focus {
      border-color: var(--ui-primary) !important;
      outline: 0 !important;
      box-shadow: 0 0 0 4px rgba(55, 111, 100, 0.13) !important;
    }

    .field-setup-hint {
      margin-top: 6px;
      color: var(--ui-muted);
      font-size: 0.69rem;
    }

    .field-setup-hint i {
      color: var(--ui-accent);
    }

    .dropdown-warning,
    .api-error:not(:empty),
    .command-error:not(:empty) {
      display: flex;
      align-items: flex-start;
      gap: 7px;
      margin-top: 7px;
      padding: 8px 10px;
      border: 1px solid #f0c0bc;
      border-radius: 9px;
      background: var(--ui-danger-soft);
      color: #9f261e;
      font-size: 0.72rem;
      font-weight: 700;
      line-height: 1.4;
    }

    .dropdown-warning[hidden] {
      display: none !important;
    }

    .input-with-icon.input-error input,
    .select-with-icon.dropdown-error select,
    .command-entry input.command-byte-input.invalid {
      border-color: var(--ui-danger) !important;
      background: #fffafa !important;
      box-shadow: 0 0 0 3px rgba(201, 54, 43, 0.11) !important;
    }

    .linked-api-panel {
      border: 1px dashed #b9cec5;
      border-radius: 14px;
      background: #f8fbf9;
    }

    .partition-howto {
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 10px;
    }

    .partition-howto-step {
      min-height: 74px;
      border-color: var(--ui-line);
      background: #f9fbfa;
    }

    .partition-howto-step .howto-num {
      background: var(--ui-primary);
    }

    .api-entry-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 12px;
      align-items: end;
      margin-top: 16px;
    }

    .api-count {
      min-height: 46px;
      display: inline-flex;
      align-items: center;
      padding: 0 14px;
      border: 1px solid var(--ui-line);
      border-radius: 12px;
      background: #f8fbf9;
      color: var(--ui-muted);
      white-space: nowrap;
    }

    .partition-workspace,
    #apiPartitionSection .partition-workspace {
      margin-top: 16px;
      border: 1px solid var(--ui-line) !important;
      border-radius: 16px !important;
      background: #fbfdfc !important;
      box-shadow: none !important;
    }

    .partition-toolbar,
    .partition-confirm-row,
    .action-row {
      gap: 10px;
    }

    button,
    .generate-btn,
    .continue-btn,
    .save-btn,
    .partition-btn,
    .add-frame-btn,
    .demo-open-btn,
    .back-btn {
      min-height: 42px;
      border-radius: 11px !important;
      font-size: 0.78rem;
      font-weight: 780;
      letter-spacing: 0;
      transition: transform 140ms ease, box-shadow 140ms ease, background-color 140ms ease, border-color 140ms ease;
    }

    .generate-btn,
    .continue-btn,
    .save-btn,
    .partition-btn.primary,
    .add-frame-btn {
      border-color: var(--ui-primary) !important;
      background: var(--ui-primary) !important;
      color: #fff !important;
      box-shadow: 0 7px 16px rgba(55, 111, 100, 0.18);
    }

    .generate-btn:hover:not(:disabled),
    .continue-btn:hover:not(:disabled),
    .save-btn:hover:not(:disabled),
    .partition-btn.primary:hover:not(:disabled),
    .add-frame-btn:hover:not(:disabled) {
      border-color: var(--ui-primary-strong) !important;
      background: var(--ui-primary-strong) !important;
      box-shadow: 0 10px 20px rgba(40, 88, 79, 0.22);
      transform: translateY(-1px);
    }

    .partition-btn:not(.primary),
    .demo-open-btn,
    .back-btn {
      border-color: #bfd0c9 !important;
      background: #fff !important;
      color: var(--ui-primary-strong) !important;
      box-shadow: none;
    }

    .partition-btn:not(.primary):hover:not(:disabled),
    .demo-open-btn:hover:not(:disabled),
    .back-btn:hover:not(:disabled) {
      border-color: var(--ui-primary) !important;
      background: var(--ui-primary-soft) !important;
      transform: none;
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.72rem 1rem;
      cursor: pointer;
    }

    button:focus-visible,
    input:focus-visible,
    select:focus-visible {
      outline: 3px solid rgba(55, 111, 100, 0.24) !important;
      outline-offset: 2px;
    }

    #commandFramesContainer {
      grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)) !important;
      gap: 14px !important;
    }

    .command-frame-card {
      border-color: var(--ui-line);
      border-radius: 15px;
      background: #fff;
      box-shadow: 0 4px 14px rgba(29, 65, 56, 0.055);
    }

    .command-frame-card:hover {
      border-color: #9ebeb1;
      box-shadow: 0 9px 24px rgba(29, 65, 56, 0.1);
      transform: translateY(-2px);
    }

    .frame-status-legend {
      color: var(--ui-muted);
    }

    .action-row {
      position: sticky;
      z-index: 20;
      bottom: 10px;
      margin-top: 24px;
      padding: 13px;
      border: 1px solid rgba(207, 225, 217, 0.9);
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.92);
      box-shadow: 0 12px 32px rgba(29, 65, 56, 0.14);
      backdrop-filter: blur(10px);
    }

    #deleteConfigurationBtn {
      border-color: #b42318 !important;
      background: #fff !important;
      color: #b42318 !important;
      box-shadow: none;
    }

    #deleteConfigurationBtn:hover {
      background: #fff1f0 !important;
      transform: none;
    }

    @media (max-width: 980px) {
      .product-meta {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }

      .command-response-error-flow {
        gap: 7px;
      }

      .flow-stage {
        padding-inline: 9px;
      }
    }

    @media (max-width: 720px) {
      body {
        padding: 0;
        background: #fff;
      }

      .card {
        padding: 16px;
        border: 0;
        border-radius: 0;
        box-shadow: none;
      }

      .header-title {
        align-items: stretch;
      }

      .brand-badge {
        align-self: flex-start;
      }

      .product-meta {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .command-response-error-flow {
        grid-template-columns: 1fr;
      }

      .flow-arrow {
        display: none;
      }

      .flow-stage {
        width: 100%;
      }

      .param-row,
      .api-entry-row,
      .partition-howto {
        grid-template-columns: 1fr;
      }

      .api-count {
        justify-content: center;
        white-space: normal;
      }

      .step-heading {
        align-items: flex-start;
      }

      .demo-open-btn {
        margin-left: auto;
      }

      .partition-toolbar,
      .partition-confirm-row,
      .action-row {
        align-items: stretch;
      }

      .action-row {
        bottom: 0;
      }

      .action-row .save-status-badge {
        width: 100%;
      }

      .action-row button {
        flex: 1 1 160px;
      }
    }

    @media (max-width: 460px) {
      .header-title {
        align-items: stretch;
      }

      .header-actions {
        width: 100%;
        justify-content: space-between;
        margin-top: 0.9rem;
      }

      .header-back-btn {
        margin-left: auto;
      }

      .product-meta {
        grid-template-columns: 1fr;
      }

      .section,
      #commandFramesSection,
      #apiPartitionSection {
        padding: 14px !important;
      }

      .partition-side-nav {
        width: 34px;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      html {
        scroll-behavior: auto;
      }

      *,
      *::before,
      *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
      }
    }

    /* =========================================================
       Enterprise UI system
       Final visual layer only. Existing IDs, events, payloads,
       PHP variables and backend behaviour remain unchanged.
       ========================================================= */
    :root {
      --ent-bg: #f5f7fb;
      --ent-surface: #ffffff;
      --ent-surface-subtle: #f8fafc;
      --ent-surface-raised: #ffffff;
      --ent-text: #172033;
      --ent-text-secondary: #536179;
      --ent-text-tertiary: #7b879d;
      --ent-border: #e1e6ef;
      --ent-border-strong: #cbd3e1;
      --ent-primary: #635bff;
      --ent-primary-hover: #5048e5;
      --ent-primary-soft: #f1f0ff;
      --ent-success: #16825d;
      --ent-success-soft: #ecf9f3;
      --ent-warning: #a15c00;
      --ent-warning-soft: #fff7e8;
      --ent-danger: #c4323f;
      --ent-danger-soft: #fff1f2;
      --ent-focus: rgba(99, 91, 255, 0.18);
      --ent-shadow-xs: 0 1px 2px rgba(23, 32, 51, 0.05);
      --ent-shadow-sm: 0 4px 14px rgba(23, 32, 51, 0.06);
      --ent-shadow-md: 0 18px 48px rgba(23, 32, 51, 0.09);
      --ent-radius-sm: 9px;
      --ent-radius-md: 13px;
      --ent-radius-lg: 18px;
      --ent-transition: 160ms cubic-bezier(0.2, 0, 0, 1);
    }

    html {
      color-scheme: light;
      background: var(--ent-bg);
      font-size: 16px;
      scroll-padding-top: 20px;
    }

    body {
      min-height: 100vh;
      padding: clamp(14px, 2.5vw, 36px) !important;
      color: var(--ent-text) !important;
      background:
        radial-gradient(circle at 50% -20%, rgba(99, 91, 255, 0.09), transparent 34rem),
        var(--ent-bg) !important;
      font-family: Inter, ui-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
      line-height: 1.5;
    }

    body,
    button,
    input,
    select,
    textarea {
      font-family: inherit !important;
    }

    .card {
      width: min(100%, 1320px) !important;
      max-width: 1320px !important;
      padding: clamp(18px, 3vw, 40px) !important;
      overflow: visible !important;
      border: 1px solid var(--ent-border) !important;
      border-radius: 24px !important;
      background: var(--ent-surface) !important;
      box-shadow: var(--ent-shadow-md) !important;
    }

    .header-title {
      display: grid !important;
      grid-template-columns: minmax(0, 1fr) auto !important;
      align-items: start !important;
      gap: 20px !important;
      margin-bottom: 22px !important;
      padding-bottom: 22px !important;
      border-bottom: 1px solid var(--ent-border) !important;
    }

    .page-heading-copy {
      min-width: 0;
    }

    .page-eyebrow {
      color: var(--ent-primary) !important;
      font-size: 0.7rem !important;
      font-weight: 750 !important;
      letter-spacing: 0.1em !important;
    }

    .header-title h1 {
      color: var(--ent-text) !important;
      font-size: clamp(1.55rem, 3vw, 2.1rem) !important;
      font-weight: 760 !important;
      letter-spacing: -0.035em !important;
    }

    .header-subtitle {
      max-width: 720px !important;
      margin-top: 6px !important;
      color: var(--ent-text-secondary) !important;
      font-size: 0.9rem !important;
    }

    .title-icon {
      width: 42px !important;
      height: 42px !important;
      border: 1px solid #d9d7ff !important;
      border-radius: 12px !important;
      color: var(--ent-primary) !important;
      background: var(--ent-primary-soft) !important;
      box-shadow: none !important;
    }

    .header-actions {
      display: flex !important;
      align-items: center !important;
      justify-content: flex-end !important;
      gap: 10px !important;
      margin-left: 0 !important;
    }

    .brand-badge {
      min-height: 40px !important;
      max-width: 320px !important;
      padding: 8px 13px !important;
      border: 1px solid var(--ent-border) !important;
      border-radius: 11px !important;
      color: var(--ent-text-secondary) !important;
      background: var(--ent-surface-subtle) !important;
      box-shadow: none !important;
    }

    .product-meta {
      display: grid !important;
      grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
      gap: 10px !important;
      margin-bottom: 22px !important;
      padding: 10px !important;
      border: 1px solid var(--ent-border) !important;
      border-radius: var(--ent-radius-lg) !important;
      background: var(--ent-surface-subtle) !important;
      box-shadow: none !important;
    }

    .product-meta .meta-item {
      display: grid !important;
      grid-template-columns: 34px minmax(0, 1fr) !important;
      column-gap: 80px !important;
      row-gap: 1px !important;
      min-width: 0 !important;
      padding: 11px !important;
      border: 1px solid transparent !important;
      border-radius: 12px !important;
      background: var(--ent-surface) !important;
      box-shadow: var(--ent-shadow-xs) !important;
      transition: border-color var(--ent-transition), box-shadow var(--ent-transition), transform var(--ent-transition) !important;
    }

    .product-meta .meta-item:hover {
      border-color: var(--ent-border-strong) !important;
      box-shadow: var(--ent-shadow-sm) !important;
      transform: translateY(-1px);
    }

    .meta-item .meta-icon {
      grid-row: 1 / 3 !important;
      align-self: center !important;
      width: 34px !important;
      height: 34px !important;
      margin: 0 !important;
      border: 1px solid #d9d7ff !important;
      border-radius: 10px !important;
      color: var(--ent-primary) !important;
      background: var(--ent-primary-soft) !important;
      box-shadow: none !important;
    }

    .meta-item .label {
      color: var(--ent-text-tertiary) !important;
      font-size: 0.64rem !important;
      font-weight: 750 !important;
      letter-spacing: 0.07em !important;
    }

    .meta-item .value,
    .meta-item .value.light {
      min-width: 0 !important;
      overflow: hidden !important;
      color: var(--ent-text) !important;
      font-size: 0.84rem !important;
      font-weight: 680 !important;
      text-overflow: ellipsis !important;
      white-space: nowrap !important;
    }

    .command-response-error-flow {
      display: grid !important;
      grid-template-columns: minmax(0, 1fr) 22px minmax(0, 1fr) 22px minmax(0, 1fr) !important;
      align-items: center !important;
      gap: 10px !important;
      margin: 0 0 22px !important;
      padding: 10px !important;
      overflow: visible !important;
      border: 1px solid var(--ent-border) !important;
      border-radius: var(--ent-radius-lg) !important;
      background: var(--ent-surface-subtle) !important;
    }

    .flow-stage {
      min-width: 0 !important;
      min-height: 64px !important;
      padding: 10px 13px !important;
      border: 1px solid transparent !important;
      border-radius: 12px !important;
      color: var(--ent-text-secondary) !important;
      background: transparent !important;
      box-shadow: none !important;
      transition: color var(--ent-transition), background-color var(--ent-transition), border-color var(--ent-transition), box-shadow var(--ent-transition), transform var(--ent-transition) !important;
    }

    .flow-stage:hover:not(:disabled) {
      border-color: var(--ent-border-strong) !important;
      color: var(--ent-text) !important;
      background: var(--ent-surface) !important;
      box-shadow: var(--ent-shadow-xs) !important;
      transform: translateY(-1px) !important;
    }

    .flow-stage.active {
      border-color: var(--ent-primary) !important;
      color: #fff !important;
      background: var(--ent-primary) !important;
      box-shadow: 0 8px 20px rgba(99, 91, 255, 0.22) !important;
    }

    .flow-stage.completed:not(.active),
    .flow-stage.filled:not(.active) {
      border-color: #b8ddce !important;
      color: var(--ent-success) !important;
      background: var(--ent-success-soft) !important;
    }

    .flow-stage.in-progress:not(.active) {
      border-color: #f0d59d !important;
      color: var(--ent-warning) !important;
      background: var(--ent-warning-soft) !important;
    }

    .flow-arrow {
      color: #9aa6ba !important;
      font-size: 0.75rem !important;
      text-align: center;
    }

    .section,
    #commandFramesSection,
    #apiPartitionSection {
      margin-top: 20px !important;
      padding: clamp(17px, 2.3vw, 26px) !important;
      border: 1px solid var(--ent-border) !important;
      border-radius: var(--ent-radius-lg) !important;
      color: var(--ent-text) !important;
      background: var(--ent-surface) !important;
      box-shadow: var(--ent-shadow-sm) !important;
    }

    .step-heading {
      display: flex !important;
      align-items: center !important;
      gap: 11px !important;
      margin-bottom: 17px !important;
      padding-bottom: 14px !important;
      border-bottom: 1px solid var(--ent-border) !important;
    }

    .step-number,
    #apiPartitionSection .step-number {
      min-width: auto !important;
      padding: 5px 9px !important;
      border: 0 !important;
      border-radius: 999px !important;
      color: #fff !important;
      background: var(--ent-primary) !important;
      box-shadow: none !important;
      font-size: 0.63rem !important;
      font-weight: 780 !important;
      letter-spacing: 0.07em !important;
    }

    .section-title,
    #apiPartitionSection .section-title,
    .field-desc-title {
      color: var(--ent-text) !important;
      font-size: 1.02rem !important;
      font-weight: 740 !important;
      letter-spacing: -0.01em !important;
    }

    .section-title i,
    #apiPartitionSection .section-title i,
    .field-desc-title-icon,
    .control-label i {
      color: var(--ent-primary) !important;
    }

    .section-guidance,
    .section-desc,
    #apiPartitionSection .section-desc {
      color: var(--ent-text-secondary) !important;
      font-size: 0.79rem !important;
    }

    .param-grid {
      gap: 16px !important;
      padding: 0 !important;
      border: 0 !important;
      background: transparent !important;
      box-shadow: none !important;
    }

    .param-row {
      display: grid !important;
      grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
      gap: 16px !important;
      align-items: start !important;
    }

    .param-item {
      display: block !important;
      min-width: 0 !important;
      width: auto !important;
      padding: 0 !important;
      border: 0 !important;
      background: transparent !important;
    }

    .control-label,
    #apiPartitionSection .control-label,
    .byte-modal .form-group label,
    .param-item label {
      display: flex !important;
      align-items: center !important;
      gap: 7px !important;
      margin: 0 0 7px !important;
      color: #344055 !important;
      font-size: 0.77rem !important;
      font-weight: 680 !important;
      white-space: normal !important;
    }

    .required-dot,
    .required-badge {
      color: var(--ent-danger) !important;
    }

    .required-badge {
      background: var(--ent-danger-soft) !important;
    }

    .input-with-icon,
    .select-with-icon {
      position: relative !important;
    }

    .param-item input,
    .param-item select,
    .input-with-icon input,
    .select-with-icon select,
    #apiPartitionSection .api-input-wrap input,
    .frame-setting select,
    .byte-modal .form-group input,
    .byte-modal .form-group select,
    .command-entry input,
    .byte-field-select,
    .partition-name-band select {
      width: 100% !important;
      min-width: 0 !important;
      min-height: 44px !important;
      padding: 10px 13px 10px 40px !important;
      border: 1px solid var(--ent-border-strong) !important;
      border-radius: 10px !important;
      color: var(--ent-text) !important;
      background: var(--ent-surface) !important;
      box-shadow: var(--ent-shadow-xs) !important;
      font-size: 0.86rem !important;
      transition: border-color var(--ent-transition), box-shadow var(--ent-transition), background-color var(--ent-transition) !important;
    }

    .partition-name-band select,
    .frame-setting select,
    .byte-field-select {
      padding-left: 12px !important;
    }

    .input-with-icon > i,
    .select-with-icon > i {
      left: 13px !important;
      color: var(--ent-text-tertiary) !important;
    }

    input::placeholder,
    textarea::placeholder {
      color: #98a3b5 !important;
      opacity: 1 !important;
    }

    input:hover,
    select:hover,
    textarea:hover {
      border-color: #aab4c5 !important;
    }

    input:focus,
    select:focus,
    textarea:focus {
      border-color: var(--ent-primary) !important;
      outline: 0 !important;
      box-shadow: 0 0 0 4px var(--ent-focus) !important;
    }

    .field-setup-hint,
    .command-frame-hint,
    .partition-status,
    .api-count {
      color: var(--ent-text-secondary) !important;
    }

    .dropdown-warning,
    .api-error:not(:empty),
    .command-error:not(:empty),
    .input-error-message:not(:empty) {
      border-color: #efb9be !important;
      color: #9d2934 !important;
      background: var(--ent-danger-soft) !important;
    }

    .input-with-icon.input-error input,
    .select-with-icon.dropdown-error select,
    [aria-invalid="true"] {
      border-color: var(--ent-danger) !important;
      background: #fffafb !important;
      box-shadow: 0 0 0 3px rgba(196, 50, 63, 0.11) !important;
    }

    button,
    .generate-btn,
    .continue-btn,
    .save-btn,
    .partition-btn,
    .add-frame-btn,
    .demo-open-btn,
    .back-btn {
      min-height: 42px !important;
      border-radius: 10px !important;
      font-size: 0.78rem !important;
      font-weight: 700 !important;
      letter-spacing: 0 !important;
      transition: color var(--ent-transition), background-color var(--ent-transition), border-color var(--ent-transition), box-shadow var(--ent-transition), transform var(--ent-transition) !important;
    }

    .generate-btn,
    .continue-btn,
    .save-btn,
    .partition-btn.primary,
    .add-frame-btn {
      border: 1px solid var(--ent-primary) !important;
      color: #fff !important;
      background: var(--ent-primary) !important;
      box-shadow: 0 5px 12px rgba(99, 91, 255, 0.18) !important;
    }

    .generate-btn:hover:not(:disabled),
    .continue-btn:hover:not(:disabled),
    .save-btn:hover:not(:disabled),
    .partition-btn.primary:hover:not(:disabled),
    .add-frame-btn:hover:not(:disabled) {
      border-color: var(--ent-primary-hover) !important;
      background: var(--ent-primary-hover) !important;
      box-shadow: 0 8px 18px rgba(80, 72, 229, 0.23) !important;
      transform: translateY(-1px) !important;
    }

    .partition-btn:not(.primary),
    .demo-open-btn,
    .back-btn,
    .btn-cancel-modal,
    .demo-popup-actions button {
      border: 1px solid var(--ent-border-strong) !important;
      color: #3d4960 !important;
      background: var(--ent-surface) !important;
      box-shadow: var(--ent-shadow-xs) !important;
    }

    .partition-btn:not(.primary):hover:not(:disabled),
    .demo-open-btn:hover:not(:disabled),
    .back-btn:hover:not(:disabled),
    .btn-cancel-modal:hover:not(:disabled) {
      border-color: #a9a5ff !important;
      color: var(--ent-primary-hover) !important;
      background: var(--ent-primary-soft) !important;
      transform: translateY(-1px) !important;
    }

    button:active:not(:disabled) {
      transform: translateY(0) scale(0.985) !important;
    }

    button:disabled {
      cursor: not-allowed !important;
      opacity: 0.52 !important;
      box-shadow: none !important;
    }

    button:focus-visible,
    input:focus-visible,
    select:focus-visible,
    textarea:focus-visible,
    [tabindex]:focus-visible {
      outline: 3px solid var(--ent-focus) !important;
      outline-offset: 2px !important;
    }

    #commandFramesContainer {
      display: grid !important;
      grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)) !important;
      gap: 14px !important;
    }

    .command-frame-card,
    .field-desc-card,
    .byte-selector,
    .partition-workspace,
    #apiPartitionSection .partition-workspace {
      border: 1px solid var(--ent-border) !important;
      border-radius: var(--ent-radius-md) !important;
      color: var(--ent-text) !important;
      background: var(--ent-surface) !important;
      box-shadow: var(--ent-shadow-xs) !important;
    }

    .command-frame-card {
      transition: border-color var(--ent-transition), box-shadow var(--ent-transition), transform var(--ent-transition) !important;
    }

    .command-frame-card:hover {
      border-color: #aaa6ff !important;
      background: var(--ent-surface) !important;
      box-shadow: var(--ent-shadow-sm) !important;
      transform: translateY(-2px) !important;
    }

    .command-frame-card.selected {
      border-color: var(--ent-primary) !important;
      background: var(--ent-primary-soft) !important;
      box-shadow: 0 0 0 3px var(--ent-focus) !important;
    }

    .command-frame-card .frame-title,
    .command-code-label,
    .frame-code strong {
      color: var(--ent-text) !important;
    }

    .frame-direction,
    .frame-status-legend span,
    .api-count,
    .save-status-badge {
      border-color: var(--ent-border) !important;
      color: var(--ent-text-secondary) !important;
      background: var(--ent-surface-subtle) !important;
    }

    .save-status-badge.saved,
    .partition-status.complete,
    .frame-partition-state.complete,
    .linked-api-state {
      border-color: #b8ddce !important;
      color: var(--ent-success) !important;
      background: var(--ent-success-soft) !important;
    }

    #apiPartitionSection,
    #apiPartitionSection .partition-workspace,
    #apiPartitionSection .partition-instruction,
    #apiPartitionSection .api-timeline {
      color: var(--ent-text) !important;
      border-color: var(--ent-border) !important;
      background: var(--ent-surface) !important;
      box-shadow: none !important;
    }

    #apiPartitionSection .partition-instruction {
      color: var(--ent-text-secondary) !important;
      background: var(--ent-surface-subtle) !important;
    }

    #apiPartitionSection .api-byte-cell {
      border-color: var(--ent-border) !important;
      color: var(--ent-text) !important;
      background: var(--ent-surface) !important;
    }

    #apiPartitionSection .partition-btn {
      color: #3d4960 !important;
      background: var(--ent-surface) !important;
      border-color: var(--ent-border-strong) !important;
    }

    #apiPartitionSection .partition-btn.primary,
    #apiPartitionSection .continue-btn {
      color: #fff !important;
      background: var(--ent-primary) !important;
      border-color: var(--ent-primary) !important;
    }

    .action-row {
      position: sticky !important;
      z-index: 30 !important;
      bottom: 12px !important;
      display: flex !important;
      align-items: center !important;
      gap: 10px !important;
      margin-top: 24px !important;
      padding: 12px !important;
      border: 1px solid rgba(203, 211, 225, 0.85) !important;
      border-radius: 14px !important;
      background: rgba(255, 255, 255, 0.94) !important;
      box-shadow: 0 12px 34px rgba(23, 32, 51, 0.13) !important;
      backdrop-filter: blur(12px);
    }

    #deleteConfigurationBtn {
      border-color: #e4aab0 !important;
      color: var(--ent-danger) !important;
      background: #fff !important;
      box-shadow: none !important;
    }

    #deleteConfigurationBtn:hover:not(:disabled) {
      border-color: var(--ent-danger) !important;
      color: #fff !important;
      background: var(--ent-danger) !important;
    }

    .byte-modal,
    .partition-mode-modal,
    .demo-popup {
      border: 1px solid var(--ent-border) !important;
      border-radius: 18px !important;
      color: var(--ent-text) !important;
      background: var(--ent-surface) !important;
      box-shadow: 0 24px 70px rgba(23, 32, 51, 0.22) !important;
    }

    .byte-modal-overlay,
    .partition-mode-overlay,
    .demo-overlay {
      background: rgba(16, 24, 40, 0.54) !important;
      backdrop-filter: blur(5px) !important;
    }

    .byte-modal-overlay[style*="flex"] .byte-modal,
    .partition-mode-overlay[style*="flex"] .partition-mode-modal,
    .demo-overlay[style*="flex"] .demo-popup {
      animation: enterpriseModalIn 220ms cubic-bezier(0.2, 0.8, 0.2, 1) both !important;
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      overflow: hidden;
      border: 1px solid var(--ent-border);
      border-radius: 12px;
      color: var(--ent-text);
      background: var(--ent-surface);
      box-shadow: var(--ent-shadow-xs);
    }

    thead th {
      position: sticky;
      z-index: 2;
      top: 0;
      padding: 11px 13px;
      border-bottom: 1px solid var(--ent-border);
      color: var(--ent-text-secondary);
      background: #f8fafc;
      font-size: 0.7rem;
      font-weight: 760;
      letter-spacing: 0.055em;
      text-align: left;
      text-transform: uppercase;
    }

    tbody td {
      padding: 11px 13px;
      border-bottom: 1px solid #edf0f5;
      color: var(--ent-text-secondary);
      font-size: 0.82rem;
      vertical-align: middle;
    }

    tbody tr:nth-child(even) {
      background: #fbfcfe;
    }

    tbody tr:hover {
      background: var(--ent-primary-soft);
    }

    tbody tr:last-child td {
      border-bottom: 0;
    }

    .table-responsive {
      width: 100%;
      overflow-x: auto;
      border-radius: 12px;
      -webkit-overflow-scrolling: touch;
    }

    @keyframes enterpriseModalIn {
      from {
        opacity: 0;
        transform: translateY(14px) scale(0.98);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @media (max-width: 1050px) {
      .product-meta {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
      }
    }

    @media (max-width: 760px) {
      body {
        padding: 0 !important;
        background: var(--ent-surface) !important;
      }

      .card {
        padding: 16px !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
      }

      .header-title {
        grid-template-columns: 1fr !important;
      }

      .header-actions {
        width: 100% !important;
        justify-content: space-between !important;
      }

      .product-meta {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
      }

      .command-response-error-flow {
        grid-template-columns: 1fr !important;
      }

      .flow-arrow {
        display: none !important;
      }

      .flow-stage {
        width: 100% !important;
      }

      .param-row,
      .api-entry-row,
      .partition-howto {
        grid-template-columns: 1fr !important;
      }

      .action-row {
        bottom: 0 !important;
        flex-wrap: wrap !important;
      }

      .action-row .save-status-badge {
        width: 100% !important;
        justify-content: center !important;
      }

      .action-row button {
        flex: 1 1 155px !important;
      }

      .table-responsive table {
        min-width: 700px;
      }
    }

    @media (max-width: 480px) {
      .header-actions {
        align-items: stretch !important;
        flex-direction: column !important;
      }

      .header-actions > * {
        width: 100% !important;
        max-width: none !important;
      }

      .product-meta {
        grid-template-columns: 1fr !important;
      }

      .section,
      #commandFramesSection,
      #apiPartitionSection {
        padding: 14px !important;
        border-radius: 14px !important;
      }

      #commandFramesContainer {
        grid-template-columns: 1fr !important;
      }
    }

  </style>
</head>

<body>
  <div class="card">
    <!-- Header -->
    <div class="header-title">
      <div class="page-heading-copy">
        <h1>
          <span class="title-icon"><i class="fas fa-code"></i></span> <?= $configurationId ? 'Edit' : 'Add' ?> API /
          Product
        </h1>
        <p class="header-subtitle">Configure parameters, frame structures and partitions for this product.</p>
      </div>
      <div class="header-actions">
        <div class="brand-badge"><i class="fas fa-tag"></i> <?= esc($brandValue) ?> · <?= esc($productValue) ?></div>
        <button type="button" class="back-btn header-back-btn" id="backBtn"
          aria-label="Go back to the previous page">
          <i class="fas fa-arrow-left" aria-hidden="true"></i> Back
        </button>
      </div>
    </div>

    <!-- Product meta -->
    <div class="product-meta" aria-label="Selected product details">
      <div class="meta-item">
        <span class="meta-icon"><i class="fas fa-building"></i></span><span class="label">Brand</span><span
          class="value"><?= esc($brandValue) ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-icon"><i class="fas fa-fan"></i></span><span class="label">Product</span><span
          class="value"><?= esc($productValue) ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-icon"><i class="fas fa-microchip"></i></span><span class="label">Model No.</span><span
          class="value"><?= esc($modelValue) ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-icon"><i class="fas fa-code-branch"></i></span><span class="label">Version</span><span
          class="value light"><?= esc($versionValue) ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-icon"><i class="fas fa-boxes-stacked"></i></span><span class="label">Batch</span><span
          class="value light"><?= esc($batchValue) ?></span>
      </div>
    </div>

    <!-- Step 2: Full API partitioning for the selected command frame -->
    <div class="api-partition-section" id="apiPartitionSection">
      <div class="step-heading">
        <span class="step-number">STEP 2</span>
        <div class="section-title" style="margin: 0">
          <i class="fas fa-scissors"></i> Enter and Partition the Full API
        </div>

        <button type="button" class="demo-open-btn" id="openStepOneDemo">
          <i class="fas fa-circle-play"></i> View Demo
        </button>

      </div>
      <div class="partition-frame-label" id="partitionStepFrameWrap">
        <i class="fas fa-layer-group"></i>
        <span id="partitionStepFrameName">Command Frame</span>
      </div>
      <div class="section-desc" style="margin-top: 0.65rem; margin-bottom: 0">

      </div>

      <div class="partition-instruction">
        <i class="fas fa-circle-info"></i>
        <span><strong>How to partition:</strong> use the
          <strong>+</strong> markers on the timeline. First click a
          <strong>+</strong> to cut, then drag an active cut to move it, or
          click it again to remove.</span>
      </div>

      <div class="partition-howto" aria-label="Partition steps">
        <div class="partition-howto-step">
          <span class="howto-num">1</span>
          <div>
            <strong><i class="fas fa-plus-circle"></i>Below Click +</strong>
          </div>
        </div>
        <div class="partition-howto-step">
          <span class="howto-num">2</span>
          <div>
            <strong><i class="fas fa-arrows-left-right"></i> Drag cut</strong>
            <span class="howto-description">Drag the red scissors marker left/right to move the cut, or
              click it to remove.</span>
          </div>
        </div>
        <div class="partition-howto-step">
          <span class="howto-num">3</span>
          <div>
            <strong><i class="fas fa-tags"></i> Name &amp; configure</strong>
            <span class="howto-description">Choose each partition name under the colored bytes, then
              save.</span>
          </div>
        </div>
      </div>

      <div class="api-entry-row">
        <div class="api-input-wrap">
          <label for="fullApiInput" class="control-label"><i class="fas fa-terminal"></i> Command Value
            <span style="font-weight: 500; color: #71839a">(hexadecimal)</span>
            <span class="required-badge">Required</span></label>
          <div class="input-with-icon">
            <i class="fas fa-code"></i><input id="fullApiInput" type="text" inputmode="text" autocomplete="off"
              spellcheck="false" placeholder="Enter hexadecimal value, e.g. A1E70A12FF0B"
              aria-describedby="apiError" aria-required="true" />
          </div>
        </div>
        <div class="api-count" id="apiByteCount">
          <i class="fas fa-ruler-horizontal"></i> 0 bytes, 0 hex characters
        </div>
      </div>
      <div class="api-error" id="apiError" role="alert"></div>
      <div class="partition-ready-hint" id="partitionReadyHint" role="status" aria-live="polite">
        <i class="fas fa-scissors"></i>
        <span>Command code is ready. Now click a <strong>+</strong> between the
          byte boxes to create partitions.</span>
      </div>

      <div class="partition-workspace">
        <!-- <div class="cut-coach" aria-hidden="false">
            <span class="cut-coach-pill"
              ><i class="fas fa-plus"></i> Grey + = add cut</span
            >
            <span class="cut-coach-pill"
              ><i class="fas fa-scissors"></i> Red scissors = drag or remove</span
            >
            <span class="cut-coach-pill"
              ><i class="fas fa-list"></i> Dropdown under bytes = partition
              name</span
            >
          </div> -->

        <div class="partition-toolbar">
          <button type="button" class="partition-btn primary" id="addPartitionBtn">
            <i class="fas fa-plus"></i> Add One More Partition
          </button>
          <button type="button" class="partition-btn" id="clearPartitionsBtn">
            <i class="fas fa-rotate-left"></i> Clear Cuts
          </button>
          <span class="partition-limit" id="partitionLimitText">Enter at least 2 bytes to add a cut.</span>
        </div>

        <div class="partition-timeline-shell">
          <button type="button" class="partition-side-nav left" id="previousPartitionBtn"
            aria-label="Show previous partition" title="Move to previous partition" disabled>
            <i class="fas fa-chevron-left" aria-hidden="true"></i>
          </button>
          <div class="api-timeline-scroll" id="apiTimelineScroll">
            <div id="apiTimelineEmpty" class="timeline-empty">
              Enter the full API above to display its bytes and partition
              controls.
            </div>
            <div id="apiTimeline" class="api-timeline" style="display: none"
              aria-label="API byte partition timeline">
            </div>
          </div>
          <button type="button" class="partition-side-nav right" id="nextPartitionBtn"
            aria-label="Show next partition" title="Move to next partition" disabled>
            <i class="fas fa-chevron-right" aria-hidden="true"></i>
          </button>
        </div>

        <div class="partition-visual" aria-live="polite">
          <div class="partition-visual-title">
            <span><i class="fas fa-cubes-stacked"></i> Partition Block
              Preview</span>
            <span class="partition-visual-legend">Each group shows byte boxes for that partition</span>
          </div>
          <div class="partition-blocks" id="partitionVisualBlocks">
            <div class="partition-visual-empty">
              <i class="fas fa-circle-info"></i>
              Enter a complete API to see each partition as a labeled block.
            </div>
          </div>
        </div>

        <div class="partition-confirm-row">
          <div class="partition-status" id="partitionStatus">
            <i class="fas fa-circle-dot"></i>
            <span>Enter the API, create partitions, and name every block.</span>
          </div>
          <button type="button" class="continue-btn" id="confirmPartitionsBtn">
            click Save Partitions <i class="fas fa-check"></i>
          </button>
        </div>
      </div>
    </div>
<div class="command-response-error-flow" id="commandResponseErrorFlow" role="tablist" aria-label="Command response and error flow">
        <button type="button" class="flow-stage active" data-flow-frame-type="command" role="tab"
          aria-selected="true">
          <span class="flow-stage-number" data-step-number="1">1</span>
          <span class="flow-stage-copy">
            <span class="flow-stage-label">Command</span>
            <span class="flow-stage-status"><i class="fas fa-circle"></i> Not filled</span>
          </span>
        </button>
        <i class="fas fa-arrow-right flow-arrow" aria-hidden="true"></i>
        <button type="button" class="flow-stage" data-flow-frame-type="response" role="tab"
          aria-selected="false">
          <span class="flow-stage-number" data-step-number="2">2</span>
          <span class="flow-stage-copy">
            <span class="flow-stage-label">Response</span>
            <span class="flow-stage-status"><i class="fas fa-circle"></i> Not filled</span>
          </span>
        </button>
        <i class="fas fa-arrow-right flow-arrow" aria-hidden="true"></i>
        <button type="button" class="flow-stage" data-flow-frame-type="error" role="tab"
          aria-selected="false">
          <span class="flow-stage-number" data-step-number="3">3</span>
          <span class="flow-stage-copy">
            <span class="flow-stage-label">Error</span>
            <span class="flow-stage-status"><i class="fas fa-circle"></i> Not filled</span>
          </span>
        </button>
      </div>
    <!-- Step 1: Parameter setup for the selected frame type -->
    <div class="section" id="parameterCommandSection">
      <div class="step-heading">
        <span class="step-number">STEP 1</span>
        <div class="section-title" id="parameterSetupTitle" style="margin: 0">
          <i class="fas fa-sliders-h"></i> Parameter & Command Setup
        </div>
      </div>
      <!-- <div class="command-response-error-flow" aria-label="Command response and error flow">
        <div class="flow-stage active">
          <span class="flow-stage-number">2</span>
          <span>Command</span>
        </div>
        <i class="fas fa-arrow-right flow-arrow" aria-hidden="true"></i>
        <div class="flow-stage">
          <span class="flow-stage-number">3</span>
          <span>Response</span>
        </div>
        <i class="fas fa-arrow-right flow-arrow" aria-hidden="true"></i>
        <div class="flow-stage">
          <span class="flow-stage-number">4</span>
          <span>Error</span>
        </div>
      </div> -->
      <div class="section-guidance">
        <i class="fas fa-circle-info" aria-hidden="true"></i>
        <span>Define the parameter and number of frames for the selected type.</span>
        <span class="required-note"><span class="required-dot">*</span> Required fields</span>
      </div>

      <div class="param-grid">
        <div class="linked-api-panel" id="linkedApiPanel">
          <div class="linked-api-info">
            <div class="linked-api-title">
              <i class="fas fa-link"></i> API Structure from Partition Step
            </div>
            <div class="linked-api-code" id="linkedApiCode">
              Complete Step 2 for a frame to import its API structure.
            </div>
            <div class="linked-partition-chips" id="linkedPartitionChips"></div>
          </div>
          <div class="linked-api-state pending" id="linkedApiState">
            <i class="fas fa-clock"></i> Waiting for Step 2
          </div>
        </div>

        <div class="param-row">
          <div class="param-item">
            <label class="control-label" for="paramNameInput"><i class="fas fa-signature"></i> Parameter Name
              <span class="required-badge">Required</span></label>
            <div class="input-with-icon">
              <i class="fas fa-pen-nib"></i><input type="text" id="paramNameInput" style="min-width: 150px"
                placeholder="e.g. Fan Speed" value="<?= esc($editParameterName, 'attr') ?>" required
                aria-required="true" />
            </div>
            <span class="field-setup-hint" data-for="paramNameInput"><i class="fas fa-circle-info"></i> Enter a
              parameter
              name</span>
          </div>
          <div class="param-item">
            <label class="control-label" for="cmdFrameCountInput"><i class="fas fa-layer-group"></i> No. of
              Frames <span class="required-badge">Required</span></label>
            <div class="input-with-icon">
              <i class="fas fa-hashtag"></i><input type="number" id="cmdFrameCountInput" value="<?= $editFrameCount ?>" style="width: 70px"
                min="0" max="20" inputmode="numeric" required aria-required="true" />
            </div>
            <span class="field-setup-hint" data-for="cmdFrameCountInput"><i class="fas fa-circle-info"></i> Set at least
              1 frame</span>
          </div>
          <!-- <div class="param-item"> -->
          <!-- <label for="triggerUiSelect" class="control-label"
                ><i class="fas fa-bolt"></i> Trigger UI</label
              > -->
          <!-- <div class="select-with-icon">
                <i class="fas fa-wand-magic-sparkles"></i
                ><select id="triggerUiSelect" class="multi-trigger" required>
                  <option value="" selected disabled>Select Trigger UI</option>
                  <option value="false">False</option>
                  <option value="true">True</option>
                </select>
              </div> -->
          <!-- <span class="field-setup-hint" data-for="triggerUiSelect"
                ><i class="fas fa-circle-info"></i> Choose True or False</span
              > -->
          <!-- </div> -->
        </div>

        <div class="param-row">
          <div class="param-item frame-type-row">
            <div class="frame-type-switcher" role="tablist" aria-label="Select frame type">
              <!-- <button type="button" class="frame-type-btn active" data-frame-type="command" role="tab"
                aria-selected="true">
                <i class="fas fa-terminal"></i> Command Frame
                <span class="type-count" id="commandTypeCount">0</span>
              </button>
              <button type="button" class="frame-type-btn" data-frame-type="response" role="tab"
                aria-selected="false">
                <i class="fas fa-reply"></i> Response Frame
                <span class="type-count" id="responseTypeCount">0</span>
              </button>
              <button type="button" class="frame-type-btn" data-frame-type="error" role="tab"
                aria-selected="false">
                <i class="fas fa-triangle-exclamation"></i> Error Frame
                <span class="type-count" id="errorTypeCount">0</span>
              </button>
            </div> -->
            <button
              type="button"
              class="generate-btn"
              id="generateFramesBtn"
              <?= $isEditMode ? 'hidden aria-hidden="true" style="display: none !important;"' : '' ?>
            >
              <i class="fas fa-sync-alt"></i> Generate Command Frames
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- COMMAND FRAMES DISPLAY -->
    <div id="commandFramesSection" style="
          margin-top: 2rem;
          background: #f5faff;
          border-radius: 24px;
          padding: 1.2rem 1.5rem;
          border: 1px solid #dbe6f5;
        ">
      <div style="
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 0.8rem;
          ">
        <div class="frames-header-left">
          <span style="font-weight: 700; color: #382e28; font-size: 1.1rem"><i class="fas fa-terminal"
              style="color: #a98b76"></i> <span id="activeFramesTitle">COMMAND FRAMES</span>
            (<span id="cmdFrameCountDisplay">0</span>)</span>
          <button type="button" class="add-frame-btn" id="addCommandFrameBtn" title="Add a new command frame">
            <i class="fas fa-plus"></i> <span id="addFrameButtonText">Add Command Frame</span>
          </button>
        </div>
        <div class="command-frame-help">
          <span class="command-frame-hint" id="activeFrameHelp"><i class="fas fa-hand-pointer"></i> Click a Command
            Frame card to open Step 2 and enter its full API.</span>
          <div class="command-example" aria-label="Example command groups A1 2C0B AB">
            <strong>Example</strong>
            <span class="example-byte">A1</span>
            <span class="example-byte">2C0B</span>
            <span class="example-byte">AB</span>
            <span class="example-result">= A12C0BAB</span>
          </div>
        </div>
      </div>
      <div class="frame-status-legend" aria-label="Frame status legend">
        <span><i class="dot pending" aria-hidden="true"></i> Pending</span>
        <span><i class="dot in-progress" aria-hidden="true"></i> In
          progress</span>
        <span><i class="dot filled" aria-hidden="true"></i> Filled</span>
      </div>
      <div class="frames-next-banner" id="framesNextBanner" role="status" aria-live="polite">
        <i class="fas fa-hand-pointer"></i>
        <span><strong>Next step:</strong> click any generated frame card below
          to open Step 2 and partition its API.</span>
      </div>
      <div id="commandFramesContainer" style="
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
          "></div>
    </div>

    <!-- Byte detail popup (for command code entry) -->
    <div id="byteDetailPopup" class="byte-detail-popup">
      <div class="popup-head">
        <span><i class="fas fa-table"></i>
          <span id="popupFrameName">Command Frame 1</span></span>
        <button type="button" class="close" id="closePopupBtn" aria-label="Close frame editor">
          <i class="fas fa-xmark"></i>
        </button>
      </div>
      <div id="byteDetailGrid" class="popup-grid"></div>
    </div>

    <!-- Field Description Section -->
    <!-- <div class="field-desc-section">
        <div class="field-desc-header">
            <div class="field-desc-title">
                <span class="field-desc-title-icon"><i class="fas fa-list-ul"></i></span>
                <span>Field Description</span>
            </div>
            <div class="field-desc-dropdown">
                <label for="fieldDescFrameSelect" id="fieldDescFrameSelectLabel">Command Frame</label>
                <select id="fieldDescFrameSelect">
                    <option value="">Select Frame</option>
                </select>
            </div>
        </div>
        <div class="field-desc-help">
            <i class="fas fa-circle-info"></i>
            <span>All descriptions use Byte Wise configuration. Choosing <strong>Data</strong> additionally enables Bit Wise configuration.</span>
        </div>
        <div id="fieldDescContainer">
            <div class="field-empty"><i class="fas fa-code"></i><span>Enter a command code to configure its fields.</span></div>
        </div>
    </div> -->

    <!-- Action row -->
    <div class="action-row">
      <div class="save-status-badge" id="saveStatusBadge" role="status" aria-live="polite">
        <i class="fas fa-cloud-arrow-down"></i>
        <span>Not saved yet</span>
      </div>
      <button type="button" class="save-btn" id="saveBtn">
        <i class="fas fa-floppy-disk"></i> Save Configuration
      </button>
      <button type="button" class="save-btn" id="deleteConfigurationBtn"
        style="background:#b42318;border-color:#b42318;<?= $configurationId ? '' : 'display:none;' ?>">
        <i class="fas fa-trash"></i> Delete Configuration
      </button>
    </div>
  </div>

  <!-- Saved JSON popup -->
  <div id="jsonSaveOverlay" class="json-save-overlay" aria-hidden="true">
    <div class="json-save-dialog" role="dialog" aria-modal="true" aria-labelledby="jsonSaveTitle">
      <div class="json-save-head">
        <i class="fas fa-circle-check"></i>
        <div>
          <h3 id="jsonSaveTitle">Configuration Saved Successfully</h3>
          <p id="jsonSaveSummary">
            The complete configuration is stored in JSON format.
          </p>
        </div>
        <button type="button" class="json-save-close" id="closeJsonSave" aria-label="Close saved JSON">
          <i class="fas fa-xmark"></i>
        </button>
      </div>
      <div class="json-save-body">
        <div class="configuration-data-preview" id="savedConfigurationPreview"></div>
        <pre class="json-save-code" id="savedJsonOutput" hidden></pre>
      </div>
      <div class="json-save-actions">
        <span class="json-copy-status" id="jsonCopyStatus" role="status"></span>
        <button type="button" id="copySavedJson">
          <i class="fas fa-copy"></i> Copy JSON
        </button>
        <button type="button" id="downloadSavedJson">
          <i class="fas fa-download"></i> Download JSON
        </button>
        <button type="button" class="json-done-btn" id="doneJsonSave">
          <i class="fas fa-check"></i> Done
        </button>
      </div>
    </div>
  </div>


  <!-- Video Demo Overlay -->
  <div id="videoDemoOverlay" class="demo-overlay">
    <div class="demo-popup" role="dialog" aria-modal="true" aria-labelledby="videoDemoTitle">
      <div class="demo-popup-head">
        <h3 id="videoDemoTitle"><i class="fas fa-circle-play"></i> STEP 2 VIDEO DEMO</h3>
        <button type="button" class="demo-close-btn" id="closeVideoDemo" aria-label="Close video"><i
            class="fas fa-xmark"></i></button>
      </div>
      <div class="demo-popup-body" style="padding:0;">
        <video id="demoVideo" controls muted playsinline preload="metadata"
          style="width:100%;height:auto;display:block;background:#000;">
          <source src="C:\Users\DELL\Downloads\WhatsApp Video 2026-07-26 at 7.30.10 PM (1).mp4" type="video/mp4">
          Your browser does not support the video tag.
        </video>
        <div id="demoVideoError" role="alert"
          style="display:none;padding:1rem;text-align:center;color:#9f1239;background:#fff1f2;font-weight:700;">
          Demo video could not be loaded. Keep the video in the same folder as this file and name it
          <code>demo-video.mp4</code>, or choose it below.
        </div>
      </div>
      <div class="demo-popup-actions">
        <input type="file" id="demoVideoFile" accept="video/mp4,video/webm,video/ogg,video/*" hidden>
        <button type="button" id="chooseDemoVideo"><i class="fas fa-folder-open"></i> Choose Video</button>
        <button type="button" id="dismissVideoDemo"><i class="fas fa-circle-check"></i> Got It</button>
      </div>
    </div>
  </div>
  <script>
    (function setupVideoDemo() {
      const videoOverlay = document.getElementById("videoDemoOverlay");
      const video = document.getElementById("demoVideo");
      const openVideoBtn = document.getElementById("openStepOneDemo");
      const closeVideoBtn = document.getElementById("closeVideoDemo");
      const dismissVideoBtn = document.getElementById("dismissVideoDemo");
      const chooseVideoBtn = document.getElementById("chooseDemoVideo");
      const videoFileInput = document.getElementById("demoVideoFile");
      const videoError = document.getElementById("demoVideoError");
      let selectedVideoUrl = "";

      if (
        !videoOverlay ||
        !video ||
        !openVideoBtn ||
        !closeVideoBtn ||
        !dismissVideoBtn ||
        !chooseVideoBtn ||
        !videoFileInput ||
        !videoError
      ) {
        return;
      }

      function showVideoError() {
        videoError.style.display = "block";
      }

      function hideVideoError() {
        videoError.style.display = "none";
      }

      function tryToPlayVideo() {
        const playRequest = video.play();
        if (playRequest && typeof playRequest.catch === "function") {
          playRequest.catch(function () {
            /* The popup remains open so the user can press Play or choose a file. */
          });
        }
      }

      function openVideoDemo() {
        videoOverlay.style.display = "flex";
        videoOverlay.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
        hideVideoError();
        closeVideoBtn.focus();

        if (video.readyState >= 2) {
          video.currentTime = 0;
          tryToPlayVideo();
        } else {
          video.load();
          tryToPlayVideo();
        }
      }

      function closeVideoDemo() {
        videoOverlay.style.display = "none";
        videoOverlay.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
        video.pause();
        try {
          video.currentTime = 0;
        } catch (error) {
          /* Some browsers do not allow seeking before metadata is loaded. */
        }
        openVideoBtn.focus();
      }

      openVideoBtn.addEventListener("click", openVideoDemo);
      closeVideoBtn.addEventListener("click", closeVideoDemo);
      dismissVideoBtn.addEventListener("click", closeVideoDemo);

      video.addEventListener("loadeddata", function () {
        hideVideoError();
        if (videoOverlay.style.display === "flex") {
          tryToPlayVideo();
        }
      });
      video.addEventListener("canplay", hideVideoError);
      video.addEventListener("error", showVideoError);

      chooseVideoBtn.addEventListener("click", function () {
        videoFileInput.click();
      });

      videoFileInput.addEventListener("change", function () {
        const selectedFile = this.files && this.files[0];
        if (!selectedFile) return;

        if (selectedVideoUrl) {
          URL.revokeObjectURL(selectedVideoUrl);
        }

        selectedVideoUrl = URL.createObjectURL(selectedFile);
        hideVideoError();
        video.src = selectedVideoUrl;
        video.load();
        tryToPlayVideo();
      });

      videoOverlay.addEventListener("click", function (event) {
        if (event.target === videoOverlay) {
          closeVideoDemo();
        }
      });

      document.addEventListener("keydown", function (event) {
        if (
          event.key === "Escape" &&
          videoOverlay.style.display === "flex"
        ) {
          closeVideoDemo();
        }
      });

      window.addEventListener("beforeunload", function () {
        if (selectedVideoUrl) {
          URL.revokeObjectURL(selectedVideoUrl);
        }
      });
    })();
  </script>
  <!-- Step 1 visual demo popup -->
  <!-- <div id="stepOneDemoOverlay" class="demo-overlay">
      <div
        class="demo-popup"
        role="dialog"
        aria-modal="true"
        aria-labelledby="stepOneDemoTitle"
      >
        <div class="demo-popup-head">
          <h3 id="stepOneDemoTitle">
            <i class="fas fa-circle-play"></i> STEP 2 VISUAL DEMO
          </h3>
          <button
            type="button"
            class="demo-close-btn"
            id="closeStepOneDemo"
            aria-label="Close demo"
          >
            <i class="fas fa-xmark"></i>
          </button>
        </div>
        <div class="demo-popup-body">
          <div class="demo-steps-grid">
            <div class="demo-step-card">
              <div class="demo-step-title">
                <span class="demo-step-number">1</span
                ><i class="fas fa-keyboard"></i> Type Full API
              </div>
              <div class="demo-canvas">
                <div class="demo-action-label">
                  <i class="fas fa-keyboard"></i> ENTER COMMAND CODE
                </div>
                <div class="demo-input-example">A1E2CAD7</div>
              </div>
            </div>

            <div class="demo-step-card">
              <div class="demo-step-title">
                <span class="demo-step-number">2</span
                ><i class="fas fa-scissors"></i> Add Partition Cut
              </div>
              <div class="demo-canvas">
                <div class="demo-action-label">
                  <i class="fas fa-scissors"></i> DO PARTITION
                </div>
                <div class="demo-bytes">
                  <div class="demo-byte">
                    A1<span class="demo-plus">+</span>
                  </div>
                  <div class="demo-byte">
                    E2<span class="demo-plus">+</span>
                  </div>
                  <div class="demo-byte">
                    CA<span class="demo-plus">+</span>
                  </div>
                  <div class="demo-byte">D7</div>
                </div>
              </div>
            </div>

            <div class="demo-step-card">
              <div class="demo-step-title">
                <span class="demo-step-number">3</span
                ><i class="fas fa-tags"></i> Select Partition Name
              </div>
              <div class="demo-canvas">
                <div class="demo-action-label">
                  <i class="fas fa-tags"></i> SELECT PARTITION NAME
                </div>
                <div class="demo-partitions">
                  <div class="demo-partition" style="--demo-color: #a98b76">
                    <code>A1</code>
                    <div class="demo-control">Command ID</div>
                  </div>
                  <div class="demo-partition" style="--demo-color: #bfa28c">
                    <code>E2</code>
                    <div class="demo-control">Function</div>
                  </div>
                  <div class="demo-partition" style="--demo-color: #babf94">
                    <code>CA</code>
                    <div class="demo-control target">Data</div>
                  </div>
                  <div class="demo-partition" style="--demo-color: #765c4b">
                    <code>D7</code>
                    <div class="demo-control">CRC</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="demo-step-card">
              <div class="demo-step-title">
                <span class="demo-step-number">4</span
                ><i class="fas fa-table-cells"></i> Byte Wise / Bit Wise
              </div>
              <div class="demo-canvas">
                <div class="demo-action-label">
                  <i class="fas fa-table-cells"></i> SELECT BIT WISE
                </div>
                <div class="demo-modes">
                  <div class="demo-mode">Byte Wise</div>
                  <div class="demo-mode target">0101<br />Bit Wise</div>
                </div>
              </div>
            </div>

            <div class="demo-step-card">
              <div class="demo-step-title">
                <span class="demo-step-number">5</span
                ><i class="fas fa-circle-check"></i> Confirm &amp; Continue
              </div>
              <div class="demo-canvas">
                <div class="demo-action-label">
                  <i class="fas fa-hand-pointer"></i> CLICK HERE
                </div>
                <div class="demo-confirm-example">
                  Confirm Partitions &amp; Continue
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="demo-popup-actions">
          <button type="button" id="dismissStepOneDemo">
            <i class="fas fa-circle-check"></i> Got It
          </button>
        </div>
      </div>
    </div> -->

  <!-- Required setup popup shown before frame generation -->
  <div id="generateValidationOverlay" class="generate-validation-overlay" aria-hidden="true">
    <div class="generate-validation-dialog" role="dialog" aria-modal="true" aria-labelledby="generateValidationTitle"
      aria-describedby="generateValidationMessage">
      <div class="generate-validation-head">
        <span class="generate-validation-icon" aria-hidden="true"><i class="fas fa-triangle-exclamation"></i></span>
        <div>
          <h3 id="generateValidationTitle">Complete the required setup</h3>
          <p id="generateValidationMessage">
            Fill Parameter Name and No of Frames before generating frames.
          </p>
        </div>
      </div>
      <div class="generate-validation-body">
        <ul class="generate-missing-list" id="generateMissingList"></ul>
      </div>
      <div class="generate-validation-actions">
        <button type="button" id="continueSetupBtn">
          <i class="fas fa-arrow-up-right-from-square"></i> Fill Missing
          Options
        </button>
      </div>
    </div>
  </div>

  <!-- Partition Byte/Bit mode popup -->
  <div id="partitionModeOverlay" class="partition-mode-overlay">
    <div id="partitionModeModal" class="partition-mode-modal" role="dialog" aria-modal="true"
      aria-labelledby="partitionModeTitle">
      <div class="partition-mode-head">
        <div class="partition-mode-heading">
          <strong id="partitionModeTitle">Partition Configuration</strong>
          <code id="partitionModeCode"></code>
        </div>
        <button type="button" class="partition-mode-close" id="closePartitionMode"
          aria-label="Close partition configuration">
          <i class="fas fa-xmark"></i>
        </button>
      </div>
      <div class="partition-mode-body" id="partitionModeBody"></div>
      <div class="partition-mode-actions">
        <button type="button" class="partition-mode-done" id="donePartitionMode">
          <i class="fas fa-circle-check"></i> Done
        </button>
      </div>
    </div>
  </div>

  <!-- Byte Field Modal (for editing individual byte/field) -->
  <div id="byteFieldModal" class="byte-modal-overlay">
    <div class="byte-modal">
      <button type="button" class="close-modal" id="closeFieldModal" aria-label="Close field configuration">
        <i class="fas fa-xmark"></i>
      </button>
      <h3>
        <i class="fas fa-pen" style="color: #a98b76"></i> Field Configuration
      </h3>
      <div class="byte-code-badge" id="modalByteCode">A1</div>
      <div class="field-form-warning" id="fieldFormWarning" role="alert">
        <i class="fas fa-triangle-exclamation"></i>
        <span>Please fill all required fields correctly before saving.</span>
      </div>
      <div id="modalFieldForm">
        <div class="form-group">
          <label class="control-label"><i class="fas fa-database"></i> Data Type</label>
          <select id="fDataType">
            <option value="unsigned integer">Unsigned Integer</option>
            <option value="signed integer">Signed Integer</option>
            <option value="float">Float</option>
            <option value="double">Double</option>
            <option value="binary">Binary</option>
            <option value="ASCII">ASCII</option>
            <option value="string">String</option>
            <option value="boolean">Boolean</option>
            <option value="hex">Hex</option>
            <option value="enum">Enum</option>
            <option value="timestamp">Timestamp</option>
            <option value="none">None</option>
          </select>
        </div>
        <div class="form-group">
          <label class="control-label"><i class="fas fa-list-check"></i> Value Definition</label>
          <select id="fValueDef">
            <option value="fixed value">Fixed Value</option>
            <option value="range value">Range Value</option>
            <option value="dynamic value">Dynamic Value</option>
            <option value="enum value">Enum Value</option>
            <option value="calculated value">Calculated Value</option>
            <option value="none">None</option>
          </select>
        </div>
        <div class="form-group ui-type-group">
          <label class="control-label" for="fUiType"><i class="fas fa-display"></i> UI Type</label>
          <p class="field-setup-hint" style="margin: 0 0 8px">
            <i class="fas fa-eye"></i> Pick how this field looks in the app
            each option shows a mini preview.
          </p>
          <div class="ui-type-picker" id="uiTypePicker" role="listbox" aria-label="UI Type options"></div>
          <select id="fUiType" tabindex="-1" aria-hidden="true">
            <option value="none">None</option>
            <option value="text">Text</option>
            <option value="toggle switch">Toggle Switch</option>
            <option value="notification">Notification</option>
            <option value="console">Console</option>
            <option value="enter text">Enter Text</option>
            <option value="slider">Slider</option>
            <option value="brightness slider">Brightness Slider</option>
            <option value="airflow slider">Airflow Slider</option>
            <option value="cct slider">CCT Slider</option>
            <option value="saturation slider">Saturation Slider</option>
            <option value="hue slider">Hue Slider</option>
            <option value="hue slider custom">Hue Slider Custom</option>
            <option value="hue circle">Hue Circle</option>
            <option value="push button">Push Button</option>
            <option value="dropdown">Dropdown</option>
            <option value="display">Display</option>
            <option value="button">Button</option>
            <option value="auto trigger with timer">
              Auto Trigger with Timer
            </option>
            <option value="toast display">Toast Display</option>
          </select>
        </div>
        <div class="form-group">
          <label class="control-label"><i class="fas fa-arrow-right-arrow-left"></i> UI In/Output</label>
          <select id="fUiInOut">
            <option value="read">Read</option>
            <option value="write">Write</option>
            <option value="read/write">Read/Write</option>
          </select>
        </div>
        <div class="form-group">
          <label class="control-label"><i class="fas fa-scale-balanced"></i> Scaling</label><input type="text"
            id="fScalling" placeholder="e.g. *1.0, /10" />
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-cancel-modal" id="cancelFieldModal">
          <i class="fas fa-ban"></i> Cancel
        </button>
        <button type="button" class="btn-save-modal" id="saveFieldModal">
          <i class="fas fa-floppy-disk"></i> Save Field
        </button>
      </div>
    </div>
  </div>

  <script>
    window.API_CONFIGURATION_APP = <?= json_encode([
      'id' => $configurationId,
      'itemId' => $itemId,
      'product' => [
        'brandname' => $brandValue,
        'productname' => $productValue,
        'modelname' => $modelValue,
        'version' => $versionValue,
        'batch' => $batchValue,
      ],
      'saveUrl' => site_url('items/saveapi'),
      'csrfHeader' => csrf_header(),
      'readUrl' => null,
      'listUrl' => null,
      'deleteUrl' => $configurationId
        ? site_url('items/delete_api_config/' . $configurationId)
        : null,
      'createUrl' => site_url('items/modal_api/' . $itemId),
      'initialConfiguration' => $initialConfiguration,
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?>;

    (function () {
      const serverApp = window.API_CONFIGURATION_APP;
      let bytes = [];
      let selectedFrameIndex = null;
      let frameCodes = [];
      let frameValueGroups = [];
      let fieldDescriptions = {};
      let byteDescriptions = {};
      let fieldViewModes = {};
      let fullApiValue = "";
      // Database-reconstructed edit rows retain byte ranges even when the
      // original hexadecimal frame value is not present in the edit payload.
      // Keep that layout count separate so restoring the UI never invents or
      // saves a fake hexadecimal value.
      let restoredByteCount = 0;
      let partitionCuts = [];
      let partitionLabels = {};
      let partitionViewModes = {};
      let partitionFieldConfigurations = {};
      let selectedBitGroups = {};
      let partitionsConfirmed = false;
      let suppressCutClick = false;
      let activePartitionFrameIndex = null;
      let framePartitionStates = [];
      let frameConfigurations = [];
      let showSetupWarnings = false;
      let showPartitionWarnings = false;
      let awaitingFrameClick = false;
      let activeFrameType = "command";
      const FRAME_TYPE_LABELS = {
        command: "Command",
        response: "Response",
        error: "Error",
      };
      const frameTypeStores = {
        command: createEmptyFrameTypeStore(),
        response: createEmptyFrameTypeStore(),
        error: createEmptyFrameTypeStore(),
      };
      const LOCAL_STORAGE_KEY = "efactor_api_product_configuration_v3";

      function createEmptyFrameTypeStore() {
        return {
          count: 0,
          frameCodes: [],
          frameValueGroups: [],
          fieldDescriptions: {},
          byteDescriptions: {},
          fieldViewModes: {},
          framePartitionStates: [],
          frameConfigurations: [],
        };
      }

      function getActiveFrameLabel() {
        return FRAME_TYPE_LABELS[activeFrameType] || "Command";
      }

      function saveActiveFrameTypeStore() {
        const store = frameTypeStores[activeFrameType];
        if (!store) return;
        store.count =
          parseInt(document.getElementById("cmdFrameCountInput")?.value, 10) ||
          frameCodes.length ||
          0;
        store.frameCodes = frameCodes;
        store.frameValueGroups = frameValueGroups;
        store.fieldDescriptions = fieldDescriptions;
        store.byteDescriptions = byteDescriptions;
        store.fieldViewModes = fieldViewModes;
        store.framePartitionStates = framePartitionStates;
        store.frameConfigurations = frameConfigurations;
      }

      function loadFrameTypeStore(frameType) {
        const store = frameTypeStores[frameType] || createEmptyFrameTypeStore();
        frameCodes = store.frameCodes || [];
        frameValueGroups = store.frameValueGroups || [];
        fieldDescriptions = store.fieldDescriptions || {};
        byteDescriptions = store.byteDescriptions || {};
        fieldViewModes = store.fieldViewModes || {};
        framePartitionStates = store.framePartitionStates || [];
        frameConfigurations = store.frameConfigurations || [];
        document.getElementById("cmdFrameCountInput").value = store.count || 0;
      }

      function getFrameTypeFillStatus(frameType) {
        const store = frameTypeStores[frameType] || createEmptyFrameTypeStore();
        const count = Math.max(
          0,
          Number(store.count) || store.frameCodes?.length || 0,
        );
        let hasEnteredData = false;
        let completedFrames = 0;

        for (let index = 0; index < count; index++) {
          const partition = store.framePartitionStates?.[index] || {};
          const configuration = store.frameConfigurations?.[index] || {};
          const code = String(
            store.frameCodes?.[index] || partition.fullApiValue || "",
          ).trim();
          const labels = partition.partitionLabels || {};
          const fieldConfigurations =
            partition.partitionFieldConfigurations || {};
          const hasPartitionData =
            Object.keys(labels).length > 0 ||
            Object.keys(fieldConfigurations).length > 0;
          const partitionReady =
            Boolean(partition.partitionsConfirmed) ||
            (Boolean(code) &&
              Object.keys(labels).length > 0 &&
              Object.values(labels).every(Boolean) &&
              Object.keys(fieldConfigurations).length > 0);

          if (
            code ||
            configuration.dataFlowDirection ||
            configuration.multipleUi ||
            hasPartitionData
          ) {
            hasEnteredData = true;
          }

          if (
            code &&
            configuration.dataFlowDirection &&
            configuration.multipleUi &&
            partitionReady
          ) {
            completedFrames++;
          }
        }

        if (count > 0 && completedFrames === count) return "completed";
        if (hasEnteredData) return "in-progress";
        return "pending";
      }

      function updateFlowStageFillState(stage) {
        const frameType = stage.dataset.flowFrameType;
        const status = getFrameTypeFillStatus(frameType);
        const number = stage.querySelector(".flow-stage-number");
        const statusText = stage.querySelector(".flow-stage-status");
        const stepNumber = number?.dataset.stepNumber || "";

        stage.classList.toggle("completed", status === "completed");
        stage.classList.toggle("in-progress", status === "in-progress");
        stage.dataset.fillStatus = status;

        if (number) {
          number.innerHTML =
            status === "completed"
              ? '<i class="fas fa-check" aria-hidden="true"></i>'
              : stepNumber;
        }
        if (statusText) {
          statusText.innerHTML =
            status === "completed"
              ? '<i class="fas fa-circle-check"></i> Filled'
              : status === "in-progress"
                ? '<i class="fas fa-pen"></i> In progress'
                : '<i class="fas fa-circle"></i> Not filled';
        }
        stage.setAttribute(
          "aria-label",
          `${FRAME_TYPE_LABELS[frameType]}: ${
            status === "completed"
              ? "filled"
              : status === "in-progress"
                ? "in progress"
                : "not filled"
          }`,
        );
      }

      function updateFrameTypeUi() {
        const label = getActiveFrameLabel();
        document.getElementById("parameterSetupTitle").innerHTML =
          `<i class="fas fa-sliders-h"></i> Parameter & ${label} Setup`;
        document.querySelectorAll(".frame-type-btn").forEach((button) => {
          const isActive = button.dataset.frameType === activeFrameType;
          button.classList.toggle("active", isActive);
          button.setAttribute("aria-selected", String(isActive));
        });
        document
          .querySelectorAll("[data-flow-frame-type]")
          .forEach((stage) => {
            const isActive =
              stage.dataset.flowFrameType === activeFrameType;
            stage.classList.toggle("active", isActive);
            stage.setAttribute("aria-selected", String(isActive));
            updateFlowStageFillState(stage);
          });
        document.getElementById("generateFramesBtn").innerHTML =
          `<i class="fas fa-sync-alt"></i> Generate ${label} Frames`;
        document.getElementById("activeFramesTitle").textContent =
          `${label.toUpperCase()} FRAMES`;
        document.getElementById("addFrameButtonText").textContent =
          `Add ${label} Frame`;
        document.getElementById("addCommandFrameBtn").title =
          `Add a new ${label.toLowerCase()} frame`;
        document.getElementById("activeFrameHelp").innerHTML =
          `<i class="fas fa-hand-pointer"></i> Click a ${label} Frame card to open Step 2 and enter its full API.`;
        document.getElementById("partitionStepFrameName").textContent =
          `${label} Frame`;
        document.getElementById("popupFrameName").textContent =
          `${label} Frame 1`;
        const fieldDescFrameSelectLabel = document.getElementById(
          "fieldDescFrameSelectLabel",
        );
        if (fieldDescFrameSelectLabel) {
          fieldDescFrameSelectLabel.textContent = `${label} Frame`;
        }
        // The compact modal uses the top flow buttons and intentionally
        // omits the optional second-row count badges.
        const commandTypeCount = document.getElementById("commandTypeCount");
        const responseTypeCount =
          document.getElementById("responseTypeCount");
        const errorTypeCount = document.getElementById("errorTypeCount");
        if (commandTypeCount) {
          commandTypeCount.textContent = frameTypeStores.command.count || 0;
        }
        if (responseTypeCount) {
          responseTypeCount.textContent = frameTypeStores.response.count || 0;
        }
        if (errorTypeCount) {
          errorTypeCount.textContent = frameTypeStores.error.count || 0;
        }
      }

      function switchFrameType(nextType, revealFullEditor = false) {
        if (!FRAME_TYPE_LABELS[nextType]) return;
        captureActiveFramePartitionState();
        saveActiveFrameTypeStore();
        activeFrameType = nextType;
        loadFrameTypeStore(nextType);
        selectedFrameIndex = null;
        activePartitionFrameIndex = null;
        fullApiValue = "";
        restoredByteCount = 0;
        partitionCuts = [];
        partitionLabels = {};
        partitionViewModes = {};
        partitionFieldConfigurations = {};
        selectedBitGroups = {};
        partitionsConfirmed = false;
        document.getElementById("fullApiInput").value = "";
        document.getElementById("byteDetailPopup").style.display = "none";
        document
          .getElementById("apiPartitionSection")
          .classList.remove("frame-partition-active");
        const framesContainer = document.getElementById(
          "commandFramesContainer",
        );
        framesContainer.replaceChildren();
        framesContainer.dataset.visibleFrameType = nextType;
        framesContainer.setAttribute(
          "aria-label",
          `${FRAME_TYPE_LABELS[nextType]} frames only`,
        );
        updateFrameTypeUi();
        const count = frameTypeStores[nextType].count || 0;
        if (count > 0) {
          generateCommandFrames(count, true);

          /*
           * Command, Response and Error share the same complete partition
           * workspace.  Previously a type switch rendered its frame cards but
           * left that workspace hidden until the user clicked a card.  Reveal
           * the first frame immediately so every flow tab displays the full
           * page and restores its saved partition/field configuration.
           */
          const firstConfiguredFrame = framePartitionStates.findIndex(
            (state, index) =>
              Boolean(
                frameCodes[index] ||
                state?.fullApiValue ||
                state?.partitionCuts?.length ||
                Object.keys(state?.partitionLabels || {}).length ||
                Object.keys(
                  state?.partitionFieldConfigurations || {},
                ).length,
              ),
          );
          const frameIndex =
            firstConfiguredFrame >= 0 ? firstConfiguredFrame : 0;

          activePartitionFrameIndex = frameIndex;
          selectedFrameIndex = frameIndex;
          loadFramePartitionState(frameIndex);

          const partitionSection =
            document.getElementById("apiPartitionSection");
          partitionSection?.classList.add("frame-partition-active");
          document
            .querySelectorAll(
              "#commandFramesContainer .command-frame-card",
            )
            .forEach((card, index) => {
              card.classList.toggle("selected", index === frameIndex);
            });

          const fieldFrameSelect =
            document.getElementById("fieldDescFrameSelect");
          if (
            fieldFrameSelect?.querySelector(
              `option[value="${frameIndex}"]`,
            )
          ) {
            fieldFrameSelect.value = String(frameIndex);
          }
          renderFieldDescription();

          if (revealFullEditor && partitionSection) {
            partitionSection.classList.add("section-highlight");
            partitionSection.scrollIntoView({
              behavior: "smooth",
              block: "start",
            });
            setTimeout(
              () =>
                partitionSection.classList.remove("section-highlight"),
              1200,
            );
          }
        } else {
          updateCommandFrameCount();
          updateFieldDescFrameSelect();
          renderFieldDescription();
        }
        renderApiPartitionEditor();
      }

      const UI_TYPE_OPTIONS = [
        {
          value: "none",
          label: "None",
          hint: "No UI control",
          preview: '<i class="fas fa-ban"></i>',
        },
        {
          value: "text",
          label: "Text",
          hint: "Read-only text",
          preview: '<i class="fas fa-font"></i>',
        },
        {
          value: "toggle switch",
          label: "Toggle Switch",
          hint: "On / Off switch",
          preview: '<span class="ui-preview-switch" aria-hidden="true"></span>',
        },
        {
          value: "notification",
          label: "Notification",
          hint: "Alert badge",
          preview: '<i class="fas fa-bell"></i>',
        },
        {
          value: "console",
          label: "Console",
          hint: "Log / console view",
          preview: '<i class="fas fa-terminal"></i>',
        },
        {
          value: "enter text",
          label: "Enter Text",
          hint: "Text input field",
          preview: '<span class="ui-preview-input" aria-hidden="true"></span>',
        },
        {
          value: "slider",
          label: "Slider",
          hint: "Value slider",
          preview: '<span class="ui-preview-slider" aria-hidden="true"></span>',
        },
        {
          value: "brightness slider",
          label: "Brightness Slider",
          hint: "Brightness control",
          preview: '<i class="fas fa-sun"></i>',
        },
        {
          value: "airflow slider",
          label: "Airflow Slider",
          hint: "Airflow control",
          preview: '<i class="fas fa-wind"></i>',
        },
        {
          value: "cct slider",
          label: "CCT Slider",
          hint: "Color temperature",
          preview: '<i class="fas fa-temperature-half"></i>',
        },
        {
          value: "saturation slider",
          label: "Saturation Slider",
          hint: "Color saturation",
          preview: '<i class="fas fa-droplet"></i>',
        },
        {
          value: "hue slider",
          label: "Hue Slider",
          hint: "Hue strip",
          preview: '<span class="ui-preview-slider" aria-hidden="true"></span>',
        },
        {
          value: "hue slider custom",
          label: "Hue Slider Custom",
          hint: "Custom hue strip",
          preview: '<i class="fas fa-palette"></i>',
        },
        {
          value: "hue circle",
          label: "Hue Circle",
          hint: "Color wheel",
          preview: '<span class="ui-preview-circle" aria-hidden="true"></span>',
        },
        {
          value: "push button",
          label: "Push Button",
          hint: "Momentary press",
          preview: '<span class="ui-preview-btn" aria-hidden="true"></span>',
        },
        {
          value: "dropdown",
          label: "Dropdown",
          hint: "Select list",
          preview: '<span class="ui-preview-dropdown" aria-hidden="true"></span>',
        },
        {
          value: "display",
          label: "Display",
          hint: "Value display",
          preview: '<i class="fas fa-tv"></i>',
        },
        {
          value: "button",
          label: "Button",
          hint: "Action button",
          preview: '<span class="ui-preview-btn" aria-hidden="true"></span>',
        },
        {
          value: "auto trigger with timer",
          label: "Auto Trigger with Timer",
          hint: "Timed trigger",
          preview: '<i class="fas fa-clock"></i>',
        },
        {
          value: "toast display",
          label: "Toast Display",
          hint: "Toast message",
          preview: '<span class="ui-preview-toast" aria-hidden="true"></span>',
        },
      ];

      function renderUiTypePicker(selectedValue) {
        const picker = document.getElementById("uiTypePicker");
        const select = document.getElementById("fUiType");
        if (!picker || !select) return;
        const value = selectedValue || select.value || "none";
        select.value = value;
        picker.innerHTML = UI_TYPE_OPTIONS.map(
          (option) => `
              <button type="button" class="ui-type-option${option.value === value ? " active" : ""}" role="option" aria-selected="${option.value === value}" data-value="${option.value}">
                <span class="ui-preview">${option.preview}</span>
                <span class="ui-preview-label">
                  <strong>${option.label}</strong>
                  <small>${option.hint}</small>
                </span>
              </button>
            `,
        ).join("");
        picker.querySelectorAll(".ui-type-option").forEach((button) => {
          button.addEventListener("click", function () {
            const nextValue = this.dataset.value;
            select.value = nextValue;
            renderUiTypePicker(nextValue);
            select.dispatchEvent(new Event("change", { bubbles: true }));
            this.closest(".form-group")?.classList.remove("field-invalid");
            select.setAttribute("aria-invalid", "false");
          });
        });
      }

      const FIELD_DESC_OPTIONS = [
        "header",
        "data",
        "payload",
        "checksum",
        "terminator",
        "commandid",
        "function code",
        "terminator / crc",
        "status",
        "error code",
        "length",
        "version",
        "reserved",
        "timestamp",
        "device id",
      ];
      const API_PARTITION_OPTIONS = [
        { value: "commandId", label: "Command ID" },
        { value: "functionCode", label: "Function Code" },
        { value: "data", label: "Data" },
        { value: "terminatorCrc", label: "Terminator / CRC" },
      ];
      /*
       * Keep partition colours independent from the page theme.
       * These distinct colours make adjacent byte partitions easy to identify.
       */
      const PARTITION_COLORS = [
        { color: "#2563EB", soft: "#EAF2FF", text: "#1746A2" },
        { color: "#7C3AED", soft: "#F2EBFF", text: "#5B21B6" },
        { color: "#0F8A75", soft: "#E5F8F4", text: "#0B6658" },
        { color: "#D97706", soft: "#FFF3DF", text: "#9A4C00" },
        { color: "#DB2777", soft: "#FDEAF3", text: "#9D174D" },
        { color: "#087EA4", soft: "#E6F7FB", text: "#075B75" },
        { color: "#6B8E23", soft: "#F1F8E4", text: "#496817" },
        { color: "#C2413B", soft: "#FDECEA", text: "#8D2925" },
      ];

      const apiPartitionSection = document.getElementById(
        "apiPartitionSection",
      );
      const commandFramesSection = document.getElementById(
        "commandFramesSection",
      );
      commandFramesSection.insertAdjacentElement(
        "afterend",
        apiPartitionSection,
      );
      apiPartitionSection.classList.remove("frame-partition-active");

      function clonePartitionObject(value) {
        return JSON.parse(JSON.stringify(value || {}));
      }

      function updateSaveBadge(state, message) {
        const badge = document.getElementById("saveStatusBadge");
        if (!badge) return;
        const icon =
          state === "saved"
            ? "fa-circle-check"
            : state === "error"
              ? "fa-circle-exclamation"
              : state === "unsaved"
                ? "fa-pen-to-square"
                : "fa-cloud-arrow-down";
        badge.className = `save-status-badge${state ? ` ${state}` : ""}`;
        badge.innerHTML = `<i class="fas ${icon}"></i><span>${message}</span>`;
        void badge.offsetWidth;
        badge.classList.add("status-change");
      }

      function markConfigurationUnsaved() {
        updateSaveBadge("unsaved", "Unsaved changes");
        updateFrameTypeUi();
      }

      function collectCurrentEntryData() {
        const entryData = {};
        document
          .querySelectorAll("input[id], select[id], textarea[id]")
          .forEach((control) => {
            if (
              control.type === "file" ||
              control.type === "button" ||
              control.type === "submit"
            ) {
              return;
            }

            if (control.type === "checkbox") {
              entryData[control.id] = control.checked;
              return;
            }

            if (control.type === "radio") {
              if (control.checked) {
                entryData[control.name || control.id] = control.value;
              }
              return;
            }

            entryData[control.id] = control.value;
          });
        return entryData;
      }

      function schemaDirectionLabel(value) {
        return value === "device_to_server"
          ? "Device to App/Server"
          : "App/Server to Device";
      }

      function normalizeDirectionValue(value) {
        const normalized = String(value || "").trim().toLowerCase();
        if (
          normalized === "device_to_server" ||
          normalized.includes("device to")
        ) {
          return "device_to_server";
        }
        if (
          normalized === "server_to_device" ||
          normalized.includes("server to") ||
          normalized.includes("app/server to")
        ) {
          return "server_to_device";
        }
        return "";
      }

      function addSchemaRowAliases(row, prefix) {
        const aliases = {};
        [
          "bitwiseMode",
          "startBytePosition",
          "endBytePosition",
          "numberOfBytes",
          "bitSize",
          "fieldDescription",
          "dataType",
          "valueDefinition",
          "displayValueMin",
          "displayValueMax",
          "valueInHexMin",
          "valueInHexMax",
          "uiType",
          "uiId",
          "asperui",
          "sno_asperui",
          "asperuivalue",
          "uiPageNo",
          "uiDescriptionInOut",
          "sliderStepValue",
          "defaultvalue",
          "developerNotes",
          "notificationEnable",
          "notificationMessage",
          "scaling",
        ].forEach((key) => {
          aliases[`${prefix}_${key}`] = row[key];
        });
        return { ...row, ...aliases };
      }

      function buildEditorConfiguration() {
        captureActiveFramePartitionState();
        saveActiveFrameTypeStore();

        function buildFramesForType(frameType) {
          const store = frameTypeStores[frameType];
          return Array.from({ length: store.count || 0 }, (_, index) => ({
            frameNumber: index + 1,
            frameType,
            frameCode: store.frameCodes[index] || "",
            [`${frameType}Code`]: store.frameCodes[index] || "",
            dataFlowDirection:
              store.frameConfigurations[index]?.dataFlowDirection || "",
            multipleUi: store.frameConfigurations[index]?.multipleUi || "",
            valueGroups: clonePartitionObject(
              store.frameValueGroups[index] || [],
            ),
            partition: clonePartitionObject(
              store.framePartitionStates[index] || {},
            ),
            fieldDescriptions: clonePartitionObject(
              store.fieldDescriptions[index] || {},
            ),
            byteDescriptions: clonePartitionObject(
              store.byteDescriptions[index] || {},
            ),
            fieldViewModes: clonePartitionObject(
              store.fieldViewModes[index] || {},
            ),
          }));
        }

        const commandFrames = buildFramesForType("command");
        const responseFrames = buildFramesForType("response");
        const errorFrames = buildFramesForType("error");
        return {
          storageVersion: 3,
          savedAt: new Date().toISOString(),
          entryData: collectCurrentEntryData(),
          form: {
            parameterName: document.getElementById("paramNameInput").value,
            commandFrameCount: frameTypeStores.command.count || 0,
            responseFrameCount: frameTypeStores.response.count || 0,
            errorFrameCount: frameTypeStores.error.count || 0,
            triggerUi:
              document.getElementById("triggerUiSelect")?.value || "",
          },
          activeFrameType,
          commandFrames,
          responseFrames,
          errorFrames,
          bytes: clonePartitionObject(bytes),
          frameTypeStores: clonePartitionObject(frameTypeStores),
        };
      }

      function buildSchemaFrames(frameType, prefix, frameTypeName) {
        const store = frameTypeStores[frameType];
        const parameterName =
          document.getElementById("paramNameInput").value.trim();

        return Array.from({ length: store.count || 0 }, (_, frameIndex) => {
          const partition = store.framePartitionStates[frameIndex] || {};
          const labels = partition.partitionLabels || {};
          const configurations =
            partition.partitionFieldConfigurations || {};
          const frameConfiguration =
            store.frameConfigurations[frameIndex] || {};
          const frameCode =
            store.frameCodes[frameIndex] ||
            partition.fullApiValue ||
            "";

          const rows = Object.keys(labels).map((key, rowIndex) => {
            const boundaries = key.split("-").map(Number);
            const start = Number.isFinite(boundaries[0])
              ? boundaries[0]
              : rowIndex;
            const end = Number.isFinite(boundaries[1])
              ? boundaries[1]
              : start + 1;
            const field = configurations[key]?.byte || {};
            const numberOfBytes = Math.max(1, end - start);
            const zeroHex = `0x${"00".repeat(numberOfBytes)}`;
            const maxHex = `0x${"FF".repeat(numberOfBytes)}`;
            const bitwise = Object.entries(configurations[key] || {})
              .filter(
                ([configurationKey, value]) =>
                  !configurationKey.startsWith("_") &&
                  configurationKey !== "byte" &&
                  value &&
                  typeof value === "object",
              )
              .map(([configurationKey, value]) => ({
                startBytePosition_1: configurationKey,
                endBytePosition_1: configurationKey,
                startBit: Array.isArray(value.bitPositions)
                  ? value.bitPositions[0]
                  : value.bitPosition,
                endBit: Array.isArray(value.bitPositions)
                  ? value.bitPositions[value.bitPositions.length - 1]
                  : value.bitPosition,
                numberOfBytes_1: Array.isArray(value.bitPositions)
                  ? value.bitPositions.length
                  : 1,
                fieldDescription_1: labels[key] || "",
                dataType_1: value.dataType || "",
                valueDefinition_1: value.valueDef || "",
                uiType_1: value.uiType || "",
                uiDescriptionInOut_1: value.uiInOut || "",
                scaling_1: value.scalling || "",
                displayValueMin_1: value.displayValueMin || "",
                displayValueMax_1: value.displayValueMax || "",
                valueInHexMin_1: value.valueInHexMin || "",
                valueInHexMax_1: value.valueInHexMax || "",
                uiId_1: value.uiId || "",
                asperui_1: value.asperui || "",
                sno_asperui_1: value.sno_asperui || "",
                asperuivalue_1: value.asperuivalue || "",
                uiPageNo_1: value.uiPageNo || "",
                sliderStepValue_1: value.sliderStepValue || "",
                defaultvalue_1: value.defaultvalue || "",
                developerNotes_1: value.developerNotes || "",
                notificationEnable_1: value.notificationEnable || "No",
                notificationMessage_1: value.notificationMessage || "",
              }));

            const row = {
              bitwise: bitwise.length ? bitwise : null,
              startBytePosition: String(start),
              endBytePosition: String(Math.max(start, end - 1)),
              numberOfBytes: String(numberOfBytes),
              bitSize: String(numberOfBytes * 8),
              fieldDescription: labels[key] || "",
              dataType: field.dataType || "",
              valueDefinition: field.valueDef || "",
              displayValueMin: field.displayValueMin || "0",
              displayValueMax: field.displayValueMax || "0",
              valueInHexMin: field.valueInHexMin || zeroHex,
              valueInHexMax: field.valueInHexMax || maxHex,
              uiType: field.uiType || "",
              uiId: field.uiId || "1",
              asperui: field.asperui || "1",
              sno_asperui: field.sno_asperui || "1",
              asperuivalue: field.asperuivalue || "1",
              uiPageNo: field.uiPageNo || "",
              uiDescriptionInOut: field.uiInOut || "",
              sliderStepValue: field.sliderStepValue || "",
              defaultvalue: field.defaultvalue || "",
              developerNotes: field.developerNotes || "",
              notificationEnable: field.notificationEnable || "No",
              notificationMessage: field.notificationMessage || "",
              scaling: field.scalling || "",
              bitwiseMode:
                partition.partitionViewModes?.[key] === "bitwise"
                  ? "Bitwise"
                  : "Bytewise",
            };
            return addSchemaRowAliases(row, prefix);
          });

          const direction = schemaDirectionLabel(
            frameConfiguration.dataFlowDirection,
          );
          const size = String(
            Math.max(1, Math.ceil(frameCode.length / 2)),
          );
          const codeProperty = `${frameType}Code`;

          return {
            parameterName,
            direction,
            size,
            triggerOptions: "None",
            frameType: frameTypeName,
            multipleui: frameConfiguration.multipleUi || "",
            triggerui:
              document.getElementById("triggerUiSelect")?.value || "false",
            frameCode,
            [codeProperty]: frameCode,
            valueGroups: clonePartitionObject(
              store.frameValueGroups[frameIndex] || [],
            ),
            partition: clonePartitionObject(partition),
            fieldDescriptions: clonePartitionObject(
              store.fieldDescriptions[frameIndex] || {},
            ),
            byteDescriptions: clonePartitionObject(
              store.byteDescriptions[frameIndex] || {},
            ),
            fieldViewModes: clonePartitionObject(
              store.fieldViewModes[frameIndex] || {},
            ),
            meta: {
              parametername: parameterName,
              noofrows: String(rows.length),
              dataflowdirection: direction,
              frametype: frameTypeName,
              framesize_bytes: size,
              frametrigger: "None",
              multipleui: frameConfiguration.multipleUi || "",
              triggerui:
                document.getElementById("triggerUiSelect")?.value || "",
            },
            rows,
          };
        });
      }

      function buildLocalConfiguration() {
        captureActiveFramePartitionState();
        saveActiveFrameTypeStore();

        const existing = serverApp.initialConfiguration || {};
        const commands = buildSchemaFrames("command", "cmd", "COMMAND");
        const responses = buildSchemaFrames("response", "resp", "RESPONSE");
        const errors = buildSchemaFrames("error", "error", "ERROR");
        const parameterName =
          document.getElementById("paramNameInput").value.trim();

        return {
          product: {
            itemid: String(serverApp.itemId),
            brandname: serverApp.product?.brandname || "",
            productname: serverApp.product?.productname || "",
            modelname: serverApp.product?.modelname || "",
            date:
              existing.product?.date ||
              new Date().toISOString().slice(0, 10),
            parameter_name: parameterName,
            page_number: existing.product?.page_number || "",
            sequence_number: existing.product?.sequence_number || "",
            command_frame_count: String(commands.length),
            response_frame_count: String(responses.length),
            error_frame_count: String(errors.length),
            page_names: existing.product?.page_names || "",
            product_description:
              existing.product?.product_description || "",
            editor_bytes: clonePartitionObject(bytes),
          },
          commands,
          responses,
          errors,
        };
      }

      let latestSavedJson = "";

      function downloadConfigurationJson(configurationOrJson) {
        const jsonText =
          typeof configurationOrJson === "string"
            ? configurationOrJson
            : JSON.stringify(configurationOrJson, null, 2);
        if (!jsonText) return;

        latestSavedJson = jsonText;
        const blob = new Blob([jsonText], {
          type: "application/json",
        });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        const parameterName =
          document.getElementById("paramNameInput")?.value?.trim() ||
          "configuration";
        link.href = url;
        link.download = `${parameterName.replace(
          /[^a-z0-9_-]+/gi,
          "_",
        )}_command-response-error.json`;
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.setTimeout(() => URL.revokeObjectURL(url), 0);
      }

      function openJsonSavePopup(configuration) {
        const overlay = document.getElementById("jsonSaveOverlay");
        const output = document.getElementById("savedJsonOutput");
        const preview = document.getElementById("savedConfigurationPreview");
        const status = document.getElementById("jsonCopyStatus");
        const summary = document.getElementById("jsonSaveSummary");
        if (!overlay || !output || !preview || !status || !summary) {
          alert(JSON.stringify(configuration, null, 2));
          return;
        }
        latestSavedJson = JSON.stringify(configuration, null, 2);
        output.textContent = latestSavedJson;
        renderConfigurationPreview(configuration, preview);
        status.textContent = "";
        const entryCount = Object.keys(
          buildEditorConfiguration().entryData || {},
        ).length;
        const commandCount = configuration.commands?.length || 0;
        const responseCount = configuration.responses?.length || 0;
        const errorCount = configuration.errors?.length || 0;
        const frameCount = commandCount + responseCount + errorCount;
        summary.textContent = `${entryCount} entries and ${frameCount} frames (${commandCount} command, ${responseCount} response, ${errorCount} error) were stored in JSON format.`;
        overlay.classList.add("open");
        overlay.style.display = "flex";
        overlay.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
        document.getElementById("doneJsonSave")?.focus();
      }

      function renderConfigurationPreview(configuration, container) {
        container.replaceChildren();
        const product = configuration?.product || {};
        const commandFrames = Array.isArray(configuration?.commands)
          ? configuration.commands
          : [];
        const errorFrames = Array.isArray(configuration?.errors)
          ? configuration.errors
          : [];
        const responseFrames = Array.isArray(configuration?.responses)
          ? configuration.responses
          : [];

        const element = (tag, className, textValue) => {
          const node = document.createElement(tag);
          if (className) node.className = className;
          if (textValue !== undefined && textValue !== null) {
            node.textContent = String(textValue);
          }
          return node;
        };
        const value = (...candidates) => {
          const found = candidates.find(
            (candidate) =>
              candidate !== undefined &&
              candidate !== null &&
              String(candidate).trim() !== "",
          );
          return found === undefined ? "N/A" : String(found);
        };

        const summaryCard = element("section", "preview-summary-card");
        const summaryHead = element("div", "preview-summary-head");
        const headingCopy = element("div");
        headingCopy.append(
          element("h4", "", "Full Product Configuration Summary"),
          element(
            "p",
            "",
            "Organized by product, frame and field values.",
          ),
        );
        const badgeWrap = element("div", "preview-count-badges");
        [
          ["Command Frames", commandFrames.length],
          ["Response Frames", responseFrames.length],
          ["Error Frames", errorFrames.length],
        ].forEach(([label, count]) => {
          badgeWrap.append(
            element("span", "preview-count-badge", `${label}: ${count}`),
          );
        });
        summaryHead.append(headingCopy, badgeWrap);

        const productGrid = element("div", "preview-product-grid");
        const productFields = [
          ["Database Config ID", serverApp.id],
          ["Item ID", product.itemid],
          ["Brand Name", product.brandname, serverApp.product?.brandname],
          ["Product Name", product.productname, serverApp.product?.productname],
          ["Model Name", product.modelname, serverApp.product?.modelname],
          ["Version", product.version, serverApp.product?.version],
          ["Batch", product.batch, serverApp.product?.batch],
          ["Date", product.date],
          ["Parameter Name", product.parameter_name],
          ["Page Number", product.page_number],
          ["Sequence Number", product.sequence_number],
          ["Page Names", product.page_names],
          ["Description", product.product_description],
        ];
        productFields.forEach((field, index) => {
          const fieldCard = element(
            "div",
            `preview-product-field${index === productFields.length - 1 ? " wide" : ""}`,
          );
          fieldCard.append(
            element("span", "preview-product-label", field[0]),
            element(
              "span",
              "preview-product-value",
              value(...field.slice(1)),
            ),
          );
          productGrid.append(fieldCard);
        });
        summaryCard.append(summaryHead, productGrid);
        container.append(summaryCard);

        const fieldRows = (frame, row, rowIndex) => {
          const bitwiseRows = Array.isArray(row?.bitwise)
            ? row.bitwise
            : [];
          const bitStart = value(
            row?.startBit,
            bitwiseRows[0]?.startBit,
          );
          const bitEnd = value(
            row?.endBit,
            bitwiseRows[0]?.endBit,
          );
          const range = (start, end) =>
            start === "N/A" && end === "N/A"
              ? "N/A"
              : `${start} : ${end}`;

          return [
            ["Parameter Name", value(frame?.parameterName, frame?.meta?.parametername, product.parameter_name)],
            ["Data Flow Direction", value(frame?.direction, frame?.meta?.dataflowdirection)],
            ["Frame Type", value(frame?.frameType, frame?.meta?.frametype)],
            ["Frame Size Bytes", value(frame?.size, frame?.meta?.framesize_bytes)],
            ["Rows", value(frame?.rows?.length)],
            ["System Row ID", value(row?.id, row?.rowId, rowIndex + 1)],
            ["Row Sequence", rowIndex + 1],
            ["Bit Field Sequence", value(row?.bitFieldSequence, row?.bit_field_sequence)],
            ["Description", value(row?.fieldDescription, row?.description)],
            ["Byte Range", range(value(row?.startBytePosition), value(row?.endBytePosition))],
            ["Bit Range", range(bitStart, bitEnd)],
            ["No. Bytes / Bits", value(row?.numberOfBytes, row?.numberOfBytes_1, row?.bitSize)],
            ["Bitwise Mode", value(row?.bitwiseMode)],
            ["Data Type", value(row?.dataType)],
            ["Value Definition", value(row?.valueDefinition)],
            ["Display Value Min", value(row?.displayValueMin)],
            ["Display Value Max", value(row?.displayValueMax)],
            ["Value In Hex Min", value(row?.valueInHexMin)],
            ["Value In Hex Max", value(row?.valueInHexMax)],
            ["UI Type", value(row?.uiType)],
            ["UI ID", value(row?.uiId)],
            ["As Per UI", value(row?.asperui)],
            ["UI Serial No.", value(row?.sno_asperui)],
            ["UI Values", value(row?.asperuivalue)],
            ["UI Page No.", value(row?.uiPageNo)],
            ["UI In/Out", value(row?.uiDescriptionInOut)],
            ["Slider Step Value", value(row?.sliderStepValue)],
            ["Default Value", value(row?.defaultvalue)],
            ["Developer Notes", value(row?.developerNotes)],
            ["Notification Enable", value(row?.notificationEnable)],
            ["Notification Message", value(row?.notificationMessage)],
            ["Scaling", value(row?.scaling)],
          ];
        };

        const appendFrameSection = (title, frames) => {
          const section = element("section", "preview-frame-section");
          const titleBar = element("div", "preview-frame-section-title");
          titleBar.append(
            element("h4", "", title),
            element("span", "", `${frames.length} item(s)`),
          );
          section.append(titleBar);
          const frameList = element("div", "preview-frame-list");

          if (!frames.length) {
            frameList.append(
              element("div", "preview-empty", `No ${title.toLowerCase()} were saved.`),
            );
          }

          frames.forEach((frame, frameIndex) => {
            const frameCard = element("article", "preview-frame-card");
            const frameHead = element("div", "preview-frame-card-head");
            frameHead.append(
              element("span", "", `Frame ${String(frameIndex + 1).padStart(2, "0")}`),
              element(
                "span",
                "preview-frame-code",
                value(
                  frame?.frameCode,
                  frame?.commandCode,
                  frame?.responseCode,
                  frame?.errorCode,
                ),
              ),
            );
            frameCard.append(frameHead);
            const rows =
              Array.isArray(frame?.rows) && frame.rows.length
                ? frame.rows
                : [{}];
            rows.forEach((row, rowIndex) => {
              frameCard.append(
                element(
                  "div",
                  "preview-row-caption",
                  `Partition / Field ${rowIndex + 1}`,
                ),
              );
              const table = element("table", "preview-field-table");
              const body = document.createElement("tbody");
              fieldRows(frame, row, rowIndex).forEach(([label, fieldValue]) => {
                const tableRow = document.createElement("tr");
                tableRow.append(
                  element("th", "", label),
                  element("td", "", value(fieldValue)),
                );
                body.append(tableRow);
              });
              table.append(body);
              frameCard.append(table);
            });
            frameList.append(frameCard);
          });
          section.append(frameList);
          container.append(section);
        };

        appendFrameSection("Command Frames", commandFrames);
        appendFrameSection("Error Frames", errorFrames);
        appendFrameSection("Response Frames", responseFrames);
      }

      function closeJsonSavePopup() {
        const overlay = document.getElementById("jsonSaveOverlay");
        if (!overlay) return;
        overlay.classList.remove("open");
        overlay.style.display = "";
        overlay.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
        moveToCommandResponseErrorFlow(true);
      }

      function moveToCommandResponseErrorFlow(focusActiveStage = false) {
        const flow = document.getElementById("commandResponseErrorFlow");
        if (!flow) return;
        flow.classList.add("section-highlight");
        flow?.scrollIntoView({ behavior: "smooth", block: "center" });
        if (focusActiveStage) {
          flow
            .querySelector(".flow-stage.active")
            ?.focus({ preventScroll: true });
        }
        window.setTimeout(
          () => flow.classList.remove("section-highlight"),
          1200,
        );
      }

      function csrfRequestHeaders() {
        const headers = {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        };
        const csrfHash =
          document.querySelector('meta[name="csrf-token-hash"]')?.content ||
          "";
        if (csrfHash && serverApp.csrfHeader) {
          headers[serverApp.csrfHeader] = csrfHash;
        }
        return headers;
      }

      function updateCsrfToken(result) {
        const nextHash =
          result?.csrfHash ||
          result?.csrf_hash ||
          result?.csrf?.hash ||
          "";
        const meta = document.querySelector('meta[name="csrf-token-hash"]');
        if (nextHash && meta) meta.content = nextHash;
      }

      async function persistConfiguration(message) {
        const configuration = buildLocalConfiguration();
        const headers = csrfRequestHeaders();
        delete headers["Content-Type"];
        if (serverApp.id) {
          headers["X-Configuration-Id"] = String(serverApp.id);
        }

        const product = configuration.product || {};
        const formData = new FormData();
        formData.append("full_config_json", JSON.stringify(configuration));
        formData.append("config_id", serverApp.id ? String(serverApp.id) : "0");
        formData.append("itemid", product.itemid || String(serverApp.itemId || ""));
        formData.append("brandname", product.brandname || "");
        formData.append("productname", product.productname || "");
        formData.append("modelname", product.modelname || "");
        formData.append("date", product.date || "");
        formData.append("parameter_name", product.parameter_name || "");
        formData.append("page_number", product.page_number || "");
        formData.append("sequence_number", product.sequence_number || "");
        formData.append(
          "command_frame_count",
          product.command_frame_count || String(configuration.commands?.length || 0),
        );
        formData.append(
          "response_frame_count",
          product.response_frame_count || String(configuration.responses?.length || 0),
        );
        formData.append(
          "error_frame_count",
          product.error_frame_count || String(configuration.errors?.length || 0),
        );
        formData.append("page_names", product.page_names || "");
        formData.append(
          "product_description",
          product.product_description || "",
        );

        const response = await fetch(serverApp.saveUrl, {
          method: "POST",
          headers,
          body: formData,
          credentials: "same-origin",
        });
        const result = await response.json().catch(() => null);
        if (!response.ok || !result?.success) {
          throw new Error(
            result?.message ||
              `Database save failed (HTTP ${response.status}).`,
          );
        }

        updateCsrfToken(result);
        serverApp.id = Number(
          result.data?.configId ||
          result.data?.config_id ||
          result.config_id ||
          result.id ||
          serverApp.id,
        );
        serverApp.initialConfiguration = configuration;
        if (serverApp.id) {
          serverApp.deleteUrl =
            `${<?= json_encode(site_url('items/delete_api_config')) ?>}/${serverApp.id}`;
          const deleteButton = document.getElementById(
            "deleteConfigurationBtn",
          );
          if (deleteButton) deleteButton.style.display = "";
        }

        const time = new Date().toLocaleTimeString([], {
          hour: "2-digit",
          minute: "2-digit",
        });
        updateSaveBadge(
          "saved",
          `${result.message || message || "Configuration saved"} · ID ${serverApp.id} · ${time}`,
        );
        return configuration;
      }

      async function handleSaveConfiguration(event) {
        event?.preventDefault();
        event?.stopPropagation();

        captureActiveFramePartitionState();
        saveActiveFrameTypeStore();
        let firstIncomplete = null;
        Object.keys(FRAME_TYPE_LABELS).some((frameType) => {
          const store = frameTypeStores[frameType];
          const index = store.frameConfigurations
            .slice(0, store.count)
            .findIndex(
              (configuration) =>
                !configuration?.dataFlowDirection ||
                !configuration?.multipleUi,
            );
          if (index >= 0) {
            firstIncomplete = { frameType, index };
            return true;
          }
          return false;
        });
        if (firstIncomplete) {
          if (activeFrameType !== firstIncomplete.frameType) {
            switchFrameType(firstIncomplete.frameType);
          }
          const index = firstIncomplete.index;
          if (!frameConfigurations[index]?.dataFlowDirection) {
            document
              .getElementById(`frameDirection${index}`)
              ?.classList.add("frame-field-error");
          }
          if (!frameConfigurations[index]?.multipleUi) {
            document
              .getElementById(`frameMultipleUi${index}`)
              ?.classList.add("frame-field-error");
          }
          alert(
            `Select Data Flow Direction and Multiple UI for ${getActiveFrameLabel()} Frame ${index + 1} before saving.`,
          );
          document
            .getElementById(`frameDirection${index}`)
            ?.closest(".command-frame-card")
            ?.scrollIntoView({ behavior: "smooth", block: "center" });
          return;
        }

        const saveButton = document.getElementById("saveBtn");
        if (saveButton) {
          saveButton.disabled = true;
          saveButton.setAttribute("aria-busy", "true");
        }

        try {
          const savedConfiguration = await persistConfiguration(
            "Configuration saved",
          );
          // Download the complete saved configuration automatically. The JSON
          // includes every Command, Response and Error frame with its
          // partition, bytewise and bitwise configuration.
          downloadConfigurationJson(savedConfiguration);
          moveToCommandResponseErrorFlow(true);

          // Let the user see the page move to the Command/Response/Error flow
          // before the existing saved-JSON confirmation popup is displayed.
          const popupDelay = window.matchMedia(
            "(prefers-reduced-motion: reduce)",
          ).matches
            ? 0
            : 550;
          window.setTimeout(
            () => openJsonSavePopup(savedConfiguration),
            popupDelay,
          );
        } catch (error) {
          console.error("Unable to save the API configuration.", error);
          updateSaveBadge("error", "Database save failed");
          alert(error.message || "The configuration could not be saved.");
        } finally {
          if (saveButton) {
            saveButton.disabled = false;
            saveButton.removeAttribute("aria-busy");
          }
        }
      }

      const saveConfigurationButton = document.getElementById("saveBtn");
      if (saveConfigurationButton) {
        saveConfigurationButton.addEventListener(
          "click",
          handleSaveConfiguration,
        );
      }

      document.getElementById("backBtn")?.addEventListener("click", function () {
        if (window.history.length > 1) {
          window.history.back();
          return;
        }
        window.location.assign(serverApp.listUrl || serverApp.createUrl);
      });

      document
        .getElementById("closeJsonSave")
        .addEventListener("click", closeJsonSavePopup);
      document
        .getElementById("doneJsonSave")
        .addEventListener("click", closeJsonSavePopup);
      document
        .getElementById("jsonSaveOverlay")
        .addEventListener("click", function (event) {
          if (event.target === this) closeJsonSavePopup();
        });
      document
        .getElementById("copySavedJson")
        .addEventListener("click", async function () {
          const status = document.getElementById("jsonCopyStatus");
          try {
            await navigator.clipboard.writeText(latestSavedJson);
            status.textContent = "JSON copied successfully.";
          } catch (error) {
            const selection = window.getSelection();
            const range = document.createRange();
            range.selectNodeContents(
              document.getElementById("savedJsonOutput"),
            );
            selection.removeAllRanges();
            selection.addRange(range);
            status.textContent = "JSON selected. Press Ctrl+C to copy.";
          }
        });
      document
        .getElementById("downloadSavedJson")
        .addEventListener("click", function () {
          downloadConfigurationJson(latestSavedJson);
        });

      function normalizeConfigurationPayload(configuration) {
        const parseStoredJson = (value) => {
          if (value && typeof value === "object") return value;
          if (typeof value !== "string" || !value.trim()) return null;
          let candidate = value.trim();
          for (let attempt = 0; attempt < 4; attempt += 1) {
            try {
              const parsed = JSON.parse(candidate);
              if (parsed && typeof parsed === "object") return parsed;
              if (typeof parsed !== "string" || !parsed.trim()) return null;
              candidate = parsed.trim();
            } catch (error) {
              return null;
            }
          }
          return null;
        };
        const source = parseStoredJson(configuration);
        if (!source) return null;
        let normalized = source;
        for (let depth = 0; depth < 5; depth += 1) {
          const next =
            parseStoredJson(normalized.payload) ||
            parseStoredJson(normalized.configuration) ||
            parseStoredJson(normalized.full_config_json) ||
            parseStoredJson(normalized.configuration_json) ||
            parseStoredJson(normalized.config_json) ||
            parseStoredJson(normalized.data?.payload) ||
            parseStoredJson(normalized.data?.configuration) ||
            parseStoredJson(normalized.data?.full_config_json) ||
            parseStoredJson(normalized.data);
          if (!next || next === normalized) break;
          normalized = next;
        }
        if (!normalized || typeof normalized !== "object") {
          return null;
        }

        const product =
          parseStoredJson(normalized.product) ||
          parseStoredJson(source.product) ||
          {};
        const normalizeFrameList = (...candidates) => {
          for (const candidate of candidates) {
            const parsed = parseStoredJson(candidate);
            if (Array.isArray(parsed)) return parsed;
            if (parsed && typeof parsed === "object") {
              const numericEntries = Object.keys(parsed)
                .filter((key) => /^\d+$/.test(key))
                .sort((left, right) => Number(left) - Number(right))
                .map((key) => parsed[key]);
              if (numericEntries.length) return numericEntries;
              for (const containerKey of [
                "frames",
                "items",
                "records",
                "data",
                "rows",
                "details",
              ]) {
                const nested = parseStoredJson(parsed[containerKey]);
                if (Array.isArray(nested)) return nested;
              }
            }
          }
          return [];
        };
        let commands = normalizeFrameList(
          normalized.commands,
          normalized.commandFrames,
          normalized.command_frames,
          source.commands,
        );
        let responses = normalizeFrameList(
          normalized.responses,
          normalized.responseFrames,
          normalized.response_frames,
          source.responses,
        );
        let errors = normalizeFrameList(
          normalized.errors,
          normalized.errorFrames,
          normalized.error_frames,
          source.errors,
        );

        const padFramesToSavedCount = (frames, ...countCandidates) => {
          const requestedCount = Math.min(
            20,
            Math.max(
              0,
              ...countCandidates.map((value) => Number.parseInt(value, 10) || 0),
            ),
          );
          const result = [...frames];
          while (result.length < requestedCount) result.push({});
          return result;
        };
        commands = padFramesToSavedCount(
          commands,
          product.command_frame_count,
          product.commandFrameCount,
        );
        responses = padFramesToSavedCount(
          responses,
          product.response_frame_count,
          product.responseFrameCount,
        );
        errors = padFramesToSavedCount(
          errors,
          product.error_frame_count,
          product.errorFrameCount,
        );
        const flatCommandValue =
          product.command_value ||
          product.commandValue ||
          product.command_code ||
          product.commandCode ||
          product.full_api_value ||
          product.fullApiValue ||
          "";
        if (flatCommandValue && commands.length) {
          const firstCommand =
            commands[0] && typeof commands[0] === "object"
              ? commands[0]
              : {};
          if (
            !firstCommand.commandCode &&
            !firstCommand.frameCode &&
            !firstCommand.commandValue
          ) {
            commands[0] = {
              ...firstCommand,
              commandValue: flatCommandValue,
            };
          }
        }

        if (!Object.keys(product).length && !commands.length && !responses.length && !errors.length) {
          return null;
        }
        return {
          product: clonePartitionObject(product),
          commands: clonePartitionObject(commands),
          responses: clonePartitionObject(responses),
          errors: clonePartitionObject(errors),
        };
      }

      function schemaFramesToStore(frames, frameType) {
        const list = Array.isArray(frames)
          ? frames
          : frames && typeof frames === "object"
            ? Object.keys(frames)
              .filter((key) => /^\d+$/.test(key))
              .sort((left, right) => Number(left) - Number(right))
              .map((key) => frames[key])
            : [];
        const codeProperty = `${frameType}Code`;
        const parseFrameObject = (value) => {
          if (value && typeof value === "object") {
            return clonePartitionObject(value);
          }
          if (typeof value === "string" && value.trim()) {
            try {
              const parsed = JSON.parse(value);
              return parsed && typeof parsed === "object" ? parsed : {};
            } catch (error) {
              return {};
            }
          }
          return {};
        };
        const normalizedFrames = list.map((frame) => parseFrameObject(frame));
        const compactKey = (value) =>
          String(value || "")
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9]/g, "");
        const readAliasedValue = (
          source,
          aliases,
          maxDepth = 3,
          depth = 0,
          visited = new Set(),
        ) => {
          if (
            !source ||
            typeof source !== "object" ||
            depth > maxDepth ||
            visited.has(source)
          ) {
            return undefined;
          }
          visited.add(source);
          const aliasKeys = new Set(aliases.map(compactKey));
          for (const [key, value] of Object.entries(source)) {
            if (aliasKeys.has(compactKey(key)) && value !== null && value !== "") {
              return value;
            }
          }
          for (const [key, value] of Object.entries(source)) {
            if (
              ["rows", "bitwise", "fields"].includes(compactKey(key)) ||
              !value ||
              typeof value !== "object"
            ) {
              continue;
            }
            const found = readAliasedValue(
              value,
              aliases,
              maxDepth,
              depth + 1,
              visited,
            );
            if (found !== undefined) return found;
          }
          return undefined;
        };
        const readRowValue = (row, key) => {
          const prefixes = {
            command: "cmd",
            response: "resp",
            error: "error",
          };
          const value = readAliasedValue(
            row,
            [
              key,
              `${prefixes[frameType]}_${key}`,
              `${prefixes[frameType]}${key}`,
            ],
            1,
          );
          return value ?? "";
        };
        const normalizePartitionName = (value) => {
          const compact = String(value || "")
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9]/g, "");
          const match = API_PARTITION_OPTIONS.find(
            (option) =>
              option.value.toLowerCase().replace(/[^a-z0-9]/g, "") === compact ||
              option.label.toLowerCase().replace(/[^a-z0-9]/g, "") === compact,
          );
          if (match) return match.value;
          if (compact === "commandid") return "commandId";
          if (compact === "functioncode") return "functionCode";
          if (compact === "data" || compact === "payload") return "data";
          if (
            compact === "terminator" ||
            compact === "crc" ||
            compact === "terminatorcrc"
          ) {
            return "terminatorCrc";
          }
          return "";
        };
        const rowToConfiguration = (row, suffix = "") => ({
          dataType:
            readRowValue(row, `dataType${suffix}`) ||
            row?.[`dataType${suffix}`] ||
            "",
          valueDef:
            readRowValue(row, `valueDefinition${suffix}`) ||
            row?.[`valueDefinition${suffix}`] ||
            "",
          uiType:
            readRowValue(row, `uiType${suffix}`) ||
            row?.[`uiType${suffix}`] ||
            "",
          uiInOut:
            readRowValue(row, `uiDescriptionInOut${suffix}`) ||
            row?.[`uiDescriptionInOut${suffix}`] ||
            "",
          scalling:
            readRowValue(row, `scaling${suffix}`) ||
            row?.[`scaling${suffix}`] ||
            "",
          displayValueMin:
            readRowValue(row, `displayValueMin${suffix}`) ||
            row?.[`displayValueMin${suffix}`] ||
            "",
          displayValueMax:
            readRowValue(row, `displayValueMax${suffix}`) ||
            row?.[`displayValueMax${suffix}`] ||
            "",
          valueInHexMin:
            readRowValue(row, `valueInHexMin${suffix}`) ||
            row?.[`valueInHexMin${suffix}`] ||
            "",
          valueInHexMax:
            readRowValue(row, `valueInHexMax${suffix}`) ||
            row?.[`valueInHexMax${suffix}`] ||
            "",
          uiId:
            readRowValue(row, `uiId${suffix}`) ||
            row?.[`uiId${suffix}`] ||
            "",
          asperui:
            readRowValue(row, `asperui${suffix}`) ||
            row?.[`asperui${suffix}`] ||
            "",
          sno_asperui:
            readRowValue(row, `sno_asperui${suffix}`) ||
            row?.[`sno_asperui${suffix}`] ||
            "",
          asperuivalue:
            readRowValue(row, `asperuivalue${suffix}`) ||
            row?.[`asperuivalue${suffix}`] ||
            "",
          uiPageNo:
            readRowValue(row, `uiPageNo${suffix}`) ||
            row?.[`uiPageNo${suffix}`] ||
            "",
          sliderStepValue:
            readRowValue(row, `sliderStepValue${suffix}`) ||
            row?.[`sliderStepValue${suffix}`] ||
            "",
          defaultvalue:
            readRowValue(row, `defaultvalue${suffix}`) ||
            row?.[`defaultvalue${suffix}`] ||
            "",
          developerNotes:
            readRowValue(row, `developerNotes${suffix}`) ||
            row?.[`developerNotes${suffix}`] ||
            "",
          notificationEnable:
            readRowValue(row, `notificationEnable${suffix}`) ||
            readRowValue(row, `notifEnable${suffix}`) ||
            row?.[`notificationEnable${suffix}`] ||
            row?.[`notifEnable${suffix}`] ||
            "No",
          notificationMessage:
            readRowValue(row, `notificationMessage${suffix}`) ||
            readRowValue(row, `notifMsg${suffix}`) ||
            row?.[`notificationMessage${suffix}`] ||
            row?.[`notifMsg${suffix}`] ||
            "",
        });
        const rebuildPartitionFromRows = (frame, frameCode) => {
          const rawRows = readAliasedValue(
            frame,
            [
              "rows",
              "frameRows",
              "rowData",
              "partitionRows",
              `${frameType}Rows`,
              `${frameType}_rows`,
            ],
            2,
          );
          const parsedRows = parseFrameObject(rawRows || []);
          const rows = Array.isArray(parsedRows)
            ? parsedRows.map(parseFrameObject)
            : [];
          if (!rows.length) return {};

          const partitionLabels = {};
          const partitionViewModes = {};
          const partitionFieldConfigurations = {};
          const partitionCuts = [];
          let inferredByteCount = Math.ceil(frameCode.length / 2);

          rows.forEach((row, rowIndex) => {
            const rawStart = Number.parseInt(
              readRowValue(row, "startBytePosition"),
              10,
            );
            const rawEnd = Number.parseInt(
              readRowValue(row, "endBytePosition"),
              10,
            );
            const byteCount = Math.max(
              1,
              Number.parseInt(readRowValue(row, "numberOfBytes"), 10) || 1,
            );
            const start = Number.isFinite(rawStart) ? rawStart : rowIndex;
            const endExclusive = Number.isFinite(rawEnd)
              ? Math.max(start + 1, rawEnd + 1)
              : start + byteCount;
            const key = `${start}-${endExclusive}`;
            inferredByteCount = Math.max(inferredByteCount, endExclusive);
            if (start > 0) partitionCuts.push(start);

            partitionLabels[key] = normalizePartitionName(
              readRowValue(row, "fieldDescription"),
            );
            const parsedBitwise = parseFrameObject(
              readAliasedValue(
                row,
                ["bitwise", "bitwiseRows", "bitRows", "bits"],
                2,
              ) || [],
            );
            const bitwiseRows = Array.isArray(parsedBitwise)
              ? parsedBitwise
              : [];
            const isBitwise =
              String(readRowValue(row, "bitwiseMode"))
                .trim()
                .toLowerCase() === "bitwise" ||
              bitwiseRows.length > 0;
            partitionViewModes[key] = isBitwise ? "bitwise" : "bytewise";
            partitionFieldConfigurations[key] = {
              byte: rowToConfiguration(row),
            };

            if (isBitwise && bitwiseRows.length) {
              const bitCuts = new Set();
              const bitCount = Math.max(8, (endExclusive - start) * 8);
              bitwiseRows.forEach((rawBitRow) => {
                const bitRow = parseFrameObject(rawBitRow);
                const startValue = String(
                  bitRow.startBytePosition_1 ??
                    bitRow.startBit ??
                    bitRow.start_bit ??
                    "0",
                );
                const endValue = String(
                  bitRow.endBytePosition_1 ??
                    bitRow.endBit ??
                    bitRow.end_bit ??
                    startValue,
                );
                const startMatch = startValue.match(/\d+/g);
                const endMatch = endValue.match(/\d+/g);
                const isEncodedGroup =
                  /bit[\s_-]*group/i.test(startValue) &&
                  startMatch?.length >= 2;
                const startBit = isEncodedGroup
                  ? Number(startMatch[0])
                  : startMatch
                    ? Number(startMatch[startMatch.length - 1])
                    : 0;
                const endBit = isEncodedGroup
                  ? Number(startMatch[startMatch.length - 1])
                  : endMatch
                    ? Number(endMatch[endMatch.length - 1])
                    : startBit;
                const lowBit = Math.max(0, Math.min(startBit, endBit));
                const highBit = Math.min(
                  bitCount - 1,
                  Math.max(startBit, endBit),
                );
                const positions = Array.from(
                  { length: highBit - lowBit + 1 },
                  (_, index) => lowBit + index,
                );
                const configKey =
                  positions.length === 1
                    ? `bit:${positions[0]}`
                    : `bit-group:${positions[0]}-${positions[positions.length - 1]}`;
                partitionFieldConfigurations[key][configKey] = {
                  ...rowToConfiguration(bitRow, "_1"),
                  bitPosition:
                    positions.length === 1 ? positions[0] : null,
                  bitPositions: positions,
                };
                if (lowBit > 0) bitCuts.add(lowBit);
                if (highBit + 1 < bitCount) bitCuts.add(highBit + 1);
              });
              partitionFieldConfigurations[key]._bitCuts = [...bitCuts].sort(
                (a, b) => a - b,
              );
            }
          });

          const normalizedCode = frameCode || "";
          const valueGroups = Object.keys(partitionLabels).map((key) => {
            const [start, end] = key.split("-").map(Number);
            return normalizedCode.slice(start * 2, end * 2);
          });
          return {
            fullApiValue: normalizedCode,
            partitionCuts: [...new Set(partitionCuts)].sort((a, b) => a - b),
            partitionLabels,
            partitionViewModes,
            partitionFieldConfigurations,
            selectedBitGroups: {},
            partitionsConfirmed: Object.values(partitionLabels).every(Boolean),
            valueGroups,
            inferredByteCount,
          };
        };
        const resolvedFrameCodes = normalizedFrames.map((frame) => {
          const rawGroups = readAliasedValue(
            frame,
            ["valueGroups", "value_groups", "hexGroups", "byteValues"],
            2,
          );
          const parsedGroups = parseFrameObject(rawGroups || []);
          const groups = Array.isArray(parsedGroups)
            ? parsedGroups
            : [];
          const codeValue = readAliasedValue(
            frame,
            [
              codeProperty,
              `${frameType}_code`,
              `${frameType}Value`,
              `${frameType}_value`,
              "frameCode",
              "frame_code",
              "commandValue",
              "command_value",
              "apiValue",
              "api_value",
              "fullApiValue",
              "full_api_value",
              "fullApi",
              "hexValue",
              "hex_value",
            ],
            4,
          );
          return String(codeValue ?? groups.join("") ?? "")
            .replace(/[^0-9a-f]/gi, "")
            .toUpperCase();
        });
        const resolvedPartitions = normalizedFrames.map((frame, index) => {
          const savedPartition = parseFrameObject(
            frame?.partition ||
              frame?.partitionState ||
              frame?.partition_state ||
              frame?.editorPartition ||
              {},
          );
          return Object.keys(savedPartition).length
            ? savedPartition
            : rebuildPartitionFromRows(frame, resolvedFrameCodes[index]);
        });
        return {
          count: normalizedFrames.length,
          frameCodes: resolvedFrameCodes,
          frameValueGroups: normalizedFrames.map((frame, index) => {
            const groups = parseFrameObject(frame?.valueGroups || []);
            return Array.isArray(groups) && groups.length
              ? groups
              : resolvedPartitions[index]?.valueGroups || [];
          }),
          fieldDescriptions: Object.fromEntries(
            normalizedFrames.map((frame, index) => [
              index,
              parseFrameObject(frame?.fieldDescriptions || {}),
            ]),
          ),
          byteDescriptions: Object.fromEntries(
            normalizedFrames.map((frame, index) => [
              index,
              parseFrameObject(frame?.byteDescriptions || {}),
            ]),
          ),
          fieldViewModes: Object.fromEntries(
            normalizedFrames.map((frame, index) => [
              index,
              parseFrameObject(frame?.fieldViewModes || {}),
            ]),
          ),
          framePartitionStates: resolvedPartitions,
          frameConfigurations: normalizedFrames.map((frame) => ({
            dataFlowDirection: normalizeDirectionValue(
              frame?.direction ||
              frame?.dataFlowDirection ||
              frame?.dataflowdirection ||
              frame?.meta?.dataflowdirection ||
              "",
            ),
            multipleUi: String(
              frame?.multipleui ??
              frame?.multipleUi ??
              frame?.meta?.multipleui ??
              "",
            ).toLowerCase(),
          })),
        };
      }

      function schemaToEditorSaved(configuration) {
        const schema = normalizeConfigurationPayload(configuration);
        if (!schema) return null;
        return {
          storageVersion: 3,
          savedAt: schema.product.date || "",
          form: {
            parameterName:
              schema.product.parameter_name ||
              schema.product.parameterName ||
              schema.commands[0]?.parameterName ||
              schema.commands[0]?.parametername ||
              schema.commands[0]?.meta?.parametername ||
              "",
            commandFrameCount: schema.commands.length,
            responseFrameCount: schema.responses.length,
            errorFrameCount: schema.errors.length,
            triggerUi:
              schema.commands[0]?.triggerui ||
              schema.responses[0]?.triggerui ||
              schema.errors[0]?.triggerui ||
              "",
          },
          activeFrameType:
            schema.commands.length > 0
              ? "command"
              : schema.responses.length > 0
                ? "response"
                : schema.errors.length > 0
                  ? "error"
                  : "command",
          bytes: clonePartitionObject(schema.product.editor_bytes || []),
          frameTypeStores: {
            command: schemaFramesToStore(schema.commands, "command"),
            response: schemaFramesToStore(schema.responses, "response"),
            error: schemaFramesToStore(schema.errors, "error"),
          },
        };
      }

      function restoreSavedConfiguration(
        configuration = serverApp.initialConfiguration,
      ) {
        const saved = schemaToEditorSaved(configuration);
        if (!saved || !saved.form) return false;

        document.getElementById("paramNameInput").value =
          saved.form.parameterName || "";
        if (document.getElementById("triggerUiSelect")) {
          document.getElementById("triggerUiSelect").value =
            saved.form.triggerUi || "";
        }
        bytes =
          Array.isArray(saved.bytes) && saved.bytes.length
            ? clonePartitionObject(saved.bytes)
            : bytes;
        if (!bytes.length) {
          const allCodes = Object.values(saved.frameTypeStores || {})
            .flatMap((store) => store?.frameCodes || []);
          const inferredByteCount = Math.max(
            8,
            ...allCodes.map((code) =>
              Math.ceil(String(code || "").replace(/[^0-9a-f]/gi, "").length / 2),
            ),
          );
          bytes = Array.from({ length: inferredByteCount }, (_, index) => ({
            id: index,
            value: generateByteValue(index),
            active: false,
          }));
        }
        if (saved.storageVersion === 3 && saved.frameTypeStores) {
          Object.keys(FRAME_TYPE_LABELS).forEach((frameType) => {
            const restoredStore =
              saved.frameTypeStores[frameType] || createEmptyFrameTypeStore();
            frameTypeStores[frameType] = {
              count: parseInt(restoredStore.count, 10) || 0,
              frameCodes: clonePartitionObject(
                restoredStore.frameCodes || [],
              ),
              frameValueGroups: clonePartitionObject(
                restoredStore.frameValueGroups || [],
              ),
              fieldDescriptions: clonePartitionObject(
                restoredStore.fieldDescriptions || {},
              ),
              byteDescriptions: clonePartitionObject(
                restoredStore.byteDescriptions || {},
              ),
              fieldViewModes: clonePartitionObject(
                restoredStore.fieldViewModes || {},
              ),
              framePartitionStates: clonePartitionObject(
                restoredStore.framePartitionStates || [],
              ),
              frameConfigurations: clonePartitionObject(
                restoredStore.frameConfigurations || [],
              ),
            };
          });
          activeFrameType = FRAME_TYPE_LABELS[saved.activeFrameType]
            ? saved.activeFrameType
            : "command";
        } else {
          frameTypeStores.command = {
            count:
              parseInt(saved.form.commandFrameCount, 10) ||
              saved.frameCodes?.length ||
              saved.commandFrames?.length ||
              0,
            frameCodes: clonePartitionObject(saved.frameCodes || []),
            frameValueGroups: clonePartitionObject(
              saved.frameValueGroups || [],
            ),
            fieldDescriptions: clonePartitionObject(
              saved.fieldDescriptions || {},
            ),
            byteDescriptions: clonePartitionObject(
              saved.byteDescriptions || {},
            ),
            fieldViewModes: clonePartitionObject(saved.fieldViewModes || {}),
            framePartitionStates: clonePartitionObject(
              saved.framePartitionStates || [],
            ),
            frameConfigurations: Array.isArray(saved.frameConfigurations)
              ? clonePartitionObject(saved.frameConfigurations)
              : Array.isArray(saved.commandFrames)
                ? saved.commandFrames.map((frame) => ({
                    dataFlowDirection:
                      frame.dataFlowDirection ||
                      saved.form.dataFlowDirection ||
                      "",
                    multipleUi:
                      frame.multipleUi || saved.form.multipleUi || "",
                  }))
                : [],
          };
          activeFrameType = "command";
        }
        loadFrameTypeStore(activeFrameType);
        updateFrameTypeUi();

        selectedFrameIndex = null;
        activePartitionFrameIndex = null;
        fullApiValue = "";
        restoredByteCount = 0;
        partitionCuts = [];
        partitionLabels = {};
        partitionViewModes = {};
        partitionFieldConfigurations = {};
        selectedBitGroups = {};
        partitionsConfirmed = false;

        const savedCount = Math.min(
          20,
          Math.max(
            0,
            frameTypeStores[activeFrameType].count || frameCodes.length || 0,
          ),
        );
        document.getElementById("cmdFrameCountInput").value = savedCount;
        // Edit restoration must not depend on the Add-form Generate
        // validation. Saved cards are rendered directly from the database
        // payload even when a legacy row has a missing optional value.
        if (savedCount > 0 && bytes.length > 0) {
          generateCommandFrames(savedCount, true);
        } else {
          document.getElementById("commandFramesContainer").innerHTML = "";
          updateCommandFrameCount();
        }
        renderApiPartitionEditor();
        updateFramePartitionStates();
        updateFieldDescFrameSelect();
        renderFieldDescription();

        // In Edit mode, reveal the first saved frame automatically. This
        // makes its partitions and field/bit settings visible without
        // requiring the user to click the frame card first.
        if (savedCount > 0) {
          const firstSavedFrameIndex = framePartitionStates.findIndex(
            (state, index) =>
              Boolean(
                frameCodes[index] ||
                state?.fullApiValue ||
                state?.partitionCuts?.length ||
                Object.keys(state?.partitionLabels || {}).length ||
                Object.keys(state?.partitionFieldConfigurations || {}).length,
              ),
          );
          const frameIndex =
            firstSavedFrameIndex >= 0 ? firstSavedFrameIndex : 0;
          activePartitionFrameIndex = frameIndex;
          selectedFrameIndex = frameIndex;
          loadFramePartitionState(frameIndex);
          document
            .getElementById("apiPartitionSection")
            ?.classList.add("frame-partition-active");
          document
            .querySelectorAll("#commandFramesContainer .command-frame-card")
            .forEach((card, index) => {
              card.classList.toggle("selected", index === frameIndex);
            });
          const fieldFrameSelect =
            document.getElementById("fieldDescFrameSelect");
          if (fieldFrameSelect?.querySelector(`option[value="${frameIndex}"]`)) {
            fieldFrameSelect.value = String(frameIndex);
          }
          renderFieldDescription();
        }

        const savedDate = saved.savedAt ? new Date(saved.savedAt) : null;
        const time =
          savedDate && !Number.isNaN(savedDate.getTime())
            ? savedDate.toLocaleTimeString([], {
              hour: "2-digit",
              minute: "2-digit",
            })
            : "";
        updateSaveBadge(
          "saved",
          time
            ? `Loaded database configuration · ${time}`
            : "Loaded database configuration",
        );
        return true;
      }

      async function loadEditConfiguration() {
        if (!serverApp.id || !serverApp.readUrl) return false;
        updateSaveBadge("saving", "Loading saved configuration...");
        try {
          const response = await fetch(serverApp.readUrl, {
            method: "GET",
            headers: {
              Accept: "application/json",
              "X-Requested-With": "XMLHttpRequest",
            },
            credentials: "same-origin",
          });
          const result = await response.json().catch(() => null);
          if (!response.ok || !result?.success) {
            throw new Error(
              result?.message || "The saved API configuration could not be loaded.",
            );
          }
          updateCsrfToken(result);
          serverApp.initialConfiguration = normalizeConfigurationPayload(
            result.data?.configuration ||
            result.configuration ||
            result.data ||
            result,
          );
          return restoreSavedConfiguration(serverApp.initialConfiguration);
        } catch (error) {
          console.error("Unable to load the API configuration.", error);
          updateSaveBadge("error", "Could not load saved configuration");
          alert(error.message || "The saved API configuration could not be loaded.");
          return false;
        }
      }

      function captureActiveFramePartitionState() {
        if (!Number.isInteger(activePartitionFrameIndex)) return;
        framePartitionStates[activePartitionFrameIndex] = {
          fullApiValue,
          inferredByteCount:
            fullApiValue.length > 0
              ? Math.ceil(fullApiValue.length / 2)
              : restoredByteCount,
          partitionCuts: [...partitionCuts],
          partitionLabels: clonePartitionObject(partitionLabels),
          partitionViewModes: clonePartitionObject(partitionViewModes),
          partitionFieldConfigurations: clonePartitionObject(
            partitionFieldConfigurations,
          ),
          selectedBitGroups: clonePartitionObject(selectedBitGroups),
          partitionsConfirmed: Boolean(partitionsConfirmed),
        };
      }

      function loadFramePartitionState(frameIndex) {
        const saved = framePartitionStates[frameIndex] || {};
        fullApiValue = saved.fullApiValue || frameCodes[frameIndex] || "";
        restoredByteCount = fullApiValue
          ? Math.ceil(fullApiValue.length / 2)
          : Math.max(
              0,
              Number.parseInt(
                saved.inferredByteCount ??
                  saved.byteCount ??
                  saved.frameSizeBytes ??
                  0,
                10,
              ) || 0,
            );
        partitionCuts = Array.isArray(saved.partitionCuts)
          ? [...saved.partitionCuts]
          : [];
        partitionLabels = clonePartitionObject(saved.partitionLabels);
        partitionViewModes = clonePartitionObject(saved.partitionViewModes);
        partitionFieldConfigurations = clonePartitionObject(
          saved.partitionFieldConfigurations,
        );
        selectedBitGroups = clonePartitionObject(saved.selectedBitGroups);
        partitionsConfirmed = Boolean(saved.partitionsConfirmed);
        document.getElementById("fullApiInput").value = fullApiValue;
        document.getElementById("partitionStepFrameName").textContent =
          `${getActiveFrameLabel()} Frame ${frameIndex + 1}`;
        renderApiPartitionEditor();
      }

      function getFrameFillStatus(frameIndex) {
        const confirmed = Boolean(
          framePartitionStates[frameIndex]?.partitionsConfirmed,
        );
        if (confirmed) return "filled";
        const hasApi = Boolean(
          (frameCodes[frameIndex] || "").trim() ||
          framePartitionStates[frameIndex]?.fullApiValue,
        );
        if (hasApi) return "in-progress";
        return "pending";
      }

      function updateFramePartitionStates() {
        document
          .querySelectorAll("#commandFramesContainer .command-frame-card")
          .forEach((card, index) => {
            const status = getFrameFillStatus(index);
            card.classList.toggle("partition-complete", status === "filled");
            card.classList.toggle("needs-partition", status === "pending");
            card.classList.toggle("frame-in-progress", status === "in-progress");
            const badge = card.querySelector(".frame-status-badge");
            const state = card.querySelector(".frame-partition-state");
            if (badge) {
              if (status === "filled") {
                badge.className = "frame-status-badge filled";
                badge.innerHTML =
                  '<i class="fas fa-circle-check"></i> Filled';
              } else if (status === "in-progress") {
                badge.className = "frame-status-badge in-progress";
                badge.innerHTML =
                  '<i class="fas fa-spinner"></i> In progress';
              } else {
                badge.className = "frame-status-badge pending";
                badge.innerHTML =
                  '<i class="fas fa-hourglass-half"></i> Pending';
              }
            }
            if (state) {
              if (status === "filled") {
                state.className = "frame-partition-state complete";
                state.innerHTML =
                  '<i class="fas fa-circle-check"></i> API partitioned';
              } else if (status === "in-progress") {
                state.className = "frame-partition-state in-progress";
                state.innerHTML =
                  '<i class="fas fa-pen-to-square"></i> Finish partitioning';
              } else {
                state.className = "frame-partition-state";
                state.innerHTML =
                  '<i class="fas fa-hand-pointer"></i> Click to enter API';
              }
            }
          });
      }

      function deleteCommandFrame(frameIndex) {
        const frameName = `${getActiveFrameLabel()} Frame ${frameIndex + 1}`;
        const status = getFrameFillStatus(frameIndex);
        const confirmMessage =
          status === "filled"
            ? `Delete ${frameName}? Its saved API partition will also be removed.`
            : `Delete ${frameName}?`;
        if (!window.confirm(confirmMessage)) return;

        const currentCount = document.querySelectorAll(
          "#commandFramesContainer .command-frame-card",
        ).length;
        frameCodes.splice(frameIndex, 1);
        frameValueGroups.splice(frameIndex, 1);
        framePartitionStates.splice(frameIndex, 1);
        frameConfigurations.splice(frameIndex, 1);
        const newFieldDesc = {};
        const newByteDesc = {};
        const newViewModes = {};
        for (let key in fieldDescriptions) {
          let idx = parseInt(key);
          if (idx === frameIndex) continue;
          if (idx > frameIndex)
            newFieldDesc[idx - 1] = fieldDescriptions[idx];
          else newFieldDesc[idx] = fieldDescriptions[idx];
        }
        for (let key in byteDescriptions) {
          let idx = parseInt(key);
          if (idx === frameIndex) continue;
          if (idx > frameIndex) newByteDesc[idx - 1] = byteDescriptions[idx];
          else newByteDesc[idx] = byteDescriptions[idx];
        }
        for (let key in fieldViewModes) {
          let idx = parseInt(key);
          if (idx === frameIndex) continue;
          if (idx > frameIndex) newViewModes[idx - 1] = fieldViewModes[idx];
          else newViewModes[idx] = fieldViewModes[idx];
        }
        fieldDescriptions = newFieldDesc;
        byteDescriptions = newByteDesc;
        fieldViewModes = newViewModes;

        if (selectedFrameIndex === frameIndex) {
          document.getElementById("byteDetailPopup").style.display = "none";
          selectedFrameIndex = null;
          activePartitionFrameIndex = null;
          document
            .getElementById("apiPartitionSection")
            .classList.remove("frame-partition-active");
        } else if (
          selectedFrameIndex !== null &&
          selectedFrameIndex > frameIndex
        ) {
          selectedFrameIndex--;
          if (
            activePartitionFrameIndex !== null &&
            activePartitionFrameIndex > frameIndex
          ) {
            activePartitionFrameIndex--;
          }
        }

        const newCount = Math.max(0, currentCount - 1);
        document.getElementById("cmdFrameCountInput").value = newCount;
        if (newCount === 0) {
          awaitingFrameClick = false;
          document.getElementById("commandFramesContainer").innerHTML = "";
          updateCommandFrameCount();
          document.getElementById("byteDetailPopup").style.display = "none";
          selectedFrameIndex = null;
          frameCodes = [];
          frameValueGroups = [];
          framePartitionStates = [];
          frameConfigurations = [];
          activePartitionFrameIndex = null;
          document
            .getElementById("apiPartitionSection")
            .classList.remove("frame-partition-active");
          fieldDescriptions = {};
          byteDescriptions = {};
          fieldViewModes = {};
          updateFieldDescFrameSelect();
          renderFieldDescription();
          document
            .getElementById("framesNextBanner")
            ?.classList.remove("show");
          markConfigurationUnsaved();
          return;
        }
        generateCommandFrames(newCount, true);
        markConfigurationUnsaved();
      }

      function addCommandFrame() {
        if (!checkRequirements()) {
          showRequirementPopup();
          return;
        }
        if (bytes.length === 0) {
          generate8Bytes();
        }
        const currentCount = document.querySelectorAll(
          "#commandFramesContainer .command-frame-card",
        ).length;
        if (currentCount >= 20) {
          alert(`Maximum of 20 ${getActiveFrameLabel().toLowerCase()} frames allowed.`);
          return;
        }
        const newCount = currentCount + 1;
        document.getElementById("cmdFrameCountInput").value = newCount;
        awaitingFrameClick = true;
        generateCommandFrames(newCount, true);
        document
          .getElementById("commandFramesSection")
          ?.scrollIntoView({ behavior: "smooth", block: "nearest" });
      }

      function openFramePartitionEditor(frameIndex) {
        const frameConfiguration = frameConfigurations[frameIndex] || {};
        const directionSelect = document.getElementById(
          `frameDirection${frameIndex}`,
        );
        const multipleUiSelect = document.getElementById(
          `frameMultipleUi${frameIndex}`,
        );
        const missing = [];
        if (!frameConfiguration.dataFlowDirection) {
          missing.push("Data Flow Direction");
          directionSelect?.classList.add("frame-field-error");
        }
        if (!frameConfiguration.multipleUi) {
          missing.push("Multiple UI");
          multipleUiSelect?.classList.add("frame-field-error");
        }
        if (missing.length) {
          alert(
            `Please select ${missing.join(" and ")} in ${getActiveFrameLabel()} Frame ${frameIndex + 1} before configuration.`,
          );
          (directionSelect?.classList.contains("frame-field-error")
            ? directionSelect
            : multipleUiSelect
          )?.focus();
          return;
        }
        captureActiveFramePartitionState();
        activePartitionFrameIndex = frameIndex;
        selectedFrameIndex = frameIndex;
        awaitingFrameClick = false;
        showPartitionWarnings = false;
        document.getElementById("framesNextBanner")?.classList.remove("show");
        loadFramePartitionState(frameIndex);
        const section = document.getElementById("apiPartitionSection");
        section.classList.add("frame-partition-active", "section-highlight");
        document
          .querySelectorAll(".command-frame-card")
          .forEach((card, index) => {
            card.classList.toggle("selected", index === frameIndex);
            card.classList.remove("awaiting-click");
          });
        section.scrollIntoView({ behavior: "smooth", block: "start" });
        setTimeout(() => section.classList.remove("section-highlight"), 1200);
        setTimeout(() => {
          document.getElementById("fullApiInput")?.focus();
        }, 350);
      }

      function applyPartitionColor(element, partitionIndex) {
        const palette =
          PARTITION_COLORS[partitionIndex % PARTITION_COLORS.length];
        element.classList.add("partition-colored");
        element.style.setProperty("--partition-color", palette.color);
        element.style.setProperty("--partition-soft", palette.soft);
        element.style.setProperty("--partition-text", palette.text);
        element.dataset.partitionColorIndex = String(partitionIndex);
      }

      function cleanFullApi(value) {
        return value
          .replace(/0x/gi, "")
          .replace(/[^0-9a-f]/gi, "")
          .toUpperCase();
      }

      function getApiBytes() {
        const actualBytes = fullApiValue.match(/.{1,2}/g) || [];
        if (actualBytes.length || restoredByteCount < 1) return actualBytes;
        return Array.from(
          { length: restoredByteCount },
          (_, index) => `B${index + 1}`,
        );
      }

      function getPartitionRanges() {
        const byteCount = getApiBytes().length;
        const hasCompleteActualApi =
          Boolean(fullApiValue) && fullApiValue.length % 2 === 0;
        const hasRestoredLayout = !fullApiValue && restoredByteCount > 0;
        if (!byteCount || (!hasCompleteActualApi && !hasRestoredLayout))
          return [];
        const boundaries = [
          0,
          ...partitionCuts.filter((cut) => cut > 0 && cut < byteCount),
          byteCount,
        ]
          .filter((value, index, array) => array.indexOf(value) === index)
          .sort((a, b) => a - b);
        const ranges = [];
        for (let index = 0; index < boundaries.length - 1; index++) {
          ranges.push({
            start: boundaries[index],
            end: boundaries[index + 1],
          });
        }
        return ranges;
      }

      function partitionKey(range) {
        return `${range.start}-${range.end}`;
      }

      function getPartitionValidation() {
        if (!fullApiValue)
          return {
            valid: false,
            message: "Enter the complete hexadecimal API.",
          };
        if (fullApiValue.length % 2 !== 0)
          return {
            valid: false,
            message:
              "The API has an incomplete byte. Add one more hexadecimal character.",
          };
        const ranges = getPartitionRanges();
        if (!ranges.length)
          return {
            valid: false,
            message: "The API must contain at least one complete byte.",
          };
        const unnamed = ranges.findIndex(
          (range) => !partitionLabels[partitionKey(range)],
        );
        if (unnamed !== -1)
          return {
            valid: false,
            message: `Select a name for Partition ${unnamed + 1}.`,
          };
        for (let index = 0; index < ranges.length; index++) {
          const configurationValidation = getPartitionConfigurationValidation(
            partitionKey(ranges[index]),
          );
          if (!configurationValidation.valid) {
            return {
              valid: false,
              message: `Partition ${index + 1}: ${configurationValidation.message}`,
            };
          }
        }
        return {
          valid: true,
          message: `${ranges.length} partition${ranges.length === 1 ? "" : "s"} ready.`,
        };
      }

      function getStepOneTemplate() {
        const apiBytes = getApiBytes();
        return getPartitionRanges().map((range, index) => {
          const nameValue = partitionLabels[partitionKey(range)] || "";
          return {
            index,
            start: range.start,
            end: range.end,
            code: apiBytes.slice(range.start, range.end).join(""),
            nameValue,
            nameLabel:
              API_PARTITION_OPTIONS.find(
                (option) => option.value === nameValue,
              )?.label || "Unnamed",
          };
        });
      }

      function mapPartitionNameToFieldDescription(nameValue) {
        const mapping = {
          commandId: "commandid",
          functionCode: "function code",
          data: "data",
          terminatorCrc: "terminator / crc",
        };
        return mapping[nameValue] || "";
      }

      function updateLinkedApiPanel() {
        const code = document.getElementById("linkedApiCode");
        const chips = document.getElementById("linkedPartitionChips");
        const state = document.getElementById("linkedApiState");
        if (!code || !chips || !state) return;

        code.textContent =
          fullApiValue || "Complete Step 2 for a frame to import its API.";
        chips.innerHTML = "";
        getStepOneTemplate().forEach((partition) => {
          const chip = document.createElement("span");
          chip.className = "linked-partition-chip";
          applyPartitionColor(chip, partition.index);
          chip.innerHTML = `<strong>${partition.nameLabel}</strong><code>${partition.code}</code>`;
          chips.appendChild(chip);
        });

        if (partitionsConfirmed && getPartitionValidation().valid) {
          state.className = "linked-api-state";
          state.innerHTML =
            '<i class="fas fa-circle-check"></i> Linked from Step 2';
        } else {
          state.className = "linked-api-state pending";
          state.innerHTML = fullApiValue
            ? '<i class="fas fa-rotate"></i> Reconfirm Step 2'
            : '<i class="fas fa-clock"></i> Waiting for Step 2';
        }
      }

      function applyStepOneTemplateToFrames(
        count,
        overwrite,
        targetFrameIndex = null,
      ) {
        const template = getStepOneTemplate();
        if (!template.length || !partitionsConfirmed) return;
        const templateCodes = template.map((partition) => partition.code);
        const frameIndexes = Number.isInteger(targetFrameIndex)
          ? [targetFrameIndex]
          : Array.from({ length: count }, (_, index) => index);

        for (const frameIndex of frameIndexes) {
          if (frameIndex < 0 || frameIndex >= count) continue;
          if (overwrite || !frameCodes[frameIndex]) {
            frameCodes[frameIndex] = fullApiValue;
          }
          if (overwrite || !frameValueGroups[frameIndex]?.length) {
            frameValueGroups[frameIndex] = templateCodes.slice();
          }
          if (overwrite || !byteDescriptions[frameIndex]) {
            byteDescriptions[frameIndex] = {};
            fieldViewModes[frameIndex] = {};
            template.forEach((partition, partitionIndex) => {
              const fieldDescription = mapPartitionNameToFieldDescription(
                partition.nameValue,
              );
              byteDescriptions[frameIndex][partitionIndex] = fieldDescription;
              if (fieldDescription === "data") {
                const key = `${partition.start}-${partition.end}`;
                fieldViewModes[frameIndex][partitionIndex] =
                  partitionViewModes[key] || "bytewise";
              }
            });
          }
          if (overwrite || !fieldDescriptions[frameIndex]) {
            fieldDescriptions[frameIndex] = {};
            template.forEach((partition, partitionIndex) => {
              const key = `${partition.start}-${partition.end}`;
              const configurations = partitionFieldConfigurations[key] || {};
              const selectedConfigurationMode =
                partition.nameValue === "data"
                  ? partitionViewModes[key] || "bytewise"
                  : "bytewise";
              Object.entries(configurations).forEach(
                ([configurationKey, configuration]) => {
                  if (
                    configurationKey === "_bitGroups" ||
                    configurationKey === "_bitCuts"
                  )
                    return;
                  if (
                    selectedConfigurationMode === "bytewise" &&
                    configurationKey !== "byte"
                  )
                    return;
                  if (
                    selectedConfigurationMode === "bitwise" &&
                    configurationKey === "byte"
                  )
                    return;
                  let frameConfigurationKey;
                  if (configurationKey === "byte") {
                    frameConfigurationKey = `byte:${partitionIndex}`;
                  } else if (configurationKey.startsWith("bit-group:")) {
                    frameConfigurationKey = `bit-group:${partitionIndex}:${configurationKey.slice("bit-group:".length)}`;
                  } else {
                    frameConfigurationKey = `bit:${partitionIndex}:${configurationKey.split(":")[1]}`;
                  }
                  fieldDescriptions[frameIndex][frameConfigurationKey] = {
                    ...configuration,
                    byteIndex: partitionIndex,
                  };
                },
              );
            });
          }
        }
      }

      function syncFrameStructureFromStepOne() {
        bytes = getApiBytes().map((byteValue, index) => ({
          id: index,
          value: `0x${byteValue}`,
          active: true,
        }));
        renderByteRow("byteRowContainer", bytes);
        updateSelectedInfo();
        updateFrameSizeLabel();
      }

      function setPartitionChanged() {
        partitionsConfirmed = false;
        captureActiveFramePartitionState();
        updateLinkedApiPanel();
        updateFrameTypeUi();
      }

      function getApiTimelineScrollLeft() {
        return (
          document.getElementById("apiTimelineScroll")?.scrollLeft || 0
        );
      }

      function restoreApiTimelineScroll(scrollLeft) {
        requestAnimationFrame(function () {
          const timelineScroll =
            document.getElementById("apiTimelineScroll");
          if (timelineScroll) {
            timelineScroll.scrollLeft = scrollLeft;
            updatePartitionNavigation();
          }
        });
      }

      function getPartitionScrollTargets() {
        const timelineScroll =
          document.getElementById("apiTimelineScroll");
        const timeline = document.getElementById("apiTimeline");
        if (!timelineScroll || !timeline || timeline.style.display === "none") {
          return [];
        }

        const maxScroll = Math.max(
          0,
          timelineScroll.scrollWidth - timelineScroll.clientWidth,
        );
        const targets = Array.from(
          timeline.querySelectorAll(".partition-name-band"),
        ).map((band) => Math.max(0, Math.min(maxScroll, band.offsetLeft)));
        targets.push(maxScroll);
        return [...new Set(targets.map((value) => Math.round(value)))].sort(
          (a, b) => a - b,
        );
      }

      function updatePartitionNavigation() {
        const timelineScroll =
          document.getElementById("apiTimelineScroll");
        const previousButton =
          document.getElementById("previousPartitionBtn");
        const nextButton = document.getElementById("nextPartitionBtn");
        if (!timelineScroll || !previousButton || !nextButton) return;

        const targets = getPartitionScrollTargets();
        const current = timelineScroll.scrollLeft;
        const tolerance = 3;
        const canMoveLeft = targets.some(
          (target) => target < current - tolerance,
        );
        const canMoveRight = targets.some(
          (target) => target > current + tolerance,
        );
        previousButton.disabled = !canMoveLeft;
        nextButton.disabled = !canMoveRight;
        previousButton.setAttribute("aria-disabled", String(!canMoveLeft));
        nextButton.setAttribute("aria-disabled", String(!canMoveRight));
      }

      function scrollToAdjacentPartition(direction) {
        const timelineScroll =
          document.getElementById("apiTimelineScroll");
        if (!timelineScroll) return;
        const targets = getPartitionScrollTargets();
        const current = timelineScroll.scrollLeft;
        const tolerance = 3;
        const target =
          direction < 0
            ? [...targets]
                .reverse()
                .find((position) => position < current - tolerance)
            : targets.find((position) => position > current + tolerance);
        if (target === undefined) return;
        timelineScroll.scrollTo({ left: target, behavior: "smooth" });
      }

      function addCut(cutPosition) {
        const previousScrollLeft = getApiTimelineScrollLeft();
        const byteCount = getApiBytes().length;
        if (
          cutPosition <= 0 ||
          cutPosition >= byteCount ||
          partitionCuts.includes(cutPosition)
        )
          return;
        partitionCuts.push(cutPosition);
        partitionCuts.sort((a, b) => a - b);
        setPartitionChanged();
        renderApiPartitionEditor();
        restoreApiTimelineScroll(previousScrollLeft);
      }

      function removeCut(cutPosition) {
        const previousScrollLeft = getApiTimelineScrollLeft();
        partitionCuts = partitionCuts.filter((cut) => cut !== cutPosition);
        setPartitionChanged();
        renderApiPartitionEditor();
        restoreApiTimelineScroll(previousScrollLeft);
      }

      function moveCutByOne(cutPosition, direction) {
        const byteCount = getApiBytes().length;
        const nextCut = cutPosition + direction;
        if (
          !partitionCuts.includes(cutPosition) ||
          nextCut < 1 ||
          nextCut >= byteCount ||
          partitionCuts.includes(nextCut)
        ) {
          return;
        }
        const previousScrollLeft = getApiTimelineScrollLeft();
        partitionCuts = partitionCuts
          .map((cut) => (cut === cutPosition ? nextCut : cut))
          .sort((a, b) => a - b);
        setPartitionChanged();
        renderApiPartitionEditor();
        restoreApiTimelineScroll(previousScrollLeft);
      }

      function startCutDrag(event, originalCut, marker) {
        if (event.button !== undefined && event.button !== 0) return;
        event.preventDefault();
        const timeline = document.getElementById("apiTimeline");
        const byteCount = getApiBytes().length;
        const rect = timeline.getBoundingClientRect();
        let targetCut = originalCut;
        let moved = false;
        marker.classList.add("dragging");

        function onMove(moveEvent) {
          const clientX =
            moveEvent.clientX ?? moveEvent.touches?.[0]?.clientX;
          if (clientX === undefined || rect.width === 0) return;
          const rawCut = Math.round(
            ((clientX - rect.left) / rect.width) * byteCount,
          );
          const candidate = Math.max(1, Math.min(byteCount - 1, rawCut));
          if (candidate !== originalCut) moved = true;
          if (
            !partitionCuts.includes(candidate) ||
            candidate === originalCut
          ) {
            targetCut = candidate;
            marker.style.left = `${(candidate / byteCount) * 100}%`;
          }
        }

        function onEnd() {
          const previousScrollLeft = getApiTimelineScrollLeft();
          window.removeEventListener("pointermove", onMove);
          window.removeEventListener("pointerup", onEnd);
          marker.classList.remove("dragging");
          if (moved && targetCut !== originalCut) {
            partitionCuts = partitionCuts.filter(
              (cut) => cut !== originalCut,
            );
            partitionCuts.push(targetCut);
            partitionCuts.sort((a, b) => a - b);
            setPartitionChanged();
            suppressCutClick = true;
            setTimeout(() => {
              suppressCutClick = false;
            }, 0);
            renderApiPartitionEditor();
            restoreApiTimelineScroll(previousScrollLeft);
          }
        }

        window.addEventListener("pointermove", onMove);
        window.addEventListener("pointerup", onEnd);
      }

      function renderApiTimeline() {
        const bytesForApi = getApiBytes();
        const ranges = getPartitionRanges();
        const timeline = document.getElementById("apiTimeline");
        const empty = document.getElementById("apiTimelineEmpty");
        const isComplete =
          (Boolean(fullApiValue) && fullApiValue.length % 2 === 0) ||
          (!fullApiValue && restoredByteCount > 0);

        if (!isComplete) {
          timeline.style.display = "none";
          timeline.innerHTML = "";
          empty.style.display = "grid";
          empty.textContent = fullApiValue
            ? "Complete the last byte to enable partition controls."
            : "Enter the full API above to display its bytes and partition controls.";
          updatePartitionNavigation();
          return;
        }

        empty.style.display = "none";
        timeline.style.display = "grid";
        timeline.innerHTML = "";
        timeline.style.gridTemplateColumns = `repeat(${bytesForApi.length}, minmax(54px, 1fr))`;

        bytesForApi.forEach((byteValue, index) => {
          const cell = document.createElement("div");
          cell.className = "api-byte-cell";
          cell.style.gridColumn = String(index + 1);
          const partitionIndex = ranges.findIndex(
            (range) => index >= range.start && index < range.end,
          );
          if (partitionIndex !== -1)
            applyPartitionColor(cell, partitionIndex);
          cell.innerHTML = `<span class="api-byte-index">Byte ${index + 1}</span><span class="api-byte-value">${byteValue}</span>`;
          timeline.appendChild(cell);
        });

        renderPartitionNameBands(timeline, ranges);

        for (let cut = 1; cut < bytesForApi.length; cut++) {
          const marker = document.createElement("button");
          marker.type = "button";
          const isActive = partitionCuts.includes(cut);
          marker.className = "cut-target" + (isActive ? " active" : "");
          marker.style.left = `${(cut / bytesForApi.length) * 100}%`;
          marker.setAttribute(
            "aria-label",
            isActive
              ? `Move or remove cut after byte ${cut}`
              : `Add cut after byte ${cut}`,
          );
          marker.title = isActive
            ? "Drag this scissors cut to move it, or click to remove it"
            : "click agin to deselect the partitons";
          const leftDisabled =
            cut <= 1 || partitionCuts.includes(cut - 1);
          const rightDisabled =
            cut >= bytesForApi.length - 1 ||
            partitionCuts.includes(cut + 1);
          marker.innerHTML = isActive
            ? `<span class="cut-nudges" aria-hidden="true">
                 <span class="cut-nudge${leftDisabled ? " disabled" : ""}" data-direction="-1" title="Move partition cut left"><i class="fas fa-chevron-left"></i></span>
                 <span class="cut-nudge${rightDisabled ? " disabled" : ""}" data-direction="1" title="Move partition cut right"><i class="fas fa-chevron-right"></i></span>
               </span>
               <span class="cut-chip"><i class="fas fa-scissors"></i></span>
               <span class="cut-hover-tip">click agin to deselect the partitons</span>`
            : `<span class="cut-chip"><i class="fas fa-plus"></i></span>
               <span class="cut-hover-tip">click agin to deselect the partitons</span>`;
          if (isActive) {
            const rightPartitionIndex = ranges.findIndex(
              (range) => range.start === cut,
            );
            applyPartitionColor(
              marker,
              rightPartitionIndex === -1 ? 0 : rightPartitionIndex,
            );
            marker.addEventListener("pointerdown", function (event) {
              if (event.target.closest(".cut-nudge")) return;
              startCutDrag(event, cut, marker);
            });
          }
          marker.addEventListener("click", function (event) {
            event.preventDefault();
            event.stopPropagation();
            if (suppressCutClick) return;
            const nudge = event.target.closest(".cut-nudge");
            if (nudge) {
              if (!nudge.classList.contains("disabled")) {
                moveCutByOne(cut, Number(nudge.dataset.direction));
              }
              return;
            }
            if (partitionCuts.includes(cut)) removeCut(cut);
            else addCut(cut);
          });
          timeline.appendChild(marker);
        }
        requestAnimationFrame(updatePartitionNavigation);
      }

      function renderPartitionNameBands(timeline, ranges) {
        ranges.forEach((range, index) => {
          const key = partitionKey(range);
          const band = document.createElement("div");
          band.className = "partition-name-band";
          band.style.gridColumn = `${range.start + 1} / ${range.end + 1}`;
          if (index === 0) band.style.borderBottomLeftRadius = "11px";
          if (index === ranges.length - 1)
            band.style.borderBottomRightRadius = "11px";
          applyPartitionColor(band, index);

          const label = document.createElement("label");
          label.innerHTML = `<span class="partition-name-dot"></span>Partition ${index + 1} name`;
          const select = document.createElement("select");
          select.setAttribute(
            "aria-label",
            `Name for partition ${index + 1}`,
          );
          select.innerHTML =
            '<option value="">Select partition name</option>';
          API_PARTITION_OPTIONS.forEach((optionData) => {
            const option = document.createElement("option");
            option.value = optionData.value;
            option.textContent = optionData.label;
            if (partitionLabels[key] === optionData.value)
              option.selected = true;
            select.appendChild(option);
          });
          select.addEventListener("change", function () {
            partitionLabels[key] = this.value;
            if (this.value !== "data") {
              partitionViewModes[key] = "bytewise";
            } else if (
              partitionViewModes[key] !== "bytewise" &&
              partitionViewModes[key] !== "bitwise"
            ) {
              partitionViewModes[key] = "bytewise";
            }
            setPartitionChanged();
            renderApiPartitionEditor();
          });

          /*
           * Keep the partition-name area compact. For Data partitions,
           * Byte Wise and Bit Wise are shown only inside the configuration
           * popup after the user clicks the Configure Partition button.
           */
          band.append(label, select);

          const configBtn = document.createElement("button");
          configBtn.type = "button";
          configBtn.className = "partition-config-open-btn";
          const hasName = Boolean(partitionLabels[key]);
          const configValidation = hasName
            ? getPartitionConfigurationValidation(key)
            : { valid: false, message: "Select a partition name first." };
          if (!hasName) {
            configBtn.disabled = true;
            configBtn.innerHTML =
              '<i class="fas fa-sliders"></i> Select name to configure';
          } else if (configValidation.valid) {
            configBtn.classList.add("config-ready");
            configBtn.innerHTML =
              '<i class="fas fa-sliders"></i> Open Configuration';
          } else {
            configBtn.classList.add("config-missing");
            configBtn.innerHTML =
              '<i class="fas fa-sliders"></i>  click Here toConfigure Partition';
          }
          configBtn.title = hasName
            ? `Open configuration for Partition ${index + 1}`
            : "Select a partition name first";
          configBtn.addEventListener("click", function (event) {
            event.preventDefault();
            event.stopPropagation();
            if (!partitionLabels[key]) return;
            openPartitionModePopup(key, index);
          });

          band.appendChild(configBtn);
          if (!partitionLabels[key]) {
            if (showPartitionWarnings) {
              band.classList.add("missing-selection", "show-warning");
              select.classList.add("dropdown-error");
              select.setAttribute("aria-invalid", "true");
              const warning = document.createElement("span");
              warning.className = "partition-dropdown-warning";
              warning.innerHTML =
                '<i class="fas fa-triangle-exclamation"></i> Select this partition name';
              band.appendChild(warning);
            } else {
              select.setAttribute("aria-invalid", "false");
              const hint = document.createElement("span");
              hint.className = "partition-dropdown-hint";
              hint.innerHTML =
                '<i class="fas fa-circle-info"></i> Choose a name, then open configuration';
              band.appendChild(hint);
            }
          } else {
            select.setAttribute("aria-invalid", "false");
          }
          timeline.appendChild(band);
        });
      }

      function renderPartitionVisualBlocks() {
        const container = document.getElementById("partitionVisualBlocks");
        if (!container) return;
        const apiBytes = getApiBytes();
        const ranges = getPartitionRanges();
        container.innerHTML = "";

        const hasCompleteActualApi =
          Boolean(fullApiValue) && fullApiValue.length % 2 === 0;
        const hasRestoredLayout = !fullApiValue && restoredByteCount > 0;
        if ((!hasCompleteActualApi && !hasRestoredLayout) || !ranges.length) {
          container.innerHTML =
            '<div class="partition-visual-empty"><i class="fas fa-circle-info"></i> Enter a complete API to see each partition as byte boxes.</div>';
          return;
        }

        ranges.forEach((range, index) => {
          const key = partitionKey(range);
          const selectedValue = partitionLabels[key] || "";
          const selectedLabel =
            API_PARTITION_OPTIONS.find(
              (option) => option.value === selectedValue,
            )?.label || "Name not selected";
          const block = document.createElement("div");
          block.className = `partition-visual-block${selectedValue ? "" : " missing"}`;
          applyPartitionColor(block, index);
          if (!selectedValue) block.classList.add("missing");
          const byteStart = range.start + 1;
          const byteEnd = range.end;
          const byteBoxes = apiBytes
            .slice(range.start, range.end)
            .map(
              (byteValue, byteOffset) => `
                  <div class="partition-byte-box">
                    <span class="byte-box-index">B${range.start + byteOffset + 1}</span>
                    <span class="byte-box-value">${byteValue}</span>
                  </div>
                `,
            )
            .join("");

          const configValidation = selectedValue
            ? getPartitionConfigurationValidation(key)
            : { valid: false };
          const configBtnClass = !selectedValue
            ? ""
            : configValidation.valid
              ? " config-ready"
              : " config-missing";
          const configBtnLabel = !selectedValue
            ? "Select name first"
            : configValidation.valid
              ? "Open Configuration"
              : "Configure Partition";

          block.innerHTML = `
                    <span class="partition-visual-index"><i class="fas fa-puzzle-piece"></i> Partition ${index + 1}</span>
                    <div class="partition-byte-boxes">${byteBoxes}</div>
                    <span class="partition-visual-name">
                        <i class="fas ${selectedValue ? "fa-tag" : "fa-circle-info"}"></i>
                        ${selectedLabel}
                    </span>
                    <span class="partition-visual-range"><i class="fas fa-ruler-horizontal"></i> Byte ${byteStart}${byteEnd === byteStart ? "" : ` to Byte ${byteEnd}`}</span>
                    <button type="button" class="partition-config-open-btn${configBtnClass}" ${selectedValue ? "" : "disabled"}>
                      <i class="fas fa-sliders"></i> ${configBtnLabel}
                    </button>
                `;
          const previewConfigBtn = block.querySelector(
            ".partition-config-open-btn",
          );
          previewConfigBtn?.addEventListener("click", function (event) {
            event.preventDefault();
            event.stopPropagation();
            if (!partitionLabels[key]) return;
            openPartitionModePopup(key, index);
          });
          container.appendChild(block);
        });
      }

      let activePartitionModeKey = null;
      let activePartitionModeIndex = null;

      function openPartitionModePopup(key, partitionIndex) {
        const range = getPartitionRanges().find(
          (item) => partitionKey(item) === key,
        );
        if (!range || !partitionLabels[key]) return;

        activePartitionModeKey = key;
        activePartitionModeIndex = partitionIndex;
        const overlay = document.getElementById("partitionModeOverlay");
        const modal = document.getElementById("partitionModeModal");
        const body = document.getElementById("partitionModeBody");
        const partitionCode = getApiBytes()
          .slice(range.start, range.end)
          .join("");
        const nameLabel =
          API_PARTITION_OPTIONS.find(
            (option) => option.value === partitionLabels[key],
          )?.label || "Partition";

        applyPartitionColor(modal, partitionIndex);
        document.getElementById("partitionModeTitle").textContent =
          `Partition ${partitionIndex + 1} ${nameLabel}`;
        document.getElementById("partitionModeCode").textContent =
          partitionCode;
        body.innerHTML = "";
        const warning = document.createElement("div");
        warning.id = "partitionConfigWarning";
        warning.className = "partition-config-warning";
        warning.setAttribute("role", "alert");
        warning.innerHTML =
          '<i class="fas fa-triangle-exclamation"></i><span>Complete and save the required configuration before clicking Done.</span>';
        body.appendChild(warning);
        modal.classList.remove("config-invalid");
        renderPartitionConfigurationControls(body, range, partitionIndex);
        overlay.style.display = "flex";
      }

      function closePartitionModePopup() {
        document.getElementById("partitionModeOverlay").style.display =
          "none";
        activePartitionModeKey = null;
        activePartitionModeIndex = null;
      }

      document
        .getElementById("closePartitionMode")
        .addEventListener("click", closePartitionModePopup);
      document
        .getElementById("donePartitionMode")
        .addEventListener("click", function () {
          if (!activePartitionModeKey) return;
          const validation = getPartitionConfigurationValidation(
            activePartitionModeKey,
          );
          const modal = document.getElementById("partitionModeModal");
          const warning = document.getElementById("partitionConfigWarning");

          if (!validation.valid) {
            modal.classList.add("config-invalid");
            if (warning) {
              warning.classList.add("show");
              warning.querySelector("span").textContent = validation.message;
            }
            renderActivePartitionMode();
            const refreshedWarning = document.getElementById(
              "partitionConfigWarning",
            );
            const refreshedModal =
              document.getElementById("partitionModeModal");
            refreshedModal.classList.add("config-invalid");
            if (refreshedWarning) {
              refreshedWarning.classList.add("show");
              refreshedWarning.querySelector("span").textContent =
                validation.message;
              refreshedWarning.scrollIntoView({
                behavior: "smooth",
                block: "nearest",
              });
            }
            return;
          }

          closePartitionModePopup();
        });
      document
        .getElementById("partitionModeOverlay")
        .addEventListener("click", function (event) {
          if (event.target === this) closePartitionModePopup();
        });

      function isFieldConfigurationComplete(configuration) {
        if (!configuration || typeof configuration !== "object") return false;
        return (
          Boolean(configuration.dataType) &&
          Boolean(configuration.valueDef) &&
          Boolean(configuration.uiType) &&
          Boolean(configuration.uiInOut)
        );
      }

      function bitGroupConfigKey(group) {
        const sorted = [...group].sort((a, b) => a - b);
        return sorted.length === 1
          ? `bit:${sorted[0]}`
          : `bit-group:${sorted[0]}-${sorted[sorted.length - 1]}`;
      }

      function bitGroupLabel(group) {
        return [...group]
          .sort((a, b) => a - b)
          .map((bit) => `b${bit}`)
          .join(" + ");
      }

      function getBitCuts(key, bitCount) {
        const configurations = partitionFieldConfigurations[key] || {};
        if (Array.isArray(configurations._bitCuts)) {
          return [...new Set(configurations._bitCuts.map(Number))]
            .filter(
              (cut) =>
                Number.isInteger(cut) && cut > 0 && cut < bitCount,
            )
            .sort((a, b) => a - b);
        }

        /* Migrate groups saved by the previous Combine Selected version. */
        if (Array.isArray(configurations._bitGroups)) {
          const cuts = new Set();
          configurations._bitGroups.forEach((group) => {
            if (!Array.isArray(group) || !group.length) return;
            const clean = [...new Set(group.map(Number))]
              .filter(
                (bit) =>
                  Number.isInteger(bit) && bit >= 0 && bit < bitCount,
              )
              .sort((a, b) => a - b);
            if (!clean.length) return;
            if (clean[0] > 0) cuts.add(clean[0]);
            if (clean[clean.length - 1] + 1 < bitCount)
              cuts.add(clean[clean.length - 1] + 1);
          });
          return [...cuts].sort((a, b) => a - b);
        }
        return [];
      }

      function bitGroupsFromCuts(cuts, bitCount) {
        const boundaries = [0, ...cuts, bitCount];
        const groups = [];
        for (let index = 0; index < boundaries.length - 1; index++) {
          const group = [];
          for (
            let bit = boundaries[index];
            bit < boundaries[index + 1];
            bit++
          )
            group.push(bit);
          if (group.length) groups.push(group);
        }
        return groups.reverse();
      }

      function getBitGroups(key, bitCount) {
        return bitGroupsFromCuts(getBitCuts(key, bitCount), bitCount);
      }

      function saveBitCuts(key, bitCount, nextCuts) {
        if (!partitionFieldConfigurations[key])
          partitionFieldConfigurations[key] = {};
        const configurations = partitionFieldConfigurations[key];
        const cleanCuts = [...new Set(nextCuts.map(Number))]
          .filter(
            (cut) =>
              Number.isInteger(cut) && cut > 0 && cut < bitCount,
          )
          .sort((a, b) => a - b);
        const validConfigurationKeys = new Set(
          bitGroupsFromCuts(cleanCuts, bitCount).map(bitGroupConfigKey),
        );

        Object.keys(configurations).forEach((configKey) => {
          if (
            (configKey.startsWith("bit:") ||
              configKey.startsWith("bit-group:")) &&
            !validConfigurationKeys.has(configKey)
          )
            delete configurations[configKey];
        });
        configurations._bitCuts = cleanCuts;
        delete configurations._bitGroups;
        selectedBitGroups[key] = [];
      }

      function toggleBitCut(key, bitCount, cut) {
        const previousScrollLeft =
          document.querySelector(
            "#partitionModeBody .bit-cut-scroll",
          )?.scrollLeft || 0;
        const cuts = getBitCuts(key, bitCount);
        const nextCuts = cuts.includes(cut)
          ? cuts.filter((item) => item !== cut)
          : [...cuts, cut];
        saveBitCuts(key, bitCount, nextCuts);
        setPartitionChanged();
        renderApiPartitionEditor();
        renderActivePartitionMode();
        restoreBitTimelineScroll(previousScrollLeft);
      }

      function clearBitCuts(key, bitCount) {
        const previousScrollLeft =
          document.querySelector(
            "#partitionModeBody .bit-cut-scroll",
          )?.scrollLeft || 0;
        saveBitCuts(key, bitCount, []);
        setPartitionChanged();
        renderApiPartitionEditor();
        renderActivePartitionMode();
        restoreBitTimelineScroll(previousScrollLeft);
      }

      function restoreBitTimelineScroll(scrollLeft) {
        requestAnimationFrame(function () {
          const timelineScroll = document.querySelector(
            "#partitionModeBody .bit-cut-scroll",
          );
          if (timelineScroll) timelineScroll.scrollLeft = scrollLeft;
        });
      }

      function getPartitionConfigurationValidation(key) {
        const range = getPartitionRanges().find(
          (item) => partitionKey(item) === key,
        );
        if (!range)
          return {
            valid: false,
            message:
              "This partition is no longer available. Recreate the partition and try again.",
          };

        const partitionName = partitionLabels[key] || "";
        if (!partitionName)
          return {
            valid: false,
            message:
              "Select a partition name before completing its configuration.",
          };

        const currentView =
          partitionName === "data"
            ? partitionViewModes[key] || "bytewise"
            : "bytewise";
        const configurations = partitionFieldConfigurations[key] || {};

        if (currentView === "bytewise") {
          return isFieldConfigurationComplete(configurations.byte)
            ? { valid: true, message: "Byte-wise configuration is complete." }
            : {
              valid: false,
              message:
                "Byte-wise configuration is incomplete. Open Configure Complete Partition, fill the form, and click Save Field.",
            };
        }

        const bitCount = (range.end - range.start) * 8;
        const bitGroups = getBitGroups(key, bitCount);
        const missingGroups = bitGroups.filter(
          (group) =>
            !isFieldConfigurationComplete(
              configurations[bitGroupConfigKey(group)],
            ),
        );
        return missingGroups.length === 0
          ? { valid: true, message: "Bit-wise configuration is complete." }
          : {
            valid: false,
            message: `Configure and save these bit partitions: ${missingGroups
              .slice(0, 8)
              .map(bitGroupLabel)
              .join(", ")}${missingGroups.length > 8 ? ` and ${missingGroups.length - 8} more` : ""}.`,
          };
      }

      function renderActivePartitionMode() {
        if (
          !activePartitionModeKey ||
          !Number.isInteger(activePartitionModeIndex)
        )
          return;
        openPartitionModePopup(
          activePartitionModeKey,
          activePartitionModeIndex,
        );
      }

      function renderPartitionConfigurationControls(
        band,
        range,
        partitionIndex,
      ) {
        const key = partitionKey(range);
        const partitionName = partitionLabels[key] || "";
        if (!partitionName) return;

        const apiBytes = getApiBytes();
        const partitionCode = apiBytes.slice(range.start, range.end).join("");
        const currentView =
          partitionName === "data"
            ? partitionViewModes[key] || "bytewise"
            : "bytewise";
        const controls = document.createElement("div");
        controls.className = "partition-config-controls";

        if (partitionName === "data") {
          const toggles = document.createElement("div");
          toggles.className = "view-toggle-buttons";
          const byteButton = document.createElement("button");
          byteButton.type = "button";
          byteButton.className =
            "view-toggle-btn" +
            (currentView === "bytewise" ? " active" : "");
          byteButton.innerHTML =
            '<i class="fas fa-cube"></i> Byte Wise';
          byteButton.addEventListener("click", function () {
            partitionViewModes[key] = "bytewise";
            setPartitionChanged();
            renderApiPartitionEditor();
            renderActivePartitionMode();
          });
          const bitButton = document.createElement("button");
          bitButton.type = "button";
          bitButton.className =
            "view-toggle-btn" +
            (currentView === "bitwise" ? " active" : "");
          bitButton.innerHTML = '<i class="fas fa-grip"></i> Bit Wise';
          bitButton.addEventListener("click", function () {
            partitionViewModes[key] = "bitwise";
            setPartitionChanged();
            renderApiPartitionEditor();
            renderActivePartitionMode();
          });
          toggles.append(byteButton, bitButton);
          controls.appendChild(toggles);
        } else {
          const byteOnly = document.createElement("div");
          byteOnly.className = "view-toggle-buttons single-view";
          const byteOnlyButton = document.createElement("button");
          byteOnlyButton.type = "button";
          byteOnlyButton.className = "view-toggle-btn active";
          byteOnlyButton.innerHTML = '<i class="fas fa-cube"></i> Byte Wise';
          byteOnlyButton.addEventListener("click", function () {
            openPartitionFieldModal(key, partitionCode, partitionIndex);
          });
          byteOnly.appendChild(byteOnlyButton);
          controls.appendChild(byteOnly);
        }

        if (partitionName !== "data" || currentView === "bytewise") {
          const field = partitionFieldConfigurations[key]?.byte || {};
          const fieldComplete = isFieldConfigurationComplete(field);
          const action = document.createElement("button");
          action.type = "button";
          action.className = `byte-wise-action${fieldComplete ? "" : " config-missing"}`;
          action.setAttribute(
            "aria-invalid",
            fieldComplete ? "false" : "true",
          );
          action.innerHTML = `<span><strong>${fieldComplete ? "Configuration Saved" : " click here Configure Complete Partition"}</strong><small>${fieldComplete ? `${field.dataType} ${field.uiType}` : "Required form is not saved"}</small></span><i class="fas ${fieldComplete ? "fa-circle-check" : "fa-triangle-exclamation"}"></i>`;
          action.addEventListener("click", function () {
            openPartitionFieldModal(key, partitionCode, partitionIndex);
          });
          controls.appendChild(action);
          if (!fieldComplete) {
            const inlineWarning = document.createElement("div");
            inlineWarning.className = "inline-config-warning";
            inlineWarning.innerHTML =
              '<i class="fas fa-triangle-exclamation"></i> Byte-wise form must be completed and saved.';
            controls.appendChild(inlineWarning);
          }
        } else {
          const binary = hexToBinary(partitionCode);
          const bitCount = binary.length;
          const bitCuts = getBitCuts(key, bitCount);
          const bitGroups = getBitGroups(key, bitCount);
          const bitGroupByPosition = {};
          bitGroups.forEach((group, groupIndex) => {
            group.forEach((bitPosition) => {
              bitGroupByPosition[bitPosition] = { group, groupIndex };
            });
          });

          const help = document.createElement("div");
          help.className = "bit-partition-help";
          help.innerHTML =
            '<i class="fas fa-scissors"></i> Click + between bits to create partitions. Click a colored bit partition below to open its Field Configuration.';
          controls.appendChild(help);

          const timelineScroll = document.createElement("div");
          timelineScroll.className = "bit-cut-scroll";
          const timeline = document.createElement("div");
          timeline.className = "bit-cut-timeline";
          timeline.style.gridTemplateColumns = `repeat(${bitCount}, minmax(68px, 1fr))`;
          timeline.style.minWidth = `${Math.max(560, bitCount * 68)}px`;

          for (
            let bitPosition = bitCount - 1;
            bitPosition >= 0;
            bitPosition--
          ) {
            const bitValue = binary[bitCount - 1 - bitPosition];
            const groupInfo = bitGroupByPosition[bitPosition];
            const group = groupInfo.group;
            const groupColor =
              PARTITION_COLORS[groupInfo.groupIndex % PARTITION_COLORS.length];
            const bit = document.createElement("div");
            bit.className = "bit-cut-cell";
            bit.style.gridColumn = String(bitCount - bitPosition);
            bit.style.setProperty("--bit-group-color", groupColor.color);
            bit.style.setProperty("--bit-group-soft", groupColor.soft);
            bit.style.setProperty("--bit-group-text", groupColor.text);
            bit.innerHTML = `<span class="bit-cut-index">b${bitPosition}</span><span class="bit-cut-value">${bitValue}</span>`;
            timeline.appendChild(bit);
          }

          bitGroups.forEach((group, groupIndex) => {
            const groupColor =
              PARTITION_COLORS[groupIndex % PARTITION_COLORS.length];
            const configKey = bitGroupConfigKey(group);
            const groupComplete = isFieldConfigurationComplete(
              partitionFieldConfigurations[key]?.[configKey],
            );
            const rangeBlock = document.createElement("div");
            const isSingleBitGroup = group.length === 1;
            rangeBlock.className = `bit-range-block${isSingleBitGroup ? " single-bit-range" : ""}${groupComplete ? "" : " missing"}`;
            const groupStart = group[0];
            const groupEnd = group[group.length - 1];
            rangeBlock.style.gridColumn = `${bitCount - groupEnd} / ${bitCount - groupStart + 1}`;
            rangeBlock.style.setProperty(
              "--bit-group-color",
              groupColor.color,
            );
            rangeBlock.style.setProperty("--bit-group-soft", groupColor.soft);
            rangeBlock.style.setProperty("--bit-group-text", groupColor.text);
            const configureButtonText = groupComplete
              ? "Edit"
              : isSingleBitGroup
                ? "Configure"
                : "Configure Partition";
            rangeBlock.innerHTML = `<span><i class="fas ${groupComplete ? "fa-circle-check" : "fa-sliders"}"></i> ${bitGroupLabel(group)}</span><small>${groupComplete ? "Configuration saved" : "Configuration required"}</small><button type="button" class="bit-configure-btn" title="${groupComplete ? "Edit field configuration" : "Configure this bit partition"}"><i class="fas ${groupComplete ? "fa-pen" : "fa-sliders"}"></i><span>${configureButtonText}</span></button>`;
            rangeBlock.setAttribute(
              "aria-label",
              `${bitGroupLabel(group)}: ${groupComplete ? "configuration saved" : "open field configuration"}`,
            );
            const openBitGroupConfiguration = function (event) {
              event?.preventDefault();
              event?.stopPropagation();
              openPartitionFieldModal(
                key,
                partitionCode,
                partitionIndex,
                undefined,
                group,
              );
            };
            rangeBlock.addEventListener(
              "click",
              openBitGroupConfiguration,
            );
            rangeBlock
              .querySelector(".bit-configure-btn")
              .addEventListener("click", openBitGroupConfiguration);
            timeline.appendChild(rangeBlock);
          });

          for (let cut = 1; cut < bitCount; cut++) {
            const marker = document.createElement("button");
            marker.type = "button";
            const isActive = bitCuts.includes(cut);
            marker.className = `bit-cut-target${isActive ? " active" : ""}`;
            marker.style.left = `${((bitCount - cut) / bitCount) * 100}%`;
            if (isActive) {
              const rightGroupIndex = bitGroups.findIndex(
                (group) => group[0] === cut,
              );
              const markerColor =
                PARTITION_COLORS[
                (rightGroupIndex === -1 ? 0 : rightGroupIndex) %
                PARTITION_COLORS.length
                ];
              marker.style.setProperty("--bit-cut-color", markerColor.color);
            }
            marker.title = isActive
              ? `Remove cut between b${cut} and b${cut - 1}`
              : `Add cut between b${cut} and b${cut - 1}`;
            marker.setAttribute("aria-label", marker.title);
            marker.addEventListener("click", function (event) {
              event.preventDefault();
              event.stopPropagation();
              toggleBitCut(key, bitCount, cut);
            });
            timeline.appendChild(marker);
          }
          timelineScroll.appendChild(timeline);
          controls.appendChild(timelineScroll);

          const actions = document.createElement("div");
          actions.className = "bit-cut-actions";
          const clearButton = document.createElement("button");
          clearButton.type = "button";
          clearButton.className = "bit-partition-action secondary";
          clearButton.disabled = bitCuts.length === 0;
          clearButton.innerHTML =
            '<i class="fas fa-eraser"></i> Clear Bit Cuts';
          clearButton.addEventListener("click", function () {
            clearBitCuts(key, bitCount);
          });
          const count = document.createElement("span");
          count.className = "partition-limit";
          count.textContent = `${bitGroups.length} bit partition${bitGroups.length === 1 ? "" : "s"}`;
          actions.append(clearButton, count);
          controls.appendChild(actions);

          const binaryValue = document.createElement("div");
          binaryValue.className = "binary-value";
          binaryValue.textContent = `${partitionCode} = ${binary}`;
          controls.appendChild(binaryValue);

          const bitValidation = getPartitionConfigurationValidation(key);
          if (!bitValidation.valid) {
            const inlineWarning = document.createElement("div");
            inlineWarning.className = "inline-config-warning";
            inlineWarning.innerHTML = `<i class="fas fa-triangle-exclamation"></i> ${bitValidation.message}`;
            controls.appendChild(inlineWarning);
          }
        }

        band.appendChild(controls);
      }

      function updatePartitionStatus() {
        const validation = getPartitionValidation();
        const status = document.getElementById("partitionStatus");
        const confirmButton = document.getElementById("confirmPartitionsBtn");
        const hasCompleteApi =
          Boolean(fullApiValue) && fullApiValue.length % 2 === 0;
        status.className = "partition-status";

        if (partitionsConfirmed && validation.valid) {
          status.classList.add("complete");
          status.innerHTML = `<i class="fas fa-circle-check"></i><span>Step 2 complete for ${getActiveFrameLabel()} Frame ${(activePartitionFrameIndex ?? 0) + 1}: ${validation.message}</span>`;
          confirmButton.innerHTML =
            'Frame Partitions Saved <i class="fas fa-check"></i>';
        } else {
          if (showPartitionWarnings && hasCompleteApi && !validation.valid) {
            status.classList.add("error");
            status.innerHTML = `<i class="fas fa-triangle-exclamation"></i><span>${validation.message}</span>`;
          } else {
            status.innerHTML = `<i class="fas fa-circle-info"></i><span>${hasCompleteApi
                ? "Tip: click + between bytes to cut, then name each partition below."
                : "Enter the API, create partitions, and name every block."
              }</span>`;
          }
          confirmButton.innerHTML =
            'Save Frame Partitions <i class="fas fa-check"></i>';
        }
        confirmButton.disabled = !hasCompleteApi;
      }

      function renderApiPartitionEditor() {
        const byteCount = getApiBytes().length;
        const hasRestoredLayout = !fullApiValue && restoredByteCount > 0;
        const isComplete =
          (Boolean(fullApiValue) && fullApiValue.length % 2 === 0) ||
          hasRestoredLayout;
        partitionCuts = partitionCuts.filter(
          (cut) => cut > 0 && cut < byteCount,
        );
        document.getElementById("apiByteCount").innerHTML =
          hasRestoredLayout
            ? `<i class="fas fa-database"></i> ${byteCount} saved byte positions restored`
            : `<i class="fas fa-ruler-horizontal"></i> ${isComplete ? byteCount : Math.floor(fullApiValue.length / 2)} bytes, ${fullApiValue.length} hex characters`;
        document.getElementById("apiError").textContent =
          fullApiValue && !isComplete
            ? "Incomplete byte: every byte needs exactly 2 hexadecimal characters."
            : hasRestoredLayout
              ? "Saved partitions and configurations were restored. Enter the original full API only if you need to change its byte values."
            : "";
        document
          .getElementById("partitionReadyHint")
          .classList.toggle(
            "show",
            Boolean(fullApiValue) && isComplete && byteCount >= 2,
          );
        document.getElementById("partitionLimitText").textContent =
          byteCount < 2
            ? "Enter at least 2 bytes to add a cut."
            : `${getPartitionRanges().length} of ${byteCount} possible partitions`;
        document.getElementById("clearPartitionsBtn").disabled =
          partitionCuts.length === 0;
        document.getElementById("addPartitionBtn").disabled =
          byteCount < 2 || partitionCuts.length >= byteCount - 1;
        renderApiTimeline();
        renderPartitionVisualBlocks();
        updatePartitionStatus();
        updateLinkedApiPanel();
      }

      document
        .getElementById("fullApiInput")
        .addEventListener("input", function () {
          const cleaned = cleanFullApi(this.value);
          if (this.value !== cleaned) this.value = cleaned;
          fullApiValue = cleaned;
          restoredByteCount = cleaned
            ? Math.ceil(cleaned.length / 2)
            : 0;
          partitionCuts = partitionCuts.filter(
            (cut) => cut < getApiBytes().length,
          );
          setPartitionChanged();
          renderApiPartitionEditor();
        });

      document
        .getElementById("addPartitionBtn")
        .addEventListener("click", function () {
          const byteCount = getApiBytes().length;
          if (byteCount < 2) return;

          const previousScrollLeft = getApiTimelineScrollLeft();
          const nextCut = Array.from(
            { length: byteCount - 1 },
            (_, index) => index + 1,
          ).find((cut) => !partitionCuts.includes(cut));

          if (!nextCut) return;

          partitionCuts = [...partitionCuts, nextCut].sort((a, b) => a - b);
          setPartitionChanged();
          renderApiPartitionEditor();
          restoreApiTimelineScroll(previousScrollLeft);
        });

      document
        .getElementById("clearPartitionsBtn")
        .addEventListener("click", function () {
          const previousScrollLeft = getApiTimelineScrollLeft();
          partitionCuts = [];
          setPartitionChanged();
          renderApiPartitionEditor();
          restoreApiTimelineScroll(previousScrollLeft);
        });

      document
        .getElementById("confirmPartitionsBtn")
        .addEventListener("click", async function () {
          const validation = getPartitionValidation();
          if (

            !validation.valid ||
            !Number.isInteger(activePartitionFrameIndex)
          ) {
            showPartitionWarnings = true;
            renderApiPartitionEditor();
            const firstMissing = document.querySelector(
              ".partition-name-band.show-warning, .byte-wise-action.config-missing, .bit.config-missing",
            );
            firstMissing?.scrollIntoView({
              behavior: "smooth",
              block: "center",
            });
            return;
          }
          showPartitionWarnings = false;
          partitionsConfirmed = true;
          captureActiveFramePartitionState();
          syncFrameStructureFromStepOne();
          const generatedCount = document.querySelectorAll(
            "#commandFramesContainer .command-frame-card",
          ).length;
          applyStepOneTemplateToFrames(
            generatedCount,
            true,
            activePartitionFrameIndex,
          );
          captureActiveFramePartitionState();
          updatePartitionStatus();
          updateLinkedApiPanel();
          if (generatedCount > 0 && checkRequirements()) {
            generateCommandFrames(generatedCount, true);
            updateFieldDescFrameSelect();
            renderFieldDescription();
          }
          updateFramePartitionStates();
          const confirmButton = document.getElementById(
            "confirmPartitionsBtn",
          );
          if (confirmButton) {
            confirmButton.disabled = true;
            confirmButton.setAttribute("aria-busy", "true");
          }
          try {
            await persistConfiguration(
              `Frame ${activePartitionFrameIndex + 1} saved`,
            );
            moveToCommandResponseErrorFlow(true);
          } catch (error) {
            console.error("Unable to save the frame partitions.", error);
            updateSaveBadge("error", "Frame partition save failed");
            alert(error.message || "The frame partitions could not be saved.");
          } finally {
            if (confirmButton) {
              confirmButton.removeAttribute("aria-busy");
            }
            updatePartitionStatus();
          }
        });

      function generateByteValue(index) {
        return "0x" + String(index + 1).padStart(2, "0");
      }

      function initBytes(count) {
        bytes = [];
        for (let i = 0; i < count; i++) {
          bytes.push({ id: i, value: generateByteValue(i), active: false });
        }
      }
      initBytes(8);
      if (bytes.length >= 7) {
        bytes[0].active = true;
        bytes[2].active = true;
        bytes[6].active = true;
      }
      function checkRequirements() {
        const paramName = document
          .getElementById("paramNameInput")
          ?.value?.trim();
        const frameCount = Number.parseInt(
          document.getElementById("cmdFrameCountInput")?.value,
          10,
        );
        return (
          Boolean(paramName) &&
          Number.isInteger(frameCount) &&
          frameCount >= 1
        );
      }


      const requiredDropdowns = [
        {
          id: "paramNameInput",
          label: "Parameter Name",
          icon: "fa-signature",
          message: "Enter the parameter name.",
          isInput: true,
        },
        {
          id: "cmdFrameCountInput",
          label: "No. of Frames",
          icon: "fa-layer-group",
          message: () =>
            `Enter at least 1 ${getActiveFrameLabel().toLowerCase()} frame.`,
          isInput: true,
          validate: (el) => {
            const count = Number.parseInt(el.value, 10);
            return Number.isInteger(count) && count >= 1;
          },
        },
      ];
      function isRequiredFieldValid(field) {
        const el = document.getElementById(field.id);
        if (!el) return false;
        if (typeof field.validate === "function") return field.validate(el);
        if (field.isInput) return Boolean(el.value?.trim());
        return Boolean(el.value);
      }

      function updateSetupFieldHints() {
        requiredDropdowns.forEach((field) => {
          const hint = document.querySelector(
            `.field-setup-hint[data-for="${field.id}"]`,
          );
          const item = document
            .getElementById(field.id)
            ?.closest(".param-item");
          const valid = isRequiredFieldValid(field);
          if (hint) hint.hidden = valid || showSetupWarnings;
          item?.classList.toggle("has-error", showSetupWarnings && !valid);
        });
      }

      function updateRequiredDropdownWarnings(showWarnings = true) {
        showSetupWarnings = showWarnings;
        requiredDropdowns.forEach((field) => {
          const { id, message, isInput } = field;
          const control = document.getElementById(id);
          if (!control) return;
          const wrapper = isInput
            ? control.closest(".input-with-icon")
            : control.closest(".select-with-icon");
          if (!wrapper) return;
          let warning = wrapper.parentElement.querySelector(
            `.dropdown-warning[data-for="${id}"]`,
          );
          if (!warning) {
            warning = document.createElement("span");
            warning.className = "dropdown-warning";
            warning.dataset.for = id;
            warning.setAttribute("role", "alert");
            warning.innerHTML =
              '<i class="fas fa-triangle-exclamation"></i><span></span>';
            wrapper.insertAdjacentElement("afterend", warning);
          }
          const warningMessage =
            typeof message === "function" ? message() : message;
          warning.querySelector("span").textContent = warningMessage;
          const invalid = !isRequiredFieldValid(field);
          wrapper.classList.toggle(
            isInput ? "input-error" : "dropdown-error",
            invalid && showWarnings,
          );
          control.setAttribute(
            "aria-invalid",
            String(invalid && showWarnings),
          );
          warning.hidden = !(invalid && showWarnings);
        });
        updateSetupFieldHints();
      }

      function showRequirementPopup() {
        const missing = requiredDropdowns.filter(
          (field) => !isRequiredFieldValid(field),
        );
        updateRequiredDropdownWarnings(true);
        if (!missing.length) return;

        const overlay = document.getElementById("generateValidationOverlay");
        const list = document.getElementById("generateMissingList");
        const message = document.getElementById("generateValidationMessage");
        if (message) {
          message.textContent =
            `Fill the highlighted fields below, then click Generate ${getActiveFrameLabel()} Frames again.`;
        }
        list.innerHTML = missing
          .map(
            ({ label, icon }) =>
              `<li class="generate-missing-item"><i class="fas ${icon}" aria-hidden="true"></i><span>${label} is required</span></li>`,
          )
          .join("");
        overlay.dataset.firstMissing = missing[0].id;
        overlay.classList.add("open");
        overlay.setAttribute("aria-hidden", "false");
        document.getElementById("continueSetupBtn").focus();
      }

      function closeRequirementPopup(focusMissingField = false) {
        const overlay = document.getElementById("generateValidationOverlay");
        const firstMissingId = overlay.dataset.firstMissing;
        overlay.classList.remove("open");
        overlay.setAttribute("aria-hidden", "true");
        if (focusMissingField && firstMissingId) {
          const field = document.getElementById(firstMissingId);
          field
            ?.closest(".param-item")
            ?.scrollIntoView({ behavior: "smooth", block: "center" });
          setTimeout(() => field?.focus(), 250);
        }
      }

      document
        .getElementById("continueSetupBtn")
        .addEventListener("click", () => closeRequirementPopup(true));
      document
        .getElementById("generateValidationOverlay")
        .addEventListener("click", function (event) {
          if (event.target === this) closeRequirementPopup(true);
        });
      document.addEventListener("keydown", function (event) {
        if (
          event.key === "Escape" &&
          document
            .getElementById("generateValidationOverlay")
            .classList.contains("open")
        ) {
          closeRequirementPopup(true);
        }
      });

      function renderAll() {
        renderByteRow("byteRowContainer", bytes);
        updateSelectedInfo();
        updateFrameSizeLabel();
        const count =
          parseInt(document.getElementById("cmdFrameCountInput").value) || 0;
        if (count > 0 && bytes.length > 0 && checkRequirements()) {
          generateCommandFrames(count, true);
        } else {
          updateCommandFrameCount();
        }
        updateFieldDescFrameSelect();
        renderFieldDescription();
      }

      function renderByteRow(containerId, byteArray) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = "";
        byteArray.forEach((b, idx) => {
          const div = document.createElement("div");
          div.className = "byte-item" + (b.active ? " active" : "");
          div.dataset.index = idx;
          div.innerHTML = `<span class="byte-label">Byte ${idx + 1}</span><span class="byte-value">${b.value}</span>`;
          div.addEventListener("click", function (e) {
            e.stopPropagation();
            toggleByte(idx);
          });
          container.appendChild(div);
        });
      }

      function toggleByte(index) {
        if (index >= 0 && index < bytes.length) {
          bytes[index].active = !bytes[index].active;
          renderAll();
          const count =
            parseInt(document.getElementById("cmdFrameCountInput").value) ||
            0;
          if (count > 0 && bytes.length > 0 && checkRequirements()) {
            generateCommandFrames(count, true);
          }
        }
      }

      function updateSelectedInfo() {
        const activeIndices = bytes
          .map((b, i) => (b.active ? i + 1 : null))
          .filter((v) => v !== null);
        const selectedCount = document.getElementById("selectedCount");
        const selectedBytesList =
          document.getElementById("selectedBytesList");
        if (selectedCount) selectedCount.textContent = activeIndices.length;
        if (selectedBytesList) {
          selectedBytesList.textContent =
            activeIndices.length > 0 ? activeIndices.join(", ") : "None";
        }
      }

      function updateFrameSizeLabel() {
        const frameSizeLabel = document.getElementById("frameSizeLabel");
        if (frameSizeLabel) frameSizeLabel.textContent = bytes.length;
      }

      function generate8Bytes() {
        initBytes(8);
        if (bytes.length >= 7) {
          bytes[0].active = true;
          bytes[2].active = true;
          bytes[6].active = true;
        }
        renderAll();
        const count =
          parseInt(document.getElementById("cmdFrameCountInput").value) || 0;
        if (count > 0 && checkRequirements()) {
          generateCommandFrames(count, true);
        }
      }

      const addByteBtn = document.getElementById("addByteBtn");
      if (addByteBtn) {
        addByteBtn.addEventListener("click", function () {
          const newId = bytes.length;
          bytes.push({
            id: newId,
            value: generateByteValue(newId),
            active: false,
          });
          renderAll();
          const count =
            parseInt(document.getElementById("cmdFrameCountInput").value) ||
            0;
          if (count > 0 && checkRequirements()) {
            generateCommandFrames(count, true);
          }
        });
      }

      const resetBytesBtn = document.getElementById("resetBytesBtn");
      if (resetBytesBtn) {
        resetBytesBtn.addEventListener("click", function () {
          generate8Bytes();
        });
      }

      function generateCommandFrames(count, preserveSelection) {
        if (!preserveSelection && !checkRequirements()) {
          showRequirementPopup();
          return;
        }

        const container = document.getElementById("commandFramesContainer");
        container.replaceChildren();
        container.dataset.visibleFrameType = activeFrameType;
        container.setAttribute(
          "aria-label",
          `${getActiveFrameLabel()} frames only`,
        );
        const byteSize = bytes.length;
        if (byteSize === 0) {
          document.getElementById("cmdFrameCountDisplay").textContent = 0;
          return;
        }

        if (!preserveSelection) {
          frameCodes = frameCodes.slice(0, count);
          frameValueGroups = frameValueGroups.slice(0, count);
          frameConfigurations = frameConfigurations.slice(0, count);
          const newFieldDesc = {};
          const newByteDesc = {};
          const newViewModes = {};
          for (let i = 0; i < count; i++) {
            if (fieldDescriptions[i]) newFieldDesc[i] = fieldDescriptions[i];
            if (byteDescriptions[i]) newByteDesc[i] = byteDescriptions[i];
            if (fieldViewModes[i]) newViewModes[i] = fieldViewModes[i];
          }
          fieldDescriptions = newFieldDesc;
          byteDescriptions = newByteDesc;
          fieldViewModes = newViewModes;
        }
        while (frameCodes.length < count) frameCodes.push("");
        while (frameValueGroups.length < count) frameValueGroups.push([]);
        while (framePartitionStates.length < count)
          framePartitionStates.push(null);
        while (frameConfigurations.length < count) {
          frameConfigurations.push({
            dataFlowDirection: "",
            multipleUi: "",
          });
        }
        framePartitionStates = framePartitionStates.slice(0, count);
        frameConfigurations = frameConfigurations.slice(0, count);
        if (
          Number.isInteger(activePartitionFrameIndex) &&
          activePartitionFrameIndex >= count
        ) {
          activePartitionFrameIndex = null;
          selectedFrameIndex = null;
          document
            .getElementById("apiPartitionSection")
            .classList.remove("frame-partition-active");
        }
        if (Number.isInteger(activePartitionFrameIndex)) {
          applyStepOneTemplateToFrames(
            count,
            false,
            activePartitionFrameIndex,
          );
        }

        for (let i = 0; i < count; i++) {
          const card = document.createElement("div");
          card.dataset.frameType = activeFrameType;
          const fillStatus = getFrameFillStatus(i);
          card.className = "command-frame-card";
          if (fillStatus === "filled") card.classList.add("partition-complete");
          else if (fillStatus === "in-progress")
            card.classList.add("frame-in-progress");
          else card.classList.add("needs-partition");
          card.style.animationDelay = `${Math.min(i * 55, 330)}ms`;
          if (selectedFrameIndex === i) card.classList.add("selected");
          const frameName = `${getActiveFrameLabel()} Frame ${i + 1}`;
          const codeText = frameCodes[i] || "Not entered";
          const frameConfiguration = frameConfigurations[i] || {
            dataFlowDirection: "",
            multipleUi: "",
          };
          const directionText =
            frameConfiguration.dataFlowDirection === "server_to_device"
              ? "Server to Device"
              : frameConfiguration.dataFlowDirection === "device_to_server"
                ? "Device to Server"
                : "Direction not selected";
          const directionIcon =
            frameConfiguration.dataFlowDirection === "device_to_server"
              ? "fa-arrow-left"
              : frameConfiguration.dataFlowDirection === "server_to_device"
                ? "fa-arrow-right"
                : "fa-right-left";
          const statusBadge =
            fillStatus === "filled"
              ? '<span class="frame-status-badge filled"><i class="fas fa-circle-check"></i> Filled</span>'
              : fillStatus === "in-progress"
                ? '<span class="frame-status-badge in-progress"><i class="fas fa-spinner"></i> In progress</span>'
                : '<span class="frame-status-badge pending"><i class="fas fa-hourglass-half"></i> Pending</span>';
          const stateHtml =
            fillStatus === "filled"
              ? '<div class="frame-partition-state complete"><i class="fas fa-circle-check"></i> API partitioned</div>'
              : fillStatus === "in-progress"
                ? '<div class="frame-partition-state in-progress"><i class="fas fa-pen-to-square"></i> Finish partitioning</div>'
                : '<div class="frame-partition-state"><i class="fas fa-hand-pointer"></i> Click to enter API</div>';

          const configLabel =
            fillStatus === "filled"
              ? "Open Configuration"
              : fillStatus === "in-progress"
                ? "Continue Configuration"
                : "Configure";

          card.innerHTML = `
                        <div class="frame-title">
                            <span>${frameName}</span>
                            <button type="button" class="remove-btn" data-idx="${i}" title="Delete ${frameName}" aria-label="Delete ${frameName}"><i class="fas fa-trash-can"></i> Delete</button>
                        </div>
                        ${statusBadge}
                        <div class="frame-direction"><i class="fas ${directionIcon}"></i> ${directionText}</div>
                        <div class="frame-settings" aria-label="${frameName} settings">
                          <div class="frame-setting">
                            <label for="frameDirection${i}"><i class="fas fa-right-left"></i> Data Flow Direction</label>
                            <select id="frameDirection${i}" class="frame-direction-select" data-idx="${i}" required>
                              <option value="" disabled ${frameConfiguration.dataFlowDirection ? "" : "selected"}>Select Direction</option>
                              <option value="server_to_device" ${frameConfiguration.dataFlowDirection === "server_to_device" ? "selected" : ""}>Server to Device</option>
                              <option value="device_to_server" ${frameConfiguration.dataFlowDirection === "device_to_server" ? "selected" : ""}>Device to Server</option>
                            </select>
                          </div>
                          <div class="frame-setting">
                            <label for="frameMultipleUi${i}"><i class="fas fa-window-restore"></i> Multiple UI</label>
                            <select id="frameMultipleUi${i}" class="frame-multiple-ui-select" data-idx="${i}" required>
                              <option value="" disabled ${frameConfiguration.multipleUi ? "" : "selected"}>Select Multiple UI</option>
                              <option value="false" ${frameConfiguration.multipleUi === "false" ? "selected" : ""}>False</option>
                              <option value="true" ${frameConfiguration.multipleUi === "true" ? "selected" : ""}>True</option>
                            </select>
                          </div>
                        </div>
                        <div class="frame-code">Code: <strong>${codeText}</strong></div>
                        ${stateHtml}
                        <button type="button" class="frame-config-btn" data-idx="${i}" title="${configLabel}" aria-label="${configLabel} for ${frameName}"><i class="fas fa-sliders"></i> ${configLabel}</button>
                    `;
          if (awaitingFrameClick && fillStatus !== "filled") {
            card.classList.add("awaiting-click");
          }

          card.addEventListener("click", function (e) {
            if (
              e.target.closest(".remove-btn") ||
              e.target.closest(".frame-config-btn")
            )
              return;
            document.getElementById("byteDetailPopup").style.display = "none";
            openFramePartitionEditor(i);
          });

          card
            .querySelector(".frame-config-btn")
            .addEventListener("click", function (e) {
              e.preventDefault();
              e.stopPropagation();
              document.getElementById("byteDetailPopup").style.display =
                "none";
              openFramePartitionEditor(i);
            });

          const deleteHandler = function (e) {
            e.preventDefault();
            e.stopPropagation();
            deleteCommandFrame(i);
          };
          card
            .querySelector(".remove-btn")
            .addEventListener("click", deleteHandler);
          card.querySelectorAll(".frame-settings, .frame-settings select")
            .forEach((element) => {
              element.addEventListener("click", (event) => {
                event.stopPropagation();
              });
            });
          card
            .querySelector(".frame-direction-select")
            .addEventListener("change", function (event) {
              event.stopPropagation();
              frameConfigurations[i].dataFlowDirection = this.value;
              const direction = card.querySelector(".frame-direction");
              const isServerToDevice = this.value === "server_to_device";
              direction.innerHTML = `<i class="fas ${isServerToDevice ? "fa-arrow-right" : "fa-arrow-left"}"></i> ${isServerToDevice ? "Server to Device" : "Device to Server"}`;
              this.classList.remove("frame-field-error");
              markConfigurationUnsaved();
            });
          card
            .querySelector(".frame-multiple-ui-select")
            .addEventListener("change", function (event) {
              event.stopPropagation();
              frameConfigurations[i].multipleUi = this.value;
              this.classList.remove("frame-field-error");
              markConfigurationUnsaved();
            });
          container.appendChild(card);
        }

        if (count < 20) {
          const addCard = document.createElement("button");
          addCard.type = "button";
          addCard.className = "add-frame-card";
          addCard.id = "addCommandFrameCard";
          addCard.setAttribute(
            "aria-label",
            `Add a new ${getActiveFrameLabel().toLowerCase()} frame`,
          );
          addCard.innerHTML = `
              <span class="add-frame-card-inner">
                <i class="fas fa-plus"></i>
                <span>Add ${getActiveFrameLabel()} Frame</span>
              </span>
            `;
          addCard.addEventListener("click", addCommandFrame);
          container.appendChild(addCard);
        }

        updateCommandFrameCount();
        updateFramePartitionStates();
        updateFieldDescFrameSelect();
        renderFieldDescription();
        const banner = document.getElementById("framesNextBanner");
        if (banner) {
          banner.classList.toggle("show", awaitingFrameClick && count > 0);
        }
      }

      function normalizeHex(value) {
        return value.replace(/\s+/g, "").replace(/^0x/i, "").toUpperCase();
      }

      function isValidHexCommand(value) {
        return (
          value.length > 0 &&
          value.length % 2 === 0 &&
          /^[0-9A-F]+$/.test(value)
        );
      }

      function hexToBinary(value) {
        return value
          .split("")
          .map((hexDigit) =>
            parseInt(hexDigit, 16).toString(2).padStart(4, "0"),
          )
          .join("");
      }

      function renderCommandCodeBoxes(command, frameIdx) {
        const grid = document.getElementById("byteDetailGrid");
        grid.innerHTML = "";
        if (!command) {
          grid.innerHTML =
            '<div class="empty-command">Enter command values above. Each value field will be shown as one box.</div>';
          return;
        }
        const savedGroups = frameValueGroups[frameIdx] || [];
        const displayGroups = savedGroups.length ? savedGroups : [command];
        const line = document.createElement("div");
        line.className = "command-code-line";
        const label = document.createElement("span");
        label.className = "command-code-label";
        label.textContent = "Command Code:";
        const row = document.createElement("div");
        row.className = "command-character-row";
        displayGroups.forEach((groupValue, groupIndex) => {
          const box = document.createElement("span");
          box.className = "command-character clickable";
          applyPartitionColor(box, groupIndex);
          const byteVal = groupValue;
          box.textContent = byteVal;
          box.dataset.byteValue = byteVal;
          box.dataset.byteIndex = groupIndex;
          box.dataset.frameIdx = frameIdx;
          box.addEventListener("click", function (e) {
            e.stopPropagation();
            const clickedFrame = parseInt(this.dataset.frameIdx);
            const byteIndex = parseInt(this.dataset.byteIndex);
            const fieldSelect = document.getElementById(
              "fieldDescFrameSelect",
            );
            if (!fieldSelect) return;
            fieldSelect.value = String(clickedFrame);
            renderFieldDescription();
            const target = document.querySelector(
              `.field-desc-card[data-byte-index="${byteIndex}"]`,
            );
            if (target) {
              target.scrollIntoView({ behavior: "smooth", block: "center" });
              target.style.boxShadow = "0 0 0 3px rgba(169,139,118,.30)";
              setTimeout(() => {
                target.style.boxShadow = "";
              }, 900);
            }
          });
          row.appendChild(box);
        });
        line.appendChild(label);
        line.appendChild(row);
        grid.appendChild(line);
      }

      // ---- Byte Field Modal ----
      let currentModalFrameIdx = null;
      let currentModalByteKey = null;
      let currentModalBitPosition = null;
      let currentModalBitPositions = null;
      let currentModalByteIndex = null;
      let currentModalPartitionKey = null;

      function openByteFieldModal(frameIdx, byteVal, byteIndex, bitPos) {
        currentModalPartitionKey = null;
        currentModalFrameIdx = frameIdx;
        currentModalByteIndex = byteIndex;
        currentModalBitPosition = bitPos !== undefined ? bitPos : null;
        currentModalBitPositions = null;
        currentModalByteKey =
          currentModalBitPosition === null
            ? `byte:${byteIndex}`
            : `bit:${byteIndex}:${currentModalBitPosition}`;

        let displayLabel = `Value ${byteIndex + 1} ${byteVal}`;
        if (bitPos !== undefined) {
          const binaryValue = hexToBinary(byteVal);
          const bitValue =
            binaryValue[binaryValue.length - 1 - bitPos] || "0";
          displayLabel = `Value ${byteIndex + 1} ${byteVal} Bit ${bitPos} = ${bitValue}`;
        }
        document.getElementById("modalByteCode").textContent = displayLabel;

        const existing =
          fieldDescriptions[frameIdx]?.[currentModalByteKey] || {};
        document.getElementById("fDataType").value =
          existing.dataType || "unsigned integer";
        document.getElementById("fValueDef").value =
          existing.valueDef || "fixed value";
        document.getElementById("fUiType").value = existing.uiType || "none";
        document.getElementById("fUiInOut").value =
          existing.uiInOut || "read";
        document.getElementById("fScalling").value = existing.scalling || "";

        clearFieldFormWarnings();
        renderUiTypePicker(existing.uiType || "none");
        document.getElementById("byteFieldModal").style.display = "flex";
      }

      function openPartitionFieldModal(
        partitionKeyValue,
        partitionCode,
        partitionIndex,
        bitPos,
        bitGroup,
      ) {
        currentModalPartitionKey = partitionKeyValue;
        currentModalFrameIdx = null;
        currentModalByteIndex = partitionIndex;
        currentModalBitPositions = Array.isArray(bitGroup)
          ? [...bitGroup].sort((a, b) => a - b)
          : null;
        currentModalBitPosition =
          currentModalBitPositions?.length === 1
            ? currentModalBitPositions[0]
            : bitPos !== undefined
              ? bitPos
              : null;
        currentModalByteKey = currentModalBitPositions
          ? bitGroupConfigKey(currentModalBitPositions)
          : currentModalBitPosition === null
            ? "byte"
            : `bit:${currentModalBitPosition}`;

        let displayLabel = `Partition ${partitionIndex + 1} ${partitionCode}`;
        if (currentModalBitPositions) {
          displayLabel = `Partition ${partitionIndex + 1} ${partitionCode} ${bitGroupLabel(currentModalBitPositions)}`;
        } else if (bitPos !== undefined) {
          const binaryValue = hexToBinary(partitionCode);
          const bitValue =
            binaryValue[binaryValue.length - 1 - bitPos] || "0";
          displayLabel = `Partition ${partitionIndex + 1} ${partitionCode} Bit ${bitPos} = ${bitValue}`;
        }
        document.getElementById("modalByteCode").textContent = displayLabel;

        const existing =
          partitionFieldConfigurations[partitionKeyValue]?.[
          currentModalByteKey
          ] || {};
        document.getElementById("fDataType").value =
          existing.dataType || "unsigned integer";
        document.getElementById("fValueDef").value =
          existing.valueDef || "fixed value";
        document.getElementById("fUiType").value = existing.uiType || "none";
        document.getElementById("fUiInOut").value =
          existing.uiInOut || "read";
        document.getElementById("fScalling").value = existing.scalling || "";
        clearFieldFormWarnings();
        renderUiTypePicker(existing.uiType || "none");
        document.getElementById("byteFieldModal").style.display = "flex";
      }

      function closeByteFieldModal() {
        document.getElementById("byteFieldModal").style.display = "none";
        currentModalFrameIdx = null;
        currentModalByteKey = null;
        currentModalBitPosition = null;
        currentModalBitPositions = null;
        currentModalByteIndex = null;
        currentModalPartitionKey = null;
      }

      document
        .getElementById("closeFieldModal")
        .addEventListener("click", closeByteFieldModal);
      document
        .getElementById("cancelFieldModal")
        .addEventListener("click", closeByteFieldModal);
      document
        .getElementById("byteFieldModal")
        .addEventListener("click", function (e) {
          if (e.target === this) closeByteFieldModal();
        });

      function clearFieldFormWarnings() {
        const warning = document.getElementById("fieldFormWarning");
        if (warning) warning.classList.remove("show");
        document
          .querySelectorAll("#modalFieldForm .form-group.field-invalid")
          .forEach((group) => {
            group.classList.remove("field-invalid");
          });
        document
          .querySelectorAll('#modalFieldForm [aria-invalid="true"]')
          .forEach((control) => {
            control.setAttribute("aria-invalid", "false");
          });
      }

      function validateFieldForm() {
        clearFieldFormWarnings();
        const requiredSelects = [
          document.getElementById("fDataType"),
          document.getElementById("fValueDef"),
          document.getElementById("fUiType"),
          document.getElementById("fUiInOut"),
        ];
        const invalidControls = [];
        requiredSelects.forEach((control) => {
          if (!control.value) invalidControls.push(control);
        });

        if (!invalidControls.length) return true;

        invalidControls.forEach((control) => {
          control.setAttribute("aria-invalid", "true");
          control.closest(".form-group")?.classList.add("field-invalid");
        });
        const warning = document.getElementById("fieldFormWarning");
        if (warning) warning.classList.add("show");
        invalidControls[0].focus();
        return false;
      }

      document
        .querySelectorAll("#modalFieldForm input, #modalFieldForm select")
        .forEach((control) => {
          control.addEventListener("input", function () {
            this.closest(".form-group")?.classList.remove("field-invalid");
            this.setAttribute("aria-invalid", "false");
          });
          control.addEventListener("change", function () {
            this.closest(".form-group")?.classList.remove("field-invalid");
            this.setAttribute("aria-invalid", "false");
          });
        });

      document
        .getElementById("saveFieldModal")
        .addEventListener("click", function () {
          if (currentModalByteKey === null) return;
          if (
            currentModalFrameIdx === null &&
            currentModalPartitionKey === null
          )
            return;
          if (!validateFieldForm()) return;

          const configuration = {
            dataType: document.getElementById("fDataType").value,
            valueDef: document.getElementById("fValueDef").value,
            uiType: document.getElementById("fUiType").value,
            uiInOut: document.getElementById("fUiInOut").value,
            scalling: document.getElementById("fScalling").value,
            bitPosition: currentModalBitPosition,
            bitPositions: currentModalBitPositions
              ? [...currentModalBitPositions]
              : null,
            byteIndex: currentModalByteIndex,
          };

          if (currentModalPartitionKey !== null) {
            const partitionKeyValue = currentModalPartitionKey;
            const partitionIndex = getPartitionRanges().findIndex(
              (range) => partitionKey(range) === partitionKeyValue,
            );
            if (!partitionFieldConfigurations[partitionKeyValue]) {
              partitionFieldConfigurations[partitionKeyValue] = {};
            }
            partitionFieldConfigurations[partitionKeyValue][
              currentModalByteKey
            ] = configuration;
            setPartitionChanged();
            closeByteFieldModal();
            renderApiPartitionEditor();
            if (partitionIndex !== -1)
              openPartitionModePopup(partitionKeyValue, partitionIndex);
            persistConfiguration("Field saved");
            return;
          }

          if (!fieldDescriptions[currentModalFrameIdx]) {
            fieldDescriptions[currentModalFrameIdx] = {};
          }
          fieldDescriptions[currentModalFrameIdx][currentModalByteKey] =
            configuration;
          closeByteFieldModal();
          renderFieldDescription();
          updateFieldDescFrameSelect();
          persistConfiguration("Field saved");
        });

      // ---- Field Description Rendering ----
      function renderFieldDescription() {
        const container = document.getElementById("fieldDescContainer");
        const frameSelect = document.getElementById("fieldDescFrameSelect");
        if (!container || !frameSelect) return;
        const selectedFrame = parseInt(frameSelect.value);

        if (
          isNaN(selectedFrame) ||
          !frameCodes[selectedFrame] ||
          frameCodes[selectedFrame].length === 0
        ) {
          container.innerHTML =
            `<div class="field-empty"><i class="fas fa-code"></i><span>Enter a ${getActiveFrameLabel().toLowerCase()} frame code to configure its fields.</span></div>`;
          return;
        }

        const command = frameCodes[selectedFrame];
        const savedGroups = frameValueGroups[selectedFrame] || [];
        const commandBytes = savedGroups.length
          ? savedGroups.slice()
          : [command];

        container.innerHTML = "";
        if (!byteDescriptions[selectedFrame])
          byteDescriptions[selectedFrame] = {};
        if (!fieldViewModes[selectedFrame])
          fieldViewModes[selectedFrame] = {};

        commandBytes.forEach((byteVal, idx) => {
          const byteKey = `byte:${idx}`;
          const currentDesc = byteDescriptions[selectedFrame][idx] || "";
          const currentView =
            fieldViewModes[selectedFrame][idx] || "bytewise";
          const wrapper = document.createElement("div");
          wrapper.className = "field-desc-card";
          applyPartitionColor(wrapper, idx);
          wrapper.dataset.byteIndex = idx;

          const topRow = document.createElement("div");
          topRow.className = "field-card-top";
          const byteLabel = document.createElement("span");
          byteLabel.className = "field-byte-badge";
          byteLabel.textContent = byteVal;
          topRow.appendChild(byteLabel);

          const byteMeta = document.createElement("div");
          byteMeta.className = "field-byte-meta";
          const indexLabel = document.createElement("span");
          indexLabel.className = "field-byte-index";
          indexLabel.textContent = `Value ${idx + 1} 0x${byteVal}`;
          byteMeta.appendChild(indexLabel);

          const select = document.createElement("select");
          select.className = "byte-field-select";
          select.setAttribute(
            "aria-label",
            `Description for value ${idx + 1} ${byteVal}`,
          );
          const emptyOpt = document.createElement("option");
          emptyOpt.value = "";
          emptyOpt.textContent = "Select field description";
          select.appendChild(emptyOpt);
          FIELD_DESC_OPTIONS.forEach((opt) => {
            const option = document.createElement("option");
            option.value = opt;
            option.textContent = opt.charAt(0).toUpperCase() + opt.slice(1);
            if (opt === currentDesc) option.selected = true;
            select.appendChild(option);
          });
          select.addEventListener("change", function (e) {
            e.stopPropagation();
            byteDescriptions[selectedFrame][idx] = this.value;
            if (this.value !== "data")
              delete fieldViewModes[selectedFrame][idx];
            else if (!fieldViewModes[selectedFrame][idx])
              fieldViewModes[selectedFrame][idx] = "bytewise";
            renderFieldDescription();
          });
          byteMeta.appendChild(select);
          topRow.appendChild(byteMeta);
          wrapper.appendChild(topRow);

          if (currentDesc) {
            const cardBody = document.createElement("div");
            cardBody.className = "field-card-body";

            if (currentDesc === "data") {
              const toggleDiv = document.createElement("div");
              toggleDiv.className = "view-toggle-buttons";
              const btnByte = document.createElement("button");
              btnByte.type = "button";
              btnByte.className =
                "view-toggle-btn" +
                (currentView === "bytewise" ? " active" : "");
              btnByte.innerHTML = '<i class="fas fa-cube"></i> Byte Wise';
              const btnBit = document.createElement("button");
              btnBit.type = "button";
              btnBit.className =
                "view-toggle-btn" +
                (currentView === "bitwise" ? " active" : "");
              btnBit.innerHTML = '<i class="fas fa-grip"></i> Bit Wise';
              btnByte.addEventListener("click", function () {
                fieldViewModes[selectedFrame][idx] = "bytewise";
                renderFieldDescription();
              });
              btnBit.addEventListener("click", function () {
                fieldViewModes[selectedFrame][idx] = "bitwise";
                renderFieldDescription();
              });
              toggleDiv.append(btnByte, btnBit);
              cardBody.appendChild(toggleDiv);
            } else {
              const byteOnlyLabel = document.createElement("div");
              byteOnlyLabel.className = "view-toggle-buttons single-view";
              const byteOnlyButton = document.createElement("button");
              byteOnlyButton.type = "button";
              byteOnlyButton.className = "view-toggle-btn active";
              byteOnlyButton.innerHTML =
                '<i class="fas fa-cube"></i> Byte Wise';
              byteOnlyButton.title = "Open complete byte configuration";
              byteOnlyButton.addEventListener("click", function () {
                openByteFieldModal(selectedFrame, byteVal, idx);
              });
              byteOnlyLabel.appendChild(byteOnlyButton);
              cardBody.appendChild(byteOnlyLabel);
            }

            if (currentDesc !== "data" || currentView === "bytewise") {
              const field = fieldDescriptions[selectedFrame]?.[byteKey] || {};
              const action = document.createElement("button");
              action.type = "button";
              action.className = "byte-wise-action";
              action.innerHTML = `<span><strong>Configure Complete Byte</strong><small>${field.dataType || "Unsigned integer"} ${field.uiType || "No UI selected"}</small></span><i class="fas fa-chevron-right"></i>`;
              action.addEventListener("click", function () {
                openByteFieldModal(selectedFrame, byteVal, idx);
              });
              cardBody.appendChild(action);
            } else {
              const labels = document.createElement("div");
              labels.className = "binary-labels";
              const binary = hexToBinary(byteVal);
              const bitCount = binary.length;
              labels.style.gridTemplateColumns = `repeat(${bitCount}, minmax(22px, 1fr))`;
              for (let b = bitCount - 1; b >= 0; b--) {
                const label = document.createElement("span");
                label.textContent = `b${b}`;
                labels.appendChild(label);
              }
              cardBody.appendChild(labels);

              const bitContainer = document.createElement("div");
              bitContainer.className = "bitwise-bits";
              bitContainer.style.gridTemplateColumns = `repeat(${bitCount}, minmax(22px, 1fr))`;
              for (let b = bitCount - 1; b >= 0; b--) {
                const bitVal = binary[bitCount - 1 - b];
                const bitEl = document.createElement("button");
                bitEl.type = "button";
                bitEl.className = `bit bit-${bitVal}`;
                bitEl.textContent = bitVal;
                bitEl.title = `Configure Bit ${b} (value ${bitVal})`;
                bitEl.setAttribute(
                  "aria-label",
                  `Configure bit ${b}, value ${bitVal}`,
                );
                bitEl.addEventListener("click", function () {
                  openByteFieldModal(selectedFrame, byteVal, idx, b);
                });
                bitContainer.appendChild(bitEl);
              }
              cardBody.appendChild(bitContainer);
              const binaryValue = document.createElement("div");
              binaryValue.className = "binary-value";
              binaryValue.textContent = `${byteVal} = ${binary}`;
              cardBody.appendChild(binaryValue);
            }
            wrapper.appendChild(cardBody);
          }

          container.appendChild(wrapper);
        });
      }

      function updateFieldDescFrameSelect() {
        const select = document.getElementById("fieldDescFrameSelect");
        if (!select) return;
        const currentVal = select.value;
        select.innerHTML = '<option value="">Select Frame</option>';
        const count = document.querySelectorAll(
          "#commandFramesContainer .command-frame-card",
        ).length;
        for (let i = 0; i < count; i++) {
          const opt = document.createElement("option");
          opt.value = i;
          opt.textContent = `${getActiveFrameLabel()} Frame ${i + 1}`;
          if (frameCodes[i] && frameCodes[i].length > 0) {
            opt.textContent += ` (${frameCodes[i]})`;
          }
          select.appendChild(opt);
        }
        if (
          currentVal &&
          select.querySelector(`option[value="${currentVal}"]`)
        ) {
          select.value = currentVal;
        } else if (select.options.length > 1) {
          select.value = select.options[1].value;
        }
      }

      function showCommandEntryPopup(frameName, frameIdx) {
        const popup = document.getElementById("byteDetailPopup");
        document.getElementById("popupFrameName").textContent = frameName;
        let entry = popup.querySelector(".command-entry");
        if (!entry) {
          entry = document.createElement("div");
          entry.className = "command-entry";
          popup
            .querySelector(".popup-head")
            .insertAdjacentElement("afterend", entry);
        }
        const existingCode = frameCodes[frameIdx] || "";
        const savedGroups = frameValueGroups[frameIdx] || [];
        let entryBytes = savedGroups.length
          ? savedGroups.slice()
          : existingCode
            ? [existingCode]
            : [""];

        entry.innerHTML = `
                    <label class="control-label"><i class="fas fa-keyboard"></i> Enter ${getActiveFrameLabel().toLowerCase()} frame code byte by byte</label>
                    <div class="byte-editor-hint"><i class="fas fa-circle-info"></i> Each box accepts any number of hexadecimal characters. Example: A12C0B or 1234567890.</div>
                    <div id="commandByteCount" class="command-byte-count"><i class="fas fa-cubes"></i> Value fields: 1</div>
                    <div id="commandByteEditor" class="command-byte-editor"></div>
                    <div class="apply-command-row">
                        <button type="button" id="applyCommandCodeBtn"><i class="fas fa-eye"></i> Show Code</button>
                    </div>
                    <div class="command-error" id="commandCodeError"></div>
                `;

        const editor = entry.querySelector("#commandByteEditor");
        const errorBox = entry.querySelector("#commandCodeError");
        const byteCount = entry.querySelector("#commandByteCount");

        function cleanHexValue(value) {
          return value.toUpperCase().replace(/[^0-9A-F]/g, "");
        }

        function resizeHexInput(input) {
          const width = Math.min(
            360,
            Math.max(80, input.value.length * 11 + 30),
          );
          input.style.width = `${width}px`;
        }

        function renderByteInputs(focusIndex) {
          editor.innerHTML = "";
          byteCount.innerHTML = `<i class="fas fa-cubes"></i> Value fields: ${entryBytes.length}`;
          entryBytes.forEach((byteValue, byteIndex) => {
            const unit = document.createElement("div");
            unit.className = "command-byte-unit";

            const byteLabel = document.createElement("label");
            byteLabel.innerHTML = `<i class="fas fa-code"></i> Hex value ${byteIndex + 1}`;

            const byteInput = document.createElement("input");
            byteInput.type = "text";
            byteInput.className = "command-byte-input";
            byteInput.value = cleanHexValue(byteValue);
            byteInput.placeholder = "A12C0B";
            byteInput.inputMode = "text";
            byteInput.autocomplete = "off";
            byteInput.spellcheck = false;
            byteInput.setAttribute(
              "aria-label",
              `${getActiveFrameLabel()} frame hexadecimal value ${byteIndex + 1}`,
            );
            resizeHexInput(byteInput);

            byteInput.addEventListener("input", function () {
              const cleaned = cleanHexValue(this.value);
              this.value = cleaned;
              entryBytes[byteIndex] = cleaned;
              resizeHexInput(this);
              this.classList.remove("invalid");
              errorBox.textContent = "";
            });
            byteInput.addEventListener("keydown", function (event) {
              if (event.key === "Enter") applyCommand(event);
            });

            byteInput.addEventListener("paste", function (event) {
              const pasted = event.clipboardData
                .getData("text")
                .replace(/\s+/g, "")
                .replace(/^0x/i, "")
                .toUpperCase()
                .replace(/[^0-9A-F]/g, "");
              if (pasted) {
                event.preventDefault();
                entryBytes[byteIndex] = pasted;
                renderByteInputs(byteIndex);
              }
            });

            unit.append(byteLabel, byteInput);

            if (entryBytes.length > 1) {
              const removeButton = document.createElement("button");
              removeButton.type = "button";
              removeButton.className = "remove-command-byte";
              removeButton.innerHTML = '<i class="fas fa-xmark"></i>';
              removeButton.title = `Remove Hex Value ${byteIndex + 1}`;
              removeButton.setAttribute(
                "aria-label",
                `Remove ${getActiveFrameLabel().toLowerCase()} frame hexadecimal value ${byteIndex + 1}`,
              );
              removeButton.addEventListener("click", function (event) {
                event.preventDefault();
                event.stopPropagation();
                entryBytes.splice(byteIndex, 1);
                renderByteInputs(Math.max(0, byteIndex - 1));
              });
              unit.appendChild(removeButton);
            }
            editor.appendChild(unit);
          });

          const addButton = document.createElement("button");
          addButton.type = "button";
          addButton.className = "add-command-byte";
          addButton.innerHTML = '<i class="fas fa-plus"></i>';
          addButton.title = "Add another hexadecimal value";
          addButton.setAttribute(
            "aria-label",
            `Add another ${getActiveFrameLabel().toLowerCase()} frame hexadecimal value`,
          );
          addButton.addEventListener("click", function (event) {
            event.preventDefault();
            event.stopPropagation();
            entryBytes.push("");
            renderByteInputs(entryBytes.length - 1);
          });
          editor.appendChild(addButton);

          if (Number.isInteger(focusIndex)) {
            const inputs = editor.querySelectorAll(".command-byte-input");
            if (inputs[focusIndex]) {
              const targetInput = inputs[focusIndex];
              targetInput.classList.add("new-byte");
              targetInput.focus();
              targetInput.select();
              setTimeout(() => {
                targetInput.scrollIntoView?.({
                  behavior: "smooth",
                  block: "nearest",
                  inline: "nearest",
                });
                targetInput.classList.remove("new-byte");
              }, 0);
            }
          }
        }

        const applyCommand = function (event) {
          if (event) {
            event.preventDefault();
            event.stopPropagation();
          }
          const inputs = Array.from(
            editor.querySelectorAll(".command-byte-input"),
          );
          let firstInvalid = null;
          inputs.forEach((byteInput, index) => {
            const byteValue = cleanHexValue(byteInput.value);
            entryBytes[index] = byteValue;
            const invalid = byteValue.length === 0;
            byteInput.classList.toggle("invalid", invalid);
            if (invalid && !firstInvalid) firstInvalid = byteInput;
          });

          if (firstInvalid) {
            errorBox.textContent =
              "Enter at least one hexadecimal character in every value field.";
            firstInvalid.focus();
            return;
          }
          const command = entryBytes.join("");
          errorBox.textContent = "";
          frameValueGroups[frameIdx] = entryBytes.slice();
          frameCodes[frameIdx] = command;
          renderCommandCodeBoxes(command, frameIdx);

          const cards = document.querySelectorAll(".command-frame-card");
          const currentCard = cards[frameIdx];
          if (currentCard) {
            const codeText = currentCard.querySelector(".frame-code strong");
            if (codeText) codeText.textContent = command;
            currentCard.classList.add("selected");
          }
          updateFieldDescFrameSelect();
          renderFieldDescription();
          markConfigurationUnsaved();
        };
        entry
          .querySelector("#applyCommandCodeBtn")
          .addEventListener("click", applyCommand);

        renderByteInputs();
        renderCommandCodeBoxes(frameCodes[frameIdx] || "", frameIdx);
        popup.style.display = "block";
        setTimeout(() => {
          const firstEmpty = Array.from(
            editor.querySelectorAll(".command-byte-input"),
          ).find((input) => !input.value);
          (
            firstEmpty || editor.querySelector(".command-byte-input")
          )?.focus();
        }, 0);
      }

      document
        .getElementById("closePopupBtn")
        .addEventListener("click", function () {
          document.getElementById("byteDetailPopup").style.display = "none";
          selectedFrameIndex = null;
          document
            .querySelectorAll(".command-frame-card")
            .forEach((c) => c.classList.remove("selected"));
        });

      // Keep the command editor open during all internal interactions.
      // This is required because + and remove rebuild their clicked controls.
      document
        .getElementById("byteDetailPopup")
        .addEventListener("click", function (event) {
          event.stopPropagation();
        });

      function updateCommandFrameCount() {
        const count = document.querySelectorAll(
          "#commandFramesContainer .command-frame-card",
        ).length;
        document.getElementById("cmdFrameCountDisplay").textContent = count;
        document.getElementById("cmdFrameCountInput").value = count;
        frameTypeStores[activeFrameType].count = count;
        saveActiveFrameTypeStore();
        updateFrameTypeUi();
        if (count === 0) {
          document.getElementById("byteDetailPopup").style.display = "none";
          selectedFrameIndex = null;
          activePartitionFrameIndex = null;
          document
            .getElementById("apiPartitionSection")
            .classList.remove("frame-partition-active");
        }
      }

      document
        .getElementById("generateFramesBtn")
        .addEventListener("click", function () {
          const requestedCount =
            parseInt(document.getElementById("cmdFrameCountInput").value) ||
            0;
          if (!checkRequirements() || requestedCount < 1) {
            showRequirementPopup();
            return;
          }
          showSetupWarnings = false;
          updateRequiredDropdownWarnings(false);
          if (bytes.length === 0) {
            generate8Bytes();
          }
          const count =
            parseInt(document.getElementById("cmdFrameCountInput").value) ||
            0;
          if (count > 0 && bytes.length > 0) {
            awaitingFrameClick = true;
            generateCommandFrames(count, false);
            document
              .getElementById("commandFramesSection")
              ?.scrollIntoView({ behavior: "smooth", block: "start" });
          } else if (count === 0) {
            awaitingFrameClick = false;
            document.getElementById("commandFramesContainer").innerHTML = "";
            updateCommandFrameCount();
            document.getElementById("byteDetailPopup").style.display = "none";
            selectedFrameIndex = null;
            frameCodes = [];
            frameValueGroups = [];
            framePartitionStates = [];
            frameConfigurations = [];
            activePartitionFrameIndex = null;
            document
              .getElementById("apiPartitionSection")
              .classList.remove("frame-partition-active");
            fieldDescriptions = {};
            byteDescriptions = {};
            fieldViewModes = {};
            updateFieldDescFrameSelect();
            renderFieldDescription();
            document
              .getElementById("framesNextBanner")
              ?.classList.remove("show");
          }
        });

      document
        .getElementById("cmdFrameCountInput")
        .addEventListener("input", function () {
          let val = parseInt(this.value) || 0;
          if (val < 0) val = 0;
          if (val > 20) val = 20;
          this.value = val;
          const invalid = val < 1;
          this.closest(".input-with-icon")?.classList.toggle(
            "input-error",
            showSetupWarnings && invalid,
          );
          this.setAttribute(
            "aria-invalid",
            String(showSetupWarnings && invalid),
          );
          updateSetupFieldHints();
        });

      document
        .getElementById("triggerUiSelect")
        ?.addEventListener("change", function () {
          updateRequiredDropdownWarnings(showSetupWarnings);
          const count = document.querySelectorAll(
            "#commandFramesContainer .command-frame-card",
          ).length;
          if (count > 0 && checkRequirements()) {
            generateCommandFrames(count, true);
          } else if (count > 0 && !checkRequirements()) {
            document.getElementById("commandFramesContainer").innerHTML = "";
            updateCommandFrameCount();
            document.getElementById("byteDetailPopup").style.display = "none";
            selectedFrameIndex = null;
            frameCodes = [];
            frameValueGroups = [];
            framePartitionStates = [];
            frameConfigurations = [];
            activePartitionFrameIndex = null;
            document
              .getElementById("apiPartitionSection")
              .classList.remove("frame-partition-active");
            fieldDescriptions = {};
            byteDescriptions = {};
            fieldViewModes = {};
            updateFieldDescFrameSelect();
            renderFieldDescription();
          }
        });

      ["paramNameInput", "cmdFrameCountInput"].forEach((id) => {
        document.getElementById(id)?.addEventListener("input", function () {
          updateRequiredDropdownWarnings(showSetupWarnings);
        });
      });

      const addCommandFrameBottom = document.getElementById(
        "addCommandFrameBottom",
      );
      if (addCommandFrameBottom) {
        addCommandFrameBottom.addEventListener("click", addCommandFrame);
      }
      document
        .getElementById("addCommandFrameBtn")
        ?.addEventListener("click", addCommandFrame);

      const apiTimelineScroll =
        document.getElementById("apiTimelineScroll");
      document
        .getElementById("previousPartitionBtn")
        ?.addEventListener("click", function () {
          scrollToAdjacentPartition(-1);
        });
      document
        .getElementById("nextPartitionBtn")
        ?.addEventListener("click", function () {
          scrollToAdjacentPartition(1);
        });
      apiTimelineScroll?.addEventListener(
        "scroll",
        updatePartitionNavigation,
        { passive: true },
      );
      window.addEventListener("resize", updatePartitionNavigation);

      document.querySelectorAll(".frame-type-btn").forEach((button) => {
        button.addEventListener("click", function () {
          switchFrameType(this.dataset.frameType, true);
        });
      });

      document.querySelectorAll(".flow-stage[data-flow-frame-type]").forEach((button) => {
        button.addEventListener("click", function () {
          switchFrameType(this.dataset.flowFrameType, true);
        });
      });

      document
        .getElementById("deleteConfigurationBtn")
        ?.addEventListener("click", async function () {
          if (!serverApp.deleteUrl) return;
          if (!window.confirm("Delete this API configuration permanently?")) {
            return;
          }
          this.disabled = true;
          try {
            const response = await fetch(serverApp.deleteUrl, {
              method: "POST",
              headers: csrfRequestHeaders(),
              credentials: "same-origin",
            });
            const result = await response.json().catch(() => null);
            if (!response.ok || !result?.success) {
              throw new Error(result?.message || "Delete failed.");
            }
            window.location.assign(
              result.redirect_url || serverApp.createUrl,
            );
          } catch (error) {
            this.disabled = false;
            alert(error.message || "The configuration could not be deleted.");
          }
        });

      const fieldDescFrameSelect = document.getElementById(
        "fieldDescFrameSelect",
      );
      if (fieldDescFrameSelect) {
        fieldDescFrameSelect.addEventListener(
          "change",
          renderFieldDescription,


        );
      }
      // const stepOneDemoOverlay =
      //   document.getElementById("stepOneDemoOverlay");
      // document
      //   .getElementById("openStepOneDemo")
      //   .addEventListener("click", function () {
      //     stepOneDemoOverlay.style.display = "flex";
      //   });
      // document
      //   .getElementById("closeStepOneDemo")
      //   .addEventListener("click", function () {
      //     stepOneDemoOverlay.style.display = "none";
      //   });
      // document
      //   .getElementById("dismissStepOneDemo")
      //   .addEventListener("click", function () {
      //     stepOneDemoOverlay.style.display = "none";
      //   });
      // stepOneDemoOverlay.addEventListener("click", function (event) {
      //   if (event.target === stepOneDemoOverlay)
      //     stepOneDemoOverlay.style.display = "none";
      // });

      const embeddedConfigurationRestored = restoreSavedConfiguration(
        serverApp.initialConfiguration,
      );
      if (!embeddedConfigurationRestored) {
        updateFrameTypeUi();
        renderApiPartitionEditor();
        renderAll();
        document.getElementById("commandFramesContainer").innerHTML = "";
        updateCommandFrameCount();
        document.getElementById("cmdFrameCountInput").value = 0;
        updateFieldDescFrameSelect();
        renderFieldDescription();
      }
      if (serverApp.id && !embeddedConfigurationRestored) {
        loadEditConfiguration();
      }
      updateRequiredDropdownWarnings(false);
      renderUiTypePicker("none");

      document
        .querySelectorAll(
          "#paramNameInput,#cmdFrameCountInput,#triggerUiSelect,#fullApiInput",
        )
        .forEach((control) => {
          control.addEventListener(
            control.tagName === "SELECT" ? "change" : "input",
            markConfigurationUnsaved,
          );
        });

      document.addEventListener("click", function (e) {
        const popup = document.getElementById("byteDetailPopup");
        if (popup.style.display === "block") {
          if (
            !popup.contains(e.target) &&
            !e.target.closest(".command-frame-card")
          ) {
            popup.style.display = "none";
            selectedFrameIndex = null;
            document
              .querySelectorAll(".command-frame-card")
              .forEach((c) => c.classList.remove("selected"));
          }
        }
      });
    })();
  </script>
</body>

</html>
