<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Studio\V1\Flow\Execution\ExecutionStep;

use Twilio\ListResource;
use Twilio\Version;

class ExecutionStepContextList extends ListResource {
    /**
     * Construct the ExecutionStepContextList
     *
     * @param Version $version Version that contains the resource
     * @param string $flowSid The SID of the Flow
     * @param string $executionSid The SID of the Execution
     * @param string $stepSid Step SID
     */
    public function __construct(Version $version, string $flowSid, string $executionSid, string $stepSid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = ['flowSid' => $flowSid, 'executionSid' => $executionSid, 'stepSid' => $stepSid, ];
    }

    /**
     * Constructs a ExecutionStepContextContext
     */
    public function getContext(): ExecutionStepContextContext {
        return new ExecutionStepContextContext(
            $this->version,
            $this->solution['flowSid'],
            $this->solution['executionSid'],
            $this->solution['stepSid']
        );
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        return '[Twilio.Studio.V1.ExecutionStepContextList]';
    }
}