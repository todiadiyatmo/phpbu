<?php
namespace phpbu\App\Cli\Executable;

use phpbu\App\Cli\Cmd;
use phpbu\App\Cli\Executable;
use phpbu\App\Cli\Process;
use phpbu\App\Exception;

/**
 * Innobackupex Executable class.
 *
 * @package    phpbu
 * @subpackage Backup
 * @author     Francis Chuang <francis.chuang@gmail.com>
 * @author     Sebastian Feldmann <sebastian@phpbu.de>
 * @copyright  Sebastian Feldmann <sebastian@phpbu.de>
 * @license    https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link       http://phpbu.de/
 * @since      Class available since Release 2.1.0
 */
class Innobackupex extends Abstraction implements Executable
{
    /**
     * MySQL data directory
     *
     * @var string
     */
    private $dataDir;

    /**
     * Dump directory
     *
     * @var string
     */
    private $dumpDir;

    /**
     * Host to connect to
     * --host <hostname>
     *
     * @var string
     */
    private $host;

    /**
     * User to connect with
     * --user <username>
     *
     * @var string
     */
    private $user;

    /**
     * Password to authenticate with
     * --password <password>
     *
     * @var string
     */
    private $password;

    /**
     * Regular expression matching the tables to be backed up.
     * The regex should match the full qualified name: myDatabase.myTable
     * --tables string
     *
     * @var string
     */
    private $include;

    /**
     * List of databases and/or tables to backup
     * Tables must e fully qualified: myDatabase.myTable
     * --databases array of strings
     *
     * @var array
     */
    private $databases;

    /**
     * Constructor.
     *
     * @param string $path
     */
    public function __construct($path = null)
    {
        $this->cmd = 'innobackupex';
        parent::__construct($path);
    }

    /**
     * Set MySQL data dir.
     *
     * @param  string $path
     * @return \phpbu\App\Cli\Executable\Innobackupex
     */
    public function dumpFrom($path)
    {
        $this->dataDir = $path;
        return $this;
    }

    /**
     * Set target dump dir.
     *
     * @param  string $path
     * @return \phpbu\App\Cli\Executable\Innobackupex
     */
    public function dumpTo($path)
    {
        $this->dumpDir = $path;
        return $this;
    }

    /**
     * Set host du connect to.
     *
     * @param  string $host
     * @return \phpbu\App\Cli\Executable\Innobackupex
     */
    public function useHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Set mysql credentials.
     *
     * @param  string $user
     * @param  string $password
     * @return \phpbu\App\Cli\Executable\Innobackupex
     */
    public function credentials($user = null, $password = null)
    {
        $this->user     = $user;
        $this->password = $password;
        return $this;
    }

    /**
     * Set include option
     *
     * @param  string $include
     * @return \phpbu\App\Cli\Executable\Innobackupex
     */
    public function including($include)
    {
        $this->include = $include;
        return $this;
    }

    /**
     * Set databases to dump.
     *
     * @param  array $databases
     * @return \phpbu\App\Cli\Executable\Innobackupex
     */
    public function dumpDatabases(array $databases)
    {
        $this->databases = $databases;
        return $this;
    }

    /**
     * Subclass Process generator.
     *
     * @return \phpbu\App\Cli\Process
     * @throws \phpbu\App\Exception
     */
    public function createProcess()
    {
        if (empty($this->dumpDir)) {
            throw new Exception('no directory to dump to');
        }
        $process  = new Process();
        $cmdDump  = new Cmd($this->binary);
        $cmdApply = new Cmd($this->binary);
        $process->addCommand($cmdDump);
        $process->addCommand($cmdApply);

        // no std error unless it is activated
        if (!$this->showStdErr) {
            $cmdDump->silence();
            $cmdApply->silence();
            // i kill you
        }

        $cmdDump->addOption('--no-timestamp');
        $cmdDump->addOptionIfNotEmpty('--datadir', $this->dataDir);
        $cmdDump->addOptionIfNotEmpty('--user', $this->user);
        $cmdDump->addOptionIfNotEmpty('--password', $this->password);
        $cmdDump->addOptionIfNotEmpty('--host', $this->host);

        if (!empty($this->include)) {
            $cmdDump->addOption('--include', $this->include);
        } else if (count($this->databases)) {
            $cmdDump->addOption('--databases', implode(' ', $this->databases));
        }

        $cmdDump->addArgument($this->dumpDir);

        $cmdApply->addOption('--apply-log');
        $cmdApply->addArgument($this->dumpDir);

        return $process;
    }
}
