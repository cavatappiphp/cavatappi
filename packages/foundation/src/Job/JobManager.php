<?php

namespace Cavatappi\Foundation\Job;

use Cavatappi\Foundation\Job\Job;
use Cavatappi\Foundation\Service;

/**
 * Service to add the given Job to a queue and
 */
interface JobManager extends Service {
	/**
	 * Add the given Job to a queue to execute on a separate thread.
	 *
	 * @param Job $job Job to enqueue.
	 * @return void
	 */
	public function enqueue(Job $job): void;
}
