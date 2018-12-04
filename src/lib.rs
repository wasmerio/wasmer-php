extern crate wasmi;

use wasmi::{
    Error,
    ExternVal,
    Externals,
    FuncInstance,
    FuncRef,
    ImportsBuilder,
    Module,
    ModuleImportResolver,
    ModuleInstance,
    ModuleRef,
    RuntimeArgs,
    RuntimeValue,
    Signature,
    Trap,
    TrapKind,
    nan_preserving_float,
};
use std::collections::HashMap;
use std::fmt;
use std::fs::File;
use std::io::{self, Read};
use std::os::raw::c_void;
use std::path::PathBuf;
use std::mem;

pub fn read_wasm_binary(path: &PathBuf) -> io::Result<Vec<u8>> {
    let mut file = File::open(path)?;
    let mut buffer = Vec::new();

    file.read_to_end(&mut buffer).unwrap();

    Ok(buffer)
}

#[derive(Debug)]
#[repr(C)]
pub enum Value {
    I32(i32),
    I64(i64),
    F32(f32),
    F64(f64),
    None,
}

impl From<RuntimeValue> for Value {
    fn from(runtime_value: RuntimeValue) -> Self {
        match runtime_value {
            RuntimeValue::I32(value) => Value::I32(value),
            RuntimeValue::I64(value) => Value::I64(value),
            RuntimeValue::F32(value) => Value::F32(value.to_float()),
            RuntimeValue::F64(value) => Value::F64(value.to_float()),
        }
    }
}

impl<'a> From<&'a Value> for Option<RuntimeValue> {
    fn from(value: &'a Value) -> Self {
        match *value {
            Value::I32(value) => Some(RuntimeValue::I32(value)),
            Value::I64(value) => Some(RuntimeValue::I64(value)),
            Value::F32(value) => Some(RuntimeValue::F32(nan_preserving_float::F32::from_float(value))),
            Value::F64(value) => Some(RuntimeValue::F64(nan_preserving_float::F64::from_float(value))),
            Value::None => None,
        }
    }
}

#[repr(C)]
pub struct FunctionInputs {
    inputs_buffer: *const Value,
    inputs_length: usize,
    inputs_capacity: usize,
}

impl<'a> From<&'a [wasmi::RuntimeValue]> for FunctionInputs {
    fn from(runtime_values: &'a [wasmi::RuntimeValue]) -> Self {
        let values: Vec<Value> = runtime_values
            .iter()
            .map(|&runtime_value| runtime_value.into())
            .collect();

        let function_inputs = FunctionInputs {
            inputs_buffer: values.as_slice().as_ptr(),
            inputs_length: values.len(),
            inputs_capacity: values.capacity(),
        };

        mem::forget(values);

        function_inputs
    }
}

pub struct ImportedFunction {
    index: usize,
    name: String,
    signature: Signature,
    implementation_pointer: fn(*const c_void, *const c_void, *const FunctionInputs) -> *const Value,
    zend_fcall_info: *const c_void,
    zend_fcall_info_cache: *const c_void,
}

impl fmt::Debug for ImportedFunction {
    fn fmt(&self, formatter: &mut fmt::Formatter) -> fmt::Result {
        write!(formatter, "ImportedFunction {{ index: {}, name: {}, ... }}", self.index, self.name)
    }
}

#[derive(Debug)]
enum HostErrorCode {
    ImportedFunctionReturnedInvalidValue,
}

#[derive(Debug)]
struct HostError(HostErrorCode);

impl fmt::Display for HostError {
    fn fmt(&self, formatter: &mut fmt::Formatter) -> fmt::Result {
        write!(formatter, "HostError({:?})", self.0)
    }
}

impl wasmi::HostError for HostError { }

#[derive(Debug)]
pub struct Runtime {
    imported_functions: HashMap<usize, ImportedFunction>,
}

impl Runtime {
    pub fn new() -> Self {
        Runtime {
            imported_functions: HashMap::new()
        }
    }

    pub fn add_function(&mut self, imported_function: ImportedFunction) {
        self.imported_functions.insert(
            imported_function.index,
            imported_function
        );
    }
}

impl Externals for Runtime {
    fn invoke_index(
        &mut self,
        index: usize,
        arguments: RuntimeArgs,
    ) -> Result<Option<RuntimeValue>, Trap> {
        match self.imported_functions.get(&index) {
            Some(ImportedFunction { implementation_pointer, zend_fcall_info, zend_fcall_info_cache, .. }) => {
                let function_inputs = arguments.as_ref().into();
                let output = implementation_pointer(
                    *zend_fcall_info,
                    *zend_fcall_info_cache,
                    Box::into_raw(Box::new(function_inputs)) /* freed by PHP */
                );

                if output.is_null() {
                    return Err(Trap::new(TrapKind::Host(Box::new(HostError(HostErrorCode::ImportedFunctionReturnedInvalidValue)))));
                }

                Ok(unsafe { &*output }.into())
            },

            _ => panic!("Unimplemented imported function at `{}`.", index),
        }
    }
}

impl ModuleImportResolver for Runtime {
    fn resolve_func(
        &self,
        field_name: &str,
        given_signature: &Signature,
    ) -> Result<FuncRef, Error> {
        let imported_function =
            self
                .imported_functions
                .iter()
                .filter(|(_, value)| { value.name == field_name })
                .nth(0);

        if let Some((_, ImportedFunction { index, signature, .. })) = imported_function {
            if signature != given_signature {
                Err(Error::Instantiation(format!("Imported function `{}` exists but has an invalid signature.", field_name)))
            } else {
                Ok(FuncInstance::alloc_host((*signature).clone(), *index))
            }
        } else {
            Err(Error::Instantiation(format!("Imported function `{}` not found/not declared.", field_name)))
        }
    }
}

pub struct Instance {
    pub file_path: String,
    instance: ModuleRef,
}

impl Instance {
    pub fn new<'a>(
        path: &PathBuf,
        wasm_binary: Vec<u8>,
        wasm_runtime: &Runtime,
    ) -> Result<Self, Error> {
        let module = Module::from_buffer(&wasm_binary)?;

        let mut import_resolver = ImportsBuilder::new();
        import_resolver.push_resolver("env", wasm_runtime);

        Ok(
            Instance {
                file_path: path.to_string_lossy().into_owned(),
                instance: ModuleInstance::new(&module, &import_resolver)?.assert_no_start(),
            }
        )
    }

    pub fn get_function_signature(&self, function_name: &str) -> Option<Signature> {
        match self.instance.export_by_name(function_name) {
            Some(ExternVal::Func(function)) => Some(function.signature().clone()),
            _ => None
        }
    }

    pub fn invoke(
        &self,
        function_name: &str,
        arguments: &[RuntimeValue],
        wasm_runtime: &mut Runtime,
    ) -> Option<RuntimeValue> {
        self.instance
            .invoke_export(function_name, arguments, wasm_runtime)
            .expect(&format!("Failed to invoke the `{}` function with arguments `{:?}`", function_name, arguments))
    }
}

pub mod ffi {
    use super::*;
    use wasmi::{
        RuntimeValue,
        nan_preserving_float::{F32, F64},
    };
    use std::{
        ffi::CStr,
        mem,
        os::raw::{c_char, c_void},
        path::PathBuf,
        ptr,
        str,
    };

    macro_rules! check_and_deref {
        ($variable:ident) => {
            {
                assert!(!$variable.is_null());

                unsafe { &*$variable }
            }
        };
    }

    macro_rules! check_and_deref_mut {
        ($variable:ident) => {
            {
                assert!(!$variable.is_null());

                unsafe { &mut *$variable }
            }
        };
    }

    macro_rules! check_and_deref_to_box {
        ($variable:ident) => {
            {
                assert!(!$variable.is_null());

                unsafe { Box::from_raw(&mut *$variable) }
            }
        }
    }

    macro_rules! check_and_deref_to_pathbuf {
        ($variable:ident) => {
            {
                assert!(!$variable.is_null());

                PathBuf::from(
                    unsafe {
                        String::from_utf8_unchecked(
                            CStr::from_ptr($variable).to_bytes().to_vec()
                        )
                    }
                )
            }
        }
    }

    macro_rules! check_and_deref_to_str {
        ($variable:ident) => {
            {
                assert!(!$variable.is_null());

                unsafe { str::from_utf8_unchecked(CStr::from_ptr($variable).to_bytes()) }
            }
        };
    }

    #[no_mangle]
    pub extern "C" fn wasm_read_binary(file_path: *const c_char) -> *const Vec<u8> {
        Box::into_raw(
            Box::new(
                super::read_wasm_binary(&check_and_deref_to_pathbuf!(file_path)).unwrap_or_else(|_| vec![])
            )
        )
    }

    #[no_mangle]
    pub extern "C" fn drop_wasm_binary(wasm_binary: *mut Vec<u8>) {
        let _: Box<Vec<u8>> = check_and_deref_to_box!(wasm_binary);
    }

    #[no_mangle]
    pub extern "C" fn wasm_new_instance(
        file_path: *const c_char,
        wasm_binary: *const Vec<u8>,
        wasm_runtime: *const Runtime,
    ) -> *mut Instance {
        let file_path = check_and_deref_to_pathbuf!(file_path);
        let wasm_binary = check_and_deref!(wasm_binary).to_vec();
        let wasm_runtime = check_and_deref!(wasm_runtime);

        match super::Instance::new(&file_path, wasm_binary, &wasm_runtime) {
            Ok(wasm_instance) => Box::into_raw(Box::new(wasm_instance)),
            Err(_) => ptr::null_mut(),
        }
    }

    #[no_mangle]
    pub extern "C" fn drop_wasm_instance(wasm_instance: *mut Instance) {
        let _: Box<Instance> = check_and_deref_to_box!(wasm_instance);
    }

    #[repr(C)]
    pub struct Signature {
        inputs_buffer: *const ValueType,
        inputs_length: usize,
        inputs_capacity: usize,
        output: *const ValueType,
    }

    impl From<wasmi::Signature> for Signature {
        fn from(signature: wasmi::Signature) -> Self {
            let inputs: Vec<ValueType> = signature.params().iter()
                .map(|&input| input.into())
                .collect();

            let signature = Signature {
                inputs_buffer: inputs.as_slice().as_ptr(),
                inputs_length: inputs.len(),
                inputs_capacity: inputs.capacity(),
                output: Box::into_raw(
                    Box::new(
                        match signature.return_type() {
                            Some(output) => output.into(),
                            None => ValueType::TypeVoid,
                        }
                    )
                ),
            };

            mem::forget(inputs);

            signature
        }
    }

    #[repr(C)]
    pub enum ValueType {
        TypeI32,
        TypeI64,
        TypeF32,
        TypeF64,
        TypeVoid,
    }

    impl From<wasmi::ValueType> for ValueType {
        fn from(value_type: wasmi::ValueType) -> Self {
            use wasmi::ValueType as V;

            match value_type {
                V::I32 => ValueType::TypeI32,
                V::I64 => ValueType::TypeI64,
                V::F32 => ValueType::TypeF32,
                V::F64 => ValueType::TypeF64,
            }
        }
    }

    impl<'a> From<&'a ValueType> for Option<wasmi::ValueType> {
        fn from(value_type: &'a ValueType) -> Self {
            use wasmi::ValueType as V;

            match value_type {
                ValueType::TypeI32 => Some(V::I32),
                ValueType::TypeI64 => Some(V::I64),
                ValueType::TypeF32 => Some(V::F32),
                ValueType::TypeF64 => Some(V::F64),
                ValueType::TypeVoid => None,
            }
        }
    }

    #[no_mangle]
    pub extern "C" fn wasm_get_function_signature(
        wasm_instance: *const Instance,
        function_name: *const c_char,
    ) -> *const Signature {
        let wasm_instance = check_and_deref!(wasm_instance);
        let function_name = check_and_deref_to_str!(function_name);

        match wasm_instance.get_function_signature(function_name) {
            Some(signature) => Box::into_raw(Box::new(signature.into())),
            None => ptr::null(),
        }
    }

    #[no_mangle]
    pub extern "C" fn wasm_invoke_function(
        wasm_instance: *const Instance,
        function_name: *const c_char,
        arguments: *const Vec<RuntimeValue>,
        wasm_runtime: *mut Runtime,
    ) -> *const Value {
        let wasm_instance = check_and_deref!(wasm_instance);
        let function_name = check_and_deref_to_str!(function_name);
        let arguments = check_and_deref!(arguments).as_slice();
        let mut wasm_runtime = check_and_deref_mut!(wasm_runtime);

        let value = match wasm_instance.invoke(function_name, arguments, &mut wasm_runtime) {
            Some(runtime_value) => runtime_value.into(),
            None => Value::None,
        };

        Box::into_raw(Box::new(value))
    }

    #[no_mangle]
    pub extern "C" fn wasm_invoke_arguments_builder() -> *mut Vec<RuntimeValue> {
        Box::into_raw(Box::new(Vec::new()))
    }

    #[no_mangle]
    pub extern "C" fn drop_wasm_arguments_builder(wasm_arguments_builder: *mut Vec<RuntimeValue>) {
        let _: Box<Vec<RuntimeValue>> = check_and_deref_to_box!(wasm_arguments_builder);
    }

    #[no_mangle]
    pub extern "C" fn wasm_invoke_arguments_builder_add_i32(arguments: *mut Vec<RuntimeValue>, argument: i32) {
        check_and_deref_mut!(arguments).push(RuntimeValue::I32(argument));
    }

    #[no_mangle]
    pub extern "C" fn wasm_invoke_arguments_builder_add_i64(arguments: *mut Vec<RuntimeValue>, argument: i64) {
        check_and_deref_mut!(arguments).push(RuntimeValue::I64(argument));
    }

    #[no_mangle]
    pub extern "C" fn wasm_invoke_arguments_builder_add_f32(arguments: *mut Vec<RuntimeValue>, argument: f32) {
        check_and_deref_mut!(arguments).push(RuntimeValue::F32(F32::from_float(argument)));
    }

    #[no_mangle]
    pub extern "C" fn wasm_invoke_arguments_builder_add_f64(arguments: *mut Vec<RuntimeValue>, argument: f64) {
        check_and_deref_mut!(arguments).push(RuntimeValue::F64(F64::from_float(argument)));
    }

    #[no_mangle]
    pub extern "C" fn wasm_new_runtime() -> *mut Runtime {
        Box::into_raw(Box::new(Runtime::new()))
    }

    #[no_mangle]
    pub extern "C" fn drop_wasm_runtime(wasm_runtime: *mut Runtime) {
        let _: Box<Runtime> = check_and_deref_to_box!(wasm_runtime);
    }

    #[no_mangle]
    pub extern "C" fn wasm_runtime_add_function(
        wasm_runtime: *mut Runtime,
        index: usize,
        name: *const c_char,
        signature: *const ValueType,
        signature_length: usize,
        implementation_pointer: *const fn(*const c_void, *const c_void, *const FunctionInputs) -> *const Value,
        zend_fcall_info: *const c_void,
        zend_fcall_info_cache: *const c_void,
    ) {
        assert!(signature_length >= 1);

        let name = check_and_deref_to_str!(name);
        let wasm_runtime = check_and_deref_mut!(wasm_runtime);

        let signature_inputs =
            Box::into_raw(
                Box::new(
                    if signature_length == 1 {
                        vec![]
                    } else {
                        unsafe {
                            Vec::from_raw_parts(
                                signature as *mut ValueType,
                                signature_length - 1,
                                signature_length - 1,
                            )
                        }
                            .iter()
                            .map(|ref input: &ValueType| {
                                Option::<wasmi::ValueType>::from(*input).unwrap()
                            })
                            .collect::<Vec<_>>()
                    }
                )
            );

        let signature_output = unsafe { signature.offset(signature_length as isize - 1) };

        let signature = wasmi::Signature::new(
            check_and_deref!(signature_inputs).as_slice(),
            check_and_deref!(signature_output).into()
        );

        let implementation_pointer = check_and_deref!(implementation_pointer);

        let function = ImportedFunction {
            index,
            name: String::from(name),
            signature,
            implementation_pointer: *implementation_pointer,
            zend_fcall_info,
            zend_fcall_info_cache,
        };

        wasm_runtime.add_function(function);
    }
}
