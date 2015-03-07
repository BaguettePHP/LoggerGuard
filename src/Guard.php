<?php
namespace Teto\Logger;
use Monolog;
use Psr\Log;

/**
 * Guard object of Logger for debugging
 *
 * @package   Baguette\Application
 * @author    USAMI Kenta <tadsan@zonu.me>
 * @copyright 2015 USAMI Kenta
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 */
final class Guard implements Log\LoggerInterface
{
    use Log\LoggerTrait;

    /** @var \Monolog\Logger */
    protected $monolog;
    /** @var Monolog\Handler\TestHandler */
    protected $test_handler;
    /** @var int */
    protected $visible_level;

    /**
     * @param Log\LoggerInterface $logger
     * @param string $visible_level
     */
    public function __construct(Log\LoggerInterface $logger = null, $visible_level = 'WARNING')
    {
        if ($logger instanceof Monolog\Logger) {
            $this->monolog = $logger;
        } else {
            $this->monolog = new Monolog\Logger('guard');
            if ($logger) {
                $this->monolog->pushHandler(new Monolog\Handler\PsrHandler($logger));
            }
        }

        $test_handler = self::getTestHandler();
        if (!$test_handler) {
            $test_handler = new Monolog\Handler\TestHandler;
            $this->monolog->pushHandler($test_handler);
        }

        $this->test_handler = $test_handler;
        $this->setVisibleLevel($visible_level);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param  mixed  $level
     * @param  string $message
     * @param  array  $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        return $this->monolog->log($level, $message, $context);
    }

    /**
     * @return false|Monolog\Handler\TestHandler
     */
    public function getTestHandler()
    {
        foreach ($this->monolog->getHandlers() as $h) {
            if ($h instanceof Monolog\Handler\TestHandler) {
                return $h;
            }
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getFormatter()
    {
        $content_type = 'text/html';

        foreach (headers_list() as $h) {
            if (!preg_match('@^Content-type: *([^;]+)@i', $h, $matches)) {
                continue;
            }

            $content_type = strtolower($matches[1]);
        }

        if (strpos($content_type, 'html') !== false) { return 'HtmlFormatter'; }
        if (strpos($content_type, 'text') !== false) { return 'LineFormatter'; }

        return false;
    }

    public function setVisibleLevel($visible_level)
    {
        $levels = $this->monolog->getLevels();
        $normalize_table = [
            'WARN'  => 'WARNING',
            'ERR'   => 'ERROR',
            'CRIT'  => 'CRITICAL',
            'EMERG' => 'EMERGENCY',
        ];

        $level = strtoupper($visible_level);
        $level = isset($normalize_table[$level]) ? $normalize_table[$level] : $level;
        $level = isset($levels[$level]) ? $level : 'WARNING';
        $this->visible_level = $levels[$level];
    }

    public function __destruct()
    {
        $name = $this->getFormatter();
        if ($name === false) { return; }

        $formatter_name = "\\Monolog\\Formatter\\$name";
        /** @var \Monolog\Formatter\FormatterInterface $formatter */
        $formatter = new $formatter_name;
        foreach ($this->test_handler->getRecords() as $record) {
            if ($this->visible_level <= $record['level']) {
                echo $formatter->format($record);
            }
        }
    }
}
