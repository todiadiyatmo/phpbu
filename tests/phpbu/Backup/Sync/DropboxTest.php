<?php
namespace phpbu\App\Backup\Sync;

/**
 * DropboxTest
 *
 * @package    phpbu
 * @subpackage tests
 * @author     Sebastian Feldmann <sebastian@phpbu.de>
 * @copyright  Sebastian Feldmann <sebastian@phpbu.de>
 * @license    https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link       http://www.phpbu.de/
 * @since      Class available since Release 1.1.5
 */
class DropboxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Dropbox::setUp
     */
    public function testSetUpOk()
    {
        $dropbox = new Dropbox();
        $dropbox->setup(array(
            'token' => 'this-is-no-token',
            'path'  => '/'
        ));

        $this->assertTrue(true, 'no exception should occur');
    }

    /**
     * Tests Dropbox::setUp
     *
     * @expectedException \phpbu\App\Backup\Sync\Exception
     */
    public function testSetUpNoToken()
    {
        $dropbox = new Dropbox();
        $dropbox->setup(array('path' => '/'));
    }

    /**
     * Tests Dropbox::setUp
     *
     * @expectedException \phpbu\App\Backup\Sync\Exception
     */
    public function testSetUpNoPath()
    {
        $dropbox = new Dropbox();
        $dropbox->setup(array('token' => 'this-is-no-token'));
    }
}
