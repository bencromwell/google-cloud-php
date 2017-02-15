<?php
/**
 * Copyright 2016 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Cloud\Spanner\Session;

use Google\Cloud\Exception\NotFoundException;
use Google\Cloud\Spanner\Connection\ConnectionInterface;
use Google\Cloud\Spanner\V1\SpannerClient;

/**
 * Represents and manages a single Cloud Spanner session.
 *
 * Example:
 * ```
 * use Google\Cloud\ServiceBuilder;
 *
 * $cloud = new ServiceBuilder();
 * $spanner = $cloud->spanner();
 *
 * $sessionClient = $spanner->sessionClient();
 * $session = $sessionClient->create('test-instance', 'test-database');
 * ```
 */
class Session
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var string
     */
    private $projectId;

    /**
     * @var string
     */
    private $instance;

    /**
     * @var string
     */
    private $database;

    /**
     * @var string
     */
    private $name;

    /**
     * @param ConnectionInterface $connection A connection to Cloud Spanner.
     * @param string $projectId The project ID.
     * @param string $instance The instance name.
     * @param string $database The database name.
     * @param string $name The session name.
     */
    public function __construct(ConnectionInterface $connection, $projectId, $instance, $database, $name)
    {
        $this->connection = $connection;
        $this->projectId = $projectId;
        $this->instance = $instance;
        $this->database = $database;
        $this->name = $name;
    }

    /**
     * Return info on the session
     *
     * Example:
     * ```
     * print_r($session->info());
     * ```
     *
     * @return array An array containing the `projectId`, `instance`, `database` and session `name` keys.
     */
    public function info()
    {
        return [
            'projectId' => $this->projectId,
            'instance' => $this->instance,
            'database' => $this->database,
            'name' => $this->name
        ];
    }

    /**
     * Check if the session exists.
     *
     * Example:
     * ```
     * if ($session->exists()) {
     *     echo 'The session is valid!';
     * }
     * ```
     *
     * @param array $options [optional] Configuration options.
     * @return array
     */
    public function exists(array $options = [])
    {
        try {
            $this->connection->getSession($options + [
                'name' => $this->name()
            ]);

            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }

    /**
     * Delete the session.
     *
     * Example:
     * ```
     * $session->delete();
     * ```
     *
     * @param array $options [optional] Configuration options.
     * @return void
     */
    public function delete(array $options = [])
    {
        return $this->connection->deleteSession($options + [
            'name' => $this->name()
        ]);
    }

    /**
     * Format the constituent parts of a session name into a fully qualified session name.
     *
     * @return string
     */
    public function name()
    {
        return SpannerClient::formatSessionName(
            $this->projectId,
            $this->instance,
            $this->database,
            $this->name
        );
    }
}