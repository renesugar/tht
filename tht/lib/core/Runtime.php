<?php

namespace o;


//// Global internal utility functions


// PHP to THT type
function v ($v) {

    $phpType = gettype($v);
    if ($phpType === 'object') {
       if ($v instanceof \Closure) {
           $phpType = 'Closure';
       } else if ($v instanceof \ONothing) {
           $v->error();
       } else {
            return $v;
       }
    }
    else if ($phpType === 'NULL') {
        Tht::error("Leaked `null` value found in transpiled PHP.");
    }

    $o = Runtime::$SINGLE[$phpType];
    $o->val = $v;

    return $o;
}

// THT to PHP type
// TODO: recursive unwrap for list and map values
function uv ($v) {
    return OMap::isa($v) || OList::isa($v) ? $v->val : $v;
}

// Assert Number type value
function vn ($v, $isAdd) {
    if (!is_numeric($v)) {
        $tag = $isAdd ? "Did you mean '~'?" : '';
        Tht::error("Can't use math on non-number value. $tag");
    }
    return $v;
}

// Convert camelCase to user_underscore_case (with u_ prefix)
function u_ ($s) {
    $out = preg_replace('/([^_])([A-Z])/', '$1_$2', $s);
    return 'u_' . strtolower($out);
}

// user_underscore_case back to camelCase (without u_ prefix)
function unu_ ($s) {
    $s = preg_replace('/^u_/', '', $s);
    return v($s)->u_to_camel_case();
}

// var has a u_ prefix
function hasu_ ($v) {
    return substr($v, 0, 2) === 'u_';
}



// Validate function arguments

// sig:
//   n = number
//   s = string
//   f = flag
//   l = list
//.  m = map
//   * = any

// NOTE: Fewer args are already handled by PHP, but we could possibly trap it here too.
// PERF: ~ 0.3 ms - not significant
function ARGS($sig, $arguments) {

    $err = '';
    
    if (count($arguments) > strlen($sig)) {
        $err = 'expects ' . strlen($sig) . ' arguments.  Got ' . count($arguments) . ' instead.';
    } 
    else {
        $i = 0;
        foreach ($arguments as $arg) {
            
            $s = $sig[$i];

            if ($s === '*') { continue; }
            if (is_null($arg)) { continue; }

            $t = gettype($arg);

            if ($t === 'integer' || $t === 'double' || $t === 'float') {
                $t = 'number';  
                if ($s === 's') {
                    // allow numbers to be cast as strings
                    continue;
                }
            }
            else if ($t === 'boolean') {
                $t = 'flag';
            }
            else if ($t === 'object') {
                $varg = v($arg);
                if ($varg->u_is_map()) {
                    $t = 'map';
                }
                else if ($varg->u_is_list()) {
                    $t = 'list';
                }
            }
 
            // Type mismatch
            if ($t !== Runtime::$SIG_TYPE_KEY_TO_LABEL[$s]) {
                $name = gettype($arg);
                // if ($name == 'object') {
                //     $name = get_class($arg);
                // }
                $err = "expects argument $i to be type `" . Runtime::$SIG_TYPE_KEY_TO_LABEL[$s] . "`.  Got a `" . $name . "` instead.";
                break;
            }
            $i += 1;
        }
    }

    if ($err) {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[1]['function'];
        Tht::error("`$caller()`" . $err);
    }
}


class Runtime {

    static $TYPES = [ 'OList', 'OString', 'ONumber', 'OFlag', 'OFunction', 'ONothing' ];

    static $PHP_TO_TYPE = [
        'string'  => 'OString',
        'array'   => 'OList',
        'boolean' => 'OFlag',
        'null'    => 'ONothing',
        'double'  => 'ONumber',
        'integer' => 'ONumber',
        'Closure' => 'OFunction'
    ];

    static $MODE_TO_TEMPLATE = [
        '_default' => 'OTemplate',
        'html'     => 'TemplateHtml',
        'js'       => 'TemplateJs',
        'css'      => 'TemplateCss',
        'lite'     => 'TemplateLite',
        'jcon'     => 'TemplateJcon',
        'text'     => 'TemplateText'
    ];

    static $SIG_TYPE_KEY_TO_LABEL = [
        'n' => 'number',
        's' => 'string',
        'f' => 'flag',
        'l' => 'list',
        'm' => 'map'
    ];

    static $SINGLE = [];

    static private $templateLevel = 0;
    static $andStack = [];
    static $fileToNameSpace = [];
    static $moduleRegistry = [];

    static function _initSingletons () {
        foreach (Runtime::$PHP_TO_TYPE as $php => $tht) {
            $c = '\\o\\' . $tht;
            Runtime::$SINGLE[$php] = new $c ();
        }
    }

    static function openTemplate ($mode) {
        Runtime::$templateLevel += 1;
        $mode = strtolower($mode);
        if (!isset(Runtime::$MODE_TO_TEMPLATE[$mode])) {
            $mode = '_default';
        }
        $class = 'o\\' . Runtime::$MODE_TO_TEMPLATE[$mode];
        return new $class ();
    }

    static function closeTemplate () {
        Runtime::$templateLevel -= 1;
    }

    static function inTemplate () {
        return Runtime::$templateLevel > 0;
    }

    static function andPush ($result) {
        array_push(Runtime::$andStack, $result);
        return $result;
    }

    static function andPop () {
        return array_pop(Runtime::$andStack);
    }

    static function setNameSpace ($file, $nameSpace) {
        $relPath = Tht::getRelativePath('app', $file);
        Runtime::$fileToNameSpace[$relPath] = $nameSpace;
        Runtime::registerModule($nameSpace, $relPath);
    }

    static function getNameSpace ($file) {
        $relPath = Tht::getRelativePath('app', $file);
        return Runtime::$fileToNameSpace[$relPath];
    }

    static function isStdLib ($lib) {
        return LibModules::isa($lib);
    }

    static function concat ($a, $b) {
        $sa = OLockString::isa($a);
        $sb = OLockString::isa($b);
        if ($sa || $sb) {
            if (!($sa && $sb)) {
                Tht::error("Can't combine (~) a LockString with a non-LockString.");
            }
            $combined = OLockString::getUnlocked($a) . OLockString::getUnlocked($b);
            return OLockString::create(get_class($a), $combined);
        } else {
            return Runtime::concatVal($a) . Runtime::concatVal($b);
        }
    }

    static function concatVal ($v) {
        $t = gettype($v);
        if ($t === 'boolean') {
            return $v ? 'true' : 'false';
        } else if ($t === 'integer' || $t === 'double' || $t === 'string'){
            return '' . $v;
        } else if ($t === 'null') {
            return '';
        } else if ($v instanceof ONothing) {
            $v->error();
        } else {
            Tht::error("Can't combine (~) an array or object.");
        }
    }

    static function registerStdModule ($name, $obj=-1) {
        Runtime::$moduleRegistry[$name] = $obj;
    }

    static function registerModule ($ns, $path) {
        Runtime::$moduleRegistry[$path] = new OModule ($ns, $path);
    }

    static function loadModule ($localNs, $fullPath) {
        $relPath = Tht::getRelativePath('app', $fullPath);
        if (!isset(Runtime::$moduleRegistry[$relPath])) {
            Tht::error("Can't find module for `$relPath`", [ 'knownModules' => Runtime::$moduleRegistry ]);
        }
        $derivedAlias = basename($relPath, '.' . Tht::getExt());
        Runtime::$moduleRegistry[$localNs . '::' . $derivedAlias] = Runtime::$moduleRegistry[$relPath];

        return Runtime::$moduleRegistry[$relPath];
    }

    static function getModule ($localNs, $alias) {
        $key = $localNs . '::' . $alias;
        if (isset(Runtime::$moduleRegistry[$key])) {
            return Runtime::$moduleRegistry[$key];
        }
        else if (isset(Runtime::$moduleRegistry[$alias])) {
            if (Runtime::$moduleRegistry[$alias] === -1) {
                // lazy init
                $c = '\\o\\u_' . $alias;
                Runtime::$moduleRegistry[$alias] = new $c ();
            }
            return Runtime::$moduleRegistry[$alias];
        } else {
            return Runtime::loadUserModule($localNs, $alias);
        }
    }

    static function loadUserModule($localNs, $relPath) {
        $fullPath = Tht::path('modules', $relPath . '.' . Tht::getExt());
        Source::process($fullPath);
        return Runtime::loadModule($localNs, $fullPath);
    }

    static function void ($fnName) {
        return new ONothing ($fnName);
    }
}

