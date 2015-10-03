<?php

if (!defined('CONFIG_FILE')) {
    define('CONFIG_FILE', 'config.json');
}

if (!defined('DATA_DIR')) {
    define('DATA_DIR', 'data/');
}

if (!defined('AB_EXECUTABLE')) {
    define('AB_EXECUTABLE', 'ab');
}


function main() {

    if (!file_exists(CONFIG_FILE)) {
        exit(1);
    }

    if ($conf = file_get_contents(CONFIG_FILE)) {
        printf("INFO: Loading configuration from %s\n", CONFIG_FILE);

        $config = json_decode($conf);
        if ($config === null) {
            printf("ERROR: Invalid configuration format in %s\n", CONFIG_FILE);
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

        $output_filename = DATA_DIR.'ab-run-' . date('YmdHis'). ".dat";
        echo "INFO: Storing benchmark results in $output_filename\n";

        $results = $abr->runBench();
        $results->save($output_filename);

        $indexFile = DATA_DIR . '../index.php';
        if (!file_exists($indexFile)) {
            copy(__DIR__ . '/index.php', $indexFile);
        }

        /*
        $ret_obj = new stdClass();
        $ret_obj->config = $abr->getConfig();
        $ret_obj->results = array();

        foreach ($results as $r) {
            $ret_obj->results[] = $r->getResults();
        }

        $ret_obj->zipped_results = zip_associative_arrays($ret_obj->results);
        file_put_contents($output_filename, json_encode($ret_obj));
        */

    } catch(Exception $e) {
        echo "ERROR: " . $e->getMessage()."\n";
        exit(1);
    }
}


main();
