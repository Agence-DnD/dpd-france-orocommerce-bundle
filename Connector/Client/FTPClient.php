<?php

declare(strict_types=1);

namespace Dnd\Bundle\DpdFranceShippingBundle\Connector\Client;

use phpseclib\Net\SFTP;
use Psr\Log\InvalidArgumentException;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class FTPClient
 *
 * @package   Dnd\Bundle\DpdFranceShippingBundle\Connector\Client
 * @author    Agence Dn'D <contact@dnd.fr>
 * @copyright 2004-present Agence Dn'D
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://www.dnd.fr/
 */
class FTPClient
{
    /**
     * Description $ftpConnection field
     *
     * @var SFTP|null $ftpConnection
     */
    protected ?SFTP $ftpConnection = null;
    /**
     * Description $settings field
     *
     * @var ParameterBag|null $settings
     */
    protected ?ParameterBag $settings = null;

    /**
     * FTPClient constructor
     *
     * @param ParameterBag $settings
     */
    public function __construct(
        ParameterBag $settings
    ) {
        $this->settings = $settings;
    }

    /**
     * Description getFtpConnection function
     *
     * @return SFTP
     * @throws \RuntimeException
     */
    private function getFtpConnection(): SFTP
    {
        if (null === $this->ftpConnection) {
            [$host, $port, $username, $password] = $this->getSettings();

            $this->ftpConnection = new SFTP($host, $port);
            if (!$this->ftpConnection->login($username, $password)) {
                throw new \RuntimeException('Login failed');
            }
        }

        return $this->ftpConnection;
    }

    /**
     * Description getSettings function
     *
     * @return mixed[]
     */
    private function getSettings(): array
    {
        /** @var string|null $host */
        $host = $this->settings->get('dpd_fr_station_ftp_host');
        /** @var int|null $host */
        $port = $this->settings->getInt('dpd_fr_station_ftp_port');
        /** @var string|null $host */
        $username = $this->settings->get('dpd_fr_station_ftp_user');
        /** @var string|null $host */
        $password = $this->settings->get('dpd_fr_station_ftp_password');
        if (!isset($host, $port, $username, $password)) {
            throw new InvalidArgumentException('At least one parameters are missing for FTP connection.');
        }

        return [$host, $port, $username, $password];
    }

    /**
     * Description getFile function
     *
     * @param string $distantPath
     * @param string $localPath
     *
     * @return string
     */
    public function getFile(string $distantPath, string $localPath): string
    {
        $this->getFtpConnection()->get($distantPath, $localPath);

        return $localPath;
    }

    /**
     * Description putFile function
     *
     * @param string $distantPath
     * @param string $localPath
     *
     * @return bool
     */
    public function putFile(string $distantPath, string $localPath): bool
    {
        return $this->getFtpConnection()->put($distantPath, $localPath, SFTP::SOURCE_LOCAL_FILE);
    }
}
