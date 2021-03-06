<?php

namespace o;


class u_Web extends StdModule {

    

    // TODO: complete list
    static private $VALIDATION_RULES = [
        'url'      => '^https?://\\S+$',
        // ___@___.__ format, only one '@' symbol
        'email'    => '^\\S+?@[^@\\s]+\\.\\S+$',
        'digits'   => '^[0-9]+$',
    ];

    private $jsData = [];
    private $validators = [];
    private $request;
    private $isCrossOrigin = null;
    private $includedValidatorJs = false;


    // REQUEST
    // --------------------------------------------

    function u_request_headers () {
        return OMap::create(Tht::data('requestHeaders'));
    }

    function u_request_header ($val) {
        return Tht::data('requestHeaders', $val);
    }

    function u_request () {

        
        if (!$this->request) {

            $ip = Tht::getPhpGlobal('server', 'REMOTE_ADDR');
            $ips = preg_split('/\s*,\s*/', $ip);

            $r = [
                'ip'          => $ips[0],
                'ips'         => $ips,
                'isHttps'     => $this->isHttps(),
                'userAgent'   => Tht::getPhpGlobal('server', 'HTTP_USER_AGENT'),
                'method'      => strtolower(Tht::getPhpGlobal('server', 'REQUEST_METHOD')),
                'referrer'    => Tht::getPhpGlobal('server', 'HTTP_REFERER', ''),
                'languages'   => $this->languages(),
                'isAjax'      => $this->isAjax(),
            ];

            $relativeUrl = $this->relativeUrl();
            $scheme = $r['isHttps'] ? 'https' : 'http';
            $hostWithPort = Tht::getPhpGlobal('server', 'HTTP_HOST');
            $fullUrl = $scheme . '://' . $hostWithPort . $relativeUrl;

            $r['url'] = $this->u_parse_url($fullUrl);

            $fullUrl = rtrim($fullUrl, '/');
            $r['url']['full'] = $fullUrl;
            $r['url']['relative'] = $relativeUrl;

            $urlParts = explode('/', $r['url']['path']);
            $r['url']['page'] = end($urlParts);

            $this->request = OMap::create($r);
        }

        return $this->request;
    }

    // TODO: support proxies (via HTTP_X_FORWARDED_PROTO?)
    function isHttps () {
        $https = Tht::getPhpGlobal('server', 'HTTPS', '');
        $port = Tht::getPhpGlobal('server', 'SERVER_PORT');

		return (!empty($https) && $https !== 'off') || intval($port) === 443;
    }

    function isAjax () {
        $requestedWith = WebMode::getWebRequestHeader('x-requested-with');
        return (strtolower($requestedWith) === 'xmlhttprequest');
    }

    function relativeUrl() {
        $path = Tht::getPhpGlobal('server', "REQUEST_URI");  // SCRIPT_URL
        if (!$path) {
            // Look for PHP dev server path instead.
            $path = Tht::getPhpGlobal('server', "SCRIPT_NAME");
            if (!$path) {
                Tht::configError("Unable to determine route path.  Only Apache and PHP dev server are supported.");
            }
        }
        return $path;
    }

    // THANKS: http://www.thefutureoftheweb.com/blog/use-accept-language-header
    function languages () {
        $langs = [];
        $acceptLang = strtolower(Tht::getPhpGlobal('server', 'HTTP_ACCEPT_LANGUAGE', ''));
        if ($acceptLang) {
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/', $acceptLang, $matches);
            if (count($matches[1])) {
                $langs = array_combine($matches[1], $matches[4]);
                foreach ($langs as $lang => $val) {
                    if ($val === '') $langs[$lang] = 1;
                }
                arsort($langs, SORT_NUMERIC);
            }
        }
        return array_keys($langs);
    }







    // QUERY / URL PARSING
    // --------------------------------------------

    function u_parse_url ($url) {
        ARGS('s', func_get_args());
        return OMap::create(parse_url($url));
    }

    function u_unparse_url($u) {
        ARGS('s', func_get_args());

        $u = uv($u);
        $parts = [
            $this->unparseVal($u, 'scheme',   '__://'),
            $this->unparseVal($u, 'host',     '__'),
            $this->unparseVal($u, 'port',     ':__'),
            $this->unparseVal($u, 'path',     '__'),
            $this->unparseVal($u, 'query',    '?__'),
            $this->unparseVal($u, 'fragment', '#__'),
        ];
        return implode('', $parts);
    }

    function unparseVal($u, $k, $template) {
        if (!isset($u[$k])) { return ''; }
        return str_replace('__', $u[$k], $template);
    }


    function u_parse_query ($s, $multiKeys=[]) {

        ARGS('sl', func_get_args());

        $ary = [];
        $pairs = explode('&', $s);

        foreach ($pairs as $i) {
            list($name, $value) = explode('=', $i, 2);
            if (in_array($name, $multiKeys)) {
                if (!array_key_exists($ary[$name])) {
                    $ary[$name] = [];
                }
                $ary[$name] []= $value;
            }
            else {
                $ary[$name] = $value;
            }
        }
        return OMap::create($ary);
    }

    function query ($params) {
        return http_build_query($params);
    }





	// RESPONSE
    // --------------------------------------------

    function u_redirect ($url, $code=303) {
        ARGS('*n', func_get_args());

        if (OLockString::isa($url)) {
            $url = $url->u_unlocked();
        } else {
            if (v($url)->u_is_url()) {
                Tht::error("Redirect URL `$url` must be relative or a LockString.");
            }
        }

        header('Location: ' . $url, true, $code);
        exit();
    }

    function u_set_response_code ($code) {
        ARGS('n', func_get_args());
        http_response_code($code);
    }

    function u_set_header ($name, $value, $multiple=false) {
        ARGS('ssf', func_get_args());

        header($name . ': ' . $value, !$multiple);
    }

    function u_set_cache_header ($expiry='+1 year') {
        ARGS('s', func_get_args());

        $this->u_set_header('Expires', gmdate('D, d M Y H:i:s \G\M\T', strtotime($expiry)));
    }

    function u_nonce () {
        return Security::getNonce();
    }

    function u_csrf_token() {
        return Security::getCsrfToken();
    }

    // SEND DOCUMENTS
    // --------------------------------------------

    // function u_print_block($h, $title='') {
    //     $html = OLockString::getUnlocked($h);
    //     $this->u_send_json([
    //         'status' => 'ok',
    //         'title' => $title,
    //         'html' => $html
    //     ]);
    // }

    function sendByType($lout) {
        $type = $lout->u_get_string_type();

        if ($type == 'css') {
            return $this->u_return_css($lout);
        }
        else if ($type == 'js') {
            return $this->u_return_js($lout);
        }
    }

    function u_send_json ($map) {
        ARGS('m', func_get_args());

        $this->u_set_header('Content-Type', 'application/json');
        u_Web::sendGzip(new \o\OLockString (json_encode(uv($map))));
    }

    function u_send_text ($text) {
        ARGS('s', func_get_args());

        $this->u_set_header('Content-Type', 'text/plain');
        u_Web::sendGzip(new \o\OLockString ($text));
    }

    function u_send_css ($chunks) {

        ARGS('*', func_get_args());

        $this->u_set_header('Content-Type', 'text/css');
        $this->u_set_cache_header();
        $this->startGzip();
        if (! (is_object($chunks) && v($chunks)->u_is_list())) {
            $chunks = OList::create([ $chunks ]); // todo make a List
        }
        foreach ($chunks->val as $c) {
            print OLockString::getUnlocked($c);
        }
        $this->endGzip();
    }

    function u_send_js ($chunks) {

        ARGS('*', func_get_args());

        $this->u_set_header('Content-Type', 'application/javascript');
        $this->u_set_cache_header();

        $this->startGzip();
        if (! (is_object($chunks) && v($chunks)->u_is_list())) {
            $chunks = OList::create([ $chunks ]);
        }
        print "(function(){\n";
        foreach ($chunks->val as $c) {
            print OLockString::getUnlocked($c);
        }
        print "\n})();";
        $this->endGzip();
    }

    function u_send_html ($html) {
        OLockString::getUnlocked($html);
        u_Web::sendGzip($html);
    }

    function u_danger_danger_send ($s) {
        ARGS('s', func_get_args());
        print $s;
    }

    // Print a well-formed HTML document with sensible defaults
    function u_send_page ($doc) {

        ARGS('m', func_get_args());

        $body = '';

        if ($doc['body']) {
            $chunks = [];
            if (OList::isa($doc['body'])) {
                $chunks = $doc['body'];
            } else {
                $chunks = [$doc['body']];
            }

            foreach ($chunks as $c) {
                $body .= OLockString::getUnlocked($c);
            }
        }

        // if (u_Web::u_is_ajax()) {
        //     u_Web::u_send_block($body, $header['title']);
        //     return;
        // }

        $css = $this->assetTags(
            'css',
            $doc['css'],
            '<link rel="stylesheet" href="{URL}" />',
            '<style nonce="{NONCE}">{BODY}</style>'
        );

        $js = $this->assetTags(
            'js',
            $doc['js'],
            '<script src="{URL}" nonce="{NONCE}"></script>',
            '<script nonce="{NONCE}">{BODY}</script>'
        );

        $title = $doc['title'];
        $description = isset($doc['description']) ?: '';

        // TODO: get updateTime of the files
        $cacheTag = '?cache=' . Source::getAppCompileTime();
        $image = isset($doc['image']) ? '<meta property="og:image" content="'. $doc['image'] . $cacheTag .'">' : "";
        $icon = isset($doc['icon']) ? '<link rel="icon" href="'. $doc['icon'] . $cacheTag .'">' : "";
       // $jsData = Tht::module('Js')->serializeData();

        $bodyClasses = uv($doc['bodyClasses']) ?: [];
        $bodyClass = implode(' ', $bodyClasses);
        $bodyClass = preg_replace('/[^a-zA-Z0-9_\- ]/', '', $bodyClass); // TODO: call a lib to untaint instead

        $this->startGzip();

        $comment = '';
        if (isset($doc['comment'])) {
            $comment = "<!--\n\n" . v(v(v($doc['comment'])->u_unlocked())->u_indent(4))->u_trim_right() . "\n\n-->";
        }

        $out = <<<HTML
<!doctype html>
$comment
<html>
    <head>
        <title>$title</title>
        <meta name="description" content="$description"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta property="og:title" content="$title"/>
        <meta property="og:description" content="$description"/>
        $image $icon $css
    </head>
    <body class="$bodyClass">
        $body
        $js
    </body>
</html>
HTML;

       print $out;

       $this->endGzip();
       flush();

       $cacheTag = defined('STATIC_CACHE_TAG') ? constant('STATIC_CACHE_TAG') : '';

       if ($cacheTag && isset($doc['staticCache']) && $doc['staticCache']) {
           $cacheFile = md5(Tht::module('Web')->u_request()['url']['relative']);
           $cachePath = Tht::path('cache', 'html/' . $cacheTag . '_' . $cacheFile . '.html');
           file_put_contents($cachePath, $out);
       }
    }

    function u_send_error ($code, $title='') {

        ARGS('ns', func_get_args());

        http_response_code($code);

        if ($code !== 500) {
            // User custom error page
            WebMode::runStaticRoute($code);
        }

        // User custom error page
        // $errorPage = Tht::module('File')->u_document_path($code . '.html');
        // if (file_exists($errorPage)) {
        //     print(file_get_contents($errorPage));
        //     exit(1);
        // }

        if (!$title) {
            $title = $code === 404 ? 'Page Not Found' : 'Website Error';
        }

        if (!$this->u_request()['isAjax']) {
            ?><html><head><title><?= $title ?></title></head><body>
            <div style="text-align: center; font-family: <?= u_Css::u_sans_serif_font() ?>;">
            <h1 style="margin-top: 40px;"><?= $title ?></h1>
            <div style="margin-top: 40px"><a style="text-decoration: none; font-size: 20px;" href="/">Home Page</a></div></div>
            </body></html><?php
        }

        exit(1);
    }

    // print css & js tags
    function assetTags ($type, $paths, $incTag, $blockTag) {

        $paths = uv($paths);

        if (!$paths) { return ''; }

        if (!is_array($paths)) {
            $paths = [$paths];
        }

        $nonce = Tht::module('Web')->u_nonce();

        $includes = [];
        $blocks = [];
        foreach ($paths as $path) {
            if (OLockString::isa($path)) {
                // Inline it in the HTML document
                $str = OLockString::getUnlocked($path);
                $tag = str_replace('{BODY}', $str, $blockTag);
                $tag = str_replace('{NONCE}', $nonce, $tag);
                $blocks []= $tag;
            }
            else {

                if (preg_match('/^http(s?):/i', $path)) {
                    
                } else {
                    // Link to asset, with cache time set to file modtime
                    $basePath = preg_replace('/\?.*/', '', $path);
                    $filePath = Tht::getThtFileName(Tht::path('pages', $basePath));
                    $cacheTag = strpos($path, '?') === false ? '?' : '&';
                    $cacheTag .= 'cache=' . filemtime($filePath);
                    $path .= $cacheTag;
                }

                $tag = str_replace('{URL}', $path, $incTag);
                $tag = str_replace('{NONCE}', $nonce, $tag);
                $includes []= $tag;
            }
        }

        $sIncludes = implode("\n", $includes);
        $sBlocks = implode("\n\n", $blocks);

        return $sIncludes . "\n" . $sBlocks;
    }




    // GZIP
    // --------------------------------------------

    function sendGzip ($xOut) {
        $out = OLockString::getUnlocked($xOut);
        $this->startGzip(true);
        print $out;
        $this->endGzip(true);
        flush();
    }

    function startGzip ($forceGzip=false) {
        if ($forceGzip || Tht::getConfig('compressOutput')) {
            ob_start("ob_gzhandler");
        }
    }

    function endGzip ($forceGzip=false) {
        if ($forceGzip || Tht::getConfig('compressOutput')) {
            ob_end_flush();
        }
    }





    // MARKUP
    // --------------------------------------------

    function u_parse_html($raw) {
        return Tht::parseTemplateString('html', $raw);
    }

    function u_table ($rows, $keys, $headings=[], $class='') {
        $class = preg_replace('/[^a-zA-Z0-9_-]/', '', $class);  // [security]

        $str = "<table class='$class'>\n";
        $rows = uv($rows);
        $keys = uv($keys);
        $headings = uv($headings);
        $str .= '<tr>';
        foreach ($headings as $h) {
            $str .= '<th>' . $h . '</th>';
        }
        $str .= '</tr>';
        foreach ($rows as $row) {
            $str .= '<tr>';
            $row = uv($row);
            foreach ($keys as $k) {
                $str .= '<td>' . (isset($row[$k]) ? $row[$k] : '') . '</td>';
            }
            $str .= '</tr>';
        }
        $str .= "</table>\n";

        return new \o\HtmlLockString ($str);
    }

    function u_link($label, $url) {
        if ($label === '') {
            $label = $url;
        }
        $url   = v($url)->u_encode_html();
        $label = v($label)->u_encode_html();

        $str = "<a href=\"$url\">$label</a>";
        return new \o\HtmlLockString ($str);
    }

    function u_breadcrumbs($links, $joiner = '&gt;') {
        $aLinks = [];
        foreach ($links as $l) {
            $aLinks []= Tht::module('Web')->u_link($l['label'], $l['url'])->u_unlocked();
        }

        $joiner = '<span class="breadcrumbs-joiner">' . v($joiner)->u_unlocked() . '</span>';
        $h = implode($aLinks, $joiner);
        $h = "<div class='breadcrumbs'>$h</div>";

        return OLockString::create('\o\HtmlLockString', $h);
    }

    // DO NOT REMOVE
    // function makeStarIcon($centerX, $centerY, $outerRadius, $innerRadius) {
    //     $arms = 5;
    //     $angle = pi() / $arms;
    //     $offset = -0.31;
    //     $points = [];
    //     for ($i = 0; $i < 2 * $arms; $i++) {
    //         $r = ($i & 1) == 0 ? $outerRadius : $innerRadius;
    //         $currX = $centerX + cos(($i * $angle) + $offset) * $r;
    //         $currY = $centerY + sin(($i * $angle) + $offset) * $r;
    //         $points []= number_format($currX,2) . "," . number_format($currY, 2);
    //     }
    //     return implode(' ', $points);
    // }

    function icons() {

        // TODO: mail, cart
        return [

            'arrowLeft'  => '<path d="M30,50H90z"/><polyline points="60,10 20,50 60,90"/>',
            'arrowRight' => '<path d="M10,50H70z"/><polyline points="40,10 80,50 40,90"/>',
            'arrowUp'    => '<path d="M50,30V90z"/><polyline points="10,60 50,20 90,60"/>',
            'arrowDown'  => '<path d="M50,10V70z"/><polyline points="10,40 50,80 90,40"/>',

            'chevronLeft'  => '<polyline points="70,10 30,50 70,90"/>',
            'chevronRight' => '<polyline points="30,10 70,50 30,90"/>',
            'chevronUp'    => '<polyline points="10,70 50,30 90,70"/>',
            'chevronDown'  => '<polyline points="10,30 50,70 90,30"/>',

            'wideChevronLeft'  => '<polyline points="60,-5 30,50 60,105"/>',
            'wideChevronRight' => '<polyline points="40,-5 70,50 40,100"/>',
            'wideChevronUp'    => '<polyline points="-5,60 50,30 105,60"/>',
            'wideChevronDown'  => '<polyline points="-5,40 50,70 105,40"/>',

            'caretLeft'   => '<path class="fill" d="M60,20 30,50 60,80z"/>',
            'caretRight'  => '<path class="fill" d="M40,20 70,50 40,80z"/>',
            'caretUp'     => '<path class="fill" d="M20,60 50,30 80,60z"/>',
            'caretDown'   => '<path class="fill" d="M20,40 50,70 80,40z"/>',

            'menu'         => '<path d="M0,20H100zM0,50H100zM0,80H100z"/>',
            'plus'         => '<path d="M15,50H85zM50,15V85z"/>',
            'minus'        => '<path d="M15,50H85z"/>',
            'cancel'       => '<path d="M20,20 80,80z M80,20 20,80z"/>',
            'check'        => '<polyline points="15,45 40,70 85,15"/>',

            'home'   => '<path class="fill" d="M0,50 50,15 100,50z"/><rect class="fill" x="15" y="50" height="40" width="25" /><rect class="fill" x="60" y="50" height="40" width="25" /><rect class="fill" x="40" y="50" height="15" width="40" /><rect class="fill" x="70" y="20" height="20" width="15" />',
            'download' => '<path class="fill" d="M10,40 50,75 90,40z"/><rect class="fill" x="35" y="0" height="42" width="30" /><rect class="fill" x="0" y="88" height="12" width="100" />',
            'upload'   => '<path class="fill" d="M10,35 50,0 90,35z"/><rect class="fill" x="35" y="33" height="40" width="30" /><rect class="fill" x="0" y="88" height="12" width="100" />',
            'search'    => '<circle cx="45" cy="45" r="30"/><path d="M95,95 65,65z"/>',
            'lock'      => '<rect class="fill" x="15" y="35" height="50" width="70" rx="5" rx="5" /><rect style="stroke-width:12" x="31" y="7" height="50" width="38" rx="15" rx="15" />',
            'heart'     => '<path class="fill" d="M90,45 50,85 10,45z"/><rect class="fill" x="48" y="43" height="4" width="4"/><circle class="fill" cx="29" cy="31" r="23"/><circle class="fill" cx="71" cy="31" r="23"/>',

            // generated from $this->starIcon(50,50,55,22)
            'star'     => '<path class="fill" d="M102.38,33.22 70.89,56.89 82.14,94.63 49.91,72.00 17.49,94.36 29.05,56.71 -2.24,32.79 37.14,32.15 50.23,-5.00 63.01,32.26z"/>',

            'twitter' => '<svg class="ticonx" viewBox="0 0 33 33"><g><path d="M 32,6.076c-1.177,0.522-2.443,0.875-3.771,1.034c 1.355-0.813, 2.396-2.099, 2.887-3.632 c-1.269,0.752-2.674,1.299-4.169,1.593c-1.198-1.276-2.904-2.073-4.792-2.073c-3.626,0-6.565,2.939-6.565,6.565 c0,0.515, 0.058,1.016, 0.17,1.496c-5.456-0.274-10.294-2.888-13.532-6.86c-0.565,0.97-0.889,2.097-0.889,3.301 c0,2.278, 1.159,4.287, 2.921,5.465c-1.076-0.034-2.088-0.329-2.974-0.821c-0.001,0.027-0.001,0.055-0.001,0.083 c0,3.181, 2.263,5.834, 5.266,6.438c-0.551,0.15-1.131,0.23-1.73,0.23c-0.423,0-0.834-0.041-1.235-0.118 c 0.836,2.608, 3.26,4.506, 6.133,4.559c-2.247,1.761-5.078,2.81-8.154,2.81c-0.53,0-1.052-0.031-1.566-0.092 c 2.905,1.863, 6.356,2.95, 10.064,2.95c 12.076,0, 18.679-10.004, 18.679-18.68c0-0.285-0.006-0.568-0.019-0.849 C 30.007,8.548, 31.12,7.392, 32,6.076z"></path></g></svg>',

            'facebook' => '<svg class="ticonx" viewBox="0 0 33 33"><g><path d="M 17.996,32L 12,32 L 12,16 l-4,0 l0-5.514 l 4-0.002l-0.006-3.248C 11.993,2.737, 13.213,0, 18.512,0l 4.412,0 l0,5.515 l-2.757,0 c-2.063,0-2.163,0.77-2.163,2.209l-0.008,2.76l 4.959,0 l-0.585,5.514L 18,16L 17.996,32z"></path></g></svg>',

        ];
    }

    function u_get_icons() {
        return array_keys($this->icons());
    }

    function u_icon($id) {

        $icons = $this->icons();

        if (!isset($icons[$id])) { Tht::error("Unknown icon: `$id`"); }

        if (substr($icons[$id], 0, 4) == '<svg') {
            return new \o\HtmlLockString($icons[$id]);
        }

        return new \o\HtmlLockString('<svg class="ticon" viewBox="0,0,100,100">' . $icons[$id] . '</svg>');
    }



    function u_mask_email($email) {

        // TODO: show placeholder if not logged in user
        // if (!Tht::module('User')->u_logged_in() && $placeholder) {
        //     return $placeholder;
        // }

        $spanPos = rand(1, strlen($email) - 5);
        $begin = substr($email, 0, $spanPos);
        $end = substr($email, $spanPos);

        $r = strtolower(Tht::module('String')->u_random(rand(6,12)));
        $r = preg_replace('/[^a-z]/', '', $r);

        $r2 = strtolower(Tht::module('String')->u_random(rand(6,12)));
        $r2 = preg_replace('/[^a-z]/', '', $r2);

        $e = Tht::module('String')->u_random(rand(5,80));
        $e = preg_replace('/[0-9xz\/\+]+/', ' ', $e);

        $xe = $begin . "<span class=\"$r\">$e</span><span class=\"$r2\">" . $end . "</span>";
        $xe .= "<style> .$r { display: none; } </style>";

        return new HtmlLockString ($xe);
    }


    function u_route_param ($key) {
        return WebMode::getWebRouteParam($key);
    }



    // USER INPUT
    // --------------------------------------------


    // Temporary Methods

    function u_temp_get_input($method, $name, $type='token') {

        if (strpos('get|post|dangerDangerRemote', $method) === false) {
            Tht::error("Invalid input method: `$method`.  Supported methods: `get`, `post`, `dangerDangerRemote`");
        }

        // Require https for non-GET requests
        // if ($method !== 'get' && !Tht::module('Web')->u_request()['isHttps'] && !Tht::isMode('testServer')) {
        //     Tht::error("Page must be run under 'https' to accept non-GET requests.\n\nCheck your web host's admin panel, or visit 'letsencrypt.org' for a free SSL cert.");
        // }

        // Disallow cross-origin request
        if ($method === 'post') {
            if (Security::isCrossOrigin()) {
                Tht::module('Web')->u_send_error(403, 'Remote Origin Not Allowed');
            }
        }

        if ($method == 'dangerDangerRemote') {
            $method = 'post';
        }

        $val = trim(Tht::getPhpGlobal($method, $name, false));

        $validate = $this->u_temp_validate_input($val, $type);

        return $validate['value'];
    }

    function u_temp_validate_input($val, $type='token') {

        $isFail = false;

        if ($type === 'token') {
            if (preg_match('/[^a-zA-Z0-9_]/', $val)) {
    			$isFail = true;
    		}
            if (strlen($val) > 64) {
                $isFail = true;
            }
        }
        else if ($type === 'id') {
            if (preg_match('/[^0-9]/', $val)) {
            	$isFail = true;
    		}
            if (strlen($val) > 12) {
                $isFail = true;
            }
            $val = intval($val);
        }
        else if ($type === 'number') {
            if (preg_match('/[^0-9\.\-]/', $val)) {
            	$isFail = true;
    		}
            if (strlen($val) > 8) {
                $isFail = true;
            }
            $val = floatval($val);
        }
        else if ($type === 'flag') {
            if ($val !== 'true' && $val !== 'false' && $val !== '1' && $val !== '0') {
            	$isFail = true;
    		}
            $val = ($val === 'true' || $val === '1');
        }
        else if ($type === 'email') {
            if (!preg_match('/^\S+?@[^@\s]+\.\S+$/', $val)) {
            	$isFail = true;
    		}
            if (strlen($val) > 128) {
                $isFail = true;
            }
		}
        else if ($type === 'text') {
            // one line of text
            $val = preg_replace('/\s+/', ' ', $val);
            $val = preg_replace('/<.*?>/', '', $val);
            if (strlen($val) > 128) {
                $isFail = true;
            }
		}
        else if ($type === 'textarea') {
            // multiline text
            $val = preg_replace('/<.*?>/', '', $val);
            $val = preg_replace('/ +/', ' ', $val);
            $val = preg_replace('/\n{2,}/', "\n\n", $val);
		}
        else if ($type === 'dangerDangerRaw') {
            // pass through as-is
		}
        else {
            Tht::error("Unknown input type: `$type`");
        }

        return OMap::create([
            'value' => $isFail ? '' : $val,
            'ok' => !$isFail
        ]);
    }





    // NEW FORM

    function u_form ($schema, $formId='defaultForm') {
        return new u_Form ($schema, $formId);
    }





    function u_reader($schema, $formId="defaultForm") {
        $this->validators[$formId] = uv($schema);
    }

    function getRuleMap($formId='defaultForm') {
        if (!isset($this->validators[$formId])) {
            Tht::error("Unknown form ID: `$formId`");
        }
        return $this->validators[$formId];
    }

    function u_get_data($method, $formId="defaultForm") {

        $ruleset = $this->getRuleMap($formId);

        $p = [];

        foreach ($ruleset as $name => $sRules) {
            $rawVal = Tht::getPhpGlobal($method, $name, false);
            $isOptional = strpos($sRules, 'optional') !== false;
            if ($isOptional && trim($rawVal) === '') {
                continue;
            }
            $rules = explode('|', $sRules);
            foreach ($rules as $rule) {
                if (trim($rule) === '') { continue; }
                if ($rule === 'optional') { continue; }
                $isOk = $this->u_test_rule($rule, $rawVal);
                if (!$isOk) {
                    return u_Status::u_fail();
                }
            }
            $p[$name] = $rawVal;
        }
        return u_Status::u_new(0, OMap::create($p));
    }

    function u_test_rule ($rule, $val) {
        $rules = u_Web::$VALIDATION_RULES;
        if (!isset($rules[$rule])) {
            Tht::error("Unknown validation rule: `$rule`");
        }
        $apply = $rules[$rule];
        if (!preg_match("#$apply#", $val)) {
            return false;
        }
        return true;
    }



    function checkAjax ($isAjax) {
        if ($isAjax !== $this->u_request()['isAjax']) {
            Tht::errorLog('Expected ' . ($isAjax ? 'Ajax' : 'non-Ajax') . ' request.');
            return false;
        }
        return true;
    }

    /***

    function u_data ($method, $key, $type='token') {
        if ($method === 'route') {
            return WebMode::getWebRouteParam($key);
        }
        else if ($method === 'get') {
            return $this->getValue('get', false, $key, $type);
        }
        else if ($method === 'ajaxGet') {
            return $this->getValue('get', true, $key, $type);
        }
        else if ($method === 'post') {
            return $this->getValue('post', false, $key, $type);
        }
        else if ($method === 'ajaxPost') {
            return $this->getValue('post', true, $key, $type);
        }
        else {
            Tht::error("Unknown method '$method' for key '$key'");
        }
    }

    function getValue ($method, $isAjax, $key, $type, $max=0) {
        if (!$this->checkAjax($isAjax)) {
            return '';
        }
        $raw = Tht::getPhpGlobal($method, $key, '');
        return $this->sanitizeParam($key, $raw, $type, $max);
    }

    ***/


    function u_validate_js($formId="defaultForm") {

        $JSON_FIELD_RULES = json_encode($this->getRuleMap($formId));
        $JSON_RULE_PATTERNS = json_encode(u_Web::$VALIDATION_RULES);
        $CSRF_TOKEN = self::u_csrf_token();
        $FORM_ID = $formId;

        // TODO: validate/sanitize formId

        $formJs = "FormValidator.registerForm('$FORM_ID', $JSON_FIELD_RULES);\n";

        // About 2kb gzipped
        $validatorJs = $this->includedValidatorJs ? '' : <<<EOJS

            (function(){

                addFormStyles();
                listen();


                function listen() {
                    document.addEventListener('submit', function(e){ 
                        e.preventDefault(); 
                        FormValidator.submitFormIfOk(e.target);
                        return false; 
                    });
                }

                function addFormStyles() {
                    
                    var css = '@keyframes submit-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(359deg); } }\\n';
                    css += '.form-spinner { display: inline-block; vertical-align: text-top; height: 1.2em; width: 1.2em; border-radius: 50%; border: solid 0.2em rgba(0,0,0,0.2); border-right-color: rgba(0,0,0,0.4); animation: submit-spin 600ms linear 0s infinite }\\n';
                    css += '.button-primary .form-spinner { border-color: rgba(255,255,255,0.5); border-right-color: rgba(255,255,255,0.8); }\\n';
                    css += 'form .form-field-invalid, form .form-field-invalid:focus { outline: 2px solid #e33; }\\n';
                    css += '.form-error-message { margin-top: 1em; color: #e33; }\\n';

                    var style = document.createElement('style');
                    style.innerHTML = css;
                    document.getElementsByTagName('head')[0].appendChild(style);
                }

                window.FormValidator = (function() {

                    var STATE = {};
                    var FORMS = {};

                    var RULE_PATTERNS = $JSON_RULE_PATTERNS;
                    var CSRF_TOKEN = '$CSRF_TOKEN';

                    return {

                        registerForm: function(formId, fieldRules) {
                            if (!FORMS[formId]) {
                                FORMS[formId] = fieldRules;
                            } 
                        },

                        submitFormIfOk: function(form) {
                            if (STATE.isSubmitting) {
                                return false;
                            }

                            if (this.validateForm(form)) {
                                this.submitForm(form);
                            }
                        },

                        validateForm: function(form) {

                            var formId = form.id;
                            var fields = form.querySelectorAll('*[name]');
                            for (var i=0; i < fields.length; i++) {
                                var field = fields[i];
                                if (!this.validateField(form, formId, field)) {
                                    return false;
                                }
                            }

                            return true;
                        },

                        submitForm: function(form) {
                            STATE.isSubmitting = true;
                            var hasSpinner = this.addSpinner(form);
                            this.setCsrfToken(form);

                            setTimeout(function(){
                                form.submit();
                            }, hasSpinner ? 600 : 1);
                        },

                        setCsrfToken: function(form) {
                            var csrfField = document.createElement('input'); 
                            csrfField.type = 'hidden';
                            csrfField.name = 'csrfToken';
                            csrfField.value = CSRF_TOKEN;
                            form.appendChild(csrfField);
                        },

                        validateRequiredField: function(elField){
                            if (elField.type == 'checkbox') {
                                if (!elField.checked) {
                                    return false;
                                }
                            }
                            else if (elField.value.trim() == '') {
                                return false;
                            }
                            return true;
                        },

                        validateFieldRule: function(elField, rule) {

                            var value = elField.value.trim();

                            if (RULE_PATTERNS[rule] && value !== '') {
                                var re = new RegExp(RULE_PATTERNS[rule]);
                                if (!value.match(re)) {
                                    return false;
                                }
                            }
                            else if (rule.indexOf(':') !== -1) {
                                var pair = rule.split(':');
                                if (pair[0] == 'min' && value.length < 0 + pair[1]) {
                                    return false;
                                } 
                                else if (pair[0] == 'max' && value.length > 0 + pair[1]) {
                                    return false;
                                } 
                            }
                            return true;
                        },

                        validateField: function(form, formId, elField) {

                            if (elField.type == 'hidden') {
                                return true;
                            }

                            var isOk = true;
                            var isRequired = true;
                            var errorMessage = '';

                            var fieldRules = FORMS[formId][elField.name];

                            var rules = fieldRules.split(/\\s*\\|\\s*/);
                            for (var i=0; i < rules.length; i++) {
                                var rule = rules[i];
                                if (rule === 'optional') {
                                    isRequired = false;
                                }
                                else if (!this.validateFieldRule(elField, rule)) {
                                    isOk = false;
                                }
                            }

                            if (elField.type == 'password') {
                                if (!this.passwordStrengthOk(elField.value)) {
                                    errorMessage = 'Please use a stronger password.';
                                    isOk = false;
                                }
                                if (!this.validateFieldRule(elField, 'min:8')) {
                                    errorMessage = 'Please use a longer password. (8+ letters)';
                                    isOk = false;
                                }
                            }

                            if (isRequired && !this.validateRequiredField(elField)) {
                                errorMessage = 'Please fill the required field.'; 
                                isOk = false;
                            }

                            if (!isOk) {

                                if (!elField.instantValidate) {
                                    elField.instantValidate = true;
                                    var self = this;
                                    elField.addEventListener('keyup', function(){
                                        self.validateField(form, formId, elField);
                                    });
                                    elField.addEventListener('change', function(){
                                        self.validateField(form, formId, elField);
                                    });
                                }
                                elField.focus();
                            }

                            elField.classList.toggle('form-field-invalid', !isOk);
                            if (!isOk && !errorMessage) { errorMessage = 'Please check the highlighted field.'; }
                            form.querySelector('.form-error-message').innerText = errorMessage;

                            return isOk;
                        },

                        passwordStrengthOk: function(v) {

                            v = v.trim();

                            // all same character
                            if (v.match(/^(.)\\1{1,}$/)) {
                                return false;
                            }
                            // all digits
                            if (v.match(/^\d+$/)) {
                                return false;
                            }
                            // most common patterns
                            if (v.match(/^(abcd|abc1|qwer|asdf|1qaz|passw|admin|login|welcome|access)/i)) {
                                return false;
                            }
                            // most common passwords
                            if (v.match(/^(football|baseball|princess|starwars|trustno1|superman|iloveyou)$/i)) {
                                return false;
                            }
                            
                            return true;
                        },

                        addSpinner: function(form) {
                            var submit = form.querySelector('*[type="submit"]');
                            if (!submit) { return false; }
                                    
                            var rect = submit.getBoundingClientRect();
                            submit.innerHTML = '';
                            submit.style.width = (Math.floor((rect.right - rect.left) / 2) * 2) + 'px';

                            var spinner = document.createElement('div');
                            spinner.classList.add('form-spinner');
                            submit.appendChild(spinner);

                            return true;
                        }
                    };
                })();

            })();

EOJS;

        $this->includedValidatorJs = true;

        return new JsLockString($validatorJs . $formJs);

    }

}



