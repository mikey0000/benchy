<?php

define('CONFIG_FILE','config.json');
define('ALT_CONFIG_FILE','benchy.json');
define('DATA_DIR', 'data/');
define('AB_EXECUTABLE', 'ab');

function zip_associative_arrays(array $array_list) {
    if (count($array_list) == 0) {
        return array();
    }
    $keys = array_keys($array_list[0]);
    $ret = array();

    foreach ($keys as $k) {
        $ret[$k] = array();
    }
    
    foreach ($array_list as $a) {
        foreach ($keys as $k) {
            $ret[$k][] = $a[$k];
        }
    }
    
    return $ret;
}


function cast($value, $type) {
    if ($type == "int") {
        return intval($value);
    } else if ($type == "float") {
        return floatval($value);
    } else {
        return $value;
    }
}


function main() {

    $file = null;
    if (file_exists(ALT_CONFIG_FILE)) {
        $file = ALT_CONFIG_FILE;
    } elseif (file_exists(CONFIG_FILE)) {
        $file = CONFIG_FILE;
    }

    if ($conf = file_get_contents($file)) {
        printf("INFO: Loading configuration from %s\n", $file);

        $config = json_decode($conf);
        if ($config === null) {
            printf("ERROR: Invalid configuration format in %s\n", $file);
            exit(1);
        }
    } else {
        $config = null;
    }

    try {
        $abr = new \Benchy\ApacheBenchRunner($config);
        if (!$abr->hasComment()) {
            $abr->setComment(readline('Please enter a comment to describe this run: '));
        }
        
        $results = $abr->runBench();
       
        $output_filename = DATA_DIR.'ab-run-' . date('YmdHis'). ".dat";
        echo "INFO: Storing benchmark results in $output_filename\n";

        $ret_obj = new stdClass();
        $ret_obj->config = $abr->getConfig();
        $ret_obj->results = array();

        foreach ($results as $r) {
            $ret_obj->results[] = $r->getResults();
        }

        $ret_obj->zipped_results = zip_associative_arrays($ret_obj->results);
        file_put_contents($output_filename, json_encode($ret_obj));

    } catch(Exception $e) {
        echo "ERROR: " . $e->getMessage()."\n";
        exit(1);
    }
} 


main();