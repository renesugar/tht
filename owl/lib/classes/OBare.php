<?php

namespace o;

// Functions without a module namespace
class OBare {

    static $FUNCTIONS = [ 'import', 'print', 'range', 'die', 'result' ];

    static function isa ($word) {
        return in_array($word, OBare::$FUNCTIONS);
    }

    static function formatPrint($parts) {

        $outs = [];
        foreach ($parts as $a) {
            if (is_object($a) || is_array($a)) {
                if ($a instanceof ONothing) {
                    $a = '(nothing)';
                } else if ($a instanceof OLockString) {
                    $a = $a->u_unlocked();
                } else if ($a instanceof ORegex) {
                    $a = $a->pattern;
                } else {
                    $a = Owl::module('Json')->u_format($a);
                }
            }
            if ($a === true)  { $a = 'true'; }
            if ($a === false) { $a = 'false'; }
            if (Owl::isMode('web')) {
                $a = htmlentities($a);
            }
            if ($a === '' || $a === null) { $a = '(nothing)'; }
            $outs []= $a;
        }

        return implode("\n", $outs);
    }

    static function u_print () {

        $out = OBare::formatPrint(func_get_args());

        if (Owl::isMode('web')) {
            Owl::queueWebPrint($out);
        }
        else {
            echo $out, "\n";
        }

        return Runtime::void('print');
    }

    static function u_import ($localNs, $relPath) {
        $fullPath = Owl::path('modules', $relPath . '.' . Owl::getExt());
        Source::process($fullPath);

        $relPath = Owl::getRelativePath('root', $fullPath);
        Runtime::loadModule($localNs, $relPath);
    }

    static function u_range ($start, $end, $step=1) {
        return range($start, $end, $step);
    }

    // TODO: Iterator Version
    // static function u_range ($start, $end, $step=1) {
    //         if ($step <= 0) {
    //             Owl::error('Step argument ' . $step . ' must be positive');
    //         }
    //     if ($start < $end) {
    //         for ($i = $start; $i <= $end; $i += $step) {
    //             yield $i;
    //         }
    //     } else {
    //         for ($i = $start; $i >= $end; $i -= $step) {
    //             yield $i;
    //         }
    //     }
    // }

    static function u_die ($msg, $data=null) {
        Owl::error($msg, $data);
    }

    static function u_result ($v=null) {
        return new Result($v);
    }
}

