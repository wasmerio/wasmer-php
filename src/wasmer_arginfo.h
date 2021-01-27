/* This is a generated file, edit the .stub.php file instead.
 * Stub hash: 47a5461059347dee7fac2e09a251580ece7c2150 */

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmer_version, 0, 0, IS_STRING,
                                        0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmer_version_major, 0, 0,
                                        IS_LONG, 0)
ZEND_END_ARG_INFO()

#define arginfo_wasmer_version_minor arginfo_wasmer_version_major

#define arginfo_wasmer_version_patch arginfo_wasmer_version_major

#define arginfo_wasmer_version_pre arginfo_wasmer_version

ZEND_FUNCTION(wasmer_version);
ZEND_FUNCTION(wasmer_version_major);
ZEND_FUNCTION(wasmer_version_minor);
ZEND_FUNCTION(wasmer_version_patch);
ZEND_FUNCTION(wasmer_version_pre);

static const zend_function_entry ext_functions[] = {
    ZEND_FE(wasmer_version, arginfo_wasmer_version)
        ZEND_FE(wasmer_version_major, arginfo_wasmer_version_major)
            ZEND_FE(wasmer_version_minor, arginfo_wasmer_version_minor)
                ZEND_FE(wasmer_version_patch, arginfo_wasmer_version_patch)
                    ZEND_FE(wasmer_version_pre, arginfo_wasmer_version_pre)
                        ZEND_FE_END};
