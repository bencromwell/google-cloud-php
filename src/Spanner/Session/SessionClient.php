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

use Google\Cloud\Spanner\Connection\ConnectionInterface;
use Google\Cloud\Spanner\V1\SpannerClient;

/**
 * Manage API interactions related to Spanner database sessions.
 *
 * In general, sessions are handled by the Spanner client internally. Direct
 * management of sessions is discouraged except in special cases where granular
 * control is most desirable.
 *
 * Example:
 * ```
 * use Google\Cloud\ServiceBuilder;
 *
 * $cloud = new ServiceBuilder();
 * $spanner = $cloud->spanner();
 *
 * $sessionClient = $spanner->sessionClient();
 */
class SessionClient
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
     * Create a new Session Client.
     *
     * @param ConnectionInterface $connection A connection to the Cloud Spanner API
     * @param string $projectId The current project ID
     */
    public function __construct(ConnectionInterface $connection, $projectId)
    {
        $this->connection = $connection;
        $this->projectId = $projectId;
    }

    /**
     * Create a new session in the given instance and database.
     *
     * Example:
     * ```
     * $sessionName = $sessionClient->create('test-instance', 'test-database');
     * ```
     *
     * @param string $instance The simple instance name.
     * @param string $database The simple database name.
     * @param array $options [optional] Configuration options.
     * @return Session|null If the operation succeeded, a Session object will be returned,
     *        otherwise null.
     */
    public function create($instance, $database, array $options = [])
    {
        $res = $this->connection->createSession($options + [
            'database' => SpannerClient::formatDatabaseName($this->projectId, $instance, $database)
        ]);

        $session = null;
        if (isset($res['name'])) {
            $session = new Session(
                $this->connection,
                $this->projectId,
                SpannerClient::parseInstanceFromSessionName($res['name']),
                SpannerClient::parseDatabaseFromSessionName($res['name']),
                SpannerClient::parseSessionFromSessionName($res['name'])
            );
        }

        return $session;
    }

    public function __debugInfo()
    {
        return [
            'connection' => get_class($this->connection),
            'projectId' => $this->projectId
        ];
    }
}