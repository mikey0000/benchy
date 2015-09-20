<?php

namespace Benchy;

class ApacheBenchRunner
{
    /**
     * @var object
     */
    private $config;

    private $defaultconfig;

    /**
     * @param array $config
     * @throws \Exception
     */
    public function __construct($config)
    {
        $this->defaultconfig = json_decode('
            {
                "url": "http://localhost/",
                "cookie": null,
                "basicauth": null,
                "comment": null,
                "tests": [
                         { "concurrency": 1,   "requests": 10   },
                         { "concurrency": 2,   "requests": 20   },
                         { "concurrency": 4,   "requests": 40   },
                         { "concurrency": 8,   "requests": 80   },
                         { "concurrency": 16,  "requests": 160  },
                         { "concurrency": 32,  "requests": 320  },
                         { "concurrency": 64,  "requests": 640  },
                         { "concurrency": 128, "requests": 1280 }
                ]
            }
        ');
        $this->config = (object) array_merge((array)$this->defaultconfig, (array) $config);
        $this->checkApacheBench();
    }

    private function checkApacheBench()
    {
        exec(AB_EXECUTABLE . " -V", $output, $return_var);
        if ($return_var != 0) {
            throw new \Exception("Could not execute ApacheBench! (used \"" .AB_EXECUTABLE. "\" as command)");
        }
    }

    /**
     * @param $concurrency
     * @param $requests
     * @return string
     */
    private function buildCommand($concurrency, $requests)
    {
        $s =  AB_EXECUTABLE;

        if (!is_null($this->config->cookie)) { $s .= " -C ".$this->config->cookie; }
        if (!is_null($this->config->basicauth)) { $s .= " -A ".$this->config->basicauth; }

        $s .= " -q";
        $s .= " -c ".$concurrency;
        $s .= " -n ".$requests;
        $s .= " ".$this->config->url;
        $s .= " 2>&1 | tee";

        echo "INFO: Running ".$s." ...\n";
        return $s;
    }

    /**
     * @return bool
     */
    public function hasComment()
    {
        return $this->config->comment !== null;
    }

    /**
     * @param string $c
     */
    public function setComment($c)
    {
        $this->config->comment = $c;
    }

    /**
     * @return object
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return ApacheBenchResultSet
     */
    public function runBench()
    {
        //$start = microtime(true);
        $result = new ApacheBenchResultSet($this->getConfig());

        foreach($this->config->tests as $test) {
            $concurrency = $test->concurrency;
            $requests = $test->requests;
            $command = $this->buildCommand($concurrency, $requests);

            ob_start();
            $output = shell_exec($command);
            ob_end_clean();

            $result[] = new ApacheBenchResult($output);
        }

        //$end = microtime(true);

        return $result;
    }
}