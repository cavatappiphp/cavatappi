<?php

namespace Cavatappi\Infrastructure\Test\Serialization;

use Cavatappi\Foundation\Registry\Registerable;
use Cavatappi\Foundation\Value;

interface Supertype extends Value, Registerable {
	public string $superValue { get; }
}