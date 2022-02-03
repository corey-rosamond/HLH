<?php
namespace Framework\Exceptional;

/**
 * Standard Exceptions
 *
 * this is a full list of the standard exceptions
 * @package Framework\Exceptional\Exceptions
 * @version 1.0.0
 */
abstract class FrameworkException     extends BaseException{}
/** Framework exceptions **/
class UndefinedIndexException         extends FrameworkException{}
class UndefinedClassException         extends FrameworkException{}
class UndefinedVariableException      extends FrameworkException{}
class UndefinedConstantException      extends FrameworkException{}
class UndefinedMethodException        extends FrameworkException{}
class UndefinedStaticMethodException  extends FrameworkException{}
class FactoryException                extends FrameworkException{}
class ModelException                  extends FrameworkException{}
class SystemException                 extends FrameworkException{}
class BadTypeExcetpion                extends FrameworkException{}
class WarningException                extends FrameworkException{}
class ParseException                  extends FrameworkException{}
class NoticeException                 extends FrameworkException{}
class CoreErrorException              extends FrameworkException{}
class CoreWarningException            extends FrameworkException{}
class CompileErrorException           extends FrameworkException{}
class CompileWarningException         extends FrameworkException{}
class UserErrorException              extends FrameworkException{}
class UserWarningException            extends FrameworkException{}
class UserNoticeException             extends FrameworkException{}
class StrictException                 extends FrameworkException{}
class RecoverableErrorException       extends FrameworkException{}
class DeprecatedException             extends FrameworkException{}
class UserDeprecatedException         extends FrameworkException{}
class IncludeOnceException            extends FrameworkException{}
class MissingArgumentException        extends FrameworkException{}
class ArrayToStringConversion         extends FrameworkException{}
class PropertyOfNoneObjectException   extends FrameworkException{}
class PDOdatabasesException           extends FrameworkException{}
class AdventException                 extends FrameworkException{}
class AdventPageException             extends FrameworkException{}
class DocketException                 extends FrameworkException{}
class OrderControllerException        extends FrameworkException{}
class TrackingControllerException     extends FrameworkException{}
