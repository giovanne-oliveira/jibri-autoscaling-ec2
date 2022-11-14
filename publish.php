<?php

require_once('vendor/autoload.php');


use Aws\CloudWatch\CloudWatchClient; 
use Aws\Exception\AwsException;

define('METRIC_NAME', 'Idle_Instances');
define('METRIC_NAMESPACE', 'Jibri');
define('AWS_REGION', 'us-east-1');

define('IDLE_INSTANCES_TRESHOLD', 1);

function putMetricData($cloudWatchClient, $cloudWatchRegion, $namespace, 
    $metricData)
{
    try {
        $result = $cloudWatchClient->putMetricData([
            'Namespace' => $namespace,
            'MetricData' => $metricData
        ]);
        
        if (isset($result['@metadata']['effectiveUri']))
        {
            if ($result['@metadata']['effectiveUri'] == 
                'https://monitoring.' . $cloudWatchRegion . '.amazonaws.com')
            {
                return 'Successfully published datapoint(s).';
            } else {
                return 'Could not publish datapoint(s).';
            }
        } else {
            return 'Error: Could not publish datapoint(s).';
        }
    } catch (AwsException $e) {
        return 'Error: ' . $e->getAwsErrorMessage();
    }
}

function sendMetric($idle_instances)
{
    $metricData = [
        [
            'MetricName' => METRIC_NAME,
            'Timestamp' => time(), // 11 May 2020, 20:26:58 UTC.
            'Unit' => 'Count',
            'Value' => $idle_instances
        ]
    ];

    $cloudWatchClient = new CloudWatchClient([
        'profile' => 'default',
        'region' => AWS_REGION,
        'version' => 'latest'
    ]);

    echo putMetricData($cloudWatchClient, AWS_REGION, METRIC_NAMESPACE, 
        $metricData);
}

sendMetric(0);