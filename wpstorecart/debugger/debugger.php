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

?>