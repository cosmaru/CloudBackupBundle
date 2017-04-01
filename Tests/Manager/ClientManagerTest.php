<?php
namespace Dizda\CloudBackupBundle\Tests\Manager;

use Dizda\CloudBackupBundle\Client\ClientInterface;
use Dizda\CloudBackupBundle\Client\DownloadableClientInterface;
use Dizda\CloudBackupBundle\Manager\ClientManager;
use Psr\Log\LoggerInterface;

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
class ClientManagerTest extends \PHPUnit\Framework\TestCase
{
	public function newGetMock($class){
		if(!class_exists('\PHPUnit\Framework\TestCase')){
			$this->getMock($class);
		}else{
			$this->getMockBuilder($class);
		}
	}
    /**
     * @test
     */
    public function shouldExecuteDownloadForFirstDownloadableClient()
    {
        $clients = [];
        $clients[] = $this->newGetMock(ClientInterface::class);
        $clientMock = $this->newGetMock(DownloadableClientInterface::class);
        $fileMock = $this
            ->getMockBuilder(\SplFileInfo::class)
            ->setConstructorArgs([tempnam(sys_get_temp_dir(), '')])
            ->getMock();
        $clientMock->expects($this->once())->method('download')->willReturn($fileMock);
        $clients[] = $clientMock;

        $clientManager = new ClientManager($this->newGetMock(LoggerInterface::class), $clients);
        $this->assertSame($fileMock, $clientManager->download());
    }

    /**
     * @test
     * @expectedException \Dizda\CloudBackupBundle\Exception\MissingDownloadableClientsException
     * @expectedExceptionMessage No downloadable client is registered.
     */
    public function shouldThrowExceptionIfNoChildIsADownloadableClient()
    {
        $clients = [];
        $clients[] = $this->newGetMock(ClientInterface::class);
        $clients[] = $this->newGetMock(ClientInterface::class);

        $clientManager = new ClientManager($this->newGetMock(LoggerInterface::class), $clients);
        $clientManager->download();
    }
}
