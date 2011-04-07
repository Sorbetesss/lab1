<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Profiler;

use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

/**
 * Base PDO storage for profiling information in a PDO database.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jan Schumann <js@schumann-it.com>
 */
abstract class PdoProfilerStorage implements ProfilerStorageInterface
{
    protected $dsn;
    protected $username;
    protected $password;
    protected $lifetime;
    protected $db;

    /**
     * Constructor.
     *
     * @param string  $dsn      A data source name
     * @param string  $username The username for the database
     * @param string  $password The password for the database
     * @param integer $lifetime The lifetime to use for the purge
     */
    public function __construct($dsn, $username = '', $password = '', $lifetime = 86400)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->lifetime = (int) $lifetime;
    }

    /**
     * {@inheritdoc}
     */
    public function find($ip, $url, $limit)
    {
        list($criteria, $args) = $this->buildCriteria($ip, $url, $limit);

        $criteria = $criteria ? 'WHERE '.implode(' AND ', $criteria) : '';

        $db = $this->initDb();
        $tokens = $this->fetch($db, 'SELECT token, ip, url, time, parent FROM sf_profiler_data '.$criteria.' ORDER BY time DESC LIMIT '.((integer) $limit), $args);
        $this->close($db);

        return $tokens;
    }

    /**
     * {@inheritdoc}
     */
     public function findChildren($token)
     {
         $db = $this->initDb();
         $args = array(':token' => $token);
         $tokens = $this->fetch($db, 'SELECT token FROM sf_profiler_data WHERE parent = :token LIMIT 1', $args);
         $this->close($db);

        return $tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function read($token)
    {
        $db = $this->initDb();
        $args = array(':token' => $token);
        $data = $this->fetch($db, 'SELECT data, parent, ip, url, time FROM sf_profiler_data WHERE token = :token LIMIT 1', $args);
        $this->close($db);
        if (isset($data[0]['data'])) {
            return array($data[0]['data'], $data[0]['parent'], $data[0]['ip'], $data[0]['url'], $data[0]['time']);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function write($token, $parent, $data, $ip, $url, $time)
    {
        $db = $this->initDb();
        $args = array(
            ':token'        => $token,
            ':parent'       => $parent,
            ':data'         => $data,
            ':ip'           => $ip,
            ':url'          => $url,
            ':time'         => $time,
            ':created_at'   => $_SERVER['REQUEST_TIME'],
        );
        try {
            $this->exec($db, 'INSERT INTO sf_profiler_data (token, parent, data, ip, url, time, created_at) VALUES (:token, :parent, :data, :ip, :url, :time, :created_at)', $args);
            $this->cleanup();
            $status = true;
        } catch (\Exception $e) {
            $status = false;
        }
        $this->close($db);

        return $status;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        $db = $this->initDb();
        $this->exec($db, 'DELETE FROM sf_profiler_data');
        $this->close($db);
    }

    /**
     * Build SQL criteria to fetch records by ip and url
     *
     * @param string $ip    The IP
     * @param string $url   The URL
     * @param string $limit The maximum number of tokens to return
     *
     * @return array An array with (creteria, args)
     */
    abstract protected function buildCriteria($ip, $url, $limit);

    /**
     * Initializes the database
     *
     * @throws \RuntimeException When the requeted database driver is not installed
     */
    abstract protected function initDb();

    protected function cleanup()
    {
        $db = $this->initDb();
        $this->exec($db, 'DELETE FROM sf_profiler_data WHERE created_at < :time', array(':time' => $_SERVER['REQUEST_TIME'] - $this->lifetime));
        $this->close($db);
    }

    protected function exec($db, $query, array $args = array())
    {
        $stmt = $this->prepareStatement($db, $query);

        foreach ($args as $arg => $val) {
            $stmt->bindValue($arg, $val, is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $success = $stmt->execute();
        if (!$success) {
            throw new \RuntimeException(sprintf('Error executing query "%s"', $query));
        }
    }

    protected function prepareStatement($db, $query)
    {
        try {
            $stmt = $db->prepare($query);
        } catch (\Exception $e) {
            $stmt = false;
        }

        if (false === $stmt) {
            throw new \RuntimeException('The database cannot successfully prepare the statement');
        }

        return $stmt;
    }

    protected function fetch($db, $query, array $args = array())
    {
        $return = array();
        $stmt = $this->prepareStatement($db, $query);

        foreach ($args as $arg => $val) {
            $stmt->bindValue($arg, $val, is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->execute();
        $return = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $return;
    }

    protected function close($db)
    {
    }
}
