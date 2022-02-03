<?php
namespace Framework\Exceptional;

/**
 * Standard faults
 *
 * this is a full list of the standard faults
 * @package Framework\Exceptional\Faults
 * @version 1.0.0
 */
abstract class FrameworkFault extends BaseException{}

class FatalHandlerFault extends FrameworkFault{}
class StandardFault     extends FrameworkFault{}


/** CORE FRAMEWORK OBJECT FAULTS */
class AutoloadFault     extends FrameworkFault{}
class ContourFault      extends FrameworkFault{}
class AdmitFault        extends FrameworkFault{}
class ModulusFault      extends FrameworkFault{}
class AdventFault       extends FrameworkFault{}
class AdventPageFault   extends FrameworkFault{}
class DocketFault       extends FrameworkFault{}
