<?php
/**
 * Returns application name and version information.
 *
 * @since 1.0.0
 */
$version = '1.0.0-dev';
$applicationName = 'Lottery Generator';
if (file_exists(__DIR__ . '/../composer.json')) {
    @$composer = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true);
    $version = $composer['version'] ?? $version;
}
define('VERSION', $version);

return array(
    'name' => $applicationName,
    'version' => VERSION,
);