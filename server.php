<?php

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use function Swoole\Coroutine\{go,defer};


$server = new Server("localhost", 9502);
$server->set([
    'enable_static_handler' => true,
    'document_root' => __DIR__
]);

$server->on("start", function () {
    echo "Swoole http server is started at http://localhost:9502\n";
});

$server->on("request", function (Request $request, Response $response) {

    // Add CORS headers
    $response->header("Access-Control-Allow-Origin", "*");
    $response->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");
    $response->header("Access-Control-Allow-Headers", "Content-Type, Authorization, X-Requested-With");

    // Handle preflight requests
    if ($request->server['request_method'] == 'OPTIONS') {
        $response->status(204);
        return;
    }


    // ? path display file
    $pathDisplay = __DIR__."/src/display/";

    if ($request->server['request_uri'] == '/') {
        $html = file_get_contents($pathDisplay.'index.html');
        $response->header("Content-Type", "text/html");
        $response->end($html);
    }elseif($request->server['request_uri'] == '/benchmark'){
        $html = file_get_contents($pathDisplay.'benchmark.html');
        $response->header("Content-Type", "text/html");
        $response->end($html);
    }elseif($request->server['request_uri'] == '/xss'){
        $html = file_get_contents($pathDisplay.'xss.html');
        $response->header("Content-Type", "text/html");
        $response->end($html);
    }elseif($request->server['request_uri'] == '/csrf'){
        $html = file_get_contents($pathDisplay.'csrf.html');
        $response->header("Content-Type", "text/html");
        $response->end($html);
    }elseif($request->server['request_uri'] == '/ddos'){
        $html = file_get_contents($pathDisplay.'ddos.html');
        $response->header("Content-Type", "text/html");
        $response->end($html);
    }elseif ($request->server['request_uri'] == '/run-benchmark') {

        // output location
        $outputXel = 'xel_results.json';
        $outputExpress = 'express_results.json';

        // benchmark step 
        go(function() use ($outputExpress, $outputXel, $response){
            go(function() use ($outputXel) {
                $command1 = "wrk -t4 -c1000 -d15s -s wrk_script.lua http://localhost:9501/users | tee $outputXel";
                shell_exec($command1);
            });
            
            defer(function() use ($outputExpress) {
                $command2 = "wrk -t4 -c1000 -d15s -s wrk_script.lua http://localhost:4000/users | tee $outputExpress";
                shell_exec($command2);
            });

        });

        $result = [];

        // Process Xel results
        $xelOutput = file_get_contents($outputXel);
        if (preg_match('/(\{.*\})/s', $xelOutput, $matches)) {
            $jsonOutput = $matches[1];
            $xelResult = json_decode($jsonOutput, true);
            if ($xelResult !== null) {
                $result['xel'] = $xelResult;
            } else {
                $result['xel'] = ['error' => 'Error parsing Xel JSON output'];
            }
        } else {
            $result['xel'] = ['error' => 'No JSON data found in Xel output'];
        }

        // Process Express results
        $expressOutput = file_get_contents($outputExpress);
        if (preg_match('/(\{.*\})/s', $expressOutput, $matches)) {
            $jsonOutput = $matches[1];
            $expressResult = json_decode($jsonOutput, true);
            if ($expressResult !== null) {
                $result['express'] = $expressResult;
            } else {
                $result['express'] = ['error' => 'Error parsing Express JSON output'];
            }
        } else {
            $result['express'] = ['error' => 'No JSON data found in Express output'];
        }

        
        $response->header("Content-Type", "application/json");
        $response->end(json_encode($result));


        
    }elseif ($request->server['request_uri'] == '/run-ddos') {

        // output location
        $outputXel = 'xel_results_with_gemstone_ddos.json';
        $outputExpress = 'express_results_with_no_ddos.json';

        // benchmark step 
        go(function() use ($outputExpress, $outputXel, $response){
            go(function() use ($outputXel) {
                $command1 = "wrk -t1 -c1000 -d15s -s wrk_script.lua http://localhost:9501/users | tee $outputXel";
                shell_exec($command1);
            });
            
            defer(function() use ($outputExpress) {
                $command2 = "wrk -t1 -c1000 -d15s -s wrk_script.lua http://localhost:4000/users | tee $outputExpress";
                shell_exec($command2);
            });
        });

        $result = [];

        // Process Xel results
        $xelOutput = file_get_contents($outputXel);
        if (preg_match('/(\{.*\})/s', $xelOutput, $matches)) {
            $jsonOutput = $matches[1];
            $xelResult = json_decode($jsonOutput, true);
            if ($xelResult !== null) {
                $result['xel'] = $xelResult;
            } else {
                $result['xel'] = ['error' => 'Error parsing Xel JSON output'];
            }
        } else {
            $result['xel'] = ['error' => 'No JSON data found in Xel output'];
        }

        // Process Express results
        $expressOutput = file_get_contents($outputExpress);
        if (preg_match('/(\{.*\})/s', $expressOutput, $matches)) {
            $jsonOutput = $matches[1];
            $expressResult = json_decode($jsonOutput, true);
            if ($expressResult !== null) {
                $result['express'] = $expressResult;
            } else {
                $result['express'] = ['error' => 'Error parsing Express JSON output'];
            }
        } else {
            $result['express'] = ['error' => 'No JSON data found in Express output'];
        }

        
        $response->header("Content-Type", "application/json");
        $response->end(json_encode($result));


        
    } else {
        $response->status(404);
        $response->end("Not found");
    }
});

$server->start();