<?php

global $wpstorecart_benchmark, $benchmark_start, $benchmark_end, $benchmark_total;

/* If benchmarking is on, start the benchmark */
if($wpstorecart_benchmark){
	$benchmark_start = microtime(true);
}

/**
 * Starts the benchmarking
 */
if(!function_exists('wpscBenchmark')) {
	function wpscBenchmark() {
		global $benchmark_total, $wpstorecart_version;
		echo '
<!-- wpStoreCart '.$wpstorecart_version.' '.__('Benchmark','wpstorecart').': '.$benchmark_total.' -->
';
	}
}


/**
* Finalize any open benchmarks
*/
if(!function_exists('wpscBenchmarkEnd')) {
	function wpscBenchmarkEnd() {
		global $wpstorecart_benchmark;
		if($wpstorecart_benchmark){
			global $benchmark_total, $benchmark_start, $benchmark_end;
			$benchmark_end = microtime(true);
			$benchmark_total = number_format($benchmark_end - $benchmark_start, 4);
		
			add_action('wp_footer','wpscBenchmark',9999); // Put the benchmark in the footer
		}
	}
}

add_action('wpsc_end', 'wpscBenchmarkEnd', 1); // Applies our benchmark ending function to our wpsc_end action hook    

?>