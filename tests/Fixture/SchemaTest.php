<?php

// @codeCoverageIgnoreStart

$testcases = [];
foreach (glob(__DIR__.'/SchemaTest_*.php') as $testcaseFile) {
    $filename = basename($testcaseFile, '.php');
    $caseLabel = str_replace('Schematest_', '', $filename);
    $testcases[$caseLabel] = require $testcaseFile;
}

return $testcases;

// @codeCoverageIgnoreEnd