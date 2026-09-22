<?php

use app\common\model\SystemConfigModel;
use support\think\Cache;

if(!defined("remove_xss")){
    function remove_xss($string): string
    {
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);
        $parm1 = ['vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound'];
        $parm2 = ['onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'];
        $parm = array_merge($parm1, $parm2);
        for ($i = 0; $i < sizeof($parm); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($parm[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                    $pattern .= '|(&#0([9][10][13]);?)?';
                    $pattern .= ')?';
                }
                $pattern .= $parm[$i][$j];
            }
            $pattern .= '/i';
            $string = preg_replace($pattern, '', $string);
        }
        return trim($string);
    }
}

if(!defined("formatDate")){
    function formatDate($timestamp = false, $format = 'Y-m-d H:i:s')
    {
        if (!$timestamp) {
            $timestamp = time();
        }
        return date($format, $timestamp);
    }
}

if(!defined("random")){
    function random(int $length = 10, string $type = 'letter', int $convert = 0): string
    {
        $config = [
            'number' => '1234567890',
            'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
            'all' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        ];

        if (!isset($config[$type])) $type = 'letter';
        $string = $config[$type];

        $code = '';
        $strlen = strlen($string) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $string[mt_rand(0, $strlen)];
        }
        return ($convert == 1) ? strtoupper($code) : strtolower($code);
    }
}

if(!defined("formatWhere")){
    function formatWhere(array $data): array
    {
        $where = [];
        foreach ($data as $k => $v) {
            if (($v == null || $v == '') || (is_int($k) && !is_array($v)) || (is_array($v) && count($v) < 2)) {
                continue;
            }
            if (count($v) == 2) {
                if ($v[1] == null || $v[1] == '') {
                    continue;
                }
                $where[] = [$v[0], '=', $v[1]];
            } else {
                $type = strtolower($v[1]);
                if ($v[2] == '' || $v[2] == null) {
                    continue;
                }
                switch ($type) {
                    case "like":
                        $where[] = [$v[0], 'like', '%' . str_replace(' ', '%', $v[2]) . '%'];
                        break;
                    case 'exp':
                        $v[0] = Db::raw($v[2]);
                        break;
                    case 'between':
                        if (is_string($v[2]) && stripos($v[2], ' - ') !== false) {
                            $v[2] = explode(' - ', $v[2]);
                        }
                        $where[] = [$v[0], 'between', $v[2]];
                        break;
                    case 'between time':
                        if (is_string($v[2]) && stripos($v[2], '-') !== false) {
                            $v[2] = explode(' - ', $v[2]);
                        }
                        if (!$v[2][0] && !$v[2][1]) {
                            break;
                        }
                        if ($v[2][0] == null) {
                            $v[2][0] = date('Y-m-d H:i:s', 0);
                        }
                        if ($v[2][1] == null) {
                            $v[2][1] = date('Y-m-d H:i:s');
                        }
                        $where[] = [$v[0], 'between time', $v[2]];
                        break;
                    case 'find in set':
                        if (is_array($v[2])) {
                            $v[2] = implode(',', $v[2]);
                        }
                        $where[] = [$v[0], 'find in set', $v[2]];
                        break;
                    default:
                        $where[] = [$v[0], $type, $v[2]];
                        break;
                }
            }
        }
        return $where;
    }
}


if(!defined("decimal")){
    function decimal($str, $format = 2): string
    {
        if (!$str) {
            return "0.00";
        }
        return number_format($str, $format, ".", "");
    }
}

if(!defined("uuid")){
    function uuid(){
        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}


if(!defined("getDomain")){
    function getDomain(){
        // 协议：优先取代理头，其次判断是否 SSL
        $request = request();
        $scheme = $request->header('x-forwarded-proto');
        if (!$scheme) {
            $scheme = $request->connection->transport === 'ssl' ? 'https' : 'http';
        }
        $host = $request->host();
        return $scheme . '://' . $host;
    }
}

if(!defined("formatUrl")){
    function formatUrl($path){
        if(stripos($path, '://') !== false){
            return getDomain() . $path;
        }
        return $path;
    }
}

if (!function_exists('sys')) {
    /**
     * 获取系统配置
     * @param string|null $key     配置键名，为空时返回全部
     * @param mixed       $default 默认值
     * @return mixed
     */
    function sys(?string $key = null, $default = null)
    {
        static $config = null;
        if ($config === null) {
            $config = Cache::get('config_data');
            if (!$config) {
                $list = SystemConfigModel::column('value', 'name');
                Cache::set('config_data', $list);
            }
        }
        // 不传 key 返回全部配置
        if ($key === null) {
            return $config;
        }
        return $config[$key] ?? $default;
    }
}