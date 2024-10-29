<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Preview\BulkExports\Export;

use Twilio\Exceptions\TwilioException;
use Twilio\InstanceContext;
use Twilio\Values;
use Twilio\Version;

/**
 * PLEASE NOTE that this class contains preview products that are subject to change. Use them with caution. If you currently do not have developer preview access, please contact help@twilio.com.
 */
class DayContext extends InstanceContext {
    /**
     * Initialize the DayContext
     *
     * @param Version $version Version that contains the resource
     * @param string $resourceType The type of communication – Messages, Calls
     * @param string $day The date of the data in the file
     */
    public function __construct(Version $version, $resourceType, $day) {
        parent::__construct($version);

        // Path Solution
        $this->solution = ['resourceType' => $resourceType, 'day' => $day, ];

        $this->uri = '/Exports/' . \rawurlencode($resourceType) . '/Days/' . \rawurlencode($day) . '';
    }

    /**
     * Fetch the DayInstance
     *
     * @return DayInstance Fetched DayInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch(): DayInstance {
        $payload = $this->version->fetch('GET', $this->uri);

        return new DayInstance(
            $this->version,
            $payload,
            $this->solution['resourceType'],
            $this->solution['day']
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
        return '[Twilio.Preview.BulkExports.DayContext ' . \implode(' ', $context) . ']';
    }
}