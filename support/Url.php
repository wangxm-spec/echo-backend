<?php

namespace support;

class Url
{
    protected static array $config = [
        'domain' => '',
        'root'   => '/',
        'suffix' => '',
    ];

    // 当前应用名（多应用模式用），如 'admin'
    protected static string $app = '';

    // 当前控制器，如 'login'
    protected static string $currentController = '';

    public static function init(array $config = []): void
    {
        self::$config = array_merge(self::$config, $config);
    }

    /**
     * 设置当前应用名
     */
    public static function setApp(string $app): void
    {
        self::$app = trim($app, '/');
    }

    public static function getApp(): string
    {
        return self::$app;
    }

    public static function setCurrentController(string $controller): void
    {
        self::$currentController = $controller;
    }

    public static function build(string $url = '', array $params = [], bool $domain = false, bool $suffix = true): string
    {
        if (preg_match('/^(https?:)?\/\//i', $url)) {
            return $url;
        }

        // 拆 ? 后的 query
        $query = [];
        if (str_contains($url, '?')) {
            [$url, $queryStr] = explode('?', $url, 2);
            parse_str($queryStr, $query);
        }
        $query = array_merge($query, $params);

        $url = trim($url, '/');

        // ★ 补当前控制器（单段地址）
        if ($url !== '' && !str_contains($url, '/')) {
            $url = self::currentController() . '/' . $url;
        }

        // ★ 补应用名（多应用模式）—— 只有 url 里没写应用名时才补
        if (self::$app && !self::hasAppPrefix($url)) {
            $url = self::$app . '/' . $url;
        }

        $root = rtrim(self::$config['root'], '/') . '/';
        $url  = $root . ltrim($url, '/');

        $queryStr  = $query ? '?' . http_build_query($query) : '';
        $suffixStr = ($suffix && !empty(self::$config['suffix']) && !$queryStr)
            ? self::$config['suffix'] : '';

        $result = $url . $suffixStr . $queryStr;

        if ($domain) {
            $result = rtrim(self::$config['domain'], '/') . $result;
        }
        return $result;
    }

    /**
     * 判断 url 是否已经带了应用名前缀
     */
    protected static function hasAppPrefix(string $url): bool
    {
        // 已带 admin/...、api/... 这种前缀的，不再重复加
        if (str_starts_with($url, self::$app . '/')) {
            return true;
        }
        // 如果 url 第一段就是已注册的其它应用名，也认为是带前缀的
        $first = explode('/', $url)[0] ?? '';
        return in_array($first, self::getAllApps(), true);
    }

    /**
     * 从 app/ 目录自动扫描所有应用名（你也可以手写死）
     */
    protected static function getAllApps(): array
    {
        static $apps = null;
        if ($apps !== null) {
            return $apps;
        }
        $apps = [];
        $dir = base_path() . '/app';   // Webman 的 base_path()
        if (is_dir($dir)) {
            foreach (scandir($dir) as $item) {
                if ($item === '.' || $item === '..') continue;
                if (is_dir($dir . '/' . $item)) {
                    $apps[] = $item;
                }
            }
        }
        return $apps;
    }

    protected static function currentController(): string
    {
        return self::$currentController ?: 'index';
    }
}