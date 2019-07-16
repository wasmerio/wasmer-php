/*
   +----------------------------------------------------------------------+
   | PHP Version 7                                                        |
   +----------------------------------------------------------------------+
   | Copyright (c) 1997-2019 The PHP Group                                |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Ivan Enderlin                                                |
   +----------------------------------------------------------------------+
*/

#ifndef PHP_WASM_H
# define PHP_WASM_H

extern zend_module_entry wasm_module_entry;
# define phpext_wasm_ptr &wasm_module_entry

# define PHP_WASM_VERSION "0.5.0"

# if defined(ZTS) && defined(COMPILE_DL_WASM)
ZEND_TSRMLS_CACHE_EXTERN()
# endif

#endif	/* PHP_WASM_H */

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */
