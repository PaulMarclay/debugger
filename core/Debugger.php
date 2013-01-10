<?php
    /*
    *   DEBUGGER version 1.0
    *
    *   Imagina - Plugin.
    *
    *
    *   Copyright (c) 2012 Dolem Labs
    *
    *   Authors:    Paul Marclay (paul.eduardo.marclay@gmail.com)
    *
    */

    class Debugger extends Ancestor {
        public static function trace($xRays = false, $returnTrace = false, $die = false) {
    		// echo '<hr>';
    		// echo '<p>Memory Get Usage: '.Conversor::convert(memory_get_usage()).'</p>';
    		// echo '<p>Memory Get Peak Usage: '.Conversor::convert(memory_get_peak_usage()).'</p>';
    		
    		// $tr 		= array_reverse(debug_backtrace(false));
    		// $postData 	= '';
    		// foreach ($tr as $call) {
    		// 	echo '<ul><li><p>'.$call['file'].'#'.$call['function'].' ('.$call['line'].')</p>';
    		// 	$postData .= '</li></ul>';
    		// }
    		// echo $postData;

            // --
            // these are our templates
            $traceline = "#%s %s(%s): %s(%s)";
            $msg = "Debugger::trace():  Result \n%s";

            // alter your trace as you please, here
            $trace = debug_backtrace($xRays);
            $trace = array_reverse($trace);

            $showParameters = $xRays;
            if (!$showParameters) {
                foreach ($trace as $key => $stackPoint) {
                    // I'm converting arguments to their type
                    // (prevents passwords from ever getting logged as anything other than 'string')
                    $trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
                }
            }
            
            // build your tracelines
            $result = array();
            $cnt = 0;
            foreach ($trace as $key => $stackPoint) {
                $ret = '';
                $cnt++;
                if ($showParameters) {
                    $keys = array_keys($stackPoint['args']);
                    $end = end($keys);
                    foreach($stackPoint['args'] as $key => $item) {
                        if (is_array($item)) {
                            $ret .= 'array(';
                            $ret .= implode(',', $item);//print_r(implode(',',$item));die;
                            $ret .= ')';
                        } elseif (is_object($item)) {
                            $ret .= gettype($item);
                        } elseif (is_numeric($item)) {
                            $ret .= $item;
                        } else {
                            $ret .= "\"$item\"";
                        }
                        if ($key != $end) {
                            $ret .= ',';
                        }
                    }
                }

                $file = ((isset($stackPoint['file'])) ? $stackPoint['file'] : ' - ');
                $line = ((isset($stackPoint['line'])) ? $stackPoint['line'] : ' - ');
                $function = ((isset($stackPoint['function'])) ? $stackPoint['function'] : ' - ');

                $result[] = sprintf(
                    $traceline,
                    $cnt,
                    $file,
                    $line,
                    $function,
                    $ret
                );
            }
            // trace always ends with {main}
            // $result[] = '#' . ++$key . ' {main}';

            // $result = array_reverse($result);

            // write tracelines into main template
            $msg = sprintf(
                $msg,
                implode("\n", $result)
            );

            if ($returnTrace) {
                return '<pre>'.$msg.'</pre>';
            } else {
                echo '<pre>'.$msg.'</pre>';
            }
            
            
            // --
    		
            if ($die == true) die;
    	}

        public static function debug($data = null, $die = true) {
            echo '<h3>print_r():</h3><hr/><pre>';
            print_r($data);
            echo '</pre><hr/>';
            echo '<h3>var_dump():</h3><pre>';
            var_dump($data);
            echo '</pre><hr/>';
            echo '<h3>var_export():</h3><pre>';
            var_export($data);
            echo '</pre><hr/>';
            echo '<h3>htmlentities():</h3><pre>';
            echo htmlentities(var_dump($data));
            echo '</pre><hr/>';
            if (is_object($data)) {
                echo '<h3>Methods:</h3><pre>'; 
                print_r(get_class_methods(get_class($data)));
                echo '</pre><hr/>';
                echo '<h3>Vars:</h3><pre>'; 
                print_r(get_object_vars($data));
                echo '</pre><hr/>';
            }
            echo '<h3>Memory Info:</h3><p>Memory Get Usage: '.Conversor::convert(memory_get_usage()).'</p>';
    		echo '<p>Memory Get Peak Usage: '.Conversor::convert(memory_get_peak_usage()).'</p>';
            echo '<hr>';
            
    		if ($die) die;
        }


    	public static function getCallerClass() {
            $trace = debug_backtrace(false); // (false,2), para php > 5.4
            return $trace[2]['class'];
        }
    }