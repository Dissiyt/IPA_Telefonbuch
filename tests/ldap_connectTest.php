<?php
use PHPUnit\Framework\TestCase;
require_once 'ldap_connect.php';
require_once 'ldap_config.php';

// This interface represents your LDAP interactions (Important for decoupling)
interface LdapConnectorInterface {
    public function connect($server, $port);
    public function bind($connection, $user, $password);
}
class ldap_connectTest extends TestCase
{
    private $ldapConnectorMock;

    protected function setUp(): void
    {
        $this->ldapConnectorMock = $this->createMock(LdapConnectorInterface::class);
    }

    public function testSuccessfulAuthentication()
    {
        // Configure the mock
        $this->ldapConnectorMock->expects($this->once())->method('connect')->willReturn(true); // Simulate successful connection
        $this->ldapConnectorMock->expects($this->once())->method('bind')->willReturn(true); // Simulate successful bind

        // You'll need a class (or logic) that uses the LdapConnectorInterface
        $ldapHandler = new LdapHandler($this->ldapConnectorMock);

        $result = $ldapHandler->authenticate('username', 'password');
        $this->assertTrue($result);
    }
    public function testFailedAuthentication()
    {
        // Configure the mock
        $this->ldapConnectorMock->expects($this->once())->method('connect')->willReturn(true);
        $this->ldapConnectorMock->expects($this->once())->method('bind')->willReturn(false);

        // Instantiate your authenticator (injecting the mock)
        $ldapHandler = new LdapHandler($this->ldapConnectorMock);

        $result = $ldapHandler->authenticate('username', 'password');
        $this->assertFalse($result);
    }
    public function testConnectionSuccess()
    {
        $this->ldapConnectorMock->expects($this->once())->method('connect')->willReturn(true);

        // Instantiate LdapHandler with the mock
        $ldapHandler = new LdapHandler($this->ldapConnectorMock);

        $ldapHandler->authenticate('username', 'password');
        $this->assertTrue(true);
    }
    public function testConnectionFailure()
    {
        $this->ldapConnectorMock->expects($this->once())->method('connect')->willReturn(false);
        $ldapHandler = new LdapHandler($this->ldapConnectorMock);

        $this->expectException(LdapConnectionException::class);
        $ldapHandler->authenticate('username', 'password');
    }

}


class LdapHandler {
    private $ldapConnector;

    public function __construct(LdapConnectorInterface $ldapConnector) {
        $this->ldapConnector = $ldapConnector;
    }

    public function authenticate($username, $password) {
        global $ldapServer, $port;
        $ldapConn = $this->ldapConnector->connect($ldapServer, $port); // Use the interface
        if (!$ldapConn) {
            throw new LdapConnectionException("Failed to connect to LDAP server");
        }
        return $this->ldapConnector->bind($ldapConn, $username, $password);

    }
}
class LdapConnectionException extends Exception {}