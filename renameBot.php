<?php

/**
 * This script can be used to rename the bot in a database dump,
 * so you don't have to re-setup everything
 *
 * PHP Version 8.0 or 8.1
 *
 * @category Tool
 * @package  RenameBot
 * @author   Nadyita RK5 <nadyita@hodorraid.org>
 * @license  GPL3 https://raw.githubusercontent.com/Nadybot/DBTools/main/LICENSE
 * @link     https://github.com/Nadybot/DBTools/wiki
 */

declare(strict_types=1);

if ($argc !== 4) {
    echo("Syntax: {$argv[0]} <sql file> <from character> <to character>\n");
    echo("\n");
    echo("Renames the bot character in a database dump and prints the\n");
    echo("changed dump to stdout. Redirect to a file or directly into your\n");
    echo("importer.\n");
    exit(1);
}

$fileName = $argv[1];
$from = strtolower($argv[2]);
$fromUf = ucfirst($from);
$to = strtolower($argv[3]);
$toUf = ucfirst($to);

if (!@file_exists($fileName)) {
    echo("{$fileName} doesn't exist.\n");
    exit(1);
}

$file = fopen($fileName, "rb");
if ($file === false) {
    echo("Unable to open {$fileName}.");
    exit(1);
}

while (!feof($file)) {
    $line = @fgets($file);
    if ($line === false) {
        continue;
    }
    if (preg_match("/^insert into [\"`]?players[\"`]? /i", $line)) {
        continue;
    }
    $convertedLine = preg_replace(
        "/(\b|_)\Q$from\E(\b|_)/",
        '$1' . $to . '$2',
        $line
    );
    if (!isset($convertedLine)) {
        $convertedLine = $line;
    }
    $convertedLine = preg_replace(
        "/(\b|_)\Q$fromUf\E(\b|_)/",
        '$1' . $toUf . '$2',
        $convertedLine
    );
    if (!isset($convertedLine)) {
        $convertedLine = $line;
    }
    echo($convertedLine);
}
@fclose($file);