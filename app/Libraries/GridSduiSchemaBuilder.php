<?php

namespace App\Libraries;

/**
 * Standalone Grid network page SDUI schema (not part of App Home get_schema).
 * Flutter / studio fetch this via grid_api_creation.php?action=get_grid.
 */
class GridSduiSchemaBuilder
{
    public const GRID_LOGO_URL = 'https://efactor.tech/assets/images/GRID_Logo.png';

    private static function publicOrigin(): string
    {
        $host = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));
        if ($host === '') {
            $host = 'efactor.tech';
        }
        $hostName = strtolower((string) preg_replace('/:\d+$/', '', $host));
        // Mobile blocks cleartext http:// for QR/logo assets — always https off localhost.
        if (!in_array($hostName, array('localhost', '127.0.0.1', '::1'), true)) {
            return 'https://' . $host;
        }
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443)
            || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
        return ($https ? 'https://' : 'http://') . $host;
    }

    private static function qrImageEndpoint(): string
    {
        return rtrim(self::publicOrigin(), '/') . '/v8/qr_image.php';
    }

    private static function qrImageUrlTemplate(): string
    {
        return self::qrImageEndpoint() . '?data={data}&mode={mode}&size=160';
    }

    private static function qrImageUrl($payload, $mode = ''): string
    {
        $payload = trim((string) $payload);
        $mode = trim((string) $mode);
        $query = 'data=' . rawurlencode($payload) . '&size=160';
        if ($mode !== '') {
            $query .= '&mode=' . rawurlencode($mode);
        }
        return self::qrImageEndpoint() . '?' . $query;
    }

    private static function isThirdPartyQrUrl($url): bool
    {
        $url = strtolower(trim((string) $url));
        if ($url === '') {
            return false;
        }
        return strpos($url, 'qrserver.com') !== false
            || strpos($url, 'chart.googleapis.com') !== false
            || strpos($url, 'chart.google.com') !== false;
    }

    private static function fillShareQrImages(array $shareQr): array
    {
        $selectedMode = (string) ($shareQr['selectedMode'] ?? 'professional');
        $payload = trim((string) ($shareQr['qrPayload'] ?? ''));
        if ($payload === '') {
            $payload = 'https://efactor.tech/grid/connect?mode=' . rawurlencode($selectedMode);
            $shareQr['qrPayload'] = $payload;
        }
        $shareQr['qrImageUrlTemplate'] = self::qrImageUrlTemplate();
        $modes = is_array($shareQr['modes'] ?? null) ? $shareQr['modes'] : array();
        $byMode = is_array($shareQr['qrImageUrlsByMode'] ?? null) ? $shareQr['qrImageUrlsByMode'] : array();
        foreach ($modes as $mode) {
            if (!is_array($mode)) {
                continue;
            }
            $modeId = trim((string) ($mode['id'] ?? ''));
            if ($modeId === '') {
                continue;
            }
            $modePayload = 'https://efactor.tech/grid/connect?mode=' . rawurlencode($modeId);
            if ($modeId === $selectedMode) {
                $modePayload = $payload;
            }
            $existing = trim((string) ($byMode[$modeId] ?? ''));
            if ($existing === '' || self::isThirdPartyQrUrl($existing)) {
                $byMode[$modeId] = self::qrImageUrl($modePayload, $modeId);
            }
        }
        $shareQr['qrImageUrlsByMode'] = $byMode;
        $image = trim((string) ($shareQr['qrImageUrl'] ?? ''));
        if ($image === '' || self::isThirdPartyQrUrl($image)) {
            $shareQr['qrImageUrl'] = isset($byMode[$selectedMode])
                ? $byMode[$selectedMode]
                : self::qrImageUrl($payload, $selectedMode);
        }
        return $shareQr;
    }

    private static function gridLayoutMetrics($columns, $gap, $rowHeight, array $extra = array()): array
    {
        return array_merge(array(
            'columns' => (int) $columns,
            'columnGap' => (int) $gap,
            'rowGap' => (int) $gap,
            'rowHeight' => (int) $rowHeight,
            'minRowHeight' => (int) $rowHeight,
            // 0 lets cells divide the real width. A fixed minimum overflows narrow phones.
            'minimumCellWidthDp' => 0,
            'fit' => 'fill',
            'width' => 'fill',
            'maxWidth' => '100%',
            'minWidth' => 0,
            'allowShrink' => true,
            'overflowX' => 'hidden',
        ), $extra);
    }

    /**
     * Tokens the Flutter avatarButton checker requires:
     * designTokens.colors.surface (or onAccent) and action.fontSize.
     */
    private static function designTokens(): array
    {
        return array(
            'colors' => array(
                'textPrimary' => '#2e1065',
                'textSecondary' => '#6b7280',
                'border' => '#e5e7eb',
                'surface' => '#ffffff',
                'onSurface' => '#111827',
                'onAccent' => '#ffffff',
                'pageBackground' => '#f7f8fc',
                'accent' => '#7c3aed',
            ),
            'action' => array(
                'fontSize' => 15,
                'fontWeight' => 700,
                'lineHeight' => 1.2,
                'color' => '#ffffff',
            ),
            'typography' => array(
                'pageTitle' => array(
                    'size' => 26,
                    'fontSize' => 26,
                    'weight' => 800,
                    'fontWeight' => 800,
                    'letterSpacing' => -0.02,
                    'maxLines' => 1,
                    'overflow' => 'ellipsis',
                ),
                'pageSubtitle' => array(
                    'size' => 13,
                    'fontSize' => 13,
                    'weight' => 500,
                    'fontWeight' => 500,
                    'maxLines' => 2,
                    'overflow' => 'ellipsis',
                ),
                'action' => array(
                    'size' => 15,
                    'fontSize' => 15,
                    'weight' => 700,
                    'fontWeight' => 700,
                    'lineHeight' => 1.2,
                ),
            ),
            'radius' => array(
                'card' => 22,
                'explore' => 18,
                'button' => 999,
            ),
        );
    }

    public static function defaultContent(): array
    {
        $defaults = array(
            'id' => 'gridPage',
            'routeId' => 'grid',
            'logoUrl' => self::GRID_LOGO_URL,
            'header' => array(
                'title' => 'Grid',
                'subtitle' => 'Your professional network space',
                'logoUrl' => self::GRID_LOGO_URL,
                'searchIconToken' => 'search',
                'avatarInitial' => 'S',
                'avatarColor' => '#7c3aed',
            ),
            'featureCards' => array(
                array(
                    'id' => 'professional-space',
                    'title' => 'Professional Space',
                    'subtitle' => 'Discover people, opportunities and grow your career.',
                    'iconToken' => 'user',
                    'image' => '',
                    'imageUrl' => '',
                    'url' => '',
                    'route' => '',
                    'color' => '#5b6cff',
                    'gradientStart' => '#dce4ff',
                    'gradientEnd' => '#b7c4fb',
                    'textColor' => '#312e81',
                    'actionId' => 'grid-open-professional-space',
                ),
                array(
                    'id' => 'circle-connect',
                    'title' => 'Circle Connect',
                    'subtitle' => 'Meaningful conversations. Stronger circles.',
                    'iconToken' => 'users',
                    'image' => '',
                    'imageUrl' => '',
                    'url' => '',
                    'route' => '',
                    'color' => '#ea580c',
                    'gradientStart' => '#ffe0cc',
                    'gradientEnd' => '#f4b496',
                    'textColor' => '#9a3412',
                    'actionId' => 'grid-open-circle-connect',
                ),
            ),
            'stats' => array(
                array('id' => 'professionals', 'label' => 'Professionals', 'value' => 248),
                array('id' => 'opportunities', 'label' => 'Opportunities', 'value' => 36),
                array('id' => 'active-circles', 'label' => 'Active Circles', 'value' => 12),
            ),
            'shareQr' => array(
                'title' => 'Share Profile QR',
                'subtitle' => 'Share your professional or circle profile with anyone.',
                'iconToken' => 'grid',
                'selectedMode' => 'professional',
                'modes' => array(
                    array('id' => 'professional', 'label' => 'Professional'),
                    array('id' => 'circle', 'label' => 'Circle'),
                ),
                'qrCaption' => 'Scan to connect',
                'qrPayload' => 'https://efactor.tech/grid/connect?mode=professional',
                'qrImageUrl' => '',
                'qrImageUrlTemplate' => '',
                'qrImageUrlsByMode' => array(),
                'shareActionId' => 'grid-share-qr',
                'selectModeActionId' => 'grid-select-qr-mode',
            ),
            'exploring' => array(
                'title' => 'Continue Exploring',
                'seeAllLabel' => 'See all',
                'seeAllActionId' => 'grid-see-all-exploring',
                'items' => array(
                    array(
                        'id' => 'find-talent',
                        'title' => 'Find Talent',
                        'subtitle' => 'People with skills you need',
                        'iconToken' => 'user',
                        'color' => '#8b5cf6',
                        'actionId' => 'grid-open-find-talent',
                    ),
                    array(
                        'id' => 'join-circles',
                        'title' => 'Join Circles',
                        'subtitle' => "Communities you'll love",
                        'iconToken' => 'users',
                        'color' => '#14b8a6',
                        'actionId' => 'grid-open-join-circles',
                    ),
                ),
            ),
            'quote' => array(
                'text' => 'Connections turn potential into progress.',
                'iconToken' => 'quote',
            ),
        );
        $defaults['shareQr'] = self::fillShareQrImages($defaults['shareQr']);
        return $defaults;
    }

    public static function mergeContent(?array $override): array
    {
        $defaults = self::defaultContent();
        if (!is_array($override) || $override === array()) {
            return $defaults;
        }

        $merged = array_replace_recursive($defaults, $override);
        if (empty($override['featureCards']) || !is_array($override['featureCards'])) {
            $merged['featureCards'] = $defaults['featureCards'];
        } else {
            $merged['featureCards'] = self::mergeFeatureCardsById(
                $defaults['featureCards'],
                $override['featureCards']
            );
        }
        if (empty($override['stats']) || !is_array($override['stats'])) {
            $merged['stats'] = $defaults['stats'];
        }
        if (empty($override['exploring']['items']) || !is_array($override['exploring']['items'])) {
            $merged['exploring']['items'] = $defaults['exploring']['items'];
        }
        if (empty($override['shareQr']) || !is_array($override['shareQr'])) {
            $merged['shareQr'] = $defaults['shareQr'];
        } else {
            $merged['shareQr'] = array_replace_recursive($defaults['shareQr'], $override['shareQr']);
            if (empty($override['shareQr']['modes']) || !is_array($override['shareQr']['modes'])) {
                $merged['shareQr']['modes'] = $defaults['shareQr']['modes'];
            }
        }
        if (empty($merged['logoUrl'])) {
            $merged['logoUrl'] = self::GRID_LOGO_URL;
        }
        if (empty($merged['header']['logoUrl'])) {
            $merged['header']['logoUrl'] = self::GRID_LOGO_URL;
        }
        $merged['shareQr'] = self::fillShareQrImages(is_array($merged['shareQr'] ?? null) ? $merged['shareQr'] : array());

        return $merged;
    }

    /**
     * Merge saved feature cards onto defaults by id so open-URLs persist.
     * Icons always come from iconToken (no remote image URLs).
     */
    private static function mergeFeatureCardsById(array $defaults, array $override): array
    {
        $byId = array();
        foreach ($override as $card) {
            if (!is_array($card)) {
                continue;
            }
            $id = (string) ($card['id'] ?? '');
            if ($id === '') {
                continue;
            }
            $byId[$id] = $card;
        }

        $merged = array();
        foreach ($defaults as $defaultCard) {
            if (!is_array($defaultCard)) {
                continue;
            }
            $id = (string) ($defaultCard['id'] ?? '');
            $saved = ($id !== '' && isset($byId[$id]) && is_array($byId[$id])) ? $byId[$id] : array();
            $card = array_replace_recursive($defaultCard, $saved);
            $card = self::normalizeSoftCard($card);
            $merged[] = $card;
            unset($byId[$id]);
        }
        foreach ($byId as $extra) {
            if (is_array($extra)) {
                $merged[] = self::normalizeSoftCard($extra);
            }
        }
        return $merged;
    }

    /**
     * Soft pastel cards (icon chip, title, subtitle, arrow).
     * Migrates Windows Start tiles; never uses image URLs for icons.
     */
    private static function normalizeSoftCard(array $card): array
    {
        $id = (string) ($card['id'] ?? '');

        if ($id === 'professional-space') {
            $card['color'] = '#5b6cff';
            $card['gradientStart'] = '#dce4ff';
            $card['gradientEnd'] = '#b7c4fb';
            $card['textColor'] = '#312e81';
            if (trim((string) ($card['iconToken'] ?? '')) === '') {
                $card['iconToken'] = 'user';
            }
        }
        if ($id === 'circle-connect') {
            $card['color'] = '#ea580c';
            $card['gradientStart'] = '#ffe0cc';
            $card['gradientEnd'] = '#f4b496';
            $card['textColor'] = '#9a3412';
            if (trim((string) ($card['iconToken'] ?? '')) === '') {
                $card['iconToken'] = 'users';
            }
        }

        $card['image'] = '';
        $card['imageUrl'] = '';
        $url = trim((string) ($card['url'] ?? $card['route'] ?? ''));
        $card['url'] = $url;
        $card['route'] = $url;
        return $card;
    }

    public static function buildComponents(array $content): array
    {
        $header = is_array($content['header'] ?? null) ? $content['header'] : array();
        $exploring = is_array($content['exploring'] ?? null) ? $content['exploring'] : array();
        $quote = is_array($content['quote'] ?? null) ? $content['quote'] : array();
        $shareQr = is_array($content['shareQr'] ?? null) ? $content['shareQr'] : array();
        $featureCards = is_array($content['featureCards'] ?? null) ? $content['featureCards'] : array();
        $stats = is_array($content['stats'] ?? null) ? $content['stats'] : array();
        $exploreItems = is_array($exploring['items'] ?? null) ? $exploring['items'] : array();
        $logoUrl = (string) ($header['logoUrl'] ?? $content['logoUrl'] ?? self::GRID_LOGO_URL);

        $components = array();
        $featureChildIds = array();
        foreach ($featureCards as $index => $card) {
            if (!is_array($card)) {
                continue;
            }
            $id = 'grid-feature-' . (string) ($card['id'] ?? $index);
            $featureChildIds[] = $id;
            $actionId = $card['actionId'] ?? null;
            $card = self::normalizeSoftCard($card);
            $tileColor = (string) ($card['color'] ?? $card['gradientStart'] ?? '#5b6cff');
            $textColor = (string) ($card['textColor'] ?? '#312e81');
            $url = trim((string) ($card['url'] ?? $card['route'] ?? ''));
            $components[$id] = array(
                'id' => $id,
                'type' => 'featureCard',
                'style' => 'softCard',
                'title' => (string) ($card['title'] ?? ''),
                'subtitle' => (string) ($card['subtitle'] ?? ''),
                'iconToken' => self::knownIconToken($card['iconToken'] ?? 'user', 'user'),
                'actionArrowIconToken' => 'chevron-right',
                'image' => null,
                'imageUrl' => null,
                'color' => $tileColor,
                'gradientStart' => (string) ($card['gradientStart'] ?? $tileColor),
                'gradientEnd' => (string) ($card['gradientEnd'] ?? $tileColor),
                'textColor' => $textColor,
                'url' => $url !== '' ? $url : null,
                'route' => $url !== '' ? $url : null,
                'openIn' => $url !== '' ? 'inAppWebView' : null,
                // Card title stays on the Grid page tile; do not reuse it as WebView chrome.
                'webViewTitle' => '',
                'webView' => $url !== '' ? array(
                    'javaScriptEnabled' => true,
                    'sharedCookies' => true,
                    'clearCache' => false,
                    'showAppBar' => false,
                    'showToolbar' => false,
                    'hideAppBar' => true,
                    'displayMode' => 'fullscreen',
                ) : null,
                'actionId' => $actionId,
                'events' => !empty($actionId) ? array('tap' => array('actionId' => $actionId)) : null,
                'layout' => array(
                    'columnSpan' => 1,
                    'rowSpan' => 1,
                    'minHeight' => 168,
                    'minWidth' => 0,
                    'width' => 'fill',
                    'maxWidth' => '100%',
                ),
                'appearance' => array(
                    'borderRadius' => 24,
                    'backgroundColor' => (string) ($card['gradientStart'] ?? $tileColor),
                    'backgroundGradient' => array(
                        'start' => (string) ($card['gradientStart'] ?? $tileColor),
                        'end' => (string) ($card['gradientEnd'] ?? $tileColor),
                    ),
                    'iconPosition' => 'topLeft',
                    'titlePosition' => 'belowIcon',
                    'showSubtitle' => true,
                    'showActionArrow' => true,
                    'iconSize' => 36,
                    'titleFontSize' => 16,
                    'subtitleFontSize' => 12,
                    'titlePadding' => 12,
                    'titleMaxLines' => 2,
                    'subtitleMaxLines' => 3,
                    'titleOverflow' => 'ellipsis',
                    'subtitleOverflow' => 'ellipsis',
                    'width' => 'fill',
                    'minWidth' => 0,
                ),
            );
        }

        $exploreChildIds = array();
        foreach ($exploreItems as $index => $item) {
            if (!is_array($item)) {
                continue;
            }
            $id = 'grid-explore-' . (string) ($item['id'] ?? $index);
            $exploreChildIds[] = $id;
            $components[$id] = array(
                'id' => $id,
                'type' => 'exploreCard',
                'title' => (string) ($item['title'] ?? ''),
                'subtitle' => (string) ($item['subtitle'] ?? ''),
                'iconToken' => self::knownIconToken($item['iconToken'] ?? 'user', 'user'),
                'color' => (string) ($item['color'] ?? '#8b5cf6'),
                'actionId' => $item['actionId'] ?? null,
                'events' => !empty($item['actionId']) ? array('tap' => array('actionId' => $item['actionId'])) : null,
                'layout' => array(
                    'columnSpan' => 1,
                    'rowSpan' => 1,
                    'minHeight' => 112,
                    'minWidth' => 0,
                    'width' => 'fill',
                    'maxWidth' => '100%',
                ),
                'appearance' => array(
                    'borderRadius' => 18,
                    'backgroundColor' => '#ffffff',
                    'titleMaxLines' => 2,
                    'subtitleMaxLines' => 2,
                    'titleOverflow' => 'ellipsis',
                    'subtitleOverflow' => 'ellipsis',
                    'minWidth' => 0,
                    'width' => 'fill',
                ),
            );
        }

        $statItems = array();
        foreach ($stats as $stat) {
            if (!is_array($stat)) {
                continue;
            }
            $statId = (string) ($stat['id'] ?? '');
            $statItems[] = array(
                'id' => $statId !== '' ? $statId : null,
                'label' => (string) ($stat['label'] ?? ''),
                'value' => $stat['value'] ?? 0,
                'colorToken' => 'colors.textPrimary',
                'appearance' => array(
                    'borderRadius' => 22,
                    'backgroundColor' => '#ffffff',
                    'valueFontSize' => 22,
                    'labelFontSize' => 12,
                    'paddingVertical' => 12,
                    'paddingHorizontal' => 12,
                ),
            );
        }

        $components['grid-page'] = array(
            'id' => 'grid-page',
            'type' => 'scrollPage',
            'style' => 'gridNetwork',
            'layout' => array(
                'direction' => 'vertical',
                'safeArea' => true,
                'safeAreaEdges' => array('top', 'left', 'right', 'bottom'),
                'overflowX' => 'hidden',
                'overflowY' => 'auto',
                'width' => 'fill',
                'maxWidth' => '100%',
                'minWidth' => 0,
                'reservedBottomInset' => 108,
                'padding' => array('left' => 16, 'right' => 16, 'top' => 12, 'bottom' => 108),
                'gap' => 16,
                'responsive' => array(
                    'narrow' => array('padding' => array('left' => 12, 'right' => 12, 'top' => 8, 'bottom' => 108), 'gap' => 12),
                    'compact' => array('padding' => array('left' => 12, 'right' => 12, 'top' => 10, 'bottom' => 108), 'gap' => 14),
                    'regular' => array('padding' => array('left' => 16, 'right' => 16, 'top' => 12, 'bottom' => 108), 'gap' => 16),
                    'medium' => array('padding' => array('left' => 20, 'right' => 20, 'top' => 12, 'bottom' => 108), 'gap' => 18),
                    'expanded' => array('padding' => array('left' => 28, 'right' => 28, 'top' => 16, 'bottom' => 108), 'gap' => 22, 'maxWidth' => 720),
                ),
            ),
            'children' => array(
                'grid-page-header',
                'grid-feature-row',
                'grid-stats-row',
                'grid-share-qr',
                'grid-explore-section',
                'grid-quote-block',
            ),
        );

        $avatarInitial = (string) ($header['avatarInitial'] ?? 'S');
        $avatarColor = (string) ($header['avatarColor'] ?? '#7c3aed');
        $components['grid-page-header'] = array(
            'id' => 'grid-page-header',
            'type' => 'pageHeader',
            'title' => (string) ($header['title'] ?? 'Grid'),
            'subtitle' => (string) ($header['subtitle'] ?? 'Your professional network space'),
            'logoUrl' => $logoUrl,
            'image' => $logoUrl,
            'designTokens' => self::designTokens(),
            'layout' => array(
                'direction' => 'horizontal',
                'alignItems' => 'center',
                'crossAxisAlignment' => 'center',
                'gap' => 10,
                'overflowX' => 'hidden',
                'width' => 'fill',
                'maxWidth' => '100%',
                'minWidth' => 0,
                'titleFlex' => 1,
                'titleMinWidth' => 0,
                'titleSubtitleDirection' => 'vertical',
                'subtitlePlacement' => 'belowTitle',
                'actionsFlex' => 0,
                'responsive' => array(
                    'narrow' => array('gap' => 8, 'titleFontSize' => 22, 'subtitleFontSize' => 12, 'logoHeight' => 28),
                    'compact' => array('gap' => 10, 'titleFontSize' => 24, 'subtitleFontSize' => 13, 'logoHeight' => 32),
                    'regular' => array('gap' => 12, 'titleFontSize' => 28, 'subtitleFontSize' => 13, 'logoHeight' => 36),
                    'medium' => array('gap' => 12, 'titleFontSize' => 30, 'subtitleFontSize' => 14, 'logoHeight' => 36),
                    'expanded' => array('gap' => 16, 'titleFontSize' => 34, 'subtitleFontSize' => 15, 'logoHeight' => 40),
                ),
            ),
            'appearance' => array(
                'logoHeight' => 32,
                'logoMaxWidth' => 96,
            ),
            'typography' => array(
                'titleToken' => 'typography.pageTitle',
                'subtitleToken' => 'typography.pageSubtitle',
                'title' => array(
                    'fontSize' => 24,
                    'fontWeight' => 800,
                    'maxLines' => 1,
                    'overflow' => 'ellipsis',
                ),
                'subtitle' => array(
                    'fontSize' => 13,
                    'fontWeight' => 500,
                    'maxLines' => 2,
                    'overflow' => 'ellipsis',
                ),
            ),
            'actions' => array(
                array(
                    'id' => 'grid-header-search',
                    'iconToken' => (string) ($header['searchIconToken'] ?? 'search'),
                    'actionId' => 'grid-open-search',
                    'layout' => array(
                        'width' => 40,
                        'height' => 40,
                        'flex' => 0,
                        'shrink' => 0,
                    ),
                    'appearance' => array(
                        'size' => 40,
                        'iconSize' => 22,
                    ),
                ),
                array(
                    'id' => 'grid-header-avatar',
                    'type' => 'avatarButton',
                    'label' => $avatarInitial,
                    'initial' => $avatarInitial,
                    'color' => $avatarColor,
                    'backgroundColor' => $avatarColor,
                    'foregroundColor' => '#ffffff',
                    'actionId' => 'navigate-profile',
                    'fontSize' => 15,
                    'action' => array(
                        'fontSize' => 15,
                        'fontWeight' => 700,
                    ),
                    'colorToken' => 'colors.onAccent',
                    'backgroundColorToken' => 'colors.accent',
                    'fontSizeToken' => 'action.fontSize',
                    'designTokens' => array(
                        'colors' => array(
                            'surface' => '#ffffff',
                            'onAccent' => '#ffffff',
                            'accent' => $avatarColor,
                        ),
                        'action' => array(
                            'fontSize' => 15,
                            'fontWeight' => 700,
                        ),
                    ),
                    'layout' => array(
                        'width' => 40,
                        'height' => 40,
                        'flex' => 0,
                        'shrink' => 0,
                    ),
                    'style' => array(
                        'width' => 40,
                        'height' => 40,
                        'fontSize' => 15,
                        'fontWeight' => 700,
                        'backgroundColor' => $avatarColor,
                        'color' => '#ffffff',
                    ),
                    'appearance' => array(
                        'size' => 40,
                        'width' => 40,
                        'height' => 40,
                        'borderRadius' => 20,
                        'backgroundColor' => $avatarColor,
                        'foregroundColor' => '#ffffff',
                        'fontSize' => 15,
                        'fontWeight' => 700,
                    ),
                ),
            ),
        );

        $components['grid-feature-row'] = array(
            'id' => 'grid-feature-row',
            'type' => 'responsiveGrid',
            'layout' => array_merge(self::gridLayoutMetrics(2, 12, 188), array(
                'independentScroll' => false,
                'responsive' => array(
                    'narrow' => self::gridLayoutMetrics(2, 10, 176),
                    'compact' => self::gridLayoutMetrics(2, 8, 188),
                    'regular' => self::gridLayoutMetrics(2, 12, 188),
                    'medium' => self::gridLayoutMetrics(2, 12, 200),
                    'expanded' => self::gridLayoutMetrics(2, 16, 210, array('maximumContentWidth' => 720)),
                ),
            )),
            'layouts' => array(
                'narrow' => self::gridLayoutMetrics(2, 10, 176),
                'compact' => self::gridLayoutMetrics(2, 8, 188),
                'regular' => self::gridLayoutMetrics(2, 12, 188),
                'medium' => self::gridLayoutMetrics(2, 12, 200),
                'expanded' => self::gridLayoutMetrics(2, 16, 210, array('maximumContentWidth' => 720)),
            ),
            'overflowPolicy' => 'reflow',
            'preserveChildOrder' => true,
            'children' => $featureChildIds,
        );

        $components['grid-stats-row'] = array(
            'id' => 'grid-stats-row',
            'type' => 'statusSummary',
            'layout' => array(
                'columns' => 3,
                'gap' => 8,
                'width' => 'fill',
                'maxWidth' => '100%',
                'minWidth' => 0,
                'overflowX' => 'hidden',
                'responsive' => array(
                    'compact' => array('columns' => 3, 'gap' => 6, 'rowHeight' => 72, 'minRowHeight' => 64),
                    'narrow' => array('columns' => 3, 'gap' => 6, 'rowHeight' => 56, 'minRowHeight' => 56),
                    'regular' => array('columns' => 3, 'gap' => 8, 'rowHeight' => 72, 'minRowHeight' => 64),
                    'medium' => array('columns' => 3, 'gap' => 8, 'rowHeight' => 72, 'minRowHeight' => 64),
                    'expanded' => array('columns' => 3, 'gap' => 12, 'rowHeight' => 80, 'minRowHeight' => 64),
                ),
            ),
            'appearance' => array(
                'backgroundColor' => '#ffffff',
                'borderRadius' => 22,
                'valueFontSize' => 20,
                'labelFontSize' => 11,
                'paddingVertical' => 12,
                'paddingHorizontal' => 8,
                'labelMaxLines' => 2,
                'labelOverflow' => 'ellipsis',
                'minWidth' => 0,
                'width' => 'fill',
            ),
            'items' => $statItems,
        );

        $selectedMode = (string) ($shareQr['selectedMode'] ?? 'professional');
        $shareQr = self::fillShareQrImages($shareQr);
        $qrPayload = (string) ($shareQr['qrPayload'] ?? ('https://efactor.tech/grid/connect?mode=' . rawurlencode($selectedMode)));
        $qrImageUrl = (string) ($shareQr['qrImageUrl'] ?? '');
        $qrImageUrlTemplate = (string) ($shareQr['qrImageUrlTemplate'] ?? self::qrImageUrlTemplate());
        $qrImageUrlsByMode = is_array($shareQr['qrImageUrlsByMode'] ?? null) ? $shareQr['qrImageUrlsByMode'] : array();
        $modeItems = array();
        $modes = is_array($shareQr['modes'] ?? null) ? $shareQr['modes'] : array();
        foreach ($modes as $mode) {
            if (!is_array($mode)) {
                continue;
            }
            $modeId = (string) ($mode['id'] ?? '');
            $modeItems[] = array(
                'id' => $modeId,
                'label' => (string) ($mode['label'] ?? $modeId),
                'selected' => $modeId !== '' && $modeId === $selectedMode,
                'actionId' => (string) ($shareQr['selectModeActionId'] ?? 'grid-select-qr-mode'),
            );
        }

        $components['grid-share-qr'] = array(
            'id' => 'grid-share-qr',
            'type' => 'shareQrCard',
            'title' => (string) ($shareQr['title'] ?? 'Share Profile QR'),
            'subtitle' => (string) ($shareQr['subtitle'] ?? 'Share your professional or circle profile with anyone.'),
            'iconToken' => self::knownIconToken($shareQr['iconToken'] ?? 'grid', 'grid'),
            'selectedMode' => $selectedMode,
            'modes' => $modeItems,
            'qrCaption' => (string) ($shareQr['qrCaption'] ?? 'Scan to connect'),
            'qrPayload' => $qrPayload,
            'image' => $qrImageUrl,
            'qrImageUrl' => $qrImageUrl,
            'qrImageUrlTemplate' => $qrImageUrlTemplate,
            'qrImageUrlsByMode' => $qrImageUrlsByMode,
            'shareActionId' => (string) ($shareQr['shareActionId'] ?? 'grid-share-qr'),
            'actionId' => (string) ($shareQr['shareActionId'] ?? 'grid-share-qr'),
            'layout' => array(
                'direction' => 'horizontal',
                'gap' => 12,
                'width' => 'fill',
                'maxWidth' => '100%',
                'minWidth' => 0,
                'overflowX' => 'hidden',
                'responsive' => array(
                    'compact' => array('direction' => 'horizontal', 'gap' => 10),
                    'narrow' => array('direction' => 'vertical', 'gap' => 10),
                    'regular' => array('direction' => 'horizontal', 'gap' => 12),
                    'medium' => array('direction' => 'horizontal', 'gap' => 12),
                    'expanded' => array('direction' => 'horizontal', 'gap' => 16),
                ),
            ),
            'appearance' => array(
                'backgroundColor' => '#eef2f7',
                'borderRadius' => 20,
                'titleFontSize' => 18,
                'subtitleFontSize' => 13,
            ),
        );

        $components['grid-explore-section'] = array(
            'id' => 'grid-explore-section',
            'type' => 'section',
            'title' => (string) ($exploring['title'] ?? 'Continue Exploring'),
            'trailing' => array(
                'label' => (string) ($exploring['seeAllLabel'] ?? 'See all'),
                'actionId' => (string) ($exploring['seeAllActionId'] ?? 'grid-see-all-exploring'),
            ),
            'children' => array('grid-explore-row'),
        );

        $components['grid-explore-row'] = array(
            'id' => 'grid-explore-row',
            'type' => 'responsiveGrid',
            'layout' => array_merge(self::gridLayoutMetrics(2, 12, 122), array(
                'independentScroll' => false,
                'responsive' => array(
                    'narrow' => self::gridLayoutMetrics(2, 10, 122),
                    'compact' => self::gridLayoutMetrics(2, 10, 122),
                    'regular' => self::gridLayoutMetrics(2, 12, 122),
                    'medium' => self::gridLayoutMetrics(2, 12, 122),
                    'expanded' => self::gridLayoutMetrics(2, 16, 122, array('maximumContentWidth' => 720)),
                ),
            )),
            'layouts' => array(
                'narrow' => self::gridLayoutMetrics(2, 10, 122),
                'compact' => self::gridLayoutMetrics(2, 10, 122),
                'regular' => self::gridLayoutMetrics(2, 12, 122),
                'medium' => self::gridLayoutMetrics(2, 12, 122),
                'expanded' => self::gridLayoutMetrics(2, 16, 122, array('maximumContentWidth' => 720)),
            ),
            'overflowPolicy' => 'reflow',
            'preserveChildOrder' => true,
            'children' => $exploreChildIds,
        );

        $components['grid-quote-block'] = array(
            'id' => 'grid-quote-block',
            'type' => 'quoteBlock',
            'title' => (string) ($quote['text'] ?? 'Connections turn potential into progress.'),
            'iconToken' => self::knownIconToken($quote['iconToken'] ?? 'quote', 'quote'),
            'appearance' => array(
                'backgroundColor' => '#f5f3ff',
                'textColor' => '#312e81',
                'borderRadius' => 20,
            ),
        );

        $components['grid-loading'] = array('id' => 'grid-loading', 'type' => 'skeleton', 'variant' => 'home-dashboard');
        $components['grid-empty'] = array(
            'id' => 'grid-empty',
            'type' => 'emptyState',
            'iconToken' => 'grid',
            'title' => 'Grid is empty',
            'subtitle' => 'Your network space will appear here.',
        );
        $components['grid-error'] = array(
            'id' => 'grid-error',
            'type' => 'errorState',
            'title' => 'Unable to load Grid',
            'retryActionId' => 'refresh-grid',
        );

        return $components;
    }

    public static function buildActions(array $content = array()): array
    {
        $featureCards = is_array($content['featureCards'] ?? null) ? $content['featureCards'] : array();
        $proUrl = '';
        $circleUrl = '';
        foreach ($featureCards as $card) {
            if (!is_array($card)) {
                continue;
            }
            $id = (string) ($card['id'] ?? '');
            $url = trim((string) ($card['url'] ?? $card['route'] ?? ''));
            if ($id === 'professional-space') {
                $proUrl = $url;
            }
            if ($id === 'circle-connect') {
                $circleUrl = $url;
            }
        }

        // prepareContentForClient() already canonicalized URLs and appended ?grid_sso=.
        // Do not call resolveCardUrl() here — it strips the token and breaks auto-login.
        $ssoToken = '';
        foreach (array($proUrl, $circleUrl) as $candidate) {
            if ($candidate === '' || strpos($candidate, 'grid_sso=') === false) {
                continue;
            }
            parse_str((string) (parse_url($candidate, PHP_URL_QUERY) ?? ''), $q);
            $ssoToken = trim((string) ($q['grid_sso'] ?? ''));
            if ($ssoToken !== '') {
                break;
            }
        }

        $proOk = $proUrl !== ''
            && preg_match('~/(index\.php/)?grid([/?#]|$)~i', $proUrl)
            && !preg_match('#grid-(chat|dashboard)|app-web-entry#i', $proUrl);
        if (!$proOk) {
            $proUrl = class_exists(GridWebSso::class)
                ? (string) (GridWebSso::defaultCardUrls()['professional-space'] ?? '')
                : 'https://efactor.tech/index.php/grid';
            if ($proUrl === '') {
                $proUrl = 'https://efactor.tech/index.php/grid';
            }
            if ($ssoToken !== '' && class_exists(GridWebSso::class)) {
                $proUrl = GridWebSso::appendToken($proUrl, $ssoToken);
            }
        }

        $circleOk = $circleUrl !== ''
            && stripos($circleUrl, 'app-web-entry') === false
            && (stripos($circleUrl, 'grid-chat') !== false || stripos($circleUrl, 'grid_chat') !== false);
        if (!$circleOk) {
            $circleUrl = class_exists(GridWebSso::class)
                ? (string) (GridWebSso::defaultCardUrls()['circle-connect'] ?? '')
                : 'https://efactor.tech/index.php/grid-chat';
            if ($circleUrl === '') {
                $circleUrl = 'https://efactor.tech/index.php/grid-chat';
            }
            if ($ssoToken !== '' && class_exists(GridWebSso::class)) {
                $circleUrl = GridWebSso::appendToken($circleUrl, $ssoToken);
            }
        }

        // No host WebView chrome (back / title / refresh). Grid & Grid-chat
        // already have their own UI; the Flutter bar was schema-driven via title.
        $hiddenWebView = array(
            'javaScriptEnabled' => true,
            'sharedCookies' => true,
            'clearCache' => false,
            'showAppBar' => false,
            'showToolbar' => false,
            'hideAppBar' => true,
            'displayMode' => 'fullscreen',
        );

        $proAction = array(
            'type' => 'open-url',
            'url' => $proUrl,
            'route' => $proUrl,
            'title' => '',
            'openIn' => 'inAppWebView',
            'webView' => $hiddenWebView,
            'destination' => array(
                'url' => $proUrl,
                'openIn' => 'inAppWebView',
                'title' => '',
                'sharedCookies' => true,
                'showAppBar' => false,
                'showToolbar' => false,
                'hideAppBar' => true,
                'webView' => $hiddenWebView,
            ),
        );

        $circleAction = array(
            'type' => 'open-url',
            'url' => $circleUrl,
            'route' => $circleUrl,
            'title' => '',
            'openIn' => 'inAppWebView',
            'webView' => $hiddenWebView,
            'destination' => array(
                'url' => $circleUrl,
                'openIn' => 'inAppWebView',
                'title' => '',
                'sharedCookies' => true,
                'showAppBar' => false,
                'showToolbar' => false,
                'hideAppBar' => true,
                'webView' => $hiddenWebView,
            ),
        );

        return array(
            'navigate-grid' => array('type' => 'navigate', 'destination' => array('routeId' => 'grid')),
            'navigate-profile' => array('type' => 'navigate', 'destination' => array('routeId' => 'profile')),
            'refresh-grid' => array(
                'type' => 'sequence',
                'actions' => array(
                    array('type' => 'set-state', 'path' => 'refreshing', 'value' => true),
                    array('type' => 'refresh-data-source', 'dataSourceId' => 'grid'),
                    array('type' => 'set-state', 'path' => 'refreshing', 'value' => false),
                ),
            ),
            'grid-open-search' => array('type' => 'host-action', 'actionId' => 'open-grid-search'),
            'grid-open-professional-space' => $proAction,
            'grid-open-circle-connect' => $circleAction,
            'grid-open-find-talent' => array('type' => 'host-action', 'actionId' => 'open-grid-find-talent'),
            'grid-open-join-circles' => array('type' => 'host-action', 'actionId' => 'open-grid-join-circles'),
            'grid-see-all-exploring' => array('type' => 'host-action', 'actionId' => 'open-grid-exploring-all'),
            'grid-share-qr' => array('type' => 'host-action', 'actionId' => 'share-grid-qr'),
            'grid-select-qr-mode' => array('type' => 'host-action', 'actionId' => 'select-grid-qr-mode'),
        );
    }

    /**
     * Apply studio open-URL inputs onto Professional Space / Circle Connect.
     * Image URL arguments are ignored — icons always come from iconToken.
     */
    public static function applyFeatureCardUrls(
        array $content,
        string $professionalSpaceUrl,
        string $circleConnectUrl,
        string $professionalSpaceImage = '',
        string $circleConnectImage = ''
    ): array {
        $content = self::mergeContent($content);
        $cards = is_array($content['featureCards'] ?? null) ? $content['featureCards'] : array();
        foreach ($cards as $i => $card) {
            if (!is_array($card)) {
                continue;
            }
            $id = (string) ($card['id'] ?? '');
            if ($id === 'professional-space') {
                $url = trim($professionalSpaceUrl);
                $cards[$i]['url'] = $url;
                $cards[$i]['route'] = $url;
                $cards[$i]['image'] = '';
                $cards[$i]['imageUrl'] = '';
                $cards[$i] = self::normalizeSoftCard($cards[$i]);
            }
            if ($id === 'circle-connect') {
                $url = trim($circleConnectUrl);
                $cards[$i]['url'] = $url;
                $cards[$i]['route'] = $url;
                $cards[$i]['image'] = '';
                $cards[$i]['imageUrl'] = '';
                $cards[$i] = self::normalizeSoftCard($cards[$i]);
            }
        }
        $content['featureCards'] = $cards;
        return $content;
    }

    private static function knownIconToken($token, $fallback = 'grid'): string
    {
        $token = strtolower(trim((string) $token));
        $token = preg_replace('/^(fas|far|fab|fa-solid|fa-regular|fa-brands)\s+/', '', $token) ?? $token;
        if (strpos($token, 'fa-') === 0) {
            $token = substr($token, 3);
        }
        $map = array(
            'user' => 'user',
            'user-circle' => 'user',
            'users' => 'users',
            'user-friends' => 'users',
            'user-tie' => 'users',
            'grid' => 'grid',
            'border-all' => 'grid',
            'search' => 'search',
            'magnifying-glass' => 'search',
            'quote' => 'quote',
            'quote-left' => 'quote',
            'chevron-right' => 'chevron-right',
            'profile' => 'profile',
            'user-pen' => 'profile',
        );
        if (isset($map[$token])) {
            return $map[$token];
        }
        $known = array('grid', 'search', 'user', 'users', 'quote', 'chevron-right', 'profile');
        return in_array($token, $known, true) ? $token : (string) $fallback;
    }

    public static function buildIconRegistry(): array
    {
        return array(
            'grid' => array('flutter' => 'borderAll', 'web' => 'border-all'),
            'search' => array('flutter' => 'magnifyingGlass', 'web' => 'magnifying-glass'),
            'user' => array('flutter' => 'user', 'web' => 'user'),
            'users' => array('flutter' => 'users', 'web' => 'users'),
            'quote' => array('flutter' => 'quoteLeft', 'web' => 'quote-left'),
            'chevron-right' => array('flutter' => 'chevronRight', 'web' => 'chevron-right'),
            'profile' => array('flutter' => 'userPen', 'web' => 'user-pen'),
        );
    }

    /**
     * @param array $options userId, apiBase, content override, endpoint
     */
    public static function build(array $options = array()): array
    {
        $userId = max(0, (int) ($options['userId'] ?? 0));
        $content = self::mergeContent(isset($options['content']) && is_array($options['content']) ? $options['content'] : null);
        if (class_exists(GridWebSso::class)) {
            $content = GridWebSso::prepareContentForClient(
                $content,
                trim((string) ($options['ssoToken'] ?? ''))
            );
        }
        $endpoint = trim((string) ($options['endpoint'] ?? 'grid_api_creation.php'));
        if ($endpoint === '') {
            $endpoint = 'grid_api_creation.php';
        }

        $components = self::buildComponents($content);

        return array(
            'schemaType' => 'server-driven-mobile-app',
            'schemaVersion' => '3.0.0',
            'pageId' => 'grid',
            'routeId' => 'grid',
            'rootComponentId' => 'grid-page',
            'replaceAppShell' => false,
            'reuseHostAppShell' => true,
            'keepHostBottomNavigation' => true,
            'route' => array('path' => '/grid', 'pageId' => 'grid'),
            'appShell' => array(
                'reuseHostShell' => true,
                'replaceOnNestedSchema' => false,
                'keepHostBottomNavigation' => true,
                'bottomNavigationFixed' => true,
                'bottomNavigationOutsidePageScroll' => true,
                'bottomNavigationPosition' => 'fixed',
                'contentAvoidsBottomNavigation' => true,
                'reservedBottomInset' => 108,
            ),
            'page' => array(
                'id' => 'grid',
                'routeId' => 'grid',
                'rootComponentId' => 'grid-page',
                'dataSourceId' => 'grid',
                'presentationStates' => array(
                    'loadingComponentId' => 'grid-loading',
                    'emptyComponentId' => 'grid-empty',
                    'errorComponentId' => 'grid-error',
                ),
            ),
            'components' => $components,
            'actions' => self::buildActions($content),
            'dataSources' => array(
                'grid' => array(
                    'type' => 'rest',
                    'endpoint' => $endpoint,
                    'method' => 'GET',
                    'queryParameters' => array_filter(array(
                        'action' => 'get_grid',
                        'user_id' => $userId > 0 ? $userId : null,
                    )),
                    'responseMapping' => array(
                        'schema' => '$.schema',
                        'content' => '$.content',
                    ),
                    'published' => true,
                ),
            ),
            'content' => $content,
            'iconRegistry' => self::buildIconRegistry(),
            'action' => array(
                'fontSize' => 15,
                'fontWeight' => 700,
            ),
            'theme' => array(
                'colors' => array(
                    'surface' => '#ffffff',
                    'onAccent' => '#ffffff',
                    'onSurface' => '#111827',
                    'accent' => '#7c3aed',
                    'textPrimary' => '#2e1065',
                    'textSecondary' => '#6b7280',
                    'pageBackground' => '#f7f8fc',
                ),
            ),
            'typography' => array(
                'action' => array(
                    'fontSize' => 15,
                    'fontWeight' => 700,
                    'lineHeight' => 1.2,
                ),
                'pageTitle' => array(
                    'fontSize' => 26,
                    'fontWeight' => 800,
                    'maxLines' => 1,
                    'overflow' => 'ellipsis',
                ),
                'pageSubtitle' => array(
                    'fontSize' => 13,
                    'fontWeight' => 500,
                    'maxLines' => 2,
                    'overflow' => 'ellipsis',
                ),
            ),
            'designTokens' => self::designTokens(),
            'layoutPolicy' => array(
                'overflowPolicy' => 'reflow',
                'overflowX' => 'hidden',
                'width' => 'fill',
                'maxWidth' => '100%',
                'reservedBottomInset' => 108,
                'responsiveBreakpoints' => array(
                    'narrow' => 0,
                    'compact' => 320,
                    'regular' => 480,
                    'medium' => 600,
                    'expanded' => 840,
                ),
                'responsive' => array(
                    'defaultBreakpoint' => 'compact',
                    'breakpoints' => array(
                        'narrow' => array('maxWidth' => 319),
                        'compact' => array('minWidth' => 320, 'maxWidth' => 599),
                        'medium' => array('minWidth' => 600, 'maxWidth' => 839),
                        'expanded' => array('minWidth' => 840),
                    ),
                    'grid' => array(
                        'narrowScreenThresholdDp' => 340,
                        'narrowScreenMaxColumns' => 2,
                        'minimumCellWidthDp' => 0,
                        'clampColumnSpanToColumns' => true,
                        'preserveMetroColumnsOnNarrow' => false,
                    ),
                    'overflowPolicy' => array(
                        'headerText' => 'ellipsis',
                        'default' => 'ellipsis',
                    ),
                ),
            ),
            'security' => array(
                'requireHttps' => true,
                'allowedAssetDomains' => array_values(array_unique(array_filter(array(
                    'efactor.tech',
                    'www.efactor.tech',
                    preg_replace('/:\d+$/', '', (string) parse_url(self::publicOrigin(), PHP_URL_HOST)),
                )))),
            ),
        );
    }
}
