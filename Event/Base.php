<?php
/**
 * Base for all events
 *
 * @package Framework\Event
 * @version 1.0.0
 */
namespace Framework\Event;

/**
 * Base
 *
 * This will be the event abstraction layer
 */
abstract class Base
{
  /** @var does this page require them to be logged in */
  public $requiresLogin   = false;

  /** @var the permission this system requires */
  public $permissionGroup = null;

  /** @var the permission this system requires */
  public $permission      = null;
}
