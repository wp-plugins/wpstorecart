<?php

if(!function_exists('wpscBacktrace')) {
    function wpscBacktrace() {
        $backtracer = NULL;
        foreach(debug_backtrace() as $k=>$v){
            if($v['function'] == "include" || $v['function'] == "include_once" || $v['function'] == "require_once" || $v['function'] == "require"){
                $backtracer .= "#".$k." ".$v['function']."(".$v['args'][0].") called at [".$v['file'].":".$v['line']."]<br />";
            }else{
                $backtracer .= "#".$k." ".$v['function']."() called at [".$v['file'].":".$v['line']."]<br />";
            }
        }
        return $backtracer;
    }
}

if(!function_exists('wpscJSConsoleLog')) {
    function wpscJSConsoleLog($msg) {
        global $wpsc_testing_mode;
        if($wpsc_testing_mode) {
            return "if(console) { console.log('{$msg}'); };";
        } else {
            return null;
        }
    }
}

?>