<?php 
class R66_Test {
  
  private $_test_group = '';
  private $_test_results = array();
  private $_skipped = array();
  
  public static function run_tests() {
    $class_name = get_called_class();
    $test = new $class_name;
    echo $test->run();
  }
  
  public function check($condition, $error_message=null) {
    $trace = debug_backtrace();
    $func = $trace[1]['function'];
    $line = $trace[0]['line'];
    $file = $trace[0]['file'];
    
    $spec = str_replace('test_', '', $func);
    $message = ucfirst(str_replace('_', ' ', $spec));
    if(!$condition && isset($error_message) && !empty($error_message)) $message .= ' :: ' . $error_message;
    $message = $condition ? 'Passed: ' . $message : 'Failed: ' . $message . "\n$file :: line #$line";
    
    $result = new stdClass();
    $result->passed = $condition;
    $result->message = $message;
    $this->_test_results[] = $result;
  }
  
  public function run() {
    $title = str_replace('_', ' ', get_class($this));
    $out = "\n==================================================\nBEGIN $title\n\n";
    
    // Look for setup hook to run before tests
    if(method_exists($this, 'before_tests')) {
      $this->before_tests();
    }
    
    $methods = get_class_methods($this);
    foreach($methods as $name) {
      $prefix = 'test';
      $length = strlen($prefix);
      if(substr($name, 0, $length) == $prefix) {
        $this->$name();
      }
      else {
        $prefix = '_test';
        $length = strlen($prefix);
        if(substr($name, 0, $length) == $prefix) {
          $name = str_replace('_test_', '', $name);
          $this->_skipped[] = 'Skipped: ' . ucfirst(str_replace('_', ' ', ltrim($name, '_')));
        }
      }
    }
    
    // Look for clean up hook to run after tests
    if(method_exists($this, 'after_tests')) {
      $this->after_tests();
    }
    
    $out .= $this->results();
    $out .= "\nEND $title\n==================================================\n\n\n\n\n\n\n\n\n\n\n\n\n";
    return $out;
  }
  
  public function results() {
    $passed = 0;
    $failed = 0;
    $passed_messages = '';
    $failed_messages = '';
    $skipped_messages = '';
    $summary = array();
    
    foreach($this->_test_results as $result) {
      if($result->passed) {
        $passed++;
        $passed_messages .= $result->message . "\n\n";
      }
      else {
        $failed++;
        $failed_messages .= $result->message . "\n\n";
      }
    }
    
    $out = '';
    
    if($passed > 0) {
      $summary[] = "Passed: $passed";
    }
    if($failed > 0) {
      $summary[] = "Failed: $failed";
    }
    if(count($this->_skipped)) {
      $summary[] = 'Skipped: ' . count($this->_skipped);
      $skipped_messages = implode("\n", $this->_skipped) . "\n";
    }
    
    $summary = implode(' -- ', $summary);
    $out = $skipped_messages . "\n" . $passed_messages . "\n" . $failed_messages . $summary . "\n\n";
    
    return $out;
  }
  
  public function log_file_check() {
    if(defined('R66_LOG_FILE')) {
      echo "\nCheck log for details:\n" . R66_LOG_FILE . "\n\n";
    }
    else {
      echo "\nSet up a log file to collect additional details.\nDefine the constant R66_LOG_FILE with the path to your log file.";
    }
  }
  
  
}