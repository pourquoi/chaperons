<?php

namespace App;

use AppBundle\Entity\Map;
use Psr\Log\LoggerInterface;

class MapRenderer
{
    private $node_path;
    private $phantom_path;
    private $app_host;
    private $app_port;

    /** @var LoggerInterface */
    private $logger;

    public function __construct($node_path, $phantom_path, $app_host, $app_port) {
        $this->node_path = $node_path;
        $this->phantom_path = $phantom_path;
        $this->app_host = $app_host;
        $this->app_port = $app_port;
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * Execute the phantomjs render script.
     *
     * @param Map $map
     */
    public function render(Map $map) {
        $user = $map->getUser();
        $url = 'http://' . $this->app_host . ($this->app_port != 80 ? (':' . $this->app_port) : '') . '/maps/' . $map->getId() . '/render';

        $script_path = __DIR__ . '/render.phantom.js';

        $capture_dir = __DIR__ . '/../../web/maps';
        if(!is_dir($capture_dir)) mkdir($capture_dir);

        $capture_filename = 'map_' . uniqid() . '.png';

        $width = $map->getWidth() ?: 740;
        $height = $map->getHeight() ?: 430;

        $cmd = implode(' ', [
                $this->node_path,
                $this->phantom_path,
                $script_path,
                escapeshellarg($url),
                $user->getId(),
                escapeshellarg($user->getApiKey()),
                $width,
                $height,
                $capture_dir.'/'.$capture_filename
            ]);

        if($this->logger) $this->logger->debug('[MapRenderer] ' . $cmd);

        $descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("file", "/dev/null", "a")
        );

        $cwd = '/tmp';
        $env = array();

        $process = proc_open($cmd, $descriptorspec, $pipes, $cwd, $env);

        if (is_resource($process)) {

            fclose($pipes[0]);

            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $exec_code = proc_close($process);

            if($this->logger) $this->logger->debug('[MapRenderer] ' . $exec_code . "\n" . $output);

            if($exec_code!=0) throw new \Exception($output, $exec_code);
        }


        if( $map->getCaptureFilename() ) @unlink($capture_dir.'/'.$map->getCaptureFilename());
        $map->setCaptureFilename($capture_filename);

    }
}