<?php

namespace o;

// TODO
// bind/call/methods/getClass
// bind = via Closure class
// equivalent of __filename, __dirname?
class u_Meta extends StdModule {

    function getCallerNamespace () {
        $trace = debug_backtrace(0, 2);
        $nameSpace = Runtime::getNameSpace(Owl::getOwlPathForPhp($trace[1]['file']));
        return $nameSpace;
    }

    function u_function_exists ($fn) {
        $fullFn = $this->getCallerNamespace() . '\\' . u_($fn);
        return function_exists($fullFn);
    }

    function u_call_function ($fn, $params=[]) {
        $fullFn = $this->getCallerNamespace() . '\\' . u_($fn);
        if (! function_exists($fullFn)) {
            Owl::error('function does not exist: ' . $fullFn);
        }
        return call_user_func_array($fullFn, uv($params));
    }

    function u_parse ($source) {
        return Source::safeParseString($source);
    }

    function u_arguments () {
        $trace = debug_backtrace(0, 2);
        $args = $trace[1]['args'];
        return $args;
    }

    // TODO: filter and clean -- sourcemap
    // function u_stack_trace ($ignoreArgs=false) {
    //     $arg = $ignoreArgs ? DEBUG_BACKTRACE_IGNORE_ARGS : 0;
    //     return debug_backtrace($arg);
    // }

    // function u_function_caller () {
    //     return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
    // }

    // function u_is_command_mode () {
    //     return Owl::isMode('cli');
    // }
    //
    // function u_is_web_mode () {
    //     return Owl::isMode('web');
    // }
    //


    function u_no_template_mode () {
        if (Runtime::inTemplate()) {
            $this->callerError('can not be called in template mode.');
        }
        return true;
    }

    function u_no_web_mode () {
        if (!Owl::isMode('cli')) {
            $this->callerError('can not be called in web mode.');
        }
    }
    //
    // function u_no_command_mode () {
    //     if (Owl::isMode('cli')) {
    //         $this->callerError('can not be called in command line mode.');
    //     }
    // }

    function u_owl_version() {
        return Owl::getOwlVersion();
    }

    function callerError ($msg) {
        $frames = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $callerFrame = false;
        foreach ($frames as $f) {
            if (substr($f['function'], 0, 2) == 'u_') {
                if ($f['class'] !== 'o\\u_Meta') {
                    $callerFrame = $f;
                    break;
                }
            }
        }
        if (!$callerFrame) {
            $callerFrame = $frames[2];
        }

        $caller = $callerFrame['function'];
        $class = $callerFrame['class'];
        Owl::error("$class.$caller() " . $msg);
    }
}
