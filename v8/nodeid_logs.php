<?php

$targetNodeId = "FFFFFFFFFFFFFFFF7EC2000000033F00";
$logFile = '/var/www/html/access.log'; 


if (!file_exists($logFile)) {
    die("<h2>Error: access.log not found at $logFile</h2>");
}


$handle = fopen($logFile, 'r');
$matches = [];


while (($line = fgets($handle)) !== false) {
    
    if (strpos($line, $targetNodeId) !== false && isRecentLine($line, 30)) {
        $matches[] = htmlspecialchars($line); 
    }
}
fclose($handle);


echo "<h1>Log Entries for Node ID: $targetNodeId (Last 30 Days)</h1>";
if (!empty($matches)) {
    echo "<pre>" . implode("\n", $matches) . "</pre>";
} else {
    echo "<p>No matching entries found in the last 30 days.</p>";
}


function isRecentLine($line, $maxDays) {
    
    if (preg_match('/\[(\d{2}\/\w{3}\/\d{4}):/', $line, $dateMatch)) {
        $logDate = DateTime::createFromFormat('d/M/Y', $dateMatch[1]);
        $cutoffDate = (new DateTime())->sub(new DateInterval("P{$maxDays}D"));
        return ($logDate >= $cutoffDate);
    }
    return false;
}
?>