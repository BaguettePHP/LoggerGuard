<?php
/**
 * Guard object of Logger for debugging
 *
 * @package   Teto\Logger
 * @author    USAMI Kenta <tadsan@zonu.me>
 * @copyright 2015 USAMI Kenta
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
namespace Teto\Logger\Test;
use Monolog;
use Teto;
require __DIR__ . '/../../vendor/autoload.php';

$content_type = isset($_GET['content_type']) ? $_GET['content_type'] : 'text/html';
header("Content-type: $content_type");

$level = isset($_GET['level']) ? $_GET['level'] : 'WARNING';
$logger = new Monolog\Logger('');
$logger->pushHandler(new Monolog\Handler\TestHandler);

$guard = new Teto\Logger\Guard($logger, $level);

$context = [
    'SERVER' => $_SERVER,
    'FILE'   => __FILE__,
];

$logger->debug('Debug!', $context + ['LINE' => __LINE__]);
$logger->info('Info!', $context + ['LINE' => __LINE__]);
$logger->notice('Notice!', $context + ['LINE' => __LINE__]);
$logger->warn('Warning!', $context + ['LINE' => __LINE__]);
$logger->err('Error!', $context + ['LINE' => __LINE__]);
$logger->crit('Critical!', $context + ['LINE' => __LINE__]);
$logger->emerg('Emergency!', $context + ['LINE' => __LINE__]);

?>
<h1>Logger Guard test</h1>
