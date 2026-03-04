<?php

namespace Cavatappi\Infrastructure\Configuration;

/**
 * An object that provides a strongly-typed configuration for a Service (or really any other object).
 *
 * Using this interface will allow the configuration to be picked up by the ConfigurationRegistry so it can be
 * given to a service at runtime.
 */
interface Configuration {}
