<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Taskrouter\V1\Workspace\Worker;

use Twilio\Exceptions\TwilioException;
use Twilio\InstanceContext;
use Twilio\Options;
use Twilio\Values;
use Twilio\Version;

class WorkersRealTimeStatisticsContext extends InstanceContext {
    /**
     * Initialize the WorkersRealTimeStatisticsContext
     *
     * @param Version $version Version that contains the resource
     * @param string $workspaceSid The SID of the Workspace with the resource to
     *                             fetch
     */
    public function __construct(Version $version, $workspaceSid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = ['workspaceSid' => $workspaceSid, ];

        $this->uri = '/Workspaces/' . \rawurlencode($workspaceSid) . '/Workers/RealTimeStatistics';
    }

    /**
     * Fetch the WorkersRealTimeStatisticsInstance
     *
     * @param array|Options $options Optional Arguments
     * @return WorkersRealTimeStatisticsInstance Fetched
     *                                           WorkersRealTimeStatisticsInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch(array $options = []): WorkersRealTimeStatisticsInstance {
        $options = new Values($options);

        $params = Values::of(['TaskChannel' => $options['taskChannel'], ]);

        $payload = $this->version->fetch('GET', $this->uri, $params);

        return new WorkersRealTimeStatisticsInstance(
            $this->version,
            $payload,
            $this->solution['workspaceSid']
        );
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        $context = [];
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Taskrouter.V1.WorkersRealTimeStatisticsContext ' . \implode(' ', $context) . ']';
    }
}