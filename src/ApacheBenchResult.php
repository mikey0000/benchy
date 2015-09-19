<?php

namespace Benchy;

class ApacheBenchResult {
    private $results;

    public function __construct($cmd_output) {
        $expressions = array(
            "server_software"       => "/Server Software:        (.*)/",
            "server_hostname"       => "/Server Hostname:        (.*)/",
            "server_port"           => "/Server Port:            (.*)/",
            "document_path"         => "/Document Path:          (.*)/",
            "document_length"       => "/Document Length:        (.*) bytes/",
            "concurrency_level"     => "/Concurrency Level:      (.*)/",
            "time_taken_for_tests"  => "/Time taken for tests:   (.*) seconds/",
            "complete_requests"     => "/Complete requests:      (.*)/",
            "failed_requests"       => "/Failed requests:        (.*)/",
            "write_errors"          => "/Write errors:           (.*)/",
            "total_transferred"     => "/Total transferred:      (.*) bytes/",
            "html_transferred"      => "/HTML transferred:       (.*) bytes/",
            "requests_per_second"   => "/Requests per second:    (.*) \[#\/sec] \(mean\)/",
            "time_per_request"      => "/Time per request:       (.*) \[ms\] \(mean\)/",
            "time_per_request_conc" => "/Time per request:       (.*) \[ms\] \(mean, across all concurrent requests\)/",
            "transfer_rate"         => "/Transfer rate:          (.*) \[Kbytes\/sec\] received/"
        );

        $types = array(
            "server_software"       => "string",
            "server_hostname"       => "string",
            "server_port"           => "string",
            "document_path"         => "string",
            "document_length"       => "int",
            "concurrency_level"     => "int",
            "time_taken_for_tests"  => "float",
            "complete_requests"     => "int",
            "failed_requests"       => "int",
            "write_errors"          => "int",
            "total_transferred"     => "int",
            "html_transferred"      => "int",
            "requests_per_second"   => "float",
            "time_per_request"      => "float",
            "time_per_request_conc" => "float",
            "transfer_rate"         => "float"
        );

        foreach($expressions as $key => $regex) {
            preg_match_all($regex, $cmd_output, $matches);
            if (isset($matches[1][0])) {
                $this->results[$key] = cast($matches[1][0], $types[$key]);
            }
        }
    }

    public function getResults() {
        return $this->results;
    }
}