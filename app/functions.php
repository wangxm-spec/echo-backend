<?php
/**
 * Here is your custom functions.
 */
/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

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

function formatDate($timestamp = false, $format = 'Y-m-d H:i:s')
{
    if (!$timestamp) {
        $timestamp = time();
    }
    return date($format, $timestamp);
}

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


function decimal($str, $format = 2): string
{
    if (!$str) {
        return "0.00";
    }
    return number_format($str, $format, ".", "");
}